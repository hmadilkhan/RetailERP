<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = "vendors";
    protected $guarded = [];
    public $timestamps = false;
	
	public function vendorpurchase()
    {
        return $this->hasMany(Purchase::class,"vendor_id","id");
    }
	
	public function vendoradvance()
    {
        return $this->hasMany(VendorAdvance::class,"vendor_id","id");
    }
	
	public function scopeCompany($query)
	{
		return $query->where("user_id",session("company_id"));
	}
	
	
	
}
