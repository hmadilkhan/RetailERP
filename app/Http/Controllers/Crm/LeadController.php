<?php

namespace App\Http\Controllers\Crm;

use App\Exports\Crm\LeadListExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\AssignLeadRequest;
use App\Http\Requests\Crm\BulkAssignLeadRequest;
use App\Http\Requests\Crm\StoreLeadRequest;
use App\Http\Requests\Crm\UpdateLeadRequest;
use App\Models\Crm\Lead;
use App\Models\Crm\LeadFollowup;
use App\Models\Crm\LeadSource;
use App\Models\Crm\LeadStatus;
use App\Models\Crm\Product;
use App\Models\Crm\ProductType;
use App\Services\Crm\LeadActivityLogger;
use App\Services\Crm\LeadAccessService;
use App\Services\Crm\LeadConversionService;
use App\Services\Crm\LeadNotificationService;
use App\Support\CrmFilterState;
use App\Support\CrmLeadPermissions;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class LeadController extends Controller
{
    public function __construct(
        private readonly LeadActivityLogger $activityLogger,
        private readonly LeadAccessService $leadAccessService,
        private readonly LeadConversionService $leadConversionService,
        private readonly LeadNotificationService $notificationService
    )
    {
    }

    public function index(Request $request): View|RedirectResponse
    {
        $this->authorize('viewAny', Lead::class);

        $filterState = CrmFilterState::restore($request, 'crm.leads.filters', [
            'search',
            'lead_source_id',
            'product_type_id',
            'product_id',
            'status_id',
            'city',
            'assigned_to',
            'my_leads',
            'unassigned_only',
        ]);

        if ($filterState['redirect']) {
            return redirect()->route('crm.leads.index', $filterState['values']);
        }

        $filters = $filterState['values'];
        $visibleLeads = $this->leadAccessService->visibleQuery($request->user());

        $leads = (clone $visibleLeads)
            ->with(['leadSource', 'productType', 'product', 'status', 'assignedUser', 'latestFollowup'])
            ->withCount('followups')
            ->search($filters['search'] ?? null)
            ->when(!empty($filters['lead_source_id']), fn ($query) => $query->where('lead_source_id', (int) $filters['lead_source_id']))
            ->when(!empty($filters['product_type_id']), fn ($query) => $query->where('product_type_id', (int) $filters['product_type_id']))
            ->when(!empty($filters['product_id']), fn ($query) => $query->where('product_id', (int) $filters['product_id']))
            ->when(!empty($filters['status_id']), fn ($query) => $query->where('status_id', (int) $filters['status_id']))
            ->when(!empty($filters['city']), fn ($query) => $query->where('city', 'like', '%' . trim((string) $filters['city']) . '%'))
            ->when(!empty($filters['assigned_to']), fn ($query) => $query->where('assigned_to', (int) $filters['assigned_to']))
            ->when(!empty($filters['my_leads']), function ($query) use ($request) {
                $query->where(function ($builder) use ($request): void {
                    $builder
                        ->where('assigned_to', $request->user()->id)
                        ->orWhere('created_by', $request->user()->id);
                });
            })
            ->when(!empty($filters['unassigned_only']), fn ($query) => $query->whereNull('assigned_to'))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $statsBaseQuery = clone $visibleLeads;

        return view('crm.leads.index', [
            'leads' => $leads,
            'filters' => $filters,
            'activeFilterSummary' => $this->leadFilterSummaryFromFilters($filters),
            'stats' => [
                'total' => (clone $statsBaseQuery)->count(),
                'open' => (clone $statsBaseQuery)->whereHas('status', fn ($query) => $query->whereNotIn('slug', ['won', 'lost', 'junk']))->count(),
                'won' => (clone $statsBaseQuery)->whereHas('status', fn ($query) => $query->where('slug', 'won'))->count(),
                'followups' => (clone $statsBaseQuery)->whereDate('next_followup_date', '>=', now()->toDateString())->count(),
            ],
            'canCreateLead' => $request->user()->can('create', Lead::class),
            'canBulkAssign' => $request->user()->can('bulkAssign', Lead::class),
            'canExportLeads' => $request->user()->can('export', Lead::class),
            'canAssignLeads' => CrmLeadPermissions::canAssign($request->user()),
            'crmRoleLabel' => $request->user()->crmRoleLabel(),
        ] + $this->formOptions($filters['product_type_id'] ?? null));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Lead::class);

        return view('crm.leads.create', [
            'lead' => new Lead([
                'priority' => 'medium',
                'temperature' => 'warm',
                'probability_percent' => 0,
                'lead_score' => 0,
                'preferred_contact_method' => 'call',
            ]),
            'duplicateLeads' => $this->findPotentialDuplicates($request),
            'suggestedLeadCode' => Lead::generateLeadCode(),
            'canAssignLead' => CrmLeadPermissions::canAssign($request->user()),
        ] + $this->formOptions($request->input('product_type_id')));
    }

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $this->authorize('create', Lead::class);

        $payload = $request->validated();

        if (!CrmLeadPermissions::canAssign($request->user())) {
            $payload['assigned_to'] = null;
        }

        $lead = Lead::create($payload + [
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $lead->load(['status', 'assignedUser']);
        $this->activityLogger->logLeadCreated($lead, auth()->user());

        if ($lead->assigned_to) {
            $this->notificationService->notifyLeadAssigned($lead, auth()->user());
        }

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('crm_success', 'Lead created successfully.')
            ->with('crm_duplicate_warning', $this->duplicateWarningMessage($this->findPotentialDuplicates($request, $lead)));
    }

    public function show(Lead $lead): View
    {
        $this->authorize('view', $lead);

        $lead->load([
            'leadSource',
            'productType',
            'product',
            'status',
            'assignedUser',
            'createdBy',
            'updatedBy',
            'convertedBy',
            'convertedCustomer',
            'followups.creator',
            'latestFollowup.creator',
            'activities.creator',
            'attachments.uploader',
            'quotations.items',
            'quotations.creator',
        ])->loadCount('followups');

        return view('crm.leads.show', compact('lead') + [
            'followupTypeOptions' => LeadFollowup::TYPES,
            'quickFollowupOptions' => ['Call', 'WhatsApp', 'Email', 'Meeting'],
            'canAssignLead' => auth()->user()->can('assign', $lead),
            'canEditLead' => auth()->user()->can('update', $lead),
            'canDeleteLead' => auth()->user()->can('delete', $lead),
            'canAddFollowup' => auth()->user()->can('addFollowup', $lead),
            'canUploadAttachment' => auth()->user()->can('uploadAttachment', $lead),
            'canChangeStatus' => auth()->user()->can('changeStatus', $lead),
            'canConvertLead' => auth()->user()->can('convert', $lead),
            'canCreateQuotation' => auth()->user()->can('update', $lead),
            'assignableUsers' => $this->leadAccessService->assignmentUsers(),
        ]);
    }

    public function edit(Lead $lead, Request $request): View
    {
        $this->authorize('update', $lead);

        $lead->load(['leadSource', 'productType', 'product', 'status', 'assignedUser']);

        return view('crm.leads.edit', [
            'lead' => $lead,
            'duplicateLeads' => $this->findPotentialDuplicates($request->merge([
                'contact_number' => $request->input('contact_number', $lead->contact_number),
                'email' => $request->input('email', $lead->email),
            ]), $lead),
            'suggestedLeadCode' => $lead->lead_code,
            'canAssignLead' => CrmLeadPermissions::canAssign($request->user()),
        ] + $this->formOptions($lead->product_type_id));
    }

    public function update(UpdateLeadRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);

        $original = $lead->only([
            'contact_person_name',
            'company_name',
            'contact_number',
            'email',
            'city',
            'product_type_id',
            'product_id',
            'status_id',
            'priority',
            'temperature',
            'assigned_to',
            'expected_deal_value',
            'probability_percent',
            'next_followup_date',
            'is_converted',
        ]);

        $payload = $request->validated();
        $oldStatusName = $lead->status?->name;
        $oldAssignedTo = $lead->assigned_to;

        if (!$request->user()->can('assign', $lead)) {
            unset($payload['assigned_to']);
        }

        $lead->update($payload + [
            'updated_by' => auth()->id(),
        ]);

        $lead->load(['status', 'assignedUser']);
        $this->activityLogger->logLeadUpdated($lead, $original, auth()->user());

        if ((string) ($original['status_id'] ?? null) !== (string) $lead->status_id) {
            $this->notificationService->notifyLeadStatusChanged($lead, $oldStatusName, auth()->user());
        }

        if ((string) $oldAssignedTo !== (string) $lead->assigned_to && $lead->assigned_to) {
            $this->notificationService->notifyLeadAssigned($lead, auth()->user());
        }

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('crm_success', 'Lead updated successfully.')
            ->with('crm_duplicate_warning', $this->duplicateWarningMessage($this->findPotentialDuplicates($request, $lead)));
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        return redirect()
            ->route('crm.leads.index')
            ->with('crm_success', 'Lead deleted successfully.');
    }

    public function productsByType(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Lead::class);

        $products = Product::query()
            ->select(['id', 'name', 'product_type_id'])
            ->when($request->filled('product_type_id'), fn ($query) => $query->where('product_type_id', $request->integer('product_type_id')))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($products);
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $this->authorize('export', Lead::class);

        $leads = $this->filteredLeadsQuery($request)
            ->with(['leadSource', 'productType', 'product', 'status', 'assignedUser', 'latestFollowup'])
            ->latest('id')
            ->get();

        return Excel::download(
            new LeadListExport($leads),
            'crm-leads-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    public function exportPdf(Request $request): Response
    {
        $this->authorize('export', Lead::class);

        $leads = $this->filteredLeadsQuery($request)
            ->with(['leadSource', 'productType', 'product', 'status', 'assignedUser'])
            ->latest('id')
            ->get();

        $rows = $leads->map(function (Lead $lead): array {
            return [
                $lead->lead_code,
                $lead->contact_person_name,
                $lead->company_name ?: 'N/A',
                $lead->contact_number,
                $lead->email ?: 'N/A',
                $lead->leadSource?->name ?? 'N/A',
                $lead->productType?->name ?? 'N/A',
                $lead->product?->name ?? 'N/A',
                $lead->status?->name ?? 'N/A',
                ucfirst((string) $lead->priority),
                $lead->assignedUser?->fullname ?? 'Unassigned',
                optional($lead->next_followup_date)?->format('Y-m-d') ?: 'Not scheduled',
                optional($lead->created_at)?->format('Y-m-d H:i:s'),
            ];
        });

        $pdf = Pdf::loadView('crm.exports.leads-pdf', [
            'title' => 'CRM Leads Export',
            'columns' => [
                'Lead ID',
                'Contact Person',
                'Company',
                'Phone',
                'Email',
                'Source',
                'Product Type',
                'Product',
                'Status',
                'Priority',
                'Assigned To',
                'Next Follow-up Date',
                'Created Date',
            ],
            'rows' => $rows,
            'appliedFilters' => $this->leadFilterSummary($request),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('crm-leads-' . now()->format('Ymd-His') . '.pdf');
    }

    public function assign(AssignLeadRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorize('assign', $lead);

        $original = $lead->only(['assigned_to']);

        $lead->update([
            'assigned_to' => $request->validated('assigned_to'),
            'updated_by' => auth()->id(),
        ]);

        $lead->load('assignedUser');
        $this->activityLogger->logLeadUpdated($lead, $original, auth()->user());
        $this->notificationService->notifyLeadAssigned($lead, auth()->user());

        return back()->with('crm_success', 'Lead assignment updated successfully.');
    }

    public function bulkAssign(BulkAssignLeadRequest $request): RedirectResponse
    {
        $this->authorize('bulkAssign', Lead::class);

        $leadIds = collect($request->validated('lead_ids'))->unique()->values();
        $assignedTo = $request->validated('assigned_to');

        $leads = $this->visibleLeadsQuery($request->user())
            ->whereIn('id', $leadIds)
            ->get();

        DB::transaction(function () use ($leads, $assignedTo): void {
            foreach ($leads as $lead) {
                $original = $lead->only(['assigned_to']);

                $lead->update([
                    'assigned_to' => $assignedTo,
                    'updated_by' => auth()->id(),
                ]);

                $lead->load('assignedUser');
                $this->activityLogger->logLeadUpdated($lead, $original, auth()->user());
                $this->notificationService->notifyLeadAssigned($lead, auth()->user());
            }
        });

        return redirect()
            ->route('crm.leads.index')
            ->with('crm_success', 'Selected leads were assigned successfully.');
    }

    public function convert(Lead $lead): RedirectResponse
    {
        $this->authorize('convert', $lead);

        try {
            $customer = $this->leadConversionService->convert($lead->load('status'), auth()->id());
        } catch (\RuntimeException $exception) {
            return back()->with('crm_duplicate_warning', $exception->getMessage());
        }

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('crm_success', 'Lead converted successfully. ERP customer linked: ' . $customer->name . '.');
    }

    private function formOptions($selectedProductTypeId = null): array
    {
        $selectedTypeId = $selectedProductTypeId ?: request('product_type_id');

        return [
            'leadSources' => LeadSource::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'productTypes' => ProductType::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'products' => Product::query()
                ->where('is_active', true)
                ->when($selectedTypeId, fn ($query) => $query->where('product_type_id', $selectedTypeId))
                ->orderBy('name')
                ->get(),
            'allProducts' => Product::query()
                ->where('is_active', true)
                ->orderBy('product_type_id')
                ->orderBy('name')
                ->get(['id', 'name', 'product_type_id']),
            'leadStatuses' => LeadStatus::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'users' => $this->leadAccessService->assignmentUsers(),
            'priorityOptions' => [
                'low' => 'Low',
                'medium' => 'Medium',
                'high' => 'High',
                'urgent' => 'Urgent',
            ],
            'temperatureOptions' => [
                'cold' => 'Cold',
                'warm' => 'Warm',
                'hot' => 'Hot',
            ],
            'contactMethodOptions' => [
                'call' => 'Call',
                'email' => 'Email',
                'whatsapp' => 'WhatsApp',
                'meeting' => 'Meeting',
            ],
        ];
    }

    private function filteredLeadsQuery(Request $request): Builder
    {
        return $this->leadAccessService->visibleQuery($request->user())
            ->search($request->string('search')->toString())
            ->when($request->filled('lead_source_id'), fn ($query) => $query->where('lead_source_id', $request->integer('lead_source_id')))
            ->when($request->filled('product_type_id'), fn ($query) => $query->where('product_type_id', $request->integer('product_type_id')))
            ->when($request->filled('product_id'), fn ($query) => $query->where('product_id', $request->integer('product_id')))
            ->when($request->filled('status_id'), fn ($query) => $query->where('status_id', $request->integer('status_id')))
            ->when($request->filled('city'), fn ($query) => $query->where('city', 'like', '%' . trim((string) $request->input('city')) . '%'))
            ->when($request->filled('assigned_to'), fn ($query) => $query->where('assigned_to', $request->integer('assigned_to')))
            ->when($request->boolean('my_leads'), function ($query) use ($request) {
                $query->where(function ($builder) use ($request): void {
                    $builder
                        ->where('assigned_to', $request->user()->id)
                        ->orWhere('created_by', $request->user()->id);
                });
            })
            ->when($request->boolean('unassigned_only') || $request->boolean('unassigned'), fn ($query) => $query->whereNull('assigned_to'));
    }

    private function leadFilterSummary(Request $request): string
    {
        return $this->leadFilterSummaryFromFilters($request->all());
    }

    private function leadFilterSummaryFromFilters(array $filters): string
    {
        $labels = [];

        if (!empty($filters['search'])) {
            $labels[] = 'Search: ' . trim((string) $filters['search']);
        }

        if (!empty($filters['lead_source_id'])) {
            $labels[] = 'Source: ' . (LeadSource::query()->whereKey((int) $filters['lead_source_id'])->value('name') ?? 'Unknown');
        }

        if (!empty($filters['product_type_id'])) {
            $labels[] = 'Product Type: ' . (ProductType::query()->whereKey((int) $filters['product_type_id'])->value('name') ?? 'Unknown');
        }

        if (!empty($filters['product_id'])) {
            $labels[] = 'Product: ' . (Product::query()->whereKey((int) $filters['product_id'])->value('name') ?? 'Unknown');
        }

        if (!empty($filters['status_id'])) {
            $labels[] = 'Status: ' . (LeadStatus::query()->whereKey((int) $filters['status_id'])->value('name') ?? 'Unknown');
        }

        if (!empty($filters['city'])) {
            $labels[] = 'City: ' . trim((string) $filters['city']);
        }

        if (!empty($filters['assigned_to'])) {
            $assignedUser = $this->leadAccessService->assignmentUsers()->firstWhere('id', (int) $filters['assigned_to']);
            $labels[] = 'Assigned To: ' . ($assignedUser->fullname ?? 'Unknown');
        }

        if (!empty($filters['my_leads'])) {
            $labels[] = 'My Leads';
        }

        if (!empty($filters['unassigned_only']) || !empty($filters['unassigned'])) {
            $labels[] = 'Unassigned Only';
        }

        return implode(' | ', $labels);
    }

    private function findPotentialDuplicates(Request $request, ?Lead $ignoreLead = null): Collection
    {
        $contactNumber = trim((string) $request->input('contact_number'));
        $email = strtolower(trim((string) $request->input('email')));

        if ($contactNumber === '' && $email === '') {
            return collect();
        }

        return Lead::query()
            ->select(['id', 'lead_code', 'contact_person_name', 'company_name', 'contact_number', 'email'])
            ->when($ignoreLead, fn ($query) => $query->whereKeyNot($ignoreLead->getKey()))
            ->where(function ($query) use ($contactNumber, $email): void {
                if ($contactNumber !== '') {
                    $query->orWhere('contact_number', $contactNumber);
                }

                if ($email !== '') {
                    $query->orWhereRaw('LOWER(email) = ?', [$email]);
                }
            })
            ->limit(5)
            ->get();
    }

    private function duplicateWarningMessage(Collection $duplicates): ?string
    {
        if ($duplicates->isEmpty()) {
            return null;
        }

        $leadCodes = $duplicates->pluck('lead_code')->filter()->implode(', ');

        return 'Potential duplicate lead found for the same contact number or email. Existing lead codes: ' . $leadCodes . '.';
    }
}
