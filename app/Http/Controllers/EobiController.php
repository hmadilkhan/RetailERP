<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use flash,Auth,Session;
use App\Eobi;

class EobiController extends Controller
{
	public function index(Eobi $eobi)
	{
		$lists = DB::table("eobi")->where("company_id",session("company_id"))->where("status",1)->get();
		return view("eobi.index",compact('lists'));
	}
	
	public function store(Request $request)
	{
		if($request->amount != ""){
			// $count = DB::table("eobi")->where("company_id",session("company_id"))->where("status",1)->count();
			// if($count == 0){
				DB::table("eobi")
				->where("status",1)
				->where("company_id",session("company_id"))
				->update([
					"status" => 0,
					"updated_at" => date("Y-m-d H:i:s"),
				]);
				DB::table("eobi")->insert([
					"company_id" => session("company_id"),
					"amount" => $request->amount,
				]);
				return response()->json(["status" => 200,"message"=> "Record inserted successfully."]);
			// }else{
				// return response()->json(["status" => 500,"message"=> "Record already exists."]);
			// }
		}else{
			return response()->json(["status" => 500,"message"=> "Amount is null."]);
		}
	}
	
	public function getFunds(Request $request)
	{
		$results = DB::table("eobi")->where("company_id",session("company_id"))->where("status",$request->status)->get();
		return $results;
	}
}