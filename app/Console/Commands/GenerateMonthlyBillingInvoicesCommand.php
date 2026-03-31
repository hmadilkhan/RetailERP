<?php

namespace App\Console\Commands;

use App\Models\InvoiceSetup;
use App\Services\InvoiceGenerationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class GenerateMonthlyBillingInvoicesCommand extends Command
{
    protected $signature = 'billing:generate-monthly {company_id?}';
    protected $description = 'Generate monthly billing invoices for the current month';

    public function handle(InvoiceGenerationService $invoiceGenerationService)
    {
        $invoiceDate = Carbon::today();
        $periodStart = $invoiceDate->copy()->startOfMonth()->toDateString();
        $periodEnd = $invoiceDate->copy()->endOfMonth()->toDateString();
        $companyId = $this->argument('company_id');

        $this->info("Generating billing invoices for {$periodStart} to {$periodEnd}");

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
                continue;
            }

            if ($invoiceGenerationService->invoiceExists($company->company_id, $periodStart, $periodEnd)) {
                $skippedCount++;
                $this->line("Skipping {$company->name}: invoice already exists for {$periodStart} to {$periodEnd}.");
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
                    $whatsAppResult = $invoiceGenerationService->sendInvoicePdfToWhatsapp($invoice);

                    if (($whatsAppResult['status'] ?? null) === 'sent') {
                        $whatsAppSentCount++;
                        $this->line("WhatsApp sent to {$whatsAppResult['to']} for invoice {$invoice->invoice_no}.");
                    } else {
                        $whatsAppSkippedCount++;
                        $this->warn("WhatsApp skipped for {$company->name}: " . ($whatsAppResult['reason'] ?? 'Unknown reason.'));
                    }
                } catch (Throwable $exception) {
                    $whatsAppFailedCount++;
                    $this->error("WhatsApp failed for {$company->name}: {$exception->getMessage()}");
                }
            } catch (Throwable $exception) {
                $failedCount++;
                $this->error("Failed for {$company->name}: {$exception->getMessage()}");
            }
        }

        $this->info(
            "Billing generation complete. Generated: {$generatedCount}, Skipped: {$skippedCount}, Failed: {$failedCount}, " .
            "WhatsApp Sent: {$whatsAppSentCount}, WhatsApp Skipped: {$whatsAppSkippedCount}, WhatsApp Failed: {$whatsAppFailedCount}"
        );

        return self::SUCCESS;
    }
}
