<?php

namespace App\Console\Commands;

use App\Services\Accounting\BackfillService;
use Illuminate\Console\Command;

// Phase 1.6: company ka purana data GL me (posting start date peeche + poori history ki posting). --dry-run = sirf plan.
class BackfillLedgerCommand extends Command
{
    protected $signature = 'accounting:backfill {company : Company id} {--from= : Cut-off date Y-m-d (default: pehle data wala fiscal year)} {--dry-run}';

    protected $description = 'Post a company\'s historical data to the General Ledger from a cut-off date';

    public function handle(BackfillService $backfill): int
    {
        $company = (int) $this->argument('company');
        $cutoff = $this->option('from') ?: $backfill->suggestedCutoff($company);
        if (!$cutoff) {
            $this->error('No data found for this company.');
            return self::FAILURE;
        }

        $plan = $backfill->plan($company, $cutoff);
        $this->info("Cut-off {$cutoff} (earliest data {$plan['earliest']}, current posting start {$plan['current_start']})");
        $this->line('Fiscal years to create: ' . (implode(', ', $plan['years_to_create']) ?: 'none'));
        $this->line('Opening entries to reverse: ' . (implode(', ', $plan['conflicting_openings']) ?: 'none'));
        foreach ($plan['sources'] as $type => $s) {
            $this->line(sprintf('  %-18s %6d documents  Dr %16s  unbalanced %d', $type, $s['documents'], number_format($s['debits'], 2), $s['unbalanced']));
        }
        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        $result = $backfill->run($company, $cutoff);
        $this->info('Fiscal years created: ' . (implode(', ', $result['years_created']) ?: 'none'));
        $this->info('Posting: ' . collect($result['stats'])->map(fn ($n, $k) => "{$k} {$n}")->implode(', '));
        return self::SUCCESS;
    }
}
