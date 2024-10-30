<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Branch;

class inventory extends Model
{
    protected  $guarded = [];

    // public function getDeal(){
    //     return $this->hasMany("App\InventoryDealGeneral","inventory_deal_id","id");
    // }

	public function searchProductByNameAndItemCode($search)
	{
		$query = DB::table('inventory_general as invent')
				 ->where("company_id",session("company_id"))
				 ->where("status",1)
				 ->select("invent.product_name","invent.id","invent.item_code");
		if(is_numeric($search)){
			$query->where('invent.item_code', 'like', '%'.$search.'%');
		}else{
			$query->where('invent.product_name', 'like', '%'.$search.'%');
		}
		$inventory = $query->get();
		return $inventory;
	}

	public function getDepartAndSubDepart($departId,$subDepartId)
	{
		$result = DB::table('inventory_department')
				 ->join("inventory_sub_department","inventory_sub_department.department_id","inventory_department.department_id")
				 ->where("inventory_department.department_id",$departId)
				 ->where("inventory_sub_department.sub_department_id",$subDepartId)
				 ->select("inventory_department.code as deptcode","inventory_department.department_name","inventory_sub_department.code as sdeptcode","inventory_sub_department.sub_depart_name")
				 ->get();
		return $result;
	}

    public function getData(){
        // DB::enableQueryLog();



		if(session("roleId") == 2)
		{
			// return DB::table('inventory_stock as stock')
			// ->join('inventory_general as invent','invent.id','=','stock.product_id')
			// ->join('inventory_uom as u','u.uom_id','=','invent.uom_id')
			// ->join('inventory_department as dept','dept.department_id','=','invent.department_id')
			// ->join('inventory_sub_department as sdept','sdept.sub_department_id','=','invent.sub_department_id')
			// ->join('inventory_product_mode','inventory_product_mode.product_mode_id','=','invent.product_mode')
			// ->join('inventory_price','inventory_price.product_id','=','invent.id')
			// ->select('invent.*','u.name','dept.department_name','sdept.sub_depart_name','inventory_product_mode.product_name as category','inventory_price.*','invent.image as product_image',DB::raw('SUM(stock.balance) As stock'))
			// ->where('stock.status_id',1)
			// ->get();
			$query = DB::table('inventory_general as invent')
			->join('inventory_uom as u','u.uom_id','=','invent.uom_id')
			->join('inventory_department as dept','dept.department_id','=','invent.department_id')
			->join('inventory_sub_department as sdept','sdept.sub_department_id','=','invent.sub_department_id')
			->join('inventory_product_mode','inventory_product_mode.product_mode_id','=','invent.product_mode')
			->join('inventory_price','inventory_price.product_id','=','invent.id')
			->leftJoin("inventory_stock",'inventory_stock.product_id','=','invent.id')
			->select('invent.*','u.name','dept.department_name','sdept.sub_depart_name','inventory_product_mode.product_name as category','inventory_price.*','invent.image as product_image','invent.url as product_image_url',DB::raw('SUM(inventory_stock.balance) As stock'))
			->where('invent.company_id',session('company_id'))
			->where('invent.status',1)
			->where('inventory_price.status_id',1)
			->where('inventory_stock.branch_id',session('branch'))
			->groupBy("invent.id")
			->orderBy("invent.id");

			$inventory = $query->paginate(50);
			return $inventory;
		}
		else
		{
			$query = DB::table('inventory_general as invent')
			->join('inventory_uom as u','u.uom_id','=','invent.uom_id')
			->join('inventory_department as dept','dept.department_id','=','invent.department_id')
			->join('inventory_sub_department as sdept','sdept.sub_department_id','=','invent.sub_department_id')
			->join('inventory_product_mode','inventory_product_mode.product_mode_id','=','invent.product_mode')
			->join('inventory_price','inventory_price.product_id','=','invent.id')
			->leftJoin("inventory_stock",'inventory_stock.product_id','=','invent.id')
			->select('invent.*','u.name','dept.department_name','sdept.sub_depart_name','inventory_product_mode.product_name as category','inventory_price.*','invent.image as product_image','invent.url as product_image_url',DB::raw('SUM(inventory_stock.balance) As stock'))
			->where('invent.company_id',session('company_id'))
			->where('invent.status',1)
			->where('inventory_price.status_id',1)
			->where('inventory_stock.branch_id',session('branch'))
			->groupBy("invent.id")
			->orderBy("invent.id");

			$inventory = $query->paginate(50);
			return $inventory;

		}

       // $inventory = $query->toSql();
        // print_r(DB::getQueryLog());exit;

    }

    public function findProductByIdInPriceTable($productid){
      $result = DB::select("SELECT * FROM inventory_price WHERE product_id = $productid");
      if(count($result) > 0){
        return true;
      }
      return false;
    }

public function updateProductName($id,$name)
{
	if($id > 0){
		 DB::table("inventory_general")->where("id",$id)->update(["product_name" => $name]);
		 return true;
	}
}

 public function updateToRetailPrice($productid,$actual,$rate,$taxamount,$retailprice,$wholesale,$online,$discount){
        $items = [
          "actual_price" => ($actual == "" ? "0.00" : $actual) ,
          "tax_rate" =>  ($rate == "" ? "0.00" : $rate),
          "tax_amount" => ($taxamount == "" ? "0.00" : $taxamount) ,
          "retail_price" => ($retailprice == "" ? "0.00" : $retailprice) ,
          "wholesale_price" => ($wholesale == "" ? "0.00" : $wholesale),
          "online_price" => ( $online == "" ? "0.00" : $online),
          "discount_price" => ($discount == "" ? "0.00" : $discount) ,
          "product_id" => $productid ,
          "status_id" =>  1,
          "date" =>  date("Y-m-d")." ".date("H:i:s"),
        ];
//        $result = DB::statement("UPDATE inventory_price i SET retail_price = $retailprice,actual_price=$actual,tax_rate=$rate,tax_amount=$taxamount,wholesale_price=$wholesale,online_price=$online,discount_price=$discount WHERE i.product_id = $productid AND i.price_id IN  (SELECT price_id FROM (SELECT MAX(price_id) AS price_id FROM inventory_price inP WHERE inP.product_id = $productid AND inP.`status_id` = 1 ) xx ) ");
        $result = DB::statement("UPDATE inventory_price i SET status_id = 2  WHERE i.product_id = $productid AND i.price_id IN  (SELECT price_id FROM (SELECT MAX(price_id) AS price_id FROM inventory_price inP WHERE inP.product_id = $productid AND inP.`status_id` = 1 ) xx ) ");
        $insert = DB::table("inventory_price")->insert($items);
      return true;
    }
    //

    public function gettestData(){
        $inventory = DB::table('inventory_general as invent')
            ->join('inventory_uom as u','u.uom_id','=','invent.uom_id')
            ->join('inventory_department as dept','dept.department_id','=','invent.department_id')
            ->join('inventory_sub_department as sdept','sdept.sub_department_id','=','invent.sub_department_id')
            ->join('inventory_product_mode','inventory_product_mode.product_mode_id','=','invent.product_mode')
            ->join('inventory_price','inventory_price.product_id','=','invent.id')
            ->leftJoin("inventory_stock",'inventory_stock.product_id','=','invent.id')
            ->select('invent.*','u.*','dept.*','sdept.*','inventory_product_mode.product_name as category','inventory_price.*','inventory_stock.*',DB::raw('AVG(inventory_stock.cost_price) as user_count'))
            ->where('invent.company_id',session('company_id'))
            ->where('invent.status',1)
            ->where('inventory_stock.status_id',1)
            ->where('inventory_price.status_id',1)
            ->orderBy("invent.id")
            ->paginate(5);

        return $inventory;
    }

    public function getInactiveData(){
        $inventory = DB::table('inventory_general as invent')
            ->join('inventory_uom as u','u.uom_id','=','invent.uom_id')
            ->join('inventory_department as dept','dept.department_id','=','invent.department_id')
            ->join('inventory_sub_department as sdept','sdept.sub_department_id','=','invent.sub_department_id')
            ->join('inventory_product_mode','inventory_product_mode.product_mode_id','=','invent.product_mode')
            ->join('inventory_price','inventory_price.product_id','=','invent.id')
			->leftJoin("inventory_stock",'inventory_stock.product_id','=','invent.id')
             ->select('invent.*','u.name','dept.department_name','sdept.sub_depart_name','inventory_product_mode.product_name as category','inventory_price.*','invent.image as product_image',DB::raw('SUM(inventory_stock.balance) As stock'))
            ->where('invent.company_id',session('company_id'))
            ->where('invent.status',2)
            ->where('inventory_price.status_id',1)
			->groupBy("invent.id")
            ->orderBy("invent.id")
            ->paginate(5);

        return $inventory;
    }

	public function getNonStockInventory($code,$name,$dept,$sdept,$retail_price,$ref)
	{
		$filter = "";
		if($code != ""){
			$filter .= " and invent.item_code like '%".$code."%'";
		}
		if($name != ""){
			$filter .= " and invent.product_name like '%".$name."%'";
		}
		if($dept != ""){
			$filter .= " and invent.department_id like '%".$dept."%'";
		}
		if($sdept != "all"){
			$filter .= " and invent.sub_department_id like '%".$sdept."%'";
		}
		if($retail_price != ""){
			$filter .= " and inventory_price.retail_price = ".$retail_price;
		}
		if(!empty($ref)){
		   $filter .= " and invent.id IN (SELECT product_id FROM `inventory_reference` where refrerence = '".$ref."' group by product_id)";
	    }
		return DB::select("SELECT invent.*,u.name,dept.department_name,sdept.sub_depart_name,inventory_product_mode.product_name as category,inventory_price.*,invent.image as product_image FROM `inventory_general` as invent INNER JOIn inventory_uom u on u.uom_id = invent.uom_id INNER JOIn inventory_department dept on dept.department_id = invent.department_id INNER JOIn inventory_sub_department sdept on sdept.sub_department_id = invent.sub_department_id INNER JOIn inventory_product_mode on inventory_product_mode.product_mode_id = invent.product_mode INNER JOIn inventory_price on inventory_price.product_id = invent.id and status_id = 1 where invent.company_id = ? and invent.id not In (SELECT product_id FROM `inventory_stock` where branch_id = ? and product_id = id ) and invent.status = 1 ".$filter,[session('company_id'),session('branch')]);
	}

	public function getInventoryReferences()
	{
		return DB::select("SELECT * FROM `inventory_reference` where product_id IN (Select id from inventory_general where company_id = ?) and refrerence != '' GROUP by refrerence",[session('company_id')]);
	}

    public function getBranches(){
        if (session("roleId") == 2) {
            $result = DB::table('branch')->where('company_id',session("company_id"))->get();
            return $result;
        }
        else{
            $result = DB::table('branch')->where('branch_id',session("branch"))->get();
            return $result;
        }
    }

    public function getInventoryForPagewiseByFiltersLivewire($code="",$name="",$dept="",$sdept="",$retail_price="",$ref="",$status="",$nonstock=false)
	{
		if($status==""){
			$status = 1;
		}
		$query = DB::table('inventory_general as invent')
		->join('inventory_uom as u','u.uom_id','=','invent.uom_id')
		->leftJoin('inventory_department as dept','dept.department_id','=','invent.department_id')
		->leftJoin('inventory_sub_department as sdept','sdept.sub_department_id','=','invent.sub_department_id')
		->join('inventory_product_mode','inventory_product_mode.product_mode_id','=','invent.product_mode')
		->join('inventory_price','inventory_price.product_id','=','invent.id')
        ->leftJoin('website_products', function($join) {
            $join->on('website_products.inventory_id', '=', 'invent.id')
                 ->where('website_products.status', '=', 1);
        })
        ->leftJoin("website_details",'website_details.id','website_products.website_id')
		->leftJoin("inventory_stock",'inventory_stock.product_id','=','invent.id')


		->where(function ($query) use ($code,$name,$dept,$sdept,$retail_price,$ref,$nonstock) {

			if($nonstock == false){
				 $query->leftJoin("inventory_stock",'inventory_stock.product_id','=','invent.id');
				 $query->where('inventory_stock.branch_id',session('branch'));
				 $query->select('invent.*','u.name','dept.department_name','sdept.sub_depart_name','inventory_product_mode.product_name as category','inventory_price.*','invent.image as product_image','invent.url as product_image_url',DB::raw('SUM(inventory_stock.balance) As stock'));
			}
			if($nonstock == true){
				$query->select('invent.*','u.name','dept.department_name','sdept.sub_depart_name','inventory_product_mode.product_name as category','inventory_price.*','invent.image as product_image','invent.url as product_image_url');
			}
			if($nonstock == false){
				$query->leftJoin("inventory_stock",'inventory_stock.product_id','=','invent.id');
				if(session("roleId") == 2){
					$query->whereIn('inventory_stock.branch_id',DB::table("branch")->where("company_id",session("company_id"))->pluck("branch_id"));
				}else{
					$query->where('inventory_stock.branch_id',session('branch'));
				}
			}

			if(!empty($code)){
				$query->where('invent.item_code', 'like', '%'.$code.'%');
			}
			if(!empty($name)){
			   $query->where('invent.product_name', 'like', '%'.$name.'%');
			}
		    if(!empty($dept)){
			   $query->where('invent.department_id', $dept);
		    }
		    if(!empty($sdept) && $sdept != "all"){
			   $query->where('invent.sub_department_id',$sdept);
		    }
		    if(!empty($retail_price)){
			   $query->where('inventory_price.retail_price',$retail_price);
		    }
		    if(!empty($ref)){
			   $query->whereIn('inventory_reference.product_id',$ids);
		    }
			if($nonstock == true){
				if(session("roleId") == 2){
					$query->whereNotIn("invent.id",DB::table("inventory_stock")->whereIn("branch_id",DB::table("branch")->where("company_id",session("company_id"))->pluck("branch_id"))->pluck("product_id"));
				}else{
					$query->whereNotIn("invent.id",DB::table("inventory_stock")->where("branch_id",session('branch'))->pluck("product_id"));
				}
			}
		})
		->select('invent.*','u.name','dept.department_name','sdept.sub_depart_name','inventory_product_mode.product_name as category','inventory_price.*','invent.image as product_image','invent.url as product_image_url',DB::raw('SUM(inventory_stock.balance) As stock'),'website_details.id as website_id','website_details.name as website_name')
		->where('invent.company_id',session('company_id'))
		->where('invent.status',$status)
		->where('inventory_price.status_id',1)
		->groupBy("invent.id")
		->orderBy("invent.id");

		return $query->paginate(50);

    }

	public function getInventoryForPagewiseByFilters($code="",$name="",$dept="",$sdept="",$retail_price="",$ref="",$status="",$nonstock=0)
	{
		if($status==""){
			$status = 1;
		}
		// echo "Non Stock".$nonstock;
		// return $nonstock;
		// return DB::table("inventory_stock")->where("branch_id",session('branch'))->pluck("product_id");
		$query = DB::table('inventory_general as invent')
		->join('inventory_uom as u','u.uom_id','=','invent.uom_id')
		->leftJoin('inventory_department as dept','dept.department_id','=','invent.department_id')
		->leftJoin('inventory_sub_department as sdept','sdept.sub_department_id','=','invent.sub_department_id')
		->join('inventory_product_mode','inventory_product_mode.product_mode_id','=','invent.product_mode')
		->join('inventory_price','inventory_price.product_id','=','invent.id')
        ->leftJoin('website_products', function($join) {
            $join->on('website_products.inventory_id', '=', 'invent.id')
                 ->where('website_products.status', '=', 1);
        })
        ->leftJoin('pos_products_gen_details', function($join) {
            $join->on('pos_products_gen_details.product_id', '=', 'invent.id')
                 ->where('pos_products_gen_details.status_id', '=', 1)
                 ->groupBy('pos_products_gen_details.product_id');
        })
        ->leftJoin("website_details",'website_details.id','website_products.website_id')
		->leftJoin("inventory_stock",'inventory_stock.product_id','=','invent.id')


		->where(function ($query) use ($code,$name,$dept,$sdept,$retail_price,$ref,$nonstock) {

			if($nonstock == 0){
				 $query->leftJoin("inventory_stock",'inventory_stock.product_id','=','invent.id');
				 $query->where('inventory_stock.branch_id',session('branch'));
				 $query->select('invent.*','u.name','dept.department_name','sdept.sub_depart_name','inventory_product_mode.product_name as category','inventory_price.*','invent.image as product_image','invent.url as product_image_url',DB::raw('SUM(inventory_stock.balance) As stock'));
			}
			if($nonstock == 1){
				$query->select('invent.*','u.name','dept.department_name','sdept.sub_depart_name','inventory_product_mode.product_name as category','inventory_price.*','invent.image as product_image','invent.url as product_image_url');
			}
			if($nonstock == 0){
				$query->leftJoin("inventory_stock",'inventory_stock.product_id','=','invent.id');
				if(session("roleId") == 2){
					$query->whereIn('inventory_stock.branch_id',DB::table("branch")->where("company_id",session("company_id"))->pluck("branch_id"));
				}else{
					$query->where('inventory_stock.branch_id',session('branch'));
				}
			}

			if(!empty($code)){
				$query->where('invent.item_code', 'like', '%'.$code.'%');
			}
			if(!empty($name)){
			   $query->where('invent.product_name', 'like', '%'.$name.'%');
			}
		    if(!empty($dept)){
			   $query->where('invent.department_id', $dept);
		    }
		    if(!empty($sdept) && $sdept != "all"){
			   $query->where('invent.sub_department_id',$sdept);
		    }
		    if(!empty($retail_price)){
			   $query->where('inventory_price.retail_price',$retail_price);
		    }
		    if(!empty($ref)){
			   $query->whereIn('inventory_reference.product_id',$ids);
		    }
			if($nonstock == 1){
				if(session("roleId") == 2){
					$query->whereNotIn("invent.id",DB::table("inventory_stock")->whereIn("branch_id",DB::table("branch")->where("company_id",session("company_id"))->pluck("branch_id"))->pluck("product_id"));
				}else{
					$query->whereNotIn("invent.id",DB::table("inventory_stock")->where("branch_id",session('branch'))->pluck("product_id"));
				}

			}
		})
		->select('invent.*','u.name','dept.department_name','sdept.sub_depart_name','inventory_product_mode.product_name as category','inventory_price.*','invent.image as product_image','invent.url as product_image_url',DB::raw('SUM(inventory_stock.balance) As stock'),'website_details.id as website_id','website_details.name as website_name','pos_products_gen_details.pos_item_id')
		->where('invent.company_id',session('company_id'))
        // ->where('website_products.status',1)
        // ,'website_details.id as website_id','website_details.name as website_name'
		->where('invent.status',$status)
		->where('inventory_price.status_id',1)
		// ->where('inventory_stock.branch_id',session('branch'))
		->groupBy("invent.id")
		->orderBy("invent.id");

		return $query->paginate(50);

    }

    public function getDataByName($code,$name,$dept,$sdept,$retail_price,$ref){
        // echo 1;exit;
        // DB::enableQueryLog();
		$ids = [];
		if(!empty($ref)){
			$references = DB::select('SELECT product_id FROM `inventory_reference` where product_id IN (Select id from inventory_general where company_id = '.session('company_id').') and refrerence = "'.$ref.'" GROUP by refrerence ');
			foreach($references as $key => $reference){
				array_push($ids,$reference->product_id);
			}
		}

        $inventory = DB::table('inventory_general as invent')
            ->join('inventory_uom as u','u.uom_id','=','invent.uom_id')
            ->join('inventory_department as dept','dept.department_id','=','invent.department_id')
            ->join('inventory_sub_department as sdept','sdept.sub_department_id','=','invent.sub_department_id')
            ->join('inventory_product_mode','inventory_product_mode.product_mode_id','=','invent.product_mode')
            ->join('inventory_price','inventory_price.product_id','=','invent.id')
			->leftJoin("inventory_stock",'inventory_stock.product_id','=','invent.id')
			->leftJoin("inventory_reference",'inventory_reference.product_id','=','invent.id')
            ->select('invent.*','u.name','dept.department_name','sdept.sub_depart_name','inventory_product_mode.product_name as category','inventory_price.*','invent.image as product_image',DB::raw('SUM(inventory_stock.balance) As stock'))

			->where(function ($query) use ($code,$name,$dept,$sdept,$retail_price,$ref,$ids) {
                if(!empty($code)){
                    $query->where('invent.item_code', 'like', '%'.$code.'%');
                }
                if(!empty($name)){
                   $query->where('invent.product_name', 'like', '%'.$name.'%');
               }
               if(!empty($dept)){
                   // $query->orWhere('invent.department_id', 'like', '%'.$dept.'%');
				   $query->where('invent.department_id', $dept);
               }
			   //
               if(!empty($sdept) && $sdept != "all"){
                   $query->where('invent.sub_department_id',$sdept);
               }
			   if(!empty($retail_price)){
                   $query->where('inventory_price.retail_price',$retail_price);
               }
			   if(!empty($ref)){
                   $query->whereIn('inventory_reference.product_id',$ids);
               }

            })
			->where('invent.company_id',session('company_id'))
			->where('inventory_stock.branch_id',session('branch'))
            ->where('invent.status',1)
            ->where('inventory_price.status_id',1)
			->where(function ($query) use ($ref,$ids) {
				if(!empty($ref)){
                   $query->whereIn('inventory_reference.product_id',$ids);
               }
			})
			->groupBy("invent.id","inventory_reference.product_id")
			->orderBy("invent.id")
            ->paginate(100);
            // ->toSql();
        // print_r(DB::getQueryLog());exit;
        return $inventory;
    }

    public function getInactiveInventoryBySearch($code,$name,$dept,$sdept,$ref){
		$ids = [];
		if(!empty($ref)){
			$references = DB::select('SELECT product_id FROM `inventory_reference` where product_id IN (Select id from inventory_general where company_id = '.session('company_id').') and refrerence = "'.$ref.'" GROUP by refrerence ');
			foreach($references as $key => $reference){
				array_push($ids,$reference->product_id);
			}
		}

        $inventory = DB::table('inventory_general as invent')
            ->join('inventory_uom as u','u.uom_id','=','invent.uom_id')
            ->join('inventory_department as dept','dept.department_id','=','invent.department_id')
            ->join('inventory_sub_department as sdept','sdept.sub_department_id','=','invent.sub_department_id')
            ->join('inventory_product_mode','inventory_product_mode.product_mode_id','=','invent.product_mode')
            ->join('inventory_price','inventory_price.product_id','=','invent.id')
			->leftJoin("inventory_stock",'inventory_stock.product_id','=','invent.id')
			->leftJoin("inventory_reference",'inventory_reference.product_id','=','invent.id')
            ->select('invent.*','u.name','dept.department_name','sdept.sub_depart_name','inventory_product_mode.product_name as category','inventory_price.*','invent.image as product_image',DB::raw('SUM(inventory_stock.balance) As stock'))
            ->where('invent.company_id',session('company_id'))
            ->where('invent.status',2)
            ->where('inventory_price.status_id',1)
            ->where('invent.item_code', 'like', '%'.$code.'%')
            ->where('invent.product_name', 'like', '%'.$name.'%')
            ->where('invent.department_id', 'like', '%'.$dept.'%')
			->where(function ($query) use ($sdept) {
				if(!empty($sdept) && $sdept != "all"){
                   $query->where('invent.sub_department_id',$sdept);
               }
			})
            // ->where('invent.sub_department_id', 'like', '%'.$sdept.'%')
			->where('inventory_stock.branch_id',session('branch'))
            ->orderBy("invent.id")
            ->paginate(100);

        return $inventory;
    }



    public function get_details($id){
        $inventory = DB::select('SELECT a.*,b.*,c.product_name as mode_name,a.short_description,a.details AS mode FROM inventory_general a INNER JOIN inventory_qty_reminders b ON b.inventory_id = a.id INNER JOIN inventory_product_mode c ON c.product_mode_id = a.product_mode WHERE a.slug = ?',[$id]);

//        $inventory = DB::table('inventory_general')
//        ->join('inventory_qty_reminders', 'inventory_general.id', '=', 'inventory_qty_reminders.inventory_id')
//        ->join('inventory_product_mode', 'inventory_general.product_mode', '=', 'inventory_product_mode.product_mode_id')
//        ->select('inventory_general.*', 'inventory_qty_reminders.*','inventory_product_mode.*')
//        ->where('inventory_id',$id)->get();

        return $inventory;
    }

    public function insert($fields){

    	$result = DB::table('inventory_general')->insertGetId($fields);
    	return $result;

    }

    public function insertgeneral($table,$items){

        $result = DB::table($table)->insertGetId($items);
        return $result;
    }

    public function modify($fields,$id){

    	$result = DB::table('inventory_general')->where('id',$id)->update($fields);;
    	return $result;

    }

    public function modifyReminder($id,$qty){

    	$result = DB::table('inventory_qty_reminders')->where('reminder_id',$id)->update(['reminder_qty' => $qty]);
    	return $result;

    }



    public function ReminderInsert($id,$qty){

    	$result = DB::table('inventory_qty_reminders')->insert(['inventory_id' => $id,'reminder_qty' => $qty]);
    	return $result;
    }

    public function department(){

    	$result = DB::table('inventory_department')->where('company_id',session('company_id'))->where('status',1)->get();
    	return $result;
    }

    public function subDepartment(){

    	$result = DB::table('inventory_sub_department')->get();
    	return $result;
    }

    public function uom(){

    	$result = DB::table('inventory_uom')->get();
    	return $result;
    }

    public function branch(){

    	$result = DB::table('branch')->where('company_id',session('company_id'))->get();
    	return $result;
    }

    public function getproductsBySubDepartment($id){

        $result = DB::table('inventory_general')->where('inventory_general.sub_department_id',$id)->get();
        return $result;
    }

    public function getproducts(){

        $result = DB::table('inventory_general')->join('inventory_department','inventory_department.department_id','=','inventory_general.department_id')->where('inventory_general.status',1)->where('inventory_general.company_id',session("company_id"))->where('inventory_general.status',1)->get();
        return $result;
    }

    public function searchproducts($search){
        $Records = array();
        // DB::enableQueryLog();
        $result = DB::table('inventory_general')->join('inventory_department','inventory_department.department_id','=','inventory_general.department_id')->where('inventory_general.status',1)->where('inventory_general.company_id',session("company_id"))->where('inventory_general.status',1)->where(function ($query) use ($search) {
            // Everything within this closure will be grouped together
            $query->where('department_name','LIKE',''.$search.'%')
                      ->orWhere('inventory_general.product_name','LIKE',''.$search.'%');
            })->get();
        // print_r(DB::getQueryLog());exit;
        if(count($result) > 0){
            foreach($result AS $val){
                $Records[] = array(
                    'tag_id' => $val->id,
                    'tag_value' => $val->department_name . ' | ' . $val->item_code . ' | ' . $val->product_name
                );
            }
        }

        return $Records;
    }

    // check exist itemcode and itemname //
    public function check_exists($where){
         return DB::table('inventory_general')->where($where)->get();
    }

    public function get_inventory($id){

        $inventory = DB::select('SELECT a.id,a.item_code,a.product_name,b.name,SUM(c.qty) as qty,d.cost_price,d.retail_price,d.wholesale_price,d.discount_price FROM inventory_general a INNER JOIN inventory_uom b on b.uom_id = a.uom_id INNER JOIN inventory_stock c on c.product_id = a.id and c.status_id = 1 INNER JOIN inventory_stock d on d.product_id = a.id and d.stock_id = (Select MAX(stock_id) from inventory_stock where product_id = a.id) where a.company_id = ?',[$id]);

        return $inventory;
    }

    public function delete_inventory($id,$status){
         if(DB::table('inventory_general')->where('id',$id)->update(['status' => $status]))
         {
			 $count = DB::table('pos_products_gen_details')->where('product_id',$id)->count();
			 if($count > 0){
				DB::table('pos_products_gen_details')->where('product_id',$id)->update(['status_id' => $status]);
				DB::table('website_products')->where('inventory_id',$id)->update(['status' => 0]);
			 }

            return 1;

         }else
         {
            return 0;
         }
    }

    public function multiple_active_inventory($id){
         if(DB::table('inventory_general')->whereIn('id',$id)->update(['status' => 1]))
         {
            return 1;
         }else
         {
            return 0;
         }
    }

    public function chk_itemcode($itemcode){
        $result = DB::table('inventory_general')->where('item_code', $itemcode)->where('company_id', session("company_id"))->count('item_code');
        return $result;
    }

    public function getProductMode()
    {
        $result = DB::table('inventory_product_mode')->get();
        return $result;
    }

    public function getUOMID($id)
    {
        $uom = DB::select("SELECT uom_id FROM `inventory_general` WHERE id = $id");
        return $uom;
    }

    public function temptable()
    {
        DB::select();
    }

    public function getCheckByStock($id,$branch)
    {
        $result = DB::table('inventory_stock')->where('product_id', $id)->where('branch_id', $branch)->count('product_id');
        return $result;
    }

    public function getRawItems(){

        $result = DB::select('SELECT a.* FROM inventory_general a INNER JOIN inventory_stock b on b.product_id = a.id WHERE a.product_mode = 1 Group by a.id');
        return $result;
    }

    public function getUOMFromProduct($id)
    {
        $result = DB::select('SELECT a.*,(Select MAX(cost_price) from inventory_stock where product_id = ?) as price FROM inventory_general a INNER JOIN inventory_stock b on b.product_id = a.id WHERE a.id = ? GROUP by a.id',[$id,$id]);
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


    public function update_all_inventory_status($inventid,$statusid){
        $result = DB::table('inventory_general')->whereIn('id', $inventid)->update(['status'=> $statusid]);
         if($statusid == 2){
              DB::table('website_products')->whereIn('inventory_id', $inventid)->update(['status'=>0]);
         }
        return $result;
    }

	public function delete_all_inventory($inventid){
		DB::table('inventory_qty_reminders')->whereIn('inventory_id', $inventid)->delete();
	   	DB::table('inventory_price')->whereIn('product_id', $inventid)->delete();
	   	DB::table('website_products')->whereIn('inventory_id', $inventid)->delete();

        $getImageName = DB::table('inventory_general')->whereIn('id', $inventid)->pluck('image');

        if($getImageName != null){
            foreach($getImageName as $val){
                if(in_array(session('company_id'),[95, 102, 104]) && !empty($val)){
                       Cloudinary::destroy($val);
                }else{
                    if(File::exists('/images/products/'.$val)){
                        File::delete('/images/products/'.$val);
                    }
                }
            }
        }

        $getImageGallery = DB::table('inventory_images')->whereIn('item_id', $inventid)->pluck('image');
        if($getImageGallery != null){
            foreach($getImageGallery as $val){
                if(in_array(session('company_id'),[95, 102, 104])){
                       Cloudinary::destroy($val);
                       if(File::exists('/images/products/').$val){
                        File::delete('/images/products/'.$val);
                       }
                }else{
                    if(File::exists('/images/products/').$val){
                        File::delete('/images/products/'.$val);
                    }
                }
            }
        }

        $getVideo = DB::table('inventory_video')->whereIn('inventory_id', $inventid)->pluck('file');
        if($getImageGallery != null){
            foreach($getImageGallery as $val){
                if(in_array(session('company_id'),[95, 102, 104])){
                       Cloudinary::destroy($val);
                }else{
                    if(File::exists('storage/video/products/'.$val)){
                        File::delete('storage/video/products/'.$val);
                    }
                }
            }
        }

	    $result = DB::table('inventory_general')->whereIn('id', $inventid)->delete();
        return $result;
    }

    public function update_department($inventid,$deptID)
    {
        $result = DB::table('inventory_general')->whereIn('id', $inventid)->update(['department_id'=> $deptID]);
        return $result;
    }

    public function update_sub_department($inventid,$subdeptID,$deptID)
    {
        $result = DB::table('inventory_general')->whereIn('id', $inventid)->update(['sub_department_id'=> $subdeptID,'department_id' => $deptID]);
        return $result;
    }

    public function update_uom($inventid,$uomID)
    {
        $result = DB::table('inventory_general')->whereIn('id', $inventid)->update(['uom_id'=> $uomID]);
        return $result;
    }

    public function get_departments()
    {
        $result = DB::table('inventory_department')->where('company_id', session("company_id"))->where('status', 1)->get();
        return $result;
    }

    public function get_sub_departments($id)
    {
        $result = DB::select('SELECT a.* FROM inventory_sub_department a Inner JOIN inventory_department b on b.department_id = a.department_id and b.company_id = ? where a.department_id = ?',[session("company_id"),$id]);
        return $result;
    }

    public function get_uom()
    {
        $result = DB::table('inventory_uom')->get();
        return $result;
    }

	public function get_taxes()
    {
        $result = DB::select('SELECT tax_rate FROM `inventory_price` where product_id IN (SELECT id FROM `inventory_general` where company_id = ? and status = 1) and tax_rate IS NOT NULL and tax_rate != 0.00 and status_id = 1 group by tax_rate',[session('company_id')]);
        return $result;
    }

	public function get_all_product_taxes($taxrate)
    {
        $result = DB::select('SELECT * FROM `inventory_price` where product_id IN (SELECT id FROM `inventory_general` where company_id = ? and status = 1) and tax_rate IS NOT NULL and tax_rate != 0.00 and status_id = 1 and tax_rate = ?',[session('company_id'),$taxrate]);
        return $result;
    }

	public function update_single_product_tax($priceId,$taxrate,$taxamount,$retail)
    {
        $result = DB::table('inventory_price')->where('price_id', $priceId)->update(['tax_rate'=> $taxrate,'tax_amount'=> $taxamount,"retail_price" => $retail]);
        return $result;
    }

    public function item_name($id)
    {
        $result = DB::table('inventory_general')->whereIn('id', $id)->get();
        return $result;
    }

    public function getDeptandSubDept()
    {
        $result = DB::select('SELECT MAX(a.department_id) as departID,b.sub_department_id FROM inventory_department a INNER JOIN inventory_sub_department b on b.department_id = a.department_id and b.department_id = a.department_id where a.company_id = ?',[session('company_id')]);
        return $result;
    }

    public function stock_report($items)
    {
        $result = DB::table('inventory_stock_report_table')->insert($items);
        return $result;
    }

    public function insert_pram($table,$items){
        $result = DB::table($table)->insertGetId($items);
        return $result;
    }

    public function updateprice($id,$items){

        $result = DB::table('inventory_price')->where('price_id',$id)->update($items);
        return $result;

    }

    public function getpricebyproduct($productid){
        $result = DB::select('SELECT * FROM inventory_price WHERE product_id = ? AND status_id = 1',[$productid]);
        return $result;
    }


    public function getImages($id)
    {
        $id = DB::table("inventory_general")->where("slug",$id)->get("id");
        $result = DB::table("inventory_images")->where("item_id",$id[0]->id)->get();
        return $result;
    }

    public function getReferences($id)
    {
        $id = DB::table("inventory_general")->where("slug",$id)->get("id");
        $result = DB::table("inventory_reference")->where("product_id",$id[0]->id)->get();
        return $result;
    }

    public function getstock_value($productid,$branch){
        // $result = DB::select('SELECT SUM(a.balance) as stock, b.actual_price as cost_price FROM inventory_stock a INNER JOIN inventory_price b on b.product_id = a.product_id and b.status_id = 1 WHERE a.product_id = ? ',[$productid]); //AND a.status_id = 1
        $result = DB::select('SELECT SUM(a.balance) as stock, a.cost_price as cost_price FROM inventory_stock a WHERE a.product_id = ?  and a.status_id = 1 and a.branch_id = ?',[$productid,$branch]); //AND a.status_id = 1
        return $result;
    }

    public function getgrns($productid){
        $result = DB::select('SELECT a.stock_id, a.grn_id, b.product_name, a.balance,DATE(a.date) as date,TIME(a.date) as time FROM inventory_stock a INNER JOIN inventory_general b ON b.id = a.product_id  WHERE a.product_id = ? AND a.grn_id IN (SELECT grn_id FROM inventory_stock WHERE product_id = ? AND status_id = 1)',[$productid,$productid]);
        return $result;
    }


    public function getbalance($stockid){
        $result = DB::select('SELECT * FROM inventory_stock WHERE stock_id = ?',[$stockid]);
        return $result;
    }

    public function update_balance_stock($id,$items){
        $result = DB::table('inventory_stock')->where('stock_id', $id)->update($items);
        return $result;
    }

    public function getproductdetails($productid){
        $result = DB::select('SELECT * FROM inventory_general WHERE id = ?',[$productid]);
        return $result;
    }


    public function getbalance_byproduct($productid){
        $result = DB::select('SELECT SUM(balance) as stock FROM inventory_stock WHERE product_id = ? AND status_id = 1',[$productid]);
        return $result;
    }

    public function getStockInventory($id)
    {
        $result = DB::select("SELECT a.id,a.product_name,a.item_code FROM inventory_general a where a.company_id = $id and a.id NOT IN (SELECT a.product_id FROM inventory_stock a inner join inventory_general b on b.id = a.product_id where b.company_id = $id and a.branch_id = ? group by product_id order by a.product_id)",[session("branch")]);
        return $result;
    }

    public function getInventoryListForRetailPriceUpdate($companyID){
        $result = DB::select("SELECT
          inventory_general.id AS productID,
          inventory_general.item_code AS ItemCode,
          inventory_general.product_name AS ItemName,
          inventory_general.product_description AS Description,
          inventory_price.`actual_price` AS actual_price,
          inventory_price.`tax_rate` AS tax_rate,
          inventory_price.`tax_amount` AS tax_amount,
          inventory_price.`retail_price` AS RetailPrice,
          inventory_price.`wholesale_price` AS wholesale_price,
          inventory_price.`online_price` AS online_price,
          inventory_price.`discount_price` AS discount_price
          FROM `inventory_general`
          INNER JOIN `inventory_price` ON `inventory_price`.`product_id` = inventory_general.id
          WHERE company_id = $companyID AND inventory_price.`status_id` = 1 GROUP BY inventory_price.`product_id` ORDER BY inventory_price.`date`,inventory_general.id DESC ");
        return $result;
    }

	public function displayInventory($code,$name,$dept,$sdept,$status)
	{
		$pos = DB::table("pos_products_gen_details")->join("pos_product_price","pos_product_price.pos_item_id","=","pos_products_gen_details.pos_item_id")
				->join("branch","branch.branch_id","=","pos_products_gen_details.branch_id")
				->where("pos_product_price.status_id",1)->where("branch.company_id",session("company_id"))
				->where(function ($query) use ($code,$name,$dept,$sdept,$status) {
					if(!empty($code)){
						$query->where('pos_products_gen_details.item_code', 'like', '%'.$code.'%');
					}
					if(!empty($name)){
						$query->where('pos_products_gen_details.item_name', 'like', '%'.$name.'%');
					}
					if(!empty($status) && $status != ""){
						$query->where('pos_products_gen_details.isPos',($status == 1 ? 1 : 0));
					}
				})
				->select("pos_products_gen_details.product_id","pos_products_gen_details.item_code","pos_products_gen_details.item_name","pos_product_price.retail_price","pos_product_price.wholesale_price","pos_product_price.online_price","pos_product_price.discount_price","pos_products_gen_details.image",DB::raw("'pos' as status"),"pos_products_gen_details.pos_item_id as pos_item_id","pos_products_gen_details.isPos","pos_products_gen_details.isOnline","pos_products_gen_details.isHide");

		$main = DB::table("inventory_general")
					->join("inventory_price","inventory_price.product_id","=","inventory_general.id")
					->select("inventory_general.id as product_id","inventory_general.item_code","inventory_general.product_name","inventory_price.retail_price","inventory_price.wholesale_price","inventory_price.online_price","inventory_price.discount_price","inventory_general.image",DB::raw("'inventory' as status"),DB::raw("'pos_item_id' as pos_item_id"),"inventory_general.isPos","inventory_general.isOnline","inventory_general.isHide")
					->where("inventory_price.status_id",1)->where("inventory_general.company_id",session("company_id"))
					->where(function ($query) use ($code,$name,$dept,$sdept,$status) {
						if(!empty($code)){
							$query->where('inventory_general.item_code', 'like', '%'.$code.'%');
						}
						if(!empty($name)){
							$query->where('inventory_general.product_name', 'like', '%'.$name.'%');
						}
						if(!empty($dept)){
							$query->where('inventory_general.department_id', $dept);
						}
						if(!empty($sdept)){
							$query->where('inventory_general.sub_department_id',$sdept);
						}
						if(!empty($status) && $status != ""){
							$query->where('inventory_general.isPos',($status == 1 ? 1 : 0));
						}
					})
					->union($pos)->orderBy('product_id', 'asc')->orderBy('status', 'asc')
					->paginate(20);

		return $main;

	}

    public function getPreviousImage($id){
        $result = DB::table('inventory_general')
                    ->where('company_id',session('company_id'))
                    ->where('id',$id)
                    ->first();
        return $result != null ? $result->image : null;
    }

}
