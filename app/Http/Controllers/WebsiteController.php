<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\WebsiteDetail;
use App\WebsiteContact;
use App\Models\InventoryDepartment;
use App\Models\Company;
use App\branch;
use App\Http\Resources\websiteDeliveryAreaResource\deliveryAreasResource;
use Session,Image,DB,Auth,Validator,File;

class WebsiteController extends Controller
{
    public function index(Request $request)
	{
		return view("websites.index",[
			"websites" => WebsiteDetail::with("company")->get(),
		]);
	}
	
	public function create(Request $request)
	{    
		return view("websites.create",[
			"companies" => Company::all()
		]);
	}
	
	public function store(Request $request)
	{

			$this->validate($request, [
				"company_id"  => "required",
				"type"        => "required",
				"theme"       => "required",
				"name"        => "required|unique:website_details",
				"url"         => "required",
				"logo"        => "required",
				"favicon"     => "required",
			]);

		try{
		       if(!empty($request->logo)){
					$request->validate([
					  'logo' => 'mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
					]);
					$imageLogo = time().'.'.$request->logo->getClientOriginalExtension();
					$img = Image::make($request->logo)->resize(150, 70);
					$res = $img->save(public_path('assets/images/website/'.$imageLogo), 75);
				}

				if(!empty($request->favicon)){
					$request->validate([
					  'favicon' => 'mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
					]);
					$imageFavicon = time().'.'.$request->favicon->getClientOriginalExtension();
					$img = Image::make($request->favicon)->resize(150, 70);
					$res = $img->save(public_path('assets/images/website/'.$imageFavicon), 75);
				}

			
				$website = WebsiteDetail::create(array_merge($request->except(["_token","step","logo","favicon"]),
					['logo'=>$imageFavicon,'favicon'=>$imageLogo,'theme_id'=>0]));
                 
               if(!isset($website->id)){
               	  Session::flash('error','Server issue'); 
                  return redirect()->route("website.create");
               }

              Session::flash('success','Success!');
			return redirect()->route("website.index");
		}catch(Exception $e)
		{
			Session::flash('error',$e->getMessage());
			return redirect()->route("website.create");
		}
	}
	
	public function edit(Request $request,$id)
	{  

          $website_detail = WebsiteDetail::find($id);
          if($website_detail == null){
             
             Session::flash('error','Record not found!');
             return redirect()->route('website.index');
          }

		return view("websites.edit",[
			"website" => $website_detail,
			"companies" => Company::all()
		]);
	}
	
	public function update(Request $request,$id)
	{
          
       $website_detail = WebsiteDetail::find($id);

		$this->validate($request, [
				"company_id"  => "required",
				"type"        => "required",
				"theme"       => "required",
				"url"         => "required",
		]);

		try{

			if(!empty($request->favicon)){
				$request->validate([
				  'favicon' => 'mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
				]);

				if(file_exists(public_path('assets/images/website/'.$website_detail->favicon))){
					unlink(public_path('assets/images/website/'.$website_detail->favicon));
				}
				
				$imageFavicon = time().'.'.$request->favicon->getClientOriginalExtension();
				$img = Image::make($request->favicon)->resize(64, 64);
				$res = $img->save(public_path('assets/images/website/'.$imageFavicon), 75);
			}

			if(!empty($request->logo)){

				$request->validate([
				  'logo' => 'mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
				]);

			
				if(file_exists(public_path('assets/images/website/'.$website_detail->logo))){
					unlink(public_path('assets/images/website/'.$website_detail->logo));
				}	

				$imageLogo = time().'.'.$request->logo->getClientOriginalExtension();
				$img = Image::make($request->logo)->resize(200, 200);
				$res = $img->save(public_path('assets/images/website/'.$imageLogo), 75);
			}
			
			$website_detail->company_id  = $request->company_id;
			$website_detail->type        = $request->type;
			$website_detail->name        = $request->name;
			$website_detail->topbar      = $request->topbar;
			$website_detail->theme       = $request->theme;
			$website_detail->url         = $request->url;
			$website_detail->whatsapp    = $request->whatsapp;
			$website_detail->uan_number  = $request->uan_number;

			if(isset($imageLogo)){
			   $website_detail->logo   = $imageLogo;
		    }

		    if(isset($imageFavicon)){
               $website_detail->favicon  = $imageFavicon;
		    }

			$website_detail->save();
			
			
			return redirect()->route("website.index");
		
		}
		catch(Exception $e)
		{
			return redirect()->route("website.edit",$website_detail->id);
		}
	}



// ======================================================================================
//                  Website Slider Modules
//======================================================================================= 

   public function getSlider(Request $request){

		return view("websites.sliders.index",[
			"websites"          => WebsiteDetail::where('company_id',Auth::user()->company_id)->get(),
			"departments"       => InventoryDepartment::where('company_id',Auth::user()->company_id)->get(),
			"websiteSlider"     => DB::table('website_sliders')
		                            ->join('website_details','website_details.id','website_sliders.website_id')
		                            ->select('website_details.*')
		                            ->where('website_details.company_id',Auth::user()->company_id)
		                            ->groupBy('website_details.name')
		                            ->get(),
            "websiteSliderList" => DB::table('website_sliders')
	                                ->join('website_details','website_details.id','website_sliders.website_id')
	                                ->select('website_sliders.id','website_sliders.website_id','website_sliders.slide','website_sliders.invent_depart_name')
	                                ->where('website_details.company_id',Auth::user()->company_id)
	                                ->get()                           
		]);
   }



   public function create_slider(){
  
		return view("websites.sliders.create",[
			"websites"   => WebsiteDetail::where('company_id',Auth::user()->company_id)->get(),
			"departments" => InventoryDepartment::where('company_id',Auth::user()->company_id)->get()
		]);

   }


   public function store_slider(Request $request){
       
       $rules = [
                  'website' => 'required',
                  'image'   => 'required|mimes:jpg,jpeg,png'
                ];

        $this->validate($request,$rules);
         
        $image = $request->file('image');  
        $imageName = date('His').'.'.$image->getClientOriginalExtension();

        $path = $this->create_folder(Auth::user()->company_id,$request->website);

    	 if($path == false){
    	 	return response()->json('Image not uploaded.',500);
    	 }

     	 if(!$image->move($path,$imageName)){
    	 	return response()->json('Image not uploaded.',500);                  
    	 }   	 

        $result = DB::table('website_sliders')
                     ->insertGetId([
	                       'website_id'         => $request->website,
	                       'invent_depart_name' => $request->depart,
	                       'slide'              => $imageName,
	                       'status'             => 1
	                   ]);
  
        if($result){
       
        	Session::flash('success','Success!');
        }else{
        	
        	Session::flash('error','Invalid record');
        }
       return redirect()->route('sliderLists'); 
   }

   public function create_folder($comFOldName,$webFoldName){
	 $path   = public_path('assets/images/website/sliders/').$comFOldName.'/'.$webFoldName;
     $result = true;
	    if(!File::isDirectory($path)){
	        $result = File::makeDirectory($path, 0777, true, true);
	    }  

	  return ($result) ? $path : false;         
   }


   public function getSocialLink(Request $request){

		return view("websites.social-link.index",[
			"websites" => WebsiteDetail::where('company_id',Auth::user()->company_id)
			                                     ->get()                          
		]);
   }

   public function store_SocialLink(Request $request){


   }


   public function destroy_socialLink(Request $request){


   }


// ======================================================================================
//                  Website Delivery areas
//======================================================================================= 

   public function getDeliveryArea(Request $request,branch $branch){

   	$companyId = Auth::user()->company_id;

      $deliveryAreaLists = DB::table('website_delivery_areas as AreaList')
	                            ->join('website_details','website_details.id','AreaList.website_id')
	                            ->join('branch','branch.branch_id','AreaList.branch_id')
	                            ->select('website_details.id as website_id','website_details.name as website_name','AreaList.branch_id','branch.branch_name','AreaList.city_id as city','AreaList.estimate_time','AreaList.charge','AreaList.min_order')
	                            ->where('website_details.company_id',$companyId)
	                            ->groupBy('AreaList.branch_id')
	                            ->get();
	    $deliveryAreaValues = DB::table('website_delivery_areas')
	                             ->whereIn('branch_id',DB::table('website_delivery_areas as AreaList')
	                            ->join('website_details','website_details.id','AreaList.website_id')
	                            ->join('branch','branch.branch_id','AreaList.branch_id')
	                            ->select('AreaList.branch_id')
	                            ->where('website_details.company_id',$companyId)
	                            ->pluck('AreaList.branch_id'))
	                           ->select('id','branch_id','name','longitude','latitude')
	                           ->get();                         

		return view("websites.delivery-area.index",[
			"websites"           => WebsiteDetail::where('company_id',Auth::user()->company_id)->get(),
			"city"               => DB::table('city')->where('country_id',170)->get(),
			"deliveryList"       => $deliveryAreaLists,
			"deliveryAreaValues" => $deliveryAreaValues                          
		]);
   }

   public function getWebsiteBranches(Request $request,branch $branch){

   	return response()->json($branch->getWebsiteBranches($request->websiteId));
   }


   public function store_deliveryArea(Request $request){
         
         $result = null; 
         $areas  = explode(',',$request->areas);
         
         for($i=0;$i < count($areas);$i++){
               $result = DB::table('website_delivery_areas')
                     ->insert([
	                       'branch_id'       => $request->branch,
	                       'city_id'         => $request->city,
	                       'website_id'      => $request->website,
	                       'name'            => $areas[$i],
	                       'estimate_time'   => $request->time_estimate,
	                       'charge'          => $request->charge,
	                       'min_order'       => $request->min_order == '' ? 0 : $request->min_order,
	                       'status'          => 1,
	                   ]); 
         }
        
       return $result == null ? response()->json('Error! Record is not created!',500) : response()->json('Success!',200); 
   }

   public function single_deliveryAreaName_store(Request $request){

   	$branchId       = $request->branchId;
   	$branchName     = $request->branchName;
      $webId          = $request->webId;
      $areaName       = $request->areaName;

       if(DB::table('website_delivery_areas')->where(['website_id'=>$websiteId,'name'=>$areaName])->count() > 0){
          
         return response()->json('This '.$areaName.' area name already taken this '.$websiteName.' website for this branch '.$branchName,500);
       }   
            
          $getRecord = DB::table('website_delivery_areas')
                         ->where(['website_id'=>$websiteId,'branch_id'=>$branchId])
                         ->first();

          if($getRecord == null){
               return response()->json('Server Issue',500);             
          } 


             $resp = DB::table('website_delivery_areas')
                        ->insert([
	                         'branch_id'       => $getRecord->branch_id,
	                         'city_id'         => $getRecord->city_id,
	                         'website_id'      => $getRecord->website_id,
	                         'name'            => $areaName,
	                         'estimate_time'   => $getRecord->time_estimate,
	                         'charge'          => $getRecord->charge,
	                         'min_order'       => $getRecord->min_order,
	                         'status'          => 1,
	                     ]);


         return $resp ? response()->json('success',200) : response()->json('Record is not submited Server issue please try again later',500);      
   }

   public function update_deliveryArea(Request $request){

   	$uniqueId    = $request->id;
   	$areaName    = $request->areaName; 
   	$websiteId   = $request->webId;
   	$websiteName = $request->webName;
         
      if(DB::table('website_delivery_areas')->where(['website_id'=>$websiteId,'name'=>$areaName])->where('id','!=',$uniqueId)->count() > 0){
          
         return response()->json('This '.$areaName.' area name already taken this '.$websiteName.' wsebsite.',);
      }

      if(DB::table('website_delivery_areas')->where(['website_id'=>$websiteId,'id'=>$uniqueId])->update(['name'=>$areaName])){

        return response()->json('success',200);
      }else{
        return response()->json('Server issue record is not updated.',500);
      }
   }   

   public function destroy_deliveryArea(Request $request){
         
      if(DB::table('website_delivery_areas')->where(['website_id'=>$request->websiteId,'branch_id'=>$request->branchId])->update(['status'=>0])){
          Session::flash('success','Successfully');
      }else{
      	Session::flash('error','This '.$request->branchName.' branch delivery area remove for this '.$request->websiteName.' website.');
      }

      return redirect()->route('deliveryAreasList');
   }



}
