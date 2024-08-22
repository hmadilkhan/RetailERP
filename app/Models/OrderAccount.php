<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderAccount extends Model
{
	protected $table = "sales_account_general";
    protected $guarded = [];
    public $timestamps = false;
}