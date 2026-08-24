<?php

namespace App\Console\Commands;

use App\Services\SalesPurgeService;
use Illuminate\Console\Command;

class DeleteCompanySalesCommand extends Command
{
    protected $signature = 'sales:delete-company
        {company : company_id whose sales should be removed}
        {--branch=* : limit to these branch_id(s)}
        {--terminal=* : limit to these terminal_id(s)}
        {--from= : start date Y-m-d (requires --to)}
        {--to= : end date Y-m-d (requires --from)}
        {--no-openings : keep sales_opening/closing/cash-in/cash-out}
        {--no-ledger : keep customer + service-provider ledger rows}
        {--chunk=5000 : rows deleted per batch}
        {--force : actually delete (default is a dry run)}';

    protected $description = "Delete a company's sales history (dry run unless --force)";

    public function handle(SalesPurgeService $service): int
    {
        $companyId = (int) $this->argument('company');
        $force     = (bool) $this->option('force');

        $options = [
            'branch_id'        => $this->option('branch'),
            'terminal_id'      => $this->option('terminal'),
            'from'             => $this->option('from'),
            'to'               => $this->option('to'),
            'chunk'            => (int) $this->option('chunk'),
            'include_openings' => !$this->option('no-openings'),
            'include_ledger'   => !$this->option('no-ledger'),
            'force'            => false,
        ];

        // Always dry-run first so the operator sees the blast radius.
        try {
            $preview = $service->purge($companyId, $options);
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->render($preview, $force ? 'PREVIEW' : 'DRY RUN');

        if (!$force) {
            $this->newLine();
            $this->info('Dry run only - nothing was deleted. Re-run with --force to apply.');
            return self::SUCCESS;
        }

        if ($preview['total_rows'] === 0) {
            $this->info('Nothing to delete.');
            return self::SUCCESS;
        }

        $this->newLine();
        $this->warn('This permanently deletes the rows above. There are no foreign keys and no undo.');
        $expected = $preview['company']['name'];
        $typed    = $this->ask("Type the company name exactly to confirm [{$expected}]");

        if ($typed !== $expected) {
            $this->error('Name did not match - aborted. Nothing was deleted.');
            return self::FAILURE;
        }

        $options['force'] = true;
        $result = $service->purge($companyId, $options);

        $this->newLine();
        $this->render($result, 'DELETED');
        $this->info("Done. {$result['total_rows']} rows removed.");

        return self::SUCCESS;
    }

    private function render(array $r, string $label): void
    {
        $this->newLine();
        $this->line("<fg=yellow>[{$label}]</> company {$r['company']['id']} - {$r['company']['name']}");
        $this->line('  branches : ' . (implode(',', $r['branches']) ?: '-'));
        $this->line('  terminals: ' . (implode(',', $r['terminals']) ?: '-'));
        if ($r['filters']) {
            $this->line('  filters  : ' . json_encode($r['filters']));
        }
        $this->line("  receipts : {$r['receipts']}");

        $rows = [];
        foreach ($r['tables'] as $table => $count) {
            if ($count > 0) {
                $rows[] = [$table, number_format($count)];
            }
        }

        if ($rows) {
            $this->newLine();
            $this->table(['table', 'rows'], $rows);
        }

        foreach ($r['skipped'] as $what => $n) {
            if ($n > 0) {
                $this->line("  <fg=cyan>skipped</> {$what}: {$n}");
            }
        }

        foreach ($r['warnings'] as $w) {
            $this->warn('  ! ' . $w);
        }

        $this->line('  <options=bold>total rows: ' . number_format(array_sum($r['tables'])) . '</>');
    }
}
