<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderStatusLogs extends Model
{
	protected $table = "sales_online_order_status";
    protected $guarded = [];
    public $timestamps = false;
	
	public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'status_id', 'order_status_id');
    }
	
	public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }
}