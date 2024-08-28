<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryVariationProduct extends Model
{
    protected $guarded  = [];
     public $timestamps = false;
	protected $table    = "inventory_variation_products";
	
// 	public function category()
// 	{
// 		return $this->belongsTo(InventoryVariation::class,"inventory_variation_id","id");
// 	}
}
