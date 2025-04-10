<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderStatus extends Model
{
	protected $table = "sales_order_status";
    protected $guarded = [];
    public $timestamps = false;
	
	public function orders()
    {
        return $this->hasMany(Order::class,"status","order_status_id");
    }
}