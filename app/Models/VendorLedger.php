<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VendorLedger extends Model
{
    protected $table = "vendor_ledger";
    protected $guarded = [];
    public $timestamps = false;
	
	public function vendor()
    {
		return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }
}