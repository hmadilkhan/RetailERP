<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadSource extends Model
{
    use HasFactory;

    protected $table = 'crm_lead_sources';

    protected $fillable = [
        'name',
        'slug',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
