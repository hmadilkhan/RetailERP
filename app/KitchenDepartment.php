<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class KitchenDepartment extends Model
{
    public function getDepartments($company)
    {
        $result = DB::table("inventory_department")->where("company_id",$company)->get();
        return $result;
    }

    public function getgeneral()
    {
        $result = DB::table("kitchen_departments_general")->where('branch_id',session("branch"))->get();
        return $result;
    }
    public function getdetails()
    {
        $result = DB::table("kitchen_department_details")->join("inventory_department","inventory_department.department_id","=","kitchen_department_details.inventory_department_id")->get();
        return $result;
    }

    public function insert($table,$items)
    {
        $result = DB::table($table)->insertGetId($items);
        return $result;
    }

    public function modifyPrinter($table,$items,$id)
    {
        $result = DB::table($table)->where("id",$id)->update($items);
        return $result;
    }

    public function getPrinters($id)
    {
        $result = DB::table("kitchen_department_printers")->where("department_id",$id)->get();
        return $result;
    }

    public function update_depart($table,$items,$id)
    {
        $result = DB::table($table)->where("id",$id)->update($items);
        return $result;
    }

    public function getKitchenDepart($id)
    {
        $result = DB::table("kitchen_department_details")->join("inventory_department","inventory_department.department_id","=","kitchen_department_details.inventory_department_id")->where("kitchen_depart_id",$id)->get();
        return $result;
    }


}