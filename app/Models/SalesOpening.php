<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceProviderLedger;

class SalesOpening extends Model
{
	protected $table = "sales_opening";
	protected $primaryKey = 'opening_id';
    protected $guarded = [];
    public $timestamps = false;
	
	public function branch()
	{
		return $this->belongsTo(Branch::class,"user_id","branch_id");
	}
	
	public function Terminal()
	{
		return $this->belongsTo(Terminal::class,"terminal_id","terminal_id");
	}
}