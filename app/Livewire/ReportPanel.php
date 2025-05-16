<?php

namespace App\Livewire;

use Livewire\Component;

class ReportPanel extends Component
{
    public array $reportHeads = [];
    public ?string $activeReport = null;

    public function mount()
    {
        $this->reportHeads = [
            [
                'name' => 'Sales',
                'reports' => [
                    ['name' => 'Sales Invoices', 'key' => 'sales-general'],
                    ['name' => 'Sales Invoice Details', 'key' => 'item-sales-record'],
                ],
            ],
            [
                'name' => 'Profit & Loss',
                'reports' => [
                    ['name' => 'P&L Summary', 'key' => 'pl-summary'],
                    ['name' => 'P&L Detailed', 'key' => 'pl-detailed'],
                ],
            ],
        ];
    }

    public function selectReport(string $key)
    {
        $this->activeReport = $key;
        $this->dispatch('initializeSelect2');
    }

    public function render()
    {
        return view('livewire.report-panel');
    }
}
