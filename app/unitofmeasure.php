<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class unitofmeasure extends Model
{
	public function exsist($uom){
		$result = DB::select('SELECT Count(uom_id) AS counter FROM inventory_uom WHERE name = ?',[$uom]);
		return $result;
	}

	public function insert($items){
		$result = DB::table('inventory_uom')->insert($items);
       return $result;   
	}

	public function getuom(){
		$result = DB::table('inventory_uom')->get();
		return $result;
	}

}