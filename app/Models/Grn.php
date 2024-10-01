<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grn extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = "purchase_rec_gen";
    protected $primaryKey = "rec_id";

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}
