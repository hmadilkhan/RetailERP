<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorBillLine extends Model
{
    protected $guarded = [];

    public function bill()
    {
        return $this->belongsTo(VendorBill::class, 'vendor_bill_id');
    }
}
