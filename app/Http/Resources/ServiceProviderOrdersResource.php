<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceProviderOrdersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
			"receipt_id" => $this->serviceprovidersorders->id,
			"receipt_no" => $this->serviceprovidersorders->receipt_no,
			"total_amount" => $this->serviceprovidersorders->total_amount,
			"total_item_qty" => $this->serviceprovidersorders->total_item_qty,
			"customer" => $this->serviceprovidersorders->customer->name,
			"service_provider_id" => $this->serviceprovider->id,
			"service_provider_name" => $this->serviceprovider->provider_name,
			"date" => $this->serviceprovidersorders->date,
			"time" => $this->serviceprovidersorders->time,
		];
    }
}
