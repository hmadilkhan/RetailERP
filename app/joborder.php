<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class joborder extends Model
{
    public function getList($statusid)
    {
        $result = DB::table('recipy_general')
            ->join("inventory_general","inventory_general.id","=","recipy_general.product_id")
            ->join("recipy_account","recipy_account.recipy_id","=","recipy_general.recipy_id")
            ->where("recipy_general.status_id",$statusid)
            ->where("recipy_general.branch_id",session('branch'))
            ->get();
        return $result;
    }
	
	public function getRestaurantList()
    {
        $result = DB::select('SELECT a.recipy_id,b.product_name,(SELECT SUM(cost_price) FROM `inventory_stock` WHERE `product_id` IN (SELECT item_id FROM `recipy_details` WHERE `recipy_id` = a.recipy_id and used_in_dinein = 1)) as DineInCost, (SELECT SUM(cost_price) FROM `inventory_stock` WHERE `product_id` IN (SELECT item_id FROM `recipy_details` WHERE `recipy_id` = a.recipy_id)) as TakedelCost,c.ingredients_cost,c.material_cost,c.infrastructure_cost FROM recipy_general a INNER JOIN inventory_general b on b.id = a.product_id INNER JOIN recipy_account c on c.recipy_id = a.recipy_id where a.branch_id = ? and a.status_id = 1',[session('branch')]);
        return $result;
    }

    public function getfinishgoods()
    {
        $products = DB::table('inventory_general')->join('inventory_department','inventory_department.department_id','=','inventory_general.department_id')->where('product_mode',2)->where('inventory_general.status',1)->where('inventory_general.company_id',session("company_id"))->get();
        return $products;
    }

    public function getproducts()
    {
        $products = DB::table('inventory_general')->join('inventory_department','inventory_department.department_id','=','inventory_general.department_id')->where('inventory_general.status',1)->where('inventory_general.company_id',session("company_id"))->whereIn('inventory_general.product_mode',array(1,3))->get();
        return $products;
    }



    public function getRaw()
    {
        $products = DB::table('inventory_general')
            ->join("inventory_stock","inventory_stock.product_id","=","inventory_general.id")
            ->groupBy('inventory_general.id')->get();
        return $products;
    }

    public function getProductByID($id)
    {
        $products = DB::table('inventory_general')
            ->join("inventory_stock","inventory_stock.product_id","=","inventory_general.id")
            ->where('inventory_general.id',$id)->get();
        return $products;
    }

    public function getuom($productid)
    { 
//        $uom = DB::select('SELECT a.*, b.*, c.retail_price FROM inventory_general a INNER JOIN inventory_uom b ON b.uom_id = a.uom_id INNER JOIN inventory_price c ON c.product_id = a.id AND c.status_id = a.status WHERE a.id = ?',[$productid]);
        $uom = DB::select('SELECT a.*, b.*, IFNULL((SELECT c.cost_price FROM inventory_stock c WHERE  c.stock_id = (Select MAX(stock_id) from inventory_stock where product_id = ? AND status_id = 1)),0) AS retail_price FROM inventory_general a INNER JOIN inventory_uom b ON b.uom_id = a.uom_id WHERE a.id = ?',[$productid,$productid]);
        return $uom;
    }

    public function getJobCount($id,$itemid)
    {
        $result = DB::table('recipy_details')->where(['recipy_id' => $id,'item_id' => $itemid])->count();
        return $result;
    }

    public function chk_already_recipy($id)
    {
        $result = DB::table('recipy_general')->where('product_id',$id)->where('status_id',1)->count();
        return $result;
    }

    public function insert_general($items)
    {
        $result = DB::table('recipy_general')->insertGetId($items);
        return $result;
    }

    public function insert_sub_details($items)
    {
        $result = DB::table('recipy_details')->insert($items);
        return $result;
    }

    public function loadJob($id)
    {
        $result = DB::table('recipy_details')
            ->join("inventory_general","inventory_general.id","=","recipy_details.item_id")
            ->where("recipy_id",$id)
            ->get();
        return $result;
    }

    public function getCost($id)
    {
        $result = DB::select("SELECT SUM(cost) as amount FROM recipy_details WHERE recipy_id = ?",[$id]);
        return $result;
    }

    public function UpdateJobSubDetails($updateid,$items)
    {
        if(DB::table('recipy_details')->where('recipy_details_id', $updateid)->update($items))
        {
            return 1;
        }
        else
        {
            return 0;
        }

    }

    public function DeleteItem($id,$recipyid)
    {
        $count = DB::select('SELECT COUNT(recipy_details_id) AS counts FROM recipy_details WHERE recipy_id = ?',[$recipyid]);
        if ($count[0]->counts > 1)
        {
            $result = DB::table('recipy_details')->where('recipy_details_id', $id)->delete();
            return 1;
        }
        else{
            //all delete cascade delete == true
            $general = DB::select('DELETE FROM recipy_general WHERE recipy_id = ?',[$recipyid]);
            return 0;
        }

    }

    public function accountAdd($items)
    {
        if(DB::table('recipy_account')->insert($items))
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function accountUpdate($updateid,$items)
    {
        if(DB::table('recipy_account')->where('recipy_id', $updateid)->update($items))
        {
            return 1;
        }
        else
        {
            return 0;
        }

    }

    public function ReceivedProduct()
    {
        $GRN =  DB::table('purchase_rec_gen')->count();
        $GRN = $GRN + 1;
        $result = DB::table('purchase_rec_gen')->insertGetId(["GRN" => "GRN-".$GRN,"user_id" => session('userid'),"updated_at" =>date('Y-m-d H:s:i')]);
        return $result;


    }

    public function ReceivedProductDetails($items)
    {
        $result = DB::table("purchase_rec_goods_details")->insert($items);
        return $result;
    }

    public function Stock($items)
    {
        $result = DB::table("inventory_stock")->insert($items);
        return $result;
    }

    public function getJobAccount($id,$podid)
    {
        $result = DB::table("job_order_subdetails")->where(['job_order_id'=>$id,'product_id'=>$podid])->get();
        return $result;
    }

    public function getuombyid($id)
    {
        $result = DB::table("inventory_general")->where('id',$id)->get();
        return $result;
    }

    public function updateJobOrderGeneral($id,$qty)
    {
        $prevoiusQty = DB::table('job_order_general')->where('job_order_id',$id)->get();
        $totalQty = $prevoiusQty[0]->Received_qty + $qty;
        if ($prevoiusQty[0]->Total_qty == $totalQty)
        {
            $status = 9;
        }
        else
        {
            $status = 7;
        }
        $result = DB::table('job_order_general')->where('job_order_id',$id)->update(['Received_qty' => $totalQty,'status_id' => $status]);
        return $result;
    }

    public function job_order_general($id)
    {
        $result = DB::table("job_order_general")
            ->join("inventory_general","inventory_general.id","=","job_order_general.finished_good_id")
            ->where('job_order_id',$id)->get();
        return $result;
    }

    public function job_order_details($id)
    {
        $result = DB::table("job_order_sub_details")
            ->join("inventory_general","inventory_general.id","=","job_order_sub_details.item_id")
            ->where('job_id',$id)->get();
        return $result;
    }
    public function job_order_account($id)
    {
        $result = DB::table("job_order_account")->where('job_id',$id)->get();
        return $result;
    }

    public function getJobIdFromProduct($productID)
    {
        $result = DB::table("job_order_general")->where('finished_good_id',$productID)->get();
        return $result[0]->job_order_id;
    }

    public function InsertIntoTemp($items)
    {
        $result = DB::table("job_order_temp_details")->insert($items);
        return $result;
    }

    public function GetDataFromRecipyWithID($id)
    {
        $result = DB::select("SELECT a.*, b.*,d.total_cost FROM recipy_details a INNER JOIN inventory_general b ON b.id = a.item_id INNER JOIN recipy_account d on d.recipy_id = a.recipy_id WHERE a.recipy_id = (SELECT recipy_id FROM recipy_general c WHERE c.status_id = 1 AND c.product_id = ?)",[$id]);
        return $result;
    }

    public function DeleteTemp()
    {
        $result = DB::table("job_order_temp_details")->delete();
        return $result;
    }

    public function UpdateTempSubDetails($updateid,$items)
    {
        if(DB::table('job_order_temp_details')->where('job_sub_id', $updateid)->update($items))
        {
            return 1;
        }
        else
        {
            return 0;
        }

    }

    public function InsertIntoTempData($items)
    {
        if(DB::table('job_order_temp_details')->insert($items))
        {
            return 1;
        }
        else
        {
            return 0;
        }

    }

    public function getProductCount($itemid)
    {
        $result = DB::table('job_order_temp_details')->where(['item_id' => $itemid])->count();
        return $result;
    }

    public function getTempCost()
    {
        $result = DB::select("SELECT SUM(amount) as amount FROM job_order_temp_details ");
        return $result;
    }

    public function DeleteTempItem($id)
    {
        if(DB::table('job_order_temp_details')->where('job_sub_id', $id)->delete())
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }



    public function recipy_general($id)
    {
        $result = DB::table("recipy_general")
            ->join("inventory_general","inventory_general.id","=","recipy_general.product_id")
            ->where('recipy_id',$id)->get();
        return $result;
    }

    public function recipy_details($id)
    {
        $result = DB::table("recipy_details")
            ->join("inventory_general","inventory_general.id","=","recipy_details.item_id")
            ->where('recipy_id',$id)->get();
        return $result;
    }
    public function recipy_account($id)
    {
        $result = DB::table("recipy_account")->where('recipy_id',$id)->get();
        return $result;
    }

    public function getDetails()
    {
        $result = DB::select("SELECT a.job_order_id, a.joborder_name, a.created_at, b.cost, b.retail_cost, c.job_status_name FROM job_order_general a INNER JOIN job_order_account b ON b.job_id = a.job_order_id INNER JOIN job_status c ON c.job_status_id = a.job_status_id WHERE a.branch_id = ?",[session("branchid")]);
        return $result;
    }

    public function cancelJob($updateid)
    {
        if(DB::table('job_order_general')->where('job_order_id', $updateid)->update(['status_id' => 4]))
        {
            return 1;
        }
        else
        {
            return 0;
        }

    }

    public function jobCost($id)
    {
        $result = DB::select("Select total_cost from recipy_account where recipy_id = (SELECT recipy_id FROM recipy_general c WHERE c.status_id = 1 AND c.product_id = ? )",[$id]);
        if($result)
        {
            return $result[0]->total_cost;
        }
        else
        {
            return 0;
        }

    }

    public function insert($table,$items){
        $result = DB::table($table)->insertGetId($items);
        return $result;
    }

    public function JobGeneral($general)
    {
        $result = DB::table('job_order_general')->insertGetId($general);
        return $result;
    }

    public function JobAccount($account)
    {
        if(DB::table('job_order_account')->insert($account))
        {
            return 1;
        }
        else
        {
            return 0;
        }

    }

    public function JobAssign($assign)
    {

        if(DB::table('job_order_assign')->insert($assign))
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function recipyCalculation($product_id)
    { 
        $result = DB::select("SELECT MIN((Select SUM(balance) from inventory_stock where product_id = a.item_id)/ a.usage_qty) * c.weight_qty as totalQty,d.name as uomname FROM recipy_details a INNER JOIN recipy_general b on b.recipy_id = a.recipy_id INNER JOIN inventory_general c on c.id = a.item_id INNER JOIN inventory_uom d on d.uom_id = c.cuom where b.product_id = ? and b.status_id = 1",[$product_id]);
        return $result;
    }

    public function getRecipyDetails($itemID)
    {
        $result = DB::select("SELECT a.* FROM recipy_details a INNER JOIN recipy_general b on b.recipy_id = a.recipy_id where b.product_id = ?",[$itemID]);
        return $result;
    }

    public function getRawStock($id)
    {
        $result = DB::select("Select SUM(a.balance) as qty, b.weight_qty from inventory_stock a INNER JOIN inventory_general b ON b.id = a.product_id where a.product_id = ? and a.status_id = 1",[$id]);
        return $result;
    }

    public function getjoborderdetails($id)
    {
        $result = DB::select("SELECT a.recipy_id, c.product_name AS finish_good, c.image, d.product_name AS raw_material, b.usage_qty, b.cost, e.ingredients_cost AS job_cost, e.infrastructure_cost, e.material_cost, e.total_cost FROM recipy_general a INNER JOIN recipy_details b ON b.recipy_id = a.recipy_id INNER JOIN inventory_general c ON c.id = a.product_id INNER JOIN inventory_general d ON d.id = b.item_id INNER JOIN recipy_account e ON e.recipy_id = a.recipy_id WHERE a.recipy_id = ?",[$id]);
        return $result;
    }

    public function invent_stock_detection($branchId,$itemCode,$totalQty){

        $result = DB::select("SELECT * FROM inventory_stock WHERE product_id = $itemCode and branch_id = $branchId and status_id = 1");
        if(!empty($result)){
            $updatedstock = $totalQty;

            for($s=0;$s < sizeof($result); $s++) {

                $value = DB::select("SELECT * FROM inventory_stock WHERE product_id = $itemCode and branch_id = $branchId and status_id = 1");
                $updatedstock = ($updatedstock - $value[0]->balance);

                if ($updatedstock > 0) {
                    $update = DB::select("update inventory_stock set balance = 0,status_id = 2 where stock_id = ?",[$value[0]->stock_id]);
                }
                else if ($updatedstock < 0) {
                    $updatedstock = $updatedstock * (-1);
                    $update = DB::select("update inventory_stock set balance = ?,status_id = 1 where stock_id = ?",[$updatedstock,$value[0]->stock_id]);
                    break;
                }
                else if ($updatedstock == 0) {
                    $columns = "balance = 0,status_id = 2";
                    $update = DB::select("update inventory_stock set balance = 0,status_id = 2 where stock_id = ?",[$value[0]->stock_id]);
                    break;
                }
                return 1;
            }

        }else {
            return 0;
        }

    }

    public function Deleteall($id)
    {
        DB::table('recipy_details')->where('recipy_id', $id)->delete();
        DB::table('recipy_account')->where('recipy_id', $id)->delete();
        DB::table('recipy_general')->where('recipy_id', $id)->delete();

        return 1;

    }


    public function sub_exsits($productid, $workorderid)
    {
        $result = DB::select("SELECT COUNT(product_id) AS counts FROM job_order_subdetails WHERE product_id = ? AND job_order_id = ?",[$productid,$workorderid]);
        return $result;
    }


    public function order_details($workorderid)
    {
        $result = DB::select("SELECT a.sub_id,a.job_order_id, b.product_name, a.order_qty, a.job_cost, a.product_id FROM job_order_subdetails a INNER JOIN inventory_general b ON b.id = a.product_id
		WHERE a.job_order_id = ?",[$workorderid]);
        return $result;
    }

    public function getsum($workorderid)
    {
        $result = DB::select("SELECT SUM(job_cost * order_qty) AS total_cost FROM job_order_subdetails WHERE job_order_id = ?",[$workorderid]);
        return $result;
    }


    public function qty_update($id,$qty){
        $result = DB::table('job_order_subdetails')->where('sub_id', $id)->update(['order_qty'=>$qty]);
        return $result;
    }

    public function item_delete($id)
    {
        if (DB::table('job_order_subdetails')->where('sub_id',$id)->delete()) {
            return 1;
        }
        else{
            return 0;
        }

    }

    public function complete_delete($id)
    {
        if (DB::table('job_order_general')->where('job_order_id',$id)->delete()) {
            return 1;
        }
        else{
            return 0;
        }

    }


    public function getcount($workorderid)
    {
        $result = DB::select('SELECT COUNT(job_order_id) AS counts FROM job_order_subdetails WHERE job_order_id = ?',[$workorderid]);
        return $result;
    }

    public function getgrncount($workorderid)
    {
        $result = DB::select('SELECT COUNT(GRN) AS counts FROM purchase_rec_goods_details WHERE job_id = ?',[$workorderid]);
        return $result;
    }

    public function workorderdetails($id)
    {
        $result = DB::select('SELECT a.job_order_id, a.joborder_name, a.created_at, c.product_name, b.order_qty, b.job_cost, c.image FROM job_order_general a INNER JOIN job_order_subdetails b ON b.job_order_id = a.job_order_id INNER JOIN inventory_general c ON c.id = b.product_id WHERE a.job_order_id = ?',[$id]);
        return $result;
    }

    public function workorderdetails_sum($id)
    {
        $result = DB::select('SELECT a.joborder_name, a.created_at,  SUM(b.job_cost) as cost FROM job_order_general a INNER JOIN job_order_subdetails b ON b.job_order_id = a.job_order_id WHERE a.job_order_id = ?',[$id]);
        return $result;
    }


    public function getdetailsRecipy($recipyid){
    $result = DB::select('SELECT * FROM recipy_general a INNER JOIN recipy_details b ON b.recipy_id = a.recipy_id INNER JOIN recipy_account c ON c.recipy_id = a.recipy_id WHERE a.recipy_id = ?',[$recipyid]);
    return $result;
    }

    public function getrecid($productid){
        $result = DB::select('SELECT recipy_id FROM recipy_general WHERE product_id = ? AND status_id = 1',[$productid]);
        return $result;
    }

    public function inactiveoldecipy($recipyid,$items)
    {
        if(DB::table('recipy_general')->where('recipy_id', $recipyid)->update($items))
        {
            return 1;
        }
        else
        {
            return 0;
        }

    }

    public function getrecipycount($recipyid){
        $result = DB::select('SELECT COUNT(recipy_id) AS counts FROM recipy_general WHERE product_id = ? AND status_id = 1',[$recipyid]);
        return $result;
    }









}
