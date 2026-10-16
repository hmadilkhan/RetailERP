<?php

namespace App\Services\Accounting\Posting\Posters;

use App\Services\Accounting\Posting\AccountResolver;
use App\Services\Accounting\Posting\Poster;
use App\Services\Accounting\Posting\PostingDocument;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Phase 2.1 vendor_bills (posted) — har document ki entry (ref id):
 *   Vendor Bill  : Dr Inventory (subtotal − discount + other charges), Dr Input Tax / Cr Accounts Payable
 *   Debit Note   : Dr Accounts Payable / Cr Inventory (amount), Cr Input Tax          (maal wapas)
 *   Credit Note  : Dr Accounts Payable / Cr Purchase Discounts & Rebates             (vendor ka rebate)
 *   Landed Cost  : Dr Inventory / Cr Accounts Payable (payee: transporter ya wahi vendor) — lots ki cost bhi badhti hai
 *   PO Bill      : (Phase 4.4) maal + AP pehle se GRN pe (GrnPoster) — sirf farq: Dr Input Tax (tax) / Dr Inventory (variance − tax)
 *                  / Cr Accounts Payable (variance). variance column migration se pehle na ho to PO bill skip (baqi bills same).
 * Cancel (status cancelled) → document gayab → engine purani entry reverse karta hai.
 * Stock report rows ("(Stock Adjustment) Vendor Bill / Debit Note …") StockAdjustmentPoster nahi uthata, aur
 * vendor_ledger ki "Vendor Bill / Debit Note / Credit Note …" rows VendorAdjustmentPoster nahi — warna double.
 * Payment VendorPaymentPoster se (vendor_payment_details link).
 */
class VendorBillPoster implements Poster
{
    /** variance column (Phase 4.4 migration) hai? null = check */
    public static ?bool $hasVariance = null;

    public function sourceType(): string
    {
        return 'vendor_bill';
    }

    public function label(): string
    {
        return 'Vendor Bills & Notes';
    }

    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable
    {
        try {
            $hasVariance = self::$hasVariance ??= \Illuminate\Support\Facades\Schema::hasColumn('vendor_bills', 'variance');
            $rows = DB::table('vendor_bills as b')->join('vendors as v', 'v.id', '=', 'b.vendor_id')
                ->leftJoin('vendor_bills as a', 'a.id', '=', 'b.against_bill_id')
                ->where('b.company_id', $companyId)->where('b.status', 'posted')
                ->whereBetween('b.bill_date', [$from, $to])
                ->when(!$hasVariance, fn ($q) => $q->where('b.type', '<>', 'po_bill'))
                ->get(['b.id', 'b.type', 'b.doc_no', 'b.vendor_ref', 'b.bill_date', 'b.branch_id', 'b.subtotal', 'b.discount',
                    'b.tax_amount', 'b.other_charges', 'b.total', 'v.vendor_name', 'a.doc_no as against_doc',
                    $hasVariance ? 'b.variance' : DB::raw('0 AS variance'), $hasVariance ? 'b.purchase_id' : DB::raw('NULL AS purchase_id')]);
        } catch (QueryException $e) {
            return []; // migration se pehle deploy — table hi nahi (to koi purani entry bhi nahi)
        }

        foreach ($rows as $row) {
            $date = substr((string) $row->bill_date, 0, 10);
            $goods = round((float) $row->subtotal - (float) $row->discount, 2);
            $narration = match ($row->type) {
                'debit_note' => 'Debit Note ' . $row->doc_no . ' — ' . $row->vendor_name . ' (against ' . $row->against_doc . ')',
                'credit_note' => 'Credit Note ' . $row->doc_no . ' — ' . $row->vendor_name . ' (against ' . $row->against_doc . ')',
                'po_bill' => 'PO Bill ' . $row->doc_no . ' — ' . $row->vendor_name . ($row->vendor_ref ? ' (Bill# ' . $row->vendor_ref . ')' : '') . ' — difference from PO #' . $row->purchase_id,
                'landed_cost' => 'Landed Cost ' . $row->doc_no . ' — ' . $row->vendor_name . ' (on ' . $row->against_doc . ')',
                default => 'Vendor Bill ' . $row->doc_no . ' — ' . $row->vendor_name . ($row->vendor_ref ? ' (Bill# ' . $row->vendor_ref . ')' : ''),
            };
            $doc = new PostingDocument((string) $row->id, $date, $row->branch_id ? (int) $row->branch_id : null, $narration, (int) $row->id);

            if ($row->type === 'bill') {
                $doc->debit('inventory', $goods + (float) $row->other_charges)
                    ->debit('input_tax', $row->tax_amount)
                    ->credit('accounts_payable', $row->total);
            } elseif ($row->type === 'debit_note') {
                $doc->debit('accounts_payable', $row->total)
                    ->credit('inventory', $goods)
                    ->credit('input_tax', $row->tax_amount);
            } elseif ($row->type === 'po_bill') {
                $doc->debit('input_tax', $row->tax_amount)
                    ->debit('inventory', (float) $row->variance - (float) $row->tax_amount)
                    ->credit('accounts_payable', $row->variance);
            } elseif ($row->type === 'landed_cost') {
                $doc->debit('inventory', $row->total)->credit('accounts_payable', $row->total);
            } else {
                $doc->debit('accounts_payable', $row->total)->credit('purchase_discounts', $row->total);
            }
            yield $doc;
        }
    }
}
