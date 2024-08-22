<?php

namespace App\Http\Controllers;

use App\expense;
use App\expense_category;
use App\tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Terbilang;
use App\bank;
use App\pdfClass;



class ExpenseController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cat = expense_category::where('branch_id',session('branch'))->get();
        $tax = tax::all();
        $expense = expense::join('expense_categories', 'expense_categories.exp_cat_id', '=', 'expenses.exp_cat_id')->where('expenses.branch_id',session('branch'))->get();

        return view('Expense.list')->with(['cat'=>$cat,'tax'=>$tax,'expense'=>$expense]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

   
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,bank $bank)
    {
        $expense = new expense([
            'branch_id' => session('branch'),
            'exp_cat_id' => $request->get('exp_cat'),
            'tax_id' => '1',
            'expense_details' => $request->get('details'),
            'tax_amount'=> '0',
            'amount'=> $request->get('amount'),
            'net_amount'=> $request->get('amount'),
            'date'=> $request->get('expensedate'),
        ]);
		
		if ($expense->save()) {
			return response()->json(array("state" => 1, "msg" => 'Expense details is saved.'));
		} else {
            return response()->json(array("state" => 0, "msg" => 'Not saved :('));
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(expense $expense)
    {
         $expense = expense::find($expense->id);
         return $expense;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,expense $expense)
    {


    }



    public function modify(Request $request,bank $bank)
    {
		$expense = DB::table('expenses')->where('exp_id',$request->get('hidd_id'))->update(['branch_id'=>session('branch'),'exp_cat_id'=>$request->get('exp_cat'),'expense_details'=>$request->get('details'),'tax_amount'=>$request->get('hidd_amt'),'amount'=>$request->get('amount'),'net_amount'=>$request->get('amount'),'date'=>$request->get('expensedate')]);
		if($expense){
		 return response()->json(array("state"=>1,"msg"=>'Save Changes.'));
		}else{
		return response()->json(array("state"=>0,"msg"=>'Not saved :('));
		}
    }


    public function deleteExpense(Request $request)
    {
        try{
			expense::where("exp_id",$request->id)->delete();
			return response()->json(["status" => 200]);
		}catch(\Exception $e){
			return response()->json(["status" => 500,"message" => "Error: ".$e->getMessage()]);
		}
    }

    public function getData(Request $req){
          
          $expense = DB::table('expenses')->where('exp_id',$req->id)->get();
          return $expense;
       
    }

    public function getCategories(){
        $categories = expense_category::where('branch_id',session('branch'))->get();
        return $categories;
    }

     public function getTax(){
        $categories =  DB::table('taxes')->get();
        return $categories;
    }


    public function expense_report_panel(Request $request,expense $expense,expense_category $expCat)
    {
      $category = $expCat->get();
      $details = $expense->expense_report($request->category,$request->first,$request->second);
      return view('reports.expense',compact('category'));
    }

    public function expense_report_filter(Request $request,expense $expense)
    {
      $details = $expense->expense_report_filter($request->category,$request->first,$request->second);
      return $details;
    }
	
	public function expenseDetailsFilter(Request $request,expense $expense)
    {
      $details = $expense->expense_report_filter($request->category,$request->first,$request->second);
      return $details;
    }

    public function generatePDF(Request $request,expense $expense)
    {
        $totalBalance = 0;
        $company = $expense->company(session('company_id'));
        $result = $expense->expense_report($request->category,$request->first,$request->second);

        $pdf = new pdfClass();
        $pdf->AliasNbPages();
        $pdf->AddPage();

        if (!file_exists(public_path('assets/images/company/qrcode.png')))
        {
            $qrcodetext = $company[0]->name." | ".$company[0]->ptcl_contact." | ".$company[0]->address;
            \QrCode::size(200)
                ->format('png')
                ->generate($qrcodetext, public_path('assets/images/company/qrcode.png'));
        }

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
        $pdf->Cell(190,10,'Expense Sheet','B,T',1,'L');



        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(10,8,'Sr.','B',0,'L');
        $pdf->Cell(25,8,'Date.','B',0,'L');
        $pdf->Cell(40,8,'Category','B',0,'L');
        $pdf->Cell(25,8,'Amount','B',0,'L');
        $pdf->Cell(90,8,'Details','B',1,'L');

        $count = 0;
        foreach ($result as $key => $value) {
            $count++;
            if ($count % 2 == 0)
            {
                $pdf->SetFont('Arial','',12);
                $pdf->setFillColor(232,232,232);
                $pdf->SetTextColor(0,0,0);
            }
            else{
                $pdf->SetFont('Arial','',12);
                $pdf->setFillColor(255,255,255);
                $pdf->SetTextColor(0,0,0);
            }
          $totalBalance = $totalBalance + $value->balance ;
          $pdf->Cell(10,8,$key+1,0,0,'L',1);
          $pdf->Cell(25,8,$value->date,0,0,'L',1);
          $pdf->Cell(40,8,$value->expense_category,0,0,'L',1);
          $pdf->Cell(25,8,number_format($value->balance,2) ,0,0,'L',1);
          $pdf->Cell(90,8,$value->expense_details,0,1,'L',1);

        }


        $pdf->ln();
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(130,8,'',0,0,'R');
        $pdf->Cell(20,8,'Total:','T,B',0,'R');
        $pdf->Cell(40,8,"Rs. ".number_format($totalBalance,2) ,'T,B',1,'R');

//        // Go to 1.5 cm from bottom
//        $pdf->SetY(-24);
//        // Select Arial italic 8
//        $pdf->SetFont('Arial','I',10);
//        // Print centered page number
//        $pdf->Cell(160,2,'System Generated Report: Sabify',0,0,'L');
//        $pdf->SetFont('Arial','',10);
//        $pdf->Cell(30,2,'Page | '.$pdf->PageNo(),0,0,'R');

        //save file
        $pdf->Output('Expense Sheet.pdf', 'I');
    }


    public function expense_voucher(Request $request,expense $expense){
        $company = $expense->company(session('company_id'));

        //get expense details
        $expenses = $expense->expense_voucher($request->expid);

        $pdf = app('Fpdf');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        if (!file_exists(public_path('assets/images/company/qrcode.png')))
        {
            $qrcodetext = $company[0]->name." | ".$company[0]->ptcl_contact." | ".$company[0]->address;
            \QrCode::size(200)
                ->format('png')
                ->generate($qrcodetext, public_path('assets/images/company/qrcode.png'));
        }

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
        $pdf->Cell(190,10,'Expense Voucher ('.$expenses[0]->date.' )','B,T',1,'L');

        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(20,8,'Sr.','B,R',0,'C');
        $pdf->Cell(140,8,'Expense Discription','B,R',0,'L');
        $pdf->Cell(30,8,'Amount','B',1,'R');



        $total = 0;
        foreach ($expenses as $key => $value) {
            $total = $total + $value->amount;
            $pdf->SetFont('Arial','B',11);
            $pdf->Cell(20,10,$key+1,'R',0,'C');
            $pdf->Cell(140,10,$value->expense_category,'R',0,'L');
            $pdf->Cell(30,10,number_format($value->amount,2) ,0,1,'R');

            $pdf->SetFont('Arial','',11);
            $pdf->Cell(20,1,'','R',0,'C');
            $pdf->Cell(5,1,'','',0,'L');
            $pdf->Cell(135,1,$value->expense_details,'R',0,'L');
            $pdf->Cell(30,1,'',0,1,'R');

            $pdf->SetFont('Arial','',11);
            $pdf->Cell(20,5,'','R',0,'C');
            $pdf->Cell(5,5,'','',0,'L');
            $pdf->Cell(135,5,'','R',0,'L');
            $pdf->Cell(30,5,'',0,1,'R');
        }


        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(160,6,'Total:','T',0,'R');
        $pdf->Cell(30,6,number_format($total,2),'T',1,'R');

        $pdf->Cell(160,6,'Tax Amount:',0,0,'R');
        $pdf->Cell(30,6,number_format($expenses[0]->tax_amount,2),0,1,'R');

        $pdf->Cell(160,6,'Net Amount:','B',0,'R');
        $pdf->Cell(30,6,number_format($expenses[0]->net_amount,2),'B',1,'R');


        //save file
        $pdf->Output('Expense Voucher.pdf', 'I');

    }



    
}
