<?php

namespace App\Http\Controllers;

use App\branch;
use App\Traits\MediaTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Image;


class BranchController extends Controller
{
	use MediaTrait;

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function show(branch $branch)
	{
		$details = $branch->get_branches(session('company_id'));
		return view('Branch.list', compact('details'));
	}

	public function index(branch $branch)
	{
		$country = $branch->getcountry();
		$company = $branch->getCompany();
		$city = $branch->getcity();
		$reports = DB::table("reports")->where("status", 1)->get();
		return view('Branch.create', compact('country', 'city', 'company', 'reports'));
	}

	public function store(branch $branch, Request $request)
	{
		$imageName = "";
		$file = "";
		$company = "";
		$company = $request->company;

		$check = $branch->exist($request->branchname, $company);

		if ($check[0]->counter == 0) {

			if (!empty($request->vdimg)) {
				$request->validate([
					'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
				]);
				$file = $this->uploads($request->vdimg, 'images/branch/');
			}

			$rules = [
				'branchname' => 'required',
				'br_address' => 'required',
				'br_ptcl' => 'required',
				'br_mobile' => 'required',
				'br_email' => 'required',
			];
			$this->validate($request, $rules);

			$items = [
				'company_id' => $company,
				'country_id' => $request->country,
				'city_id' => $request->city,
				'status_id' => 1,
				'branch_name' => $request->branchname,
				'branch_address' => $request->br_address,
				'branch_latitude' => null,
				'branch_longitude' => null,
				'branch_ptcl' => $request->br_ptcl,
				'branch_mobile' => $request->br_mobile,
				'branch_email' => $request->br_email,
				'code' => $request->br_code,
				'branch_logo' => $file["fileName"],
				'modify_by' => session('userid'),
				'modify_date' => date('Y-m-d'),
				'modify_time' => date('H:i:s'),
				'date' => date('Y-m-d'),
				'time' => date('H:i:s'),
			];
			$branch = $branch->insert_branch($items);

			if (count($request->report) > 0) {
				foreach ($request->report as $report) {
					DB::table("branch_reports")->insert([
						"branch_id" => $branch,
						"report_id" => $report,
					]);
				}
			}

			return 1;
		} else {
			return 0;
		}
	}

	public function remove(branch $branch, Request $request)
	{

		$result = $branch->branch_remove($request->id);
		$details = $branch->branch_details(session('company_id'), $request->id);
		DB::table("branch_emails")->where("branch_id", $request->id)->update([
			"status" => 0,
			"updated_at" => date("Y-m-d H:i:s"),
		]);

		DB::table("branch_reports")->where("branch_id", $request->id)->update([
			"status" => 0,
			"updated_at" => date("Y-m-d H:i:s"),
		]);
		$this->removeImage("images/branch/", $details[0]->branch_logo);

		return 1;
	}

	public function edit(branch $branch, Request $request)
	{
		$country = $branch->getcountry();
		$city = $branch->getcity();
		$company = $branch->getCompany();
		$details = $branch->branch_details(session('company_id'), Crypt::decrypt($request->id));
		$reports = DB::table("reports")->where("status", 1)->get();
		$branchreports = DB::table("branch_reports")->where("branch_id", Crypt::decrypt($request->id))->where("status", 1)->pluck("report_id");
		return view('Branch.edit', compact('country', 'city', 'details', 'company', 'reports', 'branchreports'));
	}


	public function update(branch $branch, Request $request)
	{
		$imageName = "";

		if (!empty($request->vdimg)) {
			$request->validate([
				'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
			]);
			$file = $this->uploads($request->vdimg, 'images/branch/', $request->br_old_image);
		} else {
			$imageName = $request->imagename;
		}
		$items = [
			'company_id' => $request->company,
			'country_id' => $request->country,
			'city_id' => $request->city,
			'status_id' => 1,
			'branch_name' => $request->branchname,
			'branch_address' => $request->br_address,
			'branch_latitude' => null,
			'branch_longitude' => null,
			'branch_ptcl' => $request->br_ptcl,
			'branch_mobile' => $request->br_mobile,
			'branch_email' => $request->br_email,
			'branch_logo' => !empty($request->vdimg) ? $file["fileName"] :  $imageName,
			'modify_by' => session('userid'),
			'modify_date' => date('Y-m-d'),
			'modify_time' => date('H:i:s'),
			'code' => $request->br_code,
		];
		$branch = $branch->branch_update($request->br_id, $items);

		if (!empty($request->reportlist) && count($request->reportlist) > 0) {
			DB::table("branch_reports")->where("branch_id", $request->br_id)->delete();
			foreach ($request->reportlist as $report) {
				DB::table("branch_reports")->insert([
					"branch_id" => $request->br_id,
					"report_id" => $report,
				]);
			}
		}

		return 1;
	}

	public function getEmail(Request $request)
	{
		$branchId = $request->id;
		$emails = DB::table("branch_emails")->where("branch_id", Crypt::decrypt($request->id))->where("status", 1)->get();
		return view('Branch.emails', compact('emails', 'branchId'));
	}

	public function saveEmail(Request $request)
	{
		$request->validate([
			'name' => 'required',
			'email' => 'required',
		]);
		if ($request->mode == "insert") {
			$count = DB::table("branch_emails")->where("branch_id", $request->branch_id)->where("name", $request->name)->where("email", $request->email)->where("status", 1)->count();
			if ($count == 0) {
				DB::table("branch_emails")->insert([
					'branch_id' => $request->branch_id,
					'name' => $request->name,
					'email' => $request->email,
				]);
				return redirect("branch-emails/" . Crypt::encrypt($request->branch_id));
			} else {
				return redirect("branch-emails/" . Crypt::encrypt($request->branch_id))->withErrors(['Name' => 'Details aleady exists.']);
			}
		}

		if ($request->mode == "update") {
			$count = DB::table("branch_emails")->where("branch_id", $request->branch_id)->where("name", $request->name)->where("email", $request->email)->where("status", 1)->count();
			if ($count == 0) {
				DB::table("branch_emails")->where("id", $request->email_id)->update([
					'branch_id' => $request->branch_id,
					'name' => $request->name,
					'email' => $request->email,
					'updated_at' => date("Y-m-d H:i:s"),
				]);
				return redirect("branch-emails/" . Crypt::encrypt($request->branch_id));
			} else {
				return redirect("branch-emails/" . Crypt::encrypt($request->branch_id))->withErrors(['Name' => 'Details aleady exists.']);
			}
		}
	}

	public function deleteEmail(Request $request)
	{
		try {
			DB::table("branch_emails")
				->where("id", $request->id)
				->update([
					'status' => 0,
					'updated_at' => date("Y-m-d H:i:s"),
				]);
			return 1;
		} catch (Exception $e) {
			return 0;
		}
	}
}
