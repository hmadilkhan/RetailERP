<?php

namespace App\Console\Commands;

use App\Models\Accounting\AccountingSetting;
use App\Services\Accounting\Posting\PostingService;
use Illuminate\Console\Command;
use Throwable;

// Phase 1.3: jin companies ka accounting + automatic posting on hai un ke source records GL me sync karo.
// Default window: pichle --days din (edit / delete bhi pakadne ke liye), posting_start_date se pehle nahi.
class PostToLedgerCommand extends Command
{
    protected $signature = 'accounting:post
        {--company= : Sirf ye company}
        {--from= : Y-m-d (default: aaj - days)}
        {--to= : Y-m-d (default: aaj)}
        {--days=45 : Kitne din peeche tak dobara check karna}
        {--source=* : Sirf ye source types (expense, grn ...)}';

    protected $description = 'Post source records (expenses, purchases, sales ...) to the General Ledger';

    public function handle(PostingService $posting): int
    {
        $to = $this->option('to') ?: now()->toDateString();
        $from = $this->option('from') ?: now()->subDays((int) $this->option('days'))->toDateString();

        $settings = AccountingSetting::where('enabled', true)->whereNotNull('posting_start_date')
            ->when($this->option('company'), fn ($q, $c) => $q->where('company_id', $c))
            ->get();

        foreach ($settings as $setting) {
            try {
                $stats = $posting->sync($setting->company_id, $from, $to, $this->option('source') ?: null);
                $this->info("Company {$setting->company_id}: " . collect($stats)->map(fn ($n, $k) => "{$k} {$n}")->implode(', '));
            } catch (Throwable $e) {
                $this->error("Company {$setting->company_id}: " . $e->getMessage());
                report($e);
            }
        }

        return self::SUCCESS;
    }
}
