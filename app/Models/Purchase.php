<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceProviderLedger;

class Purchase extends Model
{
	protected $primaryKey = 'purchase_id';
    protected $table = "purchase_general_details";
    protected $guarded = [];
    public $timestamps = false;
	
	public function purchaseItems()
    {
		return $this->belongsTo(PurchaseDetail::class, 'purchase_id', 'purchase_id');
    }
	
	public function purchaseAccount()
    {
		return $this->belongsTo(PurchaseAccount::class, 'purchase_id', 'purchase_id');
    }
	
	public function vendor()
    {
		return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }
	
	public function scopeBranch($query,$branch)
	{
		return $query->where("branch_id",$branch);
	}
	
	public function scopeActive($query,$status)
	{
		return $query->where("status_id",$status);
	}
}
