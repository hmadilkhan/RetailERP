<?php

namespace App\Http\Controllers\Api\Shopify;

use App\Http\Controllers\Controller;
use App\Services\Shopify\ErpOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ErpWebhookController extends Controller
{
    protected $erpOrderService;

    public function __construct(ErpOrderService $erpOrderService)
    {
        $this->erpOrderService = $erpOrderService;
    }

    /**
     * Handle Shopify Order Created Webhook
     */
    public function orderCreated(Request $request)
    {
        try {
            $payload = $request->all();

            Log::info('ERP Webhook: Order Created Received', [
                'order_id' => $payload['order_id'] ?? null,
            ]);

            // 🧠 Call service to insert order into ERP
            $receiptId = $this->erpOrderService->createOrder($payload);

            return response()->json([
                'success'     => true,
                'message'     => 'Order successfully synced to ERP.',
                'receipt_id'  => $receiptId,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('ERP Webhook Order Create Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Shopify Order Updated Webhook
     */
    public function orderUpdated(Request $request)
    {
        try {
            $payload = $request->all();

            Log::info('ERP Webhook: Order Updated Received', [
                'order_id' => $payload['order_id'] ?? null,
            ]);

            // You can reuse the same service (createOrder) or add a separate update method
            $receiptId = $this->erpOrderService->createOrder($payload);

            return response()->json([
                'success'     => true,
                'message'     => 'Order update successfully synced to ERP.',
                'receipt_id'  => $receiptId,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('ERP Webhook Order Update Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync updated order: ' . $e->getMessage(),
            ], 500);
        }
    }
}
