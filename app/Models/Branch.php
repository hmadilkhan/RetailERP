<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Branch extends Model
{
	protected $table = "branch";
	protected $primaryKey = 'branch_id';
    protected $guarded = [];
    public $timestamps = false;
	
	public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'city_id');
    }

    public function terminals()
    {
        return $this->hasMany(Terminal::class, 'branch_id', 'branch_id');
    }

}