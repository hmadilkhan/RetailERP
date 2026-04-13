<?php

namespace App\Console\Commands;

use App\Models\InvoiceSetup;
use App\Services\InvoiceGenerationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GeneratePreviousDueInvoicesCommand extends Command
{
    protected $signature = 'billing:generate-previous-due {company_id?}';
    protected $description = 'Generate previous due invoices for companies marked for due reminders';

    public function handle(InvoiceGenerationService $invoiceGenerationService)
    {
        $runId = (string) Str::uuid();
        $invoiceDate = Carbon::today();
        $periodStart = $invoiceDate->copy()->startOfMonth()->toDateString();
        $periodEnd = $invoiceDate->copy()->endOfMonth()->toDateString();
        $companyId = $this->argument('company_id');
        $auditRows = [];

        $this->info("Generating previous due invoices for {$periodStart} to {$periodEnd}");
        $this->line("Run ID: {$runId}");

        $query = InvoiceSetup::with('company')
            ->where('is_auto_invoice', 0);

        if (!empty($companyId)) {
            $query->where('company_id', $companyId);
            $this->line("Company filter applied: {$companyId}");
        }

        $invoiceSetups = $query->get();

        if ($invoiceSetups->isEmpty()) {
            $this->info('No previous-due invoice setups found.');
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

            if (!$company) {
                $skippedCount++;
                $this->warn("Skipping setup #{$setup->id}: company is missing.");
                $auditRows[] = [
                    'company' => 'Setup #' . $setup->id,
                    'invoice' => '-',
                    'generation' => 'skipped',
                    'whatsapp' => 'not_attempted',
                    'detail' => 'Company missing',
                ];
                $this->logBillingRunActivity($runId, 'billing_previous_due_generation', 'generation_skipped', null, [
                    'setup_id' => $setup->id,
                    'company_id' => null,
                    'company_name' => null,
                    'status' => 'skipped',
                    'stage' => 'generation',
                    'reason' => 'Company missing',
                    'trigger' => 'billing:generate-previous-due',
                ], 'Previous due invoice generation skipped');
                continue;
            }

            if ($invoiceGenerationService->invoiceExists($company->company_id, $periodStart, $periodEnd, 'previous_due')) {
                $skippedCount++;
                $this->line("Skipping {$company->name}: previous due invoice already exists for {$periodStart} to {$periodEnd}.");
                $auditRows[] = [
                    'company' => $company->name,
                    'invoice' => '-',
                    'generation' => 'skipped',
                    'whatsapp' => 'not_attempted',
                    'detail' => 'Previous due invoice already exists for billing period',
                ];
                $this->logBillingRunActivity($runId, 'billing_previous_due_generation', 'generation_skipped', null, [
                    'setup_id' => $setup->id,
                    'company_id' => $company->company_id,
                    'company_name' => $company->name,
                    'status' => 'skipped',
                    'stage' => 'generation',
                    'reason' => 'Previous due invoice already exists for billing period',
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'trigger' => 'billing:generate-previous-due',
                ], 'Previous due invoice generation skipped');
                continue;
            }

            try {
                $invoice = $invoiceGenerationService->generatePreviousDueInvoice($company, $invoiceDate, [
                    'generated_by' => null,
                    'tax_amount' => 0,
                    'notes' => 'Auto-generated previous due invoice.',
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                ]);

                $generatedCount++;
                $this->info("Generated previous due invoice {$invoice->invoice_no} for {$company->name}.");

                try {
                    $whatsAppResult = $invoiceGenerationService->sendInvoicePdfToWhatsapp($invoice, [
                        'batch_uuid' => $runId,
                        'trigger' => 'billing:generate-previous-due',
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
                        'invoice_type' => 'previous_due',
                        'company_id' => $company->company_id,
                        'company_name' => $company->name,
                        'reason' => $exception->getMessage(),
                    ], 'Previous due invoice WhatsApp failed');
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

                $this->logBillingRunActivity($runId, 'billing_previous_due_generation', 'generation_failed', null, [
                    'company_id' => $company->company_id,
                    'company_name' => $company->name,
                    'status' => 'failed',
                    'stage' => 'generation',
                    'reason' => $exception->getMessage(),
                    'trigger' => 'billing:generate-previous-due',
                ], 'Previous due invoice generation failed');
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
            "Previous due generation complete. Generated: {$generatedCount}, Skipped: {$skippedCount}, Failed: {$failedCount}, " .
            "WhatsApp Sent: {$whatsAppSentCount}, WhatsApp Skipped: {$whatsAppSkippedCount}, WhatsApp Failed: {$whatsAppFailedCount}"
        );
        $this->line("Audit saved with Run ID: {$runId}");

        $this->logBillingRunActivity($runId, 'billing_previous_due_run', 'completed', null, [
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
        ], 'Previous due invoice run completed');

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
            Log::warning('Failed to record previous due command activity', [
                'run_id' => $runId,
                'log_name' => $logName,
                'event' => $event,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
