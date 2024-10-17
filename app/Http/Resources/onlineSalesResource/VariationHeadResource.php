<?php
namespace App\Http\Resources\onlineSalesResource;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\onlineSalesResource\ProductVariationValuesResource;

class VariationHeadResource extends JsonResource
{
	protected $receipt_id;

	public function __construct($resource,$receipt_id) {
        // Ensure we call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;
        $this->receipt_id = $receipt_id; // $receipt_id param passed
    }

	public function toArray($request)
    {
        $items = [];

		foreach (collect($this->resource) as $key => $value) {
			$item = [
						"id"     => $value->id,
						"name"   => $value->name,
						"values" => ProductVariationValuesResource::collection(DB::table('sales_receipt_details')->where(['receipt_id'=>$this->receipt_id,'addon_variation_id'=>$value->id,'mode'=>'variation-product'])->select('item_name as name','item_price as price')->get()),

					];

			array_push($items,$item);
		}
		return collect($items);
    }
}

