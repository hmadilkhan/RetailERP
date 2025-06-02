<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Models\SalesOpening;
use App\Models\SalesReturn;
use App\Models\Terminal;
use Livewire\Component;

class SalesReturnReport extends Component
{

    // FIELDS
    public $dateFrom = '';
    public $dateTo = '';
    public $branch = '';
    public $terminal = '';

    // ARRAYS 
    public $results = [];
    public $branches = [];
    public $terminals = [];

    // Loading state
    public $isGenerating = false;

    public function mount()
    {
        $this->branches = Branch::where("company_id", session("company_id"))->get();
    }

    public function updatedBranch($value)
    {
        if ($value) {
            $this->terminals = Terminal::where("branch_id", $value)->get();
            $this->terminal = ''; // Reset terminal selection when branch changes
        } else {
            $this->terminals = collect();
            $this->terminal = '';
        }
    }

    public function generateReport()
    {
        $this->isGenerating = true;

        $openingIds = SalesOpening::where("user_id", $this->branch)->where("terminal_id", $this->terminal)->pluck("opening_id");
        $salesReturn = SalesReturn::query();
        $salesReturn->with("order", "inventory");
        $salesReturn->whereIn("opening_id", $openingIds);

        $this->results = $salesReturn->get();

        $this->isGenerating = false;
    }

    public function render()
    {
        return view('livewire.reports.sales-return-report');
    }
}
