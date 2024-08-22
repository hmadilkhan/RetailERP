<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class advanceSalary extends Model
{

    public function getbranch()
    {
    	$branch = DB::table('branch')->get();
    	return $branch;
    }

      public function getemployee()
    {
    	$employees = DB::table('employee_details')->get();
    	return $employees;
    }

     public function getemp_bybranch($branchid,$category)
    {
    	$result = DB::select('SELECT a.*,c.salary_category_id FROM employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid INNER JOIN increment_details c on c.emp_id = a.empid WHERE b.branch_id = ? and c.salary_category_id = ?',[$branchid,$category]);
		return $result;
    }

    	public function insert($table,$items){
	
        $result = DB::table($table)->insertGetId($items);
       return $result;   
    }

         public function getpreviousdetails($empid)
    {
    	$result = DB::select('SELECT MAX(amount) AS advance, date FROM advance_salary WHERE status_id = 1 AND emp_id = ?',[$empid]);
		return $result;
    }

        public function getdetails($statusid)
    {
    	$result = DB::select('SELECT a.advance_id, b.emp_acc, b.empid,b.emp_name, d.branch_name, a.amount, a.date, a.reason, e.status_name FROM advance_salary a INNER JOIN employee_details b ON b.empid = a.emp_id INNER JOIN employee_shift_details c ON c.emp_id = b.empid AND c.status_id = b.status_id INNER JOIN branch d ON d.branch_id = c.branch_id INNER JOIN accessibility_mode e ON e.status_id = a.status_id WHERE a.status_id = ? AND c.branch_id = ?',[$statusid,session('branch')]);
		return $result;
    }

    public function getbasicsalary($empid)
    {
        $result = DB::select('SELECT b.basic_pay,b.gross_salary FROM employee_details a INNER JOIN increment_details b ON b.emp_id = a.empid AND a.status_id = b.status_id WHERE a.empid = ?',[$empid]);
        return $result;
    }
	
	public function getdetailsById($advanceId)
    {
    	$result = DB::select('SELECT a.advance_id, b.emp_acc, b.empid,b.emp_name, d.branch_name, a.amount, a.date, a.reason, e.status_name,a.created_at FROM advance_salary a INNER JOIN employee_details b ON b.empid = a.emp_id INNER JOIN employee_shift_details c ON c.emp_id = b.empid AND c.status_id = b.status_id INNER JOIN branch d ON d.branch_id = c.branch_id INNER JOIN accessibility_mode e ON e.status_id = a.status_id WHERE a.advance_id = ?',[$advanceId]);
		return $result;
    }

}