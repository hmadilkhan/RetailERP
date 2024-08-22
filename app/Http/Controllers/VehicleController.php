<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
	{
		return view("vehicles.list");
	}

	public function getVehiclesList(Request $request)
	{
		$vehicles = Vehicle::where("branch_id",session("branch"))->where("status",$request->mode)->get();
		$mode = $request->mode;
		return view("partials.vehicles.table",compact("vehicles","mode"));
	}
	
	public function create()
	{
		return view("vehicles.create");
	}
	
	public function store(Request $request)
	{
		Vehicle::create([
			"company_id" => session("company_id"),
			"branch_id" => session("branch"),
			"number" => $request->number,
			"model_name" => $request->model_name,
			"model_no" => $request->model_no,
		]);
		return redirect()->route('vehicle.list');
	}
	
	public function edit(Request $request)
	{
		$vehicle = Vehicle::findOrFail($request->id);
		return view("vehicles.edit",compact("vehicle"));
	}
	
	public function update(Request $request)
	{
		$vehicle = Vehicle::findOrFail($request->id);		
		$vehicle->number = $request->number;
		$vehicle->model_name = $request->model_name;
		$vehicle->model_no = $request->model_no;
		$vehicle->save();
		return redirect()->route('vehicle.list');
	}
	
	public function inactiveOrActive(Request $request)
	{
		$vehicle = Vehicle::findOrFail($request->id);
		$vehicle->status = $request->mode;
		$vehicle->save();
		return 1;
	}
}
