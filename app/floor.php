<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class floor extends Model
{
    public function getFloors()
    {
        $result = DB::table("floors")->where('branch_id',session('branch'))->get();
        return $result;
    }

    // CHECK FLOOR NAME EXISTS //
    public function check_floor($name){
        return count(DB::table("floors")->where(['floor_name'=>$name,'branch_id'=>session('branch')])->get()) > 0 ? true : false;
    }

    // save record department //
    public function insert_floor($data){
         return DB::table("floors")->insertGetId($data);
    }

    // update record //
    public function modify($table,$data,$where){
        return DB::table($table)->where($where)->update($data);
    }

    // delete record //
    public function deleteFloor($id){
        return DB::table("floors")->where("floor_id",$id)->delete();
    }
}