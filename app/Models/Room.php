<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $guarded =[];

    public function floors()
	{
		return $this->belongsTo(Floor::class,"floor_id","floor_id");
	}
}
