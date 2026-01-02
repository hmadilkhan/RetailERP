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

    // New properties for date filtering and declarations
    public $salesDateFrom;
    public $salesDateTo;
    public $modalDeclarations = [];
    public $declarationDetails = [];
    public $terminalName = '';
    public $terminalPermissions = [];
    public $selectedTerminalId = null;

    public function mount(dashboard $dash)
    {
        $this->dateFrom = date('Y-m-d');
        $this->dateTo = date('Y-m-d');
        $this->salesDateFrom = date('Y-m-d');
        $this->salesDateTo = date('Y-m-d');
        $this->loadData($dash);
        $this->modalBranches = $dash->branches($this->salesDateFrom, $this->salesDateTo);
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

    public function updatedSalesDateFrom()
    {
        $dash = new dashboard();
        $this->modalBranches = $dash->branches($this->salesDateFrom, $this->salesDateTo);
        // If we are in 'branches' view, the list updates automatically.
        // If in 'declarations' view, we might need to refresh declarations if a terminal is selected.
        if ($this->modalView === 'declarations' && $this->selectedTerminalId) {
            $this->modalDeclarations = $dash->getDeclarationsByDateRange($this->selectedTerminalId, $this->salesDateFrom, $this->salesDateTo);
        }
    }

    public function updatedSalesDateTo()
    {
        $this->updatedSalesDateFrom();
    }

    public function selectBranch($branchId, $status)
    {
        $this->selectedBranchId = $branchId;
        $dash = new dashboard();
        // Since terminals are structural and not date-dependent (usually), we keep retrieving them as is.
        // However, if terminal sales *stats* in the list depend on date, we might need to update getTerminalsByBranch.
        // For now, let's assume getTerminalsByBranch is structure-only or uses current date by default.
        // If we want date-filtered stats per terminal, we'd need to update that method too aside from this task.
        // Based on previous code, getTerminalsByBranch just does a basic select. The sales stats come from the query.

        $this->modalTerminals = $dash->getTerminalsByBranch($branchId, $status);
        $this->modalView = 'terminals';
        $this->dispatch('terminals-loaded');
    }

    public function backToBranches()
    {
        $this->modalView = 'branches';
        $this->selectedBranchId = null;
        $this->selectedTerminalId = null;
    }

    public function backToTerminals()
    {
        $this->modalView = 'terminals';
        $this->selectedTerminalId = null;
    }

    public function backToDeclarations()
    {
        $this->modalView = 'declarations';
        $this->declarationDetails = [];
    }

    // New method for selecting a terminal to show its declarations
    public function selectTerminal($terminalId)
    {
        $this->selectedTerminalId = $terminalId;
        $dash = new dashboard();

        // Fetch declarations for this terminal within the selected date range
        $this->modalDeclarations = $dash->getDeclarationsByDateRange($terminalId, $this->salesDateFrom, $this->salesDateTo);

        // Fetch terminal name for display
        $terminalDetails = DB::table('terminal_details')->where('terminal_id', $terminalId)->first();
        $this->terminalName = $terminalDetails ? $terminalDetails->terminal_name : 'Unknown Terminal';

        $this->modalView = 'declarations';
    }

    public function selectDeclaration($openingId)
    {
        $dash = new dashboard();
        $user = new \App\userDetails(); // Using fully qualified name or importing it

        // We need to determine if it's active or closed to call the right method.
        // The getDeclarationsByDateRange returns 'status' column.
        // But for simplicity, let's look at the logic in HomeController::getTerminalDetails.
        // It checks if openingId is provided.
        // Let's use getheadsDetailsFromOpeningIdForClosing for closed (status 2) and headsDetails for active (status 1).

        // Re-fetch the specific declaration to check status if not passed, or just assume passed.
        // Let's rely on the method in dashboard logic.

        // Actually, HomeController logic suggests:
        // if openingId provided -> getheadsDetailsFromOpeningIdForClosing
        // else -> headsDetails (active)

        // Since we are selecting a specific declaration (which is likely closed or the current open one):
        $this->declarationDetails = $dash->getheadsDetailsFromOpeningIdForClosing($openingId);

        // If it returns empty, it might be the active one (status 1)? 
        // getheadsDetailsFromOpeningIdForClosing query has "where a.status = 2".
        // If the user clicks an "Active" declaration (status 1) in the list, we should use headsDetails($terminalId).
        // Let's verify status first.
        $opening = DB::table('sales_opening')->where('opening_id', $openingId)->first();

        if ($opening && $opening->status == 1) {
            $this->declarationDetails = $dash->headsDetails($opening->terminal_id);
        } else {
            $this->declarationDetails = $dash->getheadsDetailsFromOpeningIdForClosing($openingId);
        }

        $this->terminalPermissions = $user->getPermission($this->selectedTerminalId); // If needed for the view

        $this->modalView = 'details';
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
        switch ($range) {
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
