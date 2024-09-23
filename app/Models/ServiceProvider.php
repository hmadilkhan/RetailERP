<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceProviderLedger;

class ServiceProvider extends Model
{

    protected $table = "service_provider_details";
    protected $guarded = [];
    public $timestamps = false;
	
	public function serviceprovideruser()
    {
        return $this->belongsTo(ServiceProviderRelation::class, 'id', 'provider_id');
    }

	public function ledger()
    {
        return $this->hasMany(ServiceProviderLedger::class,"ladger_id","provider_id");
    }
	
	public function scopeBranch($query,$branch)
	{
		return $query->where("branch_id",$branch);
	}
	
	public function scopeActive($query)
	{
		return $query->where("status_id",1);
	}
}
