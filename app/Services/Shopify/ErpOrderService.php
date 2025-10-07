<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ErpOrderService
{
    /**
     * Insert Shopify order into ERP tables
     */
    public function createOrder(array $payload)
    {
        return DB::transaction(function () use ($payload) {
            // 1️⃣ Ensure customer exists or create a new one
            $customer = $this->findOrCreateCustomer($payload['customer'], $payload['shop']);

            // 1️⃣ Insert into sales_receipts
            $receiptId = DB::table('sales_receipts')->insertGetId([
                'receipt_no'        => 'WEB-' . Str::padLeft(rand(1, 999999), 6, '0'),
                'order_mode_id'     => 4, // e.g. Online Order
                'userid'            => null,
                'customer_id'       => $customer['id'], // can be linked later if you sync customers
                'url_orderid'       => $payload['order_id'],
                'payment_id'        => 1, // You can map payment types later
                'total_amount'      => $payload['total_price'],
                'actual_amount'     => $payload['total_price'],
                'total_item_qty'    => collect($payload['line_items'])->sum('quantity'),
                'status'            => 1,
                'delivery_date'     => null,
                'branch'            => 1,
                'terminal_id'       => 1,
                'sales_person_id'   => null,
                'web'               => 1,
                'date'              => Carbon::now()->format('Y-m-d'),
                'time'              => Carbon::now()->format('H:i:s'),
                'payment_status'    => $payload['financial_status'],
                'website_id'        => $this->getWebsiteIdFromShop($payload['shop']),
                'delivery_area_name' => $payload['shop']['domain'] ?? null,
                'delivery_type'     => 'Shopify',
                'delivery_instructions' => 'Synced from Shopify order',
                'order_reference_number' => $payload['name'],
                'turnaround_time'   => now()->diffInMinutes(Carbon::parse($payload['synced_at'])) . ' mins',
            ]);

            // 2️⃣ Insert each line item into sales_receipt_details
            foreach ($payload['line_items'] as $item) {
                DB::table('sales_receipt_details')->insert([
                    'receipt_id'     => $receiptId,
                    'item_code'      => $item['product_id'],
                    'item_name'      => $item['title'],
                    'total_qty'      => $item['quantity'],
                    'item_price'     => $item['price'],
                    'total_amount'   => $item['quantity'] * $item['price'],
                    'total_cost'     => $item['price'],
                    'discount'       => 0,
                    'taxrate'        => 0,
                    'taxamount'      => 0,
                    'discount_value' => 0,
                    'discount_code'  => 0,
                    'actual_price'   => $item['price'],
                    'status'         => 1,
                ]);
            }

            // 3️⃣ Insert into sales_account_general
            DB::table('sales_account_general')->insert([
                'receipt_id'       => $receiptId,
                'receive_amount'   => $payload['total_price'],
                'amount_paid_back' => 0,
                'total_amount'     => $payload['total_price'],
                'balance_amount'   => 0,
                'status'           => 1,
            ]);

            // 4️⃣ Insert into sales_account_subdetails
            DB::table('sales_account_subdetails')->insert([
                'receipt_id'              => $receiptId,
                'discount_amount'         => 0,
                'discount_percentage'     => 0,
                'discount_code'           => null,
                'coupon'                  => null,
                'promo_code'              => null,
                'sales_tax_amount'        => 0,
                'service_tax_amount'      => 0,
                'credit_card_transaction' => null,
                'delivery_charges'        => null,
                'delivery_charges_amount' => null,
                'bank_discount_id'        => null,
                'srb'                     => null,
                'is_sync'                 => 1,
            ]);

            return $receiptId;
        });
    }

    /**
     * Check if customer exists by email or phone; if not, create new
     */
    protected function findOrCreateCustomer(array $customerData, array $shop)
    {
        if (empty($customerData['email']) && empty($customerData['phone'])) {
            // No identifier, use a dummy guest
            $customerData['email'] = 'guest_' . uniqid() . '@shopify.local';
        }

        $existing = DB::table('customers')
            ->where('email', $customerData['email'])
            ->orWhere('mobile', $customerData['phone'])
            ->first();

        if ($existing) {
            return (array) $existing;
        }

        $id = DB::table('customers')->insertGetId([
            'name'        => trim(($customerData['first_name'] ?? '') . ' ' . ($customerData['last_name'] ?? '')) ?: 'Shopify Guest',
            'mobile'      => $customerData['phone'] ?? null,
            'email'       => $customerData['email'] ?? null,
            'address'     => $customerData['address'] ?? null,
            'country_id'  => 1,
            'city_id'     => 1,
            'status_id'   => 1,
            'branch_id'   => 1,
            'company_id'  => 1,
            'website_id'  => $shop['shop_id'] ?? null,
            'online'      => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
            'isVerified'  => 1,
        ]);

        return DB::table('customers')->where('id', $id)->first();
    }

    protected function getWebsiteIdFromShop(array $shop)
    {
        if (empty($shop['domain'])) {
            return null;
        }

        // Normalize Shopify domain (remove protocol if somehow present)
        $domain = preg_replace('#^https?://#', '', trim($shop['domain']));

        // Try direct match without protocol
        $website = DB::table('website_details')
            ->whereRaw("REPLACE(REPLACE(url, 'https://', ''), 'http://', '') LIKE ?", ["%{$domain}%"])
            ->first();

        return $website ? $website->id : null;
    }
}
