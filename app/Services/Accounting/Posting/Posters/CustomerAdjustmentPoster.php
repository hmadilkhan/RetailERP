<?php

namespace App\Services\Accounting\Posting\Posters;

use App\Services\Accounting\Posting\AccountResolver;
use App\Services\Accounting\Posting\Poster;
use App\Services\Accounting\Posting\PostingDocument;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * customer_account ki web rows (CustomersController) jo payment nahi — har row ki entry (ref cust_account_id):
 *   naya customer ka opening balance (narration 'Opening Balance') → AR ↔ Opening Balance Equity
 *   ledger screen ka manual adjustment                            → AR ↔ Adjustments Clearing
 * Sign code wala (balance formula / CustomerReceiptPoster jaisa): debit = receivable badha (Dr AR), credit = kam hua (Cr AR).
 * Ledger screen ki headings ulti hain (Debit ke neeche credit dikhta hai) — jo company screen ke hisaab se likhti hai
 * (company 120: bill credit me, wasooli debit me) us ke liye accounting_settings.customer_ledger_reversed = 1 → sign ulta.
 * Pehchaan: terminal_id = 1, total_amount = 0, received = 0, receipt_no = 0. Cash / bank wasooli (received = 1)
 * CashBookPoster / BankBookPoster se aati hai. Edit adjustment row ko wahin badalta hai → hash badla → reverse + repost.
 * created_at DB default = UTC → Pakistan date.
 */
class CustomerAdjustmentPoster implements Poster
{
    public function sourceType(): string
    {
        return 'customer_adjustment';
    }

    public function label(): string
    {
        return 'Customer Opening Balances & Adjustments';
    }

    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable
    {
        $floor = SourceQuery::idFloor('customer_account', 'cust_account_id', 'created_at', $from);
        $dateSql = SourceQuery::pkDate('ca.created_at');

        $rows = DB::select("
            SELECT ca.cust_account_id AS id, {$dateSql} AS d, ca.debit, ca.credit, ca.narration, c.name
            FROM customer_account ca
            JOIN customers c ON c.id = ca.cust_id
            WHERE ca.cust_account_id >= ? AND c.company_id = ?
              AND ca.terminal_id = 1 AND ca.total_amount = 0 AND ca.received = 0 AND ca.receipt_no = 0
              AND {$dateSql} BETWEEN ? AND ?", [$floor, $companyId, $from, $to]);

        // column migration se pehle deploy ho to purana (code wala) sign
        $reversed = Schema::hasColumn('accounting_settings', 'customer_ledger_reversed')
            && (bool) DB::table('accounting_settings')->where('company_id', $companyId)->value('customer_ledger_reversed');

        foreach ($rows as $row) {
            $isOpening = $row->narration === 'Opening Balance';
            $net = ((float) $row->debit - (float) $row->credit) * ($reversed ? -1 : 1); // + = receivable badha

            $doc = new PostingDocument((string) $row->id, $row->d, null,
                ($isOpening ? 'Customer opening balance — ' : 'Customer adjustment — ') . $row->name
                    . (!$isOpening && $row->narration ? ' — ' . $row->narration : ''),
                (int) $row->id);
            $doc->debit('accounts_receivable', $net)->credit($isOpening ? 'opening_balance_equity' : 'adjustments_clearing', $net);
            yield $doc;
        }
    }
}
