<?php

namespace App\Livewire\Inventory;

use App\inventory as AppInventory;
use App\Models\InventorySubDepartment;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Inventory extends Component
{
    use WithPagination;
    // public $inventories = [];
    public $code = '';
    public $name = '';
    public $dept = '';
    public $sdept = '';
    public $rp = '';
    public $ref = '';
    public $status = '';
    public $nonstockChecked = false;
    public $inactiveChecked = false;
    public $subDepartments = [];

    public function applyFilters()
    {
        // Reset the pagination when filters change
        $this->resetPage();
    }

    #[Computed()]
    public function updatedDept($departmentId)
    {
        $this->subDepartments = InventorySubDepartment::where('department_id', $departmentId)->get();
        $this->sdept = null; // reset sub department selection
    }

    public function clear()
    {
        $this->code = null;
        $this->name = null;
        $this->dept = null;
        $this->sdept = null;
        $this->rp = null;
        $this->ref = null;
        $this->status = null;
        $this->nonstockChecked = false;
        $this->inactiveChecked = false;
        $this->subDepartments = [];
        $this->applyFilters();
    }

    #[Title("Create Inventory")]
    public function render(AppInventory $inventory)
    {
        $inventories = $inventory->getInventoryForPagewiseByFiltersLivewire($this->code, $this->name, $this->dept, $this->sdept, $this->rp, $this->ref, $this->status, $this->nonstockChecked);
        $departments = $inventory->department();
        $references = DB::select("SELECT * FROM `inventory_reference` where product_id IN (Select id from inventory_general where company_id = ?) and refrerence != '' GROUP by refrerence", [session('company_id')]);
        return view('livewire.inventory.inventory', compact('inventories', 'departments', 'references'));
    }
}
