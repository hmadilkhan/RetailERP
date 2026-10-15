<?php

namespace App\Services\Inventory;

use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

/**
 * Stock Adjustment ka asal kaam — pehle InventoryController::creategrnadjustmnet / update_stockadjustment ke andar tha,
 * yahan isliye ke Phase 2.4 approval ke baad bhi bilkul yahi chale (user / branch parameter se, session se nahi).
 * Writes wahi: positive = purchase_rec_gen + purchase_rec_stock_adjustment + lot + report row ('1'),
 * negative = chune lots se FIFO + report rows ('0'); dono me POS sync (inventory_download_status) + push.
 */
class StockAdjustmentApplier
{
    /** $d: product_id, qty (primary), amount (cost / primary), narration, branch_id, batch_no, expiry_date, user_id. @return stock_id */
    public function positive(array $d): int
    {
        $productId = (int) $d['product_id'];
        $branchId = (int) $d['branch_id'];
        $qty = (float) $d['qty'];
        $now = date('Y-m-d H:s:i'); // legacy format jaisa

        $grnId = DB::table('purchase_rec_gen')->insertGetId([
            'GRN' => 'GRN-' . ((int) DB::table('purchase_rec_gen')->count() + 1),
            'user_id' => $d['user_id'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('purchase_rec_stock_adjustment')->insertGetId(['GRN' => $grnId, 'item_id' => $productId, 'qty_rec' => $qty]);

        $stockId = DB::table('inventory_stock')->insertGetId([
            'grn_id' => $grnId,
            'product_id' => $productId,
            'uom' => DB::table('inventory_general')->where('id', $productId)->value('uom_id'),
            'cost_price' => $d['amount'],
            'retail_price' => '0.00',
            'wholesale_price' => '0.00',
            'discount_price' => '0.00',
            'qty' => $qty,
            'balance' => $qty,
            'status_id' => 1,
            'branch_id' => $branchId,
        ] + \Custom_Helper::lotExpiryFields($d['batch_no'] ?? null, $d['expiry_date'] ?? null));

        $this->report($productId, $stockId, $branchId, $qty, $d['amount'], '0.00', $d['narration'] ?? '', '1', $now);
        $this->afterChange($branchId, $productId);
        return $stockId;
    }

    /**
     * $d: stock_ids[], qty (positive number), narration, user_id. Chune lots se FIFO.
     * $fallback = true (approval ke baad): chune lots me maal kam pad jaye to usi product + branch ke baaqi lots (FEFO) se.
     */
    public function negative(array $d, bool $fallback = false): void
    {
        $quantity = abs((float) $d['qty']);
        $stockIds = array_filter((array) ($d['stock_ids'] ?? []));
        if ($quantity <= 0 || empty($stockIds)) {
            throw new RuntimeException('Please enter a quantity and select at least one GRN lot.');
        }

        $lots = DB::table('inventory_stock')->whereIn('stock_id', $stockIds)->where('status_id', 1)->where('balance', '>', 0)->orderBy('stock_id')->get();
        if ($fallback) {
            $first = DB::table('inventory_stock')->whereIn('stock_id', $stockIds)->first(['product_id', 'branch_id']);
            if ($first) {
                $more = DB::select('SELECT * FROM inventory_stock WHERE product_id = ? AND branch_id = ? AND status_id = 1 AND balance > 0'
                    . \Custom_Helper::lotOrder(), [$first->product_id, $first->branch_id]);
                $lots = $lots->concat(collect($more)->reject(fn ($l) => in_array($l->stock_id, $stockIds)))->values();
            }
        }
        if ($lots->isEmpty() || $lots->pluck('product_id')->unique()->count() > 1 || $lots->pluck('branch_id')->unique()->count() > 1) {
            throw new RuntimeException('Selected GRN lots are not valid. Please reload and select again.');
        }
        $available = (float) $lots->sum('balance');
        if ($quantity - $available > 0.0001) {
            throw new RuntimeException('Selected lots have only ' . (float) $available . ' in stock. Select more lots or reduce the quantity.');
        }

        $productId = (int) $lots->first()->product_id;
        $branchId = (int) $lots->first()->branch_id;
        DB::transaction(function () use ($lots, $quantity, $productId, $d) {
            $remaining = $quantity;
            foreach ($lots as $lot) {
                if ($remaining <= 0.0001) {
                    break;
                }
                $deduct = min($remaining, (float) $lot->balance);
                $newbal = (float) $lot->balance - $deduct;
                DB::table('inventory_stock')->where('stock_id', $lot->stock_id)->update($newbal > 0.0001 ? ['balance' => $newbal] : ['balance' => 0, 'status_id' => 2]);
                $this->report($productId, (int) $lot->stock_id, (int) $lot->branch_id, -$deduct, $lot->cost_price, $lot->retail_price, $d['narration'] ?? '', '0', date('Y-m-d H:s:i'));
                $remaining -= $deduct;
            }
        });
        $this->afterChange($branchId, $productId);
    }

    /** approval ke liye value: positive = qty × cost, negative = chune lots ki cost se (FIFO) */
    public function negativeValue(array $stockIds, float $qty): float
    {
        $value = 0;
        $left = abs($qty);
        foreach (DB::table('inventory_stock')->whereIn('stock_id', $stockIds)->where('status_id', 1)->orderBy('stock_id')->get() as $lot) {
            $take = min($left, (float) $lot->balance);
            $value += $take * (float) $lot->cost_price;
            $left -= $take;
            if ($left <= 0) {
                break;
            }
        }
        return round($value, 2);
    }

    // $qty signed: minus = stock hata (InventoryController::stockreport jaisa)
    private function report(int $productId, int $stockId, int $branchId, float $qty, $cost, $retail, string $narration, string $mode, string $when): void
    {
        $last = DB::selectOne('SELECT SUM(stock) AS stock FROM inventory_stock_report_table WHERE stock_report_id = (SELECT MAX(stock_report_id) FROM inventory_stock_report_table WHERE product_id = ?)', [$productId]);
        DB::table('inventory_stock_report_table')->insert([
            'date' => $when,
            'product_id' => $productId,
            'foreign_id' => $stockId,
            'branch_id' => $branchId,
            'qty' => $qty,
            'stock' => (float) ($last->stock ?? 0) + $qty,
            'cost' => $cost,
            'retail' => $retail,
            'narration' => '(Stock Adjustment) ' . $narration,
            'adjustment_mode' => $mode,
        ]);
    }

    private function afterChange(int $branchId, int $productId): void
    {
        foreach (DB::table('terminal_details')->where('branch_id', $branchId)->where('status_id', 1)->pluck('terminal_id') as $terminalId) {
            DB::table('inventory_download_status')->insert([
                'branchId' => $branchId,
                'terminalId' => $terminalId,
                'productId' => $productId,
                'status' => 1,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
            ]);
        }
        $product = DB::table('inventory_general')->where('id', $productId)->first(['item_code', 'product_name']);
        try {
            app(InventoryController::class)->sendPushNotification($product->item_code ?? '', $product->product_name ?? '', 'update');
        } catch (Throwable $e) {
            // purani FCM API — push na jaye to adjustment na ruke
        }
    }
}
