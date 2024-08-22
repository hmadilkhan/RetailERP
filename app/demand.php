<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class demand extends Model
{
    public function getproducts()
    {
         $result = DB::select('SELECT * FROM inventory_general WHERE company_id = ? AND status = 1',[session('company_id')]);
        return $result;

    }

    public function get_sender_info($id)
    {
        $sender = DB::select('SELECT * FROM branch where branch_id IN (Select branch_id from demand_general_details where demand_general_details_id = ?)',[$id]);
        return $sender;
    }

    public function get_sender_information()
    {
         $sender = DB::select('SELECT * FROM branch where branch_id = ?',[session('branch')]);
        return $sender;
    }

     public function get_reciver_info()
    {
        /*SELECT b.branch_name,a.full_name,b.branch_address FROM user_details a inner join branch b on b.branch_id  = a.branch_id  where a.company_id = ? and a.user_mode_id  = ?*/
        // $reciver = DB::select('SELECT b.branch_name, c.fullname ,b.branch_address FROM user_authorization a inner join branch b on b.branch_id = a.branch_id INNER JOIN user_details c on c.id = a.user_id where c.id = ?',[session('userid')]);
        $reciver = DB::select('SELECT * FROM branch where branch_id IN (Select branch_id from user_authorization where role_id = 2 and company_id = ?)',[session('company_id')]);
        return $reciver;
    }

    public function get_demand_info(){
        $demandinfo = DB::select('SELECT a.demand_general_details_id as id,  b.name as status, a.date  from demand_general_details a
INNER JOIN demand_status b on b.id = a.demand_status_id 
where a.demand_general_details_id = (Select MAX(demand_general_details_id) from demand_general_details )');
        return $demandinfo;
    }

       public function get_neartofinish_products()
    {
        $neartofinish = DB::select('SELECT e.id, e.product_name, SUM(a.balance) as balance FROM inventory_stock a
        INNER JOIN inventory_stock b on b.stock_id = a.stock_id
        INNER JOIN inventory_qty_reminders c on c.inventory_id = a.product_id
        INNER JOIN inventory_general e on e.id = a.product_id
        where (SELECT SUM(d.balance) from inventory_stock d WHERE d.product_id = a.product_id and d.status_id = 1 )  <= c.reminder_qty  and a.status_id = 1  AND b.branch_id = ? 
        GROUP by a.product_id' ,[session('branch')]);
        return $neartofinish;
    }

    public function get_demand(){
        $demands = DB::select('Select a.demand_general_details_id as demandid, a.demand_id,  c.branch_name, a.date, d.name from demand_general_details a 
        INNER JOIN user_authorization b on b.user_id = a.user_id
        INNER JOIN branch c on c.branch_id = b.branch_id
        INNER JOIN demand_status d on d.id = a.demand_status_id
        WHERE a.user_id = ? AND a.demand_status_id != 6 ',[session('userid')]);
        return $demands;
    }

    public function get_demand_list($demandid){
        $demand_list = DB::select('SELECT b.demand_item_details_id as id, c.product_name, b.qty FROM demand_general_details a 
        INNER JOIN demand_item_details b ON b.demand_id = a.demand_general_details_id
        INNER JOIN inventory_general c ON c.id = b.product_id
        WHERE a.demand_general_details_id = ?',[$demandid]);
        return $demand_list;
    }

    public function get_count($demandid){

        //'SELECT COUNT(demand_item_details_id) as counter  FROM demand_item_details WHERE demand_id = ?', [$demandid]
        $count = DB::select('SELECT COUNT(demand_item_details_id) as counter  FROM demand_item_details WHERE demand_id= ?', [$demandid]);
        return $count[0]->counter;
    }

    public function checkitem($demandid, $productid){
          //DB::select('SELECT demand_item_details_id AS id FROM demand_item_details WHERE demand_id = ? AND product_id = ?',[$demandid,$productid]);

        $check = DB::table('demand_item_details')->select('demand_item_details_id AS id')->where(['demand_id'=>$demandid,'product_id'=>$productid])->get();
          if($check->count() > 0){
             return $check[0]->id;
          }else {
            return 0;
          }
        
    }

    public function insert($items){
        $result = DB::table('demand_general_details')->insert($items);
    }

     public function insert_itemdetails($items){
        $result = DB::table('demand_item_details')->insert($items);
        return $result;
    }

public function update_qty($items, $id){
    $result = DB::table('demand_item_details')->where('demand_item_details_id', $id)->update(['qty'=>$items]);
    return $result;
}

public function del_item($id,$state,$demand_id){

    if($state == false && $demand_id == 0){
        $result = DB::table('demand_item_details')->where('demand_item_details_id', $id)->delete();

        return $result;
    }else{
        $result_one = DB::table('demand_item_details')->where('demand_item_details_id', $id)->delete();
        $result_two = DB::table('demand_general_details')->where('demand_general_details_id', $demand_id)->delete();
        
        return $result_two;
    }
    
}

// public function update_demand_status($demandid){
//     $result = DB::table('demand_general_details')->where('demand_general_details_id', $demandid)->update(['demand_status_id'=> 2]);
//     return $result;
// }


public function update_demand_status($demandid,$statusid){
    $result = DB::table('demand_general_details')->where('demand_general_details_id', $demandid)->update(['demand_status_id'=> $statusid]);
    return $result;
}

public function update_alldemand_status($demandid,$statusid){
    $result = DB::table('demand_general_details')->whereIn('demand_general_details_id', $demandid)->update(['demand_status_id'=> $statusid]);
    return $result;
}

public function get_count_one(){
    $count = DB::select('SELECT COUNT(a.demand_general_details_id) AS counter FROM demand_general_details a
INNER JOIN user_details b ON b.id = a.user_id
WHERE a.branch_id = ?',[session('branch')]);

    return $count[0]->counter;
    
}



public function demand_details_show($id){

                $result = DB::select("SELECT a.demand_general_details_id AS doid,a.demand_id,d.name AS status1, a.date, b.demand_item_details_id AS id, c.product_name, c.id as productid, c.item_code, b.qty, e.name, c.uom_id, IFNULL((SELECT SUM(a.qty) from  transfer_item_details a
        INNER Join transfer_general_details b on b.transfer_id = a.transfer_id
        where a.product_id = b.product_id and b.demand_id = ?),0) AS transfer_qty,IFNULL((SELECT a.quantity FROM purchase_item_details a
INNER JOIN purchase_demand b ON b.purchase_id = a.purchase_id
WHERE b.demand_id = ? AND a.item_code = b.product_id),0) AS purchase_qty FROM demand_general_details a
        LEFT JOIN demand_item_details b ON b.demand_id = a.demand_general_details_id
        LEFT JOIN inventory_general c ON c.id = b.product_id
        LEFT JOIN demand_status d ON d.id = a.demand_status_id
        LEFT JOIN demand_status e ON e.id = b.status_id
        WHERE a.demand_status_id != 6  AND a.demand_general_details_id= ?",[$id,$id,$id]);

              if(count($result) > 0) 
               {
                return $result;
               
               }
               else{
                return 0;  
               }  

}

public function get_purchase_id($demandid){
    $result = DB::select('SELECT purchase_id FROM purchase_demand WHERE demand_id = ?',[$demandid]);
    return $result;
}






    
// porana manjan
// public function demand_details_show($id){

//                 $result = DB::select("SELECT a.demand_general_details_id AS doid,a.demand_id,d.name AS status1, a.date, b.demand_item_details_id AS id, c.product_name, c.id as productid, c.item_code, b.qty, e.name, c.uom_id FROM demand_general_details a
// LEFT JOIN demand_item_details b ON b.demand_id = a.demand_general_details_id
// LEFT JOIN inventory_general c ON c.id = b.product_id
// LEFT JOIN demand_status d ON d.id = a.demand_status_id
// LEFT JOIN demand_status e ON e.id = b.status_id
// WHERE a.demand_status_id != 6  AND a.demand_general_details_id= ?",[$id]);

//               if(count($result) > 0) 
//                {
//                 return $result;
               
//                }
//                else{
//                 return 0;  
//                }  

// }


	

  

}
