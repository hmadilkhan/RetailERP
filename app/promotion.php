<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class promotion extends Model
{

    public function getdetailsforpromotion($empid)
    {
        $result = DB::select('SELECT b.designation_name, (SELECT basic_pay from increment_details WHERE emp_id = ab.emp_id AND status_id = 1) AS basic_pay, IFNULL((SELECT tax_amount FROM tax_details WHERE id = (SELECT MAX(id) from tax_details WHERE emp_id = ab.emp_id)),0) AS tax_amount, IFNULL((SELECT b.amount FROM employee_overtime_details a INNER JOIN overtime_amount b ON b.otamount_id = a.otamount_id WHERE a.emp_id = ab.emp_id AND status_id = 1),0) AS ot_amount, IFNULL((SELECT b.duration FROM employee_overtime_details a INNER JOIN overtime_duration b ON b.otduration_id = a.otduration_id WHERE a.emp_id = ab.emp_id AND a.status_id = 1),0) as ot_duration, (SELECT b.category FROM increment_details a INNER JOIN salary_category b ON b.id = a.salary_category_id WHERE a.emp_id = ab.emp_id AND a.status_id = 1) AS salary_cat  FROM employee_shift_details ab INNER JOIN designation b ON b.designation_id = ab.designation_id WHERE ab.emp_id = ? AND ab.status_id = 1',[$empid]);
        return $result;
    }

    public function getdesig_acctodepart($empid)
    {
        $result = DB::select('SELECT * FROM designation WHERE department_id = (SELECT department_id FROM employee_shift_details WHERE emp_id = ? AND status_id = 1)',[$empid]);
        return $result;
    }

    public function get_tax_percentage($empid)
    {
        $result = DB::select('SELECT percentage FROM tax_slabs WHERE tax_id = (SELECT tax_id FROM tax_details WHERE id = (SELECT MAX(id) FROM tax_details WHERE emp_id = ?))',[$empid]);
        return $result;
    }

    public function get_promotion_details()
    {
        $result = DB::select('SELECT a.promotion_id, a.emp_id, b.emp_acc, b.emp_name, a.date FROM promotion_details a INNER JOIN employee_details b ON b.empid = a.emp_id');
        return $result;
    }


}