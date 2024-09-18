<?php

namespace App\Http\Controllers;

use App\inventory;
use App\Vendor;
use App\purchase;
use App\stock;
use App\posProducts;
use App\AddonCategory;
use App\Tag;
use App\Attribute;
use App\Addon;
use App\Brand;
use App\InventoryAddon;
use App\InventoryDealGeneral;
use App\InventoryDealDetail;
use App\InventoryVariation;
use App\InventoryVariationProduct;
use App\WebsiteDetail;
use App\WebsiteProduct;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Config;
use App\Traits\MediaTrait;
use Illuminate\Support\Str;
use Image, File, Auth;
use Illuminate\Support\Facades\Http;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class InventoryController extends Controller
{
    use MediaTrait;
    // $this->uploads($request->file,"images/company",);
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(inventory $inventory, Brand $brand)
    {
        // return  InventoryDepartment::whereIn("department_id",ModelsInventory::whereIn("id",WebsiteProduct::where("website_id",41)->pluck("inventory_id"))->pluck("department_id"))->where('status',1)->select("code","department_id","department_name","website_department_name","slug","image","banner")->orderBy('priority','desc')->get();
        $department    = $inventory->department();
        $subdepartment = ''; //$inventory->subDepartment();
        $uom        = $inventory->uom();
        $branch     = $inventory->branch();
        // $inventory = ''; //$inventory->getData();
        $vendors    = DB::table("vendors")->where("status_id", 1)->where("user_id", session("company_id"))->get();
        $references = DB::select("SELECT * FROM `inventory_reference` where product_id IN (Select id from inventory_general where company_id = ?) and refrerence != '' GROUP by refrerence", [session('company_id')]);

        $websites   = DB::table("website_details")->where("company_id", session("company_id"))->where("status", 1)->get();
        // $websiteProducts = WebsiteProduct::with('websiteDetails')
        //                                  ->whereIn('website_id',WebsiteDetail::where("company_id", session("company_id"))
        //                                                               ->where("status", 1)->pluck('id')
        //                                           )
        //                                  ->select('id','website_id','inventory_id')         
        //                                  ->where('status',1)         
        //                                  ->get();         
        $brandList  = $brand->getBrand();
        $tagsList   = Tag::getTags();

        //if(session("company_id") == 7 or session("company_id") ==  102 && Auth::user()->username != 'demoadmin'){ //or session("company_id") ==  102 session("company_id") == 7 or
        // if (in_array(session("company_id"), [7, 102]) && !in_array(Auth::user()->username, ['demoadmin'])) {
            $inventories = $inventory->getInventoryForPagewiseByFilters();
            // if(Auth::user()->username == 'demoadmin'){
            //    return $inventories;
            // }
            $inventory = '';
            if (in_array(Auth::user()->username, ['fn1009'])) {
                return view('Inventory.livewirelist', compact('inventory', 'inventories', 'department', 'subdepartment', 'uom', 'branch', 'vendors', 'references', 'websites', 'tagsList', 'brandList'));
            } else {
                return view('Inventory.listnew', compact('inventory', 'inventories', 'department', 'subdepartment', 'uom', 'branch', 'vendors', 'references', 'websites', 'tagsList', 'brandList'));
            }
        // } else {
        //     $inventory = '';
        //     return view('Inventory.lists', compact('inventory', 'department', 'subdepartment', 'uom', 'branch', 'vendors', 'references', 'websites', 'tagsList', 'brandList'));
        // }                                                                                                       
    }

    public function getInventory(inventory $inventory)
    {
        $inventory = $inventory->getData();
        return $inventory;
    }

    function fetchData(Request $request, inventory $inventory)
    {
        if ($request->ajax()) {
            $inventories = $inventory->getInventoryForPagewiseByFilters($request->code, $request->name, $request->dept, $request->sdept, $request->rp, $request->ref, $request->status, $request->nonstock);
            return view('Inventory.inventory_table', compact('inventories'))->render();
        }
    }


    public function getDeparmtent_wise_Inventory(Request $request)
    {
        return DB::table('inventory_general')->where('sub_department_id', $request->id)->get();
    }

    public function getInactiveInventory(inventory $inventory)
    {
        $inventory = $inventory->getInactiveData();
        return $inventory;
    }

    public function getNonStockInventory(Request $request, inventory $inventory)
    {
        $inventory = $inventory->getNonStockInventory($request->code, $request->name, $request->dept, $request->sdept, $request->rp, $request->ref);
        return $inventory;
    }

    public function getInventoryByName(Request $request, inventory $inventory)
    {
        $inventory = $inventory->getDataByName($request->code, $request->name, $request->dept, $request->sdept, $request->rp, $request->ref);
        return $inventory;
    }

    public function getInactiveInventoryBySearch(Request $request, inventory $inventory)
    {
        $inventory = $inventory->getInactiveInventoryBySearch($request->code, $request->name, $request->dept, $request->sdept, $request->ref);
        return $inventory;
    }

    public function autoGenerateCode(Request $request, inventory $inventory)
    {
        $code = "";
        if ($request->departmentId != "" && $request->subdepartmentId != "") {
            $result = $inventory->getDepartAndSubDepart($request->departmentId, $request->subdepartmentId);
            if (!empty($result) && $result[0]->deptcode != "") {
                $code = $result[0]->deptcode . "-" . $result[0]->sdeptcode . "-" . rand(1000, 9999);
            } else {
                $code = substr($result[0]->department_name, 0, 1) . substr($result[0]->sub_depart_name, 0, 1) . "-" . rand(1000, 9999);
            }
            return response()->json(["status" => 200, "code" => $code, "result" => $result]);
        } else {
            return response()->json(["status" => 500, "code" => ""]);
        }
    }

    public function insert(Request $request, inventory $inventory, purchase $purchase, stock $stock)
    {
        // if(Auth::user()->username == 'demoadmin'){
        //       return empty($request->file('productvideo')) ? 1 : 0;
        // }
        //$websiteMode = 1; // website mode "retail" and "restaurent" use of purpose image size 

        $rules = [
            'code'          => 'required',
            'name'          => 'required',
            'reminder'      => 'required',
            'uom'           => 'required',
            'cuom'          => 'required',
            'depart'        => 'required',
            'subDepart'     => 'required',
            'rp'            => 'required',
            'ap'            => 'required',
            'product_mode'  => 'required',
            // 'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,tiff|min:10|max:100',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',

        ];

        $this->validate($request, $rules);

        // if (!empty($request->website)) {
        //     $result =  WebsiteDetail::where('company_id', session('company_id'))->where('id', $request->website)->first();
        //     if (isset($result->type) && $result->type == 'restaurant') {
        //         $websiteMode = 0;
        //     }
        // }
        $imageName = NULL;   
        $imageData = NULL;
        if (!empty($request->file('image'))) {
            $image = $request->file('image');

            if (!in_array(session('company_id'), [95, 102, 104]) && !in_array(Auth::user()->username,['demoadmin','fnkhan'])) {

                    $request->validate([
                        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,tiff|max:1024',
                    ]);
            }

            $imageName = time() . '-' . pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $image->getClientOriginalExtension();

            if (in_array(session('company_id'), [95, 102, 104]) || in_array(Auth::user()->username,['demoadmin','fnkhan'])) { //cloudinary image save fro kashee
                $transformationArray = [];

                if (!isset($request->actual_image_size)) {
                    $transformationArray['width']  = 400;
                    $transformationArray['height'] = 400;
                    $transformationArray['crop']   = 'scale';
                }
                $transformationArray['quality']  = 'auto';
                $transformationArray['fetch']    = 'auto';

                $company_name = DB::table('company')->where('company_id', session('company_id'))->first();

                $folder = strtolower(str_replace(' ','',$company_name->name));

                $imageData = Cloudinary::upload($image->getRealPath(), [
                                        'public_id'      => strtolower($imageName),
                                        'folder'         => $folder,
                                        'transformation' => $transformationArray
                                    ])->getSecurePath();
            } else {
                if (!isset($request->actual_image_size)) {
                    $returnImageValue = $this->uploads($image, "images/products/", "", ['width' => 400, "height" => 400]);
                    $imageName = $returnImageValue['fileName']; 
                }
            }
        }

        $fields = [
            'company_id'          => session('company_id'),
            'department_id'       => $request->depart,
            'sub_department_id'   => $request->subDepart,
            'uom_id'              => $request->uom,
            'cuom'                => $request->cuom,
            'product_mode'        => $request->product_mode,
            'priority'            => $request->priority,
            'item_code'           => $request->code,
            'product_name'        => $request->name,
            'product_description' => $request->description,
            'image'               => $imageName,
            'url'                 => $imageData,
            'status'              => 1,
            'created_at'          => date('Y-m-d H:s:i'),
            'updated_at'          => date('Y-m-d H:s:i'),
            'weight_qty'          => $request->weight,
            'slug'                => strtolower(str_replace(' ', '-', $request->name)) . "-" . strtolower(Str::random(4)),
            'is_deal'             => (isset($request->is_deal) ? 1 : 0),
            'short_description'   => $request->sdescription,
            'details'             => $request->details,
            'brand_id'            => $request->brand,
            'actual_image_size'   => isset($request->actual_image_size) ? 1 : 0,
        ];
        $productid = $inventory->insert($fields);
        $result = $inventory->ReminderInsert($productid, $request->reminder);

        //Inventory Images Here
        // if (!empty($request->image)) {
        //     foreach ($data  as $value) {
        //         DB::table("inventory_images")->insert([
        //             "item_id" => $productid,
        //             "image" => $value
        //         ]);
        //     }
        // }


        //Inventory References Here
        $ref = explode(",", $request->reference);
        foreach ($ref as $value) {
            DB::table("inventory_reference")->insert([
                "product_id" => $productid,
                "refrerence" => $value
            ]);
        }

        //inventory price insert here
        $items = [
            'cost_price'      => $request->cost_price,
            'actual_price'    => $request->ap,
            'tax_rate'        => $request->taxrate,
            'tax_amount'      => $request->taxamount,
            'retail_price'    => $request->rp,
            'wholesale_price' => $request->wp,
            'online_price'    => $request->op,
            'discount_price'  => $request->dp,
            'product_id'      => $productid,
            'status_id'       => 1,
        ];
        $price = $inventory->insertgeneral('inventory_price', $items);

        //check product mode agar 3 hoga to POS product k table m insert krwana ha warna nahi
        if ($request->product_mode == 3) {
            if ($request->chkactive == "on") {
                //pos product m insert krwana ha
                $items = [
                    'item_code' => $request->poscode,
                    'item_name' => $request->posname,
                    'uom' => $request->posuom,
                    'product_id' => $productid,
                    'branch_id' => session("branch"),
                    'image' => $imageName,
                    'quantity' => 1,
                    'status_id' => 1,
                ];
                $itemid = $inventory->insertgeneral('pos_products_gen_details', $items);

                //pos price m insert
                $items = [
                    'retail_price' => $request->posprice,
                    'wholesale_price' => 0,
                    'online_price' => 0,
                    'discount_price' => 0,
                    'pos_item_id' => $itemid,
                    'status_id' => 1,
                    'date' => date('Y-m-d'),
                ];
                $price = $inventory->insertgeneral('pos_product_price', $items);
            }
        }

        //Stock Opening Code Here
        if ($request->chkstock == "on") {
            $grn = $purchase->getGrn();
            $grn = $grn + 1;
            $gen = [
                'GRN' => "GRN-" . $grn,
                'user_id' => session('userid'),
                'created_at' => date('Y-m-d H:s:i'),
                'updated_at' => date('Y-m-d H:s:i'),
            ];
            $gen_res = $purchase->receiving_general($gen);

            $fields = [
                'GRN' => $gen_res,
                'item_id' => $productid,
                'qty_rec' => $request->stock_qty,
            ];

            $items = [
                'grn_id' => $gen_res,
                'product_id' => $productid,
                'uom' => $request->uom,
                'cost_price' => $request->stock_cost,
                'retail_price' => 0,
                'wholesale_price' => 0,
                'discount_price' => 0,
                'qty' => $request->stock_qty,
                'balance' => $request->stock_qty,
                'status_id' => 1,
                'branch_id' =>  session("branch"),
            ];

            $stock_id = DB::table('purchase_rec_stock_opening')->insertGetId($fields);
            $lastStock = $stock->getLastStock($request->product);
            $stk = empty($lastStock) ? 0 : $lastStock[0]->stock;
            $stk = $stk + $request->qty;

            $report = [
                'date' => date('Y-m-d H:s:i'),
                'product_id' => $productid,
                'foreign_id' => $stock_id,
                'branch_id' =>  session("branch"),
                'qty' => $request->stock_qty,
                'stock' => $request->stock_qty,
                'cost' => $request->stock_cost,
                'retail' => $request->rp,
                'narration' => 'Stock Opening',
            ];

            $stock_report = $stock->stock_report($report);

            DB::table('inventory_stock')->insert($items);
        }

        $terminals = DB::table("terminal_details")->where("branch_id", session("branch"))->where("status_id", 1)->get();
        foreach ($terminals as $value) {
            $items = [
                "branchId" => session("branch"),
                "terminalId" => $value->terminal_id,
                "productId" => $productid,
                "status" => 1,
                "date" => date("Y-m-d"),
                "time" => date("H:i:s"),
            ];
            DB::table("inventory_download_status")->insert($items);
        }

        if (!empty($request->vendor)) {
            foreach ($request->vendor as $singleVendor) {
                DB::table('vendor_product')->insert([
                    "vendor_id" => $singleVendor,
                    "product_id" => $productid,
                    "status" => 1,
                ]);
            }
        }

        if (!empty($request->website) && isset($request->showProductWebsite)) {
            // foreach ($request->website as $website) {
                WebsiteProduct::create([
                    "website_id"    => $request->website,
                    "inventory_id"  => $productid,
                ]);
            // }
        }

        if (!empty($request->tags)) {
            foreach ($request->tags as $val) {
                DB::table('inventory_tags')->insert([
                    "inventory_id" => $productid,
                    "tag_id"     => $val,
                    'created_at'   => date("Y-m-d H:i:s")
                ]);
            }
        }

        //Product Gallery		
        if (!empty($request->file('prodgallery'))) {
            $count = 1;
            foreach ($request->file('prodgallery') as $val) {
                $prodGallery = $val;
                $imageName   = null;
                // $response    = Image::make($image)
                //     ->save(public_path('storage/images/products/' . $imageName));

                if (in_array(session('company_id'), [95, 102, 104]) || in_array(Auth::user()->username,['demoadmin','fnkhan'])) { //cloudinary image save fro kashee
                    $transformationArray = [];
                    $transformationArray['quality']  = 'auto';
                    $transformationArray['fetch']    = 'auto';
    
                    $company_name = DB::table('company')->where('company_id', session('company_id'))->first();
    
                    $folder = strtolower(str_replace(' ','',$company_name->name));

                    $imageName   = $productid.time().'-'.$count.'.'.$prodGallery->getClientOriginalExtension();
    
                    $imageData = Cloudinary::upload($prodGallery->getRealPath(), [
                                            'public_id'      => strtolower($imageName),
                                            'folder'         => $folder,
                                            'transformation' => $transformationArray
                                        ])->getSecurePath();
                         
                }else{
                    $path = public_path('storage/images/products/');
                    $returnImageValue = $this->uploads($prodGallery, $path);
                    $imageName = $returnImageValue['fileName']; 
                }

                if ($imageName != null) {
                    DB::table('inventory_images')->insert([
                        "item_id" => $productid,
                        "image"   => $imageName,
                        "url"     => isset($imageData) ? $imageData : null,
                    ]);

                    $count++;
                }
            }
        }

        //Product video
        if (!empty($request->file('prodvideo'))) {
            $prodVideo     = $request->file('prodvideo');
            $path          = public_path('storage/video/products/');
            $prodVideoName = $this->uploads($prodVideo, $path);

            if (isset($prodVideoName['filename'])) {

                DB::table('inventory_video')->insert([
                    "inventory_id" => $productid,
                    "file"         => $$prodVideoName['filename'],
                    'created_at'   => date("Y-m-d H:i:s")
                ]);
            }
        }

        $this->sendPushNotification($request->code, $request->name, "store");
        return  redirect()->back();
    }

    public function getProduct_attribute(Request $request, Brand $brand)
    {
        $columnName = $request->control;

        if ($columnName == 'brand') {

            return $brand->getBrand();
        } elseif ($columnName == 'tag') {

            return Tag::getTags();
        } else {
            return response()->json('Error invalid parameter values.', 500);
        }
    }

    public function insertProduct_attribute(Request $request)
    {

        $columnName = $request->control;

        if ($columnName == 'brand') {

            if (Brand::where('company_id', session('company_id'))->where('name', $request->value)->count() > 0) {
                return response()->json('Error! This ' . $request->value . ' brand already exists.', 409);
            }

            return Brand::create(['name' => $request->value, 'slug' => preg_replace("/[\s_]/", "-", strtolower($request->value)), 'company_id' => session('company_id'), 'created_at' => date('Y-m-d H:i:s')]) ? response()->json('success', 200) : response()->json('Error record is not saved.', 500);
        } elseif ($columnName == 'tag') {

            if (Tag::where('company_id', session('company_id'))->where('name', $request->value)->count() > 0) {
                return response()->json('Error! This ' . $request->value . ' brand already exists.', 409);
            }

            return Tag::create(['name' => $request->value, 'slug' => preg_replace("/[\s_]/", "-", strtolower($request->value)), 'company_id' => session('company_id'), 'created_at' => date('Y-m-d H:i:s')]) ? response()->json('success', 200) : response()->json('Error record is not saved.', 500);
        } elseif ($columnName == 'attribute') {

            if (Attribute::where('company_id', session('company_id'))->where('name', $request->value)->count() > 0) {
                return response()->json('Error! This ' . $request->value . ' brand already exists.', 409);
            }

            return Attribute::create(['name' => $request->value, 'company_id' => session('company_id'), 'created_at' => date('Y-m-d H:i:s')]) ? response()->json('success', 200) : response()->json('Error record is not saved.', 500);
        } else {
            return response()->json('Error invalid parameter values.', 500);
        }
    }

    public function setProductAttribute_update(Request $request)
    {

        if (!empty($request->website)) {
            foreach ($request->inventid as $productid) {
                $existsProduct =  WebsiteProduct::where('website_id', $request->website)->where('inventory_id', $productid)->where('status', 1)->first();
                if ($existsProduct == null) {
                    WebsiteProduct::create([
                        "website_id" => $request->website,
                        "inventory_id" => $productid,
                    ]);

                    WebsiteProduct::where('website_id','!=',$request->website)
                                   ->where('inventory_id',$productid)
                                   ->where('status',1)
                                   ->update(['status'=>0,'updated_at'=>date("Y-m-d H:i:s")]);

                }
            }
            return response()->json('success', 200);
        } elseif (!empty($request->brand)) {
            foreach ($request->inventid as $productid) {
                DB::table('inventory_general')
                    ->where('id', $productid)
                    ->update([
                        "brand_id" => $request->brand,
                    ]);
            }
            return response()->json('success', 200);
        } elseif (!empty($request->tags)) {
            foreach ($request->inventid as $productid) {
                foreach ($request->tags as $val) {
                    $existsProduct =  DB::table('inventory_tags')->where('tag_id', $val)->where('inventory_id', $productid)->where('status', 1)->first();
                    if ($existsProduct == null) {
                        DB::table('inventory_tags')->insert([
                            "inventory_id" => $productid,
                            "tag_id"       => $val,
                            'created_at'   => date("Y-m-d H:i:s")
                        ]);
                    }
                }
            }
            return response()->json('success', 200);
        } else {
            return redirect()->route('invent-list');
        }
    }


    public function getInventoryDeals(Request $request)
    {

        return DB::table('inventory_deal_general')->whereIn(
            'inventory_deal_id',
            DB::table('inventory_general')
                ->where('company_id', session('company_id'))
                ->where('status', 1)
                ->pluck('id')
        )
            ->join('addon_categories', 'addon_categories.id', 'inventory_deal_general.group_id')
            ->where('inventory_deal_general.status', 1)
            ->select('inventory_deal_general.*', 'addon_categories.name', 'addon_categories.type as group_type')
            ->get();
    }

    public function getInventoryDeals_prodValues(Request $request)
    {
        return DB::table('inventory_deal_details')
            ->join('inventory_general', 'inventory_general.id', 'inventory_deal_details.product_id')
            ->join('inventory_department', 'inventory_department.department_id', 'inventory_general.department_id')
            ->join('inventory_sub_department', 'inventory_sub_department.sub_department_id', 'inventory_general.sub_department_id')
            ->where(['inventory_deal_details.inventory_general_id' => $request->id, 'inventory_deal_details.status' => 1])
            ->select('inventory_deal_details.*', 'inventory_department.department_name', 'inventory_sub_department.sub_depart_name')
            ->get();
        // $getRecord = DB::table('inventory_deal_details')->whereIn('inventory_general_id',DB::table('inventory_deal_general')
        //                                                                     		  ->where('inventory_deal_id',$request->prod_id)
        //                                                                     		  ->where('group_id',$request->group_id)
        //                                                                     		  ->pluck('id')
        //                                                     )
        //                                         ->join('addons','addons.id','inventory_deal_details.sub_group_id') 
        //                                         ->join('inventory_general','inventory_general.id','addons.inventory_product_id')
        //                                         ->join('inventory_department','inventory_department.department_id','inventory_general.department_id')
        //                                         ->where('inventory_deal_details.status',1)
        //                                         ->select('inventory_deal_details.*','addons.inventory_product_id','addons.name','addons.quantity','inventory_department.department_name')
        //                                         ->get();  
        //   return $getRecord;                                      
        // $productId = [];

        // $departmentId = $getRecord[0]->department_id;

        // foreach($getRecord as $val){
        //     array_push($productId,$val->inventory_product_id);
        // }

        // return response()->json(['departmentId'=>$departmentId,'productId'=>$productId]);    
    }

    public function storeDeal(Request $request)
    {
        try {
            $count = AddonCategory::whereIn("id", DB::table('inventory_deal_general')->where('inventory_deal_id', $request->inventory_id)->where('status', 1)->pluck('group_id'))->where("status", 1)->where("name", $request->group_name)->count();

            if ($count != 0) {
                return response()->json(["status" => 409, "contrl" => "group_name", "msg" => "This " . $request->group_name . " group name is already taken from product " . $request->inventory_name]);
            }

            // 			if($count == 0){
            $getAddonCategoryId = AddonCategory::create([
                "name"               => $request->group_name,
                "show_website_name"  => $request->group_name,
                "user_id"            => auth()->user()->id,
                "company_id"         => session("company_id"),
                "type"               => $request->group_type,
                "is_required"        => 1,
                "mode"                 => 'groups',
                "addon_limit"        => isset($request->selection_limited) ? $request->selection_limited : 0,
            ]);

            if ($getAddonCategoryId) {
                $getproducts = $request->products;
                foreach ($getproducts as $prod_val) {
                    $getInventoryName = DB::table('inventory_general')->where('id', $prod_val)->first();
                    Addon::create([
                        "inventory_product_id"   => $prod_val,
                        "name"                   => $getInventoryName->product_name,
                        "addon_category_id"      => $getAddonCategoryId->id,
                        "user_id"                => auth()->user()->id,
                    ]);
                }

                $getInventoryDealGeneral_ID = InventoryDealGeneral::create([
                    'inventory_deal_id' => $request->inventory_id,
                    'group_id'          => $getAddonCategoryId->id,
                    'status'            => 1,
                    'created_at'        => date("Y-m-d H:i:s"),
                    'updated_at'        => date("Y-m-d H:i:s"),
                ]);

                if ($getInventoryDealGeneral_ID) {
                    $getDealGroup_values = Addon::where('addon_category_id', $getAddonCategoryId->id)->get();
                    foreach ($getDealGroup_values  as $prod_val) {
                        InventoryDealDetail::create([
                            'inventory_general_id'  => $getInventoryDealGeneral_ID->id,
                            'sub_group_id'          => $prod_val->id,
                            'status'                => 1,
                        ]);
                    }
                }
            }
            return response()->json(["status" => 200]);
        } catch (Exception $e) {
            return response()->json(["status" => 500, "msg" => $e->getMessage()]);
        }
        // 			}else{

        // 			}
    }

    public function updateDeal(Request $request)
    {
        try {
            $count = AddonCategory::whereIn("id", DB::table('inventory_deal_general')->where('inventory_deal_id', $request->inventory_id)->where('group_id', '!=', $request->group_id)->where('status', 1)->pluck('group_id'))->where("status", 1)->where("name", $request->group_name)->count();

            if ($count != 0) {
                return response()->json(["status" => 409, "control" => "group_name_editmd", "msg" => "This " . $request->group_name . " group name is already taken from product " . $request->inventory_name]);
            }

            AddonCategory::where('id', $request->group_id)->update(['status' => 0]);
            Addon::where('addon_category_id', $request->group_id)->update(['status' => 0]);

            $getIdInventory_deal_general = InventoryDealGeneral::where('inventory_deal_id', $request->inventory_id)->where('group_id', $request->group_id)->pluck('id');
            InventoryDealGeneral::where('inventory_deal_id', $request->inventory_id)->where('group_id', $request->group_id)->update(['status' => 0]);
            InventoryDealDetail::where('inventory_general_id', $getIdInventory_deal_general)->update(['status' => 0]);


            $getAddonCategoryId = AddonCategory::create([
                "name"               => $request->group_name,
                "show_website_name"  => $request->group_name,
                "user_id"            => auth()->user()->id,
                "company_id"         => session("company_id"),
                "type"               => $request->group_type,
                "is_required"        => 1,
                "mode"                 => 'groups',
                "addon_limit"        => isset($request->selection_limit) ? $request->selection_limit : 0,
            ]);

            if ($getAddonCategoryId) {
                $getproducts = $request->products;
                $getproductQuantity = $request->product_qty;
                for ($i = 0; $i < count($getproducts); $i++) {
                    $getInventoryName = DB::table('inventory_general')->where('id', $getproducts[$i])->first();
                    Addon::create([
                        "inventory_product_id"   => $prod_val,
                        "name"                   => $getInventoryName->product_name,
                        "quantity"               => $getproductQuantity[$i],
                        "addon_category_id"      => $getAddonCategoryId->id,
                        "user_id"                => auth()->user()->id,
                    ]);
                }

                $getInventoryDealGeneral_ID = InventoryDealGeneral::create([
                    'inventory_deal_id' => $request->inventory_id,
                    'group_id'          => $getAddonCategoryId->id,
                    'status'            => 1,
                    'created_at'        => date("Y-m-d H:i:s"),
                    'updated_at'        => date("Y-m-d H:i:s"),
                ]);

                if ($getInventoryDealGeneral_ID) {
                    $getDealGroup_values = Addon::where('addon_category_id', $getAddonCategoryId->id)->get();
                    foreach ($getDealGroup_values  as $prod_val) {
                        InventoryDealDetail::create([
                            'inventory_general_id'  => $getInventoryDealGeneral_ID->id,
                            'sub_group_id'          => $prod_val->id,
                            'status'                => 1,
                        ]);
                    }
                }
            }
            return response()->json(["status" => 200]);
        } catch (Exception $e) {
            return response()->json(["status" => 500, "msg" => $e->getMessage()]);
        }
    }

    public function removeDeal(Request $request)
    {
        if (AddonCategory::where('id', $request->group_id)->update(['status' => 0]) && InventoryDealGeneral::where('group_id', $request->group_id)->where('inventory_deal_id', $request->inventid)->update(['status' => 0])) {
            return response()->json(["status" => 200]);
        } else {
            return response()->json(["status" => 500, "msg" => 'Server issue! record is not removed.']);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(inventory $inventory, Vendor $vendor, Brand $brand)
    {
        // if(auth()->user()->id == 864){
        // $stocks = DB::select("SELECT * FROM `inventory_stock` WHERE `branch_id` = 219 and product_id IN (SELECT id FROM `inventory_general` where company_id = 74 and department_id = 855)");
        // $branchIds = [237,238,239,240,243,244,245,246,249];
        // foreach($branchIds as $branch){
        // foreach($stocks as $key => $stock){
        // DB::table("inventory_stock")->insert([
        // "grn_id" => $stock->grn_id,
        // "product_id" =>$stock->product_id,
        // "uom" =>$stock->uom,
        // "cost_price" =>$stock->cost_price,
        // "retail_price" =>$stock->retail_price,
        // "wholesale_price" =>$stock->wholesale_price,
        // "discount_price" =>$stock->discount_price,
        // "qty" =>$stock->qty,
        // "balance" =>$stock->balance,
        // "status_id" =>$stock->status_id,
        // "branch_id" =>$branch,
        // "date" => date("Y-m-d H:i:s"),
        // "narration" =>$stock->narration,
        // ]);
        // }
        // }

        // }
        $department = $inventory->department();
        $subdepartment = $inventory->subDepartment();
        $uom = $inventory->uom();
        $branch = $inventory->branch();
        $brandList  = $brand->getBrand();
        $tagsList   = Tag::getTags();
        $attributes  = Attribute::get_attributes();
        $mode = $inventory->getProductMode();
        $vendors = $vendor->getVendors();
        $websites = DB::table("website_details")->where("company_id", session("company_id"))->where("status", 1)->get();
        $totaladdons = AddonCategory::where("company_id", session("company_id"))->where("mode", "addons")->where('status', 1)->get();
        // 		$extras = DB::table("extra_products")->whereNull("parent")->get();

        // if (Auth::user()->username == 'demoadmin') {
            return view('Inventory.create-debug', compact('department', 'subdepartment', 'uom', 'branch', 'mode', 'vendors', 'totaladdons', 'websites', 'brandList', 'tagsList', 'attributes'));
        // } else {
        //     return view('Inventory.create', compact('department', 'subdepartment', 'uom', 'branch', 'mode', 'vendors', 'totaladdons', 'websites', 'brandList', 'tagsList'));
        // }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function getSubDepart(Request $request)
    {
        $city = DB::table('inventory_sub_department')->where('department_id', $request->id)->get();
        return $city;
    }

    public function getproducts(Request $request, inventory $inventory)
    {
        $result = $inventory->getproductsBySubDepartment($request->id);
        return $result;
    }

    public function getData(Request $request, inventory $inventory, Brand $brand)
    {
        $department = $inventory->department();
        $subdepartment = $inventory->subDepartment();
        $uom = $inventory->uom();
        $branch = $inventory->branch();
        $brandList  = $brand->getBrand();
        $tagsList  = Tag::getTags();
        $data = $inventory->get_details($request->id);
        $mode = $inventory->getProductMode();
        $images = $inventory->getImages($request->id);
        $references =  $inventory->getReferences($request->id);
        $prices = $inventory->getpricebyproduct($data[0]->id);
        $websites = DB::table("website_details")->where("company_id", session("company_id"))->where("status", 1)->get();
        $totaladdons = AddonCategory::where("company_id", session("company_id"))->where("mode", "addons")->get();
        $selectedAddons = InventoryAddon::where("product_id", $data[0]->id)->pluck("addon_id");
        $selectedWebsites = WebsiteProduct::where("inventory_id", $data[0]->id)->where("status",1)->pluck("website_id");
        $extras = DB::table("extra_products")->whereNull("parent")->get();
        $selectedExtras = DB::table("inventory_extra_products")->where("product_id", $data[0]->id)->pluck("extra_product_id");

        $inventoryBrand = DB::table("inventory_brands")->where("inventory_id", $data[0]->id)->pluck("brand_id");

        $inventoryTags = DB::table("inventory_tags")->where("inventory_id", $data[0]->id)->pluck("tag_id");

        foreach ($references as $refval) {
            $ref[] = $refval->refrerence;
        }
        if (!empty($ref)) {
            $references = implode(",", $ref);
        } else {
            $references = "";
        }
            
        // if(Auth::user()->username == 'demoadmin'){
            return view('Inventory.edit-debug', compact('data', 'department', 'subdepartment', 'uom', 'branch', 'mode', 'images', 'references', 'prices', 'totaladdons', 'selectedAddons', 'websites', 'selectedWebsites', 'extras', 'selectedExtras', 'tagsList', 'brandList', 'inventoryBrand', 'inventoryTags'));
        
        // }else{
        //  return view('Inventory.edit', compact('data', 'department', 'subdepartment', 'uom', 'branch', 'mode', 'images', 'references', 'prices', 'totaladdons', 'selectedAddons', 'websites', 'selectedWebsites', 'extras', 'selectedExtras', 'tagsList', 'brandList', 'inventoryBrand', 'inventoryTags'));
        // }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(inventory $inventory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function edit(inventory $inventory) {}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\inventory  $inventory
     * @return \Illuminate\Http\Response
     */



    public function modify(Request $request)
    {
        $invent = new inventory();
        $websiteMode = null;

        $fields = [
            'company_id'           => session('company_id'),
            'department_id'        => $request->depart,
            'sub_department_id'    => $request->subDepart,
            'priority'             => $request->priority,
            'uom_id'               => $request->uom,
            'cuom'                 => $request->cuom,
            'product_mode'         => $request->product_mode,
            'item_code'            => $request->code,
            'product_name'         => $request->name,
            'product_description'  => $request->description,
            'status'               => 1,
            'created_at'           => date('Y-m-d H:s:i'),
            'updated_at'           => date('Y-m-d H:s:i'),
            'weight_qty'           => $request->weight,
            'short_description'    => $request->sdescription,
            'details'              => $request->details,
            'brand_id'             => $request->brand,
            'actual_image_size'    => isset($request->actual_image_size) ? 1 : 0,
        ];


        //    return empty($request->file('image')) ? 1 : 0;
              
        if(!empty($request->get('galleryImage'))){

            $gallery = explode(',',$request->get('galleryImage'));
           if(Auth::user()->username == 'demoadmin'){
               return $gallery[1];
           }
           foreach($gallery as $val){
            if(Auth::user()->username == 'demoadmin'){
               return "out".$val;
            }
                if(File::exists('storage/images/products/'.$val)){
                    File::delete('storage/images/products/'.$val);
                 }
           }
        }
    
        if(!empty($request->urlGalleryImage)){
            $gallery = explode(',',$request->get('urlGalleryImage'));
            foreach($gallery as $val){
                Cloudinary::destroy($val);
            }
         }        

        if (!empty($request->file('image'))) {
            $image = $request->file('image');

            if (!in_array(session('company_id'),[95, 102, 104]) && !in_array(Auth::user()->username,['demoadmin','fnkhan'])) {

                    $request->validate([
                        'image' => 'image|mimes:jpeg,png,jpg,webp|max:1024',
                    ]);
            }

            $imageName = time() . '-' . pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $image->getClientOriginalExtension();

            if (in_array(session('company_id'), [95, 102, 104]) || in_array(Auth::user()->username,['demoadmin','fnkhan'])) { //cloudinary image save fro kashee
                $transformationArray = [];

                if (!isset($request->actual_image_size)) {
                    $transformationArray['width']  = 400;
                    $transformationArray['height'] = 400;
                    $transformationArray['crop']   = 'scale';
                }
                $transformationArray['quality']  = 'auto';
                $transformationArray['fetch']    = 'auto';

                $company_name = DB::table('company')->where('company_id', session('company_id'))->first();

                $folder = strtolower(str_replace(' ','',$company_name->name));

                // previous image remove
                $getPreviousImage = $invent->getPreviousImage($request->id);
                if($getPreviousImage != null){
                      Cloudinary::destroy($getPreviousImage);
                }


                $imageData = Cloudinary::upload($image->getRealPath(), [
                                        'public_id'      => strtolower($imageName),
                                        'folder'         => $folder,
                                        'transformation' => $transformationArray
                                    ])->getSecurePath();

                $fields['image'] = strtolower($imageName);
                $fields['url']   = $imageData;

            } else {
                $transFormation = [];
                if (!isset($request->actual_image_size)) {
                    $transFormation = ['width' => 400, "height" => 400];
                }

                $returnImageValue = $this->uploads($image, "images/products/","",$transFormation);
                $fields['image']  = $returnImageValue['fileName'];                
            }
        }

  
         $result = $invent->modify($fields, $request->id);

        $result = $invent->modifyReminder($request->reminder_id, $request->reminder);

        $result = DB::table("inventory_reference")->where("product_id", $request->id)->delete();

        //Inventory References Here
        $ref = explode(",", $request->reference);
        foreach ($ref as $value) {
            DB::table("inventory_reference")->insert([
                "product_id" => $request->id,
                "refrerence" => $value
            ]);
        }

        $prices = $invent->getpricebyproduct($request->id);

        // if ($request->rp != $prices[0]->retail_price || $request->wp != $prices[0]->wholesale_price || $request->op != $prices[0]->online_price || $request->dp != $prices[0]->discount_price) 
        // {
        // return $prices;
        $items = [
            'status_id' => 2,
        ];

        $price = $invent->updateprice($prices[0]->price_id, $items);

        //insert new prices
        $items = [
            'cost_price'      => ($request->cost_price != "" ? $request->cost_price : "0.00"),
            'actual_price'    => ($request->ap != "" ? $request->ap : "0.00"),
            'tax_rate'        => ($request->taxrate != "" ? $request->taxrate : "0"),
            'tax_amount'      => ($request->taxamount != "" ? $request->taxamount : "0"),
            'retail_price'    => ($request->rp != "" ?  $request->rp : "0.00"),
            'wholesale_price' => ($request->wp != "" ? $request->wp : "0.00"),
            'online_price'    => ($request->op != "" ? $request->op : "0.00"),
            'discount_price'  => ($request->dp != "" ? $request->dp : "0.00"),
            'product_id'      => $request->id,
            'status_id'       => 1,
        ];
        $price = $invent->insert_pram('inventory_price', $items);
        // }

        $terminals = DB::table("terminal_details")->where("branch_id", session("branch"))->where("status_id", 1)->get();
        foreach ($terminals as $value) {
            $items = [
                "branchId"   => session("branch"),
                "terminalId" => $value->terminal_id,
                "productId"  => $request->id,
                "status"     => 1,
                "date"       => date("Y-m-d"),
                "time"       => date("H:i:s"),
            ];
            DB::table("inventory_download_status")->insert($items);
        }
       
        if (!empty($request->website) && isset($request->showProductWebsite)) {
            WebsiteProduct::where("inventory_id", $request->id)->update(['status'=>0,'updated_at'=>date("Y-m-d H:i:s")]);
            // foreach ($request->website as $website) {
                WebsiteProduct::create([
                    "website_id"   => $request->website,
                    "inventory_id" => $request->id,
                ]);
            // }
        }else{
            WebsiteProduct::where("inventory_id", $request->id)->update(['status'=>0,'updated_at'=>date("Y-m-d H:i:s")]); 
        }

        DB::table('inventory_tags')->where("inventory_id", $request->id)->delete();
        if (!empty($request->tags)) {
            foreach ($request->tags as $val) {
                DB::table('inventory_tags')->insert([
                    "inventory_id" => $request->id,
                    "tag_id"       => $val,
                    'created_at'   => date("Y-m-d H:i:s")
                ]);
            }
        }


        $this->sendPushNotification($request->code, $request->name, "update");
        //   return redirect()->back();
        return  1;
    }
    

    public function unLink_websiteProduct(Request $request){

        if(isset($request->website_id) && isset($request->product_id)){

            if(DB::table('inventory_general')->where('id',$request->product_id)->where('company_id',session('company_id'))->count() == 0){
                return response()->json('Product not found!',500);
            }
           
           if(!WebsiteProduct::where("inventory_id", $request->product_id)->where('website_id',$request->website_id)->update(['status'=>0,'updated_at'=>date("Y-m-d H:i:s")])){
            return response()->json('Error! this product not unlink to website',500);
           }

           return response()->json('Success!',200);
        }

       return response()->json('bad request',500);   
    }

    public function allWebsiteProduct_unlink(Request $request){

        if(isset($request->product_id)){

            if(DB::table('inventory_general')->whereIn('id',$request->product_id)->where('company_id',session('company_id'))->count() == 0){
                return response()->json('Product not found!',500);
            }
           
           if(!WebsiteProduct::whereIn("inventory_id", $request->product_id)->update(['status'=>0,'updated_at'=>date("Y-m-d H:i:s")])){
            return response()->json('All product not unlink to website!',500);
           }

           return response()->json('Success!',200);
        }

       return response()->json('bad request',500);   
    }

    /**S
     * Remove the specified resource from storage.
     *
     * @param  \App\inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function deleteInvent(Request $request, inventory $inventory)
    {
        $result = $inventory->delete_inventory($request->id, $request->status);
        return $result;
    }

    public function multipleActiveInvent(Request $request, inventory $inventory)
    {
        $result = $inventory->multiple_active_inventory($request->inventid);
        return $result;
    }

    public function chk_itemcode_exists(Request $request, inventory $inventory)
    {
        $count = $inventory->chk_itemcode($request->itemcode);
        return $count;
    }

    public function stockopening(inventory $inventory)
    {
        $product = $inventory->getproducts();
        $branches = $inventory->getBranches();
        $uom = $inventory->uom();

        return view('Inventory.stockopening', compact('product', 'uom', 'branches'))->With('status', '');
    }

    public function getUOMID(Request $request, inventory $inventory)
    {
        $uom = $inventory->getUOMID($request->id);
        return $uom;
    }

    public function create_stock_opening(Request $request, inventory $inventory, Purchase $purchase, stock $stock)
    {


        $rules = [
            'product' => 'required',
            'branch' => 'required',
            'uom' => 'required',
            'qty' => 'required',
            'cp' => 'required',
        ];
        $this->validate($request, $rules);

        if ($inventory->getCheckByStock($request->product, $request->branch)) {
            session(['status' => 'Product is already openend.']);
            return redirect("stock-opening")->With('status', 'Product is already openend');
        } else {

            $grn = $purchase->getGrn();
            $grn = $grn + 1;
            $gen = [
                'GRN' => "GRN-" . $grn,
                'user_id' => session('userid'),
                'created_at' => date('Y-m-d H:s:i'),
                'updated_at' => date('Y-m-d H:s:i'),
            ];
            $gen_res = $purchase->receiving_general($gen);

            $fields = [
                'GRN' => $gen_res,
                'item_id' => $request->product,
                'qty_rec' => $request->qty,
            ];

            $items = [
                'grn_id' => $gen_res,
                'product_id' => $request->product,
                'uom' => $request->uom,
                'cost_price' => $request->cp,
                'retail_price' => $request->rp,
                'wholesale_price' => $request->wp,
                'discount_price' => $request->dp,
                'qty' => $request->qty,
                'balance' => $request->qty,
                'status_id' => 1,
                'branch_id' =>  $request->branch,
            ];

            $stock_id = DB::table('purchase_rec_stock_opening')->insertGetId($fields);
            $lastStock = $stock->getLastStock($request->product);
            $stk = empty($lastStock) ? 0 : $lastStock[0]->stock;
            $stk = $stk + $request->qty;


            $report = [
                'date' => date('Y-m-d H:s:i'),
                'product_id' => $request->product,
                'foreign_id' => $stock_id,
                'branch_id' => session('branch'),
                'qty' => $request->qty,
                'stock' => $stk,
                'cost' => $request->cp,
                'retail' => $request->rp,
                'narration' => 'Stock Opening',
            ];

            $stock_report = $stock->stock_report($report);

            if (DB::table('inventory_stock')->insert($items)) {
                session(['status' => '']);
                return redirect("stock-opening");
            } else {
                return 0;
            }
        }
    }

    //Upload CSV FILE CODE
    public function uploadInventory(Request $request, inventory $inventory)
    {
        $rules = [
            'file' => 'required',
        ];
        $this->validate($request, $rules);

        if ($request->input('submit') != null) {

            $deptandSubdept = $inventory->getDeptandSubDept();

            $file = $request->file('file');

            // File Details
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv", "xlsx");

            // 2MB in Bytes
            $maxFileSize = 5000000; //2097152;

            // Check file extension
            if (in_array(strtolower($extension), $valid_extension)) {

                // Check file size
                if ($fileSize <= $maxFileSize) {

                    // File upload location
                    $location = 'uploads';

                    // Upload file
                    $file->move(public_path('assets/uploads/'), $filename);

                    // Import CSV to Database
                    $filepath = public_path("assets/uploads" . "/" . $filename);

                    // Reading file
                    $file = fopen($filepath, "r");

                    $importData_arr = array();
                    $i = 0;

                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);

                        //Skip first row (Remove below comment if you want to skip the first row)
                        if ($i == 0) {
                            $i++;
                            continue;
                        }
                        for ($c = 0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata[$c];
                        }
                        $i++;
                    } //while bracket closed
                    fclose($file);
                    if (isset($request->update)) {
                        foreach ($importData_arr as $importData) {
                            $result = $inventory->updateProductName($importData[0], $importData[2]);
                            $result = $inventory->findProductByIdInPriceTable($importData[0]);

                            if ($result == true) {
                                $inventory->updateToRetailPrice($importData[0], $importData[3], $importData[4], $importData[5], $importData[6], $importData[7], $importData[8], $importData[9]);
                            }
                        }
                    } else {
                        // Insert to MySQL database
                        foreach ($importData_arr as $importData) {
                            // echo  $importData[1];  exit;  
                            $departmentId = "";
                            $subDepartmentId = "";
                            $uomId = "";

                            $departmentCount = DB::table("inventory_department")->where("department_name", $importData[10])->where("company_id", session("company_id"))->count();
                            if ($departmentCount == 0) {
                                $departmentId = DB::table("inventory_department")->insertGetId([
                                    "company_id" => session("company_id"),
                                    "department_name" => $importData[10],
                                    "date" => date("Y-m-d"),
                                    "time" => date("H:i:s"),
                                ]);
                                $departmentId = $departmentId;
                            } else {
                                $departmentId = DB::table("inventory_department")->where("department_name", $importData[10])->where("company_id", session("company_id"))->get();
                                $departmentId = $departmentId[0]->department_id;
                            }

                            $subDepartmentCount = DB::table("inventory_sub_department")->where("department_id", $departmentId)->where("sub_depart_name", $importData[11])->count();
                            if ($subDepartmentCount == 0) {
                                $subDepartmentId = DB::table("inventory_sub_department")->insertGetId([
                                    "department_id" => $departmentId,
                                    "sub_depart_name" => $importData[11],

                                ]);
                                $subDepartmentId = $subDepartmentId;
                            } else {
                                $subDepartmentId = DB::table("inventory_sub_department")->where("department_id", $departmentId)->where("sub_depart_name", $importData[11])->get();
                                $subDepartmentId = $subDepartmentId[0]->sub_department_id;
                            }

                            $uomCount = DB::table("inventory_uom")->where("name", $importData[12])->count();
                            if ($uomCount == 0) {
                                $uomId = DB::table("inventory_uom")->insertGetId([
                                    "name" => $importData[12],

                                ]);
                                $uomId = $uomId;
                            } else {
                                $uomId = DB::table("inventory_uom")->where("name", $importData[12])->get();
                                $uomId = $uomId[0]->uom_id;
                            }

                            $count = DB::table("inventory_general")->where("company_id", session("company_id"))->where("item_code", $importData[0])->count();
                            if ($count == 0) {
                                $insertData = array(
                                    'company_id' => session('company_id'),
                                    'department_id' => $departmentId, //isset($deptandSubdept[0]->departID) ? $deptandSubdept[0]->departID : 0,
                                    'sub_department_id' => $subDepartmentId, //isset($deptandSubdept[0]->sub_department_id) ? $deptandSubdept[0]->sub_department_id : 0,
                                    'uom_id' => $uomId, //1,
                                    'product_mode' => 2,
                                    'item_code' => $importData[0],
                                    'product_name' => $importData[1], // preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $importData[1]), Raza asked to remove this   
                                    'product_description' => $importData[2],
                                    'image' => '',
                                    'weight_qty' => 1,
                                    'cuom' => 1,
                                    'status' => 1,
                                    'slug' => strtolower(str_replace(' ', '-', preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $importData[1]))) . "-" . strtolower(Str::random(4)),
                                    'created_at' => date('Y-m-d H:s:i'),
                                    'updated_at' => date('Y-m-d H:s:i')
                                );

                                $result = $inventory->insert($insertData);



                                $inventory->ReminderInsert($result, 0);

                                $prices = [
                                    "actual_price" => $importData[3],
                                    "cost_price" => $importData[3],
                                    "tax_rate" => $importData[4],
                                    "tax_amount" => $importData[5],
                                    "retail_price" => $importData[6],
                                    "wholesale_price" => $importData[7],
                                    "online_price" => $importData[8],
                                    "discount_price" => $importData[9],
                                    "product_id" => $result,
                                    "status_id" => 1,
                                ];

                                $inventory->insert_pram("inventory_price", $prices);
                            }
                        }
                    }
                    Session::flash('message', '1');
                } else {
                    Session::flash('message', '2');
                }
            }

            // Redirect to index
            // return redirect()->action('InventoryController@index');
            return redirect()->route('invent-list');
        }
    }

    function setInputEncoding($file)
    {
        $fileContent = file_get_contents($file->path());
        $enc = mb_detect_encoding($fileContent, mb_list_encodings(), true);
        Config::set('excel.imports.csv.input_encoding', $enc);
    }




    public static function convert_from_latin1_to_utf8_recursively($dat)
    {
        if (is_string($dat)) {
            return utf8_encode($dat);
        } elseif (is_array($dat)) {
            $ret = [];
            foreach ($dat as $i => $d) $ret[$i] = self::convert_from_latin1_to_utf8_recursively($d);

            return $ret;
        } elseif (is_object($dat)) {
            foreach ($dat as $i => $d) $dat->$i = self::convert_from_latin1_to_utf8_recursively($d);

            return $dat;
        } else {
            return $dat;
        }
    }



    public function all_invent_remove(Request $request, inventory $inventory)
    {

        $result = $inventory->update_all_inventory_status($request->inventid, $request->statusid);

        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

    public function all_invent_delete(Request $request, inventory $inventory)
    {

        $result = $inventory->delete_all_inventory($request->inventid);

        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

    public function update_department(Request $request, inventory $inventory)
    {

        $result = $inventory->update_department($request->inventid, $request->deptId);

        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

    public function update_sub_department(Request $request, inventory $inventory)
    {

        $result = $inventory->update_sub_department($request->inventid, $request->subdeptId, $request->deptID);

        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

    public function update_uom(Request $request, inventory $inventory)
    {

        $result = $inventory->update_uom($request->inventid, $request->uomId);

        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

    public function update_tax(Request $request, inventory $inventory)
    {
        $result = 0;
        $products = $inventory->get_all_product_taxes($request->prev_tax);

        foreach ($products as $product) {
            $newTaxAmount = $product->actual_price * ($request->new_tax / 100);
            $retailPrice = $product->actual_price + $newTaxAmount;
            $result = $inventory->update_single_product_tax($product->price_id, $request->new_tax, $newTaxAmount, $retailPrice);
        }

        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

    public function get_departments(Request $request, inventory $inventory)
    {

        $result = $inventory->get_departments();

        if ($result) {
            return $result;
        } else {
            return 0;
        }
    }

    public function get_sub_departments(Request $request, inventory $inventory)
    {

        $result = $inventory->get_sub_departments($request->id);

        if ($result) {
            return $result;
        } else {
            return 0;
        }
    }

    public function get_uom(Request $request, inventory $inventory)
    {
        $result = $inventory->get_uom();

        if ($result) {
            return $result;
        } else {
            return 0;
        }
    }

    public function get_taxes(Request $request, inventory $inventory)
    {
        $result = $inventory->get_taxes();

        if ($result) {
            return $result;
        } else {
            return 0;
        }
    }

    public function get_names(Request $request, inventory $inventory)
    {

        $result = $inventory->item_name($request->ids);
        if ($result) {
            return $result;
        } else {
            return 0;
        }
    }

    public function insertnewprice(inventory $inventory, request $request)
    {

        $retail = 0;
        $discount = 0;
        $wholesale = 0;
        $online = 0;

        if ($request->departmentId == "") {

            foreach ($request->productid as $productid) {
                $oldprice = $inventory->getpricebyproduct($productid);
                if ($request->pricemode == 1) {
                    if ($request->rp != "" && $request->rp != 0) {
                        $retail = ($oldprice[0]->retail_price * $request->rp) / 100;
                        $retail = $retail + $oldprice[0]->retail_price;
                    } else {
                        $retail = $oldprice[0]->retail_price;
                    }
                    if ($request->wp != "" && $request->wp != 0) {
                        $wholesale = ($oldprice[0]->wholesale_price * $request->wp) / 100;
                        $wholesale = $wholesale + $oldprice[0]->wholesale_price;
                    } else {
                        $wholesale = $oldprice[0]->wholesale_price;
                    }
                    if ($request->dp != "" && $request->dp != 0) {
                        $discount = ($oldprice[0]->discount_price * $request->dp) / 100;
                        $discount = $discount + $oldprice[0]->discount_price;
                    } else {
                        $discount = $oldprice[0]->discount_price;
                    }
                    if ($request->op != "" && $request->op != 0) {
                        $online = ($oldprice[0]->online_price * $request->op) / 100;
                        $online = $online + $oldprice[0]->online_price;
                    } else {
                        $online = $oldprice[0]->online_price;
                    }

                    //update change status 2
                    $items = [
                        'status_id' => 2,
                    ];
                    $price = $inventory->updateprice($oldprice[0]->price_id, $items);

                    //insert new prices
                    $items = [
                        'retail_price' => $retail,
                        'wholesale_price' => $wholesale,
                        'online_price' => $online,
                        'discount_price' => $discount,
                        'product_id' => $oldprice[0]->product_id,
                        'status_id' => 1,
                    ];
                    $price = $inventory->insert_pram('inventory_price', $items);
                }
                //end if
                else {
                    if ($request->rp != "" && $request->rp != 0) {
                        $retail = $request->rp + $oldprice[0]->retail_price;
                    } else {
                        $retail = $oldprice[0]->retail_price;
                    }
                    if ($request->wp != "" && $request->wp != 0) {
                        $wholesale = $request->wp + $oldprice[0]->wholesale_price;
                    } else {
                        $wholesale = $oldprice[0]->wholesale_price;
                    }
                    if ($request->dp != "" && $request->dp != 0) {
                        $discount = $request->dp + $oldprice[0]->discount_price;
                    } else {
                        $discount = $oldprice[0]->discount_price;
                    }
                    if ($request->op != "" && $request->op != 0) {
                        $online = $request->op + $oldprice[0]->online_price;
                    } else {
                        $online = $oldprice[0]->online_price;
                    }

                    //update change status 2
                    $items = [
                        'status_id' => 2,
                    ];
                    $price = $inventory->updateprice($oldprice[0]->price_id, $items);

                    //insert new prices
                    $items = [
                        'retail_price' => $retail,
                        'wholesale_price' => $wholesale,
                        'online_price' => $online,
                        'discount_price' => $discount,
                        'product_id' => $oldprice[0]->product_id,
                        'status_id' => 1,
                    ];
                    $price = $inventory->insert_pram('inventory_price', $items);
                }
            } // foreach end

        } else {
            $products = DB::table("inventory_general")->where("department_id", $request->departmentId)->where("sub_department_id", $request->subDepartmentId)->where("company_id", session("company_id"))->get();
            foreach ($products as $product) {
                $oldprice = $inventory->getpricebyproduct($product->id);
                if ($request->pricemode == 1) {
                    if ($request->rp != "" && $request->rp != 0) {
                        $retail = ($oldprice[0]->retail_price * $request->rp) / 100;
                        $retail = $retail + $oldprice[0]->retail_price;
                    } else {
                        $retail = $oldprice[0]->retail_price;
                    }
                    if ($request->wp != "" && $request->wp != 0) {
                        $wholesale = ($oldprice[0]->wholesale_price * $request->wp) / 100;
                        $wholesale = $wholesale + $oldprice[0]->wholesale_price;
                    } else {
                        $wholesale = $oldprice[0]->wholesale_price;
                    }
                    if ($request->dp != "" && $request->dp != 0) {
                        $discount = ($oldprice[0]->discount_price * $request->dp) / 100;
                        $discount = $discount + $oldprice[0]->discount_price;
                    } else {
                        $discount = $oldprice[0]->discount_price;
                    }
                    if ($request->op != "" && $request->op != 0) {
                        $online = ($oldprice[0]->online_price * $request->op) / 100;
                        $online = $online + $oldprice[0]->online_price;
                    } else {
                        $online = $oldprice[0]->online_price;
                    }

                    //update change status 2
                    $items = [
                        'status_id' => 2,
                    ];
                    $price = $inventory->updateprice($oldprice[0]->price_id, $items);

                    //insert new prices
                    $items = [
                        'retail_price' => $retail,
                        'wholesale_price' => $wholesale,
                        'online_price' => $online,
                        'discount_price' => $discount,
                        'product_id' => $oldprice[0]->product_id,
                        'status_id' => 1,
                    ];
                    $price = $inventory->insert_pram('inventory_price', $items);
                }
                //end if
                else {
                    if ($request->rp != "" && $request->rp != 0) {
                        $retail = $request->rp + $oldprice[0]->retail_price;
                    } else {
                        $retail = $oldprice[0]->retail_price;
                    }
                    if ($request->wp != "" && $request->wp != 0) {
                        $wholesale = $request->wp + $oldprice[0]->wholesale_price;
                    } else {
                        $wholesale = $oldprice[0]->wholesale_price;
                    }
                    if ($request->dp != "" && $request->dp != 0) {
                        $discount = $request->dp + $oldprice[0]->discount_price;
                    } else {
                        $discount = $oldprice[0]->discount_price;
                    }
                    if ($request->op != "" && $request->op != 0) {
                        $online = $request->op + $oldprice[0]->online_price;
                    } else {
                        $online = $oldprice[0]->online_price;
                    }

                    //update change status 2
                    $items = [
                        'status_id' => 2,
                    ];
                    $price = $inventory->updateprice($oldprice[0]->price_id, $items);

                    //insert new prices
                    $items = [
                        'retail_price' => $retail,
                        'wholesale_price' => $wholesale,
                        'online_price' => $online,
                        'discount_price' => $discount,
                        'product_id' => $oldprice[0]->product_id,
                        'status_id' => 1,
                    ];
                    $price = $inventory->insert_pram('inventory_price', $items);
                }
            }
        }

        return 1;
    }

    public function stockadjustment_show(inventory $inventory, request $request, stock $stock)
    {
        $branches = $stock->getBranches();
        return view('Inventory.stockadjustment', compact('branches'));
    }

    public function getstock_value(inventory $inventory, request $request)
    {
        $branch = ((session('roleId') == 17 or session('roleId') == 2) ? $request->branch : session('branch'));
        $stock = $inventory->getstock_value($request->productid, $branch);
        return $stock;
    }

    public function getgrns(inventory $inventory, request $request)
    {
        $stock = $inventory->getgrns($request->productid);
        return $stock;
    }


    public function update_stockadjustment(inventory $inventory, request $request, stock $stockApp)
    {

        $quantity = $request->qty * (-1);
        $newbal = 0;
        $stockid = 0;
        $productid = 0;
        foreach ($request->stockid as $value) {
            if ($quantity < 0) {
                $quantity = $quantity * (-1);
            }
            $stockid = $value;
            $balance = $inventory->getbalance($value);
            if ($balance[0]->balance > $quantity) {
                $newbal = $balance[0]->balance - $quantity;

                $items = [
                    'balance' => $newbal,
                ];
                //update the new balance in inventory stock
                $updatebalance = $inventory->update_balance_stock($value, $items);
                $productid = $balance[0]->product_id;
                $this->stockreport($stockApp, $balance[0]->product_id, $value, $request->qty, $balance[0]->cost_price, $balance[0]->retail_price, $request->narration);
                // return 1;
            } elseif ($balance[0]->balance == $quantity) {
                $newbal = $balance[0]->balance - $quantity;

                $items = [
                    'balance' => $newbal,
                    'status_id' => 2,
                ];
                //update the new balance in inventory stock
                $productid = $balance[0]->product_id;
                $updatebalance = $inventory->update_balance_stock($value, $items);
                $this->stockreport($stockApp, $balance[0]->product_id, $value, $request->qty, $balance[0]->cost_price, $balance[0]->retail_price, $request->narration);
                // return 1;
            } else {
                // $quantity = $balance[0]->balance;
                $quantity = $balance[0]->balance - $quantity;
                $items = [
                    'balance' => 0,
                    'status_id' => 2,
                ];
                //update the new balance in inventory stock
                $productid = $balance[0]->product_id;
                $updatebalance = $inventory->update_balance_stock($value, $items);
                $this->stockreport($stockApp, $balance[0]->product_id, $value, $balance[0]->balance, $balance[0]->cost_price, $balance[0]->retail_price, $request->narration);
            }
        }
        // return $productid;
        $inventory = DB::table("inventory_general")->where("id", $productid)->get();
        $terminals = DB::table("terminal_details")->where("branch_id", session("branch"))->where("status_id", 1)->get();
        foreach ($terminals as $value) {
            $items = [
                "branchId" => ((session('roleId') == 17 or session('roleId') == 12) ? $request->branch : session('branch')),
                "terminalId" => $value->terminal_id,
                "productId" => $productid,
                "status" => 1,
                "date" => date("Y-m-d"),
                "time" => date("H:i:s"),
            ];
            DB::table("inventory_download_status")->insert($items);
        }
        $this->sendPushNotification($inventory[0]->item_code, $inventory[0]->product_name, "update");
        return 1;
    }
    function stockreport(stock $stockApp, $productid, $stockid, $qty, $cp, $rp, $narration)
    {
        $lastStock = $stockApp->getLastStock($productid);
        $stk = empty($lastStock) ? 0 : $lastStock[0]->stock;
        $stk = $stk - $qty;

        //after that stock report table main insert
        $report = [
            'date' => date('Y-m-d H:s:i'),
            'product_id' => $productid,
            'foreign_id' => $stockid,
            'branch_id' => session('branch'),
            'qty' => $qty,
            'stock' => $stk,
            'cost' => $cp,
            'retail' => $rp,
            'narration' => "(Stock Adjustment) " . $narration,
            'adjustment_mode' => "0", // 0 for negative

        ];
        $stock_report = $stockApp->stock_report($report);
    }

    function getDeleteImage(Request $request, inventory $inventory)
    {
        if (DB::table("inventory_images")->where('id', $request->id)->delete()) {
            $image_path = public_path('assets/images/products/' . $request->image);  // Value is not URL but directory file path
            if ($request->image != "") {
                unlink($image_path);
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    function creategrnadjustmnet(Request $request, inventory $inventory, Purchase $purchase, stock $stock)
    {
        //grn ban rahi ha
        $grn = $purchase->getGrn();
        $grn = $grn + 1;
        $gen = [
            'GRN' => "GRN-" . $grn,
            'user_id' => session('userid'),
            'created_at' => date('Y-m-d H:s:i'),
            'updated_at' => date('Y-m-d H:s:i'),
        ];
        $gen_res = $purchase->receiving_general($gen);

        //grn stock adjustment insert
        $fields = [
            'GRN' => $gen_res,
            'item_id' => $request->productid,
            'qty_rec' => $request->qty,
        ];

        $stock_id = $inventory->insertgeneral('purchase_rec_stock_adjustment', $fields);

        //get uom here
        $um = $inventory->getproductdetails($request->productid);

        //get stock from inventory stock
        //        $stocked = $inventory->getbalance_byproduct($request->productid);
        //        $invenStock = empty($stocked) ? 0 : $stocked[0]->stock;
        //        $invenStock = $invenStock + $request->qty;


        $items = [
            'grn_id' => $gen_res,
            'product_id' => $request->productid,
            'uom' => $um[0]->uom_id,
            'cost_price' => $request->amount,
            'retail_price' => "0.00",
            'wholesale_price' => "0.00",
            'discount_price' => "0.00",
            'qty' => $request->qty,
            'balance' => $request->qty,
            'status_id' => 1,
            'branch_id' => ((session('roleId') == 17 or session('roleId') == 2) ? $request->branch : session('branch')),
        ];
        // return $items;
        //        //insert stock table
        $stockadd = $inventory->insertgeneral('inventory_stock', $items);

        //get last stock from stock report
        $lastStock = $stock->getLastStock($request->productid);
        $stk = empty($lastStock) ? 0 : $lastStock[0]->stock;
        $stk = $stk + $request->qty;

        //stock report k table main insert
        $items = [
            'date' => date('Y-m-d H:s:i'),
            'product_id' => $request->productid,
            'foreign_id' => $stockadd,
            'branch_id' => ((session('roleId') == 17 or session('roleId') == 12) ? $request->branch : session('branch')),
            'qty' => $request->qty,
            'stock' => $stk,
            'cost' => $request->amount,
            'retail' => "0.00",
            'narration' => "(Stock Adjustment) " . $request->narration,
            'adjustment_mode' => "1", // 1 for positive

        ];
        $result = $inventory->insertgeneral('inventory_stock_report_table', $items);
        $inventory = DB::table("inventory_general")->where("id", $request->productid)->get();

        $terminals = DB::table("terminal_details")->where("branch_id", session("branch"))->where("status_id", 1)->get();
        foreach ($terminals as $value) {
            $items = [
                "branchId" => session("branch"),
                "terminalId" => $value->terminal_id,
                "productId" => $request->productid,
                "status" => 1,
                "date" => date("Y-m-d"),
                "time" => date("H:i:s"),
            ];
            DB::table("inventory_download_status")->insert($items);
        }

        $this->sendPushNotification($inventory[0]->item_code, $inventory[0]->product_name, "update");
        return 1;
    }

    public function exportCsv(Request $request, inventory $inventory)
    {
        $fileName = 'stock_opening.csv';
        $tasks = $inventory->getStockInventory(session("company_id"));


        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('S.No', 'Name', 'Cost', 'Qty', 'Item Code');

        $callback = function () use ($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $row['S.No']  = $task->id;
                $row['Name']    = $task->product_name;
                $row['Cost']  = 0;
                $row['Qty']  = 0;
                $row['Item Code']  = $task->item_code;

                fputcsv($file, array($row['S.No'], $row['Name'], $row['Cost'], $row['Qty'], $row['Item Code']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportInventoryRetailPriceUpdateCsv(Request $request, inventory $inventory)
    {
        $fileName = 'product-price-list.csv';
        $tasks = $inventory->getInventoryListForRetailPriceUpdate(session("company_id"));

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Product Id', 'Product Code', 'Product Name', 'Actual Price', 'Tax Rate', 'Tax Amount', 'Retail Price', 'Wholesale Price', 'Online Price', 'Discount Price'); //, 'Product Description'

        $callback = function () use ($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $row['productID']  = $task->productID;
                $row['ItemCode']    = $task->ItemCode;
                $row['Name']  = $task->ItemName;
                // $row['Description']  = $task->Description;
                $row['actual_price']  = $task->actual_price;
                $row['tax_rate']  = $task->tax_rate;
                $row['tax_amount']  = $task->tax_amount;
                $row['RetailPrice']  = $task->RetailPrice;
                $row['wholesale_price']  = $task->wholesale_price;
                $row['online_price']  = $task->online_price;
                $row['discount_price']  = $task->discount_price;
                //, $row['Description']
                fputcsv($file, array($row['productID'], $row['ItemCode'], $row['Name'], $row['actual_price'], $row['tax_rate'], $row['tax_amount'], $row['RetailPrice'], $row['wholesale_price'], $row['online_price'], $row['discount_price']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportSampleInventoryCsv(Request $request, inventory $inventory)
    {
        $fileName = 'sample-inventory-list.csv';
        $tasks = $inventory->getInventoryListForRetailPriceUpdate(session("company_id"));

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Product Code', 'Product Name', 'Product Description', 'Actual Price', 'Tax Rate', 'Tax Amount', 'Retail Price', 'Wholesale Price', 'Online Price', 'Discount Price', 'Department', 'Sub Department', 'Unit'); //, 'Product Description'

        $callback = function () use ($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            for ($i = 0; $i < 2; $i++) {
                $row['ItemCode']    = "1001";
                $row['Name']  = "LCD";
                $row['Description']  = "This is the uploaded product from csv";
                $row['actual_price']  = "500";
                $row['tax_rate']  = "0";
                $row['tax_amount']  = "0";
                $row['RetailPrice']  = "500";
                $row['wholesale_price']  = "0";
                $row['online_price']  = "0";
                $row['discount_price']  = "0";
                $row['department']  = "General";
                $row['sub_department']  = "General";
                $row['unit']  = "Unit";
                fputcsv($file, array($row['ItemCode'], $row['Name'], $row['Description'], $row['actual_price'], $row['tax_rate'], $row['tax_amount'], $row['RetailPrice'], $row['wholesale_price'], $row['online_price'], $row['discount_price'], $row['department'], $row['sub_department'], $row['unit']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    //Upload STOCK OPENING CSV
    public function uploadStockCsv(Request $request, inventory $inventory, purchase $purchase, stock $stock)
    {
        $rules = [
            'file' => 'required',
        ];

        $this->validate($request, $rules);

        if ($request->input('submit') != null) {

            $deptandSubdept = $inventory->getDeptandSubDept();

            $file = $request->file('file');

            // File Details
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv", "xlsx");

            // 2MB in Bytes
            $maxFileSize = 2097152;

            // Check file extension
            if (in_array(strtolower($extension), $valid_extension)) {

                // Check file size
                if ($fileSize <= $maxFileSize) {

                    // File upload location
                    $location = 'uploads';

                    // Upload file
                    $file->move(public_path('assets/uploads/'), $filename);

                    // Import CSV to Database
                    $filepath = public_path("assets/uploads" . "/" . $filename);

                    // Reading file
                    $file = fopen($filepath, "r");

                    $importData_arr = array();
                    $i = 0;

                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);

                        //Skip first row (Remove below comment if you want to skip the first row)
                        if ($i == 0) {
                            $i++;
                            continue;
                        }
                        for ($c = 0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata[$c];
                        }
                        $i++;
                    } //while bracket closed
                    fclose($file);

                    // Insert to MySQL database
                    foreach ($importData_arr as $importData) {
                        if ($importData[3] > 0) {


                            //grn ban rahi ha
                            $grn = $purchase->getGrn();
                            $grn = $grn + 1;
                            $gen = [
                                'GRN' => "GRN-" . $grn,
                                'user_id' => session('userid'),
                                'created_at' => date('Y-m-d H:s:i'),
                                'updated_at' => date('Y-m-d H:s:i'),
                            ];
                            $gen_res = $purchase->receiving_general($gen);

                            //grn stock adjustment insert
                            $fields = [
                                'GRN' => $gen_res,
                                'item_id' => $importData[0],
                                'qty_rec' => $importData[3],
                            ];

                            $stock_id = $inventory->insertgeneral('purchase_rec_stock_adjustment', $fields);

                            //get uom here
                            $um = $inventory->getproductdetails($importData[0]);

                            $items = [
                                'grn_id' => $gen_res,
                                'product_id' => $importData[0],
                                'uom' => $um[0]->uom_id,
                                'cost_price' => $importData[2],
                                'retail_price' => "0.00",
                                'wholesale_price' => "0.00",
                                'discount_price' => "0.00",
                                'qty' => $importData[3],
                                'balance' => $importData[3],
                                'status_id' => 1,
                                'branch_id' => session('branch'),
                            ];

                            //                      //insert stock table
                            $stockadd = $inventory->insertgeneral('inventory_stock', $items);

                            //get last stock from stock report
                            $lastStock = $stock->getLastStock($request->productid);
                            $stk = $lastStock == "" ? 0 : $lastStock[0]->stock;
                            $stk = $stk + $importData[3];

                            //stock report k table main insert
                            $items = [
                                'date' => date('Y-m-d H:s:i'),
                                'product_id' => $importData[0],
                                'foreign_id' => $stockadd,
                                'branch_id' => session('branch'),
                                'qty' => $importData[3],
                                'stock' => $stk,
                                'cost' => $importData[2],
                                'retail' => "0.00",
                                'narration' => "Stock Openend from csv file",
                                'adjustment_mode' => "1", // 1 for positive

                            ];
                            $result = $inventory->insertgeneral('inventory_stock_report_table', $items);
                        }
                    }

                    Session::flash('message', '1');
                } else {
                    Session::flash('message', '2');
                }
            }

            // Redirect to index
            return redirect()->route('invent-list');
        }
    }

    public function test(Request $request, inventory $inventory)
    {
        $string = "123";
        if (preg_match('~[0-9]+~', $string)) {
            return 1;
        } else {
            return 0;
        }
        // $result = DB::table("inventory_general")->where("company_id",9)->get();
        // foreach($result as $value){
        // $slug = "";
        // $slug = strtolower(str_replace(' ', '-', $value->product_name)) . "-" . strtolower(Str::random(4));
        // DB::table("inventory_general")->where("id",$value->id)->update(["slug" => $slug]);
        // }
    }

    public function displayInventory(Request $request, inventory $inventory)
    {
        $main = $inventory->displayInventory($request->code, $request->name, $request->depart, $request->sdepart, $request->status);
        return view('Inventory.inventoryselection', compact('main'));
    }

    public function fetch_data(Request $request, inventory $inventory)
    {
        $main = $inventory->displayInventory($request->code, $request->name, $request->depart, $request->sdepart, $request->status);
        return view('partials.inventory_table', compact('main'))->render();
    }

    public function changeInventoryStatus(Request $request)
    {
        if ($request->table == "inventory") {
            $count = DB::table("inventory_general")->where("id", $request->id)->count();
            if ($count == 1) {
                if ($request->columnname == "pos") {
                    DB::table("inventory_general")->where("id", $request->id)->update(["isPos" => $request->value]);
                    return $this->setNotification("inventory", $request->id);
                } else if ($request->columnname == "hide") {
                    DB::table("inventory_general")->where("id", $request->id)->update(["isHide" => $request->value]);
                    return $this->setNotification("inventory", $request->id);
                } else {
                    DB::table("inventory_general")->where("id", $request->id)->update(["isOnline" => $request->value]);
                    return $this->setNotification("inventory", $request->id);
                }
            } else {
                return 2;
            }
        } else {
            $count = DB::table("pos_products_gen_details")->where("pos_item_id", $request->id)->count();
            if ($count == 1) {
                if ($request->columnname == "pos") {
                    DB::table("pos_products_gen_details")->where("pos_item_id", $request->id)->update(["isPos" => $request->value]);
                    return $this->setNotification("pos", $request->id);
                } else if ($request->columnname == "hide") {
                    DB::table("pos_products_gen_details")->where("pos_item_id", $request->id)->update(["isHide" => $request->value]);
                    return $this->setNotification("pos", $request->id);
                } else {
                    DB::table("pos_products_gen_details")->where("pos_item_id", $request->id)->update(["isOnline" => $request->value]);
                    return $this->setNotification("pos", $request->id);
                }
            } else {
                return 2;
            }
        }
    }

    public function setNotification($mode, $id)
    {
        if ($mode == "pos") {
            $posData = DB::table("pos_products_gen_details")->where("pos_item_id", $id)->get();
            $message = "ID" . $id . ",IP" . $posData[0]->isPos . ",IO" . $posData[0]->isOnline . ",H" . $posData[0]->isHide;
            return $this->sendPushNotificationForPermission($posData, "pos", $message);
        }

        if ($mode == "inventory") {
            $inventoryData = DB::table("inventory_general")->where("id", $id)->get();
            $message = "ID" . $id . ",IP" . $inventoryData[0]->isPos . ",IO" . $inventoryData[0]->isOnline . ",H" . $inventoryData[0]->isHide;
            return $this->sendPushNotificationForPermission($inventoryData, "inventory", $message);
        }
    }

    public function sendPushNotificationForPermission($code, $mode, $funMessage)
    {

        $message = $funMessage;
        $body = "Item " . ($mode == "pos" ? $code[0]->item_name : $code[0]->product_name) . " " . ($code[0]->isPos == 0 ? "Deactivated" : "Activated");
        $tokens = array();
        $result = DB::select("SELECT branch_name,b.name as company FROM `branch` INNER Join company b on b.company_id = branch.company_id where branch.company_id = ? and branch_id = ?", [session("company_id"), session("branch")]);
        $title = "Item On/Off";
        $firebaseToken = DB::table("terminal_details")->where("branch_id", session("branch"))->whereNotNull("device_token")->get("device_token"); //["cZIiT3EPTAKce8s8lPHTkZ:APA91bH0a0zModJDvMjwLmeMIqHNfyLriX1m2EWV9BI157KY6DtxsfWPDo-mYjl-Qh92dyfjU0Q0BM_HeXykZp6xy3LxoOxZmeLIxyBTimnfsCVIOuM0PBE8j53EV-_AWi6CVMOJIDMH"];//User::whereNotNull('device_token')->pluck('device_token')->all();
        // return $firebaseToken;
        foreach ($firebaseToken as $token) {
            array_push($tokens, $token->device_token);
        }


        $SERVER_API_KEY = 'AAAATXdhnIk:APA91bHFZZbCubOgnG3dihDVsqFbwGGQaBpC6f7BPFMnvpntpOOY88ysAEVAT2puQvdng3Xkd8j4HNVWFp1FQ2rHEe9g3Cv6nSZ7oeMsQtSh2GrJYNIxGHeogmen7TSPqRWHJxrG4QF_';

        $server_api_key_mobile = 'AAAA2dlOr6s:APA91bHGDpYDSZWI0LotnIYZUTpOTA9lLS56jsyB-2hq6Fsq6l0OPBoMYFqePTAbteVFawWzdyZOfMowMf-j8LBL8xJefdnpb_pZRVQHzu5rXykkdLBfPJgcr8gmPhPBDlXMWJy_-uv2';

        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => "Item Activated or Deactivated",
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
        // return json_encode($responseOne).json_encode($response);
    }

    public function sendPushNotification($code, $name, $status)
    {
        $statusmessage = ($status == "update" ? "updated" : "added");
        $message = "Item Code  # " . $code . " (" . $name . ")  has been " . $statusmessage . "";
        $tokens = array();
        $result = DB::select("SELECT branch_name,b.name as company FROM `branch` INNER Join company b on b.company_id = branch.company_id where branch.company_id = ? and branch_id = ?", [session("company_id"), session("branch")]);
        $title = ucwords($result[0]->company) . " (" . ucwords($result[0]->branch_name) . ")";
        $firebaseToken = DB::table("terminal_details")->where("branch_id", session("branch"))->whereNotNull("device_token")->get("device_token"); //["cZIiT3EPTAKce8s8lPHTkZ:APA91bH0a0zModJDvMjwLmeMIqHNfyLriX1m2EWV9BI157KY6DtxsfWPDo-mYjl-Qh92dyfjU0Q0BM_HeXykZp6xy3LxoOxZmeLIxyBTimnfsCVIOuM0PBE8j53EV-_AWi6CVMOJIDMH"];//User::whereNotNull('device_token')->pluck('device_token')->all();
        foreach ($firebaseToken as $token) {
            array_push($tokens, $token->device_token);
        }

        $SERVER_API_KEY = 'AAAATXdhnIk:APA91bHFZZbCubOgnG3dihDVsqFbwGGQaBpC6f7BPFMnvpntpOOY88ysAEVAT2puQvdng3Xkd8j4HNVWFp1FQ2rHEe9g3Cv6nSZ7oeMsQtSh2GrJYNIxGHeogmen7TSPqRWHJxrG4QF_';

        $server_api_key_mobile = 'AAAA2dlOr6s:APA91bHGDpYDSZWI0LotnIYZUTpOTA9lLS56jsyB-2hq6Fsq6l0OPBoMYFqePTAbteVFawWzdyZOfMowMf-j8LBL8xJefdnpb_pZRVQHzu5rXykkdLBfPJgcr8gmPhPBDlXMWJy_-uv2';

        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => "Inventory Updated",
                "body" => "New set of Inventory is been updated",
                "icon" => "https://retail.sabsoft.com.pk/assets/images/Sabify72.png",
                "content_available" => true,
                "priority" => "high",
                // "click_action" => ,
            ],
            // "data" => [
            // "id" => $code,
            // "name" => $name,
            // ],
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

        curl_exec($chs);
        $response = curl_exec($ch);

        return 1; //json_encode($response);
    }

    public function get_product_names(Request $request, inventory $inventory)
    {

        $result = $inventory->searchProductByNameAndItemCode($request->q);

        if ($result) {
            return response()->json(array('items' => $result));
        } else {
            return 0;
        }
    }

    public function assignProductToVendors(Request $request, Vendor $vendor)
    {

        foreach ($request->vendors as $vendorValue) {

            $check = $vendor->check_product_by_vendor($vendorValue, $request->productId);
            if ($check == 0) {
                $items[] = [
                    'vendor_id' => $vendorValue,
                    'product_id' => $request->productId,
                ];
            }
        }

        $result = (!empty($items) ? $vendor->insert_into_vendor_product($items, 1) : 0);

        if ($result == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function sunmiCloud(Request $request)
    {

        $response = Http::asForm()->post('https://sabsoft.com.pk/Retail/webservice/sendinventorytosunmi.php', [
            'inventory' => implode(',', $request->inventory),
        ]);
        return $response;
    }


    // -----------------------------------------------------------------	
    // 	Inventroy variable product 
    // -----------------------------------------------------------------

    public function createVariableProduct(Request $request, inventory $inventory, posProducts $posProducts)
    {
        $where = ['id' => $request->id, 'status' => 1];
        $check_exists =  $inventory->check_exists($where); //DB::table('inventory_general')->where('id',$request->id)->count();

        if ($check_exists == null) {
            Session::flash('error', 'Product not found');
            return redirect()->route('invent-list');
        }

        $totalvariation      = AddonCategory::where("company_id", session("company_id"))->where("mode", "variations")->where('status', 1)->get();
        $inventoryVariations = InventoryVariation::whereIn('inventory_variations.product_id', DB::table('pos_products_gen_details')->where('branch_id', session('branch'))->where('product_id', $request->id)->pluck('pos_item_id'))
            ->where('inventory_variations.status', 1)
            ->join('addon_categories', 'addon_categories.id', 'inventory_variations.variation_id')
            ->join('pos_products_gen_details', 'pos_products_gen_details.pos_item_id', 'inventory_variations.product_id')
            ->select('inventory_variations.*', 'addon_categories.name', 'addon_categories.type', 'addon_categories.is_required', 'addon_categories.addon_limit', 'pos_products_gen_details.item_name', 'pos_products_gen_details.priority')
            ->orderBy('pos_products_gen_details.priority', 'desc')
            ->get();

        $variationProductCount = Addon::whereIn(
            'addon_category_id',
            InventoryVariation::whereIn(
                'product_id',
                DB::table('pos_products_gen_details')
                    ->where('branch_id', session('branch'))
                    ->where('product_id', $request->id)
                    ->pluck('pos_item_id')
            )
                ->where('status', 1)
                ->pluck('variation_id')
        )
            ->groupBy('addon_category_id')
            ->select(DB::raw('count(id) as countProduct,addon_category_id'))
            ->get();

        return view('Inventory.variable-products', [
            'generalItem'            => $check_exists,
            'uom'                    => $inventory->uom(),
            'posProduct_details'     => $posProducts->getposproducts_filter_by_productId($request->id),
            'department'             => $inventory->department(),
            'totalvariation'         => $totalvariation,
            'inventoryVariations'    => $inventoryVariations,
            'variationProductCount'  => $variationProductCount,
            'addonCategories'        => AddonCategory::with("addons")->whereIn("id", DB::table('inventory_addons')->where(['product_id' => $request->id, 'status' => 1, "inventory_addon_type" => 'general-inventory'])->pluck('addon_id'))->where(["company_id" => session("company_id"), "mode" => 'addons'])->orderBy('priority', 'desc')->get(),
            'attributes' => Attribute::get_attributes(),
            //   'references' => $references,
        ]);
    }

    public function get_variableProduct(Request $request)
    {
        return DB::table('pos_products_gen_details')->where('branch_id', session('branch'))->where('product_id', $request->id)->where('status_id', 1)->get();
    }

    public function get_generalItem(Request $request)
    {
        return DB::table('inventory_general')
            ->where('company_id', session('company_id'))
            ->where('department_id', $request->depart)
            ->where('sub_department_id', $request->subDepart)
            ->where('status', 1)
            ->get();
    }

    public function autoGenerateCode_variableProduct(Request $request)
    {
        $code = rand(1000000, 9999999);

        $resp = DB::table('pos_products_gen_details')->where(['item_code' => $code, 'product_id' => $request->product_id])->count();
        if ($resp > 0) {
            $code = rand(1000000, 9999999);
            return $code;
        } else {
            return $code;
        }
    }

    public function storeVariableProduct(Request $request, posProducts $posProducts)
    {
        $imageName = null;

        $rules = [
            'item_code'  => 'required',
            'item_name'  => 'required',
            'attribute'  => 'required',
            'uom'        => 'required',
            'item_price' => 'required',
        ];

        $this->validate($request, $rules);

        $exsist = $posProducts->exsist_chk($request->itemName, $request->finishgood);
        if ($exsist[0]->counts > 0) {

            return redirect()->route('createVariableProduct', $request->finishgood)->withErrors('variable_product_name_error', 'This ' . $request->item_name . ' variable product name is already taken. Try again')->withInputs();
        }

        if (!empty($request->productImage)) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,webp,tiff|min:10|max:100',
            ]);

            $pname = str_replace(' ', '', $request->item_name);
            $imageName = $pname . '.' . $request->productImage->getClientOriginalExtension();
            $request->productImage->move(public_path('assets/images/products/'), $imageName);
        }
        $items = [
            'item_code' => $request->item_code,
            'priority'  => $request->item_priority,
            'attribute' => $request->attribute,
            'item_name' => $request->item_name,
            'product_id' => $request->finishgood,
            'uom' => $request->uom,
            'branch_id' => session("branch"),
            'image' => $imageName,
            'quantity' => 1,
            'status_id' => 1,
        ];

        $itemid = $posProducts->insert('pos_products_gen_details', $items);

        if ($itemid) {
            $items = [
                'online_price' => $request->item_price,
                'retail_price' => $request->item_price,
                'pos_item_id' => $itemid,
                'status_id' => 1,
                'date' => date('Y-m-d'),
            ];
            $price = $posProducts->insert('pos_product_price', $items);

            Session::flash('success', 'Success');
        } else {
            Session::flash('error', 'Error! Server Issue! Record is not submit.');
        }

        return redirect()->route('createVariableProduct', $request->finishgood);
    }

    public function VariableProduct_set_to_generalProduct(Request $request, posProducts $posProducts)
    {
        try {

            $item_code = $request->item_code;
            foreach ($item_code as $val) {
                DB::beginTransaction();
                $exsist = $posProducts->exsist_chk($request->variableName, $val);
                if ($exsist[0]->counts == 0) {
                    $posItemId = $request->variableId;
                    $genItemId = $request->generalInventoryCode;
                    $getPosProduct =  DB::table('pos_products_gen_details')
                        ->where('branch_id', session('branch'))
                        ->where('product_id', $genItemId)
                        ->where('pos_item_id', $posItemId)
                        ->where('status_id', 1)
                        ->first();

                    if ($getPosProduct == null) {
                        return response()->json(["status" => 500, "msg" => $e->getMessage()]);
                    }
                    if (!empty($getPosProduct->image)) {
                        $pname     = strtolower(str_replace(' ', '', $getPosProduct->item_name));
                        $imageName = $pname . '-' . $val . '.' . pathinfo($getPosProduct->image, PATHINFO_EXTENSION);

                        if (File::exists(public_path('assets/images/products') . '/' . $getPosProduct->image)) {
                            File::move(public_path('assets/images/products') . '/' . $getPosProduct->image, public_path('assets/images/products') . '/' . $imageName);
                        }
                    }

                    $request['product_id'] = $getPosProduct->product_id;

                    $items = [
                        'item_code'   => $this->autoGenerateCode_variableProduct($request),
                        'priority'    => $getPosProduct->priority,
                        'item_name'   => $getPosProduct->item_name,
                        'product_id'  => $val,
                        'uom'         => $getPosProduct->uom,
                        'branch_id'   => session("branch"),
                        'image'       => $imageName,
                        'quantity'    => 1,
                        'status_id'   => 1,
                    ];

                    $itemid = $posProducts->insert('pos_products_gen_details', $items);

                    if ($itemid) {
                        $getPosProduct =  DB::table('pos_product_price')
                            ->where('pos_item_id', $posItemId)
                            ->where('status_id', 1)
                            ->first();

                        $items = [
                            'online_price' => $getPosProduct->online_price,
                            'retail_price' => $getPosProduct->retail_price,
                            'pos_item_id'  => $itemid,
                            'status_id' => 1,
                            'date' => date('Y-m-d'),
                        ];
                        $price = $posProducts->insert('pos_product_price', $items);
                    }
                }
            }

            DB::commit();
            return response()->json(["status" => 200]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["status" => 500, "msg" => $e->getMessage()]);
        }
    }

    public function updateVariableProduct(Request $request, posProducts $posProducts)
    {
        try {
            $imageName = null;

            $exsist = DB::select('SELECT COUNT(pos_item_id) AS counts FROM pos_products_gen_details WHERE status_id=1 and branch_id = ' . session('branch') . ' and pos_item_id != ? and product_id = ? and item_name = ?', [$request->item_id, $request->finishgood, $request->item_name]);

            if ($exsist[0]->counts > 0) {
                return response()->json(['status' => 409, 'msg' => 'This ' . $request->item_name . ' variable product name is already taken. Try again', 'control' => 'item_name']);
            }

            $exist_itemCode = $posProducts->exsist_chk_itemcode_notEqualItemId($request->item_code, $request->item_id);
            if ($exist_itemCode[0]->counts > 0) {
                return response()->json(['status' => 409, 'msg' => 'This ' . $request->item_code . ' variable product code is already taken. Try again', 'control' => 'item_code']);
            }


            if (!empty($request->item_image)) {

                // 			$request->validate([
                // 			  'image' => 'image|mimes:jpeg,png,jpg|max:2048',
                // 			]);	

                $validator = Validator::make($request->all(), [
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,tiff|min:10|max:100'
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => 500, 'msg' => 'product image accept only this file format {jpg,pngjpeg} ', 'control' => 'item_image_vpmd']);
                }

                if ($request->prevImageName != "") {
                    $file_path = public_path('assets/images/products/' . $request->prevImageName);
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }

                $pname = str_replace(' ', '', $request->item_name);
                $imageName = time() . '.' . $request->item_image->getClientOriginalExtension();
                $request->item_image->move(public_path('assets/images/products/'), $imageName);
            }

            $items = [
                'item_code' => $request->item_code,
                'item_name' => $request->item_name,
                'quantity' => 1,
                'uom' => $request->uom,
                'priority' => $request->priority,
            ];

            if ($imageName != null) {
                $items['image'] =  $imageName;
            }

            $update = $posProducts->update_pos_gendetails($request->item_id, $items);

            //get id and change status to inactive
            $id = $posProducts->getid($request->item_id);
            $items = [
                'status_id' => 2, //inactive old price
                'date' => date('Y-m-d'),
            ];
            $result = $posProducts->update_pos_price($id[0]->price_id, $items);

            //insert new price in price table and status 1
            $items = [
                'retail_price' => $request->price,
                'online_price' => $request->price,
                'pos_item_id' => $request->item_id,
                'status_id' => 1,
                'date' => date('Y-m-d'),
            ];
            $result = $posProducts->insert('pos_product_price', $items);

            return response()->json(['status' => 200]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'msg' => $e->getMessage()]);
        }
    }


    public function deleteVariableProduct(posProducts $posProducts, Request $request)
    {
        $items = [
            'status_id' => 2,
        ];

        if ($posProducts->update_pos_gendetails($request->id, $items)) {
            return 1;
        } else {
            return 0;
        }

        //return redirect()->route('createVariableProduct',$request->finishgood);
    }

    public function reloadVariation_singleProduct(Request $request)
    {
        $variationHead = InventoryVariation::where('inventory_variations.product_id', $request->id)
            ->where('inventory_variations.status', 1)
            ->join('addon_categories', 'addon_categories.id', 'inventory_variations.variation_id')
            ->join('pos_products_gen_details', 'pos_products_gen_details.pos_item_id', 'inventory_variations.product_id')
            ->select('inventory_variations.*', 'addon_categories.name', 'addon_categories.type', 'addon_categories.is_required', 'addon_categories.addon_limit', 'pos_products_gen_details.item_name')
            ->get();

        $variationProductCount = Addon::whereIn(
            'addon_category_id',
            InventoryVariation::where('product_id', $request->id)
                ->where('status', 1)
                ->pluck('variation_id')
        )
            ->groupBy('addon_category_id')
            ->select(DB::raw('count(id) as countProduct,addon_category_id'))
            ->get();

        return response()->json(['variationHead' => $variationHead, 'variationProductCount' => $variationProductCount]);
    }

    public function getInventoryVariationProduct_values(Request $request)
    {

        return  Addon::select('addons.id', 'addons.name', 'addons.inventory_product_id', 'addons.price', 'inventory_department.department_name', 'inventory_general.department_id', 'inventory_sub_department.sub_depart_name')
            ->join('inventory_general', 'inventory_general.id', 'addons.inventory_product_id')
            ->join('inventory_department', 'inventory_department.department_id', 'inventory_general.department_id')
            ->join('inventory_sub_department', 'inventory_sub_department.sub_department_id', 'inventory_general.sub_department_id')
            ->where('addons.addon_category_id', $request->id)
            ->where('addons.status', 1)
            ->get();

        // return InventoryVariationProduct::where('inventory_variation_products.inventory_variation_id',$request->id)
        //                                    ->where('inventory_variation_products.status',1)
        //                                    ->join('addons','addons.id','inventory_variation_products.product_id')
        //                                    ->join('inventory_general','inventory_general.id','addons.inventory_product_id')
        //                                    ->join('inventory_department','inventory_department.department_id','inventory_general.department_id')
        //                                    ->join('inventory_sub_department','inventory_sub_department.sub_department_id','inventory_general.sub_department_id')
        //                                    ->select('inventory_variation_products.*','addons.name','addons.inventory_product_id','addons.price','inventory_department.department_name','inventory_general.department_id','inventory_sub_department.sub_depart_name')
        //                                    ->get(); 

    }

    public function set_variationAllVariableProduct(Request $request)
    {
        try {
            $posItemId = $request->itemId;

            if ($posItemId == null && !isset($request->itemId)) {
                return response()->json('Item not selected!', 500);
            }

            foreach ($posItemId as $val_positem) {
                DB::beginTransaction();
                $count = AddonCategory::whereIn("id", DB::table('inventory_variations')->where('product_id', $val_positem)->where('status', 1)->pluck('variation_id'))->where("status", 1)->where("name", AddonCategory::where('id', $request->variationId)->pluck('name'))->count();

                if ($count == 0) {
                    $getVariation = AddonCategory::where('id', $request->variationId)->first();

                    if ($getVariation == null) {
                        return response()->json('variation not found!', 500);
                    }

                    $getAddonCategoryId = AddonCategory::create([
                        "name"               => $getVariation->name,
                        "show_website_name"  => $getVariation->name,
                        "user_id"            => auth()->user()->id,
                        "company_id"         => session("company_id"),
                        "type"               => $getVariation->type,
                        "is_required"        => 1,
                        "mode"                 => 'variations',
                        "addon_limit"        => $getVariation->addon_limit,
                    ]);

                    if ($getAddonCategoryId) {
                        $getproducts = Addon::where('addon_category_id', $request->variationId)->where('status', 1)->get();

                        foreach ($getproducts as $val_prod) {
                            Addon::create([
                                "inventory_product_id"   => $val_prod->inventory_product_id,
                                "name"                   => $val_prod->name,
                                "price"                  => $val_prod->price,
                                "addon_category_id"      => $getAddonCategoryId->id,
                                "user_id"                => auth()->user()->id,
                            ]);
                        }

                        $getInventoryVariation_ID = InventoryVariation::create([
                            'product_id'    => $val_positem,
                            'variation_id'  => $getAddonCategoryId->id,
                            'status'        => 1,
                            'created_at'    => date("Y-m-d H:i:s"),
                            'updated_at'    => date("Y-m-d H:i:s"),
                        ]);
                    }
                }
            }
            DB::commit();
            return response()->json(["status" => 200]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["status" => 500, "msg" => $e->getMessage()]);
        }
    }

    // variable product sub variation 
    public function addVariation(Request $request)
    {
        // return $request;
        try {
            $count = AddonCategory::whereIn("id", DB::table('inventory_variations')->where('product_id', $request->item_id)->where('status', 1)->pluck('variation_id'))->where("status", 1)->where("name", $request->variation_name)->count();

            if ($count != 0) {
                return response()->json(["status" => 409, "control" => "variation_name", "msg" => "This " . $request->variation_name . " group name is already taken from product " . $request->item_name]);
            }

            // 			if($count == 0){
            $getAddonCategoryId = AddonCategory::create([
                "name"               => $request->variation_name,
                "show_website_name"  => $request->variation_name,
                "user_id"            => auth()->user()->id,
                "company_id"         => session("company_id"),
                "type"               => $request->variation_type,
                "is_required"        => 1,
                "mode"                 => 'variations',
                "addon_limit"        => isset($request->selection_limited) ? $request->selection_limited : 0,
            ]);

            if ($getAddonCategoryId) {
                $getproducts = $request->products;
                $getPrice    = $request->price;
                for ($i = 0; $i < count($getproducts); $i++) {
                    $getInventoryName = DB::table('inventory_general')->where('id', $getproducts[$i])->first();
                    Addon::create([
                        "inventory_product_id"   => $getproducts[$i],
                        "name"                   => $getInventoryName->product_name,
                        "price"                  => $getPrice[$i],
                        "addon_category_id"      => $getAddonCategoryId->id,
                        "user_id"                => auth()->user()->id,
                    ]);
                }

                $getInventoryVariation_ID = InventoryVariation::create([
                    'product_id'    => $request->item_id,
                    'variation_id'  => $getAddonCategoryId->id,
                    'status'        => 1,
                    'created_at'    => date("Y-m-d H:i:s"),
                    'updated_at'    => date("Y-m-d H:i:s"),
                ]);

                // if($getInventoryVariation_ID){
                //       $getVariationGroup_values = Addon::where('addon_category_id',$getAddonCategoryId->id)->get();
                //         foreach($getVariationGroup_values  as $prod_val){
                //                  InventoryVariationProduct::create([
                //                                                     'inventory_variation_id'  => $getInventoryVariation_ID->id,
                //                                                     'product_id'              => $prod_val->id,
                //                                                     'status'                  => 1,
                //                                               ]);                    
                //         }   
                // }   
            }
            return response()->json(["status" => 200]);
        } catch (Exception $e) {
            return response()->json(["status" => 500, "msg" => $e->getMessage()]);
        }
    }

    public function updateVariation(Request $request)
    {
        try {
            $count = AddonCategory::whereIn("id", DB::table('inventory_variations')->where('product_id', $request->item_id)->where('variation_id', '!=', $request->variation_id)->where('status', 1)->pluck('variation_id'))->where("status", 1)->where("name", $request->variation_name)->count();

            if ($count != 0) {
                return response()->json(["status" => 409, "control" => "variation_name", "msg" => "This " . $request->variation_name . " group name is already taken from product " . $request->item_name]);
            }

            AddonCategory::where('id', $request->variation_id)->update(['status' => 0]);
            Addon::where('addon_category_id', $request->variation_id)->update(['status' => 0]);

            $getId_InventoryVariation = InventoryVariation::where('product_id', $request->item_id)->where('variation_id', $request->variation_id)->pluck('id');
            InventoryVariation::where('product_id', $request->item_id)->where('variation_id', $request->variation_id)->update(['status' => 0]);
            InventoryVariationProduct::where('inventory_variation_id', $getId_InventoryVariation)->update(['status' => 0]);


            $getAddonCategoryId = AddonCategory::create([
                "name"               => $request->variation_name,
                "show_website_name"  => $request->variation_name,
                "user_id"            => auth()->user()->id,
                "company_id"         => session("company_id"),
                "type"               => $request->variation_type,
                "is_required"        => 1,
                "mode"                 => 'variations',
                "addon_limit"        => isset($request->selection_limited) ? $request->selection_limited : 0,
            ]);

            if ($getAddonCategoryId) {
                $getproducts = $request->products;
                $getPrice    = $request->price;
                for ($i = 0; $i < count($getproducts); $i++) {
                    $getInventoryName = DB::table('inventory_general')->where('id', $getproducts[$i])->first();
                    Addon::create([
                        "inventory_product_id"   => $getproducts[$i],
                        "name"                   => $getInventoryName->product_name,
                        "price"                  => $getPrice[$i],
                        "addon_category_id"      => $getAddonCategoryId->id,
                        "user_id"                => auth()->user()->id,
                    ]);
                }

                $getInventoryVariation_ID = InventoryVariation::create([
                    'product_id'    => $request->item_id,
                    'variation_id'  => $getAddonCategoryId->id,
                    'status'        => 1,
                    'created_at'    => date("Y-m-d H:i:s"),
                    'updated_at'    => date("Y-m-d H:i:s"),
                ]);

                // if($getInventoryVariation_ID){
                //       $getVariationGroup_values = Addon::where('addon_category_id',$getAddonCategoryId->id)->get();
                //         foreach($getVariationGroup_values  as $prod_val){
                //                  InventoryVariationProduct::create([
                //                                                     'inventory_variation_id'  => $getInventoryVariation_ID->id,
                //                                                     'product_id'              => $prod_val->id,
                //                                                     'status'                  => 1,
                //                                               ]);                    
                //         }                        

                // }                                  


            }
            return response()->json(["status" => 200]);
        } catch (Exception $e) {
            return response()->json(["status" => 500, "msg" => $e->getMessage()]);
        }
    }

    public function removeVariation(Request $request)
    {
        if (AddonCategory::where('id', $request->variation_group_id)->update(['status' => 0])) {
            $getID =  InventoryVariation::where('variation_id', $request->variation_group_id)->where('product_id', $request->item_id)->pluck('id');
            InventoryVariation::where('variation_id', $request->variation_group_id)->where('product_id', $request->item_id)->update(['status' => 0]);
            InventoryVariationProduct::where('inventory_variation_id', $getID)->update(['status' => 0]);
            return response()->json(["status" => 200]);
        } else {
            return response()->json(["status" => 500, "msg" => 'Server issue! record is not removed.']);
        }
    }


    // Start Deal Products
    public function createDealProduct(Request $request, inventory $inventory, posProducts $posProducts)
    {
        $where = ['id' => $request->id, 'status' => 1];
        $check_exists =  $inventory->check_exists($where); //DB::table('inventory_general')->where('id',$request->id)->count();

        if ($check_exists == null) {
            Session::flash('error', 'Product not found');
            return redirect()->route('invent-list');
        }

        return view('Inventory.deal-products', [
            'department'        => $inventory->department(),
            'generalItem'       => $check_exists,
            'dealHead'          => DB::table('inventory_deal_general')
                ->where('inventory_deal_id', $request->id)
                ->where('status', 1)
                // ->join('addon_categories','addon_categories.id','inventory_deal_general.group_id')
                // ->select('inventory_deal_general.*','addon_categories.name','addon_categories.type as group_type','addon_categories.addon_limit as selection_limit')
                ->orderBy('priority', 'DESC')
                ->get(),

            'dealChild'        => DB::table('inventory_deal_details')->whereIn(
                'inventory_general_id',
                DB::table('inventory_deal_general')
                    ->where('inventory_deal_id', $request->id)
                    ->pluck('id')
            )
                // ->join('addons','addons.id','inventory_deal_details.sub_group_id') 
                // ->join('inventory_general','inventory_general.id','addons.inventory_product_id')
                ->where('inventory_deal_details.status', 1)
                // ->select('inventory_deal_details.*','addons.inventory_product_id','addons.name','addons.quantity','inventory_general.department_id')
                ->select('inventory_deal_details.*')
                ->get(),

            'dealprodAddons'   => DB::table('inventory_addons')
                ->whereIn(
                    'product_id',
                    DB::table('inventory_deal_details')
                        ->whereIn(
                            'inventory_general_id',
                            DB::table('inventory_deal_general')
                                ->where('inventory_deal_id', $request->id)
                                ->pluck('id')
                        )
                        //   ->join('addons','addons.id','inventory_deal_details.sub_group_id')
                        ->where('inventory_deal_details.status', 1)
                        //   ->pluck('addons.inventory_product_id')
                        ->pluck('inventory_deal_details.id')
                )
                ->where('inventory_addon_type', 'deal')
                ->where('status', 1)
                ->groupBy('product_id')
                ->select(DB::raw('product_id, count(addon_id) as counts'))
                ->get(),
        ]);
    }

    public function reload_dealProducts(Request $request)
    {
        return response()->json([
            'dealChild'        => DB::table('inventory_deal_details')
                ->where('inventory_general_id', $request->id)
                ->where('status', 1)
                ->get(),

            'dealprodAddons'   => DB::table('inventory_addons')
                ->whereIn(
                    'product_id',
                    DB::table('inventory_deal_details')
                        ->where('inventory_general_id', $request->id)
                        ->where('status', 1)
                        ->pluck('id')
                )
                ->where('inventory_addon_type', 'deal')
                ->where('status', 1)
                ->groupBy('product_id')
                ->select(DB::raw('product_id, count(addon_id) as counts'))
                ->get(),

            // 'dealChild'        => DB::table('inventory_deal_details')
            //                         ->join('addons','addons.id','inventory_deal_details.sub_group_id') 
            //                         ->join('inventory_general','inventory_general.id','addons.inventory_product_id')
            //                         ->where('inventory_general_id',$request->id)
            //                         ->where('inventory_deal_details.status',1)
            //                         ->select('inventory_deal_details.*','addons.inventory_product_id','addons.name','addons.quantity','inventory_general.department_id')
            //                         ->get(),

            // 'dealprodAddons'   => DB::table('inventory_addons')
            //                          ->whereIn('product_id',DB::table('inventory_deal_details')
            //                                                   ->where('inventory_general_id',$request->id)
            //                                                   ->join('addons','addons.id','inventory_deal_details.sub_group_id')
            //                                                   ->where('inventory_deal_details.status',1)
            //                                                   ->pluck('addons.inventory_product_id')
            //                                   )
            //                          ->where('inventory_addon_type','deal')
            //                          ->where('status',1)
            //                          ->groupBy('product_id')
            //                          ->select(DB::raw('product_id, count(addon_id) as counts'))
            //                          ->get(),  
        ]);
    }

    public function storeDeal_product(Request $request)
    {
        try {

            $count = DB::table('inventory_deal_general')->where('inventory_deal_id', $request->finishgood)->where('status', 1)->where("name", $request->group_name)->count();

            if ($count != 0) {
                return response()->json(["status" => 409, "control" => "group_name", "msg" => "This " . $request->group_name . " group name is already taken from product " . $request->itemName]);
            }

            $priorityCheck = DB::table('inventory_deal_general')->where('inventory_deal_id', $request->finishgood)->where('status', 1)->where("priority", $request->priority)->count();

            if ($priorityCheck != 0 && $count == 0) {
                return response()->json(['status' => 409, 'control' => 'priority', 'msg' => 'This priority number is already taken!']);
            }

            $getInventoryDealGeneral_ID = InventoryDealGeneral::create([
                'inventory_deal_id' => $request->finishgood,
                'group_id'          => 1,
                'name'              => $request->group_name,
                'type'              => $request->group_type,
                'selection_limit'   => ($request->group_type == 'multiple' ? $request->selection_limit : 0),
                'priority'          => $request->priority,
                'status'            => 1,
                'created_at'        => date("Y-m-d H:i:s"),
                'updated_at'        => date("Y-m-d H:i:s"),
            ]);

            if ($getInventoryDealGeneral_ID) {
                $getproducts = $request->products;
                $getQunatity = $request->product_qty;
                for ($i = 0; $i < count($getproducts); $i++) {
                    $getInventoryName = DB::table('inventory_general')->where('id', $getproducts[$i])->first();
                    InventoryDealDetail::create([
                        'inventory_general_id'  => $getInventoryDealGeneral_ID->id,
                        'product_id'            => $getproducts[$i],
                        'product_name'          => $getInventoryName->product_name,
                        'product_quantity'      => $getQunatity[$i],
                    ]);
                }
            }
            return response()->json(["status" => 200]);
        } catch (Exception $e) {
            return response()->json(["status" => 500, "msg" => $e->getMessage()]);
        }
    }


    public function updateDeal_product(Request $request)
    {
        try {
            $count = DB::table('inventory_deal_general')->where('inventory_deal_id', $request->finishgood)->where('id', '!=', $request->id_editmd)->where('status', 1)->where("name", $request->group_name)->count();

            if ($count != 0) {
                return response()->json(["status" => 409, "control" => "group_name_editmd", "msg" => "This " . $request->group_name . " group name is already taken from product " . $request->inventory_name]);
            }

            $priorityCheck = DB::table('inventory_deal_general')->where('inventory_deal_id', $request->finishgood)
                ->where('id', '!=', $request->id_editmd)->where('status', 1)
                ->where("priority", $request->priority)->count();

            if ($priorityCheck != 0 && $count == 0) {
                return response()->json(['status' => 409, 'control' => 'priority_editmd', 'msg' => 'This priority number is already taken!']);
            }

            $getInventoryDealGeneral_ID = InventoryDealGeneral::create([
                'inventory_deal_id' => $request->finishgood,
                'group_id'          => 1,
                'name'              => $request->group_name,
                'type'              => $request->group_type,
                'selection_limit'   => ($request->group_type == 'multiple' ? $request->selection_limit : 0),
                'priority'          => $request->priority,
                'status'            => 1,
                'created_at'        => date("Y-m-d H:i:s"),
                'updated_at'        => date("Y-m-d H:i:s"),
            ]);

            if ($getInventoryDealGeneral_ID) {
                InventoryDealGeneral::where('inventory_deal_id', $request->finishgood)->where('id', $request->id_editmd)->update(['status' => 0]);

                $getproducts = $request->products;
                $getQunatity = $request->product_qty;
                for ($i = 0; $i < count($getproducts); $i++) {
                    $getInventoryName = DB::table('inventory_general')->where('id', $getproducts[$i])->first();

                    $getId = InventoryDealDetail::create([
                        'inventory_general_id'  => $getInventoryDealGeneral_ID->id,
                        'product_id'            => $getproducts[$i],
                        'product_name'          => $getInventoryName->product_name,
                        'product_quantity'      => $getQunatity[$i],
                    ]);

                    $getDeal_Addon = InventoryAddon::whereIn('product_id', InventoryDealDetail::where(['inventory_general_id' => $request->id_editmd, 'status' => 1, 'product_id' => $getproducts[$i]])
                        ->pluck('id'))
                        ->get();

                    if ($getDeal_Addon) {
                        foreach ($getDeal_Addon as $addon) {
                            InventoryAddon::create([
                                'product_id'            => $getId->id,
                                'addon_id'              => $addon->addon_id,
                                'status'                => 1,
                                'inventory_addon_type'  => 'deal',
                                'created_at'            => date("Y-m-d H:i:s"),
                                'updated_at'            => date("Y-m-d H:i:s"),
                            ]);
                        }

                        InventoryAddon::whereIn('product_id', InventoryDealDetail::where(['inventory_general_id' => $request->id_editmd, 'status' => 1, 'product_id' => $getproducts[$i]])->pluck('id'))
                            ->update(['status' => 0]);
                        InventoryDealDetail::where(['inventory_general_id' => $request->id_editmd, 'status' => 1, 'product_id' => $getproducts[$i]])
                            ->update(['status' => 0]);
                    }
                }

                return response()->json(["status" => 200]);
            } else {
                return response()->json(["status" => 500, "msg" => "Record is not saved."]);
            }
        } catch (Exception $e) {
            return response()->json(["status" => 500, "msg" => $e->getMessage()]);
        }
    }

    public function singleDealProduct_store(Request $request)
    {
        try {

            $resp = Addon::create([
                "inventory_product_id"   => $request->product_id,
                "name"                   => $request->product_name,
                "quantity"               => $request->quantity,
                "addon_category_id"      => $request->id,
                "user_id"                => auth()->user()->id,
            ]);

            if ($resp) {
                InventoryDealDetail::create([
                    'inventory_general_id'  => $request->dealRowIdUnq,
                    'sub_group_id'          => $resp->id,
                    'status'                => 1,
                ]);
            }


            return ($resp) ? response()->json(["status" => 200]) : response()->json(["status" => 500, "msg" => " Record is not save"]);
        } catch (Exception $e) {
            return response()->json(["status" => 500, "msg" => $e->getMessage()]);
        }
    }

    public function removeDeal_product(Request $request)
    {
        if (InventoryDealGeneral::where('id', $request->id)->update(['status' => 0]) && InventoryDealDetail::where('inventory_general_id', $request->id)->update(['status' => 0])) {
            return response()->json(["status" => 200]);
        } else {
            return response()->json(["status" => 500, "msg" => 'Server issue! record is not removed.']);
        }
    }

    // public function removeDeal_value(Request $request){
    //   if(InventoryDealDetail::where(['inventory_general_id'=>$request->general_deal_id,''])->update(['status'=>0]) && InventoryAddon::where(['product_id'=>$request->id,'inventory_addon_type'=>'deal'])->update(['status'=>0])){
    //       return response()->json(["status" => 200]); 
    //   }else{
    //       return response()->json(["status" => 500,"msg"=>'Server issue! record is not removed.']); 
    //   }
    // }      

    public function get_addons_dealProduct(Request $request)
    {

        return response()->json([

            'addonHead'   =>  InventoryAddon::where('inventory_addons.product_id', $request->finishgood)
                ->where('inventory_addons.status', 1)
                ->where('inventory_addons.inventory_addon_type', 'deal')
                ->join('addon_categories', 'addon_categories.id', 'inventory_addons.addon_id')
                ->select('addon_categories.*')
                ->get(),
            'addon_value' =>  InventoryAddon::where('inventory_addons.product_id', $request->finishgood)
                ->where('inventory_addons.status', 1)
                ->where('inventory_addons.inventory_addon_type', 'deal')
                ->where('addons.status', 1)
                ->join('addons', 'addons.addon_category_id', 'inventory_addons.addon_id')
                ->select('addons.id', 'addons.addon_category_id', 'addons.inventory_product_id', 'addons.name', 'addons.price')
                ->get()
        ]);
    }

    // public function get_addon_dealProduct_values(Request $request){
    //   return Addon::where('addon_category_id',$request->id)
    //               ->where('addons.status',1)
    //               ->join('inventory_general','inventory_general.id','addons.inventory_product_id')
    //               ->join('inventory_department','inventory_department.department_id','inventory_general.department_id')
    //               ->select('addons.*','inventory_department.department_name')
    //               ->get();
    // }

    public function store_addon_dealProduct(Request $request)
    {
        try {

            $count = AddonCategory::whereIn("id", DB::table('inventory_addons')->where('product_id', $request->finishgood)->where('status', 1)->where('inventory_addon_type', 'deal')->pluck('addon_id'))->where("status", 1)->where("name", $request->addon_name)->count();

            if ($count != 0) {
                return response()->json(['status' => 409, 'control' => 'addon_name', 'msg' => 'This addon name is already taken this product ' . $request->productName]);
            }

            $getAddonCategoryId = AddonCategory::create([
                "name"               => $request->addon_name,
                "show_website_name"  => $request->showebsite_name,
                "user_id"            => auth()->user()->id,
                "company_id"         => session("company_id"),
                "type"               => $request->addon_type,
                "is_required"        => isset($request->is_required) ? 1 : 0,
                "mode"                 => 'addons',
                "addon_limit"        => isset($request->selection_limit) ? $request->selection_limit : 0,
            ]);

            if ($getAddonCategoryId) {
                $getproducts = $request->products;
                $getPrice    = $request->price;
                for ($i = 0; $i < count($getproducts); $i++) {
                    $getInventoryName = DB::table('inventory_general')->where('id', $getproducts[$i])->first();
                    Addon::create([
                        "inventory_product_id"   => $getproducts[$i],
                        "name"                   => $getInventoryName->product_name,
                        'price'                  => $getPrice[$i],
                        "addon_category_id"      => $getAddonCategoryId->id,
                        "user_id"                => auth()->user()->id,
                    ]);
                }

                $getInventoryDealGeneral_ID = InventoryAddon::create([
                    'product_id'            => $request->finishgood,
                    'addon_id'              => $getAddonCategoryId->id,
                    'status'                => 1,
                    'inventory_addon_type'  => 'deal',
                    'created_at'            => date("Y-m-d H:i:s"),
                    'updated_at'            => date("Y-m-d H:i:s"),
                ]);
            }

            return response()->json(['status' => 200]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'msg' => $e->getMessage()]);
        }
    }

    public function store_singleValue_addonDealProduct(Request $request)
    {
        try {

            $result = Addon::create([
                "inventory_product_id"   => $request->product_id,
                "name"                   => $request->product_name,
                'price'                  => $request->price,
                "addon_category_id"      => $request->id,
                "user_id"                => auth()->user()->id,
            ]);

            return ($result) ? response()->json(['status' => 200]) : response()->json(['status' => 500, 'msg' => 'Record is not saved!']);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'msg' => $e->getMessage()]);
        }
    }

    public function update_addonDealProduct(Request $request)
    {
        try {

            $count = AddonCategory::whereIn("id", DB::table('inventory_addons')->where('product_id', $request->finishgood)->where('inventory_addon_type', 'deal')->where('addon_id', '!=', $request->addonheadId)->where('status', 1)->pluck('addon_id'))->where("status", 1)->where("name", $request->addon_name)->count();

            if ($count != 0) {
                return response()->json(['status' => 409, 'control' => 'addon_name_editmdAddon', 'msg' => 'This addon name is already taken this product ' . $Request->productName]);
            }

            $getAddonCategoryId = AddonCategory::create([
                "name"               => $request->addon_name,
                "show_website_name"  => $request->showebsite_name,
                "user_id"            => auth()->user()->id,
                "company_id"         => session("company_id"),
                "type"               => $request->addon_type,
                "is_required"        => isset($request->is_required) ? 1 : 0,
                "mode"                 => 'addons',
                "addon_limit"        => isset($request->selection_limit) ? $request->selection_limit : 0,
            ]);

            if ($getAddonCategoryId) {
                $getproducts = $request->products;
                $getPrice    = $request->price;
                for ($i = 0; $i < count($getproducts); $i++) {
                    $getInventoryName = DB::table('inventory_general')->where('id', $getproducts[$i])->first();
                    Addon::create([
                        "inventory_product_id"   => $getproducts[$i],
                        "name"                   => $getInventoryName->product_name,
                        'price'                  => $getPrice[$i],
                        "addon_category_id"      => $getAddonCategoryId->id,
                        "user_id"                => auth()->user()->id,
                    ]);
                }

                $getInventoryDealGeneral_ID = InventoryAddon::create([
                    'product_id'            => $request->finishgood,
                    'addon_id'              => $getAddonCategoryId->id,
                    'status'                => 1,
                    'inventory_addon_type'  => 'deal',
                    'created_at'            => date("Y-m-d H:i:s"),
                    'updated_at'            => date("Y-m-d H:i:s"),
                ]);

                AddonCategory::where('id', $request->addonheadId)->update(['status' => 0]);
                Addon::where('addon_category_id', $request->addonheadId)->update(['status' => 0]);
                InventoryAddon::where('product_id', $request->finishgood)->where('addon_id', $request->addonheadId)->update(['status' => 0]);
            }

            return response()->json(['status' => 200]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'msg' => $e->getMessage()]);
        }
    }

    public function removeAddon_dealProduct(Request $request)
    {
        if (AddonCategory::where('id', $request->id)->update(['status' => 0])) {
            Addon::where('addon_category_id', $request->id)->update(['status' => 0]);
            InventoryAddon::where(['product_id' => $request->product_id, 'addon_id' => $request->id, 'inventory_addon_type' => 'deal'])->update(['status' => 0]);
            return response()->json(["status" => 200]);
        } else {
            return response()->json(["status" => 500, "msg" => 'Server issue! record is not removed.']);
        }
    }

    public function removeAddonValue_dealProduct(Request $request)
    {
        if (Addon::where('id', $request->id)->update(['status' => 0])) {
            return response()->json(["status" => 200]);
        } else {
            return response()->json(["status" => 500, "msg" => 'Server issue! record is not removed.']);
        }
    }

    public function loadAddons(Request $request)
    {
        return AddonCategory::with("addons")->whereIn("id", DB::table('inventory_addons')->where(['product_id' => $request->id, 'status' => 1, "inventory_addon_type" => 'general-inventory'])->pluck('addon_id'))->where(["company_id" => session("company_id"), "mode" => 'addons'])->orderBy('priority', 'desc')->get();
    }

    public function loadAddonValues(Request $request)
    {
        return Addon::where('addon_category_id', $request->id)
            ->where('addons.status', 1)
            ->join('inventory_general', 'inventory_general.id', 'addons.inventory_product_id')
            ->join('inventory_department', 'inventory_department.department_id', 'inventory_general.department_id')
            ->join('inventory_sub_department', 'inventory_sub_department.sub_department_id', 'inventory_sub_department.sub_department_id')
            ->select('addons.*', 'inventory_department.department_name', 'inventory_sub_department.sub_depart_name')
            ->get();
    }

    public function storeAddon(Request $request)
    {

        try {
            $count = AddonCategory::whereIn("id", DB::table('inventory_addons')->where('product_id', $request->finishgood)->where('status', 1)->where('inventory_addon_type', 'general-inventory')->pluck('addon_id'))
                ->where("status", 1)
                ->where("name", $request->addon_name)
                ->count();

            if ($count != 0) {
                return response()->json(['status' => 409, 'control' => 'addon_name', 'msg' => 'This addon name is already taken']);
            }

            $getAddonCategoryId = AddonCategory::create([
                "name"               => $request->addon_name,
                "show_website_name"  => $request->showebsite_name,
                "user_id"            => auth()->user()->id,
                "company_id"         => session("company_id"),
                "type"               => $request->addon_type,
                "is_required"        => isset($request->is_required) ? 1 : 0,
                "mode"                 => 'addons',
                "priority"           => $request->priority,
                "addon_limit"        => isset($request->selection_limit) ? $request->selection_limit : 0,
            ]);

            if ($getAddonCategoryId) {

                $getproducts = $request->products;
                $getPrice    = $request->price;
                for ($i = 0; $i < count($getproducts); $i++) {
                    $getInventoryName = DB::table('inventory_general')->where('id', $getproducts[$i])->first();
                    Addon::create([
                        "inventory_product_id"   => $getproducts[$i],
                        "name"                   => $getInventoryName->product_name,
                        'price'                  => $getPrice[$i],
                        "addon_category_id"      => $getAddonCategoryId->id,
                        "user_id"                => auth()->user()->id,
                    ]);
                }

                $getInventoryDealGeneral_ID = InventoryAddon::create([
                    'product_id'            => $request->finishgood,
                    'addon_id'              => $getAddonCategoryId->id,
                    'status'                => 1,
                    'inventory_addon_type'  => 'general-inventory',
                    'created_at'            => date("Y-m-d H:i:s"),
                    'updated_at'            => date("Y-m-d H:i:s"),
                ]);
            }

            return response()->json(['status' => 200]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'msg' => $e->getMessage()]);
        }
    }

    public function removeAddon(Request $request)
    {
        if (AddonCategory::where('id', $request->id)->update(['status' => 0])) {
            Addon::where('addon_category_id', $request->id)->update(['status' => 0]);
            InventoryAddon::where(['product_id' => $request->product_id, 'addon_id' => $request->id, 'inventory_addon_type' => 'general-inventory'])->update(['status' => 0]);
            return response()->json(["status" => 200]);
        } else {
            return response()->json(["status" => 500, "msg" => 'Server issue! record is not removed.']);
        }
    }

    public function removeAddonValue(Request $request)
    {
        if (Addon::where('id', $request->id)->update(['status' => 0])) {
            return response()->json(["status" => 200]);
        } else {
            return response()->json(["status" => 500, "msg" => 'Server issue! record is not removed.']);
        }
    }

    public function updateAddon(Request $request)
    {
        try {

            $count = AddonCategory::whereIn("id", DB::table('inventory_addons')->where('product_id', $request->finishgood)->where('addon_id', '!=', $request->addonheadId)->where('inventory_addon_type', 'general-inventory')->where('status', 1)->pluck('addon_id'))->where("status", 1)->where("name", $request->addon_name)->count();

            if ($count != 0) {
                return response()->json(['status' => 409, 'control' => 'addon_name_editmdAddon', 'msg' => 'This addon name is already taken this product ' . $request->productName]);
            }

            $getAddonCategoryId = AddonCategory::create([
                "name"               => $request->addon_name,
                "show_website_name"  => $request->showebsite_name,
                "user_id"            => auth()->user()->id,
                "company_id"         => session("company_id"),
                "type"               => $request->addon_type,
                "is_required"        => isset($request->is_required) ? 1 : 0,
                "mode"                 => 'addons',
                "priority"           => $request->priority,
                "addon_limit"        => isset($request->selection_limit) ? $request->selection_limit : 0,
            ]);

            if ($getAddonCategoryId) {
                $getproducts = $request->products;
                $getPrice    = $request->price;
                for ($i = 0; $i < count($getproducts); $i++) {
                    $getInventoryName = DB::table('inventory_general')->where('id', $getproducts[$i])->first();
                    Addon::create([
                        "inventory_product_id"   => $getproducts[$i],
                        "name"                   => $getInventoryName->product_name,
                        'price'                  => $getPrice[$i],
                        "addon_category_id"      => $getAddonCategoryId->id,
                        "user_id"                => auth()->user()->id,
                    ]);
                }

                $getInventoryDealGeneral_ID = InventoryAddon::create([
                    'product_id'            => $request->finishgood,
                    'addon_id'              => $getAddonCategoryId->id,
                    'status'                => 1,
                    'inventory_addon_type'  => 'general-inventory',
                    'created_at'            => date("Y-m-d H:i:s"),
                    'updated_at'            => date("Y-m-d H:i:s"),
                ]);

                AddonCategory::where('id', $request->addonheadId)->update(['status' => 0]);
                Addon::where('addon_category_id', $request->addonheadId)->update(['status' => 0]);
                InventoryAddon::where('product_id', $request->finishgood)->where('addon_id', $request->addonheadId)->update(['status' => 0]);
            }

            return response()->json(['status' => 200]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'msg' => $e->getMessage()]);
        }
    }

    public function store_singleValueAddon(Request $request)
    {
        try {

            $result = Addon::create([
                "inventory_product_id"   => $request->product_id,
                "name"                   => $request->product_name,
                'price'                  => $request->price == '' ? 0 : $request->price,
                "addon_category_id"      => $request->id,
                "user_id"                => auth()->user()->id,
            ]);

            return ($result) ? response()->json(['status' => 200]) : response()->json(['status' => 500, 'msg' => 'Record is not saved!']);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'msg' => $e->getMessage()]);
        }
    }
}
