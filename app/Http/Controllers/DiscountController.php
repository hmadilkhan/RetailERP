<?php

namespace App\Http\Controllers;

use App\discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;



class DiscountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, discount $discount)
    {
        if ($request->mode == 'in-active') {
            $status = 2;
        } else {
            $status = 1;
        }
        $discount = $discount->index($status);
        return view('discount.list', compact('discount', 'status'));
    }

    public function create(discount $discount)
    {
        DB::table("discount_hold_products")->delete(); //Empty Hold Database
        $discountType = $discount->getDiscountType();
        $departments = $discount->loadDepartments();
        $discountvalue = $discount->getDiscountValue();
        $websites = $discount->getWebsitesByCompany();
        return view("discount.create", compact('discountType', 'departments', 'discountvalue', 'websites'));
    }

    public function edit(Request $request, discount $discount)
    {
        $discountId = Crypt::decrypt($request->id);
        $discountType = $discount->getDiscountType();
        $websites = $discount->getWebsitesByCompany();
        $selectedDays = $discount->getWebsitesDaysByDiscountId($discountId);
        $departments = $discount->loadDepartments();
        $discountvalue = $discount->getDiscountValue();
        $discountGen = $discount->getDiscountGeneral($discountId);

        if ($discountGen[0]->discount_type == 3) {
            $discountCategories = $discount->getDiscountCategories($discountId);
            $usage = $discount->getDiscountUsageLimits($discountId);
            $period = $discount->getDiscountPeroid($discountId);
            return view("discount.edit-buy", compact('discountType', 'departments', 'discountvalue', 'discountGen', 'usage', 'period', 'discountCategories', 'websites', 'selectedDays'));
        } else {
            $discountGenDetails = $discount->getDiscountGeneralDetails($discountId);
            $usage = $discount->getDiscountUsageLimits($discountId);
            $period = $discount->getDiscountPeroid($discountId);
            return view("discount.edit", compact('discountType', 'departments', 'discountvalue', 'discountGen', 'discountGenDetails', 'usage', 'period', 'websites', 'selectedDays'));
        }
    }

    public function loadDepartment(discount $discount)
    {
        $departments = $discount->loadDepartments();
        return $departments;
    }

    public function loadProducts(discount $discount)
    {
        $products = $discount->loadPrducts();
        return $products;
    }

    public function loadProductsForDropdown(Request $request, discount $discount)
    {

        $result = DB::table("inventory_general")->whereIn('id', $request->prod)->get();
        foreach ($result as $value) {
            $items = [
                'id' => $value->id,
                'name' => $value->product_name,
            ];
            $check = DB::table("discount_hold_products")->where('id', $value->id)->count();
            if ($check == 0) {
                $discount->insertData('discount_hold_products', $items);
            }
        }
        $products = DB::table("discount_hold_products")->get();
        return $products;
    }

    public function loadProductsForDropdownEdit(Request $request, discount $discount)
    {
        DB::table("discount_hold_products")->delete(); //Empty Hold Database
        $result = DB::table("discount_product")->join("inventory_general", "inventory_general.id", "=", "discount_product.product_id")->where("discount_product.discount_id", $request->discount_id)->get();
        foreach ($result as $value) {
            $items = [
                'id' => $value->id,
                'name' => $value->product_name,
            ];
            $check = DB::table("discount_hold_products")->where('id', $value->id)->count();
            if ($check == 0) {
                $discount->insertData('discount_hold_products', $items);
            }
        }
        $products = DB::table("discount_hold_products")->get();
        return $products;
    }

    public function loadProductsBySearch(Request $request, discount $discount)
    {
        $result = $discount->loadProductsBySearch($request->search);
        return $result;
    }

    public function loadCustomers(discount $discount)
    {
        $customers = $discount->getCustomers();
        return $customers;
    }

    public function saveDiscount(Request $request, discount $discount)
    {
        // return $request;
        $status = 0;
        $onetimeuse = 0;
        $totalUsage = 0;

        // Setting the status
        if ($request->startdate > date("Y-m-d")) {
            $status = 3;
        } else {
            $status = 1;
        }

        // Setting the One Time Usage
        if ($request->chkonecustomer == 1) {
            $onetimeuse = 1;
        } else {
            $onetimeuse = 0;
        }

        // Setting the Total Time Usage
        if ($request->totlusage == 1) {
            $totalUsage = 1;
        } else {
            $totalUsage = 0;
        }

        try {
            DB::beginTransaction();
            $generalData = [
                "discount_code" => $request->code,
                "discount_type" => $request->type,
                "customer_eligibilty" => $request->discount_customer_eligibility,
                "branch_id" => session("branch"),
                "company_id" => session("company_id"),
                "website_id" => $request->website,
                "status" => $status,
            ];
            // return $request;
            if(isset($request->applyToVoucher)){
                $generalData["open_discount"] = 0;
            }

            $general = $discount->insertData('discount_general', $generalData);

            if ($request->minValue != "") {
                $minValue =   $request->minValue;
            } else {
                $minValue = 0;
            }

            //DISCOUNT PERCENTAGE & AMOUNT
            if ($request->type == 1 || $request->type == 2) {

                $general_details = [
                    "discount_id" => $general,
                    "discount_value" => $request->discountvalue,
                    "min_order" => $minValue,
                    "applies_to" => $request->discount_applies_to,
                ];

                $generaldetails = $discount->insertData('discount_general_details', $general_details);

                //BUY ONE GET ONE FREE
            } else {
                foreach ($request->ddlbuyProducts as $key => $value) {

                    $customerBuys = [
                        "discount_id" => $general,
                        "buy_qty" => $request->buyQty,
                        "buy_product" => $value,
                    ];
                    $customerInsert = $discount->insertData('discount_customer_buys', $customerBuys);
                }

                foreach ($request->ddlgetProducts as $key => $value) {

                    $customerGets = [
                        "discount_id" => $general,
                        "get_qty" => $request->buyQty,
                        "get_product" => $value,
                    ];
                    $customerInsert = $discount->insertData('discount_customer_gets', $customerGets);
                }
            }

            /**********************************************CATEGORY & PRODUCTS********************************************************/
            if ($request->discount_applies_to == 2) {
                # Collections...
                foreach ($request->ddlcategory as $key => $value) {
                    $discount_category = [
                        "discount_id" => $general,
                        "category_id" => $value,
                        "status" => 1,
                    ];
                    $res_category = $discount->insertData('discount_category', $discount_category);
                }
            } else if ($request->discount_applies_to == 3) {
                # Products...
                foreach ($request->ddlproduct as $key => $value) {
                    $discount_product = [
                        "discount_id" => $general,
                        "product_id" => $value,
                        "status" => 1,
                    ];
                    $res_product = $discount->insertData('discount_product', $discount_product);
                }
            }

            /**********************************************CUSTOMER ELIGIBILITY********************************************************/
            if ($request->discount_customer_eligibility != 1) {
                # Insert Customers...
                foreach ($request->customers as $key => $value) {
                    $discount_customer = [
                        "discount_id" => $general,
                        "cust_id" => $value,
                        "discount_limit" => 0,
                        "used" => 0,
                        "status" => 1,
                    ];
                    $res_customer = $discount->insertData('discount_customer', $discount_customer);
                }
            }

            /**********************************************TOTAL  USAGE ********************************************************/
            if ($request->chkTotalUsage == 1) {
                # code...
                $usage_limit = [
                    "discount_id" => $general,
                    "usage_limit" => $totalUsage,
                    "onetimeuse" => $onetimeuse,
                ];
                $res_total_usage = $discount->insertData('discount_limit', $usage_limit);
            }

            /********************************************** DAYS ********************************************************/
            if (count($request->days)) {
                foreach ($request->days as $key => $value) {
                    $discount_days = [
                        "discount_general_id" => $general,
                        "day" => $value,
                    ];
                    $res_total_usage = $discount->insertData('discount_days', $discount_days);
                }
            }

            /********************************************** DATES ********************************************************/
            $discount_period = [
                "discount_id" => $general,
                "startdate" => $request->startdate,
                "starttime" => $request->starttime,
                "enddate" => $request->enddate,
                "endtime" => $request->endtime,
            ];
            $discount_period = $discount->insertData('discount_period', $discount_period);
            DB::commit();
            return redirect("get-discount");
            // return response()->json(["status" => 200, "message" => "Discount saved successfully"]);
        } catch (Exception $e) {
            DB::rollback();
            return redirect("get-discount");
            // return response()->json(["status" => 500, "message" => "Discount did not save."]);

        }

        // return $request;
        return redirect()->action('DiscountController@index');
    }
    
    public function reactiveDiscount(Request $request, discount $discount){
        
        $generalData = [
                "startdate" => $request->startdate,
                "starttime" => $request->startime,
            ];
            
            if($discount->updateData('discount_period', $generalData, "discount_id", $request->id)){
                return $discount->updateData('discount_general', ['status'=>1], "discount_id", $request->id) ? 1 : 0;
            }else{
                return 0;
            } 
    }

    public function updateDiscount(Request $request, discount $discount)
    {
        // return $request;
        // Setting the status
        if ($request->startdate > date("Y-m-d")) {
            $status = 3;
        } else {
            $status = 1;
        }

        try {
            DB::beginTransaction();
            $generalData = [
                "discount_type" => $request->type,
                "customer_eligibilty" => $request->discount_customer_eligibility,
                "website_id" => $request->website,
                "status" => $status,
            ];
            $general = $discount->updateData('discount_general', $generalData, "discount_id", $request->discount_id);

            if ($request->minValue != "") {
                $minValue =   $request->minValue;
            } else {
                $minValue = 0;
            }

            //DISCOUNT PERCENTAGE & AMOUNT
            if ($request->type == 1 || $request->type == 2) {

                $general_details = [
                    "discount_id" => $request->discount_id,
                    "discount_value" => $request->discountvalue,
                    "min_order" => $minValue,
                    "applies_to" => $request->discount_applies_to,
                ];

                $generaldetails = $discount->updateData('discount_general_details', $general_details,"discount_id",$request->discount_id);


                //BUY ONE GET ONE FREE
            } else  if ($request->type == 3) {
                DB::table("discount_customer_buys")->where("discount_id",$request->discount_id)->delete();
                DB::table("discount_customer_gets")->where("discount_id",$request->discount_id)->delete();

                foreach ($request->ddlbuyProducts as $key => $value) {
                    $customerBuys = [
                        "discount_id" => $request->discount_id,
                        "buy_qty" => $request->buyQty,
                        "buy_product" => $value,
                    ];
                    $customerInsert = $discount->insertData('discount_customer_buys', $customerBuys);
                }

                foreach ($request->ddlgetProducts as $key => $value) {
                    $customerGets = [
                        "discount_id" => $request->discount_id,
                        "get_qty" => $request->buyQty,
                        "get_product" => $value,
                    ];
                    $customerInsert = $discount->insertData('discount_customer_gets', $customerGets);
                }
            }

             /**********************************************CATEGORY & PRODUCTS********************************************************/
             if ($request->discount_applies_to == 2) {
                DB::table("discount_category")->where("discount_id",$request->discount_id)->delete();
                # Collections...
                foreach ($request->ddlcategory as $key => $value) {
                    $discount_category = [
                        "discount_id" => $request->discount_id,
                        "category_id" => $value,
                        "status" => 1,
                    ];
                    $res_category = $discount->insertData('discount_category', $discount_category);
                }
            } else if ($request->discount_applies_to == 3) {
                # Products...
                DB::table("discount_product")->where("discount_id",$request->discount_id)->delete();
                foreach ($request->ddlproduct as $key => $value) {
                    $discount_product = [
                        "discount_id" => $request->discount_id,
                        "product_id" => $value,
                        "status" => 1,
                    ];
                    $res_product = $discount->insertData('discount_product', $discount_product);
                }
            }

            /**********************************************CUSTOMER ELIGIBILITY********************************************************/
            if ($request->discount_customer_eligibility != 1) {
                # Insert Customers...
                DB::table("discount_customer")->where("discount_id",$request->discount_id)->delete();
                foreach ($request->customers as $key => $value) {
                    $discount_customer = [
                        "discount_id" => $request->discount_id,
                        "cust_id" => $value,
                        "discount_limit" => 0,
                        "used" => 0,
                        "status" => 1,
                    ];
                    $res_customer = $discount->insertData('discount_customer', $discount_customer);
                }
            }

            /**********************************************TOTAL  USAGE ********************************************************/
            if ($request->chkTotalUsage == 1) {
                # code...
                $usage_limit = [
                    "usage_limit" => $totalUsage,
                    "onetimeuse" => $onetimeuse,
                ];
                $res_total_usage = $discount->updateData('discount_limit', $usage_limit,"discount_id",$request->discount_id);
            }

            /********************************************** DAYS ********************************************************/
            if (count($request->days)) {
                DB::table("discount_days")->where("discount_general_id",$request->discount_id)->delete();
                foreach ($request->days as $key => $value) {
                    $discount_days = [
                        "discount_general_id" => $request->discount_id,
                        "day" => $value,
                    ];
                    $res_total_usage = $discount->insertData('discount_days', $discount_days);
                }
            }

            /********************************************** DATES ********************************************************/
            $discount_period = [
                "startdate" => $request->startdate,
                "starttime" => $request->starttime,
                "enddate" => $request->enddate,
                "endtime" => $request->endtime,
            ];
            $discount_period = $discount->updateData('discount_period', $discount_period,"discount_id",$request->discount_id);
            DB::commit();
            return redirect("get-discount");
        } catch (\Exception $th) {
            DB::rollback();
            // return $th->getMessage();
            return redirect("get-discount");
        }
    }

    public function getDiscountInfo(Request $request, discount $discount)
    {
        $result = $discount->getDiscountInfo($request->id);
        return $result;
    }

    public function getDiscountCategories(Request $request, discount $discount)
    {
        $result = $discount->getDiscountCategories($request->id);
        return $result;
    }

    public function getDiscountProducts(Request $request, discount $discount)
    {
        $result = $discount->getDiscountProducts($request->id);
        return $result;
    }

    public function getDiscountCustomers(Request $request, discount $discount)
    {
        $result = $discount->getDiscountCustomers($request->id);
        return $result;
    }

    public function getCustomerBuys(Request $request, discount $discount)
    {
        $result = $discount->getCustomerBuys($request->id);
        return $result;
    }

    public function getCustomerGets(Request $request, discount $discount)
    {
        $result = $discount->getCustomerGets($request->id);
        return $result;
    }

    public function inactiveDiscount(Request $request, discount $discount)
    {
        return ($discount->removeDiscount($request->id, $request->mode)) ? 1 : 0;
        // return $result;
    }
}
