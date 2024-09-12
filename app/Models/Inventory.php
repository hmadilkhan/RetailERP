<?php

namespace App\Models;

use App\InventoryAddon;
use App\InventoryDealGeneral;
use App\WebsiteProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Inventory extends Model
{
	protected $table = "inventory_general";
	protected $guarded = [];
	public $timestamps = false;

	public function uom()
	{
		return $this->belongsTo(InventoryUom::class, "uom_id", "uom_id");
	}

	public function department()
	{
		return $this->belongsTo(InventoryDepartment::class, "department_id", "department_id");
	}

	public function subdepartment()
	{
		return $this->belongsTo(InventorySubDepartment::class, "sub_department_id", "sub_department_id");
	}

	public function addons()
	{
		return $this->hasMany(InventoryAddon::class, "product_id", "id");
	}

	public function variations()
	{
		return $this->hasMany(InventoryPos::class, "product_id", "id");
	}

	public function deals()
	{
		return $this->hasMany(InventoryDealGeneral::class, "inventory_deal_id", "id")->where("status", 1);
	}

	// public function websites()
	// {
	// 	return $this->hasMany(WebsiteProduct::class, "inventory_id", "id");
	// }
}
