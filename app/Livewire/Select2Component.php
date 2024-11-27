<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Attributes\On;
use Livewire\Component;

class Select2Component extends Component
{
    public $selectedOption = null;
    public $search = '';
    public $options = [];

    public function mount()
    {
        $this->options = $this->getOptions();
    }

    #[On('updatedSearch')]
    public function updatedSearch()
    {
        $this->options = $this->getOptions($this->search);
    }

    public function getOptions($search = null)
    {
        $query = Customer::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->take(10)->get()->toArray();
    }

    public function select2Updated($value)
    {
        $this->selectedOption = $value;
    }

    public function render()
    {
        return view('livewire.select2-component');
    }
}
