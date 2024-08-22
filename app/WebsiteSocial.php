<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebsiteSocial extends Model
{
    protected $table = "website_social_connects";
    protected $guarded = [];
	
	public function website()
    {
        return $this->belongsTo(WebsiteDetail::class, 'id', 'website_id');
    }
}
