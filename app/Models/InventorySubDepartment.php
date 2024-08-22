<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InventorySubDepartment extends Model
{
	protected $table = "inventory_sub_department";
    protected $guarded = [];
    public $timestamps = false;
	
}