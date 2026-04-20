<?php

namespace App\Http\Requests\Crm;

use App\Models\Crm\LeadStatus;
use App\Models\Crm\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->filled('email') ? strtolower(trim((string) $this->input('email'))) : null,
            'website' => $this->filled('website') ? trim((string) $this->input('website')) : null,
            'contact_number' => $this->normalizePhone($this->input('contact_number')),
            'alternate_number' => $this->normalizePhone($this->input('alternate_number')),
            'whatsapp_number' => $this->normalizePhone($this->input('whatsapp_number')),
            'company_name' => $this->emptyToNull($this->input('company_name')),
            'campaign_name' => $this->emptyToNull($this->input('campaign_name')),
            'referral_person_name' => $this->emptyToNull($this->input('referral_person_name')),
            'inquiry_type' => $this->emptyToNull($this->input('inquiry_type')),
            'business_type' => $this->emptyToNull($this->input('business_type')),
            'existing_system' => $this->emptyToNull($this->input('existing_system')),
            'competitor_name' => $this->emptyToNull($this->input('competitor_name')),
            'budget_range' => $this->emptyToNull($this->input('budget_range')),
            'preferred_contact_method' => $this->emptyToNull($this->input('preferred_contact_method')),
            'lost_reason' => $this->emptyToNull($this->input('lost_reason')),
            'assigned_to' => $this->emptyToNull($this->input('assigned_to')),
            'product_id' => $this->emptyToNull($this->input('product_id')),
            'required_quantity' => $this->emptyToNull($this->input('required_quantity')),
            'branch_count' => $this->emptyToNull($this->input('branch_count')),
            'lead_score' => $this->emptyToNull($this->input('lead_score')) ?? 0,
            'expected_deal_value' => $this->emptyToNull($this->input('expected_deal_value')),
            'probability_percent' => $this->emptyToNull($this->input('probability_percent')) ?? 0,
            'is_converted' => $this->boolean('is_converted'),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contact_person_name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:50'],
            'alternate_number' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'country' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string'],
            'website' => ['nullable', 'string', 'max:255'],
            'lead_source_id' => ['required', 'exists:crm_lead_sources,id'],
            'campaign_name' => ['nullable', 'string', 'max:255'],
            'referral_person_name' => ['nullable', 'string', 'max:255'],
            'product_type_id' => ['required', 'exists:crm_product_types,id'],
            'product_id' => [
                'nullable',
                'integer',
                Rule::exists('crm_products', 'id')->where(function ($query) {
                    $query->where('product_type_id', $this->input('product_type_id'));
                }),
            ],
            'inquiry_type' => ['nullable', 'string', 'max:100'],
            'business_type' => ['nullable', 'string', 'max:100'],
            'required_quantity' => ['nullable', 'integer', 'min:0'],
            'branch_count' => ['nullable', 'integer', 'min:0'],
            'existing_system' => ['nullable', 'string', 'max:255'],
            'competitor_name' => ['nullable', 'string', 'max:255'],
            'budget_range' => ['nullable', 'string', 'max:255'],
            'expected_go_live_date' => ['nullable', 'date'],
            'requirement_summary' => ['required', 'string'],
            'status_id' => ['required', 'exists:crm_lead_statuses,id'],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'temperature' => ['nullable', Rule::in(['cold', 'warm', 'hot'])],
            'assigned_to' => ['nullable', 'exists:user_details,id'],
            'lead_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'expected_deal_value' => ['nullable', 'numeric', 'min:0'],
            'probability_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'last_contact_date' => ['nullable', 'date'],
            'next_followup_date' => ['nullable', 'date'],
            'preferred_contact_method' => ['nullable', Rule::in(['call', 'email', 'whatsapp', 'meeting'])],
            'lost_reason' => ['nullable', 'string', Rule::requiredIf(fn (): bool => $this->isLostStatus())],
            'is_converted' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'lost_reason.required' => 'Lost reason is required when the lead status is Lost.',
            'product_id.exists' => 'Selected product must belong to the chosen product type.',
        ];
    }

    protected function isLostStatus(): bool
    {
        $lostStatusId = LeadStatus::query()->where('slug', 'lost')->value('id');

        return $lostStatusId !== null && (int) $this->input('status_id') === (int) $lostStatusId;
    }

    protected function normalizePhone($value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function emptyToNull($value): mixed
    {
        return blank($value) ? null : $value;
    }
}
