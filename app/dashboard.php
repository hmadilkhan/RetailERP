<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class dashboard extends Model
{

    public function getCustomersCount()
    {
        if (session("roleId") == 2) {
            $customers = DB::table('customers')
                ->join('user_authorization', 'user_authorization.user_id', '=', 'customers.user_id')
                ->where('user_authorization.company_id', session('company_id'))
                ->count('id');
        } else {
            $customers = DB::table('customers')
                ->join('user_authorization', 'user_authorization.user_id', '=', 'customers.user_id')
                ->where('user_authorization.branch_id', session('branch'))
                ->count('id');
        }

        return $customers;
    }

    public function getVendorsCount()
    {
        $vendors = DB::table('vendors')->where('user_id', session('company_id'))->count('id');
        return $vendors;
    }

    public function getmastersCount()
    {
        $masters = DB::table('masters')->count('id');
        return $masters;
    }

    public function getTotalItems()
    {
        $products = DB::select("SELECT COUNT(id) as products from inventory_general where status = 1 and company_id = ?", [session('company_id')]);
        return $products;
    }

    public function getMostSalesProduct()
    {
        if (session("roleId") == 2) {
            $products = DB::select('SELECT b.product_name,count(a.item_code) as count FROM sales_receipt_details a INNER JOIN inventory_general b on b.id = a.item_code where b.company_id = ? group by a.item_code order by count DESC LIMIT 5', [session("company_id")]);
        } else {
            $products = DB::select('SELECT b.product_name,count(a.item_code) as count FROM sales_receipt_details a INNER JOIN inventory_general b on b.id = a.item_code INNER JOin sales_receipts c on c.id = a.receipt_id where c.branch = ? group by a.item_code order by count DESC LIMIT 5', [session("branch")]);
        }


        return $products;
    }

    public function getMonthsSales()
    {
        if (session("roleId") == 2) {
            $month = DB::select("SELECT MONTHNAME(date) AS 'MonthName',SUM(total_amount) as amount FROM `sales_receipts` WHERE branch IN (Select branch_id from branch where company_id = ?) GROUP by date", [session("company_id")]);
        } else {
            $month = DB::select("SELECT MONTHNAME(date) AS 'MonthName',SUM(total_amount) as amount FROM `sales_receipts` WHERE branch = ? GROUP by date", [session("branch")]);
        }

        return $month;
    }

    public function getYearlySales()
    {
        if (session("roleId") == 2) {
            $year = DB::select("SELECT YEAR(date) AS year,SUM(total_amount)as amount FROM `sales_receipts` where branch IN (Select branch_id from branch where company_id = ?) GROUP by YEAR(date)", [session("company_id")]);
        } else {
            $year = DB::select("SELECT YEAR(date) AS year,SUM(total_amount)as amount FROM `sales_receipts` where branch = ? GROUP by YEAR(date)", [session("branch")]);
        }

        return $year;
    }

    public function orderStatus()
    {
        $result = DB::select("SELECT COUNT(id) as total,(SELECT COUNT(id) FROM sales_receipts where status = 1) as pending,(SELECT COUNT(id) FROM sales_receipts where status = 2) as processing,(SELECT COUNT(id) FROM sales_receipts where status = 3) as ready,(SELECT COUNT(id) FROM sales_receipts where status = 4) as delivery,(SELECT COUNT(id) FROM sales_receipts where status = 5) as cancelled FROM sales_receipts");
        return $result;
    }

    public function branches()
    {
        if (session("roleId") == 2) {
            $result = DB::select("SELECT c.*,(SELECT COALESCE(SUM(a.total_amount),0) AS sales   from sales_receipts a  where opening_id IN (Select opening_id as opening_id from sales_opening a where user_id = c.branch_id and status = 1) and a.status !=12) as sales,'branch' as identify from branch c where c.company_id = ? and c.status_id = 1", [session("company_id")]);
            return $result;
        } else {
            $result = DB::select("SELECT c.*,b.*,(SELECT  COALESCE(SUM(a.total_amount),0) AS sales  from sales_receipts a where opening_id IN (Select opening_id as opening_id from sales_opening a where user_id = c.branch_id and status = 1) and a.status !=12   and a.terminal_id = b.terminal_id) as sales,'terminal' as identify from branch c  INNER JOIN terminal_details b
    ON b.branch_id = c.branch_id  where c.branch_id = ? and c.status_id = 1 and b.status_id = 1", [session("branch")]);
            return $result;
        }
        // $result = DB::table("branch")->where("company_id",session("company_id"))->get();

    }

    public function getDeclarationsNumber($date, $terminal)
    {
        $declarations =  DB::select("SELECT * FROM `sales_opening` where date = ? and terminal_id = ? and status = 2", [$date, $terminal]);
        return $declarations;
    }

    public function branchesForClosedSales()
    {
        if (session("roleId") == 2) {
            $result = DB::select("SELECT c.*,(SELECT COALESCE(SUM(a.total_amount),0) AS sales   from sales_receipts a  where opening_id IN (Select opening_id as opening_id from sales_opening a where `date` = '" . date('Y-m-d', strtotime('-1 days')) . "' AND user_id = c.branch_id and status = 2)) as sales,'branch' as identify from branch c where c.company_id = ? and c.status_id = 1", [session("company_id")]);
            return $result;
        } else {
            $result = DB::select("SELECT c.*,b.*,(SELECT  COALESCE(SUM(a.total_amount),0) AS sales  from sales_receipts a where opening_id IN (Select opening_id as opening_id from sales_opening a where `date` = '" . date('Y-m-d', strtotime('-1 days')) . "' AND user_id = c.branch_id and status = 2)   and a.terminal_id = b.terminal_id) as sales,'terminal' as identify from branch c INNER JOIN terminal_details b
			ON b.branch_id = c.branch_id where c.branch_id = ? and c.status_id = 1 and b.status_id = 1", [session("branch")]);
            return $result;
        }
    }

    public function sales()
    {
        if (session("roleId") == 2) {
            //            $result = DB::select("SELECT a.*,IFNULL((SELECT SUM(a.total_amount) as sales from sales_receipts a where opening_id IN (Select opening_id as opening_id from sales_opening a where user_id = (Select user_id from user_authorization where branch_id = a.branch_id LIMIT 1)) and payment_id = 1),0) as cash,IFNULL((SELECT SUM(a.total_amount) as sales from sales_receipts a where opening_id IN (Select opening_id as opening_id from sales_opening a where user_id = (Select user_id from user_authorization where branch_id = a.branch_id LIMIT 1)) and payment_id = 2),0) as CreditCard,IFNULL((SELECT SUM(a.total_amount) as sales from sales_receipts a where opening_id IN (Select opening_id as opening_id from sales_opening a where user_id = (Select user_id from user_authorization where branch_id = a.branch_id LIMIT 1)) and payment_id = 3),0) as CustomerCredit from branch a where a.company_id = ?",[session("company_id")]);
            $result = DB::select("Select a.terminal_id,a.terminal_name,IFNULL((SELECT SUM(z.total_amount) from sales_receipts z where z.opening_id = b.opening_id and z.payment_id = 1),0) as cash,IFNULL((SELECT SUM(z.total_amount) from sales_receipts z where z.opening_id = b.opening_id and z.payment_id = 2),0) as creditCard,IFNULL((SELECT SUM(z.total_amount) from sales_receipts z where z.opening_id = b.opening_id and z.payment_id = 3),0) as CustomerCredit from terminal_details a INNER JOIN sales_opening b on b.terminal_id = a.terminal_id and b.status=1 where a.branch_id IN (Select branch_id from branch where company_id = ?)", [session("company_id")]);
            return $result;
        } else {
            //            $result = DB::select("SELECT a.*,IFNULL((SELECT SUM(a.total_amount) as sales from sales_receipts a where opening_id IN (Select opening_id as opening_id from sales_opening a where user_id = (Select user_id from user_authorization where branch_id = a.branch_id LIMIT 1)) and payment_id = 1),0) as cash,IFNULL((SELECT SUM(a.total_amount) as sales from sales_receipts a where opening_id IN (Select opening_id as opening_id from sales_opening a where user_id = (Select user_id from user_authorization where branch_id = a.branch_id LIMIT 1)) and payment_id = 2),0) as CreditCard,IFNULL((SELECT SUM(a.total_amount) as sales from sales_receipts a where opening_id IN (Select opening_id as opening_id from sales_opening a where user_id = (Select user_id from user_authorization where branch_id = a.branch_id LIMIT 1)) and payment_id = 3),0) as CustomerCredit from branch a where a.branch_id = ?",[session("branch")]);
            $result = DB::select("Select a.terminal_id,a.terminal_name,IFNULL((SELECT SUM(z.total_amount) from sales_receipts z where z.opening_id = b.opening_id and z.payment_id = 1),0) as cash,IFNULL((SELECT SUM(z.total_amount) from sales_receipts z where z.opening_id = b.opening_id and z.payment_id = 2),0) as creditCard,IFNULL((SELECT SUM(z.total_amount) from sales_receipts z where z.opening_id = b.opening_id and z.payment_id = 3),0) as CustomerCredit from terminal_details a INNER JOIN sales_opening b on b.terminal_id = a.terminal_id and b.status=1 where a.branch_id  = ?", [session("branch")]);
            return $result;
        }
    }

    public function monthsales()
    {
        $result = DB::select("SELECT SUM(a.total_amount) as total,a.date,b.branch_name,a.branch FROM sales_receipts a INNER JOIN branch b on b.branch_id = a.branch where a.branch IN (Select branch_id from branch where company_id = ?) group by a.branch,MONTH(a.date) LIMIT 25", [session("company_id")]);
        return $result;
    }

    public function getTerminalsByBranch($branch, $status = "")
    {
        if ($status == "branch") {
            $result = DB::table("terminal_details")->where("branch_id", $branch)->where("status_id", 1)->get();
        } else if ($status == "terminal") {
            $result = DB::table("terminal_details")->where("terminal_id", $branch)->where("status_id", 1)->get();
        } else {
            $result = DB::table("terminal_details")->where("branch_id", $branch)->where("status_id", 1)->get();
        }

        return $result;
    }


    public function headsDetails($terminal)
    {
        $result = DB::select("SELECT a.opening_id,a.status,a.balance as bal,a.date,a.time,a.terminal_id,a.user_id,IFNULL((SELECT SUM(b.actual_amount) as sales from sales_receipts b where b.opening_id = a.opening_id),0) as TotalSales,IFNULL((Select balance from sales_closing where opening_id = a.opening_id),0) as closingBal,IFNULL((Select date from sales_closing where opening_id = a.opening_id),0) as closingDate,IFNULL((Select time from sales_closing where opening_id = a.opening_id),0) as closingTime,(Select date from sales_closing where opening_id = a.opening_id) as closingDate,(Select time from sales_closing where opening_id = a.opening_id) as closingTime,IFNULL((SELECT SUM(b.actual_amount) as sales from sales_receipts b where b.opening_id = a.opening_id and b.order_mode_id = 1),0) as TakeAway,IFNULL((SELECT
        SUM(b.actual_amount) AS sales
      FROM
        sales_receipts b
      WHERE opening_id = a.opening_id
        AND b.order_mode_id = 3),0) as Delivery,
    IFNULL((SELECT
        SUM(b.actual_amount) AS sales
      FROM
        sales_receipts b
      WHERE opening_id = a.opening_id
        AND b.payment_id = 1
        AND b.void_receipt = 1),0) as VoidReceiptsCash,
    IFNULL((SELECT
        SUM(b.actual_amount) AS sales
      FROM
        sales_receipts b
      WHERE opening_id = a.opening_id
      AND b.payment_id = 2
        AND b.void_receipt = 1),0) as VoidReceiptsCard,IFNULL((SELECT SUM(b.actual_amount) as sales from sales_receipts b where opening_id = a.opening_id and b.order_mode_id = 4),0) as Online,IFNULL((SELECT SUM(b.actual_amount) as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 1),0) as Cash,
IFNULL((SELECT SUM(b.actual_amount) as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 2) + (Select SUM(credit_card_transaction) from sales_account_subdetails where receipt_id IN (SELECT id as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 2)),0) as CreditCard,
IFNULL((SELECT SUM(b.actual_amount) as sales from sales_receipts b where b.opening_id = a.opening_id ),0)  as CustomerCredit,
(SELECT SUM(total_cost) FROM sales_receipt_details a INNER JOIN sales_receipts b on b.id = a.receipt_id where b.opening_id = a.opening_id) as cost,IFNULL((SELECT SUM(discount_amount) FROM sales_account_subdetails where receipt_id IN (Select id from sales_receipts where opening_id = a.opening_id and void_receipt = 0)),0) as Discount,IFNULL((SELECT SUM(amount) FROM sales_return where opening_id = a.opening_id),0) as SalesReturn,IFNULL((SELECT SUM(amount) as cashout FROM sales_cash_in where terminal_id = a.terminal_id and opening_id = a.opening_id),0) as cashIn,IFNULL((SELECT SUM(amount) as cashout FROM sales_cash_out where terminal_id = a.terminal_id and opening_id = a.opening_id),0) as cashOut,IFNULL((SELECT SUM(debit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 1 and received = 1),0) as CashReturn,IFNULL((SELECT SUM(debit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 2 and received = 1),0) as CardReturn,IFNULL((SELECT SUM(debit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 4 and received = 1),0) as ChequeReturn,IFNULL((Select SUM(receive_amount) from sales_account_general where receipt_id IN (SELECT id as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 3 and b.payment_id = 3 and b.status != 12 and b.is_sale_return = 0)),0) as paidByCustomer,(SELECT SUM(b.discount_amount) FROM sales_receipts c inner join sales_account_subdetails b on b.receipt_id = c.id where c.opening_id = a.opening_id and c.payment_id IN(2,3)) as CardCustomerDiscount, (SELECT
    SUM(b.credit_card_transaction)
  FROM
    sales_receipts c
    INNER JOIN sales_account_subdetails b
      ON b.receipt_id = c.id
  WHERE c.opening_id = a.opening_id
    AND c.payment_id IN (2, 3)) AS credit_card_transaction,
    (SELECT SUM(credit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 1 and received = 0) as adv_booking_cash,
    (SELECT SUM(credit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 2 and received = 0 ) as adv_booking_card,
    (SELECT SUM(received) FROM customer_account  where opening_id = a.opening_id and payment_mode_id = 1 and total_amount = 0) AS order_delivered_cash,
    (SELECT SUM(received) FROM customer_account  where opening_id = a.opening_id and payment_mode_id = 2) AS order_delivered_card,
    (SELECT SUM(net_amount) FROM `expenses` where terminal_id = a.terminal_id and opening_id = a.opening_id) as expenses,
    (SELECT SUM(sales_tax_amount) FROM `sales_account_subdetails` where receipt_id IN (Select id from sales_receipts where terminal_id = a.terminal_id and opening_id = a.opening_id)) as fbr,
    (SELECT SUM(srb) FROM `sales_account_subdetails` where receipt_id IN (Select id from sales_receipts where terminal_id = a.terminal_id and opening_id = a.opening_id)) as srb 
     FROM sales_opening a where a.status = 1 and a.terminal_id = ?", [$terminal]); //.($status == 'close' ? ' order by a.opening_id DESC LIMIT 1' : '')


        return $result;
    }

    public function getheadsDetailsFromOpeningIdForClosing($openingId)
    {
        $result = DB::select("SELECT a.opening_id,a.status,a.balance as bal,a.date,a.time,a.terminal_id,a.user_id,IFNULL((SELECT SUM(b.actual_amount) as sales from sales_receipts b where b.opening_id = a.opening_id ),0) as TotalSales,IFNULL((Select balance from sales_closing where opening_id = a.opening_id),0) as closingBal,(Select date from sales_closing where opening_id = a.opening_id) as closingDate,(Select time from sales_closing where opening_id = a.opening_id) as closingTime,IFNULL((SELECT SUM(b.actual_amount) as sales from sales_receipts b where b.opening_id = a.opening_id and b.order_mode_id = 1),0) as TakeAway,IFNULL((SELECT
        SUM(b.actual_amount) AS sales
      FROM
        sales_receipts b
      WHERE opening_id = a.opening_id
        AND b.order_mode_id = 3),0) as Delivery,
        IFNULL((SELECT SUM(b.actual_amount) AS sales FROM sales_receipts b WHERE opening_id = a.opening_id AND b.void_receipt = 1 and b.payment_id = 1),0) as VoidReceiptsCash,
        IFNULL((SELECT SUM(b.actual_amount) AS sales FROM sales_receipts b WHERE opening_id = a.opening_id AND b.void_receipt = 1 and b.payment_id = 2),0) as VoidReceiptsCard,
        IFNULL((SELECT SUM(b.actual_amount) as sales from sales_receipts b where opening_id = a.opening_id and b.order_mode_id = 4),0) as Online,
        IFNULL((SELECT SUM(b.actual_amount) as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 1 and b.status != 12),0) as Cash,
        IFNULL((SELECT SUM(b.actual_amount) as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 2 and b.status != 12) + (Select SUM(credit_card_transaction) from sales_account_subdetails where receipt_id IN (SELECT id as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 2 and b.status != 12)),0) as CreditCard,
        IFNULL((SELECT SUM(b.actual_amount) as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 3 and b.status != 12),0)  as CustomerCredit,
        (SELECT SUM(total_cost) FROM sales_receipt_details a INNER JOIN sales_receipts b on b.id = a.receipt_id where b.opening_id = a.opening_id) as cost,
        IFNULL((SELECT SUM(discount_amount) FROM sales_account_subdetails where receipt_id IN (Select id from sales_receipts where opening_id = a.opening_id and void_receipt = 0)),0) as Discount,
        IFNULL((SELECT SUM(amount) FROM sales_return where opening_id = a.opening_id),0) as SalesReturn,
        IFNULL((SELECT SUM(amount) as cashout FROM sales_cash_in where terminal_id = a.terminal_id and opening_id = a.opening_id),0) as cashIn,
        IFNULL((SELECT SUM(amount) as cashout FROM sales_cash_out where terminal_id = a.terminal_id and opening_id = a.opening_id),0) as cashOut,
        IFNULL((SELECT SUM(debit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 1 and received = 1),0) as CashReturn,
        IFNULL((SELECT SUM(debit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 2 and received = 1),0) as CardReturn,
        IFNULL((SELECT SUM(debit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 4 and received = 1),0) as ChequeReturn,
        IFNULL((Select SUM(receive_amount) from sales_account_general where receipt_id IN (SELECT id as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 3 and b.status != 12 and b.is_sale_return = 0)),0) as paidByCustomer,
        (SELECT SUM(b.discount_amount) FROM sales_receipts c inner join sales_account_subdetails b on b.receipt_id = c.id where c.opening_id = a.opening_id and c.payment_id IN(2,3)) as CardCustomerDiscount, 
        (SELECT SUM(b.credit_card_transaction) FROM sales_receipts c INNER JOIN sales_account_subdetails b ON b.receipt_id = c.id WHERE c.opening_id = a.opening_id
        AND c.payment_id IN (2, 3)) AS credit_card_transaction,
        (SELECT SUM(credit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 1 and received = 0) as adv_booking_cash,
        (SELECT SUM(credit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 2 and received = 0 ) as adv_booking_card,
        (SELECT SUM(received) FROM customer_account  where opening_id = a.opening_id and payment_mode_id = 1 and total_amount = 0) AS order_delivered_cash,
        (SELECT SUM(received) FROM customer_account  where opening_id = a.opening_id and payment_mode_id = 2) AS order_delivered_card,
        (SELECT SUM(sales_tax_amount) FROM `sales_account_subdetails` where receipt_id IN (Select id from sales_receipts where terminal_id = a.terminal_id and opening_id = a.opening_id)) as fbr,
        (SELECT SUM(srb) FROM `sales_account_subdetails` where receipt_id IN (Select id from sales_receipts where terminal_id = a.terminal_id and opening_id = a.opening_id)) as srb, 
        (SELECT IFNULL(SUM(net_amount),0) FROM `expenses` where terminal_id = a.terminal_id and opening_id = a.opening_id) as expenses  FROM sales_opening a where a.status = 2 and a.opening_id = ?", [$openingId]);


        return $result;
    }

    public function lastDayDetails($terminal)
    {
        $result = DB::select("SELECT a.opening_id,a.balance as bal,a.date,a.time,a.terminal_id,a.user_id,
        IFNULL((SELECT SUM(b.total_amount) as sales from sales_receipts b where b.opening_id = a.opening_id),0) as TotalSales,
        IFNULL((Select balance from sales_closing where opening_id = a.opening_id),0) as closingBal,
        (Select date from sales_closing where opening_id = a.opening_id) as closingDate,
        (Select time from sales_closing where opening_id = a.opening_id) as closingTime,
        IFNULL((SELECT SUM(b.total_amount) as sales from sales_receipts b where b.opening_id = a.opening_id and b.order_mode_id = 1),0) as TakeAway,
        IFNULL((SELECT SUM(b.total_amount) AS sales FROM sales_receipts b WHERE opening_id = a.opening_id AND b.order_mode_id = 3),0) as Delivery,

        IFNULL((SELECT SUM(b.total_amount) AS sales FROM sales_receipts b WHERE opening_id = a.opening_id AND b.void_receipt = 1 AND b.payment_id = 1),0) as VoidReceiptsCash,
        IFNULL((SELECT SUM(b.total_amount) AS sales FROM sales_receipts b WHERE opening_id = a.opening_id AND b.void_receipt = 1 AND b.payment_id = 2),0) as VoidReceiptsCard,
        IFNULL((SELECT SUM(b.total_amount) as sales from sales_receipts b where opening_id = a.opening_id and b.order_mode_id = 4),0) as Online,
        IFNULL((SELECT SUM(b.total_amount) as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 1),0) as Cash,
        IFNULL((SELECT SUM(b.total_amount) as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 2)+ (SELECT SUM(credit_card_transaction) FROM sales_account_subdetails WHERE receipt_id IN
          (SELECT
            id AS sales
          FROM
            sales_receipts b
          WHERE b.opening_id = a.opening_id
            AND b.payment_id = 2)) ,0) as CreditCard,IFNULL((SELECT SUM(b.total_amount) as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 3),0) as CustomerCredit,(SELECT SUM(total_cost) FROM sales_receipt_details a INNER JOIN sales_receipts b on b.id = a.receipt_id where b.opening_id = a.opening_id) as cost,IFNULL((SELECT SUM(discount_amount) FROM sales_account_subdetails where receipt_id IN (Select id from sales_receipts where opening_id = a.opening_id)),0) as Discount,IFNULL((SELECT SUM(amount) FROM sales_return where opening_id = a.opening_id),0) as SalesReturn,IFNULL((SELECT SUM(amount) as cashout FROM sales_cash_in where terminal_id = a.terminal_id and opening_id = a.opening_id),0) as cashIn,IFNULL((SELECT SUM(amount) as cashout FROM sales_cash_out where terminal_id = a.terminal_id and opening_id = a.opening_id),0) as cashOut,IFNULL((SELECT SUM(debit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 1 and received = 1),0) as CashReturn,IFNULL((SELECT SUM(debit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 2 and received = 1),0) as CardReturn,IFNULL((SELECT SUM(debit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 4 and received = 1),0) as ChequeReturn,IFNULL((Select SUM(receive_amount) from sales_account_general where receipt_id IN (SELECT id as sales from sales_receipts b where b.opening_id = a.opening_id and b.payment_id = 3 and b.status != 12 and b.is_sale_return = 0)),0) as paidByCustomer,(SELECT SUM(b.discount_amount) FROM sales_receipts c inner join sales_account_subdetails b on b.receipt_id = c.id where c.opening_id = a.opening_id and c.payment_id IN(2,3)) as CardCustomerDiscount, 
            (SELECT SUM(b.credit_card_transaction) FROM sales_receipts c
    INNER JOIN sales_account_subdetails b
      ON b.receipt_id = c.id
  WHERE c.opening_id = a.opening_id
    AND c.payment_id IN (2, 3)) AS credit_card_transaction,
    (SELECT SUM(sales_tax_amount) FROM `sales_account_subdetails` where receipt_id IN (Select id from sales_receipts where terminal_id = a.terminal_id and opening_id = a.opening_id)) as fbr,
    (SELECT SUM(srb) FROM `sales_account_subdetails` where receipt_id IN (Select id from sales_receipts where terminal_id = a.terminal_id and opening_id = a.opening_id)) as srb,
    (SELECT SUM(credit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 1 and received = 0) as adv_booking_cash,
    (SELECT SUM(credit) FROM customer_account where opening_id = a.opening_id and payment_mode_id = 2 and received = 0 ) as adv_booking_card,
    (SELECT SUM(received) FROM customer_account  where opening_id = a.opening_id and payment_mode_id = 1 and total_amount = 0) AS order_delivered_cash,
    (SELECT SUM(received) FROM customer_account  where opening_id = a.opening_id and payment_mode_id = 2) AS order_delivered_card,
    (SELECT SUM(net_amount) FROM `expenses` where terminal_id = a.terminal_id and opening_id = a.opening_id) as expenses FROM sales_opening a where a.opening_id  = (Select MAX(opening_id) from sales_opening where terminal_id = ?) and a.terminal_id = ?", [$terminal, $terminal]);

    return $result;
    }

    public function getDetailsByMode($opening, $terminal, $mode)
    {
        $result = DB::select("SELECT a.id,a.receipt_no,a.total_item_qty,(a.total_amount + ( SELECT SUM(discount_amount) FROM sales_account_subdetails sas WHERE sas.receipt_id = a.id)) as ActualReceiptAmount,( SELECT SUM(discount_amount) FROM sales_account_subdetails sas WHERE sas.receipt_id = a.id) as discount_amount,a.total_amount,a.date,a.time,c.name as customer FROM sales_receipts a  LEFT join customers c on c.id = a.customer_id   where a.payment_id = ? and a.opening_id = ? and a.terminal_id = ?", [$mode, $opening, $terminal]);
        return $result;
    }

    public function isdb($opening, $terminal)
    {
        $result = DB::select("SELECT b.item_code,b.product_name, SUM(a.total_qty) as qty, SUM(a.total_amount) as total_amount,SUM(a.total_cost) as cost FROM sales_receipt_details a INNER JOIN inventory_general b on b.id = a.item_code  where a.receipt_id IN (SELECT id FROM `sales_receipts` where opening_id = ? and terminal_id = ?  ) GROUP BY product_name", [$opening, $terminal]); //AND order_mode_id = 1

        return $result;
    }

    public function getchequesCounts($today, $tomorrow)
    {
        $result = DB::select('SELECT COUNT(a.cheque_id) AS todays, COALESCE((SELECT COUNT(c.cheque_id) FROM bank_cheque_general c INNER JOIN bank_cheque_details d on d.cheque_id = c.cheque_id WHERE c.cheque_date = ? AND d.status_id = 1 AND b.cheque_status_id = 1),0) AS tomorrow FROM bank_cheque_general a INNER JOIN bank_cheque_details b ON b.cheque_id = a.cheque_id WHERE a.cheque_date = ? AND b.status_id = 1 AND b.cheque_status_id = 1', [$tomorrow, $today]);
        return $result;
    }


    public function totalSales()
    {
        //$result = DB::select("SELECT SUM(a.total_amount) as TotalSales FROM sales_receipt_details a INNER JOIN sales_receipts b on b.id = a.receipt_id  where b.date = CURDATE() and b.branch = ?",[session("branch")]);
        if (session("roleId") == 2) {
            $result = DB::select("SELECT SUM((SELECT SUM(a.total_amount)  as sales from sales_receipts a  where opening_id IN (Select opening_id as opening_id from sales_opening a where  user_id = a.branch_id and status = 1) and a.status != 12)) as TotalSales  from branch a where a.company_id = ? and a.status_id = 1", [session("company_id")]);
        } else {
            $result = DB::select("SELECT SUM((SELECT SUM(a.total_amount)  as sales from sales_receipts a  where opening_id IN (Select opening_id as opening_id from sales_opening a where  user_id = a.branch_id and status = 1) and a.status != 12)) as TotalSales  from branch a where a.branch_id = ? and a.status_id = 1", [session("branch")]);
        }

        return $result;
    }

    public function totalExpense()
    {
        if (session("roleId") == 2) {
            $result = DB::select("SELECT SUM(net_amount) as expenseAmount FROM `expenses` where date(created_at) = CURDATE() and branch_id = ?", [session("branch")]);
            return $result;
        }
    }

    public function getVendorPayable()
    {
        if (session("roleId") == 2) {
            $balance = 0;
            $vendor = DB::select('SELECT *,(SELECT balance FROM vendor_ledger where vendor_account_id = (Select MAX(vendor_account_id) from vendor_ledger where vendor_id = a.id)) as balance FROM vendors a INNER JOIN vendor_company_details b on b.vendor_id = a.id INNER JOIN country c on c.country_id = a.country_id INNER JOIN city d on d.city_id = a.city_id where a.user_id = ? and a.status_id = 1', [session('company_id')]);
            foreach ($vendor as $value) {
                if ($value->balance < 0) {
                    $balance = $balance + ($value->balance * (-1));
                }
            }
            return $balance;
        } else {

            $balance = 0;
            $vendor = DB::select('SELECT a.* FROM vendor_ledger a inner join purchase_general_details b on b.purchase_id = a.po_no where b.branch_id = ? and a.vendor_account_id = (Select Max(vendor_account_id) from vendor_ledger where po_no = a.po_no )', [session('branch')]);
            foreach ($vendor as $value) {
                if ($value->balance < 0) {
                    $balance = $balance + ($value->balance * (-1));
                }
            }
            return $balance;
        }
    }

    public function getCustomerPayable()
    {
        $balance = 0;
        $customer = DB::select('SELECT a.id,a.name,a.mobile,(SELECT balance FROM customer_account where cust_account_id = (SELECT MAX(cust_account_id) from customer_account where cust_id = a.id) ) as balance FROM customers a WHERE user_id IN (SELECT user_id FROM user_authorization WHERE company_id = ?)', [session('company_id')]);
        foreach ($customer as $value) {
            if ($value->balance > 0) {
                $balance = $balance + $value->balance;
            }
        }
        return $balance;
    }

    public function dashboardRole()
    {
        $result = DB::table("role_settings")->where("page_id", 2)->where("role_id", session("roleId"))->get();
        return $result->isEmpty() ? 0 : 1;
    }

    public function cashIn($opening, $terminal)
    {
        $result = DB::table("sales_cash_in")->where("terminal_id", $terminal)->where("opening_id", $opening)->get();
        return $result;
    }

    public function cashOut($opening, $terminal)
    {
        $result = DB::table("sales_cash_out")->where("terminal_id", $terminal)->where("opening_id", $opening)->get();
        return $result;
    }

    public function salesReturn($opening)
    {
        $result = DB::select("SELECT c.receipt_no,b.product_name,a.qty,a.amount,a.timestamp FROM sales_return a INNER JOIN inventory_general b on b.id = a.item_id LEFT JOIN sales_receipts c on c.id = a.receipt_id where a.opening_id = ?", [$opening]);
        return $result;
    }

    public function expenses($opening)
    {
        $result = DB::select("SELECT * FROM expenses a INNER JOIN expense_categories b on b.exp_cat_id = a.exp_cat_id where a.opening_id = ?", [$opening]);
        return $result;
    }

    public  function getBranchAndTerminalName($terminal)
    {
        $result = DB::select("SELECT a.terminal_name,b.branch_name FROM terminal_details a INNER JOIN branch b on b.branch_id = a.branch_id where a.terminal_id = $terminal");
        return $result;
    }

    public function getProjectedSales()
    {
        $result = DB::select("SELECT AVG(total_amount) AS sales FROM `sales_receipts` where branch = ? and date IN (SELECT  * 
   FROM (
        SELECT  date_format(DATE_ADD((select date_format(DATE_ADD(CURRENT_DATE, INTERVAL -120 DAY),'%Y-%m-%d')), 
            INTERVAL n4.num*1000+n3.num*100+n2.num*10+n1.num DAY ),'%Y-%m-%d') AS DATE 
          FROM  (
              SELECT 0 AS num
              UNION ALL SELECT 1
              UNION ALL SELECT 2
              UNION ALL SELECT 3
              UNION ALL SELECT 4
              UNION ALL SELECT 5
              UNION ALL SELECT 6
              UNION ALL SELECT 7
              UNION ALL SELECT 8
              UNION ALL SELECT 9
         ) AS n1,
         (
              SELECT 0 AS num
              UNION ALL SELECT 1
              UNION ALL SELECT 2
              UNION ALL SELECT 3
              UNION ALL SELECT 4
              UNION ALL SELECT 5
              UNION ALL SELECT 6
              UNION ALL SELECT 7
              UNION ALL SELECT 8
              UNION ALL SELECT 9
        ) AS n2,
        (
              SELECT 0 AS num
              UNION ALL SELECT 1
              UNION ALL SELECT 2
              UNION ALL SELECT 3
              UNION ALL SELECT 4
              UNION ALL SELECT 5
              UNION ALL SELECT 6
              UNION ALL SELECT 7
              UNION ALL SELECT 8
              UNION ALL SELECT 9
        ) AS n3,
        (
              SELECT 0 AS num
              UNION ALL SELECT 1
              UNION ALL SELECT 2
              UNION ALL SELECT 3
              UNION ALL SELECT 4
              UNION ALL SELECT 5
              UNION ALL SELECT 6
              UNION ALL SELECT 7
              UNION ALL SELECT 8
              UNION ALL SELECT 9
        ) AS n4
    ) AS a
WHERE DATE >=  (select date_format(DATE_ADD(CURRENT_DATE, INTERVAL -120 DAY),'%Y-%m-%d')) AND DATE < NOW()
  AND WEEKDAY(DATE) = (SELECT WEEKDAY(CURDATE()))
ORDER BY DATE)", [session("branch")]);

        return $result;
    }
}
