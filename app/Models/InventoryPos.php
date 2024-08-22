<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InventoryPos extends Model
{
	protected $table = "pos_products_gen_details";
	protected $primaryKey = 'pos_item_id';
    protected $guarded = [];
    public $timestamps = false;
	
	public function uom()
	{
		return $this->belongsTo(InventoryUom::class,"uom_id","uom_id");
	}
	
	public function department()
	{
		return $this->belongsTo(InventoryDepartment::class,"department_id","department_id");
	}
	
	public function subdepartment()
	{
		return $this->belongsTo(InventorySubDepartment::class,"sub_department_id","sub_department_id");
	}
	
	
}