<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class AccountingSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'enabled' => 'boolean',
        'enabled_at' => 'datetime',
        'posting_start_date' => 'date',
        'last_posted_at' => 'datetime',
    ];

    public static function isEnabled($companyId): bool
    {
        // sidebar har page pe ye poochta hai — table abhi na bana ho (migration pending) to site na gire
        try {
            return (bool) static::where('company_id', $companyId)->value('enabled');
        } catch (QueryException $e) {
            return false;
        }
    }
}
