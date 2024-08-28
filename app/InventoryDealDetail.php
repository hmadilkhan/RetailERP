<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryDealDetail extends Model
{
    protected $guarded = [];
    
    public $timestamps = false;
	
// 	public function addons()
// 	{
// 		return $this->hasMany("App\Addon","addon_category_id","id");
// 	}
}
