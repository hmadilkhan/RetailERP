<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\User;

class OrderAssign extends Model
{
	protected $table = "sales_receipts_assign";
    protected $guarded = ["id"];

	
	public function order()
    {
        return $this->belongsTo(Order::class, 'receipt_id', 'id');
    }
	
	public function vehicles()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }
	
	public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id');
    }
	
	public function loader()
    {
        return $this->belongsTo(User::class, 'loader_id', 'id');
    }
	
	public function checker()
    {
        return $this->belongsTo(User::class, 'checker_id', 'id');
    }
	
	
}