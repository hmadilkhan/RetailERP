<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebsiteProduct extends Model
{
    protected $table = "website_products";
    protected $guarded = [];

    // public function inventoryGeneral()
    // {
    //     return $this->belongsTo(Inventory::class,"id","inventory_id");
    // }

     public function websiteDetails(){
        return $this->belongsTo(websiteDetails::class, 'id', 'website_id')->where('status',1);
     } 
}
