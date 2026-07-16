<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FbrInvoiceService
{
    private const LIVE_URL = 'https://gw.fbr.gov.pk/imsp/v1/api/Live/PostData';

    /**
     * Send one or many receipts to FBR as sales returns (InvoiceType 3).
     *
     * @param  array<int>  $orderIds
     * @return array<int, array{order_id: int, success: bool, message: string, invoice_number?: string|null}>
     */
    public function sendMany(array $orderIds, bool $asReturn = true): array
    {
        $results = [];

        foreach ($orderIds as $orderId) {
            $orderId = (int) $orderId;
            if ($orderId <= 0) {
                continue;
            }

            try {
                $results[] = $this->sendOne($orderId, $asReturn);
            } catch (\Throwable $e) {
                Log::error('FBR send failed', [
                    'order_id' => $orderId,
                    'error' => $e->getMessage(),
                ]);
                $results[] = [
                    'order_id' => $orderId,
                    'success' => false,
                    'message' => $e->getMessage(),
                    'invoice_number' => null,
                ];
            }
        }

        return $results;
    }

    /**
     * @return array{order_id: int, success: bool, message: string, invoice_number?: string|null}
     */
    public function sendOne(int $orderId, bool $asReturn = true): array
    {
        $order = Order::find($orderId);
        if (!$order) {
            return [
                'order_id' => $orderId,
                'success' => false,
                'message' => 'Order not found',
                'invoice_number' => null,
            ];
        }

        if (!empty($order->fbrInvNumber)) {
            return [
                'order_id' => $orderId,
                'success' => false,
                'message' => "Already has FBR invoice: {$order->fbrInvNumber}",
                'invoice_number' => $order->fbrInvNumber,
            ];
        }

        $branchId = (int) $order->branch;
        $fbrData = DB::table('fbr_details')
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->first();

        if (!$fbrData) {
            return [
                'order_id' => $orderId,
                'success' => false,
                'message' => "No active FBR details for branch {$branchId}",
                'invoice_number' => null,
            ];
        }

        $payload = $this->buildPayload($order, $fbrData, $asReturn);
        return $this->postToFbr($payload, $orderId, $fbrData->token_id);
    }

    private function buildPayload(Order $order, object $fbrData, bool $asReturn): array
    {
        $header = DB::selectOne(
            "SELECT a.id, a.date, a.time, a.order_ref,
                    IFNULL(c.total_amount, 0) as total_amount,
                    IFNULL(b.discount_amount, 0) as discount_amount,
                    IFNULL(b.sales_tax_amount, 0) as sales_tax_amount,
                    a.actual_amount
             FROM sales_receipts a
             LEFT JOIN sales_account_subdetails b ON b.receipt_id = a.id
             LEFT JOIN sales_account_general c ON c.receipt_id = a.id
             WHERE a.id = ?",
            [$order->id]
        );

        $items = DB::select(
            "SELECT a.receipt_id,a.item_code, b.product_name, a.total_qty, a.total_amount,
                    a.item_price, a.taxamount, a.discount
             FROM sales_receipt_details a
             INNER JOIN inventory_general b ON b.id = a.item_code
             WHERE a.receipt_id = ?",
            [$order->id]
        );

        $sign = $asReturn ? -1 : 1;
        $invoiceType = $asReturn ? 3 : 1;
        $refUsin = $asReturn ? ($header->order_ref ?? null) : null;
        $taxRate = $fbrData->tax_rate;
        $orderDetails = [];
        $totalProducts = 0;

        foreach ($items as $key => $product) {
            $qty = (float) $product->total_qty;
            $totalProducts += $sign * $qty;
            $orderDetails[] = [
                'ItemCode' => $product->item_code,
                'ItemName' => $product->product_name,
                'Quantity' => $sign * $qty,
                'PCTCode' => '11001010',
                'TaxRate' => $taxRate,
                'SaleValue' => $sign * ((float) $product->item_price * $qty),
                'TotalAmount' => $sign * (float) $product->total_amount,
                'TaxCharged' => $sign * (float) $product->taxamount,
                'Discount' => $sign * (float) $product->discount,
                'FurtherTax' => 0,
                'InvoiceType' => $invoiceType,
                'RefUSIN' => $refUsin,
            ];
        }

        return [
            'InvoiceNumber' => '',
            'POSID' => $fbrData->pos_id,
            'USIN' => $header->id,
            'DateTime' => $header->date . ' ' . $header->time,
            'BuyerNTN' => '',
            'BuyerName' => '',
            'BuyerPhoneNumber' => '',
            'TotalBillAmount' => $sign * ((float) $header->total_amount - (float) $header->discount_amount),
            'TotalQuantity' => $totalProducts,
            'TotalSaleValue' => $sign * (float) $header->actual_amount,
            'TotalTaxCharged' => $sign * (float) $header->sales_tax_amount,
            'Discount' => $sign * (float) $header->discount_amount,
            'FurtherTax' => 0,
            'PaymentMode' => 1,
            'RefUSIN' => $asReturn ? $refUsin : 'NULL',
            'InvoiceType' => $invoiceType,
            'Items' => $orderDetails,
        ];
    }

    /**
     * @return array{order_id: int, success: bool, message: string, invoice_number?: string|null}
     */
    private function postToFbr(array $payload, int $orderId, string $token): array
    {
        $response = Http::withToken($token)
            ->withOptions([
                'verify' => false,
            ])
            ->timeout(60)
            ->asJson()
            ->post(self::LIVE_URL, $payload);

        $outPut = $response->json();

        if (!is_array($outPut)) {
            return [
                'order_id' => $orderId,
                'success' => false,
                'message' => 'Invalid FBR response: ' . $response->body(),
                'invoice_number' => null,
            ];
        }

        $code = $outPut['Code'] ?? null;
        $invoiceNumber = $outPut['InvoiceNumber'] ?? null;

        if ((int) $code === 100 && !empty($invoiceNumber)) {
            DB::table('sales_receipts')
                ->where('id', $orderId)
                ->update(['fbrInvNumber' => $invoiceNumber]);

            return [
                'order_id' => $orderId,
                'success' => true,
                'message' => 'Posted to FBR successfully',
                'invoice_number' => $invoiceNumber,
            ];
        }

        $message = $outPut['Message'] ?? $outPut['message'] ?? ('FBR rejected with code ' . ($code ?? 'unknown'));

        return [
            'order_id' => $orderId,
            'success' => false,
            'message' => is_string($message) ? $message : json_encode($outPut),
            'invoice_number' => $invoiceNumber,
        ];
    }
}
