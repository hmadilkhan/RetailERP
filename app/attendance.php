<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class attendance extends Model
{

    public function getbranch()
    {
    	$branch = DB::table('branch')->where('company_id',session('company_id'))->get();
    	return $branch;
    }

    	public function insert($table,$items){
		$result = DB::table($table)->insertGetId($items);
       return $result;   
	}

    public function getshifts(){
		$filter = "";
		if (session("roleId") == 2) {
			$filter = " b.branch_id IN (Select branch_id from branch where company_id = ".session('company_id')." )";
		}else{
			$filter = " b.branch_id = ?".session('branch')." )";
		}
    	$result = DB::select('SELECT a.shift_id, a.shiftname, a.shift_start, a.shift_end, a.grace_time_in, a.grace_time_out, b.branch_name, a.ATT_time FROM office_shift a INNER JOIN branch b ON b.branch_id = a.branch_id where '.$filter);
		return $result;
    }

       public function getcompany()
    {
        $company = DB::table('company')->where('company_id',session('company_id'))->get();
        return $company;
    }

    	public function shift_delete($id)
	{
		if (DB::table('office_shift')->where('shift_id',$id)->delete()) {
		return 1;
		}
		else{
		return 0;	
		}
		
	}
	   public function shift_update($id, $items){
       $result = DB::table('office_shift')->where('shift_id', $id)->update($items);
        return $result;
    }

      public function getshiftdetails($id)
    {
    	$branch = DB::table('office_shift')->where('shift_id',$id)->get();
    	return $branch;
    }

      public function getotformula()
    {
        $getot = DB::table('overtime_formula')->get();
        return $getot;
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



       public function getotformulabyid($otid)
    {
        $getot = DB::table('overtime_formula')->where('OT_formulaid',$otid)->get();
        return $getot;
    }

    public function ot_update($id, $items){
       $result = DB::table('overtime_formula')->where('OT_formulaid', $id)->update($items);
        return $result;
    }

        public function ot_delete($id)
    {
        if (DB::table('overtime_formula')->where('OT_formulaid',$id)->delete()) {
        return 1;
        }
        else{
        return 0;   
        }
        
    }

        public function ot_exsit_chk($otformula){
        $result = DB::select('SELECT COUNT(OT_formulaid) AS counts FROM overtime_formula WHERE OTFormula = ?',[$otformula]);
        return $result;
    }  

          public function otamt_exsit_chk($otamount){
        $result = DB::select('SELECT COUNT(otamount_id) AS counts FROM overtime_amount WHERE amount = ?',[$otamount]);
        return $result;
    }  

            public function otduration_exsit_chk($otduration){
        $result = DB::select('SELECT COUNT(otduration_id) AS counts FROM overtime_duration WHERE duration = ?',[$otduration]);
        return $result;
    }  

        public function getattendancedetails($date){
        $result = DB::select('SELECT a.branch_id, a.branch_name, a.branch_logo, (SELECT COUNT(ed.empid) FROM employee_details ed INNER JOIN employee_shift_details es ON ed.empid = es.emp_id AND ed.status_id = es.status_id WHERE es.branch_id = a.branch_id) AS employees, (SELECT COUNT(emp_id) FROM attendance_details WHERE late > 0) AS late_count, (SELECT COUNT(ed.empid) FROM employee_details ed INNER JOIN employee_shift_details es ON ed.empid = es.emp_id AND ed.status_id = es.status_id WHERE es.branch_id = a.branch_id AND ed.empid NOT IN(SELECT emp_id FROM attendance_details WHERE date = ? AND branch_id = a.branch_id)) AS absent, (SELECT COUNT(emp_id) FROM attendance_details WHERE date = ? AND branch_id = a.branch_id) AS present FROM branch a WHERE a.company_id = ?',[$date, $date, session("company_id")]);
        return $result;
    }


     public function getemployee($branchid){
        $result = DB::select('SELECT a.empid, a.emp_name FROM employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid AND a.status_id = b.status_id WHERE b.branch_id = ? AND a.status_id = 1',[$branchid]);
        return $result;
    }


    public function getdetails($branchid,$empid,$date){
        $result = DB::select('SELECT * FROM attendance_details WHERE branch_id = ? AND emp_id = ?  AND date = ?',[$branchid,$empid,$date]);
        if ( $result) {
        return $result;    
        }
        else{
            return 0;
        }
        
    }

       public function getgracetime($empid){
        $result = DB::select('SELECT c.grace_time_in, c.grace_time_out, c.shift_start, c.shift_end FROM employee_shift_details b
            RIGHT JOIN office_shift c ON c.shift_id = b.shift_id
            WHERE b.emp_id = ?
            GROUP BY b.emp_id',[$empid]);
        return $result;
    }


    public function dailyattendance_update($id, $items){
       $result = DB::table('attendance_details')->where('attendance_id', $id)->update($items);
        return $result;
    }

         public function getpresentemp($date){
        $result = DB::select('SELECT a.empid, a.emp_name, a.emp_picture, c.branch_name, b.clock_in,b.late, b.branch_id, e.designation_name FROM employee_details a INNER JOIN attendance_details b ON b.emp_id = a.empid  INNER JOIN branch c ON c.branch_id = b.branch_id INNER JOIN employee_shift_details d ON d.emp_id = a.empid AND d.status_id = a.status_id INNER JOIN designation e ON e.designation_id = d.designation_id WHERE b.date = ? AND a.status_id = 1 and b.branch_id IN (Select branch_id from branch where company_id = ?)',[$date,session("company_id")]);
        return $result;
    }
    // public function getabsentemp($date){
    //     $result = DB::select('SELECT a.empid, a.emp_name, a.emp_picture, c.branch_name,d.branch_id, e.designation_name FROM employee_details a INNER JOIN absent_details b ON b.acc_no = a.emp_acc  INNER JOIN  employee_office_details d ON d.emp_id = a.empid INNER JOIN branch c ON c.branch_id = d.branch_id  INNER JOIN designation e ON e.designation_id = d.designation_id WHERE b.absent_date = ?',[$date]);
    //     return $result;
    // }


      public function getabsent($date){
        $result = DB::select('SELECT a.empid, a.emp_picture, a.emp_name, c.branch_name, d.designation_name  FROM employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid AND b.status_id = a.status_id INNER JOIN branch c ON c.branch_id = b.branch_id INNER JOIN designation d ON d.designation_id = b.designation_id WHERE a.empid NOT IN(SELECT emp_id FROM attendance_details WHERE date = ?) AND a.status_id = 1 and b.branch_id IN (Select branch_id from branch where company_id = ?)',[$date,session("company_id")]);
        return $result;
    }

          public function getpresent($date,$branchid){
        $result = DB::select('SELECT a.empid, a.emp_name, a.emp_picture, c.branch_name, b.clock_in FROM employee_details a INNER JOIN attendance_details b ON b.emp_id = a.empid INNER JOIN branch c ON c.branch_id = b.branch_id WHERE b.date = ? AND b.branch_id = ?',[$date,$branchid]);
        return $result;
    }

           public function getlate($date,$branchid){
        $result = DB::select('SELECT a.empid, a.emp_name, a.emp_picture, c.branch_name, b.late FROM employee_details a INNER JOIN attendance_details b ON b.emp_id = a.empid INNER JOIN branch c ON c.branch_id = b.branch_id WHERE b.date = ? AND b.branch_id = ? AND b.late > 0',[$date,$branchid]);
        return $result;
    }

       public function getattendance(){
        $result = DB::select("SELECT (SELECT a.empid from employee_details a WHERE a.emp_acc = a.acc_no) AS employeeid,(SELECT b.branch_id FROM employee_details a INNER JOIN employee_office_details b ON b.emp_id = a.empid WHERE a.emp_acc = a.acc_no) AS branchid, a.dateIN, a.ClockIn, b.clockOut, TIMESTAMPDIFF(MINUTE, concat(a.dateIN, ' ', ((SELECT a.shift_start from office_shift a INNER JOIN employee_office_details b ON b.officeshift_id = a.shift_id INNER JOIN employee_details c ON c.empid = b.emp_id WHERE c.emp_acc = a.acc_no))),concat(a.dateIN,' ',a.ClockIN))-(SELECT a.grace_time_in from office_shift a INNER JOIN employee_office_details b ON b.officeshift_id = a.shift_id INNER JOIN employee_details c ON c.empid = b.emp_id WHERE c.emp_acc = a.acc_no) AS late, TIMESTAMPDIFF(MINUTE, concat(b.dateout,' ',b.clockOut), concat(b.dateout, ' ', ((SELECT a.shift_end from office_shift a INNER JOIN employee_office_details b ON b.officeshift_id = a.shift_id INNER JOIN employee_details c ON c.empid = b.emp_id WHERE c.emp_acc = a.acc_no))))-(SELECT a.grace_time_out from office_shift a INNER JOIN employee_office_details b ON b.officeshift_id = a.shift_id INNER JOIN employee_details c ON c.empid = b.emp_id WHERE c.emp_acc = a.acc_no) AS early, TIMESTAMPDIFF(MINUTE,  concat(b.dateout, ' ', ((SELECT a.shift_end from office_shift a INNER JOIN employee_office_details b ON b.officeshift_id = a.shift_id INNER JOIN employee_details c ON c.empid = b.emp_id WHERE c.emp_acc = a.acc_no))),concat(b.dateout,' ',b.clockOut)) AS overtime, TIMEDIFF(concat(b.dateout, ' ', (b.clockOut)),concat(a.dateIN,' ',a.ClockIn)) AS ATT_time FROM attendance_in a LEFT JOIN attendance_out b ON b.acc_no = a.acc_no AND b.dateOut = a.dateIN WHERE a.dateIN = CURRENT_DATE()");
        return $result;
    }

     public function upload_exsist_chk($empid){
             $result = DB::select('SELECT COUNT(emp_id) AS counter FROM attendance_details WHERE emp_id = ? AND date = CURRENT_DATE()',[$empid]);
        return $result;
        // $result = DB::select('SELECT COUNT(attendance_id) AS counter FROM attendance_details WHERE  date = CURRENT_DATE() OR clock_in = null');
        // return $result;
    }

     public function clockout_update($empid, $items){
       $result = DB::table('attendance_details')->where('emp_id', $empid)->where('date', date('Y-m-d'))->update($items);
        return $result;
    }

    //     public function attendance_sheet($branchid,$empid){
    //    $result = DB::select('SELECT a.attendance_id, a.emp_id, a.branch_id, b.emp_acc, b.emp_picture, b.emp_name, c.branch_name, a.date, a.clock_in, IFNULL(a.clock_out,0) AS clockout ,IFNULL(a.late,0) AS lates, IFNULL(a.early,0) AS earlys, IFNULL(a.OT_time,0) AS ot,  IFNULL(a.ATT_time,0) AS Atttime FROM attendance_details a INNER JOIN employee_details b ON b.empid = a.emp_id
    //         INNER JOIN branch c ON c.branch_id = a.branch_id WHERE a.date = CURRENT_DATE() AND a.branch_id = ? '.($empid == '' ? '' : 'AND a.emp_id = ?'),[$branchid,$empid]);
    //     return $result;
    // }

    
	public function attendance_sheet($branchid,$empid,$date = ""){
		$date = ($date != "" ? $date : date("Y-m-d"));
		$result = DB::select("SELECT a.attendance_id, a.emp_id, a.branch_id, b.emp_acc, b.emp_picture, b.emp_name, c.branch_name, a.date, TIME_FORMAT(a.clock_in, '%h:%i:%s') AS clock_in, IFNULL(TIME_FORMAT(a.clock_out, '%h:%i:%s'),0) AS clockout ,IFNULL(a.late,0) AS lates, IFNULL(a.early,0) AS earlys, IFNULL(a.OT_time,0) AS ot,  IFNULL(a.ATT_time,0) AS Atttime FROM attendance_details a INNER JOIN employee_details b ON b.empid = a.emp_id INNER JOIN branch c ON c.branch_id = a.branch_id WHERE a.date = ? AND a.branch_id = ? ".($empid == '' ? '' : 'AND a.emp_id = ?'),[$date,$branchid,$empid]);
		return $result;
	}
	
	public function notify(){
       $result = DB::select("SELECT a.id, b.emp_name, b.emp_picture, TIME_FORMAT(a.ClockIn, '%h:%i %p') as ClockIn, a.acc_no from attendance_in a INNER JOIN employee_details b ON b.emp_acc = a.acc_no WHERE a.notify_id = 1 AND a.dateIN = CURRENT_DATE()");
       return $result;
    }

      public function notify_checkout(){
       $result = DB::select("SELECT a.id, b.emp_name, b.emp_picture, TIME_FORMAT(a.clockOut, '%h:%i %p') as ClockIn, a.acc_no from attendance_out a INNER JOIN employee_details b ON b.emp_acc = a.acc_no WHERE a.notify_id = 1 AND a.dateOut = CURRENT_DATE()");
        return $result;
    }

   public function attendanceIn_update($id, $items){
       $result = DB::table('attendance_in')->where('id', $id)->update($items);
        return $result;
    }

      public function attendanceOut_update($id, $items){
       $result = DB::table('attendance_out')->where('id', $id)->update($items);
        return $result;
    }


     public function chk_absent($empid,$date){
       $result = DB::select("SELECT COUNT(id) AS absent from absent_details WHERE acc_no = ? AND absent_date = ?",[$empid,$date]);
        return $result;
    }

     public function chk_holiday($date){
       $result = DB::select("SELECT day FROM month_table WHERE date = ?",[$date]);
        return $result;
    }

     public function get_holiday(){
       $result = DB::select("SELECT day_off FROM holidays WHERE branch_id = ?",[session('branch')]);
        return $result;
    }

       public function chk_events($date){
       $result = DB::select("SELECT COUNT(event_id) AS event FROM company_events WHERE event_date = ? AND branch_id = ?",[$date,session('branch')]);
        return $result;
    }

        public function absent_delete($empid,$date)
    {
        if (DB::table('absent_details')->where('acc_no',$empid)->where('absent_date',$date)->delete()) {
        return 1;
        }
        else{
        return 0;   
        }
        
    }

        public function attendance_exist($empid,$date){
       $result = DB::select("SELECT COUNT(attendance_id) as counts from attendance_details WHERE emp_id = ? AND date = ?",[$empid,$date]);
        return $result;
    }

        public function absent_exsist($empid,$date){
       $result = DB::select("SELECT COUNT(id) AS counts FROM `absent_details` WHERE acc_no = ? AND absent_date = ?",[$empid,$date]);
        return $result;
    }

          public function generate_month($date){
       $result = DB::select('Insert into month_table (date,day)
select a.Date,DAYNAME(a.Date) 
from (
    select last_day(?) - INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY as Date
    from (select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7 union all select 8 union all select 9) as a
    cross join (select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7 union all select 8 union all select 9) as b
    cross join (select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7 union all select 8 union all select 9) as c
) a 
where a.Date between ? and last_day(?) order by a.Date
',[$date,$date,$date]);
        return $result;
    }


   public function get_absent_details($branch,$empid,$fromdate,$todate){
    $clause = "";
    if ($branch == "" && $empid == "" && $fromdate == "" && $todate == "") {
      $clause = "";
    }
    else if ($branch != "") {
      $clause = "WHERE c.branch_id = ".$branch;
    }
    else if ($empid != "") {
      $clause = "WHERE a.acc_no = ".$empid;
    }
    else if ($fromdate != "" && $todate != "") {
      $clause = "WHERE a.absent_date BETWEEN '".$fromdate. "' AND '".$todate."'";
    }
    
       $result = DB::select('SELECT a.id, b.empid, b.emp_acc, b.emp_name, a.absent_date, d.branch_name, e.shiftname, f.department_name FROM absent_details a INNER JOIN employee_details b ON b.empid = a.acc_no INNER JOIN employee_shift_details c ON c.emp_id = b.empid AND c.status_id = b.status_id INNER JOIN branch d ON d.branch_id = c.branch_id INNER JOIN office_shift e ON e.shift_id = c.shift_id INNER JOIN departments f ON f.department_id = c.department_id '.$clause);
        return $result;
    }

      public function getemployees($branchid)
    {
        if (session("roleId") == 2) {
         $result = DB::select('SELECT a.empid, a.emp_acc, a.emp_name, c.department_name, d.branch_name FROM employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid AND a.status_id = b.status_id INNER JOIN departments c ON c.department_id = b.department_id INNER JOIN branch d ON d.branch_id = b.branch_id WHERE a.status_id = 1');
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






}