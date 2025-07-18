<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRecStockAdjustment extends Model
{
    use HasFactory;

    protected $primaryKey = 'rec_details_id';
    protected $table = "purchase_rec_stock_adjustment";
    protected $guarded = [];
    public $timestamps = false;
}
