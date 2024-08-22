<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorAdvance extends Model
{
	protected $table = "vendor_advance";
    protected $guarded = [];
	
	public function vendor()
    {
        return $this->belongsTo(Vendor::class,"vendor_id","id");
    }
}