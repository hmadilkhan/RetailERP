<?php
namespace App\Http\Resources\websiteDeliveryAreaResource;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\websiteDeliveryAreaResource\deliveryAreaValuesResource;
use Illuminate\Support\Facades\DB;

class deliveryAreasResource extends JsonResource
{
    /***
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
        	"website_id"      =>$this->website_id,
        	"website_name"    =>$this->website_name,
        	"branch_id"       =>$this->branch_id,
			"branch_name"     =>$this->branch_name,
			"city"            =>$this->city,
			"estimate_time"   =>$this->estimate_time,
			"charge"          =>$this->charge,
			"min_order"       =>$this->min_order,
			"areaLists"       =>deliveryAreaValuesResource(DB::table('website_delivery_areas')->where('branch_id',$this->branch_id)->select('id','name','longitude','latitude')->get()),

		];
    }
}
