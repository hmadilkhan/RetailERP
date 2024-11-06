<?php

namespace App;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class inventory_department extends Model
{

  protected $table = "inventory_department";

  // save record department //
  public function insert_dept($data)
  {

    return DB::table("inventory_department")->insertGetId($data);
  }

  // save record sub-department //
  public function insert_sdept($data)
  {

    return DB::table("inventory_sub_department")->insert($data);
  }


  // update record //
  public function modify($table, $data, $where)
  {
    return DB::table($table)->where($where)->update($data);
  }

  // remove record //
  public function remove_sbdept($id)
  {

    return DB::table("inventory_sub_department")->where('department_id', $id)->delete();
  }

  // get department record //
  public static function getdepartment($id,$selectedColumn = [])
  {
    $getRecord = DB::table("inventory_department")
                    ->where(['company_id' => session('company_id')])
                    ->where("status", 1);

        if(!empty($id)){
            return $getRecord->where('department_id',$id);
        }

        if(!empty($selectedColumn)){
            return $getRecord->select($selectedColumn);
        }

    return $getRecord->get();
  }

  // get sub-department record //
  public static function get_subdepart($id)
  {

    return DB::table("inventory_department as dept")->join('inventory_sub_department as sbdpt', 'sbdpt.department_id', '=', 'dept.department_id')->where(['dept.company_id' => session('company_id') . (empty($id) ? "',dept.department_id=>" . $id . "'" : "")])->where('sbdpt.status',1)->get();
  }

  public  function get_edit($id)
  {

    return DB::table("inventory_department as dept")->join('inventory_sub_department as sbdpt', 'sbdpt.department_id', '=', 'dept.department_id')->select('dept.department_name as deptname', 'sbdpt.sub_department_id as sb_id', 'sbdpt.sub_depart_name as sbname')->where(['dept.company_id' => session('company_id'), 'dept.department_id' => $id])->get();
  }

  // check record department exists //
  public function check_dept($name)
  {
    return count(DB::table("inventory_department")->where(['department_name' => $name, 'company_id' => session('company_id'),'status'=>1])->get()) > 0 ? true : false;
  }

  // check record department id check this name another taken //
  public function check_depart_code($code)
  {
    return count(DB::table("inventory_department")->where(['code' => $code, 'company_id' => session('company_id'),'status'=>1])->get()) > 0 ? true : false;
  }

  public function check_edit_depart_name($id, $name)
  {
    return count(DB::table("inventory_department")->where(['department_name' => $name, 'company_id' => session('company_id'),'status'=>1])->where('department_id', '!=', $id)->get()) > 0 ? true : false;
  }

  public function check_edit_depart_code($id, $code)
  {
    return count(DB::table("inventory_department")->where(['code' => $code, 'company_id' => session('company_id'),'status'=>1])->where('department_id', '!=', $id)->get()) > 0 ? true : false;
  }

  public function check_edit_sub_depart_name($id, $name, $departmentId)
  {
    if ($name != "") {
      return count(DB::table("inventory_sub_department")->where(['sub_depart_name' => $name, 'department_id' => $departmentId,'status'=>1])->where('sub_department_id', '!=', $id)->get()) > 0 ? true : false;
    }
  }

  public function check_edit_sub_depart_code($id, $code, $departmentId)
  {
    if ($code != "") {
      return count(DB::table("inventory_sub_department")->where(['code' => $code, 'department_id' => $departmentId,'status'=>1])->where('sub_department_id', '!=', $id)->get()) > 0 ? true : false;
    }
  }


  // check record sub department exists //
  public function check_sdept($id, $name, $dept, $code)
  {
    return count(DB::table("inventory_sub_department")->where('sub_department_id', '!=', $id)->where('status','=',1)->where('code', $code)->where('sub_depart_name', $name)->where('department_id', '=', $dept)->get()) > 0 ? true : false;
  }

  public function depart_exists($departname)
  {
    $exists = DB::select('SELECT COUNT(department_id) AS counter FROM inventory_department WHERE status = 1 and department_name = ? and company_id = ?', [$departname, session('company_id')]);
    return $exists;
  }

  public function subdepart_exists($subdepartname, $deptid)
  {
    $exists = DB::select('SELECT COUNT(sub_department_id) AS counter FROM inventory_sub_department WHERE status = 1 and sub_depart_name = ? and department_id = ?', [$subdepartname, $deptid]);
    return $exists;
  }

  public function get_departments()
  {
    $departs = DB::table('inventory_department')->where('company_id', session('company_id'))->where('status',1)->get();
    return $departs;
  }


  public function get_subdepartments($departid)
  {
    $departs = DB::table('inventory_sub_department')->where('department_id', $departid)->where('status',1)->get();
    return $departs;
  }


  public function update_depart($id, $items)
  {
    $result = DB::table('inventory_department')->where('department_id', $id)->where('company_id', session('company_id'))->where('status',1)->update($items);
    return $result;
  }


  // save record insert_sections //
  public function insert_section($data)
  {

    return DB::table("inventory_department_sections")->insert($data);
  }

  // remove sections
  public function remove_section($id)
  {

    return DB::table("inventory_department_sections")->where('department_id', $id)->delete();
  }

  public function inventoryDepartmentSection()
  {
    return $this->hasMany("App\InventoryDepartmentSection", "department_id", "department_id");
  }

  public function inventoryProducts()
  {
    return $this->hasMany(Inventory::class, "department_id", "department_id");
  }

  public function products()
  {
    return $this->hasMany(Inventory::class, "department_id", "department_id")->where("status", 1);
  }

  public function websiteProducts()
    {
        return $this->hasManyThrough(
            WebsiteProduct::class, // The related model you want to access
            Inventory::class, // The intermediate model
            'department_id', // Foreign key on the InventoryGeneral table
            'inventory_id', // Foreign key on the WebsiteProduct table
            'department_id', // Local key on the InventoryDepartment table
            'id'  // Local key on the InventoryGeneral table
        );
    }
}
