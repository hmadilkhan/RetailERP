<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class bankDiscount extends Model
{
    public function getBankDiscount()
    {
        return DB::table("bank_discount")->join("banks","banks.bank_id","=","bank_discount.bank_id")->where(['branch_id'=>session('branch'),'status_id' => 1])->get();
    }
    // CHECK FLOOR NAME EXISTS //
    public function check_bank_discount($bank){
        return count(DB::table("bank_discount")->where(['bank_id'=>$bank,'branch_id'=>session('branch'),'status_id' => 1])->get()) > 0 ? true : false;
    }

    // save record department //
    public function insert_bank_discount($data){
        return DB::table("bank_discount")->insertGetId($data);
    }

    // update record //
    public function modify($table,$data,$where){
        return DB::table($table)->where($where)->update($data);
    }
}