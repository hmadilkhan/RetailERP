<?php

namespace App\Services\Accounting\Posting\Posters;

use App\Services\Accounting\Posting\AccountResolver;
use App\Services\Accounting\Posting\Poster;
use App\Services\Accounting\Posting\PostingDocument;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;

/**
 * Salary ka kharcha (accrual) — har payslip ki entry (ref payslip_id), payslip_date pe:
 *   Dr Salaries & Wages        = net + advance + loan + tax + EOBI + security deposit
 *   Cr Salaries Payable        = net + tax + EOBI + security deposit   (employee / govt ko abhi dena hai)
 *   Cr Employee Advances       = advance + loan deduction              (pehle diya hua paisa wapas)
 * Paisa nikalna (Employee Salary / Advance / Loan) CashBookPoster karta hai — payslip pe cash nahi hilta.
 * Branch = employee ki active shift branch (ek se zyada ho to pehli — payslip do baar na aaye).
 */
class PayrollPoster implements Poster
{
    public function sourceType(): string
    {
        return 'payroll';
    }

    public function label(): string
    {
        return 'Payroll (payslips)';
    }

    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable
    {
        $branches = implode(',', SourceQuery::branchIds($companyId));

        $rows = DB::select("
            SELECT p.payslip_id, LEFT(p.payslip_date, 10) AS d, p.emp_id,
                   (SELECT MIN(es.branch_id) FROM employee_shift_details es WHERE es.emp_id = p.emp_id AND es.status_id = 1) AS branch_id,
                   COALESCE(p.net_salary + 0, 0) AS net,
                   COALESCE(p.advance_amount + 0, 0) + COALESCE(p.loan_amount + 0, 0) AS recovered,
                   COALESCE(p.tax_amount + 0, 0) + COALESCE(p.eobi + 0, 0) + COALESCE(p.security_deposit + 0, 0) AS withheld
            FROM payslip p
            WHERE LEFT(p.payslip_date, 10) BETWEEN ? AND ?
            HAVING branch_id IN ({$branches})", [$from, $to]);

        foreach ($rows as $row) {
            $doc = new PostingDocument((string) $row->payslip_id, $row->d, (int) $row->branch_id,
                'Payslip #' . $row->payslip_id . ' — employee #' . $row->emp_id, (int) $row->payslip_id);
            $doc->debit('salary_expense', $row->net + $row->recovered + $row->withheld)
                ->credit('salaries_payable', $row->net + $row->withheld)
                ->credit('employee_advances', $row->recovered, 'Advance / loan recovered');
            yield $doc;
        }
    }
}
