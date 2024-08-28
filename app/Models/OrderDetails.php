<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderDetails extends Model
{
	protected $table = "sales_receipt_details";
    protected $guarded = [];
    public $timestamps = false;
	
	public function order()
    {
        return $this->belongsTo(Order::class, 'receipt_id', 'id');
    }
	
	public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'item_code', 'id');
    }
	
	public function getAmountAttribute($value)
	{
		return number_format($this->total_amount,2);
	}
	
	public function itemstatus() 
    {
        return $this->belongsTo(OrderStatus::class, 'status', 'order_status_id');
    }
	
	public function statusLogs() 
    {
        return $this->hasMany(OrderItemStatusLog::class, 'item_id', 'receipt_detail_id');
    }
}