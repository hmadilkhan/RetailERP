<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class damagedBottles extends Model
{
	  public function getvendors()
    {
    	$vendor = DB::table('vendors')->where('user_id',session('userid'))->where('status_id',1)->get();
    	return $vendor;
    }

	  public function getprodutcs()
    {
    	$product = DB::table('inventory_general')->where('user_id',session('userid'))->where('status',1)->get();
    	return $product;
    }
      public function getcustomers()
    {
    	$customers = DB::table('customers')->where('user_id',session('userid'))->where('status_id',1)->get();
    	return $customers;
    }


    public function insert($table,$items){
	
        $result = DB::table($table)->insertGetId($items);
       return $result;   
    }

    public function get_vendor_damaged()
    {
        $result = DB::select('SELECT a.damaged_vendor_id, b.vendor_name, c.item_code, c.product_name, a.qty, a.date FROM demaged_bottel_vendor a
			INNER JOIN vendors b ON b.id = a.vendor_id
			INNER JOIN inventory_general c ON c.id = a.product_id');
        return $result;
    }

      public function get_customer_damaged()
    {
        $result = DB::select('SELECT a.id, b.name, c.item_code, c.product_name, a.qty, a.date FROM damaged_bottles_cutomers a
			INNER JOIN customers b ON b.id = a.customer_id
			INNER JOIN inventory_general c ON c.id = a.product_id');
        return $result;
    }





}