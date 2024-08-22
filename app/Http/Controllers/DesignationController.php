<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\designation;
use Illuminate\Support\Facades\Validator;

class DesignationController extends Controller
{
	  public function __construct()
    {
        $this->middleware('auth');
    } 

          public function view(designation $desg){
          	$getdesg = $desg->getdesg();
    	 return view('Designation.view-designation', compact('getdesg'));	
    }

          public function show(designation $desg){
            $depart = $desg->getdepart();
    	 return view('Designation.create-designation', compact('depart'));	
    }

     public function store(designation $desg, Request $request){

       	$count = $desg->exist($request->desg,$request->department);
    	if ($count[0]->counts == 0) {
    	$items = [
    		'department_id' => $request->department,
        'designation_name' => $request->desg,
    	 ];

    	 $result = $desg->insert($items);
    	 return 1;
    	}
    	else{
    		return 0;
    	}

    	   
    }


          public function remove(designation $desg, Request $request){
    	 $result = $desg->desg_delete($request->id);
    	 return 1;
    	 
    }

         public function edit(designation $desg, Request $request){
    	 $designation = $desg->getdesg_byid($request->id);
    	 return view('Designation.edit-designation', compact('designation'));	
    }

    
          public function update(designation $desg, Request $request){

         	$items = [
    		'designation_name' => $request->desg,
    	 ];

    	 $result = $desg->update_desg($request->desgid, $items);
    	 return 1;
    	 
    }
}