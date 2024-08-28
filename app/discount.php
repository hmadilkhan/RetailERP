<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Session;

class discount extends Model
{
	public function index($mode)
	{
		if ($mode == 2) {
			$statusfilter = " a.status = 2 and a.is_remove = 0 and a.company_id =".session('company_id');
		} else {
			$statusfilter = " a.status IN (1,3) and a.is_remove = 0 and a.company_id =".session('company_id');
		}

		$result = DB::select("SELECT a.discount_id,a.discount_code,a.status,a.open_discount,a.customer_eligibilty,b.type_name,c.discount_value,d.applies_name,e.startdate,e.starttime,e.enddate,e.endtime,f.name as status_name,g.name as website_name  FROM discount_general a 
			INNER JOIN discount_type b on b.discount_type_id = a.discount_type
			LEFT JOIN discount_general_details c on c.discount_id = a.discount_id
			LEFT JOIN discount_applies_to d on d.discount_applies_id = c.applies_to
			INNER JOIN discount_period e on e.discount_id = a.discount_id
			INNER JOIN discount_status f on f.id = a.status
			INNER JOIN website_details g on g.id = a.website_id where " . $statusfilter);
		return $result;
	}

	public function getDiscountType()
	{
		$result = DB::table("discount_type")->get();
		return $result;
	}

	public function loadDepartments()
	{
		$result = DB::table('inventory_department')->where("company_id", session("company_id"))->get();
		return $result;
	}

	public function loadPrducts()
	{
		$result = DB::table('inventory_general')->where('status', 1)->where("company_id", session("company_id"))->paginate(15);
		return $result;
	}

	public function loadProductsBySearch($name)
	{
		$result = DB::table('inventory_general')->where('status', 1)->where("company_id", session("company_id"))->where('inventory_general.product_name', 'like', '%' . $name . '%')->paginate(15);
		return $result;
	}

	public function loadProductsDropdown()
	{
		$result = DB::table('inventory_general')->where('status', 1)->where("company_id", session("company_id"))->get();
		return $result;
	}

	public function getCustomers()
	{
		$result = DB::table('customers')->where("company_id", session("company_id"))->where('status_id', 1)->get();
		return $result;
	}

	public function getDiscountValue()
	{
		$result = DB::table('discount_value')->get();
		return $result;
	}

	public function insertData($table, $items)
	{
		$result = DB::table($table)->insertGetId($items);
		return $result;
	}

	public function updateData($table, $items,$whereColumn,$whereId)
	{
		$result = DB::table($table)->where($whereColumn,$whereId)->update($items);
		return $result;
	}

	public function getDiscountInfo($discountID)
	{
		$result = DB::select("SELECT a.discount_code,b.type_name,d.applies_name,CONCAT(e.startdate,' ',e.starttime) as starts,CONCAT(e.enddate,' ',e.endtime) as ends,f.name  as status FROM discount_general a 
			INNER JOIN discount_type b on b.discount_type_id = a.discount_type
			LEFT JOIN discount_general_details c on c.discount_id = a.discount_id
			LEFT JOIN discount_applies_to d on d.discount_applies_id = c.applies_to
			INNER JOIN discount_period e on e.discount_id = a.discount_id
			INNER JOIN discount_status f on f.id = a.status where a.discount_id = ?", [$discountID]);
		return $result;
	}

	public function getDiscountCategories($discountID)
	{
		$result = DB::table('discount_category')
			->join('inventory_department', 'inventory_department.department_id', '=', 'discount_category.category_id')
			->where('discount_category.discount_id', $discountID)->get();
		return $result;
	}

	public function getDiscountProducts($discountID)
	{
		$result = DB::table('discount_product')
			->join('inventory_general', 'inventory_general.id', '=', 'discount_product.product_id')
			->where('discount_product.discount_id', $discountID)->get();
		return $result;
	}

	public function getDiscountCustomers($discountID)
	{
		$result = DB::table('discount_customer')
			->where('discount_customer.discount_id', $discountID)->get();
		return $result;
	}

	public function getCustomerBuys($discountID)
	{
		$result = DB::table('discount_customer_buys')
			->join('inventory_general', 'inventory_general.id', '=', 'discount_customer_buys.buy_product')
			->where('discount_customer_buys.discount_id', $discountID)
			->get();
		return $result;
	}

	public function getCustomerGets($discountID)
	{
		$result = DB::table('discount_customer_gets')
			->join('inventory_general', 'inventory_general.id', '=', 'discount_customer_gets.get_product')
			->where('discount_customer_gets.discount_id', $discountID)
			->get();
		return $result;
	}

	public function getDiscountGeneral($discountID)
	{
		$result = DB::table('discount_general as disc_gen')
        		    ->join('website_details','website_details.id','disc_gen.website_id')
        		    ->select('disc_gen.*','website_details.name as website_name')
        			->where('discount_id', $discountID)
        			->get();
		return $result;
	}

	public function getDiscountGeneralDetails($discountID)
	{
		$result = DB::table('discount_general_details')
			->where('discount_id', $discountID)
			->get();
		return $result;
	}

	public function getDiscountUsageLimits($discountID)
	{
		$result = DB::table('discount_limit')
			->where('discount_id', $discountID)
			->get();
		return $result;
	}

	public function getDiscountPeroid($discountID)
	{
		$result = DB::table('discount_period')
			->where('discount_id', $discountID)
			->get();
		return $result;
	}

	public function removeDiscount($discountID, $mode)
	{
	    if($mode == 'removeAll'){
	        $status = ['is_remove'=>1,'status'=>2];
            return DB::table('discount_general')
            			->whereIn('discount_id', $discountID)
            			->update($status);	        
	    }
	    
	    
		$status =['status'=>$mode];
		
		if($mode == 'delete'){
		    $status = ['is_remove'=>1,'status'=>2];
		}
		
		return DB::table('discount_general')
			->where('discount_id', $discountID)
			->update($status);
			
			
// 		return $result;
	}

	public function getWebsitesByCompany()
	{
		$result = DB::table('website_details')->where('company_id', session("company_id"))->get();
		return $result;
	}

	public function getWebsitesDaysByDiscountId($discountId)
	{
		$result = DB::table('discount_days')->where('discount_general_id', $discountId)->pluck("day");
		return $result;
	}
}
