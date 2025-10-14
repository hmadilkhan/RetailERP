<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncShopifyController extends Controller
{
    public function sync(Request $request)
    {
        $inventory = Inventory::with("uom", "department", "subdepartment", "variations", "images", "price", "variations.price")->where("id", $request->inventoryId)->first();
        $currency = json_decode(DB::table("settings")->where("company_id", $inventory->company_id)->first()->data)->currency;

        if (!$inventory) {
            return response()->json(['message' => 'Inventory not found'], 404);
        }

        $payload = [
            'product' => [
                'sku'          => $inventory->item_code,
                'title'        => $inventory->product_name,
                'description'  => $inventory->description ?? null,
                'price'        => $inventory->price->retail_price ?? 0,
                'currency'     => $currency,
                'stock'        => number_format($inventory->total_stock) ?? 0,
                'vendor'       => null,
                'product_type' => $inventory->department->department_name ?? null,
                'status'       => $inventory->status == 1 ? 'active' : 'inactive',

                'variants' => collect($inventory->variations ?? [])->map(function ($variant) {
                    return [
                        'sku'    => $variant->item_code ?? null,
                        'option' => $variant->item_name ?? null,
                        'price'  => $variant->price->retail_price ?? 0,
                        'stock'  => $variant->total_stock ?? 0,
                        'inventory_management' => 'shopify',
                        'inventory_policy' => 'deny',
                        'requires_shipping' => true,
                    ];
                })->values()->toArray(),

                'images' => collect($inventory->images ?? [])->map(function ($image) {
                    return asset('storage/images/products/' . $image->image);
                })->values()->toArray(),
            ],
        ];
        return $payload;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.shopify.token'),
            'Content-Type' => 'application/json',
        ])->post('https://sync.sabsoft.com.pk/api/webhooks/shopify/product/create', $payload);



        return response()->json(['message' => 'Sync with Shopify initiated', 'response' => $response->json()], 200);
    }
}
