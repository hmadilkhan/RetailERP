<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class company extends Model
{
	protected $table = "company";
	protected $guarded = [];
	public $timestamps = false; 
	
	
	public function get_company(){
		$result = DB::select('SELECT a.company_id, a.name, b.city_name, a.mobile_contact, a.email, a.address, c.status_name FROM company a
		INNER JOIN city b ON b.city_id = a.city_id
		INNER JOIN accessibility_mode c ON c.status_id = a.status_id
		WHERE a.status_id = 1');
		return $result;
	}

	  public function getcountry()
    {
    	$country = DB::table('country')->get();
    	return $country;
    }
     public function getcity()
    {
    	$city = DB::table('city')->get();
    	return $city;
    }

    public function insert($items)
    {
        $result = DB::table('company')->insert($items);
        return $result;   
    }

}