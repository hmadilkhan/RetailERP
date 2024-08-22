<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\advanceSalary;
use App\leave;
use App\salary;
use App\bank;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdvanceSalaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(advanceSalary $advsal){
        $details = $advsal->getdetails(1);
        return view('Advance_Salary.list-advancesal', compact('details'));
    }


    public function show(advanceSalary $advsal, leave $leave){
		$branches = DB::table("branch")->where("company_id",session("company_id"))->get();
		$categories = DB::table("salary_category")->get();
        $getemp = [];//$leave->getemployee(session("branch"));
        return view('Advance_Salary.issue-advancesal', compact('getemp','branches','categories'));
    }

    public function getempbybranch(advanceSalary $advsal, Request $request){
        $employees = $advsal->getemp_bybranch($request->branchid,$request->category);
        return $employees;
    }

    public function store(advanceSalary $advsal, Request $request, salary $salary, bank $bank)
	{
		
		foreach($request->empid as $emp)
		{
			$basicsal = $advsal->getbasicsalary($emp);
			// if ((float)$basicsal[0]->basic_pay >= (float)$request->amount)
			// {
				 $items = [
					'amount' => $request->amount,
					'date' => $request->date,
					'reason' => $request->reason,
					'emp_id' => $emp,//$request->empid,
					'status_id' => 1,
				];

				 //check cash ledger
				$cash = $this->cashledger($bank,$request->amount,$request->reason);
				if ($cash == 1)
				{
					//insert in advance table
					$result = $advsal->insert('advance_salary', $items);
					//insert in employee ledger
					$empledger = $this->insert_emp_ledger($salary, $bank, $emp,$request->amount,$request->reason);
					// return $empledger;
				}
				else{
					// return $cash;
				}
			// }
			// else{
				// return  0;
			// }
		}
		return 1;
    }

    public function getpreivousdetails(advanceSalary $advsal, Request $request){
        $result = $advsal->getpreviousdetails($request->empid);
        return $result;
    }


    public function getinactivedetails(advanceSalary $advsal){
        $details = $advsal->getdetails(2);
        return $details;
    }

    public function getbasicsalary(advanceSalary $advsal, Request $request){
        $result = $advsal->getbasicsalary($request->empid);
        return $result;
    }

    public function insert_emp_ledger(salary $salary, bank $bank, $empid,$amount,$narration){

        $prebal = $salary->getpreviousbalance($empid);
          //first time null ae ga
            if (empty($prebal))
            {
                $bal = $amount;
            }
            //agar balance minus me ae ga to
            else if ($prebal[0]->balance <= 0) {
                $bal = $prebal[0]->balance - $amount;

            }
            //agar balance positive me ae ga to
            else{
                $bal = $prebal[0]->balance + $amount;

            }

            $items=[
                'emp_id' =>$empid,
                'debit' => 0,
                'credit' =>$amount,
                'balance' => $bal,
                'narration' => "Advance Salary :  ".$narration,
            ];
            $ledger = $salary->insert('employee_ledger',$items);
            return 1;
    }

    public function cashledger(bank $bank,$amount,$narration){

        $balanceStock = $bank->getLastCashBalance();

        if(sizeof($balanceStock) > 0) {
            if ($balanceStock[0]->balance >= $amount)
            {
                $balance = $balanceStock[0]->balance -  $amount;
                $items = [
                    'branch_id' => session('branch'),
                    'date' => date("Y-m-d"),
                    'debit' => $amount,
                    'credit' => 0,
                    'balance' => $balance,
                    'narration' => "Advance Salary :  ".$narration,
                ];
                $result = $bank->insert_bankdetails('cash_ledger', $items);

                return 1;
            }else{
                return 3;
            }
        }
        else{
            return 3;
        }
    }


}
