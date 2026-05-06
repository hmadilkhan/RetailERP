<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceDiscount;
use App\Models\InvoiceLine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceDiscountService
{
    public function __construct(private CustomerCreditService $customerCreditService)
    {
    }

    public function addDiscount(Invoice $invoice, array $data, ?int $createdBy = null): InvoiceDiscount
    {
        return DB::transaction(function () use ($invoice, $data, $createdBy) {
            $targetInvoice = Invoice::query()->lockForUpdate()->findOrFail($invoice->id);
            $baseAmount = round((float) $targetInvoice->total_amount, 2);
            $appliedAmount = round((float) $targetInvoice->paid_amount + (float) ($targetInvoice->credit_applied_amount ?? 0), 2);
            $overpaidBefore = max(0, round($appliedAmount - $baseAmount, 2));
            $discountType = (string) $data['discount_type'];
            $discountValue = round((float) $data['discount_value'], 2);

            if ($baseAmount <= 0) {
                throw ValidationException::withMessages([
                    'discount_value' => 'Invoice total must be greater than zero before adding a discount.',
                ]);
            }

            $discountAmount = $discountType === 'percentage'
                ? round($baseAmount * ($discountValue / 100), 2)
                : $discountValue;

            if ($discountType === 'percentage' && $discountValue > 100) {
                throw ValidationException::withMessages([
                    'discount_value' => 'Percentage discount cannot be more than 100%.',
                ]);
            }

            if ($discountAmount <= 0) {
                throw ValidationException::withMessages([
                    'discount_value' => 'Discount amount must be greater than zero.',
                ]);
            }

            if ($discountAmount > $baseAmount) {
                throw ValidationException::withMessages([
                    'discount_value' => 'Discount cannot be greater than current invoice total.',
                ]);
            }

            $discount = InvoiceDiscount::create([
                'company_id' => $targetInvoice->company_id,
                'invoice_id' => $targetInvoice->id,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_amount' => $discountAmount,
                'discount_date' => Carbon::parse($data['discount_date'])->toDateString(),
                'reason' => $data['reason'],
                'created_by' => $createdBy,
            ]);

            $targetInvoice->discount_amount = round((float) ($targetInvoice->discount_amount ?? 0) + $discountAmount, 2);
            $targetInvoice->total_amount = round($baseAmount - $discountAmount, 2);
            $this->customerCreditService->recalculateInvoiceState($targetInvoice);

            InvoiceLine::create([
                'invoice_id' => $targetInvoice->id,
                'scope_type' => 'discount',
                'scope_id' => null,
                'description' => 'Discount: ' . $discount->reason . ' (' . ($discountType === 'percentage' ? number_format($discountValue, 2) . '%' : 'Amount') . ')',
                'qty' => 1,
                'unit_price' => -$discountAmount,
                'line_amount' => -$discountAmount,
                'meta' => json_encode(['discount_id' => $discount->id]),
            ]);

            $overpaidAfter = max(0, round($appliedAmount - (float) $targetInvoice->total_amount, 2));
            $overpaidDelta = round($overpaidAfter - $overpaidBefore, 2);

            if ($overpaidDelta > 0) {
                $this->customerCreditService->recordCredit(
                    $targetInvoice->company_id,
                    $overpaidDelta,
                    $data['discount_date'],
                    'Overpayment credit from invoice ' . $targetInvoice->invoice_no . ' after discount: ' . $discount->reason,
                    $targetInvoice->id,
                    'invoice_discount',
                    $discount->id,
                    $createdBy
                );
            }

            return $discount;
        });
    }
}

