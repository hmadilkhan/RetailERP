<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\bank;
use App\bankDiscount;




class BankDiscountController extends Controller
{
    public function index(bank $bank,bankDiscount $bankDiscount)
    {
        $discounts = $bankDiscount->getBankDiscount();
        $banks = $bank->get_banks();
        return view("BankDiscount.list",compact('banks','discounts'));
    }

    public function store(Request $request,bankDiscount $bankDiscount,bank $bank)
    {
        $data = [
            'branch_id' => session('branch'),
            'bank_id'=> $request->get('bank'),
            'percentage'=> $request->get('discount_percentage'),
            'status_id'=> 1,
            'date'=> date('Y-m-d H:i:s')
        ];

        if($bankDiscount->check_bank_discount($request->get('deptname'))){
            return response()->json(array("state"=>1,"msg"=>'This department already exists.',"contrl"=>'deptname'));
        }else {
            $result = $bankDiscount->insert_bank_discount($data);
            if($result){
                return response()->json(array("state"=>0,"msg"=>'',"contrl"=>''));
            }else{
                return response()->json(array("state"=>1,"msg"=>'Not saved :(',"contrl"=>''));
            }
        }
    }

    public function update(Request $request,bankDiscount $bankDiscount)
    {
            if($bankDiscount->modify("bank_discount",['status_id'=>2],['bank_discount_id' => $request->get('id')])){
                $data = [
                    'branch_id' => session('branch'),
                    'bank_id'=> $request->get('bank'),
                    'percentage'=> $request->get('discount_percentage'),
                    'status_id'=> 1,
                    'date'=> date('Y-m-d H:i:s')
                ];
                $result = $bankDiscount->insert_bank_discount($data);
                if($result){
                    return response()->json(array('state'=>0,'msg'=>'Saved changes :) '));
                }else{
                    return response()->json(array("state"=>1,"msg"=>'Not saved :(',"contrl"=>''));
                }

            }else {
                return response()->json(array('state'=>1,'msg'=>'Oops! not saved changes :('));
            }

    }

    public function deleteDiscount(Request $request,bankDiscount $bankDiscount)
    {
        if($bankDiscount->modify("bank_discount",['status_id'=>2],['bank_discount_id' => $request->get('id')])){
            return 1;
        }else{
            return 2;
        }
    }



}