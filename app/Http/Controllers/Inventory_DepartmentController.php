<?php

namespace App\Http\Controllers;

use App\inventory_department;
use Illuminate\Http\Request;
use Image;
use Illuminate\Support\Facades\DB;
use App\Helpers\custom_helper;
use File;

class Inventory_DepartmentController extends Controller
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
	  $depart = inventory_department::getdepartment('');

	  $sdepart = inventory_department::get_subdepart('');

      return view('Invent_Department.lists',compact('depart','sdepart'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request,inventory_department $invent_department,custom_helper $helper)
    {

		$imageName = ""; 
		
		if(!empty($request->post('parent'))){
            $exsist = $invent_department->subdepart_exists($request->deptname,$request->post('parent'));
    
            if ($exsist[0]->counter == 0) {

        	    if(!empty($request->file('departImage'))){
        	        $imageName = preg_replace("/[\s_]/", "-",strtolower($request->get('deptname'))).time().'.'.strtolower($request->file('departImage')->getClientOriginalExtension()); 
        	        $request->file('departImage')->move(public_path('assets/images/department'),$imageName);
        	    }                
                
                $items = [
                    'code' => $request->code,
                    'department_id' => $request->post('parent'),
                    'sub_depart_name' => $request->deptname,
                    'slug'=> preg_replace("/[\s_]/", "-",strtolower($request->deptname)),
                    'image'=>$imageName,
                ];
                $result = $invent_department->insert_sdept($items);
                $getsubdepart = $invent_department->get_subdepartments($request->post('parent'));
                return 1;
            }
            else{
                return 0;   
            }		    
		    
		}else{    
		
        // return $invent_department->check_dept($request->get('deptname'),$request->get('code'));

        if($invent_department->check_depart_code($request->get('code'))){
             return response()->json(array("state"=>1,"msg"=>'This department code already exists.',"contrl"=>'deptname'));
        }
		if($invent_department->check_dept($request->get('deptname'))){
             return response()->json(array("state"=>1,"msg"=>'This department name already exists.',"contrl"=>'deptname'));
        }
		// else {
		
    	    if(!empty($request->file('departImage'))){
    	        $imageName = preg_replace("/[\s_]/", "-",strtolower($request->get('deptname'))).time().'.'.strtolower($request->file('departImage')->getClientOriginalExtension()); 
    	        $request->file('departImage')->move(public_path('assets/images/department'),$imageName);
    	    }		

			 $data = [
                'company_id'               => session('company_id'),
                'code'                     => $request->get('code'),
                'department_name'          => $request->get('deptname'),
                'website_department_name'  => (empty($request->webdeptname) ?  $request->get('deptname') : $request->webdeptname),
                'date'                     => date('Y-m-d'),
                'time'                     => date('H:i:s'),
                'slug'                     => preg_replace("/[\s_]/", "-",strtolower($request->get('deptname'))),
				"image"                    => $imageName,
             ];
				
            $result = $invent_department->insert_dept($data);
            if($result){
                   
                 $subdpt_value = $request->subdpt;
             
                 $subdpt_value = explode(",",$subdpt_value);
				 
				 $code = $request->get('code');
        
            
                for($i=0;$i < count($subdpt_value);$i++){
                    
                     $invent_department->insert_sdept([
						'code'=>++$code,
						'department_id'=>$result,
						'sub_depart_name'=>$subdpt_value[$i],
						'slug'=> preg_replace("/[\s_]/", "-",strtolower($subdpt_value[$i])),
					]);
 
                }
				// $msg = "ID # ".$result.", Name : ".$request->get('deptname');
				// $helper->sendPushNotification("New Department Added",$msg); 
               return response()->json(array("state"=>0,"msg"=>'',"contrl"=>''));

             }else{
               return response()->json(array("state"=>1,"msg"=>'Not saved :(',"contrl"=>''));
             } 
        // }
		}
    }

    public function depart_update(Request $request,inventory_department $invent_department,custom_helper $helper){

         if($invent_department->check_edit_depart_name($request->get('id'),$request->get('depart'))){
            return response()->json(array("state"=>1,"msg"=>'This department already exists.',"contrl"=>'udeptname'));
         }else {
                
                if($invent_department->modify("inventory_department",['department_name'=>$request->get('depart'),'slug'=> preg_replace("/[\s_]/", "-",strtolower(get('depart')))],['department_id'=>$request->get('id')])){
                    
                    //   return response()->json(['department_name'=>$request->get('depart'),'slug'=> preg_replace("/[\s_]/", "-",strtolower($request->get('depart')))]);
					$msg = "ID # ".$request->get('id').", Name : ".$request->get('depart');
					$helper->sendPushNotification("Department Updated",$msg);
                  return response()->json(array('state'=>0,'msg'=>'Saved changes :) '));
                }else {
                  return response()->json(array('state'=>1,'msg'=>'Oops! not saved changes :('));
                }

         }
    }


   public function sb_depart_update(Request $request,inventory_department $invent_department){
		
		$imageName = null;
		
		if($invent_department->check_edit_sub_depart_code($request->id,$request->code,$request->dept))
		{
			return response()->json(array("state"=>1,"msg"=>'This Sub-department code already exists.',"contrl"=>'deptname'));
		}
		
		if($invent_department->check_edit_sub_depart_name($request->id,$request->sdepart,$request->dept))
		{
			return response()->json(array("state"=>1,"msg"=>'This Sub-Department name already exists.',"contrl"=>'deptname'));
		}
         // if($invent_department->check_sdept($request->get('id'),$request->get('sdepart'),$request->get('dept'),$request->get('code'))){
            // return response()->json(array("state"=>1,"msg"=>'This sub-department already exists.',"contrl"=>'tbx_'.$request->get('sdepart')));
         // }else {
         
         
         
	    if(!empty($request->file('subdepartImage'))){
	        $imageName = preg_replace("/[\s_]/", "-",strtolower($request->get('sdepart'))).time().'.'.strtolower($request->file('subdepartImage')->getClientOriginalExtension()); 
	        
	        $getFormFile = $request->file('subdepartImage');
	        $getFormFile->move(public_path('assets/images/department'),$imageName);
	      
	       $get = DB::table('inventory_sub_department')->where('sub_department_id',$request->id)->first();
	        if($get){
	            if(File::exists(public_path('assets/images/department/').$get->image)){
	                File::delete(public_path('assets/images/department/').$get->image);
	            }
	        }
	    } 
	    
	    $column = ['sub_depart_name'=>$request->get('sdepart'),'slug'=> preg_replace("/[\s_]/", "-",strtolower($request->get('sdepart')))];
	    
	    if($imageName != null){
	       $column['image']=$imageName;
	    }
	    
	    if($request->get('code') != null){
	        $column['code']=$request->get('code');
	    }
                
            if($invent_department->modify("inventory_sub_department",$column,['sub_department_id'=>$request->get('id')])){
                return response()->json(array('state'=>0,'msg'=>'Saved changes :) '));
            }else {
                return response()->json(array('state'=>1,'msg'=>'Oops! not saved changes :('));
            }

         // }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(inventory_department $invent_department)
    {
        //
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function edit(inventory_department $invent_department,$id)
    {
          $depart = $invent_department->get_edit($id);
             
             if($depart->count() > 0 ){
                 return response()->json($depart);
             }else {
                return response()->json(0);
             }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\inventory  $inventory
     * @return \Illuminate\Http\Response
     */

   

    public function update(Request $request,inventory_department $invent_department)
    {
              /*$result = $invent_department->modify(['department_name'=>$request->deptname],$request->hidd_id);
              
                if($invent_department->check_sdept($request->hidd_id)){
                   $invent_department->remove_sbdept($request->hidd_id);
                 }

                 $subdpt_value = explode(",",$request->subdpt);

                for($i=0;$i<count($subdpt_value);$i++){
                    
                    $result = $invent_department->insert_sdept(['department_id'=>$request->hidd_id,'sub_depart_name'=>$subdpt_value[$i]]);
                }*/
               
               $result = false;
               if($result){ 
                 return response()->json(array('state'=>1,'msg'=>'Saved changes :) '));
               }else {
                 return response()->json(array('state'=>0,'msg'=>'Oops! not saved changes :('.$request->subdpt));
               }      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(inventory_department $invent_department)
    {
        //
    }
	
	public function deletedepartment(Request $request)
	{
		try{
			if($request->id != ""){
				DB::beginTransaction();
				DB::table("inventory_general")->where("department_id",$request->id)->update(["status" => 2]);
				DB::table("inventory_sub_department")->where("department_id",$request->id)->update(["status" => 2]);
				DB::table("inventory_department")->where("department_id",$request->id)->update(["status" => 2]);
				DB::commit();
				return response()->json(["status" => 200,"message" => "Department Deleted successfully."]);
				
			}
		}catch(\Exception $e){
			DB::rollback();
			return response()->json(["status" => 500,"message" => "Error: ".$e->getMessage()]);
		}
	}


    public function adddepartment(inventory_department $in_depart, Request $request)
    {
        $exsist = $in_depart->depart_exists($request->departname);

        if ($exsist[0]->counter == 0) {
            $items = [
                'company_id' => session('company_id'),
                'department_name' => $request->departname,
                'slug'=> preg_replace("/[\s_]/", "-",strtolower($request->departname)),
                'date'=> date('Y-m-d'),
                'time'=> date('H:i:s')
            ];
            $result = $in_depart->insert_dept($items);
            $getdepart = $in_depart->get_departments();
            return $getdepart; 
        }
        else{
            return 0;   
        }

    }

     public function addsubdepartment(inventory_department $in_depart, Request $request)
    {
        $imageName = null;
        
        $exsist = $in_depart->subdepart_exists($request->subdepart,$request->departid);

        if ($exsist[0]->counter == 0) {
            
        	    if(!empty($request->file('subdepartImage'))){
        	        $imageName = preg_replace("/[\s_]/", "-",strtolower($request->get('subdepart'))).time().'.'.strtolower($request->file('subdepartImage')->getClientOriginalExtension()); 
        	        $getFormFile = $request->file('subdepartImage'); 
        	        $getFormFile->move(public_path('assets/images/department'),$imageName);
        	    }              
            
            $items = [
                'code'                         => $request->code,
                'department_id'                => $request->departid,
                'sub_depart_name'              => $request->subdepart,
                'website_sub_department_name'  => empty($request->websubdepart) ? $request->subdepart : $request->websubdepart,
                'slug'                         => preg_replace("/[\s_]/", "-",strtolower($request->subdepart)),
                'image'                        => $imageName,
            ];
            $result = $in_depart->insert_sdept($items);
            $getsubdepart = $in_depart->get_subdepartments($request->departid);
            return $getsubdepart;
        }
        else{
            return 0;   
        }

    }


    public function updatedepart(inventory_department $in_depart, Request $request)
    {
        $imageName = null;
        
		if($in_depart->check_edit_depart_code($request->departid,$request->editcode))
		{
			return response()->json(array("state"=>1,"msg"=>'This department code already exists.',"contrl"=>'codeid'));
		}
		
		if($in_depart->check_edit_depart_name($request->departid,$request->departname))
		{
			return response()->json(array("state"=>1,"msg"=>'This department name already exists.',"contrl"=>'deptname'));
		}
		
	    if(!empty($request->file('departImage'))){
	        $imageName = preg_replace("/[\s_]/", "-",strtolower($request->get('departname'))).time().'.'.strtolower($request->file('departImage')->getClientOriginalExtension()); 
	        $request->file('departImage')->move(public_path('assets/images/department'),$imageName);
	        
	       $get = DB::table('inventory_department')->where('company_id',session('company_id'))->where('department_id',$request->departid)->first();
	        if($get){
	            if(File::exists(public_path('assets/images/department/').$get->image)){
	                File::delete(public_path('assets/images/department/').$get->image);
	            }
	        }
	    } 		
		
		$items = [
			'company_id'               => session('company_id'),
			'code'                     => $request->editcode,
			'department_name'          => $request->departname,
			'website_department_name'  => (empty($request->webdeptname) ?  $request->departname : $request->webdeptname),
			'slug'                     => preg_replace("/[\s_]/", "-",strtolower($request->departname)),
			'date'                     => date('Y-m-d'),
			'time'                     => date('H:i:s'),
		];
		
		if($imageName != null){
		    $items['image']=$imageName;
		}
	
		$result = $in_depart->update_depart($request->departid, $items);
		return response()->json(array("state"=>0,"msg"=>'Department edit successfully.',"contrl"=>'deptname'));;

    }

  public function getsubdepart(inventory_department $in_depart, Request $request)
    {
        $getsubdepart = $in_depart->get_subdepartments($request->departid);
            return $getsubdepart;
    }




}
