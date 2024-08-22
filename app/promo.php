<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class promo extends Model
{
    public function promotionMode()
    {
        $result = DB::table("promotion_mode")->get();
        return $result;
    }

    public function getPromotion()
    {
        $result = DB::table("promotion")
                ->join("promotion_mode","promotion_mode.id","=","promotion.promotion_mode")
                ->join("accessibility_mode","accessibility_mode.status_id","=","promotion.access_mode")
                ->where("branch_id",session("branch"))->get();
        return $result;
    }

    public function getCustomersByBranch($branch)
    {
        $result = DB::select("SELECT * FROM customers a inner join user_authorization b on b.user_id = a.user_id where b.branch_id = ?",[$branch]);
        return $result;
    }
    
    public function insert($table,$items)
    {
        $result = DB::table($table)->insertGetId($items);
        return $result;
    }
}