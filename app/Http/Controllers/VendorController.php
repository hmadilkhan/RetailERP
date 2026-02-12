<?php
namespace App\Http\Controllers;
use App\Vendor;
use App\Models\Vendor as Vendors;
use App\Models\VendorAdvance;
use App\Models\Purchase;
use App\Models\VendorPurchase;
use App\Models\VendorLedger;
use App\bank;
use App\inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Terbilang;
use App\pdfClass;
use App\Traits\MediaTrait;
use Illuminate\Support\Str;
use Exception;
class VendorController extends Controller
{
    use MediaTrait;
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
        $vendor = DB::select('SELECT *,(SELECT balance FROM vendor_ledger where vendor_account_id = (Select MAX(vendor_account_id) from vendor_ledger where vendor_id = a.id)) as balance FROM vendors a INNER JOIN vendor_company_details b on b.vendor_id = a.id INNER JOIN country c on c.country_id = a.country_id INNER JOIN city d on d.city_id = a.city_id where a.user_id = ? and a.status_id = 1 order by a.id desc', [session('company_id')]);
        return view('Vendor.list', compact('vendor'));
    }
    public function generatePDF()
    {
        $data = [
            'title' => 'First PDF for Medium',
            'heading' => 'Hello from 99Points.info',
            'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.'
        ];
        $customPaper = array(0, 0, 567.00, 283.80);
        $pdf = PDF::loadView('mypdf', $data)->setPaper(500, 700);
        return $pdf->download('medium.pdf');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $country = DB::table('country')->where('country_id', 170)->get();
        $city = DB::table('city')->where('country_id', 170)->get();
        return view('Vendor.create', compact('country', 'city'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file = [];
        $rules = [
            'vdname' => 'required',
            'vdcontact' => 'required',
            'country' => 'required',
            'city' => 'required',
        ];
        /*$customMessages = [
                'required' => 'The :attribute field is required.'
            ];*/
        $this->validate($request, $rules);
        $imageName = "";
        if (!empty($request->vdimg)) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);
            $file = $this->uploads($request->vdimg,"images/vendors/");
        }
        $vendor = new Vendor([
            'status_id' => 1,
            'user_id' => session('company_id'),
            'country_id' => $request->get('country'),
            'city_id' => $request->get('city'),
            'vendor_name' => $request->get('vdname'),
            'vendor_contact' => $request->get('vdcontact'),
            'vendor_email' => $request->get('vdemail'),
            'address' => $request->get('address'),
            'image' => !empty($file) ? $file["fileName"] : "",
            'payment_terms' => ($request->get('paymentdays') == "" ? 0 : $request->get('paymentdays')),
            'slug' => strtolower(Str::random(4)),
            'ntn' => $request->get('ntn'),
            'strn' => $request->get('strn'),
        ]);
        $vendor->save();
        // if(!empty($request->get('cpname')) || !empty($request->get('cpemail')) || !empty($request->get('cpcontact')) || !empty($request->get('cpfax')) || !empty($request->get('cptype')) || !empty($request->get('website'))){
        $logo = [];
        if (!empty($request->logo)) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);
            $logo = $this->uploads($request->logo,"images/vendors/");
        }
        $result = DB::table('vendor_company_details')->insert([
            'vendor_id' => $vendor->id,
            'company_name' => $request->get('cpname'),
            'company_email' => $request->get('cpemail'),
            'company_contact' => $request->get('cpcontact'),
            'company_fax' => '',
            'company_type' => '',
            'website' => '',
            'logo' => !empty($logo) ? $logo["fileName"] : "",
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);
        $ledger = DB::table('vendor_ledger')->insert([
            'vendor_id' => $vendor->id,
            'po_no' => '0',
            'total_amount' => '0',
            'debit' => '0',
            'credit' => ($request->ob == "" ? 0 : $request->ob),
            'balance' => ($request->ob == "" ? 0 : $request->ob),
        ]);
        // }
        return redirect()->route('vendors.index');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor)
    {
        $vendor = Vendor::find($vendor->id);
        return view('Vendor.show', compact('vendor'));
    }
    public function emailcheck(Request $request)
    {
        $vendor = Vendor::where('vendor_email', $request->email)->get();
        if (sizeof($vendor) > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    public function namecheck(Request $request)
    {
        $vendor = Vendor::where(['vendor_name' => $request->name, 'user_id' => session('company_id')])->get();
        if (sizeof($vendor) > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Vendor $vendor)
    {
        $vendor = Vendor::find($vendor->id);
        $country = DB::table('country')->where('country_id', 170)->get();
        $city = DB::table('city')->where('country_id', 170)->get();
        $company = DB::table('vendor_company_details')->where('vendor_id', $vendor->id)->get();
        return view('Vendor.edit', compact('vendor', 'company', 'country', 'city'));
    }
    public function getVendorProduct(Request $request, Vendor $vendor)
    {
        $result = $vendor->getVendorProduct($request->id);
        return $result;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vendor $vendor)
    {
        $vendor = Vendor::find($vendor->id);
        $rules = [
            'vdname' => 'required',
            'vdcontact' => 'required',
            'country' => 'required',
            'city' => 'required',
        ];
        $this->validate($request, $rules);
        $file = [];
        if (!empty($request->vdimg)) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);
            $file = $this->uploads($request->vdimg,"images/vendors/");
        }
        $imageName =  (!empty($file) ? $file["fileName"] : $request->prevvendorimage);
        $vendor->user_id = session('company_id');
        $vendor->country_id = $request->get('country');
        $vendor->city_id = $request->get('city');
        $vendor->vendor_name = $request->get('vdname');
        $vendor->vendor_contact = $request->get('vdcontact');
        $vendor->vendor_email = $request->get('vdemail');
        $vendor->address = $request->get('address');
        $vendor->image = $imageName;
        $vendor->payment_terms = $request->get('paymentdays');
        $vendor->ntn = $request->get('ntn');
        $vendor->strn = $request->get('strn');
        $vendor->save();
        if (!empty($request->get('cpname')) || !empty($request->get('cpemail')) || !empty($request->get('cpcontact')) || !empty($request->get('cpfax')) || !empty($request->get('cptype')) || !empty($request->get('website'))) {
            $logo = [];
            if (!empty($request->logo)) {
                $request->validate([
                    'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
                ]);
                $logo = $this->uploads($request->logo,"images/vendors/",$request->get('companyvendorimage'));
            }
            $imageName =  (!empty($request->logo) ? $logo["fileName"] : $request->get('companyvendorimage') );
            $result = DB::table('vendor_company_details')->where('vendor_id', $vendor->id)->update([
                'company_name' => $request->get('cpname'),
                'company_email' => $request->get('cpemail'),
                'company_contact' => $request->get('cpcontact'),
                'company_fax' => '',
                'company_type' => '',
                'website' => '',
                'logo' => $imageName,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
        }
        return redirect()->route('vendors.index');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function all_vendoremove(Request $request)
    {
        $result = Vendor::whereIn('id', $request->id)->update(['status_id' => 2]);
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }
    public function LedgerDetails(Request $request, Vendor $vendor)
    {
        $vendorName = $vendor->getVendorName($request->id);
        if (!$vendorName->isEmpty()) {
            $details = $vendor->LedgerDetails($request->id, "", "");
            $purchases = $vendor->getPOByVendor($vendorName[0]->id);
            $vendorID = $vendorName[0]->id;
            $vendor = $vendorName[0]->vendor_name;
            $slug = $request->id;
            return view('Vendor.ledger', compact('details', 'purchases', 'vendorID', 'vendor', 'slug'));
        } else {
            return view("404");
        }
    }
    public function LedgerDetailsByID(Request $request, Vendor $vendor)
    {
        $details = $vendor->LedgerDetails($request->id, "", "");
        return $details;
    }
    public function destroy($id)
    {
        $vendor = Vendor::find($id);
        $vendor->status_id = 2;
        $result = $vendor->save();
        return response()->json(array('result' => $result));
    }
    public function getPO(Request $request, Vendor $vendor)
    {
        $details = $vendor->getAmountFromPO($request->id);
        return $details;
    }
    public function createPayment(Request $request, Vendor $vendor, bank $bank)
    {
        $vendorid = $vendor->getVendorId($request->id);
        $purchases = $vendor->getPOsByVendor($vendorid[0]->id);
        $balance = $vendor->getLastBalance($vendorid[0]->id);
        $banks = $bank->getbankAccounts();
        $vendorName = $vendor->getVendorName($request->id);
        $advanceBalance =  VendorAdvance::where("vendor_id", $vendorid[0]->id)->select(DB::raw("SUM(credit)-SUM(debit) as balance"))->get();
        $advance = (!empty($advanceBalance) ? $advanceBalance[0]->balance : 0);
        $vendor = $vendorid[0]->id;
        $slug = $request->id;
        if (sizeof($balance) == 0) {
            $balance = 0;
        }
        return view('Vendor.Createpayment', compact('purchases', 'balance', 'vendor', 'banks', 'vendorName', 'slug', 'advance'));
    }
    public function checkLastBalance(Request $request, Vendor $vendor, bank $bank)
    {
        $balance = $bank->getLastBalance($request->bankid);
        return $balance;
    }
    public function makePayment(Request $request, Vendor $vendor, bank $bank)
    {
        if ($request->id != "") {
            $total_credit = $request->totalCredit;
            $fields = [
                'vendor_id' => $request->vendor,
                'po_no' => $request->id,
                'total_amount' => 0,
                'debit' => $request->amount,
                'credit' => 0,
                'balance' => $total_credit,
                'narration' => $request->narration,
            ];
            $account = $vendor->po_account_update($request->id, $request->bal);
            $general = $vendor->po_general_status_update($request->id, $request->status);
            $ledger = $vendor->insert_into_ledger($fields);
            // $items = [
            //   'vendor_account_id' =>$ledger,
            //   'bankid' =>$request->accountid,
            //   'cheque' =>$request->cheque_number,
            //   'narration' =>$request->narration,
            // ];
            //         $bank = $vendor->insert_into_bank_details_for_vendor($items);
            if ($ledger > 0) {
                return $ledger;
            } else {
                return 0;
            }
        } else {
            $total_credit = $request->totalCredit;
            $fields = [
                'vendor_id' => $request->vendor,
                'po_no' => 0,
                'total_amount' => 0,
                'debit' => $request->amount,
                'credit' => 0,
                'balance' => $request->totalCredit,
                'narration' => $request->narration,
            ];
            $account = $vendor->po_account_update(
                $request->id,
                $request->bal
            );
            $general = $vendor->po_general_status_update($request->id, $request->status);
            $ledger = $vendor->insert_into_ledger($fields);
            if ($ledger > 0) {
                return $ledger;
            } else {
                return 0;
            }
        }
    }
    public function addIntoLedger(Request $request, Vendor $vendor)
    {
        $total_credit = $request->totalCredit;
        $fields = [
            'vendor_id' => $request->vendor,
            'po_no' => $request->id,
            'total_amount' => 0,
            'debit' => $request->debit,
            'credit' => 0,
            'balance' => $total_credit,
            'narration' => $request->narration,
        ];
        $ledger = $vendor->insert_into_ledger($fields);
        if ($ledger > 0) {
            return $ledger;
        } else {
            return 0;
        }
    }
    public function LastCashBalance(Request $request, Vendor $vendor, bank $bank)
    {
        $balance = $bank->getLastCashBalance();
        return $balance;
    }
    public function debitPayment(Request $request, Vendor $vendor)
    {
        $fields = [
            'vendor_id' => $request->vendor,
            'po_no' => $request->id,
            'total_amount' => 0,
            'debit' => $request->amount,
            'credit' => 0,
            'balance' => $request->amount,
            'narration' => $request->narration,
        ];
        $ledger = $vendor->insert_into_ledger($fields);
        return 1;
    }
    public function vendorPayment(Request $request, Vendor $vendor, bank $bank)
    {
        $items = [
            'bankid' => $request->accountid,
            'cheque' => $request->cheque_number,
            'payment' => $request->credit,
            'narration' => $request->narration,
        ];
        $bank = $vendor->insert_into_bank_details_for_vendor($items);
        return $bank;
    }
    public function vendorPaymentDetails(Request $request, Vendor $vendor, bank $bank)
    {
        $items = [
            'payment_id' => $request->payment,
            'account_id' => $request->account,
        ];
        $bank = $vendor->vendor_payment_details($items);
        return $bank;
    }
    public function creditBank(Request $request, Vendor $vendor, bank $bank)
    {
        $items = [
            'bank_account_id' => $request->accountid,
            'cheque_number' => $request->cheque_number,
            'cheque_date' => date("Y-m-d"),
            'debit' => $request->credit,
            'credit' => 0,
            'balance' => $request->balance,
            'narration' => $request->narration,
            'mode' => ($request->mode == "cash" ? 'Cash' : 'Cheque')
        ];
        $result = $bank->insert_bankdetails('bank_deposit_details', $items);
        return 1;
    }
    public function make_cash_payment(Request $request, Vendor $vendor, bank $bank)
    {
        $amount = str_replace(',', '', $request->amount);
        $requestedbalance = str_replace(',', '', $request->bal);
        $requestedcredit = str_replace(',', '', $request->totalCredit);
        $fields = [
            'vendor_id' => $request->vendor,
            'po_no' => ($request->id != 0 ? $request->id : 0),
            'batch_no' => $request->batch_no,
            'total_amount' => 0,
            'debit' => $amount,
            'credit' => 0,
            'balance' => $requestedcredit,
            'narration' => $request->narration,
        ];
        $ledger = $vendor->insert_into_ledger($fields);
        if ($ledger > 0) {
            $account = $vendor->po_account_update($request->id, $requestedbalance);
            $general = $vendor->po_general_status_update($request->id, $request->status);
            //INSERT INTO PAYMENT GENERAL
            $items = [
                'bankid' => 0,
                'cheque' => 0,
                'payment' => $amount,
                'narration' => $request->narration,
            ];
            $payment = $vendor->insert_into_bank_details_for_vendor($items);
            //INSERT INTO PAYMENT DETAILS
            $items = [
                'payment_id' => $payment,
                'account_id' => $ledger,
            ];
            $paymentDetails = $vendor->vendor_payment_details($items);
            //INSERT INTO CASH LEDGER
            $balanceStock = $bank->getLastCashBalance();
            $balance = $balanceStock[0]->balance -  $amount;
            $items = [
                'branch_id' => session('branch'),
                'date' => date("Y-m-d"),
                'debit' => $amount,
                'credit' => 0,
                'balance' => $balance,
                'narration' => "Payment to Vendor " . $request->vendorName,
            ];
            $result = $bank->insert_bankdetails('cash_ledger', $items);
            return $ledger;
        } else {
            return 0;
        }
    }
    public function vendor_report_panel(Request $request, Vendor $vendor)
    {
        $vendors = $vendor->getVendors();
        $details = $vendor->account_payable("", "", "");
        return view('reports.vendor', compact('details', 'vendors'));
    }
    public function vendor_report_filter(Request $request, Vendor $vendor)
    {
        $details = $vendor->account_payable($request->vendor, $request->first, $request->second);
        return $details;
    }
    public function exportpDF(Request $request, Vendor $vendor)
    {
        $totalBalance = 0;
        $company = $vendor->company(session('company_id'));
        $result = $vendor->account_payable($request->vendor, $request->first, $request->second);
        $qrcodetext = $company[0]->name . " | " . $company[0]->ptcl_contact . " | " . $company[0]->address;
        \QrCode::size(200)
            ->format('png')
            ->generate($qrcodetext, public_path('assets/images/company/qrcode.png'));
        $pdf = new pdfClass();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        //first row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Cell(105, 0, "Company Name:", 0, 0, 'L');
        $pdf->Cell(50, 0, "", 0, 1, 'L');
        //second row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Image(public_path('assets/images/company/' . $company[0]->logo), 12, 10, -200);
        $pdf->Cell(105, 12, $company[0]->name, 0, 0, 'L');
        $pdf->Cell(50, 0, "", 0, 1, 'R');
        $pdf->Image(public_path('assets/images/company/qrcode.png'), 175, 10, -200);
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
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, -18, '', 0, 0);
        $pdf->Cell(105, -18, $company[0]->address, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, -18, "Generate Date:  " . date('Y-m-d'), 0, 1, 'R');
        //report name
        $pdf->ln(15);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(190, 10, 'Vendor Payable', 'B,T', 1, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(10, 8, 'Sr.', 'B', 0, 'L');
        $pdf->Cell(60, 8, 'Vendor Name', 'B', 0, 'L');
        $pdf->Cell(65, 8, 'Company Name', 'B', 0, 'L');
        $pdf->Cell(25, 8, 'Contact', 'B', 0, 'L');
        $pdf->Cell(30, 8, 'Balance', 'B', 1, 'R');
        $count = 0;
        foreach ($result as $key => $value) {
            $count++;
            if ($count % 2 == 0) {
                $pdf->SetFont('Arial', '', 10);
                $pdf->setFillColor(232, 232, 232);
                $pdf->SetTextColor(0, 0, 0);
            } else {
                $pdf->SetFont('Arial', '', 10);
                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
            }
            if ($value->balance != 0) {
                $totalBalance = $totalBalance + ($value->balance * (-1));
                $pdf->Cell(10, 6, $key + 1, 0, 0, 'L', 1);
                $pdf->Cell(60, 6, $value->vendor_name, 0, 0, 'L', 1);
                $pdf->Cell(65, 6, $value->company_name, 0, 0, 'L', 1);
                $pdf->Cell(25, 6, $value->vendor_contact, 0, 0, 'L', 1);
                $pdf->Cell(30, 6, number_format($value->balance * (-1), 2), 0, 1, 'R', 1);
            }
        }
        $pdf->ln();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(130, 8, '', 0, 0, 'R');
        $pdf->Cell(20, 8, 'Total:', 'T,B', 0, 'R');
        $pdf->Cell(40, 8, "Rs. " . number_format($totalBalance, 2), 'T,B', 1, 'R');
        //        // Go to 1.5 cm from bottom
        //        $pdf->SetY(-24);
        //        // Select Arial italic 8
        //        $pdf->SetFont('Arial','I',10);
        //        // Print centered page number
        //        $pdf->Cell(160,2,'System Generated Report: Sabify',0,0,'L');
        //        $pdf->SetFont('Arial','',10);
        //        $pdf->Cell(30,2,'Page | '.$pdf->PageNo(),0,0,'R');
        //save file
        $pdf->Output('Vendor Payable.pdf', 'I');
    }
    public function voucher(Request $request, Vendor $vendor)
    {
        $print = $vendor->voucherPrint($request->id);
        $company = $vendor->company(session('company_id'));
        $cash = $print[0]->cheque == 0 ? "CASH" : "";
        $pdf = app('Fpdf');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Image(public_path('assets/images/company/' . $company[0]->logo), 10, 10, -200);
        $pdf->SetFont('Arial', 'BU', 18);
        $pdf->MultiCell(0, 10, 'PAYMENT VOUCHER', 0, 'C');
        $pdf->Cell(2, 2, '', 0, 1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'Voucher No # ' . $print[0]->payment_id, 0, 1, 'R'); //Here is center title
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(10, 10, '', 0, 1);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 8, 'Amount :', 'T,B,L', 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(75, 8, 'Rs.' . number_format($print[0]->debit, 2), 'T,B,R', 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 8, 'Date : ', 'T,B,L', 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(75, 8, date("d F Y", strtotime($print[0]->date)), 'T,B,R', 1, 'L');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(190, 8, 'Method of Payment', 1, 1, 'C');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 8, 'Cash :', 'T,B,L', 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(75, 8, $cash, 'T,B,R', 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 8, 'Cheque : ', 'T,B,L', 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(75, 8, ($print[0]->cheque != 0 ? $print[0]->cheque . " (" . $print[0]->bank_name . ")" : ''), 'T,B,R', 1, 'L');
        if ($print[0]->cheque != 0) {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(35, 8, 'Account Title :', 'T,B,L', 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(60, 8, $print[0]->account_title, 'T,B,R', 0, 'L');
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(35, 8, 'Account Number : ', 'T,B,L', 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(60, 8, ($print[0]->account_no), 'T,B,R', 1, 'L');
        }
        $amount = Terbilang::make($print[0]->debit);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 8, 'To : ', 'T,B,L', 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(180, 8, 'Mr. ' . strtoupper($print[0]->vendor_name), 'T,B,R', 1, 'L');
        $pdf->Cell(25, 8, 'The Sum of : ', 'T,B,L', 0, 'L');
        $pdf->Cell(165, 8, strtoupper($amount) . " RUPEES ONLY.", 'T,B,R', 1, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(15, 20, 'Being :', 'T,B,L', 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(125, 20, $print[0]->narration, 'T,B,R', 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 20, 'Payee :', 'T,B,L', 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(30, 20, strtoupper($company[0]->name), 'T,B,R', 1, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(70, 20, 'Approved By :', 1, 0, 'L');
        $pdf->Cell(70, 20, 'Paid By :', 1, 0, 'L');
        $pdf->Cell(50, 20, 'Signature :', 1, 1, 'L');
        //save file
        $pdf->Output('Payment Voucher.pdf', 'I');
    }
    function numberTowords($num)
    {
        $ones = array(
            0 => "ZERO",
            1 => "ONE",
            2 => "TWO",
            3 => "THREE",
            4 => "FOUR",
            5 => "FIVE",
            6 => "SIX",
            7 => "SEVEN",
            8 => "EIGHT",
            9 => "NINE",
            10 => "TEN",
            11 => "ELEVEN",
            12 => "TWELVE",
            13 => "THIRTEEN",
            14 => "FOURTEEN",
            15 => "FIFTEEN",
            16 => "SIXTEEN",
            17 => "SEVENTEEN",
            18 => "EIGHTEEN",
            19 => "NINETEEN",
            "014" => "FOURTEEN"
        );
        $tens = array(
            0 => "ZERO",
            1 => "TEN",
            2 => "TWENTY",
            3 => "THIRTY",
            4 => "FORTY",
            5 => "FIFTY",
            6 => "SIXTY",
            7 => "SEVENTY",
            8 => "EIGHTY",
            9 => "NINETY"
        );
        $hundreds = array(
            "HUNDRED",
            "THOUSAND",
            "MILLION",
            "BILLION",
            "TRILLION",
            "QUARDRILLION"
        ); /*limit t quadrillion */
        $num = number_format($num, 2, ".", ",");
        $num_arr = explode(".", $num);
        $wholenum = $num_arr[0];
        $decnum = $num_arr[1];
        $whole_arr = array_reverse(explode(",", $wholenum));
        krsort($whole_arr, 1);
        $rettxt = "";
        foreach ($whole_arr as $key => $i) {
            while (substr($i, 0, 1) == "0")
                $i = substr($i, 1, 5);
            if ($i < 20) {
                /* echo "getting:".$i; */
                $rettxt .= $ones[$i];
            } elseif ($i < 100) {
                if (substr($i, 0, 1) != "0")  $rettxt .= $tens[substr($i, 0, 1)];
                if (substr($i, 1, 1) != "0") $rettxt .= " " . $ones[substr($i, 1, 1)];
            } else {
                if (substr($i, 0, 1) != "0") $rettxt .= $ones[substr($i, 0, 1)] . " " . $hundreds[0];
                if (substr($i, 1, 1) != "0") $rettxt .= " " . $tens[substr($i, 1, 1)];
                if (substr($i, 2, 1) != "0") $rettxt .= " " . $ones[substr($i, 2, 1)];
            }
            if ($key > 0) {
                $rettxt .= " " . $hundreds[$key] . " ";
            }
        }
        if ($decnum > 0) {
            $rettxt .= " and ";
            if ($decnum < 20) {
                $rettxt .= $ones[$decnum];
            } elseif ($decnum < 100) {
                $rettxt .= $tens[substr($decnum, 0, 1)];
                $rettxt .= " " . $ones[substr($decnum, 1, 1)];
            }
        }
        return $rettxt;
    }
    // extract($_POST);
    // if(isset($convert))
    // {
    // echo "<p align='center' style='color:blue'>".numberTowords("$num")."</p>";
    // }
    public function profitLoss(Request $request, Vendor $vendor)
    {
        $totalBalance = 0;
        $totalRevenue = 0;
        $totalCogs = 0;
        $gross = 0;
        $net = 0;
        $result = $vendor->profitandloss($request->first, $request->second);
        $expense = $vendor->profitandlossexpense($request->first, $request->second);
        $cogs = $vendor->cogs($request->first, $request->second);
        $master = $vendor->masterAmount($request->first, $request->second);
        $pdf = app('Fpdf');
        $pdf->AddPage();
        #HEADERS STARTS FROM HERE
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Image(asset('storage/images/company/' . $company[0]->logo), 10, 10, -200);
        $pdf->SetFont('Arial', 'BU', 18);
        $pdf->MultiCell(0, 10, 'TAYYEB JAMAL', 0, 'C');
        $pdf->Cell(2, 2, '', 0, 1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 3, 'PROFIT AND LOSS REPORT', 0, 1, 'C'); //Here is center title
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(10, 10, '', 0, 1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'Tayyeb Jamal', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, 'Hamid Hussain Farooqi Rd, P.E.C.H.S Block 2,', 0, 0, 'L');
        $pdf->Cell(0, 4, 'From : ' . date("F d Y", strtotime($request->first)), 0, 1, 'R');
        $pdf->Cell(0, 5, 'Karachi, Karachi City, Sindh', 0, 0, 'L');
        $pdf->Cell(0, 5, 'To : ' . date("F d Y", strtotime($request->second)), 0, 1, 'R');
        $pdf->Cell(0, 4, '021-34513353', 0, 0, 'L');
        $pdf->Cell(0, 4, '', 0, 1, 'R');
        $pdf->Cell(190, 8, '', '', 1); //SPACE
        //REVENUE START HERE
        $totalRevenue = $result[0]->Total - $result[0]->Discount + $result[0]->salesreturn;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(230, 230, 230);
        $pdf->Cell(190, 8, 'REVENUE', 0, 1, 'L', 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(70, 8, 'REVENUE - All Products ', 0, 0, 'L');
        $pdf->Cell(40, 8, '', 0, 0, 'L');
        $pdf->Cell(40, 8, '', 0, 0, 'L');
        $pdf->Cell(40, 8, number_format($result[0]->Total, 2), 0, 1, 'R');
        $pdf->Cell(70, 8, 'Sales Discount - All Products', 0, 0, 'L');
        $pdf->Cell(40, 8, '', 0, 0, 'L');
        $pdf->Cell(50, 8, '', 0, 0, 'L');
        $pdf->Cell(30, 8, number_format($result[0]->Discount, 2), 0, 1, 'R');
        $pdf->Cell(70, 8, 'Sales Return and Allowances - All Products ', 0, 0, 'L');
        $pdf->Cell(40, 8, '', '', 0, 'L');
        $pdf->Cell(50, 8, '', '', 0, 'L');
        $pdf->Cell(30, 8, number_format($result[0]->salesreturn, 2), 'B', 1, 'R');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(95, 8, 'Total Revenue', 0, 0, 'L');
        $pdf->Cell(95, 8, number_format($totalRevenue, 2), 0, 1, 'R');
        //REVENUE END HERE
        $pdf->Cell(190, 8, '', '', 1); //SPACE
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(230, 230, 230);
        $pdf->Cell(190, 8, 'COST OF GOOD SALES', 0, 1, 'L', 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(70, 8, 'Cost of Good Sold - All Products', 0, 0, 'L');
        $pdf->Cell(40, 8, '', 0, 0, 'L');
        $pdf->Cell(40, 8, '', 0, 0, 'L');
        $pdf->Cell(40, 8, number_format($cogs[0]->amount, 2), 0, 1, 'R');
        $pdf->Cell(70, 8, 'Master Payment ', 0, 0, 'L');
        $pdf->Cell(40, 8, '', '', 0, 'L');
        $pdf->Cell(50, 8, '', '', 0, 'L');
        $pdf->Cell(30, 8, number_format($master[0]->debit, 2), 'B', 1, 'R');
        $totalCogs = $cogs[0]->amount + $master[0]->debit;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(95, 8, 'Total CoGS', 0, 0, 'L');
        $pdf->Cell(95, 8, number_format($totalCogs, 2), 0, 1, 'R');
        $pdf->Cell(190, 8, '', '', 1); //SPACE
        $gross = $totalRevenue - $totalCogs;
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(95, 10, "GROSS PROFIT", 0, 0, 'L', 1); //your cell
        $pdf->Cell(95, 10, "Rs. " . number_format($gross, 2), 0, 1, 'R', 1); //your cell
        $pdf->Cell(190, 8, '', '', 1); //SPACE
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(230, 230, 230);
        $pdf->SetTextColor(0, 0, 0); // Set Text Color
        $pdf->Cell(190, 8, 'EXPENSES', 0, 1, 'L', 1);
        $pdf->SetFont('Arial', '', 10);
        $expenseSum = 0;
        foreach ($expense as $key => $value) {
            # code...
            $expenseSum = $expenseSum + $value->balance;
            $pdf->Cell(70, 8, $value->expense_category, 0, 0, 'L');
            $pdf->Cell(40, 8, '', 0, 0, 'L');
            $pdf->Cell(40, 8, '', 0, 0, 'L');
            $pdf->Cell(40, 8, number_format($value->balance, 2), 0, 1, 'R');
        }
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(70, 8, 'Total Expenses', 0, 0, 'L');
        $pdf->Cell(120, 8, number_format($expenseSum, 2), 'T', 1, 'R');
        $net = $gross - $expenseSum;
        $pdf->Cell(190, 8, '', '', 1); //SPACE
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(95, 10, "NET PROFIT", 0, 0, 'L', 1); //your cell
        $pdf->Cell(95, 10, "Rs. " . number_format($net, 2), 0, 1, 'R', 1); //your cell
        //save file
        $pdf->Output('Vendor Payable.pdf', 'I');
    }
    public function profitPanel(Request $request, Vendor $vendor)
    {
        return view("reports.profitandloss");
    }
    public function addCity(Request $request, Vendor $vendor)
    {
        $check = $vendor->citycheck($request->city, $request->country);
        if ($check[0]->count == 1) {
            return response()->json(['message' => 'Name already exist', 'status' => 2, 'data' => $check]);
        } else {
            $items = [
                'country_id' => $request->country,
                'city_name' => $request->city,
            ];
            $result = $vendor->addCity($items);
            if ($result > 0) {
                return response()->json(['message' => 'City added Successfully', 'status' => true]);
            } else {
                return response()->json(['message' => 'Cannot add city', 'status' => false]);
            }
        }
    }
    public function activeVendor(Request $request, Vendor $vendor)
    {
        $result = $vendor->activeVendor($request->id);
        return $result;
    }
    public function adjustment(Request $request, Vendor $vendor)
    {
        $rules = [
            'date' => 'required',
            'debit' => 'required|min:1',
            'credit' => 'required|min:1',
        ];
        $this->validate($request, $rules);
        $lastBalance = $vendor->getLastBalance($request->vendor_id);
        $lastBalance = $lastBalance[0]->balance + $request->debit - $request->credit;
        $ledger = DB::table('vendor_ledger')->insert([
            'vendor_id' => $request->vendor_id,
            'po_no' => '0',
            'total_amount' => '0',
            'debit' => $request->debit,
            'credit' => $request->credit,
            'balance' => $lastBalance,
            'narration' => $request->narration,
            'created_at' => date("Y-m-d", strtotime($request->date)) . " " . date("H:i:s"),
        ]);
        if ($ledger) {
            return redirect()->back();
        }
    }
    public function addVendorProduct(Request $request, inventory $inventory, Vendor $vendor)
    {
        $products = $inventory->getproducts();
        $vendorProducts = $vendor->getVendorsProducts($request->id);
        $vendor = $request->id;
        return view('Vendor.add', compact('products', 'vendor', 'vendorProducts'));
    }
    public function searchVendorProduct(Request $request, inventory $inventory)
    {
        $products = $inventory->searchproducts($request->q);
        return response()->json(array('items' => $products));
    }
    public function saveProduct(Request $request, Vendor $vendor)
    {
        $rules = [
            'product' => 'required|array',
        ];
        $this->validate($request, $rules);
        $vendorID = $vendor->getVendorId($request->vendor);
        foreach ($request->product as $val) {
            $items[] = [
                'vendor_id' => $vendorID[0]->id,
                'product_id' => $val,
            ];
        }
        $result = $vendor->insert_into_vendor_product($items, 1);
        if ($result == 1) {
            return redirect()->back();
        }
    }
    public function inactive(Request $request)
    {
        $result = DB::table("vendor_product")->where("vendor_product_id", $request->id)->update(["status" => 2]);
        return $result;
    }
    public function active(Request $request)
    {
        $result = DB::table("vendor_product")->where("vendor_product_id", $request->id)->update(["status" => 1]);
        return $result;
    }
    public function  getVendorPo(Request $request, Vendor $vendor)
    {
        $po = $vendor->getVendorPurchaseOrders($request->id);
        if (!empty($po)) {
            $name = $vendor->getVendorName($request->id);
            return view('Vendor.polist', compact('po', 'name'));
        } else {
            return view("404");
        }
    }
    public function get_vendor_names(Request $request, Vendor $vendor)
    {
        $result = $vendor->search_by_vendor_name($request->q);
        if ($result) {
            return response()->json(array('items' => $result));
        } else {
            return 0;
        }
    }
    /**
        Vendor Payable
     */
    public function vendor_payable(Request $request, Vendor $vendor)
    {
        $data = array();
        return view("Vendor.vendor-payables-list", compact('data'));
    }
    /**
        Get Customer Due Payment
     */
    public function get_vendor_payable(Request $request, Vendor $vendor)
    {
        ## Read value
        $draw = $request->get('draw');
        $vendor_name = $request->get('vendor_name');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        $filter = array(
            'vendor_name' => $vendor_name,
            'from_date' => $from_date,
            'to_date' => $to_date,
        );
        // Total records
        $totalRecords =  $vendor->getTotalNoOfVendorPayable($columnName, $columnSortOrder, $start, $rowperpage, $searchValue, $filter);
        $totalRecordswithFilter = $vendor->getTotalNoOfVendorPayableWithFilter($columnName, $columnSortOrder, $start, $rowperpage, $searchValue, $filter);
        // Fetch records
        $records = $vendor->VendorPayableDetails($columnName, $columnSortOrder, $start, $rowperpage, $searchValue, $filter);
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $records
        );
        echo json_encode($response);
    }
    public function ledgerPDF(Request $request, Vendor $vendor)
    {
        $vendorid = $vendor->getVendorId($request->id);
        $company = $vendor->company(session('company_id'));
        $name = $vendor->getVendorName($request->id);
        $totalBalance = $vendor->getLastBalance($vendorid[0]->id);
        $result = $vendor->LedgerDetails($request->id, $request->from, $request->to);
        $pdf = new pdfClass();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $qrimage = "qr" . $name[0]->vendor_name . ".png";
        if (!file_exists(public_path('assets/images/vendors/qrcode/' . $qrimage))) {
            $qrcodetext = $name[0]->vendor_contact . " | " . $name[0]->user_id . " | 1"; //mode 1 for vendor
            \QrCode::size(200)
                ->format('png')
                ->generate($qrcodetext, public_path('assets/images/vendors/qrcode/' . $qrimage));
        }
        //first row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Cell(65, 0, "Company Name:", 0, 0, 'L');
        $pdf->Cell(45, 0, "Vendor Name", 0, 1, 'L');
        $pdf->Cell(30, 0, "", 0, 1, 'L');
        //second row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Image(public_path('assets/images/company/' . $company[0]->logo), 12, 10, -200);
        $pdf->Cell(65, 12, $company[0]->name, 0, 0, 'L');
        $pdf->Cell(45, 12, $name[0]->vendor_name, 0, 0, 'L');
        $pdf->Cell(30, 0, "", 0, 1, 'R');
        $pdf->Image(public_path('assets/images/vendors/qrcode/' . $qrimage), 175, 10, -200);
        //third row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 25, '', 0, 0);
        $pdf->Cell(65, 25, "Contact Number:", 0, 0, 'L');
        $pdf->Cell(45, 25, "Contact Number:", 0, 0, 'L');
        $pdf->Cell(30, 25, "", 0, 1, 'L');
        //forth row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, -15, '', 0, 0);
        $pdf->Cell(65, -15, $company[0]->ptcl_contact, 0, 0, 'L');
        $pdf->Cell(45, -15, $name[0]->vendor_contact, 0, 0, 'L');
        $pdf->Cell(30, -15, "", 0, 1, 'L');
        //fifth row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 28, '', 0, 0);
        $pdf->Cell(105, 28, "Company Address:", 0, 0, 'L');
        $pdf->Cell(50, 28, "", 0, 1, 'L');
        //sixth row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, -18, '', 0, 0);
        $pdf->Cell(105, -18, $company[0]->address, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, -18, "Generate Date:  " . date('Y-m-d'), 0, 1, 'R');
        //report name
        $pdf->ln(15);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(190, 10, 'Ledger Report', 'B,T', 1, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(10, 8, 'Sr.', 'B', 0, 'L');
        $pdf->Cell(25, 8, 'Date', 'B', 0, 'L');
        $pdf->Cell(25, 8, 'Debit', 'B', 0, 'R');
        $pdf->Cell(25, 8, 'Credit', 'B', 0, 'R');
        $pdf->Cell(25, 8, 'Balance', 'B', 0, 'R');
        $pdf->Cell(80, 8, 'Narration', 'B', 1, 'C');
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
            $pdf->Cell(25, 6, date("d F Y", strtotime($value->created_at)), 0, 0, 'L', 1);
            $pdf->Cell(25, 6, number_format($value->debit, 2), 0, 0, 'R', 1);
            $pdf->Cell(25, 6, number_format($value->credit, 2), 0, 0, 'R', 1);
            $pdf->Cell(25, 6, number_format($value->balance, 2), 0, 0, 'R', 1);
            $pdf->cell(80, 6, $value->narration, 0, 1, 'C', 1);
        }
        $pdf->ln();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(130, 8, '', 0, 0, 'R');
        $pdf->Cell(20, 8, 'Total:', 'T,B', 0, 'R');
        $pdf->Cell(40, 8, "Rs. " . number_format($totalBalance[0]->balance, 2), 'T,B', 1, 'R');
        //save file
        $pdf->Output('Vendor Ledger' . $name[0]->vendor_name . '.pdf', 'I');
    }
    public function editvendornarration(Request $request, Vendor $vendor)
    {
        $items = [
            'narration' => $request->narration,
        ];
        $result = $vendor->update_vendor_narration($request->id, $items);
        return 1;
    }
    public function VendorPaymentView()
    {
        $vendors = Vendors::Company()->get();
        return view('Vendor.vendor-payment', compact('vendors'));
    }
    public function getVendorPayment(Request $request)
    {
        $from = "";
        $to = "";
        if ($request->from != "" && $request->to != "") {
            $from = $request->from;
            $to = $request->to;
        }
        if ($request->mode == "today") {
            $from = date("Y-m-d");
            $to = date("Y-m-d");
        }
        $result =  VendorPurchase::with("vendorpurchases:purchase_id,po_no,branch_id,order_date,delivery_date,payment_date,vendor_id,date,time", "vendorpurchases.vendor:id,vendor_name,address", "vendorpurchases.purchaseAccount:purchase_id,total_amount,balance_amount")
            ->whereHas('vendorpurchases', function ($q) use ($from, $to, $request) {
                $q->whereNotNull('payment_date')
                    ->where("branch_id", session("branch"))
                    ->when($from != "" && $to != "", function ($q) use ($from, $to) {
                        $q->whereBetween("payment_date", [$from, $to]);
                    })
                    ->when($request->vendor != "", function ($q) use ($request) {
                        $q->where("vendor_id", $request->vendor);
                    });
            })
            ->whereHas('vendorpurchases.purchaseAccount', function ($q) use ($request) {
                $q->where("purchase_id", ">", 0)
                    ->when($request->mode == "all", function ($q) {
                        $q->where("balance_amount", ">", 0);
                    })
                    ->when($request->mode == "today", function ($q) {
                        $q->where("balance_amount", ">", 0);
                    })
                    ->when($request->mode == "clear", function ($q) {
                        $q->where("balance_amount", "=", 0);
                    });
            })
            ->where("status", 1)->orderBy("created_at", "desc")->paginate(10);
        return view("partials.vendor-payment-orders", compact('result'));
    }
    public function updateVendorPaymentDueDate(Request $request)
    {
        try {
            $purchase = Purchase::findOrFail($request->id);
            $purchase->payment_date = $request->date;
            $purchase->save();
            return response()->json(["status" => 200, "message" => "success"]);
        } catch (Exception $e) {
            return response()->json(["status" => 404, "message" => "Error :" . $e->getMessage()]);
        }
    }
    public function adavancePaymentView(Request $request)
    {
        $vendor = Vendors::find($request->id);
        $advanceBalance =  VendorAdvance::where("vendor_id", $request->id)->select(DB::raw("SUM(credit)-SUM(debit) as balance"))->get();
        $balance = (!empty($advanceBalance) ? $advanceBalance[0]->balance : 0);
        return view('Vendor.vendor-advance-payment', compact('vendor', 'balance'));
    }
    public function saveAdvancePayment(Request $request)
    {
        $rules = [
            'date' => 'required',
            'debit' => 'required',
            'credit' => 'required',
        ];
        $this->validate($request, $rules);
        $advance = VendorAdvance::create([
            "vendor_id" => $request->vendor_id,
            "debit" => $request->debit,
            "credit" => $request->credit,
            "narration" => $request->narration,
        ]);
        if (!empty($advance)) {
            if ($request->ajax()) {
                return 1;
            } else {
                return \Redirect::route('advance-payment-view', [$request->vendor_id]);
            }
        } else {
            if ($request->ajax()) {
                return 0;
            } else {
                return \Redirect::route('advance-payment-view', [$request->vendor_id]);
            }
        }
    }
    public function getAdvancePayments(Request $request)
    {
        $payments = VendorAdvance::where("vendor_id", $request->id)->latest()->paginate(10);
        return view("partials.vendor-advance-payment-table", compact('payments'));
    }
    public function saveProductFromPurchaseOrder(Request $request, Vendor $vendor)
    {
        foreach ($request->products as $val) {
            $check = $vendor->check_vendor_product($request->vendor, $val);
            if ($check == 0) {
                $items[] = [
                    'vendor_id' => $request->vendor,
                    'product_id' => $val,
                ];
            }
        }
        $result = (!empty($items) ? $vendor->insert_into_vendor_product($items, 1) : 0);
        if ($result == 1) {
            return true;
        } else {
            return false;
        }
    }
    public function vendorPaymentHistory(Request $request)
    {
        // $request->id = 328;
        $rules = [
            'id' => 'required',
        ];
        $this->validate($request, $rules);
        $history = VendorLedger::where("vendor_id", $request->id)->where("debit", ">", "0")->orderBy("created_at", "DESC")->get();
        return view("partials.vendor_payment_history", compact('history'));
    }
    public function getVendorsByProduct(Request $request, Vendor $vendor)
    {
        if ($request->id != "") {
            $products = $vendor->get_product_by_vendor($request->id);
            return response()->json(["status" => 200, "products" => $products]);
        } else {
            return response()->json(["status" => 500, "products" => ""]);
        }
    }
}
