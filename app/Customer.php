<?php

namespace App;

use App\Models\Customer as ModelsCustomer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'country_id',
        'city_id',
        'name',
        'mobile',
        'dob',
        'phone',
        'nic',
        'address',
        'image'
    ];

    public function insert_customer($items)
    {
        $result = DB::table('customers')->insertGetId($items);
        return $result;
    }
    public function getcountry()
    {
        $country = DB::table('country')->where('country_id', 170)->get();
        return $country;
    }
    public function getcity()
    {
        $city = DB::table('city')->where('country_id', 170)->get();
        return $city;
    }

    public function getcustomers($id = "",$branch="",$name="",$contact="",$membership="")
    {
        $filter = "";
        $webfilter = "";
        if (session("roleId") == 2) {
            $filter .= " and a.user_id IN(SELECT user_id FROM user_authorization where company_id = " . session("company_id") . ")";
            $webfilter .= " and a.company_id = " . session("company_id");
        } else {
            $filter .= " and a.user_id IN(SELECT user_id FROM user_authorization where branch_id = " . ($branch != "" ? $branch : session("branch") ) . ")";
            $webfilter .= " and a.branch_id = " . session("branch");
        }

        if ($id != "") {
            $filter .= " and a.id = " . $id . " ";
        }

        if (!empty($name)) {
            $filter .= " and a.name = '".$name."'";
        }
        if (!empty($contact)) {
            $filter .= " and a.mobile = ".$contact;
        }
        if (!empty($membership)) {
            $filter .= " and a.membership_card_no = ".$membership;
        }

        $customers = DB::select('SELECT (SELECT
  ABS(IFNULL(SUM(total_amount - credit ),0) + 
  IFNULL((SELECT SUM(debit) FROM customer_account WHERE cust_id = a.id AND receipt_no = 0),0)
  - IFNULL((SELECT
      SUM(credit)
    FROM
      customer_account
    WHERE cust_id = a.id
      AND receipt_no = 0),0))
FROM
  customer_account
WHERE cust_id = a.id  AND receipt_no != 0 ) as balance, a.id, a.image,d.branch_name, a.name,a.is_mobile_app_user, a.mobile, a.nic, a.credit_limit,a.status_id, b.status_name,a.slug  FROM customers a
            INNER JOIN accessibility_mode b ON b.status_id = a.status_id
            INNER JOIN user_authorization c ON c.user_id = a.user_id
            INNER JOIN branch d ON d.branch_id = c.branch_id
            WHERE a.status_id IN(1,2) ' . $filter . ' UNION SELECT (SELECT
  ABS(IFNULL(SUM(total_amount - credit ),0) + 
  IFNULL((SELECT SUM(debit) FROM customer_account WHERE cust_id = a.id AND receipt_no = 0),0)
  - IFNULL((SELECT
      SUM(credit)
    FROM
      customer_account
    WHERE cust_id = a.id
      AND receipt_no = 0),0))
FROM
  customer_account
WHERE cust_id = a.id  AND receipt_no != 0 ) as balance, a.id, a.image,d.branch_name, a.name,a.is_mobile_app_user, a.mobile, a.nic, a.credit_limit,a.status_id, b.status_name,a.slug  FROM customers a
            INNER JOIN accessibility_mode b ON b.status_id = a.status_id
            INNER JOIN branch d ON d.branch_id = a.branch_id
            WHERE a.status_id IN(1,2) ' . $webfilter);
        return $customers;
    }

    public function getCustomersForLivewire($id = "", $name, $status,$page,$branch,$contact,$membership)
    {
        $query = ModelsCustomer::query();
        $query->with("branch", "userauthorization", "userauthorization.branch");

        // Add the custom balance calculation
        $query->selectRaw(
            '
                customers.*, 
                ABS(
                    IFNULL(SUM(customer_account.total_amount - customer_account.credit), 0) + 
                    IFNULL((SELECT SUM(debit) FROM customer_account WHERE cust_id = customers.id AND receipt_no = 0), 0) - 
                    IFNULL((SELECT SUM(credit) FROM customer_account WHERE cust_id = customers.id AND receipt_no = 0), 0)
                ) AS balance'
        )
            ->leftJoin('customer_account', 'customer_account.cust_id', '=', 'customers.id')
            ->groupBy('customers.id');

        if (session("roleId") == 2 && $branch == "") {
            $query->where("company_id", session("company_id"));
        } else {
            $query->whereHas("userauthorization", function ($q) use ($branch) {
                $q->where('branch_id', $branch);
            });
        }

        if (!empty($id)) {
            $query->where("id", $id);
        }
        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }
        if (!empty($contact)) {
            $query->where('mobile', '=', $contact);
        }
        if (!empty($membership)) {
            $query->where('membership_card_no', '=', $membership);
        }
        if (!empty($status)) {
            $query->where('status_id', $status);
        }

        return $query->paginate($page);
    }

    public function getcustomersForReceipt($id = "", $company, $branch)
    {
        $filter = "";
        if (session("roleId") == 2) {
            $filter .= " and a.user_id IN(SELECT user_id FROM user_authorization where company_id = " . $company . ")";
        } else {
            $filter .= " and a.user_id IN(SELECT user_id FROM user_authorization where branch_id = " . $branch . ")";
        }

        if ($id != "") {
            $filter .= " and a.id = " . $id . " ";
        }

        $customers = DB::select('SELECT (SELECT
  ABS(IFNULL(SUM(total_amount - credit ),0) + 
  IFNULL((SELECT SUM(debit) FROM customer_account WHERE cust_id = a.id AND receipt_no = 0),0)
  - IFNULL((SELECT
      SUM(credit)
    FROM
      customer_account
    WHERE cust_id = a.id
      AND receipt_no = 0),0))
FROM
  customer_account
WHERE cust_id = a.id  AND receipt_no != 0 ) as balance, a.id, a.image,d.branch_name, a.name,a.is_mobile_app_user, a.mobile, a.nic, a.credit_limit,a.status_id, b.status_name,a.slug  FROM customers a
            INNER JOIN accessibility_mode b ON b.status_id = a.status_id
            INNER JOIN user_authorization c ON c.user_id = a.user_id
            INNER JOIN branch d ON d.branch_id = c.branch_id
            WHERE a.status_id IN(1,2) ' . $filter . 'order by a.id,balance DESC');
        return $customers;
    }

    public function getcustomerBalances($branch="",$name="",$contact="",$membership="")
    {
        $filter = "";
        if (session("roleId") == 2 && $branch == "") {
            $filter .= " and a.user_id IN(SELECT user_id FROM user_authorization where company_id = " . session("company_id") . ")";
        } else {
            $filter .= " and a.user_id IN(SELECT user_id FROM user_authorization where branch_id = " . ($branch != "" ? $branch : session("branch") ). ")";
        }

        if (!empty($name)) {
            $filter .= " and a.name = '".$name."'";
        }
        if (!empty($contact)) {
            $filter .= " and a.mobile = ".$contact;
        }
        if (!empty($membership)) {
            $filter .= " and a.membership_card_no = ".$membership;
        }

        $customers = DB::select('SELECT (SELECT SUM(credit)-SUM(debit) as balance FROM `customer_account` WHERE `cust_id` = a.id)
		as balance, a.id, a.image,d.branch_name,a.address, a.name, a.mobile, a.nic, a.credit_limit,a.status_id, b.status_name,a.slug,a.membership_card_no  FROM customers a
            INNER JOIN accessibility_mode b ON b.status_id = a.status_id
            INNER JOIN user_authorization c ON c.user_id = a.user_id
            INNER JOIN branch d ON d.branch_id = c.branch_id
            WHERE a.status_id =  1 ' . $filter . 'order by a.id,balance DESC');
        return $customers;
    }

    public function getcustomers_byid($id)
    {
        $customers = DB::select('SELECT a.id, a.image, a.name, a.mobile, a.nic, b.status_name, a.slug  FROM customers a INNER JOIN accessibility_mode b ON b.status_id = a.status_id
            WHERE a.id = ?', [$id]);
        return $customers;
    }

    public function remove_customer($id)
    {
        $result = DB::table('customers')->where('id', $id)->update(['status_id' => 2]);
        return $result;
    }

    public function dueDateUpdate($date, $id)
    {
        $result = DB::table('sales_receipts')->where('id', $id)->update(['due_date' => $date]);
        return $result;
    }

    public function customers($id)
    {
        $result = DB::table('customers')
            ->join('country', 'country.country_id', '=', 'customers.country_id')
            ->join('city', 'city.city_id', '=', 'customers.city_id')
            ->where('slug', $id)->get();
        return $result;
    }

    public function update_customer($id, $items)
    {
        $result = DB::table('customers')->where('id', $id)->update($items);
        return $result;
    }

    public function LedgerDetailsShow($id)
    {
        $result = DB::select("SELECT payment_mode,cust_id,a.cust_account_id,b.name,a.total_amount,a.debit,a.credit,a.balance,a.created_at,c.receipt_no,a.received, a.narration FROM customer_account a
            INNER JOIN customers b on b.id = a.cust_id
            LEFT JOIN sales_receipts c on c.id = a.receipt_no
            LEFT JOIN `sales_payment` ON sales_payment.`payment_id` = c.`payment_id` 
            where b.slug = '" . $id . "'
            UNION
            SELECT '',a.id as cust_id,'','',0,0, amount as credit,0,b.timestamp,c.receipt_no,'','Sales Return'  FROM  customers a INNER JOIN sales_receipts c  ON c.customer_id = a.id INNER JOIN `sales_return` b  ON b.receipt_id = c.id  WHERE a.slug = '" . $id . "' ");
        return $result;
    }

    public function LedgerDetailsShowInOrderDetails($custId, $receiptId)
    {
        $result = DB::select("SELECT payment_mode,cust_id,a.cust_account_id,b.name,a.total_amount,a.debit,a.credit,a.balance,a.created_at,c.receipt_no,a.received, a.narration FROM customer_account a
            INNER JOIN customers b on b.id = a.cust_id
            LEFT JOIN sales_receipts c on c.id = a.receipt_no
            LEFT JOIN `sales_payment` ON sales_payment.`payment_id` = a.payment_mode_id  
            where b.id = ? and c.id = ?
            UNION
            SELECT '',a.id as cust_id,'','',0,0, amount as credit,0,b.timestamp,c.receipt_no,'','Sales Return'  FROM  customers a INNER JOIN sales_receipts c  ON c.customer_id = a.id INNER JOIN `sales_return` b  ON b.receipt_id = c.id  WHERE a.id = ?", [$custId, $receiptId, $custId]);
        return $result;
    }

    public function LedgerDetailsPDFShow($id, $from, $to)
    {
        $filter = "";
        if ($from != "" && $to != "") {
            $filter = " and DATE(a.created_at) between '$from' and '$to'";
        }
        $result = DB::select("SELECT payment_mode,cust_id,a.cust_account_id,b.name,a.total_amount,a.debit,a.credit,a.balance,a.created_at,c.receipt_no,a.received, a.narration FROM customer_account a
            INNER JOIN customers b on b.id = a.cust_id
            LEFT JOIN sales_receipts c on c.id = a.receipt_no
            LEFT JOIN `sales_payment` ON sales_payment.`payment_id` = c.`payment_id` 
            where b.slug = '" . $id . "' " . $filter . "
            UNION
            SELECT '',a.id as cust_id,'','',0,0, amount as credit,0,b.timestamp,c.receipt_no,'','Sales Return'  FROM  customers a INNER JOIN sales_receipts c  ON c.customer_id = a.id INNER JOIN `sales_return` b  ON b.receipt_id = c.id  WHERE a.slug = '" . $id . "' ");
        return $result;
    }


    public function getCustomerLastBalance($id, $acount_id = false)
    {
        if (!$acount_id) {
            $result = DB::select("SELECT balance FROM customer_account where cust_account_id = (Select MAX(cust_account_id) from customer_account where cust_id = ?)", [$id]);
            return $result;
        } else {
            $result = DB::select("SELECT balance FROM customer_account where cust_account_id = (Select MAX(cust_account_id) from customer_account where cust_account_id != " . $acount_id . "  AND  cust_id = ?)", [$id]);
            return $result;
        }
    }

    public function getCustomerLastBalanceByID($id)
    {
        $result = DB::select("SELECT balance FROM customer_account where cust_account_id = $id ");
        return $result;
    }

    public function discount_exists_check($id, $custID)
    {
        $count = DB::table('customer_discount')->where(['product_id' => $id, 'status_id' => 1, 'cust_id' => $custID])->count();
        return $count;
    }

    public function discount_insert($fields)
    {
        if (DB::table('customer_discount')->insert($fields)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getDiscount($id)
    {
        $result = DB::table('customer_discount')
            ->join('inventory_general', 'inventory_general.id', '=', 'customer_discount.product_id')
            ->where(['customer_discount.cust_id' => $id, 'customer_discount.status_id' => 1])
            ->get();
        return $result;
    }

    public function getProducts($dept, $subDept)
    {
        $result = DB::table('inventory_general')
            ->where(['inventory_general.company_id' => session('company_id')])
            ->where(['inventory_general.status' => 1, 'department_id' => $dept, 'sub_department_id' => $subDept])
            ->get();
        return $result;
    }

    public function getActiveProduct($product_id)
    {
        $result = DB::table('customer_discount')->where(['product_id' => $product_id, 'status_id' => 1])->get();
        if (DB::table('customer_discount')->where(['customer_discount_id' => $result[0]->customer_discount_id, 'status_id' => 1])->update(['status_id' => 2])) {
            return 1;
        } else {
            return 0;
        }
    }

    public function deleteProduct($cust_discount_id)
    {
        if (DB::table('customer_discount')->where(['customer_discount_id' => $cust_discount_id])->delete()) {
            return 1;
        } else {
            return 0;
        }
    }

    public function insertIntoShalwar($shalwar, $id)
    {
        $shalwarCount = DB::table('shamwarqameez')->where('customer_id', $id)->count('customer_id');
        if ($shalwarCount == 0) {
            if (DB::table('shamwarqameez')->insert($shalwar)) {
                return 0;
            } else {
                return 0;
            }
        }
    }

    public function getShalwarData($id)
    {
        $result = DB::table('shamwarqameez')->where('customer_id', $id)->get();
        return $result;
    }

    public function getPantData($id)
    {
        $result = DB::table('pantshirt')->where('customer_id', $id)->get();
        return $result;
    }

    public function insertIntoPant($paint, $id)
    {
        $pentCount = DB::table('pantshirt')->where('customer_id', $id)->count('customer_id');

        if ($pentCount == 0) {
            if (DB::table('pantshirt')->insert($paint)) {
                return 0;
            } else {
                $result = DB::table('pantshirt')->where('customer_id', $id)->get();
                return $result;
            }
        }
    }

    public function updateMeasurement($items, $customerID)
    {

        if (DB::table('shamwarqameez')->where('customer_id', $customerID)->update($items)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function updatePantMeasurement($items, $customerID)
    {
        if (DB::table('pantshirt')->where('customer_id', $customerID)->update($items)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getCustName($id, $byid = false)
    {
        if ($byid) {
            $customers = DB::select('SELECT a.id, a.name FROM customers a WHERE a.id = ?', [$id]);
        } else {
            $customers = DB::select('SELECT a.id, a.name  FROM customers a WHERE a.slug = ?', [$id]);
        }
        if ($customers > 0) {
            return $customers[0]->name;
        } else {
            return 0;
        }
    }

    public function getSlug($id)
    {

        $customers = DB::select('SELECT a.slug FROM customers a WHERE a.id = ?', [$id]);

        if ($customers > 0) {
            return $customers[0]->slug;
        } else {
            return 0;
        }
    }
    public function getCustAdvance($id)
    {
        $customers = DB::select('SELECT IFNULL(SUM(total_amount - credit), 0) + (SELECT IFNULL(SUM(debit), 0) FROM customer_account WHERE cust_id = ' . $id . ' AND receipt_no = 0) - (SELECT IFNULL(SUM(credit), 0) FROM customer_account WHERE cust_id = ' . $id . ' AND receipt_no = 0) AS balance FROM customer_account WHERE cust_id = ' . $id . ' AND receipt_no != 0');
        if ($customers > 0) {
            if ($customers[0]->balance < 0) {
                return abs($customers[0]->balance);
            }
        } else {
            return 0;
        }
        return 0;
    }

    public function getCustId($id)
    {
        $customers = DB::select('SELECT a.id,a.name  FROM customers a WHERE a.slug = ?', [$id]);
        if ($customers == "") {
            return 0;
        } else {
            return (!empty($customers) ?  $customers : 0);
        }
    }



    public function getCustomerReport($customer, $paymentType)
    {
        $filter = "";
        $mainFilter = "";

        if ($customer != "") {
            $filter .= $customer;
            $mainFilter = " and a.id = " . $customer;
        } else {
            $filter .= " a.id";
        }
        // if ($first != "") {
        // $filter .= " and date(created_at) BETWEEN '" . $first . "' and '" . $second . "'";
        // }
        if ($paymentType != "") {
            $mainFilter .= " and a.payment_type = '" . $paymentType . "'";
        }

        $result = DB::select("SELECT a.id,a.name,a.mobile,a.payment_type,(SELECT
  ABS(
    IFNULL(SUM(total_amount - credit), 0) +
    (SELECT SUM(debit) FROM customer_account
    WHERE cust_id = " . $filter . " AND receipt_no = 0) - (SELECT SUM(credit) FROM customer_account WHERE cust_id = " . $filter . " AND receipt_no = 0)) FROM customer_account WHERE cust_id = " . $filter . "
  AND receipt_no != 0) as balance FROM customers a WHERE user_id IN (SELECT user_id FROM user_authorization WHERE company_id = " . session('company_id') . ") " . $mainFilter . " order by balance desc");
        return $result;
    }

    //filter of company vise
    //WHERE user_id IN (SELECT user_id FROM user_authorization WHERE company_id = 7))

    public function update_all_customers_status($customerid, $statusid)
    {
        $result = DB::table('customers')->whereIn('id', $customerid)->update(['status_id' => $statusid]);
        return $result;
    }

    public function active_customer($id)
    {
        if (DB::table('customers')->where('id', $id)->update(['status_id' => 1])) {
            return 1;
        } else {
            return 0;
        }
    }

    public function multiple_active_customer($id)
    {
        if (DB::table('customers')->whereIn('id', $id)->update(['status_id' => 1])) {
            return 1;
        } else {
            return 0;
        }
    }

    public function item_name($id)
    {
        $result = DB::table('customers')->whereIn('id', $id)->get();
        return $result;
    }

    public function search_by_customer_name($name,$branch="")
    {
        $filter = "";

        if (session("roleId") == 2 && $branch == "all") {
            $filter .= " a.name LIKE '" . $name . "%' AND  a.user_id IN(SELECT user_id FROM user_authorization where company_id = " . session("company_id") . ")";
        } else {
            $filter .= " a.name LIKE '" . $name . "%' AND a.user_id IN(SELECT user_id FROM user_authorization where branch_id = " . ( $branch != "all" ? $branch : session("branch") ). ")";
        }

        $customers = DB::select('SELECT a.name,a.id,d.branch_name,a.mobile FROM customers a
            INNER JOIN user_authorization c ON c.user_id = a.user_id
            INNER JOIN branch d ON d.branch_id = c.branch_id
            WHERE ' . $filter);
        return $customers;
    }

    public function getReceipts($customer_id)
    {
        $result = DB::select("SELECT a.*,b.total_amount,b.balance_amount as receive_amount FROM sales_receipts a INNER JOIN sales_account_general b on b.receipt_id = a.id where a.customer_id = ? and a.payment_id = 3 and b.status = 0", [$customer_id]);
        return $result;
    }

    public function getBalance($customer_id)
    {
        $result = DB::select("SELECT SUM(b.balance_amount) as balance FROM sales_receipts a INNER JOIN sales_account_general b on b.receipt_id = a.id where a.customer_id = ? and a.payment_id = 3", [$customer_id]);
        return $result;
    }

    public function insert_into_ledger($fields)
    {
        $result = DB::table('customer_account')->insertGetId($fields);
        return $result;
    }

    public function update_customer_advance($cust_id, $curtBal, $requestPayment, $receipt_id, $type, $amount)
    {
        $advancePayment = 0;
        if ($type == 'credit') {
            if ($requestPayment > $curtBal) {
                $advancePayment = $requestPayment - $curtBal;
                $lastAdvance = DB::table('customers')->where('id', $cust_id)->get();
                $balance = 0;
                if (count($lastAdvance) > 0) {
                    $balance = $lastAdvance[0]->advance;
                }

                $fields = array('credit' => $advancePayment, 'debit' => 0, 'customer_id' => $cust_id, 'receipt_id' => $receipt_id, 'type' => 'Payment');
                DB::table('customer_advance_payment_log')->insertGetId($fields);
                $result = DB::table('customers')->where('id', $cust_id)->update(['advance' => $advancePayment + $balance]);
                return $result;
            }
        }
        if ($type == 'debit') {
            $advance_payment = DB::table('customers')->where('id', $cust_id)->get();
            $balance = 0;
            if (count($advance_payment) > 0) {
                $balance = $advance_payment[0]->advance - $amount;
                if ($balance < 0) {
                    $balance = 0;
                }
            }
            $fields = array('credit' => 0, 'debit' => $advance_payment[0]->advance - $balance, 'customer_id' => $cust_id, 'receipt_id' => $receipt_id, 'type' => 'Payment');
            $payment_log = DB::table('customer_advance_payment_log')->insertGetId($fields);
            DB::table('customers')->where('id', $cust_id)->update(['advance' => $balance]);
        }
    }

    public function customer_payment_log($fields)
    {
        $result = DB::table('customer_payment_log')->insertGetId($fields);
        return $result;
    }

    public function update_into_ledger($cust_account_id, $fields)
    {
        $result = DB::table('customer_account')->where('cust_account_id', $cust_account_id)->update($fields);
        return $result;
    }


    public function get_sales_account_general($id)
    {
        $result = DB::table('sales_account_general')->where('receipt_id', $id)->get();
        return $result;
    }
    public function sales_account_update($id, $bal, $status)
    {
        $result = DB::table('sales_account_general')->where('receipt_id', $id)->update(['balance_amount' => $bal, "status" => $status]);
        return $result;
    }


    public function getCustomerName($id)
    {
        $result = DB::select("SELECT a.* FROM customers a INNER JOIN user_details b ON b.id = a.user_id INNER JOIN user_authorization c ON c.user_id = a.user_id INNER JOIN company d ON d.company_id = c.company_id WHERE a.slug = ?", [$id]);
        return $result;
    }

    /**
        Customer Due Payment
     **/
    public function getTotalNoOfCustomerDuePayment($columnName, $columnSortOrder, $start, $rowperpage, $searchValue, $filterVal)
    {
        $filter = "";
        if ($filterVal['type'] != 'clear') {
            if (session("roleId") == 2) {
                $filter .= " where f.payment_id = 3 AND e.`status` = 0 AND g.company_id = " . session("company_id") . " ";
            } else {
                $filter .= " where f.payment_id = 3 AND e.`status` = 0 AND branch_id = " . session("branch") . " ";
            }
        } else {
            if (session("roleId") == 2) {
                $filter .= " where f.payment_id = 3 AND e.`status` = 1 AND g.company_id = " . session("company_id") . " ";
            } else {
                $filter .= " where f.payment_id = 3 AND e.`status` = 1 AND branch_id = " . session("branch") . " ";
            }
        }

        if ($filterVal['type'] == 'today') {
            $filter .= "  AND DATE_FORMAT(a.due_date,'%Y-%m-%d') = '" . date('Y-m-d') . "'    ";
        }

        if ($filterVal['customer_name'] != '') {
            $filter .= "  AND c.name LIKE '" . $filterVal['customer_name'] . "%' ";
        }

        if ($filterVal['from_date'] != '' && $filterVal['to_date'] != '') {
            $filter .= "  AND DATE_FORMAT(a.due_date,'%Y-%m-%d') >= '" . $filterVal['from_date'] . "' AND DATE_FORMAT(a.due_date,'%Y-%m-%d') <= '" . $filterVal['to_date'] . "'    ";
        }

        $result = DB::select("SELECT a.id,a.receipt_no,b.order_mode,c.name,c.payment_type,a.total_amount,d.order_status_name,g.branch_name as branch,h.terminal_name,a.date,a.time,a.delivery_date,a.order_mode_id,a.status,c.mobile,e.receive_amount, f.payment_mode from sales_receipts a
                            INNER JOIN sales_order_mode b on b.order_mode_id = a.order_mode_id
                            INNER JOIN customers c on c.id = a.customer_id
                            INNER JOIN sales_order_status d on d.order_status_id = a.status
                            INNER JOIN branch g on g.branch_id = a.branch
                            INNER JOIN terminal_details h on h.terminal_id = a.terminal_id
                            INNER JOIN sales_account_general e on e.receipt_id = a.id
                            INNER JOIN sales_payment f on f.payment_id = a.payment_id " . $filter . " order by a.id DESC");

        return count($result);
    }

    /**
        Customer Due Payment With Filter
     **/
    public function getTotalNoOfCustomerDuePaymentWithFilter($columnName, $columnSortOrder, $start, $rowperpage, $searchValue, $filterVal)
    {
        $filter = "";
        if ($filterVal['type'] != 'clear') {
            if (session("roleId") == 2) {
                $filter .= " where f.payment_id = 3 AND e.`status` = 0 AND g.company_id = " . session("company_id") . " ";
            } else {
                $filter .= " where f.payment_id = 3 AND e.`status` = 0 AND branch_id = " . session("branch") . " ";
            }
        } else {
            if (session("roleId") == 2) {
                $filter .= " where f.payment_id = 3 AND e.`status` = 1 AND g.company_id = " . session("company_id") . " ";
            } else {
                $filter .= " where f.payment_id = 3 AND e.`status` = 1 AND branch_id = " . session("branch") . " ";
            }
        }

        if ($filterVal['type'] == 'today') {
            $filter .= "  AND DATE_FORMAT(a.due_date,'%Y-%m-%d') = '" . date('Y-m-d') . "'    ";
        }
        if ($filterVal['customer_name'] != '') {
            $filter .= "  AND c.name LIKE '" . $filterVal['customer_name'] . "%' ";
        }

        if ($filterVal['from_date'] != '' && $filterVal['to_date'] != '') {
            $filter .= "  AND DATE_FORMAT(a.due_date,'%Y-%m-%d') >= '" . $filterVal['from_date'] . "' AND DATE_FORMAT(a.due_date,'%Y-%m-%d') <= '" . $filterVal['to_date'] . "'    ";
        }

        $result = DB::select("SELECT a.id,a.receipt_no,b.order_mode,c.name,c.payment_type,a.total_amount,d.order_status_name,g.branch_name as branch,h.terminal_name,a.date,a.time,a.delivery_date,a.order_mode_id,a.status,c.mobile,e.receive_amount, f.payment_mode from sales_receipts a
                            INNER JOIN sales_order_mode b on b.order_mode_id = a.order_mode_id
                            INNER JOIN customers c on c.id = a.customer_id
                            INNER JOIN sales_order_status d on d.order_status_id = a.status
                            INNER JOIN branch g on g.branch_id = a.branch
                            INNER JOIN terminal_details h on h.terminal_id = a.terminal_id
                            INNER JOIN sales_account_general e on e.receipt_id = a.id
                            INNER JOIN sales_payment f on f.payment_id = a.payment_id " . $filter . " order by a.id DESC ");
        return count($result);
    }

    /**
        Customer Due Payment Detail
     **/
    public function customerDuePaymentDetails($columnName, $columnSortOrder, $start, $rowperpage, $searchValue, $filterVal)
    {
        // DB::enableQueryLog();
        $data = array();
        $filter = "";
        if ($filterVal['type'] != 'clear') {
            if (session("roleId") == 2) {
                $filter .= " where f.payment_id = 3 AND e.`status` = 0 AND g.company_id = " . session("company_id") . " ";
            } else {
                $filter .= " where f.payment_id = 3 AND e.`status` = 0 AND branch_id = " . session("branch") . " ";
            }
        } else {
            if (session("roleId") == 2) {
                $filter .= " where f.payment_id = 3 AND e.`status` = 1 AND g.company_id = " . session("company_id") . " ";
            } else {
                $filter .= " where f.payment_id = 3 AND e.`status` = 1 AND branch_id = " . session("branch") . " ";
            }
        }

        if ($filterVal['type'] == 'today') {
            $filter .= "  AND DATE_FORMAT(a.due_date,'%Y-%m-%d') = '" . date('Y-m-d') . "'    ";
        }

        if ($filterVal['customer_name'] != '') {
            $filter .= "  AND c.name LIKE '" . $filterVal['customer_name'] . "%' ";
        }

        if ($filterVal['payment_type'] != '') {
            $filter .= "  AND c.payment_type = '" . $filterVal['payment_type'] . "' ";
        }

        if ($filterVal['from_date'] != '' && $filterVal['to_date'] != '') {
            $filter .= "  AND DATE_FORMAT(a.due_date,'%Y-%m-%d') >= '" . $filterVal['from_date'] . "' AND DATE_FORMAT(a.due_date,'%Y-%m-%d') <= '" . $filterVal['to_date'] . "'    ";
        }



        $result = DB::select("SELECT (SELECT COALESCE(SUM(payment_received),0) FROM `customer_payment_log` WHERE receipt_id = a.id ) AS bal, due_date,c.name AS customerName,c.address,a.id,a.receipt_no,b.order_mode,c.name,c.payment_type,a.total_amount,d.order_status_name,g.branch_name as branch,h.terminal_name,a.date,a.time,a.delivery_date,a.order_mode_id,a.status,c.mobile,e.receive_amount, f.payment_mode from sales_receipts a
                            INNER JOIN sales_order_mode b on b.order_mode_id = a.order_mode_id
                            INNER JOIN customers c on c.id = a.customer_id
                            INNER JOIN sales_order_status d on d.order_status_id = a.status
                            INNER JOIN branch g on g.branch_id = a.branch
                            INNER JOIN terminal_details h on h.terminal_id = a.terminal_id
                            INNER JOIN sales_account_general e on e.receipt_id = a.id
                            INNER JOIN sales_payment f on f.payment_id = a.payment_id " . $filter . " order by a.id DESC LIMIT $start,$rowperpage");
        // print_r(DB::getQueryLog());exit;
        if (count($result) > 0) {
            foreach ($result as $key => $value) {
                $data[] = array(
                    'Customer Name' => $value->customerName,
                    'address' => $value->address,
                    'balance' => number_format(($value->total_amount - $value->receive_amount) - $value->bal, 0),
                    'Order' => $value->id,
                    'Date' => $value->date,
                    'Due Date' => date("Y-m-d", strtotime($value->due_date)),
                    'due_date' => date("Y-m-d", strtotime($value->due_date)),
                    'Time' => date("h:i a", strtotime($value->time)),
                    'Branch' => $value->branch,
                    'Terminal' => $value->terminal_name,
                    'Receipt No' => $value->receipt_no,
                    'receipt_no' => $value->receipt_no,
                    'OrderType' => $value->order_mode,
                    'Payment' => $value->payment_type,
                    'Payment Type' => $value->payment_mode,
                    'Total Amount' => number_format($value->total_amount - $value->receive_amount, 0),
                );
            }
        }
        return $data;
    }

    public function getOrders($id)
    {
        $result = DB::select("SELECT a.id,a.receipt_no,b.order_mode,c.name,a.total_amount,d.order_status_name,g.branch_name as branch,h.terminal_name,a.date,a.time,a.delivery_date,a.order_mode_id,a.status,c.mobile,e.receive_amount, f.payment_mode from sales_receipts a
                            INNER JOIN sales_order_mode b on b.order_mode_id = a.order_mode_id
                            INNER JOIN customers c on c.id = a.customer_id
                            INNER JOIN sales_order_status d on d.order_status_id = a.status
                            INNER JOIN branch g on g.branch_id = a.branch
                            INNER JOIN terminal_details h on h.terminal_id = a.terminal_id
                            INNER JOIN sales_account_general e on e.receipt_id = a.id
                            INNER JOIN sales_payment f on f.payment_id = a.payment_id where a.customer_id = (select id from customers where slug = '" . $id . "') order by a.id DESC");
        return $result;
    }

    public function checkReceiptID($id)
    {
        $result = DB::select("SELECT * FROM sales_receipts where id = $id ");
        if (count($result) > 0) {
            return true;
        }
        return false;
    }

    public function getAllPaymentLogByReceiptID($receipt_id)
    {
        $result = DB::select("SELECT customer_payment_log.*,user_details.fullname as name FROM customer_payment_log LEFT JOIN user_details ON user_details.id = customer_payment_log.user_id where receipt_id = $receipt_id ");
        if (count($result) > 0) {
            return $result;
        }
        return array();
    }

    public function getLastBalance($id)
    {
        $result = DB::select('SELECT balance FROM customer_account WHERE cust_account_id = (SELECT MAX(cust_account_id) FROM customer_account WHERE cust_id = ?)', [$id]);
        return $result;
    }

    public function customer_supplier($cust_id)
    {
        $result = DB::select('SELECT * FROM customer_supplier_detail WHERE customer_id = ? ', [$cust_id]);
        return $result;
    }

    public function getcustomerList()
    {
        // return session("branch");
        $result = DB::table('customers')
            ->join('country', 'country.country_id', '=', 'customers.country_id')
            ->join('city', 'city.city_id', '=', 'customers.city_id')
            ->where('branch_id', session("branch"))
            ->paginate(20);
        return $result;
    }
}
