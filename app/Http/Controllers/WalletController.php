<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\WalletDiscount;
use Image;



class WalletController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(WalletDiscount $walletDiscount)
    {
           $wallet_discount = $walletDiscount->getWalletDiscount();

           $wallets = DB::table('service_provider_details')
                                ->where('categor_id',6)
                                ->where('branch_id',session('branch'))
                                ->get();
        return view('Delivery.wallet.index',compact('wallet_discount','wallets'));
    }

    public function store(Request $request,WalletDiscount $walletDiscount)
    {
        $data = [
            'wallet_id'=> Crypt::decrypt($request->get('wallet')),
            'percentage'=> $request->get('discount_percentage'),
            'created_at'=> date('Y-m-d H:i:s')
        ];

        if($walletDiscount->check_wallet_discount(Crypt::decrypt($request->get('wallet')))){
            return response()->json(array("state"=>1,"msg"=>'This wallet already exists.',"contrl"=>'wallet'));
        }else {
            $result = $walletDiscount->insert_wallet_discount($data);
            if($result){
                return response()->json(array("state"=>0,"msg"=>'',"contrl"=>''));
            }else{
                return response()->json(array("state"=>1,"msg"=>'Not saved :(',"contrl"=>''));
            }
        }
    }

    public function update(Request $request,WalletDiscount $walletDiscount)
    {
            if($walletDiscount->modify("wallet_discount",['status'=>0,'updated_at'=>date('Y-m-d H:i:s')],['id' => $request->get('id')])){
                $data = [
                    'wallet_id'=> Crypt::decrypt($request->get('wallet')),
                    'percentage'=> $request->get('discount_percentage'),
                    'created_at'=> date('Y-m-d H:i:s')
                ];
                $result = $walletDiscount->insert_wallet_discount($data);
                if($result){
                    return response()->json(array('state'=>0,'msg'=>'Saved changes :) '));
                }else{
                    return response()->json(array("state"=>1,"msg"=>'Not saved :(',"contrl"=>''));
                }

            }else {
                return response()->json(array('state'=>1,'msg'=>'Oops! not saved changes :('));
            }

    }

    public function deleteDiscount(Request $request,WalletDiscount $walletDiscount)
    {
        if($walletDiscount->modify("wallet_discount",['status'=>0,'updated_at'=>date('Y-m-d H:i:s')],['id' => $request->get('id')])){
            return 1;
        }else{
            return 2;
        }
    }

}
