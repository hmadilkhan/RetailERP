<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InventoryUom extends Model
{
	protected $table = "inventory_uom";
    protected $guarded = [];
    public $timestamps = false;
	
}