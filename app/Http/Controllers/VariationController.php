<?php

namespace App\Http\Controllers;

use App\Models\Variation;
use Illuminate\Http\Request;
use Image,Auth,Session;
use Illuminate\Support\Facades\DB;
use App\Helpers\custom_helper;

class VariationController extends Controller
{
 
    public function __construct(){

       $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	  $variations   = Variation::where(['parent'=>0,'company_id'=>Auth::user()->company_id,'status'=>1])->get();
    	  $variat_value = Variation::whereNotNull('parent')->where(['company_id'=>Auth::user()->company_id,'status'=>1])->get();
      return view('variations.index',compact('variations','variat_value'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;

            if(Variation::where(['name'=>$request->variat_name,'company_id'=>$companyId])->count() > 0){
                return response()->json('This '.$request->variat_name.' variation name already taken',500);
            } 



            $createVariatParent = Variation::create([
                                                  'company_id'         => $companyId,
                                                  'name'               => $request->variat_name,
                                                  'show_website_name'  => $request->show_website_name,
                                                  'required_status'    => $request->required_status,
                                                  'status'             => 1,
                                                  'created_at'         => date("Y-m-d H:i:s")

                                                ]);

                 
             if($createVariatParent->save()){
                
                  $sub_values   = $request->variat_values;
                  $sub_values = explode(",",$sub_values);

                for($i=0;$i < count($sub_values);$i++){
                     Variation::insert([
                          
                          'company_id' => $companyId,
                          'name'       => $sub_values[$i],
                          'parent'     => $createVariatParent->id,
                          'status'     => 1,
                          'created_at' => date("Y-m-d H:i:s")

                        ]);
                }
                
                return response()->json('Success!',200);
             }else{
                return response()->json('Error! Record not create to server',500);
             }				
    }

    public function edit(){


    }

   

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if(Variation::where(['id'=>$id,'company_id'=>Auth::user()->company_id])->update(['status'=>0]) && Variation::where(['parent'=>$id,'company_id'=>Auth::user()->company_id])->update(['status'=>0])){
           Session::flash('success','Success!');
        }else{
           Session::flash('error','Error! Record is not remove');
        }

        return redirect()->route('listVariation');
    }


 

}
