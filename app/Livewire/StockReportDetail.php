<?php

namespace App\Livewire;

use App\Models\InventoryStock;
use Livewire\Attributes\On;
use Livewire\Component;

class StockReportDetail extends Component
{
    public $details;
    public $name;
    protected $listeners = ['showStockModal'];

    #[On('showStockModal')]
    public function showStockModal($productId,$branch,$from,$to,$name)
    {
        $this->dispatch('show-delete-modal');
        $this->name = $name;
        $this->details = InventoryStock::where("product_id", 833630)->where("branch_id", $branch)->whereBetween("date", [$from, $to])->get();
    }

    public function render()
    {
        return view('livewire.stock-report-detail');
    }
}
