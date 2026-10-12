<?php

namespace App\Services\Accounting\Posting\Posters;

use App\Services\Accounting\Posting\AccountResolver;
use App\Services\Accounting\Posting\Poster;
use App\Services\Accounting\Posting\PostingDocument;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;

/**
 * cash_ledger ki woh rows jin ka koi aur source nahi — har row ki entry (ref id). debit = cash bahar, credit = cash andar.
 *   "Employee Salary : …"                → Dr Salaries Payable        / Cr Cash   (PayrollPoster ne payable banaya)
 *   "Advance Salary : …" / "Loan : …"    → Dr Employee Advances       / Cr Cash
 *   "Payment received from Customer …"   → Dr Cash / Cr Accounts Receivable   (web wasooli)
 *   baaqi manual cash in / out           → Adjustments Clearing ke saath (doosra account source me nahi — JV se reclassify)
 * Chhodi jati hain (doosra poster post karta hai, warna double): "Payment to Vendor", "Deposited to Bank" (BankBookPoster),
 * "Expense #" + "Reversal … Expense #" (ExpensePoster), "Expense head" (purani expenses, expenses table se).
 * date column me 'Y-m-d' aur 'd-m-Y' dono.
 */
class CashBookPoster implements Poster
{
    const SKIP = ['Payment to Vendor%', 'Deposited to Bank%', '%Expense #%', 'Expense head%'];

    public function sourceType(): string
    {
        return 'cash_book';
    }

    public function label(): string
    {
        return 'Cash Book (salary, advances, manual cash)';
    }

    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable
    {
        $branches = implode(',', SourceQuery::branchIds($companyId));
        $dateSql = "(CASE WHEN c.date LIKE '__-__-____%' THEN DATE(STR_TO_DATE(LEFT(c.date, 10), '%d-%m-%Y')) ELSE LEFT(c.date, 10) END)";
        $skip = implode(' AND ', array_map(fn ($p) => "COALESCE(c.narration, '') NOT LIKE " . DB::getPdo()->quote($p), self::SKIP));

        $rows = DB::select("
            SELECT c.id, {$dateSql} AS d, c.branch_id, c.debit, c.credit, c.narration
            FROM cash_ledger c
            WHERE c.branch_id IN ({$branches}) AND {$skip} AND {$dateSql} BETWEEN ? AND ?", [$from, $to]);

        foreach ($rows as $row) {
            $narration = trim((string) $row->narration);
            $doc = new PostingDocument((string) $row->id, $row->d, (int) $row->branch_id, 'Cash: ' . ($narration ?: 'manual entry'), (int) $row->id);
            $out = (float) $row->debit;
            $in = (float) $row->credit;

            if (stripos($narration, 'Employee Salary') === 0) {
                $doc->debit('salaries_payable', $out - $in)->credit('cash', $out - $in);
            } elseif (stripos($narration, 'Advance Salary') === 0 || stripos($narration, 'Loan :') === 0) {
                $doc->debit('employee_advances', $out - $in)->credit('cash', $out - $in);
            } elseif (stripos($narration, 'Payment received from Customer') === 0) {
                $doc->debit('cash', $in - $out)->credit('accounts_receivable', $in - $out);
            } else {
                $doc->debit('cash', $in - $out)->credit('adjustments_clearing', $in - $out);
            }
            yield $doc;
        }
    }
}
