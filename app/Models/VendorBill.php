<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Phase 2.1: vendor bill / debit note / credit note — App\Services\Purchases\VendorBillService. */
class VendorBill extends Model
{
    const TYPES = ['bill' => 'Vendor Bill', 'debit_note' => 'Debit Note', 'credit_note' => 'Credit Note'];
    const PREFIX = ['bill' => 'VB', 'debit_note' => 'DN', 'credit_note' => 'CN'];

    protected $guarded = [];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'posted_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function lines()
    {
        return $this->hasMany(VendorBillLine::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function notes()
    {
        return $this->hasMany(self::class, 'against_bill_id');
    }

    public function againstBill()
    {
        return $this->belongsTo(self::class, 'against_bill_id');
    }

    /** vendor ko abhi kitna dena hai (sirf bill) */
    public function getOpenAmountAttribute(): float
    {
        return $this->type === 'bill' && $this->status === 'posted' ? round((float) $this->total - (float) $this->paid, 2) : 0.0;
    }
}
