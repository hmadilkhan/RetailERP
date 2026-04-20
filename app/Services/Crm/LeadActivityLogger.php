<?php

namespace App\Services\Crm;

use App\Models\Crm\Lead;
use App\Models\Crm\LeadActivity;
use App\Models\Crm\LeadFollowup;
use App\Models\Crm\LeadQuotation;
use App\Models\Customer;
use App\Models\Crm\Product;
use App\Models\Crm\ProductType;
use App\Models\Crm\LeadStatus;
use App\User;

class LeadActivityLogger
{
    public function logLeadCreated(Lead $lead, ?User $actor = null): LeadActivity
    {
        return $this->log(
            lead: $lead,
            type: 'lead_created',
            message: 'Lead was created.',
            newValue: [
                'lead_code' => $lead->lead_code,
                'status' => optional($lead->status)->name,
                'assigned_to' => optional($lead->assignedUser)->fullname,
            ],
            actor: $actor
        );
    }

    public function logLeadUpdated(Lead $lead, array $original, ?User $actor = null): void
    {
        $this->logStatusChange($lead, $original, $actor);
        $this->logAssignmentChange($lead, $original, $actor);
        $this->logConverted($lead, $original, $actor);
        $this->logClosedOutcome($lead, $original, $actor);

        $trackedFields = [
            'contact_person_name' => 'Contact person',
            'company_name' => 'Company',
            'contact_number' => 'Contact number',
            'email' => 'Email',
            'city' => 'City',
            'product_type_id' => 'Product type',
            'product_id' => 'Product',
            'priority' => 'Priority',
            'temperature' => 'Temperature',
            'expected_deal_value' => 'Expected deal value',
            'probability_percent' => 'Probability',
            'next_followup_date' => 'Next follow-up date',
        ];

        $changes = [];
        foreach ($trackedFields as $field => $label) {
            $old = $this->readableFieldValue($lead, $field, $original[$field] ?? null);
            $new = $this->readableFieldValue($lead, $field, $lead->{$field});

            if ($old !== $new) {
                $changes[$label] = [
                    'old' => $old,
                    'new' => $new,
                ];
            }
        }

        if ($changes !== []) {
            $this->log(
                lead: $lead,
                type: 'lead_updated',
                message: 'Lead information was updated.',
                oldValue: collect($changes)->map(fn ($change) => $change['old'])->all(),
                newValue: collect($changes)->map(fn ($change) => $change['new'])->all(),
                actor: $actor
            );
        }
    }

    public function logFollowupAdded(Lead $lead, LeadFollowup $followup, ?User $actor = null): LeadActivity
    {
        return $this->log(
            lead: $lead,
            type: 'followup_added',
            message: sprintf(
                '%s follow-up added for %s.',
                $followup->followup_type,
                $followup->followup_date->format('d M Y')
            ),
            newValue: [
                'followup_type' => $followup->followup_type,
                'followup_date' => $followup->followup_date->format('Y-m-d'),
                'next_followup_date' => optional($followup->next_followup_date)->format('Y-m-d'),
                'followup_result' => $followup->followup_result,
            ],
            actor: $actor
        );
    }

    public function logAttachmentUploaded(Lead $lead, string $fileName, ?User $actor = null): LeadActivity
    {
        return $this->log(
            lead: $lead,
            type: 'attachment_uploaded',
            message: sprintf('Attachment uploaded: %s.', $fileName),
            newValue: ['file_name' => $fileName],
            actor: $actor
        );
    }

    public function logAttachmentDeleted(Lead $lead, string $fileName, ?User $actor = null): LeadActivity
    {
        return $this->log(
            lead: $lead,
            type: 'attachment_deleted',
            message: sprintf('Attachment deleted: %s.', $fileName),
            oldValue: ['file_name' => $fileName],
            actor: $actor
        );
    }

    public function logLeadConvertedToCustomer(Lead $lead, Customer $customer, ?User $actor = null): LeadActivity
    {
        return $this->log(
            lead: $lead,
            type: 'lead_converted',
            message: sprintf('Lead was converted to ERP customer %s.', $customer->name),
            newValue: [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
            ],
            actor: $actor
        );
    }

    public function logQuotationCreated(Lead $lead, LeadQuotation $quotation, ?User $actor = null): LeadActivity
    {
        return $this->log(
            lead: $lead,
            type: 'quotation_created',
            message: sprintf('Quotation %s was created.', $quotation->quotation_no),
            newValue: [
                'quotation_no' => $quotation->quotation_no,
                'status' => $quotation->statusLabel(),
                'total' => (string) $quotation->total,
            ],
            actor: $actor
        );
    }

    public function logQuotationUpdated(Lead $lead, LeadQuotation $quotation, ?User $actor = null): LeadActivity
    {
        return $this->log(
            lead: $lead,
            type: 'quotation_updated',
            message: sprintf('Quotation %s was updated.', $quotation->quotation_no),
            newValue: [
                'quotation_no' => $quotation->quotation_no,
                'status' => $quotation->statusLabel(),
                'total' => (string) $quotation->total,
            ],
            actor: $actor
        );
    }

    public function logQuotationStatusChanged(Lead $lead, LeadQuotation $quotation, string $oldStatus, string $newStatus, ?User $actor = null): LeadActivity
    {
        return $this->log(
            lead: $lead,
            type: 'quotation_status_changed',
            message: sprintf(
                'Quotation %s status changed from %s to %s.',
                $quotation->quotation_no,
                LeadQuotation::STATUSES[$oldStatus] ?? ucfirst($oldStatus),
                LeadQuotation::STATUSES[$newStatus] ?? ucfirst($newStatus)
            ),
            oldValue: ['status' => LeadQuotation::STATUSES[$oldStatus] ?? ucfirst($oldStatus)],
            newValue: ['status' => LeadQuotation::STATUSES[$newStatus] ?? ucfirst($newStatus)],
            actor: $actor
        );
    }

    public function log(Lead $lead, string $type, string $message, ?array $oldValue = null, ?array $newValue = null, ?User $actor = null): LeadActivity
    {
        return $lead->activities()->create([
            'activity_type' => $type,
            'message' => $message,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'created_by' => $actor?->id ?? auth()->id(),
        ]);
    }

    private function logStatusChange(Lead $lead, array $original, ?User $actor): void
    {
        $oldStatusId = $original['status_id'] ?? null;
        $newStatusId = $lead->status_id;

        if ((string) $oldStatusId === (string) $newStatusId) {
            return;
        }

        $oldStatus = $this->statusName($oldStatusId);
        $newStatus = optional($lead->status)->name;

        $this->log(
            lead: $lead,
            type: 'status_changed',
            message: sprintf('Lead status changed from %s to %s.', $oldStatus ?? 'N/A', $newStatus ?? 'N/A'),
            oldValue: ['status' => $oldStatus],
            newValue: ['status' => $newStatus],
            actor: $actor
        );
    }

    private function logAssignmentChange(Lead $lead, array $original, ?User $actor): void
    {
        $oldAssignedTo = $original['assigned_to'] ?? null;
        $newAssignedTo = $lead->assigned_to;

        if ((string) $oldAssignedTo === (string) $newAssignedTo) {
            return;
        }

        $oldUser = $this->userName($oldAssignedTo);
        $newUser = optional($lead->assignedUser)->fullname;

        $this->log(
            lead: $lead,
            type: 'lead_assigned',
            message: sprintf('Lead assignment changed from %s to %s.', $oldUser ?? 'Unassigned', $newUser ?? 'Unassigned'),
            oldValue: ['assigned_to' => $oldUser],
            newValue: ['assigned_to' => $newUser],
            actor: $actor
        );
    }

    private function logConverted(Lead $lead, array $original, ?User $actor): void
    {
        $oldConverted = (bool) ($original['is_converted'] ?? false);
        $newConverted = (bool) $lead->is_converted;

        if ($oldConverted || !$newConverted) {
            return;
        }

        $this->log(
            lead: $lead,
            type: 'lead_converted',
            message: 'Lead was marked as converted.',
            oldValue: ['is_converted' => 'No'],
            newValue: ['is_converted' => 'Yes'],
            actor: $actor
        );
    }

    private function logClosedOutcome(Lead $lead, array $original, ?User $actor): void
    {
        $oldStatusId = $original['status_id'] ?? null;
        $newSlug = optional($lead->status)->slug;

        if ((string) $oldStatusId === (string) $lead->status_id) {
            return;
        }

        if ($newSlug === 'won') {
            $this->log(
                lead: $lead,
                type: 'lead_marked_won',
                message: 'Lead was marked as Won.',
                newValue: ['status' => 'Won'],
                actor: $actor
            );
        }

        if ($newSlug === 'lost') {
            $this->log(
                lead: $lead,
                type: 'lead_marked_lost',
                message: 'Lead was marked as Lost.',
                newValue: ['status' => 'Lost'],
                actor: $actor
            );
        }
    }

    private function readableFieldValue(Lead $lead, string $field, mixed $value): mixed
    {
        return match ($field) {
            'status_id' => $this->statusName($value),
            'assigned_to' => $this->userName($value) ?? 'Unassigned',
            'product_type_id' => $value ? ProductType::query()->whereKey($value)->value('name') : null,
            'product_id' => $value ? Product::query()->whereKey($value)->value('name') : null,
            'next_followup_date' => $value ? \Carbon\Carbon::parse($value)->format('d M Y') : null,
            default => $value,
        };
    }

    private function statusName(mixed $statusId): ?string
    {
        if (!$statusId) {
            return null;
        }

        return LeadStatus::query()->whereKey($statusId)->value('name');
    }

    private function userName(mixed $userId): ?string
    {
        if (!$userId) {
            return null;
        }

        return User::query()->whereKey($userId)->value('fullname');
    }
}
