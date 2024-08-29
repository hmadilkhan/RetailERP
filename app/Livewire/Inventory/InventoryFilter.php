<?php

namespace App\Livewire\Inventory;

use App\inventory;
use App\Models\InventorySubDepartment;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class InventoryFilter extends Component
{
    public $code = '';
    public $name = '';
    public $dept = '';
    public $sdept = '';
    public $rp = '';
    public $ref = '';
    public $status = '';
    public $nonstock = '';
    public $subDepartments = [];

    #[Computed()]
    public function updatedDept($departmentId)
    {
        $this->subDepartments = InventorySubDepartment::where('department_id', $departmentId)->get();
        $this->sdept = null; // reset sub department selection
    }

    public function applyFilters()
    {
        // Emit an event with the filter data
        $this->dispatch('filtersUpdated', [
            'code' => $this->code,
            'name' => $this->name,
            'dept' => $this->dept,
            'sdept' => $this->sdept,
            'rp' => $this->rp,
            'ref' => $this->ref,
            'status' => $this->status,
            'nonstock' => $this->nonstock,
        ]);
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
        $this->nonstock = null;
        $this->subDepartments = [];
        $this->applyFilters();
    }

    public function render(inventory $inventory)
    {
        $departments = $inventory->department();
        $references = DB::select("SELECT * FROM `inventory_reference` where product_id IN (Select id from inventory_general where company_id = ?) and refrerence != '' GROUP by refrerence", [session('company_id')]);
        return view('livewire.inventory.inventory-filter', compact("references", "departments"));
    }
}
