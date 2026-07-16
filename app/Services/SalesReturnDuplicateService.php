<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderAccount;
use App\Models\OrderDetails;
use App\Models\OrderSubAccount;
use Illuminate\Support\Facades\DB;

class SalesReturnDuplicateService
{
    /**
     * Duplicate receipts as sales returns (status 14, order_ref = original id).
     *
     * @param  array<int>  $orderIds
     * @return array{duplicated: array, skipped: array, failed: array}
     */
    public function duplicateMany(array $orderIds): array
    {
        $duplicated = [];
        $skipped = [];
        $failed = [];

        foreach ($orderIds as $orderId) {
            $orderId = (int) $orderId;
            if ($orderId <= 0) {
                continue;
            }

            try {
                $result = $this->duplicateOne($orderId);
                if ($result['status'] === 'skipped') {
                    $skipped[] = $result;
                } else {
                    $duplicated[] = $result;
                }
            } catch (\Throwable $e) {
                $failed[] = [
                    'original_id' => $orderId,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return compact('duplicated', 'skipped', 'failed');
    }

    /**
     * @return array{status: string, original_id: int, new_id?: int, message?: string}
     */
    public function duplicateOne(int $orderId): array
    {
        $existingReturn = Order::where('order_ref', $orderId)->where('status', 14)->first();
        if ($existingReturn) {
            return [
                'status' => 'skipped',
                'original_id' => $orderId,
                'new_id' => $existingReturn->id,
                'message' => "Already duplicated as receipt #{$existingReturn->id}",
            ];
        }

        $original = Order::find($orderId);
        if (!$original) {
            throw new \RuntimeException("Order #{$orderId} not found");
        }

        return DB::transaction(function () use ($original, $orderId) {
            $receiptData = $original->getAttributes();
            unset($receiptData['id']);

            $receiptData['status'] = 14;
            $receiptData['order_ref'] = $orderId;
            $receiptData['is_sale_return'] = 1;
            $receiptData['fbrInvNumber'] = null;
            $receiptData['srbInvNumber'] = null;
            $receiptData['void_receipt'] = 0;
            $receiptData['void_date'] = null;
            $receiptData['void_reason'] = null;
            $receiptData['isSeen'] = 0;
            $receiptData['receipt_seen'] = 0;
            $receiptData['total_amount'] = $receiptData['actual_amount'];

            $newReceiptId = DB::table('sales_receipts')->insertGetId($receiptData);

            $details = OrderDetails::where('receipt_id', $orderId)->get();
            foreach ($details as $detail) {
                $row = $detail->getAttributes();
                unset($row['receipt_detail_id']);
                $row['receipt_id'] = $newReceiptId;
                $row['is_sale_return'] = 1;
                DB::table('sales_receipt_details')->insert($row);
            }

            $account = OrderAccount::where('receipt_id', $orderId)->first();
            if ($account) {
                $accountRow = $account->getAttributes();
                unset($accountRow['account_id']);
                $accountRow['receipt_id'] = $newReceiptId;
                $accountRow['total_amount'] = $receiptData['actual_amount'];
                $accountRow['receive_amount'] = $receiptData['actual_amount'];
                $accountRow['balance_amount'] = 0;
                DB::table('sales_account_general')->insert($accountRow);
            }

            $subAccount = OrderSubAccount::where('receipt_id', $orderId)->first();
            if ($subAccount) {
                $subRow = $subAccount->getAttributes();
                unset($subRow['id']);
                $subRow['receipt_id'] = $newReceiptId;
                $subRow['is_sync'] = 1;
                DB::table('sales_account_subdetails')->insert($subRow);
            }

            return [
                'status' => 'duplicated',
                'original_id' => $orderId,
                'new_id' => $newReceiptId,
                'message' => 'Duplicated successfully',
            ];
        });
    }
}
