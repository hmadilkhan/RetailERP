<?php

namespace App\Livewire\Dashboard;

use App\dashboard;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

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

    public function mount(dashboard $dash)
    {
        $this->products = $dash->getMostSalesProduct();
        $this->totalstock = $dash->getTotalItems();
        $this->months = $dash->getMonthsSales();
        $this->orders = $dash->orderStatus();
        $this->branches = $dash->branches();
        $this->totalSales = $dash->totalSales();
        $this->permission = $dash->dashboardRole();
        $this->projected = $dash->getProjectedSales();
        $this->year = $dash->getYearlySales();
        $this->sales = $dash->sales();
    }

    public function render()
    {
        return view('livewire.dashboard.premium-dashboard');
    }
}
