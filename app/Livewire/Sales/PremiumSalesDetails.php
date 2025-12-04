<?php

namespace App\Livewire\Sales;

use App\dashboard;
use Livewire\Component;
use Illuminate\Support\Facades\Crypt;

class PremiumSalesDetails extends Component
{
    public $branches = [];
    public $branchesClosedSales = [];
    public $permission = [];
    public $selectedTab = 'active';
    public $selectedDate;

    public function mount(dashboard $dash)
    {
        $this->branches = $dash->branches();
        $this->branchesClosedSales = $dash->branchesForClosedSales();
        $this->selectedDate = date('Y-m-d', strtotime('-1 days'));
    }

    public function switchTab($tab)
    {
        $this->selectedTab = $tab;
    }

    public function render()
    {
        return view('livewire.sales.premium-sales-details');
    }
}
