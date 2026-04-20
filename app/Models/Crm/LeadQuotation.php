<?php

namespace App\Models\Crm;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadQuotation extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_EXPIRED = 'expired';

    public const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_SENT => 'Sent',
        self::STATUS_ACCEPTED => 'Accepted',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_EXPIRED => 'Expired',
    ];

    protected $table = 'crm_lead_quotations';

    protected $fillable = [
        'lead_id',
        'quotation_no',
        'quotation_date',
        'valid_until',
        'subtotal',
        'discount',
        'tax',
        'total',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $quotation): void {
            if (blank($quotation->quotation_no)) {
                $quotation->quotation_no = static::generateQuotationNo();
            }
        });
    }

    public static function generateQuotationNo(): string
    {
        $prefix = 'QT-' . now()->format('Ym');
        $lastCode = static::query()
            ->where('quotation_no', 'like', $prefix . '-%')
            ->latest('id')
            ->value('quotation_no');

        $nextNumber = $lastCode ? ((int) substr($lastCode, strrpos($lastCode, '-') + 1)) + 1 : 1;

        return sprintf('%s-%04d', $prefix, $nextNumber);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LeadQuotationItem::class, 'quotation_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isLocked(): bool
    {
        return in_array($this->status, [self::STATUS_ACCEPTED, self::STATUS_REJECTED, self::STATUS_EXPIRED], true);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }
}
