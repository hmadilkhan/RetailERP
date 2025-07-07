<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'companyname' => 'required',
            'country' => 'required',
            'city' => 'required',
            'company_email' => 'required',
            'company_mobile' => 'required',
            'company_ptcl' => 'required',
            'company_address' => 'required',
            'vdimg' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'posbgimg' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'ordercallingbgimg' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
