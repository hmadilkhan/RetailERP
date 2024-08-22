<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class receiveddemand extends Model
{
	public function get_demandslist(){
		$result = DB::select('SELECT a.demand_general_details_id as id, a.demand_id, c.branch_name, a.date,  b.name FROM demand_general_details a
INNER JOIN demand_status b ON b.id = a.demand_status_id
INNER JOIN branch c ON c.branch_id = a.branch_id
WHERE a.demand_status_id IN (2,7,8) AND a.branch_id IN (Select branch_id from branch where company_id = ?)',[session('company_id')]);
		return $result;

	}

	public function status(){
		$result = DB::select('SELECT * FROM demand_status
WHERE id IN (3,4)');
		return $result;
	}

	public function branches(){
		$result = DB::select('SELECT COUNT(branch_id) as branch FROM branch');
		return $result;
	}

	public function update_status($id, $statusid){
    $result = DB::table('demand_general_details')->where('demand_general_details_id', $id)->update(['demand_status_id'=>$statusid]);
    return $result;
	}

public function stock_details($itemcode,$brnch){
	$result = DB::select('SELECT a.product_name, SUM(b.balance) AS stock, c.branch_name, c.branch_id, b.product_id FROM inventory_general a
INNER JOIN inventory_stock b ON b.product_id = a.id
INNER JOIN branch c ON c.branch_id = b.branch_id
WHERE b.status_id = 1 AND a.item_code = ? '.(empty($brnch) ? '' : ' AND b.branch_id = '.$brnch.' ') .' 
GROUP BY b.branch_id',[$itemcode]  );
	return $result;
	}

    public function insert_transfer($table,$items){
	
        $result = DB::table($table)->insertGetId($items);
       return $result;   
    }

    public function get_count(){
    $count = DB::select('SELECT COUNT(a.transfer_id) AS counter FROM transfer_general_details a');
    return $count[0]->counter;
	}

	public function exsits_chk($brnchfrom,$demandid){
		$result = DB::select('SELECT COUNT(demand_id) as doid FROM transfer_general_details
WHERE branch_from = ? AND demand_id = ?',[$brnchfrom,$demandid]);
		return $result;
	}   

	public function gettransferid($demandid,$brnchfrom) {
		$result = DB::select('SELECT transfer_id FROM transfer_general_details
WHERE demand_id = ? AND branch_from = ? ',[$demandid,$brnchfrom]);
		return $result;
	}

	public function updateitem_demand($id,$statusid){
		$result = DB::table('demand_item_details')->where('demand_item_details_id', $id)->update(['status_id'=>$statusid]);
    return $result;
	}
}



