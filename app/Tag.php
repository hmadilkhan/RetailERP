<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Session;

class Tag extends Model
{
    protected $guarded = [];
	
    public static function getTags(){
        return DB::table('tags')->where("company_id",session("company_id"))->where('status',1)->get();
    }
    
    public function filterTag($id){
      
      $filter = ['company_id'=>session("company_id"),'status'=>1];   
          
        if($id != 0){
            $filter['id']=$id;
        }
        
        
       return DB::table('tags')->where($filter)->first(); 
    } 
    
    public static function customQuery_allColumFetch($condition,$mode,$count){
      $filter = 'company_id = "'.session("company_id").'" and status = 1 and '.$condition;   
       
       if($mode == 1){
           return DB::table('tags')->whereRaw($filter)->first();
       }else{
           $record = DB::table('tags')->whereRaw($filter)->get();
           return ($count) ? count($record) : $record; 
       }
    }    
}


