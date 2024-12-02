<?php

namespace App\Livewire\Orders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderAccount;
use App\Models\OrderDetails;
use App\Models\OrderMode;
use App\Models\OrderPayment;
use App\Models\OrderSubAccount;
use App\Models\ServiceProvider;
use App\Models\Terminal;
use App\tax;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class PreOrderBooking extends Component
{
    #[Title("Pre Order Booking")]

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'fetchSelect2Options' => 'fetchOptions',
        'select2Updated' => 'updateSelectedCustomer'
    ];

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
    public $paymentId = "";
    public $errorMessage = "";
    public $taxValue = "";
    public $discountType = "";
    public $discountText = "";
    public $discountValue = "";

    // ORDER ITEMS MODELS
    public $productId = "";
    public $qty = 0;
    public $price = 0;

    // ORDER ITEMS TOTALS
    public $subTotal = 0;
    public $discount = 0;
    public $taxAmount = 0;
    public $totalAmount = 0;

    public $selectedOption = null;
    public $options = [];

    protected $rules = [
        "customerId" => "required",
        "orderTypeId" => "required",
        "paymentId" => "required",
        "branchId" => "required",
        "terminalId" => "required",
    ];

    public function mount()
    {
        $this->branches = Branch::where("company_id", session("company_id"))->get();
        $this->orderTypes = OrderMode::all();
        $this->discountType = "percentage";
        $this->discountText = "Enter Percentage";
        // dd($this->discountType);
    }

    public function hydrate()
    {
        $this->dispatch("reinitializeSelect2");
        // dd("Hydrate");
    }


    public function fetchOptions($search = null)
    {
        $query = Customer::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $results = $query->take(10)->get(['id', 'name']);
        $this->options = $results ? $results->toArray() : [];

        // Emit the options back to JavaScript
        $this->dispatch('select2OptionsFetched', ['options' => $this->options]);
    }

    public function updateSelectedCustomer($value)
    {
        $this->customerId = $value;
    }

    public function updatedDiscountType($value)  
    {
        $this->discountType = $value;
        if ($value == "percentage") {
            $this->discountText = "Enter Percentage";
        }else if ($value == "amount") {
            $this->discountText = "Enter Amount";
        }
        $this->calculateTotal();
    }

    public function updatedDiscountValue($value)
    {
        $this->discount = $value;
        $this->calculateTotal();
    }

    public function updatedBranchId($value)
    {
        if ($this->branchId != "") {
            $this->terminals = Terminal::where("branch_id", $value)->get();
            $this->salesPersons = ServiceProvider::with("serviceprovideruser")->where("branch_id", $value)->where("categor_id", 1)->where("status_id", 1)->get();
        }
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

    public function updatedTaxValue($value)
    {
        $this->taxValue = $value;
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->resetCalculationControls();

        collect($this->orderItems)->map(function ($item) {
            $this->subTotal += $item["amount"];
        });
        if ($this->taxValue != "") {
           $taxvalue = $this->taxValue / 100;
           $this->taxAmount  = $this->subTotal *  $taxvalue;
        }
        
        if ($this->discountType == "percentage") {
            $this->discount = $this->subTotal * ((float) $this->discountValue / 100);
        }else{
            $this->discount = (float) $this->discountValue;

        }
       
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

        $this->orderItems = array_values(array_filter($this->orderItems, function ($item) use ($productId) {
            return $item['productId'] !== $productId;
        }));

        $this->calculateTotal();
    }

    #[On('placeOrder')]
    public function placeOrder($customerId, $type, $branchId, $terminalId, $salesPersonId, $paymentId)
    {
        $this->validate();

        $this->dispatch("reinitializeSelect2");

        if (count($this->orderItems) == 0) {
            $this->addError('orderItems', 'Please select items.');
            // $this->dispatch('reinitializeSelect2'); // Emit custom event
            return; // Stop execution and return control to Livewire
        }

        try {
            DB::beginTransaction();
            $order = Order::create([
                "receipt_no" => date("YmdHis"),
                "order_mode_id" => $type,
                "userid" => auth()->user()->id,
                "customer_id" => $customerId,
                "payment_id" => $paymentId,
                "actual_amount" => $this->subTotal,
                "total_amount" => $this->totalAmount,
                "total_item_qty" => count($this->orderItems),
                "status" => 1,
                "branch" => $branchId,
                "terminal_id" => $terminalId,
                "sales_person_id" => $salesPersonId,
                "date" => date("Y-m-d"),
                "time" => date("H:i:s"),
            ]);
            foreach ($this->orderItems as $key => $item) {
                OrderDetails::create([
                    "receipt_id" => $order->id,
                    "item_code" => $item["productId"],
                    "total_qty" => $item["qty"],
                    "total_amount" => $item["amount"],
                    "item_price" => $item["price"],
                    "item_name" => $item["productName"],
                ]);
            }
            OrderAccount::create([
                "receipt_id" => $order->id,
                "receive_amount" => $this->subTotal,
                "amount_paid_back" => 0,
                "total_amount" => $this->totalAmount,
            ]);
            OrderSubAccount::create([
                "receipt_id" => $order->id,
                "discount_amount" => 0,
                "sales_tax_amount" => 0,
            ]);
            DB::commit();
            dd($order->id . " has been placed");
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }
    }

    public function render()
    {
        // $this->dispatch('childRendered'); // Emit event after child render
        $products = Inventory::where("company_id", session('company_id'))->where("status", 1)->get();
        $payments = OrderPayment::all();
        $taxes = tax::where("company_id", session('company_id'))->where("status_id", 1)->where("show_in_pos", 1)->get();
        return view('livewire.orders.pre-order-booking', [
            'branches' => $this->branches,
            'customers' => $this->customers,
            'products' => $products,
            'payments' => $payments,
            'options' => is_array($this->options) ? $this->options : [],
            'taxes' => $taxes,
        ]);
    }
}
