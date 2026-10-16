<?php

namespace App\Services\Accounting;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Phase 3.2: budget vs actual. Budget = fiscal year (+ ikhtiyari branch / cost center), income / expense account × mahina.
 * Raqam positive: income = aamdani, expense = kharcha. Actual GL se (posted, year_close bahar, budget ka branch / cost center):
 * income = credit − debit, expense = debit − credit. Farq: income me zyada = achha, expense me kam = achha.
 */
class BudgetService
{
    public function create(int $companyId, int $fiscalYearId, string $name, ?int $branchId, ?int $costCenterId, $userId): int
    {
        if (!DB::table('fiscal_years')->where('company_id', $companyId)->where('id', $fiscalYearId)->exists()) {
            throw new RuntimeException('Select a fiscal year.');
        }
        if (trim($name) === '') {
            throw new RuntimeException('Enter a budget name.');
        }
        return DB::table('budgets')->insertGetId(['company_id' => $companyId, 'fiscal_year_id' => $fiscalYearId, 'name' => trim($name),
            'branch_id' => $branchId ?: null, 'cost_center_id' => $costCenterId ?: null, 'created_by' => $userId, 'created_at' => now(), 'updated_at' => now()]);
    }

    public function get(int $companyId, int $id): object
    {
        $b = DB::table('budgets')->where('company_id', $companyId)->where('id', $id)->first();
        if (!$b) {
            throw new RuntimeException('Budget not found.');
        }
        return $b;
    }

    /** fiscal year ke 12 (ya jitne) mahine YYYY-MM */
    public function periods(object $budget): array
    {
        $fy = DB::table('fiscal_years')->find($budget->fiscal_year_id);
        $out = [];
        for ($p = substr($fy->start_date, 0, 7); $p <= substr($fy->end_date, 0, 7); $p = date('Y-m', strtotime($p . '-01 +1 month'))) {
            $out[] = $p;
        }
        return $out;
    }

    public function accounts(int $companyId)
    {
        return DB::table('chart_of_accounts')->where('company_id', $companyId)->whereIn('type', ['Revenue', 'Expense'])
            ->where('is_group', 0)->where('is_active', 1)->orderBy('code')->get(['id', 'code', 'name', 'type']);
    }

    /** account_id => [period => amount] */
    public function grid(object $budget): array
    {
        $out = [];
        foreach (DB::table('budget_lines')->where('budget_id', $budget->id)->get() as $l) {
            $out[$l->account_id][$l->period] = (float) $l->amount;
        }
        return $out;
    }

    /** $grid: account_id => [period => amount]; khali / 0 = row hatao */
    public function save(int $companyId, int $budgetId, array $grid): void
    {
        $budget = $this->get($companyId, $budgetId);
        $periods = array_flip($this->periods($budget));
        $valid = $this->accounts($companyId)->pluck('id')->flip();
        DB::transaction(function () use ($budgetId, $grid, $periods, $valid) {
            foreach ($grid as $accountId => $months) {
                if (!isset($valid[(int) $accountId])) {
                    continue;
                }
                foreach ((array) $months as $period => $amount) {
                    if (!isset($periods[$period])) {
                        continue;
                    }
                    $amount = round((float) str_replace(',', '', (string) $amount), 2);
                    if ($amount < 0) {
                        throw new RuntimeException('Budget amounts cannot be negative.');
                    }
                    $where = ['budget_id' => $budgetId, 'account_id' => (int) $accountId, 'period' => $period];
                    $amount == 0.0
                        ? DB::table('budget_lines')->where($where)->delete()
                        : DB::table('budget_lines')->updateOrInsert($where, ['amount' => $amount, 'updated_at' => now(), 'created_at' => now()]);
                }
            }
        });
    }

    /** pichle saal (same mahine, 12 mahine pehle) ke asal × (1 + %) — account => period => amount (save nahi) */
    public function fromLastYear(object $budget, float $percent): array
    {
        $periods = $this->periods($budget);
        $from = date('Y-m-01', strtotime($periods[0] . '-01 -12 months'));
        $to = date('Y-m-t', strtotime(end($periods) . '-01 -12 months'));
        $out = [];
        foreach ($this->actualQuery($budget, $from, $to)->get() as $r) {
            $period = date('Y-m', strtotime($r->period . '-01 +12 months'));
            $out[$r->account_id][$period] = round(max(0, (float) $r->amount) * (1 + $percent / 100), 0);
        }
        return $out;
    }

    /**
     * mahinon ($fromPeriod..$toPeriod) ka budget vs actual. @return rows by type: [account, budget, actual, variance, pct]
     * variance hamesha "achha = +": income actual − budget, expense budget − actual.
     */
    public function report(object $budget, string $fromPeriod, string $toPeriod): array
    {
        $budgetRows = DB::table('budget_lines')->where('budget_id', $budget->id)->whereBetween('period', [$fromPeriod, $toPeriod])
            ->groupBy('account_id')->selectRaw('account_id, SUM(amount) AS amount')->pluck('amount', 'account_id');
        $actual = [];
        foreach ($this->actualQuery($budget, $fromPeriod . '-01', date('Y-m-t', strtotime($toPeriod . '-01')))->get() as $r) {
            $actual[$r->account_id] = ($actual[$r->account_id] ?? 0) + (float) $r->amount;
        }
        $rows = ['Revenue' => [], 'Expense' => []];
        foreach ($this->accounts($budget->company_id) as $a) {
            $b = (float) ($budgetRows[$a->id] ?? 0);
            $act = round($actual[$a->id] ?? 0, 2);
            if ($b == 0 && $act == 0) {
                continue;
            }
            $variance = $a->type === 'Revenue' ? $act - $b : $b - $act;
            $rows[$a->type][] = ['code' => $a->code, 'name' => $a->name, 'budget' => $b, 'actual' => $act, 'variance' => round($variance, 2),
                'pct' => $b > 0 ? round($act / $b * 100, 1) : null];
        }
        return $rows;
    }

    private function actualQuery(object $budget, string $from, string $to)
    {
        return DB::table('journal_entry_lines as l')->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->join('chart_of_accounts as a', 'a.id', '=', 'l.account_id')
            ->where('e.company_id', $budget->company_id)->where('e.status', 'posted')->whereBetween('e.entry_date', [$from, $to])
            ->where(fn ($q) => $q->whereNull('e.source_type')->orWhere('e.source_type', '<>', 'year_close'))
            ->whereIn('a.type', ['Revenue', 'Expense'])
            ->when($budget->branch_id, fn ($q) => $q->where('e.branch_id', $budget->branch_id))
            ->when($budget->cost_center_id, fn ($q) => $q->where('l.cost_center_id', $budget->cost_center_id))
            ->groupBy('l.account_id', 'a.type', DB::raw("DATE_FORMAT(e.entry_date, '%Y-%m')"))
            ->selectRaw("l.account_id, DATE_FORMAT(e.entry_date, '%Y-%m') AS period,
                SUM(CASE WHEN a.type = 'Revenue' THEN l.credit - l.debit ELSE l.debit - l.credit END) AS amount");
    }
}
