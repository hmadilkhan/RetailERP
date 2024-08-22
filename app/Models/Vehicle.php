<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = "vehicles";
    protected $guarded = [];
    public $timestamps = false;
	
	public function getNameAttribute($value)
    {
        return "{$this->model_name} - {$this->number}";
    }
}
