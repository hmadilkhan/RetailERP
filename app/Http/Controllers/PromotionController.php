<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\promotion;
use App\increment;
use App\employee;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DateTime;

class PromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(promotion $promotion){
        $details = $promotion->get_promotion_details();
        return view('Promotion.show-promotion', compact('details'));
    }


    public function show(increment $increment, promotion $promotion, employee $employee){
        $getemp = $increment->getemployee(session("branch"));
        $taxslabs= $increment->gettaxslabs();
        $otamount = $employee->getotamount();
        $category = $employee->getcategory();
        $otduration = $employee->getotduration();
        $allowance = $employee->getallowancehead();
        return view('Promotion.create-promotion', compact('getemp','taxslabs','otamount','category','otduration','allowance'));
    }


    public function getoldetails(promotion $promotion, Request $request){
        $details = $promotion->getdetailsforpromotion($request->empid);
        return $details;
    }

    public function getdesigbyempid(promotion $promotion, Request $request){
        $details = $promotion->getdesig_acctodepart($request->empid);
        return $details;
    }

    public function promote_employee(promotion $promotion, employee $employee, increment $increment, Request $request){

//designation updated
        if ($request->desg != "") {

            $getidshift = $employee->getidforupdate_empshift($request->empid);
            $shiftupdate = $employee->remove_emp_shift($getidshift[0]->id,2);

            $item = [
                'emp_id' => $request->empid,
                'branch_id' => $getidshift[0]->branch_id,
                'department_id' => $getidshift[0]->department_id,
                'designation_id' => $request->desg,
                'shift_id' => $getidshift[0]->shift_id,
                'status_id' => 1,
            ];

            $officeshift = $employee->insert('employee_shift_details',$item);
        }

//designation updated

        //Salary Catgory Updated
        if ($request->salcat != "" && $request->basicpay != "") {

            $getidsalary = $employee->getidforupdate_salary($request->empid);
            $salaryupdate = $employee->remove_emp_salary($getidsalary[0]->increment_id,2);

            $item = [
                'emp_id' => $request->empid,
                'basic_pay' => $request->basicpay,
                'salary_category_id' => $request->salcat,
                'tax_applicable_id' => $getidsalary[0]->tax_applicable_id,
                'inc_status_id' => 3, // 3 belongs to Promotion Status
                'status_id' => 1,
            ];

            $salary = $employee->insert('increment_details',$item);

        }
//Salary Catgory Updated

        //Overtime details updated
        if ($request->otamount != "" && $request->otduration != "") {
            $getidovertime = $employee->getidforupdate_overtime($request->empid);
            $overtimeupdate = $employee->remove_emp_overtime($getidovertime[0]->id,2);

            $item = [
                'emp_id' => $request->empid,
                'otamount_id' => $request->otamount,
                'otduration_id' => $request->otduration,
                'status_id' => 1,
            ];
            $overtime = $employee->insert('employee_overtime_details',$item);

        }
        //Overtime details updated

//tax details update
        if ($request->taxslab != "") {

            $last_day = date('Y-m-t', strtotime(date('Y-m-d')));
            $first_day = date('Y-m-01', strtotime(date('Y-m-d')));

            $exsits = $increment->gettaxdetails($request->empid,$first_day,$last_day);

            $gettaxper = $promotion->get_tax_percentage($request->empid);
            $annualsal =  ($request->basicpay * 12);
            $taxamt = ($annualsal * $gettaxper[0]->percentage) / 100;

            if ($exsits[0]->counts == 0) {

                $item = [
                    'emp_id' => $request->empid,
                    'tax_id' => $request->taxslab,
                    'tax_amount' => $taxamt,
                ];
                $taxdetails = $increment->insert('tax_details',$item);
            }
            else{
                $taxid = $increment->gettaxid_update($request->empid,$first_day,$last_day);

                $item = [
                    'tax_id' => $request->taxslab,
                    'tax_amount' => $taxamt,
                    'date' => date('Y-m-d'),
                ];
                $result = $increment->update_taxdetails($taxid[0]->id,$item);
            }
        }
//tax details update
        $items=[
            'emp_id' => $request->empid,
            'designation' => ($request->desg == "" ? 0 : 1),
            'basic_salary' => ($request->basicpay == "" ? 0 : 1),
            'salary_category' => ($request->salcat == "" ? 0 : 1),
            'allowance' => ($request->allowance == 0 ? 0 : 1),
            'tax' => ($request->taxslab == "" ? 0 : 1),
            'overtime' => ($request->otamount == "" ? 0 : 1),
        ];
        $promtion = $employee->insert('promotion_details',$items);
        return 1;
    }

}
