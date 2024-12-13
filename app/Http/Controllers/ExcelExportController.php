<?php

namespace App\Http\Controllers;

use App\Vendor;
use App\Customer;
use App\report;
use Illuminate\Http\Request;
use App\Exports\CustomerLedgerExport;
use App\Exports\CustomerExport;
use App\Exports\ReceiptExport;
use App\Exports\ItemSalesDatabaseExport;
use App\Exports\FBRReportExport;
use App\Exports\IsdbDatewiseExport;
use Excel;

class ExcelExportController extends Controller
{
	public function VendorLedgerReportExport(Request $request,Vendor $vendor)
	{
		$vendordata = [];
		$result = $vendor->account_payable($request->vendor,$request->first,$request->second);
		
		foreach ($result as $key => $value) { 
			 $items = [
				"serial" => ++$key,
				"name" => $value->vendor_name,
				"contact" => $value->vendor_contact,
				"company" => $value->company_name,
				"balance" => $value->balance,
			 ]; 
			 array_push($vendordata, $items);
		}
		return  Excel::download(new VendorLedgerExport($vendordata), "Vendor Ledger.xlsx");
	}
	
	public function CustomerLedgerReportExport(Request $request,Customer $customer)
	{
		$customerdata = [];
		$details = $customer->getCustomerReport($request->customer, $request->first, $request->second);
		
		foreach ($details as $key => $value) { 
			 $items = [
				"serial" => ++$key,
				"name" => $value->name,
				"contact" => $value->mobile,
				"balance" => $value->balance,
			 ]; 
			 array_push($customerdata, $items);
		}
		return  Excel::download(new CustomerLedgerExport($customerdata), "Customer Ledger.xlsx");
	}
	
	public function CustomerBalances(Request $request,Customer $customer)
	{
		$balance = [];
		$advance = [];
		$details = $customer->getcustomerBalances();
		
		foreach ($details as $key => $value) { 
			 $items = [
				"Name" => $value->name,
				"Branch" => $value->branch_name,
				"Mobile" => $value->mobile,
				"CNIC" => $value->nic,
				"Address" => $value->address,
				"balance" => $value->balance,
			 ]; 
			 if($value->balance < 0){
				array_push($balance, $items); 
			 }else {
				array_push($advance, $items); 
			 }
		}
	
		return  Excel::download(new CustomerExport($balance,$advance), "Customer Balances.xlsx");
	}
	
	public function ItemSalesDatabaseReportInExcel(Request $request,report $report)
	{
		$isdb = [];
		$details = $report->itemsale_details_excel($request->from,$request->to,$request->terminal);
		foreach ($details as $key => $value) { 
			 $items = [
				"Terminal" => $value->terminal_name,
				"Cost" => $value->cost,
				"Amount" => $value->amount,
				"Qty" => $value->qty,
				"Name" => $value->product_name,
			 ]; 
			 array_push($isdb, $items);
		}
		return  Excel::download(new ItemSalesDatabaseExport($isdb), "Item Sales Database.xlsx");;
	}
	
	public function ItemSalesDatabaseReportDatewiseInExcel(Request $request,report $report)
	{
		
		return  Excel::download(new IsdbDatewiseExport($report), "Item Sales Database.xlsx");;
	}
	
	public function FbrReportExcel(Request $request,report $report)
	{
		$isdb = [];
		$details = $report->sales($request->terminal,$request->from,$request->to);	
		foreach ($details as $key => $value) { 
			 $items = [
				"Sales Id" => $value->id,
				"FBR Inv Number" => $value->fbrInvNumber,
				"Date" => date("d M Y",strtotime($value->date)),
				"Sales" => number_format($value->actual_amount,2),
				"Sales Tax" => number_format($value->sales_tax_amount,2),
				"Total Amount" => number_format($value->total_amount,2),
			 ]; 
			 array_push($isdb, $items);
		}
		return  Excel::download(new FBRReportExport($isdb), "FBR Report.xlsx");;
	}

	public function receiptExport(Request $request) 
    {
        return Excel::download(new ReceiptExport($request->id), 'invoices.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }
}