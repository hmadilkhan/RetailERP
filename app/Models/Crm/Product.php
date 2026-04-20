<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'crm_products';

    protected $fillable = [
        'product_type_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
