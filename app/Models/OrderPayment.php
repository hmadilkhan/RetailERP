<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderPayment extends Model
{
	protected $table = "sales_payment";
	protected $primaryKey = "payment_id";
    protected $guarded = [];
    public $timestamps = false;
	
}