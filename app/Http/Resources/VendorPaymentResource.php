<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorPaymentResource extends JsonResource
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
			"id" => $this->vendorpurchases->purchase_id,
			"date" => date("d M Y",strtotime($this->vendorpurchases->date)),
			"time" => date("H:i",strtotime($this->vendorpurchases->time)),
			"po" => $this->vendorpurchases->po_no,
			"vendor" => $this->vendorpurchases->vendor["vendor_name"],
			"address" => $this->vendorpurchases->vendor["address"],
			"due_date" => $this->vendorpurchases->payment_date,
			"amount" => number_format($this->vendorpurchases->purchaseAccount["total_amount"],2),
			"balance" => number_format($this->vendorpurchases->purchaseAccount["balance_amount"],2),
		];
    }
}
