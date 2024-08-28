<?php

namespace App\Http\Controllers;

use App\inventory_department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Inventory;
use App\InventoryAddon;

class apiController extends Controller
{
    public function index()
    {
        return csrf_token();
    }

    public function getCountry()
    {
        $result = DB::table("country")->get();
        return $result;
    }

    public function getCity()
    {
        $result = DB::table("city")->get();
        return $result;
    }

    public function getCustomers(Request $request)
    {
        $user_id = DB::table("user_authorization")->where("company_id",$request->id)->get();
        if (!$user_id->isEmpty()){

            $result = DB::table("customers")
                ->join('country','customers.country_id','=','country.country_id')
                ->join('city','customers.city_id','=','city.city_id')
                ->select('customers.*','country.country_name','city.city_name')
                ->where("customers.user_id",$user_id[0]->user_id)
                ->where("online",1)
                ->get();
            if (!$result->isEmpty()) {
                return response()->json(["status"=>200,"data"=>$result]);
            }else{
                return response()->json(["status"=>404,"message"=>"404 Not Found"]);
            }
        }else{
            return response()->json(["status"=>200,"message"=>"404 Not Found"]);
        }

    }

	public function departmentJSON(Request $request)
    {
      $depart = DB::table('inventory_department')->where('company_id',$request->id)->get();
      return json_encode($depart);
    }

    public function subdepartmentJSON()
    {
      $subdepart = DB::table('inventory_sub_department')->get();
      return json_encode($subdepart);
    } 

    public function productJSON(Request $request)
    {
        $product = DB::select('
        SELECT a.id,a.item_code,a.product_name,a.slug,a.short_description, a.product_description,a.details,b.department_name,c.sub_depart_name,d.name as uom,CONCAT("https://retail.sabsoft.com.pk/assets/images/products/" ,a.image) as image,e.name as companyName,f.*,IFNULL((SELECT SUM(balance) from inventory_stock where product_id = a.id and branch_id = 10 ),0) as qty FROM inventory_general a
    INNER JOIN inventory_price f on f.product_id = a.id and f.status_id = 1
    INNER JOIN inventory_department b on b.department_id = a.department_id
    INNER JOIN inventory_sub_department c on c.sub_department_id = a.sub_department_id
    INNER JOIN inventory_uom d on d.uom_id = a.uom_id
    INNER JOIN company e on e.company_id = a.company_id where a.company_id = ? and a.status = 1 and a.product_mode IN(2, 3)', [$request->id]);
        return json_encode($product);
    }
	
	public function productJSONByDepartment(Request $request)
    {
        $product = DB::select('
        SELECT a.id,a.item_code,a.product_name,a.slug,a.short_description, a.product_description,a.details,b.department_name,c.sub_depart_name,d.name as uom,CONCAT("https://retail.sabsoft.com.pk/assets/images/products/" ,a.image) as image,e.name as companyName,f.*,IFNULL((SELECT SUM(balance) from inventory_stock where product_id = a.id and branch_id = 10 ),0) as qty FROM inventory_general a
    INNER JOIN inventory_price f on f.product_id = a.id and f.status_id = 1
    INNER JOIN inventory_department b on b.department_id = a.department_id
    INNER JOIN inventory_sub_department c on c.sub_department_id = a.sub_department_id
    INNER JOIN inventory_uom d on d.uom_id = a.uom_id
    INNER JOIN company e on e.company_id = a.company_id where a.department_id = ? and a.status = 1 and a.product_mode IN(2, 3)', [$request->id]);
        return json_encode($product);
    }
	
    public function productJSONByID(Request $request)
    {
        $product = DB::select('
        SELECT a.id,a.department_id,a.sub_department_id,a.slug,a.short_description,a.item_code,a.details,a.product_name,a.product_description,b.department_name,c.sub_depart_name,d.name as uom,CONCAT("https://retail.sabsoft.com.pk/assets/images/products/" ,a.image) as image,e.name as companyName,f.*,IFNULL((SELECT SUM(balance) from inventory_stock where product_id = a.id and branch_id = 10 ),0) as qty FROM inventory_general a
    INNER JOIN inventory_price f on f.product_id = a.id and f.status_id = 1
    INNER JOIN inventory_department b on b.department_id = a.department_id
    INNER JOIN inventory_sub_department c on c.sub_department_id = a.sub_department_id
    INNER JOIN inventory_uom d on d.uom_id = a.uom_id
    INNER JOIN company e on e.company_id = a.company_id where a.slug = ? and a.status = 1 and a.product_mode IN(2, 3) ',[$request->id]);
        return json_encode($product);
    }


    public function login(Request $request)
    {
      $result = DB::select("SELECT * FROM user_details where username = ? and show_password = ?",[$request->username,$request->password]);
      return $result;
    }

    public function productByDepartment(Request $request)
    {
        $product = DB::select('
        SELECT a.id,a.item_code,a.product_name,a.short_description,a.slug,a.details,a.product_description,b.department_name,c.sub_depart_name,d.name as uom,CONCAT("https://retail.sabsoft.com.pk/assets/images/products/" ,a.image) as image,e.name as companyName,f.*,IFNULL((SELECT SUM(balance) from inventory_stock where product_id = a.id and branch_id = 10 ),0) as qty FROM inventory_general a
    INNER JOIN inventory_price f on f.product_id = a.id and f.status_id = 1
    INNER JOIN inventory_department b on b.department_id = a.department_id
    INNER JOIN inventory_sub_department c on c.sub_department_id = a.sub_department_id
    INNER JOIN inventory_uom d on d.uom_id = a.uom_id
    INNER JOIN company e on e.company_id = a.company_id where a.department_id = ? and a.status = 1 and a.product_mode IN(2, 3)  ', [$request->id]);
        return json_encode($product);
    }
    public function productByRelated(Request $request)
    {
        $product = DB::select('
        SELECT a.id,a.item_code,a.product_name,a.short_description,a.slug,a.details,a.product_description,b.department_name,c.sub_depart_name,d.name as uom,CONCAT("https://retail.sabsoft.com.pk/assets/images/products/" ,a.image) as image,e.name as companyName,f.*,IFNULL((SELECT SUM(balance) from inventory_stock where product_id = a.id and branch_id = 10 ),0) as qty FROM inventory_general a
    INNER JOIN inventory_price f on f.product_id = a.id and f.status_id = 1
    INNER JOIN inventory_department b on b.department_id = a.department_id
    INNER JOIN inventory_sub_department c on c.sub_department_id = a.sub_department_id
    INNER JOIN inventory_uom d on d.uom_id = a.uom_id
    INNER JOIN company e on e.company_id = a.company_id where a.department_id = ? and a.status = 1 and a.product_mode IN(2, 3) LIMIT 6 ',[$request->id]);
        return json_encode($product);
    }

    public function productBySubdepartment(Request $request)
    {
        $product = DB::select('
        SELECT a.id,a.item_code,a.product_name,a.product_description,a.slug,a.department_id,b.department_name, c.sub_department_id,c.sub_depart_name,d.name as uom,CONCAT("https://retail.sabsoft.com.pk/assets/images/products/" ,a.image) as image,e.name as companyName,f.*,IFNULL((SELECT SUM(balance) from inventory_stock where product_id = a.id and branch_id = 10 ),0) as qty FROM inventory_general a
    INNER JOIN inventory_price f on f.product_id = a.id and f.status_id = 1
    INNER JOIN inventory_department b on b.department_id = a.department_id
    INNER JOIN inventory_sub_department c on c.sub_department_id = a.sub_department_id
    INNER JOIN inventory_uom d on d.uom_id = a.uom_id
    INNER JOIN company e on e.company_id = a.company_id where a.sub_department_id = ? and a.status = 1 and a.product_mode IN(2, 3)  ',[$request->id]);
        return json_encode($product);
    }

    public function getDepartments(Request $request)
    {
        $result = DB::table("inventory_department")
                  ->join("inventory_sub_department" , "inventory_sub_department.department_id","=","inventory_department.department_id")
                  ->join("inventory_general","inventory_general.department_id","=","inventory_department.department_id")
                  ->where("inventory_department.company_id",$request->id)
                  ->where("inventory_general.status",1)
                  ->groupBy('inventory_department.department_id')
                  ->get();
        return $result;
    }

    public function getmultiimage(Request $request){
        $image = DB::table("inventory_images")
                ->where("inventory_images.item_id",$request->id)
                ->get();
        return json_encode($image);
    }

    public function add_sales(Request $request)
    {

        $items = [
          'receipt_no' => $request->receipt_no,
          'opening_id' => 0,
          'order_mode_id' => $request->order_mode,
          'userid' => 0,
          'customer_id' => $request->customer,
          'payment_id' => $request->payment_id,
          'total_amount' => $request->total_amount,
          'total_item_qty' => $request->item_qty,
          'is_sale_return' => 0,
          'status' => $request->status,
          'delivery_date' => '',
          'branch' => $request->branch,
          'terminal_id' => 0,
          'sales_person_id' => 0,
          'date' => $request->date,
          'time' => $request->time,
        ];
        return $items;
//        $general = DB::table("sales_receipts")->insertGetId($items);
//        if ($general){
//            return $general;
//        }
//        else{
//            return 0;
//        }


    }

    public function add_customer(Request $request)
    {
        $user_id = DB::table("user_authorization")->where("company_id",$request->company_id)->get();
        $items = [
            'user_id' => $user_id[0]->user_id,
            'status_id' => 1,
            'country_id' => $request->country,
            'city_id' => $request->city,
            'name' => $request->name,
            'mobile'=> $request->mobile,
            'phone'=> $request->phone,
            'nic'=> 12456789,
            'address'=> $request->address,
            'image'=> null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'credit_limit' => 0,
            'discount' =>0,
            'email' => $request->email,
            'slug' => strtolower(Str::random(4)),
            'online' => 1,
        ];

        $customer = DB::table("customers")->insertGetId($items);
        return $customer;
    }

    public function topcollection(Request $request)
    {
        $user_id = DB::table("user_authorization")->where("company_id",$request->id)->get();
            $result = DB::select('SELECT a.id,a.item_code,a.slug,a.product_name,a.short_description,a.product_description,b.department_name,c.sub_depart_name,d.name as uom,CONCAT("https://retail.sabsoft.com.pk/assets/images/products/" ,a.image) as image,e.name as companyName,f.*,IFNULL((SELECT SUM(balance) from inventory_stock where product_id = a.id and branch_id = 10 ),0) as qty FROM inventory_general a
    INNER JOIN inventory_price f on f.product_id = a.id and f.status_id = 1
    INNER JOIN inventory_department b on b.department_id = a.department_id
    INNER JOIN inventory_sub_department c on c.sub_department_id = a.sub_department_id
    INNER JOIN inventory_uom d on d.uom_id = a.uom_id
    INNER JOIN company e on e.company_id = a.company_id where a.company_id = ? and a.status = 1 and a.product_mode IN(2, 3) LIMIT 8',[$request->id]);
            return $result;
    }
    public function newproduct(Request $request)
    {
        $user_id = DB::table("user_authorization")->where("company_id",$request->id)->get();
        $result = DB::select('SELECT a.id,a.item_code,a.slug,a.product_name,a.short_description,a.product_description,b.department_name,c.sub_depart_name,d.name as uom,CONCAT("https://retail.sabsoft.com.pk/assets/images/products/" ,a.image) as image,e.name as companyName,f.*,IFNULL((SELECT SUM(balance) from inventory_stock where product_id = a.id and branch_id = 10 ),0) as qty FROM inventory_general a
    INNER JOIN inventory_price f on f.product_id = a.id and f.status_id = 1
    INNER JOIN inventory_department b on b.department_id = a.department_id
    INNER JOIN inventory_sub_department c on c.sub_department_id = a.sub_department_id
    INNER JOIN inventory_uom d on d.uom_id = a.uom_id
    INNER JOIN company e on e.company_id = a.company_id where a.company_id = ? and a.status = 1 and a.product_mode IN(2, 3) ORDER BY a.id DESC LIMIT 8',[$request->id]);
        return $result;
    }

    public function addSales(Request $request)
    {
        $branch = DB::table("branch")->where("company_id",$request->company)->get();
        $general = [
            'receipt_no'=>$request->receipt_no,
            'opening_id'=>0,
            'order_mode_id'=>4,
            'userid'=>0,
            'customer_id'=>$request->customer,
            'payment_id'=>$request->cod,
            'total_amount'=>$request->totalamount,
            'total_item_qty'=>$request->totalqty,
            'is_sale_return'=>0,
            'status'=>1,
            'delivery_date'=>'',
            'branch'=>$branch[0]->branch_id,
            'terminal_id'=>0,
            'sales_person_id'=>0,
            'web'=>1,
            'date'=>$request->date,
            'time'=>$request->time,
        ];

        $insertGeneral = DB::table("sales_receipts")->insertGetId($general);

        $account = [
            'receipt_id'=>$insertGeneral,
            'receive_amount'=>0,
            'amount_paid_back'=>0,
            'total_amount'=>$request->totalamount,
            'balance_amount'=>"",
            'status'=>0,
        ];

        $accountGeneral = DB::table("sales_account_general")->insertGetId($account);

        $accountSubDetails = [
            'receipt_id'=>$insertGeneral,
            'discount_amount'=>0,
            'coupon'=>0,
            'promo_code'=>0,
            'sales_tax_amount'=>0,
            'service_tax_amount'=>0,
        ];

        $accountSubDetailsResult = DB::table("sales_account_subdetails")->insertGetId($accountSubDetails);

        return $insertGeneral;
    }

    public function addSalesDetails(Request $request)
    {
        $salesSubDetails = [
            'receipt_id'=>$request->receipt,
            'item_code'=>$request->code,
            'total_qty'=>$request->qty,
            'total_amount'=>$request->amount,
            'is_sale_return'=>0,
            'status'=>0,
            'total_cost'=>0,
            'discount'=>0,
        ];

        $salesSubDetails = DB::table("sales_receipt_details")->insertGetId($salesSubDetails);
        return $salesSubDetails;
    }
    public function getjsonsearch(Request $request){
        $product = DB::select('
        SELECT a.id,a.item_code,a.product_name,a.slug,a.short_description, a.product_description,a.details,b.department_name,c.sub_depart_name,d.name as uom,CONCAT("https://retail.sabsoft.com.pk/assets/images/products/" ,a.image) as image,e.name as companyName,f.*,IFNULL((SELECT SUM(balance) from inventory_stock where product_id = a.id and branch_id = 10 ),0) as qty FROM inventory_general a 
        INNER JOIN inventory_price f on f.product_id = a.id and f.status_id = 1 
        INNER JOIN inventory_department b on b.department_id = a.department_id 
        INNER JOIN inventory_sub_department c on c.sub_department_id = a.sub_department_id 
        INNER JOIN inventory_uom d on d.uom_id = a.uom_id 
        INNER JOIN company e on e.company_id = a.company_id where a.company_id = ? and a.product_name LIKE ? and a.status = 1 and a.product_mode IN(2, 3)', [$request->id,$request->search]);

        return json_encode($product);

    }
	
	public function productById(Request $request)
	{
		return Inventory::with("addons","addons.category","addons.category.addons","variations")->where("id",$request->id)->first();
	}


}


?>