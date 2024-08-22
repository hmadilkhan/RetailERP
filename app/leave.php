<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class leave extends Model
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

       public function getleaveshead($empid)
    {
        $result = DB::select('SELECT b.leave_id, b.leave_head FROM leaves_details a INNER JOIN leaves b ON b.leave_id = a.leave_id WHERE emp_id = ? AND status_id = 1',[$empid]);
        return $result;
    }

      public function leavebalance($empid,$leaveid)
    {
        $result = DB::select('SELECT id, balance FROM leaves_details WHERE emp_id = ? AND status_id = 1 AND leave_id = ?',[$empid,$leaveid]);
        return $result;
    }


	public function insert($table,$items){
	
        $result = DB::table($table)->insertGetId($items);
       return $result;   
    }

    public function update_leaves_details($id,$items){
        $result = DB::table('leaves_details')->where('id', $id)->update($items);
        return $result;
    }


      public function exsist_chk_leavedetails($fromdate,$todate,$empid)
    {
        $result = DB::select('SELECT COUNT(id) AS counts from leaves_avail_details WHERE from_date BETWEEN ? AND ? AND to_date BETWEEN ? AND ? AND emp_id = ?',[$fromdate,$todate,$fromdate,$todate,$empid]);
        return $result;
    }


      public function getleavedetails($empid,$branchid)
    {

    	$clause = "";
    	if ($empid != "") {
    	$clause = "WHERE a.emp_id = ".$empid;	
    	}
    	else if ($branchid != "") {
    		$clause = "WHERE f.branch_id = ".$branchid;	
    	}
    	else{
    		$clause = "";
    	}
        // $result = DB::select('SELECT a.id, b.id as updateid, a.emp_id, c.emp_name, d.leave_head, b.leave_id, b.balance, a.days, a.from_date, a.to_date, e.leave_status, g.branch_name FROM leaves_avail_details a INNER JOIN leaves_details b ON b.leave_id = a.leave_id INNER JOIN employee_details c ON c.empid = a.emp_id INNER JOIN leaves d ON d.leave_id = a.leave_id INNER JOIN leave_status e ON e.leave_status_id = a.leave_status INNER JOIN employee_shift_details f ON f.emp_id = c.empid AND f.status_id = c.status_id  INNER JOIN branch g ON g.branch_id =f.branch_id WHERE b.status_id = 1 '.$clause.' GROUP BY a.id');
        // return $result;

          $result = DB::select('SELECT a.id, a.emp_id, c.leave_id, b.emp_name, c.leave_head, f.branch_name, a.days, a.from_date, a.to_date, d.leave_status, (SELECT id FROM leaves_details WHERE emp_id = a.emp_id AND leave_id = c.leave_id AND status_id = 1) AS updateid  FROM leaves_avail_details a INNER JOIN employee_details b ON b.empid = a.emp_id INNER JOIN leaves c ON c.leave_id = a.leave_id INNER JOIN leave_status d ON d.leave_status_id = a.leave_status INNER JOIN employee_shift_details e ON e.emp_id = b.empid AND e.status_id = b.status_id INNER JOIN branch f ON f.branch_id = e.branch_id '.$clause);
        return $result;
    }


       public function update_status($id,$items){
        $result = DB::table('leaves_avail_details')->where('id', $id)->update($items);
        return $result;
    }

      public function getbalance($empid)
    {
        $result = DB::select('SELECT * FROM leaves_details a INNER JOIN leaves b ON b.leave_id = a.leave_id WHERE a.status_id = 1 AND a.emp_id = ?',[$empid]);
        return $result;
    }


      public function check_absentdata($fromdate,$todate,$empid)
    {
        $result = DB::select('SELECT * FROM absent_details WHERE absent_date BETWEEN ? AND ? AND acc_no = ?',[$fromdate,$todate,$empid]);
        return $result;
    }

       public function delete_absent($id){
         if (DB::table('absent_details')->where('id',$id)->delete()) {
        return 1;
        }
        else{
        return 0;   
        }
    }




}