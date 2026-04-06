<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InvoiceSettlementService
{
    public function calculateOutstandingPreviousDue(int $companyId): float
    {
        return (float) Invoice::where('company_id', $companyId)
            ->where('status', '!=', 'void')
            ->lockForUpdate()
            ->sum('balance_amount');
    }

    public function applyPaymentToCompany(Invoice $invoice, array $paymentData): array
    {
        $requestedAmount = round((float) $paymentData['amount'], 2);

        if ($requestedAmount <= 0) {
            throw new RuntimeException('Payment amount must be greater than zero.');
        }

        $openInvoices = $this->getOutstandingInvoicesForCompany($invoice->company_id);
        $companyOutstanding = round((float) $openInvoices->sum('balance_amount'), 2);

        if ($companyOutstanding <= 0) {
            throw new RuntimeException('This company has no outstanding invoices to settle.');
        }

        if ($requestedAmount > $companyOutstanding) {
            throw new RuntimeException('Payment amount exceeds the company outstanding balance.');
        }

        $allocations = $this->allocatePaymentAcrossInvoices(
            $openInvoices,
            $requestedAmount,
            function (Invoice $targetInvoice, float $amount) use ($invoice, $paymentData) {
                InvoicePayment::create([
                    'invoice_id' => $targetInvoice->id,
                    'company_id' => $targetInvoice->company_id,
                    'payment_voucher_id' => $paymentData['payment_voucher_id'] ?? null,
                    'payment_date' => $paymentData['payment_date'],
                    'payment_mode_id' => $paymentData['payment_mode_id'] ?? null,
                    'amount' => $amount,
                    'reference_no' => $paymentData['reference_no'] ?? null,
                    'narration' => $paymentData['narration'] ?? null,
                    'received_by' => $paymentData['received_by'] ?? null,
                ]);
            },
            true
        );

        return [
            'requested_amount' => $requestedAmount,
            'allocated_amount' => round(array_sum(array_column($allocations, 'amount')), 2),
            'allocations' => $allocations,
        ];
    }

    public function repairCarriedForwardInvoices(?string $fromDate = null, ?int $companyId = null, bool $dryRun = false): array
    {
        $fromDate = $fromDate ? Carbon::parse($fromDate)->toDateString() : null;

        $companyIds = Invoice::query()
            ->where('status', '!=', 'void')
            ->when($companyId, function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->when($fromDate, function ($query) use ($fromDate) {
                $query->whereDate('invoice_date', '>=', $fromDate)
                    ->where('previous_due', '>', 0);
            }, function ($query) {
                $query->where('previous_due', '>', 0);
            })
            ->distinct()
            ->pluck('company_id');

        $summary = [
            'companies' => [],
            'company_count' => 0,
            'invoice_count' => 0,
            'payment_count' => 0,
            'dry_run' => $dryRun,
        ];

        foreach ($companyIds as $targetCompanyId) {
            $result = DB::transaction(function () use ($targetCompanyId, $fromDate, $dryRun) {
                $invoices = Invoice::where('company_id', $targetCompanyId)
                    ->where('status', '!=', 'void')
                    ->orderBy('invoice_date')
                    ->orderBy('due_date')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                if ($invoices->isEmpty()) {
                    return null;
                }

                $payments = InvoicePayment::where('company_id', $targetCompanyId)
                    ->whereIn('invoice_id', $invoices->pluck('id'))
                    ->orderBy('payment_date')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->map(function (InvoicePayment $payment) {
                        return [
                            'payment_voucher_id' => $payment->payment_voucher_id,
                            'payment_date' => $payment->payment_date,
                            'payment_mode_id' => $payment->payment_mode_id,
                            'amount' => round((float) $payment->amount, 2),
                            'reference_no' => $payment->reference_no,
                            'narration' => $payment->narration,
                            'received_by' => $payment->received_by,
                            'source_invoice_id' => $payment->invoice_id,
                        ];
                    })
                    ->all();

                $correctedInvoiceIds = [];

                foreach ($invoices as $invoice) {
                    if ($invoice->previous_due > 0 && (!$fromDate || $invoice->invoice_date >= $fromDate)) {
                        $invoice->total_amount = max(0, round((float) $invoice->total_amount - (float) $invoice->previous_due, 2));
                        $correctedInvoiceIds[] = $invoice->id;
                    }

                    $invoice->paid_amount = 0;
                    $invoice->balance_amount = round((float) $invoice->total_amount, 2);
                    $invoice->status = $invoice->balance_amount <= 0 ? 'paid' : 'issued';
                }

                if (!$dryRun) {
                    foreach ($invoices as $invoice) {
                        $invoice->save();
                    }

                    InvoicePayment::where('company_id', $targetCompanyId)
                        ->whereIn('invoice_id', $invoices->pluck('id'))
                        ->delete();
                }

                $allocations = [];

                foreach ($payments as $payment) {
                    if ($payment['amount'] <= 0) {
                        continue;
                    }

                    $paymentAllocations = $this->allocatePaymentAcrossInvoices(
                        $invoices,
                        $payment['amount'],
                        function (Invoice $targetInvoice, float $amount) use ($payment, $dryRun) {
                            if ($dryRun) {
                                return;
                            }

                            InvoicePayment::create([
                                'invoice_id' => $targetInvoice->id,
                                'company_id' => $targetInvoice->company_id,
                                'payment_voucher_id' => $payment['payment_voucher_id'] ?? null,
                                'payment_date' => $payment['payment_date'],
                                'payment_mode_id' => $payment['payment_mode_id'],
                                'amount' => $amount,
                                'reference_no' => $payment['reference_no'],
                                'narration' => $payment['narration'],
                                'received_by' => $payment['received_by'],
                            ]);
                        },
                        !$dryRun
                    );

                    foreach ($paymentAllocations as $allocation) {
                        $allocations[] = $allocation;
                    }
                }

                return [
                    'company_id' => $targetCompanyId,
                    'corrected_invoice_ids' => $correctedInvoiceIds,
                    'invoice_count' => $invoices->count(),
                    'payment_count' => count($payments),
                    'allocation_count' => count($allocations),
                ];
            });

            if (!$result) {
                continue;
            }

            $summary['companies'][] = $result;
            $summary['company_count']++;
            $summary['invoice_count'] += count($result['corrected_invoice_ids']);
            $summary['payment_count'] += $result['payment_count'];
        }

        return $summary;
    }

    private function getOutstandingInvoicesForCompany(int $companyId): Collection
    {
        return Invoice::where('company_id', $companyId)
            ->where('status', '!=', 'void')
            ->where('balance_amount', '>', 0)
            ->orderBy('invoice_date')
            ->orderBy('due_date')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    private function allocatePaymentAcrossInvoices(
        Collection $invoices,
        float $amount,
        callable $persistAllocation,
        bool $persistInvoiceState = true
    ): array {
        $remaining = round($amount, 2);
        $allocations = [];

        $orderedInvoices = $this->buildAllocationOrder($invoices);

        /** @var Invoice $targetInvoice */
        foreach ($orderedInvoices as $targetInvoice) {
            if ($remaining <= 0) {
                break;
            }

            $available = round((float) $targetInvoice->balance_amount, 2);
            if ($available <= 0) {
                continue;
            }

            $appliedAmount = min($remaining, $available);
            $appliedAmount = round($appliedAmount, 2);

            if ($appliedAmount <= 0) {
                continue;
            }

            $targetInvoice->paid_amount = round((float) $targetInvoice->paid_amount + $appliedAmount, 2);
            $targetInvoice->balance_amount = round(max(0, (float) $targetInvoice->total_amount - (float) $targetInvoice->paid_amount), 2);
            $targetInvoice->status = $targetInvoice->balance_amount <= 0
                ? 'paid'
                : ((float) $targetInvoice->paid_amount > 0 ? 'partial' : 'issued');

            if ($persistInvoiceState) {
                $targetInvoice->save();
            }

            $persistAllocation($targetInvoice, $appliedAmount);

            $allocations[] = [
                'invoice_id' => $targetInvoice->id,
                'invoice_no' => $targetInvoice->invoice_no,
                'amount' => $appliedAmount,
            ];

            $remaining = round($remaining - $appliedAmount, 2);
        }

        if ($remaining > 0) {
            throw new RuntimeException('Unable to allocate the full payment across outstanding invoices.');
        }

        return $allocations;
    }

    private function buildAllocationOrder(Collection $invoices): Collection
    {
        return $invoices->filter(function (Invoice $invoice) {
            return (float) $invoice->balance_amount > 0;
        })->values();
    }
}
