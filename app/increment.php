<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Session;

class increment extends Model
{
	   public function getempidbysession()
    {
    	  $result = DB::select('SELECT IFNULL((SELECT emp_id FROM user_employees WHERE user_id = ?),0) AS emp_id',[session("userid")]);
        return $result;
    }

       public function getemployee($branchid)
    {
        if (session("roleId") == 2) {
         $result = DB::select('SELECT a.empid, a.emp_acc, a.emp_name, c.department_name, d.branch_name FROM employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid AND a.status_id = b.status_id INNER JOIN departments c ON c.department_id = b.department_id INNER JOIN branch d ON d.branch_id = b.branch_id WHERE a.status_id = 1 AND b.branch_id = ?',[session("branch")]);
        return $result;
        }
        else if (session("roleId") == 6) {

        	$getempid = $this->getempidbysession();
        
          $result = DB::select('SELECT a.empid, a.emp_acc, a.emp_name, c.department_name, d.branch_name FROM employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid AND a.status_id = b.status_id INNER JOIN departments c ON c.department_id = b.department_id INNER JOIN branch d ON d.branch_id = b.branch_id WHERE a.status_id = 1 AND b.branch_id = ? AND a.empid = ?',[$branchid,$getempid[0]->emp_id]);
        return $result;
        }
        else{
               $result = DB::select('SELECT a.empid, a.emp_acc, a.emp_name, c.department_name, d.branch_name FROM employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid AND a.status_id = b.status_id INNER JOIN departments c ON c.department_id = b.department_id INNER JOIN branch d ON d.branch_id = b.branch_id WHERE a.status_id = 1 AND b.branch_id = ?',[$branchid]);
        return $result;
        }
    }

    public function getbasicsal($empid){
         $result = DB::select('SELECT * FROM increment_details WHERE emp_id = ? AND status_id = 1',[$empid]);
        return $result;
    }

       public function gettaxslabs()
    {
          $result = DB::table('tax_slabs')->where('company_id',session("company_id"))->get();
        return $result;
    }

    public function gettaxslab_byempid($empid){
         $result = DB::select('SELECT * FROM tax_details WHERE id = (SELECT MAX(id) FROM tax_details WHERE emp_id = ?)',[$empid]);
        return $result;
    }

           public function getallowancedetails($empid)
    {
        $result = DB::select('SELECT c.emp_name, a.id, b.allowance_name, a.amount, a.date, d.status_name, b.allowance_id  FROM allowances_details a INNER JOIN allowances b ON b.allowance_id = a.allowance_id INNER JOIN employee_details c ON c.empid = a.emp_id AND a.status_id = c.status_id INNER JOIN accessibility_mode d ON d.status_id = a.status_id
            WHERE a.status_id = 1 AND a.emp_id = ?',[$empid]);
        return $result;
    }

      public function update_allowance_details($id,$items){
        $result = DB::table('allowances_details')->where('id', $id)->update($items);
        return $result;
    }

    public function insert($table,$items){
    
        $result = DB::table($table)->insertGetId($items);
       return $result;   
    }


    public function getsalarydetails($empid)
    {
        $result = DB::select('SELECT * FROM increment_details WHERE emp_id = ? AND status_id = 1',[$empid]);
        return $result;
    }

    public function update_increment($id,$items){
        $result = DB::table('increment_details')->where('increment_id', $id)->update($items);
        return $result;
    }

      public function gettaxdetails($empid,$firstdate,$todate)
    {
        $result = DB::select('SELECT COUNT(id) AS counts FROM tax_details WHERE emp_id = ? AND date BETWEEN ? AND ?',[$empid,$firstdate,$todate]);
        return $result;
    }


      public function gettaxid_update($empid,$firstdate,$todate)
    {
        $result = DB::select('SELECT * FROM tax_details WHERE emp_id = ? AND date BETWEEN ? AND ?',[$empid,$firstdate,$todate]);
        return $result;
    }


    public function update_taxdetails($id,$items){
        $result = DB::table('tax_details')->where('id', $id)->update($items);
        return $result;
    }

       public function getincrement_details($firstdate,$todate)
    {
        $result = DB::select('SELECT a.increment_id, a.emp_id, b.emp_acc, b.emp_name, (SELECT MAX(basic_pay) FROM increment_details WHERE emp_id = a.emp_id AND status_id = 2) AS previous_pay, a.basic_pay AS incremented_sal, CASE WHEN a.tax_applicable_id = 1 THEN (SELECT tax_amount FROM tax_details WHERE emp_id = a.emp_id AND date BETWEEN ? AND ?) ELSE 0 END AS tax_rate,  a.date FROM increment_details a INNER JOIN employee_details b ON b.empid = a.emp_id AND a.status_id = b.status_id WHERE a.inc_status_id = 2 AND b.status_id = 1',[$firstdate,$todate]);
        return $result;
    }


}