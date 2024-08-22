<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryAddon extends Model
{
    protected $guarded = [];
	protected $table = "inventory_addons";
	
	public function category()
	{
		return $this->belongsTo(AddonCategory::class,"addon_id","id");
	}
}
