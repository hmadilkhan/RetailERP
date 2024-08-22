<?php

namespace App\Http\Controllers;

use App\emptybottles;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class emptyController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    } 

	public function index(emptybottles $empty)
	{
		$list = $empty->getList();
		return view('Empty.list',compact('list'));
	}

	public function create(emptybottles $empty)
	{
		$products = $empty->getProducts();
		$vendor = $empty->getVendors();
		return view('Empty.vendorlist',compact('products','vendor'));
	}

	public function getProducts(Request $request,emptybottles $empty)
	{
		$result = $empty->getListByProduct($request->product,$request->vendor);
		return $result;
	}

	public function insert(Request $request,emptybottles $empty)
	{
		$debit = 0;
		$credit = 0;
		$balance = 0;
		$last_balance = $empty->getLastBalance($request->vendor,$request->product);

		if(empty($last_balance[0]->balance))
		{
			$balance = 0;
		}
		else
		{
			$balance = $last_balance[0]->balance;
		}
		if($request->mode == 1)
		{
			$debit = $request->debit;
			$balance = $request->debit + $balance;
		}
		else
		{
			$credit = $request->debit;
			$balance = $balance - $request->debit;
		}
		

		$items = [
			'vendor_id' => $request->vendor,
			'product_id' => $request->product,
			'In_bottles' => $debit,
			'Out_bottles' => $credit,
			'Balance' =>  $balance,
		];
		$result = $empty->insertEmpty($items);
		return  $result;
	}


	public function customerIndex(emptybottles $empty)
	{
		$list = $empty->getCustomerList();
		return view('Empty.customerlist',compact('list'));
	}

	public function customerCreate(emptybottles $empty)
	{
		$products = $empty->getProducts();
		$customer = $empty->getCustomers();
		return view('Empty.customercreate',compact('products','customer'));
	}

	public function addCustomerBottles(Request $request,emptybottles $empty)
	{
		$debit = 0;
		$credit = 0;
		$balance = 0;
		$last_balance = $empty->getLastCustomerBalance($request->customer,$request->product);

		if(empty($last_balance[0]->balance))
		{
			$balance = 0;
		}
		else
		{
			$balance = $last_balance[0]->balance;
		}
		if($request->mode == 1)
		{
			$debit = $request->debit;
			$balance = $request->debit + $balance;
		}
		else
		{
			$credit = $request->debit;
			$balance = $balance - $request->debit;
		}
		

		$items = [
			'customer_id' => $request->customer,
			'product_id' => $request->product,
			'In_bottles' => $debit,
			'Out_bottles' => $credit,
			'Balance' =>  $balance,
		];
		$result = $empty->insertCustomersEmpty($items);
		return  $result;
	}

	public function getCustomerWiseProducts(Request $request,emptybottles $empty)
	{
		$result = $empty->getListByCustomerProduct($request->product,$request->customer);
		return $result;
	}
}