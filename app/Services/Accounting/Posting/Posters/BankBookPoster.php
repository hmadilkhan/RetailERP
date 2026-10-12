<?php

namespace App\Services\Accounting\Posting\Posters;

use App\Services\Accounting\Posting\AccountResolver;
use App\Services\Accounting\Posting\Poster;
use App\Services\Accounting\Posting\PostingDocument;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;

/**
 * bank_deposit_details ki woh rows jin ka koi aur source nahi — har row ki entry (ref bank_deposit_id). credit = bank me aaya, debit = gaya.
 *   mode 'cash' (BankController::insert_deposit)  → cash se bank (ya bank se cash) transfer: Bank ↔ Cash
 *   "Payment Received from customer …"            → Dr Bank / Cr Accounts Receivable   (web bank wasooli)
 *   baaqi manual deposit / withdrawal / cheque clearance → Adjustments Clearing ke saath (JV se reclassify)
 * Chhodi jati hain: mode 'Expense' (ExpensePoster), aur vendor cheque payment (vendor_payment me same bank + cheque —
 * VendorPaymentPoster). Cash deposit ki cash_ledger wali "Deposited to Bank" row CashBookPoster chhodta hai — yahan ek hi baar.
 * Bank account → GL account: Posting Rules mapping, warna Bank Accounts. date = DB default (UTC).
 */
class BankBookPoster implements Poster
{
    public function sourceType(): string
    {
        return 'bank_book';
    }

    public function label(): string
    {
        return 'Bank Book (deposits, withdrawals, customer cheques)';
    }

    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable
    {
        $branches = implode(',', SourceQuery::branchIds($companyId));
        $dateSql = SourceQuery::pkDate('d.date');

        $rows = DB::select("
            SELECT d.bank_deposit_id AS id, {$dateSql} AS dt, d.bank_account_id, d.debit, d.credit, d.mode, d.narration, d.cheque_number,
                   a.branch_id_company AS branch_id, a.account_title
            FROM bank_deposit_details d
            JOIN bank_account_generaldetails a ON a.bank_account_id = d.bank_account_id
            WHERE a.branch_id_company IN ({$branches}) AND COALESCE(d.mode, '') <> 'Expense'
              AND {$dateSql} BETWEEN ? AND ?
              AND NOT (d.debit > 0 AND EXISTS (SELECT 1 FROM vendor_payment vp
                        WHERE vp.bankid = d.bank_account_id AND vp.cheque = d.cheque_number AND d.cheque_number <> ''))",
            [$from, $to]);

        foreach ($rows as $row) {
            $bank = $accounts->mapped('bank_account', (int) $row->bank_account_id, 'bank');
            $narration = trim((string) $row->narration);
            $net = (float) $row->credit - (float) $row->debit; // + = bank me aaya

            if (strtolower((string) $row->mode) === 'cash') {
                $contra = $accounts->id('cash');
            } elseif (stripos($narration, 'Payment Received from customer') === 0) {
                $contra = $accounts->id('accounts_receivable');
            } else {
                $contra = $accounts->id('adjustments_clearing');
            }

            $doc = new PostingDocument((string) $row->id, $row->dt, $row->branch_id ? (int) $row->branch_id : null,
                'Bank ' . $row->account_title . ': ' . ($narration ?: 'manual entry') . ($row->cheque_number ? ' — cheque ' . $row->cheque_number : ''),
                (int) $row->id);
            $doc->debit($bank, $net)->credit($contra, $net);
            yield $doc;
        }
    }
}
