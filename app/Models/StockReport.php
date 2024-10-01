<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReport extends Model
{
    protected $guarded = [];
    protected $table = "inventory_stock_report_table";
    protected $primaryKey = "stock_report_id";
    public $timestamps = false;

    public function products(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, "product_id", "id");
    }

    public function productstock(): BelongsTo
    {
        return $this->belongsTo(InventoryStock::class, "foreign_id", "stock_id");
    }


}
