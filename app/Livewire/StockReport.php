<?php

namespace App\Livewire;

use App\Services\BranchService;
use App\Services\StockAdjustmentService;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class StockReport extends Component
{
    use WithPagination;

    #[Title("Stock Adjustment")]

    public $from = '';
    public $to = '';
    public $branch = '';
    public $stocks;

    public function mount()
    {
        $this->from = date("Y-m-d");
        $this->to = date("Y-m-d");
    }

    public function submitForm($from, $to, $branch)
    {

        $this->from = $from;
        $this->to = $to;
        $this->branch = $branch;

        $stockAdjustmentService = new StockAdjustmentService ();
        $this->stocks = $stockAdjustmentService->stockReport($this->from, $this->to, $this->branch);
        
        // Reapply filters and reset pagination
        $this->applyFilters();
    }

    public function applyFilters()
    {
        // Reset the pagination when filters change
        $this->resetPage();
    }

    public function clear()
    {
        $this->from = null;
        $this->to = null;
        $this->branch = null;
        $this->applyFilters();
    }

    public function render(BranchService $branchService, StockAdjustmentService $stockAdjustmentService)
    {
        $this->stocks = $stockAdjustmentService->stockReport($this->from, $this->to, $this->branch);
        $branches = $branchService->getBranches();
        return view('livewire.stock-report', compact('branches'));
    }
}
