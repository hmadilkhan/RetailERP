<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class bonus extends Model
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

      public function insert($table,$items){
    
        $result = DB::table($table)->insertGetId($items);
       return $result;   
    }

     public function bonus_exsist($empid,$first,$last){
         $result = DB::select('SELECT COUNT(bonus_id) AS counts FROM bonus_details WHERE emp_id = ? AND status_id = 1 AND date BETWEEN ? AND ?',[$empid,$first,$last]);
        return $result;
    }

   public function bonus_details(){
         $result = DB::select('SELECT a.bonus_id, a.emp_id, b.emp_acc, b.emp_name, c.basic_pay, a.bonus_amt, a.bonus_percentage, (c.basic_pay + a.bonus_amt) AS net_amt, a.reason, a.date, d.status_name FROM bonus_details a INNER JOIN employee_details b ON b.empid = a.emp_id  INNER JOIN increment_details c ON c.emp_id = a.emp_id AND c.status_id = a.status_id INNER JOIN accessibility_mode d ON d.status_id = a.status_id');
        return $result;
    }

     public function delete_bonus($id)
   {
      if(DB::table('bonus_details')->where('bonus_id',$id)->delete())
      {

        return 1;

      }else{

        return 0;
        
      }
   }

     public function bonus_details_byid($bonusid){
         $result = DB::select('SELECT a.bonus_id, a.emp_id, b.emp_acc, b.emp_name, c.basic_pay, a.bonus_amt, a.bonus_percentage, (c.basic_pay + a.bonus_amt) AS net_amt, a.reason, a.date, d.status_name FROM bonus_details a INNER JOIN employee_details b ON b.empid = a.emp_id AND a.status_id = b.status_id INNER JOIN increment_details c ON c.emp_id = a.emp_id AND c.status_id = a.status_id INNER JOIN accessibility_mode d ON d.status_id = a.status_id WHERE a.bonus_id = ?',[$bonusid]);
        return $result;
    }

      public function update_bonus_details($id,$items){
        $result = DB::table('bonus_details')->where('bonus_id', $id)->update($items);
        return $result;
    }


}