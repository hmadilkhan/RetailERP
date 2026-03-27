<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhatsAppUser extends Model
{
    protected $table = 'whatsapp_users';
    protected $guarded = [];
    public $timestamps = false;

    public function accesses()
    {
        return $this->hasMany(WhatsAppUserAccess::class, 'whatsapp_user_id', 'id');
    }
}
