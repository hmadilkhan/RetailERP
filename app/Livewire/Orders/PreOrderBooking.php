<?php

namespace App\Livewire\Orders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\OrderMode;
use App\Models\ServiceProvider;
use App\Models\Terminal;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PreOrderBooking extends Component
{
    public $customerText = "";
    public $branchId = "";
    public $terminalId = "";
    public $customers = [];
    public $terminals = [];
    public $salesPersons = [];

    public function mount()
    {
        $this->customers = Customer::where("company_id", session("company_id"))->get();
    }

    #[Computed()]
    public function updatedBranchId($value)
    {
        if ($this->branchId != "") {
            $this->terminals = Terminal::where("branch_id", $this->branchId)->get();
            $this->salesPersons = ServiceProvider::with("serviceprovideruser")->where("branch_id", $this->branchId)->where("categor_id", 2)->get();
            // dd($this->salesPersons);
        }
    }

    public function render()
    {
        $orderTypes = OrderMode::all();
        $branches = Branch::where("company_id", session("company_id"))->get();
        return view('livewire.orders.pre-order-booking', compact('orderTypes', 'branches'));
    }
}
