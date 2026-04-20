<?php

namespace App\Services\Crm;

use App\Models\City;
use App\Models\Country;
use App\Models\Crm\Lead;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class LeadConversionService
{
    public function __construct(private readonly LeadActivityLogger $activityLogger)
    {
    }

    public function convert(Lead $lead, int $actorId): Customer
    {
        if ($lead->is_converted && $lead->converted_customer_id) {
            $existingCustomer = Customer::query()->find($lead->converted_customer_id);

            if ($existingCustomer) {
                return $existingCustomer;
            }
        }

        if (!in_array(optional($lead->status)->slug, ['qualified', 'won'], true)) {
            throw new RuntimeException('Only qualified or won leads can be converted to customers.');
        }

        return DB::transaction(function () use ($lead, $actorId): Customer {
            $customer = $this->findExistingCustomer($lead) ?? $this->createCustomerFromLead($lead, $actorId);

            $original = $lead->only(['is_converted']);

            $lead->forceFill([
                'is_converted' => true,
                'converted_customer_id' => $customer->id,
                'converted_at' => Carbon::now(),
                'converted_by' => $actorId,
                'updated_by' => $actorId,
            ])->save();

            $lead->load(['status', 'assignedUser', 'convertedCustomer']);
            $this->activityLogger->logLeadUpdated($lead, $original, auth()->user());
            $this->activityLogger->logLeadConvertedToCustomer($lead, $customer, auth()->user());

            return $customer;
        });
    }

    public function findExistingCustomer(Lead $lead): ?Customer
    {
        return Customer::query()
            ->when($lead->contact_number, function ($query) use ($lead) {
                $query->orWhere('mobile', $lead->contact_number)
                    ->orWhere('phone', $lead->contact_number);
            })
            ->when($lead->email, fn ($query) => $query->orWhereRaw('LOWER(email) = ?', [strtolower((string) $lead->email)]))
            ->when($lead->company_name, fn ($query) => $query->orWhere('name', $lead->company_name))
            ->when($lead->contact_person_name, fn ($query) => $query->orWhere('name', $lead->contact_person_name))
            ->orderByDesc('id')
            ->first();
    }

    private function createCustomerFromLead(Lead $lead, int $actorId): Customer
    {
        $countryId = $this->resolveCountryId($lead->country);
        $cityId = $this->resolveCityId($lead->city, $countryId);

        return Customer::query()->create([
            'user_id' => $lead->assigned_to ?: $lead->created_by ?: $actorId,
            'status_id' => 1,
            'country_id' => $countryId,
            'city_id' => $cityId,
            'name' => $lead->company_name ?: $lead->contact_person_name,
            'mobile' => $lead->contact_number,
            'phone' => $lead->alternate_number ?: $lead->whatsapp_number ?: $lead->contact_number,
            'address' => $lead->address ?: 'Lead converted from CRM',
            'email' => $lead->email,
            'slug' => strtolower(Str::random(4)),
            'company_id' => session('company_id'),
            'customer_area' => $lead->city,
            'customer_type' => 'crm-converted',
            'payment_type' => 'credit',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function resolveCountryId(?string $countryName): int
    {
        if ($countryName) {
            $countryId = Country::query()
                ->whereRaw('LOWER(country_name) = ?', [strtolower(trim($countryName))])
                ->value('country_id');

            if ($countryId) {
                return (int) $countryId;
            }
        }

        return 170;
    }

    private function resolveCityId(?string $cityName, int $countryId): int
    {
        if ($cityName) {
            $cityId = City::query()
                ->where('country_id', $countryId)
                ->whereRaw('LOWER(city_name) = ?', [strtolower(trim($cityName))])
                ->value('city_id');

            if ($cityId) {
                return (int) $cityId;
            }
        }

        return (int) City::query()->where('country_id', $countryId)->value('city_id');
    }
}
