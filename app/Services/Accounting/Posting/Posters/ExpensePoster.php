<?php

namespace App\Services\Accounting\Posting\Posters;

use App\Services\Accounting\Posting\AccountResolver;
use App\Services\Accounting\Posting\Poster;
use App\Services\Accounting\Posting\PostingDocument;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;

/**
 * Har expense ki ek entry (ref exp_id):  Dr expense head (category mapping, warna General Expenses) / Cr paisa kahan se.
 *   - POS terminal expense (opening_id set, 98% rows) → till ka cash
 *   - web expense payment_mode bank → us bank account ki mapping (warna Bank Accounts), baaqi → Cash in Hand
 * Web expense edit / hard delete hoti hai — PostingService hash / gayab hone se reverse + repost karta hai.
 * cash_ledger ki "Expense #…" rows post NAHI hoti (double count hota) — sirf expenses table.
 */
class ExpensePoster implements Poster
{
    public function sourceType(): string
    {
        return 'expense';
    }

    public function label(): string
    {
        return 'Expenses';
    }

    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable
    {
        $branches = implode(',', SourceQuery::branchIds($companyId));
        $dateSql = "COALESCE(NULLIF(e.date, ''), " . SourceQuery::pkDate('e.created_at') . ")";

        $rows = DB::select("
            SELECT e.exp_id, {$dateSql} AS d, e.branch_id, e.exp_cat_id, e.net_amount, e.expense_details,
                   e.payment_mode, e.bank_account_id, e.opening_id, c.expense_category
            FROM expenses e
            LEFT JOIN expense_categories c ON c.exp_cat_id = e.exp_cat_id
            WHERE e.branch_id IN ({$branches}) AND {$dateSql} BETWEEN ? AND ?", [$from, $to]);

        foreach ($rows as $row) {
            if ($row->opening_id > 0 || $row->payment_mode !== 'bank') {
                $paidFrom = $accounts->id('cash');
            } else {
                $paidFrom = $accounts->mapped('bank_account', (int) $row->bank_account_id, 'bank');
            }

            $doc = new PostingDocument((string) $row->exp_id, $row->d, (int) $row->branch_id,
                trim('Expense #' . $row->exp_id . ' — ' . ($row->expense_category ?? '') . ' — ' . $row->expense_details, ' —'),
                (int) $row->exp_id);
            $doc->debit($accounts->mapped('expense_category', (int) $row->exp_cat_id, 'general_expense'), $row->net_amount)
                ->credit($paidFrom, $row->net_amount);
            yield $doc;
        }
    }
}
