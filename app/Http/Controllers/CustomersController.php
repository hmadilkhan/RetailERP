<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\order;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Session;
use App\Vendor;
use App\bank;
use App\pdfClass;
use Illuminate\Support\Str;
use App\Helpers\custom_helper;
use App\inventory;
use App\Models\City;
use App\Models\Country;
use App\Models\Customer as ModelsCustomer;
use App\Models\QuickBookSetting;
use App\Services\QuickBooks\QuickBooksCustomerService;
use App\Traits\MediaTrait;
use Illuminate\Support\Facades\Storage;

class CustomersController extends Controller
{
    use MediaTrait;

    protected $quickBooksService;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(QuickBooksCustomerService $quickBooksService)
    {
        $this->middleware('auth');
        $this->quickBooksService = $quickBooksService;
    }
    public function index(Customer $customer)
    {
        $details = $customer->getcustomers();
        return view('customer.lists', compact('details'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Customer $customer)
    {
        $country = $customer->getcountry();
        $city = $customer->getcity();
        return view('customer.create', compact('country', 'city'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Customer $customer, custom_helper $helper)
    {
        $file = [];
        $imageName = "";
        $rules = [
            'name' => 'required',
            'mobile' => 'required|size:11|regex:/[0-9]{9}/',
            'address'  => 'required',
            'country' => 'required',
            'city' => 'required',
        ];
        $this->validate($request, $rules);
        if (!empty($request->vdimg)) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1048',
            ]);
            $file = $this->uploads($request->vdimg, 'images/customers/');
        }
        $items = [
            'user_id' => session('userid'),
            'status_id' => 1,
            'country_id' => $request->country,
            'city_id' => $request->city,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'phone' => $request->phone,
            'nic' => $request->nic,
            'address' => $request->address,
            'image' => $file["fileName"] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'credit_limit' => $request->creditlimit,
            'discount' => $request->discount,
            'email' => $request->email,
            'slug' => strtolower(Str::random(4)),
            'company_id' => session('company_id'),
            'customer_area' => $request->get('customer_area'),
            'customer_type' => $request->get('customer_type'),
            'payment_type' => $request->get('payment_type'),
        ];
        $cust = $customer->insert_customer($items);
        // QUICKBOOK DATA
        $check = QuickBookSetting::where('company_id', session('company_id'))->count();
        if ($check > 0) {
            $country = Country::findOrFail($request->country);
            $city = City::findOrFail($request->city);
            $qbCustomer = [
                'GivenName' =>  $request->name,
                'DisplayName' =>  $request->name,
                'PrimaryEmailAddr' => [
                    'Address' => $request->email
                ],
                'BillAddr' => [
                    'Line1' => $request->address,
                    'City' => $city->city_name,
                    'Country' => $country->country_name,
                ],
                'PrimaryPhone' => [
                    'FreeFormNumber' => $request->phone
                ]
            ];

            // Then, add the customer to QuickBooks
            $qbResponse = $this->quickBooksService->createCustomer($qbCustomer);

            if (isset($qbResponse->Id)) {
                Customer::where("id", $cust)->update(["qb_customer_id" => $qbResponse->Id]);
                // return response()->json(['success' => false, 'message' => $qbResponse['message']], 400);
            }
        }
        /* Service Provide bulk insertion */
        $arrData = array();
        $comment = $request->get('comment');
        $street = $request->get('street_address');
        $area = $request->get('area');
        if ($area) {
            foreach ($area as $key => $n) {
                $arrData[] = array("area" => $area[$key], "street" => $street[$key], "comment" => $comment[$key], "customer_id" => $cust);
            }
            DB::table('customer_supplier_detail')->insert($arrData);
        }
        $bal = $customer->getCustomerLastBalance($cust);
        $balance = (!empty($bal)  ? $bal[0]->balance - $request->ob : $request->ob);
        $fields = [
            'cust_id' => $cust,
            'receipt_no' => 0,
            'total_amount' => 0,
            'debit' => ($request->ob < 0 ? ($request->ob * (-1)) : 0),
            'credit' => ($request->ob > 0 ? $request->ob : 0),
            'balance' => $balance,
            'narration' => "Opening Balance",
            'terminal_id' => 1,
            'payment_mode_id' => 1,
            'received' => 0,
            'opening_id' => 1,
        ];
        $ledger = $customer->insert_into_ledger($fields);
        $msg = "ID" . $cust;
        $helper->sendPushNotification("New Customer Added", $msg, $request->name);
        return $this->index($customer);
    }
    public function getCity(Request $request)
    {
        $city = DB::table('city')->where('country_id', $request->id)->get();
        return $city;
    }
    public function edit(Request $request, Customer $customer)
    {
        $details = $customer->customers($request->id);
        $supplier = $customer->customer_supplier($details[0]->id);
        $country = $customer->getcountry();
        $city = $customer->getcity();
        if (count($details) > 0) {
            return view('customer.edit', compact('country', 'city', 'details', 'supplier'));
        } else {
            return view("404");
        }
    }
    public function update(Request $request, Customer $customer, custom_helper $helper)
    {
        $imageName = "";
        $rules = [
            'name' => 'required',
            'mobile' => 'required|size:11|regex:/[0-9]{9}/',
            'address'  => 'required',
            'country' => 'required',
            'city' => 'required',
        ];
        $this->validate($request, $rules);
        if (!empty($request->vdimg)) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);
            $file = $this->uploads($request->vdimg, 'images/customers/', $request->custimage);
        } else {
            $imageName = $request->custimage;
        }
        $items = [
            'user_id' => session('userid'),
            'status_id' => 1,
            'country_id' => $request->country,
            'city_id' => $request->city,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'phone' => $request->phone,
            'nic' => $request->nic,
            'address' => $request->address,
            'image' => (!empty($request->vdimg) ? $file["fileName"] : $imageName),
            'created_at' => $request->created_at,
            'updated_at' => date('Y-m-d H:i:s'),
            'credit_limit' => $request->creditlimit,
            'discount' => $request->discount,
            'email' => $request->email,
            'company_id' => session('company_id'),
            'customer_area' => $request->get('customer_area'),
            'customer_type' => $request->get('customer_type'),
            'payment_type' => $request->get('payment_type'),
        ];
        // dd($items);
        $cust = $customer->update_customer($request->custid, $items);
        /* Service Provide bulk insertion */
        $arrData = array();
        $comment = $request->get('comment');
        $street = $request->get('street_address');
        $area = $request->get('area');
        if ($area) {
            foreach ($area as $key => $n) {
                $arrData[] = array("area" => $area[$key], "street" => $street[$key], "comment" => $comment[$key], "customer_id" => $request->custid);
            }
            DB::table('customer_supplier_detail')->insert($arrData);
        }
        $msg = "ID" . $request->custid;
        $helper->sendPushNotification("Customer Updated", $msg, $request->name);
        return $this->index($customer);
    }
    public function remove(Request $request, Customer $customer)
    {
        $customer = ModelsCustomer::findOrFail($request->id);
        $result = $customer->remove_customer($request->id);
        if ($result) {
            $this->removeImage("images/customers/", $customer->image);
        }
        return 1;
    }
    public function LedgerDetails(Request $request, Customer $customer)
    {
        $details = $customer->LedgerDetailsShow($request->id);
        $customername = $customer->getCustName($request->id);
        $cust = $customer->getCustId($request->id);
        $customerID = $cust[0]->id;
        $slug = $request->id;
        if ($customername) {
            return view('customer.ledger', compact('details', 'customername', 'customerID', 'slug'));
        } else {
            return view("404");
        }
        // $customers = $customer->getcustomers();
        // return view('Accounts.customer-ledger', compact('customers'));
    }
    public function LedgerDetailsByID(Request $request, Customer $customer)
    {
        $details = $customer->LedgerDetailsShow($request->id);
        return $details;
    }
    public function getReceiptGeneral(Request $request, Order $order)
    {
        $orderGeneral = $order->getReceiptGeneral($request->receiptID);
        return $orderGeneral;
    }
    public function discountPanel(Request $request, Customer $customer)
    {
        $cust = $customer->getCustId($request->id);
        if ($cust != 0) {
            $customerID = $cust[0]->id;
            $customer_name = $cust[0]->name;
            return view('customer.discount', compact('customerID', 'customer_name'));
        } else {
            return view("404");
        }
    }
    public function discountInsert(Request $request, Customer $customer)
    {
        $fields = [
            'cust_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'discount' => $request->discount,
            'date' => date("Y-m-d"),
            'time' => date("H:i:s"),
            'status_id' => 1,
        ];
        $count = $customer->discount_exists_check($request->product_id, $request->customer_id);
        if ($count == 0) {
            $result = $customer->discount_insert($fields);
            return $result;
        } else {
            return 0;
        }
    }
    public function loadDiscount(Request $request, Customer $customer)
    {
        $result = $customer->getDiscount($request->id);
        return $result;
    }
    public function getProducts(Request $request, Customer $customer)
    {
        $result = $customer->getProducts($request->dept, $request->subdept);
        return $result;
    }
    public function discountUpdate(Request $request, Customer $customer)
    {
        $fields = [
            'cust_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'discount' => $request->discount,
            'time' => date("h:i:s"),
            'status_id' => 1,
        ];
        $count = $customer->getActiveProduct($request->product_id);
        if ($count == 1) {
            $result = $customer->discount_insert($fields);
            return 1;
        } else {
            return 0;
        }
    }
    public function deleteProduct(Request $request, Customer $customer)
    {
        $count = $customer->deleteProduct($request->id);
        if ($count == 1) {
            return 1;
        } else {
            return 0;
        }
    }
    public function customer_report(Request $request, Customer $customer)
    {
        $master = $customer->getcustomers();
        $details = $customer->getCustomerReport($request->customer, $request->paymentType);
        return view('reports.customer', compact('master', 'details'));
    }
    public function customer_report_filter(Request $request, Customer $customer)
    {
        $details = $customer->getCustomerReport($request->customer, $request->paymentType);
        return $details;
    }
    public function createMeasurement(Request $request, Customer $customer)
    {
        $shalwarQameez = [
            'customer_id' => $request->id,
            'chest' => '0',
            'waist' => '0',
            'abdomen' => '0',
            'hips' => '0',
            'shoulder' => '0',
            'sleeves' => '0',
            'neck' => '0',
            'kurta_length' => '0',
            'shirt_length' => '0',
            'jacket_length' => '0',
            'sherwani' => '0',
            'pentshalwar' => '0',
            'arm_hole' => '0',
            'bicep' => '0',
            'wc_length' => '0',
        ];
        $pentShirt = [
            'customer_id' => $request->id,
            'waist' => '0',
            'hip' => '0',
            'thy' => '0',
            'knee' => '0',
            'caff' => '0',
            'fly' => '0',
            'length' => '0',
            'bottom' => '0',
        ];
        $shalwar = $customer->insertIntoShalwar($shalwarQameez, $request->id);
        $pant = $customer->insertIntoPant($pentShirt, $request->id);
        if ($shalwar == 0) {
            $shalwar = $customer->getShalwarData($request->id);
        }
        if ($pant == 0) {
            $pant = $customer->getPantData($request->id);
        }
        $customers = $request->id;
        $customerName = $customer->getCustName($request->id);
        return view('customer.naap', compact('customers', 'shalwar', 'pant', 'customerName'));
    }
    public function updatePantMeasurement(Request $request, Customer $customer)
    {
        $items = [
            'waist' => $request->waist,
            'hip' => $request->hip,
            'thy' => $request->thy,
            'knee' => $request->knee,
            'caff' => $request->caff,
            'fly' => $request->fly,
            'length' => $request->length,
            'bottom' => $request->bottom,
        ];
        $result = $customer->updatePantMeasurement($items, $request->customer);
        return $result;
    }
    public function measurementUpdate(Request $request, Customer $customer)
    {
        $items = [
            'chest' => $request->chest,
            'waist' => $request->waist,
            'abdomen' => $request->abdomen,
            'hips' => $request->hips,
            'shoulder' => $request->shoulder,
            'sleeves' => $request->sleeves,
            'neck' => $request->neck,
            'kurta_length' => $request->kurta,
            'shirt_length' => $request->shirt,
            'jacket_length' => $request->jacket,
            'sherwani' => $request->sherwani,
            'pentshalwar' => $request->pentshalwar,
            'arm_hole' => $request->arm_hole,
            'bicep' => $request->bicep,
            'wc_length' => $request->wc_length,
        ];
        $result = $customer->updateMeasurement($items, $request->customer);
        return $result;
    }
    public function exportpDF(Request $request, Customer $customer, Vendor $vendor)
    {
        $totalBalance = 0;
        $company = $vendor->company(session('company_id'));
        $result = $customer->getCustomerReport($request->customer, $request->first, $request->second);
        if (!file_exists(public_path('assets/images/company/qrcode.png'))) {
            $qrcodetext = $company[0]->name . " | " . $company[0]->ptcl_contact . " | " . $company[0]->address;
            \QrCode::size(200)
                ->format('png')
                ->generate($qrcodetext, public_path('assets/images/company/qrcode.png'));
        }
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
        $pdf->Cell(190, 10, 'Customer Receivable', 'B,T', 1, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(20, 8, 'Sr.', 'B', 0, 'L');
        $pdf->Cell(90, 8, 'Customer Name', 'B', 0, 'L');
        $pdf->Cell(40, 8, 'Contact', 'B', 0, 'L');
        $pdf->Cell(40, 8, 'Balance', 'B', 1, 'R');
        foreach ($result as $key => $value) {
            if ($value->balance  > 0) {
                $totalBalance = ($totalBalance + $value->balance);
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(20, 6, $key + 1, 0, 0, 'L');
                $pdf->Cell(90, 6, $value->name, 0, 0, 'L');
                $pdf->Cell(40, 6, $value->mobile, 0, 0, 'L');
                $pdf->Cell(40, 6, number_format($value->balance, 2), 0, 1, 'R');
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
        $pdf->Output('Customer Receivable.pdf', 'I');
    }
    //Upload CSV FILE CODE
    public function uploadFile(Request $request, Customer $customer)
    {
        if ($request->input('submit') != null) {
            $file = $request->file('file');
            // File Details
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();
            // Valid File Extensions
            $valid_extension = array("csv");
            // 2MB in Bytes
            $maxFileSize = 2097152;
            // Check file extension
            if (in_array(strtolower($extension), $valid_extension)) {
                // Check file size
                if ($fileSize <= $maxFileSize) {
                    // File upload location
                    $location = 'uploads';
                    // Upload file
                    $file->move(public_path('assets/uploads/'), $filename);
                    // Import CSV to Database
                    $filepath = public_path("assets/uploads" . "/" . $filename);
                    // Reading file
                    $file = fopen($filepath, "r");
                    $importData_arr = array();
                    $i = 0;
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if ($i == 0) {
                            $i++;
                            continue;
                        }
                        for ($c = 0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata[$c];
                        }
                        $i++;
                    }
                    fclose($file);
                    // Insert to MySQL database
                    foreach ($importData_arr as $importData) {
                        $insertData = array(
                            // "username"=>  preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $importData[0]),
                            // "name"=>$importData[1],
                            // "gender"=>$importData[2],
                            // "email"=>$importData[3]);
                            'user_id' => session('userid'),
                            'branch_id' => session('branch'),
                            'status_id' => 1,
                            'country_id' => 170,
                            'city_id' => 1,
                            'name' => preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $importData[0]),
                            'mobile' => $importData[1],
                            'phone' => '',
                            'nic' => $importData[2],
                            'address' => $importData[3],
                            'image' => '',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'credit_limit' => $importData[4],
                            'discount' => $importData[5],
                            'email' => $importData[6],
                            'slug' => strtolower(Str::random(4))
                        );
                        $customer->insert_customer($insertData);
                    }
                    Session::flash('message', '1');
                } else {
                    Session::flash('message', '2');
                }
            } else {
                Session::flash('message', '3');
            }
        }
        // Redirect to index
        return redirect()->action('CustomersController@index');
    }
    public function all_customers_remove(Request $request, Customer $customer)
    {
        $result = $customer->update_all_customers_status($request->customerid, $request->statusid);
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }
    public function activeCustomer(Request $request, Customer $customer)
    {
        $result = $customer->active_customer($request->id);
        return $result;
    }
    public function multipleactiveCustomer(Request $request, Customer $customer)
    {
        $result = $customer->multiple_active_customer($request->id);
        return $result;
    }
    public function get_names(Request $request, Customer $customer)
    {
        $result = $customer->item_name($request->ids);
        if ($result) {
            return $result;
        } else {
            return 0;
        }
    }
    public function get_customer_names(Request $request, Customer $customer)
    {
        $result = $customer->search_by_customer_name($request->q, $request->branch);
        if ($result) {
            return response()->json(array('items' => $result));
        } else {
            return 0;
        }
    }
    public function createPayment(Request $request, Customer $customer, bank $bank)
    {
        $receipts = $customer->getReceipts($request->id);
        $customerName = $customer->getCustName($request->id, true);
        $customerAdvance = $customer->getCustAdvance($request->id);
        $balance = $customer->getBalance($request->id);
        $slug = $customer->getSlug($request->id);
        $customer = $request->id;
        $banks = $bank->getbankAccounts();
        return view("customer.Createpayment", compact('customerAdvance', 'customerName', 'customer', 'receipts', 'balance', 'banks', 'slug'));
    }
    public function make_cash_payment(Request $request, Vendor $vendor, Customer $customer, bank $bank)
    {
        // $currentBalance = $request->curBal - $request->amount;
        $bal = $customer->getCustomerLastBalance($request->customer);
        $balance = (!empty($bal)  ? $bal[0]->balance - $request->payment : $request->payment);
        $fields = [
            'cust_id' => $request->customer,
            'receipt_no' => ($request->id != 0 ? $request->id : 0),
            'total_amount' => 0,
            'debit' => 0,
            'credit' => $request->payment,
            'balance' => abs($balance),
            'narration' => $request->narration,
            'terminal_id' => 1,
            'payment_mode_id' => 1,
            'received' => 1,
            'opening_id' => 1,
        ];
        $ledger = 1;
        if (isset($request->ledgerEntries) && $request->ledgerEntries == 'true') {
            $ledger = $customer->insert_into_ledger($fields);
        }
        $sales_general = $customer->get_sales_account_general($request->id);
        $balance = (!empty($sales_general) ? $sales_general[0]->balance_amount : 0) - $request->amount;
        $status = ($balance == 0 ? 1 : 0);
        $account = $customer->sales_account_update($request->id, $balance, $status);
        $payment_fields = ['user_id' => session('userid'), 'cust_id' => $request->customer, 'receipt_id' => ($request->id != 0 ? $request->id : 0), 'payment_received' => $request->amount, 'narration' => $request->narration];
        $payment_log = $customer->customer_payment_log($payment_fields);
        //            //INSERT INTO PAYMENT GENERAL
        //            $items = [
        //                'bankid' =>0,
        //                'cheque' =>0,
        //                'payment' =>$request->amount,
        //                'narration' =>$request->narration,
        //            ];
        //            $payment = $vendor->insert_into_bank_details_for_vendor($items);
        //
        //            //INSERT INTO PAYMENT DETAILS
        //            $items = [
        //                'payment_id' =>$payment,
        //                'account_id' =>$ledger,
        //            ];
        //            $paymentDetails = $vendor->vendor_payment_details($items);
        //INSERT INTO CASH LEDGER
        $balanceStock = $bank->getLastCashBalance();
        $balance = isset($balanceStock[0]->balance) ? $balanceStock[0]->balance - $request->amount : $request->curBal - $request->amount;
        $items = [
            'branch_id' => session('branch'),
            'date' => date("Y-m-d"),
            'debit' => 0,
            'credit' => $request->amount,
            'balance' => $balance,
            'narration' => "Payment received from Customer " . $request->customerName,
        ];
        $result = $bank->insert_bankdetails('cash_ledger', $items);
        return $ledger;
    }
    /**
        Customer Due Payment
     */
    public function customer_due_payment(Request $request, Customer $customer)
    {
        $data = array();
        return view("customer.customer-due-payment-list", compact('data'));
    }
    /**
        Get Customer Due Payment
     */
    public function get_customer_due_payment(Request $request, Customer $customer)
    {
        ## Read value
        $draw = $request->get('draw');
        $customer_name = $request->get('customer_name');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $type = $request->get('type');
        $payment_type = $request->get('payment_type');
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
            'customer_name' => $customer_name,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'type' => $type,
            'payment_type' => $payment_type,
        );
        // Total records
        $totalRecords =  $customer->getTotalNoOfCustomerDuePayment($columnName, $columnSortOrder, $start, $rowperpage, $searchValue, $filter);
        $totalRecordswithFilter = $customer->getTotalNoOfCustomerDuePaymentWithFilter($columnName, $columnSortOrder, $start, $rowperpage, $searchValue, $filter);
        // Fetch records
        $records = $customer->customerDuePaymentDetails($columnName, $columnSortOrder, $start, $rowperpage, $searchValue, $filter);
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $records,
            "filter" => $filter,
        );
        echo json_encode($response);
    }
    public function make_bank_payment(Request $request, Vendor $vendor, Customer $customer, bank $bank)
    {
        $bal = $customer->getCustomerLastBalance($request->customer);
        $balance = (!empty($bal)  ? $bal[0]->balance - $request->payment : $request->payment);
        $fields = [
            'cust_id' => $request->customer,
            'receipt_no' => ($request->id != 0 ? $request->id : 0),
            'total_amount' => 0,
            'debit' => $request->amount,
            'credit' => 0,
            'balance' => $balance,
            'narration' => $request->narration,
            'terminal_id' => 1,
            'payment_mode_id' => 2,
            'received' => 1,
            'opening_id' => 1,
        ];
        $ledger = $customer->insert_into_ledger($fields);
        if ($ledger > 0) {
            $sales_general = $customer->get_sales_account_general($request->id);
            $balance = (!empty($sales_general) ? $sales_general[0]->balance_amount : 0) - $request->amount;
            $status = ($balance == 0 ? 1 : 0);
            $account = $customer->sales_account_update($request->id, $balance, $status);
            $bal = $bank->getLastBalance($request->accountid);
            $balance = (!empty($bal)  ? $bal[0]->balance : 0) + $request->amount;
            //INSERT INTO BANK LEDGER
            $items = [
                'bank_account_id' => $request->accountid,
                'cheque_number' => $request->cheque_number,
                'cheque_date' => date("Y-m-d"),
                'debit' => 0,
                'credit' => $request->amount,
                'balance' => $balance,
                'narration' => "Payment Received from customer " . $request->customerName,
                'mode' =>  'Cheque'
            ];
            $result = $bank->insert_bankdetails('bank_deposit_details', $items);
            return $ledger;
        } else {
            return 0;
        }
    }
    public function adjustment(Request $request, Customer $customer)
    {
        $lastBalance = 0;
        $curBalance = 0;
        $rules = [
            'date' => 'required',
            'debit' => 'required|min:1',
            'credit' => 'required|min:1',
        ];
        $this->validate($request, $rules);
        $bal = $customer->getCustomerLastBalance($request->customer);
        $balance =  isset($bal[0]->balance) ? $bal[0]->balance : 0 + $request->credit - $request->debit;
        $received = ($request->debit > 0 ? 1 : 0);
        $fields = [
            'cust_id' => $request->customer,
            'receipt_no' => ($request->id != 0 ? $request->id : 0),
            'total_amount' => 0,
            'debit' => $request->debit,
            'credit' => $request->credit,
            'balance' => $balance,
            'narration' => $request->narration,
            'terminal_id' => 1,
            'payment_mode_id' => 1,
            'received' => 0,
            'opening_id' => 1,
        ];
        $ledger = $customer->insert_into_ledger($fields);
        if ($ledger) {
            return redirect()->back();
        }
    }
    public function editAdjustment(Request $request, Customer $customer)
    {
        $rules = [
            'debit' => 'required|min:1',
            'credit' => 'required|min:1',
        ];
        $validator = Validator::make(request()->all(), $rules);
        if ($request->isMethod('post') && $request->ajax()) {
            if ($validator->fails()) {
                return response(['status' => "false", 'message' => 'Please enter to debit or credit value']);
            }
            $bal = $customer->getCustomerLastBalance($request->cust_id, $request->cust_account_id);
            $balance =  $bal[0]->balance + $request->credit - $request->debit;
            // echo $balance;exit;
            $received = ($request->debit > 0 ? 1 : 0);
            $fields = [
                'debit' => $request->debit,
                'credit' => $request->credit,
                'balance' => $balance,
                'narration' => $request->narration,
            ];
            $ledger = $customer->update_into_ledger($request->cust_account_id, $fields);
            return response(['status' => "true", 'message' => 'Manual adjustment updated.']);
        }
    }
    public function customer_due_date(Request $request, Customer $customer)
    {
        $rules = [
            'due_date' => 'required',
        ];
        $validator = Validator::make(request()->all(), $rules);
        if ($request->isMethod('post') && $request->ajax()) {
            if ($validator->fails()) {
                return response(['status' => "false", 'message' => 'Please enter to due date.']);
            }
            $receipts = $customer->checkReceiptID($request->cust_receipt_id);
            if ($receipts == true) {
                $customer->dueDateUpdate($request->due_date, $request->cust_receipt_id);
                return response(['status' => "true", 'message' => 'Due Date updated.']);
            }
            return response(['status' => "false", 'message' => 'There is issue contact with Administrator.']);
        }
    }
    public function customer_payment_log(Request $request, Customer $customer)
    {
        if ($request->isMethod('post') && $request->ajax()) {
            $data = $customer->getAllPaymentLogByReceiptID($request->receipt_no);
            $returnHTML = view('customer.paymentGrid')->with('data', $data)->render();
            echo  $returnHTML;
            exit;
            return response(['status' => "true", 'html' => $returnHTML]);
        }
    }
    public function getCustomerReceipts(Request $request, Customer $customer)
    {
        $receipts = $customer->getOrders($request->id);
        $customer = $customer->getCustId($request->id);
        if ($customer != 0) {
            return view("customer.invoicelist", compact('receipts', 'customer'));
        } else {
            return view("404");
        }
    }
    public function customerPDF(Request $request, Vendor $vendor, Customer $customer)
    {
        $company = $vendor->company(session('company_id'));
        $details = $customer->getcustomers("", $request->branch, $request->name, $request->contact, $request->membership);
        $branch = DB::table("branch")->where("branch_id", session("branch"))->get();
        $pdf = new pdfClass();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        // $qrimage = "qr" . $name[0]->name . ".png";
        // if (!file_exists(public_path('assets/images/customers/qrcode/' . $qrimage))) {
        // $qrcodetext = $name[0]->mobile . " | " . $name[0]->user_id . " | 2"; //mode 2 for customer (or bt suno yeh company id nh USER ID HA) :-)
        // \QrCode::size(200)
        // ->format('png')
        // ->generate($qrcodetext, public_path('assets/images/customers/qrcode/' . $qrimage));
        // }
        //first row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Cell(110, 0, "Company Name:", 0, 0, 'L');
        // $pdf->Cell(45, 0, "Customer Name:", 0, 1, 'L');
        $pdf->Cell(30, 0, "", 0, 1, 'L');
        //second row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Image(asset('storage/images/company/' . $company[0]->logo), 12, 10, -200);
        $pdf->Cell(110, 12, $company[0]->name, 0, 0, 'L');
        // $pdf->Cell(45, 12, $urduname, 0, 0, 'L');
        $pdf->Cell(30, 0, "", 0, 1, 'R');
        // $pdf->Image(public_path('assets/images/customers/qrcode/' . $qrimage), 175, 10, -200);
        //third row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 25, '', 0, 0);
        $pdf->Cell(110, 25, "Contact Number:", 0, 0, 'L');
        // $pdf->Cell(45, 25, "Contact Number:", 0, 0, 'L');
        $pdf->Cell(30, 25, "", 0, 1, 'L');
        //forth row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, -15, '', 0, 0);
        $pdf->Cell(110, -15, $company[0]->ptcl_contact, 0, 0, 'L');
        // $pdf->Cell(45, -15, $name[0]->mobile, 0, 0, 'L');
        $pdf->Cell(30, -15, "", 0, 1, 'L');
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
        $pdf->Cell(190, 10, 'Customers Report (' . (session("roleId") != 2 ? $branch[0]->branch_name : "") . ')', 'B,T', 1, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(10, 8, 'Sr.', 'B', 0, 'L');
        $pdf->Cell(80, 8, 'Name', 'B', 0, 'L');
        $pdf->Cell(25, 8, 'Branch', 'B', 0, 'L');
        $pdf->Cell(25, 8, 'Balance', 'B', 0, 'L');
        $pdf->Cell(25, 8, 'Mobile', 'B', 0, 'L');
        $pdf->Cell(25, 8, 'CNIC', 'B', 1, 'L');
        $pdf->SetFont('Arial', '', 9);
        foreach ($details as $key => $value) {

            $pdf->Cell(10, 5, $key + 1, 0, 0, 'L', 0);
            $pdf->Cell(80, 5, $value->name, 0, 0, 'L', 0);
            $pdf->Cell(25, 5, $value->branch_name, 0, 0, 'L', 0);
            $pdf->Cell(25, 5, number_format($value->balance, 2), 0, 0, 'L', 0);
            $pdf->Cell(25, 5, $value->mobile, 0, 0, 'L', 0);
            $pdf->Cell(28, 5, $value->nic, 0, 1, 'L', 0);
        }
        //save file
        $pdf->Output('Customer Report' . '.pdf', 'I');
    }
    public function ledgerPDF(Request $request, Vendor $vendor, Customer $customer)
    {
        $company = $vendor->company(session('company_id'));
        $urduname = $customer->getCustName($request->id);
        $name = $customer->getCustomerName($request->id);
        $totalBalance = $customer->getLastBalance($name[0]->id);
        $result = $customer->LedgerDetailsPDFShow($request->id, $request->from, $request->to);
        $pdf = new pdfClass();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $qrimage = "qr" . $name[0]->name . ".png";
        if (!file_exists(asset('assets/images/customers/qrcode/' . $qrimage))) {
            $qrcodetext = $name[0]->mobile . " | " . $name[0]->user_id . " | 2"; //mode 2 for customer (or bt suno yeh company id nh USER ID HA) :-)
            \QrCode::size(200)
                ->format('png')
                ->generate($qrcodetext, Storage::disk('public')->put("images/company/", "qrcode.png"));
        }
        //first row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Cell(65, 0, "Company Name:", 0, 0, 'L');
        $pdf->Cell(45, 0, "Customer Name:", 0, 1, 'L');
        $pdf->Cell(30, 0, "", 0, 1, 'L');
        //second row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Image(asset('storage/images/company/' . $company[0]->logo), 12, 10, -200);
        $pdf->Cell(65, 12, $company[0]->name, 0, 0, 'L');
        $pdf->Cell(45, 12, $urduname, 0, 0, 'L');
        $pdf->Cell(30, 0, "", 0, 1, 'R');
        $pdf->Image(asset('storage/images/company/qrcode.png'), 175, 10, -200);
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
        $pdf->Cell(45, -15, $name[0]->mobile, 0, 0, 'L');
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
        $receipt_balance = 0;
        $total_amount = 0;
        $credit = 0;
        $debit = 0;
        $count = 0;
        foreach ($result as $key => $value) {
            $creditGreater = false;
            if ($credit > $total_amount) {
                $creditGreater = true;
            }
            if ($value->total_amount > 0) {
                $total_amount += $value->total_amount;
            } else {
                $total_amount += $value->debit;
            }
            $credit += $value->credit;
            $debit += $value->debit;
            $receipt_balance =  custom_helper::getLedgerCal($value, $receipt_balance, $total_amount, $credit, $debit, $creditGreater);
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
            $pdf->Cell(25, 6, number_format($receipt_balance, 2), 0, 0, 'R', 1);
            $pdf->Multicell(80, 6, $value->narration, 0, 'C', true);
        }
        $pdf->ln();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(130, 8, '', 0, 0, 'R');
        $pdf->Cell(20, 8, 'Total:', 'T,B', 0, 'R');
        $pdf->Cell(40, 8, "Rs. " . number_format($receipt_balance, 2), 'T,B', 1, 'R');
        //save file
        $pdf->Output('Customer Ledger' . $name[0]->name . '.pdf', 'I');
    }
    // public function TCPDF()
    // {
    //     // at the top of the file
    //     PDF::SetTitle('Hello World');
    //     PDF::AddPage();
    //     PDF::Write(0, 'Hello World');
    //     PDF::Output('hello_world.pdf');
    // }
    public function customerList(Request $request, Customer $customer)
    {
        $main = $customer->getcustomerList();
        return view('customer.customer-list', compact('main'));
    }
    public function fetch_customer_data(Request $request, inventory $inventory)
    {
        $main = $inventory->displayInventory($request->code, $request->name, $request->depart, $request->sdepart, $request->status);
        return view('partials.inventory_table', compact('main'))->render();
    }
    public function changeMobileAppStatus(Request $request)
    {
        if (DB::table("customers")->where("id", $request->id)->update(["is_mobile_app_user" => $request->value])) {
            return 1;
        } else {
            return 0;
        }
    }
}
