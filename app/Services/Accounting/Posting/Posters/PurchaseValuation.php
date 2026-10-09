<?php

namespace App\Services\Accounting\Posting\Posters;

use Illuminate\Support\Facades\DB;

/**
 * PO ki valuation — PurchaseLedgerService (vendor_ledger sync) wala hi hisaab, taake GL aur vendor ledger match karein:
 *   item ki unit price = Σ(quantity × price) / Σ quantity   (total_amount column bharosemand nahi)
 *   ratio = PO ka vendor_ledger credit / Σ(quantity × price) — tax / discount / shipment proportional
 */
class PurchaseValuation
{
    private array $cache = [];

    /** @return array{prices: array<int, float>, ratio: float, vendor_id: int|null, branch_id: int|null} */
    public function forPo(int $poId): array
    {
        if (isset($this->cache[$poId])) {
            return $this->cache[$poId];
        }

        $po = DB::table('purchase_general_details')->where('purchase_id', $poId)->first(['vendor_id', 'branch_id']);
        $lines = DB::table('purchase_item_details')->where('purchase_id', $poId)
            ->select('item_code', DB::raw('SUM(quantity) AS qty'), DB::raw('SUM(quantity * price) AS amount'))
            ->groupBy('item_code')->get();

        $prices = [];
        foreach ($lines as $line) {
            $prices[(int) $line->item_code] = $line->qty > 0 ? $line->amount / $line->qty : 0.0;
        }
        $gross = (float) $lines->sum('amount');

        $credit = $po ? (float) DB::table('vendor_ledger')->where('po_no', $poId)->where('vendor_id', $po->vendor_id)
            ->where('credit', '>', 0)->whereNull('narration')->sum('credit') : 0.0;

        return $this->cache[$poId] = [
            'prices' => $prices,
            'ratio' => $gross > 0 && $credit > 0 ? $credit / $gross : 1.0,
            'vendor_id' => $po->vendor_id ?? null,
            'branch_id' => isset($po->branch_id) ? (int) $po->branch_id : null,
        ];
    }

    /** qty × unit price × ratio */
    public function value(int $poId, int $itemId, float $qty): float
    {
        $po = $this->forPo($poId);
        return round($qty * ($po['prices'][$itemId] ?? 0) * $po['ratio'], 2);
    }
}
