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

	public function websitesProducts()
	{
		return $this->hasMany(WebsiteProduct::class, "inventory_id", "id");
	}

	public function images()
	{
		return $this->hasMany(InventoryImage::class, "item_id", "id");
	}

	public function price()
	{
		return $this->hasOne(InventoryPrice::class, "product_id", "id")->where("status_id", 1);
	}

	public function stock()
	{
		return $this->hasMany(InventoryStock::class, "product_id", "id")->where('status_id', 1);
	}

	public function getTotalStockAttribute()
	{
		return DB::table('inventory_stock')
			->where('product_id', $this->id)
			->where('status_id', 1)
			->sum('balance');
	}
}
