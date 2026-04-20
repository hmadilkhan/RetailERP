<?php

namespace App\Http\Requests\Crm;

use App\Models\Crm\LeadQuotation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadQuotationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $items = collect($this->input('items', []))
            ->map(function ($item) {
                return [
                    'item_name' => trim((string) ($item['item_name'] ?? '')),
                    'description' => blank($item['description'] ?? null) ? null : trim((string) $item['description']),
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_price' => $item['unit_price'] ?? 0,
                ];
            })
            ->filter(fn ($item) => $item['item_name'] !== '')
            ->values()
            ->all();

        $this->merge([
            'discount' => $this->input('discount', 0) ?: 0,
            'tax' => $this->input('tax', 0) ?: 0,
            'notes' => blank($this->input('notes')) ? null : trim((string) $this->input('notes')),
            'status' => $this->input('status', LeadQuotation::STATUS_DRAFT),
            'items' => $items,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quotation_date' => ['required', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:quotation_date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(array_keys(LeadQuotation::STATUSES))],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'At least one quotation item is required.',
            'items.min' => 'At least one quotation item is required.',
        ];
    }
}
