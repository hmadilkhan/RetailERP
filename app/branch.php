<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class branch extends Model
{
	protected $table = "branch";
	protected $guarded = [];
	public $timestamps = false; 
	
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

    public function getCompany(){
        $company = DB::table('company')->get();
        return $company;
    }

    public function exist($branchname, $companyid){
    	$result = DB::select('SELECT COUNT(branch_id) AS counter FROM branch
		WHERE branch_name = ? AND company_id = ?',[$branchname, $companyid]);
		return $result;
    }

    public function insert_branch($items){
        $result = DB::table('branch')->insertGetId($items);
        return $result;
    }
	
    public function get_branches($companyid){
        $filter = "";
        if (session("roleId") == 2) {
            $filter .= " AND a.company_id  = " . session("company_id");
        }else if(session("roleId") == 1){
            $filter = "";
        }else{
            $filter .= " AND a.branch_id  = ".session("branch");
        }

    	$result = DB::select('SELECT a.branch_id,b.name as company, a.branch_name, c.country_name,  d.city_name, a.branch_mobile, a.branch_email, a.branch_address, a.branch_logo FROM branch a
        INNER JOIN company b ON b.company_id = a.company_id
        INNER JOIN country c ON c.country_id = a.country_id
        INNER JOIN city d on d.city_id = a.city_id
        WHERE a.status_id = 1 and a.deleted_at IS NULL'.$filter);
		return $result;


    
    }

    public function getBranchesforAdmin(){
        $result = DB::select('SELECT a.branch_id, a.branch_name, c.country_name,  d.city_name, a.branch_mobile, a.branch_email, a.branch_address, a.branch_logo FROM branch a
        INNER JOIN company b ON b.company_id = a.company_id
        INNER JOIN country c ON c.country_id = a.country_id
        INNER JOIN city d on d.city_id = a.city_id
        WHERE a.status_id = 1 AND a.modify_by = ?',[session('userid')]);
        return $result;
    }

    public function getBranchById($id){
        $result = DB::table('branch')->where('branch_id',$id)->get();
        return $result;
    }


    public function branch_remove($id){
         $result = DB::table('branch')->where('branch_id', $id)->update(['status_id'=>2]);
        return $result;
    }

    public function branch_details($companyid, $branchid){
        $result = DB::select('SELECT a.branch_id,a.code,a.company_id, a.branch_name,c.country_name,d.city_name,a.branch_mobile,a.branch_email,a.branch_address, a.branch_ptcl, a.branch_logo, a.date, a.time,a.report_send_date  FROM branch a
        INNER JOIN company b ON b.company_id = a.company_id
        INNER JOIN country c ON c.country_id = a.country_id
        INNER JOIN city d on d.city_id = a.city_id
        WHERE a.branch_id = ?',[$branchid]);
        return $result;
    }

    public function branch_details_for_admin($branchid){
        $result = DB::select('SELECT a.branch_id,a.company_id, a.branch_name, c.country_name,  d.city_name, a.branch_mobile, a.branch_email, a.branch_address, a.branch_ptcl, a.branch_logo, a.date, a.time FROM branch a
        INNER JOIN company b ON b.company_id = a.company_id
        INNER JOIN country c ON c.country_id = a.country_id
        INNER JOIN city d on d.city_id = a.city_id
        WHERE   a.branch_id = ?',[$branchid]);
        return $result;
    }

    public function branch_update($id, $items){
       $result = DB::table('branch')->where('branch_id', $id)->update($items);
        return $result;
    }


    public function getWebsiteBranches($websiteId){

       return  DB::table('website_branches as web_branches')
                   ->join('branch','branch.branch_id','web_branches.branch_id')
                   ->select('branch.branch_id','branch.branch_name')
                   ->where('web_branches.website_id',$websiteId)
                   ->get();
    }    

}

