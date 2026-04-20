<?php

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;

class AssignLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'assigned_to' => blank($this->input('assigned_to')) ? null : $this->input('assigned_to'),
        ]);
    }

    public function rules(): array
    {
        return [
            'assigned_to' => ['nullable', 'exists:user_details,id'],
        ];
    }
}
