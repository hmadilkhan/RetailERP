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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class EnforceBillingOverdueCommand extends Command
{
    protected $signature = 'billing:enforce-overdue {company_id? : Optional company ID to inspect} {--dry-run : Show actions without applying them}';
    protected $description = 'Deactivate companies and lock terminals when unpaid invoices cross overdue thresholds';

    public function handle(TerminalLockService $terminalLockService)
    {
        $runId = (string) Str::uuid();
        $companyId = $this->argument('company_id');
        $dryRun = (bool) $this->option('dry-run');
        $today = Carbon::today();
        $deactivateThresholdMonths = 3 + $this->daysToBillingMonthFraction(5);
        $lockThresholdMonths = 4 + $this->daysToBillingMonthFraction(10);

        $this->info('Checking overdue billing thresholds...');
        $this->line('Run ID: ' . $runId);
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
            $this->logOverdueRunActivity($runId, [
                'status' => 'completed',
                'stage' => 'overdue_run',
                'dry_run' => $dryRun,
                'company_filter' => $companyId,
                'reference_date' => $today->toDateString(),
                'deactivate_threshold_months' => $deactivateThresholdMonths,
                'lock_threshold_months' => $lockThresholdMonths,
                'affected_company_count' => 0,
                'message' => 'No companies crossed the billing overdue thresholds.',
            ]);
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
            $exceptionMessage = null;
            $lockResult = null;
            $terminalLockSummary = [
                'locked_terminal_ids' => [],
                'already_locked_terminal_ids' => [],
                'skipped_terminal_ids' => [],
                'locked_terminals' => [],
                'already_locked_terminals' => [],
                'skipped_terminals' => [],
            ];

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
                        $terminalLockSummary = $this->buildTerminalLockSummary($lockResult);
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
                $exceptionMessage = $exception->getMessage();
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

            $this->logCompanyOverdueActivity($runId, $company, [
                'status' => $exceptionMessage ? 'failed' : 'processed',
                'stage' => 'overdue_enforcement',
                'dry_run' => $dryRun,
                'reference_date' => $today->toDateString(),
                'oldest_due_date' => $oldestDueDate ? $oldestDueDate->toDateString() : null,
                'days_overdue' => $oldestDueDate ? $oldestDueDate->diffInDays($today) : 0,
                'billing_time_due' => round((float) $row->billing_time_due, 1),
                'open_invoices' => (int) $row->open_invoices,
                'outstanding_balance' => round((float) $row->outstanding_balance, 2),
                'should_deactivate' => $shouldDeactivate,
                'should_lock' => $shouldLock,
                'company_action' => $companyAction,
                'lock_action' => $lockAction,
                'detail' => implode(' | ', array_filter($detail)) ?: null,
                'error' => $exceptionMessage,
                'lock_result_message' => $lockResult['message'] ?? null,
                'lock_status' => $lockResult['status'] ?? null,
                'locked_terminal_ids' => $terminalLockSummary['locked_terminal_ids'],
                'already_locked_terminal_ids' => $terminalLockSummary['already_locked_terminal_ids'],
                'skipped_terminal_ids' => $terminalLockSummary['skipped_terminal_ids'],
                'locked_terminals' => $terminalLockSummary['locked_terminals'],
                'already_locked_terminals' => $terminalLockSummary['already_locked_terminals'],
                'skipped_terminals' => $terminalLockSummary['skipped_terminals'],
            ]);
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
        $this->line("Audit saved with Run ID: {$runId}");

        $this->logOverdueRunActivity($runId, [
            'status' => 'completed',
            'stage' => 'overdue_run',
            'dry_run' => $dryRun,
            'company_filter' => $companyId,
            'reference_date' => $today->toDateString(),
            'deactivate_threshold_months' => $deactivateThresholdMonths,
            'lock_threshold_months' => $lockThresholdMonths,
            'affected_company_count' => count($rows),
            'deactivated_company_count' => collect($rows)->whereIn('company_action', ['company_deactivated', 'would_deactivate'])->count(),
            'locked_company_count' => collect($rows)->whereIn('lock_action', ['terminals_locked', 'would_lock_terminals'])->count(),
            'failed_company_count' => collect($rows)->filter(function ($row) {
                return $row['company_action'] === 'failed' || $row['lock_action'] === 'failed' || $row['lock_action'] === 'lock_failed';
            })->count(),
            'rows' => $rows,
        ]);

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

    private function buildTerminalLockSummary(?array $lockResult): array
    {
        $lockedIds = collect($lockResult['locked_terminal_ids'] ?? [])->map(fn ($id) => (int) $id)->values();
        $alreadyLockedIds = collect($lockResult['already_locked_terminal_ids'] ?? [])->map(fn ($id) => (int) $id)->values();
        $skippedIds = collect($lockResult['skipped_terminal_ids'] ?? [])->map(fn ($id) => (int) $id)->values();
        $allIds = $lockedIds
            ->merge($alreadyLockedIds)
            ->merge($skippedIds)
            ->unique()
            ->values();

        $terminalDetails = $allIds->isEmpty()
            ? collect()
            : Terminal::query()
                ->leftJoin('branch', 'branch.branch_id', '=', 'terminal_details.branch_id')
                ->whereIn('terminal_details.terminal_id', $allIds->all())
                ->select([
                    'terminal_details.terminal_id',
                    'terminal_details.terminal_name',
                    'terminal_details.serial_no',
                    'branch.branch_id',
                    'branch.branch_name',
                ])
                ->get()
                ->keyBy('terminal_id');

        return [
            'locked_terminal_ids' => $lockedIds->all(),
            'already_locked_terminal_ids' => $alreadyLockedIds->all(),
            'skipped_terminal_ids' => $skippedIds->all(),
            'locked_terminals' => $this->formatTerminalDetails($lockedIds, $terminalDetails),
            'already_locked_terminals' => $this->formatTerminalDetails($alreadyLockedIds, $terminalDetails),
            'skipped_terminals' => $this->formatTerminalDetails($skippedIds, $terminalDetails),
        ];
    }

    private function formatTerminalDetails(Collection $terminalIds, Collection $terminalDetails): array
    {
        return $terminalIds->map(function (int $terminalId) use ($terminalDetails) {
            $terminal = $terminalDetails->get($terminalId);

            return [
                'terminal_id' => $terminalId,
                'terminal_name' => $terminal->terminal_name ?? null,
                'serial_no' => $terminal->serial_no ?? null,
                'branch_id' => $terminal->branch_id ?? null,
                'branch_name' => $terminal->branch_name ?? null,
            ];
        })->values()->all();
    }

    private function logCompanyOverdueActivity(string $runId, Company $company, array $properties): void
    {
        try {
            activity('billing_overdue_enforcement')
                ->withCompany($company->company_id)
                ->withProperties(array_merge($properties, [
                    'company_id' => $company->company_id,
                    'company_name' => $company->name,
                    'run_id' => $runId,
                    'trigger' => 'billing:enforce-overdue',
                ]))
                ->tap(function ($activity) use ($runId) {
                    $activity->batch_uuid = $runId;
                })
                ->event($properties['status'] === 'failed' ? 'overdue_failed' : 'overdue_processed')
                ->log('Billing overdue enforcement processed for ' . $company->name);
        } catch (Throwable $exception) {
            Log::warning('Failed to record billing overdue company activity', [
                'run_id' => $runId,
                'company_id' => $company->company_id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function logOverdueRunActivity(string $runId, array $properties): void
    {
        try {
            activity('billing_overdue_run')
                ->withProperties(array_merge($properties, [
                    'run_id' => $runId,
                    'trigger' => 'billing:enforce-overdue',
                ]))
                ->tap(function ($activity) use ($runId) {
                    $activity->batch_uuid = $runId;
                })
                ->event('completed')
                ->log('Billing overdue enforcement run completed');
        } catch (Throwable $exception) {
            Log::warning('Failed to record billing overdue run activity', [
                'run_id' => $runId,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
