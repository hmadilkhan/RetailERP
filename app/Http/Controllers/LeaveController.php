<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\leave;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class LeaveController extends Controller
{
	  public function __construct()
    {
        $this->middleware('auth');
    } 

        public function view(leave $leave){


        $getempid = $leave->getempidbysession();
        if ($getempid[0]->emp_id != 0) {
        $details = $leave->getleavedetails($getempid[0]->emp_id,"");

        }
        else if ($getempid[0]->emp_id == 0) {
        if (session("roleId") != 2)  {
       $details = $leave->getleavedetails("",session("branch"));

        	}
        	else{
        	$details = $leave->getleavedetails("","");	
        	}
        }
        return view('Leaves.view-leaveform', compact('details'));	
    }

   	 public function showform(leave $leave){
      	$getemp = $leave->getemployee(session("branch"));
	 	return view('Leaves.create-leaveform', compact('getemp'));	
    }

    public function leaveheads(leave $leave, Request $request){
      	$leavehead = $leave->getleaveshead($request->empid);
	 	return $leavehead;
    }

      public function leavebalance(leave $leave, Request $request){
      	if ($request->leaveid == "") {
      	$balance = $leave->getbalance($request->empid);
	 	return $balance;
      	}
      	else{
      	$balance = $leave->leavebalance($request->empid,$request->leaveid);
	 	return $balance;	
      	}
      	
    }

    public function storeleaveform(leave $leave, Request $request){
      	//already exsist check
      	$exsist = $leave->exsist_chk_leavedetails($request->fromdate,$request->todate,$request->empid);
      	if ($exsist[0]->counts == 0) {
          //check absent data
          $absentdata = $leave->check_absentdata($request->fromdate,$request->todate,$request->empid);
          if (count($absentdata) != 0) {
            //delete first absent data
            foreach ($absentdata as $value) {
            //delete
            $deleteabsent = $leave->delete_absent($value->id);
            }
          }
      	//check balance of leave
      	$balance = $leave->leavebalance($request->empid,$request->leaveid);

      	//compare balance and insert
      	if ($balance[0]->balance >= $request->days) {
      	$items=[
      		'leave_id' => $request->leaveid,
      		'emp_id' => $request->empid,
      		'from_date' => $request->fromdate,
      		'to_date' => $request->todate,
      		'days' => $request->days,
      		'reason' => $request->reason,
      		'leave_status' => 3,
      	];
      	$result = $leave->insert('leaves_avail_details',$items);

      	//detact from balance and update
      	//get balance and check if zero then set status IN-ACTIVE
      	$newbal = ($balance[0]->balance - $request->days);
      	if ($newbal == 0) {
      	$items=[
      		'balance' => $newbal,
      		'status_id' => 2,
      	];
      	$resultupdate = $leave->update_leaves_details($balance[0]->id,$items);
      	}
      	else{
      	$items=[
      		'balance' => $newbal,
      	];
      	$resultupdate = $leave->update_leaves_details($balance[0]->id,$items);
      	}
	 	return 1;
	 		}
	 		else{
	 			return 0;
	 		}

	}else{
      		return 2;
      	}
    }


      public function updatestatus(leave $leave, Request $request){
      	if ($request->statusid == 2) {
      	$balance = $leave->leavebalance($request->empid,$request->leaveid);
      	$qty = ($balance[0]->balance + $request->days);
      	$items=[
      		'balance' => $qty,
      	];
      	$result = $leave->update_leaves_details($request->updateid,$items);
      	}

      	$items=[
      		'leave_status' => $request->statusid,
      	];
      	$result = $leave->update_status($request->id,$items);
	 	return $result;
    }

    
    
}
