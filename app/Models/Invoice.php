<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function lines()
    {
        return $this->hasMany(InvoiceLine::class, 'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id');
    }

    public function adjustments()
    {
        return $this->hasMany(InvoiceAdjustment::class, 'invoice_id');
    }

    public function creditApplications()
    {
        return $this->hasMany(InvoiceCreditApplication::class, 'invoice_id');
    }

    public function paymentVouchers()
    {
        return $this->hasManyThrough(
            PaymentVoucher::class,
            InvoicePayment::class,
            'invoice_id',
            'id',
            'id',
            'payment_voucher_id'
        );
    }

    public function paymentScreenshots()
    {
        return $this->hasManyThrough(
            PaymentVoucherScreenshot::class,
            InvoicePayment::class,
            'invoice_id',
            'payment_voucher_id',
            'id',
            'payment_voucher_id'
        );
    }
}
