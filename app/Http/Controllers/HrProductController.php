<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use flash,Auth,Session;

class HrProductController extends Controller
{
	public function index()
	{
		$lists = DB::table("hr_products")
				->join("hr_product_price","hr_product_price.hr_product_id","=","hr_products.id")
				->where("hr_products.status",1)
				->where("hr_product_price.status",1)
				->where("hr_products.company_id",session("company_id"))
				->select("hr_products.id","hr_products.name","hr_product_price.price","hr_products.status")
				->get();
		return view("hrproducts.index",compact('lists'));
	}
	
	public function store(Request $request)
	{
		if($request->product != "" && $request->price != ""){
			$count = DB::table("hr_products")->where("name",$request->product)->where("company_id",session("company_id"))->where("status",1)->count();
			if($count == 0){
				try{
					DB::beginTransaction();
					$product = DB::table("hr_products")->insertGetId([
						"company_id" => session("company_id"),
						"name" => $request->product,
					]);
					DB::table("hr_product_price")->insertGetId([
						"hr_product_id" => $product,
						"price" => $request->price,
					]);
					DB::commit();
				}catch(Exception $e){
					DB::rollback();
				}
				return response()->json(["status" => 200,"message"=> "Record inserted successfully."]);
			}else{
				return response()->json(["status" => 500,"message"=> "Record already exists."]);
			}
		}else{
			return response()->json(["status" => 500,"message"=> "Product or Price is null."]);
		}
	}
	
	public function update(Request $request)
	{
		if($request->mode == "changename" ){
			try{
				DB::beginTransaction();
				DB::table("hr_products")->where("id",$request->id)->update([
					"name" => $request->product,
				]);
				DB::commit();
				return response()->json(["status" => 200,"message"=> "Record inserted successfully."]);
			}catch(Exception $e){
				DB::rollback();
				return response()->json(["status" => 500,"message"=> "Record already exists.","error" => $e->getMessage() ]);
			}
		}
		
		if($request->mode == "changeprice" ){
			try{
				DB::beginTransaction();
				DB::table("hr_product_price")->where("hr_product_id",$request->id)->update(["status"=> 0]);
				DB::table("hr_product_price")->insertGetId([
					"hr_product_id" => $request->id,
					"price" => $request->price,
				]);
				DB::commit();
				return response()->json(["status" => 200,"message"=> "Record inserted successfully."]);
			}catch(Exception $e){
				DB::rollback();
				return response()->json(["status" => 500,"message"=> "Record already exists."]);
			}
		}
	}
	
	public function delete(Request $request)
	{
		if($request->id != ""){
			$count = DB::table("hr_products")->where("id",$request->id)->where("status",1)->count();
			if($count > 0){
				DB::table("hr_products")->where("id",$request->id)->update([
					"status" => 0,
				]);
				DB::table("hr_product_price")->where("hr_product_id",$request->id)->update([
					"status" => 0,
				]);
				return response()->json(["status" => 200,"message"=> "Record deleted successfully."]);
			}else{
				return response()->json(["status" => 500,"message"=> "Record does not exists."]);
			}
		}else{
			return response()->json(["status" => 500,"message"=> "Id is not null."]);
		}
	}
	
	public function getProducts(Request $request)
	{
		$results =   DB::table("hr_products")
					->join("hr_product_price","hr_product_price.hr_product_id","=","hr_products.id")
					->where("hr_products.status",$request->status)
					->where("hr_product_price.status",$request->status)
					->where("hr_products.company_id",session("company_id"))
					->select("hr_products.id","hr_products.name","hr_product_price.price","hr_products.status")
					->get();
		return $results;
	}
	
	public function getDailyEmployeeTask()
	{
		$lists = DB::table("hr_products")
				->join("hr_product_price","hr_product_price.hr_product_id","=","hr_products.id")
				->where("hr_products.status",1)
				->where("hr_product_price.status",1)
				->where("hr_products.company_id",session("company_id"))
				->select("hr_products.id","hr_products.name","hr_product_price.price","hr_products.status")
				->get();
		$employees = DB::table("employee_details")
					 ->join("employee_shift_details","employee_shift_details.emp_id","=","employee_details.empid")
					 ->where("employee_shift_details.designation_id",50)
					 ->whereIn("employee_shift_details.branch_id",DB::table('branch')->where('company_id',session('company_id'))->pluck('branch_id'))
					 ->get();
		return view("hrproducts.dailytask",compact('lists','employees'));
	}
	
	public function perpcsSalary(Request $request)
	{
		// return $request;
		DB::beginTransaction();
		try{
			if(count($request->dailysalary) > 0){
				for($i=0; $i< count($request->employee); $i++ ){
					$empid = $request->employee[$i];
					$date = date("Y-m-d",strtotime($request->date));

					for($j=0; $j< count($request->product[$empid]); $j++){
						$productId = $request->product[$empid][$j];
						$quantity = $request->quantity[$empid][$j];
						$price = $request->price[$empid][$j];
						
						$count = DB::table("hr_emp_daily_product_record")->where(["date" => $date,"emp_id" => $empid,"product_id" => $productId])->count();
						if($count == 0){
							DB::table("hr_emp_daily_product_record")->insertGetId([
								"emp_id" => $empid,
								"date" => $date,
								"product_id" => $productId,
								"price" => $price,
								"quantity" => $quantity,
							]);
						}
					}
					
					$count = DB::table("advance_salary")->where(["date" => $date,"emp_id" => $empid])->count();
					if($count == 0){
						DB::table("advance_salary")->insertGetId([
							"amount" => $request->dailysalary[$i],
							"date" => $date,
							"reason" => "(Per Pcs) Advance Salary",
							"emp_id" => $empid,
							"status_id" => 1,
						]);
					}
				}
			}
			DB::commit();
			return redirect("get-emp-per-pcs");
		}catch(Exception $e){
			DB::rollback();
			return redirect("get-emp-per-pcs");
		}
		
	}
	
	
	public function getSteamPressProducts()
	{
		$lists = DB::table("hr_steam_press_products")
				->where("hr_steam_press_products.status",1)
				->where("hr_steam_press_products.company_id",session("company_id"))
				->select("hr_steam_press_products.id","hr_steam_press_products.name","hr_steam_press_products.status")
				->get();
		return view("hrproducts.steamproduct",compact('lists'));
	}
	
	public function steamProductStore(Request $request)
	{
		if($request->product != ""){
			$count = DB::table("hr_steam_press_products")->where("name",$request->product)->where("company_id",session("company_id"))->where("status",1)->count();
			if($count == 0){
				try{
					DB::beginTransaction();
					$product = DB::table("hr_steam_press_products")->insertGetId([
						"company_id" => session("company_id"),
						"name" => $request->product,
					]);
					DB::commit();
				}catch(Exception $e){
					DB::rollback();
				}
				return response()->json(["status" => 200,"message"=> "Record inserted successfully."]);
			}else{
				return response()->json(["status" => 500,"message"=> "Record already exists."]);
			}
		}else{
			return response()->json(["status" => 500,"message"=> "Product is null."]);
		}
	}
	
	public function steamProductUpdate(Request $request)
	{
		if($request->mode == "changename" ){
			try{
				DB::beginTransaction();
				DB::table("hr_steam_press_products")->where("id",$request->id)->update([
					"name" => $request->product,
				]);
				DB::commit();
				return response()->json(["status" => 200,"message"=> "Record inserted successfully."]);
			}catch(Exception $e){
				DB::rollback();
				return response()->json(["status" => 500,"message"=> "Record already exists.","error" => $e->getMessage() ]);
			}
		}
	}
	
	public function steamProductDelete(Request $request)
	{
		if($request->id != ""){
			$count = DB::table("hr_steam_press_products")->where("id",$request->id)->where("status",1)->count();
			if($count > 0){
				DB::table("hr_steam_press_products")->where("id",$request->id)->update([
					"status" => 0,
				]);
				return response()->json(["status" => 200,"message"=> "Record deleted successfully."]);
			}else{
				return response()->json(["status" => 500,"message"=> "Record does not exists."]);
			}
		}else{
			return response()->json(["status" => 500,"message"=> "Id is not null."]);
		}
	}
	
	public function getDailySteamEmployeeTask()
	{
		$lists = DB::table("hr_steam_press_products")
				->where("hr_steam_press_products.status",1)
				->where("hr_steam_press_products.company_id",session("company_id"))
				->select("hr_steam_press_products.id","hr_steam_press_products.name","hr_steam_press_products.status")
				->get();
		$employees = DB::table("employee_details")
					 ->join("employee_shift_details","employee_shift_details.emp_id","=","employee_details.empid")
					 ->join("increment_details","increment_details.emp_id","=","employee_details.empid")
					 ->where("employee_shift_details.designation_id",50)
					 ->whereIn("employee_shift_details.branch_id",DB::table('branch')->where('company_id',session('company_id'))->pluck('branch_id'))
					 ->get();
		$perpiecerate =  DB::table("hr_steam_cotton_price")->where(["name" => "Steam","status" => 1])->get();
		$perpiecerate =  (!empty($perpiecerate) ? $perpiecerate[0]->price : 0);
		return view("hrproducts.steamdailytask",compact('lists','employees','perpiecerate'));
	}
	
	public function getDailyCottonEmployeeTask()
	{
		$lists = DB::table("hr_steam_press_products")
				->where("hr_steam_press_products.status",1)
				->where("hr_steam_press_products.company_id",session("company_id"))
				->select("hr_steam_press_products.id","hr_steam_press_products.name","hr_steam_press_products.status")
				->get();
		$employees = DB::table("employee_details")
					 ->join("employee_shift_details","employee_shift_details.emp_id","=","employee_details.empid")
					 ->join("increment_details","increment_details.emp_id","=","employee_details.empid")
					 ->where("employee_shift_details.designation_id",50)
					 ->whereIn("employee_shift_details.branch_id",DB::table('branch')->where('company_id',session('company_id'))->pluck('branch_id'))
					 ->get();
		$perpiecerate =  DB::table("hr_steam_cotton_price")->where(["name" => "Cotton","status" => 1])->get();
		$perpiecerate =  (!empty($perpiecerate) ? $perpiecerate[0]->price : 0);
		return view("hrproducts.cottondailytask",compact('lists','employees','perpiecerate'));
	}
}