<?php

namespace App\Http\Controllers;

use App\AddonCategory;
use App\Addon;
// use App\inventory;
use App\Models\Inventory ;
use App\InventoryDealGeneral;
use App\InventoryDealDetail;
use Illuminate\Http\Request;
use DB,Session;

class InventoryDealController extends Controller
{
    

    public function __construct()
    {
        $this->middleware('auth');
    }    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $DealProducts = DB::table('inventory_general')->select('id','product_name')->where(['is_deal'=>1,'company_id'=>session('company_id')])->get();
        
        $InventGroups = AddonCategory::with("addons")->where(["company_id"=>session("company_id"),'mode'=>'groups'])->get();

        $getRecord2   =  Inventory::with("deals","deals.inventoryGroup","deals.getDeal_details")
						->where("company_id",session('company_id'))->where("is_deal",1)->get();
						// return $getRecord2;
                        //   return     $getRecord2;              
        return view('inventory-deal.index',compact('DealProducts','InventGroups','getRecord2'));
    }
    
    public function getGroups_values(Request $request){
        return Addon::where("addon_category_id",$request->id)->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $DealProducts = DB::table('inventory_general')->select('id','product_name')->where(['is_deal'=>1,'company_id'=>session('company_id')])->get();
        
        $InventGroups = AddonCategory::with("addons")->where(["company_id"=>session("company_id"),'mode'=>'groups'])->get();        
        
        return view('inventory-deal.create',compact('DealProducts','InventGroups'));
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
		    
		    $rules = [
		               'deal' => 'required',
		               'group'=> 'required',
		             ];
		             
		    $this->validate($request,$rules);           
	
			$count = InventoryDealGeneral::where(['inventory_deal_id'=>$request->deal,'group_id'=>$request->group])->count();
            
            if($count > 0){
                return redirect()->route('listInventDeal')->withInput($request->input());
            }
            
            $result = InventoryDealGeneral::create([
                                'inventory_deal_id' => $request->deal,
                                'group_id'          => $request->group,
                                'status'            => 1,
                                'created_at'        => date("Y-m-d H:i:s"),
                                'updated_at'        => date("Y-m-d H:i:s"),
                          ]);
            
            if($result){
                $getProducts = $request->products;
                foreach($getProducts as $val){
                    InventoryDealDetail::create([
                                                    'inventory_general_id'  => $result->id,
                                                    'sub_group_id'          => $val,
                                                    'status'                => 1,
                                              ]);                    
                }
                
                Session::flash('success','Success!');
            }else{
                Session::flash('success','Success!');
            }              
            
          return redirect()->route('listInventDeal');
           
		}catch(Exception $e){
			return redirect()->route('listInventDeal')->with('error', "Error : ".$e->getMessage());
		}
    }
    
    public function get_deal_detail(Request $request){
        
        return InventoryDealGeneral::with('getDeal_details')->where('inventory_deal_id',$request->id)->get();   
    }



    public function update(Request $request)
    {
        try{

            $dealId =  $request['deal'];
            $request = $request->except(['_token','deal']);
            
            $dealGeneral = InventoryDealGeneral::where('inventory_deal_id',$dealId)->where('status',1)->whereIn('group_id',array_keys($request))->get(); 
            if($dealGeneral != null){
               $getGroup_key = array_keys($request);
               
                foreach($dealGeneral as $val){
                    
                    if(InventoryDealDetail::where('inventory_general_id',$val->id)->update(['status'=>0])){
                        // $getValues_group = explode(',',$request[$val->group_id]);
                        // return $getValues_group[0];
                        // return $request[$val->group_id];
                        // exist();
                          if(count($request[$val->group_id]) > 0){
                                foreach($request[$val->group_id] as $setValue_group){
                                            InventoryDealDetail::create([
                                                                    'inventory_general_id'  => $val->id,
                                                                    'sub_group_id'          => $setValue_group,
                                                                    'status'                => 1,
                                                              ]);
                                }
                          }else{
                             InventoryDealGeneral::where('id',$val->id)->update(['status'=>0]); 
                          }

                    }
                }
                
                return redirect()->route('listInventDeal')->with('success', "Success!");
                
            }else{
               	return redirect()->route('listInventDeal')->with('error', "Error : Server Issue"); 
            }
        
		}catch(Exception $e){
			return redirect()->route('listInventDeal')->with('error', "Error : ".$e->getMessage());
		}
    }


    public function destroy(Request $request)
    {
        try{
            
            if(!empty($request->mode)){
    			if(InventoryDealGeneral::where(["inventory_deal_id"=>$request->id,"group_id"=>$request->mode])->update(['status'=>0,'updated_at'=>date('Y-m-d H:i:s')])){
    			    Session::flash('success','Success!');
    			}else{
    			    Session::flash('error','Error!');
    			}                
                
            }else{
    			if(InventoryDealGeneral::where("inventory_deal_id",$request->id)->update(['status'=>0,'updated_at'=>date('Y-m-d H:i:s')])){
    			    Session::flash('success','Success!');
    			}else{
    			    Session::flash('error','Error!');
    			}
            }
            
			return redirect()->route('listInventDeal');
		}catch(Exception $e){
			return redirect()->route('listInventDeal')->with('error', "Error : ".$e->getMessage());
		}
    }
}
