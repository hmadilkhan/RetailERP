<?php

namespace App\Livewire\Dashboard;

use App\dashboard;
use Livewire\Component;

class TerminalSalesChart extends Component
{
    public $sales;

    public function mount(dashboard $dash)
    {
        $this->sales = $dash->sales();
    }

    public function render()
    {
        return view('livewire.dashboard.terminal-sales-chart');
    }
}
