<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceProviderOrdersResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderLogs;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\OrderStatus;
use App\Models\OrderAssign;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceProviderLedger;
use App\Models\ServiceProviderOrders;


class ServiceProviderOrderController extends Controller
{
	public function index()
	{
		$from = date("Y-m-d");
		$to = date("Y-m-d");
		$status = OrderStatus::orderBy('sortBy','ASC')->get();
		$providers =  ServiceProvider::Branch(session("branch"))->Active()->select(["id","provider_name"])->get();
		$drivers = Driver::where("branch_id",session("branch"))->where("status",1)->get();
		$vehicles = Vehicle::where("branch_id",session("branch"))->where("status",1)->get();
		$loaders = DB::table("user_authorization")
					->join("user_details","user_details.id","=","user_authorization.user_id")
					->where("user_authorization.branch_id",session("branch"))->where("user_authorization.role_id",12)
					->select("id","fullname")
					->get();
		$checkers = DB::table("user_authorization")
					->join("user_details","user_details.id","=","user_authorization.user_id")
					->where("user_authorization.branch_id",session("branch"))->where("user_authorization.role_id",13)
					->select("id","fullname")
					->get();
		return view('order.service-providers-orders',compact("providers","status","drivers","vehicles","loaders","checkers"));
	}
	
	public function getServiceProviderOrders(Request $request)
	{
		$from = date("Y-m-d");
		$to = date("Y-m-d");
	
		if($request->from != "" && $request->to != "")
		{
			$from = $request->from;
			$to = $request->to;
		}
		
		if($request->receipt != "")
		{
			$order = Order::where("receipt_no",$request->receipt)->first("id");
		}
		//,"driver:id,name,mobile","loader:id,fullname"
		$data = ServiceProviderOrders::with("serviceprovidersorders:id,receipt_no,customer_id,total_amount,total_item_qty,status,date,time","serviceprovider:id,provider_name","serviceprovidersorders.customer:id,name","serviceprovidersorders.orderStatus:order_status_id,order_status_name","serviceprovidersorders.orderassign:receipt_id,driver_id,created_at","serviceprovidersorders.orderassign.driver:id,name")
				->whereHas('serviceprovidersorders', function($q) use ($request) {
					$q->where("branch",session("branch"));
					$q->when($request->receipt != "", function ($q) use ($request) {
						return $q->where("receipt_id",[$request->receipt]);
					});
					$q->when($request->status != "", function ($q) use ($request) {
						return $q->where("status",[$request->status]);
					});
				})
				->when($request->status != 1 , function ($q) use ($from,$to) { //&& $request->status != 1
					return $q->whereBetween(DB::raw('DATE(date)'),[$from,$to]);
				})
				->when($request->serviceprovider != "", function ($q) use ($request) {
					return $q->where("service_provider_id",[$request->serviceprovider]);
				})
				->orderBy("receipt_id","DESC")
				->get(); 
			// return $data;
		$providers =  ServiceProvider::Branch(session("branch"))->Active()->select(["id","provider_name"])->get();
		$status = OrderStatus::all();

		return view("partials.service-provider-orders-table",compact("data","providers","status"));
	}
	
	public function updateServiceProvider(Request $request)
	{
		if($request->id != "" && $request->serviceprovider != "")
		{
			try{
				$serviceprovider = ServiceProviderOrders::find($request->id);
				$serviceprovider->service_provider_id = $request->serviceprovider;
				$serviceprovider->save();
				
				$ledger = ServiceProviderLedger::where("provider_id",$request->oldserviceprovider)->where("receipt_id",$request->receipt)->first();
				ServiceProviderLedger::where("ladger_id",$ledger->ladger_id)->update(["provider_id" => $request->serviceprovider ]);
				
				return response()->json(["success" => 1]);
			}catch(Exception $e){
				return response()->json(["success" => 0]);
			}
		}			
	}
	
	public function updateOrderStatus(Request $request)
	{
		try{
			
			$order = Order::find($request->receipt);
			$order->status = $request->status;
			$order->save();
			$orderlogs = OrderLogs::create([
				"order_id" => $request->receipt,
				"status_id" => 6,
				"date" => date("Y-m-d H:i:s"),
			]);
			return response()->json(["success" => 1]);
		}catch(Exception $e){
			return response()->json(["success" => 0]);
		}
	}
	
	public function AssignOrders(Request $request)
	{
		
		try{
			foreach($request->orders as $order)
			{
				OrderAssign::updateOrCreate(
					 ['receipt_id' => $order],
					 [
						"driver_id" => $request->driver,
						"Vehicle_id" => $request->vehicle,
						"loader_id" => $request->loader,
						"checker_id" => $request->checker,
					 ]
				);
				
				$orderlogs = OrderLogs::create([
						"order_id" => $order,
						"status_id" => 6,
						"date" => date("Y-m-d H:i:s"),
				]);
				
				$order = Order::findOrFail($order);
				$order->status = 6;
				$order->save();
				
			}
			return response()->json(["status" => 200,"message" => "Order Assigned Successfully"]);
		}catch(Exception $e){
			return response()->json(["status" => 500,"message" => "Some Error Occurred"]);
		}
	}
	
	public function getDriverOrders(Request $request)
	{
		$from = date("Y-m-d");
		$to = date("Y-m-d");

		if($request->from != "" && $request->to != "")
		{
			$from = $request->from;
			$to = $request->to;
		}
		$driverOrders = OrderAssign::with("driver:id,name,mobile","loader:id,fullname","checker:id,fullname","order","vehicles:id,model_name,number")
						->whereHas('order', function($q) {
								$q->where("branch",session("branch"));
						})
						->whereDate("created_at",[$from,$to])
						->select("id","receipt_id","driver_id","loader_id","vehicle_id","checker_id","created_at",DB::raw("count('receipt_id') as orders"))
						->groupBy("driver_id",DB::raw("TIME(created_at)"))
						->get();	
		return view("partials.driverAssignOrders.table",compact("driverOrders"));
	}
	
	public function getDriversItems(Request $request)
	{	
		$from = date("Y-m-d");
		$to = date("Y-m-d");

		if($request->from != "" && $request->to != "")
		{
			$from = $request->from;
			$to = $request->to;
		}
		$driverOrdersDetails = OrderAssign::with("order:id,receipt_no,total_amount,total_item_qty,customer_id,status,payment_status","order.orderdetails:receipt_detail_id,receipt_id,item_code,item_name,item_price,total_qty,total_amount,status,narration","order.orderdetails.inventory:id,product_name","order.customer:id,name,mobile,address")
							->whereHas('order', function($q) {
								$q->where("branch",session("branch"));
							})
							->whereBetween(DB::raw('DATE(created_at)'),[$from,$to])
							->where("driver_id",$request->driverId)
							->where(DB::raw('TIME(created_at)'),date("H:i:s",strtotime($request->time)))
							->get();
		$orderStatus = OrderStatus::orderBy('sortBy','ASC')->get();
		return view("partials.driverAssignOrders.detailsTable",compact("driverOrdersDetails","orderStatus"));
	}
	
	function saveNarration(Request $request)
	{
		try{
			if($request->narration != ""){
				$order = OrderDetails::where("receipt_detail_id",$request->receipt)->update(["narration" => $request->narration]);
				$message = "Narration saved Successfully";
			}else if($request->paymentStatus != ""){
				$order = Order::where("id",$request->receipt)->update(["payment_status" => $request->paymentStatus]);
				$message = "Payment Status changed Successfully";
			}else if($request->mainStatus != ""){
				Order::where("id",$request->receipt)->update(["status" => $request->mainStatus]);
				$message = "Receipt changed Successfully";
			}
			
			return response()->json(["status" => 200,"message" => $message ]);
		}catch(Exception $e){
			return response()->json(["status" => 500,"message" => "Some Error Occurred"]);
		}
		
	}
}