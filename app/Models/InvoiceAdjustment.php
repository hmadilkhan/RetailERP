<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceAdjustment extends Model
{
    protected $table = 'invoice_adjustments';
    protected $guarded = [];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}

