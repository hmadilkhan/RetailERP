<?php

namespace App\Livewire;

use App\inventory;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ViewInventory extends Component
{
    use WithPagination;

    // #[Layout('layouts.master-layout')] 
    public function render()
    {
        return view('livewire.view-inventory',[
            'inventories' => Inventory::getData(),
        ]);
    }
}
