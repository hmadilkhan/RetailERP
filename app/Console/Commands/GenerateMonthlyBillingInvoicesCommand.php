<?php

namespace App\Console\Commands;

use App\Models\InvoiceSetup;
use App\Services\InvoiceGenerationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GenerateMonthlyBillingInvoicesCommand extends Command
{
    protected $signature = 'billing:generate-monthly {company_id?}';
    protected $description = 'Generate monthly billing invoices for the current month';

    public function handle(InvoiceGenerationService $invoiceGenerationService)
    {
        $runId = (string) Str::uuid();
        $invoiceDate = Carbon::today();
        $periodStart = $invoiceDate->copy()->startOfMonth()->toDateString();
        $periodEnd = $invoiceDate->copy()->endOfMonth()->toDateString();
        $companyId = $this->argument('company_id');
        $auditRows = [];

        $this->info("Generating billing invoices for {$periodStart} to {$periodEnd}");
        $this->line("Run ID: {$runId}");

        $query = InvoiceSetup::with('company')
            ->where('is_auto_invoice', 1);

        if (!empty($companyId)) {
            $query->where('company_id', $companyId);
            $this->line("Company filter applied: {$companyId}");
        }

        $invoiceSetups = $query->get();

        if ($invoiceSetups->isEmpty()) {
            $this->info('No auto-invoice setups found.');
            return self::SUCCESS;
        }

        $generatedCount = 0;
        $skippedCount = 0;
        $failedCount = 0;
        $whatsAppSentCount = 0;
        $whatsAppSkippedCount = 0;
        $whatsAppFailedCount = 0;

        foreach ($invoiceSetups as $setup) {
            $company = $setup->company;

            if (!$company || (int) $company->status_id !== 1) {
                $skippedCount++;
                $this->warn("Skipping setup #{$setup->id}: company is missing or inactive.");
                $auditRows[] = [
                    'company' => $company->name ?? ('Setup #' . $setup->id),
                    'invoice' => '-',
                    'generation' => 'skipped',
                    'whatsapp' => 'not_attempted',
                    'detail' => 'Company missing or inactive',
                ];
                $this->logBillingRunActivity($runId, 'billing_invoice_generation', 'generation_skipped', null, [
                    'setup_id' => $setup->id,
                    'company_id' => $company->company_id ?? null,
                    'company_name' => $company->name ?? null,
                    'status' => 'skipped',
                    'stage' => 'generation',
                    'reason' => 'Company missing or inactive',
                    'trigger' => 'billing:generate-monthly',
                ], 'Billing invoice generation skipped');
                continue;
            }

            if ($invoiceGenerationService->invoiceExists($company->company_id, $periodStart, $periodEnd)) {
                $skippedCount++;
                $this->line("Skipping {$company->name}: invoice already exists for {$periodStart} to {$periodEnd}.");
                $auditRows[] = [
                    'company' => $company->name,
                    'invoice' => '-',
                    'generation' => 'skipped',
                    'whatsapp' => 'not_attempted',
                    'detail' => 'Invoice already exists for billing period',
                ];
                $this->logBillingRunActivity($runId, 'billing_invoice_generation', 'generation_skipped', null, [
                    'setup_id' => $setup->id,
                    'company_id' => $company->company_id,
                    'company_name' => $company->name,
                    'status' => 'skipped',
                    'stage' => 'generation',
                    'reason' => 'Invoice already exists for billing period',
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'trigger' => 'billing:generate-monthly',
                ], 'Billing invoice generation skipped');
                continue;
            }

            try {
                $invoice = $invoiceGenerationService->generateInvoice($company, $periodStart, $periodEnd, $invoiceDate, [
                    'generated_by' => null,
                    'tax_amount' => 0,
                    'notes' => 'Auto-generated monthly invoice.',
                ]);

                $generatedCount++;
                $this->info("Generated invoice {$invoice->invoice_no} for {$company->name}.");

                try {
                    $whatsAppResult = $invoiceGenerationService->sendInvoicePdfToWhatsapp($invoice, [
                        'batch_uuid' => $runId,
                        'trigger' => 'billing:generate-monthly',
                    ]);

                    if (($whatsAppResult['status'] ?? null) === 'sent') {
                        $whatsAppSentCount++;
                        $this->line("WhatsApp sent to {$whatsAppResult['to']} for invoice {$invoice->invoice_no}.");
                        $auditRows[] = [
                            'company' => $company->name,
                            'invoice' => $invoice->invoice_no,
                            'generation' => 'generated',
                            'whatsapp' => 'sent',
                            'detail' => 'Sent to ' . ($whatsAppResult['to'] ?? 'N/A'),
                        ];
                    } else {
                        $whatsAppSkippedCount++;
                        $this->warn("WhatsApp skipped for {$company->name}: " . ($whatsAppResult['reason'] ?? 'Unknown reason.'));
                        $auditRows[] = [
                            'company' => $company->name,
                            'invoice' => $invoice->invoice_no,
                            'generation' => 'generated',
                            'whatsapp' => 'skipped',
                            'detail' => $whatsAppResult['reason'] ?? 'Unknown reason.',
                        ];
                    }
                } catch (Throwable $exception) {
                    $whatsAppFailedCount++;
                    $this->error("WhatsApp failed for {$company->name}: {$exception->getMessage()}");
                    $auditRows[] = [
                        'company' => $company->name,
                        'invoice' => $invoice->invoice_no,
                        'generation' => 'generated',
                        'whatsapp' => 'failed',
                        'detail' => $exception->getMessage(),
                    ];

                    $this->logBillingRunActivity($runId, 'billing_invoice_whatsapp', 'whatsapp_failed', $invoice, [
                        'invoice_id' => $invoice->id,
                        'invoice_no' => $invoice->invoice_no,
                        'company_id' => $company->company_id,
                        'company_name' => $company->name,
                        'reason' => $exception->getMessage(),
                    ], 'Billing invoice WhatsApp failed');
                }
            } catch (Throwable $exception) {
                $failedCount++;
                $this->error("Failed for {$company->name}: {$exception->getMessage()}");
                $auditRows[] = [
                    'company' => $company->name,
                    'invoice' => '-',
                    'generation' => 'failed',
                    'whatsapp' => 'not_attempted',
                    'detail' => $exception->getMessage(),
                ];

                $this->logBillingRunActivity($runId, 'billing_invoice_generation', 'generation_failed', null, [
                    'company_id' => $company->company_id,
                    'company_name' => $company->name,
                    'status' => 'failed',
                    'stage' => 'generation',
                    'reason' => $exception->getMessage(),
                    'trigger' => 'billing:generate-monthly',
                ], 'Billing invoice generation failed');
            }
        }

        if (!empty($auditRows)) {
            $this->newLine();
            $this->table(
                ['Company', 'Invoice', 'Generation', 'WhatsApp', 'Detail'],
                array_map(function (array $row) {
                    return [
                        $row['company'],
                        $row['invoice'],
                        $row['generation'],
                        $row['whatsapp'],
                        $row['detail'],
                    ];
                }, $auditRows)
            );
        }

        $this->info(
            "Billing generation complete. Generated: {$generatedCount}, Skipped: {$skippedCount}, Failed: {$failedCount}, " .
            "WhatsApp Sent: {$whatsAppSentCount}, WhatsApp Skipped: {$whatsAppSkippedCount}, WhatsApp Failed: {$whatsAppFailedCount}"
        );
        $this->line("Audit saved with Run ID: {$runId}");

        $this->logBillingRunActivity($runId, 'billing_invoice_run', 'completed', null, [
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'company_filter' => $companyId,
            'generated_count' => $generatedCount,
            'skipped_count' => $skippedCount,
            'failed_count' => $failedCount,
            'whatsapp_sent_count' => $whatsAppSentCount,
            'whatsapp_skipped_count' => $whatsAppSkippedCount,
            'whatsapp_failed_count' => $whatsAppFailedCount,
            'audit_rows' => $auditRows,
        ], 'Monthly billing invoice run completed');

        return self::SUCCESS;
    }

    private function logBillingRunActivity(
        string $runId,
        string $logName,
        string $event,
        $subject = null,
        array $properties = [],
        ?string $description = null
    ): void {
        try {
            $logger = activity($logName)
                ->withProperties($properties)
                ->tap(function ($activity) use ($runId) {
                    $activity->batch_uuid = $runId;
                })
                ->event($event);

            if (!empty($properties['company_id'])) {
                $logger->withCompany($properties['company_id']);
            }

            if ($subject) {
                $logger->performedOn($subject);
            }

            $logger->log($description ?? $event);
        } catch (Throwable $exception) {
            Log::warning('Failed to record monthly billing command activity', [
                'run_id' => $runId,
                'log_name' => $logName,
                'event' => $event,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
