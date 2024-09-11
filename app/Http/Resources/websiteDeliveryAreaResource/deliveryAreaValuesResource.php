<?php
namespace App\Http\Resources\websiteDeliveryAreaResource;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class deliveryAreaValuesResource extends JsonResource
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
        	"id"        =>$this->id,
        	"name"      =>$this->name,
        	"longitude" =>$this->longitude,
        	"latitude"  =>$this->latitude
		];
    }
}
