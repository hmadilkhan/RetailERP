<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\InventoryStock;
use App\Services\BranchService;
use App\Services\StockAdjustmentService;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class StockReport extends Component
{
    use WithPagination;

    #[Title("Stock Adjustment")]

    protected $listeners = ['loadMoreDetails'];

    public $from = '';
    public $to = '';
    public $branch = "";

    public function mount()
    {
        $branchselect = Branch::where("company_id",session("company_id"))->first();
        $this->from = date("Y-m-d");
        $this->to = date("Y-m-d");
        $this->branch =  $branchselect->branch_id;
    }

    public function submitForm($from, $to, $branch)
    {
        $this->from = $from;
        $this->to = $to;
        $this->branch = $branch;
        // dd($branch);
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
        $stocks = $stockAdjustmentService->getStockReport($this->from, $this->to, $this->branch);
        $branches = $branchService->getBranches();
        $branch = $this->branch;
        return view('livewire.stock-report', compact('branches', 'stocks','branch'));
    }
}
