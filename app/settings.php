<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class settings extends Model
{
    public function insert($table, $items)
    {
        $result = DB::table($table)->insertGetId($items);
        return $result;
    }

    public function getdetails(){
        $result = DB::table('pages_details')->get();
        return $result;
    }

    public function getpages(){
        $result = DB::select('SELECT * FROM pages_details WHERE page_url != "NULL"');
        return $result;
    }
    public function getCompany(){
//        $company = DB::table('company')->where('company_id',[session("company_id")])->get();
        $company = DB::table('company')->get();
        return $company;
    }

    public function getparentid($pageid){
        $result = DB::select('SELECT * FROM pages_details WHERE id = ?',[$pageid]);
        return $result;
    }
    public function exsist_chk_modules($companyid, $pageid)
    {
        $result = DB::select('SELECT COUNT(id) AS counts FROM module_permissions_details WHERE company_id = ? AND page_id = ?',[$companyid, $pageid]);
        return $result;
    }

    public function getcompany_name(){
        $result = DB::select('SELECT a.id, a.company_id, b.name FROM module_permissions_details a INNER JOIN company b ON b.company_id = a.company_id INNER JOIN pages_details c ON c.id = a.page_id GROUP BY a.company_id');
        return $result;
    }

    public function getmodulesdetails(){
        $result = DB::select('SELECT a.id, a.company_id, b.name, c.page_name FROM module_permissions_details a INNER JOIN company b ON b.company_id = a.company_id INNER JOIN pages_details c ON c.id = a.page_id');
        return $result;
    }

    public function getbycompanyid($companyid)
    {
        $result = DB::select('SELECT a.id, b.page_name FROM module_permissions_details a INNER JOIN pages_details b ON b.id = a.page_id WHERE a.company_id = ?',[$companyid]);
        return $result;
    }

    public function delete_module($id){
        if (DB::table('module_permissions_details')->where('id',$id)->delete()) {
            return 1;
        }
        else{
            return 0;
        }
    }
}