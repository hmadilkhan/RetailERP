<?php

namespace App\Services\Accounting\Posting\Posters;

use App\Services\Accounting\Posting\AccountResolver;
use App\Services\Accounting\Posting\Poster;
use App\Services\Accounting\Posting\PostingDocument;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;

/**
 * Vendor ko payment — har vendor_ledger debit row ki entry (ref vendor_account_id):  Dr Accounts Payable / Cr Cash ya Bank.
 * Sirf woh debits jin ki vendor_payment_details link hai (asal payment); vendor_payment.bankid 0 = cash, warna bank account.
 * Manual "adjustment" screen ke debits (koi link nahi, cash_ledger bhi nahi) aur "PO receipt adjustment" rows post nahi hote —
 * un ka contra account nahi pata (agla hissa). vendor_ledger.created_at DB default = UTC → Pakistan date.
 * cash_ledger ki "Payment to Vendor" rows post nahi hoti (double count).
 * Phase 4.5: foreign currency bill ki payment (fx_payments) — AP bill ke rate pe (ledger debit), cash / bank payment ke rate pe;
 * farq → Exchange Gain / Loss (cash zyada gaya = loss = Dr).
 */
class VendorPaymentPoster implements Poster
{
    /** fx_payments table hai? null = check (test me override) */
    public static ?bool $fxTable = null;

    public function sourceType(): string
    {
        return 'vendor_payment';
    }

    public function label(): string
    {
        return 'Vendor Payments';
    }

    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable
    {
        $dateSql = SourceQuery::pkDate('vl.created_at');

        $rows = DB::select("
            SELECT vl.vendor_account_id AS id, {$dateSql} AS d, vl.debit, vl.po_no, v.vendor_name,
                   MAX(vp.bankid) AS bankid, MAX(vp.cheque) AS cheque, g.branch_id
            FROM vendor_ledger vl
            JOIN vendors v ON v.id = vl.vendor_id
            JOIN vendor_payment_details vpd ON vpd.account_id = vl.vendor_account_id
            JOIN vendor_payment vp ON vp.payment_id = vpd.payment_id
            LEFT JOIN purchase_general_details g ON g.purchase_id = vl.po_no AND vl.po_no > 0
            WHERE v.user_id = ? AND vl.debit > 0 AND {$dateSql} BETWEEN ? AND ?
            GROUP BY vl.vendor_account_id, d, vl.debit, vl.po_no, v.vendor_name, g.branch_id", [$companyId, $from, $to]);

        $fx = [];
        if ($rows && (self::$fxTable ??= \Illuminate\Support\Facades\Schema::hasTable('fx_payments'))) {
            $fx = DB::table('fx_payments')->whereIn('ledger_id', array_column($rows, 'id'))->pluck('cash', 'ledger_id')->all();
        }

        foreach ($rows as $row) {
            $paidFrom = (int) $row->bankid > 0
                ? $accounts->mapped('bank_account', (int) $row->bankid, 'bank')
                : $accounts->id('cash');
            $narration = 'Payment to ' . $row->vendor_name . ($row->po_no > 0 ? ' — PO #' . $row->po_no : '')
                . ((int) $row->bankid > 0 && $row->cheque ? ' — cheque ' . $row->cheque : '');

            $doc = new PostingDocument((string) $row->id, $row->d, $row->branch_id ? (int) $row->branch_id : null, $narration, (int) $row->id);
            $cash = isset($fx[$row->id]) ? (float) $fx[$row->id] : (float) $row->debit;
            $doc->debit('accounts_payable', $row->debit)->credit($paidFrom, $cash);
            if (abs($cash - (float) $row->debit) >= 0.005) {
                $doc->debit('fx_gain_loss', $cash - (float) $row->debit, 'Exchange difference');
            }
            yield $doc;
        }
    }
}
