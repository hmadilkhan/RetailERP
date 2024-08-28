<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	protected $table = "user_details";
    protected $guarded = [];
    public $timestamps = false;
	
}