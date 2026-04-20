<?php

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;

class BulkAssignLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lead_ids' => ['required', 'array', 'min:1'],
            'lead_ids.*' => ['required', 'integer', 'exists:crm_leads,id'],
            'assigned_to' => ['nullable', 'exists:user_details,id'],
        ];
    }
}
