<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddonCategory extends Model
{
    protected $guarded = [];
	
	public function addons()
	{
		return $this->hasMany("App\Addon","addon_category_id","id");
	}
}
