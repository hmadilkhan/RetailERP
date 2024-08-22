<?php

namespace App\Http\Controllers;

use App\damagedBottles;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class DamagedBottlesController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    } 

    public function view(damagedBottles $damage){
    	 $list = $damage->get_vendor_damaged();
    	return view('Damaged_Bottles.view-vendor-damage', compact('list'));	
    }

    public function viewcustomer(damagedBottles $damage){
    	 $list = $damage->get_customer_damaged();
    	 return view('Damaged_Bottles.view-customerdamage', compact('list'));	
    }


    public function show(damagedBottles $damage){
    	 $vendors = $damage->getvendors();
    	 $product = $damage->getprodutcs();
    	return view('Damaged_Bottles.create', compact('vendors','product'));	
    }

     public function showcustomer(damagedBottles $damage){
    	 $customers = $damage->getcustomers();
    	 $product = $damage->getprodutcs();
    	return view('Damaged_Bottles.create-customerDamaged', compact('customers','product'));	
    }

       public function insert_vendor_damage(damagedBottles $damage, Request $request){

    	  $items=[
                'vendor_id' => $request->vendor,
                'product_id' =>$request->product,
                'qty' => $request->qty,
                'date' => date('Y-m-d'),
           		];
             $vendor = $damage->insert('demaged_bottel_vendor',$items);  
             return $vendor;
    }

     public function insert_customer_damage(damagedBottles $damage, Request $request){

    	  $items=[
                'customer_id' => $request->customer,
                'product_id' =>$request->product,
                'qty' => $request->qty,
                'date' => date('Y-m-d'),
           		];
             $customer = $damage->insert('damaged_bottles_cutomers',$items);  
             return $customer;
    }

}