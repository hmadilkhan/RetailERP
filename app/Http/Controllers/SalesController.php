<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Sales;
use App\purchase;


class SalesController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    } 

    public function index()
    {
    	return view('Sales.sales');
    }

    public function view(Request $request,purchase $purchase)
    {
        $receipt = DB::table('sales_receipts')
                    ->join('sales_order_status','sales_order_status.order_status_id','=','sales_receipts.status')
                    ->where('id',$request->id)->get();
        $receiptDetails = DB::table('sales_receipt_details')
                            ->join('inventory_general','inventory_general.id','=','sales_receipt_details.item_code')
                            ->where('receipt_id',$request->id)->get();
        $receiptAccount = DB::table('sales_account_general')->where('receipt_id',$request->id)->get();
        $receiptAccountSubDetails = DB::table('sales_account_subdetails')->where('receipt_id',$request->id)->get();
        $customers = DB::table('customers')->where('id',$receipt[0]->customer_id)->get();
        $company = $purchase->companyDetails();
        return view('Sales.view',compact('company','receipt','receiptDetails','receiptAccount','receiptAccountSubDetails','customers'));
    }

    public function SalesReturn(Request $request,purchase $purchase)
    {
        $receipt = DB::table('sales_receipts')
            ->join('sales_order_status','sales_order_status.order_status_id','=','sales_receipts.status')
            ->where('id',$request->id)->get();
        $returnDetails = DB::table('sales_return')
            ->join('inventory_general','inventory_general.id','=','sales_return.item_id')
            ->where('receipt_id',$request->id)->get();
        $receiptAccount = DB::table('sales_return')->where('receipt_id',$request->id)->SUM('amount');
        $customers = DB::table('customers')->where('id',$receipt[0]->customer_id)->get();
        $company = $purchase->companyDetails();

        return view('Sales.Sales-Return',compact('company','receipt','returnDetails','receiptAccount','receiptAccountSubDetails','customers'));
    }


}