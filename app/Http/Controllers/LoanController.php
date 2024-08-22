<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\loan;
use App\leave;
use App\salary;
use App\bank;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;

class LoanController extends Controller
{
	  public function __construct()
    {
        $this->middleware('auth');
    }

      public function view(loan $loan){
      	$rules = $loan->getrules();
	 	return view('Loan.loan-deduction', compact('rules'));	
    } 

     public function show(loan $loan){
	 	return view('Loan.create-rule', compact(0));	
    } 

       public function store(loan $loan, Request $request){

 		$rules = [
                'loan' => 'required',
            ];
             $this->validate($request, $rules);
       	$count = $loan->exist_check($request->loan);
       	if ($count[0]->counts == 0) {

       		$items = [
    		'Loan_Deduct_type' => $request->loan,
    	 ];
    	 $deduct = $loan->insert('Loan_Deduct_Type',$items);
    	 return $this->view($loan);
       	}
       	else{
       		return 0;
       	}

    } 

    public function insert(loan $loan, Request $request){

       	$count = $loan->exist_check($request->loan);
       	if ($count[0]->counts == 0) {

       		$items = [
    		'Loan_Deduct_type' => $request->loan,
    	 ];
    	 $deduct = $loan->insert('Loan_Deduct_Type',$items);
		 $getdeduct = $loan->getdeduction();
    	 return $getdeduct;
       	}
       	else{
       		return 0;
       	}

    } 

     public function deletededuct(loan $loan, Request $request){
     	$result = $loan->rule_delete($request->id);
     	return $result;
     }

       public function edit(loan $loan, Request $request){
       	$details = $loan->getbyid($request->id);
	 	return view('Loan.edit-rule', compact('details'));	
    } 

       public function updatededuct(loan $loan, Request $request){
       	$items = [
    		'Loan_Deduct_type' => $request->loan,
    	 ];
     	$result = $loan->update_loandeduct($request->loanid, $items);

     	 return $this->view($loan);
     }

      public function viewdetails(loan $loan){
        $details = $loan->getdetails(1);
	 	return view('Loan.loan-details', compact('details'));	
    } 

public function showloan(loan $loan, leave $leave,  Request $request){
   $getemp = $leave->getemployee(session("branch"));
	 	return view('Loan.issue-loan', compact('getemp'));	
    } 

          public function getempbybranch(loan $loan, Request $request){
       $employees = $loan->getemp_bybranch($request->branchid);
	 	return $employees;
    } 

     public function issueloan(loan $loan, bank $bank, salary $salary, Request $request){
       	$items = [
    		'emp_id' => $request->empid,
    		'loan_amount' => $request->amount,
    		'deduction_days' => $request->deduct,
    		'date' => $request->loandate,
    		'balance' => $request->amount,
    		'reason' => $request->reason,
    		'status_id' => 1,
            'deduction_amount' => $request->deductionamount,
    	 ];

         //check cash ledger
         $cash = $this->cashledger($bank,$request->amount,$request->reason);
         if ($cash == 1)
         {
             //insert in loan table
             $result = $loan->insert('loan_details', $items);
             //insert in employee ledger
             $empledger = $this->insert_emp_ledger($salary, $bank, $request->empid,$request->amount,$request->reason);
             return $empledger;
         }
         else{
             return $cash;
         }
    	 //this is for installment
//       $installment = 0;
//       $installment = $request->amount / $request->deduct;
//
//      $lastDate = date('Y-m-d', strtotime($request->loandate . " + ".($request->deduct - 1)." day"));
//
//    $period = CarbonPeriod::create($request->loandate, $lastDate);
//    foreach ($period as $key => $value) {
//
//        $installmentdate = date("Y-m-d",strtotime($value));
//
//        $items = [
//        'emp_id' => $request->empid,
//        'loan_id' => $result,
//        'installment_amount' =>$installment,
//        'date' => $installmentdate,
//        'status_id' => 1,
//       ];
//       $insta = $loan->insert('loan_installment', $items);
//        }

    } 

    
     public function getpreivousdetails(loan $loan, Request $request){
      $getbalance = $loan->getbalance($request->empid);
     	return $getbalance;
     }

       public function remove(loan $loan, Request $request){
      $result = $loan->remove_loan($request->loanid, $request->statusid);
      return $result;
     }

     public function getinstallments(loan $loan, Request $request){
      $insta = $loan->getinstallments($request->loanid);
      return $insta;
     }

      public function loandeduction(loan $loan, Request $request){
      $loanbalance = 0;
      $status = 0;
      $getbalance = $loan->getactivebalance($request->empid,$request->loanid);
      $loanbalance = $getbalance[0]->balance - $request->amount;
      if ($loanbalance == 0) {
          $status = 2;
      }
      else{
         $status = 1; 
      }
        $items = [
        'balance' => $loanbalance,
        'status_id' => $status,
       ];

       $result = $loan->status_update_loan($request->loanid,$items);
       
        $items = [
        'status_id' => 2,
       ];
       $insta = $loan->status_update_installment($request->instaid,$items);
      return 1;
    } 



      public function getdetails_loan_inactive(loan $loan){
        $details = $loan->getdetails(2);
    return $details;
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
            'narration' => "Loan :  ".$narration,
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
                    'narration' => "Loan :  ".$narration,
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

