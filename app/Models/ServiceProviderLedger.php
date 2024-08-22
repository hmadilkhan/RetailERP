<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ServiceProviderLedger extends Model
{
	protected $table = "service_provider_ladger";
    protected $guarded = [];
    public $timestamps = false;
	
	public function serviceprovider()
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id', 'id');
    }
	
	public function serviceprovidersorders()
    {
        return $this->belongsTo(Order::class,"receipt_id","id");
    }
	
}