<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\promo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DateTime;
use App\inventory;

class PromoController extends Controller
{
    public function index(promo $promo)
    {
        $promotions = $promo->getPromotion();
        return view("Promo.index",compact('promotions'));
    }

    public function getCustomerByBranch(Request $request,promo $promo)
    {
        $result = $promo->getCustomersByBranch($request->branch);
        return $result;
    }

    public function create(Request $request,inventory $inventory,promo $promo)
    {
        $department = $inventory->department();
        $branch = $inventory->branch();
        $promo_mode = $promo->promotionMode();
        return view("Promo.create",compact('department','branch','promo_mode'));
    }

    public function store(Request $request,promo $promo)
    {
        $rules = [
            'suffix' => 'required',
            'startdate' => 'required',
            'enddate'=>'required',
            'branch'=>'required',
            'department'=>'required',
            'subdepartment'=>'required',
            'products'=>'required',
            'promotionmode'=>'required',
            'customer'=>'required',
            'message'=>'required',
        ];
        $this->validate($request, $rules);

        $general = [
          'company_id' => session("company_id"),
          'branch_id' => $request->branch,
          'promo_code' => $request->suffix,
          'generation_date' => $request->startdate,
          'expiration_date' => $request->enddate,
          'day' => 1,
          'promotion_mode' => $request->promotionmode,
          'access_mode' => 1,
          'message' => $request->message,
          'date' => date("Y-m-d"),
          'time' => date("H:i:s"),
        ];

        $result = $promo->insert("promotion",$general);
        foreach ($request->customer as $value)
        {
            $customer = [
                'promo_code' => $result,
                'cust_id' => $value,
                'limit_mode' => 1,
                'used' => 0,
                'status' => 1,
                'date' => date("Y-m-d"),
                'time' => date("H:i:s"),
            ];
            $res = $promo->insert("promotion_code_assign",$customer);
        }

        foreach ($request->products as $value)
        {
            $product = [
                'promo_code' => $result,
                'department_id' => $request->department,
                'sub_department_id' => $request->subdepartment,
                'product' => $value,
            ];
            $res = $promo->insert("promotion_product",$product);
        }

        return redirect("promotion");




    }
}