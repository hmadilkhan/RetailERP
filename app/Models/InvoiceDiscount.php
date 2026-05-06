<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDiscount extends Model
{
    protected $table = 'invoice_discounts';
    protected $guarded = [];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}

