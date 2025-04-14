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

}
