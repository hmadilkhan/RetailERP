<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class department extends Model
{

	    public function getbranches()
    {
    	$branch = DB::table('branch')->where('company_id',session('company_id'))->get();
    	return $branch;
    }

        public function getdepart()
    {
    	$result = DB::select('SELECT a.department_id, b.branch_name, a.department_name FROM departments a INNER JOIN branch b ON b.branch_id = a.branch_id WHERE a.branch_id = ?',[session("branch")]);
		return $result;
    }

        public function getdepart_byid($id)
    {
    	$result = DB::select('SELECT a.department_id, b.branch_name, a.department_name FROM departments a INNER JOIN branch b ON b.branch_id = a.branch_id
WHERE a.department_id = ?',[$id]);
		return $result;
    }

       public function insert($items){
        $result = DB::table('departments')->insert($items);
        return $result;
    }

     public function exist($departname, $branchid){
    	$result = DB::select('SELECT COUNT(department_id) AS counts FROM departments WHERE department_name = ? AND branch_id = ?',[$departname, $branchid]);
		return $result;
    }

    public function depart_delete($id)
	{
		if (DB::table('departments')->where('department_id',$id)->delete()) {
		return 1;
		}
		else{
		return 0;	
		}
		
	}

		public function update_depart($id,$items){
		$result = DB::table('departments')->where('department_id', $id)->update($items);
    	return $result;
	}




}