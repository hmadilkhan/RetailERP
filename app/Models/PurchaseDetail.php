<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceProviderLedger;

class PurchaseDetail extends Model
{
	protected $primaryKey = 'p_item_details_idPrimary';
    protected $table = "purchase_item_details";
    protected $guarded = [];
    public $timestamps = false;
	
	
	public function purchase()
    {
		return $this->belongsTo(Purchase::class, 'purchase_id', 'purchase_id');
    }
	
}
