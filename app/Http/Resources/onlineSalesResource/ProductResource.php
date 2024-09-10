<?php
namespace App\Http\Resources\onlineSalesResource;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
// use App\Models\InventoryPos;
use App\Http\Resources\onlineSalesResource\POSProductVariationResource;
use App\Http\Resources\onlineSalesResource\ProductAddonResource;
use App\Http\Resources\onlineSalesResource\ProductDealHeadResource;

class ProductResource extends JsonResource
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
            "item_code"          => $this->item_code,
			"product_name"       => $this->item_name,
            "total_qty"          => $this->total_qty,
            "item_price"         => $this->item_price,
            "total_amount"       => $this->total_amount,
            "webcart_amount"     => $this->calcu_amount_webcart,
            "discount_value"     => $this->discount_value,
            "discount_code"      => $this->discount_code,
            "actual_price"       => $this->actual_price,
            "image"              => $this->image,
            "deal"               => new ProductDealHeadResource(DB::table('inventory_deal_general')->whereIn('id',DB::table('sales_receipt_details')->where('parent_item_code',$this->receipt_detail_id)->where('mode','deal-product')->pluck('group_id'))->get(),$this->receipt_id,$this->receipt_detail_id),            
            "prod_variation"     => new POSProductVariationResource(DB::table('sales_receipt_details')->where(['receipt_id'=>$this->receipt_id,'parent_item_code'=>$this->receipt_detail_id,'mode'=>'variable-product'])->first()),
            "prod_addons"        => new ProductAddonResource(DB::table('addon_categories')->whereIn('id',DB::table('addons')->whereIn('id',DB::table('sales_receipt_details')->where(['receipt_id'=>$this->receipt_id,'parent_item_code'=>$this->receipt_detail_id,'mode'=>'addon'])->pluck('addon_variation_id'))->pluck('addon_category_id'))->get(),$this->receipt_id,$this->receipt_detail_id),
            
            // ,$this->receipt_id,$this->item_code
            
            // DB::table('addon_categories')->whereIn('id',DB::table('addons')->whereIn('id',DB::table('sales_receipt_addons')->where(['receipt_id'=>$this->receipt_id,'product_id'=>$this->item_code])->pluck('addon_id'))->groupBy('addon_category_id')->pluck('addon_category_id'))->get()
		];
    }
}
