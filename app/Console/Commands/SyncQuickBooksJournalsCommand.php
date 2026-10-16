<?php

namespace App\Console\Commands;

use App\Services\QuickBooks\QboJournalSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/** Phase 3.6: posted journal entries → QuickBooks (jin companies me journal sync on hai). */
class SyncQuickBooksJournalsCommand extends Command
{
    protected $signature = 'quickbooks:sync-journals {--company= : sirf ye company}';
    protected $description = 'Send posted journal entries to QuickBooks Online';

    public function handle(QboJournalSyncService $svc): int
    {
        if (!$svc->ready()) {
            $this->info('QuickBooks journal sync tables not migrated.');
            return self::SUCCESS;
        }
        $companies = DB::table('quickbook_settings')->where('sync_journals', 1)->whereNotNull('sync_from_date')
            ->when($this->option('company'), fn ($q, $c) => $q->where('company_id', (int) $c))->pluck('company_id');
        foreach ($companies as $companyId) {
            try {
                $r = $svc->sync((int) $companyId);
                $this->line("company {$companyId}: synced {$r['synced']}, failed {$r['failed']}");
                foreach (array_slice($r['errors'], 0, 5) as $e) {
                    $this->warn('  ' . $e);
                }
            } catch (Throwable $e) {
                $this->error("company {$companyId}: " . $e->getMessage());
            }
        }
        return self::SUCCESS;
    }
}
