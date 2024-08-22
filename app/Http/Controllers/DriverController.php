<?php

namespace App\Http\Controllers;

use Image;
use App\Models\Driver;
use Illuminate\Http\Request;
use App\Http\Requests\DriverPostRequest;

class DriverController extends Controller
{
    public function index()
	{
		$drivers = Driver::where("branch_id",session("branch"))->where("status",1)->get();
		return view("drivers.list",compact("drivers"));
	}
	
	public function getDriversList(Request $request)
	{
		$drivers = Driver::where("branch_id",session("branch"))->where("status",$request->mode)->get();
		$mode = $request->mode;
		return view("partials.drivers.table",compact("drivers","mode"));
	}
	
	public function create()
	{
		return view("drivers.create");
	}
	
	public function store(DriverPostRequest $request)
	{
		$imageName= "";
        if(!empty($request->logo)){
			$request->validate([
			  'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			]);
	
			$imageName = time().'.'.$request->logo->getClientOriginalExtension();
			$img = Image::make($request->logo)->resize(600, 600);
			$res = $img->save(public_path('assets/images/drivers/'.$imageName), 75);
        }
		Driver::create([
			"company_id" => session("company_id"),
			"branch_id" => session("branch"),
			"name" => $request->name,
			"mobile" => $request->mobile,
			"address" => $request->address,
			"license_no" => $request->license_no,
			"nic_no" => $request->nic_no,
			"image" => $imageName,
		]);
		return redirect()->route('driver.list');
	}
	
	public function edit(Request $request)
	{
		$driver = Driver::findOrFail($request->id);
		return view("drivers.edit",compact("driver"));
	}
	
	public function update(Request $request)
	{
		$driver = Driver::findOrFail($request->id);
		$imageName= "";
        if(!empty($request->logo)){
			$path = public_path('assets/images/drivers/') . $request->prevImage;
            if (file_exists($path)) {
                @unlink($path);
            }
			$request->validate([
			  'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			]);
	
			$imageName = time().'.'.$request->logo->getClientOriginalExtension();
			$img = Image::make($request->logo)->resize(600, 600);
			$res = $img->save(public_path('assets/images/drivers/'.$imageName), 75);
        }
		
		$driver->name = $request->name;
		$driver->mobile = $request->mobile;
		$driver->address = $request->address;
		$driver->license_no = $request->license_no;
		$driver->nic_no = $request->nic_no;
		if(!empty($request->logo)){
			$driver->image = $imageName;
		}
		$driver->save();
		return redirect()->route('driver.list');
	}
	
	public function inactiveOrActive(Request $request)
	{
		$driver = Driver::findOrFail($request->id);
		$driver->status = $request->mode;
		$driver->save();
		return 1;
	}
}
