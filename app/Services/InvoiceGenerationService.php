<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\InvoiceSetup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceGenerationService
{
    public function invoiceExists($companyId, $periodStart, $periodEnd)
    {
        return Invoice::where('company_id', $companyId)
            ->whereDate('period_start', $periodStart)
            ->whereDate('period_end', $periodEnd)
            ->exists();
    }

    public function generateInvoice(Company $company, $periodStart, $periodEnd, Carbon $invoiceDate, array $options = [])
    {
        $periodStart = Carbon::parse($periodStart)->toDateString();
        $periodEnd = Carbon::parse($periodEnd)->toDateString();
        $dueDate = !empty($options['due_date'])
            ? Carbon::parse($options['due_date'])->toDateString()
            : $invoiceDate->copy()->addDays((int) ($company->payment_due_days ?? 15))->toDateString();

        return DB::transaction(function () use ($company, $periodStart, $periodEnd, $invoiceDate, $dueDate, $options) {
            $lines = $this->buildInvoiceLines($company, $periodStart, $periodEnd);
            $subtotal = collect($lines)->sum('line_amount');

            $previousDue = (float) Invoice::where('company_id', $company->company_id)
                ->whereNotIn('status', ['paid', 'void'])
                ->sum('balance_amount');

            $taxAmount = (float) ($options['tax_amount'] ?? 0);
            $totalAmount = $subtotal + $taxAmount + $previousDue;

            $invoice = Invoice::create([
                'company_id' => $company->company_id,
                'invoice_no' => $this->generateInvoiceNo($company, $invoiceDate),
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'invoice_date' => $invoiceDate->toDateString(),
                'due_date' => $dueDate,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'previous_due' => $previousDue,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'balance_amount' => $totalAmount,
                'status' => 'issued',
                'generated_by' => $options['generated_by'] ?? null,
                'notes' => $options['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $line['invoice_id'] = $invoice->id;
                InvoiceLine::create($line);
            }

            return $invoice;
        });
    }

    private function buildInvoiceLines($company, $periodStart, $periodEnd)
    {
        $lines = [];
        $billingMonths = $this->getBillingPeriodMonths($periodStart, $periodEnd);
        $billingPeriodLabel = $this->formatBillingPeriodLabel($periodStart, $periodEnd);

        $setup = InvoiceSetup::with(['billingRates' => function ($query) use ($periodStart, $periodEnd) {
            $query->where('is_active', 1);
        }])->where('company_id', $company->company_id)->first();

        if ($setup && $setup->billingRates->count() > 0) {
            foreach ($setup->billingRates as $rate) {
                $chargeTypeLabel = ucwords(str_replace('_', ' ', $rate->charge_type));
                $rateMultiplier = $this->isMonthlyChargeType($rate->charge_type) ? $billingMonths : 1;

                if ($rate->scope_type === 'company') {
                    $lines[] = [
                        'scope_type' => 'company',
                        'scope_id' => $rate->scope_id,
                        'description' => 'Sabsoft (Sabify) POS Application - ' . $chargeTypeLabel . ' (' . $billingPeriodLabel . ') (Company)',
                        'qty' => $rateMultiplier,
                        'unit_price' => $rate->rate,
                        'line_amount' => $rateMultiplier * $rate->rate,
                    ];
                } elseif ($rate->scope_type === 'branch') {
                    if ($rate->scope_id) {
                        $branch = DB::table('branch')
                            ->where('branch_id', $rate->scope_id)
                            ->where('status_id', 1)
                            ->first();

                        if ($branch) {
                            $lines[] = [
                                'scope_type' => 'branch',
                                'scope_id' => $rate->scope_id,
                                'description' => 'Sabsoft (Sabify) POS Application - ' . $chargeTypeLabel . ' (' . $billingPeriodLabel . ') (Branch: ' . $branch->branch_name . ')',
                                'qty' => $rateMultiplier,
                                'unit_price' => $rate->rate,
                                'line_amount' => $rateMultiplier * $rate->rate,
                            ];
                        }
                    } else {
                        $branchCount = DB::table('branch')
                            ->where('company_id', $company->company_id)
                            ->where('status_id', 1)
                            ->count();

                        if ($branchCount > 0) {
                            $lines[] = [
                                'scope_type' => 'branch',
                                'scope_id' => null,
                                'description' => 'Sabsoft (Sabify) POS Application - ' . $chargeTypeLabel . ' (' . $billingPeriodLabel . ') (' . $branchCount . ' Branch' . ($branchCount > 1 ? 'es' : '') . ')',
                                'qty' => $branchCount * $rateMultiplier,
                                'unit_price' => $rate->rate,
                                'line_amount' => $branchCount * $rateMultiplier * $rate->rate,
                            ];
                        }
                    }
                } elseif ($rate->scope_type === 'terminal') {
                    if ($rate->scope_id) {
                        $terminal = DB::table('terminal_details')
                            ->join('branch', 'terminal_details.branch_id', '=', 'branch.branch_id')
                            ->where('terminal_details.terminal_id', $rate->scope_id)
                            ->where('branch.status_id', 1)
                            ->select('terminal_details.*')
                            ->first();

                        if ($terminal) {
                            $lines[] = [
                                'scope_type' => 'terminal',
                                'scope_id' => $rate->scope_id,
                                'description' => 'Sabsoft (Sabify) POS Application - ' . $chargeTypeLabel . ' (' . $billingPeriodLabel . ') (Terminal: ' . $terminal->terminal_name . ' | TID # ' . $terminal->terminal_id . ')',
                                'qty' => $rateMultiplier,
                                'unit_price' => $rate->rate,
                                'line_amount' => $rateMultiplier * $rate->rate,
                            ];
                        }
                    } else {
                        $terminalCount = DB::table('terminal_details')
                            ->join('branch', 'terminal_details.branch_id', '=', 'branch.branch_id')
                            ->where('branch.company_id', $company->company_id)
                            ->where('branch.status_id', 1)
                            ->count();

                        if ($terminalCount > 0) {
                            $lines[] = [
                                'scope_type' => 'terminal',
                                'scope_id' => null,
                                'description' => 'Sabsoft (Sabify) POS Application - ' . $chargeTypeLabel . ' (' . $billingPeriodLabel . ') (' . $terminalCount . ' Terminal' . ($terminalCount > 1 ? 's' : '') . ')',
                                'qty' => $terminalCount * $rateMultiplier,
                                'unit_price' => $rate->rate,
                                'line_amount' => $terminalCount * $rateMultiplier * $rate->rate,
                            ];
                        }
                    }
                }
            }

            return $lines;
        }

        if ($company->invoice_type === 'branch') {
            $branchCount = DB::table('branch')
                ->where('company_id', $company->company_id)
                ->where('status_id', 1)
                ->count();

            if ($branchCount > 0 && $company->monthly_charges_amount > 0) {
                $lines[] = [
                    'scope_type' => 'branch',
                    'scope_id' => null,
                    'description' => 'Sabsoft (Sabify) POS Application - Monthly Subscription (' . $billingPeriodLabel . ') (' . $branchCount . ' Branch' . ($branchCount > 1 ? 'es' : '') . ')',
                    'qty' => $branchCount * $billingMonths,
                    'unit_price' => $company->monthly_charges_amount,
                    'line_amount' => $branchCount * $billingMonths * $company->monthly_charges_amount,
                ];
            }
        } elseif ($company->invoice_type === 'terminal') {
            $terminalCount = DB::table('terminal_details')
                ->join('branch', 'terminal_details.branch_id', '=', 'branch.branch_id')
                ->where('branch.company_id', $company->company_id)
                ->where('branch.status_id', 1)
                ->count();

            if ($terminalCount > 0 && $company->monthly_charges_amount > 0) {
                $lines[] = [
                    'scope_type' => 'terminal',
                    'scope_id' => null,
                    'description' => 'Sabsoft (Sabify) POS Application - Monthly Subscription (' . $billingPeriodLabel . ') (' . $terminalCount . ' Terminal' . ($terminalCount > 1 ? 's' : '') . ')',
                    'qty' => $terminalCount * $billingMonths,
                    'unit_price' => $company->monthly_charges_amount,
                    'line_amount' => $terminalCount * $billingMonths * $company->monthly_charges_amount,
                ];
            }
        }

        return $lines;
    }

    private function getBillingPeriodMonths($periodStart, $periodEnd)
    {
        $start = Carbon::parse($periodStart)->startOfMonth();
        $end = Carbon::parse($periodEnd)->startOfMonth();

        return $start->diffInMonths($end) + 1;
    }

    private function formatBillingPeriodLabel($periodStart, $periodEnd)
    {
        $start = Carbon::parse($periodStart);
        $end = Carbon::parse($periodEnd);

        return $start->format('M-Y') . ' to ' . $end->format('M-Y');
    }

    private function isMonthlyChargeType($chargeType)
    {
        return $chargeType === 'flat_monthly';
    }

    private function generateInvoiceNo($company, $invoiceDate)
    {
        $prefix = trim((string) ($company->invoice_prefix ?? '')) ?: 'INV';
        $year = $invoiceDate->format('Y');
        $month = $invoiceDate->format('m');
        $seriesPrefix = $prefix . '-' . $year . $month;

        $lastInvoice = Invoice::where('invoice_no', 'like', $seriesPrefix . '%')
            ->orderByDesc('invoice_no')
            ->first();

        $sequence = 1;
        if ($lastInvoice) {
            $lastSequence = (int) substr($lastInvoice->invoice_no, -4);
            $sequence = $lastSequence + 1;
        }

        return $seriesPrefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
