<?php

namespace App\Livewire\Orders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Inventory;
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
    public $selectedCustomerId = null;
    public $selectedCustomerName = '';
    public $selectedCustomers = [];

    // ORDER ITEMS MODELS
    public $productId = "";
    public $qty = 0;
    public $price = 0;

    // ORDER ITEMS TOTALS
    public $subTotal = 0;
    public $discount = 0;
    public $taxAmount = 0;
    public $totalAmount = 0;

    public function mount()
    {
        // $this->customers = []; //Customer::where("company_id", session("company_id"))->get();
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

    #[Computed()]
    public function updatedCustomerText()
    {
        // Perform the search only if there is input text
        if (!empty($this->customerText)) {
            $this->customers = Customer::where("company_id", session("company_id"))
                ->when($this->customerText, function ($query) {
                    $query->where("name", "like", "%{$this->customerText}%");
                })
                ->limit(10)  // Limit results for performance
                ->get();
        } else {
            // Reset customers if input text is empty
            $this->customers = [];
        }
    }

    public function removeCustomer($id)
    {
        $this->selectedCustomers = collect($this->selectedCustomers)->reject(function ($customer) use ($id) {
            return $customer['id'] === $id;
        })->values()->toArray();
    }

    public function selectCustomer($id, $name)
    {
        if (!collect($this->selectedCustomers)->contains('id', $id)) {
            $this->selectedCustomers[] = ['id' => $id, 'name' => $name];
        }

        $this->customerText = "";
        $this->customers = [];
    }

    #[On('addItems')]
    public function addItems($productId, $productName, $qty, $price) //$productId, $qty, $price
    {
        $item = [
            "productId" => (int) $productId,
            "productName" => trim($productName),
            "qty" => $qty,
            "price" => $price,
            "amount" => $qty * $price,
        ];

        // Check if the product already exists in the orderItems array
        $key = array_search($item['productId'], array_column($this->orderItems, 'productId'));

        if ($key !== false) {
            // Update the existing item's quantity, price, and amount
            $this->orderItems[$key]['qty'] = $item['qty'];
            $this->orderItems[$key]['amount'] = $this->orderItems[$key]['qty'] * $this->orderItems[$key]['price'];
        } else {
            // Add the new item to the orderItems array
            $this->orderItems[] = $item;
        }


        $this->calculateTotal();
        $this->resetOrderControls();
        $this->dispatch("resetControls");
        $this->dispatch("itemAdded");
    }

    public function calculateTotal()
    {
        $this->resetCalculationControls();

        collect($this->orderItems)->map(function ($item) {
            $this->subTotal += $item["amount"];
        });
        $this->totalAmount = $this->subTotal + $this->taxAmount  - $this->discount;
    }

    public function resetCalculationControls()
    {
        $this->subTotal = 0;
        $this->discount = 0;
        $this->taxAmount = 0;
        $this->totalAmount = 0;
    }

    public function resetOrderControls()
    {
        $this->productId = "";
        $this->qty = 0;
        $this->price = 0;
    }

    #[On('deleteItem')]
    public function deleteItem($productId)
    {
        // Type cast productId to ensure correct matching
        $productId = (int) $productId;
        // dd($productId);
        // $item = array_values(array_filter($this->orderItems, function ($item) use ($productId) {
        //     return $item['productId'] !== $productId;
        // }));
        // dd($item);
        $this->orderItems = array_values(array_filter($this->orderItems, function ($item) use ($productId) {
            return $item['productId'] !== $productId;
        }));

        $this->calculateTotal();
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
