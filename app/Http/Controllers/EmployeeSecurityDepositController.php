<?php

namespace App\Http\Controllers;

use App\branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeSecurityDepositController extends Controller
{
	public function index(Request $request)
	{
		$branches = DB::table("branch")->where("company_id",session("company_id"))->get();
		$deposits = DB::table("hr_branch_security_deposit")->join("branch","hr_branch_security_deposit.branch_id","=","branch.branch_id")->where("hr_branch_security_deposit.company_id",session("company_id"))->where("status",1)->get();
		$deposit = ($request->id != "" ? DB::table("hr_branch_security_deposit")->where("id",$request->id)->get() : "");

		return view("securitydeposit.index",compact("branches","deposits","deposit"));
	}
	
	public function store(Request $request)
	{

		$rules = [
			'branch' => 'required',
			'employee_security_deposit' => 'required',
			'monthly_deduction' => 'required',
		];
		$this->validate($request, $rules);
		try{
			$count = DB::table("hr_branch_security_deposit")->where("branch_id",$request->branch)->where("total_limit",$request->employee_security_deposit)->count();
			if($count == 0){
				DB::table("hr_branch_security_deposit")->insert([
					'company_id' => session("company_id"),
					'branch_id' => $request->branch,
					'total_limit' => $request->employee_security_deposit,
					'monthly_deduction' => $request->monthly_deduction,
				]);
				// return redirect("view-security-deposit");
				return response()->json(["status" => 200,"message" => "Data inserted Successfully"]);
			}else{
				// return 2;//redirect("view-security-deposit");
				return response()->json(["status" => 201,"message" => "Data already exists."]);
			}
		}catch(Exception $e){
			return $e->getMessage();
		}
		
	}
	
	public function update(Request $request)
	{
		$rules = [
			'branch' => 'required',
			'employee_security_deposit' => 'required',
			'monthly_deduction' => 'required',
		];
		$this->validate($request, $rules);
		
		DB::table("hr_branch_security_deposit")->where("id",$request->id)->update([
			'branch_id' => $request->branch,
			'total_limit' => $request->employee_security_deposit,
			'monthly_deduction' => $request->monthly_deduction,
		]);
		return response()->json(["status" => 200,"message" => "Data updated Successfully"]);
	}
	
	public function delete(Request $request)
	{
		if($request->id != ""){
			DB::table("hr_branch_security_deposit")->where("id",$request->id)
			->update([
				'status' => 0,
			]);
			return response()->json(["status" => 200,"message" => "Data deleted successfully"]);
		}else{
			return response()->json(["status" => 500,"message" => "Some Error Occurred"]);
		}
	}
}