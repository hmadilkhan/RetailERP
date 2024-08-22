<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class designation extends Model
{
    public function getbranches()
    {
    	$branch = DB::table('branch')->where('company_id',session('company_id'))->get();
    	return $branch;
    }


       public function insert($items){
        $result = DB::table('designation')->insert($items);
        return $result;
    }

     public function exist($desg,$departid){
    	$result = DB::select('SELECT COUNT(designation_id) AS counts FROM designation WHERE designation_name = ? AND department_id = ?',[$desg,$departid]);
		return $result;
    }

         public function getdesg()
    {
    	$designation = DB::table('designation')->get();
    	return $designation;
    }

        public function getdesg_byid($id)
    {
    	$designation = DB::table('designation')->where('designation_id',$id)->get();
    	return $designation;
    }

     public function desg_delete($id)
	{
		if (DB::table('designation')->where('designation_id',$id)->delete()) {
		return 1;
		}
		else{
		return 0;	
		}
		
	}

		public function update_desg($id,$items){
		$result = DB::table('designation')->where('designation_id', $id)->update($items);
    	return $result;
	}


         public function getdepart()
    {
        $result = DB::select('SELECT a.department_id,a.department_name FROM departments a where a.branch_id = ?',[session("branch")]);
        return $result;
    }

}