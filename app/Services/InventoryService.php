<?php

namespace App\Services;

use App\Models\InventoryPrice;
use Illuminate\Http\JsonResponse;

class InventoryService 
{
    public function getPriceFromProduct($productId) : JsonResponse 
    {
        if($productId != ""){
            $price = InventoryPrice::where("product_id",$productId)->where("status_id",1)->first();
            return response()->json(["status" => 200,"price" => $price]);
        }
        return response()->json(["status" => 500,"price" => []]);
    }
}