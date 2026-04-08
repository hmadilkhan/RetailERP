<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    protected $table = 'invoice_payments';
    protected $guarded = [];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function paymentMode()
    {
        return $this->belongsTo(OrderPayment::class, 'payment_mode_id', 'payment_id');
    }

    public function voucher()
    {
        return $this->belongsTo(PaymentVoucher::class, 'payment_voucher_id');
    }

    public function screenshots()
    {
        return $this->hasMany(PaymentVoucherScreenshot::class, 'payment_voucher_id', 'payment_voucher_id')->orderBy('sort_order')->orderBy('id');
    }
}
