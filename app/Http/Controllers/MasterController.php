<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\master;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MasterController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(master $master)
    {
        $details = $master->getMasterDetails();
        return view('master.lists', compact('details'));
    }

    public function create(master $master)
    {
    	$country = $master->getcountry();
        $city = $master->getcity();
    	return view('master.create', compact('country','city'));
    }

    public function store(Request $request,master $master)
    {
    	 $imageName= "";

          $rules = [
                'name' => 'required',
                'mobile' => 'required',
                'address'  => 'required',
                'country' => 'required',
                'city'  => 'required',

            ];
          $this->validate($request, $rules);


        if(!empty($request->vdimg)){
              $request->validate([
                  'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1048',
              ]);
              $imageName = $request->name.'.'.$request->vdimg->getClientOriginalExtension();
              $request->vdimg->move(public_path('assets/images/master/'), $imageName);
         }

         $items = [
            'user_id' => session('userid'),
            'status_id' => 1,
            'country_id' => $request->country,
            'city_id' => $request->city,
            'name' => $request->name,
            'mobile'=> $request->mobile,
            'phone'=> $request->phone,
            'email' => $request->email,
            'nic'=> $request->phone,
            'address'=> $request->address,
            'image'=> $imageName,
            'credit_limit' => $request->creditlimit,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            
        ];

        $cust = $master->insertMasters($items);
        return $this->index($master);
    }

    public function remove(Request $request,master $master)
    {
        $result = $master->deleteMaster($request->id);
        return 1;
    }

    public function getMasterByCategory(Request $request,master $master)
    {
        $result = $master->getMasterByCategory($request->id);
        return $result;
    }

    public function edit(Request $request,master $master)
    {
        $details = $master->masters($request->id);
        $country = $master->getcountry();
        $city = $master->getcity();
        return view('master.edit', compact('country','city','details'));
    }

    public function LedgerDetails(Request $request,master $master)
    {
         $details = $master->LedgerDetailsShow($request->id);
         $balance = $master->getLastBalance($request->id);
         $masterID = $request->id;
         return view('master.ledger', compact('details','masterID','balance'));

        // $masters = $master->getMasters();
        // return view('Accounts.master-ledger',compact('masters'));
    }

    public function LedgerPayment(Request $request,master $master)
    {
         $details = $master->LedgerDetails($request->id);
         $balance = $master->getLastBalance($request->id);
         $masterID = $request->id;
        return view('master.paymentledger', compact('details','masterID','balance'));
         
         // return $details;
    }

    public function category(Request $request,master $master)
    {
        $masterID = $request->id;
        return view('master.categories',compact('masterID'));
    }

    public function getcategory(Request $request,master $master)
    {
        $result = $master->category();
        return $result;
    }

    public function insertCategory(Request $request,master $master)
    {
        $items = [
            'name' => $request->catname,
        ];
        $count = $master->checkCategory($request->catname);
        if($count == 0)
        {
            $result = $master->insertCategory($items);
            return $result;
        }
        else
        {
            return 2;
        }
        
    }

    public function getMaster(Request $request,master $master)
    {
      
        $result =  $master->getMaster();
        return $result;
          
    }

    public function MasterRateInsert(Request $request,master $master)
    {
        $count = $master->checkAlreadyCategoryRate($request->categoryid,$request->masterid);
        if($count == 0)
        {
            $items = [
                'finished_good_id' => $request->categoryid,
                'master_id' => $request->masterid,
                'rate' => $request->rate,
            ];
            $result = $master->masterRateInsert($items);
            return $result;
        }
        else
        {
            return 2;
        } 
    }

    public function getRateList(Request $request,master $master)
    {
        $result = $master->getRateList($request->masterid);
        return $result;
    }

    public function MasterRateUpdate(Request $request,master $master)
    {
        
        $items = [
            'finished_good_id' => $request->categoryid,
            'master_id' => $request->masterid,
            'rate' => $request->rate,
        ];
        $result = $master->updateRateList($request->id,$items);
        return $result;
       
    }



    public function update(Request $request,master $master)
    {
        $imageName= "";

          $rules = [
                'name' => 'required',
                'mobile' => 'required',
                'address'  => 'required',
            ];
          $this->validate($request, $rules);

                if(!empty($request->vdimg)){

                    $path = public_path('assets/images/master/').$request->custimage;
                    if(file_exists($path)){
                        @unlink($path);
                    }

                  $request->validate([
                      'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1048',
                  ]);
                  $imageName = $request->name.'.'.$request->vdimg->getClientOriginalExtension();
                  $request->vdimg->move(public_path('assets/images/master/'), $imageName);

                 }else{

                    $imageName = $request->custimage;
                 }

         $items = [
            'user_id' => session('userid'),
            'status_id' => 1,
            'country_id' => $request->country,
            'city_id' => $request->city,
            'name' => $request->name,
            'mobile'=> $request->mobile,
            'phone'=> $request->phone,
            'email' => $request->email,
            'nic'=> $request->phone,
            'address'=> $request->address,
            'image'=> $imageName,
            'credit_limit' => $request->creditlimit,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            
        ];

        $result = $master->update_master($request->masterid,$items);
        return redirect("/get-masters");
    }

    public function debitInsert(Request $request,master $master)
    {
        $lastBalance = $master->getLastBalance($request->master);
        $balance = $request->debit - $lastBalance;

        $items=[
            'master_id' => $request->master,
            'receipt_no' => '0',
            'total_amount' => '0',
            'debit' => $request->debit,
            'credit' => '0',
            'balance' => $balance,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $result = $master->insertDebit($items);
        return redirect("/ledger-details/".$request->master);

    }

    public function ledgerInsert(Request $request,master $master)
    {

        $cust = $master->updateLedger($request->id);
        $bal = $request->totalCredit * (-1);
        $items = [
            'master_id' => $request->master,
            'receipt_no' => $request->receipt,
            'total_amount' => $request->net,
            'debit' => $request->amount,
            'credit' => '0',
            'balance'=> $request->bal,
            'TotalBalance'=> $bal,
            'status_id' => $request->status,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $ledInsert = $master->insertDebit($items);
        return $ledInsert;
    }

    public function workload(Request $request,master $master)
    {
        $masters = $master->mastersworkload();
        $category = $master->mastersCategory();
        $assign = $master->mastersAssignOrders();
        return view('master.workload',compact('masters','category','assign'));
    }

    public function updateMasterAssign(Request $request,master $master)
    {

        $status = 0;
        //GRN HERE
        $grn = $master->getGrn();
        $grn = $grn + 1;
        $gen = [
            'GRN' => "GRN-".$grn,
            'user_id' => session('userid'),
            'created_at' => date('Y-m-d H:s:i'),
            'updated_at' => date('Y-m-d H:s:i'),
        ];
        $gen_res = $master->receiving_general($gen);

        $resultItems = $master->getItemsForStock($request->id);


        foreach ($resultItems as $value) {

            if($value->total_qty == $request->recivedqty)
            {
                $status = 3;
            }
            else
            {
                $status = 2;
            }

            $items = [
                'GRN' => $gen_res,
                'item_id' => $value->finished_good_id,
                'qty_rec' => $request->recivedqty,
            ];

            $stock = [
                'grn_id' => $gen_res,
                'product_id' => $value->finished_good_id,
                'uom' => $value->uom_id,
                'cost_price' => $value->cost,
                'retail_price' => '0',
                'wholesale_price' => '0',
                'discount_price' => '0',
                'qty' => $request->recivedqty,
                'balance' =>$request->recivedqty,
                'status_id' => 1,
                'branch_id' => session('branch'),
                'date' => date('Y-m-d'),

            ];
            
            $rec = $master->receiving_items($items);
            $stockResult = $master->createStock($stock);

            $result = $master->UpdateOrderAssign($request->id,$request->recivedqty,$status);
            $result = $master->UpdateSalesDetailsAssign($status,$request->receipt_id,$request->itemid);

            $compare = $master->compareStatus($request->receipt_id);

            if($compare[0]->count == $compare[0]->received)
            {
                $result = $master->updateSalesReceiptStatus($request->receipt_id);
            }

        }
        return 1;
        
    }

    



    public function workloadDetails(Request $request,master $master)
    {

        $data = $master->workload($request->id);
        $masterID = $request->id;
        return view('master.workloaddetails',compact('data','masterID'));
    }

    public function getReceipt(Request $request,master $master)
    {
        $data = $master->getReceipt($request->receipt);
        return $data;
    }

    public function master_report(Request $request,master $master)
    {
      $master = $master->getMasters();
      return view('reports.master',compact('master'));
    }

    public function master_report_filter(Request $request,master $master)
    {

      $details = $master->masterPayableReport($request->vendor,$request->first,$request->second);
      return $details;
    }

    public function exportPDF(Request $request,master $master)
    {
        $totalBalance = 0;
        $result = $master->masterPayableReport($request->vendor,$request->first,$request->second);
         
        $pdf = app('Fpdf');
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        // $pdf->Cell(40,10,'Hello World!');

        $pdf->Image(public_path('assets/images/company/Sabsons Distribution.jpg'),10,10,-200);
        $pdf->SetFont('Arial','BU',18);
        $pdf->MultiCell(0,10,'ACCOUNT PAYABLE REPORT',0,'C');
        $pdf->Cell(2,2,'',0,1);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,5,'MASTERS',0,1,'C'); //Here is center title
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(10,10,'',0,1);

        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,5,'Tayyeb Jamal',0,1,'L');
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(0,4,'Hamid Hussain Farooqi Rd, P.E.C.H.S Block 2,',0,0,'L');
        $pdf->Cell(0,4,'From : 02-12-2020',0,1,'R');
        // $pdf->Cell(10,10,'',0,1);
        $pdf->Cell(0,5,'Karachi, Karachi City, Sindh',0,0,'L');
        $pdf->Cell(0,5,'To : 02-12-2020',0,1,'R');
        // $pdf->SetFont('Arial','',12);
        // $pdf->Cell(0,8,'Company Address','B',0,'L');
        // $pdf->Cell(0,8,'To : 03-12-2020','B',1,'R');
        $pdf->Cell(0,4,'021-34513353',0,0,'L');
        $pdf->Cell(0,4,'',0,1,'R');

        $pdf->Cell(190,8,'','B',1);

      



        $pdf->SetFont('Arial','B',13);
        $pdf->Cell(20,8,'S.No','B',0,'L');
        $pdf->Cell(90,8,'Name','B',0,'L');
        $pdf->Cell(40,8,'Contact','B',0,'L');
        $pdf->Cell(40,8,'Balance','B',1,'L');



        foreach ($result as $key => $value) {
          $totalBalance = $totalBalance + ($value->balance * (-1));
          $pdf->SetFont('Arial','',12);
          $pdf->Cell(20,8,$key+1,0,0,'L');
          $pdf->Cell(90,8,$value->name,0,0,'L');
          $pdf->Cell(40,8,$value->mobile,0,0,'L');
          $pdf->Cell(40,8,number_format($value->balance * (-1),2) ,0,1,'L');
        }
          $pdf->ln();
          $pdf->SetFont('Arial','B',12);
          $pdf->Cell(150,8,'Total Payable :',0,0,'R');
          $pdf->Cell(40,8,"Rs. ".number_format($totalBalance,2) ,0,1,'R');

        //save file
        $pdf->Output('Master Payable.pdf', 'I');
    }

    public function exportWorkLoadPDF(Request $request,master $master)
    {
        $totalOrders = 0;
        $result = $master->getMasters();
         
        $pdf = app('Fpdf');
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        // $pdf->Cell(40,10,'Hello World!');

        $pdf->Image(public_path('assets/images/company/Sabsons Distribution.jpg'),10,10,-200);
        $pdf->SetFont('Arial','BU',18);
        $pdf->MultiCell(0,10,'MASTER WORKLOAD REPORT',0,'C');
        $pdf->Cell(2,2,'',0,1);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,5,'MASTERS',0,1,'C'); //Here is center title
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(10,10,'',0,1);

        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,5,'Tayyeb Jamal',0,1,'L');
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(0,4,'Hamid Hussain Farooqi Rd, P.E.C.H.S Block 2,',0,0,'L');
        $pdf->Cell(0,4,'From : 02-12-2020',0,1,'R');
        // $pdf->Cell(10,10,'',0,1);
        $pdf->Cell(0,5,'Karachi, Karachi City, Sindh',0,0,'L');
        $pdf->Cell(0,5,'To : 02-12-2020',0,1,'R');
        // $pdf->SetFont('Arial','',12);
        // $pdf->Cell(0,8,'Company Address','B',0,'L');
        // $pdf->Cell(0,8,'To : 03-12-2020','B',1,'R');
        $pdf->Cell(0,4,'021-34513353',0,0,'L');
        $pdf->Cell(0,4,'',0,1,'R');

        $pdf->Cell(190,8,'','B',1);

      



        $pdf->SetFont('Arial','B',13);
        $pdf->Cell(20,8,'S.No','B',0,'L');
        $pdf->Cell(90,8,'Name','B',0,'L');
        $pdf->Cell(40,8,'Contact','B',0,'L');
        $pdf->Cell(40,8,'Order Pending','B',1,'L');



        foreach ($result as $key => $value) {
            $workload = $master->workload($value->id);
          $totalOrders = $totalOrders + sizeof($workload);
          $pdf->SetFont('Arial','',12);
          $pdf->Cell(20,8,$key+1,0,0,'L');
          $pdf->Cell(90,8,$value->name,0,0,'L');
          $pdf->Cell(40,8,$value->mobile,0,0,'L');
          $pdf->Cell(40,8,sizeof($workload) ,0,1,'L');
        }
          $pdf->ln();
          $pdf->SetFont('Arial','B',12);
          $pdf->Cell(150,8,'Total Orders :',0,0,'R');
          $pdf->Cell(40,8,$totalOrders ,0,1,'L');

        //save file
        $pdf->Output('Master WorkLoad.pdf', 'D');
    }
}