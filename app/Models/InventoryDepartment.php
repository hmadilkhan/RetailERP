<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InventoryDepartment extends Model
{
    protected $table = "inventory_department";
    protected $guarded = [];
    public $timestamps = false;


    public function products()
    {
        return $this->hasMany(Inventory::class, "department_id", "department_id")->where("status", 1);
    }
}
