<?php
namespace App\Http\Resources\onlineSalesResource;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\onlineSalesResource\ProductAddonValuesResource;
use Illuminate\Support\Facades\DB;

class ProductAddonResource extends JsonResource
{
// 
    protected $receipt_id,$item_code;

    public function __construct($resource,$receipt_id,$receipt_detail_id) {
        // Ensure we call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;
        //json_encode(DB::table('test')->insert(['data'=>json_encode($resource)]));
        // $explodeValue = explode(',',$data);
        $this->receipt_id = $receipt_id; // $receipt_id param passed
        // $this->item_code  = $item_code;
        $this->receipt_detail_id = $receipt_detail_id;
    }


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

            $items = [];

            foreach (collect($this->resource) as $key => $value) {
                $item = [
                            "id"     => $value->id,
                            "name"   => $value->name,
                            "values" => ProductAddonValuesResource::collection(DB::table('addons')->whereIn('id',DB::table('sales_receipt_details')->where(['receipt_id'=>$this->receipt_id,'parent_item_code'=>$this->receipt_detail_id,'mode'=>'addon'])->pluck('addon_variation_id'))->where('addon_category_id',$value->id)->get()),
                                ];

                array_push($items,$item);
            }
            return collect($items);

        // DB::table('addons')->join('sales_receipt_addons','sales_receipt_addons.addon_id','addons.id')->where(['addons.addon_category_id'=>$value->id,'sales_receipt_addons.receipt_id'=>$this->receipt_id,'sales_receipt_addons.product_id'=>$this->item_code])->select('sales_receipt_addons.name','sales_receipt_addons.price')->get()


  //       return [
		// 	"id"     => $this->id,
  //           "name"   => $this->name,
  //           "values" => new ProductAddonValuesResource(DB::table('addon_categories')->whereIn('id',DB::table('addons')->whereIn('id',DB::table('sales_receipt_addons')->where(['receipt_id'=>$this->receipt_id,'product_id'=>$this->item_code])->pluck('addon_id'))->groupBy('addon_category_id')->pluck('addon_category_id'))->get(),$this->receipt_id),
		// ];
    }
}
