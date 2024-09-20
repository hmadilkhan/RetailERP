<?php

namespace App\Livewire\Inventory;

use Livewire\Attributes\Title;
use Livewire\Component;

class Inventory extends Component
{
    protected $listeners = ['filtersUpdated'];

    public function filtersUpdated($filters)
    {
        dd($filters);
        // Dispatch event to the second child component (InventoryList)
        $this->dispatch('livewire.inventory.inventory-list', 'applyListFilters', $filters);
    }

    #[Title("Create Inventory")]
    public function render()
    {
        return view('livewire.inventory.inventory');
    }
}
