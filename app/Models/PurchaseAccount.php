<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseAccount extends Model
{
	protected $primaryKey = 'account_id';
    protected $table = "purchase_account_details";
    protected $guarded = [];
    public $timestamps = false;
	
	public function purchase()
    {
		return $this->belongsTo(Purchase::class, 'purchase_id', 'purchase_id');
    }
}