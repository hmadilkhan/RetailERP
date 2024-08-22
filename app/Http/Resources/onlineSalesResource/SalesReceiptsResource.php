<?php
namespace App\Http\Resources\Api\SalesDetailsResource;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\SalesDetailsResource\CustomerResource;
use App\Http\Resources\Api\SalesDetailsResource\ProductResource;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryPos;

class SalesReceiptsResource extends JsonResource
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
        	"order_status"              =>$this->status,
			"url_orderid"               =>$this->url_orderid,
			"sub_total"                 =>number_format($this->total_amount - $this->delivery_charges,0),
			"total_amount"              =>number_format($this->total_amount,0),
			"delivery_type"             =>$this->delivery_type,
			"delivery_area_name"        =>$this->delivery_area_name,
			"delivery_charges"          =>$this->delivery_charges,
			"delivery_instructions"     =>$this->delivery_instructions,
			"dateTime"                  =>date("M ,d Y H:i:s",strtotime($this->date." ".$this->time)),
			"deliveryDate"              =>$this->delivery_date,
			"customer"                  =>new CustomerResource(DB::table('customers')->join('customer_addresses','customer_addresses.customer_id','customers.id')->where(['customers.id'=>$this->customer_id,'customer_addresses.id'=>$this->cust_location_id])->select('customers.name','customers.mobile','customer_addresses.address')->first()),
			"products"                  =>ProductResource::collection(DB::table('inventory_general')->join('sales_receipt_details','sales_receipt_details.item_code','inventory_general.id')->where('sales_receipt_details.receipt_id',$this->id)->select('sales_receipt_details.receipt_id','sales_receipt_details.item_code','sales_receipt_details.item_name','sales_receipt_details.total_qty','sales_receipt_details.total_amount','sales_receipt_details.calcu_amount_webcart')->get()),
			// "sub_department_id" => $this->sub_department_id,
			// "price"             => $this->apiprice->online_price,
			// "description"       => $this->short_description,
			// "image"             => $this->image,
			// "variations"        => ProductVariationResource::collection(InventoryPos::with("price")->where("product_id",$this->id)->get()),
			// "addons"            => ProductAddonResource::collection(DB::table('addon_categories')->whereIn('id',DB::table('inventory_addons')->where('product_id',$this->id)->pluck('addon_id'))->get()),
			// "systemname" => $this->systemname,
			// "sizes" => PizzaSizeResource::collection(DB::table("pizzasizes")->where("crustId",$this->myId)->get()),
		];
    }
}
