<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class posProducts extends Model
{
    public function getbranches()
    {
        $branch = DB::table('branch')->where('company_id',session('company_id'))->get();
        return $branch;
    }

    public function getfinishgood()
    {
        $result = DB::select('SELECT * FROM inventory_general a INNER JOIN inventory_uom b ON b.uom_id = a.uom_id WHERE a.status = 1 AND product_mode IN( 2,3) AND company_id = ?',[session("company_id")]);
        return $result;
    }

    public function exsist_chk($itemname,$prod_code)
    {
        $result = DB::select('SELECT COUNT(pos_item_id) AS counts FROM pos_products_gen_details WHERE branch_id = '.session('branch').' and status_id = 1 and item_name = ? and product_id = ?',[$itemname,$prod_code]);
        return $result;
    }

    public function exsist_chk_itemcode_notEqualItemId($item_code,$item_id)
    {
        $result = DB::select('SELECT COUNT(pos_item_id) AS counts FROM pos_products_gen_details WHERE branch_id = '.session('branch').' and item_code = ? and pos_item_id != ?',[$item_code,$item_id]);
        return $result;
    }

    public function insert($table,$items){

        $result = DB::table($table)->insertGetId($items);
        return $result;
    }

    public function getposproducts()
    {
        $result = DB::select('SELECT a.pos_item_id, a.item_code, a.item_name, a.image, c.branch_name, d.product_name, e.department_name, f.status_name, b.*, a.quantity,a.uom ,g.uom_id,g.name as uomname FROM pos_products_gen_details a
			INNER JOIN pos_product_price b on b.pos_item_id = a.pos_item_id AND a.status_id = b.status_id
			INNER JOIN branch c ON c.branch_id = a.branch_id
			INNER JOIN inventory_general d ON d.id = a.product_id
			INNER JOIN inventory_department e ON e.department_id = d.department_id
			INNER JOIN accessibility_mode f ON f.status_id = a.status_id
			LEFT JOIN inventory_uom g ON g.uom_id = a.uom
			WHERE a.status_id = 1 AND b.status_id = 1 AND a.branch_id = ? order by a.pos_item_id DESC',[session("branch")]);
        return $result;
    }

    // get pos product filter by inventory general id
    public function getposproducts_filter_by_productId($productId)
    {
        $result = DB::select('SELECT a.pos_item_id,a.priority, a.item_code,a.product_id, a.item_name,a.description, a.image,a.is_hidden_attribute, c.branch_name, d.product_name, e.department_name, f.status_name, b.*, a.quantity,a.uom ,g.uom_id,g.name as uomname, h.name as attribute,h.id as attribute_id FROM pos_products_gen_details a
			INNER JOIN pos_product_price b on b.pos_item_id = a.pos_item_id AND a.status_id = b.status_id
			INNER JOIN branch c ON c.branch_id = a.branch_id
			INNER JOIN inventory_general d ON d.id = a.product_id
			INNER JOIN inventory_department e ON e.department_id = d.department_id
			INNER JOIN accessibility_mode f ON f.status_id = a.status_id
			LEFT JOIN inventory_uom g ON g.uom_id = a.uom
			Left JOIN attributes h ON h.id = a.attribute
			WHERE a.status_id = 1 AND b.status_id = 1 AND a.branch_id = ? and a.product_id = ? order by a.priority DESC',[session("branch"),$productId]);
        return $result;
    }

    public function update_pos_price($id,$items){
        $result = DB::table('pos_product_price')->where('price_id', $id)->update($items);
        return $result;
    }

	public function getuom(){
        $result = DB::table('inventory_uom')->get();
        return $result;
    }


    public function inactiveposproducts()
    {
        // $result = DB::select('SELECT a.item_id, b.sub_id, a.item_name, a.image, c.branch_name, e.department_name, b.price, f.status_name FROM pos_products_gen_details a
			// INNER JOIN pos_products_sub_details b ON b.item_id = a.item_id
			// INNER JOIN branch c ON c.branch_id = a.branch_id
			// INNER JOIN inventory_general d ON d.id = a.product_id
			// INNER JOIN inventory_department e ON e.department_id = d.department_id
			// INNER JOIN accessibility_mode f ON f.status_id = b.status_id
			// WHERE b.status_id = 2');
		$result = DB::select('SELECT a.pos_item_id, a.item_code, a.item_name, a.image, c.branch_name, d.product_name, e.department_name, f.status_name, b.*, a.quantity FROM pos_products_gen_details a
			INNER JOIN pos_product_price b on b.pos_item_id = a.pos_item_id AND a.status_id = b.status_id
			INNER JOIN branch c ON c.branch_id = a.branch_id
			INNER JOIN inventory_general d ON d.id = a.product_id
			INNER JOIN inventory_department e ON e.department_id = d.department_id
			INNER JOIN accessibility_mode f ON f.status_id = a.status_id
			WHERE a.status_id = 2 AND b.status_id = 2 AND a.branch_id = ?',[session("branch")]);
        return $result;
    }

    public function update_pos_gendetails($id,$items){
        $result = DB::table('pos_products_gen_details')->where('pos_item_id', $id)->update($items);
        return $result;
    }

    public function update_pos_gendetails_finishgoodId($productId,$items){
        $result = DB::table('pos_products_gen_details')->where('status_id',1)->where('product_id', $productId)->update($items);
        return $result;
    }

    public function getoldprice($itemid)
    {
        $result = DB::select('SELECT MAX(price) AS price FROM pos_products_sub_details WHERE item_id = ? AND status_id = 1',[$itemid]);
        return $result;
    }

    public function getid($itemid)
    {
        $result = DB::select('SELECT * FROM pos_product_price WHERE status_id = 1 AND pos_item_id = ?',[$itemid]);
        return $result;
    }

    public function getitems()
    {
        $result = DB::select('SELECT * FROM pos_products_gen_details a INNER JOIN pos_products_sub_details b ON b.item_id = a.item_id
			WHERE b.status_id = 1');
        return $result;
    }

    public function deal_exist($dealname,$branch)
    {
        $result = DB::select('SELECT COUNT(deail_id) as counts FROM pos_deals WHERE deal_name = ? AND branch_id = ?',[$dealname,$branch]);
        return $result;
    }

    public function deal_exist_items($itemid,$dealid)
    {
        $result = DB::select('SELECT COUNT(id) as counts FROM pos_deal_details WHERE item_id = ? AND deal_id = ?',[$itemid,$dealid]);
        return $result;
    }


    public function getdealtable($dealid)
    {
        $result = DB::select('SELECT * FROM pos_deals a INNER JOIN pos_deal_details b ON b.deal_id = a.deail_id INNER JOIN pos_products_gen_details c ON c.item_id = b.item_id WHERE a.deail_id = ?',[$dealid]);
        return $result;
    }


    public function DeleteItem($id)
    {
        if(DB::table('pos_deal_details')->where('id', $id)->delete())
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function update_deal_sub($id,$items){
        $result = DB::table('pos_deal_details')->where('id', $id)->update($items);
        return $result;
    }

    public function getdealdetails_active()
    {
        $result = DB::select('SELECT a.deail_id, a.image, c.branch_name, a.deal_name, a.price, e.status_name, d.item_name, b.qty, b.id FROM pos_deals a INNER JOIN pos_deal_details b ON b.deal_id = a.deail_id
            INNER JOIN branch c ON c.branch_id = a.branch_id
            INNER JOIN pos_products_gen_details d ON d.item_id = b.item_id
            INNER JOIN accessibility_mode e ON e.status_id = a.status_id
            WHERE a.status_id = 1 GROUP BY a.deail_id');
        return $result;
    }

    public function getdealdetails_in_active()
    {
        $result = DB::select('SELECT a.deail_id, a.image, c.branch_name, a.deal_name, a.price, e.status_name, d.item_name, b.qty, b.id FROM pos_deals a INNER JOIN pos_deal_details b ON b.deal_id = a.deail_id
            INNER JOIN branch c ON c.branch_id = a.branch_id
            INNER JOIN pos_products_gen_details d ON d.item_id = b.item_id
            INNER JOIN accessibility_mode e ON e.status_id = a.status_id
            WHERE a.status_id = 2 GROUP BY a.deail_id');
        return $result;
    }

    public function update_pos_deals($id,$items){
        $result = DB::table('pos_deals')->where('deail_id', $id)->update($items);
        return $result;
    }

    public function getpos_subdetails($dealid)
    {
        $result = DB::select('SELECT a.deail_id, b.id, c.image, c.item_name, b.qty FROM pos_deals a INNER JOIN pos_deal_details b ON b.deal_id = a.deail_id
            INNER JOIN pos_products_gen_details c ON c.item_id = b.item_id
            WHERE a.deail_id = ?',[$dealid]);
        return $result;
    }

    public function getdeals($dealid)
    {
        $result = DB::select('SELECT * FROM pos_deals a INNER JOIN pos_deal_details b ON b.deal_id = a.deail_id INNER JOIN branch c ON c.branch_id = a.branch_id WHERE a.deail_id = ?',[$dealid]);
        return $result;
    }

    public function verifycode($code){
        $result = DB::select('SELECT COUNT(pos_item_id) AS counts FROM pos_products_gen_details WHERE item_code = ?',[$code]);
        return $result;

    }




}
