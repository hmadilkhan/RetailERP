<?php

namespace App\Livewire\Dashboard;

use App\dashboard;
use Livewire\Component;

class YearlySalesChart extends Component
{
    public $year;

    public function mount(dashboard $dash)
    {
        $this->year = $dash->getYearlySales();
    }

    public function render()
    {
        return view('livewire.dashboard.yearly-sales-chart');
    }
}
