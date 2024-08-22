<?php

namespace App;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Vendor extends Model
{


    

    protected $fillable = [
            'status_id',
            'user_id',
            'country_id',
            'city_id',
            'vendor_name',
            'vendor_contact',
            'vendor_email',
            'address',
            'image',
            'payment_terms',
            'slug',
            'ntn',
            'strn',
    ];

    public function LedgerDetails($id,$from,$to)
    {
		$filter = "";
		
		if($from != "" and $to != ""){
			$filter = "and DATE(a.created_at) between '$from' and '$to'";
		}
		
        $result = DB::select("SELECT a.vendor_account_id,b.vendor_name,c.po_no,a.total_amount,a.debit,a.credit,a.balance,
                a.created_at,a.narration,c.purchase_id FROM vendor_ledger a
                INNER JOIN vendors b on b.id = a.vendor_id 
                LEFT JOIN purchase_general_details c on c.purchase_id = a.po_no
                where b.slug = '$id' ".$filter);
        return $result;
    }

    public function getVendors()
    {
        $filter = "";
        // if (session("roleId") == 2) 
        // {
           $filter .= " and user_id = ".session("company_id")."";
        // }
        

      $result = DB::select('Select * from vendors where status_id = 1 '.$filter);
      return $result;
    }

    public function getVendorName($id)
    {
      $result = DB::table("vendors")
          ->join('vendor_company_details','vendors.id','=','vendor_company_details.vendor_id')
          ->where('vendors.slug',$id)->get();
      return $result;
    }

    public function getVendorId($id)
    {
        $result = DB::table("vendors")->where('vendors.slug',$id)->get();
        return $result;
    }





    public function getPOByVendor($id)
    {
        $result = DB::select('SELECT a.purchase_id,a.po_no,b.net_amount FROM purchase_general_details a
                INNER JOIN purchase_account_details b on b.purchase_id = a.purchase_id
                where a.vendor_id = ?',[$id]);
        return $result;
    }

    public function getAmountFromPO($id)
    {
        $result = DB::table('purchase_account_details')->where('purchase_id',$id)->get();
        return $result;
    }

    public function getPOsByVendor($id)
    {
        $result = DB::select('SELECT a.purchase_id,a.po_no,b.* from  purchase_general_details a 
                    INNER JOIN purchase_account_details b on b.purchase_id = a.purchase_id
                    where a.status_id IN("3","7","8") and a.vendor_id = ? and a.branch_id = ? and b.balance_amount != 0',[$id,session('branch')]);
        return $result;
    }

    public function getLastBalance($id)
   {
      $result = DB::select('SELECT balance FROM vendor_ledger WHERE vendor_account_id =  (SELECT MAX(vendor_account_id) from vendor_ledger where vendor_id = ?) ',[$id]);
      return $result;
   }

   public function po_account_update($id,$bal)
   {
        $result = DB::table('purchase_account_details')->where('purchase_id',$id)->update(['balance_amount' => $bal]);;
        return $result;
   }

   public function po_general_status_update($id,$status)
   {
        $result = DB::table('purchase_general_details')->where('purchase_id',$id)->update(['status_id' => $status]);;
        return $result;
   }

   public function insert_into_ledger($fields)
   {
        $result = DB::table('vendor_ledger')->insertGetId($fields);
        return $result;
   }

   public function insert_into_bank_details_for_vendor($fields)
   {
        $result = DB::table('vendor_payment')->insertGetId($fields);
        return $result;
   }

   public function vendor_payment_details($fields)
   {
        $result = DB::table('vendor_payment_details')->insertGetId($fields);
        return $result;
   }

   public function account_payable($vendor,$first,$second)
   {
    $filter = "";
    $mainFilter = "";
    if($vendor != "")
    {
      $filter .= $vendor;
      $mainFilter = " and a.id = ".$vendor;
    }
    else
    {
      $filter .= " a.id";
      $mainFilter = " and a.user_id = ".session("company_id");
    }
    if($first != "")
    {
      $filter .= " and date(created_at) BETWEEN '".$first."' and '".$second."'";
    }


    $result = DB::select("Select a.vendor_name,a.vendor_contact,b.company_name,(SELECT balance FROM vendor_ledger WHERE vendor_account_id = (SELECT MAX(vendor_account_id) from vendor_ledger where vendor_id = ".$filter.")) as balance from vendors a INNER JOIN vendor_company_details b on b.vendor_id = a.id where a.status_id = 1 AND (SELECT balance FROM vendor_ledger WHERE vendor_account_id = (SELECT MAX(vendor_account_id) from vendor_ledger where vendor_id = ".$filter.")) != 0 ".$mainFilter);
    return $result;
   }

   public function voucherPrint($accountID)
   {
   $result = DB::select("Select b.payment_id,b.payment as debit,b.cheque,b.narration,d.vendor_name,e.bank_name,b.cheque,b.narration,b.date,f.account_title,f.account_no from vendor_payment_details a INNER JOIN vendor_payment b on b.payment_id = a.payment_id INNER JOIN vendor_ledger c on c.vendor_account_id = a.account_id INNER JOIN vendors d on d.id = c.vendor_id LEFT JOIN bank_account_generaldetails f on f.bank_account_id = b.bankid LEFT JOIN banks e on e.bank_id = f.bank_id where a.account_id = ?",[$accountID]);
    return $result;
   }

   public function profitandloss($first,$second)
   {
    $result = DB::select('SELECT (Select SUM(total_amount) from sales_receipts where date between ? and ? and branch = ? ) as Total,(SELECT SUM(a.discount_amount) FROM sales_account_subdetails a INNER JOIN sales_receipts b on b.id = a.receipt_id where b.date between ? and ? and b.branch = ?) as Discount,IFNULL((SELECT SUM(amount) FROM sales_return where Date(timestamp) between ? and ? ),0) as salesreturn',[$first,$second,session("branch"),$first,$second,session("branch"),$first,$second]);
    return $result;
   }
  
   public function profitandlossexpense($first,$second)
   {
     $result = DB::select('SELECT a.exp_id,b.expense_category,a.expense_details,SUM(a.net_amount) as balance FROM expenses a INNER JOIN expense_categories b on b.exp_cat_id = a.exp_cat_id where Date(a.created_at) between ? and ? GROUP BY date(a.created_at)',[$first,$second]);
        return $result;
    }

    public function cogs($first,$second)
    {
     $result = DB::select('SELECT SUM(a.amount) as amount FROM master_assign_details a INNER JOIN master_assign b on b.assign_id = a.assign_id where date between ? and ?',[$first,$second]);
     return $result;
    }


    public function masterAmount($first,$second)
    {
       $result = DB::select('SELECT SUM(debit) as debit FROM master_account where debit <> 0 and DATE(created_at) between ? and ?',[$first,$second]);
        return $result;
    }

    public function citycheck($city,$country)
    {
        $result = DB::select('SELECT Count(*) as count FROM `city` where city_name = ? and country_id = ?',[$city,$country]);
        return $result;
    }

    public function addCity($items)
    {
        $result = DB::table('city')->insertGetId($items);
        return $result;
    }

    public function activeVendor($id)
    {
        $result = DB::table('vendors')->where('id',$id)->update(['status_id'=>1]);
        return $result;
    }

    public function company($id)
    {
        $result = DB::table('company')->where('company_id',$id)->get();
        return $result;
    }
	
	public function getCompanyByBranch($branchId)
    {
		$company = DB::table('branch')->where('branch_id',$branchId)->get();
        $result = DB::table('company')->where('company_id',$company[0]->company_id)->get();
        return $result;
    }
	
	public function getBranch($branchId)
    {
		$branch = DB::table('branch')->where('branch_id',$branchId)->get();
        return $branch;
    }

    public function insert_into_vendor_product($fields,$bulk = false)
    {   
        if($bulk == false){
            if(DB::table('vendor_product')->insertGetId($fields))
            {
                return 1;
            }else{
                return 0;
            }
        }else{
            DB::table('vendor_product')->insert($fields);
            return true;
        }

    }
	
	public function check_vendor_product($vendor,$Id)
    {
		if(DB::table('vendor_product')->where("vendor_id",$vendor)->where("product_id",$Id)->count() > 0){
			return true;
		}else{
			return false;
		}
	}
	
	public function get_product_by_vendor($productId)
    {
		return  DB::table('vendor_product')
				->join("inventory_general","inventory_general.id","vendor_product.product_id")
				->where("product_id",$productId)
				->groupBy("vendor_id")
				->get();
		
	}
	
	public function check_product_by_vendor($vendorId,$productId)
    {
		if(DB::table('vendor_product')->where("vendor_id",$vendorId)->where("product_id",$productId)->count() > 0){
			return true;
		}else{
			return false;
		}
	}

    public function getVendorsProducts($id)
    {
        $result = DB::select("SELECT d.vendor_name,a.id,a.item_code,a.product_name,b.name,a.image,c.status,c.vendor_product_id FROM inventory_general a INNER JOIN inventory_uom b on b.uom_id = a.uom_id INNER JOIN vendor_product c on c.product_id = a.id inner join vendors d on d.id = c.vendor_id where d.slug = '".$id."'");
        return $result;
    }

    public function getVendorProduct($id)
    {
        $result = DB::table('vendor_product')
                ->join('inventory_general','inventory_general.id','=','vendor_product.product_id')
                ->join('inventory_department','inventory_department.department_id','=','inventory_general.department_id')
                ->where('vendor_id',$id)
				->where('vendor_product.status',1)
				->get();
        return $result;
    }

    public function getVendorPurchaseOrders($id)
    {
        $result = DB::select("Select a.*,b.*,c.*,d.*,e.* from purchase_general_details a INNER JOIN vendors b on b.id = a.vendor_id INNER JOIN branch c on c.branch_id = a.branch_id INNER JOIN purchase_status d on d.po_status_id = a.status_id INNER JOIN purchase_account_details e on e.purchase_id = a.purchase_id inner join vendors f on f.id = a.vendor_id where f.slug = '".$id."'");
        return $result;
    }

    public function update_vendor_narration($id,$items){
        $result = DB::table('vendor_ledger')->where('vendor_account_id', $id)->update($items);
        return $result;

    }


    /**
        Vendor Due Payment
    **/

    public function getTotalNoOfVendorPayable($columnName,$columnSortOrder,$start,$rowperpage,$searchValue,$filterVal){
      $filter = '';  
      if($filterVal['vendor_name'] != ''){
        $filter .= " b.vendor_name LIKE '".$filterVal['vendor_name']."%' AND ";   
      }

      
      if($filterVal['from_date'] != '' && $filterVal['to_date'] != ''){
        $filter .= "  a.payment_date >= '".$filterVal['from_date']."' AND a.payment_date <= '".$filterVal['to_date']."' AND   ";   
      }


      $Records = array();
       if (session("roleId") == 2) {
      $result = DB::select("Select a.*,b.*,c.*,d.*,e.* from purchase_general_details a INNER JOIN vendors b on b.id = a.vendor_id INNER JOIN branch c on c.branch_id = a.branch_id INNER JOIN purchase_status d on d.po_status_id = a.status_id INNER JOIN purchase_account_details e on e.purchase_id = a.purchase_id where ". $filter . "  a.user_id = ? and a.status_id <> ? ORDER BY a.purchase_id DESC,$columnName $columnSortOrder",[session("company_id"),10]);
        $Records = $result;
    }
    else
    {
      $result = DB::select("Select a.*,b.*,c.*,d.*,e.* from purchase_general_details a INNER JOIN vendors b on b.id = a.vendor_id INNER JOIN branch c on c.branch_id = a.branch_id INNER JOIN purchase_status d on d.po_status_id = a.status_id INNER JOIN purchase_account_details e on e.purchase_id = a.purchase_id where ". $filter . "  a.branch_id = ? and a.status_id <> ? ORDER BY a.purchase_id DESC,$columnName $columnSortOrder",[session("branch"),10]);
        $Records = $result;
    }
    return count($Records);
    }

    /**
        Vendor Due Payment With Filter
    **/

    public function getTotalNoOfVendorPayableWithFilter($columnName,$columnSortOrder,$start,$rowperpage,$searchValue,$filterVal){
      $filter = '';   
      if($filterVal['vendor_name'] != ''){
        $filter .= " b.vendor_name LIKE '".$filterVal['vendor_name']."%' AND ";   
      }

     
      if($filterVal['from_date'] != '' && $filterVal['to_date'] != ''){
        $filter .= "  a.payment_date >= '".$filterVal['from_date']."' AND a.payment_date <= '".$filterVal['to_date']."' AND    ";   
      }


      $Records = array();
      if (session("roleId") == 2) {
      $result = DB::select("Select a.*,b.*,c.*,d.*,e.* from purchase_general_details a INNER JOIN vendors b on b.id = a.vendor_id INNER JOIN branch c on c.branch_id = a.branch_id INNER JOIN purchase_status d on d.po_status_id = a.status_id INNER JOIN purchase_account_details e on e.purchase_id = a.purchase_id where ". $filter . "  a.user_id = ? and a.status_id <> ? ORDER BY a.purchase_id DESC,$columnName $columnSortOrder",[session("company_id"),10]);
       $Records = $result;
    }
    else
    {
      $result = DB::select("Select a.*,b.*,c.*,d.*,e.* from purchase_general_details a INNER JOIN vendors b on b.id = a.vendor_id INNER JOIN branch c on c.branch_id = a.branch_id INNER JOIN purchase_status d on d.po_status_id = a.status_id INNER JOIN purchase_account_details e on e.purchase_id = a.purchase_id where ". $filter . "  a.branch_id = ? and a.status_id <> ? ORDER BY a.purchase_id DESC,$columnName $columnSortOrder",[session("branch"),10]);
       $Records;
    }
    
    return count($Records);
    }

    /**
        Vendor Due Payment Detail
    **/

    public function VendorPayableDetails($columnName,$columnSortOrder,$start,$rowperpage,$searchValue,$filterVal){
     $filter = '';
     $records = array();

     if($filterVal['vendor_name'] != ''){
      $filter .= " b.vendor_name LIKE '".$filterVal['vendor_name']."%' AND ";   
    }

    
    if($filterVal['from_date'] != '' && $filterVal['to_date'] != ''){
      $filter .= "  a.payment_date >= '".$filterVal['from_date']."' AND a.payment_date <= '".$filterVal['to_date']."' AND    ";   
    }

    if (session("roleId") == 2) {
      $result = DB::select("Select a.*,b.*,c.*,d.*,e.* from purchase_general_details a INNER JOIN vendors b on b.id = a.vendor_id INNER JOIN branch c on c.branch_id = a.branch_id INNER JOIN purchase_status d on d.po_status_id = a.status_id INNER JOIN purchase_account_details e on e.purchase_id = a.purchase_id where ". $filter . "  a.user_id = ? and a.status_id <> ? ORDER BY $columnName $columnSortOrder LIMIT $start ,$rowperpage",[session("company_id"),10]);
        $records = $result;
    }
    else
    {
      $result = DB::select("Select a.*,b.*,c.*,d.*,e.* from purchase_general_details a INNER JOIN vendors b on b.id = a.vendor_id INNER JOIN branch c on c.branch_id = a.branch_id INNER JOIN purchase_status d on d.po_status_id = a.status_id INNER JOIN purchase_account_details e on e.purchase_id = a.purchase_id where ". $filter . "  a.branch_id = ? and a.status_id <> ? ORDER BY $columnName $columnSortOrder LIMIT $start ,$rowperpage",[session("branch"),10]);
        $records = $result;
    }
      $data_arr = array();
      foreach($records as $record){
        $data_arr[] = array(
          "order_date" => $record->order_date,
          "po_no" => $record->po_no,
          "Vendor" => $record->vendor_name,
          "Branch" => $record->branch_name,
          "delivery_date" => $record->delivery_date,
          "payment_date" => $record->payment_date,
          "Amount" => number_format($record->balance_amount,2),
          "Status" => $record->name,
          'purchase_id' => $record->purchase_id
        );
      }
      return $data_arr;
    }

    public function search_by_vendor_name($name){
        $filter = " AND vendor_name LIKE '".$name."%' ";
         if (session("roleId") == 2) 
        {
           $filter .= " AND user_id = ".session("company_id")."";
        }

       $result = DB::select('Select vendor_name AS name from vendors where status_id = 1 '.$filter);
       return $result;
    }


}

         