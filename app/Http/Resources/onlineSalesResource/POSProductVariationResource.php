<?php
namespace App\Http\Resources\onlineSalesResource;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\onlineSalesResource\ProductVariationResource;

class POSProductVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
			"variable_name"    => $this->item_name,
            "total_qty"        => $this->total_qty,
            "total_amount"     => $this->total_amount,
            "discount_value"     => $this->discount_value,
            "discount_code"      => $this->discount_code,
            "actual_price"       => $this->actual_price,            
            "variation"       => new ProductVariationResource(DB::table("addon_categories")->whereIn("id",DB::table("sales_receipt_details")->where(["parent_item_code"=>$this->receipt_detail_id,"mode"=>'variation-product'])->pluck("addon_variation_id"))->get(),$this->receipt_id,$this->receipt_detail_id),
            // "prod_addons"    => new ProductAddonResource(DB::table('addon_categories')->whereIn('id',DB::table('addons')->whereIn('id',DB::table('sales_receipt_addons')->where(['receipt_id'=>$this->receipt_id,'product_id'=>$this->item_code])->pluck('addon_id'))->groupBy('addon_category_id')->pluck('addon_category_id'))->get(),$this->receipt_id,$this->item_code)
		];
		
// 		$this->item_code;
// 		DB::table('variations')->whereIn('id',DB::table('variations')->whereIn('id',DB::table('sales_receipt_variations')->where(['receipt_id'=>$this->receipt_id,'product_id'=>$this->item_code])->pluck('variation_id'))->pluck('parent'))->get()
    }
}
