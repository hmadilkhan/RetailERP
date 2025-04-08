<?php

namespace App\Jobs;

use App\Models\Inventory;
use App\Models\QuickBookSetting;
use App\Services\QuickBooks\QuickBooksAuthService;
use App\Services\QuickBooks\QuickBooksCustomerService;
use App\Services\QuickBooks\QuickBooksInventoryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncQuickBooksItemsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $settings = QuickBookSetting::all(); // or only active ones

        foreach ($settings as $setting) {

            $authService = app(QuickBooksAuthService::class);
            $quickBooksService = new QuickBooksInventoryService($authService, $setting->company_id);

            $products = Inventory::where('company_id', $setting->company_id)
                ->where(function ($q) {
                    $q->where('needs_qb_insert', true)
                        ->orWhere('needs_qb_update', true)
                        ->orWhere('needs_qb_deletion', true);
                })
                ->limit(100)
                ->get();

            foreach ($products as $product) {
                try {
                    if ($product->needs_qb_deletion && $product->qb_inventory_id) {
                        // $quickBooksService->deleteCustomer($customer->qb_inventory_id);
                        // $customer->delete(); // or soft delete
                    } elseif ($product->qb_inventory_id && $product->needs_qb_update) {
                        $qbData = $this->formatQuickBooksData($product);
                        $quickBooksService->updateItem($product->qb_inventory_id, $qbData);
                        $product->update(['needs_qb_update' => false]);
                    } elseif ($product->needs_qb_insert) {
                        $qbData = $this->formatQuickBooksData($product);
                        $response = $quickBooksService->createItem($qbData);
                        if (isset($response->Id)) {
                            $product->update(['qb_inventory_id' => $response->Id, 'needs_qb_insert' => false]);
                        }
                    }
                    Log::info("QB Sync Success for Item {$product->id}: ");
                } catch (\Exception $e) {
                    Log::error("QB Sync Failed for Item {$product->id}: " . $e->getMessage());
                }
            }
        }
    }


    protected function formatQuickBooksData($product)
    {

        return [
            "Name" => $product->product_name,
            "UnitPrice" => 20,
            "IncomeAccountRef" => [
                "value" => "79",
                "name" => "Sales of Product Income"
            ],
            "ExpenseAccountRef" => [
                "value" => "80",
                "name" => "Cost of Goods Sold"
            ],
            "AssetAccountRef" => [
                "value" => "81",
                "name" => "Inventory Asset"
            ],
            "Type" => "Inventory",
            "TrackQtyOnHand" => true,
            "QtyOnHand" => 10,
            "InvStartDate" => date("Y-m-d",strtotime($product->created_at))
        ];
    }
}
