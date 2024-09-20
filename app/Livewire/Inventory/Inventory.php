<?php

namespace App\Livewire\Inventory;

use Livewire\Attributes\Title;
use Livewire\Component;

class Inventory extends Component
{
    #[Title("Create Inventory")]
    public function render()
    {
        return view('livewire.inventory.inventory');
    }
}
