<?php

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;

class LeadDashboardFilterRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'date_from' => $this->filled('date_from') ? trim((string) $this->input('date_from')) : null,
            'date_to' => $this->filled('date_to') ? trim((string) $this->input('date_to')) : null,
            'lead_source_id' => $this->emptyToNull($this->input('lead_source_id')),
            'product_type_id' => $this->emptyToNull($this->input('product_type_id')),
            'product_id' => $this->emptyToNull($this->input('product_id')),
            'assigned_to' => $this->emptyToNull($this->input('assigned_to')),
            'status_id' => $this->emptyToNull($this->input('status_id')),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'lead_source_id' => ['nullable', 'integer', 'exists:crm_lead_sources,id'],
            'product_type_id' => ['nullable', 'integer', 'exists:crm_product_types,id'],
            'product_id' => ['nullable', 'integer', 'exists:crm_products,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:user_details,id'],
            'status_id' => ['nullable', 'integer', 'exists:crm_lead_statuses,id'],
        ];
    }

    private function emptyToNull(mixed $value): mixed
    {
        return blank($value) ? null : $value;
    }
}
