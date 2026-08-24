<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Hard-deletes a company's sales history.
 *
 * WHY THIS IS NOT A SIMPLE CASCADE
 * --------------------------------
 * 1. `sales_receipts` has NO foreign keys at all, so nothing cascades. Every
 *    child table has to be cleaned explicitly, children before parents.
 * 2. `sales_receipts` has NO `company_id`. Ownership is `company -> branch ->
 *    sales_receipts.branch` (note: the column is `branch`, not `branch_id`).
 * 3. Scoping by `terminal_id` is UNSAFE: ~23% of receipts point at terminals
 *    that no longer exist in `terminal_details` (company 17 has 0 registered
 *    terminals but 279k receipts). `branch` is the only reliable authority.
 * 4. `sales_opening.user_id` actually holds a BRANCH id, not a user id
 *    (verified: 35,903/36,232 match `branch`, 0 match `users`).
 * 5. Openings are deleted by OWNERSHIP ONLY, never because a receipt points at
 *    them: 3,616 receipts reference an opening owned by a *different* company,
 *    so following receipt->opening would delete another tenant's day-close data.
 * 6. Soft-deleted branches/terminals still own live sales, so they are included.
 *
 * Default mode is a dry run; nothing is deleted unless $force is true.
 */
class SalesPurgeService
{
    /**
     * Child tables keyed by `sales_receipts.id`. [table, column]
     */
    private const RECEIPT_TABLES = [
        // --- core sale documents --------------------------------------------
        ['sales_receipt_details',        'receipt_id'],
        ['sales_account_general',        'receipt_id'],
        ['sales_account_subdetails',     'receipt_id'],
        ['sales_creditcard_details',     'receipt_id'],
        ['sales_receipts_services',      'sales_receipt_id'],
        ['sales_receipt_addons',         'receipt_id'],
        ['sales_receipt_variations',     'receipt_id'],
        ['sales_receipt_discounts',      'receipt_id'],
        ['sales_receipt_online_payment', 'receipt_id'],
        ['sales_receipt_audios',         'order_id'],
        ['sales_receipts_assign',        'receipt_id'],
        ['sales_return',                 'receipt_id'],
        ['sales_table_hold_unhold',      'receipt_id'],
        // --- logs / status ---------------------------------------------------
        ['discount_logs',                'receipt_id'],
        ['orders_logs',                  'order_id'],
        ['orders_calling',               'order_id'],
        ['sales_online_order_status',    'order_id'],
        ['sales_online_item_status',     'order_id'],
        ['accept_deliveries',            'order_id'],
        // --- financial / ledger ----------------------------------------------
        ['service_provider_orders',      'receipt_id'],
        ['service_provider_ladger',      'receipt_id'],
        ['customer_payment_log',         'receipt_id'],
        ['customer_advance_payment_log', 'receipt_id'],
        ['customer_account',             'receipt_no'], // int column holding sales_receipts.id
        ['master_account',               'receipt_no'],
        ['master_assign',                'receipt_no'],
        ['job_order_customer',           'receipt_no'],
    ];

    /**
     * Tables keyed by `sales_opening.opening_id`.
     * `expenses` is deliberately absent - it is reported as a warning instead.
     */
    private const OPENING_TABLES = [
        ['sales_closing',           'opening_id'],
        ['sales_cash_in',           'opening_id'],
        ['sales_cash_out',          'opening_id'],
        ['sales_declaration_local', 'opening_id'],
        ['daily_recipe_usage',      'opening_id'],
        ['sales_return',            'opening_id'], // returns recorded without a receipt link
    ];

    /** Ledger tables dropped from the run when include_ledger is false. */
    private const LEDGER_TABLES = [
        'service_provider_ladger',
        'customer_payment_log',
        'customer_advance_payment_log',
        'customer_account',
        'master_account',
    ];

    private string $tmpReceipts = 'tmp_purge_receipt_ids';
    private string $tmpOpenings = 'tmp_purge_opening_ids';

    /**
     * @param  array $options branch_id[], terminal_id[], from, to, force,
     *                        include_openings, include_ledger, chunk
     * @return array report
     */
    public function purge(int $companyId, array $options = []): array
    {
        $force           = (bool) ($options['force'] ?? false);
        $includeOpenings = (bool) ($options['include_openings'] ?? true);
        $includeLedger   = (bool) ($options['include_ledger'] ?? true);
        $chunk           = max(500, (int) ($options['chunk'] ?? 5000));

        $branchFilter   = array_values(array_filter(array_map('intval', (array) ($options['branch_id'] ?? []))));
        $terminalFilter = array_values(array_filter(array_map('intval', (array) ($options['terminal_id'] ?? []))));
        $from           = $options['from'] ?? null;
        $to             = $options['to'] ?? null;

        if (($from && !$to) || ($to && !$from)) {
            throw new InvalidArgumentException('Both --from and --to must be supplied together.');
        }

        $company = DB::table('company')->where('company_id', $companyId)->first();
        if (!$company) {
            throw new InvalidArgumentException("Company {$companyId} not found.");
        }

        // ---- 1. resolve hierarchy (soft-deleted rows included on purpose) ----
        $branchIds = DB::table('branch')->where('company_id', $companyId)
            ->pluck('branch_id')->map('intval')->all();

        if ($branchFilter) {
            $invalid = array_diff($branchFilter, $branchIds);
            if ($invalid) {
                throw new InvalidArgumentException(
                    'Branch(es) ' . implode(',', $invalid) . " do not belong to company {$companyId}."
                );
            }
            $branchIds = $branchFilter;
        }

        if (!$branchIds) {
            return $this->emptyReport($company, 'Company has no branches - nothing to purge.');
        }

        $terminalIds = DB::table('terminal_details')->whereIn('branch_id', $branchIds)
            ->pluck('terminal_id')->map('intval')->all();

        if ($terminalFilter) {
            $invalid = array_diff($terminalFilter, $terminalIds);
            if ($invalid) {
                throw new InvalidArgumentException(
                    'Terminal(s) ' . implode(',', $invalid) . ' do not belong to the selected branches.'
                );
            }
            $terminalIds = $terminalFilter;
        }

        // ---- 2. receipt id set ------------------------------------------------
        $receiptQuery = DB::table('sales_receipts')->whereIn('branch', $branchIds);
        if ($terminalFilter) {
            $receiptQuery->whereIn('terminal_id', $terminalFilter);
        }
        if ($from) {
            $receiptQuery->whereBetween('date', [$from, $to]);
        }
        $receiptCount = (clone $receiptQuery)->count();

        // ---- 3. opening id set (OWNERSHIP ONLY) -------------------------------
        $openingIds = [];
        if ($includeOpenings) {
            $openingQuery = DB::table('sales_opening')
                ->where(function ($q) use ($branchIds, $terminalIds) {
                    $q->whereIn('user_id', $branchIds); // user_id really holds branch_id
                    if ($terminalIds) {
                        $q->orWhereIn('terminal_id', $terminalIds);
                    }
                });
            if ($from) {
                $openingQuery->whereBetween('date', [$from, $to]);
            }
            $openingIds = $openingQuery->pluck('opening_id')->map('intval')->all();
        }

        $report = [
            'company'   => ['id' => $companyId, 'name' => $company->name],
            'branches'  => $branchIds,
            'terminals' => $terminalIds,
            'filters'   => array_filter([
                'branch_id'   => $branchFilter ?: null,
                'terminal_id' => $terminalFilter ?: null,
                'from'        => $from,
                'to'          => $to,
            ]),
            'dry_run'    => !$force,
            'receipts'   => $receiptCount,
            'tables'     => [],
            'warnings'   => [],
            'skipped'    => [],
            'total_rows' => 0,
        ];

        if ($receiptCount === 0 && !$openingIds) {
            $report['warnings'][] = 'Nothing matched the given filters.';
            return $report;
        }

        try {
            // ---- 4. stage ids (one scan per child table instead of N) --------
            $this->stageFromQuery($this->tmpReceipts, (clone $receiptQuery)->select('id'));

            if ($openingIds) {
                // Never delete an opening that still has surviving receipts.
                $orphans = $this->openingsWithoutSurvivingReceipts($openingIds);
                $report['skipped']['sales_opening_still_in_use'] = count($openingIds) - count($orphans);
                $openingIds = $orphans;
                $this->stageFromList($this->tmpOpenings, $openingIds);
            }

            // ---- 5. children --------------------------------------------------
            $receiptTables = self::RECEIPT_TABLES;
            if (!$includeLedger) {
                $receiptTables = array_values(array_filter(
                    $receiptTables,
                    fn ($t) => !in_array($t[0], self::LEDGER_TABLES, true)
                ));
            }

            foreach ($receiptTables as [$table, $column]) {
                $report['tables'][$table] = $this->countOrDelete(
                    $table, $column, $this->tmpReceipts, $force, $chunk
                );
            }

            // master_assign_details hangs off master_assign.assign_id, so the ids
            // must be resolved before master_assign rows are removed above.
            $report['tables']['master_assign_details'] = $this->purgeMasterAssignDetails($force, $chunk);

            // Legacy junk: sales_cheque_details.receipt_no is a varchar matching
            // neither id nor receipt_no reliably (1/18 and 0/18), so match both.
            $report['tables']['sales_cheque_details'] = $this->purgeChequeDetails($force);

            if ($openingIds) {
                foreach (self::OPENING_TABLES as [$table, $column]) {
                    $report['tables'][$table . ' (by opening)'] = $this->countOrDelete(
                        $table, $column, $this->tmpOpenings, $force, $chunk
                    );
                }

                if ($includeLedger) {
                    $report['tables']['customer_account (by opening)'] = $this->countOrDelete(
                        'customer_account', 'opening_id', $this->tmpOpenings, $force, $chunk
                    );
                }

                $expenses = DB::table('expenses')
                    ->whereIn('opening_id', fn ($q) => $q->select('id')->from($this->tmpOpenings))
                    ->count();
                if ($expenses > 0) {
                    $report['warnings'][] = "{$expenses} rows in `expenses` reference openings being "
                        . 'deleted and will be left orphaned (expenses are outside the chosen scope).';
                }
            }

            // ---- 6. parents last ----------------------------------------------
            $report['tables']['sales_receipts'] = $this->countOrDelete(
                'sales_receipts', 'id', $this->tmpReceipts, $force, $chunk
            );

            if ($openingIds) {
                $report['tables']['sales_opening'] = $this->countOrDelete(
                    'sales_opening', 'opening_id', $this->tmpOpenings, $force, $chunk
                );
            }
        } finally {
            $this->dropTemp();
        }

        $report['total_rows'] = array_sum($report['tables']);

        return $report;
    }

    // ------------------------------------------------------------------------

    /** Openings that will have no receipts left once the purge completes. */
    private function openingsWithoutSurvivingReceipts(array $openingIds): array
    {
        $keep = [];
        foreach (array_chunk($openingIds, 5000) as $batch) {
            $keep = array_merge($keep, DB::table('sales_receipts')
                ->whereIn('opening_id', $batch)
                ->whereNotIn('id', fn ($q) => $q->select('id')->from($this->tmpReceipts))
                ->distinct()->pluck('opening_id')->map('intval')->all());
        }

        return array_values(array_diff($openingIds, $keep));
    }

    private function createTemp(string $tmp): void
    {
        DB::statement("DROP TEMPORARY TABLE IF EXISTS `{$tmp}`");
        DB::statement("CREATE TEMPORARY TABLE `{$tmp}` (id BIGINT NOT NULL, PRIMARY KEY(id)) ENGINE=InnoDB");
    }

    private function stageFromQuery(string $tmp, $query): void
    {
        $this->createTemp($tmp);
        DB::insert("INSERT IGNORE INTO `{$tmp}` (id) " . $query->toSql(), $query->getBindings());
    }

    private function stageFromList(string $tmp, array $ids): void
    {
        $this->createTemp($tmp);
        foreach (array_chunk($ids, 1000) as $batch) {
            DB::table($tmp)->insertOrIgnore(array_map(fn ($i) => ['id' => $i], $batch));
        }
    }

    private function dropTemp(): void
    {
        DB::statement("DROP TEMPORARY TABLE IF EXISTS `{$this->tmpReceipts}`");
        DB::statement("DROP TEMPORARY TABLE IF EXISTS `{$this->tmpOpenings}`");
    }

    /**
     * Counts matching rows, or deletes them in bounded batches.
     *
     * Batching matters: orders_logs, customer_account, service_provider_ladger,
     * sales_receipts_services and sales_receipts.branch have NO index on the
     * link column, so an unbounded delete would hold locks across a full scan of
     * a multi-hundred-thousand row table on a live production database.
     */
    private function countOrDelete(
        string $table,
        string $column,
        string $tmp,
        bool $force,
        int $chunk
    ): int {
        if (!$force) {
            return DB::table($table)
                ->whereIn($column, fn ($q) => $q->select('id')->from($tmp))
                ->count();
        }

        $deleted = 0;
        do {
            $n = DB::affectingStatement(
                "DELETE FROM `{$table}` WHERE `{$column}` IN (SELECT id FROM `{$tmp}`) LIMIT {$chunk}"
            );
            $deleted += $n;
        } while ($n > 0);

        return $deleted;
    }

    private function purgeMasterAssignDetails(bool $force, int $chunk): int
    {
        $assignIds = DB::table('master_assign')
            ->whereIn('receipt_no', fn ($q) => $q->select('id')->from($this->tmpReceipts))
            ->pluck('assign_id')->all();

        if (!$assignIds) {
            return 0;
        }

        $total = 0;
        foreach (array_chunk($assignIds, $chunk) as $batch) {
            $q = DB::table('master_assign_details')->whereIn('assign_id', $batch);
            $total += $force ? $q->delete() : $q->count();
        }

        return $total;
    }

    /**
     * `sales_cheque_details.receipt_no` is a varchar that matches neither
     * `sales_receipts.id` nor `sales_receipts.receipt_no` reliably (1/18 and
     * 0/18 respectively) - it is legacy data. Both interpretations are checked.
     *
     * Driven from the cheque side: that table is tiny, whereas the receipt id
     * set can be hundreds of thousands of rows and must never be materialised
     * into query bindings.
     */
    private function purgeChequeDetails(bool $force): int
    {
        $cheques = DB::table('sales_cheque_details')
            ->whereNotNull('receipt_no')->where('receipt_no', '<>', '')
            ->pluck('receipt_no', 'id');

        if ($cheques->isEmpty()) {
            return 0;
        }

        $values = $cheques->values()->unique()->all();

        // (a) receipt_no holding a numeric sales_receipts.id present in the set
        $numeric = array_values(array_filter($values, 'is_numeric'));
        $byId = $numeric
            ? DB::table($this->tmpReceipts)->whereIn('id', $numeric)->pluck('id')
                ->map(fn ($i) => (string) $i)->all()
            : [];

        // (b) receipt_no holding the receipt_no string of a receipt in the set
        $byNumber = DB::table('sales_receipts')
            ->whereIn('receipt_no', $values)
            ->whereIn('id', fn ($q) => $q->select('id')->from($this->tmpReceipts))
            ->pluck('receipt_no')->map(fn ($n) => (string) $n)->all();

        $hits = array_values(array_unique(array_merge($byId, $byNumber)));
        if (!$hits) {
            return 0;
        }

        $matchedIds = $cheques->filter(fn ($no) => in_array((string) $no, $hits, true))->keys()->all();
        if (!$matchedIds) {
            return 0;
        }

        return $force
            ? DB::table('sales_cheque_details')->whereIn('id', $matchedIds)->delete()
            : count($matchedIds);
    }

    private function emptyReport($company, string $note): array
    {
        return [
            'company'    => ['id' => $company->company_id, 'name' => $company->name],
            'branches'   => [],
            'terminals'  => [],
            'filters'    => [],
            'dry_run'    => true,
            'receipts'   => 0,
            'tables'     => [],
            'warnings'   => [$note],
            'skipped'    => [],
            'total_rows' => 0,
        ];
    }
}
