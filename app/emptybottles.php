<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class emptybottles extends Model
{
	public function getProducts()
	{
		$result = DB::table('inventory_general')->where('department_id',8)->where('sub_department_id',26)->where('user_id',session('userid'))->get();
		return $result;
	}

	public function getVendors()
	{
		$result = DB::table('vendors')->get();
		return $result;
	}

	public function getList()
	{
		$result = DB::table('empty_bottles_vendor')
				  ->join('vendors','vendors.id','=','empty_bottles_vendor.vendor_id')
				  ->join('inventory_general','inventory_general.id','=','empty_bottles_vendor.product_id')
				  ->get();
		return $result;
	}

	public function getLastBalance($vendor,$product)
	{
		$result = DB::select('SELECT balance FROM empty_bottles_vendor where empty_vendor_id = (Select Max(empty_vendor_id) from empty_bottles_vendor where vendor_id = ? and product_id = ?)',[$vendor,$product]);
		return $result;
	}

	public function insertEmpty($items)
	{
		if(DB::table('empty_bottles_vendor')->insert($items))
		{
			return 1;
		}
		else
		{
			return 0;
		}

	}

	public function getListByProduct($product,$vendor)
	{
		$result = DB::select('SELECT a.*,b.vendor_name,c.product_name FROM empty_bottles_vendor a INNER JOIN vendors b on b.id = a.vendor_id INNER JOIN inventory_general c on c.id = a.product_id where product_id = ?'.((empty($vendor)) ? '' :  ' AND vendor_id = '.$vendor.''),[$product]);
		return $result;
	}

	/*Customer Data Starts*/

	public function getCustomerList()
	{
		$result = DB::table('empty_bottles_customers')
				  ->join('customers','customers.id','=','empty_bottles_customers.customer_id')
				  ->join('inventory_general','inventory_general.id','=','empty_bottles_customers.product_id')
				  ->get();
		return $result;
	}

	public function getCustomers()
	{
		$result = DB::table('customers')->where('user_id',session('userid'))->get();
		return $result;
	}

	public function getListByCustomerProduct($product,$customer)
	{
		$result = DB::select('SELECT a.*,b.name,c.product_name FROM empty_bottles_customers a INNER JOIN customers b on b.id = a.customer_id INNER JOIN inventory_general c on c.id = a.product_id where product_id = ?'.((empty($customer)) ? '' :  ' AND customer_id = '.$customer.''),[$product]);
		return $result;
	}

	public function insertCustomersEmpty($items)
	{
		if(DB::table('empty_bottles_customers')->insert($items))
		{
			return 1;
		}
		else
		{
			return 0;
		}

	}

	public function getLastCustomerBalance($customer,$product)
	{
		$result = DB::select('SELECT balance FROM empty_bottles_customers where empty_customer_id = (Select Max(empty_customer_id) from empty_bottles_customers where customer_id = ? and product_id = ?)',[$customer,$product]);
		return $result;
	}


}