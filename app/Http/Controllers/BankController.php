<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\bank;
use App\Vendor;
use App\pdfClass;
use App\Traits\MediaTrait;
use Image;



class BankController extends Controller
{
    use MediaTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(bank $bank)
    {
        $getaccounts = $bank->get_accounts();
        $website = DB::table('website_details')
                      ->where('company_id',session('company_id'))
                      ->where('status',1)
                      ->get();
        return view('Accounts.view-accounts', compact('getaccounts','website'));
    }

    public function showBanks(bank $bank)
    {
        $banks = $bank->get_banks();
        $website = DB::table('website_details')
                      ->where('company_id',session('company_id'))
                      ->where('status',1)
                      ->get();
        return view('Accounts.view-accounts', compact('getaccounts','website'));
    }

    public function index(bank $bank)
    {
        $getbank = $bank->get_banks();
        $getbranches = $bank->get_branches();

        $website = DB::table('website_details')
                      ->where('company_id',session('company_id'))
                      ->where('status',1)
                      ->get();
        return view('Accounts.bankaccounts-details', compact('getbank', 'getbranches','website'));
    }

    public function link_website(Request $request){
      try{
       if($request->bank == 0 && $request->website == 0){
          Session::flash('error','Invalid parameter');
          return redirect()->url('view-accounts');
       }
       $bankId    = Crypt::decrypt($request->bank);
       $websiteId = Crypt::decrypt($request->website);


       DB::table('website_banks')
            ->insert([
                       'bank_id'    => $bankId,
                       'website_id' => $websiteId,
                       'created_at' => date("Y-m-d H:i:s")
            ]);

            return response()->json('success',200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function unlink_website(Request $request){
       try{
            if($request->uniqueId == 0){
               Session::flash('error','Invalid parameter');
               return redirect()->url('view-accounts');
            }
            $bankId    = Crypt::decrypt($request->bank);
            $websiteId = Crypt::decrypt($request->website);
            $uniqueId  = (int) Crypt::decrypt($request->uniqueId);

            DB::table('website_banks')
                 ->update([
                         'status' => 0,
                         'updated_at' => date('Y-m-d H:i:s')
                  ])
                  ->where('id',$uniqueId);

                 return response()->json('success',200);
          }catch(\Exception $e){
             return response()->json($e->getMessage(),500);
          }
     }

    public function cash_ledger(bank $bank, Request $request)
    {
        $cashLedger = $bank->getCashDetails();
        return view('Accounts.cash-ledger', compact('cashLedger'));
    }

    public function insert_cashLedger(bank $bank, Request $request)
    {
        $balanceStock = $bank->getLastCashBalance();

        $balance = 0;
        if (sizeof($balanceStock) > 0) {
            if ($request->debit > 0) {
                if ($balanceStock[0]->balance > 0) {
                    if ($request->debit <= $balanceStock[0]->balance) {
                        $balance = $balanceStock[0]->balance -  $request->debit;
                    } else {
                        return 2;
                    }
                } else {
                    return 2;
                }
            } else {
                $balance = $balanceStock[0]->balance +  $request->credit;
            }
        } else {
            if ($request->credit > 0) {
                $balance = $request->credit;
            } else {
                return 2;
            }
        }
        $items = [
            'branch_id' => session('branch'),
            'date' => $request->date,
            'debit' => $request->debit,
            'credit' => $request->credit,
            'balance' => $balance,
            'narration' => $request->narration,
        ];
        $result = $bank->insert_bankdetails('cash_ledger', $items);
        return 1;
    }

    public function insert_account(Request $request, bank $bank)
    {
        $imageName = "";
        $exsists = $bank->exsists_account($request->accnumber);

        if ($exsists[0]->counter == 0) {
            if (!empty($request->image)) {
                // $imageName = time() . "." . $request->image->getClientOriginalExtension();
                // $img = Image::make($request->image)->resize(250, 250);
                // $img->save(public_path('assets/images/bank-account/' . $imageName), 75);
                $result = $this->uploads($request->image, "images/bank-account/", "", ["width" => 250, "height" => 250]);
            }
            $items = [
                'account_title' => $request->accountitle,
                'account_no' => $request->accountno,
                'bank_id' => $request->bank,
                'branch_id' => $request->branch,
                'account_type' => $request->accounttype,
                'date' => date('Y-m-d H:i:s'),
                'user_id' => session('userid'),
                'branch_id_company' => session('branch'),
                'image' => (!empty($result) ? $result["fileName"] : ""),
            ];
            $acc = $bank->insert_bankdetails('bank_account_generaldetails', $items);

            if($acc != 0 && isset($request->website)){
                  DB::table('website_banks')
                      ->insert(
                         [
                                   'website_id' => $request->website,
                                   'bank_id'    => $acc,
                                   'created_at' => date('Y-m-d H:i:s')
                                 ]);
            }

            return redirect("view-accounts");
        } else {
            return 0;
        }
    }

    public function show_deposit(bank $bank, Request $request)
    {

        $ledger = $bank->getLedger(Crypt::decrypt($request->id));

        $accountID = Crypt::decrypt($request->id);
        $getaccounts = $bank->getaccountdetails_byid(Crypt::decrypt($request->id));
        return view('Accounts.create-deposit', compact('getaccounts', 'ledger', 'accountID'));
    }

    public function insert_deposit(bank $bank, Request $request)
    {

        $exist = $bank->cheque_exsits($request->cheque_number);
        if ($exist[0]->counts == 0) {
            $oldbalance = $bank->getLastBalance($request->accountid);
            $balance = 0;
            if (sizeof($oldbalance) > 0) {
                if ($request->debit > 0) {
                    if ($oldbalance[0]->balance <= $request->debit) {
                        return 2;
                    } else {
                        $balance = $oldbalance[0]->balance -  $request->debit;
                    }
                } else {
                    $balance = $oldbalance[0]->balance +  $request->credit;
                }
            } else {
                if ($request->debit > 0) {
                    return 2;
                } else {
                    $balance = $request->credit;
                }
            }
            $items = [
                'bank_account_id' => $request->accountid,
                'cheque_number' => $request->cheque_number,
                'cheque_date' => $request->cheque_date,
                'debit' => $request->debit,
                'credit' => $request->credit,
                'balance' => $balance,
                'narration' => $request->narration,
                'mode' => $request->mode,
            ];
            $result = $bank->insert_bankdetails('bank_deposit_details', $items);
            if ($request->mode == 'cash') {
                //INSERT INTO CASH LEDGER
                $balanceStock = $bank->getLastCashBalance();
                $balance = $balanceStock[0]->balance -  $request->credit;
                $items = [
                    'branch_id' => session('branch'),
                    'date' => date("Y-m-d"),
                    'debit' => $request->credit,
                    'credit' => 0,
                    'balance' => $balance,
                    'narration' => "Deposited to Bank Account " . $request->accountid,
                ];
                $result = $bank->insert_bankdetails('cash_ledger', $items);
            }
            return 1;
        } else {
            return 0;
        }
    }

    public function getdetails(bank $bank, Request $request)
    {
        $getbank = $bank->get_banks();
        $getbranches = $bank->get_branches();
        $getdetails = $bank->get_details(Crypt::decrypt($request->id));
        return view('Accounts.edit-accounts', compact('getbank', 'getbranches', 'getdetails'));
    }

    public function updateaccountdetails(bank $bank, Request $request)
    {
        $imageName = "";
        if (!empty($request->image)) {
            // if ($request->prev_image != "") {
            //     $path = public_path('assets/images/bank-account/' . $request->prev_image);
            //     if (file_exists($path)) {
            //         unlink($path);
            //     }
            // }
            // $imageName = time() . "." . $request->image->getClientOriginalExtension();
            // $img = Image::make($request->image)->resize(250, 250);
            // $img->save(public_path('assets/images/bank-account/' . $imageName), 75);
            $result = $this->uploads($request->image, "images/bank-account/", $request->prev_image, ["width" => 250, "height" => 250]);
        }
        $items = [
            'account_title' => $request->accountitle,
            'account_no' => $request->accountno,
            'bank_id' => $request->bank,
            'branch_id' => $request->branch,
            'account_type' => $request->accounttype,
            'date' => date('Y-m-d H:i:s'),
            'user_id' => session('userid'),
            'image' => (!empty($result) ? $result["fileName"] : $request->prev_image)
        ];

        $acc = $bank->update_accounts($request->id, $items);
        return redirect("view-accounts");
    }

    public function chequeView(bank $bank)
    {
        $details = $bank->getchequedetails();
        $status = $bank->cheque_status();
        $customer = $bank->getcustomers();
        return view('Accounts.cheque-details', compact('details', 'status', 'customer'));
    }

    public function chequeInsert(bank $bank, request $request)
    {

        $rules = [
            'Chequenumber' => 'required',
            'Chequedate' => 'required',
            'amount' => 'required',
        ];

        $this->validate($request, $rules);

        $exsit = $bank->exsit_cheque($request->Chequenumber);

        if ($exsit[0]->counts == 0) {
            $items = [
                'cheque_number' => $request->Chequenumber,
                'cheque_date' => $request->Chequedate,
                'amount' => $request->amount,
                'payment_mode' => $request->chtype,
                'bank_name' => $request->bankname,
                'naraation' => $request->narration,
            ];

            $chequeid = $bank->insert('bank_cheque_general', $items);

            $items = [
                'cheque_id' => $chequeid,
                'naraation' => $request->narration,
                'cheque_status_id' => 1, //by default received
                'date' => $request->Chequedate,
                'status_id' => 1, //by default active
            ];
            $result = $bank->insert('bank_cheque_details', $items);

            $items = [
                'cheque_id' => $chequeid,
                'customer_id' => $request->customer,
            ];
            $result = $bank->insert('bank_cheque_customer', $items);

            return  redirect('view-cheque');
        } else {
            return  0;
        }
    }


    public function chequeStatusInsert(bank $bank, request $request)
    {

        $exist = $bank->exist_status($request->statusname);
        if ($exist[0]->counts == 0) {
            $items = [
                'status' => $request->statusname,
            ];
            $result = $bank->insert('cheque_status', $items);
            return 1;
        } else {
            return 0;
        }
    }

    public function clearance(bank $bank, request $request)
    {
        //get primray key for update by cheque id
        $id = $bank->getid($request->chequeid);
        $items = [
            'status_id' => 2,
        ];
        $result = $bank->update_cheque_details($id[0]->id, $items);

        //after update insert new raw
        $account = $request->accountid;
        if ($account == 0) {
            $account = 0;
        } else {
            $account = $request->accountid;
        }
        $items = [
            'cheque_id' => $request->chequeid,
            'naraation' => $request->narration,
            'cheque_status_id' => $request->status,
            'date' => $request->date,
            'status_id' => 1,
            'bank_account_id' => $account,
        ];
        $result = $bank->insert('bank_cheque_details', $items);

        // clearance k table m data dalwana ha agar bounce hwa to hi
        if ($request->status == 4) {
            $items = [
                'cheque_no' => $request->chequenumber,
                'Sync' => 1,
            ];
            $result = $bank->insert('cheque_bounce', $items);
        }

        //bank account m entry dalwani ha
        if ($account > 0) {
            //yaha krte kuch
            //get last balance
            $oldbalance = $bank->getLastBalance($account);

            $balance = $oldbalance[0]->balance +  $request->amount;
            //                if(sizeof($oldbalance) >= 0)
            //                {
            //                    $balance = $oldbalance[0]->balance +  $request->amount;
            //                }
            //                else
            //                {
            //                    $balance = $oldbalance[0]->balance +  $request->amount;
            //                }
            $items = [
                'bank_account_id' => $account,
                'cheque_number' => $request->chequenumber,
                'cheque_date' => $request->chequedate,
                'debit' => 0,
                'credit' => $request->amount,
                'balance' => $balance,
                'narration' => $request->narration,
                'mode' => $request->mode
            ];
            $result = $bank->insert_bankdetails('bank_deposit_details', $items);
        }
        return 1;
    }


    public function viewbychequeid(bank $bank, request $request)
    {
        $details = $bank->getdetailsbychequeid($request->chequeid);
        return $details;
    }

    public function cheque_module(bank $bank, Request $request)
    {
        $details = $bank->getcheques_bydate($request->date);
        $status = $bank->cheque_status();
        $customer = $bank->getcustomers();
        $accounts = $bank->getbankAccounts();
        return view('Accounts.cheque-module', compact('details', 'status', 'customer', 'accounts'));
    }

    public function filter_cheque(bank $bank, Request $request)
    {
        $result = $bank->getcheques_filter($request->fromdate, $request->todate, $request->chequestatus, $request->paymentmode, $request->customer);
        return $result;
    }

    public function editledgernarration(bank $bank, Request $request)
    {

        $items = [
            'narration' => $request->narration,
        ];
        $result = $bank->update_ledger_narration($request->id, $items);
        return 1;
    }
    public function editbankrnarration(bank $bank, Request $request)
    {

        $items = [
            'narration' => $request->narration,
        ];
        $result = $bank->update_bank_narration($request->id, $items);
        return 1;
    }

    public function getBanks(bank $bank, Request $request)
    {
        $banks = $bank->get_banks();
        return view('Bank.index', compact('banks'));
    }

    public function addNewBank(bank $bank, Request $request)
    {
        return view('Bank.create');
    }

    public function editBank(bank $bank, Request $request)
    {
        $getBank = $bank->getOneBank(Crypt::decrypt($request->id));
        return view('Bank.edit', compact('getBank'));
    }

    public function saveNewBank(bank $bank, Request $request)
    {
        $imageName = "";

        $exsists = $bank->exsists_bank($request->bankname);

        if ($exsists[0]->counter == 0) {

            if (!empty($request->vdimg)) {
                $request->validate([
                    'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
                ]);
                // $imageName = time() . '.' . $request->vdimg->getClientOriginalExtension();
                // $img = Image::make($request->vdimg)->resize(600, 600);
                // $res = $img->save(public_path('assets/images/banks/' . $imageName), 75);

                $result = $this->uploads($request->vdimg, "images/banks/", "", ["width" => 600, "height" => 600]);
            }

            $items = [
                'bank_name' => $request->bankname,
                'image' => (!empty($result) ? $result["fileName"] : ""),

            ];
            $addbank = $bank->insert_bankdetails('banks', $items);


            return redirect("get-banks");
        }

        return view('Bank.create');
    }

    public function updateBank(bank $bank, Request $request)
    {
        $imageName = "";


        if (!empty($request->vdimg)) {
            // $image_path = public_path('assets/images/banks/' . $request->prev_image);  // Value is not URL but directory file path
            // if ($request->prev_image != "") {
            //     unlink($image_path);
            // }
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);
            // $imageName = time() . '.' . $request->vdimg->getClientOriginalExtension();
            // $img = Image::make($request->vdimg)->resize(600, 600);
            // $res = $img->save(public_path('assets/images/banks/' . $imageName), 75);
            $result = $this->uploads($request->vdimg, "images/banks/", $request->prev_image, ["width" => 600, "height" => 600]);
        }

        $items = [
            'bank_name' => $request->bankname,
            'image' => (!empty($result) ? $result["fileName"] : $request->prev_image),

        ];
        $addbank = $bank->update_bank($request->id, $items);

        return redirect("get-banks");
    }

    // INSERT BANK
    public function submit_details(bank $bank, Request $request)
    {

        $exsists = $bank->exsists_bank($request->bankname);

        if ($exsists[0]->counter == 0) {

            $items = [
                'bank_name' => $request->bankname,
            ];
            $addbank = $bank->insert_bankdetails('banks', $items);
            $getbank = $bank->get_banks();

            return $getbank;
        } else {
            if ($request->branchname == '') {
                return 0;
            } else {
                $exsists = $bank->exsists_branch($request->branchname);

                if ($exsists[0]->counter == 0) {
                    $items = [
                        'branch_name' => $request->branchname,
                        'bank_id' => $request->bank_id,
                    ];
                    $addbranch = $bank->insert_bankdetails('bank_branches', $items);
                    $getbranch = $bank->get_branches();
                    return $getbranch;
                } else {
                    return 0;
                }
            }
        }
    }

    public function cashledgerPDF(Request $request, Vendor $vendor, bank $bank)
    {

        $company = $vendor->company(session('company_id'));
        $totalBalance = $bank->getcashledgerbalance();
        $result = $bank->getCashDetails();

        $pdf = new pdfClass();
        $pdf->AliasNbPages();
        $pdf->AddPage();


        if (!file_exists(public_path('storage/images/company/qrcode.png'))) {
            $qrcodetext = $company[0]->name . " | " . $company[0]->ptcl_contact . " | " . $company[0]->address;
            \QrCode::size(200)
                ->format('png')
                ->generate($qrcodetext, public_path('storage/images/company/qrcode.png'));
        }

        //first row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Cell(105, 0, "Company Name:", 0, 0, 'L');
        $pdf->Cell(50, 0, "", 0, 1, 'L');

        //second row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Image(public_path('storage/images/company/' . $company[0]->logo), 12, 10, -200);
        $pdf->Cell(105, 12, $company[0]->name, 0, 0, 'L');
        $pdf->Cell(50, 0, "", 0, 1, 'R');
        $pdf->Image(public_path('storage/images/company/qrcode.png'), 175, 10, -200);

        //third row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 25, '', 0, 0);
        $pdf->Cell(105, 25, "Contact Number:", 0, 0, 'L');
        $pdf->Cell(50, 25, "", 0, 1, 'L');

        //forth row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, -15, '', 0, 0);
        $pdf->Cell(105, -15, $company[0]->ptcl_contact, 0, 0, 'L');
        $pdf->Cell(50, -15, "", 0, 1, 'L');

        //fifth row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 28, '', 0, 0);
        $pdf->Cell(105, 28, "Company Address:", 0, 0, 'L');
        $pdf->Cell(50, 28, "", 0, 1, 'L');

        //sixth row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(35, -18, '', 0, 0);
        $pdf->Cell(105, -18, $company[0]->address, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, -18, "Generate Date:  " . date('Y-m-d'), 0, 1, 'R');

        //report name
        $pdf->ln(15);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(190, 10, 'Cash Statement', 'B,T', 1, 'L');



        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(10, 8, 'Sr.', 'B', 0, 'L');
        $pdf->Cell(25, 8, 'Date', 'B', 0, 'L');
        $pdf->Cell(25, 8, 'Debit', 'B', 0, 'R');
        $pdf->Cell(25, 8, 'Credit', 'B', 0, 'R');
        $pdf->Cell(25, 8, 'Balance', 'B', 0, 'R');
        $pdf->Cell(80, 8, 'Narration', 'B', 1, 'L');



        $count = 0;
        foreach ($result as $key => $value) {
            $count++;
            if ($count % 2 == 0) {
                $pdf->SetFont('Arial', '', 8);
                $pdf->setFillColor(232, 232, 232);
                $pdf->SetTextColor(0, 0, 0);
            } else {
                $pdf->SetFont('Arial', '', 8);
                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
            }
            $pdf->Cell(10, 6, $key + 1, 0, 0, 'L', 1);
            $pdf->Cell(25, 6, date("d F Y", strtotime($value->date)), 0, 0, 'L', 1);
            $pdf->Cell(25, 6, number_format($value->debit, 2), 0, 0, 'R', 1);
            $pdf->Cell(25, 6, number_format($value->credit, 2), 0, 0, 'R', 1);
            $pdf->Cell(25, 6, number_format($value->balance, 2), 0, 0, 'R', 1);
            $pdf->Multicell(80, 6, $value->narration, 0, 1, 'L', 1);
        }


        $pdf->ln();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(130, 8, '', 0, 0, 'R');
        $pdf->Cell(20, 8, 'Total:', 'T,B', 0, 'R');
        $pdf->Cell(40, 8, "Rs. " . number_format($totalBalance[0]->balance, 2), 'T,B', 1, 'R');

        //save file
        $pdf->Output('Cash Ledger.pdf', 'I');
    }

    public function bankledgerPDF(Request $request, Vendor $vendor, bank $bank)
    {

        $company = $vendor->company(session('company_id'));
        $totalBalance = $bank->getbankledgerbalance($request->id);
        $result = $bank->getLedger($request->id);
        $account = $bank->getaccountdetails_byid($request->id);


        $pdf = new pdfClass();
        $pdf->AliasNbPages();
        $pdf->AddPage();


        if (!file_exists(public_path('storage/images/company/qrcode.png'))) {
            $qrcodetext = $company[0]->name . " | " . $company[0]->ptcl_contact . " | " . $company[0]->address;
            \QrCode::size(200)
                ->format('png')
                ->generate($qrcodetext, public_path('storage/images/company/qrcode.png'));
        }

        //first row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Cell(105, 0, "Company Name:", 0, 0, 'L');
        $pdf->Cell(50, 0, "", 0, 1, 'L');

        //second row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Image(public_path('storage/images/company/' . $company[0]->logo), 12, 10, -200);
        $pdf->Cell(105, 12, $company[0]->name, 0, 0, 'L');
        $pdf->Cell(50, 0, "", 0, 1, 'R');
        $pdf->Image(public_path('storage/images/company/qrcode.png'), 175, 10, -200);

        //third row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 25, '', 0, 0);
        $pdf->Cell(105, 25, "Contact Number:", 0, 0, 'L');
        $pdf->Cell(50, 25, "", 0, 1, 'L');

        //forth row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, -15, '', 0, 0);
        $pdf->Cell(105, -15, $company[0]->ptcl_contact, 0, 0, 'L');
        $pdf->Cell(50, -15, "", 0, 1, 'L');

        //fifth row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 28, '', 0, 0);
        $pdf->Cell(105, 28, "Company Address:", 0, 0, 'L');
        $pdf->Cell(50, 28, "", 0, 1, 'L');

        //sixth row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(35, -18, '', 0, 0);
        $pdf->Cell(105, -18, $company[0]->address, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, -18, "Generate Date:  " . date('Y-m-d'), 0, 1, 'R');

        //report name
        $pdf->ln(15);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(190, 10, $account[0]->account_title . ' | Bank Statement', 'B,T', 1, 'L');



        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(10, 8, 'Sr.', 'B', 0, 'L');
        $pdf->Cell(25, 8, 'Date', 'B', 0, 'L');
        $pdf->Cell(25, 8, 'Debit', 'B', 0, 'R');
        $pdf->Cell(25, 8, 'Credit', 'B', 0, 'R');
        $pdf->Cell(25, 8, 'Balance', 'B', 0, 'R');
        $pdf->Cell(80, 8, 'Narration', 'B', 1, 'L');



        $count = 0;
        foreach ($result as $key => $value) {
            $count++;
            if ($count % 2 == 0) {
                $pdf->SetFont('Arial', '', 8);
                $pdf->setFillColor(232, 232, 232);
                $pdf->SetTextColor(0, 0, 0);
            } else {
                $pdf->SetFont('Arial', '', 8);
                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
            }
            $pdf->Cell(10, 6, $key + 1, 0, 0, 'L', 1);
            $pdf->Cell(25, 6, date("d F Y", strtotime($value->date)), 0, 0, 'L', 1);
            $pdf->Cell(25, 6, number_format($value->debit, 2), 0, 0, 'R', 1);
            $pdf->Cell(25, 6, number_format($value->credit, 2), 0, 0, 'R', 1);
            $pdf->Cell(25, 6, number_format($value->balance, 2), 0, 0, 'R', 1);
            $pdf->Multicell(80, 6, $value->narration, 0, 1, 'L', 1);
        }


        $pdf->ln();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(130, 8, '', 0, 0, 'R');
        $pdf->Cell(20, 8, 'Total:', 'T,B', 0, 'R');
        $pdf->Cell(40, 8, "Rs. " . number_format($totalBalance[0]->balance, 2), 'T,B', 1, 'R');

        //save file
        $pdf->Output('Bank Ledger.pdf', 'I');
    }
}
