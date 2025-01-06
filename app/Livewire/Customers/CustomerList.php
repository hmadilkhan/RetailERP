<?php

namespace App\Livewire\Customers;

use App\Customer;
use App\Services\BranchService;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use WithPagination;

    #[Title("Customer List")]

    public $id;
    public $name;
    public $contact;
    public $membership;
    public $branch;
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

    #[On("searchCustomer")]
    public function searchCustomer($branch,$name,$contact,$membership)
    {
        $this->branch = $branch;
        $this->name = $name;
        $this->contact = $contact;
        $this->membership = $membership;
        $this->applyFilter();
    }

    public function render(Customer $customer,BranchService $branchService)
    {
       
        $customers = $customer->getCustomersForLivewire($this->id, $this->name, $this->status,$this->pageNo,$this->branch,$this->contact,$this->membership);
        $branches = $branchService->getBranches();
        return view('livewire.customers.customer-list', compact('customers','branches'));
    }
}
