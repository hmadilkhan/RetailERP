<?php

namespace App\Livewire\Dashboard;

use App\dashboard;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;

class PremiumDashboard extends Component
{
    #[Title('Dashboard')]
    public $products;
    public $totalstock;
    public $months;
    public $orders;
    public $branches;
    public $totalSales;
    public $projected;
    public $permission;
    public $year;
    public $sales;
    public $dateFrom;
    public $dateTo;
    public $lowStockProducts;
    public $profitMargin;
    public $topCustomers;
    public $salesComparison;
    public $selectedRange = '';
    public $showSalesModal = false;
    public $modalBranches = [];
    public $modalTerminals = [];
    public $selectedBranchId = null;
    public $modalView = 'branches';

    public function mount(dashboard $dash)
    {
        $this->dateFrom = date('Y-m-d');
        $this->dateTo = date('Y-m-d');
        $this->loadData($dash);
        $this->modalBranches = $dash->branches();
    }

    public function openSalesModal()
    {
        $this->showSalesModal = true;
        $this->modalView = 'branches';
    }

    public function closeSalesModal()
    {
        $this->showSalesModal = false;
        $this->modalView = 'branches';
        $this->selectedBranchId = null;
    }

    public function selectBranch($branchId, $status)
    {
        $this->selectedBranchId = $branchId;
        $dash = new dashboard();
        $this->modalTerminals = $dash->getTerminalsByBranch($branchId, $status);
        $this->modalView = 'terminals';
    }

    public function backToBranches()
    {
        $this->modalView = 'branches';
        $this->selectedBranchId = null;
    }

    public function loadData($dash = null)
    {
        if (!$dash) $dash = new dashboard();
        
        $this->products = $dash->getMostSalesProduct();
        $this->totalstock = $dash->getTotalItems();
        $this->months = $dash->getMonthsSales();
        $this->orders = $dash->orderStatus();
        $this->branches = $dash->branches();
        $this->totalSales = $this->getFilteredSales();
        $this->permission = $dash->dashboardRole();
        $this->projected = $dash->getProjectedSales();
        $this->year = $dash->getYearlySales();
        $this->sales = $dash->sales();
        $this->lowStockProducts = $this->getLowStockProducts();
        $this->profitMargin = $this->getProfitMargin();
        $this->topCustomers = $this->getTopCustomers();
        $this->salesComparison = $this->getSalesComparison();
    }

    public function setDateRange($range)
    {
        $this->selectedRange = $range;
        switch($range) {
            case 'today':
                $this->dateFrom = $this->dateTo = date('Y-m-d');
                break;
            case 'yesterday':
                $this->dateFrom = $this->dateTo = date('Y-m-d', strtotime('-1 day'));
                break;
            case 'this_week':
                $this->dateFrom = date('Y-m-d', strtotime('monday this week'));
                $this->dateTo = date('Y-m-d');
                break;
            case 'last_week':
                $this->dateFrom = date('Y-m-d', strtotime('monday last week'));
                $this->dateTo = date('Y-m-d', strtotime('sunday last week'));
                break;
            case 'this_month':
                $this->dateFrom = date('Y-m-01');
                $this->dateTo = date('Y-m-d');
                break;
            case 'last_month':
                $this->dateFrom = date('Y-m-01', strtotime('first day of last month'));
                $this->dateTo = date('Y-m-t', strtotime('last day of last month'));
                break;
        }
        $this->applyFilter();
    }

    public function applyFilter()
    {
        $this->loadData();
    }

    public function exportData()
    {
        $data = [
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'totalSales' => $this->totalSales[0]->TotalSales ?? 0,
            'profitMargin' => $this->profitMargin,
            'topCustomers' => $this->topCustomers,
        ];
        
        session()->flash('message', 'Export functionality ready!');
    }

    private function getFilteredSales()
    {
        if (session("roleId") == 2) {
            return DB::select("SELECT SUM(a.total_amount) as TotalSales FROM sales_receipts a WHERE a.date BETWEEN ? AND ? AND a.branch IN (SELECT branch_id FROM branch WHERE company_id = ?) AND a.status != 12", [$this->dateFrom, $this->dateTo, session("company_id")]);
        } else {
            return DB::select("SELECT SUM(a.total_amount) as TotalSales FROM sales_receipts a WHERE a.date BETWEEN ? AND ? AND a.branch = ? AND a.status != 12", [$this->dateFrom, $this->dateTo, session("branch")]);
        }
    }

    private function getLowStockProducts()
    {
        $branches = DB::table('branch')
            ->where('company_id', session('company_id'))
            ->pluck('branch_id');

        return DB::table('inventory_general as ig')
            ->join(DB::raw('(SELECT product_id, SUM(balance) as balance_qty FROM inventory_stock WHERE branch_id IN (' . $branches->implode(',') . ') AND status_id = 1 GROUP BY product_id) as ist'), 'ig.id', '=', 'ist.product_id')
            ->where('ig.company_id', session('company_id'))
            ->whereRaw('ist.balance_qty < 10')
            ->orderBy('ist.balance_qty', 'ASC')
            ->limit(5)
            ->get(['ig.product_name', 'ist.balance_qty', 'ig.item_code']);
    }

    private function getProfitMargin()
    {
        $revenue = DB::table('sales_receipts')
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->where('status', '!=', 12)
            ->sum('total_amount');

        $cost = DB::table('sales_receipt_details')
            ->join('sales_receipts', 'sales_receipts.id', '=', 'sales_receipt_details.receipt_id')
            ->whereBetween('sales_receipts.date', [$this->dateFrom, $this->dateTo])
            ->where('sales_receipts.status', '!=', 12)
            ->sum('sales_receipt_details.total_cost');

        return [
            'revenue' => $revenue,
            'cost' => $cost,
            'profit' => $revenue - $cost,
            'margin' => $revenue > 0 ? (($revenue - $cost) / $revenue) * 100 : 0
        ];
    }

    private function getTopCustomers()
    {
        return DB::table('customers')
            ->join('sales_receipts', 'sales_receipts.customer_id', '=', 'customers.id')
            ->whereBetween('sales_receipts.date', [$this->dateFrom, $this->dateTo])
            ->where('sales_receipts.status', '!=', 12)
            ->select('customers.name', 'customers.mobile', DB::raw('SUM(sales_receipts.total_amount) as total'))
            ->groupBy('customers.id', 'customers.name', 'customers.mobile')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get();
    }

    private function getSalesComparison()
    {
        $today = DB::table('sales_receipts')
            ->whereDate('date', date('Y-m-d'))
            ->where('status', '!=', 12)
            ->sum('total_amount');

        $yesterday = DB::table('sales_receipts')
            ->whereDate('date', date('Y-m-d', strtotime('-1 day')))
            ->where('status', '!=', 12)
            ->sum('total_amount');

        $change = $yesterday > 0 ? (($today - $yesterday) / $yesterday) * 100 : 0;

        return [
            'today' => $today,
            'yesterday' => $yesterday,
            'change' => $change,
            'isPositive' => $change >= 0
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.premium-dashboard');
    }
}
