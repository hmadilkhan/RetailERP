<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceSetup extends Model
{
    protected $table = 'invoice_setups';
    protected $primaryKey = 'id';
    protected $guarded = [];

    protected $casts = [
        'is_auto_invoice' => 'boolean',
        'monthly_charges_amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function billingRates()
    {
        return $this->hasMany(InvoiceSetupBillingRate::class, 'invoice_setup_id', 'id');
    }
}
