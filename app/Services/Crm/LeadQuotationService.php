<?php

namespace App\Services\Crm;

use App\Models\Crm\Lead;
use App\Models\Crm\LeadQuotation;
use App\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LeadQuotationService
{
    public function __construct(private readonly LeadActivityLogger $activityLogger)
    {
    }

    public function create(Lead $lead, array $payload, User $actor): LeadQuotation
    {
        return DB::transaction(function () use ($lead, $payload, $actor): LeadQuotation {
            $totals = $this->calculateTotals($payload['items'], (float) $payload['discount'], (float) $payload['tax']);

            $quotation = $lead->quotations()->create([
                'quotation_no' => LeadQuotation::generateQuotationNo(),
                'quotation_date' => $payload['quotation_date'],
                'valid_until' => $payload['valid_until'] ?? null,
                'subtotal' => $totals['subtotal'],
                'discount' => $payload['discount'] ?? 0,
                'tax' => $payload['tax'] ?? 0,
                'total' => $totals['total'],
                'status' => $payload['status'],
                'notes' => $payload['notes'] ?? null,
                'created_by' => $actor->id,
            ]);

            $quotation->items()->createMany($totals['items']);
            $quotation->load(['items', 'creator']);

            $this->activityLogger->logQuotationCreated($lead, $quotation, $actor);

            if ($quotation->status !== LeadQuotation::STATUS_DRAFT) {
                $this->activityLogger->logQuotationStatusChanged($lead, $quotation, LeadQuotation::STATUS_DRAFT, $quotation->status, $actor);
            }

            return $quotation;
        });
    }

    public function update(LeadQuotation $quotation, array $payload, User $actor): LeadQuotation
    {
        if ($quotation->isLocked()) {
            throw new RuntimeException('This quotation can no longer be edited because it is already finalized.');
        }

        return DB::transaction(function () use ($quotation, $payload, $actor): LeadQuotation {
            $lead = $quotation->lead;
            $originalStatus = $quotation->status;
            $totals = $this->calculateTotals($payload['items'], (float) $payload['discount'], (float) $payload['tax']);

            $quotation->update([
                'quotation_date' => $payload['quotation_date'],
                'valid_until' => $payload['valid_until'] ?? null,
                'subtotal' => $totals['subtotal'],
                'discount' => $payload['discount'] ?? 0,
                'tax' => $payload['tax'] ?? 0,
                'total' => $totals['total'],
                'status' => $payload['status'],
                'notes' => $payload['notes'] ?? null,
            ]);

            $quotation->items()->delete();
            $quotation->items()->createMany($totals['items']);
            $quotation->load(['items', 'creator']);

            $this->activityLogger->logQuotationUpdated($lead, $quotation, $actor);

            if ($originalStatus !== $quotation->status) {
                $this->activityLogger->logQuotationStatusChanged($lead, $quotation, $originalStatus, $quotation->status, $actor);
            }

            return $quotation;
        });
    }

    public function updateStatus(LeadQuotation $quotation, string $status, User $actor): LeadQuotation
    {
        $oldStatus = $quotation->status;

        if ($oldStatus === $status) {
            return $quotation;
        }

        $quotation->update(['status' => $status]);
        $quotation->refresh();

        $this->activityLogger->logQuotationStatusChanged($quotation->lead, $quotation, $oldStatus, $status, $actor);

        return $quotation;
    }

    private function calculateTotals(array $items, float $discount, float $tax): array
    {
        $normalizedItems = collect($items)
            ->map(function (array $item): array {
                $quantity = round((float) $item['quantity'], 2);
                $unitPrice = round((float) $item['unit_price'], 2);
                $lineTotal = round($quantity * $unitPrice, 2);

                return [
                    'item_name' => $item['item_name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $lineTotal,
                ];
            })
            ->all();

        $subtotal = round(collect($normalizedItems)->sum('total'), 2);
        $total = round(max(0, $subtotal - $discount + $tax), 2);

        return [
            'items' => $normalizedItems,
            'subtotal' => $subtotal,
            'total' => $total,
        ];
    }
}
