<?php

namespace App\Livewire\StockAdjustment;

use App\Services\BranchService;
use App\Services\StockAdjustmentService;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ListAdjustmentV2 extends Component
{
    use WithPagination;

    #[Title("Stock Adjustment")]

    public $from = '';
    public $to = '';
    public $code = '';
    public $name = '';
    public $branch = '';

    public function submitForm($from, $to, $code, $name, $branch)
    {
        $this->from = $from;
        $this->to = $to;
        $this->code = $code;
        $this->name = $name;
        $this->branch = $branch;
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function clear()
    {
        $this->from = null;
        $this->to = null;
        $this->code = null;
        $this->name = null;
        $this->branch = null;
        $this->applyFilters();
    }

    public function render(BranchService $branchService, StockAdjustmentService $stockAdjustmentService)
    {
        $branches = $branchService->getBranches();
        $stocks = $stockAdjustmentService->getStockAdjustmentLists($this->from, $this->to, $this->code, $this->name, $this->branch);

        return view('livewire.stock-adjustment.list-adjustment-v2', compact('branches', 'stocks'))
            ->extends('layouts.master-tailwind');
    }
}
