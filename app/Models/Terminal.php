<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Terminal extends Model
{
	protected $table = "terminal_details";
	protected $primaryKey = 'terminal_id';
    protected $guarded = [];
    public $timestamps = false;
	
	public function branch()
	{
		return $this->belongsTo(Branch::class,"branch_id","branch_id");
	}
	
	public function orders()
	{
		return $this->hasMany(Order::class,"terminal_id","terminal_id");
	}
}