<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyBillingRate extends Model
{
    protected $table = 'company_billing_rates';
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}

