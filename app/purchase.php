<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class purchase extends Model
{
	public $ID;

    public function purchaseStatus($type){
      $data = array(
        'Draft' => 1,
        'Placed' => 2,
        'Received' => 3,
        'Cancelled' => 4,
        'Partially Return' => 5,
        'Complete Return' => 6,
        'Partially Received' => 7,
        'Partial Payment' => 8,
        'Complete' => 9,
        'Delete' => 10,
        'Replacement' => 11

      );
      return $data[$type];
    }
     public function getTotalNoOfPurchase($columnName,$columnSortOrder,$start,$rowperpage,$searchValue,$type){
      $type = $this->purchaseStatus($type);
      $Records = array();
       if (session("roleId") == 2) {
      $result = DB::select("Select a.*,b.*,c.*,d.*,e.* from purchase_general_details a INNER JOIN vendors b on b.id = a.vendor_id INNER JOIN branch c on c.branch_id = a.branch_id INNER JOIN purchase_status d on d.po_status_id = a.status_id INNER JOIN purchase_account_details e on e.purchase_id = a.purchase_id where (c.branch_name LIKE '%$searchValue%' OR b.vendor_name LIKE '%$searchValue%' OR po_no LIKE '%$searchValue%') AND  a.status_id = $type AND a.user_id = ? and a.status_id <> ? ORDER BY a.purchase_id DESC,$columnName $columnSortOrder",[session("company_id"),10]);
        $Records = $result;
    }
    else
    {
      $result = DB::select("Select a.*,b.*,c.*,d.*,e.* from purchase_general_details a INNER JOIN vendors b on b.id = a.vendor_id INNER JOIN branch c on c.branch_id = a.branch_id INNER JOIN purchase_status d on d.po_status_id = a.status_id INNER JOIN purchase_account_details e on e.purchase_id = a.purchase_id where (c.branch_name LIKE '%$searchValue%' OR b.vendor_name LIKE '%$searchValue%' OR po_no LIKE '%$searchValue%') AND  a.status_id = $type AND a.branch_id = ? and a.status_id <> ? ORDER BY a.purchase_id DESC,$columnName $columnSortOrder",[session("branch"),10]);
        $Records = $result;
    }
    return count($Records);
    }

    public function getTotalNoOfPurchasessWithFilter($columnName,$columnSortOrder,$start,$rowperpage,$searchValue,$type){
      $type = $this->purchaseStatus($type);
      $Records = array();
      if (session("roleId") == 2) {
      $result = DB::select("Select a.*,b.*,c.*,d.*,e.* from purchase_general_details a INNER JOIN vendors b on b.id = a.vendor_id INNER JOIN branch c on c.branch_id = a.branch_id INNER JOIN purchase_status d on d.po_status_id = a.status_id INNER JOIN purchase_account_details e on e.purchase_id = a.purchase_id where (c.branch_name LIKE '%$searchValue%' OR b.vendor_name LIKE '%$searchValue%' OR po_no LIKE '%$searchValue%') AND  a.status_id = $type AND a.user_id = ? and a.status_id <> ? ORDER BY a.purchase_id DESC,$columnName $columnSortOrder",[session("company_id"),10]);
       $Records = $result;
    }
    else
    {
      $result = DB::select("Select a.*,b.*,c.*,d.*,e.* from purchase_general_details a INNER JOIN vendors b on b.id = a.vendor_id INNER JOIN branch c on c.branch_id = a.branch_id INNER JOIN purchase_status d on d.po_status_id = a.status_id INNER JOIN purchase_account_details e on e.purchase_id = a.purchase_id where (c.branch_name LIKE '%$searchValue%' OR b.vendor_name LIKE '%$searchValue%' OR po_no LIKE '%$searchValue%') AND  a.status_id = $type AND a.branch_id = ? and a.status_id <> ? ORDER BY a.purchase_id DESC,$columnName $columnSortOrder",[session("branch"),10]);
       $Records;
    }
    
    return count($Records);
      
  }

  public function purchaseDetails($columnName,$columnSortOrder,$start,$rowperpage,$searchValue,$type){
    $type = $this->purchaseStatus($type);
    $records = array();
    if (session("roleId") == 2) {
      $result = DB::select("Select a.*,b.*,c.*,d.*,e.* from purchase_general_details a INNER JOIN vendors b on b.id = a.vendor_id INNER JOIN branch c on c.branch_id = a.branch_id INNER JOIN purchase_status d on d.po_status_id = a.status_id INNER JOIN purchase_account_details e on e.purchase_id = a.purchase_id where (c.branch_name LIKE '%$searchValue%' OR b.vendor_name LIKE '%$searchValue%' OR po_no LIKE '%$searchValue%') AND a.status_id = $type AND a.user_id = ? and a.status_id <> ? ORDER BY $columnName $columnSortOrder LIMIT $start ,$rowperpage",[session("company_id"),10]);
        $records = $result;
    }
    else
    {
      $result = DB::select("Select a.*,b.*,c.*,d.*,e.* from purchase_general_details a INNER JOIN vendors b on b.id = a.vendor_id INNER JOIN branch c on c.branch_id = a.branch_id INNER JOIN purchase_status d on d.po_status_id = a.status_id INNER JOIN purchase_account_details e on e.purchase_id = a.purchase_id where (c.branch_name LIKE '%$searchValue%' OR b.vendor_name LIKE '%$searchValue%' OR po_no LIKE '%$searchValue%') AND a.status_id = $type AND a.branch_id = ? and a.status_id <> ? ORDER BY $columnName $columnSortOrder LIMIT $start ,$rowperpage",[session("branch"),10]);
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
          'purchase_id' => $record->purchase_id,
          'refrence' => $record->refrence
        );
      }
      return $data_arr;
      // $result = DB::select('SELECT a.*,b.*,c.*,d.*,e.net_amount from purchase_general_details a INNER JOIN vendors b on b.id = a.vendor_id
      //   INNER JOIN branch c on c.branch_id = a.branch_id INNER JOIN purchase_status d on d.po_status_id = a.status_id INNER JOIN purchase_account_details e on e.purchase_id = a.purchase_id where a.branch_id IN (Select branch_id from branch where company_id = ?) and a.status_id <> 10 order by a.order_date desc',[session("company_id")]);
      // return $result;
  }

  public function companyDetails(){
    $company = DB::table('company')->where('company_id',session('company_id'))->get();
    return $company;
  }

	public function setID($ID) {
	  $this->ID = $ID;
	}

	public function getID() {
	  return $this->ID;
	}

    public function getVendors(){
    	$result = DB::select('select a.*,b.company_name from vendors a inner join vendor_company_details b on b.vendor_id = a.id where a.user_id = ? and a.status_id = 1',[session("company_id")]);
    	return $result;
    }

    public function getBranches(){
    	$result = DB::table('branch')->where('company_id',session("company_id"))->get();
    	return $result;

    }

    public function UOM(){
    	$result = DB::table('inventory_uom')->get();
    	return $result;

    }

    public function getTaxes(){
    	$result = DB::table('taxes')->where('company_id',session('company_id'))->where('show_in_purchase',1)->where("status_id",1)->get();
    	return $result;
    }

    public function getPurchaseItems(){
    	$result = DB::table('purchase_item_details')->get();
    	return $result;
    }

    public function products(){
    	$result = DB::table('inventory_general')->where('company_id',session('company_id'))->whereIn('product_mode',[1,3])->where('status',1)->get();
    	return $result;
    }

    public function get_item_details($id){
    	$result = DB::table('inventory_general')->where('id',$id)->get();
    	return $result;
    }
    public function getPONumber(){
    	$result = DB::table('purchase_general_details')->where('branch_id',session('branch'))->max('purchase_id');
    	return $result == "" ? 0 : $result;
    }

    public function insert($fields,$items,$id){
    	   // $resultOne = DB::table('purchase_general_details')->where('id',$id)->update($fields);
    		$resultTwo = DB::table('purchase_item_details')->insert($items);
    }

    public function purchaseInsert($fields){
	    	$result = DB::table('purchase_general_details')->insertGetId($fields);
	    	return $result;
    }
	
	public function vendorPurchases($vendorId,$purchaseId){
	    	$result = DB::table('vendor_purchases')->insert([
				"vendor_id" => $vendorId,
				"purchase_id" => $purchaseId,
				"created_at" => date("Y-m-d")." ".date("H:i:s"),
				"updated_at" => date("Y-m-d")." ".date("H:i:s"),
			]);
	    	return $result;
    }

    public function itemsInsert($fields){
    	$resultTwo = DB::table('purchase_item_details')->insert($fields);
    }	

    public function updateVendor($fields,$id){
    	$result = DB::table('purchase_general_details')->where('purchase_id',$id)->update($fields);
    }

   public function getItems($id){
        $result = DB::table('purchase_item_details')
        ->join('inventory_uom', 'inventory_uom.uom_id', '=', 'purchase_item_details.unit')
        ->join('inventory_general', 'inventory_general.id', '=', 'purchase_item_details.item_code')
        ->select('purchase_item_details.*', 'inventory_general.*','inventory_uom.name as unitName')
        ->where('purchase_id',$id)->get();
        return $result;
   }

   public function EditItem($fields,$id){
        $resultTwo = DB::table('purchase_item_details')->where('p_item_details_id',$id)->update($fields);
   }

   public function getAccounts($id){
      $total = array('unitCost'=>0,'totalCost'=>0);
      $unitCost = 0;
      $totalCost = 0;
      $totalTax = 0;
      // $result = DB::select("SELECT total_amount,price,quantity AS total,tax_per_item_value as peritemtax FROM purchase_item_details where purchase_id = $id ");
      $result = DB::select("SELECT SUM(price * quantity) as unitcost,SUM(tax_per_item_value *quantity) as totalAmountithTax,SUM(discount_per_item * quantity) as totalDiscount FROM purchase_item_details where purchase_id = $id ");
      if(count($result) > 0){
		  $unitCost = $result[0]->unitcost;
		  $totalCost = $unitCost + $result[0]->totalAmountithTax - $result[0]->totalDiscount;
		  
		  return array('unitCost'=>$unitCost,'totalCost'=>$totalCost);
        // foreach($result AS $val ){
            // $unitCost += $val->price * $val->total;
            // $totalTax += $val->peritemtax * $val->total;
            // $totalCost += $val->total_amount * $val->total; // previously commented
            // $totalCost += ($unitCost + $totalTax);
			
        // }
         // $total = array('unitCost'=>$unitCost,'totalCost'=>$totalCost);
      }
      // return $total;
      // return array('unitCost'=>$unitCost,'totalCost'=>$totalCost,'totalTax' => ($unitCost + $totalTax));
   }

   public function updateGeneral($fields,$id){
        $result = DB::table('purchase_general_details')->where('purchase_id',$id)->update($fields);
        return $result;
   }
   public function LedgerInsert($fields){
        $result = DB::table('vendor_ledger')->insert($fields);
        return $result;
   }
   
   public function accInsert($fields){
        $result = DB::table('purchase_account_details')->insert($fields);
        return $result;
   }

   public function getPurchaseGeneral($id){
        $result = DB::table('purchase_general_details')
                    ->join('vendors', 'vendors.id', '=', 'purchase_general_details.vendor_id')
                    ->join('branch', 'branch.branch_id', '=', 'purchase_general_details.branch_id')
                    ->join('purchase_status','purchase_status.po_status_id', '=','purchase_general_details.status_id')
                    ->join('purchase_item_details','purchase_item_details.purchase_id', '=','purchase_general_details.purchase_id')
                    ->join('purchase_account_details','purchase_account_details.purchase_id', '=','purchase_general_details.purchase_id')
                    ->select('purchase_general_details.*', 'branch.*','purchase_status.*','vendors.*','purchase_account_details.*')
                    ->where('purchase_general_details.purchase_id',$id)->get();
        return $result;
   }


//RECEIVING MODE START FROM HERE
   public function getGrn(){
        $grn = DB::select('SELECT COUNT(rec_id) as count FROM purchase_rec_gen');
        return $grn[0]->count;
   }
    public function getItemDetails($id){
        // $result = DB::table('purchase_item_details')
        //          ->join('inventory_general','inventory_general.id', '=','purchase_item_details.item_code')
        //          ->where('purchase_item_details.purchase_id',$id)->get();
        $result = DB::select('Select a.*,b.*,inventory_uom.name as unitName, (SELECT MAX(retail_price) FROM inventory_stock a where a.product_id = b.id) as retail,(SELECT MAX(wholesale_price) FROM inventory_stock a where a.product_id = b.id) as wholesale from purchase_item_details a INNER JOIN inventory_uom on  inventory_uom.uom_id = a.unit INNER JOIN inventory_general b on b.id = a.item_code where a.purchase_id = ?',[$id]);
        return $result;
    }

    public function getReceived($id){
        $received = DB::select('SELECT a.*,b.*,inventory_uom.name as unitName,c.item_code as code,SUM(b.qty_rec) as rec,(Select SUM(balance) from inventory_stock where product_id = a.item_code and grn_id IN (Select GRN from purchase_rec_details where po_id = ? )) as received,c.product_name,c.product_description,(Select IFNULL(SUM(quantity),0) from purchase_return_itemdetails where purchase_id = a.purchase_id and item_code = a.item_code and status = 1) as qty_return,(SELECT MAX(retail_price) FROM inventory_stock a where a.product_id = c.id) as retail,(SELECT MAX(wholesale_price) FROM inventory_stock a where a.product_id = c.id) as wholesale from purchase_item_details a INNER JOIN purchase_rec_details b on b.po_rec_details_id = a.p_item_details_id  INNER JOIN inventory_general c on c.id = a.item_code INNER JOIN inventory_uom on  inventory_uom.uom_id = a.unit  where a.purchase_id = ? group by a.item_code,a.p_item_details_id,a.purchase_id,a.item_code,a.unit,a.quantity,a.price,a.total_amount',[$id,$id]);
        return $received;
    }


   public function receiving_general($fields){
        $result = DB::table('purchase_rec_gen')->insertGetId($fields);
        return $result;
   }

   public function receiving_items($fields){
      $result = DB::table('purchase_rec_details')->insertGetId($fields);
        if($result){
            return $result;
        }else{
            return 0;    
        }
        
   }

   public function exists($po,$id){
     $result = DB::table('purchase_item_details')->where('purchase_id',$po)->where('item_code',$id)->get();
     if($result->count() > 0){
        return 1;
     }else{
        return 0; 
     }
     
   }

   public function createStock($stock){
    $result = DB::table('inventory_stock')->insert($stock);
    return $result;
        
   }

   public function changeStatus($id,$status){
      $result = DB::table('purchase_general_details')->where('purchase_id',$id)->update(['status_id' => $status]);
   }

    //Fetching General Details for Edit Mode
   public function getGeneral($id){
        $result = DB::table('purchase_general_details')->where('purchase_id',$id)->get();
        return $result;
   }

   public function getTaxPercentage($id){
        $result = DB::table('taxes')->where('company_id',session("company_id"))->where('show_in_purchase',1)->get();
        return $result;
   }
   
   public function getAccDetails($id){
        $result = DB::table('purchase_account_details')->where('purchase_id',$id)->get();
        return $result;
   }

   public function purchaseAccCount($id){
        $result = DB::select('SELECT IFNULL(SUM(account_id),0) as id FROM `purchase_account_details` where purchase_id = ?',[$id]);
        return $result[0]->id;
   }

   public function getVendorDetails($id){
        $result = DB::table('vendors')->where('id',$id)->get();
        return $result;
   }
   public function receivedItems($id){
        $result = DB::select('SELECT a.rec_details_id,a.GRN,a.item_id,d.product_name,c.name,b.unit,b.quantity,SUM(a.qty_rec) as rec,b.price,b.total_amount,b.purchase_id FROM purchase_rec_details a INNER Join purchase_item_details b on b.p_item_details_id = a.po_rec_details_id INNER JOIN inventory_uom c on c.uom_id = b.unit INNER JOIN inventory_general d on d.id = a.item_id where b.purchase_id = ? GROUP by a.rec_details_id,a.GRN,a.item_id',[$id]);
        return $result;
   }

   public function insertIntoReturn($fields){
     $result = DB::table('purchase_return_itemdetails')->insert($fields);
     return $result;
   }
   public function updateRecDetails($fields,$id){
    $result = DB::table('purchase_rec_details')->where('rec_details_id',$id)->update($fields);
    return $result;
   }

   public function getTaxValue($id){
        $result = DB::table('purchase_general_details')
                  ->join('taxes','taxes.id', '=','purchase_general_details.tax_id')
                  ->join('purchase_account_details','purchase_account_details.purchase_id', '=','purchase_general_details.purchase_id')
                  ->select('taxes.value','purchase_account_details.*')
                  ->where('purchase_general_details.purchase_id',$id)->get();
        return $result;
   }

   public function UpdateAccounts($fields,$id){
    $result = DB::table('purchase_account_details')->where('account_id',$id)->update($fields);
    return $result;
   }

   public function updateGeneralPurchaseStatus($id,$status){
     $result = DB::table('purchase_general_details')->where('purchase_id',$id)->update(['status_id' => $status]);
   }

   public function getStockDetails($id,$pid){
        $result = DB::table('inventory_stock')->where(['grn_id' => $id,'product_id' => $pid])->get();
        return $result;
   }

   public function updateStock($fields,$id){
     $result = DB::table('inventory_stock')->where('stock_id',$id)->update($fields);
     return $result;
   }

   public function deletePurchaseItems($id)
   {
      if(DB::table('purchase_item_details')->where('p_item_details_id',$id)->delete())
      {

        return 1;

      }else{

        return 0;
        
      }
   }

   /*FOR RETURN CODE*/
   public function getDetails($id,$itemid){
        $result = DB::table('purchase_rec_details')
                 ->join('purchase_rec_gen','purchase_rec_gen.rec_id','=','purchase_rec_details.GRN')
                 ->where(['purchase_rec_gen.po_id' => $id,'purchase_rec_details.item_id' => $itemid,'purchase_rec_details.status_id' => 3])
                 ->get();
        return $result;
   }

   public function getGRNDetails($id){
      $GRN = DB::select("SELECT a.* FROM purchase_rec_gen a INNER JOIN purchase_rec_details b on b.GRN = a.rec_id INNER JOIN inventory_stock c on c.grn_id = b.GRN and c.balance > 0 where b.po_id = $id group by a.GRN");
        
        return $GRN;
   }

   public function getStockGRN($id){
        $stock = DB::table('inventory_stock')
        ->join('inventory_general','inventory_general.id', '=','inventory_stock.product_id')
        ->join('purchase_rec_details','purchase_rec_details.GRN', '=','inventory_stock.grn_id')
        ->join('purchase_item_details','purchase_item_details.purchase_id', '=','purchase_rec_details.po_id')
        ->where('grn_id',$id)
        ->groupBy('inventory_general.id')
        ->get();
        return $stock;
   }

   // FOR COMPLETE RETURN
   public function getStockforComplete($id)
   {
      $stock = DB::select("SELECT a.p_item_details_id,a.purchase_id,a.item_code,c.product_name,a.unit,b.name as unit,a.quantity,(Select SUM(qty_rec) from purchase_rec_details where po_rec_details_id = a.p_item_details_id) as received,a.price,a.total_amount FROM purchase_item_details a 
        INNER JOIN inventory_uom b on b.uom_id = a.unit 
        INNER JOIN inventory_general c on c.id = a.item_code
        where a.purchase_id = ?",[$id]);
      return $stock;
   }

   public function changeReturnStatus($id,$qty){
    $return = DB::table('purchase_return_itemdetails')->where(['purchase_id'=>$id,'status'=> 1,'quantity' => $qty])->update(['status' => 0]);
    return $return;
   }

   public function getReturnAmount($id){
    $amount = DB::table('purchase_return_itemdetails')->where(['purchase_id'=> $id,'status'=> 1])->sum('total_amount');
    return $amount;
   }

   public function getAccountAmount($id){
    $acc_amount = DB::table('purchase_account_details')->where('purchase_id',$id)->get();
    return $acc_amount;
   }

   public function updatePOStatus($id)
   {
      if(DB::table('purchase_general_details')->where('purchase_id',$id)->update(['status_id' => 4]))
      {
        return 1;
      }
      else
      {
        return 0;
      }
     
   }

   public function GRNDetails($id)
   {
      $result = DB::select('SELECT b.rec_id,b.GRN,b.created_at FROM purchase_rec_details a
                            INNER JOIN purchase_rec_gen b on b.rec_id = a.GRN
                            where a.po_id = ? group by b.rec_id',[$id]);
      return $result;
   }

   public function DetailsofGRN($id)
   {
      $result = DB::select('SELECT
                            (SELECT COALESCE(SUM(qty_rec),0)  FROM purchase_rec_details WHERE po_id = a.`po_id` AND  `GRN` < a.GRN )  lastReceived , 
                            a.GRN,b.image,b.item_code,b.product_name, a.qty_rec,d.quantity
                            FROM purchase_rec_details a
                            INNER JOIN inventory_general b on b.id = a.item_id
                            INNER JOIN purchase_item_details d on d.p_item_details_id = a.po_rec_details_id
                            where a.GRN = ?',[$id]);
      return $result;
   }

   public function getLastBalance($id)
   {
      $result = DB::select('SELECT balance FROM vendor_ledger WHERE vendor_account_id =  (SELECT MAX(vendor_account_id) from vendor_ledger  where vendor_id = ?)',[$id]);
      return $result;
   }

   public function deletePO($id)
   {
      if(DB::table('purchase_general_details')->where('purchase_id',$id)->update(['status_id' => 10]))
      {
        return 1;
      }
      else
      {
        return 0;
      }
     
   }

   public function getPurchaseitemsForGRN($poid)
   {
      $result = DB::select('SELECT a.*,c.uom_id FROM purchase_item_details a INNER JOIN inventory_general b on b.id = a.item_code INNER JOIN inventory_uom c on c.uom_id = b.uom_id where a.purchase_id = ?',[$poid]);
      return $result;
   }


    public function getstatusname($poid)
    {
        $result = DB::select('SELECT b.name FROM purchase_general_details a INNER JOIN purchase_status b ON b.po_status_id = a.status_id WHERE a.purchase_id = ?',[$poid]);
        return $result;
    }





}
