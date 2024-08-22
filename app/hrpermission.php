<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class hrpermission extends Model
{

	public function insert($table,$items){
		$result = DB::table($table)->insertGetId($items);
		return $result;
	}

	public function getpermissions(){
		$result = DB::table('hr_permission')->where('company_id', session('company_id'))->get();
		return $result;
	}

	public function exsist_chk(){
		$result = DB::select('select COUNT(id) AS counts from hr_permission where company_id = ?',[session('company_id')]);
		return $result;
	}

	public function update_permission($id,$items){
		$result = DB::table('hr_permission')->where('id',$id)->update($items);
		return $result;
	}

}