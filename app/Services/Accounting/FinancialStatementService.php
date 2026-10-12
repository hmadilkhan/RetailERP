<?php

namespace App\Services\Accounting;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Phase 1.5: GL se Trial Balance, Income Statement, Balance Sheet, Cash Flow. Sab AccountBalanceService ke balances par.
 * Amounts "natural" shakal me (Revenue / Liability / Equity ka credit balance positive).
 */
class FinancialStatementService
{
    public function __construct(private AccountBalanceService $balances)
    {
    }

    /** Har leaf account: opening, period Dr / Cr, closing (Dr / Cr columns). Groups subtotal ke liye bhi. */
    public function trialBalance(int $companyId, string $from, string $to, ?int $branchId = null): array
    {
        $accounts = $this->balances->accounts($companyId, $from, $to, $branchId);
        $leaves = $accounts->where('is_group', false);

        return [
            'rows' => $accounts->filter(fn ($a) => abs($a->opening) + $a->debit + $a->credit > 0.004),
            'totals' => [
                'opening_dr' => $leaves->sum(fn ($a) => max($a->opening, 0)),
                'opening_cr' => $leaves->sum(fn ($a) => max(-$a->opening, 0)),
                'debit' => $leaves->sum('debit'),
                'credit' => $leaves->sum('credit'),
                'closing_dr' => $leaves->sum(fn ($a) => max($a->closing, 0)),
                'closing_cr' => $leaves->sum(fn ($a) => max(-$a->closing, 0)),
            ],
        ];
    }

    /** Period ka P&L: Revenue − Cost of Sales = Gross Profit − baaqi expense groups = Net Profit. */
    public function incomeStatement(int $companyId, string $from, string $to, ?int $branchId = null): array
    {
        // saal band ki entry (P&L → Retained Earnings) P&L me nahi dikhni chahiye, warna band saal ka munafa 0 aata
        $accounts = $this->balances->accounts($companyId, $from, $to, $branchId, ['year_close']);
        $movement = fn ($a) => AccountBalanceService::natural($a, $a->debit - $a->credit);
        $cogsGroup = AccountBalanceService::topGroupOf($accounts, 'cogs');

        $section = function ($root) use ($accounts, $movement) {
            $lines = $this->leavesUnder($accounts, $root->id)->map(fn ($a) => ['account' => $a, 'amount' => $movement($a)])
                ->filter(fn ($l) => abs($l['amount']) > 0.004)->values();
            return ['title' => $root->name, 'lines' => $lines, 'total' => $lines->sum('amount')];
        };

        $roots = $accounts->whereNull('parent_id');
        $revenue = $roots->where('type', 'Revenue')->map($section)->values();
        $cost = $cogsGroup ? collect([$section($cogsGroup)]) : collect();
        $expenses = $roots->where('type', 'Expense')->reject(fn ($r) => $cogsGroup && $r->id === $cogsGroup->id)->map($section)->values();

        $totalRevenue = $revenue->sum('total');
        $grossProfit = $totalRevenue - $cost->sum('total');

        return [
            'revenue' => $revenue,
            'cost' => $cost,
            'expenses' => $expenses,
            'total_revenue' => $totalRevenue,
            'gross_profit' => $grossProfit,
            'total_expenses' => $expenses->sum('total'),
            'net_profit' => $grossProfit - $expenses->sum('total'),
        ];
    }

    /** As-of balance sheet. Revenue / Expense ka band na hua hissa "Current Year Earnings" (Equity) me. */
    public function balanceSheet(int $companyId, string $asOf, ?int $branchId = null): array
    {
        $accounts = $this->balances->accounts($companyId, null, $asOf, $branchId);
        $closing = fn ($a) => AccountBalanceService::natural($a, $a->closing);

        $section = function ($root) use ($accounts, $closing) {
            $lines = $this->leavesUnder($accounts, $root->id)->map(fn ($a) => ['account' => $a, 'amount' => $closing($a)])
                ->filter(fn ($l) => abs($l['amount']) > 0.004)->values();
            return ['title' => $root->name, 'lines' => $lines, 'total' => $lines->sum('amount')];
        };

        $roots = $accounts->whereNull('parent_id');
        $leaves = $accounts->where('is_group', false);
        $earnings = $leaves->where('type', 'Revenue')->sum(fn ($a) => -$a->closing) - $leaves->where('type', 'Expense')->sum(fn ($a) => $a->closing);

        $assets = $roots->where('type', 'Asset')->map($section)->values();
        $liabilities = $roots->where('type', 'Liability')->map($section)->values();
        $equity = $roots->where('type', 'Equity')->map($section)->values();

        $totalAssets = $assets->sum('total');
        $totalLiabilities = $liabilities->sum('total');
        $totalEquity = $equity->sum('total') + $earnings;

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'earnings' => $earnings,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'difference' => round($totalAssets - $totalLiabilities - $totalEquity, 2),
        ];
    }

    /**
     * Cash Flow (direct method): Cash / Bank accounts ki har entry ka doosra hissa dekh kar category:
     *   Investing = Fixed Assets group, Financing = Equity + Long-term Liabilities group, baaqi Operating.
     * Ek hi entry me cash ↔ bank (transfer) ka cash pe asar 0 — nahi dikhta.
     */
    public function cashFlow(int $companyId, string $from, string $to, ?int $branchId = null): array
    {
        $accounts = $this->balances->accounts($companyId, $from, $to, $branchId);
        $cashIds = $this->cashAccountIds($companyId, $accounts);

        $investingRoot = optional($accounts->firstWhere('system_key', 'accumulated_depreciation'))->parent_id;
        $financingRoot = optional($accounts->firstWhere('system_key', 'loans_payable'))->parent_id;
        $investing = $investingRoot ? $this->leavesUnder($accounts, $investingRoot)->pluck('id')->all() : [];
        $financing = array_merge(
            $financingRoot ? $this->leavesUnder($accounts, $financingRoot)->pluck('id')->all() : [],
            $accounts->where('type', 'Equity')->pluck('id')->all()
        );

        // period ki cash wali entries ki saari lines; contra line ka cash pe asar = −(debit − credit)
        $lines = DB::table('journal_entry_lines as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->where('e.company_id', $companyId)->where('e.status', 'posted')
            ->whereBetween('e.entry_date', [$from, $to])
            ->when($branchId, fn ($q) => $q->where('e.branch_id', $branchId))
            ->whereIn('l.journal_entry_id', function ($q) use ($cashIds) {
                $q->select('journal_entry_id')->from('journal_entry_lines')->whereIn('account_id', $cashIds ?: [0]);
            })
            ->whereNotIn('l.account_id', $cashIds ?: [0])
            ->groupBy('l.account_id')
            ->selectRaw('l.account_id, SUM(l.credit - l.debit) AS effect')
            ->get();

        $sections = ['Operating' => [], 'Investing' => [], 'Financing' => []];
        foreach ($lines as $line) {
            if (abs((float) $line->effect) < 0.005) {
                continue;
            }
            $category = in_array($line->account_id, $investing) ? 'Investing' : (in_array($line->account_id, $financing) ? 'Financing' : 'Operating');
            $sections[$category][] = ['account' => $accounts->firstWhere('id', $line->account_id), 'amount' => (float) $line->effect];
        }

        $cash = $accounts->whereIn('id', $cashIds);
        $opening = $cash->sum('opening');
        $closing = $cash->sum('closing');
        $net = collect($sections)->flatten(1)->sum('amount');

        return [
            'sections' => collect($sections)->map(fn ($lines, $title) => [
                'title' => $title . ' Activities',
                'lines' => collect($lines)->sortBy(fn ($l) => $l['account']->code)->values(),
                'total' => collect($lines)->sum('amount'),
            ])->values(),
            'opening' => $opening,
            'net' => $net,
            'closing' => $closing,
            'difference' => round($opening + $net - $closing, 2),
            'cash_accounts' => $cash->pluck('name'),
        ];
    }

    /** Cash, Bank aur Posting Rules me bank accounts se jode gaye GL accounts */
    private function cashAccountIds(int $companyId, Collection $accounts): array
    {
        $ids = $accounts->whereIn('system_key', ['cash', 'bank'])->pluck('id')->all();
        try {
            $mapped = DB::table('account_mappings')->where('company_id', $companyId)->where('map_type', 'bank_account')->pluck('account_id')->all();
        } catch (\Throwable $e) {
            $mapped = [];
        }
        return array_values(array_unique(array_map('intval', array_merge($ids, $mapped))));
    }

    /** Group ke andar ke saare leaf accounts (group khud leaf ho to wahi) */
    private function leavesUnder(Collection $accounts, int $rootId): Collection
    {
        $root = $accounts->firstWhere('id', $rootId);
        if ($root && !$root->is_group) {
            return collect([$root]);
        }
        $ids = [$rootId];
        $out = collect();
        while ($ids) {
            $children = $accounts->whereIn('parent_id', $ids);
            $out = $out->merge($children->where('is_group', false));
            $ids = $children->where('is_group', true)->pluck('id')->all();
        }
        return $out->sortBy('code')->values();
    }
}
