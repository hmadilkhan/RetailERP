<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\unitofmeasure;


class UnitofmeasureController extends Controller
{
	  public function __construct()
    {
        $this->middleware('auth');
    } 

    public function store(unitofmeasure $uom, Request $request)
    {
    	$exsist = $uom->exsist($request->uom);
    	if ($exsist[0]->counter == 0) {
    		$items = [
    			'name' => $request->uom,
    		];
    		$result = $uom->insert($items);
    		$getuom = $uom->getuom();
    		return $getuom;
    	}
    	else{
    		return 0;	
    	}

    }


}