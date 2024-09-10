<?php
namespace App\Http\Resources\onlineSalesResource;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class CustomerResource extends JsonResource
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
			"Name"   => $this->name,
			"Mobile" => $this->mobile,
            "LandMark" => $this->landmark,
			"Address" => $this->address
		];
    }
}
