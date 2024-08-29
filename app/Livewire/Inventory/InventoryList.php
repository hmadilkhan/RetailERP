<?php

namespace App\Livewire\Inventory;

use App\inventory;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryList extends Component
{
    use WithPagination;

    protected $listeners = ['filtersUpdated'];

    public $code = '';
    public $name = '';
    public $dept = '';
    public $sdept = '';
    public $rp = '';
    public $ref = '';
    public $status = '';
    public $nonstock = '';

    public function filtersUpdated($filters)
    {
        // Update the component's filter properties
        $this->code = $filters['code'];
        $this->name = $filters['name'];
        $this->dept = $filters['dept'];
        $this->sdept = $filters['sdept'];
        $this->rp = $filters['rp'];
        $this->ref = $filters['ref'];
        $this->status = $filters['status'];
        $this->nonstock = $filters['nonstock'];

        // Reset pagination to the first page when filters change
        $this->resetPage();
    }


    public function render(inventory $inventory)
    {
        $inventories = $inventory->getInventoryForPagewiseByFilters($this->code, $this->name, $this->dept, $this->sdept, $this->rp, $this->ref, $this->status, $this->nonstock);
        return view('livewire.inventory.inventory-list', compact('inventories'));
    }
}
