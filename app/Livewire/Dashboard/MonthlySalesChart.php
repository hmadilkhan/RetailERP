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

    function generateHSLColor($index, $total)
    {
        $hue = ($index / $total) * 360; // Spread hues evenly across 360 degrees
        return "hsla($hue, 70%, 50%, 0.8)"; // Adjust saturation and lightness as desired
    }
    public function render()
    {
        // Predefined color palette for a more consistent and eye-catching look
        $colors = [
            'rgba(54, 162, 235, 0.8)',  // Blue
            'rgba(255, 99, 132, 0.8)',  // Red
            'rgba(75, 192, 192, 0.8)',  // Teal
            'rgba(255, 206, 86, 0.8)',  // Yellow
            'rgba(153, 102, 255, 0.8)', // Purple
            'rgba(255, 159, 64, 0.8)',  // Orange
            'rgba(201, 203, 207, 0.8)', // Grey
            'rgba(46, 204, 113, 0.8)',  // Green
            'rgba(231, 76, 60, 0.8)',   // Strong Red
            'rgba(52, 152, 219, 0.8)'   // Light Blue
        ];
        $sevenMonthsAgo = Carbon::now()->subMonths(8)->startOfMonth()->toDateString();
        $currentMonth = Carbon::now()->endOfMonth()->toDateString();
        // Assuming `sales` table has `amount`, `created_at`, and `branch_id` fields
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

        // Organize the data for charting
        $branches = DB::table("branch")->where("company_id", session("company_id"))->get(); //$salesData->pluck('branch_id')->unique()->values(); // Unique branch IDs
        $months = $salesData->pluck('month')->unique()->values(); // Unique months in order
        $monthNames = $salesData->pluck('month')->unique()->values()->map(function ($month) {
            return \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M-Y'); // Format as "Jun-2024"
        });

        $totalBranches = count($branches);
        $chartData = [];
        $colorIndex = 0;
        foreach ($branches as $index => $branch) {
            $branchSales = [];
            foreach ($months as $month) {
                // Get total sales for this branch and month, or 0 if none
                $salesForMonth = $salesData->firstWhere(function ($data) use ($branch, $month) {
                    return $data->branch_id == $branch->branch_id && $data->month == $month;
                });
                $branchSales[] = $salesForMonth ? $salesForMonth->total_sales : 0;
            }
            // Counter for cycling through colors

            $chartData[] = [
                'label' => "Branch : $branch->branch_name", // You can customize this label if needed
                'data' => $branchSales,
                'backgroundColor' => $colors[$colorIndex % count($colors)],
                'borderColor' => 'rgba(0, 0, 0, 0.1)',
                'borderWidth' => 1
            ];
            $colorIndex++;
        }

        return view('livewire.dashboard.monthly-sales-chart', compact('chartData', 'months', 'monthNames'));
    }
}
