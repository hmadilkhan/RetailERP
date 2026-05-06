<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceCreditApplication extends Model
{
    protected $table = 'invoice_credit_applications';
    protected $guarded = [];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}

