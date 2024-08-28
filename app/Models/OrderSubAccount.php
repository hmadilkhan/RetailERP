<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderSubAccount extends Model
{
	protected $table = "sales_account_subdetails";
    protected $guarded = [];
    public $timestamps = false;
	
	
}