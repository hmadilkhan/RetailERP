<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryDealGeneral extends Model
{
    protected $table = "inventory_deal_general";
    protected $guarded = [];
    
	public function getDeal_details()
	{
	  return $this->hasMany("App\InventoryDealDetail","inventory_general_id","id");
	}
	
	public function inventory_general(){
	  return $this->belongsTo('App\Models\Inventory',"id","inventory_deal_id");
	}
	
	public function inventoryGroup(){
	  return $this->belongsTo('App\AddonCategory',"group_id","id");
	}	
}
