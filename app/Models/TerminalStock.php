<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TerminalStock extends Model
{
	protected $table = "terminal_stock_details";
    protected $guarded = [];
    public $timestamps = false;
	
	public function inventory()
	{
		return $this->belongsTo(Inventory::class,"product_id","id");
	}
	
}