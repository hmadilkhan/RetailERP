<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
class ReportPanel extends Component
{
    #[Title('Report Panel')]
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
                    ['name' => 'Product Sales Report', 'key' => 'product-sales-report'],
                    ['name' => 'Customer Sales Summary', 'key' => 'customer-sale-summary'],
                    ['name' => 'Order Timing Summary', 'key' => 'order-timing-summary'],
                ],
            ],
            [
                'name' => 'Profit & Loss',
                'reports' => [
                    ['name' => 'P&L Summary', 'key' => 'pl-summary'],
                    ['name' => 'P&L Detailed', 'key' => 'pl-detailed'],
                ],
            ],
            [
                'name' => 'Expense',
                'reports' => [
                    ['name' => 'Expense Category', 'key' => 'expense-category'],
                    ['name' => 'Expense Detailed', 'key' => 'expense-detailed'],
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
