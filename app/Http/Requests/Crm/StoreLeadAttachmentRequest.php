<?php

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attachments' => ['required', 'array', 'min:1', 'max:10'],
            'attachments.*' => [
                'required',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'attachments.required' => 'Please select at least one attachment.',
            'attachments.*.mimes' => 'Only images, PDF, DOC, DOCX, XLS, and XLSX files are allowed.',
            'attachments.*.max' => 'Each attachment must be 10 MB or smaller.',
        ];
    }
}
