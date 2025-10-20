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
        $inventory = Inventory::with("uom", "department", "subdepartment", "variations", "variations.price", "images", "price", "variations.price")->where("id", $request->inventoryId)->first();
        $currency = json_decode(DB::table("settings")->where("company_id", $inventory->company_id)->first()->data)->currency;

        if (!$inventory) {
            return response()->json(['message' => 'Inventory not found'], 404);
        }

        $payload = [
            'product' => [
                "id"           => $inventory->id,
                'sku'          => $inventory->item_code,
                'title'        => $inventory->product_name,
                'description'  => $inventory->description ?? null,
                'price'        => (int)($inventory->price->retail_price ?? 0),
                'currency'     => $currency,
                'stock'        => (int)($inventory->total_stock ?? 0),
                'vendor'       => null,
                'product_type' => $inventory->department->department_name ?? null,
                'status'       => $inventory->status == 1 ? 'active' : 'inactive',

                'variants' => collect($inventory->variations ?? [])->map(function ($variant) use ($inventory) {

                    $imageUrl = isset($variant->image)
                        ? asset('storage/images/products/' . $variant->image)
                        : null;
                    return [
                        'id'    => $variant->pos_item_id ?? null,
                        'sku'    => $variant->item_code ?? null,
                        'option' => $variant->item_name ?? null,
                        'price'  => $variant->price->retail_price ?? 0,
                        // 'stock'  => $variant->total_stock ?? 0,
                        'inventory_quantity'  => (int)($inventory->total_stock ?? 0), // (int)($variant->total_stock ?? 0),
                        'inventory_management' => 'shopify',
                        'inventory_policy' => 'deny',
                        'requires_shipping' => true,
                        'image' => $imageUrl ? ['src' => $imageUrl] : null,
                    ];
                })->values()->toArray(),

                // 'images' => collect($inventory->images ?? [])->map(function ($image) {
                //     return asset('storage/images/products/' . $image->image);
                // })->values()->toArray(),



                // 'images' => (function () use ($inventory) {
                //     $images = collect($inventory->images ?? [])->map(function ($image) {
                //         return asset('storage/images/products/' . $image->image);
                //     })->values();

                //     // If no images found, but main image exists, add it
                //     if ($images->isEmpty() && !empty($inventory->image)) {
                //         $images->push(asset('storage/images/products/' . $inventory->image));
                //     }

                //     return $images->toArray();
                // })(),

                'images' => (function () use ($inventory) {
                    $images = collect();

                    // Product-level images
                    if (!empty($inventory->images)) {
                        $images = collect($inventory->images)->map(function ($image) {
                            return asset('storage/images/products/' . $image->image);
                        });
                    }

                    // Add main product image if empty
                    if ($images->isEmpty() && !empty($inventory->image)) {
                        $images->push(asset('storage/images/products/' . $inventory->image));
                    }

                    // Add variant images too
                    if (!empty($inventory->variations)) {
                        foreach ($inventory->variations as $variant) {
                            if (!empty($variant->image)) {
                                $variantImage = asset('storage/images/products/' . $variant->image);
                                if (!$images->contains($variantImage)) {
                                    $images->push($variantImage);
                                }
                            }
                        }
                    }

                    return $images->values()->toArray();
                })(),
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.shopify.token'),
            'Content-Type' => 'application/json',
        ])->post('https://sync.sabsoft.com.pk/api/erp/products/sync', $payload);

        $responseData = $response->json();

        if ($responseData['success']) {
            return response()->json(['message' => 'Product synced successfully', 'data' => $responseData, "payload" => $payload], 200);
        } else {
            return response()->json(['message' => 'Sync failed', 'errors' => $responseData['errors'] ?? $responseData, "payload" => $payload], 400);
        }
    }
}
