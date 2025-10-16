<?php

namespace App\Services\Shopify;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShopifySyncService
{
    /**
     * Send order status update to Shopify Sync Server based on status
     */
    public static function sendStatusUpdate($order,$status)
    {
        try {
            $url = 'https://sync.sabsoft.com.pk/api/erp/orders/update-status';

            $status = strtolower($status);
          
            // 🧩 Base payload (common fields)
            $payload = [
                'order_update' => [
                    'erp_order_id' => $order->id,
                    'status'       => $status,
                ],
            ];
       
            // 🧭 Add fields depending on status type
            switch ($status) {
                case 'fulfilled':
                case 'delivered':
                case 'shipped':
                    $payload['order_update'] += [
                        'tracking_number'  => $order->tracking_number ?? null,
                        'tracking_url'     => $order->tracking_url ?? null,
                        'tracking_company' => $order->tracking_company ?? 'ERP Logistics',
                        'notify_customer'  => $order->notify_customer ?? true,
                    ];
                    break;

                case 'cancelled':
                case 'canceled':
                    $payload['order_update'] += [
                        'reason'           => $order->cancel_reason ?? 'customer',
                        'restock'          => $order->restock ?? true,
                        'notify_customer'  => $order->notify_customer ?? true,
                    ];
                    break;

                case 'refunded':
                case 'refund':
                    $payload['order_update'] += [
                        'refund_amount'    => $order->refund_amount ?? 0,
                        'note'             => $order->note ?? 'Refund processed',
                        'restock'          => $order->restock ?? false,
                        'notify_customer'  => $order->notify_customer ?? true,
                    ];
                    break;

                case 'return':
                case 'returned':
                    $payload['order_update'] += [
                        'return_reason'    => $order->return_reason ?? null,
                        'return_note'      => $order->return_note ?? 'Return initiated',
                        'notify_customer'  => $order->notify_customer ?? true,
                    ];
                    break;

                default:
                    // Tag-based or intermediate statuses like pending, packed, etc.
                    $payload['order_update'] += [
                        'note' => $order->note ?? "Status changed to {$status}",
                    ];
                    break;
            }

            Log::info("➡️ Sending order status update [{$status}] to Shopify Sync", $payload);

            $response = Http::timeout(20)
                ->acceptJson()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('services.shopify.token'),
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $payload);
            return $response;
            Log::info('✅ Shopify Sync Response', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('❌ Failed to send Shopify order update', [
                'error' => $e->getMessage(),
                'order_id' => $order->id ?? null,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
