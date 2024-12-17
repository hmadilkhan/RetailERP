<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryPrice extends Model
{
    protected $table = "inventory_price";
	protected $guarded = [];
	public $timestamps = false;
}
