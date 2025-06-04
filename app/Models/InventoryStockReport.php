<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryStockReport extends Model
{
    protected $table = "inventory_stock_report_table";
    protected $primaryKey = "stock_report_id";
	protected $guarded = [];
	public $timestamps = false;
}
