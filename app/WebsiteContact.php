<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebsiteContact extends Model
{
    protected $table = "website_contacts";
    protected $guarded = [];
	
	public function website()
    {
        return $this->belongsTo(WebsiteDetail::class, 'id', 'website_id');
    }
}
