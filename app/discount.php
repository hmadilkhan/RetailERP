<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class discount extends Model
{
	public function index($mode)
	{
		// $result = DB::select('SELECT a.discount_id,a.discount_code,b.type_name,c.name,d.startdate,d.starttime,d.enddate,d.endtime FROM discount_general a
		// 	INNER JOIN discount_type b on b.discount_type_id = a.discount_type
		// 	INNER JOIN discount_status c on c.id = a.status
		// 	INNER JOIN discount_period d on d.discount_id = a.discount_id  where a.status IN (1,3)');
		// return $result;
		
		if($mode == 2){
			$statusfilter = " a.status = 2";
		}else{
			$statusfilter = " a.status IN (1,3)";
		}

		$result = DB::select("SELECT a.discount_id,a.discount_code,b.type_name,d.applies_name,e.startdate,e.starttime,e.enddate,e.endtime,f.name  FROM discount_general a 
			INNER JOIN discount_type b on b.discount_type_id = a.discount_type
			LEFT JOIN discount_general_details c on c.discount_id = a.discount_id
			LEFT JOIN discount_applies_to d on d.discount_applies_id = c.applies_to
			INNER JOIN discount_period e on e.discount_id = a.discount_id
			INNER JOIN discount_status f on f.id = a.status where ".$statusfilter);
		return $result;	
	}

	public function getDiscountType()
	{
		$result = DB::table("discount_type")->get();
		return $result;
	}

	public function loadDepartments()
	{
		$result = DB::table('inventory_department')->get();
		return $result;
	}

	public function loadPrducts()
	{
		$result = DB::table('inventory_general')->where('status',1)->where("company_id",session("company_id"))->paginate(15);
		return $result;
	}

    public function loadProductsBySearch($name)
    {
        $result = DB::table('inventory_general')->where('status',1)->where("company_id",session("company_id"))->where('inventory_general.product_name', 'like', '%'.$name.'%')->paginate(15);
        return $result;
    }

    public function loadProductsDropdown()
    {
        $result = DB::table('inventory_general')->where('status',1)->where("company_id",session("company_id"))->get();
        return $result;
    }

	public function getCustomers()
	{
		$result = DB::table('customers')->get();
		return $result;
	}

	public function getDiscountValue()
	{
		$result = DB::table('discount_value')->get();
		return $result;
	}

	public function insertData($table,$items)
	{
		$result = DB::table($table)->insertGetId($items);
		return $result;
	}

	public function getDiscountInfo($discountID)
	{
		$result = DB::select("SELECT a.discount_code,b.type_name,d.applies_name,CONCAT(e.startdate,' ',e.starttime) as starts,CONCAT(e.enddate,' ',e.endtime) as ends,f.name  as status FROM discount_general a 
			INNER JOIN discount_type b on b.discount_type_id = a.discount_type
			LEFT JOIN discount_general_details c on c.discount_id = a.discount_id
			LEFT JOIN discount_applies_to d on d.discount_applies_id = c.applies_to
			INNER JOIN discount_period e on e.discount_id = a.discount_id
			INNER JOIN discount_status f on f.id = a.status where a.discount_id = ?",[$discountID]);
		return $result;
	}

	Public function getDiscountCategories($discountID)
	{
		$result = DB::table('discount_category')
					->join('inventory_department','inventory_department.department_id','=','discount_category.category_id')
					->where('discount_category.discount_id',$discountID)->get();
		return $result;
	}

	Public function getDiscountProducts($discountID)
	{
		$result = DB::table('discount_product')
					->join('inventory_general','inventory_general.id','=','discount_product.product_id')
					->where('discount_product.discount_id',$discountID)->get();
		return $result;
	}

	Public function getDiscountCustomers($discountID)
	{
		$result = DB::table('discount_customer')
					->where('discount_customer.discount_id',$discountID)->get();
		return $result;
	}

	public function getCustomerBuys($discountID)
	{
		$result = DB::table('discount_customer_buys')
				->join('inventory_general','inventory_general.id','=','discount_customer_buys.buy_product')
				->where('discount_customer_buys.discount_id',$discountID)
				->get();
		return $result;
	}

	public function getCustomerGets($discountID)
	{
		$result = DB::table('discount_customer_gets')
				->join('inventory_general','inventory_general.id','=','discount_customer_gets.get_product')
				->where('discount_customer_gets.discount_id',$discountID)
				->get();
		return $result;
	}

	public function getDiscountGeneral($discountID)
	{
		$result = DB::table('discount_general')
				->where('discount_id',$discountID)
				->get();
		return $result;
	}

	public function getDiscountGeneralDetails($discountID)
	{
		$result = DB::table('discount_general_details')
				->where('discount_id',$discountID)
				->get();
		return $result;
	}

	public function getDiscountUsageLimits($discountID)
	{
		$result = DB::table('discount_limit')
				->where('discount_id',$discountID)
				->get();
		return $result;
	}

	public function getDiscountPeroid($discountID)
	{
		$result = DB::table('discount_period')
				->where('discount_id',$discountID)
				->get();
		return $result;
	}
	
	public function removeDiscount($discountID,$mode)
	{
		$status = ($mode == "delete" ? 0 : 1);
		$result = DB::table('discount_general')
				->where('discount_id',$discountID)
				->update(["status" => $status]);
		return $result;
	}
}