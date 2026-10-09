<?php

namespace App\Services\Accounting\Posting\Posters;

use App\Services\Accounting\Posting\AccountResolver;
use App\Services\Accounting\Posting\Poster;
use App\Services\Accounting\Posting\PostingDocument;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;

/**
 * Credit customers se POS pe wasooli — branch + din ki ek entry (ref "branch:date"):  Dr Cash / Bank, Cr Accounts Receivable.
 * Source: customer_account rows jahan credit > 0, total_amount = 0 (payment, sale nahi), opening_id > 1 (POS; web wale 1 hote hain).
 * created_at DB default = UTC → Pakistan date. Credit SALE ka AR debit SalesPoster karta hai (payment_id 3), yahan nahi.
 * Web wasooli (CustomersController make_cash/bank_payment) 2023 se band hai — abhi post nahi hoti.
 */
class CustomerReceiptPoster implements Poster
{
    public function sourceType(): string
    {
        return 'customer_receipt';
    }

    public function label(): string
    {
        return 'Customer Receipts (POS)';
    }

    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable
    {
        $branches = implode(',', SourceQuery::branchIds($companyId));
        $floor = SourceQuery::idFloor('customer_account', 'cust_account_id', 'created_at', $from);
        $dateSql = SourceQuery::pkDate('ca.created_at');

        $rows = DB::select("
            SELECT t.branch_id, {$dateSql} AS d, ca.payment_mode_id, SUM(ca.credit) AS amount
            FROM customer_account ca
            JOIN terminal_details t ON t.terminal_id = ca.terminal_id
            WHERE ca.cust_account_id >= ? AND t.branch_id IN ({$branches})
              AND ca.credit > 0 AND ca.total_amount = 0 AND ca.opening_id > 1
              AND {$dateSql} BETWEEN ? AND ?
            GROUP BY t.branch_id, d, ca.payment_mode_id", [$floor, $from, $to]);

        $docs = [];
        foreach ($rows as $row) {
            $ref = $row->branch_id . ':' . $row->d;
            $doc = $docs[$ref] ??= new PostingDocument($ref, $row->d, (int) $row->branch_id,
                'Customer receipts (POS) — branch #' . $row->branch_id . ' — ' . $row->d);
            $receivedIn = (int) $row->payment_mode_id === 2
                ? $accounts->mapped('payment_mode', 2, 'bank')
                : $accounts->id('cash');
            $doc->debit($receivedIn, $row->amount)->credit('accounts_receivable', $row->amount);
        }

        return array_values($docs);
    }
}
