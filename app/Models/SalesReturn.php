<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use HasFactory;

    protected $table = "sales_return";
    protected $primaryKey = "sr_id";
    protected $guarded = [];
    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class, 'receipt_id', 'id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'item_id', 'id');
    }
}
