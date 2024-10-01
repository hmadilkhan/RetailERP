<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class report extends Model
{
    public function getbranch()
    {
        $branch = DB::table('branch')->where('company_id', session("company_id"))->get();
        return $branch;
    }

    public function getbranchbyid($branchid)
    {
        $branch = DB::table('branch')->where('branch_id', $branchid)->get();
        return $branch;
    }

    public function getemployee()
    {
        $employee = DB::table('employee_details')->where('status_id', 1)->get();
        return $employee;
    }

    public function getemployeebyid($empid)
    {
        $employee = DB::table('employee_details')->where('status_id', 1)->where('empid', $empid)->get();
        return $employee;
    }


    public function getcompany()
    {
        $company = DB::table('company')->where('company_id', session('company_id'))->get();
        return $company;
    }




    public function attendance_sheet_report($branchid, $fromdate, $todate, $empid, $approchid)
    {
        $clause = "";
        if ($approchid == 1) {

            if ($todate != "" && $empid != "") {
                $clause .= "a.date BETWEEN '" . $fromdate . "' AND '" . $todate . "' AND a.emp_id = '" . $empid . "'";
            } else {
                $clause .= "a.date BETWEEN '" . $fromdate . "' AND '" . $todate . "'";
            }
            $result = DB::select('SELECT a.attendance_id, a.date, a.emp_id, a.branch_id, b.emp_acc, b.emp_picture, b.emp_name, c.branch_name, a.date, a.clock_in, IFNULL(a.clock_out,0) AS clockout ,IFNULL(a.late,0) AS lates, IFNULL(a.early,0) AS earlys, IFNULL(a.OT_time,0) AS ot,  IFNULL(a.ATT_time,0) AS Atttime FROM attendance_details a INNER JOIN employee_details b ON b.empid = a.emp_id INNER JOIN branch c ON c.branch_id = a.branch_id WHERE a.branch_id = ? AND ' . $clause, [$branchid]);
            return $result;
        } else {
            if ($empid == '') {
                $result = DB::select('SELECT a.emp_name, ((SELECT COUNT(emp_id) FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = a.empid  )) AS present, (SELECT IFNULL(SUM(late),0) FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = a.empid ) AS late, (SELECT IFNULL(SUM(OT_time),0) FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = a.empid ) AS ot, (SELECT IFNULL(SUM(early),0) FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = a.empid ) AS early, IFNULL((SELECT COUNT(a.acc_no) FROM absent_details a INNER JOIN employee_details b ON b.emp_acc = a.acc_no WHERE a.absent_date BETWEEN ? AND ? AND emp_id = a.empid ),0) AS absent,IFNULL((SELECT COUNT(a.acc_no) FROM absent_details a INNER JOIN employee_details b ON b.emp_acc = a.acc_no WHERE a.absent_date BETWEEN ? AND ? AND emp_id = a.empid AND a.weekday = 1),0) AS weekend FROM employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid INNER JOIN branch c ON c.branch_id = b.branch_id INNER JOIN departments d ON d.department_id = b.department_id INNER JOIN designation e ON e.designation_id = b.designation_id WHERE b.branch_id = ?', [$fromdate, $todate, $fromdate, $todate, $fromdate, $todate, $fromdate, $todate, $fromdate, $todate, $fromdate, $todate, $branchid]);
                return $result;
            } else {
                $result = DB::select('SELECT a.emp_name, ((SELECT COUNT(emp_id) FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = ?)) AS present, (SELECT IFNULL(SUM(late),0) FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = ?) AS late, (SELECT IFNULL(SUM(OT_time),0) FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = ?) AS ot, (SELECT IFNULL(SUM(early),0) FROM attendance_details WHERE date BETWEEN ? AND ? AND emp_id = ?) AS early, IFNULL((SELECT COUNT(a.acc_no) FROM absent_details a INNER JOIN employee_details b ON b.emp_acc = a.acc_no WHERE a.absent_date BETWEEN ? AND ?  AND b.empid = ?),0) AS absent,IFNULL((SELECT COUNT(a.acc_no) FROM absent_details a INNER JOIN employee_details b ON b.emp_acc = a.acc_no WHERE a.absent_date BETWEEN ? AND ? AND b.empid = ? AND a.weekday = 1),0) AS weekend FROM employee_details a INNER JOIN employee_shift_details b ON b.emp_id = a.empid INNER JOIN branch c ON c.branch_id = b.branch_id INNER JOIN departments d ON d.department_id = b.department_id INNER JOIN designation e ON e.designation_id = b.designation_id WHERE b.branch_id = ? AND a.empid = ?', [$fromdate, $todate, $empid, $empid, $fromdate, $todate, $empid, $fromdate, $todate, $empid, $fromdate, $todate, $empid, $fromdate, $todate, $empid, $fromdate, $todate, $empid, $branchid, $empid]);
                return $result;
            }
        }
    }
    //+ IFNULL((SELECT quantity FROM holidays WHERE emp_id = a.empid),0)
    //+ IFNULL((SELECT quantity FROM holidays WHERE emp_id = ?),0)

    public  function loan_installment($fromdate, $todate, $empid, $loanid)
    {
        $result = DB::select('SELECT * FROM loan_installment WHERE date BETWEEN ? AND ? AND emp_id = ? AND loan_id = ?', [$fromdate, $todate, $empid, $loanid]);
        return $result;
    }

    public  function loans($fromdate, $todate, $empid)
    {
        $result = DB::select('SELECT * from loan_details a INNER JOIN employee_details b ON b.empid = a.emp_id INNER JOIN accessibility_mode c ON c.status_id = a.status_id WHERE a.date BETWEEN ? AND ? AND a.emp_id = ? AND b.status_id = 1', [$fromdate, $todate, $empid]);
        return $result;
    }
    public  function advance($fromdate, $todate, $empid)
    {
        $result = DB::select('SELECT * FROM advance_salary a INNER JOIN employee_details b ON b.empid = a.emp_id INNER JOIN accessibility_mode c ON c.status_id = a.status_id WHERE a.date BETWEEN ? AND ? AND b.status_id = 1 AND a.emp_id = ?', [$fromdate, $todate, $empid]);
        return $result;
    }



    public  function salaries($fromdate, $todate, $branch)
    {
        $branch = ($branch != "" ? $branch : session('branch'));
        $result = DB::select('SELECT a.date, b.emp_name, a.net_salary FROM salary_details a INNER JOIN employee_details b ON b.empid = a.emp_id WHERE a.date BETWEEN ? AND ? AND a.emp_id IN (SELECT emp_id FROM employee_details a INNER JOIN employee_shift_details b ON a.empid = b.emp_id WHERE b.branch_id = ?)', [$fromdate, $todate, $branch]);
        return $result;
    }

    public  function sales_recipts($fromdate, $todate, $branch)
    {
        $branch = ($branch != "" ? $branch : session('branch'));
        $result = DB::select('SELECT a.id, a.date, (c.total_amount + f.discount_amount) AS total_amount, e.name, d.payment_mode  FROM sales_receipts a INNER JOIN sales_receipt_details b ON b.receipt_id = a.id INNER JOIN sales_account_general c ON c.receipt_id = a.id INNER JOIN sales_payment d ON d.payment_id = a.payment_id INNER JOIN customers e ON e.id = a.customer_id INNER JOIN sales_account_subdetails f ON f.receipt_id = a.id WHERE a.opening_id IN (SELECT opening_id FROM sales_opening WHERE date BETWEEN ? AND ? AND terminal_id IN (SELECT terminal_id FROM terminal_details WHERE branch_id = ?)) group by a.id', [$fromdate, $todate, $branch]);
        return $result;
    }

    public  function receiveable_recipts($fromdate, $todate)
    {
        $result = DB::select('SELECT b.created_at, a.id, c.name,b.payment_mode_id, b.debit FROM sales_receipts a INNER JOIN customer_account b ON b.receipt_no = a.id INNER JOIN customers c ON c.id = b.cust_id WHERE b.received = 1 AND a.date BETWEEN ? AND ? AND a.branch = ?', [$fromdate, $todate, session("branch")]);
        return $result;
    }

    public  function receiveable_amount($fromdate, $todate)
    {
        $result = DB::select('SELECT IFNULL(SUM(b.debit),0) AS receivables FROM sales_receipts a INNER JOIN customer_account b ON b.receipt_no = a.id INNER JOIN customers c ON c.id = b.cust_id WHERE b.received = 1 AND a.date BETWEEN ? AND ? AND a.branch = ?', [$fromdate, $todate, session("branch")]);
        return $result;
    }

    public  function expenses_details($fromdate, $todate, $branch)
    {
        $branch = ($branch != "" ? $branch : session('branch'));
        $result = DB::select('SELECT a.date, a.exp_id, a.expense_details, b.expense_category, a.net_amount FROM expenses a INNER JOIN expense_categories b ON b.exp_cat_id = a.exp_cat_id WHERE a.date BETWEEN ? AND ? AND a.branch_id = ?', [$fromdate, $todate, $branch]);
        return $result;
    }

    public  function pruchase_orders($fromdate, $todate, $branch)
    {
        $branch = ($branch != "" ? $branch : session('branch'));
        $result = DB::select('SELECT a.po_no, a.order_date, c.vendor_name, d.name, b.net_amount FROM purchase_general_details a INNER JOIN purchase_account_details b ON b.purchase_id = a.purchase_id INNER JOIN vendors c ON c.id = a.vendor_id INNER JOIN purchase_status d ON d.po_status_id = a.status_id WHERE a.date BETWEEN ? AND ? AND a.branch_id = ?', [$fromdate, $todate, $branch]);
        return $result;
    }

    public  function Customer_receivable($fromdate, $todate)
    {
        $result = DB::select('SELECT IFNULL(SUM((SELECT balance FROM customer_account where cust_account_id = (SELECT MAX(cust_account_id) from customer_account where cust_id = a.id AND created_at BETWEEN ? AND ?))),0) as balance FROM customers a WHERE user_id IN (SELECT user_id FROM user_authorization WHERE company_id = ?)', [$fromdate, $todate, session("company_id")]);
        return $result;
    }

    public  function Customer_receivable_details($fromdate, $todate)
    {
        $result = DB::select('SELECT a.name, IFNULL(SUM((SELECT balance FROM customer_account where cust_account_id = (SELECT MAX(cust_account_id) from customer_account where cust_id = a.id AND created_at BETWEEN ? AND ?))),0) as balance FROM customers a WHERE user_id IN (SELECT user_id FROM user_authorization WHERE company_id = ?) GROUP BY a.id', [$fromdate, $todate, session("company_id")]);
        return $result;
    }

    public  function discounts($fromdate, $todate, $branch)
    {
        $branch = ($branch != "" ? $branch : session('branch'));
        $result = DB::select('SELECT a.receipt_id, b.date, c.name, a.discount_amount, d.payment_mode FROM sales_account_subdetails a INNER JOIN sales_receipts b ON b.id = a.receipt_id INNER JOIN customers c on c.id = b.customer_id INNER JOIN sales_payment d ON d.payment_id = b.payment_id WHERE b.opening_id IN (SELECT opening_id FROM sales_opening WHERE date BETWEEN ? AND ? AND terminal_id IN (SELECT terminal_id FROM terminal_details WHERE branch_id = ?))', [$fromdate, $todate, $branch]);
        return $result;
    }

    public  function sales_return($fromdate, $todate, $branch)
    {
        $branch = ($branch != "" ? $branch : session('branch'));
        $result = DB::select('SELECT a.receipt_id, a.timestamp, c.name, a.amount, d.payment_mode FROM sales_return a INNER JOIN sales_receipts b ON b.id = a.receipt_id INNER JOIN customers c ON c.id = b.customer_id INNER JOIN sales_payment d ON d.payment_id = b.payment_id WHERE a.opening_id IN (SELECT opening_id FROM sales_opening WHERE date BETWEEN ? AND ? AND terminal_id IN (SELECT terminal_id FROM terminal_details WHERE branch_id = ?))', [$fromdate, $todate, $branch]);
        return $result;
    }

    public  function COGS($fromdate, $todate)
    {
        // Total cost has been changes to item price
        $result = DB::select('SELECT a.id, a.date, SUM(b.total_cost) as total_cost, e.name, d.payment_mode FROM sales_receipts a INNER JOIN sales_receipt_details b ON b.receipt_id = a.id INNER JOIN sales_payment d ON d.payment_id = a.payment_id INNER JOIN customers e ON e.id = a.customer_id WHERE a.opening_id IN (SELECT opening_id FROM sales_opening WHERE date BETWEEN ? AND ? AND terminal_id IN (SELECT terminal_id FROM terminal_details WHERE branch_id = ?)) group by a.id', [$fromdate, $todate, session("branch")]);
        return $result;
    }


    //total amounts for proft and loss report

    public  function total_sales($fromdate, $todate, $branch)
    {
        $branch = ($branch != "" ? $branch : session('branch'));
        $result = DB::select("SELECT SUM(a.total_amount ) AS sales FROM sales_receipts a  INNER JOIN sales_account_subdetails c ON c.receipt_id = a.id   WHERE a.opening_id IN (SELECT opening_id FROM sales_opening WHERE date BETWEEN ? AND ? AND terminal_id IN (SELECT terminal_id FROM terminal_details WHERE branch_id = ?))", [$fromdate, $todate, $branch]);
        return $result;
    }


    public  function expenses($fromdate, $todate, $branch)
    {
        $branch = ($branch != "" ? $branch : session('branch'));
        $result = DB::select('SELECT b.expense_category, IFNULL(SUM(a.net_amount),0) AS expenseamt FROM expenses a INNER JOIN expense_categories b ON b.exp_cat_id = a.exp_cat_id WHERE a.date BETWEEN ? AND ? AND a.branch_id = ? GROUP BY b.exp_cat_id', [$fromdate, $todate, $branch]);
        return $result;
    }

    public  function pruchase_amount($fromdate, $todate, $branch)
    {
        $branch = ($branch != "" ? $branch : session('branch'));
        $result = DB::select('SELECT COUNT(a.purchase_id) AS counts, SUM(b.net_amount) AS purchase_amount FROM purchase_general_details a INNER JOIN purchase_account_details b ON b.purchase_id = a.purchase_id WHERE a.date BETWEEN ? AND ? AND a.branch_id = ?', [$fromdate, $todate, $branch]);
        return $result;
    }

    public  function total_salaries($fromdate, $todate, $branch)
    {
        $branch = ($branch != "" ? $branch : session('branch'));
        $result = DB::select('SELECT IFNULL(SUM(a.net_salary),0) AS salaries FROM salary_details a INNER JOIN employee_details b ON b.empid = a.emp_id WHERE a.date BETWEEN ? AND ? AND a.emp_id IN (SELECT emp_id FROM employee_details a INNER JOIN employee_shift_details b ON a.empid = b.emp_id WHERE b.branch_id = ?)', [$fromdate, $todate, $branch]);
        return $result;
    }

    public  function total_discounts($fromdate, $todate, $branch)
    {
        $branch = ($branch != "" ? $branch : session('branch'));
        $result = DB::select('SELECT IFNULL(SUM(a.discount_amount),0) AS discounts FROM sales_account_subdetails a INNER JOIN sales_receipts b ON b.id = a.receipt_id INNER JOIN customers c on c.id = b.customer_id INNER JOIN sales_payment d ON d.payment_id = b.payment_id WHERE b.opening_id IN (SELECT opening_id FROM sales_opening WHERE date BETWEEN ? AND ? AND terminal_id IN (SELECT terminal_id FROM terminal_details WHERE branch_id = ?))', [$fromdate, $todate, $branch]);
        return $result;
    }

    public  function total_sales_return($fromdate, $todate, $branch)
    {
        $branch = ($branch != "" ? $branch : session('branch'));
        $result = DB::select('SELECT IFNULL(SUM(a.amount),0) AS salesreturn FROM sales_return a INNER JOIN sales_receipts b ON b.id = a.receipt_id INNER JOIN customers c ON c.id = b.customer_id INNER JOIN sales_payment d ON d.payment_id = b.payment_id WHERE a.opening_id IN (SELECT opening_id FROM sales_opening WHERE date BETWEEN ? AND ? AND terminal_id IN (SELECT terminal_id FROM terminal_details WHERE branch_id = ?))', [$fromdate, $todate, $branch]);
        return $result;
    }

    public  function total_COGS($fromdate, $todate, $branch)
    {
        $branch = ($branch != "" ? $branch : session('branch'));
        $result = DB::select('SELECT IFNULL(SUM(b.total_cost),0) AS cost FROM sales_receipts a INNER JOIN sales_receipt_details b ON b.receipt_id = a.id INNER JOIN sales_payment d ON d.payment_id = a.payment_id INNER JOIN customers e ON e.id = a.customer_id WHERE a.opening_id IN (SELECT opening_id FROM sales_opening WHERE date BETWEEN ? AND ? AND terminal_id IN (SELECT terminal_id FROM terminal_details WHERE branch_id = ?))', [$fromdate, $todate, $branch]);
        // $result = DB::select('SELECT IFNULL(SUM(b.item_price * b.total_qty),0) AS cost FROM sales_receipts a INNER JOIN sales_receipt_details b ON b.receipt_id = a.id INNER JOIN sales_payment d ON d.payment_id = a.payment_id INNER JOIN customers e ON e.id = a.customer_id WHERE a.opening_id IN (SELECT opening_id FROM sales_opening WHERE date BETWEEN ? AND ? AND terminal_id IN (SELECT terminal_id FROM terminal_details WHERE branch_id = ?))',[$fromdate,$todate,session("branch")]);
        return $result;
    }


    //inventory query
    public  function get_inventory_details($branch, $department = "", $subdepartment = "")
    {
        $filter = "";
        if ($department != "") {
            $filter .= " and a.department_id = " . $department;
        }
        if ($subdepartment != "") {
            $filter .= " and a.sub_department_id = " . $subdepartment;
        }
        if ($branch != "all") {
            $filter .= " and c.branch_id = " . $branch;
        }
        $result = DB::select('SELECT a.id, a.product_name, a.item_code, SUM(c.qty) AS totalqty, SUM(c.balance) AS qty, b.name AS um, (SELECT AVG(e.cost_price) from inventory_stock e WHERE e.product_id = a.id AND e.status_id = 1) AS cost, d.retail_price, c.cost_price,a.image  FROM inventory_general a INNER JOIN inventory_uom b ON b.uom_id = a.uom_id INNER JOIN inventory_stock c ON c.product_id = a.id AND c.status_id = 1 INNER JOIN inventory_price d ON d.product_id = a.id AND d.status_id = 1 WHERE a.status = 1 AND a.company_id = ?  ' . $filter . ' GROUP BY a.id', [session("company_id")]);
        return $result;
    }

    public  function get_inventory_details_with_image($department, $subdepartment, $branch)
    {
        // $filter = "";
        // DB::enableQueryLog();
        // if($subdepartment != ""){
        // 	$filter = " and a.sub_department_id = ".$subdepartment;
        // }
        // $result = DB::select('SELECT a.id, a.product_name, a.item_code,b.department_name,c.sub_depart_name ,a.image,a.url  FROM inventory_general a  INNER JOIN inventory_department b ON b.department_id = a.department_id INNER JOIN inventory_sub_department c ON c.sub_department_id = a.sub_department_id WHERE a.status = 1 AND a.company_id = ? and a.department_id = ? '.$filter.' GROUP BY a.id',[session("company_id"),$department]);
        // return $result;
        $filter = "";
        if ($department != "") {
            $filter .= " and b.department_id = " . $department;
        }
        if ($subdepartment != "") {
            $filter .= " and b.sub_department_id = " . $subdepartment;
        }

        if ($branch !=  "all") {
            $filter .= " and a.branch_id = " . $branch;
        }

        $result = DB::select('SELECT b.id, b.product_name, b.item_code,c.department_name,d.sub_depart_name ,b.image,b.url FROM inventory_stock a INNER JOIN inventory_general b on b.id = a.product_id INNER JOIN inventory_department c ON c.department_id = b.department_id INNER JOIN inventory_sub_department d ON d.sub_department_id = b.sub_department_id where b.company_id = ? ' . $filter, [session("company_id")]);
        // dd(DB::getQueryLog());
        return $result;
    }

    //cash voucher
    public  function cash_voucher($cashid)
    {
        $result = DB::select('SELECT * FROM cash_ledger WHERE branch_id = ? AND id = ?', [session("branch"), $cashid]);
        return $result;
    }

    //get inventory products
    public  function  get_inventory_products($branch, $department, $subdepartment = "")
    {
        $filter = "";
        if ($department != "") {
            $filter .= " and a.department_id = " . $department;
        }
        if ($subdepartment != "") {
            $filter .= " and a.sub_department_id = " . $subdepartment;
        }
        if ($branch != "all") {
            $filter .= " and id IN (SELECT product_id FROM `inventory_stock` where branch_id = $branch )";
        }
        $result = DB::select("SELECT * FROM inventory_general a WHERE a.company_id = ? AND a.status = 1 " . $filter, [session("company_id")]);
        return $result;
    }

    public  function  stock_report_details($productid, $fromdate, $todate, $branch, $department)
    {
        $filter = "";
        if ($department != "") {
            $filter .= " and b.department_id = " . $department;
        }
        if ($branch != "all") {
            $filter .= " and a.branch_id = " . $branch;
        }
        $result = DB::select("SELECT * FROM inventory_stock_report_table a INNER JOIN inventory_general b on b.id = a.product_id WHERE a.product_id = ? AND DATE(a.date) BETWEEN ? AND ? " . $filter, [$productid, $fromdate, $todate]);
        return $result;
    }

    public  function  current_stock_asset($productid)
    {
        $result = DB::select('SELECT IFNULL(SUM(a.balance),0) AS stock, IFNULL(AVG(a.cost_price),0) AS cp FROM inventory_stock a WHERE a.status_id = 1 AND a.product_id = ?', [$productid]);
        return $result;
    }

    public  function  get_branches()
    {

        if (session("roleId") == 2) {
            $result = DB::table('branch')->where("company_id", session("company_id"))->where("status_id", 1)->select("branch_id", "branch_name")->get();
            return $result;
        } else if (session("roleId") == 16) {
            $branchIds = DB::table("user_branches")->where("user_id", auth()->user()->id)->pluck("branch_id");
            $result = DB::table('branch')->whereIn("branch_id", $branchIds)->where("status_id", 1)->select("branch_id", "branch_name")->get();
            return $result;
        } else {
            $result = DB::table('branch')->where("branch_id", session('branch'))->where("branch_id", session("branch"))->where("status_id", 1)->select("branch_id", "branch_name")->get();
            return $result;
        }
    }

    public  function  getPaymentModes()
    {
        $result = DB::table('sales_payment')->whereIn("payment_id", [1, 2])->get();
        return $result;
    }

    //sales decleration report
    public  function  get_terminals()
    {

        if (session("roleId") == 2) {
            $result = DB::select('SELECT * FROM terminal_details WHERE branch_id IN (SELECT branch_id FROM branch WHERE company_id = ?) and status_id = 1', [session("company_id")]);
            return $result;
        } else {
            $result = DB::select('SELECT * FROM terminal_details WHERE branch_id = ? and status_id = 1', [session("branch")]);
            return $result;
        }
    }

    //sales decleration report
    public  function  getTerminals($branch = "")
    {

        if (session("roleId") == 2 && $branch == "all") {
            $result = DB::select('SELECT * FROM terminal_details WHERE branch_id IN (SELECT branch_id FROM branch WHERE company_id = ?) and status_id = 1', [session("company_id")]);
            return $result;
        } else {
            $result = DB::select('SELECT * FROM terminal_details WHERE branch_id = ? and status_id = 1', [$branch]);
            return $result;
        }
    }


    public function sales($terminal, $fromdate, $todate)
    {
        $terminalFilter = " and a.terminal_id = " . ($terminal != "" && $terminal != 0  ? $terminal : "(SELECT terminal_id FROM `terminal_details` where branch_id IN (SELECT branch_id FROM `branch` WHERE `company_id` = " . session('company_id') . "))");
        $result = DB::select("SELECT a.id,a.receipt_no,a.terminal_id,a.actual_amount,a.total_amount,b.sales_tax_amount,a.date,a.fbrInvNumber FROM sales_receipts a INNER JOIN sales_account_subdetails b on b.receipt_id = a.id where a.date BETWEEN ? and ? " . $terminalFilter . " and a.fbrInvNumber IS NOT NULL", [$fromdate, $todate]);
        return $result;
    }

    public function terminalPermission($terminal)
    {
        $result = DB::select("SELECT fbr_sync,srb_sync FROM `users_sales_permission` WHERE `terminal_id` = ?", [$terminal]);
        return $result;
    }

    public function totalSales($terminal, $fromdate, $todate, $type)
    {
        if ($type != "" && $type == "datewise") {
            $filter = " a.date between '" . $fromdate . "' and '" . $todate . "' and a.terminal_id = " . $terminal;
        } else {
            $filter = " opening_id IN (SELECT opening_id FROM `sales_opening` WHERE date between '" . $fromdate . "' and '" . $todate . "' and terminal_id = " . $terminal . ")";
        }
        $terminalFilter = " and a.terminal_id = " . ($terminal != "" && $terminal != 0  ? $terminal : "(SELECT terminal_id FROM `terminal_details` where branch_id IN (SELECT branch_id FROM `branch` WHERE `company_id` = " . session('company_id') . "))");
        $result = DB::select("SELECT (SELECT COUNT(*) FROM `sales_receipt_details` where receipt_id = a.id) as countItems,(SELECT SUM(total_qty) FROM `sales_receipt_details` where receipt_id = a.id) as totalItems,a.id,a.receipt_no,a.terminal_id,a.actual_amount,a.total_amount,b.sales_tax_amount,b.srb,a.date,b.discount_amount,c.name as customer,d.order_mode,a.void_receipt,e.receive_amount FROM sales_receipts a LEFT JOIN sales_account_subdetails b on b.receipt_id = a.id LEFT JOIN customers c on c.id = a.customer_id INNER JOIN sales_order_mode d on d.order_mode_id = a.order_mode_id LEFT JOIN sales_account_general e on e.receipt_id = a.id where " . $filter); //[$fromdate,$todate,$terminal]
        return $result;
    }

    public function receiptDetails($receipt)
    {
        $result = DB::select("SELECT sales_receipt_details.item_code,sales_receipt_details.item_name,sales_receipt_details.item_price,sales_receipt_details.total_qty,sales_receipt_details.total_amount,b.weight_qty FROM `sales_receipt_details` INNER JOIN inventory_general b on b.id = sales_receipt_details.item_code WHERE `receipt_id` = ?", [$receipt]);
        return $result;
    }

    public function exportSales($fromdate, $todate)
    {
        $result = DB::select("SELECT a.id,a.receipt_no,a.terminal_id,a.actual_amount,a.total_amount,b.sales_tax_amount,a.date,a.fbrInvNumber FROM sales_receipts a INNER JOIN sales_account_subdetails b on b.receipt_id = a.id where a.date BETWEEN ? and ? and a.fbrInvNumber IS NOT NULL", [$fromdate, $todate]);
        return $result;
    }

    public function sales_details($terminal, $fromdate, $todate)
    {
        $result = DB::select("SELECT a.opening_id,a.balance as bal,a.date,a.time,a.terminal_id,
		IFNULL((SELECT SUM(b.total_amount) as sales from sales_receipts b where b.opening_id = a.opening_id),0) as TotalSales,
		IFNULL((Select balance from sales_closing where opening_id = a.opening_id),0) as closingBal,
		(Select date from sales_closing where opening_id = a.opening_id) as closingDate,
		(Select time from sales_closing where opening_id = a.opening_id) as closingTime,
		IFNULL((SELECT SUM(b.total_amount) as sales from sales_receipts b where b.opening_id = a.opening_id and b.order_mode_id = 1),0) as TakeAway,
		IFNULL((SELECT SUM(a.total_amount) as sales from sales_receipts a where opening_id = a.opening_id and a.order_mode_id = 3),0) as Delivery,
		IFNULL((SELECT SUM(a.total_amount) as sales from sales_receipts a where opening_id = a.opening_id and a.order_mode_id = 4),0) as Online,
		IFNULL((SELECT SUM(b.total_amount) as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 1),0) as Cash, 
		IFNULL((SELECT SUM(b.total_amount) as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 2),0) as CreditCard, 
		IFNULL((SELECT SUM(b.total_amount) as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 3),0) as CustomerCredit,
		(SELECT SUM(total_cost) FROM sales_receipt_details a INNER JOIN sales_receipts b on b.id = a.receipt_id where b.opening_id = a.opening_id) as cost,
		IFNULL((SELECT SUM(discount_amount) FROM sales_account_subdetails where receipt_id IN (Select id from sales_receipts where opening_id = a.opening_id)),0) as Discount,
		IFNULL((SELECT SUM(delivery_charges) FROM sales_account_subdetails where receipt_id IN (Select id from sales_receipts where opening_id = a.opening_id)),0) as Delivery,
		IFNULL((SELECT SUM(net_amount) FROM `expenses` where opening_id = a.opening_id),0) as Expenses,
		IFNULL((SELECT SUM(amount) FROM sales_return where opening_id = a.opening_id),0) as SalesReturn,
		IFNULL((SELECT SUM(amount) as cashout FROM sales_cash_in where terminal_id = a.terminal_id and opening_id = a.opening_id),0) as cashIn,
		IFNULL((SELECT SUM(amount) as cashout FROM sales_cash_out where terminal_id = a.terminal_id and opening_id = a.opening_id),0) as cashOut,
		IFNULL((SELECT SUM(debit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 1 and received = 1),0) as CashReturn,
		IFNULL((SELECT SUM(debit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 2 and received = 1),0) as CardReturn,
		IFNULL((SELECT SUM(debit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 4 and received = 1),0) as ChequeReturn,
		(Select SUM(receive_amount) from sales_account_general where receipt_id IN 
		(SELECT id as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 3)) as paidByCustomer,
		IFNULL((SELECT SUM(promo_code) FROM sales_account_subdetails where receipt_id IN (Select id from sales_receipts where opening_id = a.opening_id )),0) as promo,
		IFNULL((SELECT SUM(coupon) FROM sales_account_subdetails where receipt_id IN (Select id from sales_receipts where opening_id = a.opening_id )),0) as coupon,
		IFNULL((SELECT SUM(sales_tax_amount) FROM sales_account_subdetails where receipt_id IN (Select id from sales_receipts where opening_id = a.opening_id )),0) as sale_tax,
		IFNULL((SELECT SUM(service_tax_amount) FROM sales_account_subdetails where receipt_id IN(Select id from sales_receipts where opening_id = a.opening_id )),0) as service_tax 
		FROM sales_opening a where a.terminal_id = ? and a.date BETWEEN ? and ?", [$terminal, $fromdate, $todate]);
        return $result;
    }

    public  function  get_terminals_byid($terminalid)
    {
        $result = DB::select('SELECT * FROM terminal_details WHERE terminal_id = ?', [$terminalid]);
        return $result;
    }

    public  function  get_terminals_by_branch($branchId)
    {
        $result = DB::select('SELECT * FROM terminal_details WHERE branch_id = ?', [$branchId]);
        return $result;
    }

    public function get_departments()
    {
        $result = DB::table('inventory_department')->where('company_id', session("company_id"))->where('status', 1)->get();
        return $result;
    }

    public function itemSalesOrderMode($fromdate, $todate, $terminalid)
    {
        $result = DB::select("SELECT e.order_mode_id,e.order_mode as ordermode FROM sales_receipt_details a INNER JOIN sales_receipts b ON b.id = a.receipt_id INNER JOIN sales_order_mode e on e.order_mode_id = b.order_mode_id where receipt_id IN (Select id from sales_receipts where date between ? and ? and terminal_id = ?) GROUP BY e.order_mode_id",[$fromdate, $todate, $terminalid]);
        return $result;
    }
    //item sale database
    public function  itemsale_details($fromdate, $todate, $terminalid, $type, $department, $subdepartment = "")
    {
        $filter = "";
        if ($type != "") {
            $filter .= " and b.order_mode_id = ".$type;
        }
        // if ($type != "" && $type == "datewise") {
        //     $filter = "  a.date between '" . $fromdate . "' and '" . $todate . "' and a.terminal_id = " . $terminalid . "";
        // } else {
        //     $filter = "  a.opening_id IN (SELECT a.opening_id FROM `sales_opening` a WHERE a.date between '" . $fromdate . "' and '" . $todate . "' and a.terminal_id = " . $terminalid . ")";
        // }
        if ($department != "") {
            $filter .= " and c.department_id = " . $department;
        }
        if ($subdepartment != "") {
            $filter .= " and c.sub_department_id = " . $subdepartment;
        }
        // $result = DB::select('SELECT c.id as itemId,c.item_code as code ,c.product_name, SUM(b.total_qty) as qty, SUM(b.total_amount) as amount,item_price as price, b.total_cost as cost,a.void_receipt,c.weight_qty,b.is_sale_return,d.order_status_name,e.order_mode as ordermode FROM sales_receipts a INNER JOIN sales_receipt_details b ON b.receipt_id = a.id INNER JOIN inventory_general c ON c.id = b.item_code INNER JOIN sales_order_status d on d.order_status_id = a.status INNER JOIN sales_order_mode e on e.order_mode_id = a.order_mode_id WHERE ' . $filter . ' GROUP BY b.item_code,a.status');
        $result = DB::select('SELECT c.id as itemId,c.item_code as code ,c.product_name, SUM(a.total_qty) as qty, SUM(a.total_amount) as amount,a.item_price as price, a.total_cost as cost,b.void_receipt,c.weight_qty,a.is_sale_return,d.order_status_name,e.order_mode as ordermode FROM sales_receipt_details a INNER JOIN sales_receipts b ON b.id = a.receipt_id INNER JOIN inventory_general c ON c.id = a.item_code INNER JOIN sales_order_status d on d.order_status_id = b.status INNER JOIN sales_order_mode e on e.order_mode_id = b.order_mode_id where receipt_id IN (Select id from sales_receipts where date between ? and ? and terminal_id = ? and web = 0) '.$filter.' GROUP BY a.item_code,b.status',[$fromdate, $todate, $terminalid]);
        return $result;
    }

    //item sale database query for excel
    public  function  itemsale_details_excel($fromdate, $todate, $terminal)
    {
        $filter = ($terminal != "" ? " and a.terminal_id = $terminal" : " and user_id = " . session("branch"));
        $result = DB::select('SELECT c.product_name, SUM(a.total_qty) as qty, SUM(a.total_amount) as amount, SUM(a.total_cost) as cost,d.terminal_name,c.weight_qty FROM sales_receipt_details a INNER JOIN sales_receipts b on b.id = a.receipt_id INNER JOIN inventory_general c ON c.id = a.item_code INNER JOIN terminal_details d on d.terminal_id = b.terminal_id where b.opening_id IN (SELECT opening_id FROM sales_opening a WHERE a.date BETWEEN ? AND ? ' . $filter . ' ) GROUP BY a.item_code ', [$fromdate, $todate]);
        return $result;
    }

    // sales return database
    public  function  salereturn_details($fromdate, $todate, $terminalid, $code)
    {
        $filter = "";
        if ($code != "") {
            $filter .= " and c.item_code = " . $code;
        }
        $result = DB::select('SELECT d.receipt_no,c.product_name, SUM(qty) as qty, SUM(amount) as amount, e.date,e.time FROM sales_return a  INNER JOIN inventory_general c ON c.id = a.item_id INNER JOIN sales_receipts d on d.id = a.receipt_id INNER JOIN sales_opening e on e.opening_id = a.opening_id WHERE a.opening_id IN (SELECT a.opening_id FROM sales_opening a WHERE a.date BETWEEN ? AND ? AND a.terminal_id = ?) ' . $filter . ' GROUP BY a.receipt_id,a.item_id ', [$fromdate, $todate, $terminalid]);
        return $result;
    }

    // 
    public  function  orderBookingQuery($fromdate, $todate, $paymentmethod, $branch, $mode)
    {
        $filter = "";
        if ($paymentmethod != "") {
            $filter .= " and a.payment_mode_id  = " . $paymentmethod;
        }
        if ($branch != "" and $branch != "all") {
            $filter .= " and b.branch = '" . $branch . "'";
        } else {
            $filter .= " and b.branch IN ( Select branch_id from branch where company_id = " . session("company_id") . ")";
        }
        // if ($mode != "" and $mode == "balances") {
        //     $filter .= " and c.balance_amount > 0";
        // }
        // $query = 'SELECT b.id,b.receipt_no,d.name,c.total_amount,c.receive_amount,c.balance_amount,e.payment_mode FROM customer_account a INNER JOIN sales_receipts b on b.id = a.receipt_no INNER JOIN sales_account_general c on c.receipt_id = a.receipt_no INNER JOIN customers d on d.id = a.cust_id INNER JOIN sales_payment e on e.payment_id = a.payment_mode_id where b.date between ? and ? and b.order_mode_id = 2  ' . $filter;
        // return $query;
        $result = DB::select('SELECT b.id,b.receipt_no,d.name,c.total_amount,c.receive_amount,c.balance_amount,e.payment_mode,(SELECT SUM(received) FROM `customer_account` WHERE `receipt_no` = b.id) as received FROM customer_account a INNER JOIN sales_receipts b on b.id = a.receipt_no INNER JOIN sales_account_general c on c.receipt_id = a.receipt_no INNER JOIN customers d on d.id = a.cust_id INNER JOIN sales_payment e on e.payment_id = a.payment_mode_id where b.date between ? and ? and b.order_mode_id = 2  ' . $filter . " group by a.receipt_no", [$fromdate, $todate]);
        return $result;
    }

    public  function  salesPersonReportQuery($fromdate, $todate, $branch, $salesperson, $status)
    {
        $filter = "";
        if ($salesperson != '' and $salesperson != "all") {
            $filter .= " and a.sales_person_id = " . $salesperson;
        }
        if ($branch != "" and $branch != "all") {
            $filter .= " and a.branch = '" . $branch . "'";
        } else {
            $filter .= " and branch IN (select branch_id from branch where company_id = " . session('company_id') . ")";
        }
        if ($status != "" && $status != "all") {
            $filter .= " and a.status = " . $status;
        }
        $result = DB::select('SELECT a.id,a.receipt_no,b.order_status_name as status,a.date,a.time,c.name,a.total_amount,d.fullname FROM sales_receipts a INNER JOIN sales_order_status b on b.order_status_id = a.status INNER JOIN customers c on c.id = a.customer_id INNER JOIN user_details d on d.id = a.sales_person_id WHERE a.date between ? and ?  ' . $filter, [$fromdate, $todate]);
        return $result;
    }

    public  function  salesPersonReportQueryByStatus($fromdate, $todate, $branch, $salesperson, $status)
    {
        $filter = "";
        if ($salesperson != '' and $salesperson != "all") {
            $filter .= " and a.sales_person_id = " . $salesperson;
        }
        if ($branch != "" and $branch != "all") {
            $filter .= " and a.branch = '" . $branch . "'";
        } else {
            $filter .= " and branch IN (select branch_id from branch where company_id = " . session('company_id') . ")";
        }
        if ($status != "" && $status != "all") {
            $filter .= " and a.status = " . $status;
        }
        $result = DB::select('SELECT COUNT(*) as totalorders,SUM(a.total_amount) as totalamount,b.order_status_name as status FROM sales_receipts a INNER JOIN sales_order_status b on b.order_status_id = a.status INNER JOIN customers c on c.id = a.customer_id INNER JOIN user_details d on d.id = a.sales_person_id WHERE a.date between ? and ?  ' . $filter.' group by b.order_status_name', [$fromdate, $todate]);
        return $result;
    }

    public  function  totalsalesPersonReportQuery($fromdate, $todate, $branch, $salesperson, $status)
    {
        $filter = "";
        if ($salesperson != '' and $salesperson != "all") {
            $filter .= " and a.sales_person_id = " . $salesperson;
        }
        if ($branch != "" and $branch != "all") {
            $filter .= " and a.branch = '" . $branch . "'";
        } else {
            $filter .= " and branch IN (select branch_id from branch where company_id = " . session('company_id') . ")";
        }
        if ($status != "" && $status != "all") {
            $filter .= " and a.status = " . $status;
        }
        $result = DB::select('SELECT d.id,d.fullname FROM sales_receipts a INNER JOIN sales_order_status b on b.order_status_id = a.status INNER JOIN customers c on c.id = a.customer_id INNER JOIN user_details d on d.id = a.sales_person_id WHERE a.date between ? and ?  ' . $filter . ' group by d.fullname', [$fromdate, $todate]);
        return $result;
    }

    //physcial inventory count

    public  function  physical_inventory($departid)
    {
        if ($departid == 0) {
            $result = DB::select('SELECT a.product_name, a.product_description, (SELECT SUM(b.balance) FROM inventory_stock b WHERE b.product_id = a.id AND b.status_id = 1) AS stock, c.name AS uom, e.vendor_name FROM inventory_general a INNER JOIN inventory_uom c ON c.uom_id = a.uom_id INNER JOIN vendor_product d ON d.product_id = a.id AND d.status = 1 INNER JOIN vendors e ON e.id = d.vendor_id WHERE a.company_id = ? AND a.status = 1', [session("company_id")]);
            return $result;
        } else {
            $result = DB::select('SELECT a.product_name, a.product_description, (SELECT SUM(b.balance) FROM inventory_stock b WHERE b.product_id = a.id AND b.status_id = 1) AS stock, c.name AS uom, e.vendor_name, f.department_name FROM inventory_general a INNER JOIN inventory_uom c ON c.uom_id = a.uom_id INNER JOIN vendor_product d ON d.product_id = a.id AND d.status = 1 INNER JOIN vendors e ON e.id = d.vendor_id INNER JOIN inventory_department f ON f.department_id = a.department_id WHERE a.company_id = ? AND a.status = 1 AND a.department_id = ?', [session("company_id"), $departid]);
            return $result;
        }
    }

    //stock adjustment report

    public  function  stockadjustment($fromdate, $todate, $branch = "")
    {
        $filter  = "";
        if ($branch != "" && $branch != "all") {
            $filter .= " and c.branch_id = " . $branch;
        }
        $result = DB::select('SELECT c.grn_id,b.item_code,b.product_name,a.narration,a.adjustment_mode,a.qty,d.name,a.date FROM inventory_stock_report_table a INNER JOIN inventory_general b on b.id = a.product_id INNER JOIN inventory_stock c on c.stock_id = a.foreign_id INNER JOIN inventory_uom d on d.uom_id = b.uom_id  WHERE Date(a.date) BETWEEN ? AND ? AND a.adjustment_mode != "NULL" AND a.branch_id IN (SELECT branch.branch_id FROM branch WHERE branch.company_id = ?) ' . $filter . " order by a.date DESC", [$fromdate, $todate, session("company_id")]);
        return $result;
    }

    public  function customerAgingQuery()
    {
        $result = DB::select('Select b.id,b.name,b.mobile,MAX(date) as lastorderdate from sales_receipts a INNER JOIN customers b on b.id = a.customer_id where branch = ? group by customer_id order by lastorderdate ASC', [session("branch")]);
        return $result;
    }

    public function expense_report($cat, $first, $second)
    {
        $filter = "";
        if ($cat != "") {
            if ($filter == "") {
                $filter .= " and a.exp_cat_id = " . $cat;
            } else {
                $filter .= " and a.exp_cat_id = " . $cat;
            }
        }
        if ($first != "") {
            if ($filter == "") {
                $filter .= " and date(a.created_at) BETWEEN '" . $first . "' and '" . $second . "' ";
            } else {
                $filter .= " and date(a.created_at) BETWEEN '" . $first . "' and '" . $second . "' ";
            }
        }

        $result = DB::select('SELECT a.exp_id,a.date,b.expense_category,a.expense_details,SUM(a.net_amount) as balance FROM expenses a INNER JOIN expense_categories b on b.exp_cat_id = a.exp_cat_id WHERE a.branch_id = ? ' . $filter . ' GROUP BY date(a.created_at),b.expense_category', [session('branch')]);
        return $result;
    }

    public function getSalesItemByDate($from, $to, $company, $branch)
    {
        $filter = "";
        $datefilter = "";
        if (session("roleId") == 2) {
            $filter = "and b.branch IN ( Select branch_id from branch where company_id = " . $company . ")";
            $datefilter = "and b.opening_id IN (SELECT terminal_id FROM `terminal_details` where branch_id IN (Select branch_id from branch where company_id = " . $company . "))";
        } else {
            $filter = "and b.branch = " . $branch;
            $datefilter = "and b.opening_id IN (SELECT terminal_id FROM `terminal_details` where branch_id IN (Select branch_id from branch where branch_id = " . $branch . "))";
        }
        return DB::select("SELECT b.id,a.item_code,a.item_name,SUM(a.total_qty) as totalqty,c.recipy_id,a.receipt_id,b.opening_id,b.date FROM sales_receipt_details a INNER JOIN sales_receipts b on b.id = a.receipt_id LEFT JOIN recipy_general c on c.product_id = a.item_code WHERE b.date between ? and ? and c.recipy_id > 0 " . $filter . " group by a.item_code", [$from, $to]);
    }

    public function getRecipeDetails($company)
    {
        return DB::select("SELECT a.recipy_id,a.item_id,a.mode_id,a.usage_qty,b.product_name,c.name as uom FROM recipy_details a INNER JOIN inventory_general b on b.id = a.item_id INNER JOIN inventory_uom c on c.uom_id = b.uom_id  where recipy_id IN (Select recipy_id from recipy_general where branch_id IN (Select branch_id from branch where company_id = ?) and status_id = 1)", [$company]);
    }

    public function getInventories($inventArray)
    {
        return DB::table("inventory_general")->join("inventory_uom", "inventory_uom.uom_id", "=", "inventory_general.uom_id")->whereIn("id", $inventArray)->get();
    }

    public function getAllItemUsage($from, $to)
    {
        return DB::select("SELECT b.product_name,c.name as uom,SUM(a.usage_qty) as usageQty,SUM(a.total_qty) as totalQty,SUM(a.total_usage) as totalUsage,(SELECT AVG(cost_price) FROM `inventory_stock` where product_id = b.id) as cost,SUM(a.previous_stock) as previous_stock,SUM(a.current_stock) as current_stock,SUM(d.wastage) as wastage,SUM(d.stock) as closing_stock FROM daily_recipe_usage a 
		INNER JOIN inventory_general b on b.id = a.item_id 
		INNER JOIN inventory_uom c on c.uom_id = b.uom_id 
		LEFT JOIN physical_stock_taking d on d.inventory_id = a.item_id and d.entry_date between ? and ?
		where a.original_date between ? and ?
		group by a.item_id;", [$from, $to, $from, $to]);
    }
}
