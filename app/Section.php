<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Session;

class Section extends Model
{
    protected $guarded = [];

    public static function getSection(){
        return DB::table('sections')->where('company_id',session('company_id'))->get();
    }

    public static function customQuery_allColumFetch($condition,$mode,$count){
        $filter = 'company_id = "'.session("company_id").'" and '.$condition;   
         
         if($mode == 1){
             return DB::table('sections')->whereRaw($filter)->first();
         }else{
             $record = DB::table('sections')->whereRaw($filter)->get();
             return ($count) ? count($record) : $record; 
         }
      }   

}
