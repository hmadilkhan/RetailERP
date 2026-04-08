<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PaymentVoucherScreenshot extends Model
{
    protected $table = 'payment_voucher_screenshots';
    protected $guarded = [];

    public function voucher()
    {
        return $this->belongsTo(PaymentVoucher::class, 'payment_voucher_id');
    }

    public function getUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return url(Storage::disk($this->disk ?: 'public')->url($this->file_path));
    }
}
