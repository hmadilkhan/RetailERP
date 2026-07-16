<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderSubAccount;
use Illuminate\Support\Facades\DB;

class OrderRecalculateService
{
    /**
     * Recalculate receipt totals from remaining line items.
     *
     * @return array{actual_amount: float, total_amount: float, total_item_qty: float, sales_tax_amount: float, discount_amount: float, srb: float}
     */
    public function recalculate(int $receiptId): array
    {
        return DB::transaction(function () use ($receiptId) {
            $order = Order::findOrFail($receiptId);
            $lines = OrderDetails::where('receipt_id', $receiptId)->get();

            $actualAmount = 0.0;
            $salesTaxAmount = 0.0;
            $totalItemQty = 0.0;
            $lineDiscountSum = 0.0;

            foreach ($lines as $line) {
                $qty = (float) $line->total_qty;
                $price = (float) $line->item_price;
                $actualAmount += $price * $qty;
                $salesTaxAmount += (float) $line->taxamount;
                $totalItemQty += $qty;
                $lineDiscountSum += (float) $line->discount;
            }

            $subAccount = OrderSubAccount::where('receipt_id', $receiptId)->first();
            $discountAmount = $subAccount
                ? (float) $subAccount->discount_amount
                : $lineDiscountSum;
            $srb = $subAccount ? (float) ($subAccount->srb ?? 0) : 0.0;

            // total_amount mirrors actual_amount (pre-tax sale value of remaining lines)
            $totalAmount = $actualAmount;

            $order->actual_amount = $actualAmount;
            $order->total_amount = $totalAmount;
            $order->total_item_qty = $totalItemQty;
            $order->save();

            if ($subAccount) {
                $subAccount->sales_tax_amount = $salesTaxAmount;
                $subAccount->is_sync = 1;
                $subAccount->save();
            }

            DB::table('sales_account_general')
                ->where('receipt_id', $receiptId)
                ->update([
                    'total_amount' => $actualAmount,
                    'receive_amount' => $actualAmount,
                    'balance_amount' => 0,
                ]);

            return [
                'actual_amount' => $actualAmount,
                'total_amount' => $totalAmount,
                'total_item_qty' => $totalItemQty,
                'sales_tax_amount' => $salesTaxAmount,
                'discount_amount' => $discountAmount,
                'srb' => $srb,
            ];
        });
    }

    /**
     * Delete a line item and recalculate the parent receipt.
     *
     * @return array{totals: array, deleted_id: int}
     */
    public function deleteLineAndRecalculate(int $detailId): array
    {
        return DB::transaction(function () use ($detailId) {
            $detail = OrderDetails::findOrFail($detailId);
            $receiptId = (int) $detail->receipt_id;

            $order = Order::findOrFail($receiptId);
            if ((int) $order->status !== 14) {
                throw new \RuntimeException('Only sales return orders (status 14) can be edited here.');
            }

            $detail->delete();
            $totals = $this->recalculate($receiptId);

            return [
                'deleted_id' => $detailId,
                'receipt_id' => $receiptId,
                'totals' => $totals,
            ];
        });
    }
}
