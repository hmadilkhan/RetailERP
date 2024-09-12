<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class inventory_department extends Model
{

    protected $table = "inventory_department";

    // save record department //
    public function insert_dept($data){

       return DB::table("inventory_department")->insertGetId($data);

    }

     // save record sub-department //
    public function insert_sdept($data){

       return DB::table("inventory_sub_department")->insert($data);

    }


    // update record //
    public function modify($table,$data,$where){
      return DB::table($table)->where($where)->update($data);
    }

    // remove record //
    public function remove_sbdept($id){
      
      return DB::table("inventory_sub_department")->where('department_id',$id)->delete();
    }

     // get department record //
    public static function getdepartment($id){
     return DB::table("inventory_department")->where(['company_id'=>session('company_id').(empty($id) ? "',department_id=>".$id."'" : "") ])->where("status",1)->get();

    } 

     // get sub-department record //
    public static function get_subdepart($id){

     return DB::table("inventory_department as dept")->join('inventory_sub_department as sbdpt','sbdpt.department_id','=','dept.department_id')->where(['dept.company_id'=>session('company_id').(empty($id) ? "',dept.department_id=>".$id."'" : "")])->get();

    }   

   public  function get_edit($id){

     return DB::table("inventory_department as dept")->join('inventory_sub_department as sbdpt','sbdpt.department_id','=','dept.department_id')->select('dept.department_name as deptname','sbdpt.sub_department_id as sb_id','sbdpt.sub_depart_name as sbname')->where(['dept.company_id'=>session('company_id'),'dept.department_id'=>$id])->get();

    } 

     // check record department exists // 
     public function check_dept($name){
      return count(DB::table("inventory_department")->where(['department_name'=>$name,'company_id'=>session('company_id')])->get()) > 0 ? true : false;

    } 

     // check record department id check this name another taken // 
    public function check_depart_code($code){
        return count(DB::table("inventory_department")->where(['code'=>$code,'company_id'=>session('company_id')])->get()) > 0 ? true : false;
    }
	
	public function check_edit_depart_name($id,$name){
        return count(DB::table("inventory_department")->where(['department_name'=>$name,'company_id'=>session('company_id')])->where('department_id','!=',$id)->get()) > 0 ? true : false;
    }
	
	public function check_edit_depart_code($id,$code){
        return count(DB::table("inventory_department")->where(['code'=>$code,'company_id'=>session('company_id')])->where('department_id','!=',$id)->get()) > 0 ? true : false;
    }
	
	public function check_edit_sub_depart_name($id,$name,$departmentId){
		if($name != ""){
			return count(DB::table("inventory_sub_department")->where(['sub_depart_name'=>$name,'department_id'=>$departmentId])->where('sub_department_id','!=',$id)->get()) > 0 ? true : false;
		}
    }
	
	public function check_edit_sub_depart_code($id,$code,$departmentId){
		if($code != ""){
			return count(DB::table("inventory_sub_department")->where(['code'=>$code,'department_id'=>$departmentId])->where('sub_department_id','!=',$id)->get()) > 0 ? true : false;
		}
    }


     // check record sub department exists // 
     public function check_sdept($id,$name,$dept,$code){
     return count(DB::table("inventory_sub_department")->where('sub_department_id','!=',$id)->where('code',$code)->where('sub_depart_name',$name)->where('department_id','=',$dept)->get()) > 0 ? true : false;

    }

    public function depart_exists($departname){
        $exists = DB::select('SELECT COUNT(department_id) AS counter FROM inventory_department WHERE department_name = ? and company_id = ?',[$departname,session('company_id')]);
        return $exists;
    }

      public function subdepart_exists($subdepartname,$deptid){
        $exists = DB::select('SELECT COUNT(sub_department_id) AS counter FROM inventory_sub_department WHERE sub_depart_name = ? and department_id = ?',[$subdepartname,$deptid]);
        return $exists;
    }

    public function get_departments()
    {
        $departs = DB::table('inventory_department')->where('company_id',session('company_id'))->get();
        return $departs;
    }


    public function get_subdepartments($departid)
    {
        $departs = DB::table('inventory_sub_department')->where('department_id',$departid)->get();
        return $departs;
    }


        public function update_depart($id,$items){
        $result = DB::table('inventory_department')->where('department_id', $id)->update($items);
        return $result;
    }


    // save record insert_sections //
    public function insert_section($data){

      return DB::table("inventory_department_sections")->insert($data);
    }  
    
    // remove sections
    public function remove_section($data){

      return DB::table("inventory_department_sections")->where($data)->where('company_id',session('company_id'))->delete();
    }  
    
    public function inventoryDepartmentSection()
    {
      return $this->hasMany("App\InventoryDepartmentSection","department_id","department_id")->select('id','department_id','section_id');
    }   

}
