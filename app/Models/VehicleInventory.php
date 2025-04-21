<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleInventory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class,"product_id","id");
    }
}
