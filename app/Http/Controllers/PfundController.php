<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\PfFund;

class PfundController extends Controller
{
	public function index(PfFund $pffund)
	{
		$pffunds = $pffund->getFunds(1);
		return view("pffund.index",compact('pffunds'));
	}
	
	public function store(Request $request)
	{
		if($request->rate != ""){
			$count = DB::table("hr_pf_fund")->where("rate",$request->rate)->where("company_id",session("company_id"))->where("status",1)->count();
			if($count == 0){
				DB::table("hr_pf_fund")
				->where("status",1)
				->where("company_id",session("company_id"))
				->update([
					"status" => 0,
					"updated_at" => date("Y-m-d H:i:s"),
				]);
				DB::table("hr_pf_fund")->insert([
					"company_id" => session("company_id"),
					"rate" => $request->rate,
				]);
				return response()->json(["status" => 200,"message"=> "Record inserted successfully."]);
			}else{
				return response()->json(["status" => 500,"message"=> "Record already exists."]);
			}
		}else{
			return response()->json(["status" => 500,"message"=> "Rate is null."]);
		}
	}
	
	public function getFunds(Request $request,PfFund $pffund)
	{
		$pffunds = $pffund->getFunds($request->status);
		return $pffunds;
	}
}