<?php

namespace App\Livewire\Customers;

use App\Customer;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use WithPagination;

    #[Title("Customer List")]

    public $id;
    public $name;
    public $status = 1;

    public function applyFilter()
    {
        
    }

    public function render(Customer $customer)
    {
        $customers = $customer->getCustomersForLivewire($this->id, $this->name,$this->status);
        return view('livewire.customers.customer-list', compact('customers'));
    }
}
