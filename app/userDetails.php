<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class userDetails extends Model
{
    public function getcountry()
    {
    	$country = DB::table('country')->get();
    	return $country;
    }
     public function getcity()
    {
    	$city = DB::table('city')->get();
    	return $city;
    }
    public function getroles()
    {
        if(session('roleId') == 2) {
            $role = DB::select('SELECT * FROM user_roles WHERE role_id NOT IN (1)');
        }else if (session('roleId') == 4) {
    		$role = DB::select('SELECT * FROM user_roles WHERE role_id NOT IN (1 , 2)');
    	}else{
    		$role = DB::select('SELECT * FROM user_roles');
    	}
    	
    	return $role;
    }
    public function getbranches()
    {
    	$filter = "";
    	if (session("roleId") == 2) 
    	{
    		$filter .= " where company_id = ".session("company_id")." and status_id = 1";
    	}

    	$branch = DB::select('Select * from branch '.$filter);
    	return $branch;
    }


    /*
        Admin Methods
    */
    public function getBranchByCompany($id)
    {
        $branch = DB::table('branch')->where(['company_id' => $id,'status_id' => 1 ])->get();
        return $branch;
    }

    public function getbranchesForAdmin($id)
    {
        $branch = DB::table('branch')->where('company_id',$id)->get();
        return $branch;
    }

    public function getCompany()
    {
    	$filter = "";
    	if (session("roleId") == 2) 
    	{
    		$filter .= " where company_id = ".session("company_id");
    	}
    	
        $branch = DB::select('Select * from company '.$filter);
        return $branch;
    }

    public function exist($username){
    	$result = DB::select('SELECT COUNT(id) AS counter FROM user_details WHERE username = ?',[$username]);
    	return $result[0]->counter;
    }

    public function getrolesForAdmin()
    {
        $role = DB::select('SELECT * FROM user_roles WHERE role_id  = 2');
        return $role;
    }

    public function insert_user($table, $items)
    {
    	$result = DB::table($table)->insertGetId($items);
       	return $result;   
    }

    public function get_users()
    {
    	$filter = "";
    	if (session("roleId") == 2) 
    	{
    		$filter .= " and b.company_id = ".session("company_id");
    	}else if(session("roleId") == 1){
            $filter = "";
        }
    	else //if (session("roleId") == 3)
    	{
    		$filter .= " and b.branch_id = ".session('branch');
    	}
    	// return  $filter;
    	$result = DB::select('SELECT a.id, a.fullname, a.username, c.role, d.branch_name, e.status_name, b.authorization_id,a.image,b.isLoggedIn,a.show_password FROM user_details a
		INNER JOIN user_authorization b ON b.user_id = a.id
		INNER JOIN user_roles c ON c.role_id = b.role_id
		INNER JOIN branch d ON d.branch_id = b.branch_id
		INNER JOIN accessibility_mode e ON e.status_id = b.status_id
		WHERE b.status_id = 1 '.$filter);
		return $result;
    }

    public function user_details($userid)
    {
    	$details = DB::select('SELECT a.id, a.fullname, a.email, a.contact, f.country_name, g.city_name, a.address, a.username, a.show_password, c.role, d.branch_name, e.status_name, a.created_at, b.authorization_id,b.company_id,b.branch_id,a.image FROM user_details a
		INNER JOIN user_authorization b ON b.user_id = a.id
		INNER JOIN user_roles c ON c.role_id = b.role_id
		INNER JOIN branch d ON d.branch_id = b.branch_id
		INNER JOIN accessibility_mode e ON e.status_id = b.status_id
        INNER JOIN country f ON f.country_id = a.country_id
        INNER JOIN city g ON g.city_id = a.city_id
		WHERE a.id = ?',[$userid]);
		return $details;
    }

    public function update_userdetails($id,$items){
		 $result = DB::table('user_details')->where('id', $id)->update($items);
    	return $result;

	}

	 public function update_user_authorization($id,$items){
		 $result = DB::table('user_authorization')->where('authorization_id', $id)->update($items);
    	return $result;
	}

	public function delete_user($id){
		 $result = DB::table('user_authorization')->where('authorization_id', $id)->update(['status_id'=>2]);
		 $result = DB::table('user_details')->where('id', $id)->update(['deleted_at'=> date("Y-m-d H:i:s")]);
    	 return $result;
	}

    public function chk_user($username){
        $result = DB::table('user_details')->where('username', $username)->count('username');
        return $result;
    }

     public function chk_role($rolename)
    {
        $result = DB::select('SELECT COUNT(role_id) as id FROM `user_roles` WHERE role = ?',[$rolename]);
        return $result[0]->id;
    }

    public function addRole($items)
    {
        if($result = DB::table('user_roles')->insert($items))
        {
            return 1;
        }
        else
        {
            return 0;
        }
        
    }

    public function getRole()
    {
        $result = DB::table('user_roles')->get();
        return $result;
    }


    public function createPermission($permission)
    {
        if(DB::table('users_sales_permission')->insert($permission))
        {
             return 1;  
        }
        else
        {
            return 0;
        }
        
    }


    public function getPermission($id)
    {
        $result = DB::table('users_sales_permission')->where('terminal_id',$id)->get();
        return $result;
    }

    public function updatePermission($id,$items)
    {
        $result = DB::table('users_sales_permission')->where('permission_id',$id)->update($items);
        return $result;
    }

    public function getTerminalName($id)
    {
        $result = DB::select("SELECT a.terminal_name, b.branch_name FROM terminal_details a INNER JOIN branch b on b.branch_id = a.branch_id where a.terminal_id = ?",[$id]);
        return $result;
    }

    public function getBranchName($id)
    {
        $result = DB::table("branch")->where("terminal_id",$id)->get();
        return $result;
    }








       
       

     
}
