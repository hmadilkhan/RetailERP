<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosProductPrice extends Model
{
    protected $primaryKey = 'price_id';
    protected $table = "pos_product_price";
    protected $guarded = [];
    public $timestamps = false;


}
