<?php

namespace App\Services\Crm;

use App\Models\Crm\Lead;
use App\Models\Crm\LeadSource;
use App\Models\Crm\LeadStatus;
use App\Models\Crm\ProductType;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class LeadBoardService
{
    public function __construct(private readonly LeadAccessService $leadAccessService)
    {
    }

    private const BOARD_STATUS_SLUGS = [
        'new',
        'contacted',
        'follow-up',
        'qualified',
        'proposal-sent',
        'negotiation',
        'won',
        'lost',
    ];

    public function filterOptions(): array
    {
        return [
            'users' => $this->leadAccessService->assignmentUsers(),
            'leadSources' => LeadSource::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'productTypes' => ProductType::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'boardStatuses' => $this->boardStatuses(),
        ];
    }

    public function boardStatuses(): Collection
    {
        return LeadStatus::query()
            ->whereIn('slug', self::BOARD_STATUS_SLUGS)
            ->where('is_active', true)
            ->get()
            ->sortBy(function (LeadStatus $status) {
                return array_search($status->slug, self::BOARD_STATUS_SLUGS, true);
            })
            ->values();
    }

    public function boardData(User $user, Request $request): array
    {
        $statuses = $this->boardStatuses();
        $baseQuery = $this->filteredQuery($user, $request);

        $leads = (clone $baseQuery)
            ->with(['leadSource:id,name', 'assignedUser:id,fullname', 'status:id,name,slug,color'])
            ->orderByRaw('COALESCE(next_followup_date, created_at) asc')
            ->latest('id')
            ->get();

        $grouped = $leads->groupBy('status_id');

        return [
            'columns' => $statuses->map(function (LeadStatus $status) use ($grouped) {
                return [
                    'status' => $status,
                    'leads' => $grouped->get($status->id, collect())->values(),
                    'count' => $grouped->get($status->id, collect())->count(),
                ];
            })->all(),
            'summary' => [
                'total' => $leads->count(),
                'overdue' => $leads->filter(fn (Lead $lead) => $lead->isOverdue())->count(),
                'won' => $leads->filter(fn (Lead $lead) => optional($lead->status)->slug === 'won')->count(),
                'active' => $leads->filter(fn (Lead $lead) => !in_array(optional($lead->status)->slug, ['won', 'lost'], true))->count(),
            ],
        ];
    }

    public function visibleQuery(User $user): Builder
    {
        return $this->leadAccessService->visibleQuery($user);
    }

    public function filteredQuery(User $user, Request $request): Builder
    {
        return $this->visibleQuery($user)
            ->whereHas('status', fn (Builder $query) => $query->whereIn('slug', self::BOARD_STATUS_SLUGS))
            ->when($request->filled('assigned_to'), fn (Builder $query) => $query->where('assigned_to', $request->integer('assigned_to')))
            ->when($request->filled('lead_source_id'), fn (Builder $query) => $query->where('lead_source_id', $request->integer('lead_source_id')))
            ->when($request->filled('product_type_id'), fn (Builder $query) => $query->where('product_type_id', $request->integer('product_type_id')))
            ->when($request->filled('date_from'), fn (Builder $query) => $query->whereDate('created_at', '>=', $request->date('date_from')->toDateString()))
            ->when($request->filled('date_to'), fn (Builder $query) => $query->whereDate('created_at', '<=', $request->date('date_to')->toDateString()));
    }

}
