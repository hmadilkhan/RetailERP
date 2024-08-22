<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class businessPolicies extends Model
{
	 public function insert_tax($items){
        $result = DB::table('taxes')->insert($items);
        return $result;
    }

    public function tax_esists($taxname){
    	$result = DB::select('SELECT COUNT(id) AS counter FROM taxes WHERE name = ?',[$taxname]);
    	return $result;
    }

    public function get_tax_rules(){
    	$result = DB::select('SELECT a.id, a.name, a.value, b.status_name ,a.show_in_purchase,a.show_in_pos FROM taxes a INNER JOIN accessibility_mode b ON b.status_id = a.status_id WHERE a.status_id = 1 AND a.company_id = ?',[session('company_id')]);
    	return $result;
    }

     public function tax_update($id){
       $result = DB::table('taxes')->where('id', $id)->update(['status_id'=>2]);
        return $result;
    }

    public function get_tax_rules_id($id){
    	$result = DB::select('SELECT a.id, a.name, a.value, b.status_name,a.show_in_purchase,a.show_in_pos FROM taxes a INNER JOIN accessibility_mode b ON b.status_id = a.status_id
			WHERE a.status_id = 1 and a.id = ?',[$id]);
    	return $result;
    }

      public function tax_edit($id, $items){
       $result = DB::table('taxes')->where('id', $id)->update($items);
        return $result;
    }


        public function get_tax_slabs($statusid){
        $result = DB::select('SELECT * FROM tax_slabs a INNER JOIN accessibility_mode b ON b.status_id = a.status_id WHERE a.status_id = ?',[$statusid]);
        return $result;
    }

        public function insert($table,$items){
    
        $result = DB::table($table)->insertGetId($items);
       return $result;   
    }

      public function slab_exsists($companyid,$slabmin,$slabmax){
        $result = DB::select('SELECT COUNT(tax_id) AS counts FROM tax_slabs WHERE company_id = ? AND slab_min = ? AND slab_max = ?',[$companyid,$slabmin,$slabmax]);
        return $result;
    }

     public function update_taxslabs($id,$items){
        $result = DB::table('tax_slabs')->where('tax_id', $id)->update($items);
        return $result;
    }



}