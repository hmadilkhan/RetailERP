<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class InventoryStock extends Model
{
	protected $table = "inventory_stock";
    protected $primaryKey = "stock_id";
    protected $guarded = [];
    public $timestamps = false;
	
    public function grn(): BelongsTo
    {
        return $this->belongsTo(Grn::class, "grn_id", "rec_id");
    }
	
}