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


    public function submitForm($from, $to,$branch)
    {
        $this->from = $from;
        $this->to = $to;
        $this->branch = $branch;
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

    public function render(BranchService $branchService,StockAdjustmentService $stockAdjustmentService)
    {
        $stocks = $stockAdjustmentService->stockReport();
        $branches = $branchService->getBranches();
        return view('livewire.stock-report',compact('branches','stocks'));
    }
}
