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
            'latitude' => null,
            'longitude' => null,
            'logo' => $data['vdimg'] ?? '',
            'pos_background' => $data['posbgimg'] ?? '',
            'order_calling_display_image' => $data['ordercallingbgimg'] ?? '',
            'created_at' => now(),
            'updated_at' => now(),
            'package_id' => $data['package'] ?? null,
        ];
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
        return $companyId;
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
            'logo' => $data['vdimg'] ?? $data['prev_logo'] ?? '',
            'pos_background' => $data['posbgimg'] ?? $data['pos_bg_logo'] ?? '',
            'order_calling_display_image' => $data['ordercallingbgimg'] ?? $data['prev_order_calling_display'] ?? '',
            'updated_at' => now(),
            'package_id' => $data['package'] ?? null,
        ];
        $result = adminCompany::updateCompany($items, $id);
        if (!empty($data['currency'])) {
            $myObj = new \stdClass();
            $myObj->currency = $data['currency'];
            $myJSON = json_encode($myObj);
            DB::table('settings')->updateOrInsert(
                ['company_id' => $id], // Search conditions
                ['data' => $myJSON]    // Fields to update or insert
            );
        }
        return $result;
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