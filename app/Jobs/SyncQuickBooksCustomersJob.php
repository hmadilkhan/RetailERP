<?php

namespace App\Jobs;

use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\Customer;
use App\Services\QuickBooks\QuickBooksAuthService;
use App\Services\QuickBooks\QuickBooksCustomerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncQuickBooksCustomersJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    public function handle()
    {
        $companies = Company::all(); // or only active ones

        foreach ($companies as $company) {

            $authService = app(QuickBooksAuthService::class);
            $quickBooksService = new QuickBooksCustomerService($authService, $company->id);

            $customers = Customer::where('company_id', $company->id)
                                 ->where(function ($q) {
                                     $q->where('needs_qb_update',true)
                                       ->orWhere('needs_qb_update', true)
                                       ->orWhere('needs_qb_deletion', true);
                                 })
                                 ->limit(100)
                                 ->get();

            foreach ($customers as $customer) {
                try {
                    if ($customer->needs_qb_deletion && $customer->qb_customer_id) {
                        // $quickBooksService->deleteCustomer($customer->qb_customer_id);
                        // $customer->delete(); // or soft delete
                    } elseif ($customer->qb_customer_id && $customer->needs_qb_update) {
                        $qbData = $this->formatQuickBooksData($customer);
                        $quickBooksService->updateCustomer($customer->qb_customer_id, $qbData);
                        $customer->update(['needs_qb_update' => false]);
                    } elseif (is_null($customer->qb_customer_id)) {
                        $qbData = $this->formatQuickBooksData($customer);
                        $response = $quickBooksService->createCustomer($qbData);
                        if (isset($response->Id)) {
                            $customer->update(['qb_customer_id' => $response->Id]);
                        }
                    }
                    Log::info("QB Sync Success for customer {$customer->id}: ");
                } catch (\Exception $e) {
                    Log::error("QB Sync Failed for customer {$customer->id}: " . $e->getMessage());
                }
            }
        }
    }

    protected function formatQuickBooksData($customer)
    {
        $country = Country::find($customer->country_id);
        $city = City::find($customer->city_id);

        return [
            'GivenName' => $customer->name,
            'DisplayName' => $customer->name,
            'PrimaryEmailAddr' => ['Address' => $customer->email],
            'BillAddr' => [
                'Line1' => $customer->address,
                'City' => $city?->city_name ?? '',
                'Country' => $country?->country_name ?? '',
            ],
            'PrimaryPhone' => ['FreeFormNumber' => $customer->phone]
        ];
    }
}
