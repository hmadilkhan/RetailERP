<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\hrpermission;


class HRPermissionController extends Controller
{
	  public function __construct()
    {
        $this->middleware('auth');
    } 

    public function show(hrpermission $permission){
    	$result = $permission->getpermissions();
    	 return view('HR_Permission.permission-panel', compact('result'));	
    }

    public function store(hrpermission $permission, Request $request){

    	$exsist = $permission->exsist_chk();
    	if ($exsist[0]->counts == 0) {
    	$items=[
    			'allowances' => ($request->allowances == "" ? 0 : 1),
    			'increment' => ($request->increment == "" ? 0 : 1),
    			'promotion' => ($request->promotion == "" ? 0 : 1),
    			'bonus' => ($request->bonus == "" ? 0 : 1),
    			'advance' => ($request->advance == "" ? 0 : 1),
    			'loan' => ($request->loan == "" ? 0 : 1),
    			'leaves' => ($request->leaves == "" ? 0 : 1),
    			'qualification' => ($request->qualification == "" ? 0 : 1),
    			'switch_transfer' => ($request->switch_transfer == "" ? 0 : 1),
    			'overtime' => ($request->overtime == "" ? 0 : 1),
    			'taxes' => ($request->taxes == "" ? 0 : 1),
    			'company_id' => session('company_id'),
    	];

    	$result = $permission->insert('hr_permission',$items);
    	return redirect('/showhrpermission')->with('success',"Permission Successfully Granted!!");
    	}
    	else{
    	$items=[
    			'allowances' => ($request->allowances == "" ? 0 : 1),
    			'increment' => ($request->increment == "" ? 0 : 1),
    			'promotion' => ($request->promotion == "" ? 0 : 1),
    			'bonus' => ($request->bonus == "" ? 0 : 1),
    			'advance' => ($request->advance == "" ? 0 : 1),
    			'loan' => ($request->loan == "" ? 0 : 1),
    			'leaves' => ($request->leaves == "" ? 0 : 1),
    			'qualification' => ($request->qualification == "" ? 0 : 1),
    			'switch_transfer' => ($request->switch_transfer == "" ? 0 : 1),
    			'overtime' => ($request->overtime == "" ? 0 : 1),
    			'taxes' => ($request->taxes == "" ? 0 : 1),
    			'company_id' => session('company_id'),
    	];

    	$result = $permission->update_permission($request->id,$items);
    	return redirect('/showhrpermission')->with('success',"Permission Updated Successfully!!");
    	}

    	
	}
}