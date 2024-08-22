<?php

namespace App\Http\Controllers;

use App\AddonCategory;
use App\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddonCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		// return AddonCategory::with("addons")->where("company_id",session("company_id"))->get();
        return view("addon-category.index",[
			"categories" => AddonCategory::with("addons")->where("company_id",session("company_id"))->get(),
		]);
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
			$count = AddonCategory::where("name",$request->name)->where("company_id",session("company_id"))->count();
			if($count == 0){
				AddonCategory::create([
					"name"               => $request->name,
                    "show_website_name"  => $request->show_website_name,
					"user_id"            => auth()->user()->id,
					"company_id"         => session("company_id"),
					"type"               => $request->type,
                    "is_required"        => $request->is_required,
					"description"        => $request->description,
				]);
				return response()->json(["status" => 200,"contrl" => "name"]);
			}else{
				return response()->json(["status" => 500,"contrl" => "name","msg" => "Addon category created successfully"]);
			}
		}catch(Exception $e){
			return response()->json(["status" => 500,"contrl" => "name","msg" => "Error : ".$e->getMessage() ]);
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AddonCategory  $addonCategory
     * @return \Illuminate\Http\Response
     */
    public function show(AddonCategory $addonCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AddonCategory  $addonCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(AddonCategory $addonCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AddonCategory  $addonCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AddonCategory $addonCategory)
    { 
        try{
			$count = AddonCategory::where("name",$request->name)->where("company_id",session('company_id'))->where("id","!=",$request->id)->count();
			if($count == 0){
				AddonCategory::where("id",$request->id)->update(
				[
					"name"               => $request->name,
                    "show_website_name"  => $request->show_website_addoname,
					"type"               => $request->type,
                    "is_required"        => $request->is_required,
					"description"        => $request->description,
					"addon_limit"        => $request->limit,
					"user_id"            => auth()->user()->id,
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
     * @param  \App\AddonCategory  $addonCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try{
			DB::beginTransaction();
			Addon::where("addon_category_id",$request->id)->delete();
			AddonCategory::where("id",$request->id)->delete();
			DB::commit();
			return response()->json(["status" => 200,"contrl" => "name"]);
		}catch(Exception $e){
			DB::rollback();
			return response()->json(["status" => 500,"contrl" => "name","msg" => "Error : ".$e->getMessage() ]);
		}
    }
}
