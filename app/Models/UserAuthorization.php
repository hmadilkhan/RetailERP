<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAuthorization extends Model
{
    use HasFactory;

    protected $table = "user_authorization";
    protected $primaryKey = "authorization_id";
    protected $guarded = [];
    public $timestamps = false;

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }
}
