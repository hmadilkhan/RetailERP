<?php

namespace App\Services\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 4.2: accounting health / go-live — ek company ke ledger ki jaanch. Har check:
 *   [group, key, title, status ok|warn|fail|info, value, detail, link, hint]
 * fail = numbers ghalat hain (pehle theek karo), warn = dhyan do, info = maloomat / agla qadam.
 * Sirf padhta hai, kuch nahi likhta.
 */
class AccountingHealthService
{
    /** test me temporary tables ke liye: table => bool */
    public static array $tables = [];

    private int $companyId;
    private object $setting;
    private array $sys = [];
    private array $balances = [];

    public function checks(int $companyId): array
    {
        $this->companyId = $companyId;
        $setting = AccountingSetting::where('company_id', $companyId)->first();
        if (!$setting || !$setting->enabled) {
            return [];
        }
        $this->setting = $setting;
        $this->sys = DB::table('chart_of_accounts')->where('company_id', $companyId)->whereNotNull('system_key')->pluck('id', 'system_key')->all();
        $this->balances = DB::table('journal_entry_lines as l')->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->where('e.company_id', $companyId)->where('e.status', 'posted')
            ->groupBy('l.account_id')->selectRaw('l.account_id, SUM(l.debit) AS dr, SUM(l.credit) AS cr')->get()
            ->mapWithKeys(fn ($r) => [(int) $r->account_id => ['dr' => (float) $r->dr, 'cr' => (float) $r->cr]])->all();

        return array_merge($this->posting(), $this->balanceChecks(), $this->inventory(), $this->setup(), $this->periods(), $this->banksAndAssets(), $this->integrations());
    }

    public function summary(array $checks): array
    {
        $c = collect($checks)->countBy('status');
        return ['fail' => $c['fail'] ?? 0, 'warn' => $c['warn'] ?? 0, 'ok' => $c['ok'] ?? 0, 'info' => $c['info'] ?? 0];
    }

    // ---------------------------------------------------------------- posting

    private function posting(): array
    {
        $out = [];
        $start = $this->setting->posting_start_date;
        $last = $this->setting->last_posted_at;
        if (!$start) {
            $out[] = $this->row('Posting', 'posting_start', 'Automatic posting', 'fail', 'Off', 'No "Automatic Posting From" date — sales, purchases and expenses are not reaching the ledger.', 'accounting-setup', 'Set the date in Accounting Setup (Admin).');
        } else {
            $age = $last ? now()->diffInMinutes($last, true) : null;
            $status = $age === null || $age > 120 ? 'fail' : ($age > 45 ? 'warn' : 'ok');
            $out[] = $this->row('Posting', 'posting_run', 'Automatic posting is running', $status,
                $last ? \Illuminate\Support\Carbon::parse($last)->diffForHumans() : 'never',
                'Posting from ' . date('d M Y', strtotime($start)) . '. It runs every 15 minutes.' . ($status !== 'ok' ? ' The server scheduler may be stopped.' : ''),
                'posting-rules', $status !== 'ok' ? 'Check the server cron (php artisan schedule:run), then use Post Now on Posting Rules.' : null);
        }

        $errors = DB::table('posting_errors')->where('company_id', $this->companyId)->groupBy('level')->selectRaw('level, COUNT(*) AS n')->pluck('n', 'level');
        $out[] = $this->row('Posting', 'posting_errors', 'Documents that could not be posted', ($errors['error'] ?? 0) ? 'fail' : 'ok', (int) ($errors['error'] ?? 0),
            'These source documents are missing from the ledger (closed period, missing account, bad data).', 'posting-rules', 'Open Posting Rules → Needs Attention for the reason of each one.');
        $out[] = $this->row('Posting', 'posting_warnings', 'Posted with a difference (to suspense)', ($errors['warning'] ?? 0) ? 'warn' : 'ok', (int) ($errors['warning'] ?? 0),
            'The source document did not add up, so the difference went to Suspense / Rounding.', 'posting-rules', null);
        return $out;
    }

    // ---------------------------------------------------------------- balances

    private function balanceChecks(): array
    {
        $out = [];
        $dr = array_sum(array_column($this->balances, 'dr'));
        $cr = array_sum(array_column($this->balances, 'cr'));
        $out[] = $this->row('Balances', 'trial_balance', 'Trial balance is balanced', abs($dr - $cr) < 0.5 ? 'ok' : 'fail', $this->money($dr - $cr) . ' difference',
            'Total debit ' . $this->money($dr) . ' / credit ' . $this->money($cr) . '.', 'financial-statements', null);

        $suspense = $this->bal('suspense');
        $out[] = $this->row('Balances', 'suspense', 'Suspense / Rounding balance', abs($suspense) < 1000 ? 'ok' : (abs($suspense) < 100000 ? 'warn' : 'fail'), $this->money($suspense),
            'Differences between source documents and what was posted collect here. It should stay near zero.', $this->gl('suspense'),
            abs($suspense) >= 1000 ? 'Open the account, find the days with big amounts and fix the receipts or post a journal to the right account.' : null);

        $clearing = $this->bal('adjustments_clearing');
        $out[] = $this->row('Balances', 'clearing', 'Adjustments Clearing balance', abs($clearing) < 1000 ? 'ok' : 'warn', $this->money(-$clearing),
            'Manual cash / vendor / customer adjustments whose other side is unknown are parked here.', $this->gl('adjustments_clearing'),
            abs($clearing) >= 1000 ? 'Review the entries and move each one to the right account with a journal entry.' : null);

        $cash = $this->bal('cash');
        if ($cash < -1) {
            $out[] = $this->row('Balances', 'cash_negative', 'Cash in hand is negative', 'fail', $this->money($cash),
                'More cash went out than came in — usually a missing opening balance or sales posted to another account.', $this->gl('cash'), 'Enter the opening cash on Opening Balances.');
        }
        $ar = $this->bal('accounts_receivable');
        if ($ar < -1) {
            $out[] = $this->row('Balances', 'ar_negative', 'Customers owe a negative amount', 'warn', $this->money($ar),
                'Receivables show a credit balance — customer opening balances missing, or the ledger sign setting is wrong.', $this->gl('accounts_receivable'),
                'Check "Customer ledger: Credit increases balance" in Accounting Setup and the customer opening balances.');
        }
        $ap = -$this->bal('accounts_payable');
        if ($ap < -1) {
            $out[] = $this->row('Balances', 'ap_negative', 'Vendors payable is negative', 'warn', $this->money($ap),
                'More was paid to vendors than billed — vendor opening balances or purchases are missing.', $this->gl('accounts_payable'), null);
        }

        // ye saal: COGS > Sales = cost data ghalat / test data
        $fy = DB::table('fiscal_years')->where('company_id', $this->companyId)->where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'))->first();
        if ($fy && !empty($this->sys['sales']) && !empty($this->sys['cogs'])) {
            $y = DB::table('journal_entry_lines as l')->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
                ->where('e.company_id', $this->companyId)->where('e.status', 'posted')->whereBetween('e.entry_date', [$fy->start_date, $fy->end_date])
                ->whereIn('l.account_id', [$this->sys['sales'], $this->sys['cogs']])
                ->groupBy('l.account_id')->selectRaw('l.account_id, SUM(l.debit - l.credit) AS net')->pluck('net', 'account_id');
            $sales = -(float) ($y[$this->sys['sales']] ?? 0);
            $cogs = (float) ($y[$this->sys['cogs']] ?? 0);
            if ($sales > 0) {
                $pct = round($cogs / $sales * 100);
                $out[] = $this->row('Balances', 'margin', 'Cost of sales vs sales (this year)', $pct > 100 ? 'fail' : ($pct > 90 || $pct < 5 ? 'warn' : 'ok'), $pct . '%',
                    'COGS ' . $this->money($cogs) . ' on sales ' . $this->money($sales) . '. Above 100% means product costs are wrong (or test data).', 'inventory-valuation',
                    $pct > 90 || $pct < 5 ? 'Check product costs on Inventory Valuation.' : null);
            }
        }
        return $out;
    }

    // ---------------------------------------------------------------- inventory

    private function inventory(): array
    {
        $out = [];
        $v = DB::table('product_valuations')->where('company_id', $this->companyId)->selectRaw('COUNT(*) AS n,
            SUM(CASE WHEN qty > 0 THEN value ELSE 0 END) AS value,
            SUM(CASE WHEN avg_cost <= 0 AND (qty <> 0 OR unvalued_sales > 0) THEN 1 ELSE 0 END) AS no_cost,
            SUM(CASE WHEN qty < 0 THEN 1 ELSE 0 END) AS negative, MAX(computed_at) AS computed_at')->first();
        if (!$v || !$v->n) {
            return [$this->row('Inventory', 'valuation', 'Inventory valuation', 'warn', 'not run', 'Product costs have not been calculated yet.', 'inventory-valuation', 'Press Recalculate on Inventory Valuation.')];
        }
        $age = $v->computed_at ? now()->diffInHours($v->computed_at, true) : 999;
        $out[] = $this->row('Inventory', 'valuation_age', 'Valuation is up to date', $age > 24 ? 'warn' : 'ok', \Illuminate\Support\Carbon::parse($v->computed_at)->diffForHumans(),
            'Average costs are recalculated with each posting run.', 'inventory-valuation', null);

        $gl = $this->bal('inventory');
        $diff = $gl - (float) $v->value;
        $pct = abs((float) $v->value) > 0 ? abs($diff) / abs((float) $v->value) * 100 : (abs($diff) > 1 ? 100 : 0);
        $out[] = $this->row('Inventory', 'inventory_gl', 'Ledger inventory = stock value', $pct < 1 ? 'ok' : ($pct < 10 ? 'warn' : 'fail'), $this->money($diff) . ' difference',
            'Ledger ' . $this->money($gl) . ' vs stock at average cost ' . $this->money($v->value) . '. Differences come from stock changes that have no posting rule or an opening stock that was not entered.',
            'inventory-valuation', $pct >= 1 ? 'Enter opening stock on Opening Balances, or post a journal for the difference after review.' : null);

        $since = date('Y-m-d', strtotime('-90 days'));
        $unvalued = (int) DB::table('stock_valuation_daily')->where('company_id', $this->companyId)->where('date', '>=', $since)->sum('unvalued_lines');
        $out[] = $this->row('Inventory', 'unvalued', 'Sales without a cost (last 90 days)', $unvalued ? 'warn' : 'ok', number_format($unvalued) . ' lines',
            'These sale lines had no product cost, so their cost of sales was posted as 0 and profit is overstated.', 'inventory-valuation',
            $unvalued ? 'Filter "Needs cost" on Inventory Valuation and enter purchase / opening costs.' : null);
        $out[] = $this->row('Inventory', 'no_cost', 'Products without a cost', $v->no_cost ? 'warn' : 'ok', number_format($v->no_cost), 'Products in stock or sold that have no average cost.', 'inventory-valuation', null);
        $out[] = $this->row('Inventory', 'negative_stock', 'Products with negative stock', $v->negative ? 'info' : 'ok', number_format($v->negative),
            'Sold more than was received — usually purchases or opening stock not entered.', 'inventory-valuation', null);
        return $out;
    }

    // ---------------------------------------------------------------- setup

    private function setup(): array
    {
        $out = [];
        $opening = DB::table('journal_entries as e')->where('e.company_id', $this->companyId)->where('e.source_type', 'opening_balance')->where('e.status', 'posted')
            ->whereNotExists(fn ($q) => $q->from('journal_entries as r')->whereColumn('r.reversal_of_id', 'e.id'))->whereNull('e.reversal_of_id')->exists();
        $start = $this->setting->posting_start_date;
        $firstSale = $start ? DB::table('sales_receipts')->whereIn('branch', SourceQuery::branchIds($this->companyId))->where('date', 'like', '____-__-__')->where('date', '>=', '2015-01-01')->min('date') : null;
        $needsOpening = $start && $firstSale && $firstSale < $start;
        $out[] = $this->row('Setup', 'opening', 'Opening balances', $opening ? 'ok' : ($needsOpening ? 'warn' : 'ok'), $opening ? 'entered' : ($needsOpening ? 'missing' : 'not needed'),
            $needsOpening && !$opening ? 'The business traded before ' . date('d M Y', strtotime($start)) . ' (first sale ' . date('d M Y', strtotime($firstSale)) . '), so cash, stock, customers and vendors start from zero.' : 'Ledger history covers the whole business, or opening balances are entered.',
            'opening-balances', $needsOpening && !$opening ? 'Use Fill from system on Opening Balances, review and save.' : null);

        $since = date('Y-m-d', strtotime('-90 days'));
        $mapped = DB::table('account_mappings')->where('company_id', $this->companyId)->where('map_type', 'expense_category')->pluck('map_key')->all();
        $used = DB::table('expenses')->whereIn('branch_id', SourceQuery::branchIds($this->companyId))->where('date', '>=', $since)
            ->distinct()->pluck('exp_cat_id')->map(fn ($v) => (string) $v)->all();
        $unmapped = count(array_diff($used, $mapped));
        $out[] = $this->row('Setup', 'expense_mapping', 'Expense categories linked to accounts', $unmapped ? 'info' : 'ok', $unmapped ? $unmapped . ' of ' . count($used) . ' not linked' : 'all linked',
            'Expenses in categories without a link are posted to General Expenses, so the P&L shows them together.', 'posting-rules', $unmapped ? 'Link each category to an expense account on Posting Rules.' : null);
        return $out;
    }

    // ---------------------------------------------------------------- periods

    private function periods(): array
    {
        $out = [];
        $nextMonth = date('Y-m-d', strtotime('first day of next month'));
        $hasNext = DB::table('fiscal_years')->where('company_id', $this->companyId)->where('start_date', '<=', $nextMonth)->where('end_date', '>=', $nextMonth)->exists();
        $out[] = $this->row('Period close', 'next_year', 'Fiscal year for next month exists', $hasNext ? 'ok' : 'fail', $hasNext ? 'yes' : 'no',
            'Without it nothing dated next month can be posted.', 'fiscal-years', $hasNext ? null : 'Create the next fiscal year on Fiscal Years.');

        $cutoff = date('Y-m-d', strtotime('first day of -2 months'));
        $open = DB::table('accounting_periods as p')->join('fiscal_years as f', 'f.id', '=', 'p.fiscal_year_id')
            ->where('f.company_id', $this->companyId)->where('p.status', 'open')->where('p.end_date', '<', $cutoff)->count();
        $out[] = $this->row('Period close', 'old_open', 'Old months still open', $open ? 'info' : 'ok', number_format($open),
            'Closing a month stops back-dated changes from altering reports already given out. Automatic posting cannot change a closed month either.', 'fiscal-years',
            $open ? 'After checking the numbers, close (or lock) the old months on Fiscal Years.' : null);
        return $out;
    }

    // ---------------------------------------------------------------- banks / assets

    private function banksAndAssets(): array
    {
        $out = [];
        if ($this->has('bank_reconciliations')) {
            $since = date('Y-m-d', strtotime('-90 days'));
            $stale = [];
            foreach (app(BankReconciliationService::class)->bankAccounts($this->companyId) as $a) {
                $active = DB::table('journal_entry_lines as l')->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
                    ->where('e.company_id', $this->companyId)->where('e.status', 'posted')->where('l.account_id', $a->id)->where('e.entry_date', '>=', $since)->exists();
                if (!$active) {
                    continue;
                }
                $last = DB::table('bank_reconciliations')->where('company_id', $this->companyId)->where('account_id', $a->id)->where('status', 'reconciled')->max('statement_to');
                if (!$last || $last < date('Y-m-d', strtotime('-60 days'))) {
                    $stale[] = $a->code . ' ' . $a->name . ($last ? ' (last ' . date('d M Y', strtotime($last)) . ')' : ' (never)');
                }
            }
            $out[] = $this->row('Banks & assets', 'bank_rec', 'Bank accounts reconciled', $stale ? 'warn' : 'ok', $stale ? count($stale) . ' behind' : 'up to date',
                $stale ? implode(', ', $stale) : 'Every active bank account was reconciled in the last 60 days.', 'bank-reconciliation',
                $stale ? 'Import the bank statement on Bank Reconciliation and match it.' : null);
        }
        if ($this->has('fixed_assets')) {
            $lastMonth = date('Y-m', strtotime('first day of last month'));
            $due = DB::table('fixed_assets as a')->where('a.company_id', $this->companyId)->where('a.status', 'active')
                ->where('a.depreciation_start', '<=', date('Y-m-t', strtotime($lastMonth . '-01')))
                ->whereNotExists(fn ($q) => $q->from('fixed_asset_depreciations as d')->whereColumn('d.fixed_asset_id', 'a.id')->where('d.period', $lastMonth))
                ->whereRaw('a.cost - a.salvage_value - a.opening_accumulated - COALESCE((SELECT SUM(d2.amount) FROM fixed_asset_depreciations d2 WHERE d2.fixed_asset_id = a.id), 0) > 0.01')
                ->count();
            $out[] = $this->row('Banks & assets', 'depreciation', 'Depreciation posted for last month', $due ? 'warn' : 'ok', $due ? $due . ' asset(s) missing' : 'done',
                'Depreciation runs on the 1st of each month for the previous month.', 'fixed-assets', $due ? 'Use "Run depreciation" on Fixed Assets.' : null);
        }
        return $out;
    }

    // ---------------------------------------------------------------- integrations

    private function integrations(): array
    {
        $out = [];
        if ($this->has('qbo_journal_sync') && Schema::hasColumn('quickbook_settings', 'sync_journals')
            && DB::table('quickbook_settings')->where('company_id', $this->companyId)->where('sync_journals', 1)->exists()) {
            $failed = DB::table('qbo_journal_sync')->where('company_id', $this->companyId)->where('status', 'failed')->count();
            $out[] = $this->row('Integrations', 'qbo', 'QuickBooks sync', $failed ? 'warn' : 'ok', $failed ? $failed . ' not sent' : 'all sent',
                'Journal entries that QuickBooks rejected or could not map.', 'quickbooks-sync', $failed ? 'Link the missing accounts on QuickBooks Sync and Retry.' : null);
        }
        if ($this->has('sales_tax_filings')) {
            $p = date('Y-m', strtotime('first day of last month'));
            $tax = app(SalesTaxService::class)->summary($this->companyId, $p, $p)[$p];
            if ($tax['fbr_tax'] > 0 || $tax['srb_tax'] > 0) {
                $filed = DB::table('sales_tax_filings')->where('company_id', $this->companyId)->where('period', $p)->pluck('authority')->all();
                $missing = array_keys(array_filter(['fbr' => $tax['fbr_tax'] > 0 && !in_array('fbr', $filed, true), 'srb' => $tax['srb_tax'] > 0 && !in_array('srb', $filed, true)]));
                $out[] = $this->row('Integrations', 'sales_tax', 'Sales tax return for ' . date('M Y', strtotime($p . '-01')), $missing ? 'info' : 'ok',
                    $missing ? strtoupper(implode(' + ', $missing)) . ' not recorded' : 'recorded',
                    'Output tax ' . $this->money($tax['fbr_tax'] + $tax['srb_tax']) . ($tax['missing_fbr'] ? '; ' . number_format($tax['missing_fbr']) . ' taxed receipts not sent to FBR' : '') . '.',
                    'sales-tax', $missing ? 'Record the return (CPR and amount paid) on Sales Tax.' : null);
            }
        }
        return $out;
    }

    // ---------------------------------------------------------------- helpers

    /** debit − credit of a system account */
    private function bal(string $key): float
    {
        $id = $this->sys[$key] ?? null;
        return $id && isset($this->balances[$id]) ? round($this->balances[$id]['dr'] - $this->balances[$id]['cr'], 2) : 0.0;
    }

    private function gl(string $key): string
    {
        return isset($this->sys[$key]) ? 'general-ledger?account=' . $this->sys[$key] : 'general-ledger';
    }

    private function has(string $table): bool
    {
        return self::$tables[$table] ??= Schema::hasTable($table);
    }

    private function money($v): string
    {
        return number_format((float) $v, 0);
    }

    private function row(string $group, string $key, string $title, string $status, $value, string $detail, ?string $link, ?string $hint): array
    {
        return compact('group', 'key', 'title', 'status', 'value', 'detail', 'link', 'hint');
    }
}
