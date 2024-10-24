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
    public $checkboxValue;
    public $checkboxText = "Show In-Active";
    public $pageNo = 10;

    public function applyFilter()
    {
        // Reset the pagination when filters change
        $this->resetPage();
    }

    public function updatedPageNo()
    {
        $this->resetPage();
    }

    public function updatedCheckboxValue()
    {
        // $this->status = 1;
        if ($this->status == 1) {
            $this->status = 2;
        }else{
            $this->status = 1;
        }
        dd($this->status);
    }

    public function render(Customer $customer)
    {
       
        $customers = $customer->getCustomersForLivewire($this->id, $this->name, $this->status,$this->pageNo);
        return view('livewire.customers.customer-list', compact('customers'));
    }
}
