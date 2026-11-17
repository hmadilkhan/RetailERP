<?php

namespace App\Http\Resources\onlineSalesResource;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
// use App\Models\InventoryPos;
use App\Http\Resources\onlineSalesResource\POSProductVariationResource;
use App\Http\Resources\onlineSalesResource\ProductRetailVariationResource;
use App\Http\Resources\onlineSalesResource\ProductAddonResource;
use App\Http\Resources\onlineSalesResource\ProductDealHeadResource;

class ProductResource extends JsonResource
{
    private static $websiteMode, $websiteId;
    /*
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $deparmentName = $this->department_name != "" ? " - (" . $this->department_name . ")" : "";
        $setItem = [
            "item_code"          => $this->item_code,
            "product_name"       => $this->item_name,
            "department_name"    => $deparmentName,
            "total_qty"          => $this->total_qty,
            "item_price"         => $this->item_price,
            "total_amount"       => $this->total_amount,
            "webcart_amount"     => $this->calcu_amount_webcart,
            "discount_value"     => $this->discount_value,
            "discount_code"      => $this->discount_code,
            "actual_price"       => $this->actual_price,
            "image"              => $this->image,
            "url"                => $this->url,
            "deal"               => new ProductDealHeadResource(DB::table('inventory_deal_general')->whereIn('id', DB::table('sales_receipt_details')->where('parent_item_code', $this->receipt_detail_id)->where('mode', 'deal-product')->pluck('group_id'))->get(), $this->receipt_id, $this->receipt_detail_id),
            "prod_variation"     => $this->getVariation(),
            // "prod_variation"     => new POSProductVariationResource(DB::table('sales_receipt_details')->where(['receipt_id'=>$this->receipt_id,'parent_item_code'=>$this->receipt_detail_id,'mode'=>'variable-product'])->first()),
            "prod_addons"        => new ProductAddonResource(DB::table('addon_categories')->whereIn('id', DB::table('addons')->whereIn('id', DB::table('sales_receipt_details')->where(['receipt_id' => $this->receipt_id, 'parent_item_code' => $this->receipt_detail_id, 'mode' => 'addon'])->pluck('addon_variation_id'))->pluck('addon_category_id'))->get(), $this->receipt_id, $this->receipt_detail_id),

            // ,$this->receipt_id,$this->item_code

            // DB::table('addon_categories')->whereIn('id',DB::table('addons')->whereIn('id',DB::table('sales_receipt_addons')->where(['receipt_id'=>$this->receipt_id,'product_id'=>$this->item_code])->pluck('addon_id'))->groupBy('addon_category_id')->pluck('addon_category_id'))->get()
        ];

        if (!empty($this->laundry_package_id)) {
            $setItem['laundryPackage'] = ['id' => $this->laundry_package_id, 'name' => $this->laundry_package_name, 'price' => $this->laundry_package_price];
        }

        return $setItem;
    }

    //I made custom function that returns collection type
    public static function customCollection($resource, $mode, $websiteId): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        //you can add as many params as you want.
        self::$websiteMode = $mode;
        self::$websiteId = $websiteId;
        return parent::collection($resource);
    }

    public function getVariation()
    {
        if (self::$websiteMode == "shopify") {
            return new POSProductVariationResource($this->variableProductRow());
        }

        // Sabify API (generateOrder) ab har website type ke liye product → 'attribute' → 'variable-product'
        // save karti hai (pehle restaurant ke liye seedha 'variable-product'). Jo rows hain unhi ke hisaab se dikhao.
        $attributes = DB::table('sales_receipt_details')->where(['receipt_id' => $this->receipt_id, 'parent_item_code' => $this->receipt_detail_id, 'mode' => 'attribute'])->get();

        if ($attributes->isNotEmpty() || self::$websiteMode == "boutique") {
            return ProductRetailVariationResource::collection($attributes);
        }

        if (self::$websiteMode == "restaurant") {
            // Purane restaurant orders (attribute row ke baghair)
            return new POSProductVariationResource(DB::table('sales_receipt_details')->where(['receipt_id' => $this->receipt_id, 'parent_item_code' => $this->receipt_detail_id, 'mode' => 'variable-product'])->first());
        }

        return null;
    }

    /**
     * Purane orders: 'variable-product' seedha product row ke neeche.
     * Naye orders (Sabify API generateOrder, website type check band hone ke baad):
     * product → 'attribute' row → 'variable-product' — us ke liye attribute ke neeche bhi dhoondo.
     */
    private function variableProductRow()
    {
        $row = DB::table('sales_receipt_details')
            ->where(['receipt_id' => $this->receipt_id, 'parent_item_code' => $this->receipt_detail_id, 'mode' => 'variable-product'])
            ->first();

        if ($row) {
            return $row;
        }

        return DB::table('sales_receipt_details')
            ->where(['receipt_id' => $this->receipt_id, 'mode' => 'variable-product'])
            ->whereIn('parent_item_code', DB::table('sales_receipt_details')
                ->where(['receipt_id' => $this->receipt_id, 'parent_item_code' => $this->receipt_detail_id, 'mode' => 'attribute'])
                ->pluck('receipt_detail_id'))
            ->first();
    }
}
