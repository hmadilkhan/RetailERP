<?php

namespace App\Livewire;

use App\Models\InventoryStock;
use App\Services\OrderService;
use Livewire\Attributes\On;
use Livewire\Component;

class StockReportDetail extends Component
{
    public $details = [];
    public $sales = [];
    public $name;
    protected $listeners = ['showStockModal'];

    #[On('showStockModal')]
    public function showStockModal($productId,$branch,$from,$to,$name)
    {
        $orderService = new OrderService();

        $this->dispatch('show-delete-modal');
        $this->name = $name;
        $this->details = InventoryStock::where("product_id", $productId)->where("branch_id", $branch)->whereBetween("date", [$from, $to])->get();
        $this->sales = $orderService->getOrderDetailsFromItems($from,$to,$branch,$productId);
    }

    public function render()
    {
        return view('livewire.stock-report-detail');
    }
}
