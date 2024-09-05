<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class transfer extends Model
{
	public function get_transfer_orders($demandid)
	{
		$result = DB::select('SELECT transfer_id, transfer_No, date FROM transfer_general_details WHERE demand_id = ? AND status_id != 7', [$demandid]);
		return $result;
	}

	public function tranferOrder_details($transferid)
	{

		$result = DB::select('SELECT a.transfer_id, a.transfer_No, a.date, e.branch_name as branch_from, e.branch_address as br_fr_address, f.branch_name as branch_to, f.branch_address as br_to_address, c.status_name as to_status, d.item_code, d.product_name, b.qty, g.cost_price, c.status_name as item_status, d.id as product_id, b.transfer_item_id as id  FROM transfer_general_details a 
			INNER JOIN transfer_item_details b on b.transfer_id = a.transfer_id
			INNER JOIN transfer_status c on c.status_id = a.status_id
			INNER JOIN inventory_general d ON d.id = b.product_id 
            INNER JOIN branch e ON e.branch_id = a.branch_from
            INNER JOIN branch f ON f.branch_id = a.branch_to
            INNER JOIN inventory_stock g ON g.product_id = d.id
			WHERE a.transfer_id = ?
            GROUP BY d.id', [$transferid]);
		return $result;
	}

	public function get_transferlist()
	{
		$result = DB::select('SELECT a.transfer_id, a.transfer_No, a.branch_to, d.branch_name AS demanded_by, a.date, e.status_name AS name FROM transfer_general_details a
		INNER JOIN branch d ON d.branch_id = a.branch_to
		INNER JOIN transfer_status e ON e.status_id = a.status_id
		WHERE  a.status_id NOT IN (3,7) AND a.branch_from = ?', [session('branch')]);
		return $result;
	}

	public function get_transferlist_byid($trfid)
	{
		$result = DB::select('SELECT a.transfer_id, a.transfer_No, a.branch_to, d.branch_name AS demanded_by, a.date, e.status_name AS name FROM transfer_general_details a
		INNER JOIN branch d ON d.branch_id = a.branch_to
		INNER JOIN transfer_status e ON e.status_id = a.status_id
		WHERE  a.status_id NOT IN (3,7) AND a.branch_from = ? AND a.transfer_id = ?', [session('branch'), $trfid]);
		return $result;
	}

	public function status()
	{
		$result = DB::select('SELECT * FROM transfer_status
		WHERE status_id IN (2,3)');
		return $result;
	}

	public function stock_details($itemcode, $brnch)
	{
		$result = DB::select('SELECT a.product_name, SUM(b.balance) AS stock, c.branch_name, c.branch_id, b.product_id FROM inventory_general a
INNER JOIN inventory_stock b ON b.product_id = a.id
INNER JOIN branch c ON c.branch_id = b.branch_id
WHERE b.status_id = 1 AND a.item_code = ? AND c.branch_id = ?' . (empty($brnch) ? '' : ' AND b.branch_id = ' . $brnch . ' ') . ' 
GROUP BY b.branch_id', [$itemcode, session('branch')]);
		return $result;
	}

	public function updateitem_transfer($id, $statusid)
	{
		$result = DB::table('transfer_item_details')->where('transfer_item_id', $id)->update(['status_id' => $statusid]);
		return $result;
	}

	public function get_count()
	{
		$count = DB::select('SELECT COUNT(a.DC_id) AS counter FROM deliverychallan_general_details a');
		return $count[0]->counter;
	}

	// 	public function exsits_chk($brnchfrom,$transferid){
	// 		$result = DB::select('SELECT COUNT(Transfer_id) as toid FROM deliverychallan_general_details
	// WHERE branch_from = ? AND Transfer_id = ?',[$brnchfrom,$transferid]);
	// 		return $result;
	// 	}   

	public function exsits_chk($brnchfrom, $transferid)
	{
		$result = DB::table('deliverychallan_general_details')->where(['branch_from' => $brnchfrom, 'Transfer_id' => $transferid])->count();
		return $result;
	}






	public function getchallanid($transferid, $brnchfrom)
	{
		$result = DB::select('SELECT DC_id FROM deliverychallan_general_details
		WHERE Transfer_id = ? AND branch_from = ?  ', [$transferid, $brnchfrom]);
		return $result;
	}


	public function insert_deliverychallan($table, $items)
	{

		$result = DB::table($table)->insertGetId($items);
		return $result;
	}

	public function update_challan($id, $shipmentamt)
	{

		$result = DB::table('deliverychallan_general_details')->where('DC_id', $id)->update(['shipment_amount' => $shipmentamt]);
		return $result;
	}

	public function update_challan_charges($id, $shipmentcharges)
	{

		$result = DB::table('deliverychallan_item_details')->where('dc_item_id', $id)->update(['shipment_charges' => $shipmentcharges]);
		return $result;
	}


	public function get_challanitems_id($deliveryid)
	{
		$result = DB::select('SELECT * FROM deliverychallan_item_details a WHERE a.DC_Id = ?', [$deliveryid]);
		return $result;
	}

	public function get_challanlist()
	{
		$result = DB::select('SELECT a.DC_id, a.DC_No, a.Transfer_id, b.branch_name as deliverd_by, c.branch_name as destination, a.date, a.branch_to, 
IFNULL((SELECT COUNT(DC_id) AS counter FROM purchase_rec_dc_details WHERE DC_id = a.DC_id),0) AS counter FROM deliverychallan_general_details a 
INNER JOIN branch b ON b.branch_id = a.branch_from
INNER JOIN branch c ON c.branch_id = a.branch_to
WHERE a.branch_from = ? or a.branch_to = ?', [session('branch'), session('branch')]);
		return $result;
	}


	public function get_challan_Details($dcid)
	{
		$result = DB::select('SELECT a.DC_id, a.DC_No, b.branch_name as deliverd_by, b.branch_address AS del_add,  c.branch_name as destination, c.branch_address AS des_add, a.date, a.shipment_amount, d.dc_item_id, d.product_id, d.deliverd_qty, d.cost_price, d.shipment_charges, e.product_name, e.uom_id
FROM deliverychallan_general_details a 
INNER JOIN branch b ON b.branch_id = a.branch_from
INNER JOIN branch c ON c.branch_id = a.branch_to
INNER JOIN deliverychallan_item_details d ON d.DC_Id = a.DC_id
INNER JOIN inventory_general e ON e.id = d.product_id
WHERE a.DC_id = ?', [$dcid]);
		return $result;
	}

	public function insert_GRN($table, $items)
	{
		$result = DB::table($table)->insertGetId($items);
		return $result;
	}

	public function get_count_GRN()
	{
		$count = DB::select('SELECT COUNT(rec_id) AS counter FROM purchase_rec_gen');
		return $count[0]->counter;
	}

	public function insert_stock($items)
	{
		$result = DB::table('inventory_stock')->insert($items);
		return $result;
	}

	public function get_stockbalance($productid)
	{
		$result = DB::select('SELECT * from inventory_stock
		WHERE status_id = 1 AND branch_id = ? AND product_id = ?', [session('branch'), $productid]);
		return $result;
	}


	public function deduction_stock($id, $deduction, $statusid)
	{
		$result = DB::table('inventory_stock')->where('stock_id', $id)->update(['balance' => $deduction, 'status_id' => $statusid]);
		return $result;
	}


	public function getPO($demandid)
	{
		$result = DB::select('SELECT e.product_name, b.product_id, c.qty AS demandqty, b.qty AS transferqty,
	IFNULL((SELECT a.deliverd_qty FROM deliverychallan_item_details a INNER JOIN deliverychallan_general_details b on b.DC_id = a.DC_Id  WHERE product_id = c.product_id and  b.Transfer_id = a.transfer_id),0)  AS deliverdqty, IFNULL((SELECT a.qty_rec FROM purchase_rec_dc_details a
	INNER JOIN deliverychallan_general_details b ON b.DC_id = a.DC_id
	WHERE a.item_id = c.product_id AND b.Transfer_id = a.transfer_id),0) AS grnqty FROM transfer_general_details a
	INNER JOIN transfer_item_details b ON b.transfer_id = a.transfer_id
	INNER JOIN demand_item_details c ON c.demand_id = a.demand_id
	INNER JOIN deliverychallan_general_details d ON d.Transfer_id = a.transfer_id INNER JOIN inventory_general e ON e.id = c.product_id
	WHERE a.demand_id = ? GROUP BY b.product_id', [$demandid]);
		return $result;
	}


	public function insert_PO($table, $items)
	{
		$result = DB::table($table)->insertGetId($items);
		return $result;
	}

	public function count_PO()
	{
		$result = DB::select('SELECT COUNT(purchase_id) AS counter FROM purchase_general_details WHERE branch_id = ?', [session('branch')]);
		return $result;
	}

	public function edit_transfer($id, $qty)
	{
		$result = DB::table('transfer_item_details')->where('transfer_item_id', $id)->update(['qty' => $qty]);
		return $result;
	}

	public function gettransferorders()
	{
		$result = DB::select('SELECT a.transfer_id, a.transfer_No, a.demand_id, c.branch_name AS trans_from, d.branch_name AS trans_to, a.date, e.status_name  FROM transfer_general_details a
		INNER JOIN branch c ON c.branch_id = a.branch_from
		INNER JOIN branch d ON d.branch_id = a.branch_to
		INNER JOIN transfer_status e ON e.status_id = a.status_id
		WHERE a.status_id != 7 AND a.branch_from IN (SELECT branch_id FROM branch WHERE company_id = ?)
		ORDER BY a.transfer_id DESC', [session("company_id")]);
		return $result;
	}

	public function removetransferorder($id, $statusid)
	{
		$result = DB::table('transfer_general_details')->where('transfer_id', $id)->update(['status_id' => $statusid]);
		return $result;
	}

	public function getbranches()
	{
		$branches = DB::table('branch')->where('company_id', session('company_id'))->get();
		return $branches;
	}

	/* THIS IS USED TO LOAD WHERET THE STOCK WILL BE TRANSFER SO THE BRANCH IN FROM DROPDOWN SHOULD NOT SHOW UP HERE*/
	public function getTobranches($branch)
	{
		$branches = DB::table('branch')->where('company_id', session('company_id'))->whereNot('branch_id', [$branch])->get();
		return $branches;
	}

	public function getproducts($branchid)
	{
		$result = DB::select('SELECT a.id, a.product_name,a.item_code FROM inventory_general a
		INNER JOIN inventory_stock b ON b.product_id = a.id
		WHERE b.branch_id = ?
		ORDER BY a.id ASC', [$branchid]);
		return $result;
	}

	public function getstock($productid, $branchid)
	{
		$result = DB::select('SELECT IFNULL(Sum(a.balance),0) AS stock, IFNULL(b.reminder_qty,0) as reminder_qty FROM inventory_stock a
		INNER JOIN inventory_qty_reminders b ON b.inventory_id = a.product_id
		WHERE a.product_id = ? AND a.branch_id = ? AND a.status_id = 1', [$productid, $branchid]);
		return $result;
	}

	public function insert_trf($table, $items)
	{

		$result = DB::table($table)->insertGetId($items);
		return $result;
	}

	public function get_count_trf()
	{
		// $result = DB::select('SELECT COUNT(transfer_id) AS counter FROM transfer_without_demand');
		$result = DB::select('SELECT MAX(transfer_No) AS counter FROM transfer_without_demand where company_id = ?', [session("company_id")]);
		return $result;
	}

	public function exsits_chk_trf($trfid)
	{
		$result = DB::select('SELECT COUNT(transfer_id) AS counter  FROM transfer_without_demand
		WHERE status_id = 1 AND transfer_id = ?', [$trfid]);
		return $result;
	}

	public function update_trf($id, $items)
	{
		$result = DB::table('transfer_without_demand')->where('transfer_id', $id)->update($items);
		return $result;
	}

	public function trf_details($trfid)
	{
		$result = DB::select('SELECT a.transfer_item_id, a.product_id, b.image as product_image,b.url as product_image_url, b.product_name,b.uom_id, a.qty AS Transfer_Qty,(SELECT MAX(cost_price) FROM inventory_stock WHERE branch_id = ? AND status_id = 1 AND product_id = a.product_id) AS cp FROM transfer_item_details a
			INNER JOIN inventory_general b ON b.id = a.product_id
			WHERE a.transfer_id = ?', [session('branch'), $trfid]);
		return $result;
	}

	public function trf_delete($id)
	{
		if (DB::table('transfer_item_details')->where('transfer_item_id', $id)->delete()) {
			return 1;
		} else {
			return 0;
		}
	}

	public function product_exsist($trfid, $productid)
	{
		$result = DB::select('SELECT COUNT(product_id) as counter FROM transfer_item_details WHERE transfer_id = ? AND product_id = ?', [$trfid, $productid]);
		return $result;
	}

	public function trf_submit_update($id, $statusid)
	{
		$result = DB::table('transfer_without_demand')->where('transfer_id', $id)->update(['status_id' => $statusid]);

		$items = DB::table('transfer_item_details')->where('transfer_id', $id)->update(['status_id' => $statusid]);

		return $items;
	}

	public function get_trf_orders_without_demand()
	{
		$result = DB::select('SELECT a.transfer_id, a.transfer_No, a.date, b.branch_name, c.status_name as name,d.fullname FROM transfer_without_demand a
		INNER JOIN branch b ON b.branch_id = a.branch_to
		INNER JOIN transfer_status c ON c.status_id = a.status_id
		INNER JOIN user_details d ON d.id = a.user_id
		where a.branch_from IN (Select branch_id from branch where company_id = ?)
		', [session('company_id')]);
		return $result;
	}

	public function get_headoffice()
	{
		$result = DB::select('SELECT branch_id, branch_name FROM branch WHERE branch_id = ?', [session('branch')]);
		return $result;
	}

	public function trforder_delete($id)
	{
		if (DB::table('transfer_without_demand')->where('transfer_id', $id)->delete()) {
			return 1;
		} else {
			return 0;
		}
	}

	public function get_trf_details($trfid)
	{
		$result = DB::select('SELECT a.transfer_id, a.transfer_No, a.date, d.branch_name, d.branch_address, f.shipment_amount, g.shipment_charges,(g.cost_price + g.shipment_charges) AS cp , 
			e.item_code, e.product_name, b.qty AS transfer_qty, c.status_name as item_status
			FROM transfer_without_demand a
			INNER JOIN transfer_item_details b ON b.transfer_id = a.transfer_id
			INNER JOIN transfer_status c ON c.status_id = a.status_id
			INNER JOIN branch d ON d.branch_id = a.branch_to
			INNER JOIN inventory_general e ON e.id = b.product_id
            INNER JOIN deliverychallan_general_details f ON f.Transfer_id = a.transfer_id
            INNER JOIN deliverychallan_item_details g ON g.DC_Id = f.DC_id
            WHERE a.transfer_id = ?', [$trfid]);
		return $result;
	}

	public function qty_update_trf($id, $trfqty)
	{
		$result = DB::table('transfer_item_details')->where('transfer_item_id', $id)->update(['qty' => $trfqty]);
		return $result;
	}


	public function get_cp($productid)
	{

		$result = DB::select('SELECT MAX(cost_price) AS cp FROM inventory_stock WHERE branch_id = ? AND status_id = 1 AND product_id = ?', [session('branch'), $productid]);
		return $result;
	}

	public function get_details($trfid)
	{

		$result = DB::select('SELECT a.transfer_id, a.transfer_No, a.date, a.branch_from, a.branch_to, d.branch_name, d.branch_address,
			e.item_code, e.product_name, b.qty AS transfer_qty, c.status_name as item_status
			FROM transfer_without_demand a
			LEFT JOIN transfer_item_details b ON b.transfer_id = a.transfer_id
			LEFT JOIN transfer_status c ON c.status_id = a.status_id
			LEFT JOIN branch d ON d.branch_id = a.branch_to
			LEFT JOIN inventory_general e ON e.id = b.product_id
            WHERE a.transfer_id = ?', [$trfid]);
		return $result;
	}
}
