<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * PO final submit pe vendor_ledger me poore PO ka credit lagta hai. Ye service us credit ko asal haalat
 * ke mutabiq rakhti hai — vendor payable = (jo maal aaya - jo wapas gaya) ki value:
 *   - Phase 0.4: GRN short / partial receipt
 *   - Purchase return (Complete / Partial) — vendor ka payable kam
 *   - Replacement (maal wapas gaya, naya aana hai) — naya maal GRN se aaye to adjustment khud ulta
 *   - PO cancel / delete — baqi maal nahi aayega
 * Ek hi formula: adjustment = (ordered - effective received) × line price × (PO credit / Σ qty×price).
 * Har trigger ke baad sync() target vs ab tak laga hua adjustment ka sirf farq daalta hai (idempotent).
 * Jis PO pe abhi koi trigger nahi chala (e.g. Placed, koi GRN nahi) us ka poora credit waisa hi rehta hai.
 *
 * vendor_ledger convention: balance = last + debit - credit (negative = payable).
 * Received qty inventory_stock_report_table se (immutable log), returned qty purchase_return_itemdetails se.
 * purchase_item_details.total_amount bharosemand nahi (kai POs me per-unit value hai), quantity * price use hota hai.
 */
class PurchaseLedgerService
{
    const NARRATION = 'PO receipt adjustment';

    /** Is PO ka target adjustment aur ab tak laga hua adjustment. */
    public function calculate(int $poId): ?array
    {
        $po = DB::table('purchase_general_details')->where('purchase_id', $poId)->first();
        if (!$po) {
            return null;
        }

        // finalSubmit wala PO credit (narration NULL) — yahi vendor pe book hua tha
        $poCredit = (float) DB::table('vendor_ledger')
            ->where('po_no', $poId)->where('vendor_id', $po->vendor_id)
            ->where('credit', '>', 0)->whereNull('narration')
            ->sum('credit');
        if ($poCredit <= 0) {
            return null;
        }

        $lines = DB::table('purchase_item_details')->where('purchase_id', $poId)
            ->select('item_code', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(quantity * price) as amount'))
            ->groupBy('item_code')->get();
        $grossTotal = (float) $lines->sum('amount');
        if ($grossTotal <= 0) {
            return null;
        }

        $received = DB::table('inventory_stock_report_table')
            ->where('foreign_id', $poId)->where('narration', 'Stock Purchase through Purchase Order')
            ->groupBy('product_id')->pluck(DB::raw('SUM(qty)'), 'product_id');

        // har mode (Replacement / Complete / Partial) ka maal vendor ko wapas gaya
        $returned = DB::table('purchase_return_itemdetails')->where('purchase_id', $poId)
            ->groupBy('item_code')->pluck(DB::raw('SUM(quantity)'), 'item_code');

        $missingGross = 0;
        foreach ($lines as $line) {
            if ($line->qty <= 0) {
                continue;
            }
            $kept = (float) ($received[$line->item_code] ?? 0) - (float) ($returned[$line->item_code] ?? 0);
            $missing = $line->qty - min(max($kept, 0), $line->qty);
            $missingGross += $missing * ($line->amount / $line->qty);
        }

        // tax/discount/shipment PO credit me shamil hain, isliye proportionally lagao
        $target = (int) round($missingGross * ($poCredit / $grossTotal));

        $posted = (int) DB::table('vendor_ledger')
            ->where('po_no', $poId)->where('vendor_id', $po->vendor_id)
            ->where('narration', 'like', self::NARRATION . '%')
            ->sum(DB::raw('IFNULL(debit,0) - IFNULL(credit,0)'));

        return ['vendor_id' => $po->vendor_id, 'po_credit' => $poCredit, 'target' => $target, 'posted' => $posted];
    }

    /** Target aur posted ka farq vendor_ledger (aur PO pending amount) me daalo. */
    public function sync(int $poId, string $reason = 'GRN'): void
    {
        DB::transaction(function () use ($poId, $reason) {
            // return ki har line alag ajax request hai — PO row lock karo taake do request ek saath farq na daalein
            DB::table('purchase_general_details')->where('purchase_id', $poId)->lockForUpdate()->first();

            $calc = $this->calculate($poId);
            if (!$calc) {
                return;
            }

            $delta = $calc['target'] - $calc['posted'];
            if ($delta == 0) {
                return;
            }

            $last = DB::table('vendor_ledger')->where('vendor_id', $calc['vendor_id'])
                ->orderByDesc('vendor_account_id')->lockForUpdate()->first();
            $debit = max($delta, 0);
            $credit = max(-$delta, 0);

            DB::table('vendor_ledger')->insert([
                'vendor_id' => $calc['vendor_id'],
                'po_no' => $poId,
                'total_amount' => 0,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => ($last->balance ?? 0) + $debit - $credit,
                'narration' => self::NARRATION . ' PO #' . $poId . ' (' . $reason . ')',
            ]);

            // payment screen PO-wise pending isi se dikhata hai
            DB::table('purchase_account_details')->where('purchase_id', $poId)
                ->update(['balance_amount' => DB::raw('GREATEST(balance_amount - ' . (int) $delta . ', 0)')]);
        });
    }
}
