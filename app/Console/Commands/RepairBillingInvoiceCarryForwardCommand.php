<?php

namespace App\Console\Commands;

use App\Services\InvoiceSettlementService;
use Illuminate\Console\Command;

class RepairBillingInvoiceCarryForwardCommand extends Command
{
    protected $signature = 'billing:repair-carry-forward
        {--from=2026-04-01 : Repair invoices on or after this invoice date}
        {--company_id= : Limit repair to a single company}
        {--dry-run : Preview the repair without writing changes}';

    protected $description = 'Correct carried-forward billing invoices and reallocate payments oldest-first.';

    public function handle(InvoiceSettlementService $invoiceSettlementService)
    {
        $fromDate = $this->option('from');
        $companyId = $this->option('company_id') ? (int) $this->option('company_id') : null;
        $dryRun = (bool) $this->option('dry-run');

        $summary = $invoiceSettlementService->repairCarriedForwardInvoices($fromDate, $companyId, $dryRun);

        if (empty($summary['companies'])) {
            $this->warn('No companies matched the repair criteria.');
            return self::SUCCESS;
        }

        $this->table(
            ['Company ID', 'Corrected Invoices', 'Payments Reallocated', 'Allocation Rows'],
            array_map(function (array $company) {
                return [
                    $company['company_id'],
                    count($company['corrected_invoice_ids']),
                    $company['payment_count'],
                    $company['allocation_count'],
                ];
            }, $summary['companies'])
        );

        $modeLabel = $dryRun ? 'Dry run complete' : 'Repair complete';
        $this->info(
            $modeLabel .
            '. Companies: ' . $summary['company_count'] .
            ', Corrected Invoices: ' . $summary['invoice_count'] .
            ', Payments Reallocated: ' . $summary['payment_count']
        );

        return self::SUCCESS;
    }
}
