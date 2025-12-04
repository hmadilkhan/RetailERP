<?php

namespace App\Livewire\Dashboard;

use App\dashboard;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PremiumDashboard extends Component
{
    public $products;
    public $totalstock;
    public $months;
    public $year;
    public $orders;
    public $branches;
    public $sales;
    public $totalSales;
    public $projected;
    public $permission;

    public function mount(dashboard $dash)
    {
        $this->products = $dash->getMostSalesProduct();
        $this->totalstock = $dash->getTotalItems();
        $this->months = $dash->getMonthsSales();
        $this->year = $dash->getYearlySales();
        $this->orders = $dash->orderStatus();
        $this->branches = $dash->branches();
        $this->sales = $dash->sales();
        $this->totalSales = $dash->totalSales();
        $this->permission = $dash->dashboardRole();
        $this->projected = $dash->getProjectedSales();
    }

    public function render()
    {
        return view('livewire.dashboard.premium-dashboard');
    }
}
