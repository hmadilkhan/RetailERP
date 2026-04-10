<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Terminal;
use App\Models\UserAuthorization;
use App\Services\TerminalLockService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class EnforceBillingOverdueCommand extends Command
{
    protected $signature = 'billing:enforce-overdue {company_id? : Optional company ID to inspect} {--dry-run : Show actions without applying them}';
    protected $description = 'Deactivate companies and lock terminals when unpaid invoices cross overdue thresholds';

    public function handle(TerminalLockService $terminalLockService)
    {
        $companyId = $this->argument('company_id');
        $dryRun = (bool) $this->option('dry-run');
        $today = Carbon::today();
        $deactivateCutoff = $today->copy()->subMonthsNoOverflow(3)->subDays(5)->toDateString();
        $lockCutoff = $today->copy()->subMonthsNoOverflow(4)->subDays(5)->toDateString();

        $this->info('Checking overdue billing thresholds...');
        $this->line('Reference date: ' . $today->toDateString());
        $this->line('Company deactivation threshold due on or before: ' . $deactivateCutoff);
        $this->line('Device lock threshold due on or before: ' . $lockCutoff);
        if ($companyId) {
            $this->line('Company filter applied: ' . $companyId);
        }
        if ($dryRun) {
            $this->warn('Dry run enabled. No records will be updated and no terminals will be locked.');
        }

        $companies = $this->getCompaniesWithOutstandingInvoices($companyId);

        if ($companies->isEmpty()) {
            $this->info('No companies with outstanding overdue invoices were found.');
            return self::SUCCESS;
        }

        $rows = [];

        foreach ($companies as $row) {
            $company = Company::query()->find($row->company_id, ['company_id', 'name', 'status_id']);
            if (!$company) {
                continue;
            }

            $oldestDueDate = Carbon::parse($row->oldest_due_date);
            $shouldDeactivate = $oldestDueDate->lessThanOrEqualTo(Carbon::parse($deactivateCutoff));
            $shouldLock = $oldestDueDate->lessThanOrEqualTo(Carbon::parse($lockCutoff));

            $companyAction = 'none';
            $lockAction = 'none';
            $detail = [];

            try {
                if ($shouldDeactivate) {
                    if ((int) $company->status_id === 1) {
                        $companyAction = $dryRun
                            ? 'would_deactivate'
                            : $this->deactivateCompany($company->company_id);
                        $detail[] = 'company threshold reached';
                    } else {
                        $companyAction = 'already_inactive';
                    }
                }

                if ($shouldLock) {
                    if ($dryRun) {
                        $lockAction = 'would_lock_terminals';
                    } else {
                        $lockResult = $terminalLockService->lockCompanyTerminals($company->company_id);
                        $lockAction = $lockResult['success'] ? 'terminals_locked' : 'lock_failed';
                        $detail[] = $lockResult['message'];
                        if (!empty($lockResult['locked_terminal_ids'])) {
                            $detail[] = 'locked: ' . implode(',', $lockResult['locked_terminal_ids']);
                        }
                        if (!empty($lockResult['already_locked_terminal_ids'])) {
                            $detail[] = 'already locked: ' . implode(',', $lockResult['already_locked_terminal_ids']);
                        }
                        if (!empty($lockResult['skipped_terminal_ids'])) {
                            $detail[] = 'skipped(no serial): ' . implode(',', $lockResult['skipped_terminal_ids']);
                        }
                    }
                }
            } catch (Throwable $exception) {
                $detail[] = $exception->getMessage();
                if ($companyAction === 'none') {
                    $companyAction = 'failed';
                }
                if ($shouldLock && $lockAction === 'none') {
                    $lockAction = 'failed';
                }
            }

            $rows[] = [
                'company_id' => $company->company_id,
                'company_name' => $company->name,
                'oldest_due_date' => $oldestDueDate->toDateString(),
                'days_overdue' => $oldestDueDate->diffInDays($today),
                'open_invoices' => (int) $row->open_invoices,
                'outstanding_balance' => number_format((float) $row->outstanding_balance, 2),
                'company_action' => $companyAction,
                'lock_action' => $lockAction,
                'detail' => implode(' | ', array_filter($detail)) ?: '-',
            ];
        }

        $this->newLine();
        $this->table(
            ['Company ID', 'Company', 'Oldest Due', 'Days Overdue', 'Open Invoices', 'Outstanding', 'Company Action', 'Lock Action', 'Detail'],
            array_map(function (array $row) {
                return [
                    $row['company_id'],
                    $row['company_name'],
                    $row['oldest_due_date'],
                    $row['days_overdue'],
                    $row['open_invoices'],
                    $row['outstanding_balance'],
                    $row['company_action'],
                    $row['lock_action'],
                    $row['detail'],
                ];
            }, $rows)
        );

        $this->info('Billing overdue enforcement completed.');

        return self::SUCCESS;
    }

    private function getCompaniesWithOutstandingInvoices($companyId = null): Collection
    {
        return Invoice::query()
            ->select('company_id')
            ->selectRaw('MIN(due_date) as oldest_due_date')
            ->selectRaw('COUNT(*) as open_invoices')
            ->selectRaw('SUM(balance_amount) as outstanding_balance')
            ->where('status', '!=', 'void')
            ->where('balance_amount', '>', 0)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', Carbon::today()->toDateString())
            ->when($companyId, function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->groupBy('company_id')
            ->orderBy('oldest_due_date')
            ->get();
    }

    private function deactivateCompany(int $companyId): string
    {
        DB::transaction(function () use ($companyId) {
            Company::query()->where('company_id', $companyId)->update(['status_id' => 2]);
            UserAuthorization::query()->where('company_id', $companyId)->update(['status_id' => 2]);

            $branchIds = Branch::query()
                ->where('company_id', $companyId)
                ->pluck('branch_id');

            if ($branchIds->isNotEmpty()) {
                Terminal::query()->whereIn('branch_id', $branchIds)->update(['status_id' => 2]);
                Branch::query()->whereIn('branch_id', $branchIds)->update(['status_id' => 2]);
            }
        });

        return 'company_deactivated';
    }
}
