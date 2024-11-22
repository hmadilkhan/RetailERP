<?php

namespace App\Livewire\Orders;

use App\Models\Inventory;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy()]
class OrderItem extends Component
{
    public $productId = "";
    public $qty = 0;
    public $price = 0;

    public function sendDataToParent($productId, $productName, $qty, $price) //$productId, $qty, $price
    {
        $this->dispatch('addItems', $productId, $productName, $qty, $price);
    }

    public function render()
    {
        $products = Inventory::where("company_id", session('company_id'))->where("status", 1)->get();
        return view('livewire.orders.order-item',[
            'products' => $products,
        ]);
    }
}
