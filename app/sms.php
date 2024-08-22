<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class sms extends Model
{
  

	public function insert($table,$items){
	
        $result = DB::table($table)->insertGetId($items);
       return $result;   
    }

     public function insert_sms_subdetails($data){

       return DB::table("sms_subdetails")->insert($data);
    }

       public function getsmsgeneral()
    {
    	$smsgeneral = DB::table('sms_general_details')
    	->join('accessibility_mode','accessibility_mode.status_id','=','sms_general_details.status_id')
        ->where('sms_general_details.status_id',1)
    	->get();
    	return $smsgeneral;
    }

// for in active numbers
       public function getsmsgeneral_inactive()
    {
      $result = DB::select('SELECT * FROM sms_general_details a INNER JOIN sms_subdetails b ON b.sms_id = a.sms_id INNER JOIN accessibility_mode c ON c.status_id = b.status_id WHERE b.status_id = 2');
        return $result;
    }


       public function getsmssubdetails()
    {
    	$smssubdetails = DB::table('sms_subdetails')->where('status_id',1)->get();
    	return $smssubdetails;
    }

     public function getsmssubdetailsbyid($id)
    {
    	$result = DB::select('SELECT * FROM sms_subdetails a INNER JOIN accessibility_mode b ON b.status_id = a.status_id INNER JOIN sms_general_details c ON c.sms_id = a.sms_id WHERE a.sms_id = ? AND a.status_id = 1',[$id]);
		return $result;
    }

    public function update_subdetails($id,$items){
    $result = DB::table('sms_subdetails')->where('id', $id)->update($items);
    return $result;
}
    public function update_general($id,$items){
    $result = DB::table('sms_general_details')->where('sms_id', $id)->update($items);
    return $result;
}

    public function getcounts($id,$statusid){
  $result = DB::select('SELECT COUNT(id) as counts FROM sms_subdetails WHERE sms_id = ? AND status_id = ?',[$id,$statusid]);
        return $result;
}

    public function update_subdetails_bysmsid($smsid,$items){
    $result = DB::table('sms_subdetails')->where('sms_id', $smsid)->update($items);
    return $result;
}




}