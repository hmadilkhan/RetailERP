<?php

namespace App\Livewire\Orders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\OrderMode;
use App\Models\ServiceProvider;
use App\Models\Terminal;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class PreOrderBooking extends Component
{
    protected $listeners = ['refreshComponent' => '$refresh'];

    #[Title("Pre Order Booking")]

    public $customerText = "";
    public $branchId = "";
    public $terminalId = "";
    public $customers = [];
    public $terminals = [];
    public $salesPersons = [];
    public $orderItems = [];
    public $branches = [];
    public $orderTypes = [];
    public $orderTypeId = "";
    public $customerId = "";

    // ORDER ITEMS MODELS
    public $productId = "";
    public $qty = 0;
    public $price = 0;

    public function mount()
    {
        $this->customers = []; //Customer::where("company_id", session("company_id"))->get();
        // Fetch essential data only on component mount
        $this->branches = Branch::where("company_id", session("company_id"))->get();
    }

    #[Computed()]
    public function updatedBranchId($value)
    {
        if ($this->branchId != "") {
            $this->terminals = Terminal::where("branch_id", $value)->get();
            $this->salesPersons = ServiceProvider::with("serviceprovideruser")->where("branch_id", $value)->where("categor_id", 1)->where("status_id", 1)->get();
        }
    }

    // // Computed property to get customers filtered by search text
    // public function getCustomersProperty()
    // {
    //     return Customer::where("company_id", session("company_id"))
    //         ->when($this->customerText, function ($query) {
    //             $query->where("name", "like", "%{$this->customerText}%");
    //         })
    //         ->limit(10)  // Limit results for performance
    //         ->get();
    // }

    // // Computed property to get terminals for the selected branch
    // public function getTerminalsProperty()
    // {
    //     return $this->branchId ? Terminal::where("branch_id", $this->branchId)->get() : [];
    // }

    // // Computed property to get sales persons for the selected branch
    // public function getSalesPersonsProperty()
    // {
    //     return $this->branchId
    //         ? ServiceProvider::with("serviceprovideruser")
    //         ->where("branch_id", $this->branchId)
    //         ->where("categor_id", 1)
    //         ->where("status_id", 1)
    //         ->get()
    //         : [];
    // }

    #[On('addItems')]
    public function addItems($productId, $productName, $qty, $price) //$productId, $qty, $price
    {
        $item = [
            "productId" => $productId,
            "productName" => trim($productName),
            "qty" => $qty,
            "price" => $price,
            "amount" => $qty * $price,
        ];

        $this->orderItems[] = $item; // Append new item to orderItems array
    }

    public function render()
    {
        $orderTypes = []; //OrderMode::all();
        $branches = []; //Branch::where("company_id", session("company_id"))->get();
        $products = Inventory::where("company_id", session('company_id'))->where("status", 1)->get();
        return view('livewire.orders.pre-order-booking', [
            'orderTypes' => $orderTypes,
            'branches' => $this->branches,
            'customers' => $this->customers,
            'terminals' => $this->terminals,
            'salesPersons' => $this->salesPersons,
            'products' => $products,
        ]);
    }
}
