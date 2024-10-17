<?php
namespace App\Http\Resources\onlineSalesResource;

use App\Http\Resources\Api\onlineSalesResource\POSProductRetailVariationResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;


class ProductRetailVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    // protected $receiptId,$parent_item_code;

    // public function __construct($resource,$receiptId,$parent_item_code) {
    //     // Ensure we call the parent constructor
    //     parent::__construct($resource);
    //     $this->resource = $resource;

    //     $this->receiptId = $receiptId;
    //     $this->parent_item_code = $parent_item_code;
    // }


    public function toArray($request)
    {

    //   $exportArray = [];
    //   foreach (collect($this->resource) as $key => $value) {
        return [
			"id"            => $this->item_code,
			"name"          => $this->item_name,
            "values"        => POSProductRetailVariationResource::collection(DB::table('sales_receipt_details')->where(['receipt_id'=>$this->receipt_id,'parent_item_code'=>$this->receipt_detail_id,'mode'=>'variable-product'])->get())
		];

// 		array_push($exportArray,$data);
//       }

//       return $exportArray;
    }

}
