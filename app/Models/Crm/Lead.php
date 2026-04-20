<?php

namespace App\Models\Crm;

use App\Models\Customer;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'crm_leads';

    protected $fillable = [
        'lead_code',
        'contact_person_name',
        'company_name',
        'contact_number',
        'alternate_number',
        'whatsapp_number',
        'email',
        'country',
        'city',
        'address',
        'website',
        'lead_source_id',
        'campaign_name',
        'referral_person_name',
        'product_type_id',
        'product_id',
        'inquiry_type',
        'business_type',
        'required_quantity',
        'branch_count',
        'existing_system',
        'competitor_name',
        'budget_range',
        'expected_go_live_date',
        'requirement_summary',
        'status_id',
        'priority',
        'temperature',
        'assigned_to',
        'lead_score',
        'expected_deal_value',
        'probability_percent',
        'last_contact_date',
        'next_followup_date',
        'preferred_contact_method',
        'lost_reason',
        'is_converted',
        'converted_customer_id',
        'converted_at',
        'converted_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'expected_go_live_date' => 'date',
        'last_contact_date' => 'date',
        'next_followup_date' => 'date',
        'converted_at' => 'datetime',
        'expected_deal_value' => 'decimal:2',
        'is_converted' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $lead): void {
            if (blank($lead->lead_code)) {
                $lead->lead_code = static::generateLeadCode();
            }
        });
    }

    public static function generateLeadCode(): string
    {
        $prefix = 'LD-' . now()->format('ymd');
        $lastCode = static::query()
            ->where('lead_code', 'like', $prefix . '-%')
            ->latest('id')
            ->value('lead_code');

        $nextNumber = $lastCode ? ((int) substr($lastCode, strrpos($lastCode, '-') + 1)) + 1 : 1;

        return sprintf('%s-%04d', $prefix, $nextNumber);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term): void {
            $builder
                ->where('contact_person_name', 'like', '%' . $term . '%')
                ->orWhere('company_name', 'like', '%' . $term . '%')
                ->orWhere('contact_number', 'like', '%' . $term . '%')
                ->orWhere('email', 'like', '%' . $term . '%')
                ->orWhere('lead_code', 'like', '%' . $term . '%');
        });
    }

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class);
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(LeadStatus::class, 'status_id');
    }

    public function followups(): HasMany
    {
        return $this->hasMany(LeadFollowup::class)->latest('followup_date')->latest('id');
    }

    public function latestFollowup(): HasOne
    {
        return $this->hasOne(LeadFollowup::class)->latestOfMany('followup_date');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest('created_at')->latest('id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(LeadAttachment::class)->latest('created_at')->latest('id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(LeadQuotation::class)->latest('quotation_date')->latest('id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function convertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'converted_by');
    }

    public function convertedCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'converted_customer_id');
    }

    public function isClosed(): bool
    {
        return in_array(optional($this->status)->slug, ['won', 'lost'], true);
    }

    public function isOverdue(): bool
    {
        return !$this->isClosed()
            && $this->next_followup_date !== null
            && $this->next_followup_date->isPast();
    }
}
