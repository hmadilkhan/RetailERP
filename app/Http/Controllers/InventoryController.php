<?php

namespace App\Http\Controllers;

use App\inventory;
use App\Vendor;
use App\purchase;
use App\stock;
use App\AddonCategory;
use App\InventoryAddon;
use App\WebsiteProduct;
use Session;
use Illuminate\Session\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Config;
use Illuminate\Support\Str;
use Image;
use Illuminate\Support\Facades\Http;


class InventoryController extends Controller
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
    public function index(inventory $inventory)
    {
        $department = $inventory->department();
        $subdepartment = ''; //$inventory->subDepartment();
        $uom = $inventory->uom();
        $branch = $inventory->branch();
        $inventory = ''; //$inventory->getData();
		$vendors = DB::table("vendors")->where("status_id",1)->where("user_id",session("company_id"))->get();
		$references = DB::select("SELECT * FROM `inventory_reference` where product_id IN (Select id from inventory_general where company_id = ?) and refrerence != '' GROUP by refrerence",[session('company_id')]);

        return view('Inventory.lists', compact('inventory', 'department', 'subdepartment', 'uom', 'branch','vendors','references'));
    }

    public function getInventory(inventory $inventory)
    {
        $inventory = $inventory->getData();
        return $inventory;
    }

    public function getInactiveInventory(inventory $inventory)
    {
        $inventory = $inventory->getInactiveData();
        return $inventory;
    }
	
	public function getNonStockInventory(Request $request,inventory $inventory)
    {
        $inventory = $inventory->getNonStockInventory($request->code, $request->name, $request->dept, $request->sdept,$request->rp,$request->ref);
        return $inventory;
    }

    public function getInventoryByName(Request $request, inventory $inventory)
    {
        $inventory = $inventory->getDataByName($request->code, $request->name, $request->dept, $request->sdept,$request->rp,$request->ref);
        return $inventory;
    }

    public function getInactiveInventoryBySearch(Request $request, inventory $inventory)
    {
        $inventory = $inventory->getInactiveInventoryBySearch($request->code, $request->name, $request->dept, $request->sdept,$request->ref);
        return $inventory;
    }
	
	public function autoGenerateCode(Request $request, inventory $inventory)
	{
		$code = "";
		if($request->departmentId != "" && $request->subdepartmentId != ""){
			$result = $inventory->getDepartAndSubDepart($request->departmentId,$request->subdepartmentId);
			if(!empty($result) && $result[0]->deptcode != ""){
				$code = $result[0]->deptcode."-".$result[0]->sdeptcode."-".rand(1000,9999);
			}else{
				$code = substr($result[0]->department_name, 0, 1).substr($result[0]->sub_depart_name, 0, 1)."-".rand(1000,9999);
			}
			return response()->json(["status" => 200,"code" => $code,"result" => $result ]);
		}else{
			return response()->json(["status" => 500,"code" => "" ]);
		}
	}

    public function insert(Request $request, inventory $inventory, purchase $purchase, stock $stock)
    {
		
        $rules = [
            'code' => 'required',
            'name' => 'required',
            'reminder' => 'required',
            'uom' => 'required',
			'cuom' => 'required',
            'depart' => 'required',
            'subDepart' => 'required',
            'rp' => 'required',
            'ap' => 'required',
            'product_mode' => 'required',

        ];

        $this->validate($request, $rules);

        $imageName = "";
        if (!empty($request->image)) {

            //          $request->validate([
            //              'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            //          ]);

            foreach ($request->file('image') as $image) {
                $imageName = time() . '-' . $image->getClientOriginalName() . '.' . $image->getClientOriginalExtension();
                $img = Image::make($image)->resize(400, 400);
                $res = $img->save(public_path('assets/images/products/' . $imageName), 70);

                //                $imageName= time().'.'.$image->getClientOriginalName();
                //                $image->move(public_path('assets/images/products/'), $imageName);
                $data[] = $imageName;
            }
        }

        $fields = [
            'company_id' => session('company_id'),
            'department_id' => $request->depart,
            'sub_department_id' => $request->subDepart,
            'uom_id' => $request->uom,
			'cuom' => $request->cuom,
            'product_mode' => $request->product_mode,
            'item_code' => $request->code,
            'product_name' => $request->name,
            'product_description' => $request->description,
            'image' => (!empty($request->image) ? $data[0] : ""),
            'status' => 1,
            'created_at' => date('Y-m-d H:s:i'),
            'updated_at' => date('Y-m-d H:s:i'),
            'weight_qty' => $request->weight,
            'slug' => strtolower(str_replace(' ', '-', $request->name)) . "-" . strtolower(Str::random(4)),
            'short_description' => $request->sdescription,
            'details' => $request->details,
        ];
        $productid = $inventory->insert($fields);
        $result = $inventory->ReminderInsert($productid, $request->reminder);

        //Inventory Images Here
        if (!empty($request->image)) {
            foreach ($data  as $value) {
                DB::table("inventory_images")->insert([
                    "item_id" => $productid,
                    "image" => $value
                ]);
            }
        }


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
            'cost_price' => $request->cost_price,
            'actual_price' => $request->ap,
            'tax_rate' => $request->taxrate,
            'tax_amount' => $request->taxamount,
            'retail_price' => $request->rp,
            'wholesale_price' => $request->wp,
            'online_price' => $request->op,
            'discount_price' => $request->dp,
            'product_id' => $productid,
            'status_id' => 1,
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
       
		$terminals = DB::table("terminal_details")->where("branch_id",session("branch"))->where("status_id",1)->get();
		foreach($terminals as $value){
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
		if(!empty($request->vendor))
		{
			foreach($request->vendor as $singleVendor){
				DB::table('vendor_product')->insert([
					"vendor_id" => $singleVendor,
					"product_id" => $productid,
					"status" => 1,
				]);
			}
		}
		
		if(!empty($request->addons))
		{
			foreach($request->addons as $singleAddon){
				InventoryAddon::create([
					"addon_id" => $singleAddon,
					"product_id" => $productid,
				]);
			}
		}
		if(!empty($request->website))
		{
			foreach($request->website as $website){
				WebsiteProduct::create([
					"website_id" => $website,
					"inventory_id" => $productid,
				]);
			}
		}
	   $this->sendPushNotification($request->code,$request->name,"store");
	   return  redirect()->back();
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(inventory $inventory,Vendor $vendor)
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
        $mode = $inventory->getProductMode();
        $vendors = $vendor->getVendors();
		$websites = DB::table("website_details")->where("company_id",session("company_id"))->where("status",1)->get();
		$totaladdons = AddonCategory::where("company_id",session("company_id"))->get();
        return view('Inventory.create', compact('department', 'subdepartment', 'uom', 'branch', 'mode','vendors','totaladdons','websites'));
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

    public function getData(Request $request, inventory $inventory)
    {
        $department = $inventory->department();
        $subdepartment = $inventory->subDepartment();
        $uom = $inventory->uom();
        $branch = $inventory->branch();
        $data = $inventory->get_details($request->id);
        $mode = $inventory->getProductMode();
        $images = $inventory->getImages($request->id);
        $references =  $inventory->getReferences($request->id);
        $prices = $inventory->getpricebyproduct($data[0]->id);
		$websites = DB::table("website_details")->where("company_id",session("company_id"))->where("status",1)->get();
		$totaladdons = AddonCategory::where("company_id",session("company_id"))->get();
		$selectedAddons = InventoryAddon::where("product_id",$data[0]->id)->pluck("addon_id");
		$selectedWebsites = WebsiteProduct::where("inventory_id",$data[0]->id)->pluck("website_id");

        foreach ($references as $refval) {
            $ref[] = $refval->refrerence;
        }
        if (!empty($ref)) {
            $references = implode(",", $ref);
        } else {
            $references = "";
        }

        return view('Inventory.edit', compact('data', 'department', 'subdepartment', 'uom', 'branch', 'mode', 'images', 'references', 'prices','totaladdons','selectedAddons','websites','selectedWebsites'));
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
    public function edit(inventory $inventory)
    {
    }

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

        if ($request->image != "") {
            foreach ($request->file('image') as $image) {
                $imageName = time().'.' . $image->getClientOriginalExtension();
                $img = Image::make($image)->resize(400, 400);
                $res = $img->save(public_path('assets/images/products/' . $imageName), 70);
                $data[] = $imageName;
            }

            $fields = [
                'company_id' => session('company_id'),
                'department_id' => $request->depart,
                'sub_department_id' => $request->subDepart,
                'uom_id' => $request->uom,
				'cuom' => $request->cuom,
                'product_mode' => $request->product_mode,
                'item_code' => $request->code,
                'product_name' => $request->name,
                'product_description' => $request->description,
                'image' => $data[0],
                'status' => 1,
                'created_at' => date('Y-m-d H:s:i'),
                'updated_at' => date('Y-m-d H:s:i'),
                'weight_qty' => $request->weight,
                'short_description' => $request->sdescription,
                'details' => $request->details,
            ];
			
			

            $result = $invent->modify($fields, $request->id);

            //Inventory Images Here
            foreach ($data  as $value) {
                DB::table("inventory_images")->insert([
                    "item_id" => $request->id,
                    "image" => $value
                ]);
            }
        } else {

            $fields = [
                'company_id' => session('company_id'),
                'department_id' => $request->depart,
                'sub_department_id' => $request->subDepart,
                'uom_id' => $request->uom,
				'cuom' => $request->cuom,
                'product_mode' => $request->product_mode,
                'item_code' => $request->code,
                'product_name' => $request->name,
                'product_description' => $request->description,
                'created_at' => date('Y-m-d H:s:i'),
                'updated_at' => date('Y-m-d H:s:i'),
                'weight_qty' => $request->weight,
                'short_description' => $request->sdescription,
                'details' => $request->details,
            ];
            $result = $invent->modify($fields, $request->id);
        }
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
				'cost_price' => ($request->cost_price != "" ? $request->cost_price : "0.00"),
                'actual_price' => ($request->ap != "" ? $request->ap : "0.00"),
                'tax_rate' => ($request->taxrate != "" ? $request->taxrate : "0"),
                'tax_amount' => ($request->taxamount != "" ? $request->taxamount : "0"),
                'retail_price' => ($request->rp != "" ?  $request->rp : "0.00"),
                'wholesale_price' => ($request->wp != "" ? $request->wp : "0.00"),
                'online_price' => ($request->op != "" ? $request->op : "0.00"),
                'discount_price' => ($request->dp != "" ? $request->dp : "0.00" ),
                'product_id' => $request->id,
                'status_id' => 1,
            ];
            $price = $invent->insert_pram('inventory_price', $items);
        // }

		$terminals = DB::table("terminal_details")->where("branch_id",session("branch"))->where("status_id",1)->get();
		foreach($terminals as $value){
			$items = [
				"branchId" => session("branch"),
				"terminalId" => $value->terminal_id,
				"productId" => $request->id,
				"status" => 1,
				"date" => date("Y-m-d"),
				"time" => date("H:i:s"),
			]; 
			DB::table("inventory_download_status")->insert($items);
		}
		
		InventoryAddon::where("product_id",$request->id)->delete();
		if(!empty($request->addons))
		{
			foreach($request->addons as $singleAddon){
				InventoryAddon::create([
					"addon_id" => $singleAddon,
					"product_id" => $request->id,
				]);
			}
		}
		
		WebsiteProduct::where("inventory_id",$request->id)->delete();
		if(!empty($request->website))
		{
			foreach($request->website as $website){
				WebsiteProduct::create([
					"website_id" => $website,
					"inventory_id" => $request->id,
				]);
			}
		}
		
		$this->sendPushNotification($request->code,$request->name,"update");
        // return  redirect()->back();
        return  1;
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

        if ($inventory->getCheckByStock($request->product,$request->branch)) {
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
            $maxFileSize = 5000000;//2097152;

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
                    if(isset($request->update)){	
                      foreach ($importData_arr as $importData) {
                        $result = $inventory->updateProductName($importData[0],$importData[2]);
						$result = $inventory->findProductByIdInPriceTable($importData[0]);
						
                        if($result == true){
                            $inventory->updateToRetailPrice($importData[0],$importData[3],$importData[4],$importData[5],$importData[6],$importData[7],$importData[8],$importData[9]);
                        }
                      }  


                    }else{
                    // Insert to MySQL database
                        foreach ($importData_arr as $importData) {
                            // echo  $importData[1];  exit;  
							$count = DB::table("inventory_general")->where("company_id",session("company_id"))->where("item_code",$importData[0])->count();
							if($count == 0){
								$insertData = array(
									'company_id' => session('company_id'),
									'department_id' => isset($deptandSubdept[0]->departID) ? $deptandSubdept[0]->departID : 0,
									'sub_department_id' => isset($deptandSubdept[0]->sub_department_id) ? $deptandSubdept[0]->sub_department_id : 0,
									'uom_id' => 1,
									'product_mode' => 2,
									'item_code' => $importData[0],
									'product_name' =>$importData[1],// preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $importData[1]), Raza asked to remove this   
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
									"tax_rate"=> $importData[4],
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
            return redirect()->action('InventoryController@index');
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
		
		foreach($products as $product){
			$newTaxAmount = $product->actual_price * ($request->new_tax / 100);
			$retailPrice = $product->actual_price + $newTaxAmount;
			$result = $inventory->update_single_product_tax($product->price_id,$request->new_tax,$newTaxAmount,$retailPrice);
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
		
		if($request->departmentId == ""){

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
        }// foreach end
        
		}else{
			$products = DB::table("inventory_general")->where("department_id",$request->departmentId)->where("sub_department_id",$request->subDepartmentId)->where("company_id",session("company_id"))->get();
			foreach($products as $product){
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

    public function stockadjustment_show(inventory $inventory, request $request,stock $stock)
    {
		$branches = $stock->getBranches();
        return view('Inventory.stockadjustment', compact('branches'));
    }

    public function getstock_value(inventory $inventory, request $request)
    {
		$branch = (session('roleId') == 17 ? $request->branch : session('branch'));
        $stock = $inventory->getstock_value($request->productid,$branch);
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
		$inventory = DB::table("inventory_general")->where("id",$productid)->get();
		$terminals = DB::table("terminal_details")->where("branch_id",session("branch"))->where("status_id",1)->get();
		foreach($terminals as $value){
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
		$this->sendPushNotification($inventory[0]->item_code,$inventory[0]->product_name,"update");
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
            'narration' => "(Stock Adjustment) ".$narration,
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
            'branch_id' => (session('roleId') == 17 ? $request->branch : session('branch')),
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
            'branch_id' => (session('roleId') == 17 ? $request->branch : session('branch')),
            'qty' => $request->qty,
            'stock' => $stk,
            'cost' => $request->amount,
            'retail' => "0.00",
            'narration' => "(Stock Adjustment) ".$request->narration,
            'adjustment_mode' => "1", // 1 for positive

        ];
        $result = $inventory->insertgeneral('inventory_stock_report_table', $items);
		$inventory = DB::table("inventory_general")->where("id",$request->productid)->get();
		
		$terminals = DB::table("terminal_details")->where("branch_id",session("branch"))->where("status_id",1)->get();
		foreach($terminals as $value){
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
		
		$this->sendPushNotification($inventory[0]->item_code,$inventory[0]->product_name,"update");
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

        $columns = array('S.No','Name', 'Cost', 'Qty','Item Code');

        $callback = function () use ($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $row['S.No']  = $task->id;
                $row['Name']    = $task->product_name;
                $row['Cost']  = 0;
                $row['Qty']  = 0;
				$row['Item Code']  = $task->item_code;

                fputcsv($file, array($row['S.No'], $row['Name'], $row['Cost'], $row['Qty'],$row['Item Code']));
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

        $columns = array('Product Id', 'Product Code', 'Product Name' , 'Product Description', 'Actual Price','Tax Rate','Tax Amount','Retail Price','Wholesale Price','Online Price','Discount Price');

        $callback = function () use ($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $row['productID']  = $task->productID;
                $row['ItemCode']    = $task->ItemCode;
                $row['Name']  = $task->ItemName;
                $row['Description']  = $task->Description;
                $row['actual_price']  = $task->actual_price;
                $row['tax_rate']  = $task->tax_rate;
                $row['tax_amount']  = $task->tax_amount;
                $row['RetailPrice']  = $task->RetailPrice;
                $row['wholesale_price']  = $task->wholesale_price;
                $row['online_price']  = $task->online_price;
                $row['discount_price']  = $task->discount_price;

                fputcsv($file, array($row['productID'], $row['ItemCode'],$row['Name'] , $row['Description'], $row['actual_price'], $row['tax_rate'], $row['tax_amount'], $row['RetailPrice'], $row['wholesale_price'], $row['online_price'], $row['discount_price']));
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
                            $stk = empty($lastStock) ? 0 : $lastStock[0]->stock;
                            $stk = $stk + $request->qty;

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
            return redirect()->action('InventoryController@index');
        }
    }

    public function test(Request $request, inventory $inventory)
    {
		$string = "123";
		if (preg_match('~[0-9]+~',$string)){
			return 1;
		}else{
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
		$main = $inventory->displayInventory($request->code,$request->name,$request->depart,$request->sdepart,$request->status);
		return view('Inventory.inventoryselection',compact('main'));
	}
	
	public function fetch_data(Request $request, inventory $inventory){
		$main = $inventory->displayInventory($request->code,$request->name,$request->depart,$request->sdepart,$request->status);
		return view('partials.inventory_table', compact('main'))->render();
	}
	
	public function changeInventoryStatus(Request $request)
	{
		if($request->table == "inventory")
		{
			$count = DB::table("inventory_general")->where("id",$request->id)->count();
			if($count == 1)
			{
				if($request->columnname == "pos"){
					DB::table("inventory_general")->where("id",$request->id)->update(["isPos" => $request->value]);
					return $this->setNotification("inventory",$request->id);
				}else if($request->columnname == "hide"){
					DB::table("inventory_general")->where("id",$request->id)->update(["isHide" => $request->value]);
					return $this->setNotification("inventory",$request->id);
				}else{
					DB::table("inventory_general")->where("id",$request->id)->update(["isOnline" => $request->value]);
					return $this->setNotification("inventory",$request->id);
				}
			}else{
				return 2;
			}
		}
		else
		{
			$count = DB::table("pos_products_gen_details")->where("pos_item_id",$request->id)->count();
			if($count == 1)
			{
				if($request->columnname == "pos"){
					DB::table("pos_products_gen_details")->where("pos_item_id",$request->id)->update(["isPos" => $request->value]);
					return $this->setNotification("pos",$request->id);
				}else if($request->columnname == "hide"){
					DB::table("pos_products_gen_details")->where("pos_item_id",$request->id)->update(["isHide" => $request->value]);
					return $this->setNotification("pos",$request->id);
				}else{
					DB::table("pos_products_gen_details")->where("pos_item_id",$request->id)->update(["isOnline" => $request->value]);
					return $this->setNotification("pos",$request->id);
				}
				
			}else{
				return 2;
			}
		}
	}
	
	public function setNotification($mode,$id)
	{
		if($mode == "pos"){
			$posData = DB::table("pos_products_gen_details")->where("pos_item_id",$id)->get();
			$message = "ID".$id.",IP".$posData[0]->isPos.",IO".$posData[0]->isOnline.",H".$posData[0]->isHide;
			return $this->sendPushNotificationForPermission($posData,"pos",$message);
		}
		
		if($mode == "inventory"){
			$inventoryData = DB::table("inventory_general")->where("id",$id)->get();
			$message = "ID".$id.",IP".$inventoryData[0]->isPos.",IO".$inventoryData[0]->isOnline.",H".$inventoryData[0]->isHide;
			return $this->sendPushNotificationForPermission($inventoryData,"inventory",$message);
		}
	}
	
	public function sendPushNotificationForPermission($code,$mode,$funMessage){ 
		
		$message = $funMessage;
		$body = "Item ".($mode == "pos" ? $code[0]->item_name : $code[0]->product_name)." ".($code[0]->isPos == 0 ? "Deactivated" : "Activated");
		$tokens = array();
		$result = DB::select("SELECT branch_name,b.name as company FROM `branch` INNER Join company b on b.company_id = branch.company_id where branch.company_id = ? and branch_id = ?",[session("company_id"),session("branch")]);
		$title = "Item On/Off";
        $firebaseToken = DB::table("terminal_details")->where("branch_id",session("branch"))->whereNotNull("device_token")->get("device_token");//["cZIiT3EPTAKce8s8lPHTkZ:APA91bH0a0zModJDvMjwLmeMIqHNfyLriX1m2EWV9BI157KY6DtxsfWPDo-mYjl-Qh92dyfjU0Q0BM_HeXykZp6xy3LxoOxZmeLIxyBTimnfsCVIOuM0PBE8j53EV-_AWi6CVMOJIDMH"];//User::whereNotNull('device_token')->pluck('device_token')->all();
		// return $firebaseToken;
		foreach($firebaseToken as $token){
			array_push($tokens,$token->device_token);
		}
		

		$SERVER_API_KEY = 'AAAATXdhnIk:APA91bHFZZbCubOgnG3dihDVsqFbwGGQaBpC6f7BPFMnvpntpOOY88ysAEVAT2puQvdng3Xkd8j4HNVWFp1FQ2rHEe9g3Cv6nSZ7oeMsQtSh2GrJYNIxGHeogmen7TSPqRWHJxrG4QF_';

        $server_api_key_mobile = 'AAAA2dlOr6s:APA91bHGDpYDSZWI0LotnIYZUTpOTA9lLS56jsyB-2hq6Fsq6l0OPBoMYFqePTAbteVFawWzdyZOfMowMf-j8LBL8xJefdnpb_pZRVQHzu5rXykkdLBfPJgcr8gmPhPBDlXMWJy_-uv2';   
		   
        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => "Item Activated or Deactivated",
                "body" => $body,
				"icon" => "https://sabsoft.com.pk/Retail/public/assets/images/Sabify72.png",
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
	
	public function sendPushNotification($code,$name,$status){ 
		$statusmessage = ($status == "update" ? "updated" : "added");
		$message = "Item Code  # ".$code." (".$name.")  has been ".$statusmessage."";
		$tokens = array();
		$result = DB::select("SELECT branch_name,b.name as company FROM `branch` INNER Join company b on b.company_id = branch.company_id where branch.company_id = ? and branch_id = ?",[session("company_id"),session("branch")]);
		$title = ucwords($result[0]->company)." (".ucwords($result[0]->branch_name).")";
        $firebaseToken = DB::table("terminal_details")->where("branch_id",session("branch"))->whereNotNull("device_token")->get("device_token");//["cZIiT3EPTAKce8s8lPHTkZ:APA91bH0a0zModJDvMjwLmeMIqHNfyLriX1m2EWV9BI157KY6DtxsfWPDo-mYjl-Qh92dyfjU0Q0BM_HeXykZp6xy3LxoOxZmeLIxyBTimnfsCVIOuM0PBE8j53EV-_AWi6CVMOJIDMH"];//User::whereNotNull('device_token')->pluck('device_token')->all();
		foreach($firebaseToken as $token){
			array_push($tokens,$token->device_token);
		}

		$SERVER_API_KEY = 'AAAATXdhnIk:APA91bHFZZbCubOgnG3dihDVsqFbwGGQaBpC6f7BPFMnvpntpOOY88ysAEVAT2puQvdng3Xkd8j4HNVWFp1FQ2rHEe9g3Cv6nSZ7oeMsQtSh2GrJYNIxGHeogmen7TSPqRWHJxrG4QF_';

        $server_api_key_mobile = 'AAAA2dlOr6s:APA91bHGDpYDSZWI0LotnIYZUTpOTA9lLS56jsyB-2hq6Fsq6l0OPBoMYFqePTAbteVFawWzdyZOfMowMf-j8LBL8xJefdnpb_pZRVQHzu5rXykkdLBfPJgcr8gmPhPBDlXMWJy_-uv2';   
		   
        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => "Inventory Updated",
                "body" => "New set of Inventory is been updated",
				"icon" => "https://sabsoft.com.pk/Retail/public/assets/images/Sabify72.png",
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
		
		return 1;//json_encode($response);
	}
	
	 public function get_product_names(Request $request, inventory $inventory)
    {

        $result = $inventory->searchProductByNameAndItemCode($request->q);

        if ($result) {
            return response()->json(array('items'=>$result));
        } else {
            return 0;
        }
    }
	
	public function assignProductToVendors(Request $request,Vendor $vendor)
	{
		
		foreach($request->vendors as $vendorValue){
			
			$check = $vendor->check_product_by_vendor($vendorValue,$request->productId);
			if($check == 0){
				$items[] = [
					'vendor_id' => $vendorValue,
					'product_id' => $request->productId,
				];
			}
        }
		
        $result = (!empty($items) ? $vendor->insert_into_vendor_product($items,1) : 0);

        if ($result == 1){
            return true;
        }else{
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

}
