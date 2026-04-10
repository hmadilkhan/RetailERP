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
        $deactivateThresholdMonths = 3 + $this->daysToBillingMonthFraction(5);
        $lockThresholdMonths = 4 + $this->daysToBillingMonthFraction(5);

        $this->info('Checking overdue billing thresholds...');
        $this->line('Reference date: ' . $today->toDateString());
        $this->line('Company deactivation threshold: ' . number_format($deactivateThresholdMonths, 1) . ' billing months due');
        $this->line('Device lock threshold: ' . number_format($lockThresholdMonths, 1) . ' billing months due');
        if ($companyId) {
            $this->line('Company filter applied: ' . $companyId);
        }
        if ($dryRun) {
            $this->warn('Dry run enabled. No records will be updated and no terminals will be locked.');
        }

        $companies = $this->getCompaniesWithOutstandingInvoices($companyId)
            ->map(function ($row) {
                $row->billing_time_due = $this->calculateBillingTimeDue($row->invoices);
                return $row;
            })
            ->filter(function ($row) use ($deactivateThresholdMonths, $lockThresholdMonths) {
                return $row->billing_time_due >= $deactivateThresholdMonths
                    || $row->billing_time_due >= $lockThresholdMonths;
            })
            ->sortByDesc('billing_time_due')
            ->values();

        if ($companies->isEmpty()) {
            $this->info('No companies crossed the billing overdue thresholds.');
            return self::SUCCESS;
        }

        $rows = [];

        foreach ($companies as $row) {
            $company = Company::query()->find($row->company_id, ['company_id', 'name', 'status_id']);
            if (!$company) {
                continue;
            }

            $oldestDueDate = !empty($row->oldest_due_date) ? Carbon::parse($row->oldest_due_date) : null;
            $shouldDeactivate = $row->billing_time_due >= $deactivateThresholdMonths;
            $shouldLock = $row->billing_time_due >= $lockThresholdMonths;

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
                'oldest_due_date' => $oldestDueDate ? $oldestDueDate->toDateString() : '-',
                'days_overdue' => $oldestDueDate ? $oldestDueDate->diffInDays($today) : 0,
                'billing_time_due' => number_format($row->billing_time_due, 1),
                'open_invoices' => (int) $row->open_invoices,
                'outstanding_balance' => number_format((float) $row->outstanding_balance, 2),
                'company_action' => $companyAction,
                'lock_action' => $lockAction,
                'detail' => implode(' | ', array_filter($detail)) ?: '-',
            ];
        }

        $this->newLine();
        $this->table(
            ['Company ID', 'Company', 'Billing Time Due', 'Oldest Due', 'Days Overdue', 'Open Invoices', 'Outstanding', 'Company Action', 'Lock Action', 'Detail'],
            array_map(function (array $row) {
                return [
                    $row['company_id'],
                    $row['company_name'],
                    $row['billing_time_due'] . ' months',
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
            ->where('status', '!=', 'void')
            ->where('balance_amount', '>', 0)
            ->when($companyId, function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->get()
            ->groupBy('company_id')
            ->map(function (Collection $invoices, $groupCompanyId) {
                return (object) [
                    'company_id' => (int) $groupCompanyId,
                    'oldest_due_date' => $invoices
                        ->filter(fn($invoice) => !empty($invoice->due_date))
                        ->min('due_date'),
                    'open_invoices' => $invoices->count(),
                    'outstanding_balance' => (float) $invoices->sum('balance_amount'),
                    'invoices' => $invoices,
                ];
            })
            ->values();
    }

    private function calculateBillingTimeDue(Collection $invoices): float
    {
        return round($invoices->sum(function ($invoice) {
            if (empty($invoice->period_start) || empty($invoice->period_end)) {
                return 0;
            }

            $periodStart = Carbon::parse($invoice->period_start)->startOfMonth();
            $periodEnd = Carbon::parse($invoice->period_end)->startOfMonth();
            $invoiceMonths = $periodStart->diffInMonths($periodEnd) + 1;
            $totalAmount = (float) $invoice->total_amount;
            $balanceAmount = max((float) $invoice->balance_amount, 0);

            if ($invoiceMonths <= 0 || $totalAmount <= 0 || $balanceAmount <= 0) {
                return 0;
            }

            return $invoiceMonths * min($balanceAmount / $totalAmount, 1);
        }), 1);
    }

    private function daysToBillingMonthFraction(int $days): float
    {
        return $days / 30;
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
                Terminal::query()->whereIn('branch_id', $branchIds)->update(['status_id' => 2, 'deleted_at' => now()]);
                Branch::query()->whereIn('branch_id', $branchIds)->update(['status_id' => 2, 'deleted_at' => now()]);
            }
        });

        return 'company_deactivated';
    }
}
