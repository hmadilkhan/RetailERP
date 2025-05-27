<?php

namespace App\Livewire\Reports;

use App\expense_category;
use App\report;
use Livewire\Component;

class ExpenseCategory extends Component
{
    // FIELDS
    public $dateFrom = '';
    public $dateTo = '';

    // ARRAYS 
    public $results = [];

    // Loading state
    public $isGenerating = false;

    public function generateReport(report $report)
    {
        $this->isGenerating = true;

        $this->results = $report->expenses_details($this->dateFrom, $this->dateTo, "");

        $this->isGenerating = false;
    }

    public function render()
    {
        return view('livewire.reports.expense-category');
    }
}
