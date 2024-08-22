<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLogs extends Model
{
	protected $table = "orders_logs";
    protected $guarded = [];
    public $timestamps = false;
}