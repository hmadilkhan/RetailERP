<?php

namespace App\Http\Controllers;

use charlieuki\ReceiptPrinter\ReceiptPrinter as ReceiptPrinter;
use Illuminate\Http\Request;
use App\Vendor;
use App\order;
use App\pdfClass;
use App\receiptpdf;
use App\labelPrinter;
use Crabbly\Fpdf\Fpdf;
use Illuminate\Support\Facades\DB;
use PDF;
use App\Customer;

class PrintController extends Controller
{

    public function printBarcode(Request $request)
    {
        return $request->url."?code=".$request->code."&name=".$request->name."&price=".$request->price."&name_margin1=".$request->margin1."&name_margin2=".$request->margin2."&printheader=".$request->printheader;
    }
    //
    public function try(Request $request,Vendor $vendor,order $order){
        $data = array();
        $returnHTML = view('Print.receipt')->with('data', $data)->render();

        // PDF::SetTitle('Hello World');
        // set default header data

        PDF::AddPage('P',array(80,200));
        // PDF::setRTL(true);
        // set font
        // echo public_path('/');exit;
        PDF::SetFont('dejavusans', '', 18);
        PDF::writeHTML($returnHTML, true, false, true, false, '');

        PDF::Output('hello_world.pdf');
    }
    public function index(Request $request,Vendor $vendor,order $order, Customer $customer)
    {
		$request->receipt = str_replace("{{1}}","",$request->receipt);
        $itemQty = 0;
        $tQty = 0;
        // $company = $vendor->company(session('company_id'));
		$general = $order->getReceiptGeneral($request->receipt);
        $company = $vendor->getCompanyByBranch($general[0]->branchId);
        $branch = $vendor->getBranch($general[0]->branchId);
        $details = $order->orderItemsForPrint($general[0]->receiptID);
        // $balance = $order->getCustBalance($general[0]->customerId);
        $balance = $customer->getcustomersForReceipt($general[0]->customerId,$company[0]->company_id,$general[0]->branchId);


        $pdf = new receiptpdf('P','mm',array(80,200));


        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetMargins(1,0,0,1);
        $pdf->SetFont('Arial','B',10);
		$pdf->SetTitle($general[0]->receipt_no);
		
        $pdf->Image(public_path('assets/images/company/'.$company[0]->logo),28,4,-200);
        $pdf->ln(23);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(80,0,$company[0]->name,0,1,'C');
        $pdf->SetFont('Arial','',7);
        $pdf->Multicell(80,7,$branch[0]->branch_address,0,'C',0);
        $pdf->Cell(80,1,$branch[0]->branch_ptcl." | ".$branch[0]->branch_mobile,0,1,'C');
//        $pdf->Cell(50,6,"www.sabsons.com.pk",0,1,'C');
        $pdf->ln(2);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(80,5,"RECEIPT",0,1,'C');
        $pdf->Cell(80,4,$general[0]->receipt_no,0,1,'C');
        $pdf->Cell(80,5,$general[0]->customerName,0,1,'C');
        $pdf->SetTextColor(255,0,0);
        $pdf->Cell(80,4,strtoupper($general[0]->payment_mode. " payment"),0,1,'C');
        $pdf->ln(2);
        $pdf->SetTextColor(0,0,0);

 
//
//        $pdf->SetFont('Arial','B',10);
//        $pdf->Cell(20,5,"Customer",0,0,'L');
//        $pdf->SetFont('Arial','',10);
//        $pdf->Cell(60,5,$general[0]->customerName,0,1,'L');
//
//
//        $pdf->SetFont('Arial','B',10);
//        $pdf->Cell(15,5,"Mobile :",0,0,'L');
//        $pdf->SetFont('Arial','',10);
//        $pdf->Cell(25,5,$general[0]->mobile,0,0,'L');
//        $pdf->SetFont('Arial','B',10);
//        $pdf->Cell(15,5,"Phone :",0,0,'L');
//        $pdf->SetFont('Arial','',10);
//        $pdf->Cell(25,5,$general[0]->phone,0,1,'L');
//
//
//        $pdf->SetFont('Arial','B',10);
//        $pdf->Cell(20,5,"Address :",0,0,'L');
//        $pdf->SetFont('Arial','',10);
//        $pdf->Cell(60,5,$general[0]->address,0,1,'L');

        $pdf->ln(1);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(10,6,"Date  ",'T,B',0,'L');
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(40,6,date("d-m-Y",strtotime($general[0]->date)),'T,B',0,'L');

        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(10,6,"Time  ",'T,B',0,'L');
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(15,6,date("H:i a",strtotime($general[0]->time)),'T,B',1,'L');

        $pdf->ln(2);
        $pdf->SetFont('Arial','B',10);
        $pdf->setFillColor(233,233,233);
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(43,7,'Product',0,0,'L',1);
        $pdf->Cell(12,7,'Price',0,0,'L',1);
        $pdf->Cell(10,7,'Qty',0,0,'C',1);
        $pdf->Cell(10,7,'Price',0,1,'C',1);

        $pdf->SetFont('Arial','',10);
        $pdf->setFillColor(255,255,255);
        $pdf->SetTextColor(0,0,0);
        foreach ($details as $val){
			$pdf->SetFont('Arial','',10);
            $itemQty++;
            $tQty = $tQty + $val->total_qty;

            $pdf->Cell(43,5,$val->product_name,0,0,'L',1);
            $pdf->Cell(12,5,$val->item_price,0,0,'L',1);
            $pdf->Cell(10,5,$val->total_qty,0,0,'C',1);
            $pdf->Cell(10,5,number_format($val->total_amount,0),0,1,'C',1);
			
			if($val->note != "" && $val->note != "None"){
				$pdf->SetFont('Arial','BI',8);
				$pdf->Cell(5,5,"",0,0,'L',1);
				$pdf->Cell(70,5,"Note: ".$val->note,0,1,'L',1);
				$pdf->ln(1);
			}
        }

        $pdf->ln(3);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(20,6,"Item Qty : ",'T,B',0,'L');
        $pdf->Cell(38,6,$itemQty,'T,B',0,'L');
        $pdf->Cell(17,6,$tQty,'T,B',1,'L');

        $pdf->ln(3);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(35,6,"Total Amount",0,0,'L',1);
        $pdf->Cell(10,6,":",0,0,'C',1);
        $pdf->Cell(30,6,number_format($general[0]->actual_amount,0),0,1,'R',1);



        if ($general[0]->discount_amount > 0 ){
            $pdf->Cell(35,6,"Discount Amount",0,0,'L',1);
            $pdf->Cell(10,6,":",0,0,'C',1);
            $pdf->Cell(30,6,number_format($general[0]->discount_amount,0),0,1,'R',1);
        }

        if ($general[0]->credit_card_transaction > 0 ){
            $pdf->Cell(35,6,"Card Charges",0,0,'L',1);
            $pdf->Cell(10,6,":",0,0,'C',1);
            $pdf->Cell(30,6,number_format($general[0]->actual_amount / 100 * $general[0]->value,0),0,1,'R',1);
        }

        if ($general[0]->delivery_charges > 0 ){
            $pdf->Cell(35,6,"Delivery Charges",0,0,'L',1);
            $pdf->Cell(10,6,":",0,0,'C',1);
            $pdf->Cell(30,6,number_format($general[0]->charges,0),0,1,'R',1);
        }

        if ($general[0]->delivery_charges > 0 || $general[0]->credit_card_transaction > 0 || $general[0]->discount_amount > 0 ){
            $pdf->Cell(35,6,"Gross Amount",0,0,'L',1);
            $pdf->Cell(10,6,":",0,0,'C',1);
            $pdf->Cell(30,6,number_format($general[0]->actual_amount - $general[0]->discount_amount + ($general[0]->total_amount / 100 * $general[0]->value)  + $general[0]->charges,0),0,1,'R',1);
        }
		
		if ($general[0]->sales_tax_amount > 0 || $general[0]->srb > 0 ){
            $pdf->Cell(35,6,"Sales Tax (".($general[0]->sales_tax_amount > 0 ?  "FBR" : "SRB").") ",0,0,'L',1);
            $pdf->Cell(10,6,":",0,0,'C',1);
            $pdf->Cell(30,6,number_format( ($general[0]->sales_tax_amount > 0 ?  $general[0]->sales_tax_amount : $general[0]->srb),0),0,1,'R',1);
        }
		
		$pdf->Cell(35,6,"Net Amount",0,0,'L',1);
		$pdf->Cell(10,6,":",0,0,'C',1);
		$pdf->Cell(30,6,number_format( (float)$general[0]->total_amount,0),0,1,'R',1); //- $general[0]->discount_amount  + $general[0]->sales_tax_amount +$general[0]->srb

        if ($general[0]->receive_amount < $general[0]->total_amount ){
            $pdf->Cell(35,6,"Received Amount",0,0,'L',1);
            $pdf->Cell(10,6,":",0,0,'C',1);
            $pdf->Cell(30,6,number_format((float)$general[0]->receive_amount,0),0,1,'R',1);

            $pdf->Cell(35,6,"Receipt Balance",0,0,'L',1);
            $pdf->Cell(10,6,":",0,0,'C',1);
            $pdf->Cell(30,6,number_format($general[0]->total_amount  ,0),0,1,'R',1);//((float)$general[0]->total_amount - $general[0]->discount_amount) - (float)$general[0]->receive_amount + $general[0]->sales_tax_amount +$general[0]->srb

            $pdf->Cell(35,6,"Total Balance",0,0,'L',1);
            $pdf->Cell(10,6,":",0,0,'C',1);
            $pdf->Cell(30,6,number_format((!empty($balance) ? $balance[0]->balance : 0),0),0,1,'R',1);
        }




        $pdf->ln(3);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(75,8,"Timing : 10:30 AM To 6:30 PM",'T,B',1,'C');

        $pdf->ln(2);
        $pdf->Cell(75,5,"Solution By Sabsons|Sabsoft",0,1,'C');
        $pdf->Cell(75,5,"www.sabsoft.com.pk | 9221-34389215-16-17",0,1,'C');



        header('Content-Type: application/pdf; charset=utf-8');
        echo $pdf->Output('I',$general[0]->receipt_no.".pdf",true);
		exit;


        return response($pdf->Output())
            ->header('Content-Type', 'application/pdf');

        //save file
//        $pdf->Output('Receipt.pdf', 'D');



    }

    public function labelPrintingsixtyforty(Request $request,Vendor $vendor,order $order)
    {

        $company = $vendor->company(session('company_id'));
		
        $pdf = new labelPrinter();//'L','mm',array(38,28)
        $pdf->AliasNbPages();
        $pdf->AddPage('L',array(60,40));
//        $pdf->SetMargins(0,0,0,0);

         $pdf->SetFont('Arial','B',11);
		 if($request->printheader == "branch"){
			$branch = DB::table("branch")->where("branch_id",session("branch"))->get("branch_name");
			$pdf->cell(40,-9,strtoupper($branch[0]->branch_name),0,1,"C");
		 }else{
			$pdf->cell(40,-9,strtoupper($company[0]->name),0,1,"C");
		 }
         $pdf->cell(40,18,strtoupper($request->name),0,1,"C");
//         $pdf->text(12,12,strtoupper($company[0]->name));

        //A set
        $code='ABCDEFG123456';
        $pdf->Code128(7,13,$request->code,45,12);
        $pdf->SetXY(5,5);
//        $pdf->cell(60,10,number_format($request->price,0));
        $pdf->text(15,32,"PRICE : ".number_format($request->price)." RS");


        //A,C,B sets
//        $code='ABCDEFG1234567890AbCdEf';
//        $pdf->Code128(7,15,$code,45,12);
//        $pdf->SetXY(10,5);
//        $pdf->text(7,32,"HEAD & SHOULDER");


          //save file
        return response($pdf->Output())
            ->header('Content-Type', 'application/pdf');




    }

    //SINGLE PATTERN 19 h x 28 w
    public function labelPrinting1928(Request $request,Vendor $vendor,order $order)
    {
        $name_margin = $request->name_margin;
        $company = $vendor->company(session('company_id'));

        $pdf = new labelPrinter();
        $pdf->AliasNbPages();
        $pdf->AddPage('L',array(28,19));
//        $pdf->SetMargins(0,0,0,0);

        $pdf->SetFont('Arial','B',6);
		if($request->printheader == "branch"){
			$branch = DB::table("branch")->where("branch_id",session("branch"))->get("branch_name");
			$pdf->cell(8,-15,strtoupper($branch[0]->branch_name),0,1,"C");
		}else{
			$pdf->cell(8,-15,strtoupper($company[0]->name),0,1,"C");
		}
        $pdf->cell(8,-10,strtoupper($request->name),0,1,"C");
        $pdf->text($name_margin,6,strtoupper($request->name));

        //A set
        $code='ABCDEFG123456';
        $pdf->Code128(1,7,$request->code,27,5);
        $pdf->SetXY(5,5);
        $pdf->text(7,15,"PRICE : ".number_format($request->price)." Rs");


        //save file
        return response($pdf->Output())
            ->header('Content-Type', 'application/pdf');



    }

    //DOUBLE PATTERN 19 h x 28 w
    public function labeldoublePrinting1928(Request $request,Vendor $vendor,order $order)
    {
        $name_margin = $request->name_margin;
        $company = $vendor->company(session('company_id'));

        $pdf = new labelPrinter();
        $pdf->AliasNbPages();
        $pdf->AddPage('L',array(63,20));
//        $pdf->SetMargins(0,0,0,0);

        $pdf->SetFont('Arial','B',6);
		if($request->printheader == "branch"){
			$branch = DB::table("branch")->where("branch_id",session("branch"))->get("branch_name");
			$pdf->cell(8,-15,strtoupper($branch[0]->branch_name),0,0,"C");$pdf->cell(55,-15,strtoupper($branch[0]->branch_name),0,1,"C"); 
		 }else{
			$pdf->cell(8,-15,strtoupper($company[0]->name),0,0,"C");$pdf->cell(55,-15,strtoupper($company[0]->name),0,1,"C"); 
		 }
        
        $pdf->cell(8,-10,strtoupper($request->name),0,1,"C");
        $pdf->text(2,6,strtoupper($request->name));$pdf->text(33,6,strtoupper($request->name));

        //A set
        $code='ABCDEFG123456';
        $pdf->Code128(1,7,$request->code,27,5);$pdf->Code128(32.5,7,$request->code,27,5);
        $pdf->SetXY(5,5);
        $pdf->text(7,15,"PRICE : ".number_format($request->price)." Rs");$pdf->text(38,15,"PRICE : ".number_format($request->price)." Rs");


        //save file
        return response($pdf->Output())
            ->header('Content-Type', 'application/pdf');



    }

    //TRIPPLE PATTERN 19 h x 28 w
    public function labeltripplePrinting1928(Request $request,Vendor $vendor,order $order)
    {
        $name_margin1 = $request->name_margin1;
        $name_margin2 = $request->name_margin2;
        $name_margin3 = $request->name_margin3;

        $company = $vendor->company(session('company_id'));

        $pdf = new labelPrinter();
        $pdf->AliasNbPages();
        $pdf->AddPage('L',array(90,19));
//        $pdf->SetMargins(0,0,0,0);

        $pdf->SetFont('Arial','B',6);//8,55,8
//        $pdf->cell(8,-15,strtoupper($company[0]->name),0,0,"C");$pdf->cell(55,-15,strtoupper($company[0]->name),0,0,"C");$pdf->cell(8,-15,strtoupper($company[0]->name),0,1,"C");
//        $pdf->cell(8,-10,strtoupper($request->name),0,1,"C");
        $pdf->text($name_margin1,3,strtoupper($request->name)); $pdf->text($name_margin2,3,strtoupper($request->name));$pdf->text($name_margin3,3,strtoupper($request->name));//2,33.5,63

        //A set
        $code='ABCDEFG123456';
        $pdf->Code128(3,4,$request->code,24,10);$pdf->Code128(33.5,4,$request->code,24,10);$pdf->Code128(63.5,4,$request->code,24,10);
        $pdf->SetXY(5,5);
        $pdf->text(7,17,"PRICE : ".number_format($request->price)." ");$pdf->text(38.5,17,"PRICE : ".number_format($request->price)." ");$pdf->text(69.5,17,"PRICE : ".number_format($request->price)." ");


        //save file
        return response($pdf->Output())
            ->header('Content-Type', 'application/pdf');

    }

    //DOUBLE PATTERN 20 h x 38 w
    public function labelPrinting3828(Request $request,Vendor $vendor,order $order)
    {
        $name_margin = $request->name_margin;
        $company = $vendor->company(session('company_id'));

        $pdf = new labelPrinter();
        $pdf->AliasNbPages();
        $pdf->AddPage('L',array(38,28));
//        $pdf->SetMargins(0,0,0,0);

        $pdf->SetFont('Arial','B',10);
		if($request->printheader == "branch"){
			$branch = DB::table("branch")->where("branch_id",session("branch"))->get("branch_name");
			$pdf->cell(20,-14,strtoupper($branch[0]->branch_name),0,1,"C");
		}else{
			$pdf->cell(20,-14,strtoupper($company[0]->name),0,1,"C");
		}
        
        $pdf->cell(20,-10,strtoupper("Classic Flavor"),0,1,"C");
        $pdf->text($name_margin,8,strtoupper($request->name));

        //A set
        $code='ABCDEFG123456';
        $pdf->Code128(1,10,$code,35,8);
        $pdf->SetXY(5,5);
        $pdf->text(7,22,"PRICE : ".number_format($request->price)." Rs");


        //save file
        return response($pdf->Output())
            ->header('Content-Type', 'application/pdf');



    }

    //SINGLE PATTERN 20 h x 40 w
    public function labelPrinting4020(Request $request,Vendor $vendor,order $order)
    {
        $name_margin = $request->name_margin;
        $company = $vendor->company(session('company_id'));

        $pdf = new labelPrinter();
        $pdf->AliasNbPages();
        $pdf->AddPage('L',array(40,20));
//        $pdf->SetMargins(0,0,0,0);

        $pdf->SetFont('Arial','B',9);
		if($request->printheader == "branch"){
			$branch = DB::table("branch")->where("branch_id",session("branch"))->get("branch_name");
			$pdf->cell(20,-13,strtoupper($branch[0]->branch_name),0,1,"C");
		}else{
			$pdf->cell(20,-13,strtoupper($company[0]->name),0,1,"C");
		}
        
        $pdf->cell(16,-10,strtoupper("Classic Flavor"),0,1,"C");
        $pdf->text($name_margin,8,strtoupper($request->name));

        //A set
        $code='ABCDEFG123456';
        $pdf->Code128(1,9,$code,38,5);
        $pdf->SetXY(5,5);
        $pdf->text(8,18,"PRICE : ".number_format($request->price)." Rs");


        //save file
        return response($pdf->Output())
            ->header('Content-Type', 'application/pdf');



    }

    //DOUBLE PATTERN 20 h x 40 w
    public function labelPrintingdouble4020(Request $request,Vendor $vendor,order $order)
    {
        $name_margin1 = ($request->name_margin1 == 0 ? 1 : $request->name_margin1);
        $name_margin2 = ($request->name_margin2 == 0 ? 46 : $request->name_margin2);
        $company = $vendor->company(session('company_id'));

        $pdf = new labelPrinter();
        $pdf->AliasNbPages();
        $pdf->AddPage('L',array(86,23));
//        $pdf->SetMargins(0,0,0,0);

        $pdf->SetFont('Arial','B',9);
		if($request->printheader == "branch"){
			$branch = DB::table("branch")->where("branch_id",session("branch"))->get("branch_name");
			$pdf->cell(20,-13,strtoupper($branch[0]->branch_name),0,0,"C");$pdf->cell(71,-13,strtoupper($branch[0]->branch_name),0,1,"C");
		}else{
			$pdf->cell(20,-13,strtoupper($company[0]->name),0,0,"C");$pdf->cell(71,-13,strtoupper($company[0]->name),0,1,"C");
		}
        $pdf->cell(16,-10,strtoupper("Classic Flavor"),0,0,"C");$pdf->cell(16,-10,strtoupper("Classic Flavor"),0,1,"C");
        $pdf->text($name_margin1,8,strtoupper($request->name));$pdf->text($name_margin2,8,strtoupper($request->name));

        //A set
        $code='ABCDEFG123456';
        $pdf->Code128(1,9,$request->code,38,8);$pdf->Code128(47,9,$request->code,38,8);
        $pdf->SetXY(5,5);
        $pdf->text(7,21,"PRICE : ".number_format($request->price)." Rs");$pdf->text(55,21,"PRICE : ".number_format($request->price)." Rs");


        //save file
        return response($pdf->Output())
            ->header('Content-Type', 'application/pdf');



    }
}

//REFERENCE LINK FOR BARCODES : http://www.fpdf.org/en/script/script88.php