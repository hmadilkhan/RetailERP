<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\demand;
use App\pdfClass;
use App\Vendor;
use Illuminate\Support\Facades\Crypt;
use Crabbly\Fpdf\Fpdf;


class DemandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(demand $demand)
    {
        // $customer = Customer::all();
        $demands = $demand->get_demand();
        return view('Demand.view-demand', compact('demands'));

    }

     public function add_demand(demand $demand)
    {
        $count = $demand->get_count_one();
        $count = $count + 1;

        $items=[
            'demand_id' => $count,
            'user_id' => session('userid'),
            'demand_status_id' => 1,
            'date' => date('Y-m-d'),
            'time' => date('H:s:i'),
            'comment' => 'NULL',
            'branch_id' => session('branch'),

        ];
        $adddemand = $demand->insert($items);
        $products = $demand->getproducts();
        $sender = $demand->get_sender_information();
        $reciver = $demand->get_reciver_info();
        $neartofinish = $demand->get_neartofinish_products();
        $demandinfo = $demand->get_demand_info();

        

        // $customer = Customer::all();
        return view('Demand.create-demand',compact('products','sender','reciver','neartofinish','demandinfo'));
    }


    public function insert_item_details(Request $request, demand $demand){

    

       $check = $demand->checkitem($request->demandid, $request->productid);
         if($check == 0){

                $items=[
                    'demand_id' => $request->demandid,
                    'product_id' => $request->productid,
                    'qty' => $request->qty,
                    'status_id' => 2
                ];
                    $count = $demand->get_count($request->demandid);   

                if($count <= 11){
                     $itemsdetails = $demand->insert_itemdetails($items);
                     return response()->json(array("r"=>0,"msg"=>''));
                 }else {
                     return response()->json(array("r"=>1,"msg"=>'Limit Excceed!!','c'=>1));
                 }
         }else {
             return response()->json(array("r"=>1,"msg"=>'Item is already in Demand List!'));
               
         }

      }

    public function get_demandlist(Request $request, demand $demand){

          $demand_list = $demand->get_demand_list($request->demandid);
         return $demand_list;

    }

    public function update_qty(Request $request, demand $demand){

          $qty = $demand->update_qty($request->qty, $request->productid);

         return $qty;

    }

      public function del_item(Request $request, demand $demand){

         $count = $demand->get_count($request->demandid);   
        if ($count > 1) {
           $del = $demand->del_item($request->productid,false,0);

         return  response()->json(array('r'=>$del,'c'=>0));  
        }else {
             $del = $demand->del_item($request->productid,true,$request->demandid); 
         return response()->json(array('r'=>$del,'c'=>1));  
        }

    }

     public function update_status(Request $request, demand $demand){

            $result = $demand->update_demand_status($request->demandid, $request->statusid);
            return $result;
    }

    public function all_demand_state_up(Request $request, demand $demand){

        $result = $demand->update_alldemand_status($request->demandid, $request->statusid);

           if($result){
              return 1;
           }else {
              return 0;
           }
    }


    public function show(Request $request, demand $demand){
      
      $details = $demand->demand_details_show(Crypt::decrypt($request->id));
       $sender = $demand->get_sender_info(Crypt::decrypt($request->id));
        $reciver = $demand->get_reciver_info();
        $purchaseid = $demand->get_purchase_id(Crypt::decrypt($request->id));

        return view('Demand.demand-details',compact('details','sender','reciver','purchaseid'));

    }

    public function edit(Request $request, demand $demand){
         $sender = $demand->get_sender_info(Crypt::decrypt($request->id));
         $reciver = $demand->get_reciver_info();
         $details = $demand->demand_details_show(Crypt::decrypt($request->id));
         $neartofinish = $demand->get_neartofinish_products();
         $products = $demand->getproducts();

         return view('Demand.edit-demand',compact('sender','reciver','details','neartofinish','products'));
         

    }


    //demand report
    public function demandorderReport(Request $request,Vendor $vendor, demand $demand)
    {


        $company = $vendor->company(session('company_id'));

        //queries
        $details = $demand->demand_details_show($request->id);
        $sender = $demand->get_sender_info($request->id);
        $reciver = $demand->get_reciver_info();
        $purchaseid = $demand->get_purchase_id($request->id);


        if (!file_exists(public_path('assets/images/company/qrcode.png')))
        {
            $qrcodetext = $company[0]->name." | ".$company[0]->ptcl_contact." | ".$company[0]->address;
            \QrCode::size(200)
                ->format('png')
                ->generate($qrcodetext, public_path('assets/images/company/qrcode.png'));
        }

        $pdf = new pdfClass();

        $pdf->AliasNbPages();
        $pdf->AddPage();

        //first row
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(35,0,'',0,0);
        $pdf->Cell(105,0,"Company Name:",0,0,'L');
        $pdf->Cell(50,0,"",0,1,'L');

        //second row
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(35,0,'',0,0);
        $pdf->Image(public_path('assets/images/company/'.$company[0]->logo),12,10,-200);
        $pdf->Cell(105,12,$company[0]->name,0,0,'L');
        $pdf->Cell(50,0,"",0,1,'R');
        $pdf->Image(public_path('assets/images/company/qrcode.png'),175,10,-200);

        //third row
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(35,25,'',0,0);
        $pdf->Cell(105,25,"Contact Number:",0,0,'L');
        $pdf->Cell(50,25,"",0,1,'L');

        //forth row
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(35,-15,'',0,0);
        $pdf->Cell(105,-15,$company[0]->ptcl_contact,0,0,'L');
        $pdf->Cell(50,-15,"",0,1,'L');

        //fifth row
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(35,28,'',0,0);
        $pdf->Cell(105,28,"Company Address:",0,0,'L');
        $pdf->Cell(50,28,"",0,1,'L');

        //sixth row
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(35,-18,'',0,0);
        $pdf->Cell(105,-18,$company[0]->address,0,0,'L');
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(50,-18,"Generate Date:  ".date('Y-m-d'),0,1,'R');

        //report name
        $pdf->ln(15);
        $pdf->SetFont('Arial','B',18);
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(190,10,'Demand Order','B,T',1,'L');
        $pdf->ln(1);

        //details start here
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(70,6,'FROM :',0,0);
        $pdf->Cell(60,6,'TO:',0,0);
        $pdf->Cell(40,6,'DEMAND NUMBER | ',0,0);
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(20,6,$details[0]->demand_id,0,1,'L');

        $pdf->SetFont('Arial','',11);
        $pdf->Cell(70,6,'BRANCH MANAGER:',0,0);
        $pdf->Cell(60,6,'ADMINISTRATOR:',0,0);
        $pdf->Cell(30,6,'CREATED ON: ',0,0);
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(30,6,date('d-m-Y', strtotime($details[0]->date)),0,1,'L');

        $pdf->SetFont('Arial','',11);
        $pdf->Cell(70,6,$sender[0]->branch_name,0,0);
        $pdf->Cell(60,6,$reciver[0]->branch_name,0,0);
        $pdf->Cell(30,6,'STATUS: ',0,0);
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(30,6,$details[0]->status1,0,1,'L');

        $pdf->SetFont('Arial','',9);
        $pdf->Cell(70,4,$sender[0]->branch_address,0,0);
        $pdf->Cell(50,4,$reciver[0]->branch_address,0,0);
        $pdf->Cell(40,4,'',0,0);
        $pdf->Cell(30,4,'',0,1,'L');

        $pdf->ln(2);


        $pdf->SetFont('Arial','B',10);
        $pdf->setFillColor(0,0,0);
        $pdf->SetTextColor(255,255,255);
        $pdf->Cell(70,7,'Poduct Name','B',0,'L',1);
        $pdf->Cell(25,7,'Demand','B',0,'L',1);
        $pdf->Cell(25,7,'Transfer','B',0,'L',1);
        $pdf->Cell(25,7,'Purchase','B',0,'L',1);
        $pdf->Cell(25,7,'Balance','B',0,'L',1);
        $pdf->Cell(20,7,'Status','B',1,'L',1);



        $pdf->SetFont('Arial','',10);
        $pdf->setFillColor(255,255,255);
        $pdf->SetTextColor(0,0,0);
        foreach ($details as $value)
        {
            $pdf->Cell(70,7,$value->product_name,0,0,'L',1);
            $pdf->Cell(25,7,number_format($value->qty,2),0,0,'L',1);
            $pdf->Cell(25,7,number_format($value->transfer_qty,2),0,0,'L',1);
            $pdf->Cell(25,7,number_format($value->purchase_qty,2),0,0,'L',1);
            $pdf->Cell(25,7,number_format($value->qty - $value->transfer_qty - $value->purchase_qty,2),0,0,'L',1);
            $pdf->Cell(20,7,$value->name,0,1,'L',1);
        }


        //save file
        $pdf->Output('Demand Order'.$details[0]->demand_id.'.pdf', 'I');


    }


 
}
