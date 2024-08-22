<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class loan extends Model
{

	public function insert($table,$items){
	
        $result = DB::table($table)->insertGetId($items);
       return $result;   
    }

        public function exist_check($loandeduct)
    {
    	$result = DB::select('SELECT COUNT(Loan_Deduct_Type_Id) as counts FROM Loan_Deduct_Type WHERE loan_deduct_type = ?',[$loandeduct]);
		return $result;
    }

    public function getrules()
    {
    	$rules = DB::table('Loan_Deduct_Type')->get();
    	return $rules;
    }

    public function rule_delete($id)
	{
		if (DB::table('Loan_Deduct_Type')->where('Loan_Deduct_Type_Id',$id)->delete()) {
		return 1;
		}
		else{
		return 0;	
		}
		
	}

	 public function getbyid($id)
    {
    	$result = DB::select('SELECT * FROM loan_deduct_type WHERE Loan_Deduct_Type_Id = ?',[$id]);
		return $result;
    }

    	public function update_loandeduct($id,$items){
		$result = DB::table('loan_deduct_type')->where('Loan_Deduct_Type_Id', $id)->update($items);
    	return $result;
	}


    public function getbranch()
    {
    	$branch = DB::table('branch')->get();
    	return $branch;
    }

 


         public function getdeduction()
    {
    	$deduction = DB::table('loan_deduct_type')->get();
    	return $deduction;
    }

      public function getbalance($empid)
    {
    	$result = DB::select('SELECT MAX(balance) as balance, date FROM loan_details WHERE status_id = 1 AND emp_id = ?',[$empid]);
		return $result;
    }

      public function getdetails($statusid)
    {
    	$result = DB::select('SELECT a.loan_id,b.empid, b.emp_acc, b.emp_name, d.branch_name, a.loan_amount, a.date, a.balance, e.status_name FROM loan_details a INNER JOIN employee_details b ON b.empid = a.emp_id INNER JOIN employee_shift_details c ON c.emp_id = b.empid AND c.status_id = b.status_id INNER JOIN branch d ON d.branch_id = c.branch_id INNER JOIN accessibility_mode e ON e.status_id = a.status_id WHERE a.status_id = ? AND c.branch_id = ?',[$statusid, session('branch') ]);
		return $result;
    }

        public function remove_loan($id,$statusid){
		$result = DB::table('loan_details')->where('loan_id', $id)->update(['status_id'=>$statusid]);
    	return $result;
	}

    public function getinstallments($loanid)
    {
        $result = DB::select('SELECT b.emp_name, a.installment_amount, a.date, c.status_name FROM loan_installment a INNER JOIN employee_details b ON b.empid = a.emp_id
            INNER JOIN accessibility_mode c ON c.status_id = a.status_id WHERE a.loan_id = ?',[$loanid]);
        return $result;
    }

      public function getactivebalance($empid,$loanid)
    {
        $result = DB::select('SELECT balance FROM loan_details WHERE status_id = 1 AND emp_id = ? AND loan_id = ?',[$empid,$loanid]);
        return $result;
    }

    public function status_update_loan($id,$items){
        $result = DB::table('loan_details')->where('loan_id', $id)->update($items);
        return $result;
    }
    public function status_update_installment($id,$items){
        $result = DB::table('loan_installment')->where('installment_id', $id)->update($items);
        return $result;
    }
	
	public function getdetailsById($loanId)
    {
    	$result = DB::select('SELECT a.loan_id,b.empid, b.emp_acc, b.emp_name, d.branch_name, a.loan_amount, a.date, a.balance, e.status_name,a.created_at,a.reason FROM loan_details a INNER JOIN employee_details b ON b.empid = a.emp_id INNER JOIN employee_shift_details c ON c.emp_id = b.empid AND c.status_id = b.status_id INNER JOIN branch d ON d.branch_id = c.branch_id INNER JOIN accessibility_mode e ON e.status_id = a.status_id WHERE a.loan_id = ?',[$loanId]);
		return $result;
    }






}
