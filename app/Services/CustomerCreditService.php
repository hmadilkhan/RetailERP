<?php

namespace App\Services;

use App\Models\CompanyCreditLedger;
use App\Models\Invoice;
use App\Models\InvoiceCreditApplication;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CustomerCreditService
{
    public function availableBalance(int $companyId): float
    {
        $totals = CompanyCreditLedger::query()
            ->where('company_id', $companyId)
            ->selectRaw("COALESCE(SUM(CASE WHEN entry_type = 'credit' THEN amount ELSE 0 END), 0) as credits")
            ->selectRaw("COALESCE(SUM(CASE WHEN entry_type = 'debit' THEN amount ELSE 0 END), 0) as debits")
            ->first();

        return round(max((float) ($totals->credits ?? 0) - (float) ($totals->debits ?? 0), 0), 2);
    }

    public function recordCredit(
        int $companyId,
        float $amount,
        string $entryDate,
        string $description,
        ?int $invoiceId = null,
        ?string $sourceType = null,
        ?int $sourceId = null,
        ?int $createdBy = null
    ): CompanyCreditLedger {
        $amount = round($amount, 2);

        if ($amount <= 0) {
            throw new RuntimeException('Credit amount must be greater than zero.');
        }

        return CompanyCreditLedger::create([
            'company_id' => $companyId,
            'invoice_id' => $invoiceId,
            'entry_type' => 'credit',
            'entry_date' => Carbon::parse($entryDate)->toDateString(),
            'amount' => $amount,
            'description' => $description,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'created_by' => $createdBy,
        ]);
    }

    public function recordDebit(
        int $companyId,
        float $amount,
        string $entryDate,
        string $description,
        ?int $invoiceId = null,
        ?string $sourceType = null,
        ?int $sourceId = null,
        ?int $createdBy = null
    ): CompanyCreditLedger {
        $amount = round($amount, 2);

        if ($amount <= 0) {
            throw new RuntimeException('Debit amount must be greater than zero.');
        }

        if ($amount > $this->availableBalance($companyId)) {
            throw new RuntimeException('Debit amount exceeds available customer credit.');
        }

        return CompanyCreditLedger::create([
            'company_id' => $companyId,
            'invoice_id' => $invoiceId,
            'entry_type' => 'debit',
            'entry_date' => Carbon::parse($entryDate)->toDateString(),
            'amount' => $amount,
            'description' => $description,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'created_by' => $createdBy,
        ]);
    }

    public function applyCreditToInvoice(
        Invoice $invoice,
        float $amount,
        string $applicationDate,
        string $reason,
        ?int $createdBy = null
    ): InvoiceCreditApplication {
        $amount = round($amount, 2);

        if ($amount <= 0) {
            throw new RuntimeException('Credit application amount must be greater than zero.');
        }

        return DB::transaction(function () use ($invoice, $amount, $applicationDate, $reason, $createdBy) {
            $targetInvoice = Invoice::query()->lockForUpdate()->findOrFail($invoice->id);
            $availableCredit = $this->availableBalance($targetInvoice->company_id);
            $balance = $this->calculateBalance($targetInvoice);

            if ($availableCredit <= 0) {
                throw new RuntimeException('This company has no available customer credit.');
            }

            if ($balance <= 0) {
                throw new RuntimeException('This invoice has no outstanding balance.');
            }

            if ($amount > $availableCredit) {
                throw new RuntimeException('Credit amount exceeds available customer credit.');
            }

            if ($amount > $balance) {
                throw new RuntimeException('Credit amount exceeds invoice balance.');
            }

            $application = InvoiceCreditApplication::create([
                'invoice_id' => $targetInvoice->id,
                'company_id' => $targetInvoice->company_id,
                'application_date' => Carbon::parse($applicationDate)->toDateString(),
                'amount' => $amount,
                'reason' => $reason,
                'created_by' => $createdBy,
            ]);

            $this->recordDebit(
                $targetInvoice->company_id,
                $amount,
                $applicationDate,
                'Credit applied to invoice ' . $targetInvoice->invoice_no . ': ' . $reason,
                $targetInvoice->id,
                'invoice_credit_application',
                $application->id,
                $createdBy
            );

            $targetInvoice->credit_applied_amount = round((float) ($targetInvoice->credit_applied_amount ?? 0) + $amount, 2);
            $this->recalculateInvoiceState($targetInvoice);

            return $application;
        });
    }

    public function recalculateInvoiceState(Invoice $invoice): void
    {
        $invoice->balance_amount = $this->calculateBalance($invoice);
        $invoice->status = $invoice->balance_amount <= 0
            ? 'paid'
            : (((float) $invoice->paid_amount + (float) ($invoice->credit_applied_amount ?? 0)) > 0 ? 'partial' : 'issued');
        $invoice->save();
    }

    public function calculateBalance(Invoice $invoice): float
    {
        return round(max(
            0,
            (float) $invoice->total_amount
                - (float) $invoice->paid_amount
                - (float) ($invoice->credit_applied_amount ?? 0)
        ), 2);
    }
}

