<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\employee;
use App\designation;
use App\department;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Image;
use Carbon\CarbonPeriod;

class EmployeeController extends Controller
{
	  public function __construct()
    {
        $this->middleware('auth');
    } 

    public function view(employee $employee){
      	$getemp = $employee->get_employees(1);
	 	return view('Employee.view-employee', compact('getemp'));	
    }

     public function getdeparts(employee $employee, Request $request){

        $departs = $employee->getdepart($request->branchid);
    return $departs;
    }

    public function getdesig(employee $employee, Request $request){

     $desg = $employee->getdesg($request->departid);
    return $desg;
    }

    public function empdetails(employee $employee, Request $request){
        $details = $employee->get_employee_byid($request->id);
        $getdata = $employee->getperviousdata($request->id);
	 	return view('Employee.details-employee', compact('details','getdata'));	
    }

     public function edit(employee $employee, Request $request){
        $details = $employee->get_employee_byid($request->id);
        if ($details[0]->tax_applicable_id == 1) {
          $taxdetails = $employee->gettaxdetails($request->id);
        }
        else{
          $taxdetails = [
            'id' => 0,
            'tax_id' => 0,
          ];
        }
        $getbranch = $employee->getbranches();
        $departments = $employee->getdepart($details[0]->branch_id);
        $designations = $employee->getdesg($details[0]->department_id);
        $shift = $employee->getofficeshift($details[0]->branch_id);
        $category = $employee->getcategory();
        $otamount = $employee->getotamount();
        $otduration = $employee->getotduration();
        $taxslabs= $employee->gettaxslabs();
        $permission = $employee->getpermissions();
	 	return view('Employee.edit-employee', compact('details','getbranch','taxslabs','category','otamount','otduration','designations','departments','shift','taxdetails','permission'));	
    }

      public function viewinactive(employee $employee){
      	$getempinactive = $employee->get_employees(2);
	 	return $getempinactive;
    }

    public function show(employee $employee){
      	$getbranch = $employee->getbranches();
		
        $otamount = $employee->getotamount();

        $category = $employee->getcategory();

        $otduration = $employee->getotduration();

        $taxslabs= $employee->gettaxslabs();

        $departments = $employee->getdepart(session('branch'));

        $permission = $employee->getpermissions();
		
		if($permission->isEmpty()){
			return redirect()->to("view-employee")->with(["message" => "Permission not assigned.Please assign HR Permission"]);
		}

		return view('Employee.create-employee', compact('getbranch','category','otamount','otduration','taxslabs','departments','permission'));	
    }


       public function store(employee $employee, Request $request){
		// return $request;
		
		
        $imageName = '';
        $docImage1 = '';
        $docImage2 = '';
		
		$rules = [
            'empacc' => 'required',
            'empname' => 'required',
            'fname' => 'required',
            'empnic' => 'required',
            'empcontact' => 'required',
            'gender' => 'required',
            'branch' => 'required',
            'department' => 'required',
            'designation' => 'required',
            'officeshift' => 'required',
            'doj' => 'required',
        ];
         $this->validate($request, $rules);

       	$count = $employee->empAcc_exist($request->empacc,1);
    	if ($count[0]->counts == 0) {

    	if(!empty($request->empimg)){
         $request->validate([
              'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
          ]);
          $imageName = $request->empacc.'.'.$request->empimg->getClientOriginalExtension();
          $img = Image::make($request->empimg)->resize(600, 600);
          $res = $img->save(public_path('assets/images/employees/images/'.$imageName), 75);
//          $request->empimg->move(public_path('assets/images/employees/images/'), $imageName);
         }

            if(!empty($request->docimg1)){
                $request->validate([
                    'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);
                $docImage1 = $request->docimg1->getClientOriginalName();
                $img = Image::make($request->docimg1)->resize(600, 600);
                $res = $img->save(public_path('assets/images/employees/documents/'.$docImage1), 75);
//                $request->docimg1->move(public_path('assets/images/employees/documents/'), $docImage1);
            }

            if(!empty($request->docimg2)){
                $request->validate([
                    'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);
                $docImage2 = $request->docimg2->getClientOriginalName();
                $img = Image::make($request->docimg2)->resize(600, 600);
                $res = $img->save(public_path('assets/images/employees/documents/'.$docImage2), 75);
//                $request->docimg2->move(public_path('assets/images/employees/documents/'), $docImage2);
            }

       
			try{
			DB::beginTransaction();				
			// Employee general details insert start ==>
				
				$items = [
					'emp_acc' => $request->empacc,
					'emp_name' => $request->empname,
					'emp_fname' => $request->fname,
					'emp_cnic' => $request->empnic,
					'emp_contact' => $request->empcontact,
					'emp_address' => $request->empaddress,
					'gender' => $request->gender,
					'emp_picture' => $imageName,
					'status_id' => 1,
					'document1' => $docImage1,
					'document2' => $docImage2,
					'pf_enable' => $request->pf_enable,
					'security_deposit' => $request->security_deposit,
				 ];

				 $empdata = $employee->insert('employee_details',$items);
		 // Employee general details insert end ==>
 
		// Employee hire and fire details insert start ==>
			   $item = [
				'emp_id' => $empdata,
				'reason' => null,
				'date_of_joining' => $request->doj, 
				'date_of_firing' => null,
				'status_id' => 1,
			   ];
			  
				$joiningdata = $employee->insert('employee_fire_details',$item);
		// Employee hire and fire details insert end ==>

		// Employee shift details insert start ==>
			   $item = [
				'emp_id' => $empdata,
				'branch_id' => $request->branch,
				'department_id' => $request->department,
				'designation_id' => $request->designation,
				'shift_id' => $request->officeshift,
				'status_id' => 1, 
			   ];
			  
				$officeshift = $employee->insert('employee_shift_details',$item);
		// Employee shift details insert end ==>

		// Employee salary details insert start ==>
				 $item = [
				'emp_id' => $empdata,
				'basic_pay' => $request->basicpay,
				'pf_fund' => $request->pf_fund,
				'allowance' => $request->allowance,
				'gross_salary' => $request->grosspay,
				'salary_category_id' => $request->cat,
				'tax_applicable_id' => $request->tax,
				'inc_status_id' => 1, // 1 belongs to Hiring Status
				'status_id' => 1, 
			   ];
			  
				$salary = $employee->insert('increment_details',$item);
		// Employee salary details insert end ==>

		// Employee overtime details insert start ==>        
				$item = [
				'emp_id' => $empdata,
				'otamount_id' => $request->otamount,
				'otduration_id' => $request->otduration,
				'status_id' => 1,
			   ];
				$overtime = $employee->insert('employee_overtime_details',$item);
		// Employee overtime details insert end ==>  

		// Employee tax details insert start ==> 
		if ($request->taxslab != "") {
		  $annualsalary = ($request->basicpay * 12);
		  $slab = $employee->gettaxslabrange($request->taxslab,$annualsalary,$annualsalary);

		  $yearlyamt = (($annualsalary * $slab[0]->percentage) / 100);
		  $monthamt = ($yearlyamt / 12);

				 $item = [
				'emp_id' => $empdata,
				'tax_id' => $request->taxslab,
				'tax_amount' => $monthamt,
			   ];
			  
				$taxdetails = $employee->insert('tax_details',$item);   
		 }    
		 if($request->new_hiring == "on"){
			
			$holidays = DB::table("holidays")->where("branch_id",$request->branch)->pluck("day_off");
			$startdate = date("Y-m",strtotime($request->doj));
			$startdate = $startdate."-02";
			$enddate = $request->doj;
			$period = CarbonPeriod::create($startdate, $enddate);
			
			// Iterate over the period
			foreach ($period as $date) {
				// echo $date->format('Y-m-d');
				// echo $date->format('l');
				if (!in_array($date->format('l'), $holidays->toArray())){
					$item = [
						'acc_no' => $empdata,
						'absent_date' => $date->format('Y-m-d'),
						'weekday' => 0,
						'event' => 0,
				   ];
				   $taxdetails = $employee->insert('absent_details',$item);   
				}
			}
			// Convert the period to an array of dates
			// $dates = $period->toArray();
		}
		// Employee tax details insert end ==>  
		  DB::commit();
    	  return response()->json(["status" => 200,"message" => "Employee Added Successfully"]);
		}catch(\Exception $e){
			DB::rollback();
			return response()->json(["status" => 500,"message" => "Error :".$e->getMessage()]);
		}
    	}
    	else{
    		return 0;
    	}
	   
    }

     public function empacccheck(employee  $employee, Request $request){
     	$result = $employee->empAcc_check($request->empacc);
     	return $result;
     }

     public function store_desg(employee  $employee, designation $desg, Request $request){
        $count = $desg->exist($request->desg,$request->depart);
    	if ($count[0]->counts == 0) {
			$items = [
				'department_id' => $request->depart,
				'designation_name' => $request->desg,
			 ];

			 $result = $desg->insert($items);
			 $getdesg = $employee->getdesg($request->depart);
			 return $getdesg;
    	}
    	else{
    		return 0;
    	}
     }

       public function store_depart(department $depart, Request $request){

       	$count = $depart->exist($request->department, $request->branch);

    	if ($count[0]->counts == 0) {
    	$items = [
    		'branch_id' => $request->branch,
    		'department_name' => $request->department,
    	 ];
    	 $result = $depart->insert($items);
    	 $departments = $depart->getdepart();
    	 return $departments;
    	}
    	else{
    		return 0;
    	}


    }
     public function remove(employee $employee, Request $request){

// ===== This tables neeed to update for Fire Employee =====
      // 1. Employee details
      // 2. Employee Shift details
      // 3. Employee Overtime details
      // 4. Employee Fire details
      // 5. Leave details
      // 6. Allowance details
      // 7. Increment details (Salary)
// ===== This tables neeed to update for Fire Employee =====

   $item = [
        'reason' => $request->reason,
        'date_of_firing' => date('Y-m-d'),
        'status_id'=>$request->statusid,
       ];
$joiningdata = $employee->update_emp_joining($request->fireid, $item);

$result = $employee->remove_emp($request->empid, $request->statusid);

$getidshift = $employee->getidforupdate_empshift($request->empid);
$shiftupdate = $employee->remove_emp_shift($getidshift[0]->id,2);


$getidovertime = $employee->getidforupdate_overtime($request->empid);
$overtimeupdate = $employee->remove_emp_overtime($getidovertime[0]->id,2);

$getidsalary = $employee->getidforupdate_salary($request->empid);
$salaryupdate = $employee->remove_emp_salary($getidsalary[0]->increment_id,2);


$getidleaves = $employee->getidforupdate_leaves($request->empid);
if (count($getidleaves) != 0) {
foreach ($getidleaves as $value) {
  $leavesupdate = $employee->remove_emp_leaves($value->id,2);
}
}

$getidallowances = $employee->getidforupdate_allowances($request->empid);
if (count($getidallowances) != 0) {
foreach ($getidallowances as $value) {
  $allowanceupdate = $employee->remove_emp_allowances($value->id,2);
}
}

return 1;

}

      public function hireagain(employee $employee, Request $request){

       $item = [
        'emp_id' => $request->empid,
        'reason' => null,
        'date_of_joining' => date('Y-m-d'),
        'date_of_firing' => null,
        'status_id' => 1,
       ];
      
    $joiningdata = $employee->insert('employee_fire_details',$item);

 $result = $employee->remove_emp($request->empid, $request->statusid);

  $getsalaryid = $employee->getMAXidforupdate_salary($request->empid);
  $salaryupdate = $employee->remove_emp_salary($getsalaryid[0]->increment_id,1);

  $getshifid = $employee->getMAXidforupdate_shift($request->empid);
  $shiftupdate = $employee->remove_emp_shift($getshifid[0]->id,1);

  $getovertimeid = $employee->getMAXidforupdate_overtime($request->empid);
  $overtimeupdate = $employee->remove_emp_overtime($getovertimeid[0]->id,1);
  
  return 1;
   }

   public function update(employee $employee, Request $request){

   if(!empty($request->empimg)){
     $path = public_path('assets/images/employees/images/').$request->profileimg;
            if(file_exists($path)){
                  @unlink($path);
                }
             $request->validate([
                 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                 ]);
           $imageName = $request->empacc.'.'.$request->empimg->getClientOriginalExtension();
           $img = Image::make($request->empimg)->resize(600, 600);
           $res = $img->save(public_path('assets/images/employees/images/'.$imageName), 75);
//           $request->empimg->move(public_path('assets/images/employees/images/'), $imageName);

            }
            else{
                $imageName = $request->profileimg;
            }

       if(!empty($request->docimg1)){
           $path = public_path('assets/images/employees/documents/').$request->docimg1old;
           if(file_exists($path)){
               @unlink($path);
           }
           $request->validate([
               'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
           ]);
           $docImage1 = $request->docimg1->getClientOriginalName();
           $img = Image::make($request->docimg1)->resize(600, 600);
           $res = $img->save(public_path('assets/images/employees/documents/'.$docImage1), 75);
//           $request->docimg1->move(public_path('assets/images/employees/documents/'), $docImage1);

       }
       else{
           $docImage1 = $request->docimg1old;
       }

       if(!empty($request->docimg2)){
           $path = public_path('assets/images/employees/documents/').$request->docimg2old;
           if(file_exists($path)){
               @unlink($path);
           }
           $request->validate([
               'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
           ]);
           $docImage2 = $request->docimg2->getClientOriginalName();
           $img = Image::make($request->docimg2)->resize(600, 600);
           $res = $img->save(public_path('assets/images/employees/documents/'.$docImage2), 75);
//           $request->docimg2->move(public_path('assets/images/employees/documents/'), $docImage2);

       }
       else{
           $docImage2 = $request->docimg2old;
       }


            // Employee general details insert start ==>
      $items = [
			'emp_acc' => $request->empacc,
			'emp_name' => $request->empname,
			'emp_fname' => $request->fname,
			'emp_cnic' => $request->empnic,
			'emp_contact' => $request->empcontact,
			'emp_address' => $request->empaddress,
			'gender' => $request->gender,
			'emp_picture' => $imageName,
			'status_id' => 1,
			'document1' => $docImage1,
			'document2' => $docImage2,
			'pf_enable' => $request->pf_enable,
			'security_deposit' => $request->security_deposit,
       ];

       $empdata = $employee->update_emp_details($request->empid, $items);
 // Employee general details insert end ==>
 
// Employee hire and fire details insert start ==>
       $item = [
        'emp_id' => $request->empid,
        'reason' => null,
        'date_of_joining' => $request->doj, 
        'date_of_firing' => null,
        'status_id' => 1,
       ];

        $joiningdata = $employee->update_emp_joining($request->joiningid,$item);
// Employee hire and fire details insert end ==>

// Employee shift details insert start ==>
       $item = [
        'emp_id' => $request->empid,
        'branch_id' => $request->branch,
        'department_id' => $request->department,
        'designation_id' => $request->designation,
        'shift_id' => $request->officeshift,
        'status_id' => 1, 
       ];
      
        $officeshift = $employee->update_emp_shift($request->shiftid,$item);
// Employee shift details insert end ==>

// Employee salary details insert start ==>
         $item = [
        'emp_id' => $request->empid,
        'basic_pay' => $request->basicpay,
		'pf_fund' => $request->pf_fund,
		'allowance' => $request->allowance,
		'gross_salary' => $request->grosspay,
        'salary_category_id' => $request->cat,
        'tax_applicable_id' => $request->tax,
        'inc_status_id' => 1, // 1 belongs to Hiring Status
        'status_id' => 1, 
       ];
      
        $salary = $employee->update_emp_salary($request->salaryid,$item);
// Employee salary details insert end ==>

// Employee overtime details insert start ==>        
        $item = [
        'emp_id' => $request->empid,
        'otamount_id' => $request->otamount,
        'otduration_id' => $request->otduration,
        'status_id' => 1,
       ];
        $overtime = $employee->update_emp_overttime($request->overtimeid,$item);
// Employee overtime details insert end ==>  

// Employee tax details insert start ==> 
if ($request->taxslab != "") {
  if ($request->tax == 0) {
     $getdata = $employee->gettaxdetails($request->empid);
  $taxdetails = $employee->taxdetails_delete($getdata[0]->id);
  }
  else{
    $annualsalary = ($request->basicpay * 12);
  $slab = $employee->gettaxslabrange($request->taxslab,$annualsalary,$annualsalary);

  $yearlyamt = (($annualsalary * $slab[0]->percentage) / 100);
  $monthamt = ($yearlyamt / 12);

         $item = [
        'emp_id' => $request->empid,
        'tax_id' => $request->taxslab,
        'tax_amount' => $monthamt,
       ];

      //exsists check for insert and update
    $exsists = $employee->taxdetails_exsists($request->empid);
    if ($exsists[0]->counts == 0) {
      $taxdetails = $employee->insert('tax_details',$item); 
    }
    else{
        $taxdetails = $employee->update_taxdetails($request->taxid,$item); 
    }
  
        }  
 }

// Employee tax details insert end ==>  

    return 1;
    }

      public function switchbranch(employee $employee){
      	$getemp = $employee->get_employees(1);
      	$getbranch = $employee->getbranches();
	 	return view('Employee.branch-switch', compact('getemp','getbranch'));	
    }

    public function branchupdate(employee $employee, Request $request){
    
    //check exsists
     $count = $employee->branch_chck($request->branchid,$request->shiftid,$request->empid);
     if ($count[0]->counts == 0) {
      //get shift id for update
      $shiftid = $employee->get_shiftid($request->empid);
      //Update office shift table and set Status = 2 (In-Active)
     	$result = $employee->change_branch($shiftid[0]->id,2);
      //Now Insert New Row in Office shift details
     	$items=[
        'emp_id' => $request->empid,
        'branch_id' => $request->branchid,
        'department_id' => $shiftid[0]->department_id,
        'designation_id' => $shiftid[0]->designation_id,
        'shift_id' => $request->shiftid,
        'status_id' => 1,
      ];
      $data = $employee->insert('employee_shift_details',$items);
      return 1;
     }
     else{
     	return 0;
     }


    }

        public function fireshow(employee $employee){
      	$getemp = $employee->get_employees(1);
	 	return view('Employee.fire-employee', compact('getemp'));	
    }

     public function getshifts(employee $employee, Request $request){
        $shifts = $employee->getofficeshift($request->branchid);
        return $shifts;
    }

    public function storecat(employee $employee, Request $request){
      $exsist = $employee->cat_chck($request->cat);
        if ($exsist[0]->counts == 0) {
            $item = [
        'category' => $request->cat,
       ];
        $cat = $employee->insert('salary_category',$item);

        $category = $employee->getcategory();
        return $category;
        }
        else{
          return 0;
        }
     
    }

    // public function view_holiday(employee $employee){
    //     $details = $employee->getholidays();
    // return view('Employee.view-holiday', compact('details')); 
    // }

    public function show_holiday(employee $employee){
        $getbranch = $employee->getbranches();
        $details = $employee->getholidays();
        // $emp = $employee->getmonthly_emp();
    return view('Employee.create-holiday', compact('getbranch','details')); 
    }


    public function getempmonthly(employee $employee, request $request){        
      $emp = $employee->getmonthly_emp($request->branchid);
    return $emp;
    }

    public function storeholiday(employee $employee, request $request){     

    // $exist = $employee->exist_holiday_chk($request->emp);
    // if ($exist[0]->counts == 0) {
      $item = [
        // 'emp_id' => $request->emp,
        'day_off' => $request->holiday, 
        'branch_id' => $request->branch,
       ];
      
        $holiday = $employee->insert("holidays",$item);

        $details = $employee->getholidays();
        return $details;
    //     }
    // else{
    //   return 0;
    // }
    }


     public function updateholiday(employee $employee, request $request){     
      
      $item = [
        'day_off' => $request->holiday, 
       ];
      
        $holiday = $employee->update_holiday($request->id,$item);

        $details = $employee->getholidays();
        return $details;
    }

     public function show_event(employee $employee){
        $getbranch = $employee->getbranches();
        $details = $employee->getevents();
    return view('Employee.create-event', compact('getbranch','details')); 
    }
     

    public function storeevents(employee $employee, request $request){     

    $exist = $employee->exist_event_chk($request->branch,$request->doe);
    if ($exist[0]->counts == 0) {
      $item = [
        'event_name' => $request->event, 
        'event_date' => $request->doe,
        'branch_id' => $request->branch,
       ];
      
        $event = $employee->insert("company_events",$item);

        $details = $employee->getevents();
        return $details;
        }
    else{
      return 0;
    }
    }


      public function updateevents(employee $employee, request $request){     
      
       $exist = $employee->exist_event_chk($request->branchmodal,$request->doemodal);
    if ($exist[0]->counts == 0) {
      $item = [
        'event_name' => $request->eventmodal, 
        'event_date' => $request->doemodal,
       ];
      
        $event = $employee->update_event($request->eventid,$item);

        $details = $employee->getevents();
        return $details;
        }
    else{
      return 0;
    }
    }

  public function deleteevents(employee $employee, request $request){
        
        $result = $employee->event_delete($request->id);
        return $result;
    }


    public function showqual(employee $employee){
    $getemp = $employee->getemployee(session("branch"));
    return view('Employee.create-education', compact('getemp')); 
    }

    public function storeeducation(employee $employee, request $request){
      $docName = "";
      $exsists = $employee->education_exsist($request->employee,$request->degree);
      if ($exsists[0]->counts == 0) {
      if(!empty($request->docimg)){
         $request->validate([
              'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
          ]);
          $docName = $request->degree.'.'.$request->docimg->getClientOriginalExtension();
          $request->docimg->move(public_path('assets/images/employees/documents/'), $docName);
         }
         $item = [
        'emp_id' => $request->employee, 
        'degree_name' => $request->degree,
        'institute_name' => $request->institute,
        'passing_year' => $request->passingyear,
        'document' => $docName,
       ];
        $result = $employee->insert('employee_education_details',$item);
        return 1;
        }
        else{
          return 0;
        }
    }

    public function getqual(employee $employee, Request $request){
      $details =$employee->geteducationdetails($request->employee);      
      return $details;
    }

       public function deletequal(employee $employee, Request $request){
      $result =$employee->educationdelete($request->id);      
      return $result;
    }




      public function updatequal(employee $employee, request $request){
$docName = "";
         if(!empty($request->docimgmodal)){
     $path = public_path('assets/images/employees/documents/').$request->oldimage;
            if(file_exists($path)){
                  @unlink($path);
                }
             $request->validate([
                 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                 ]);
             $docName = $request->degreemodal.'.'.$request->docimgmodal->getClientOriginalExtension();
          $request->docimgmodal->move(public_path('assets/images/employees/documents/'), $docName);

            }
            else{
                $docName = $request->oldimage;
            }
    
         $item = [
        'emp_id' => $request->employeemodal, 
        'degree_name' => $request->degreemodal,
        'institute_name' => $request->institutemodal,
        'passing_year' => $request->passingyearmodal,
        'document' => $docName,
       ];
        $result = $employee->update_educations($request->educationid,$item);
        return 1;
    }



    public function showallowances(employee $employee){
        $getemp = $employee->getemployee(session("branch"));
        $allowance = $employee->getallowancehead();
        return view('Employee.allowance', compact('getemp','allowance')); 
    }

      public function storeallowance(employee $employee, request $request){

        $exsist = $employee->allowanceheadexsists($request->allowance);
        if ($exsist[0]->counts == 0) {
        $item = [
        'allowance_name' => $request->allowance, 
       ];
        $result = $employee->insert("allowances",$item);
        $allowance = $employee->getallowancehead();
        return $allowance;
        }
        else{
          return 0;
        }
    }


  public function storeallowancedetails(employee $employee, request $request){

    

        $exsist = $employee->exsists_chk_allowance($request->allowancehead,$request->employee);
        if ($exsist[0]->counts == 0) {
        $item = [
        'emp_id' => $request->employee, 
        'allowance_id' => $request->allowancehead, 
        'amount' => $request->amount, 
        'status_id' => 1, 
       ];
        $result = $employee->insert("allowances_details",$item);
        $allowances = $employee->getallowancedetails(1,$request->employee);
        return $allowances;
        }
        else{
          return 0;
        }
    }

   public function allowancedetails(employee $employee, request $request){
        $details = $employee->getallowancedetails(1,$request->employee);
        return $details;
    }

   public function deleteallowancedetails(employee $employee, Request $request){
      $result =$employee->allowancedetails_delete($request->id);      
      return $result;
    }

    public function updateallowancedetails(employee $employee, request $request){
        $item = [
        'amount' => $request->amount, 
        'date' => date('Y-m-d')
       ];
        $result = $employee->update_allowance_details($request->id,$item);
        return 1;
    }

    public function showleaves(employee $employee){
        $getemp = $employee->getemployee(session("branch"));
        $leaves = $employee->getleaveshead();
        return view('Employee.leaves', compact('getemp','leaves')); 
    }

     public function storeleavehead(employee $employee, request $request){

        $exsist = $employee->leaveheadexsist($request->leavehead);
        if ($exsist[0]->counts == 0) {
        $item = [
        'leave_head' => $request->leavehead, 
       ];
        $result = $employee->insert("leaves",$item);
        $leaves = $employee->getleaveshead();
        return $leaves;
        }
        else{
          return 0;
        }
    }


     public function store_leavedetails(employee $employee, request $request){

        $exsist = $employee->exsists_chk_leavedetails($request->employee,$request->leavehead,$request->year);
        if ($exsist[0]->counts == 0) {
        $item = [
        'emp_id' => $request->employee, 
        'leave_id' => $request->leavehead, 
        'leave_qty' => $request->qty, 
        'balance' => $request->qty, 
        'year' => $request->year, 
        'status_id' => 1, 
       ];
        $result = $employee->insert("leaves_details",$item);
        $leaves = $employee->getleavesdetails(1,$request->employee);
        return $leaves;
        }
        else{
          return 0;
        }
    }

       public function leavesdetails(employee $employee, request $request){
        $leaves = $employee->getleavesdetails(1,$request->employee);
        return $leaves;
    }

   public function deleteleavesdetails(employee $employee, Request $request){
      $result =$employee->leavesdetails_delete($request->id);      
      return $result;
    }


   public function updateleavedetails(employee $employee, request $request){
        $item = [
        'leave_qty' => $request->qty, 
        'balance' => $request->qty, 
       ];
        $result = $employee->update_leaves_details($request->id,$item);
        return 1;
    }




    


     

}