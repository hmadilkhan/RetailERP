<?php

namespace App\Services;

use App\adminCompany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Exception;

class CompanyService
{
    public function getAll()
    {
        return adminCompany::get_company();
    }

    public function getFormData()
    {
        return [
            'country' => adminCompany::getcountry(),
            'city' => adminCompany::getcity(),
            'currencies' => DB::table('currencies')->get(),
            'packages' => DB::table('packages')->get(),
            'branches' => collect(),
            'terminals' => collect(),
            'billingRates' => collect(),
        ];
    }

    public function getEditFormData($id)
    {
        $company = adminCompany::getCompanyById($id);
        $currencies = DB::table('currencies')->get();
        $setting = DB::table('settings')->where('company_id', $id)->first();
        $currencyname = $setting ? json_decode($setting->data, true)['currency'] : null;
        $packages = DB::table('packages')->get();
        return [
            'country' => adminCompany::getcountry(),
            'city' => adminCompany::getcity(),
            'company' => $company,
            'currencies' => $currencies,
            'currencyname' => $currencyname,
            'packages' => $packages,
            'branches' => DB::table('branch')->where('company_id', $id)->select('branch_id', 'branch_name')->orderBy('branch_name')->get(),
            'terminals' => DB::table('terminal_details')
                ->join('branch', 'branch.branch_id', '=', 'terminal_details.branch_id')
                ->where('branch.company_id', $id)
                ->select('terminal_details.terminal_id', 'terminal_details.terminal_name', 'terminal_details.branch_id')
                ->orderBy('terminal_details.terminal_name')
                ->get(),
            'billingRates' => DB::table('company_billing_rates')->where('company_id', $id)->orderByDesc('id')->get(),
        ];
    }

    public function create(array $data)
    {
        $items = [
            'status_id' => 1,
            'country_id' => $data['country'],
            'city_id' => $data['city'],
            'name' => $data['companyname'],
            'address' => $data['company_address'],
            'email' => $data['company_email'],
            'ptcl_contact' => $data['company_ptcl'],
            'mobile_contact' => $data['company_mobile'],
            'whatsapp_number' => $data['whatsapp_number'] ?? null,
            'latitude' => null,
            'longitude' => null,
            'logo' => $data['vdimg'] ?? '',
            'pos_background' => $data['posbgimg'] ?? '',
            'order_calling_display_image' => $data['ordercallingbgimg'] ?? '',
            'created_at' => now(),
            'updated_at' => now(),
            'package_id' => $data['package'] ?? null,
            'invoice_type' => $data['invoice_type'] ?? 'branch',
            'billing_cycle_day' => $data['billing_cycle_day'] ?? 1,
            'invoice_prefix' => $data['invoice_prefix'] ?? null,
            'payment_due_days' => $data['payment_due_days'] ?? 15,
            'is_auto_invoice' => $data['is_auto_invoice'] ?? 1,
            'monthly_charges_amount' => $data['monthly_charges_amount'] ?? 0,
        ];
        return DB::transaction(function () use ($data, $items) {
            $companyId = adminCompany::insert($items);
            if (!empty($data['currency'])) {
                $myObj = new \stdClass();
                $myObj->currency = $data['currency'];
                $myJSON = json_encode($myObj);
                DB::table('settings')->insert([
                    'company_id' => $companyId,
                    'data' => $myJSON,
                ]);
            }

            $this->syncBillingRates($companyId, $data['billing_rates'] ?? [], $data['monthly_charges_amount'] ?? 0);
            return $companyId;
        });
    }

    public function update($id, array $data)
    {
        $items = [
            'status_id' => 1,
            'country_id' => $data['country'],
            'city_id' => $data['city'],
            'name' => $data['companyname'],
            'address' => $data['company_address'],
            'email' => $data['company_email'],
            'ptcl_contact' => $data['company_ptcl'],
            'mobile_contact' => $data['company_mobile'],
            'whatsapp_number' => $data['whatsapp_number'] ?? null,
            'logo' => $data['vdimg'] ?? $data['prev_logo'] ?? '',
            'pos_background' => $data['posbgimg'] ?? $data['pos_bg_logo'] ?? '',
            'order_calling_display_image' => $data['ordercallingbgimg'] ?? $data['prev_order_calling_display'] ?? '',
            'updated_at' => now(),
            'package_id' => $data['package'] ?? null,
            'invoice_type' => $data['invoice_type'] ?? 'branch',
            'billing_cycle_day' => $data['billing_cycle_day'] ?? 1,
            'invoice_prefix' => $data['invoice_prefix'] ?? null,
            'payment_due_days' => $data['payment_due_days'] ?? 15,
            'is_auto_invoice' => $data['is_auto_invoice'] ?? 1,
            'monthly_charges_amount' => $data['monthly_charges_amount'] ?? 0,
        ];
        return DB::transaction(function () use ($id, $data, $items) {
            $result = adminCompany::updateCompany($items, $id);
            if (!empty($data['currency'])) {
                $myObj = new \stdClass();
                $myObj->currency = $data['currency'];
                $myJSON = json_encode($myObj);
                DB::table('settings')->updateOrInsert(
                    ['company_id' => $id],
                    ['data' => $myJSON]
                );
            }

            $this->syncBillingRates($id, $data['billing_rates'] ?? [], $data['monthly_charges_amount'] ?? 0);
            return $result;
        });
    }

    private function syncBillingRates(int $companyId, array $rates, $fallbackAmount = 0): void
    {
        DB::table('company_billing_rates')->where('company_id', $companyId)->delete();

        $rows = [];
        foreach ($rates as $rate) {
            if (empty($rate['scope_type']) || !isset($rate['rate']) || $rate['rate'] === '') {
                continue;
            }

            $scopeType = $rate['scope_type'];
            $scopeId = $scopeType === 'company' ? null : (!empty($rate['scope_id']) ? (int) $rate['scope_id'] : null);

            if (in_array($scopeType, ['branch', 'terminal'], true) && empty($scopeId)) {
                continue;
            }

            $rows[] = [
                'company_id' => $companyId,
                'scope_type' => $scopeType,
                'scope_id' => $scopeId,
                'charge_type' => $rate['charge_type'] ?? 'flat_monthly',
                'rate' => (float) $rate['rate'],
                'effective_from' => $rate['effective_from'] ?? now()->toDateString(),
                'effective_to' => !empty($rate['effective_to']) ? $rate['effective_to'] : null,
                'is_active' => !empty($rate['is_active']) ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (empty($rows) && (float) $fallbackAmount > 0) {
            $rows[] = [
                'company_id' => $companyId,
                'scope_type' => 'company',
                'scope_id' => null,
                'charge_type' => 'flat_monthly',
                'rate' => (float) $fallbackAmount,
                'effective_from' => now()->toDateString(),
                'effective_to' => null,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($rows)) {
            DB::table('company_billing_rates')->insert($rows);
        }
    }

    public static function delete($id)
    {
        $details = adminCompany::getCompanyById($id);
        $result = adminCompany::deleteCompany($id);
        if ($result && !empty($details[0]->logo)) {
            // Remove image logic here if needed
        }
        return $result;
    }
} 
