<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhatsAppUserAccess extends Model
{
    protected $table = 'whatsapp_user_access';
    protected $guarded = [];
    public $timestamps = false;

    public function whatsappUser()
    {
        return $this->belongsTo(WhatsAppUser::class, 'whatsapp_user_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(company::class, 'company_id', 'company_id');
    }

    public function branch()
    {
        return $this->belongsTo(branch::class, 'branch_id', 'branch_id');
    }
}
