<?php
namespace App\Http\Resources\onlineSalesResource;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\onlineSalesResource\CustomerResource;
use App\Http\Resources\onlineSalesResource\ProductResource;
use App\Http\Resources\onlineSalesResource\ServiceProviderOrderResource;
use Illuminate\Support\Facades\DB;
// use App\Models\InventoryPos;

class salesReceiptResource extends JsonResource
{
    /*
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
        	"id"                        =>$this->id,
        	"receipt_no"                =>$this->receipt_no,
        	"order_statusId"            =>$this->status,
        	"order_estimate_time"       =>$this->order_estimate_time,
			"company_name"              =>str_replace(' ','',strtolower($this->company_name)),
        	"order_status"              =>$this->status_name,
        	"website_name"              =>$this->website_name,
			"url_orderid"               =>$this->url_orderid,
			"branch"                    =>$this->branch_name,
			"sub_total"                 =>number_format($this->actual_amount,0),
			"total_amount"              =>number_format($this->total_amount,0),
			"discount_amount"           =>number_format($this->discount_amount,0),
			"discount_percentage"       =>$this->discount_percentage,
			"delivery_type"             =>$this->delivery_type,
			"delivery_area_name"        =>$this->delivery_area_name,
			"delivery_charges"          =>$this->delivery_charges,
			"delivery_instructions"     =>$this->delivery_instructions,
			"dateTime"                  =>date('M d, Y  h:i a', strtotime($this->date." ".$this->time)), //date("M ,d Y H:i:s",strtotime($this->date." ".$this->time)),
			"deliveryDate"              =>$this->delivery_date,
			"Rider"                     =>ServiceProviderOrderResource::collection(DB::table('service_provider_orders')
			                                                                          ->join('service_provider_details','service_provider_details.id','service_provider_orders.service_provider_id')
			                                                                          ->select('service_provider_details.provider_name','service_provider_details.contact')
			                                                                          ->where('service_provider_orders.receipt_id',$this->id)
			                                                                          ->get()),
			"customer"                  =>new CustomerResource(DB::table('customers')->join('customer_addresses','customer_addresses.customer_id','customers.id')->where(['customers.id'=>$this->customer_id,'customer_addresses.id'=>$this->cust_location_id])->select('customers.name','customers.mobile','customer_addresses.address','customer_addresses.landmark')->first()),
			"products"                  =>ProductResource::customCollection(DB::table('inventory_general')
			                                                              ->join('sales_receipt_details','sales_receipt_details.item_code','inventory_general.id')
																		  ->where('sales_receipt_details.receipt_id',$this->id)
																		  ->select('sales_receipt_details.receipt_id','sales_receipt_details.item_code',
																		  'sales_receipt_details.item_name','sales_receipt_details.total_qty',
																		  'sales_receipt_details.item_price','sales_receipt_details.total_amount',
																		  'sales_receipt_details.calcu_amount_webcart','sales_receipt_details.receipt_detail_id',
																		  'sales_receipt_details.discount_value','sales_receipt_details.discount_code','sales_receipt_details.group_id',
																		  'sales_receipt_details.actual_price','inventory_general.image','inventory_general.url')
																		  ->get(),$this->website_type,$this->website_id),


			// ->where('sales_receipt_details.mode','inventory-general')
// 			DB::table('inventory_general')->join('sales_receipt_details','sales_receipt_details.item_code','inventory_general.id')->where('sales_receipt_details.receipt_id',$this->id)->select('sales_receipt_details.receipt_id','sales_receipt_details.item_code','sales_receipt_details.item_name','sales_receipt_details.total_qty','sales_receipt_details.item_price','sales_receipt_details.total_amount','sales_receipt_details.calcu_amount_webcart','sales_receipt_details.receipt_detail_id','sales_receipt_details.discount_value','sales_receipt_details.discount_code','sales_receipt_details.actual_price','inventory_general.image')->get()

			/*DB::table('inventory_general')->join('sales_receipt_details','sales_receipt_details.item_code','inventory_general.id')->where('sales_receipt_details.receipt_id',$this->id)->where('sales_receipt_details.mode','inventory-general')->select('sales_receipt_details.receipt_id','sales_receipt_details.item_code','sales_receipt_details.item_name','sales_receipt_details.total_qty','sales_receipt_details.total_amount','sales_receipt_details.calcu_amount_webcart','sales_receipt_details.receipt_detail_id','sales_receipt_details.group_id','sales_receipt_details.actual_price')->get()*/
		];
    }
}
