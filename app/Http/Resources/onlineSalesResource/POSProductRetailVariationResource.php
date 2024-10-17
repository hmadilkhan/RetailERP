<?php
namespace App\Http\Resources\onlineSalesResource;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\onlineSalesResource\ProductVariationResource;

class POSProductRetailVariationResource extends JsonResource
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
			"variate_name"    => $this->item_name,
            "total_qty"       => $this->total_qty,
            "total_amount"    => $this->total_amount,
			"variation"  => new VariationHeadResource(DB::table("addon_categories")
                                                         ->whereIn("id",DB::table("sales_receipt_details")
                                                                           ->where([
                                                                              "parent_item_code"=>$this->receipt_detail_id,
                                                                              "mode"=>'variation-attribute'
                                                                              ])
                                                                              ->pluck("item_code")
                                                                  )
                                                                  ->get(),$this->receipt_id)
		];
    }
}
