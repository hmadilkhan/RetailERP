<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
	protected $table = "sales_receipts";
    protected $guarded = [];
    public $timestamps = false;
	
	public function orderdetails()
    {
        return $this->hasMany(OrderDetails::class,"receipt_id","id");
    }
	
	public function sumorderdetailsqty()
    {
        return $this->hasMany(OrderDetails::class,"receipt_id","id")->sum('total_qty');
    }
	
	public function orderAccount()
    {
        return $this->belongsTo(OrderAccount::class, 'id', 'receipt_id');
    }
	
	public function terminal()
    {
        return $this->belongsTo(Terminal::class, 'terminal_id', 'terminal_id');
    }
	
	public function opening()
    {
        return $this->belongsTo(SalesOpening::class, 'opening_id', 'opening_id');
    }
	
	public function branchrelation()
    {
        return $this->belongsTo(Branch::class, 'branch', 'branch_id');
    }
	
	public function orderAccountSub() 
    {
        return $this->belongsTo(OrderSubAccount::class, 'id', 'receipt_id');
    }
	
	public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }
	
	public function orderStatus() 
    {
        return $this->hasOne(OrderStatus::class, 'order_status_id', 'status');
    }
	
	public function orderAssign() 
    {
        return $this->hasOne(OrderAssign::class, 'receipt_id', 'id');
    }
	public function mode() 
    {
        return $this->belongsTo(OrderMode::class, 'order_mode_id', 'order_mode_id');
    }
	public function payment() 
    {
        return $this->belongsTo(OrderPayment::class, 'payment_id', 'payment_id');
    }
	
	public function getAmountAttribute($value)
	{
		return number_format($this->total_amount,2);
	}
}