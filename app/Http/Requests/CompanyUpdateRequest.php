<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'whatsapp_number' => $this->normalizeWhatsappNumber($this->input('whatsapp_number')),
        ]);
    }

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
            'whatsapp_number' => ['nullable', 'regex:/^92\d{10}$/'],
            'company_ptcl' => 'required',
            'company_address' => 'required',
            'currency' => 'required',
            'package' => 'required',
            'invoice_type' => 'nullable|in:branch,terminal',
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

    private function normalizeWhatsappNumber($value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0092')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (!str_starts_with($digits, '92')) {
            $digits = '92' . $digits;
        }

        return $digits;
    }
}
