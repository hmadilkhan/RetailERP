<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
	protected $table = "company";
	protected $primaryKey = 'company_id';
    protected $guarded = [];
    public $timestamps = false;
	
	public function branch()
    {
        return $this->hasMany(Branch::class,"company_id","company_id");
    }
}