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
    public function index(Request $request)
    {
        if(isset($request->id)){
            return Variation::where(['parent'=>$request->id,'company_id'=>session('company_id'),'status'=>1])->get();
        }
        
    	  $variations   = Variation::where(['parent'=>0,'company_id'=>session('company_id'),'status'=>1])->get();
    	  $variat_value = Variation::whereNotNull('parent')->where(['company_id'=>session('company_id'),'status'=>1])->get();
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
        // $rules = [
        //           'variat_name'       => 'required',
        //           'show_website_name' => 'required',
        //           'required_status'   => 'required',
        //           'type'              => 'required'
        //          ];
                 
        // $this->validate($request,$rules);         
        
        
        $companyId = session('company_id');

            if(Variation::where(['name'=>$request->variat_name,'company_id'=>$companyId,'status'=>1])->count() > 0){
                return response()->json(['status'=>500,'msg'=>'This '.$request->variat_name.' variation name is already taken','control'=>'variat_name']);
            } 

            $createVariatParent = Variation::create([
                                                  'company_id'         => $companyId,
                                                  'name'               => $request->variat_name,
                                                  'show_website_name'  => $request->show_website_name,
                                                  'required_status'    => $request->required_status,
                                                  'type'               => $request->type,
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
                
                return response()->json(['status'=>200,'msg'=>'Success!']);
             }else{
                return response()->json(['status'=>500,'msg'=>'Error! Record not create to server']);
             }				
    }
    
    public function singleVariationAdd(Request $request){
        $companyId = session('company_id');
        
            if(Variation::where(['name'=>$request->value,'company_id'=>$companyId,'status'=>1,'parent'=>$request->id])->count() > 0){
                return response()->json(['msg'=>'This '.$request->value.' variation name is already taken','status'=>500,'control'=>'variation_value_md']);
            } 
            
            $result = Variation::create([
                                          'company_id'    => $companyId,
                                          'name'          => $request->value,
                                          'parent'        => $request->id,
                                          'status'        => 1,
                                          'created_at'    => date("Y-m-d H:i:s")
                                        ]);
            
            return ($result) ? response()->json(['status'=>200]) :  response()->json(['status'=>500,'msg'=>'Server Issue']);                           
    }

    public function update(Request $request){
        $companyId = session('company_id');
        
            if(Variation::where(['name'=>$request->edit_variat_name,'company_id'=>$companyId,'status'=>1])->where('id','!=',$request->id)->count() > 0){
                return response()->json(['msg'=>'This '.$request->edit_variat_name.' variation name already taken','status'=>500,'control'=>'edit_variat_name']);
            } 
            
             $result = Variation::where(['id'=>$request->id,'company_id'=>$companyId])->first();     
             
             $result->name               = $request->edit_variat_name;
             $result->show_website_name  = $request->edit_show_website_name;
             $result->type               = strtolower($request->edit_vtype);
             $result->required_status    = $request->edit_is_required;
             $result->updated_at         = date('Y-m-d H:i:s');

        return ($result->save()) ? response()->json(['status'=>200]) :  response()->json(['status'=>500,'msg'=>'Server Issue']); 
    }

    // sub variation value update method //
    public function updateValue(Request $request){
        $companyId = session('company_id');
        
            if(Variation::where(['name'=>$request->name,'company_id'=>$companyId,'status'=>1])->where('parent','=',$request->parentId)->count() > 0){
                return response()->json(['msg'=>'This '.$request->name.' variation name is already taken','status'=>500,'control'=>'name'.$request->id]);
            } 
            
             $result = Variation::where(['id'=>$request->id,'company_id'=>$companyId])->first();     
             
             $result->status       = 0;
             $result->updated_at   = date('Y-m-d H:i:s');
             $result->save();
             
             $insert_resp = Variation::create([
                                          'company_id'    => $companyId,
                                          'name'          => $request->name,
                                          'parent'        => $request->parentId,
                                          'status'        => 1,
                                          'created_at'    => date("Y-m-d H:i:s")
                                        ]);
                                        
        return ($insert_resp) ? response()->json(['status'=>200,'control'=>'name'.$request->id,'msg'=>'Success!']) :  response()->json(['status'=>500,'msg'=>'Server Issue']); 
    }   


    public function destroy($id){

        if(Variation::where(['id'=>$id,'company_id'=>session('company_id')])->update(['status'=>0]) && Variation::where(['parent'=>$id,'company_id'=>session('company_id')])->update(['status'=>0])){
           Session::flash('success','Success!');
        }else{
           Session::flash('error','Error! Record is not remove');
        }
            
        return redirect()->route('listVariation');
    }
    
    public function destroyVariat_value(Request $request){
        
        $result = Variation::where(['id'=>$request->id,'parent'=>$request->parentid,'company_id'=>session('company_id')])->update(['status'=>0]);
                
        return ($result) ? response()->json(['status'=>200]) : response()->json(['status'=>500,'msg'=>'Server Issue','control'=>'name'.$request->id]);
    }


 

}
