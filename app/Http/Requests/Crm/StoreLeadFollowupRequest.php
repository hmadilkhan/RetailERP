<?php

namespace App\Http\Requests\Crm;

use App\Models\Crm\LeadFollowup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadFollowupRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'remarks' => trim((string) $this->input('remarks')),
            'followup_result' => $this->filled('followup_result') ? trim((string) $this->input('followup_result')) : null,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'followup_date' => ['required', 'date'],
            'followup_type' => ['required', Rule::in(LeadFollowup::TYPES)],
            'remarks' => ['required', 'string'],
            'next_followup_date' => ['nullable', 'date', 'after_or_equal:followup_date'],
            'followup_result' => ['nullable', 'string', 'max:150'],
        ];
    }
}
