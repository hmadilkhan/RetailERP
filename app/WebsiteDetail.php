<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company;

class WebsiteDetail extends Model
{
    protected $table = "website_details";
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function contacts()
    {
        return $this->hasMany(WebsiteContact::class, 'website_id', 'id');
    }

    public function social()
    {
        return $this->hasMany(WebsiteSocial::class, 'website_id', 'id');
    }

    // public function websiteProducts()
    // {
    //     return $this->hasMany(WebsiteProduct::class,"website_id","id");
    // }
}
