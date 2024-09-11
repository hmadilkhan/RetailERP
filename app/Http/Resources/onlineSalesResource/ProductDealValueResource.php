<?php
namespace App\Http\Resources\onlineSalesResource;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\onlineSalesResource\ProductDealAddonHeadResource;

class ProductDealValueResource extends JsonResource
{

    /*
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $receiptId = $this->receipt_id;
        // $receiptDetail_Id = $this->receipt_detail_id;
        // $mode = 'deal-addons';
        return [
                    "name"   => $this->item_name,
                    "addons" => new ProductDealAddonHeadResource(DB::table('addon_categories')->whereIn('id',DB::table('addons')->whereIn('id',DB::table('sales_receipt_details')->where('parent_item_code',$this->receipt_detail_id)->where('mode','deal-addon')->where('receipt_id',$this->receipt_id)->pluck('addon_variation_id'))->pluck('addon_category_id'))->get(),$this->receipt_id,$this->receipt_detail_id),
                                    //     $item['values'] = ProductAddonValuesResource::collection(DB::table('addons')->whereIn('id',DB::table('sales_receipt_details')->where(['receipt_id'=>$this->receipt_id,'parent_item_code'=>$this->receipt_detail_id,'addon_variation_mode'=>'addons'])->pluck('addon_variation_id'))->where('addon_category_id',$value->id)->get());
               ];
    }
}
