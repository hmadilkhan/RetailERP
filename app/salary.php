<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class salary extends Model
{
    public function getbranches()
    {
    	$branch = DB::table('branch')->where('company_id',session('company_id'))->get();
    	return $branch;
    }

     public function getemployee($branchid)
    {
        if (session("roleId") == 2) {
         $result = DB::select('SELECT a.empid, a.emp_acc, a.emp_name, c.department_name, d.branch_name FROM employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid AND a.status_id = b.status_id INNER JOIN departments c ON c.department_id = b.department_id INNER JOIN branch d ON d.branch_id = b.branch_id WHERE a.status_id = 1 AND b.branch_id = ?',[$branchid]);
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

      public function getdepart()
    {
    	$result = DB::select('SELECT a.department_id, b.branch_name, a.department_name FROM departments a INNER JOIN branch b ON b.branch_id = a.branch_id');
		return $result;
    }

    public function insert($table,$items){
		$result = DB::table($table)->insertGetId($items);
       return $result;   
	}

	   public function exsit_chk_special($empid, $date)
    {
    	$result = DB::select('SELECT COUNT(special_id) AS counter, amount, special_id FROM special_allowance WHERE emp_id = ? AND date = ?',[$empid, $date]);
		return $result;
    }

       public function special_update($id,$items){
        $result = DB::table('special_allowance')->where('special_id', $id)->update($items);
        return $result;
    }

    public function emp_details($empid,$fromdate, $todate){
        $result = DB::select("SELECT a.empid, a.emp_acc, a.emp_name, a.emp_fname, a.emp_contact, a.emp_picture,a.pf_enable ,f.basic_pay AS basic_salary,f.pf_fund,f.allowance,f.gross_salary ,c.branch_name, d.department_name, e.designation_name, (SELECT COUNT(DISTINCT date) FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = ? ) AS present, (SELECT IFNULL(SUM(late),0) FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = ? and clock_out != '00:00:00') AS late, (SELECT IFNULL(SUM(OT_time),0) FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = ? and clock_out != '00:00:00') AS ot, (SELECT IFNULL(SUM(early),0) FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = ? and clock_out != '00:00:00') AS early, (SELECT COUNT(id) from absent_details WHERE absent_date BETWEEN ? AND ? AND acc_no = ?) AS absent, (SELECT IFNULL(SUM(days),0) FROM leaves_avail_details WHERE emp_id = ? AND from_date BETWEEN ? AND ? AND to_date BETWEEN ? AND ?) AS leaves FROM employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid AND a.status_id = b.status_id INNER JOIN branch c ON c.branch_id = b.branch_id INNER JOIN departments d ON d.department_id = b.department_id INNER JOIN designation e ON e.designation_id = b.designation_id INNER JOIN increment_details f ON f.emp_id = a.empid AND f.status_id = a.status_id WHERE a.empid = ?",[$fromdate, $todate,$empid,$fromdate, $todate,$empid,$fromdate, $todate,$empid,$fromdate, $todate,$empid,$fromdate, $todate,$empid,$empid,$fromdate, $todate,$fromdate, $todate,$empid]);
        return $result;
    }
	
	public function calculateLate($empid,$fromdate, $todate)
	{
		$result = DB::select("SELECT IFNULL(SUM(late),0) as late FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = ?",[$fromdate,$todate,$empid]);
		return $result;
	}
	
	public function calculateEarly($empid,$fromdate, $todate)
	{
		$result = DB::select("SELECT IFNULL(SUM(early),0) as early FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = ?",[$fromdate,$todate,$empid]);
		return $result;
	}
	
	public function calculateOvertime($empid,$fromdate, $todate)
	{
		$result = DB::select("SELECT IFNULL(SUM(OT_time),0) as ot FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = ?",[$fromdate,$todate,$empid]);
		return $result;
	}

    public function get_gross_deduct_details($empid,$fromdate, $todate){
      $get = DB::select('SELECT a.salary_category_id FROM increment_details a INNER JOIN salary_category b ON b.id = a.salary_category_id WHERE a.emp_id = ? AND a.status_id = 1',[$empid]);
      if ($get[0]->salary_category_id == 1) {
        $result = DB::select('SELECT IFNULL(attendance_allowance,0) AS at_allowance,(a.basic_salary) AS perday_salary,IFNULL((SELECT COUNT(a.acc_no) FROM absent_details a INNER JOIN employee_details b ON b.emp_acc = a.acc_no WHERE a.absent_date BETWEEN ? AND ? AND b.empid = ? AND a.weekend = 1),0) AS weekendcount,IFNULL((SELECT COUNT(a.acc_no) FROM absent_details a INNER JOIN employee_details b ON b.emp_acc = a.acc_no WHERE a.absent_date BETWEEN ? AND ? AND b.empid = ? AND a.weekend = 1) * (a.basic_salary/2),0) AS weekend, IFNULL((b.installment_amount),0) AS loan,b.installment_id,b.loan_id,IFNULL(SUM(c.amount),0) AS advance,e.ATT_time, (SELECT IFNULL(SUM(a.OT_time),0) FROM attendance_details a WHERE a.date BETWEEN ? AND ? AND a.emp_id = ?) AS ot_hours, ((SELECT IFNULL(SUM(a.OT_time),0) FROM attendance_details a WHERE a.date BETWEEN ? AND ? AND a.emp_id = ?) >=  (SELECT b.duration FROM employee_office_details a INNER JOIN overtime_duration b ON b.otduration_id = a.otduration_id WHERE a.emp_id = ?)) AS otapplicable,IFNULL((((SELECT IFNULL(SUM(a.OT_time),0) FROM attendance_details a WHERE a.date BETWEEN ? AND ? AND a.emp_id = ?) / 60) * d.amount),0) AS ot_amount FROM employee_office_details a LEFT JOIN loan_installment b ON b.emp_id = ? AND b.status_id = 1 LEFT JOIN advance_salary c ON c.emp_id = a.emp_id AND c.date >= ? AND c.date <= ? LEFT JOIN overtime_amount d ON d.otamount_id = a.otamount_id LEFT JOIN office_shift e ON e.shift_id = a.officeshift_id WHERE a.emp_id = ?',[$fromdate, $todate,$empid,$fromdate, $todate,$empid,$fromdate, $todate,$empid,$fromdate, $todate,$empid,$empid,$fromdate, $todate,$empid,$empid,$fromdate, $todate,$empid]);
    
       return $result;
      }
      else{
       $result = DB::select('SELECT IFNULL(attendance_allowance,0) AS at_allowance,(a.basic_salary) AS perday_salary,IFNULL((SELECT COUNT(a.acc_no) FROM absent_details a INNER JOIN employee_details b ON b.emp_acc = a.acc_no WHERE a.absent_date BETWEEN ? AND ? AND b.empid = ? AND a.weekend = 1),0) AS weekendcount,IFNULL((SELECT COUNT(a.acc_no) FROM absent_details a INNER JOIN employee_details b ON b.emp_acc = a.acc_no WHERE a.absent_date BETWEEN ? AND ? AND b.empid = ? AND a.weekend = 1) * (a.basic_salary/2),0) AS weekend, IFNULL((b.installment_amount),0) AS loan,b.installment_id,b.loan_id,IFNULL(SUM(c.amount),0) AS advance,e.ATT_time, (SELECT IFNULL(SUM(a.OT_time),0) FROM attendance_details a WHERE a.date BETWEEN ? AND ? AND a.emp_id = ?) AS ot_hours, ((SELECT IFNULL(SUM(a.OT_time),0) FROM attendance_details a WHERE a.date BETWEEN ? AN D ? AND a.emp_id = ?) >=  (SELECT b.duration FROM employee_office_details a INNER JOIN overtime_duration b ON b.otduration_id = a.otduration_id WHERE a.emp_id = ?)) AS otapplicable,IFNULL((((SELECT IFNULL(SUM(a.OT_time),0) FROM attendance_details a WHERE a.date BETWEEN ? AND ? AND a.emp_id = ?) / 60) * d.amount),0) AS ot_amount FROM employee_office_details a LEFT JOIN loan_installment b ON b.emp_id = ? AND b.status_id = 1 LEFT JOIN advance_salary c ON c.emp_id = a.emp_id AND c.date >= ? AND c.date <= ? LEFT JOIN overtime_amount d ON d.otamount_id = a.otamount_id LEFT JOIN office_shift e ON e.shift_id = a.officeshift_id WHERE a.emp_id = ?',[$fromdate, $todate,$empid,$fromdate, $todate,$empid,$fromdate, $todate,$empid,$fromdate, $todate,$empid,$empid,$fromdate, $todate,$empid,$empid,$fromdate, $todate,$empid]);
      return $result;
      }
    }

      public function salary_details($fromdate,$todate,$branchid,$empid)
    {
      $clause = "";
      if ($fromdate != "") {
         $clause .= " a.date BETWEEN '".$fromdate."' AND '".$todate."'";
      }
      else{
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');
        
        $clause .= " c.branch_id = ".session('branch')." AND a.date BETWEEN '".$start->toDateString()."' AND '".$end->toDateString()."'";
      }
      if($branchid != "")
      {
        $clause .= " AND d.branch_id = ".$branchid;
      }
      if($empid != "")
      {
        $clause .= " AND b.empid = ".$empid;
      }
      $result = DB::select('SELECT b.empid, a.date, b.emp_acc, b.emp_picture, b.emp_name, d.branch_name, a.gross_salary, a.deduction_salary, a.special_amount, a.net_salary FROM salary_details a INNER JOIN employee_details b ON b.empid = a.emp_id INNER JOIN employee_shift_details c ON c.emp_id = a.emp_id AND c.status_id = b.status_id INNER JOIN branch d ON d.branch_id = c.branch_id WHERE'.$clause,[$fromdate,$todate]);
    return $result;
    }

     public function absent_record($date,$empid)
    {
      $result = DB::select('SELECT COUNT(emp_id) AS counter FROM attendance_details WHERE date = ? AND emp_id = ?',[$date,$empid]);
    return $result;
    }

      public function present_record($firstdate,$lastdate,$empid)
    {
      $result = DB::select('SELECT COUNT(emp_id) AS present, (SELECT attendance_allowance FROM employee_office_details WHERE emp_id = ?) AS allowance FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = ?',[$empid,$firstdate,$lastdate,$empid]);
    return $result;
    }

      public function payslip_exsit($date,$empid)
    {
      $result = DB::select('SELECT COUNT(emp_id) AS counter FROM payslip WHERE payslip_date = ? AND emp_id = ?',[$date,$empid]);
    return $result;
    }

// salary procedure start here 
        public function check_permision()
    {
      $result = DB::table('hr_permission')->where('company_id',session('company_id'))->get();
      return $result;
    }

         public function getallowance_details($empid)
    {
      $result = DB::select('SELECT b.allowance_name, a.amount FROM allowances_details a INNER JOIN allowances b ON b.allowance_id = a.allowance_id WHERE a.emp_id = ? AND a.status_id = 1',[$empid]);
    return $result;
    }

     public function getbonus_details($empid,$fromdate,$todate)
    {
      $result = DB::select('SELECT bonus_id, SUM(bonus_amt) AS bonus_amount FROM bonus_details WHERE emp_id = ? AND status_id = 1 AND date BETWEEN ? AND ?',[$empid,$fromdate,$todate]);
    return $result;
    }

     public function get_overtimeAmount($empid,$fromdate,$todate)
    {
      $result = DB::select('SELECT id, CASE WHEN (SELECT b.duration FROM employee_overtime_details a INNER JOIN overtime_duration b ON b.otduration_id = a.otduration_id WHERE a.emp_id = ? AND a.status_id = 1) > 0 THEN CASE WHEN (SELECT SUM(OT_time) AS overtime FROM attendance_details WHERE emp_id = ? AND date BETWEEN ? AND ?) >= (SELECT b.duration FROM employee_overtime_details a INNER JOIN overtime_duration b ON b.otduration_id = a.otduration_id WHERE a.emp_id = ? AND a.status_id = 1) THEN (SELECT b.amount FROM employee_overtime_details a INNER JOIN overtime_amount b ON b.otamount_id = a.otamount_id WHERE a.emp_id = ? AND a.status_id = 1) * ((SELECT SUM(OT_time) AS overtime FROM attendance_details WHERE emp_id = ? AND date BETWEEN ? AND ?)/60) END ELSE 0 END otamount from employee_overtime_details where emp_id = ? AND status_id = 1',[$empid,$empid,$fromdate,$todate,$empid,$empid,$empid,$fromdate,$todate,$empid]);
    return $result;
    }

     public function getadvance_details($empid,$fromdate,$todate)
    {
      $result = DB::select('SELECT advance_id, IFNULL(SUM(amount),0) AS advance FROM advance_salary WHERE emp_id = ? AND status_id = 1 AND date BETWEEN ? AND ?',[$empid,$fromdate,$todate]);
    return $result;
    }

    public function getloanamt_details($empid)
    {
      $result = DB::select('SELECT SUM(balance) AS loanamt,deduction_amount FROM loan_details WHERE emp_id = ? AND status_id = 1',[$empid]);
    return $result;
    }

      public function getabsent_amount($empid,$fromdate,$todate)
    {
      $result = DB::select('Select IFNULL(((SELECT COUNT(id) AS absent from absent_details WHERE acc_no = ? AND absent_date BETWEEN ? AND ? AND weekday = 0 AND event = 0) * (SELECT CASE WHEN salary_category_id = 1 THEN basic_pay ELSE basic_pay/30 END FROM increment_details WHERE emp_id = ? AND status_id = 1)),0)AS absent_amt',[$empid,$fromdate,$todate,$empid]);
    return $result;
    }

       public function gettax_amount($empid)
    {
      $result = DB::select('SELECT IFNULL((SELECT tax_amount FROM tax_details a INNER JOIN increment_details b ON b.emp_id = a.emp_id WHERE b.status_id = 1 AND b.tax_applicable_id = 1 AND  a.id = (SELECT MAX(id) FROM tax_details WHERE emp_id = ?)),0) AS tax_amount',[$empid]);
    return $result;
    }

      public function getspecial_allowance($empid,$fromdate,$todate)
    {
      $result = DB::select('Select IFNULL((SELECT amount FROM special_allowance WHERE emp_id = ? AND date BETWEEN ? AND ?),0) AS amount',[$empid,$fromdate,$todate]);
    return $result;
    }

// update salary affacted tables
  public function bonus_update($id,$statusid){
        $result = DB::table('bonus_details')->where('bonus_id', $id)->update(['status_id'=>$statusid]);
        return $result;
    }

    public function advance_update($id,$statusid){
        $result = DB::table('advance_salary')->where('advance_id', $id)->update(['status_id'=>$statusid]);
        return $result;
    }

     public function getloan_installmentdetails($empid,$fromdate,$todate){
       $result = DB::select('SELECT * FROM loan_installment WHERE emp_id = ? AND status_id = 1 AND date BETWEEN ? AND ?',[$empid,$fromdate,$todate]);
    return $result;
    }

       public function loaninstallment_update($id,$statusid){
        $result = DB::table('loan_installment')->where('installment_id', $id)->update(['status_id'=>$statusid]);
        return $result;
    }



     public function getcompany()
    {
        $company = DB::table('company')->where('company_id',session('company_id'))->get();
        return $company;
    }
	
    public function payslip_report($empid,$fromdate,$todate)
    {
        $result = DB::select('SELECT * FROM payslip WHERE emp_id = ? AND payslip_date BETWEEN ? AND ?',[$empid,$fromdate,$todate]);
		return $result;
    }
	
	public function payslip_details($empid,$todate)
    {
        $result = DB::select('SELECT * FROM salary_details WHERE emp_id = ? AND date = ?',[$empid,$todate]);
		return $result;
    }

    public function getloandetails($empid)
    {
        $result = DB::select('SELECT * from loan_details WHERE emp_id = ? AND status_id = 1',[$empid]);
        return $result;
    }

    public function loandetails_update($id,$items){
        $result = DB::table('loan_details')->where('loan_id', $id)->update($items);
        return $result;
    }

    public  function getsalcategory(){
        $result = DB::table('salary_category')->get();
        return $result;
    }

    public function getemployee_bysalarycategory($branchid,$salcat)
    {
        $result = DB::select('SELECT a.empid, a.emp_acc, a.emp_name, c.department_name, d.branch_name FROM employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid AND a.status_id = b.status_id INNER JOIN departments c ON c.department_id = b.department_id INNER JOIN branch d ON d.branch_id = b.branch_id INNER JOIN increment_details e ON e.emp_id = a.empid AND e.status_id = a.status_id WHERE a.status_id = 1 AND b.branch_id = ? AND e.salary_category_id = ?',[$branchid,$salcat]);
        return $result;

    }


    public function getdaysname($fromdate, $todate,$days){
        $result = DB::select('select * from 
  (select adddate("1970-01-01",t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date,DAYNAME(adddate("1970-01-01",t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i)) as Dayname from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
where selected_date between ? and ? HAVING Dayname IN (?)',[$fromdate,$todate,$days]);
        return $result;
    }

    public  function getdayoff ($empid){
        $result = DB::select('SELECT day_off from holidays WHERE branch_id = (SELECT b.branch_id from employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid AND b.status_id = a.status_id WHERE a.empid = ?)',[$empid]);
        return $result;
    }

    public function generatedates($fromdate, $todate){
        $result = DB::select('select * from 
  (select adddate("1970-01-01",t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date,DAYNAME(adddate("1970-01-01",t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i)) as Dayname from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
where selected_date between ? and ?',[$fromdate,$todate]);
        return $result;
    }
	
	 public function totalmonthdays($fromdate, $todate){
        $result = DB::select('select COUNT(*) from 
  (select adddate("1970-01-01",t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date,DAYNAME(adddate("1970-01-01",t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i)) as Dayname from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
where selected_date between ? and ?',[$fromdate,$todate]);
        return $result;
    }


    public function getpreviousbalance($empid){
        $result = DB::select('SELECT balance FROM employee_ledger WHERE emp_id = ? AND ledger_id = (SELECT MAX(ledger_id) FROM employee_ledger WHERE emp_id = ?)',[$empid,$empid]);
        return $result;
    }

    public function getledger(){
        $result = DB::select('SELECT a.empid, a.emp_acc, a.emp_name, a.emp_picture, a.emp_contact, c.designation_name,IFNULL((SELECT balance FROM employee_ledger WHERE emp_id = a.empid AND ledger_id = (SELECT MAX(ledger_id) FROM employee_ledger WHERE emp_id = a.empid)),0) AS balance,(SELECT ledger_id FROM employee_ledger WHERE emp_id = a.empid AND ledger_id = (SELECT MAX(ledger_id) FROM employee_ledger WHERE emp_id = a.empid)) AS ledger_id FROM employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid AND b.status_id = a.status_id INNER JOIN designation c ON c.designation_id = b.designation_id WHERE a.status_id = 1 AND b.branch_id = ?',[session('branch')]);
        return $result;

    }

    public function getledgerdetails($empid){
        $result = DB::select('SELECT DATE_FORMAT(a.date,\'%Y-%m-%d\') AS Date, a.*, b.*  FROM employee_ledger a INNER JOIN employee_details b ON b.empid = a.emp_id WHERE a.emp_id = ?',[$empid]);
        return $result;

    }

    public function company($id)
    {
        $result = DB::table('company')->where('company_id',$id)->get();
        return $result;
    }

    public function getemployeename($id)
    {
        $result = DB::table('employee_details')->where('empid',$id)->get();
        return $result;
    }





//    public function loandetails_update($id,$statusid){
//        $result = DB::table('loan_details')->where('loan_id', $id)->update(['status_id'=>$statusid]);
//        return $result;
//    }

}