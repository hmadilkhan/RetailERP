<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\StoreLeadQuotationRequest;
use App\Http\Requests\Crm\UpdateLeadQuotationRequest;
use App\Models\Crm\Lead;
use App\Models\Crm\LeadQuotation;
use App\Services\Crm\LeadQuotationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use RuntimeException;

class LeadQuotationController extends Controller
{
    public function __construct(private readonly LeadQuotationService $quotationService)
    {
    }

    public function create(Lead $lead): View
    {
        $this->authorize('update', $lead);

        return view('crm.quotations.create', [
            'lead' => $lead->load(['assignedUser', 'status']),
            'quotation' => new LeadQuotation([
                'quotation_date' => now()->format('Y-m-d'),
                'valid_until' => now()->addDays(15)->format('Y-m-d'),
                'status' => LeadQuotation::STATUS_DRAFT,
                'discount' => 0,
                'tax' => 0,
            ]),
            'statusOptions' => LeadQuotation::STATUSES,
            'action' => route('crm.leads.quotations.store', $lead),
            'method' => 'POST',
            'submitLabel' => 'Create Quotation',
        ]);
    }

    public function store(StoreLeadQuotationRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);

        $quotation = $this->quotationService->create($lead, $request->validated(), $request->user());

        return redirect()
            ->route('crm.quotations.show', $quotation)
            ->with('crm_success', 'Quotation created successfully.');
    }

    public function show(LeadQuotation $quotation): View
    {
        $lead = $quotation->lead()->with(['status', 'assignedUser', 'activities.creator'])->firstOrFail();
        $this->authorize('view', $lead);

        $quotation->load(['lead', 'items', 'creator']);

        return view('crm.quotations.show', [
            'quotation' => $quotation,
            'lead' => $lead,
            'statusOptions' => LeadQuotation::STATUSES,
            'canEditQuotation' => !$quotation->isLocked() && auth()->user()->can('update', $lead),
        ]);
    }

    public function edit(LeadQuotation $quotation): View
    {
        $lead = $quotation->lead()->with(['assignedUser', 'status'])->firstOrFail();
        $this->authorize('update', $lead);

        if ($quotation->isLocked()) {
            abort(403, 'This quotation can no longer be edited.');
        }

        $quotation->load('items');

        return view('crm.quotations.edit', [
            'lead' => $lead,
            'quotation' => $quotation,
            'statusOptions' => LeadQuotation::STATUSES,
            'action' => route('crm.quotations.update', $quotation),
            'method' => 'PUT',
            'submitLabel' => 'Update Quotation',
        ]);
    }

    public function update(UpdateLeadQuotationRequest $request, LeadQuotation $quotation): RedirectResponse
    {
        $lead = $quotation->lead;
        $this->authorize('update', $lead);

        try {
            $this->quotationService->update($quotation, $request->validated(), $request->user());
        } catch (RuntimeException $exception) {
            return back()->with('crm_duplicate_warning', $exception->getMessage())->withInput();
        }

        return redirect()
            ->route('crm.quotations.show', $quotation)
            ->with('crm_success', 'Quotation updated successfully.');
    }

    public function pdf(LeadQuotation $quotation): Response
    {
        $lead = $quotation->lead;
        $this->authorize('view', $lead);

        $quotation->load(['lead', 'items', 'creator']);

        $pdf = Pdf::loadView('crm.quotations.pdf', [
            'quotation' => $quotation,
            'lead' => $lead,
        ])->setPaper('a4', 'portrait');

        return $pdf->download(str($quotation->quotation_no)->slug('-') . '.pdf');
    }
}
