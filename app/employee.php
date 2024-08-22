<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class employee extends Model
{

    public function getbranches()
    {
        if (session("roleId") == 2) {
          $result = DB::table('branch')->where('company_id',session("company_id"))->where('status_id',1)->get();
        return $result;
        }else{
              $result = DB::table('branch')->where('branch_id',session("branch"))->where('status_id',1)->get();
        return $result;
        }
    }

       public function getemployee($branchid)
    {
        if (session("roleId") == 2) {
         $result = DB::select('SELECT * FROM employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid AND a.status_id = b.status_id WHERE a.status_id = 1 AND b.branch_id IN (Select branch_id from branch where company_id = ?)',[session("company_id")]);
        return $result;
        }else{
               $result = DB::select('SELECT * FROM employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid AND a.status_id = b.status_id WHERE a.status_id = 1 AND b.branch_id = ?',[$branchid]);
				return $result;
        }
    }

     public function gettaxslabs()
    {
          $result = DB::table('tax_slabs')->where('company_id',session("company_id"))->get();
        return $result;
    }


      public function getofficeshift($branchid)
    {
        $shifts = DB::table('office_shift')->where('branch_id',$branchid)->get();
        return $shifts;
    }
       public function getotformula()
    {
        $ot = DB::table('overtime_formula')->get();
        return $ot;
    }
      public function getotamount()
    {
        $ot = DB::table('overtime_amount')->get();
        return $ot;
    }
    public function getotduration()
    {
        $ot = DB::table('overtime_duration')->get();
        return $ot;
    }


     public function getdesg($departid)
    {
         $result = DB::select('SELECT * FROM designation WHERE department_id = ?',[$departid]);
        return $result;
    }

    public function getcategory()
    {
        $category = DB::table('salary_category')->get();
        return $category;
    }

           public function getdepart($branchid)
    {
    	$result = DB::select('SELECT a.department_id, b.branch_name, a.department_name FROM departments a INNER JOIN branch b ON b.branch_id = a.branch_id where a.branch_id = ?',[$branchid]);
		return $result;
    }

              public function empAcc_exist($empacc, $statusid)
    {
    	$result = DB::select('SELECT COUNT(a.emp_acc) AS counts FROM employee_details a INNER JOIN employee_shift_details b on b.emp_id = a.empid WHERE a.emp_acc = ? and b.branch_id = ?  AND a.status_id = ?',[$empacc,session("branch"),$statusid]);
		return $result;
    }

     public function empAcc_check($empacc)
    {
    	$result = DB::select('SELECT a.emp_acc, a.emp_name FROM employee_details a INNER JOIN employee_shift_details b on b.emp_id = a.empid WHERE a.emp_acc = ? and b.branch_id = ? ',[$empacc,session("branch")]);
		return $result;
    }


	public function insert($table,$items){
	
        $result = DB::table($table)->insertGetId($items);
       return $result;   
    }

    public function get_employees($statusid)
    {
         if (session("roleId") == 2) {
    	$result = DB::select('SELECT a.emp_picture, a.empid, a.emp_acc, a.emp_name, a.emp_contact, c.branch_name, d.department_name, e.designation_name, f.status_name, g.shiftname,g.ATT_time, h.id  FROM employee_details a 
            INNER JOIN employee_shift_details b ON b.emp_id = a.empid and b.status_id = a.status_id
            INNER JOIN branch c ON c.branch_id = b.branch_id
            INNER JOIN departments d ON d.department_id = b.department_id
            INNER JOIN designation e ON e.designation_id = b.designation_id
            INNER JOIN accessibility_mode f ON f.status_id = a.status_id
            INNER JOIN office_shift g ON g.shift_id = b.shift_id
            LEFT JOIN employee_fire_details h ON h.emp_id = a.empid AND h.status_id = a.status_id
            WHERE a.status_id = ? AND c.company_id = ? GROUP BY a.empid',[$statusid,session('company_id')]);
		return $result;
        }
    else{
        $result = DB::select('SELECT a.emp_picture, a.empid, a.emp_acc, a.emp_name, a.emp_contact, c.branch_name, d.department_name, e.designation_name, f.status_name, g.shiftname,g.ATT_time, h.id  FROM employee_details a 
            INNER JOIN employee_shift_details b ON b.emp_id = a.empid and b.status_id = a.status_id
            INNER JOIN branch c ON c.branch_id = b.branch_id
            INNER JOIN departments d ON d.department_id = b.department_id
            INNER JOIN designation e ON e.designation_id = b.designation_id
            INNER JOIN accessibility_mode f ON f.status_id = a.status_id
            INNER JOIN office_shift g ON g.shift_id = b.shift_id
            INNER JOIN employee_fire_details h ON h.emp_id = a.empid AND h.status_id = a.status_id
            WHERE a.status_id = ? AND c.branch_id = ? GROUP BY a.empid',[$statusid,session('branch')]);
        return $result;
        }
    }

  //    public function get_employees_inactive()
  //   {
  //   	$result = DB::select('SELECT a.empid, a.emp_acc, a.emp_name, a.emp_picture, a.emp_contact, c.branch_name, d.department_name, e.designation_name, f.status_name
		// 	FROM employee_details a INNER JOIN employee_office_details b ON b.emp_id = a.empid
		// 	INNER JOIN branch c ON c.branch_id = b.branch_id
		// 	INNER JOIN departments d ON d.department_id = b.department_id
		// 	INNER JOIN designation e on e.designation_id = b.designation_id
  //           INNER JOIN accessibility_mode f ON f.status_id = a.status_id
		// 	WHERE a.status_id = 2');
		// return $result;
  //   }



     public function get_employee_byid($empid)
    {
    	$result = DB::select('SELECT a.*, c.branch_name, d.shiftname, e.department_name, f.designation_name, g.*, h.*, i.category, k.amount, l.duration, m.status_name, e.department_id, c.branch_id, h.tax_applicable_id, g.id as joiningid, h.increment_id AS salaryid, j.id AS overtimeid, b.id AS shiftid,a.pf_enable,a.security_deposit FROM employee_details a
        INNER JOIN employee_shift_details b ON b.emp_id = a.empid
        INNER JOIN branch c ON c.branch_id = b.branch_id
        INNER JOIN office_shift d ON d.shift_id = b.shift_id
        INNER JOIN departments e ON e.department_id = b.department_id
        INNER JOIN designation f ON f.designation_id = b.designation_id
        LEFT JOIN employee_fire_details g ON g.emp_id = a.empid
        INNER JOIN increment_details h ON h.emp_id = a.empid
        INNER JOIN salary_category i ON i.id = h.salary_category_id
        INNER JOIN employee_overtime_details j ON j.emp_id = a.empid
        INNER JOIN overtime_amount k ON k.otamount_id = j.otamount_id
        INNER JOIN overtime_duration l ON l.otduration_id = j.otduration_id
        INNER JOIN accessibility_mode m ON m.status_id = a.status_id
        WHERE a.empid = ? GROUP BY a.empid',[$empid]);
		return $result;
    }

    	public function update_emp_details($id,$items){
		$result = DB::table('employee_details')->where('empid', $id)->update($items);
    	return $result;
	}



      public function update_emp_salary($id,$items){
        $result = DB::table('increment_details')->where('increment_id', $id)->update($items);
        return $result;
    }

         public function update_emp_shift($id,$items){
        $result = DB::table('employee_shift_details')->where('id', $id)->update($items);
        return $result;
    }

          public function update_emp_overttime($id,$items){
        $result = DB::table('employee_overtime_details')->where('id', $id)->update($items);
        return $result;
    }

             public function update_taxdetails($id,$items){
        $result = DB::table('tax_details')->where('id', $id)->update($items);
        return $result;
    }



	   public function branch_chck($branchid,$shiftid,$empid)
    {
    	$result = DB::select('SELECT COUNT(id) AS counts FROM employee_shift_details WHERE branch_id = ? AND shift_id = ? AND status_id = 1 AND emp_id = ?',[$branchid,$shiftid,$empid]);
		return $result;
    }

       public function get_shiftid($empid)
    {
        $result = DB::select('SELECT * FROM employee_shift_details WHERE  status_id = 1 AND emp_id = ?',[$empid]);
        return $result;
    }

      public function change_branch($id,$statusid){
		$result = DB::table('employee_shift_details')->where('id', $id)->update(['status_id'=>$statusid]);
    	return $result;
	}

        public function getperviousdata($empid)
    {
        $getdata = DB::table('employee_fire_details')->where('emp_id',$empid)->get();
        return $getdata;
    }


       public function cat_chck($cat)
    {
        $result = DB::select('SELECT COUNT(id) as counts FROM salary_category WHERE category = ?',[$cat]);
        return $result;
    }

     public function getholidays()
    {
        $result = DB::select('SELECT a.id, a.day_off, b.branch_name FROM holidays a INNER JOIN branch b ON b.branch_id = a.branch_id WHERE b.branch_id = ?',[session('branch')]);
        return $result;
    }

     public function getmonthly_emp($branchid)
    {
        $result = DB::select('SELECT a.empid, a.emp_name FROM employee_details a INNER JOIN employee_office_details b ON b.emp_id = a.empid WHERE b.salary_category = 2 AND b.branch_id = ? AND a.status_id = 1',[$branchid]);
        return $result;
    }

      public function exist_holiday_chk($empid)
    {
        $result = DB::select('SELECT COUNT(emp_id) AS counts FROM holidays WHERE emp_id = ?',[$empid]);
        return $result;
    }

       public function update_holiday($id,$items){
        $result = DB::table('holidays')->where('id', $id)->update($items);
        return $result;
    }

      public function exsist_office_shift($shiftid,$empid)
    {
        $result = DB::select('SELECT COUNT(id) AS counts FROM employee_shift_details WHERE shift_id = ? AND status_id = 1 AND emp_id = ?',[$shiftid,$empid]);
        return $result;
    }

        public function getoldshift($empid)
    {
        $result = DB::select('SELECT * FROM employee_shift_details WHERE status_id = 1 AND emp_id = ?',[$empid]);
        return $result;
    }

           public function update_officeshift($id,$items){
        $result = DB::table('employee_shift_details')->where('id', $id)->update($items);
        return $result;
    }

      public function getevents()
    {
        $result = DB::select('SELECT b.branch_id, a.event_id, a.event_name, a.event_date, b.branch_name FROM company_events a INNER JOIN branch b ON b.branch_id = a.branch_id WHERE a.branch_id = ?',[session('branch')]);
        return $result;
    }


      public function exist_event_chk($branchid,$date)
    {
        $result = DB::select('SELECT COUNT(event_id) as counts from company_events WHERE branch_id = ? AND event_date = ?',[$branchid,$date]);
        return $result;
    }

        public function update_event($id,$items){
        $result = DB::table('company_events')->where('event_id', $id)->update($items);
        return $result;
    }

      public function event_delete($id)
    {
        if (DB::table('company_events')->where('event_id',$id)->delete()) {
        return 1;
        }
        else{
        return 0;   
        }
        
    }

        public function gettaxslabrange($taxid,$salarymin,$salarymax)
    {
        $result = DB::select('SELECT * FROM tax_slabs WHERE tax_id = ? AND status_id = 1 AND slab_min <= ? AND slab_max >= ?',[$taxid,$salarymin,$salarymax]);
        return $result;
    }

         public function gettaxdetails($empid)
    {
        $result = DB::select('SELECT * FROM tax_details WHERE emp_id = ?',[$empid]);
        return $result;
    }



        public function taxdetails_delete($id)
    {
        if (DB::table('tax_details')->where('id',$id)->delete()) {
        return 1;
        }
        else{
        return 0;   
        }
        
    }

           public function taxdetails_exsists($empid)
    {
        $result = DB::select('SELECT COUNT(id) AS counts FROM tax_details WHERE emp_id = ?',[$empid]);
        return $result;
    }

      public function education_exsist($empid,$degreename)
    {
        $result = DB::select('SELECT COUNT(education_id) AS counts FROM employee_education_details WHERE emp_id = ? AND degree_name = ?',[$empid,$degreename]);
        return $result;
    }


      public function geteducationdetails($empid)
    {
        $result = DB::select('SELECT * FROM employee_education_details a INNER JOIN employee_details b ON b.empid = a.emp_id WHERE a.emp_id = ?',[$empid]);
        return $result;
    }


      public function educationdelete($id)
    {
        if (DB::table('employee_education_details')->where('education_id',$id)->delete()) {
        return 1;
        }
        else{
        return 0;   
        }
        
    }

    public function update_educations($id,$items){
        $result = DB::table('employee_education_details')->where('education_id', $id)->update($items);
        return $result;
    }

       public function getallowancehead()
    {
        $result = DB::table('allowances')->get();
        return $result;
    }


     public function allowanceheadexsists($allowance)
    {
        $result = DB::select('SELECT COUNT(allowance_id) AS counts FROM allowances WHERE allowance_name = ?',[$allowance]);
        return $result;
    }

      public function exsists_chk_allowance($allowance,$empid)
    {
        $result = DB::select('SELECT COUNT(id) AS counts FROM allowances_details WHERE allowance_id = ? AND emp_id = ? AND status_id = 1',[$allowance,$empid]);
        return $result;
    }

       public function getallowancedetails($statsid,$empid)
    {
        $result = DB::select('SELECT c.emp_name, a.id, b.allowance_name, a.amount, d.status_name  FROM allowances_details a INNER JOIN allowances b ON b.allowance_id = a.allowance_id INNER JOIN employee_details c ON c.empid = a.emp_id AND a.status_id = c.status_id INNER JOIN accessibility_mode d ON d.status_id = a.status_id
            WHERE a.status_id = ? AND a.emp_id = ?',[$statsid,$empid]);
        return $result;
    }

         public function allowancedetails_delete($id)
    {
        if (DB::table('allowances_details')->where('id',$id)->delete()) {
        return 1;
        }
        else{
        return 0;   
        }
        
    }

        public function update_allowance_details($id,$items){
        $result = DB::table('allowances_details')->where('id', $id)->update($items);
        return $result;
    }




          public function getleaveshead()
    {
        $result = DB::table('leaves')->get();
        return $result;
    }

      public function leaveheadexsist($leave)
    {
        $result = DB::select('SELECT COUNT(leave_id) AS counts FROM leaves WHERE leave_head = ?',[$leave]);
        return $result;
    }


          public function exsists_chk_leavedetails($empid,$leaveid,$year)
    {
        $result = DB::select('SELECT COUNT(id) AS counts from leaves_details WHERE emp_id = ? AND leave_id = ? AND year = ? AND status_id = 1',[$empid,$leaveid,$year]);
        return $result;
    }

        public function getleavesdetails($statsid,$empid)
    {
        $result = DB::select('SELECT a.id, b.emp_name, c.leave_head, a.leave_qty, a.balance, a.year, d.status_name FROM leaves_details a INNER JOIN employee_details b ON b.empid = a.emp_id INNER JOIN leaves c ON c.leave_id = a.leave_id INNER JOIN accessibility_mode d ON d.status_id = a.status_id WHERE a.status_id = ? AND a.emp_id = ?',[$statsid,$empid]);
        return $result;
    }

      public function leavesdetails_delete($id)
    {
        if (DB::table('leaves_details')->where('id',$id)->delete()) {
        return 1;
        }
        else{
        return 0;   
        }
        
    }

         public function update_leaves_details($id,$items){
        $result = DB::table('leaves_details')->where('id', $id)->update($items);
        return $result;
    }



    // ============ This is Firing process of Employee =======

    public function update_emp_joining($id,$items){
        $result = DB::table('employee_fire_details')->where('id', $id)->update($items);
        return $result;
    }

    public function remove_emp($id,$statusid){
        $result = DB::table('employee_details')->where('empid', $id)->update(['status_id'=>$statusid]);
        return $result;
    }

    public function getidforupdate_empshift($empid)
    {
        $result = DB::select('SELECT * FROM employee_shift_details WHERE emp_id = ? AND status_id = 1',[$empid]);
        return $result;
    }

    public function remove_emp_shift($id,$statusid){
        $result = DB::table('employee_shift_details')->where('id', $id)->update(['status_id'=>$statusid]);
        return $result;
    }


     public function getidforupdate_overtime($empid)
    {
        $result = DB::select('SELECT * FROM employee_overtime_details WHERE emp_id = ? AND status_id = 1',[$empid]);
        return $result;
    }

      public function remove_emp_overtime($id,$statusid){
        $result = DB::table('employee_overtime_details')->where('id', $id)->update(['status_id'=>$statusid]);
        return $result;
    }

     public function getidforupdate_leaves($empid)
    {
        $result = DB::select('SELECT * FROM leaves_details WHERE emp_id = ? AND status_id = 1',[$empid]);
        return $result;
    }

    public function remove_emp_leaves($id,$statusid){
        $result = DB::table('leaves_details')->where('id', $id)->update(['status_id'=>$statusid]);
        return $result;
    }


     public function getidforupdate_allowances($empid)
    {
        $result = DB::select('SELECT * FROM allowances_details WHERE emp_id = ? AND status_id = 1',[$empid]);
        return $result;
    }

      public function remove_emp_allowances($id,$statusid){
        $result = DB::table('allowances_details')->where('id', $id)->update(['status_id'=>$statusid]);
        return $result;
    }

        public function getidforupdate_salary($empid)
    {
        $result = DB::select('SELECT * FROM increment_details WHERE emp_id = ? AND status_id = 1',[$empid]);
        return $result;
    }

       public function remove_emp_salary($id,$statusid){
        $result = DB::table('increment_details')->where('increment_id', $id)->update(['status_id'=>$statusid]);
        return $result;
    }

// ============  Firing process of Employee End =======

// ============  Re Hiring Process of Employee Start =======
    
      public function getMAXidforupdate_salary($empid)
    {
        $result = DB::select('SELECT * FROM increment_details WHERE increment_id = (SELECT MAX(increment_id) FROM increment_details WHERE emp_id = ? AND status_id = 2)',[$empid]);
        return $result;
    }

      public function getMAXidforupdate_shift($empid)
    {
        $result = DB::select('SELECT * FROM employee_shift_details WHERE id = (SELECT MAX(id) FROM employee_shift_details WHERE emp_id = ? AND status_id = 2)',[$empid]);
        return $result;
    }

        public function getMAXidforupdate_overtime($empid)
    {
        $result = DB::select('SELECT * FROM employee_overtime_details WHERE id = (SELECT MAX(id) FROM employee_overtime_details WHERE emp_id = ? AND status_id = 2)',[$empid]);
        return $result;
    }
// ============  Re Hiring Process of Employee End =======



    public function getpermissions(){
        $result = DB::table('hr_permission')->where('company_id', session('company_id'))->get();
        return $result;
    }







}

