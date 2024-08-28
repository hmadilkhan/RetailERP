<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class terminal extends Model
{
    public function getbranches()
    {
    	$branch = DB::table('branch')->where('company_id',session('company_id'))->get();
    	return $branch;
    }

    	public function insert($table,$items){
	
        $result = DB::table($table)->insertGetId($items);
       return $result;   
    }

      public function exsist_chk($terminalname,$mac, $branch) 
    { 
    	$result = DB::select('SELECT COUNT(terminal_id) as counts FROM terminal_details WHERE terminal_name = ? or mac_address = ?  AND branch_id = ?',[$terminalname,$mac, $branch]);
		return $result;
    }

        public function getterminals($branch)
    {
       $result = DB::select('SELECT * FROM terminal_details a INNER JOIN branch b ON b.branch_id = a.branch_id INNER JOIN accessibility_mode c ON c.status_id = a.status_id WHERE a.status_id = 1  and a.branch_id = ? order by a.terminal_id DESC',[$branch]); 
		return $result;
    }

       	public function update_terminal($id,$items){
		$result = DB::table('terminal_details')->where('terminal_id', $id)->update($items);
    	return $result;
	}


        public function getterminals_inactive($branch)
    {
       $result = DB::select('SELECT * FROM terminal_details a INNER JOIN branch b ON b.branch_id = a.branch_id INNER JOIN accessibility_mode c ON c.status_id = a.status_id WHERE a.status_id = 2 and a.branch_id = ?',[$branch]);
		return $result;
    }

    public function printingDetails($id)
    {
        $result = DB::select('Select * from terminal_print_details a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id where a.terminal_id = ?',[$id]);
        return $result;
    }

    public function storePrintDetails()
    {

    }

    public function getBindTerminals($id)
    {
        return DB::select("SELECT a.id,b.terminal_name FROM terminal_bind a INNER JOIN terminal_details b on b.terminal_id = a.bind_terminal_id where a.terminal_id = ?",[$id]);
    }

    public function saveBindTerminal($terminalId,$bindTerminalId)
    {
      return DB::table("terminal_bind")->insert(["terminal_id" => $terminalId,"bind_terminal_id" => $bindTerminalId]);
    }

    public function deleteBindTerminal($id)
    {
      return DB::table("terminal_bind")->where("id",$id)->delete();
    }

    public function chkAlreadyExistsBindTerminal($id)
    {
      return DB::table("terminal_bind")->where("bind_terminal_id",$id)->count();
    }

    public function getTerminalName($terminal_id)
    {
      return DB::table("terminal_details")->where("terminal_id",$terminal_id)->get("terminal_name");
    }

}