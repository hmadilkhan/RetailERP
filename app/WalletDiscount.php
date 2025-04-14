<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WalletDiscount extends Model
{
    protected $table = "wallet_discount";
    public function getWalletDiscount()
    {
        return DB::table('wallet_discount')
                ->join('service_provider_details','service_provider_details.id','wallet_discount.wallet_id')
                ->where('service_provider_details.categor_id',6)
                ->where('service_provider_details.branch_id',session('branch'))
                ->select('wallet_discount.*','service_provider_details.provider_name','service_provider_details.image')
                ->get();
    }

}
