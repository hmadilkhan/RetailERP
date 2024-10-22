<?php

namespace App\Livewire;

use App\Models\InventoryStock;
use Livewire\Attributes\On;
use Livewire\Component;

class StockReportDetail extends Component
{
    public $index;
    public $branch = '';
    public $from;
    public $to;
    public $moreDetails = [];
    public $moreDetailsVisible = [];

    protected $listeners = ['loadMoreDetails'];

   
    // Listener for toggling the visibility of more details
    public function toggleDetails($productId, $index)
    {
        $this->index = $index;
        // Check if the visibility state is already set for the row
        if (isset($this->moreDetailsVisible[$index])) {
            // Toggle visibility
            $this->moreDetailsVisible[$index] = !$this->moreDetailsVisible[$index];
        } else {
            // Load additional details for the first time and set visibility to true
            $this->loadMoreDetails($productId, $index);
            $this->moreDetailsVisible[$index] = true;
        }
    }

    #[On('loadMoreDetails')]
    public function loadMoreDetails($productId, $index)
    {
        if ($this->branch == "") {
            $this->branch = 283;
        }
        // Query additional details based on the stockId
        $stock = InventoryStock::where("product_id", $productId)->where("branch_id", $this->branch)->whereBetween("date", [$this->from, $this->to])->get(); // Replace with your actual query logic

        // Load additional information (this could be another related query)
        $additionalDetails = [
            'stock' => $stock, // Replace with actual data
        ];

        // Store additional details in the array for the given index
        $this->moreDetails[$index] = $additionalDetails;
        // dd($productId);
    }

    public function render()
    {
        return view('livewire.stock-report-detail');
    }
}
