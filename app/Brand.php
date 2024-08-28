<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Session;

class Brand extends Model
{
    protected $guarded = [];
	
    public function getBrand(){
        return DB::table('brands')->where("company_id",session("company_id"))->where('status',1)->get();
    }
    
    public function singleRow_byFilter($array){
      
      $filter = ['company_id'=>session("company_id"),'status'=>1];   
          
        if(isset($array['id'])){
            $filter['id']=$id;
        }
        
        
       return DB::table('brands')->where($filter)->first(); 
    }
    
    
    public static function customQuery_allColumFetch($condition,$mode,$count){
      $filter = 'company_id = "'.session("company_id").'" and status = 1 and '.$condition;   
       
       if($mode == 1){
           return DB::table('brands')->whereRaw($filter)->first();
       }else{
           $record = DB::table('brands')->whereRaw($filter)->get();
           return ($count) ? count($record) : $record; 
       }
    }
    
}
