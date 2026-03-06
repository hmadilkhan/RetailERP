<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceSetupBillingRate extends Model
{
    protected $table = 'invoice_setup_billing_rates';
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'rate' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function invoiceSetup()
    {
        return $this->belongsTo(InvoiceSetup::class, 'invoice_setup_id', 'id');
    }
}
