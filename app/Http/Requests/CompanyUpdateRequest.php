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
            'invoice_type' => 'required|in:branch,terminal',
            'billing_cycle_day' => 'nullable|integer|min:1|max:28',
            'invoice_prefix' => 'nullable|string|max:30',
            'payment_due_days' => 'nullable|integer|min:1|max:90',
            'monthly_charges_amount' => 'nullable|numeric|min:0',
            'is_auto_invoice' => 'nullable|boolean',
            'billing_rates' => 'nullable|array',
            'billing_rates.*.scope_type' => 'required_with:billing_rates|in:company,branch,terminal',
            'billing_rates.*.scope_id' => 'nullable|integer',
            'billing_rates.*.charge_type' => 'required_with:billing_rates|in:flat_monthly,per_order,per_amount',
            'billing_rates.*.rate' => 'required_with:billing_rates|numeric|min:0',
            'billing_rates.*.effective_from' => 'required_with:billing_rates|date',
            'billing_rates.*.effective_to' => 'nullable|date',
            'billing_rates.*.is_active' => 'nullable|boolean',
            'vdimg' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'posbgimg' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'ordercallingbgimg' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
