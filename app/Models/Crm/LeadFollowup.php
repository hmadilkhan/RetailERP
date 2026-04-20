<?php

namespace App\Models\Crm;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadFollowup extends Model
{
    use HasFactory;

    protected $table = 'crm_lead_followups';

    public const TYPES = [
        'Call',
        'WhatsApp',
        'Email',
        'Meeting',
        'Demo',
        'Visit',
    ];

    protected $fillable = [
        'lead_id',
        'followup_date',
        'followup_type',
        'remarks',
        'next_followup_date',
        'followup_result',
        'created_by',
    ];

    protected $casts = [
        'followup_date' => 'date',
        'next_followup_date' => 'date',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
