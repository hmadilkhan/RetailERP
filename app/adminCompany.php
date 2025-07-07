<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class adminCompany extends Model
{

	// get company //
    public static function get_company(){
		$result = DB::select('SELECT a.company_id, a.name, b.city_name, a.mobile_contact, a.email, a.address, c.status_name,a.logo FROM company a
		INNER JOIN city b ON b.city_id = a.city_id
		INNER JOIN accessibility_mode c ON c.status_id = a.status_id
		WHERE a.status_id = 1');
		return $result;
	}
    
    // get country //
	public static function getcountry()
    {
    	$country = DB::table('country')->get();
    	return $country;
    }

    // get city //
    public static function getcity()
    {
    	$city = DB::table('city')->get();
    	return $city;
    }

    public static function insert($items)
    {
        $result = DB::table('company')->insertGetId($items);
        return $result;   
    } 

    public static function updateCompany($items,$id)
    {
        if((DB::table('company')->where('company_id',$id)->update($items)))
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public static function getCompanyById($id){
        $result = DB::select('SELECT a.* FROM company a
        INNER JOIN city b ON b.city_id = a.city_id
        INNER JOIN accessibility_mode c ON c.status_id = a.status_id
        WHERE a.status_id = 1 and a.company_id = ?',[$id]);
        return $result;
    }

    public function deleteCompany($id)
    {
        if((DB::table('company')->where('company_id',$id)->delete()))
        {
            return 1;
        }else
        {
            return 0;
        }
          
    }   

}
