<?php

namespace App\Services\Accounting;

use App\Models\Accounting\ChartOfAccount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Phase 1.5: saari financial reports ka ek hi hisaab. Sirf posted entries.
 * Har account ke liye: opening (from se pehle), period debit / credit, closing — sab "debit − credit" ki shakal me
 * (positive = debit balance). Groups apne andar ke saare accounts ka jod.
 */
class AccountBalanceService
{
    /** Debit balance jin ka normal hai; baaqi (Liability, Equity, Revenue) credit-normal */
    const DEBIT_NORMAL = ['Asset', 'Expense'];

    /**
     * @return Collection<int, object> har account (groups samet) — tree order me, ->depth, ->opening, ->debit, ->credit, ->closing
     */
    public function accounts(int $companyId, ?string $from, string $to, ?int $branchId = null, array $excludeSources = []): Collection
    {
        $accounts = ChartOfAccount::forCompany($companyId)->orderBy('code')->get();
        $totals = $this->totals($companyId, $from, $to, $branchId, $excludeSources);

        foreach ($accounts as $a) {
            $t = $totals[$a->id] ?? null;
            $a->opening = (float) ($t->opening ?? 0);
            $a->debit = (float) ($t->debit ?? 0);
            $a->credit = (float) ($t->credit ?? 0);
        }

        // groups: bachon ka jod (neeche se upar)
        $byParent = $accounts->groupBy(fn ($a) => $a->parent_id ?? 0);
        $roll = function ($account) use (&$roll, $byParent) {
            foreach ($byParent[$account->id] ?? [] as $child) {
                $roll($child);
                $account->opening += $child->opening;
                $account->debit += $child->debit;
                $account->credit += $child->credit;
            }
        };
        foreach ($byParent[0] ?? [] as $root) {
            $roll($root);
        }

        // tree order + depth + closing
        $ordered = collect();
        $walk = function ($parentId, $depth) use (&$walk, $byParent, $ordered) {
            foreach ($byParent[$parentId] ?? [] as $a) {
                $a->depth = $depth;
                $a->closing = round($a->opening + $a->debit - $a->credit, 2);
                $ordered->push($a);
                $walk($a->id, $depth + 1);
            }
        };
        $walk(0, 0);

        return $ordered;
    }

    /** Account ka "natural" amount — debit-normal pe debit balance positive, credit-normal pe credit balance positive. */
    public static function natural(object $account, float $amount): float
    {
        return in_array($account->type, self::DEBIT_NORMAL, true) ? $amount : -$amount;
    }

    /** Jis account / group ke andar $systemKey wala account hai us ka sab se ooncha group (e.g. cogs → 5000 Cost of Sales) */
    public static function topGroupOf(Collection $accounts, string $systemKey): ?object
    {
        $node = $accounts->firstWhere('system_key', $systemKey);
        while ($node && $node->parent_id) {
            $node = $accounts->firstWhere('id', $node->parent_id);
        }
        return $node;
    }

    /** Ek query: account-wise opening (from se pehle) + period debit / credit. */
    private function totals(int $companyId, ?string $from, string $to, ?int $branchId, array $excludeSources = []): Collection
    {
        $from = $from ?: '1900-01-01';

        return DB::table('journal_entry_lines as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->where('e.company_id', $companyId)
            ->where('e.status', 'posted')
            ->where('e.entry_date', '<=', $to)
            ->when($branchId, fn ($q) => $q->where('e.branch_id', $branchId))
            ->when($excludeSources, fn ($q) => $q->whereNotIn('e.source_type', $excludeSources))
            ->groupBy('l.account_id')
            ->selectRaw('l.account_id,
                SUM(CASE WHEN e.entry_date < ? THEN l.debit - l.credit ELSE 0 END) AS opening,
                SUM(CASE WHEN e.entry_date >= ? THEN l.debit ELSE 0 END) AS debit,
                SUM(CASE WHEN e.entry_date >= ? THEN l.credit ELSE 0 END) AS credit', [$from, $from, $from])
            ->get()->keyBy('account_id');
    }
}
