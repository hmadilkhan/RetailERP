<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\posProducts;
use App\AddonCategory;
use App\Addon;
use App\InventoryVariation;
use App\InventoryVariationProduct;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Session;

class PosProductController extends Controller
{
	 public function __construct()
    {
        $this->middleware('auth');
    }

        public function show(posProducts $posProducts){
            $getbranch = $posProducts->getbranches();
            $getfinishgood = $posProducts->getfinishgood();
            $details = $posProducts->getposproducts();
			$uoms = $posProducts->getuom();
			$totalvariation = AddonCategory::where("company_id",session("company_id"))->where("mode","variations")->where('status',1)->get();
			$inventoryVariations = InventoryVariation::whereIn('product_id',DB::table('pos_products_gen_details')->where('branch_id',session('branch'))->pluck('pos_item_id'))
			                                         ->where('inventory_variations.status',1)
			                                         ->join('addon_categories','addon_categories.id','inventory_variations.variation_id')
			                                         ->select('inventory_variations.*','addon_categories.name')
			                                         ->get();
			                                         
            return view('Pos_Products.pos-products', compact('getbranch','getfinishgood','details','uoms','totalvariation','inventoryVariations'));
        }
     
     public function getVariation_posproduct(Request $request){
        return Addon::where("addon_category_id",$request->id)->where('status','=',1)->get();
     }
     
     public function reloadVariation(Request $request){
        return InventoryVariation::where('product_id',$request->id)
			                                         ->where('inventory_variations.status',1)
			                                         ->join('addon_categories','addon_categories.id','inventory_variations.variation_id')
			                                         ->select('inventory_variations.*','addon_categories.name')
			                                         ->get();
     }     
     
     public function getVariationProduct_values(Request $request){
         
         return InventoryVariationProduct::where('inventory_variation_id',$request->id)->where('status',1)->pluck('product_id');
     }
     
     public function store(posProducts $posProducts, request $request){

		$imageName = '';

     	$exsist = $posProducts->exsist_chk($request->itemname,$request->finishgood);
     	if ($exsist[0]->counts == 0) {

     	if(!empty($request->productimage)){
         $request->validate([
              'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
          ]);

          $pname = str_replace(' ', '',$request->itemname);
          $imageName =$pname.'.'.$request->productimage->getClientOriginalExtension();
          $request->productimage->move(public_path('assets/images/products/'), $imageName);
         }
         	$items = [
         	'item_code' => $request->code,
    		'item_name' => $request->itemname,
    		'product_id' => $request->finishgood,
			'uom' => $request->uom,
    		'branch_id' => session("branch"),
    		'image' => $imageName,
    		'quantity' => $request->qty,
    		'status_id' => 1,
    	 ];
 			$itemid = $posProducts->insert('pos_products_gen_details',$items);

 			$items = [
    		'actual_price' => $request->ap,
    		'tax_rate' => $request->taxrate,
    		'tax_amount' => $request->taxamount,
    		'retail_price' => $request->rp,
    		'wholesale_price' => $request->wp,
    		'online_price' => $request->op,
    		'discount_price' => $request->dp,
    		'pos_item_id' => $itemid,
    		'status_id' => 1,
    		'date' => date('Y-m-d'),
    	 ];
 			$price = $posProducts->insert('pos_product_price',$items);
 			
 			return 1;
     	}

     	else{
     		return 0;
     	}


    }
    
    public function storeVariation(Request $request){
        
        $process = true;
        
        $existsRecord = InventoryVariation::where(['variation_id'=>$request->variations,"product_id"=>$request->itemId,'status'=>1])->count();
        
        if($existsRecord > 0){
            return response()->json(['status'=>409,'msg'=>'This variation is already taken this product '.$request->productName]);
        }
    
 		
        $getID = InventoryVariation::create([
                            					"variation_id" => $request->variations,
                            					"product_id"   => $request->itemId,
                                			]);
                                	
        if(!empty($request->products) && isset($getID->id)){   
			foreach($request->products as $value){
        			InventoryVariationProduct::create([
                                					"inventory_variation_id" => $getID->id,
                                					"product_id"             => $value,
                                				]);
			}
        }
	
	    if($getID){
	        
	        return response()->json(['status'=>200]);
	    }else{
	        return response()->json(['status'=>500,'msg'=>'Server Issue! Record is not submit.']);
	    }
		
// 		  Session::flash('success','Success!');
		  
// 	  return redirect()->route('posProducts');
    }
    
    public function updateVariation(Request $request){
        
        $process = true;
        
        $existsRecord = InventoryVariation::where(["id"=>$request->id,'status'=>1])->count();
        
        if($existsRecord == 0){
            return response()->json(['status'=>500,'msg'=>'This variation is removed this product '.$request->productName.' please refresh the page.']);
        }
        
        InventoryVariationProduct::where("inventory_variation_id",$request->id)->update(["status" => 0]);

		foreach($request->products as $value){
    			InventoryVariationProduct::create([
                            					"inventory_variation_id" => $request->id,
                            					"product_id"             => $value,
                            				]);
		}

	   // if($getID){
	        
	        return response()->json(['status'=>200]);
	   // }else{
	   //     return response()->json(['status'=>500,'msg'=>'Server Issue! Record is not submit.']);
	   // }
		
// 		  Session::flash('success','Success!');
		  
// 	  return redirect()->route('posProducts');
    }    
    
    
    public function destroyVariation(Request $request){
        
        if(InventoryVariation::where('id',$request->id)->update(['status'=>0])){
           return response()->json(['status'=>200]); 
        }else{
           return response()->json(['status'=>500,'msg'=>'Server Issue! Record is not removed.']); 
        }
    }

     public function delete(posProducts $posProducts, request $request){


 		$items = [
    		'status_id' => 2,
    	 ];
 			$result = $posProducts->update_pos_gendetails($request->subid,$items);
 			
 			if($result){
 			    InventoryVariation::where('product_id',$request->subid)->update(['status'=>0]);    
 			}
 			
 			return 1;
    }

     public function reactiveposproduct(posProducts $posProducts, request $request){

 		$items = [
    		'status_id' => 1,
    	 ];
 		$result = $posProducts->update_pos_gendetails($request->subid,$items);
 			return 1;
    }

       public function inactiveposproducts(posProducts $posProducts, request $request)
		{
 			$result = $posProducts->inactiveposproducts();
 			return $result;
    }

     public function update(posProducts $posProducts, request $request){
	//update general details
		if(!empty($request->updateproduct)){
			$request->validate([
			  'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			]);
			
			if($request->prevImageName != ""){
				$file_path = public_path('assets/images/products/'.$request->prevImageName) ;
				if(file_exists($file_path)){
					unlink($file_path);
				}
			}
			
			$pname = str_replace(' ', '',$request->itemnamemodal);
			$imageName = time().'.'.$request->updateproduct->getClientOriginalExtension();
			$request->updateproduct->move(public_path('assets/images/products/'), $imageName);
        }

		$items = [
			'item_code' => $request->itemmodalcode,
			'item_name' => $request->itemnamemodal,
			'quantity' => $request->qtymodal,
			'uom' => $request->uommodal,
			'image' => !empty($request->updateproduct) ? $imageName : $request->prevImageName,
		];
 		
		$update = $posProducts->update_pos_gendetails($request->itemid,$items);

		//get id and change status to inactive
    	$id = $posProducts->getid($request->itemid);
		$items = [
			'status_id' => 2, //inactive old price
			'date' => date('Y-m-d'),
		];
		$result = $posProducts->update_pos_price($id[0]->price_id,$items);
		
		

		//insert new price in price table and status 1
		$items = [
			'actual_price' => $request->apmodal,
			'tax_rate' => $request->modaltaxrate,
			'tax_amount' => $request->modaltaxamount,
			'retail_price' => $request->rpmodal,
			'wholesale_price' => $request->wpmodal,
			'online_price' => $request->opmodal,
			'discount_price' => $request->dpmodal,
			'pos_item_id' => $request->itemid,
			'status_id' => 1,
			'date' => date('Y-m-d'),
		 ];
		$result = $posProducts->insert('pos_product_price',$items);
 		return 1;
}

		 public function view(posProducts $posProducts){
      	$getbranch = $posProducts->getbranches();
        $getitems = $posProducts->getitems();
      	// $details = $posProducts->getposproducts();
	 	return view('Pos_Products.pos-deals', compact('getbranch','getitems'));
    }



     public function store_deal(posProducts $posProducts, request $request){

        $imageName = '';

      if ($request->dealid == 0) {

      $exsist = $posProducts->deal_exist($request->dealname, $request->branch);
        if ($exsist[0]->counts == 0) {

        if(!empty($request->productimage)){
         $request->validate([
              'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
          ]);

         $dname = str_replace(' ', '',$request->dealname);

          $imageName =$dname.'.'.$request->productimage->getClientOriginalExtension();
          $request->productimage->move(public_path('assets/images/products/'), $imageName);
         }
            $items = [
            'deal_name' => $request->dealname,
            'price' => $request->price,
            'status_id' => 1,
            'branch_id' => $request->branch,
            'image' => $imageName,

         ];
            $deailid = $posProducts->insert('pos_deals',$items);
            $items = [
            'deal_id' => $deailid,
            'item_id' => $request->itemname,
            'qty' => $request->qty,
         ];
            $result = $posProducts->insert('pos_deal_details',$items);
            return $deailid;
            }

        else{
            return 0;
        }
            }

      else{
        $chck = $posProducts->deal_exist_items($request->itemname,$request->dealid);
        if ($chck[0]->counts == 0) {
            $items = [
            'deal_id' => $request->dealid,
            'item_id' => $request->itemname,
            'qty' => $request->qty,
         ];
            $result = $posProducts->insert('pos_deal_details',$items);
            return $request->dealid;
        }
        else{
            return -1;
        }
              }
    }

    public function getsubdetails(posProducts $posProducts, request $request){
        $details = $posProducts->getdealtable($request->dealid);
        return $details;
    }

       public function delete_subdetails(posProducts $posProducts, request $request){

        $result = $posProducts->DeleteItem($request->subid);
        return $result;
    }

       public function update_subdetails(posProducts $posProducts, request $request){

         $items = [
            'qty' => $request->qty,
         ];

          $result = $posProducts->update_deal_sub($request->subid,$items);
          return $result;

    }


    public function details(posProducts $posProducts){
        $details = $posProducts->getdealdetails_active();
        return view('Pos_Products.pos-deal-view', compact('details'));
    }

    public function inactivedeals(posProducts $posProducts){
        $details = $posProducts->getdealdetails_in_active();
        return $details;
    }

       public function reactivedeal(posProducts $posProducts, request $request){
       $items = [
            'status_id' => 1,
         ];
            $result = $posProducts->update_pos_deals($request->dealid,$items);
            return 1;
    }


           public function deletedeal(posProducts $posProducts, request $request){
       $items = [
            'status_id' => 2,
         ];
            $result = $posProducts->update_pos_deals($request->dealid,$items);
            return 1;
    }



        public function dealsubdetails(posProducts $posProducts, request $request){

            $result = $posProducts->getpos_subdetails($request->dealid);
            return $result;
    }

          public function edit(posProducts $posProducts , request $request){
            $getbranch = $posProducts->getbranches();
        $getitems = $posProducts->getitems();
        $details = $posProducts->getdeals($request->id);
        $subdetails = $posProducts->getpos_subdetails($request->id);
        return view('Pos_Products.pos-deal-edit', compact('getbranch','getitems','details','subdetails'));

    }



     public function update_deal(posProducts $posProducts, request $request){

       if(!empty($request->productimage)){
     $path = public_path('assets/images/products/').$request->oldimage;
            if(file_exists($path)){
                  @unlink($path);
                }
             $request->validate([
                 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                 ]);
               $dname = str_replace(' ', '',$request->dealname);
           $imageName = $dname.'.'.$request->productimage->getClientOriginalExtension();
              $request->productimage->move(public_path('assets/images/products/'), $imageName);

            }
            else{
                $imageName = $request->oldimage;
            }

            $items = [
            'deal_name' => $request->dealname,
            'price' => $request->price,
            'status_id' => 1,
            'branch_id' => $request->branch,
            'image' => $imageName,

         ];

         $update = $posProducts->update_pos_deals($request->dealid,$items);

         if ($request->itemname != "") {

        $chck = $posProducts->deal_exist_items($request->itemname,$request->dealid);
        if ($chck[0]->counts == 0) {
            $items = [
            'deal_id' => $request->dealid,
            'item_id' => $request->itemname,
            'qty' => $request->qty,
         ];
            $result = $posProducts->insert('pos_deal_details',$items);
            return $request->dealid;
        }
        else{
            return -1;
        }

         }
         else{
            return $request->dealid;
         }

    }


    public function codeverify(posProducts $posProducts, request $request){
        $result = $posProducts->verifycode($request->code);
        return $result[0]->counts;
    }




}