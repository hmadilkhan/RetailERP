<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class master extends Model
{
	public function getcountry()
    {
    	$country = DB::table('country')->where('country_id',170)->get();
    	return $country;
    }
    public function getcity()
    {
    	$city = DB::table('city')->where('country_id',170)->get();
    	return $city;
    }

    public function getMasters()
    {
        $master = DB::table('masters')->where('status_id',1)->get();
        return $master;
    }

    public function mastersworkload()
    {
        $result = DB::select("SELECT a.id,a.name,(SELECT COUNT(assign_id) FROM `master_assign` where master_id = a.id and status = 2) as ordercount from masters a");
        return $result;
    }

	public function getMasterDetails()
	{
		$master = DB::table('masters')
				->join('accessibility_mode','accessibility_mode.status_id','=','masters.status_id')
				->where('masters.status_id',1)
				->get();
    	return $master;
	}

    public function category()
    {
        $result = DB::table('inventory_general')->where('product_mode',2)->get();
        return $result;
    }

    public function insertCategory($items)
    {
        if(DB::table('master_category')->insert($items))
        {
            return 1;
        }
        else
        {
            return 0;
        }
        
    }

    public function checkCategory($name)
    {
        $result = DB::table('master_category')->where('name',$name)->count();
        return $result;
    }

	public function insertMasters($fields)
	{
		$master = DB::table('masters')->insert($fields);
    	return $master;
	}

	public function updateMaster($masterID,$items)
	{
		$result = DB::table('masters')->where('id',$masterID)->update($items);
		return $result;
	}

	public function deleteMaster($id)
	{

        $result = DB::table('masters')->where('id', $id)->update(['status_id'=>2]);
        return $result;
    }

    public function masters($id){
    	$result = DB::table('masters')
    	->join('country', 'country.country_id', '=', 'masters.country_id')
    	->join('city', 'city.city_id', '=', 'masters.city_id')
    	->where('id',$id)->get();
    	return $result;
    }

    public function LedgerDetailsShow($id)
    {
        $result = DB::select('SELECT a.*,b.name,c.receipt_no,a.receipt_no as receipt_id FROM master_account a
            INNER JOIN masters b on b.id = a.master_id LEFT JOIN sales_receipts c on c.id = a.receipt_no
            where a.master_id = ? ',[$id]);
        return $result;
    }

    public function LedgerDetails($id)
    {
        $result = DB::select('SELECT a.*,b.name,c.receipt_no,a.receipt_no as receipt_id FROM master_account a
            LEFT JOIN masters b on b.id = a.master_id LEFT JOIN sales_receipts c on c.id = a.receipt_no
            where a.master_id = ? and a.status_id = 1',[$id]);
        return $result;
    }

    public function update_master($id, $items){
    	$result = DB::table('masters')->where('id', $id)->update($items);
        return $result;
    }

    public function insertDebit($fields)
    {
        if(DB::table('master_account')->insert($fields))
        {
            return 1;
        }
        else
        {
            return 0;
        }
        
    }

    public function getLastBalance($master_id)
    {
        $result = DB::select("SELECT IFNULL(SUM(TotalBalance), '0') as balance  FROM master_account where master_account_id = (Select MAX(master_account_id) from master_account where master_id = $master_id)");
        if ($result == "") {
            return 0;
        }else{
            return $result[0]->balance;
        }
        
    }

    public function updateLedger($id)
    {
        if(DB::table('master_account')->where('master_account_id', $id)->update(['status_id' => 2]))
        {
            return 1;
        }
        else
        {
            return 0;
        }
        
    } 

    public function getMaster()
    {
        $result = DB::table('masters')->get();
        return $result;
    }

    public function checkAlreadyCategoryRate($categoryid,$masterid)
    {
        $result = DB::table('master_category_rate')->where('finished_good_id',$categoryid)->where('master_id',$masterid)->count();
        return $result;
    }

    public function masterRateInsert($items)
    {
        if(DB::table('master_category_rate')->insert($items))
        {
            return 1;
        }
        else
        {
            return 0;
        }

    }

    public function getRateList($id)
    {
        $result = DB::select('SELECT b.product_name as category,c.name as master ,a.* FROM master_category_rate a INNER JOIn inventory_general b on b.id = a.finished_good_id INNER JOIN masters c on c.id = a.master_id WHERE a.master_id = ?',[$id]);
        return $result;
    }

    public function updateRateList($id,$items)
    {
        if(DB::table('master_category_rate')->where('id', $id)->update($items))
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function workload($id)
    {
        // $result = DB::select('SELECT a.*,SUM(a.qty) as qty,SUM(a.amount) as amount,b.product_name,c.date,c.delivery_date,d.name as master FROM master_assign a INNER JOIN inventory_general b on b.id = a.finished_good_id INNER Join sales_receipts c on c.id = a.receipt_no INNER JOIN masters d on d.id = a.master_id group by a.finished_good_id');
        $result = DB::select('SELECT a.assign_id,a.finished_good_id,e.product_name as finished,a.assign_id,b.name as master,d.receipt_no,d.date,d.delivery_date,a.qty,a.status,a.received,d.id as receipt_id FROM master_assign a INNER JOIN masters b on b.id = a.master_id  INNER JOIN sales_receipts d on d.id = a.receipt_no INNER JOIN inventory_general e on e.id = a.finished_good_id where a.status = 2 and a.master_id = ? GROUP by a.finished_good_id,a.receipt_no',[$id]);
        return $result;
    }

    public function UpdateOrderAssign($id,$received,$status)
    {
        $result = DB::table('master_assign')->where('assign_id',$id)->get();
        $received = $received + $result[0]->received;
        if($result[0]->received == $result[0]->qty)
        {
            $statusValue = 3;
        }
        else
        {
            $statusValue = 2;
        }
        if(DB::table('master_assign')->where('assign_id',$id)->update(['received' => $received,'status' => $statusValue]))
        {
            $result = DB::table('master_assign')->where('assign_id',$id)->get();
            if($result[0]->received == $result[0]->qty)
            {
                if(DB::table('master_assign')->where('assign_id',$id)->update(['received' => $received,'status' => 3]))
                {
                    return 1;
                }
                else
                {
                    return 0;
                }
            }
           
        }
        else
        {
            return 0;
        }

    }


    public function UpdateSalesDetailsAssign($status,$id,$code)
    {
        if(DB::select('update sales_receipt_details set status = ? WHERE receipt_id = ? and item_code = ?',[$status,$id,$code]))
        {
            return 1;
        }
        else
        {
            return 0;
        }

    }

    

    public function compareStatus($receipt)
    {
        $result = DB::select('SELECT COUNT(a.item_code) as count,(SELECT COUNT(a.item_code) from sales_receipt_details a where a.receipt_id = ?) as received from sales_receipt_details a where a.receipt_id = ? and a.status = 3',[$receipt,$receipt]);
        return $result;
    }

    public function updateSalesReceiptStatus($id)
    {
        if(DB::table('sales_receipts')->where('id',$id)->update(['status' => 3]))
        {
            return 1;
        }
        else
        {
            return 0;
        }

    }

    public function mastersCategory()
    {
        $result = DB::select('SELECT a.*,b.product_name FROM master_category_rate a INNER JOIN inventory_general b on b.id = a.finished_good_id');
        return $result;
    }

    public function mastersAssignOrders()
    {
        $result = DB::select('SELECT * FROM master_assign where status = 2 GROUP by finished_good_id,receipt_no');
        return $result;
    }

    public function masterPayableReport($master,$first,$second)
    {
      
        $filter = "";
        $mainFilter = "";

        if($master != "")
        {
          $filter .= $master;
          $mainFilter = " where a.id = ".$master;
        }
        else{
          $filter .= " a.id";
        }
        if($first != "")
        {
          $filter .= " and date(created_at) BETWEEN '".$first."' and '".$second."'";
        }
        $result = DB::select("SELECT a.id,a.name,a.mobile,(SELECT IFNULL(SUM(TotalBalance), '0') as balance FROM master_account where master_account_id = (Select MAX(master_account_id) from master_account where master_id = ".$filter." )) as balance FROM masters a".$mainFilter);
        return $result;
    }

    public function masterReportViewer()
    {
        return 1;
    }

    public function getReceipt($receipt)
    {
        $result = DB::select('SELECT * FROM sales_receipt_details a INNER JOIN sales_receipts b on b.id = a.receipt_id 
                    INNER JOIN inventory_general c on c.id = a.item_code 
                    INNER JOIN customers d on d.id = b.customer_id 
                    INNER JOIN sales_order_mode e on e.order_mode_id = b.order_mode_id
                    INNER JOIN sales_order_status f on f.order_status_id = b.status
                    INNER JOIN sales_account_general g on g.receipt_id = b.id
                    where b.receipt_no = ?',[$receipt]);
        return $result;
    }

    public function getGrn(){
        $grn = DB::select('SELECT COUNT(rec_id) as count FROM purchase_rec_gen');
        return $grn[0]->count;
   }

   public function receiving_general($fields){
        $result = DB::table('purchase_rec_gen')->insertGetId($fields);
        return $result;
   }

   public function receiving_items($fields){
        if(DB::table('purchase_rec_master')->insert($fields)){
            return 1;
        }else{
            return 0;    
        }
        
   }

    public function createStock($stock){
        $result = DB::table('inventory_stock')->insert($stock);
        return $result;
        
   }

   public function getItemsForStock($assignID)
   {
    $result = DB::select("SELECT a.finished_good_id,b.product_name,b.uom_id,c.total_qty,c.total_amount,(Select SUM(amount) from master_assign_details where assign_id = ?) as cost FROM master_assign a
                 INNER JOIN inventory_general b on b.id = a.finished_good_id
                INNER JOIN sales_receipt_details c on c.receipt_id = a.receipt_no and c.item_code = a.finished_good_id
                where assign_id = ?",[$assignID,$assignID]);
    return $result;
   }

   






   

    


}