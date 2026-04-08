<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentVoucher extends Model
{
    protected $table = 'payment_vouchers';
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function paymentMode()
    {
        return $this->belongsTo(OrderPayment::class, 'payment_mode_id', 'payment_id');
    }

    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class, 'payment_voucher_id');
    }

    public function screenshots()
    {
        return $this->hasMany(PaymentVoucherScreenshot::class, 'payment_voucher_id')->orderBy('sort_order')->orderBy('id');
    }
}
