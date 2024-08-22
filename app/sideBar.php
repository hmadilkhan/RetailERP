<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class sideBar extends Model
{
    public function insert($table,$items){

        $result = DB::table($table)->insertGetId($items);
        return $result;
    }

    public function exsist_chk($pagename, $pageurl)
    {
        $result = DB::select('SELECT COUNT(id) AS counts FROM pages_details WHERE page_name = ? AND page_url = ?',[$pagename, $pageurl]);
        return $result;
    }
    public function getdetails(){
        $result = DB::table('pages_details')->get();
        return $result;
    }
    public function getroles(){
        $result = DB::table('user_roles')->get();
        return $result;
    }
    public function getpages(){
       $result = DB::select('SELECT * FROM pages_details WHERE page_url != "NULL"');
       return $result;

        // $result = DB::select('SELECT a.page_id AS id, b.page_name FROM module_permissions_details a INNER JOIN pages_details b ON a.page_id = b.id WHERE a.company_id = ?',[session("company_id")]);
        // return $result;
    }

    public function getparents(){
        $result = DB::select('SELECT * FROM pages_details WHERE page_mode = "Parent"');
        return $result;
    }

    public function getparentid($pageid){
        $result = DB::select('SELECT * FROM pages_details WHERE id = ?',[$pageid]);
        return $result;
    }


    public function getchilds($parentid){
        $result = DB::select('SELECT id,page_name, page_mode FROM pages_details WHERE parent_id = ? UNION SELECT id,page_name,page_mode from pages_details WHERE parent_id = (SELECT id FROM pages_details WHERE icofont_arrow = 1 AND parent_id = ?) UNION SELECT id,page_name,page_mode FROM pages_details WHERE parent_id = (SELECT id FROM pages_details WHERE icofont_arrow = 1 AND parent_id = ?) UNION SELECT id,page_name,page_mode FROM pages_details WHERE parent_id = (SELECT id FROM pages_details WHERE page_mode = "Grand Child" AND icofont_arrow = 1 AND parent_id = ?)',[$parentid,$parentid,$parentid,$parentid]);
        return $result;
    }


    public function delete_page($id){
        if (DB::table('pages_details')->where('id',$id)->delete()) {
            return 1;
        }
        else{
            return 0;
        }
    }

    public function update_page($id,$items){
        $result = DB::table('pages_details')->where('id', $id)->update($items);
        return $result;
    }

    public function getroledetails(){
        $result = DB::select('SELECT a.id, a.role_id, b.role, c.page_name FROM role_settings a INNER JOIN user_roles b ON b.role_id = a.role_id INNER JOIN pages_details c ON c.id = a.page_id');
        return $result;
    }
    public function getroles_name(){
        $result = DB::select('SELECT a.id, a.role_id, b.role FROM role_settings a INNER JOIN user_roles b ON b.role_id = a.role_id INNER JOIN pages_details c ON c.id = a.page_id GROUP BY a.role_id');
        return $result;
    }

    public function exsist_chk_roles($roleid, $pageid)
    {
        $result = DB::select('SELECT COUNT(id) AS counts FROM role_settings WHERE role_id = ? AND page_id = ?',[$roleid, $pageid]);
        return $result;
    }

    public function getbyroleid($roleid)
    {
        $result = DB::select('SELECT a.id, b.page_name FROM role_settings a INNER JOIN pages_details b ON b.id = a.page_id WHERE a.role_id = ?',[$roleid]);
        return $result;
    }

    public function delete_rolepage($id){
        if (DB::table('role_settings')->where('id',$id)->delete()) {
            return 1;
        }
        else{
            return 0;
        }
    }


    public  function  getpages_byroleid($roleid){
        $result = DB::select('SELECT page_id from role_settings WHERE role_id = ? ORDER BY page_id',[$roleid]);
        return $result;
    }




}