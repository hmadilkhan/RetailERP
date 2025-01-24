<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProviderRelation extends Model
{
    use HasFactory;

    protected $table = "user_salesprovider_relation";
    protected $guarded = [];
    public $timestamps = false;


    public function serviceprovider()
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id', 'id');
    }
	
	public function user()
    {
        return $this->belongsTo(Order::class,"user_id","id");
    }
}
