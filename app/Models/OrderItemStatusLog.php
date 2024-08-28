<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderItemStatusLog extends Model
{
	protected $table = "sales_online_item_status";
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
	
	public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}