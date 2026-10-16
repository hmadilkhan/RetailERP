<?php

namespace App\Console\Commands;

use App\Services\Accounting\FixedAssetService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

// Phase 3.3: accounting-enabled companies ki depreciation pichle mahine tak (Kernel: har mahine 1 tareekh). Dobara chalana safe.
class RunDepreciationCommand extends Command
{
    protected $signature = 'accounting:depreciation {--company= : Sirf ye company} {--upto= : YYYY-MM (default: pichla mahina)}';

    protected $description = 'Post monthly depreciation of fixed assets to the General Ledger';

    public function handle(FixedAssetService $service): int
    {
        $upTo = $this->option('upto') ?: date('Y-m', strtotime('first day of last month'));
        $companies = $this->option('company')
            ? [(int) $this->option('company')]
            : DB::table('accounting_settings')->where('enabled', 1)->pluck('company_id')->all();

        foreach ($companies as $companyId) {
            if (!DB::table('fixed_assets')->where('company_id', $companyId)->where('status', 'active')->exists()) {
                continue;
            }
            try {
                foreach ($service->run((int) $companyId, $upTo, null) as $period => $r) {
                    $this->line("company {$companyId} {$period}: {$r['entry_no']} {$r['amount']} ({$r['assets']} assets)");
                }
            } catch (Throwable $e) {
                $this->error("company {$companyId}: " . $e->getMessage()); // e.g. band period — agle mahine phir koshish
            }
        }
        return self::SUCCESS;
    }
}
