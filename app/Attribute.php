<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Session;

class Attribute extends Model
{
    protected $guarded = [];
	
	public static function get_attributes(){
	    return DB::table('attributes')->where('company_id',session('company_id'))->where('status',1)->get();
	}
}
