<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\department;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
	  public function __construct()
    {
        $this->middleware('auth');
    } 

       public function view(department $depart){
    	$departments = $depart->getdepart();
    	 return view('Departments.view-departs', compact('departments'));	
    }

     public function show(department $depart, Request $request){
    	 $getbranch = $depart->getbranches();
    	 return view('Departments.create-departs', compact('getbranch'));	
    }



       public function store(department $depart, Request $request){

       	$rules = [
                'department' => 'required',
            ];	
             $this->validate($request, $rules);

       	$count = $depart->exist($request->department, $request->branch);
    	if ($count[0]->counts == 0) {
    	$items = [
    		'branch_id' => $request->branch,
    		'department_name' => $request->department,
    	 ];
    	 $result = $depart->insert($items);
    	 return 1;
    	}
    	else{
    		return 0;
    	}

    	   
    }

       public function edit(department $depart, Request $request){
    	 $getbranch = $depart->getbranches();
    	 $departments = $depart->getdepart_byid($request->id);
    	 return view('Departments.edit-depart', compact('getbranch','departments'));	
    }

          public function update(department $depart, Request $request){

          	$items = [
    		'branch_id' => session('branch'),
    		'department_name' => $request->department,
    	 ];

    	 $result = $depart->update_depart($request->departid, $items);
    	 return 1;
    	 
    }



          public function remove(department $depart, Request $request){
    	 $result = $depart->depart_delete($request->id);
    	 return 1;
    	 
    }
  
  

}