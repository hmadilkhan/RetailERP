<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use DB;

class SliderController extends Controller
{
    public function index(Request $request)
	{
		return view("websites.lists-slider",[
			"sliders" => BD::table("website_sliders")->get(),
		]);
	}
	
	public function create(Request $request)
	{
		return view("websites.create-slider",[
			"companies" => Company::all()
		]);
	}
	
	public function store(Request $request)
	{
		$this->validate($request, [
			"slide" => "required",
		]);

		try{
		
			if(!empty($request->slide))
			{
				$request->validate([
				  'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
				]);
				$imageName = time().'.'.$request->logo->getClientOriginalExtension();
				$img = Image::make($request->logo)->resize(150, 70);
				$res = $img->save(public_path('assets/images/website/'.$imageName), 75);
			}
			\DB::beginTransaction();
			$website = WebsiteDetail::create(array_merge($request->except(["_token","logo","contacttype","number"]),[
				"logo" => $imageName,
			]));
			$count = count($request->number);
			for($i=0; $i<$count; $i++){
				if($request->contacttype[$i] != ""){
					WebsiteContact::create([
						"website_id" => $website->id,
						"type" => $request->contacttype[$i],
						"number" => $request->number[$i],
					]);
				}
			}
			\DB::commit();
			return redirect()->route("website-details.index");
		}catch(Exception $e)
		{
			\DB::rollback();
			return redirect()->route("website-details.create");
		}
	}
	
	// public function edit(Request $request,WebsiteDetail $website_detail)
	// {
	// 	return view("websites.edit",[
	// 		"website" => $website_detail,
	// 		"companies" => Company::all()
	// 	]);
	// }
	
	// public function update(Request $request,WebsiteDetail $website_detail)
	// {
	// 	$this->validate($request, [
	// 		"company_id" => "required",
	// 		"type" => "required",
	// 		"web_name" => "required",
	// 		"topbar_content" => "required",
	// 		"url" => "required",
	// 	]);
	// 	try{
	// 		if(!empty($request->logo))
	// 		{
	// 			$request->validate([
	// 			  'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
	// 			]);
	// 			unlink(public_path('assets/images/website/'.$request->previous_logo));
	// 			$imageName = time().'.'.$request->logo->getClientOriginalExtension();
	// 			$img = Image::make($request->logo)->resize(200, 200);
	// 			$res = $img->save(public_path('assets/images/website/'.$imageName), 75);
	// 		}
			
	// 		$website_detail->company_id = $request->company_id;
	// 		$website_detail->type = $request->type;
	// 		$website_detail->web_name = $request->web_name;
	// 		$website_detail->topbar_content = $request->topbar_content;
	// 		$website_detail->url = $request->url;
	// 		$website_detail->logo = (!empty($request->logo) ?  $imageName : $request->previous_logo);
	// 		$website_detail->save();
			
	// 		WebsiteContact::where("website_id", $website_detail->id)->delete();
	// 		$count = count($request->number);
	// 		for($i=0; $i<$count; $i++){
	// 			if($request->contacttype[$i] != ""){
	// 				WebsiteContact::create([
	// 					"website_id" => $website_detail->id,
	// 					"type" => $request->contacttype[$i],
	// 					"number" => $request->number[$i],
	// 				]);
	// 			}
	// 		}
			
	// 		return redirect()->route("website-details.index");
		
	// 	}
	// 	catch(Exception $e)
	// 	{
	// 		return redirect()->route("website-details.create");
	// 	}
	// }
}
