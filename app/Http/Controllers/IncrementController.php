<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\increment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DateTime;

class IncrementController extends Controller
{
	  public function __construct()
    {
        $this->middleware('auth');
    } 

    public function view(increment $increment){
    	$last_day = date('Y-m-t', strtotime(date('Y-m-d')));
    	$first_day = date('Y-m-01', strtotime(date('Y-m-d')));

    $details = $increment->getincrement_details($first_day,$last_day);
    return view('increment.show-increment', compact('details'));	
    }

      public function show(increment $increment){
      	$getemp = $increment->getemployee(session("branch"));

      	$taxslabs= $increment->gettaxslabs();
        return view('increment.create-increment', compact('getemp','taxslabs'));	
    }

    public function getbasicsal(increment $increment, Request $request){
      	$basic = $increment->getbasicsal($request->empid);
        return $basic;
    }

   public function gettaxslab(increment $increment, Request $request){
      	$slabs = $increment->gettaxslab_byempid($request->empid);
        return $slabs;
    }

     public function getallowances(increment $increment, Request $request){
      	$allowance = $increment->getallowancedetails($request->empid);
        return $allowance;
    }


 public function allowanceincre(increment $increment, Request $request){

//update old allowance set in-active
	 $item = [
        'status_id' => 2, 
       ];
     $result = $increment->update_allowance_details($request->id,$item);

//insert new allowance
        $item = [
        'emp_id' => $request->employee, 
        'allowance_id' => $request->allowancehead, 
        'amount' => $request->amount, 
        'status_id' => 1, 
       ];
        $result = $increment->insert("allowances_details",$item);

      	$allowance = $increment->getallowancedetails($request->employee);
        return $allowance;
    }


public function store(increment $increment, Request $request){

	$details = $increment->getsalarydetails($request->empid);
	if (count($details) == 0) {
		return 0;	
	}
	else{
		$items =[
			'status_id' => 2, 
		];
		$result = $increment->update_increment($details[0]->increment_id, $items);

	 $item = [
        'emp_id' => $request->empid,
        'basic_pay' => $request->basicpay,
        'salary_category_id' => 2, // 2 belong monthly salary
        'tax_applicable_id' => $request->tax,
        'inc_status_id' => 2, // 2 belongs to Increment Status
        'status_id' => 1, 
       ];
      
        $salary = $increment->insert('increment_details',$item);
        
        if ($request->tax == 1) {

     	$last_day = date('Y-m-t', strtotime(date('Y-m-d')));
    	$first_day = date('Y-m-01', strtotime(date('Y-m-d')));

    	$exsits = $increment->gettaxdetails($request->empid,$first_day,$last_day);
    	if ($exsits[0]->counts == 0) {
        $item = [
        'emp_id' => $request->empid,
        'tax_id' => $request->taxslabid,
        'tax_amount' => $request->taxamt,
       ];
        $taxdetails = $increment->insert('tax_details',$item);
    	}
    	else{
    $taxid = $increment->gettaxid_update($request->empid,$first_day,$last_day);

  		$item = [
        'tax_id' => $request->taxslabid,
        'tax_amount' => $request->taxamt,
        'date' => date('Y-m-d'),
       ];
       $result = $increment->update_taxdetails($taxid[0]->id,$item);
    	}
  }
        return 1;
	}
}



     


}   