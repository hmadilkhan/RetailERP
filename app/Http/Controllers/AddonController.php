<?php

namespace App\Http\Controllers;

use App\Addon;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("addons.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		try{
			$count = Addon::where("name",$request->name)->where("addon_category_id",$request->addon_category_id)->count();
			if($count == 0){
				Addon::create([
					"name" => $request->addon_name,
					"price" => $request->addon_price,
					"addon_category_id" => $request->addon_category_id,
					"user_id" => auth()->user()->id,
				]);
				return response()->json(["status" => 200,"contrl" => "name"]);
			}else{
				return response()->json(["status" => 500,"contrl" => "name","msg" => "Addon created successfully"]);
			}
		}catch(Exception $e){
			return response()->json(["status" => 500,"contrl" => "name","msg" => "Error : ".$e->getMessage() ]);
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Addon  $addon
     * @return \Illuminate\Http\Response
     */
    public function show(Addon $addon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Addon  $addon
     * @return \Illuminate\Http\Response
     */
    public function edit(Addon $addon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Addon  $addon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Addon $addon)
    {
        try{
			$count = Addon::where("name",$request->name)->where("addon_category_id",$request->category)->where("id","!=",$request->id)->count();
			if($count == 0){
				Addon::where("id",$request->id)->update(
				[
					"name" => $request->name,
					"price" => $request->price,
					"user_id" => auth()->user()->id,
				]);
				return response()->json(["status" => 200,"contrl" => "name"]);
			}else{
				return response()->json(["status" => 500,"contrl" => "name","msg" => "Addon updated successfully"]);
			}
		}catch(Exception $e){
			return response()->json(["status" => 500,"contrl" => "name","msg" => "Error : ".$e->getMessage() ]);
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Addon  $addon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try{
			Addon::where("id",$request->id)->delete();
			return response()->json(["status" => 200,"contrl" => "name"]);
		}catch(Exception $e){
			return response()->json(["status" => 500,"contrl" => "name","msg" => "Error : ".$e->getMessage() ]);
		}
    }
}
