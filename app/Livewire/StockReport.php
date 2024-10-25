<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\InventoryDepartment;
use App\Models\InventoryStock;
use App\Models\InventorySubDepartment;
use App\Services\BranchService;
use App\Services\StockAdjustmentService;
use Livewire\Attributes\Computed;
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
    public $department = "";
    public $subdepartment = "";
    public $subDepartmentLists = [];

    public function mount()
    {
        $branchselect = Branch::where("company_id", session("company_id"))->first();
        $this->from = date("Y-m-d");
        $this->to = date("Y-m-d");
        $this->branch =  $branchselect->branch_id;
        if ($this->department != "" && $this->department != "all") {
            $this->subDepartmentLists = InventorySubDepartment::where("department_id", $this->department)->where("status", 1)->get();
        }
    }

    #[Computed()]
    public function updatedDepartment($department)
    {
        if ($department != "" && $department != "all") {
            $this->subDepartmentLists = InventorySubDepartment::where("department_id", $department)->where("status", 1)->get();
            $this->subdepartment = "";
        }
    }

    public function submitForm($from, $to, $branch, $department, $subdepartment)
    {
        $this->from = $from;
        $this->to = $to;
        $this->branch = $branch;
        $this->department = $department;
        $this->subdepartment = $subdepartment;
        // dd($branch);
    }

    public function excelExport($from, $to, $branch, $department, $subdepartment)
    {
        $this->from = $from;
        $this->to = $to;
        $this->branch = $branch;
        $this->department = $department;
        $this->subdepartment = $subdepartment;
        dd($branch);
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
        $stocks = $stockAdjustmentService->getStockReport($this->from, $this->to, $this->branch, $this->department,$this->subdepartment);
        $branches = $branchService->getBranches();
        $departments = InventoryDepartment::where("company_id", session("company_id"))->where("status", 1)->get();
        $branch = $this->branch;
        return view('livewire.stock-report', compact('branches', 'stocks', 'branch', 'departments'));
    }
}
