<?php

namespace App\Services\Crm;

use App\Models\Crm\Lead;
use App\Models\Crm\LeadSource;
use App\Models\Crm\LeadStatus;
use App\Models\Crm\Product;
use App\Models\Crm\ProductType;
use App\Support\CrmLeadPermissions;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LeadDashboardReportService
{
    public function filters(): array
    {
        return [
            'leadSources' => LeadSource::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'productTypes' => ProductType::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(),
            'leadStatuses' => LeadStatus::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'users' => $this->assignmentUsers(),
        ];
    }

    public function getDashboardData(User $user, array $filters): array
    {
        $baseQuery = $this->filteredLeadsQuery($user, $filters);

        $summaryCards = $this->summaryCards($baseQuery);
        $reports = [
            'sourceWise' => $this->sourceWiseLeadCount($baseQuery),
            'productWise' => $this->productWiseLeadCount($baseQuery),
            'statusWise' => $this->statusWiseLeadSummary($baseQuery),
            'salesPerformance' => $this->salesPersonPerformance($baseQuery),
            'monthlyTrend' => $this->monthlyLeadTrend($baseQuery, $filters),
            'conversionRate' => $this->conversionRate($baseQuery),
            'wonLostRatio' => $this->wonLostRatio($baseQuery),
            'expectedRevenue' => $this->expectedRevenuePipeline($baseQuery),
        ];

        return [
            'summaryCards' => $summaryCards,
            'reports' => $reports,
            'charts' => $this->chartData($reports),
        ];
    }

    public function reportDataset(User $user, array $filters, string $report): Collection
    {
        $baseQuery = $this->filteredLeadsQuery($user, $filters);

        return match ($report) {
            'source-wise' => $this->sourceWiseLeadCount($baseQuery),
            'status-wise' => $this->statusWiseLeadSummary($baseQuery),
            'sales-performance' => $this->salesPersonPerformance($baseQuery),
            'monthly-trend' => $this->monthlyLeadTrend($baseQuery, $filters),
            default => collect(),
        };
    }

    public function reportDefinition(string $report): array
    {
        return match ($report) {
            'source-wise' => [
                'title' => 'Source-wise Lead Report',
                'columns' => ['Source', 'Lead Count'],
            ],
            'status-wise' => [
                'title' => 'Status-wise Lead Report',
                'columns' => ['Status', 'Lead Count', 'Expected Value'],
            ],
            'sales-performance' => [
                'title' => 'Sales Person Performance Report',
                'columns' => ['Sales Person', 'Assigned Leads', 'Won Leads', 'Converted Leads', 'Conversion Rate', 'Pipeline Value'],
            ],
            'monthly-trend' => [
                'title' => 'Monthly Leads Report',
                'columns' => ['Month', 'Lead Count'],
            ],
            default => [
                'title' => 'CRM Report',
                'columns' => [],
            ],
        };
    }

    public function filterSummary(array $filters): string
    {
        $labels = [];

        if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
            $labels[] = 'Date: ' . ($filters['date_from'] ?? 'Start') . ' to ' . ($filters['date_to'] ?? 'Today');
        }

        if (!empty($filters['lead_source_id'])) {
            $labels[] = 'Source: ' . (LeadSource::query()->whereKey($filters['lead_source_id'])->value('name') ?? 'Unknown');
        }

        if (!empty($filters['product_type_id'])) {
            $labels[] = 'Product Type: ' . (ProductType::query()->whereKey($filters['product_type_id'])->value('name') ?? 'Unknown');
        }

        if (!empty($filters['product_id'])) {
            $labels[] = 'Product: ' . (Product::query()->whereKey($filters['product_id'])->value('name') ?? 'Unknown');
        }

        if (!empty($filters['assigned_to'])) {
            $labels[] = 'Assigned To: ' . (User::query()->whereKey($filters['assigned_to'])->value('fullname') ?? 'Unknown');
        }

        if (!empty($filters['status_id'])) {
            $labels[] = 'Status: ' . (LeadStatus::query()->whereKey($filters['status_id'])->value('name') ?? 'Unknown');
        }

        return implode(' | ', $labels);
    }

    public function filteredLeadsQuery(User $user, array $filters): Builder
    {
        return $this->visibleLeadsQuery($user)
            ->when(!empty($filters['date_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']))
            ->when(!empty($filters['lead_source_id']), fn (Builder $query) => $query->where('lead_source_id', $filters['lead_source_id']))
            ->when(!empty($filters['product_type_id']), fn (Builder $query) => $query->where('product_type_id', $filters['product_type_id']))
            ->when(!empty($filters['product_id']), fn (Builder $query) => $query->where('product_id', $filters['product_id']))
            ->when(!empty($filters['assigned_to']), fn (Builder $query) => $query->where('assigned_to', $filters['assigned_to']))
            ->when(!empty($filters['status_id']), fn (Builder $query) => $query->where('status_id', $filters['status_id']));
    }

    private function summaryCards(Builder $baseQuery): array
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $todayStart = now()->startOfDay();

        return [
            [
                'label' => 'Total Leads',
                'value' => (clone $baseQuery)->count(),
                'tone' => 'blue',
                'helper' => 'Total visible leads in the current CRM scope.',
            ],
            [
                'label' => 'New Leads Today',
                'value' => (clone $baseQuery)->whereDate('created_at', $today)->count(),
                'tone' => 'cyan',
                'helper' => 'Fresh lead intake captured today.',
            ],
            [
                'label' => 'Leads This Month',
                'value' => (clone $baseQuery)->whereDate('created_at', '>=', $monthStart)->count(),
                'tone' => 'indigo',
                'helper' => 'Lead generation volume for the current month.',
            ],
            [
                'label' => 'Pending Follow-ups',
                'value' => (clone $baseQuery)
                    ->whereNotNull('next_followup_date')
                    ->whereDate('next_followup_date', '>=', $today)
                    ->whereHas('status', fn (Builder $query) => $query->whereNotIn('slug', ['won', 'lost', 'junk']))
                    ->count(),
                'tone' => 'amber',
                'helper' => 'Upcoming follow-ups that still need action.',
            ],
            [
                'label' => 'Overdue Follow-ups',
                'value' => (clone $baseQuery)
                    ->whereNotNull('next_followup_date')
                    ->where('next_followup_date', '<', $todayStart)
                    ->whereHas('status', fn (Builder $query) => $query->whereNotIn('slug', ['won', 'lost', 'junk']))
                    ->count(),
                'tone' => 'rose',
                'helper' => 'Follow-ups that are past their planned date.',
            ],
            [
                'label' => 'Won Leads',
                'value' => (clone $baseQuery)->whereHas('status', fn (Builder $query) => $query->where('slug', 'won'))->count(),
                'tone' => 'emerald',
                'helper' => 'Leads already marked as won.',
            ],
            [
                'label' => 'Lost Leads',
                'value' => (clone $baseQuery)->whereHas('status', fn (Builder $query) => $query->where('slug', 'lost'))->count(),
                'tone' => 'slate',
                'helper' => 'Leads marked as lost in the funnel.',
            ],
            [
                'label' => 'Converted Leads',
                'value' => (clone $baseQuery)->where('is_converted', true)->count(),
                'tone' => 'violet',
                'helper' => 'Leads already converted downstream.',
            ],
        ];
    }

    private function sourceWiseLeadCount(Builder $baseQuery): Collection
    {
        return (clone $baseQuery)
            ->leftJoin('crm_lead_sources', 'crm_lead_sources.id', '=', 'crm_leads.lead_source_id')
            ->selectRaw('COALESCE(crm_lead_sources.name, ?) as label, COUNT(crm_leads.id) as total', ['Unspecified'])
            ->groupBy('crm_lead_sources.name')
            ->orderByDesc('total')
            ->get();
    }

    private function productWiseLeadCount(Builder $baseQuery): Collection
    {
        return (clone $baseQuery)
            ->leftJoin('crm_products', 'crm_products.id', '=', 'crm_leads.product_id')
            ->leftJoin('crm_product_types', 'crm_product_types.id', '=', 'crm_leads.product_type_id')
            ->selectRaw(
                'COALESCE(crm_products.name, ?) as product_name, COALESCE(crm_product_types.name, ?) as product_type_name, COUNT(crm_leads.id) as total',
                ['Unspecified', 'Unspecified']
            )
            ->groupBy('crm_products.name', 'crm_product_types.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    private function statusWiseLeadSummary(Builder $baseQuery): Collection
    {
        return (clone $baseQuery)
            ->join('crm_lead_statuses', 'crm_lead_statuses.id', '=', 'crm_leads.status_id')
            ->selectRaw(
                'crm_lead_statuses.name, crm_lead_statuses.slug, crm_lead_statuses.color, COUNT(crm_leads.id) as total, COALESCE(SUM(crm_leads.expected_deal_value), 0) as expected_value'
            )
            ->groupBy('crm_lead_statuses.name', 'crm_lead_statuses.slug', 'crm_lead_statuses.color')
            ->orderByDesc('total')
            ->get();
    }

    private function salesPersonPerformance(Builder $baseQuery): Collection
    {
        return (clone $baseQuery)
            ->leftJoin('user_details', 'user_details.id', '=', 'crm_leads.assigned_to')
            ->leftJoin('crm_lead_statuses', 'crm_lead_statuses.id', '=', 'crm_leads.status_id')
            ->selectRaw(
                'COALESCE(user_details.fullname, ?) as sales_person,
                COUNT(crm_leads.id) as total_leads,
                SUM(CASE WHEN crm_lead_statuses.slug = ? THEN 1 ELSE 0 END) as won_leads,
                SUM(CASE WHEN crm_leads.is_converted = 1 THEN 1 ELSE 0 END) as converted_leads,
                COALESCE(SUM(crm_leads.expected_deal_value), 0) as pipeline_value',
                ['Unassigned', 'won']
            )
            ->groupBy('user_details.fullname')
            ->orderByDesc('pipeline_value')
            ->get()
            ->map(function ($row) {
                $row->conversion_rate = (int) $row->total_leads > 0
                    ? round(((int) $row->converted_leads / (int) $row->total_leads) * 100, 1)
                    : 0;

                return $row;
            });
    }

    private function monthlyLeadTrend(Builder $baseQuery, array $filters): Collection
    {
        $from = !empty($filters['date_from'])
            ? Carbon::parse($filters['date_from'])->startOfMonth()
            : now()->copy()->subMonths(11)->startOfMonth();

        $to = !empty($filters['date_to'])
            ? Carbon::parse($filters['date_to'])->endOfMonth()
            : now()->copy()->endOfMonth();

        $rawResults = (clone $baseQuery)
            ->whereBetween('crm_leads.created_at', [$from, $to])
            ->selectRaw(
                "DATE_FORMAT(crm_leads.created_at, '%Y-%m') as period_key,
                DATE_FORMAT(crm_leads.created_at, '%b %Y') as period_label,
                COUNT(crm_leads.id) as total"
            )
            ->groupBy('period_key', 'period_label')
            ->orderBy('period_key')
            ->get()
            ->keyBy('period_key');

        return collect(CarbonPeriod::create($from, '1 month', $to))
            ->map(function (Carbon $date) use ($rawResults) {
                $key = $date->format('Y-m');
                $row = $rawResults->get($key);

                return (object) [
                    'period_key' => $key,
                    'period_label' => $date->format('M Y'),
                    'total' => (int) ($row->total ?? 0),
                ];
            });
    }

    private function conversionRate(Builder $baseQuery): array
    {
        $total = (clone $baseQuery)->count();
        $converted = (clone $baseQuery)->where('is_converted', true)->count();
        $won = (clone $baseQuery)->whereHas('status', fn (Builder $query) => $query->where('slug', 'won'))->count();

        return [
            'total' => $total,
            'converted' => $converted,
            'won' => $won,
            'conversion_rate' => $total > 0 ? round(($converted / $total) * 100, 1) : 0,
            'win_rate' => $total > 0 ? round(($won / $total) * 100, 1) : 0,
        ];
    }

    private function wonLostRatio(Builder $baseQuery): array
    {
        $won = (clone $baseQuery)->whereHas('status', fn (Builder $query) => $query->where('slug', 'won'))->count();
        $lost = (clone $baseQuery)->whereHas('status', fn (Builder $query) => $query->where('slug', 'lost'))->count();

        return [
            'won' => $won,
            'lost' => $lost,
            'ratio' => $lost > 0 ? round($won / $lost, 2) : ($won > 0 ? $won : 0),
        ];
    }

    private function expectedRevenuePipeline(Builder $baseQuery): array
    {
        $openPipeline = (clone $baseQuery)
            ->whereHas('status', fn (Builder $query) => $query->whereNotIn('slug', ['won', 'lost', 'junk']))
            ->sum('expected_deal_value');

        $weightedPipeline = (clone $baseQuery)
            ->whereHas('status', fn (Builder $query) => $query->whereNotIn('slug', ['won', 'lost', 'junk']))
            ->selectRaw('COALESCE(SUM(expected_deal_value * (probability_percent / 100)), 0) as total')
            ->value('total');

        $wonRevenue = (clone $baseQuery)
            ->whereHas('status', fn (Builder $query) => $query->where('slug', 'won'))
            ->sum('expected_deal_value');

        return [
            'open_pipeline' => (float) $openPipeline,
            'weighted_pipeline' => (float) $weightedPipeline,
            'won_revenue' => (float) $wonRevenue,
        ];
    }

    private function chartData(array $reports): array
    {
        return [
            'monthlyLeads' => [
                'labels' => $reports['monthlyTrend']->pluck('period_label')->values()->all(),
                'datasets' => [
                    [
                        'label' => 'Leads',
                        'data' => $reports['monthlyTrend']->pluck('total')->map(fn ($value) => (int) $value)->values()->all(),
                        'borderColor' => '#114a8f',
                        'backgroundColor' => 'rgba(17, 74, 143, 0.12)',
                        'fill' => true,
                        'tension' => 0.38,
                    ],
                ],
            ],
            'sourceDistribution' => [
                'labels' => $reports['sourceWise']->pluck('label')->values()->all(),
                'datasets' => [
                    [
                        'label' => 'Leads by Source',
                        'data' => $reports['sourceWise']->pluck('total')->map(fn ($value) => (int) $value)->values()->all(),
                        'backgroundColor' => ['#114a8f', '#2f80ed', '#1d6fd6', '#7c3aed', '#10b981', '#f59e0b', '#ef4444', '#0f766e'],
                    ],
                ],
            ],
            'conversion' => [
                'labels' => ['Converted', 'Won', 'Open / Other'],
                'datasets' => [
                    [
                        'label' => 'Lead Conversion Mix',
                        'data' => [
                            (int) $reports['conversionRate']['converted'],
                            (int) $reports['conversionRate']['won'],
                            max(
                                0,
                                (int) $reports['conversionRate']['total']
                                - (int) $reports['conversionRate']['converted']
                                - (int) $reports['conversionRate']['won']
                            ),
                        ],
                        'backgroundColor' => ['#7c3aed', '#10b981', '#cbd5e1'],
                    ],
                ],
            ],
            'salesPerformance' => [
                'labels' => $reports['salesPerformance']->pluck('sales_person')->values()->all(),
                'datasets' => [
                    [
                        'label' => 'Assigned Leads',
                        'data' => $reports['salesPerformance']->pluck('total_leads')->map(fn ($value) => (int) $value)->values()->all(),
                        'backgroundColor' => '#114a8f',
                    ],
                    [
                        'label' => 'Won Leads',
                        'data' => $reports['salesPerformance']->pluck('won_leads')->map(fn ($value) => (int) $value)->values()->all(),
                        'backgroundColor' => '#10b981',
                    ],
                ],
            ],
        ];
    }

    private function visibleLeadsQuery(User $user): Builder
    {
        $query = Lead::query();

        if (CrmLeadPermissions::canViewAll($user)) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($user): void {
            $builder
                ->where('assigned_to', $user->id)
                ->orWhere('created_by', $user->id);
        });
    }

    private function assignmentUsers(): Collection
    {
        return User::query()
            ->select('user_details.id', 'user_details.fullname')
            ->leftJoin('user_authorization', function ($join): void {
                $join->on('user_authorization.user_id', '=', 'user_details.id')
                    ->where('user_authorization.status_id', 1);
            })
            ->leftJoin('user_roles', 'user_roles.role_id', '=', 'user_authorization.role_id')
            ->where(function ($query): void {
                $query
                    ->where('user_roles.role', 'like', '%admin%')
                    ->orWhere('user_roles.role', 'like', '%sales manager%')
                    ->orWhere('user_roles.role', 'like', '%sales executive%')
                    ->orWhereNull('user_roles.role');
            })
            ->orderBy('user_details.fullname')
            ->distinct()
            ->get();
    }
}
