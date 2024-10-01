<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class stock extends Model
{
    public function getData()
    {
        if (session("roleId") == 2) {
        } else {
        }

        $inventory = DB::select('SELECT a.id,a.item_code,a.product_name,b.name,a.product_description,c.department_name,d.sub_depart_name,a.image,a.status,MAX(e.retail_price) as amount,f.reminder_qty,a.product_mode as mode,IFNULL((SELECT SUM(balance) from inventory_stock where product_id = a.id and branch_id = ?),0) as qty FROM inventory_general a 
			INNER JOIN inventory_uom b on b.uom_id = a.uom_id
			INNER JOIN inventory_department c on c.department_id = a.department_id
			INNER JOIN inventory_sub_department d on d.sub_department_id = a.sub_department_id
			INNER JOIN inventory_stock e on e.product_id = a.id
			INNER JOIN inventory_qty_reminders f on f.inventory_id = a.id
			where a.status = 1 and e.branch_id = ?  
			group by a.id', [session('branch'), session('branch')])->paginate(5);

        return $inventory;
    }

    public function test()
    {
        return 1;
    }

    public function getStockByPO($product)
    {

        $result = DB::select('SELECT c.po_no,b.GRN,e.vendor_name, a.rec_details_id,d.product_name,a.qty_rec,b.created_at,f.name as status,a.po_id,(SELECT price FROM `purchase_item_details` where purchase_id = a.po_id and item_code = a.item_id) as cost_price FROM purchase_rec_details a
			INNER JOIN purchase_rec_gen b on b.rec_id = a.GRN
			INNER JOIN purchase_general_details c on c.purchase_id = a.po_id
			INNER JOIN inventory_general d on d.id = a.item_id
			INNER JOIN vendors e on e.id = c.vendor_id
			INNER JOIN purchase_status f on f.po_status_id = a.status_id
			where a.item_id = ? and c.branch_id = ?', [$product, session('branch')]);
        return $result;
    }

    public function getCostPriceLogs($product)
    {
        $result = DB::select('SELECT * FROM `inventory_price` where product_id = "' . $product . '" order by price_id DESC');
        return $result;
    }

    public function getStockByTransfer($product)
    {
        $result = DB::select('SELECT e.Transfer_id,d.GRN,d.created_at,b.product_name,a.qty_rec,c.name as status,f.branch_name as fromBranch , g.branch_name as toBranch FROM purchase_rec_dc_details a
			INNER JOIN inventory_general b on b.id = a.item_id
			INNER JOIN purchase_status c on c.po_status_id = a.status_id
			INNER JOIN purchase_rec_gen d on d.rec_id = a.GRN
			INNER JOIN deliverychallan_general_details e on e.DC_id = a.DC_id
			INNER JOIN branch f on f.branch_id = e.branch_from
			INNER JOIN branch g on g.branch_id = e.branch_to
			where a.item_id = ? and e.branch_to = ?', [$product, session('branch')]);
        return $result;
    }

    public function getStockByBranch($product_id)
    {
        // $result = DB::select('SELECT IFNULL((SELECT DISTINCT  po_id FROM `purchase_rec_details` where GRN = a.grn_id),0) as purchase,IFNULL((SELECT total_amount FROM `purchase_item_details` WHERE purchase_id = purchase and item_code = a.product_id),0) AS total_amount,IFNULL((SELECT tax_per_item_value FROM `purchase_item_details` WHERE purchase_id = purchase and item_code = a.product_id),0) AS taxCost,IFNULL((SELECT discount_per_item FROM `purchase_item_details` WHERE purchase_id = purchase and item_code = a.product_id),0) AS discount
        // ,IFNULL((SELECT quantity FROM `purchase_item_details` WHERE purchase_id = purchase and item_code = a.product_id),0) AS purchaseQuantity
        // ,IFNULL((SELECT discount_by FROM `purchase_item_details` WHERE purchase_id = purchase and item_code = a.product_id),0) AS discountBy,a.stock_id,a.date,a.qty as totalQty,SUM(a.balance) as qty,a.retail_price,a.cost_price,a.wholesale_price,a.discount_price,b.branch_name FROM inventory_stock a 
        // INNER JOIN branch b on b.branch_id = a.branch_id
        // where product_id = ? group by a.branch_id',[$product_id]);

        $result = DB::select('SELECT IFNULL((SELECT DISTINCT  po_id FROM `purchase_rec_details` where GRN = a.grn_id),0) as purchase,IFNULL((SELECT total_amount FROM `purchase_item_details` WHERE purchase_id = purchase and item_code = a.product_id),0) AS total_amount,IFNULL((SELECT tax_per_item_value FROM `purchase_item_details` WHERE purchase_id = purchase and item_code = a.product_id),0) AS taxCost,IFNULL((SELECT discount_per_item FROM `purchase_item_details` WHERE purchase_id = purchase and item_code = a.product_id),0) AS discount
            ,IFNULL((SELECT quantity FROM `purchase_item_details` WHERE purchase_id = purchase and item_code = a.product_id),0) AS purchaseQuantity
            ,IFNULL((SELECT discount_by FROM `purchase_item_details` WHERE purchase_id = purchase and item_code = a.product_id),0) AS discountBy,a.stock_id,a.date,a.qty as totalQty,SUM(a.balance) as qty,c.retail_price,a.cost_price,c.wholesale_price,c.discount_price,b.branch_name FROM inventory_stock a 
            INNER JOIN branch b on b.branch_id = a.branch_id 
            INNER JOIN inventory_price c on c.product_id = a.product_id
            where a.product_id = ? group by a.branch_id', [$product_id]);
        return $result;
    }

    public function getStockDateWiseDetails($product)
    {
        //        $result = DB::select('SELECT a.grn_id,e.created_at as date,b.name as uom,a.cost_price,a.qty,c.branch_name,d.status_name,IFNULL((SELECT po_id FROM `purchase_rec_details` where GRN = a.grn_id),0) as purchase,IFNULL((SELECT DC_id FROM `purchase_rec_dc_details` where GRN = a.grn_id),0) as transfer FROM inventory_stock a INNER JOIN inventory_uom b on b.uom_id = a.uom INNER Join branch c on c.branch_id = a.branch_id INNER Join accessibility_mode d on d.status_id = a.status_id INNER JOIN purchase_rec_gen e on e.rec_id = a.grn_id where a.product_id = ?',[$product]);
        $result = DB::select('SELECT a.balance, a.grn_id,"" as receipt_id,e.created_at as date,b.name as uom,a.cost_price,a.qty,c.branch_name,d.status_name,IFNULL((SELECT DISTINCT  po_id FROM `purchase_rec_details` where GRN = a.grn_id),0) as purchase,IFNULL((SELECT total_amount FROM `purchase_item_details` WHERE purchase_id = purchase and item_code = a.product_id),0) AS total_amount,IFNULL((SELECT tax_per_item_value FROM `purchase_item_details` WHERE purchase_id = purchase and item_code = a.product_id),0) AS taxCost,IFNULL((SELECT discount_per_item FROM `purchase_item_details` WHERE purchase_id = purchase and item_code = a.product_id),0) AS discount
            ,IFNULL((SELECT quantity FROM `purchase_item_details` WHERE purchase_id = purchase and item_code = a.product_id),0) AS purchaseQuantity
            ,IFNULL((SELECT discount_by FROM `purchase_item_details` WHERE purchase_id = purchase and item_code = a.product_id),0) AS discountBy,
            IFNULL((SELECT DISTINCT DC_id FROM `purchase_rec_dc_details` where GRN = a.grn_id),0) as transfer,"" as terminal FROM inventory_stock a INNER JOIN inventory_uom b on b.uom_id = a.uom INNER Join branch c on c.branch_id = a.branch_id INNER Join accessibility_mode d on d.status_id = a.status_id INNER JOIN purchase_rec_gen e on e.rec_id = a.grn_id where a.status_id =1 AND a.product_id = ? and a.date = ' . date("Y-m-d") . ' GROUP by grn_id 
            UNION 
            SELECT a.receipt_detail_id as id,"" as grn, b.receipt_no as receipt_id,b.date,d.name as uom,a.total_amount as price,a.total_qty as qty,e.branch_name,"" as status_name,"Sales" as purchase,"" as total_amount,"" as tax_cost,"" as discount,"" as poqty,"" as discountBy,0 as transfer,f.terminal_name as terminal  FROM sales_receipt_details a INNER JOIN sales_receipts b on b.id = a.receipt_id INNER JOIN inventory_general c on c.id = a.item_code INNER JOIN inventory_uom d on d.uom_id = c.uom_id INNER JOIN branch e on e.branch_id = b.branch INNER JOIN terminal_details f on f.branch_id = e.branch_id and f.terminal_id = b.terminal_id WHERE a.item_code = ? and b.branch IN (Select branch_id from branch where company_id = ? )  and b.date = ' . date("Y-m-d") . '', [$product, $product, session('company_id')]);
        // dd($result);
        return $result;
    }

    public function getBranches()
    {
        if (session("roleId") == 2) {
            $result = DB::table('branch')->where('company_id', session("company_id"))->get();
            return $result;
        } else if (session("roleId") == 17) { // Accountant Role
            $result = DB::table('branch')->where('company_id', session("company_id"))->get();
            return $result;
        } else {
            $result = DB::table('branch')->where('branch_id', session("branch"))->get();
            return $result;
        }
    }

    public function branchwise($branchid)
    {
        $inventory = DB::select('SELECT a.id,a.item_code,a.product_name,b.name,a.product_description,c.department_name,d.sub_depart_name,a.image,a.status,g.retail_price as amount,f.reminder_qty,a.product_mode as mode,IFNULL((SELECT SUM(balance) from inventory_stock where product_id = a.id and branch_id = ?),0) as qty FROM inventory_general a 
			INNER JOIN inventory_uom b on b.uom_id = a.uom_id
			INNER JOIN inventory_department c on c.department_id = a.department_id
			INNER JOIN inventory_sub_department d on d.sub_department_id = a.sub_department_id
			INNER JOIN inventory_stock e on e.product_id = a.id
			INNER JOIN inventory_qty_reminders f on f.inventory_id = a.id
            INNER JOIN inventory_price g on g.product_id = a.id and g.status_id = 1
			where a.status = 1 and e.branch_id = ?
			group by a.id', [$branchid, $branchid]);

        return $inventory;
    }

    public function getStockByBranchPageWise($branch, $code, $name, $dept, $sdept, $status)
    {
        $result = DB::table("inventory_general")
            ->join("inventory_uom", 'inventory_uom.uom_id', '=', 'inventory_general.uom_id')
            ->leftJoin("inventory_uom AS con", 'con.uom_id', '=', 'inventory_general.cuom')
            ->join("inventory_department", 'inventory_department.department_id', '=', 'inventory_general.department_id')
            ->join("inventory_sub_department", 'inventory_sub_department.sub_department_id', '=', 'inventory_general.sub_department_id')
            ->join("inventory_stock", 'inventory_stock.product_id', '=', 'inventory_general.id')
            ->join("branch", 'inventory_stock.branch_id', '=', 'branch.branch_id')
            ->join("inventory_qty_reminders", 'inventory_qty_reminders.inventory_id', '=', 'inventory_general.id')
            ->join("inventory_price", 'inventory_price.product_id', '=', 'inventory_general.id')
            ->select("inventory_general.*", "inventory_uom.name", "con.name as cname", "inventory_department.department_name", "inventory_sub_department.sub_depart_name", DB::raw('SUM(balance) as qty'), "inventory_price.retail_price as amount", "inventory_qty_reminders.reminder_qty", "branch.branch_name")
            ->where("inventory_price.status_id", 1)
            ->where("inventory_general.status", 1)
            ->when($code != "", function ($query) use ($code) {
                return $query->where('inventory_general.item_code', 'like', '%' . $code . '%');
            })
            ->when($name != "", function ($query) use ($name) {
                return $query->where('inventory_general.product_name', 'like', '%' . $name . '%');
            })
            ->when($dept != "", function ($query) use ($dept) {
                return $query->where('inventory_general.department_id', 'like', '%' . $dept . '%');
            })
            ->when($sdept != "", function ($query) use ($sdept) {
                return $query->where('inventory_general.sub_department_id', 'like', '%' . $sdept . '%');
            })
            // ->where('inventory_general.item_code', 'like', '%'.$code.'%')
            // ->where('inventory_general.product_name', 'like', '%'.$name.'%')
            // ->where('inventory_general.department_id', 'like', '%'.$dept.'%')
            // ->where('inventory_general.sub_department_id', 'like', '%'.$sdept.'%')

            ->when($status == "true", function ($query) {
                return $query->whereIn("inventory_stock.branch_id", DB::table('branch')->where("company_id", session("company_id"))->pluck("branch_id"))->groupBy("inventory_general.id")->groupBy("inventory_stock.branch_id");
            }, function ($q) use ($branch) {
                return $q->where("inventory_stock.branch_id", $branch)->groupBy("inventory_general.id");
            })

            // ->where("inventory_stock.branch_id",$branch)
            // ->groupBy("inventory_general.id")
            ->paginate(100);
        // ->toSql();
        return $result;
    }

    public function getStockByBranchForExcel($branch, $code, $name, $dept, $sdept)
    {
        $result = DB::table("inventory_general")
            ->join("inventory_uom", 'inventory_uom.uom_id', '=', 'inventory_general.uom_id')
            ->leftJoin("inventory_uom AS con", 'con.uom_id', '=', 'inventory_general.cuom')
            ->join("inventory_department", 'inventory_department.department_id', '=', 'inventory_general.department_id')
            ->join("inventory_sub_department", 'inventory_sub_department.sub_department_id', '=', 'inventory_general.sub_department_id')
            ->join("inventory_stock", 'inventory_stock.product_id', '=', 'inventory_general.id')
            ->join("inventory_qty_reminders", 'inventory_qty_reminders.inventory_id', '=', 'inventory_general.id')
            ->join("inventory_price", 'inventory_price.product_id', '=', 'inventory_general.id')
            ->select("inventory_general.*", "inventory_uom.name", "con.name as cname", "inventory_department.department_name", "inventory_sub_department.sub_depart_name", DB::raw('SUM(balance) as qty'), "inventory_price.retail_price as amount", "inventory_qty_reminders.reminder_qty")
            ->where("inventory_price.status_id", 1)
            ->where("inventory_general.status", 1)
            ->where('inventory_general.item_code', 'like', '%' . $code . '%')
            ->where('inventory_general.product_name', 'like', '%' . $name . '%')
            ->where('inventory_general.department_id', 'like', '%' . $dept . '%')
            ->where('inventory_general.sub_department_id', 'like', '%' . $sdept . '%')
            ->where("inventory_stock.branch_id", $branch)
            ->groupBy("inventory_general.id")
            ->get();
        return $result;
    }

    public function getProductName($id)
    {
        $result = DB::select('Select product_name from inventory_general where id = ?', [$id]);
        return $result;
    }

    public function getProductReport($id, $branch)
    {
        // echo $id.'------------'.$branch;exit;
        $result = DB::select('Select a.*,b.weight_qty,c.grn_id,e.fullname from inventory_stock_report_table a INNER JOIN inventory_general b on b.id = a.product_id LEFT JOIN inventory_stock c on c.stock_id = a.foreign_id and a.adjustment_mode != "NULL" LEFT JOIN purchase_rec_gen d on d.rec_id = c.grn_id LEFT JOIN user_details e on e.id = d.user_id where a.product_id = ? and a.branch_id = ?', [$id, $branch]);
        return $result;
    }

    public function getProductReportFilter($id, $branch, $from, $to)
    {
        $result = DB::select('Select * from inventory_stock_report_table where product_id = ? and branch_id = ? and DATE(date) between ? and ?', [$id, $branch, $from, $to]);
        return $result;
    }

    public function stock_report($items)
    {
        $result = DB::table('inventory_stock_report_table')->insert($items);
        return $result;
    }

    public function getLastStock($id)
    {
        $result = DB::select('SELECT SUM(stock) as stock FROM `inventory_stock_report_table` where stock_report_id = (Select MAX(stock_report_id) from inventory_stock_report_table WHERE product_id = ?)', [$id]);
        return $result;
    }

    public function stockAdjustmentVoucher($grn)
    {
        return DB::select("SELECT c.grn_id,b.item_code,b.product_name,a.narration,a.adjustment_mode,a.qty,d.name,a.date,f.fullname,g.branch_name FROM inventory_stock_report_table a INNER JOIN inventory_general b on b.id = a.product_id INNER JOIN inventory_stock c on c.stock_id = a.foreign_id INNER JOIN inventory_uom d on d.uom_id = b.uom_id INNER JOIN purchase_rec_gen e on e.rec_id = c.grn_id INNER JOIN user_details f on f.id = e.user_id INNER JOIN branch g on g.branch_id = a.branch_id where c.grn_id = ? and a.narration like '%(Stock Adjustment)%' order by a.date DESC",[$grn]);
    }
}
