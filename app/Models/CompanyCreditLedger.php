<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyCreditLedger extends Model
{
    protected $table = 'company_credit_ledger';
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}

