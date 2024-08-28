<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryVariation extends Model
{
    protected $guarded = [];
	protected $table = "inventory_variations";
	
	public function category()
	{
		return $this->belongsTo(AddonCategory::class,"addon_id","id");
	}
}
