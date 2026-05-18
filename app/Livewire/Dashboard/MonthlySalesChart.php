<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MonthlySalesChart extends Component
{
    public function placeholder()
    {
        return <<<'HTML'
        <div class="card">
            <div class="card-body">
                <div wire:loading.class="d-flex flex-column" wire:loading >
                    <div class='position-relative w-100 h-100 d-flex flex-column align-items-center bg-white justify-content-center'>
                        <div class='spinner-border text-dark' role='status'>
                            <span class='visually-hidden'>Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        HTML;
    }

    public function render()
    {
        $colors = [
            '#4CAF50',
            '#2196F3',
            '#FFC107',
            '#00BCD4',
            '#F44336',
            '#78909C',
        ];

        $sevenMonthsAgo = Carbon::now()->subMonths(8)->startOfMonth()->toDateString();
        $currentMonth = Carbon::now()->endOfMonth()->toDateString();

        $salesData = DB::table('sales_receipts')
            ->select(
                DB::raw('DATE_FORMAT(sales_receipts.date, "%Y-%m") as month'),
                'branch.branch_name',
                'branch.branch_id',
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->join("branch", "branch.branch_id", "=", "sales_receipts.branch")
            ->whereBetween('sales_receipts.date', [$sevenMonthsAgo, $currentMonth])
            ->whereIn("sales_receipts.branch", DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"))
            ->whereNotIn("sales_receipts.status", [12, 14, 5])
            ->groupBy('month', 'sales_receipts.branch')
            ->orderBy('month')
            ->get();

        $monthRange = collect(range(8, 0))->map(function ($monthsBack) {
            return Carbon::now()->subMonths($monthsBack)->format('Y-m');
        })->values();

        $monthNames = $monthRange->map(function ($month) {
            return Carbon::createFromFormat('Y-m', $month)->format('M-y');
        });

        $branchTotals = $salesData
            ->groupBy('branch_id')
            ->map(function ($rows) {
                return [
                    'branch_id' => $rows->first()->branch_id,
                    'branch_name' => $rows->first()->branch_name,
                    'total' => (float) $rows->sum('total_sales'),
                ];
            })
            ->sortByDesc('total')
            ->values();

        $topBranches = $branchTotals->take(5);
        $otherBranchIds = $branchTotals->slice(5)->pluck('branch_id')->all();

        $chartData = $topBranches->map(function ($branch, $index) use ($salesData, $monthRange, $colors) {
            return [
                'label' => $branch['branch_name'],
                'data' => $monthRange->map(function ($month) use ($salesData, $branch) {
                    return (float) $salesData
                        ->where('branch_id', $branch['branch_id'])
                        ->where('month', $month)
                        ->sum('total_sales');
                })->values(),
                'backgroundColor' => $colors[$index % count($colors)],
                'borderColor' => $colors[$index % count($colors)],
            ];
        })->values();

        if (!empty($otherBranchIds)) {
            $otherIndex = $chartData->count();
            $chartData->push([
                'label' => 'Other Branches',
                'data' => $monthRange->map(function ($month) use ($salesData, $otherBranchIds) {
                    return (float) $salesData
                        ->whereIn('branch_id', $otherBranchIds)
                        ->where('month', $month)
                        ->sum('total_sales');
                })->values(),
                'backgroundColor' => $colors[$otherIndex % count($colors)],
                'borderColor' => $colors[$otherIndex % count($colors)],
            ]);
        }

        $totalSales = (float) $salesData->sum('total_sales');
        $avgSales = $monthRange->count() > 0 ? $totalSales / $monthRange->count() : 0;
        $topBranchName = $branchTotals->first()['branch_name'] ?? 'N/A';

        $summary = [
            'totalSales' => $totalSales,
            'avgSales' => $avgSales,
            'topBranch' => $topBranchName,
            'branchCount' => $branchTotals->count(),
        ];

        return view('livewire.dashboard.monthly-sales-chart', compact('chartData', 'monthNames', 'summary'));
    }
}
