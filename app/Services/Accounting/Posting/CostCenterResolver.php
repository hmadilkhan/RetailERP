<?php

namespace App\Services\Accounting\Posting;

use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Phase 3.4: automatic posting ke liye cost center (cost_center_assignments):
 *   expense_category → us expense ki line; branch → us branch ki income / expense lines (jin pe khud cost center nahi).
 * Koi qaida (ya table) na ho to null — tab lines / hash bilkul pehle jaise.
 */
class CostCenterResolver
{
    private static array $instances = [];

    private array $branch = [];
    private array $category = [];
    private array $pnlAccounts = [];

    public static function forCompany(int $companyId): self
    {
        return self::$instances[$companyId] ??= new self($companyId);
    }

    /** naya posting run — qaide dobara padho */
    public static function flush(): void
    {
        self::$instances = [];
    }

    private function __construct(int $companyId)
    {
        try {
            $rows = DB::table('cost_center_assignments as a')->join('cost_centers as c', 'c.id', '=', 'a.cost_center_id')
                ->where('a.company_id', $companyId)->where('c.is_active', 1)->get(['a.source', 'a.source_id', 'a.cost_center_id']);
        } catch (Throwable $e) {
            return; // migration se pehle
        }
        foreach ($rows as $r) {
            $r->source === 'branch' ? $this->branch[(int) $r->source_id] = (int) $r->cost_center_id : $this->category[(int) $r->source_id] = (int) $r->cost_center_id;
        }
        if ($this->branch) {
            $this->pnlAccounts = DB::table('chart_of_accounts')->where('company_id', $companyId)->whereIn('type', ['Revenue', 'Expense'])
                ->pluck('id')->flip()->all();
        }
    }

    public function expenseCategory(?int $categoryId): ?int
    {
        return $categoryId ? ($this->category[$categoryId] ?? null) : null;
    }

    public function branchDefault(?int $branchId, int $accountId): ?int
    {
        if (!$branchId || !isset($this->branch[$branchId]) || !isset($this->pnlAccounts[$accountId])) {
            return null;
        }
        return $this->branch[$branchId];
    }
}
