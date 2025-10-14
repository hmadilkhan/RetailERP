<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryImage extends Model
{
    protected $table = "inventory_images";
    protected $primaryKey = "id";
    protected $guarded = [];
    public $timestamps = false;


    public function inventory()
    {
        return $this->belongsTo(Inventory::class, "item_id", "id");
    }
}
