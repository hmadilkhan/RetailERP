<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    protected $table = "customers";
    protected $guarded = [];
    public $timestamps = false;
	
	public function orders()
    {
        return $this->belongsTo(Order::class, 'id', 'customer_id');
    }
	
}
