<?php

namespace App\Jobs;

use App\Models\Branch;
use App\Models\Order;
use App\Models\QuickBookSetting;
use App\Services\QuickBooks\QuickBooksAuthService;
use App\Services\QuickBooks\QuickBooksInventoryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncQuickBooksInvoiceJob implements ShouldQueue
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

            $invoices = Order::whereIn('branch', Branch::where("company_id", $setting->company_id)->pluck("branch_id"))
                ->where(function ($q) {
                    $q->where('needs_qb_insert', true)
                        ->orWhere('needs_qb_update', true)
                        ->orWhere('needs_qb_deletion', true);
                })
                ->limit(100)
                ->get();

            foreach ($invoices as $invoice) {
                try {
                    if ($invoice->needs_qb_deletion && $invoice->qb_invoice_id) {
                        // $quickBooksService->deleteCustomer($customer->qb_invoice_id);
                        // $customer->delete(); // or soft delete
                    } elseif ($invoice->qb_invoice_id && $invoice->needs_qb_update) {
                        $qbData = $this->formatQuickBooksData($invoice);
                        $quickBooksService->updateItem($invoice->qb_invoice_id, $qbData);
                        $invoice->update(['needs_qb_update' => false]);
                    } elseif ($invoice->needs_qb_insert) {
                        $qbData = $this->formatQuickBooksData($invoice);
                        $response = $quickBooksService->createItem($qbData);
                        if (isset($response->Id)) {
                            $invoice->update(['qb_invoice_id' => $response->Id, 'needs_qb_insert' => false]);
                        }
                    }
                    Log::info("QB Sync Success for invoice {$invoice->id}: ");
                } catch (\Exception $e) {
                    Log::error("QB Sync Failed for invoice {$invoice->id}: " . $e->getMessage());
                }
            }
        }
    }

    protected function formatQuickBooksData($product)
    {
        $lineItems = [];

        foreach ($product->orderdetails as $detail) {
            $lineItems[] = [
                "Amount" => (float) $detail->amount,
                "DetailType" => "SalesItemLineDetail",
                "SalesItemLineDetail" => [
                    "ItemRef" => [
                        "value" => $detail->inventory->qb_inventory_id, // assuming `item_id` refers to QB ItemRef ID
                        "name" => $detail->item_name // optional but nice if you have it
                    ]
                ]
            ];
        }
        return [
            "Line" => $lineItems,
            "CustomerRef" => [
                "value" => $product->customer->qb_customer_id
            ],
            "BillEmail" => [
                "Address" => "Familiystore@intuit.com"
            ],
            "BillEmailCc" => [
                "Address" => "a@intuit.com"
            ],
            "BillEmailBcc" => [
                "Address" => "v@intuit.com"
            ]
        ];
    }
}
