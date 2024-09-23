<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProviderRelation extends Model
{
    use HasFactory;

    protected $table = "user_salesprovider_relation";
    protected $guarded = [];
    public $timestamps = false;
}
