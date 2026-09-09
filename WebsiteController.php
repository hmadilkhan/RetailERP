<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\inventory_department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Inventory;
use App\Models\InventoryAddon;
use App\Models\WebsiteProduct;
use App\Http\Resources\Api\ProductResource;
use App\Http\Resources\Api\BranchResource;
use App\Http\Resources\Api\testResource;
use App\Http\Resources\Api\websiteResource;
use App\Http\Resources\Api\ProductAddonResource;
use App\Http\Resources\Api\SalesDetailsResource\SalesReceiptsResource;
use App\Http\Resources\Api\SalesDetailsResource\PosSalesDetailsResource;
use App\Http\Resources\Api\websiteDiscountResource;
use App\Http\Resources\Api\MobileSliderResource;
use App\Http\Resources\Api\CustomerReviewResource;
use App\Http\Resources\Api\TagResource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Resources\Json\JsonResource;
use File;
use Validator;
use App\Models\WebsiteContactForm;
use Illuminate\Support\Facades\Mail;
use App\Mail\WebsiteContactFormSubmitted;

class WebsiteController extends Controller
{
  public function getDepartments(Request $request)
    {
        $result = DB::table("inventory_department")
                  ->where("company_id",$request->id)
                  ->get();

        // return response()->json($result);
    }
    
    // testing app notification firebase //
    public function GetNotify(Request $request){
        $data_pushNotification = ['websiteName'=>'sdb','orderId'=>484789,'terminalId'=>305];
             
             return $this->sendPushNotificationForPermission($data_pushNotification); 

    }
    
    public function encrypt_text(){

        return Crypt::encryptString('35','aes-128-gcm',100);
    } 
    
    public function websiteLogo(Request $request){
        
      $result = DB::table('website_details')->where('id',$request->id)->where('status',1)->first();
      
      if($result == null){
           $path      = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/no-image.png';  
           $headers = array(
                              'Content-Type'        => 'image/png',
                              'Content-Description' => 'no-image.png',
                              'Cache-Control'       => 'public, max-age=604800',
                            ); 
    
        return response()->file($path, $headers);            
      }

        $path = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/website/'.$result->logo;
        $extension = strtolower(pathinfo($result->logo,PATHINFO_EXTENSION));
        $filename  = $result->logo;        
        
        if(!\File::exists($path)){
           $extension = 'png';
           $filename  = 'no-image.png';
           $path      = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/'.$filename;

        }

        $headers = array(
                          'Content-Type'        => 'image/'.$extension,
                          'Content-Description' =>  $filename,
                          'Cache-Control'       => 'public, max-age=604800',
                        ); 

      return response()->file($path, $headers); 
    }
    
    public function productJSON(Request $request)
    {
        $product = DB::select('
        SELECT a.id,a.item_code,a.product_name,a.slug,a.short_description, a.product_description,a.details,b.department_name,c.sub_depart_name,d.name as uom,CONCAT("http://sabsoft.com.pk/Retail/public/assets/images/products/" ,a.image) as image,e.name as companyName,f.*,IFNULL((SELECT SUM(balance) from inventory_stock where product_id = a.id and branch_id = 10 ),0) as qty FROM inventory_general a
    INNER JOIN inventory_price f on f.product_id = a.id and f.status_id = 1
    INNER JOIN inventory_department b on b.department_id = a.department_id
    INNER JOIN inventory_sub_department c on c.sub_department_id = a.sub_department_id
    INNER JOIN inventory_uom d on d.uom_id = a.uom_id
    INNER JOIN company e on e.company_id = a.company_id where a.company_id = ? and a.status = 1 and a.product_mode IN(2, 3)', [$request->id]);
        return response()->json($product);
    }    

  public function productById(Request $request)
  {
    return Inventory::with("addons","addons.category","addons.category.addons","variations","price")->where('company_id',$request->id)->get();
  }
  
  public function getWebsiteStatus(Request $request){
     // return DB::table("website_branches_schedule")->where("opening_time",date("H:i"))->select("branch_id")->get();
    return DB::table('website_branches')
                          ->where(['website_id'=>$request->id,'branch_id'=>$request->branch_id,'status'=>1,'is_open'=>1])
                          ->count() > 0 ? 1 : 0;
     
    //   return response()->json($result);
  }
  
  public function getWebProduct_filter(Request $request){
      
       if(!isset($request->id) && !isset($request->webtype)){
            return response()->json("Not available parameter",500);
       } 
       
       if($request->mode == 'dept'){
         $websiteId = $request->id;
         return ['department'=>DB::table("inventory_general")
                                  ->join("website_products","website_products.inventory_id","inventory_general.id")
                                  ->join("inventory_department","inventory_department.department_id","inventory_general.department_id")
                                  ->where('website_products.website_id',$request->id)
                                  ->where('website_products.status',1)
                                  ->where('inventory_department.slug',$request->slug)
                                  ->select(DB::raw('inventory_department.department_id ,inventory_department.department_name,inventory_department.slug,count(inventory_general.id) as product_counts,inventory_department.meta_title,inventory_department.meta_description,inventory_department.description,inventory_department.image,inventory_department.banner,inventory_department.mobile_banner'))
                                  ->first(),
               
                'products'=>ProductResource::customCollection(Inventory::with("price","brand","website_product","department")
                                                            ->whereHas("website_product",function($q) use ($request){
                                                                    $q->where('website_id',$request->id);
                                                                })
                                                            ->whereHas("department",function($q) use ($request){
                                                                    $q->where('slug',$request->slug);
                                                                    $q->where('status',1);
                                                                })    
                                                            ->orderBy('priority','desc')
                                                            ->get(),$request->id,$request->webtype) 
                ];
                                                            
       }
       
       if($request->mode == 'sbdept'){
		$department = DB::table("inventory_sub_department")->where("slug",$request->slug)->first();
         return [
		 'subdepartment'=> DB::table("inventory_sub_department")
                                  ->where('department_id',$department->department_id)
                                  ->get(),
		 'department'=> DB::table("inventory_general")
                                  ->join("website_products","website_products.inventory_id","inventory_general.id")
                                  ->join("inventory_sub_department","inventory_sub_department.sub_department_id","inventory_general.sub_department_id")
                                  ->where('website_products.website_id',$request->id)
                                  ->where('inventory_sub_department.slug',$request->slug)
                                  ->select(DB::raw('inventory_sub_department.sub_department_id ,inventory_sub_department.sub_depart_name,count(inventory_general.id) as product_counts'))
                                  ->first(),
                'products'=>ProductResource::customCollection(Inventory::with("price","brand","website_product","subdepartment")
                                                            ->whereHas("website_product",function($q) use ($request){
                                                                    $q->where('website_id',$request->id);
                                                                })
                                                            ->whereHas("subdepartment",function($q) use ($request){
                                                                    $q->where('slug',$request->slug);
                                                                    $q->where('status',1);
                                                                })    
                                                            ->orderBy('priority','desc')
                                                            ->get(),$request->id,$request->webtype) 
                ];
                
                            // ProductResource::customCollection(Inventory::with("price","brand","website_product")
                            //                               ->whereHas("website_product",function($q) use ($request){
                            //                                     $q->where('website_id',$request->id);
                            //                                 })
                            //                                 ->where('sub_department_id',DB::table('inventory_sub_department')
                            //                                                              ->where('slug',$request->slug)
                            //                                                              ->pluck('sub_department_id')
                            //                                         )
                            //                                 ->orderBy('priority','desc')
                            //                                 ->get(),$request->id,$request->webtype)                 
                                                            
       }       
       
       if($request->mode == 'prod'){
      
		$product = Inventory::with("price","website_product","brand")
			->whereHas("website_product", function($q) use ($request) {
				$q->where('website_id', $request->id);
			})
			->where('slug', $request->slug)
			->orderBy('priority','desc')
			->first();

		if (!$product) {
			return [
				'department' => null,
				'product' => null,
				'message' => 'Product not found'
			];
		}

         return [
					'department'=>DB::table("inventory_general")
                                  ->join("website_products","website_products.inventory_id","inventory_general.id")
                                  ->join("inventory_department","inventory_department.department_id","inventory_general.department_id")
                                  ->where('website_products.website_id',$request->id)
                                  ->where('inventory_general.slug',$request->slug)
                                  ->select(DB::raw('inventory_department.department_id ,inventory_department.department_name,inventory_department.slug,inventory_department.banner,inventory_department.mobile_banner'))
                                  ->first(),
								  
                 // 'product'=>ProductResource::customCollection(Inventory::with("price","website_product","brand")
                                                            // ->whereHas("website_product",function($q) use ($request){
                                                                // $q->where('website_id',$request->id);
                                                            // })
                                                            // ->where('slug',$request->slug) 
                                                            // ->orderBy('priority','desc')
                                                            // ->get(),$request->id,$request->webtype)
					'product' => ProductResource::customItem($product, $request->id, $request->webtype)
                ];    
                                                            
       }     
	   if($request->mode == 'tags'){
		   return ['tag'=>DB::table("tags")
		                     ->join('website_details','website_details.company_id','tags.company_id')
		                     ->where('website_details.id',$request->id)
		                     ->where('tags.slug',$request->slug)
		                     ->where('tags.status',1)
		                     ->select('tags.id','tags.name','tags.slug','tags.meta_title','tags.meta_description','tags.desktop_banner','tags.mobile_banner')
		                     ->first(),
		          'products'=>ProductResource::customCollection(Inventory::with("price","website_product","brand","inventoryTags.tags")
                                                            ->whereHas("inventoryTags.tags",function($q) use ($request){
                                                                $q->where('slug',$request->slug);
                                                            })
                                                            // ->where('slug',$request->slug) 
                                                            ->orderBy('priority','desc')
                                                            ->get(),$request->id,$request->webtype)];
	   }
  }
  
  public function getwebProducts_for_app($data){

       if(!isset($data->companyId)){
            return response()->json("Not available parameter",500);
       }
       
     return ProductResource::customCollection(Inventory::with("price","brand")
                                                        ->where('company_id',$data->companyId)
                                                        ->orderBy('priority','desc')
                                                        ->where("status",1)
                                                        ->get(),'');        
  }  
  
  public function getwebProducts(Request $request){

    //   $website  = DB::table('website_details')
    //                   ->where('id',$request->id)
    //                   ->first();
    
    //     if($website == null){
    //          return response()->json("Not Available content",404);
    //     }  
    
       if(!isset($request->id) && !isset($request->type)){
            return response()->json("Not available parameter",500);
       }
       
     return ProductResource::customCollection(Inventory::with("price","brand")
                                                        ->whereIn('id',WebsiteProduct::where("website_id",$request->id)
                                                                                     ->where("status",1)
                                                                                     ->pluck("inventory_id"))
                                                                                    //  ->select('inventory_general.*','apiprice.*','brand.name','brand.slug as brand_slug')
                                                        ->orderBy('priority','desc')
                                                        ->get(),$request->id,$request->type);        
       
    //   $re = Inventory::with("price","brand")
    //                                                         ->whereIn('id',WebsiteProduct::where("website_id",$request->id)
    //                                                                                      ->where("status",1)
    //                                                                                      ->pluck("inventory_id"))
    //                                                                                     //  ->select('inventory_general.*','apiprice.*','brand.name','brand.slug as brand_slug')
    //                                                         ->orderBy('priority','desc')
    //                                                         ->get();
    //                                                         return $re[0]['apiprice'];

  }
  
  public function getwebProducts_debug(Request $request){


       if(!isset($request->id) && !isset($request->type)){
            return response()->json("Not available parameter",500);
       }

         return Inventory::whereIn('id',WebsiteProduct::where("website_id",$request->id)
                                                         ->where("status",1)
                                                         ->pluck("inventory_id"))
                                                        //  ->select('inventory_general.*','apiprice.*','brand.name','brand.slug as brand_slug')
                            ->orderBy('priority','desc')
                            ->select('inventory_general.id','inventory_general.product_name','inventory_general.slug')
                            ->get(); 
  }  
  
  public function mobile_slider(Request $request){
      
       if(!isset($request->id)){
            return response()->json("Not available parameter",500);
       } 
       
       $sliders = DB::table('website_sliders')
                    ->join('website_details','website_details.id','website_sliders.website_id')
                    ->where(['website_sliders.website_id'=>$request->id,'website_sliders.type'=>'mobile'])
                    ->select('website_sliders.*','website_details.url as website_url','website_details.company_id')
                    ->get(); 
                    
        if($sliders == null){
           return response()->json("Not found slider",500);  
        }            
      // JsonResource::withoutWrapping();
      return MobileSliderResource::collection($sliders);
    //  return $sliders;
  }
  
  public function newsLetterEmail_save(Request $request){
        try {
            // Define validation rules
            $rules = [
                'website_id' => 'required',
                'email'      => 'required|email',
            ];
        
            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);
        
            // Check if validation fails
            if ($validator->fails()) {
                // Return validation errors
                return response()->json(['errors' => $validator->errors()], 500);
            }
            
            if(DB::table('website_newsletter_emails')->where('website_id',$request->website_id)->where('email',$request->email)->count() > 0){
                return response()->json('Success', 200);
            }
        
            // Attempt to insert data into the database
            $insert = DB::table('website_newsletter_emails')->insert([
                'website_id'  => $request->website_id,
                'email'       => $request->email,
                'created_at'  => date("Y-m-d H:i:s"),
            ]);
        
            // Check if insert was successful
            if ($insert) {
                return response()->json('Success', 200);
            } else {
                // Throw a custom exception if the insert fails
                throw new \Exception('Database insertion failed.');
            }
        } catch (\Exception $e) {
            // Catch any exceptions and return the error message with the exception details
            return response()->json([
                'error'   => 'Error! Database insertion failed.',
                'message' => $e->getMessage(),
            ], 500);
        }
  }
  
  public function save_customer_review(Request $request){
     try {

         $imageName = null;

         $rules = [
                    'website_id'     => 'required',
                    'customer_name'  => 'required',
                    'customer_email' => 'required|email',
                    'review_title'   => 'required',
                    'review'         => 'required',
                    'rating'         => 'required'
                ];
        
         $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
          return response()->json($validator->messages(), 422);
        }       
         

                // Insert data into the database
             $result =   DB::table('website_customer_reviews')
                           ->insertGetId([
                                'website_id'     => $request->website_id,
                                'customer_name'  => $request->customer_name,
                                'customer_email' => $request->customer_email,
                                'review_title'   => $request->review_title,
                                'review'         => $request->review,
                                'product_code'   => $request->product_code,
                                'status'         => 1,
                                'rating'         => $request->rating, 
                                'created_at'     => now() 
                          ]);
                          
            
            if($result){
                 if(isset($request->images)){
                      $rules = [
                                 'images.*' =>'required|image|mimes:jpeg,png,jpg,webp|max:1048'
                              ];
        
                      $validator = Validator::make($request->all(), $rules);
        
                      if ($validator->fails()) {
                        return response()->json($validator->messages(), 422);
                      }   
                      
                      $file = $request->file('images');
                      foreach($file as $value){
                        // $image = $request->file('image');
                        $imagePath = '../Retail/storage/images/customer-reviews'; // Define your target directory
                        $imageName = time() . '_' . $value->getClientOriginalName(); // Create a unique image name
                        if($value->move($imagePath, $imageName)){
                            DB::table('website_customer_review_images')
                               ->insert([
                                  'review_id'      => $result,
                                  'image'          => $imageName,
                               ]);
                        }
                      }
                 }                
            }
                          
        
             return response()->json('Success!', 200);

        } catch (\Exception $e) {
            return response()->json('Error! ' . $e->getMessage(), 500);
        }
  }
  
  public function get_customer_reviews(Request $request){
    //   $rules = [
    //               'website' => 'required',
    //           ];
    //     $this->validate($request,$rules);  
    
      if(!isset($request->website)){
          return response()->json('Record not found Server Issue!',500);
      }
        
        $product_code = $request->product_code;
        
        $result = DB::table('website_customer_reviews')
                      ->where('website_id',$request->website)
                      ->where('status',1)
                      ->when('product_code',function($query) use($product_code){
                              $query->where('product_code', $product_code);
                      })
                      ->get();
                      
        // $getRating = DB::table('website_customer_reviews')
        //               ->where('website_id',$request->website)
        //               ->whereIn('rating',[1,2,3,4,5])
        //               ->where('status',1)
        //               ->select(DB::raw('rating,count(id) as count'))
        //               ->groupBy('rating')
        //               ->orderBy('rating','ASC')
        //               ->get();     
        
        
        $ratingReviews = DB::table('website_customer_reviews')
                            ->select('rating',DB::raw('COUNT(*) as count'))
                            ->where('status',1)
                              ->when('product_code',function($query) use($product_code){
                                      $query->where('product_code', $product_code);
                              })                            
                            ->groupBy('rating')
                            ->pluck('count', 'rating');   
                            
        $allRatings    = collect([1, 2, 3, 4, 5])
                            ->mapWithKeys(function ($rating) use ($ratingReviews) {
                                return [$rating => $ratingReviews->get($rating, 0)];
                            });            

        return response()->json(['reviews'=>CustomerReviewResource::collection($result),'counts'=>count($result),'average'=>number_format($result->avg('rating'),2),'rating'=>$allRatings]);        
  }
  
//   public function get_product_reviews(Request $request){
      
//       if(!isset($request->website)){
//           return response()->json('Record not found Server Issue!',500);
//       }
        
//         $product_url = $request->product_url;
        
//         $result = DB::table('website_customer_reviews')
//                       ->where('website_id',$request->website)
//                       ->where('status',1)
//                     //   ->when('product_url',function($query) use($product_url){
//                     //           $query->where('product_url', $product_url);
//                     //   })
//                       ->get();
                      
   
        
        
//         $ratingReviews = DB::table('website_customer_reviews')
//                             ->select('rating',DB::raw('COUNT(*) as count'))
//                             ->where('status',1)
//                             ->groupBy('rating')
//                             ->pluck('count', 'rating');   
                            
//         $allRatings    = collect([1, 2, 3, 4, 5])
//                             ->mapWithKeys(function ($rating) use ($ratingReviews) {
//                                 return [$rating => $ratingReviews->get($rating, 0)];
//                             });            

//         return response()->json(['reviews'=>CustomerReviewResource::collection($result),'counts'=>count($result),'average'=>number_format($result->avg('rating'),2),'rating'=>$allRatings]);            
//   }  
  
  public function getDealAddon(Request $request){

       $website  = DB::table('website_details')
                      ->where('id',$request->id)
                      ->first();
    
        if($website == null){
             return response()->json("Not Available content",404);
        }       
      
      
     return ProductAddonResource::collection(DB::table('inventory_addons')
                            ->where('inventory_addons.product_id',$request->product_id)
                            ->where('inventory_addons.status',1)
                            ->where('inventory_addons.inventory_addon_type','deal')
                            ->join('addon_categories','addon_categories.id','inventory_addons.addon_id')
                            ->select('addon_categories.id','addon_categories.name','addon_categories.show_website_name','addon_categories.type','addon_categories.is_required','addon_categories.addon_limit','inventory_addons.product_id')
                            ->get()); 
  }
  
  public function applyVoucher(Request $request){
      
      $customerRecord = null;
      
      $record = DB::table('discount_general as a') 
                    ->join('discount_general_details as b','b.discount_id','a.discount_id')
        			->join('discount_days as c','c.discount_general_id','a.discount_id')
        			->where(['a.discount_code'=>$request->voucher,'a.website_id'=>$request->id,'a.open_discount'=>0,'a.status'=> 1,'c.day'=>strtolower(date("D"))])
        			->select('a.discount_id','a.customer_eligibilty')
        			->get()
        			->toArray();
      	
        			if($record != null){
        			 //   return $record[0]->customer_eligibilty;
        			 
            			 if(isset($request->customer) && $record[0]->customer_eligibilty == 2){
                                      $customerRecord = DB::table('customers')->where(['mobile'=>$request->customer,'website_id'=>$request->id])->pluck('id');
                                      if($customerRecord == null){
                                          return response()->json('invalid voucher',500);
                                      }
                                      
                                $cust_discount = DB::table('discount_customer')->where(['cust_id'=>$customerRecord,'discount_id'=>$record[0]->discount_id,'status'=>1])->count();  
                           
                                if($cust_discount != null){
                                    return $this->callWebsiteDiscountResource($request);
                                }
                                
            			 }elseif($record[0]->customer_eligibilty == 1){
            			         return $this->callWebsiteDiscountResource($request); 
            			 }else{
            			         return response()->json('invalid voucher',500);
            			 }
        			    
        			}else{
        			    return response()->json('invalid voucher',500);
        			}
    	
    //  return reponse()->json($record);	
  }
  
  public function callWebsiteDiscountResource(Request $request){
      
         return new websiteDiscountResource(DB::table('discount_general as a')
                                                ->join('discount_type as b','b.discount_type_id','a.discount_type')  
                                                ->join('discount_general_details as c','c.discount_id','a.discount_id')
                                    			->join('discount_period as d','d.discount_id','a.discount_id')
                                    			->join('discount_days as e','e.discount_general_id','a.discount_id')
                                    			->where(['a.discount_code'=>$request->voucher,'a.open_discount'=>0,'a.website_id'=>$request->id,'a.status'=> 1,'e.day'=>strtolower(date("D"))])
                                    			->select('a.discount_id','a.discount_code','c.applies_to','b.discount_type_id','b.type_name','d.startdate','d.starttime','d.enddate','d.endtime','c.discount_value','c.min_order')
                                                ->get()
        			->toArray());
  }

   public function getWebDetails(Request $request){

           $website  = DB::table('website_details')
                          ->join('settings','settings.company_id','website_details.company_id')
                          ->where('website_details.id',$request->id)
                          ->where('website_details.status',1)
                          ->select('website_details.*','settings.data as companySetting')
                          ->first();

            if($website == null){
                 return response()->json("Not Available",404);
            } 

          $websiteId = $request->id;  
          $website = new websiteResource($website);  
          
        $response = response()->json(['website'=>$website]);
        // $response->headers->set('Cache-Control', 'public, max-age=86400'); 
         
         return $response;
    }
    
    // public function getDeliveryDetails(Request $request){
    //     return DB::table('website_delivery_areas')
    //                                          ->where('city',DB::raw('SELECT city_id FROM customer_addresses INNSER JOIN customers ON customers.id = customer_addresses.customer_id  WHERE customer_addresses.id = '.$request->address_id.' and customer.mobile = "'.$request->mobile.'"'))
    //                                          ->where('website_id',$request->webId)
    //                                          ->where('status',1)
    //                                          ->where('is_city',1)
    //                                          ->first();
    // }
// 	public function debugGenerateOrder(Request $request){
// 		try{
// // 			return $request;
//   DB::table('test')->insert(['json'=>json_encode($request->json()->all())]);
  

// 			 // $data = $request->json()->all();
// 			 //$data = json_decode(json_encode($request->json()->all()));
//               return $this->generateOrder($request);
// 			 // $checkWebSite = DB::table('website_details')->where('id',$request->webId)->first();
        
//                 // if(!$checkWebSite){
//                   // return response()->json('Invalid Details',500);   
//                 // }

// 			// Return the same data as JSON
// 			return response()->json($request->webId, 200);
			
// 		}catch(\Exception $e){
// 			return response()->json($e->getMessage(),500); ;
// 		}
		
// 	}
	
    public function generateOrder(Request $request){
        
      try{    
          
              $sentData = '';
              $discountArray = [];
              $orderMode = isset($request->orderMode) ? $request->orderMode : 'default';
              
              $checkWebSite = DB::table('website_details')->where('id',$request->webId)->first();
        
              if(!$checkWebSite){
                   return response()->json('Invalid Details',500);   
              }
			  
                  
              $customer        = $request->contact_details;
              $deliveryArea    = $request->cart_data['deliveryArea'];
              $custId          = null;
              $cust_locationId = null;
             

            //  return response()->json($deliveryArea['minOrder']);
             
             if(isset($deliveryArea['minOrder']) && strtolower($deliveryArea['type']) == 'delivery'){ 
              if($deliveryArea['minOrder'] > $request->cart_data['subtotal']){
                 return response()->json('min order value greater than totalAmount',500);
              }
             }
             
               
              if(isset($request->entireDiscountDetails) || isset($request->voucher)){ 
               
                   if($request->entireDiscountDetails != null && $request->voucher == null){
                       $discountArray = (array) $request->entireDiscountDetails;
                       
                       $discountArray['type'] = 'entir discount';
                   }
                   
                   if($request->entireDiscountDetails == null && $request->voucher != null){
                       $discountArray = (array) $request->voucher;
                       
                       $discountArray['type'] = 'voucher';
                   }
                 
                     
                   if(count($discountArray) > 0){
                        $checkDiscount = DB::table('discount_general')->where(['discount_code'=>$discountArray['discount_code'],'status'=>1])->count();
                       
                        if($checkDiscount == 0){
                            return response()->json('invalid voucher',406);
                        }
                        
                    //   if($discountArray['min_order'] != null){
                           if($discountArray['min_order'] > $request->cart_data['subtotal']){
                               return response()->json('min order value greater than totalAmount',500);
                           }
                    //   }
                   }
              }
                     
             
            // if(!isset($customer['address_id'])){  
              $getBranch = DB::table('branch')
                              ->join('website_branches','website_branches.branch_id','branch.branch_id')
                              ->join('website_details','website_details.id','website_branches.website_id')
                              ->where(['website_branches.website_id'=>$request->webId,'website_branches.branch_id'=>$deliveryArea['branchId'],'website_branches.status'=>1])
                              ->select('branch.*','website_branches.terminal_id','website_details.type as website_type')
                              ->first();
              
              if($getBranch == null){
                   return response()->json('Server Message Invalid branch',500);
              }
            // }
              
            DB::beginTransaction(); 
           
              $checkCust = DB::table('customers')->where(['mobile'=>$customer['phNumber'],'website_id'=>$request->webId])->first();
               if($checkCust == null){
        
                    $createCust = DB::table('customers')
                                      ->insertGetId([
														'branch_id'  => $deliveryArea['branchId'],
														'company_id' => $checkWebSite->company_id,
														'website_id' => $request->webId,
														'status_id'  => 1,
														'country_id' => $getBranch->country_id,
														'city_id'    => $deliveryArea['cityId'],
														'title'      => $customer['title'] ,
														'name'       => $customer['fullName'],
														'mobile'     => $customer['phNumber'],
														'alternate_number'  => $customer['alternate_number'] ?? ''
                                                    ]);
                      if(!$createCust){
                            return response()->json('Server Message customer create',500);
                      }
                       
        
                 $custId =  $createCust;                    
                      
               }else{
                 $custId =  $checkCust->id; 
               }
               

        
               if($custId && !isset($customer['address_id'])){
                   $checkAddress_exists = DB::table('customer_addresses')
                                             ->where(['customer_id'=>$custId,'address'=>$customer['fullAddress'],'landmark'=>$customer['landmark'],'city_id'=>$deliveryArea['cityId']])
                                             ->first();
                   if($checkAddress_exists != null){
                       $cust_locationId = $checkAddress_exists->id;
                   }else{
                   
                        $createCust = DB::table('customer_addresses')
                                          ->insertGetId([
                                                        'latitude'    => '',
                                                        'longitude'   => '',
                                                        'customer_id' => $custId,
                                                        'city_id'     => $deliveryArea['cityId'],
                                                        'address'     => $customer['fullAddress'],
                                                        'landmark'    => $customer['landmark'],
                                                        ]);
                      if(!$createCust){
                           return response()->json('Server Message customer address error',500);
                      }  
            
                      $cust_locationId = $createCust; 
                   }
        
               }else{

                  if(isset($customer['address_id'])){
                     $cust_locationId = $customer['address_id'];

                     $getDeliveryDetail = DB::table('customer_addresses')
                                             ->join('customers','customers.id','customer_addresses.customer_id')
                                             ->where('customer_addresses.id',$cust_locationId)
                                             ->where('customers.mobile',$customer['phNumber'])
                                             ->where('customers.website_id',$request->webId)
                                             ->count();
                                             
                     if($getDeliveryDetail == 0){
                         return response()->json('Server Message customer address server error',500); 
                     }
                
                  }else{
                      return response()->json('Server Message customer address error',500); 
                  }
              }
               
               
               $random_orderId = $this->random_orderId_generate();// for url purpose
        
               
                $result = DB::table('sales_receipts')->insertGetId([
                                        'receipt_no'            => date("YmdHis").$deliveryArea['branchId'].$request->webId,
                                        'customer_id'           => $custId,
                                        'url_orderid'           => $random_orderId, 
                                        'branch'                => ($custId == 55213 ? 17 : $deliveryArea['branchId']),
                                        'website_id'            => ($custId == 55213 ? 36 : $request->webId),
                                        'terminal_id'           => $getBranch->terminal_id,
                                        'order_mode_id'         => $orderMode == 'laundry' ? 10 : 4,
                                        'payment_id'            => isset($request->cart_data['payment_id']) ? $request->cart_data['payment_id'] : null,
                                        'wallet_id'            => isset($request->cart_data['wallet_id']) ? $request->cart_data['wallet_id'] : null,
                                        'actual_amount'         => isset($request->cart_data['subtotal']) ? $request->cart_data['subtotal'] : null,
                                        'total_amount'          => $request->cart_data['totalAmount'],
                                        'total_item_qty'        => $request->cart_data['count'],
                                        'delivery_area_id'      => $deliveryArea['areaId'],
                                        'delivery_area_name'    => $deliveryArea['areaName'],
                                        'delivery_type'         => strtolower($deliveryArea['type']),
                                        'amount_change_request' => strtolower($deliveryArea['amount_change_request']),
                                        'delivery_charges'      => $request->cart_data['deliveryCharges'],
                                        'cust_location_id'      => $cust_locationId,
                                        'delivery_instructions' => isset($customer['instructions']) ? $customer['instructions'] : null,
                                        'status'                => 1,
                                        'web'                   => 1,
                                        'date'                  => date("Y-m-d"),
                                        'time'                  => date("h:i:s"),
                                        'isSeen'                => 1,
                                        'turnaround_time'       => isset($request->isUrgent) ? $request->isUrgent : '',
										'pickup_date'   		=> data_get($request, 'scheduleDates.pickupDate'),
										'pickup_slot'   		=> data_get($request, 'scheduleDates.pickupSlot'),
										'delivery_date' 		=> data_get($request, 'scheduleDates.deliveryDate'),
										'delivery_slot' 		=> data_get($request, 'scheduleDates.deliverySlot')
                                  ]);
        
        
                   if($result){
                      if(DB::table('sales_account_general')->where(['receipt_id'=> $result])->count() == 0){
                          
                        if(isset($request->paymentMethod) && $request->paymentMethod != null){  
                              DB::table('sales_receipt_online_payment')
                                 ->insert([
                                            'receipt_id'      => $result,
                                            'payment_id'      => $request->paymentMethod['id'],
                                            'type'            => $request->paymentMethod['account_mode'],
                                            'status'          => 1,
                                            'created_at'      => date('Y-m-d')
                                      ]);  
                                      
                              DB::table('sales_receipt_discounts')
                                 ->insert([
                                            'receipt_id'            => $result,
                                            'discount_percentage'   => $request->paymentMethod['discount_percentage'],
                                            'discount_value'        => $request->paymentMethod['discount_value'],
                                            'reason'                => $request->paymentMethod['account_mode'],
                                      ]);                                       
                                      
                        }
						
						// Record Service Type like Normal/Urgent
						if(isset($request->services) && $request->services != null){  
							DB::table('sales_receipts_services')
							 ->insert([
										'sales_receipt_id'  => $result,
										'service_type_id'   => $request->services['service_type_id'],
									]);
						}
                          
                          
                          DB::table('sales_account_general')
                             ->insert([
                                        'receipt_id'      => $result,
                                        'total_amount'    => $request->cart_data['totalAmount'],
                                        'status'          => 1,
                                  ]);
                                  
                                  
                          DB::table('sales_account_subdetails')
                             ->insert([
                                        'receipt_id'               => $result,
                                        'delivery_charges'         => $deliveryArea['areaId'],
                                        'delivery_charges_amount'  => $request->cart_data['deliveryCharges'],
                                        'discount_code'            => (count($discountArray) > 0 ? $discountArray['discount_code'] : null),
                                        'discount_percentage'      => (count($discountArray) > 0 ? $discountArray['percentage'] : null),
                                        'discount_amount'          => (count($discountArray) > 0 ? $discountArray['price'] : null),
        
                                  ]); 
                                  
                            if(count($discountArray) > 0){      
                              DB::table('sales_receipt_discounts')
                                 ->insert([
                                            'receipt_id'            => $result,
                                            'discount_percentage'   => $discountArray['percentage'],
                                            'discount_value'        => $discountArray['price'],
                                            'reason'                => $discountArray['type'],
                                      ]);
                            }
                                  
                      }
                      
                  if(!empty($request->cart_data['cartItems'])){      
        
                    foreach($request->cart_data['cartItems'] as $receipt_detail_val){
                                
                        $itemCode = $receipt_detail_val['id'];
                          // if(DB::table('sales_receipt_details')->where(['receipt_id'=>$result,'item_code'=>$receipt_detail_val['id']])->count() == 0){
        
                             $rece_detail = DB::table('sales_receipt_details')
                                          ->insertGetId([
                                            'receipt_id'            => $result,
                                            'item_code'             => $receipt_detail_val['id'],
                                            'total_qty'             => $receipt_detail_val['quantity'],
                                            'total_amount'          => $receipt_detail_val['totalAmount'],
                                            'item_name'             => $receipt_detail_val['name'],
                                            'item_price'            => $receipt_detail_val['price'],
                                            'calcu_amount_webcart'  => $receipt_detail_val['totalAmount'],
                                            'note'                  => $receipt_detail_val['instructions'] ?? null,
                                            'mode'                  => 'inventory-general',
                                            'discount_code'         => isset($receipt_detail_val['discount_code']) ? $receipt_detail_val['discount_code'] : 0 ,
                                            'discount_value'        => isset($receipt_detail_val['discount_value']) ? $receipt_detail_val['discount_value'] : 0,
                                            'actual_price'          => isset($receipt_detail_val['bdPrice']) ? $receipt_detail_val['bdPrice'] : 0,
                                            'rust'                  => isset($receipt_detail_val['rust']) ? $receipt_detail_val['rust'] : 0,
                                            'laundry_package_id'     => isset($receipt_detail_val['laundryPackage']) ? $receipt_detail_val['laundryPackage']['id'] : 0,
                                            'laundry_package_name'   => isset($receipt_detail_val['laundryPackage']) ? $receipt_detail_val['laundryPackage']['name'] : 0,
                                            'laundry_package_price'  => isset($receipt_detail_val['laundryPackage']) ? $receipt_detail_val['laundryPackage']['price'] : 0,
                                      ]);
        
                         // }             
                                // return 'result:'.($val['selectedVariation'] != null ? 1 : 2);
                                
                          if(isset($receipt_detail_val['selectedDeals']) && $receipt_detail_val['selectedDeals'] != null){
                              foreach($receipt_detail_val['selectedDeals'] as $dealVal){
                                   foreach($dealVal['values'] as $dealSbVal){
                                       $getRecipy = DB::table('recipy_general_test')->where(['product_id'=>$dealSbVal['product_id'],'status_id'=>1,'branch_id'=>$deliveryArea['branchId']])->first();
                                         $getDeal_ID =  DB::table('sales_receipt_details')
                                                          ->insertGetId([
                                                                        'receipt_id'        => $result,
                                                                        'item_code'         => $dealSbVal['product_id'],
                                                                        'total_qty'         => 1,
                                                                        'total_amount'      => 0,
                                                                        'item_price'        => 0,
                                                                        'item_name'         => $dealSbVal['name'],
                                                                        'parent_item_code'  => $rece_detail,
                                                                        'group_id'          => $dealVal['id'],
                                                                        'mode'              => 'deal-product',
                                                                        'recipy_id'         => ($getRecipy) ? $getRecipy->recipy_id : null
                                                                  ]); 
                                     if(isset($dealSbVal['addons']) && $dealSbVal['addons'] != null){                  
                                        foreach($dealSbVal['addons'] as $dealAddon_val){
                                            foreach($dealAddon_val['values'] as $Addon_val){
                                                $getRecipy = DB::table('recipy_general_test')->where(['product_id'=>$Addon_val['product_id'],'status_id'=>1,'branch_id'=>$deliveryArea['branchId']])->first();
                                                
                                                    DB::table('sales_receipt_details')
                                                      ->insert([
                                                                    'receipt_id'            => $result,
                                                                    'item_code'             => $Addon_val['product_id'],
                                                                    'total_qty'             => 1,
                                                                    'total_amount'          => $Addon_val['price'],
                                                                    'item_price'            => $Addon_val['price'],
                                                                    'item_name'             => $Addon_val['name'],
                                                                    'parent_item_code'      => $getDeal_ID,
                                                                    'group_id'              => $dealVal['id'],
                                                                    'addon_variation_id'    => $Addon_val['id'], 
                                                                    'mode'                  => 'deal-addon', 
                                                                    'recipy_id'             => ($getRecipy) ? $getRecipy->recipy_id : null
                                                              ]);
                                            }
                                        }
                                     }
                                                      
                                   }
                              }
                              
                          }
                                
                          if(isset($receipt_detail_val['selectedVariation']) && $receipt_detail_val['selectedVariation'] != null){
                               
                             // foreach($val['selectedVariation'] as $variat_prod){

                                                  
                                                //   if($getBranch->website_type != 'restaurant'){ //start condition variation retails setup (website type check != to restaurant)
                                                  
                                 $variableId = $receipt_detail_val['selectedVariation']['id'];
                               $rece_detail_prodVariat_ID = DB::table('sales_receipt_details')
                                                             ->insertGetId([
                                                        'receipt_id'         => $result,
                                                        'item_code'          => $receipt_detail_val['selectedVariation']['id'],
                                                        'item_name'          => $receipt_detail_val['selectedVariation']['name'],
                                                        'parent_item_code'   => $rece_detail, 
                                                        'mode'               => 'attribute',
                                                  ]);
                                                  
                                                      foreach($receipt_detail_val['selectedVariation']['values'] as $variationValues){
                                                              $rece_detail_variationValues_ID = DB::table('sales_receipt_details')
                                                                                                  ->insertGetId([
                                                                                                        'receipt_id'         => $result,
                                                                                                        'item_code'          => $variationValues['id'],
                                                                                                        'total_qty'          => $receipt_detail_val['quantity'],
                                                                                                        'total_amount'       => $variationValues['price'],
                                                                                                        'item_price'         => $variationValues['price'],
                                                                                                        'item_name'          => $variationValues['name'],
                                                                                                        'parent_item_code'   => $rece_detail_prodVariat_ID,
                                                                                                        'addon_variation_id' => $receipt_detail_val['selectedVariation']['id'],
                                                                                                        'mode'               => 'variable-product',
                                                                                                  ]);  
                                                                                                  
                                                              foreach($variationValues['subvariations'] as $variationSubValues){
                                                                              $rece_detail_variationSubValues_ID = DB::table('sales_receipt_details')
                                                                                                                  ->insertGetId([
                                                                                                                        'receipt_id'        => $result,
                                                                                                                        'item_code'         => $variationSubValues['id'],
                                                                                                                        'item_name'         => $variationSubValues['name'],
                                                                                                                        'parent_item_code'  => $rece_detail_variationValues_ID,
                                                                                                                        'mode'              => 'variation-attribute',
                                                                                                                  ]);
                                                              for($i=0;$i < count($variationSubValues['values']);$i++){
                                                                              DB::table('sales_receipt_details')
                                                                                          ->insertGetId([
                                                                                                            'receipt_id'            => $result,
                                                                                                            'item_code'             => $variationSubValues['values'][$i]['product_id'],
                                                                                                            'total_qty'             => 1,
                                                                                                            'total_amount'          => $variationSubValues['values'][$i]['price'],
                                                                                                            'item_price'            => $variationSubValues['values'][$i]['price'],
                                                                                                            'item_name'             => $variationSubValues['values'][$i]['name'],
                                                                                                            'parent_item_code'      => $rece_detail_variationSubValues_ID,   
                                                                                                            'addon_variation_id'    => $variationSubValues['id'], 
                                                                                                            'mode'                  => 'variation-product', 
                                                                                                  ]);  
                                                                                                                  
                                                                              
                                                                      }                                                                                   
                                                                                 
                                                                                                                  
                                                                              
                                                                      }                                                                                                  
                                                              
                                                      }
                                                      
                                                //   }
                                                
// else{
//                                  $variableId = $receipt_detail_val['selectedVariation']['id'];
//                               $rece_detail_prodVariat_ID = DB::table('sales_receipt_details')
//                                                              ->insertGetId([
//                                                         'receipt_id'         => $result,
//                                                         'item_code'          => $receipt_detail_val['selectedVariation']['id'],
//                                                         'item_name'          => $receipt_detail_val['selectedVariation']['name'],
//                                                         'parent_item_code'   => $rece_detail, 
//                                                         'mode'               => 'variable-product',
//                                                   ]);                                                      
                                                      
                                                      
// 									if(isset($request->operator)){
// 									 $extra_array = [
// 													  'receipt_id'                  =>$result,
// 													  'receipt_detail_prodVariatId' =>$rece_detail_prodVariat_ID,
// 													  'rece_detail'                 =>$rece_detail,
// 													  'itemCode'                    =>$itemCode,
// 													  'variableId'                  =>$variableId,
// 													  'branchId'                    =>$deliveryArea['branchId']
// 													];
// 									 $this->subvariation_create($receipt_detail_val['subVariations'],$extra_array);
// 									}
									
//                                   if(!isset($request->operator) && count($receipt_detail_val['selectedVariation']['subvariations']) != 0 && $rece_detail_prodVariat_ID != 0){
                                    
//                                         foreach($receipt_detail_val['selectedVariation']['subvariations'] as $subVariat){ 
//                                           for($v_i=0; $v_i < count($subVariat['values']); $v_i++){
                                               
//                                                 $variationId = $subVariat['id'];
//                                                   DB::table('sales_receipt_details')->insert([
//                                                         'receipt_id'            => $result,
//                                                         'item_code'             => $subVariat['values'][$v_i]['product_id'],
//                                                         'total_qty'             => 1,
//                                                         'total_amount'          => $subVariat['values'][$v_i]['price'],
//                                                         'item_price'            => $subVariat['values'][$v_i]['price'],
//                                                         'item_name'             => $subVariat['values'][$v_i]['name'],
//                                                         'parent_item_code'      => $rece_detail_prodVariat_ID,   
//                                                         'addon_variation_id'    => $subVariat['id'], 
//                                                         'mode'                  => 'variation-product', 
//                                                   ]);
                                                  
//                                                   $getRecipy = DB::table('recipy_general_test')->where(['product_id'=>$itemCode,'variation_id'=>$variableId,'sub_variation_id'=>$variationId,'status_id'=>1,'branch_id'=>$deliveryArea['branchId']])->first();
//                                                   if($getRecipy){
//                                                       DB::table('sales_receipt_details')->where('receipt_detail_id',$rece_detail)->update(['recipy_id'=>$getRecipy->recipy_id]);
//                                                   }
//                                           }
//                                       }                                             
                                            
                                     
//                                  }else{
//                                      $getRecipy = DB::table('recipy_general_test')->where(['product_id'=>$itemCode,'variation_id'=>$variableId,'status_id'=>1,'branch_id'=>$deliveryArea['branchId']])->first();
//                                                   if($getRecipy){
//                                                       DB::table('sales_receipt_details')->where('receipt_detail_id',$rece_detail)->update(['recipy_id'=>$getRecipy->recipy_id]);
//                                                   }
//                                  }
                                 
//                                                   } // close else condition website type                                                 
                           }  
        
        
                        if(isset($receipt_detail_val['selectedAddons']) && count($receipt_detail_val['selectedAddons']) != 0 ){
                         foreach($receipt_detail_val['selectedAddons'] as $addon){
                          foreach($addon['values'] as $addon_val){
                              $getRecipy = DB::table('recipy_general_test')->where(['product_id'=>$addon_val['product_id'],'status_id'=>1,'branch_id'=>$deliveryArea['branchId']])->first();
                              
                                      DB::table('sales_receipt_details')->insert([
                                            'receipt_id'            => $result,
                                            'item_code'             => $addon_val['product_id'],
                                            'total_qty'             => 1,
                                            'total_amount'          => $addon_val['price'],
                                            'item_price'            => $addon_val['price'],
                                            'item_name'             => $addon_val['name'],
                                            'parent_item_code'      => $rece_detail,   
                                            'addon_variation_id'    => $addon_val['id'], 
                                            'mode'                  => 'addon', 
                                            'recipy_id'             => ($getRecipy) ? $getRecipy->recipy_id : null
                                      ]);
                            }      
                         }
                       }                      
        
                    } 
                 }
                    
                      $data_pushNotification = ['websiteName'=>$checkWebSite->name,'orderId'=>$result,'terminalId'=>$getBranch->terminal_id];
                     
                      $this->sendPushNotificationForPermission($data_pushNotification); 
                      // $this->sentWhatsAppOTP($request->phNumber,$checkWebSite->url.'order/'.$random_orderId);    
        
        
                      // $url = $checkWebSite->url.'/order-status/'.$custId.'/'.$random_orderId; 
                      $url = $random_orderId; 
                      
        
                      // $sentData = $random_orderId." of PKR.".$request->cart_data['totalAmount']." placed with ".$checkWebSite->name." will be ready in ".$checkWebSite->order_estimate_time."mins Payment by Cash on Delivery. ".$url;
        
                      // $sentData = [
                                    // "msgOne"=>$random_orderId." of PKR.".$request->cart_data['totalAmount'],
                                    // "msgTwo"=>$checkWebSite->name,
                                    // "msgThree"=>$checkWebSite->order_estimate_time."mins Payment by Cash on Delivery. ".$url
                                  // ];
								  
					// number normalize
					$phone = $customer['phNumber'];
					$phone = preg_replace('/\D/', '', $customer['phNumber']);   // sirf digits
					if (str_starts_with($phone, '0')) {
						$phone = '92' . substr($phone, 1);        // 0311... → 92311...
					} elseif (!str_starts_with($phone, '92')) {
						$phone = '92' . $phone;                    // 311... → 92311...
					}
					$sentData = [
						"msgOne"   => $random_orderId,
						"msgTwo"   => $request->cart_data['totalAmount'],
						"msgThree" => $checkWebSite->name,
						"msgFour"  => $checkWebSite->order_estimate_time ?: '30',
						"msgFive"  => "Cash on Delivery",
					];
                      
                       $this->sentWhatsAppMessageOrderTrack($phone,$checkWebSite->name,$sentData,$url);
                   }
        
                
                  DB::commit(); 
               return response()->json(['orderId'=>$random_orderId,'customerId'=>$custId],200);
            }catch(\Exception $e){
        
                    DB::rollback();
                    return response()->json('Server Issue: '.$e->getMessage().' Line Number : '.$e->getLine(),500);        
                
            }
    }
    
    public function subvariation_create($subvariations,$extra_array){
        foreach($subvariations as $value){ 
           for($v_i=0; $v_i < count($value['values']); $v_i++){
               
                $variationId = $value['id'];
                  DB::table('sales_receipt_details')->insert([
                        'receipt_id'            => $extra_array['receipt_id'],
                        'item_code'             => $value['values'][$v_i]['product_id'],
                        'total_qty'             => 1,
                        'total_amount'          => $value['values'][$v_i]['price'],
                        'item_price'            => $value['values'][$v_i]['price'],
                        'item_name'             => $value['values'][$v_i]['name'],
                        'parent_item_code'      => $extra_array['receipt_detail_prodVariatId'],   
                        'addon_variation_id'    => $value['id'], 
                        'mode'                  => 'variation-product', 
                  ]);
                  
                 /* $getRecipy = DB::table('recipy_general_test')->where(['product_id'=>$extra_array['itemCode'],'variation_id'=>$extra_array['variableId'],'sub_variation_id'=>$variationId,'status_id'=>1,'branch_id'=>$extra_array['branchId']])->first();
                  if($getRecipy){
                      DB::table('sales_receipt_details')->where('receipt_detail_id',$extra_array['rece_detail'])->update(['recipy_id'=>$getRecipy->recipy_id]);
                  }*/
           }
       }  
    }


    public function random_orderId_generate(){
      $id = \Str::random(9);
      if(DB::table('sales_receipts')->where('url_orderid',$id)->count() > 0){
         $id = \Str::random(9);
      }
      return $id;
    }

    public function orderDetails(Request $request){
        
        if(isset($request->custid)){
            // $webId =  Crypt::decryptString($request->webid);
            
											$getRecord = DB::table('sales_receipts')
                                             ->join('sales_account_subdetails','sales_account_subdetails.receipt_id','sales_receipts.id')
                                             ->join('sales_order_status','sales_order_status.order_status_id','sales_receipts.status')
                                             ->join('sales_order_mode','sales_order_mode.order_mode_id','sales_receipts.order_mode_id')
                                             ->join('customers','customers.id','sales_receipts.customer_id')
                                             ->join('website_details','website_details.id','sales_receipts.website_id')
                                             ->leftJoin('booking_slots as pickupslots','pickupslots.id','sales_receipts.pickup_slot')
                                             ->leftJoin('booking_slots as deliveryslots','deliveryslots.id','sales_receipts.delivery_slot')
                                             ->select('sales_receipts.*',
												'pickupslots.start_time as pickup_start_time',
												'pickupslots.end_time as pickup_end_time',
												'deliveryslots.start_time as delivery_start_time',
												'deliveryslots.end_time as delivery_end_time',
												'sales_order_status.order_status_name as status_name','website_details.order_estimate_time','website_details.type as website_type','sales_account_subdetails.discount_amount','sales_account_subdetails.discount_percentage','sales_order_mode.order_mode')
                                             ->where('sales_receipts.url_orderid',$request->orderid)
                                             ->where('sales_receipts.customer_id',$request->custid)->first();            
        }else{
        
											$getRecord = DB::table('sales_receipts')
                                             ->join('sales_account_subdetails','sales_account_subdetails.receipt_id','sales_receipts.id')
                                             ->join('sales_order_status','sales_order_status.order_status_id','sales_receipts.status')
                                             ->join('sales_order_mode','sales_order_mode.order_mode_id','sales_receipts.order_mode_id')
                                             ->join('website_details','website_details.id','sales_receipts.website_id')
											 ->leftJoin('booking_slots as pickupslots','pickupslots.id','sales_receipts.pickup_slot')
                                             ->leftJoin('booking_slots as deliveryslots','deliveryslots.id','sales_receipts.delivery_slot')
                                             ->select('sales_receipts.*',
												'pickupslots.start_time as pickup_start_time',
												'pickupslots.end_time as pickup_end_time',
												'deliveryslots.start_time as delivery_start_time',
												'deliveryslots.end_time as delivery_end_time',
												'sales_order_status.order_status_name as status_name','website_details.order_estimate_time','website_details.type as website_type','sales_account_subdetails.discount_amount','sales_account_subdetails.discount_percentage','sales_order_mode.order_mode')
                                             ->where('sales_receipts.url_orderid',$request->orderid)->first();
        }
        
        if($getRecord == null){
            return response()->json('Record not found!',500);
        }                                     

        $resp = new SalesReceiptsResource($getRecord);

        return response()->json($resp,200);
    }
	
	public function posOrderDetails(Request $request)
	{
        
		$getRecord = DB::table('sales_receipts')
		 ->join('sales_account_subdetails','sales_account_subdetails.receipt_id','sales_receipts.id')
		 ->join('sales_order_status','sales_order_status.order_status_id','sales_receipts.status')
		 ->join('sales_order_mode','sales_order_mode.order_mode_id','sales_receipts.order_mode_id')
		 ->leftJoin('booking_slots as pickupslots','pickupslots.id','sales_receipts.pickup_slot')
		 ->leftJoin('booking_slots as deliveryslots','deliveryslots.id','sales_receipts.delivery_slot')
		 ->leftJoin('sales_payment as payment','payment.payment_id','sales_receipts.payment_id')
		 ->select('sales_receipts.*',
			'pickupslots.start_time as pickup_start_time',
			'pickupslots.end_time as pickup_end_time',
			'deliveryslots.start_time as delivery_start_time',
			'deliveryslots.end_time as delivery_end_time',
			'sales_order_status.order_status_name as status_name','sales_account_subdetails.discount_amount',
			'sales_account_subdetails.srb',
			'sales_account_subdetails.discount_percentage','sales_order_mode.order_mode','payment.payment_mode')
		 ->where('sales_receipts.receipt_no',$request->orderid)->first();
        

        
        if($getRecord == null){
            return response()->json('Record not found!',500);
        }                                     

        $resp = new PosSalesDetailsResource($getRecord);

        return response()->json($resp,200);
    }

    public function website_customerLogin(Request $request){

      $otpGenerate = mt_rand(100000, 999999);
      
          if(empty($request->phNumber)){
              return response()->json('Invalid phone number!',500);
          } 

         // $checkBranch = DB::table('branch')->join('website_branches','website_branches.branch_id','branch.branch_id')
         //                ->where(['website_branches.website_id'=>$request->webId,'website_branches.branch_id'=>$request->branchId])
         //                ->select('branch.*')
         //                ->first();

         //  if($checkBranch == null){
         //      return response()->json('Invalid branch!',500);
         //  }               

          if(DB::table('customers')->where(['mobile'=>$request->phNumber,'website_id'=>$request->webId])->count() == 0){

                DB::table('customers')
                  ->insert([
                            'website_id'  => $request->webId,
                            'status_id'   => 1,
                            'country_id'  => isset($checkBranch->country_id) ? $checkBranch->country_id : 170,
                            'city_id'     => isset($checkBranch->city_id) ? $checkBranch->city_id : 170,
                            'name'        => $request->fullName,
                            'mobile'      => $request->phNumber,
                            'online'      => 1
                            ]);

          }

          

      if(DB::table('customers')->where(['mobile'=>$request->phNumber,'website_id'=>$request->webId,'otp'=>$otpGenerate])->count() != 0){
           $otpGenerate = mt_rand(100000, 999999);
      }

      if(DB::table('customers')
            ->where(['mobile'=>$request->phNumber,'website_id'=>$request->webId])
            ->update(['otp'=>$otpGenerate])){
                
              $getWebsite = DB::table('website_details')->join('website_theme','website_theme.website_id','website_details.id')
                                                          ->select('website_details.*','website_theme.otp_whatsapp_msg','website_theme.otp_msg')
                                                          ->where('website_details.id',$request->webId)
                                                          ->first();                

            // $data = [
            //           'number'       => $request->phNumber,
            //           'companyName'  => 'JaniBiryani',
            //           'customerName' => 'URS',
            //           'receiptNo'    => date("Ymd"),
            //           'receiptDate'  => date("Ymd"),
            //           'amount'       => $otpGenerate
            //         ];

             if($getWebsite->otp_whatsapp_msg == 1){
                 $this->sentWhatsAppOTP($request->phNumber,$otpGenerate);
             }
    
             if($getWebsite->otp_msg == 1){
                 $this->send_otp_sms($request->phNumber,$otpGenerate);
             }
		 
		  if($request->mode == "android"){
			 return response()->json(['status' => 200,"otp" => $otpGenerate]);
		  }
          return response()->json('success',200);
      }else{
          return response()->json('Error! Server Issue',500);
      }

        
    }

    public function website_verify_otp_by_cust(Request $request){

       if(empty($request->phNumber) && empty($request->otp)){
           return response()->json('Error! OTP number not found',500);
       }

       if(DB::table('customers')->where(['mobile'=>$request->phNumber,'otp'=>$request->otp,'website_id'=>$request->webId])->count() == 1){
          return response()->json('success',200);
       }else{
          return response()->json('Error! Invalid otp number',500);
       }
    }
    
    public function customerRecord_get(Request $request){
        
        if(!isset($request->webId) || !isset($request->mobile)){
            return response()->json('Invalid Customer',500);
            // exist();
        }        
        
        
        if(empty($request->webId) || empty($request->mobile)){
            return response()->json('Invalid Customer',500);
            // exist();
        }
        
        $orders    = DB::table('sales_receipts')
                        ->join('customers','customers.id','sales_receipts.customer_id')
                        ->join('sales_order_status','sales_order_status.order_status_id','sales_receipts.status')
                        ->select('sales_receipts.url_orderid','sales_receipts.customer_id','sales_receipts.actual_amount','sales_receipts.total_amount','sales_receipts.total_item_qty',
                        'sales_receipts.date','sales_receipts.time','sales_order_status.order_status_name as status_name','sales_receipts.status as status_id')
                        ->where(['customers.mobile'=>$request->mobile,'customers.website_id'=>$request->webId])
                        ->orderBy('sales_receipts.id','DESC')
                        ->get();
        
        $addresses  = DB::table('customer_addresses')
                        ->join('customers','customers.id','customer_addresses.customer_id')
                        ->leftJoin('city','city.city_id','customer_addresses.city_id')
                        // ->leftJoin('website_delivery_areas','website_delivery_areas.city','customer_addresses.city_id')
						->leftJoin('website_delivery_areas', function ($join) use ($request) {
							$join->on('website_delivery_areas.city', '=', 'customer_addresses.city_id')
								 ->where('website_delivery_areas.website_id', '=',$request->webId );
						})
                        ->select('customers.id as cust_id','customer_addresses.id','customer_addresses.city_id','customer_addresses.address','customer_addresses.landmark','city.city_name','website_delivery_areas.id as delivery_id','website_delivery_areas.branch_id','website_delivery_areas.charge','website_delivery_areas.min_order','website_delivery_areas.estimate_of_days')
                        ->where(['customers.mobile'=>$request->mobile,'customers.website_id'=>$request->webId,'customer_addresses.status'=>1])
                        ->orderBy('customer_addresses.id','DESC')
                        ->get();
                        
       return response()->json(['orders'=>$orders,'addresses'=>$addresses]);                
    }
    
    public function save_CustomerAddress(Request $request){
        
        if(empty($request->webid) || empty($request->mobile)){
            return response()->json('Invalid values',500);
        }
        
        $customer = DB::table('customers')
                           ->where([
                                     'mobile'=>$request->mobile,
                                     'website_id'=>$request->webid
                                   ])
                           ->select('id')
                           ->first();

        if(DB::table('customer_addresses as tbl_address')
                           ->where([
                                     'tbl_address.address'=>$request->address,
                                     'tbl_address.landmark'=>$request->landmark,
                                     'tbl_cust.mobile'=>$request->mobile,
                                     'tbl_cust.website_id'=>$request->webid
                                   ])
                           ->join('customers as tbl_cust','tbl_cust.id','tbl_address.customer_id')
                           ->count() > 0){
            
            return response()->json('This address already taken',500);
        } 
       
       return DB::table('customer_addresses')
                 ->insert([
                      'customer_id' => $customer->id,
                      'address'     => $request->address,
                      'landmark'    => $request->landmark,
                   ]) ? response()->json('Success!',200) :response()->json('Error! address not saved. Server Issue!',500); 
        
    }
    
    public function removeCustomerAddress(Request $request){

        if(empty($request->webId) || empty($request->mobile) || empty($request->address_id)){
            return response()->json('Invalid Customer',500);
            // exist();
        } 
        
        return  DB::table('customer_addresses')
                        ->join('customers','customers.id','customer_addresses.customer_id')
                        ->select('customer_addresses.id','customer_addresses.address','customer_addresses.landmark')
                        ->where(['customers.mobile'=>$request->mobile,'customers.website_id'=>$request->webId,'customer_addresses.status'=>1,'customer_addresses.id'=>$request->address_id])
                        ->update(['customer_addresses.status'=>0]) ? response()->json('success',200) : response()->json('Error! Server isset',500);        
        
    }
    
    public function show_video_website(Request $request){
         $mode      = $request->mode;
         // $compId    = $request->compId;
         $filename  = $request->filename;  
         // =====================================================
         $extension  = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
         $headerType = 'video/'.$extension;
         $path       = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/video/products/';
        // =====================================================   
        
               // Error video format
        // ===================================================== 
         if(!in_array($extension,['ogg','mp4','webm'])){
            return response()->json('Invalid video format!',500);
         }
       // =====================================================          
        
        
        if($mode == 'prod'){
            $path .= $filename;
        }else{
           $extension  = 'png';
           $filename   = 'no-image.png';
           $headerType = 'image/'.$extension; 
           $path       = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/'.$filename;            
        }
        
        if(!\File::exists($path)){
           $extension  = 'png';
           $filename   = 'no-image.png';
           $headerType = 'image/'.$extension; 
           $path       = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/'.$filename;
        }
        
        
        
        $headers = array(
                          'Content-Type'        => $headerType,
                          'Content-Description' => $filename,
                          'Cache-Control'       => 'public, max-age=604800',
                        ); 

      return response()->file($path, $headers);         
    }
    
    public function video_slider(Request $request){
         $mode      = $request->mode;
         // $compId    = $request->compId;
         $filename  = $request->filename;
         $webid     = $request->webid != null ? explode('-',$request->webid) : null;
         // =====================================================
         $extension = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
         $path = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/';
        // =====================================================

               // Error file format
        // ===================================================== 
         if(!in_array($extension,['ogg','mp4','webm'])){
            return response()->json('Invalid file format!',500);
         }
       // =====================================================  
       
        if($mode == 'slider'){
             if($webid == null){
                $extension = 'png';
                $filename  = 'no-image.png';
                $path     .= $filename;
                $headers = array(
                                  'Content-Type'        => 'image/'.$extension,
                                  'Content-Description' => $filename,
                                  'Cache-Control'       => 'public, max-age=604800',
                                ); 

                return response()->file($path, $headers);                                                   
             }

           $path .= 'website/sliders/'.$webid[0].'/'.$webid[1].'/'.$filename;
            
        } 
        
        if(!\File::exists($path)){
           $extension = 'png';
           $filename  = 'no-image.png';
           $path      = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/'.$filename;

        }
        
        // MIME type set karein (video)
        $file = \File::get($path);
        $mimeType = File::mimeType($path);

        return \Response::make($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"', // Inline to play the video
            'Content-Length' => filesize($path),
        ]);        

    //     $headers = array(
    //                       'Content-Type'        => 'image/'.$extension,
    //                       'Content-Description' => $filename,
    //                       'Cache-Control'       => 'public, max-age=604800',
    //                     ); 

    //   return response()->file($path, $headers);         
        
    }


    public function show_image_website(Request $request){

         $mode      = $request->mode;
         // $compId    = $request->compId;
         $filename  = $request->filename;
         $webid     = $request->webid != null ? explode('-',$request->webid) : null;
         // =====================================================
         $extension = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
         $path = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/';
        // =====================================================

               // Error image format
        // ===================================================== 
         if(!in_array($extension,['jpeg','jpg','png','webp'])){
            return response()->json('Invalid image format!',500);
         }
       // =====================================================  
       
        if($mode == 'slider'){
             if($webid == null){
                $extension = 'png';
                $filename  = 'no-image.png';
                $path     .= $filename;
                $headers = array(
                                  'Content-Type'        => 'image/'.$extension,
                                  'Content-Description' => $filename,
                                  'Cache-Control'       => 'public, max-age=604800',
                                ); 

                return response()->file($path, $headers);                                                   
             }

           $path .= 'website/sliders/'.$webid[0].'/'.$webid[1].'/'.$filename;
            
        }elseif($mode == 'advertisement'){

             if($webid == null){
                $extension = 'png';
                $filename  = 'no-image.png';
                $path     .= $filename;
                $headers = array(
                                  'Content-Type'        => 'image/'.$extension,
                                  'Content-Description' => $filename,
                                  'Cache-Control'       => 'public, max-age=604800',
                                ); 

                return response()->file($path, $headers);                                                   
             }

            $path .= 'website/advertisements/'.$webid[0].'/'.$webid[1].'/'.$filename;
        }elseif($mode == 'review'){
            $path .= 'customer-reviews/'.$filename;
        }elseif($mode == 'department'){
            $path .= 'department/'.$filename;
        }elseif($mode == 'prod'){
            $path .= 'products/'.$filename;
        }elseif($mode == 'prodvariation'){

             if($webid == null){
                $extension = 'png';
                $filename  = 'no-image.png';
                $path     .= $filename;
                $headers = array(
                                  'Content-Type'        => 'image/'.$extension,
                                  'Content-Description' => $filename,
                                  'Cache-Control'       => 'public, max-age=604800',
                                ); 

                return response()->file($path, $headers);                                                   
             }

            $path .= 'variation-product/'.$webid[0].'/'.$filename;
        }elseif($mode == 'tag'){
            $path .= 'tags/'.$filename;
        }elseif($mode == 'brand'){

             if($webid == null){
                $extension = 'png';
                $filename  = 'no-image.png';
                $path     .= $filename;
                $headers = array(
                                  'Content-Type'        => 'image/'.$extension,
                                  'Content-Description' => $filename,
                                  'Cache-Control'       => 'public, max-age=604800',
                                ); 

                return response()->file($path, $headers);                                                   
             }

            $path .= 'brands/'.$webid[0].'/'.$filename;
        }elseif($mode == 'testimonial'){
            $path .= 'testimonials/'.$filename;
        }else{
            $path .= 'website/'.$filename;
        }

        if(!\File::exists($path)){
           $extension = 'png';
           $filename  = 'no-image.png';
           $path      = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/'.$filename;

        }

        $headers = array(
                          'Content-Type'        => 'image/'.$extension,
                          'Content-Description' => $filename,
                          'Cache-Control'       => 'public, max-age=604800',
                        ); 

      return response()->file($path, $headers); 
    }


    public function sendEmail(Request $request){
      return response()->json(['state'=>500,'msg'=>'']);
    }
	
  public function productResponse(Request $request)
  {

        $variation = DB::table('variations')->where('company_id',92)->get();       

        // $products = testResource::collection($variation,1);
        $products = new testResource($variation,1);    

    // return DB::table("product_variable_relation")->join("product_variable_details","product_variable_details.variable_id","=","product_variable_relation.id")->whereIn("product_variable_relation.variation_id",DB::table("variations")->where("parent",10)->pluck("id"))->get();
    // $singleProduct = [];
    // $variationsArray= [];
    // // $products = DB::table("inventory_general")->where("company_id",92)->get();
    // $products = Inventory::with("addons","addons.category","addons.category.addons","variations","apiprice")
    //              ->whereIn('id',WebsiteProduct::where("website_id",25)->pluck("inventory_id"))
  //                                ->get();
    //             // return $products;
    // return ProductResource::collection($products);
    // // return DB::table("variation_test")->whereIn("id",DB::table("variation_test")->whereIn("id",DB::table("product_variable_relation")->where("prod_variable_id",589)->pluck("variation_id"))->groupBy("parent")->pluck("parent"))->get();
    // // return DB::table("variation_test")->whereIn("parent",DB::table("variation_test")->whereIn("id",)->groupBy("parent")->pluck("parent"))->get()
    // $variations = DB::table("pos_products_gen_details")->where("product_id",820996)->get();
    
    // foreach($products as $product){
    //  $singleProduct = [
    //    "id" => $product->id,
    //    "name" => $product->product_name,
    //  ];
      
    // }
    // foreach($variations as $variation){
    //    $variation = [
    //      "pos_item_id" => $variation->pos_item_id,
    //      "item_name" => $variation->item_name,
    //    ];
    //  array_push($singleProduct,$variation);
    // }
    
    return response()->json($products);
  }


public function sentWhatsAppMessage($request){

  $number = $request['number'];
  $companyName = $request['companyName'];
  $customerName = $request['customerName'];
  $receiptNo = $request['receiptNo'];
  $receiptDate = $request['receiptDate'];
  $amount = $request['amount'];

  $number =  $number;//$_GET['number'];
  $version =  "v15.1";//$_GET['version'];
  $phoneId = "105420688989582";//$_GET['phoneId'];
  $template_name = "receipt";//$_GET['template'];
  $code = "en_US";//$_GET['code'];
  $bearer = "EAASlFdcyGIsBO5VIwpZCPV759TMB7HQDAoKPqZAX7cwdX2NcvpSb6e0urQJMRy9WzLGroBZC5KfgX2ZBsmoSmWZBzXZArEWk0o32zReVccSBswVWX5WjzWU8ZCTZBRmAiX0TX0ouZBbQNSmJxSFJzv2ZC5AJgN9DbGEEDOwP9lMsIZAmP0ZAdnzQaJUfAa6G1yJCTY3xj2N1fXUcajzNjOCf";//$_GET['bearer'];
  
  // $orderDetails = $GLOBALS['crud']->runQuery("SELECT a.id,a.receipt_no,a.total_amount,a.date,b.branch_name,c.name as company_name,d.name as customer_name FROM sales_receipts a INNER JOIN branch b on b.branch_id = a.branch INNER JOIN company c on c.company_id = b.company_id INNER Join customers d on d.id = a.customer_id where receipt_no = $receiptNo");
  
  $myArray = [];
  $insideArray = [];
  
  array_push($insideArray, (object)[
        'type' => 'text',
        'text' => 'URS',
  ]);
  array_push($myArray, (object)[
        'type' => 'header',
        'parameters' => $insideArray,
  ]);
  
  $insideArray = [];
  
  array_push($insideArray, (object)[
        'type' => 'text',
        'text' => "customer_name",
  ]);
  
  array_push($insideArray, (object)[
        'type' => 'text',
        'text' => "receipt_no",
  ]);
  
  array_push($insideArray, (object)[
        'type' => 'text',
        'text' => "date",
  ]);
  array_push($insideArray, (object)[
        'type' => 'text',
        'text' => "total_amount",
  ]);
  
  array_push($insideArray, (object)[
        'type' => 'text',
        'text' => "id",
  ]);
  
  array_push($insideArray, (object)[
        'type' => 'text',
        'text' => " Branch",
  ]);
  
  array_push($myArray, (object)[
        'type' => 'body',
        'parameters' => $insideArray,
  ]);
  
  $insideArray = [];
  array_push($insideArray, (object)[
        'type' => 'payload',
        'payload' => $receiptNo,
  ]);
  array_push($myArray, (object)[
        'type' => 'button',
    "sub_type" => "URL",
    "index" => "0",
        'parameters' => $insideArray,
  ]);
  
  $template = [
    "name" => $template_name,
    "language" => [
      "code" =>  $code,
    ],
    "components" => $myArray,
  ];
  $myObj   = array();
    $myObj['messaging_product'] =   "whatsapp";
    $myObj['to'] = $number; //"923452670301",
    $myObj["type"] =  "template";
    $myObj["template"] = $template;
  $myobject    = json_encode($myObj);
  
  $url = "https://graph.facebook.com/v17.0/103444702767558/messages";
  $authorization = "Authorization: Bearer ".$bearer; //EAAIz8ObxruYBANXiBwoBzm56FIFHLnBZBnJHuHosRRM1fr8Iu7wZB9iUioYjl2yDQGyTCIhm3RsikAUpl19b82qOxtfBOtyOMLlMmZCCijn5Nc2cx1UuzkRsHnla9XZBiFwAHSrckepNlSY5ngYyiIZCp3VnZCEQ94kAkVsC8DtgyMKYEjsIhNRJjm6vQjQpzIWCgkjBwZCBQZDZD
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/json",$authorization));
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $myobject);
  $result = curl_exec($curl);
  $outPut = json_decode($result, true);
  if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
  }
  if (isset($error_msg)) {
    print_r($error_msg);
  }
  
  $result = json_decode($result);
  print_r($result);
  // echo $result["messages"];
}


public function sendPushNotificationForPermission($array){ 
    
    $message = 'OID'.$array['orderId'];
    $body    = "Order from ".$array['websiteName'];
    $tokens  = array();
    // $result = DB::select("SELECT branch_name,b.name as company FROM `branch` INNER Join company b on b.company_id = branch.company_id where branch.company_id = ? and branch_id = ?",[session("company_id"),session("branch")]);
    //$title = "Item On/Off";
        $firebaseToken = DB::table("terminal_details")->where("terminal_id",$array['terminalId'])->whereNotNull("device_token")->get("device_token");//["cZIiT3EPTAKce8s8lPHTkZ:APA91bH0a0zModJDvMjwLmeMIqHNfyLriX1m2EWV9BI157KY6DtxsfWPDo-mYjl-Qh92dyfjU0Q0BM_HeXykZp6xy3LxoOxZmeLIxyBTimnfsCVIOuM0PBE8j53EV-_AWi6CVMOJIDMH"];//User::whereNotNull('device_token')->pluck('device_token')->all();
    // return $firebaseToken;
    foreach($firebaseToken as $token){
      array_push($tokens,$token->device_token);
    }
    

    $SERVER_API_KEY = 'AAAATXdhnIk:APA91bHFZZbCubOgnG3dihDVsqFbwGGQaBpC6f7BPFMnvpntpOOY88ysAEVAT2puQvdng3Xkd8j4HNVWFp1FQ2rHEe9g3Cv6nSZ7oeMsQtSh2GrJYNIxGHeogmen7TSPqRWHJxrG4QF_';

        $server_api_key_mobile = 'AAAA2dlOr6s:APA91bHGDpYDSZWI0LotnIYZUTpOTA9lLS56jsyB-2hq6Fsq6l0OPBoMYFqePTAbteVFawWzdyZOfMowMf-j8LBL8xJefdnpb_pZRVQHzu5rXykkdLBfPJgcr8gmPhPBDlXMWJy_-uv2';   
       
        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => "Online Order",
                "body" => $body,
        "icon" => "https://retail.sabsoft.com.pk/assets/images/Sabify72.png",
                "content_available" => true,
                "priority" => "high",
        // "click_action" => ,
            ],
      "data" => [
        "par1" => $message,
      ],
        ]; 
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $headers1 = [
            'Authorization: key=' . $server_api_key_mobile,
            'Content-Type: application/json',
        ];

        $chs = curl_init();

        curl_setopt($chs, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($chs, CURLOPT_POST, true);
        curl_setopt($chs, CURLOPT_HTTPHEADER, $headers1);
        curl_setopt($chs, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($chs, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chs, CURLOPT_POSTFIELDS, $dataString);

        $responseOne = curl_exec($chs);
        $response = curl_exec($ch);
		// print_r($responseOne);
		// echo "</br>";
		// print_r($response);
		// echo "</br>";
    // return json_encode($responseOne).json_encode($response);
  }



function sentWhatsAppOTP($number,$OTPcode){
  $number =  $number;//$_GET['number'];
  $version =  "v15.1";//$_GET['version'];
  $phoneId = "105420688989582";//$_GET['phoneId'];
  $template_name = "otp";//$_GET['template'];
  $code = "en";//$_GET['code'];
  $bearer = "EAASlFdcyGIsBO5VIwpZCPV759TMB7HQDAoKPqZAX7cwdX2NcvpSb6e0urQJMRy9WzLGroBZC5KfgX2ZBsmoSmWZBzXZArEWk0o32zReVccSBswVWX5WjzWU8ZCTZBRmAiX0TX0ouZBbQNSmJxSFJzv2ZC5AJgN9DbGEEDOwP9lMsIZAmP0ZAdnzQaJUfAa6G1yJCTY3xj2N1fXUcajzNjOCf";//$_GET['bearer'];
  
  $myArray = [];
  $insideArray = [];
  
  array_push($insideArray, (object)[
        'type' => 'text',
        'text' => $OTPcode,
  ]);
  
  array_push($myArray, (object)[
        'type' => 'body',
        'parameters' => $insideArray,
  ]);
  
  $insideArray = [];
  array_push($insideArray, (object)[
        'type' => 'text',
        'text' => $OTPcode,
  ]);
  array_push($myArray, (object)[
        'type' => 'button',
    "sub_type" => "URL",
    "index" => "0",
        'parameters' => $insideArray,
  ]);
  
  $template = [
    "name" => $template_name,
    "language" => [
      "code" =>  $code,
    ],
    "components" => $myArray,
  ];
  
  $myObj   = array();
    $myObj['messaging_product'] =   "whatsapp";
    $myObj['to'] = $number; //"923452670301",
    $myObj["type"] =  "template";
    $myObj["template"] = $template;
  $myobject    = json_encode($myObj);

  $url = "https://graph.facebook.com/v17.0/103444702767558/messages";
  $authorization = "Authorization: Bearer ".$bearer; //EAAIz8ObxruYBANXiBwoBzm56FIFHLnBZBnJHuHosRRM1fr8Iu7wZB9iUioYjl2yDQGyTCIhm3RsikAUpl19b82qOxtfBOtyOMLlMmZCCijn5Nc2cx1UuzkRsHnla9XZBiFwAHSrckepNlSY5ngYyiIZCp3VnZCEQ94kAkVsC8DtgyMKYEjsIhNRJjm6vQjQpzIWCgkjBwZCBQZDZD
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/json",$authorization));
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $myobject);
    $result = curl_exec($curl);
    $outPut = json_decode($result, true);
    if (curl_errno($curl)) {
      $error_msg = curl_error($curl);
    }
    if (isset($error_msg)) {
      print_r($error_msg);
    }
    
    //print_r($result);
    // echo $result["messages"];
}  

/*public function sentWhatsAppMessageOrderTrack($number,$header,$message,$url){
// public function sentWhatsAppMessageOrderTrack(Request $request){
  $number  =  $number;//$_GET['number'];
  $version =  "v15.1";//$_GET['version'];
  $phoneId = "105420688989582";//$_GET['phoneId'];
  $template_name = "product_description";//$_GET['template'];
  $code = "en_US";//$_GET['code'];
  $bearer = "EAASlFdcyGIsBO5VIwpZCPV759TMB7HQDAoKPqZAX7cwdX2NcvpSb6e0urQJMRy9WzLGroBZC5KfgX2ZBsmoSmWZBzXZArEWk0o32zReVccSBswVWX5WjzWU8ZCTZBRmAiX0TX0ouZBbQNSmJxSFJzv2ZC5AJgN9DbGEEDOwP9lMsIZAmP0ZAdnzQaJUfAa6G1yJCTY3xj2N1fXUcajzNjOCf";//$_GET['bearer'];
  
  // $orderDetails = $GLOBALS['crud']->runQuery("SELECT a.id,a.receipt_no,a.total_amount,a.date,b.branch_name,c.name as company_name,d.name as customer_name FROM sales_receipts a INNER JOIN branch b on b.branch_id = a.branch INNER JOIN company c on c.company_id = b.company_id INNER Join customers d on d.id = a.customer_id where receipt_no = $receiptNo");
  
  $myArray = [];
  $insideArray = [];
  array_push($insideArray, (object)[
        'type' => 'text',
        'text' => $header,
  ]);
  array_push($myArray, (object)[
        'type' => 'header',
        'parameters' => $insideArray,
  ]);
  
  $insideArray = [];
  // array_push($insideArray, (object)[
        // 'type' => 'text',
        // 'text' => $request->message,
  // ]);
  array_push($insideArray, (object)[
        'type' => 'text',
        'text' => $message["msgOne"],
  ]);
  array_push($insideArray, (object)[
        'type' => 'text',
        'text' => $message["msgTwo"],
  ]);
  array_push($insideArray, (object)[
        'type' => 'text',
       'text' => $message["msgThree"],
  ]);
  array_push($myArray, (object)[
        'type' => 'body',
        'parameters' => $insideArray,
  ]);
  
  $insideArray = [];
  array_push($insideArray, (object)[
        'type' => 'payload',
        'payload' => $url,
  ]);
  array_push($myArray, (object)[
    'type' => 'button',
    "sub_type" => "URL",
    "index" => "0",
    'parameters' => $insideArray,
  ]);
  
  $template = [
    "name" => $template_name,
    "language" => [
      "code" =>  $code,
    ],
    "components" => $myArray,
  ];
  $myObj   = array();
    $myObj['messaging_product'] =   "whatsapp";
    $myObj['to'] = $number; //"923452670301",
    $myObj["type"] =  "template";
    $myObj["template"] = $template;
    $myobject    = json_encode($myObj);
  // return $myobject;
  $url = "https://graph.facebook.com/v17.0/103444702767558/messages";
  $authorization = "Authorization: Bearer ".$bearer; //EAAIz8ObxruYBANXiBwoBzm56FIFHLnBZBnJHuHosRRM1fr8Iu7wZB9iUioYjl2yDQGyTCIhm3RsikAUpl19b82qOxtfBOtyOMLlMmZCCijn5Nc2cx1UuzkRsHnla9XZBiFwAHSrckepNlSY5ngYyiIZCp3VnZCEQ94kAkVsC8DtgyMKYEjsIhNRJjm6vQjQpzIWCgkjBwZCBQZDZD
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/json",$authorization));
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $myobject);
  $result = curl_exec($curl);
  $outPut = json_decode($result, true);
  if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
  }
  if (isset($error_msg)) {
    // print_r($error_msg);
  }
  
  $result = json_decode($result);
  // print_r($result);
   // echo $result["messages"];
}*/

public function testWhatsAppOrderTrack(Request $request)
{
    $number  = $request->get('number', '923112108156'); // apna number
    $orderId = 'TEST' . rand(1000, 9999);
	
    $sentData = [
        'msgOne'   => $orderId,
        'msgTwo'   => '1500',
        'msgThree' => 'Test Restaurant',
        'msgFour'  => '30',
        'msgFive'  => 'Cash on Delivery',
    ];
	
    $result = $this->sentWhatsAppMessageOrderTrack($number, 'Test Restaurant', $sentData, $orderId);

    return response()->json($result, 200, [], JSON_PRETTY_PRINT);
}

public function sentWhatsAppMessageOrderTrack($number, $header, $message, $orderId)
{
    $template_name = "product_description";
    $code   = "en_US";
    $bearer = "EAASlFdcyGIsBO5VIwpZCPV759TMB7HQDAoKPqZAX7cwdX2NcvpSb6e0urQJMRy9WzLGroBZC5KfgX2ZBsmoSmWZBzXZArEWk0o32zReVccSBswVWX5WjzWU8ZCTZBRmAiX0TX0ouZBbQNSmJxSFJzv2ZC5AJgN9DbGEEDOwP9lMsIZAmP0ZAdnzQaJUfAa6G1yJCTY3xj2N1fXUcajzNjOCf";//$_GET['bearer'];;

    $components = [
        [
            'type' => 'header',
            'parameters' => [['type' => 'text', 'text' => $header]],
        ],
        [
            'type' => 'body',
            'parameters' => [
                ['type' => 'text', 'text' => (string) $message['msgOne']],
                ['type' => 'text', 'text' => (string) $message['msgTwo']],
                ['type' => 'text', 'text' => (string) $message['msgThree']],
                ['type' => 'text', 'text' => (string) $message['msgFour']],
                ['type' => 'text', 'text' => (string) $message['msgFive']],
            ],
        ],
        [
            'type'     => 'button',
            'sub_type' => 'url',
            'index'    => '0',
            'parameters' => [['type' => 'text', 'text' => (string) $orderId]],
        ],
    ];

    $payload = [
        'messaging_product' => 'whatsapp',
        'to'       => $number,
        'type'     => 'template',
        'template' => [
            'name'       => $template_name,
            'language'   => ['code' => $code],
            'components' => $components,
        ],
    ];
	
    $curl = curl_init("https://graph.facebook.com/v17.0/103444702767558/messages");
    curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER     => ["Content-Type: application/json", "Authorization: Bearer $bearer"],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
    ]);
    $result = curl_exec($curl);
    $error  = curl_error($curl);
    curl_close($curl);
	print_r($payload);

    return [
        'request'  => $payload,
        'response' => json_decode($result, true),
        'curl_error' => $error ?: null,
    ];
}

public function testWhatsAppMessageOrderTrack(){
   $header = "DHA Laundry";
   $url = 'https://dha-laundry.vercel.app/orders/R6e11wrqs/91511'; 
                      
        
  $message = [
				"msgOne"=>" of PKR. 1200",
				"msgTwo"=> "DHA Laundry",
				"msgThree"=>" 12 mins Payment by Cash on Delivery. "
			  ];
  $number  =  "923112108156";//$_GET['number'];
  $version =  "v15.1";//$_GET['version'];
  $phoneId = "105420688989582";//$_GET['phoneId'];
  $template_name = "product_description";//$_GET['template'];
  $code = "en_US";//$_GET['code'];
  $bearer = "EAASlFdcyGIsBO5VIwpZCPV759TMB7HQDAoKPqZAX7cwdX2NcvpSb6e0urQJMRy9WzLGroBZC5KfgX2ZBsmoSmWZBzXZArEWk0o32zReVccSBswVWX5WjzWU8ZCTZBRmAiX0TX0ouZBbQNSmJxSFJzv2ZC5AJgN9DbGEEDOwP9lMsIZAmP0ZAdnzQaJUfAa6G1yJCTY3xj2N1fXUcajzNjOCf";//$_GET['bearer'];
  
  // $orderDetails = $GLOBALS['crud']->runQuery("SELECT a.id,a.receipt_no,a.total_amount,a.date,b.branch_name,c.name as company_name,d.name as customer_name FROM sales_receipts a INNER JOIN branch b on b.branch_id = a.branch INNER JOIN company c on c.company_id = b.company_id INNER Join customers d on d.id = a.customer_id where receipt_no = $receiptNo");
  
  $myArray = [];
  $insideArray = [];
  array_push($insideArray, (object)[
        'type' => 'text',
        'text' => $header,
  ]);
  array_push($myArray, (object)[
        'type' => 'header',
        'parameters' => $insideArray,
  ]);
  
  $insideArray = [];

  array_push($insideArray, (object)[
        'type' => 'text',
        'text' => $message["msgOne"],
  ]);
  array_push($insideArray, (object)[
        'type' => 'text',
        'text' => $message["msgTwo"],
  ]);
  array_push($insideArray, (object)[
        'type' => 'text',
       'text' => $message["msgThree"],
  ]);
  array_push($myArray, (object)[
        'type' => 'body',
        'parameters' => $insideArray,
  ]);
  
  $insideArray = [];
  array_push($insideArray, (object)[
        'type' => 'payload',
        'payload' => $url,
  ]);
  array_push($myArray, (object)[
    'type' => 'button',
    "sub_type" => "URL",
    "index" => "0",
    'parameters' => $insideArray,
  ]);
  
  $template = [
    "name" => $template_name,
    "language" => [
      "code" =>  $code,
    ],
    "components" => $myArray,
  ];
 
    $myObj   = array();
    $myObj['messaging_product'] =   "whatsapp";
    $myObj['to'] = $number; //"923452670301",
    $myObj["type"] =  "template";
    $myObj["template"] = $template;
    $myobject    = json_encode($myObj);
	
	  print_r( $myobject);
	  exit();
  $url = "https://graph.facebook.com/v17.0/103444702767558/messages";
  $authorization = "Authorization: Bearer ".$bearer; //EAAIz8ObxruYBANXiBwoBzm56FIFHLnBZBnJHuHosRRM1fr8Iu7wZB9iUioYjl2yDQGyTCIhm3RsikAUpl19b82qOxtfBOtyOMLlMmZCCijn5Nc2cx1UuzkRsHnla9XZBiFwAHSrckepNlSY5ngYyiIZCp3VnZCEQ94kAkVsC8DtgyMKYEjsIhNRJjm6vQjQpzIWCgkjBwZCBQZDZD
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/json",$authorization));
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $myobject);
  $result = curl_exec($curl);
  $outPut = json_decode($result, true);
  if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
  }
  if (isset($error_msg)) {
    // print_r($error_msg);
  }
  
  $result = json_decode($result);
  print_r($result);
   // echo $result["messages"];
}


public function send_otp_sms($sender,$otp){
    $sender = ltrim($sender, '0');
    // echo $sender;exit;
    $username = "sabify";///Your Username
    $password = "sabify";///Your Password
    $mobile = '92'.$sender;///Recepient Mobile Number
    $sender = "SABIFY.";
    // $api_key = "ab2ae90c15c5f1675f74aa04b2631efd";
    $api_key = "GywQnMPxKiuWI4AFcblZso2agRJdSTrC";
    $message = $otp; // "Your OTP is ".$otp." ";
    $url = "https://connectpulse.net/API/SMS/Key?key=".$api_key."&receiver=".urlencode($mobile)."&sender=".$sender."&msgdata=".rawurlencode($message);
    // echo $url;exit;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $output=curl_exec($ch);
    // echo $output;exit;
    if(curl_errno($ch))
    {
        // return 0;//'error:' . curl_error($c);
    }
    else
    {
        // return 1;//$output;
    }
    curl_close($ch);
}



}
