<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class emptyData extends Model
{
    public function getCompany(){
//        $company = DB::table('company')->where('company_id',[session("company_id")])->get();
        $company = DB::table('company')->get();
        return $company;
    }

    public function deletedatabase($companyid,$erp,$hr){
        if ($erp == 1)
        {

        //delete purchases
        $purchases = DB::table('purchase_general_details')->where('user_id',$companyid)->delete();

        //delete demand
        $demand = DB::select('DELETE FROM demand_general_details WHERE branch_id IN (SELECT branch_id FROM branch WHERE company_id = ?)',[$companyid]);

        //delete transfer
        $transfer = DB::select('DELETE FROM transfer_general_details WHERE branch_from IN (SELECT branch_id FROM branch WHERE company_id = ?)',[$companyid]);

        //delete delivery challan
        $challan = DB::select('DELETE FROM deliverychallan_general_details WHERE branch_from IN (SELECT branch_id FROM branch WHERE company_id = ?)',[$companyid]);

        //delete inventory department
        $department = DB::select('DELETE FROM inventory_department WHERE company_id = ?',[$companyid]);

        //delete inventory
        $inventory = DB::select('DELETE FROM inventory_general WHERE company_id = ?',[$companyid]);

        //delete vendors
        $vendor = DB::select('DELETE FROM vendors WHERE user_id = ?',[$companyid]);

        //vendor payment details

        //bank accounts
        $accounts = DB::select('DELETE FROM bank_account_generaldetails WHERE branch_id_company IN (SELECT branch_id FROM branch WHERE company_id = ?)',[$companyid]);

        //bank cheques
        $cheques = DB::select('DELETE FROM bank_cheque_general WHERE branch_id IN (SELECT branch_id FROM branch WHERE company_id = ?)',[$companyid]);

        //cash ledger
        $cash = DB::select('DELETE FROM cash_ledger WHERE branch_id IN (SELECT branch_id FROM branch WHERE company_id = ?)',[$companyid]);

        //terminal
        $terminal = DB::select('DELETE FROM terminal_details WHERE branch_id IN (SELECT branch_id FROM branch WHERE company_id = ?)',[$companyid]);

        //terminal delete sales delete

        //expenses
        $expense = DB::select('DELETE FROM expense_categories WHERE branch_id IN (SELECT branch_id FROM branch WHERE company_id = ?)',[$companyid]);

        //customer
        $customer = DB::select('DELETE FROM customers WHERE user_id IN (SELECT user_id FROM user_authorization WHERE company_id = ?)',[$companyid]);

        //taxes
        $taxes = DB::select('DELETE FROM taxes WHERE company_id = ?',[$companyid]);
        }

        if ($hr == 1)
        {
            //delete employees
            $employees = DB::select('DELETE FROM employee_details WHERE empid IN (SELECT emp_id FROM employee_shift_details WHERE branch_id IN (SELECT branch_id FROM branch WHERE company_id = ?))',[$companyid]);

            //delete tax slabs
            $taxslabs = DB::select('DELETE FROM tax_slabs WHERE company_id = ?',[$companyid]);

            //office shift
            $shifts = DB::select('DELETE FROM office_shift WHERE branch_id IN (SELECT branch_id FROM branch WHERE company_id = ?)',[$companyid]);

            //company events
            $events = DB::select('DELETE FROM company_events WHERE branch_id IN (SELECT branch_id FROM branch WHERE company_id = ?)',[$companyid]);

            //company holidays
            $holiday = DB::select('DELETE FROM holidays WHERE branch_id IN (SELECT branch_id FROM branch WHERE company_id = ?)',[$companyid]);

            //delivery charges
            $charges = DB::select('DELETE FROM delivery_charges WHERE branch_id IN (SELECT branch_id FROM branch WHERE company_id = ?)',[$companyid]);

            //service provider
            $provider = DB::select('DELETE FROM service_provider_details WHERE branch_id IN (SELECT branch_id FROM branch WHERE company_id = ?)',[$companyid]);

            //atteandance in or out
            $atteandancein = DB::select('DELETE FROM attendance_in WHERE acc_no IN (SELECT emp_acc FROM employee_details WHERE empid IN (SELECT emp_id FROM employee_shift_details WHERE branch_id IN (SELECT branch_id FROM branch WHERE company_id = ?)))',[$companyid]);

            $atteandanceout = DB::select('DELETE FROM attendance_out WHERE acc_no IN (SELECT emp_acc FROM employee_details WHERE empid IN (SELECT emp_id FROM employee_shift_details WHERE branch_id IN (SELECT branch_id FROM branch WHERE company_id = ?)))',[$companyid]);

        }

        return 1;

    }
}