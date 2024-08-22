<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPurchase extends Model
{
    protected $table = "vendor_purchases";
    protected $guarded = [];
	
	public function vendorpurchases()
    {
        return $this->belongsTo(Purchase::class,"purchase_id","purchase_id");
    }
	
}
