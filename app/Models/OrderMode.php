<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderMode extends Model
{
	protected $table = "sales_order_mode";
	protected $key = "order_mode_id";
    protected $guarded = [];
    public $timestamps = false;
	
}