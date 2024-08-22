<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\inventory;



class webServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



     // Get Login//
     public function Get_Login(Request $request){
            
          // $password =  Hash::make($request->password);

            $result = DB::table('user_details')->where(["username"=>$request->username])->get();

           if($result->count() > 0 ){
                return $result;
           }else {
                return "0";
           }
         //return $request->username;
     }   

     

     // Get Inventory//
     public function Get_Inventory(Request $request,inventory $inventory){

           if(!empty($request->id) && $request->id > 0){
                return $inventory->get_inventory($request->id);
           }else {
                return "";
           }

     }
    
}
