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

    // ORDER ITEMS MODELS
    public $productId = "";
    public $qty = 0;
    public $price = 0;

    public function mount()
    {
        $this->customers = Customer::where("company_id", session("company_id"))->get();
    }

    #[Computed()]
    public function updatedBranchId($value)
    {
        if ($this->branchId != "") {
            $this->terminals = Terminal::where("branch_id", $value)->get();
            $this->salesPersons = ServiceProvider::with("serviceprovideruser")->where("branch_id", $value)->where("categor_id", 1)->where("status_id", 1)->get();
        }
    }

    #[On('addItems')]
    public function addItems() //$productId, $qty, $price
    {
        $item = [
            "productId" => $this->productId,
            "qty" => $this->qty,
            "price" => $this->price,
        ];

        $this->orderItems[] = $item; // Append new item to orderItems array
    }

    public function updatedOrderItems()
    {
        logger('Order items updated:', $this->orderItems);
    }

    public function render()
    {
        $orderTypes = OrderMode::all();
        $branches = Branch::where("company_id", session("company_id"))->get();
        $products = Inventory::where("company_id", session('company_id'))->where("status", 1)->get();
        return view('livewire.orders.pre-order-booking', compact('orderTypes', 'branches', 'products'));
    }
}
