<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\purchase;
use App\Vendor;
use App\inventory;
use App\stock;
use Illuminate\Support\Facades\App;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Crabbly\Fpdf\Fpdf;
use App\pdfClass;

class purchaseController extends Controller
{

    public $obj;
    public function __construct()
    {
        $this->middleware('auth');
        $this->obj = new purchase();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ViewPurchase(purchase $purchase)
    {
        $po = array(); //$purchase->purchaseDetails();
        return view('Purchase.view-purchase', compact('po'));
    }

    /*
   AJAX request
   */
    public function get_purchaseData(Request $request)
    {

        ## Read value
        $draw = $request->get('draw');
        $type = $request->get('type');
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

        // Total records
        $totalRecords =  $this->obj->getTotalNoOfPurchase($columnName, $columnSortOrder, $start, $rowperpage, $searchValue, $type);
        $totalRecordswithFilter = $this->obj->getTotalNoOfPurchasessWithFilter($columnName, $columnSortOrder, $start, $rowperpage, $searchValue, $type);

        // Fetch records
        $records = $this->obj->purchaseDetails($columnName, $columnSortOrder, $start, $rowperpage, $searchValue, $type);

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $records
        );

        echo json_encode($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function add_purchaseForm(purchase $purchase)
    {
        $branch = $purchase->getBranches();
        $tax = $purchase->getTaxes();
        $vendors = $purchase->getVendors();
        $products = $purchase->products();
        $items = $purchase->getPurchaseItems();
        $uom = $purchase->UOM();
        $lg_branchid = session('branch');
        return view('Purchase.add-purchase', compact('branch', 'tax', 'vendors', 'products', 'items', 'lg_branchid', 'uom'));
    }

    public function get_items(Request $request, purchase $purchase)
    {
        $result = $purchase->get_item_details($request->id);
        return $result;
    }

    public function getMaxId(purchase $purchase)
    {
        $result = $purchase->getPONumber();
        return $result;
    }

    public function addPurchase(Request $request, purchase $purchase)
    {
        $fields = [
            'po_no' => $request->po_number,
            'user_id' => session('company_id'),
            'vendor_id' => $request->vendor,
            'branch_id' => $request->branch,
            'order_date ' => $request->date,
            'refrence ' => $request->ref,
            'delivery_date ' => $request->rpdate,
            'payment_date' => $request->payment,
            'comments ' => $request->po_number,
            'date ' => date('Y-m-d'),
            'time ' => date('H:s:i'),
        ];
        // $items = [
        //     '' => ,
        // ];
    }

    public function firstInsert(Request $request, purchase $purchase)
    {
        $fields = [
            'po_no' => $request->po_number,
            'user_id' => session('company_id'),
            'vendor_id' => $request->vendor,
            'branch_id' => $request->branch,
            'tax_id' => $request->tax,
            'order_date' => $request->date,
            'refrence' => $request->ref,
            'delivery_date' => $request->rpdate,
            'payment_date' => $request->payment,
            'comments' => $request->comments,
            'status_id' => 1,
            'date' => date('Y-m-d'),
            'time' => date('H:s:i'),
        ];

        $result =   $purchase->purchaseInsert($fields);

        $vendorResult = $purchase->vendorPurchases($request->vendor, $result);
        return $result;
    }

    public function test(Request $request, purchase $purchase)
    {
        $fields = [
            'po_no' => 'PO-2',
            'user_id' => session('company_id'),
            'vendor_id' => 3,
            'branch_id' => 2,
            'tax_id' => 1,
            'order_date' => '',
            'refrence' => '',
            'delivery_date' => '',
            'comments' => '',
            'status_id' => 1,
            'date' => date('Y-m-d'),
            'time' => date('H:s:i'),
        ];

        return  $purchase->purchaseInsert($fields);
    }

    public function secondInsert(Request $request, purchase $purchase)
    {

        $fields = [
            'vendor_id' => $request->vendor,
            'branch_id' => $request->branch,
            'tax_id' => $request->tax,
            'order_date' => $request->date,
            'refrence' => $request->ref,
            'delivery_date' => $request->rpdate,
            'payment_date' => $request->payment,
            'comments' => $request->comments,
            'status_id' => 1,
            'date' => date('Y-m-d'),
            'time' => date('H:s:i'),
        ];

        $items = [
            'purchase_id' => $request->ID,
            'batch_no' => $request->batch_no,
            "expiry_date" => $request->expiry_date,
            'item_code' => $request->product,
            'unit' => $request->unit,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'total_amount' => $request->total_amount,
            'discount_per_item' => $request->discount_per_item != '' ? $request->discount_per_item : 0,
            'tax_per_item_value' => $request->tax_per_item_value != '' ? $request->tax_per_item_value : 0,
            'tax_per_item_id' => $request->tax,
            'discount_by' => $request->discount_by,
        ];
        $filter = [
            'purchase_id' => $request->ID,
            'item_code' => $request->product
        ];

        $result = $purchase->updateVendor($fields, $request->ID);
        if ($purchase->exists($request->ID, $request->product)) {
            return 1;
        } else {
            $result = $purchase->itemsInsert($items);
            return $result;
        }
    }

    public function check(Request $request, purchase $purchase)
    {
        $check = $purchase->exists();
        return $check;
    }

    public function accounts(Request $request, purchase $purchase)
    {
        $result = $purchase->getItems($request->id);
        return $result;
    }

    public function UpdateItems(Request $request, purchase $purchase)
    {
        $items = [
            'item_code' => $request->product,
            'unit' => $request->unit,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'discount_per_item' => $request->discount_per_item != '' ? $request->discount_per_item : 0,
            'tax_per_item_value' => $request->tax_per_item_value != '' ? $request->tax_per_item_value : 0,
            'tax_per_item_id' => $request->tax_per_item_id,
            'discount_by' => $request->discount_by,
            'total_amount' => $request->total_amount,
            'batch_no' => $request->batch_no,
            "expiry_date" => $request->expiry_date,
        ];

        $result = $purchase->EditItem($items, $request->ID);
        return $items;
    }

    public function getAccDetails(Request $request, purchase $purchase)
    {
        $result = $purchase->getAccounts($request->id);
        return $result;
    }

    public function PurchaseDraft(Request $request, purchase $purchase)
    {
        $status = 0;
        $netAmount = 0;
        $poAccount = 0;

        $fields = [
            'vendor_id' => $request->vendor,
            'branch_id' => $request->branch,
            'tax_id' => $request->tax,
            'order_date' => $request->date,
            'refrence' => $request->ref,
            'delivery_date' => $request->rpdate,
            'payment_date' => $request->payment,
            'comments' => $request->comments,
            'status_id' => 1,
            'date' => date('Y-m-d'),
            'time' => date('H:s:i'),
        ];

        $acc = [
            'purchase_id' => $request->ID,
            'total_amount' => (float) str_replace(',', '', $request->total_amount),
            'tax_amount' => (float) str_replace(',', '', $request->taxAmount),
            'discount' => (float) str_replace(',', '', $request->discount),
            'discount_by' => (float) str_replace(',', '', $request->discount_by),
            'shipment' => (float) str_replace(',', '', $request->delivery),
            'net_amount' => (float) str_replace(',', '', $request->net_amount),
            'balance_amount' => (float) str_replace(',', '', $poAccount),
        ];

        $gen = $purchase->updateGeneral($fields, $request->ID);
        $pocount = $purchase->purchaseAccCount($request->ID);
        if ($pocount == 0) {
            $acc = $purchase->accInsert($acc);
        } else {
            $acc = $purchase->UpdateAccounts($acc, $pocount);
        }
    }

    public function finalSubmit(Request $request, purchase $purchase)
    {
        $status = 0;
        $netAmount = 0;
        $net_Amount = 0;
        $poAccount = 0;
        $fields = [
            'vendor_id' => $request->vendor,
            'branch_id' => $request->branch,
            'tax_id' => $request->tax,
            'order_date' => $request->date,
            'refrence' => $request->ref,
            'delivery_date' => $request->rpdate,
            'payment_date' => $request->payment,
            'comments' => $request->comments,
            'status_id' => 2,
            'date' => date('Y-m-d'),
            'time' => date('H:s:i'),
        ];
        $netAmount = str_replace(',', '', $request->net_amount);
        $totalAmount = str_replace(',', '', $request->total_amount);


        $balance = $purchase->getLastBalance($request->vendor);

        if (sizeof($balance) != 0) {
            if ($balance[0]->balance > 0) {
                $poAccount = $balance[0]->balance - $netAmount;
            } else {
                $poAccount = $netAmount;
            }

            $net_Amount = $balance[0]->balance - $netAmount;
        } else {
            $net_Amount = $netAmount;
        }



        if ($poAccount < 0) {
            $poAccount = $poAccount * (-1);
        }

        $netamt = (($totalAmount - $request->discount) + $request->taxAmount + $request->delivery);

        $acc = [
            'purchase_id' => $request->ID,
            'total_amount' => $totalAmount,
            'tax_amount' => $request->taxAmount,
            'discount' => $request->discount,
            'shipment' => $request->delivery,
            'net_amount' => $netamt,
            'balance_amount' => $netamt, //$poAccount,
        ];

        $ledger = [
            'vendor_id' => $request->vendor,
            'po_no' => $request->ID,
            'total_amount' => $netamt,
            'debit' => 0,
            'credit' => $netamt,
            'balance' => $net_Amount,
        ];

        $gen = $purchase->updateGeneral($fields, $request->ID);
        $pocount = $purchase->purchaseAccCount($request->ID);
        if ($pocount == 0) {
            $acc = $purchase->accInsert($acc);
        } else {
            $acc = $purchase->UpdateAccounts($acc, $pocount);
        }

        $ledger = $purchase->LedgerInsert($ledger);

        //GRN HERE
        // $grn = $purchase->getGrn();
        // $grn = $grn + 1;
        // $gen = [
        //     'GRN' => "GRN-".$grn,
        //     'user_id' => session('userid'),
        //     'created_at' => date('Y-m-d H:s:i'),
        //     'updated_at' => date('Y-m-d H:s:i'),
        // ];
        // $gen_res = $purchase->receiving_general($gen);

        // $resultItems = $purchase->getPurchaseitemsForGRN($request->ID);

        // foreach ($resultItems as $value) {

        //     $items = [
        //         'GRN' => $gen_res,
        //         'po_rec_details_id' => $value->p_item_details_id, 
        //         'item_id' => $value->item_code,
        //         'qty_rec' => $value->quantity,
        //         'status_id' => 3,
        //         'po_id' =>  $request->ID,
        //     ];

        //     $stock = [
        //         'grn_id' => $gen_res,
        //         'product_id' => $value->item_code,
        //         'uom' => $value->uom_id,
        //         'cost_price' => $value->price,
        //         'retail_price' => '0',
        //         'wholesale_price' => '0',
        //         'discount_price' => '0',
        //         'qty' => $value->quantity,
        //         'balance' =>$value->quantity,
        //         'status_id' => 1,
        //         'branch_id' => $request->branch,
        //         'date' => date('Y-m-d'),

        //     ];

        //     $rec = $purchase->receiving_items($items);
        //     $stockResult = $purchase->createStock($stock);
        // }
        return  1;
    }

    public function viewPO(Request $request, purchase $purchase)
    {
        $general = $purchase->getGeneral($request->id);
        $vendor = $purchase->getVendorDetails($general[0]->vendor_id);
        $items = $purchase->getItemDetails($request->id);
        $accounts = $purchase->getAccDetails($request->id);
        $received = $purchase->getReceived($request->id);
        $company = $purchase->companyDetails();
        $purchaseid = $request->id;
        return view('Purchase.view-po', compact('general', 'items', 'accounts', 'vendor', 'received', 'company', 'purchaseid'));
    }

    public function receivepo(Request $request, purchase $purchase)
    {

        // $grn = $purchase->getGrn();
        // $grn = $grn + 1;
        // $gen = [
        //     'GRN' => "GRN-".$grn,
        //     'user_id' => session('userid'),
        //     'created_at' => date('Y-m-d H:s:i'),
        //     'updated_at' => date('Y-m-d H:s:i'),
        // ];
        // $gen_res = $purchase->receiving_general($gen);
        $general = $purchase->getPurchaseGeneral($request->id);
        $receive = $purchase->getItemDetails($request->id);
        $itemReceived = $purchase->getReceived($request->id);
        $accounts = $purchase->getAccDetails($request->id);
        $po_id = $request->id;
        return view('Purchase.receive-po', compact('receive', 'general', 'itemReceived', 'po_id', 'accounts'));
    }

    public function createGRN(Request $request, purchase $purchase)
    {
        $grn = $purchase->getGrn();
        $grn = $grn + 1;
        $gen = [
            'GRN' => "GRN-" . $grn,
            'user_id' => session('userid'),
            'created_at' => date('Y-m-d H:s:i'),
            'updated_at' => date('Y-m-d H:s:i'),
        ];
        $gen_res = $purchase->receiving_general($gen);
        return $gen_res;
    }

    public function addGrn(Request $request, purchase $purchase, inventory $inventory, stock $stockApp)
    {

        $items = [
            'GRN' => $request->grn,
            'po_rec_details_id' => $request->item_details_id,
            'item_id' => $request->itemid,
            'qty_rec' => $request->rec,
            'status_id' => 3,
            'po_id' =>  $request->po,
        ];

        $stock = [
            'grn_id' => $request->grn,
            'product_id' => $request->itemid,
            'uom' => $request->uom,
            'cost_price' => $request->cp,
            'retail_price' => $request->rp,
            'wholesale_price' => $request->wp,
            'discount_price' => $request->dp,
            'qty' => $request->rec,
            'balance' => $request->rec,
            'status_id' => 1,
            'branch_id' => $request->branch,
            'date' => date('Y-m-d H:s:i'),

        ];



        $rec = $purchase->receiving_items($items);
        $stockResult = $purchase->createStock($stock);

        $lastStock = $stockApp->getLastStock($request->itemid);
        $stk = empty($lastStock) ? 0 : $lastStock[0]->stock;
        $stk = $stk + $request->rec;

        $report = [
            'date' => date('Y-m-d H:s:i'),
            'product_id' => $request->itemid,
            'foreign_id' => $request->po,
            'branch_id' => session('branch'),
            'qty' => $request->rec,
            'stock' => $stk,
            'cost' => $request->cp,
            'retail' => $request->rp,
            'narration' => 'Stock Purchase through Purchase Order',
        ];
        $stock_report = $stockApp->stock_report($report);

        $return = $purchase->changeReturnStatus($request->po, $request->rec);
        //$update = $purchase->changeStatus($request->po,3);
        return 1;
    }

    public function return(Request $request, purchase $purchase)
    {
        $general = $purchase->getGeneral($request->id);
        $vendor = $purchase->getVendorDetails($general[0]->vendor_id);
        $items = $purchase->getItemDetails($request->id);
        $accounts = $purchase->getAccDetails($request->id);
        $received = $purchase->getReceived($request->id);
        $GRN = $purchase->getGRNDetails($request->id);
        $purchaseID = $request->id;
        return view('Purchase.return', compact('general', 'items', 'accounts', 'vendor', 'received', 'GRN', 'purchaseID'));
    }

    public function getGRNStock(Request $request, purchase $purchase)
    {
        $stock = $purchase->getStockGRN($request->grn);
        return $stock;
    }

    public function getStockForCompleteReturn(Request $request, purchase $purchase)
    {
        $stock = $purchase->getStockforComplete($request->po);
        return $stock;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changePOStatus(Request $request, purchase $purchase)
    {
        if ($request->status > 0) {
            $update = $purchase->changeStatus($request->po, 7);
        } else {
            $update = $purchase->changeStatus($request->po, 3);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, purchase $purchase)
    {
        $branch = $purchase->getBranches();
        $tax = $purchase->getTaxes();
        $vendors = $purchase->getVendors();
        $products = $purchase->products();
        $items = $purchase->getPurchaseItems();
        $uom = $purchase->UOM();
        $lg_branchid = session('branch');
        $general = $purchase->getGeneral($request->id);
        $accounts = $purchase->getAccDetails($request->id);
        $purchaseID = $request->id;
        return view('Purchase.edit', compact('general', 'branch', 'tax', 'vendors', 'products', 'items', 'lg_branchid', 'uom', 'purchaseID', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function insertReturn(Request $request, purchase $purchase, stock $stockApp)
    {
        $return = [
            'purchase_id' => $request->po,
            'return_mode' => $request->mode,
            'item_code' => $request->itemid,
            'unit'  => $request->uom,
            'quantity' => $request->qty,
            'total_amount' => $request->amount,
            'status' => 1,
        ];

        $rec = [
            'qty_rec' => $request->qty,
            'status_id' => $request->status,
        ];

        $report = [
            'date' => date('Y-m-d H:s:i'),
            'product_id' => $request->itemid,
            'foreign_id' => $request->po,
            'branch_id' => session('branch'),
            'qty' => $request->qty,
            'stock' => 0,
            'cost' => 0,
            'retail' => '',
            'narration' => 'Stock Return',
        ];
        $stock_report = $stockApp->stock_report($report);
        //Insert into Purchase Return 
        $return = $purchase->insertIntoReturn($return);
        //Update Received Details

        $rec = $purchase->updateRecDetails($rec, $request->rec_details_id);


        //Getting Stock Details First Then Update the Stock
        $stockDetails = $purchase->getStockDetails($request->GRN, $request->itemid);
        $quantityCheck = $stockDetails[0]->balance - $request->qty;

        //Update Stock Table
        if ($quantityCheck == 0) {
            $stockFields = [
                'balance' => $quantityCheck,
                'status_id' => 2,
                'narration' => $request->narration,
            ];
            $stockDetails = $purchase->updateStock($stockFields, $request->stockid);
        } else {
            $stockFields = [
                'balance' => $quantityCheck,
                'narration' => $request->narration,
            ];
            $stockDetails = $purchase->updateStock($stockFields, $request->stockid);
        }
        //Change the Status of according to quantity
        if ($request->status == 6) {
            $update = $purchase->changeStatus($request->po, 6);
        } else {
            $update = $purchase->changeStatus($request->po, 5);
        }
    }

    public function UpdateAccounts(Request $request, purchase $purchase)
    {

        $returnamount = $purchase->getReturnAmount($request->po);
        $accAmount = $purchase->getAccountAmount($request->po);
        $amount = $accAmount[0]->total_amount - $returnamount;
        $acc = $purchase->getTaxValue($request->po);
        $tax = (($amount / 100) * $acc[0]->value);
        $net = $amount + $tax;

        $account = [
            'total_amount' => $amount,
            'tax_amount'  => $tax,
            'net_amount' => $net,
        ];

        $acc = $purchase->UpdateAccounts($account, $acc[0]->account_id);
        //$status = $purchase->updateGeneralPurchaseStatus($request->po,$request->purchase_status);

        return $returnamount;
    }

    /**
     * FOR RETURN PURCHASE ORDER.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getReceiveItems(Request $request, purchase $purchase)
    {
        $qty = $request->qty;


        while ($qty  > 0) {
            $result = $purchase->getDetails($request->po, $request->itemid);
            // $res = $result[0]->qty_rec - $qty;
            $qty = $result[0]->qty_rec > $qty ? $result[0]->qty_rec - $qty : $qty - $result[0]->qty_rec;
            $res = $result[0]->qty_rec - $qty;
            //Setting the status according to remaining qty
            if ($res == 0) {
                $fields = [
                    'qty_rec' => $res,
                    'status_id' => 6,
                ];
                $update = $purchase->updateRecDetails($fields, $result[0]->rec_details_id);
                //return $result[0]->rec_details_id;
            } else {
                $fields = [
                    'qty_rec' => $res,
                ];
                $update = $purchase->updateRecDetails($fields, $result[0]->rec_details_id);
                // return $result[0]->rec_details_id;

            }
        }
        return $result[0]->rec_details_id;
    }

    public function updatePOStatus(Request $request, purchase $purchase)
    {
        $result = $purchase->updatePOStatus($request->id);
        if ($result == 1) {
            return redirect('/view-purchases');
        } else {
            return redirect('/view-purchases');
        }
    }

    public function grnDetails(Request $request, purchase $purchase)
    {
        $grn = $purchase->GRNDetails($request->id);
        return view('Purchase.grn-details', compact('grn'));
    }

    public function DetailsOfGrn(Request $request, purchase $purchase)
    {
        $result = $purchase->DetailsofGRN($request->id);
        return $result;
    }

    public function DownloadPDF(Request $request, purchase $purchase)
    {
        // $show = Disneyplus::find($id);
        $pdf = PDF::loadView('Purchase.pdf')->setPaper('a4', 'landscape');
        return $pdf->download('disney.pdf');
    }

    public function DeletePurchaseItems(Request $request, purchase $purchase)
    {
        $result = $purchase->deletePurchaseItems($request->id);
        return $result;
    }

    public function DeletePurchaseOrder(Request $request, purchase $purchase)
    {
        $result = $purchase->deletePO($request->id);
        return $result;
    }


    //purchase report
    public function purchasereport(Request $request, Vendor $vendor, purchase $purchase)
    {


        $company = $vendor->company(session('company_id'));

        //queries
        $general = $purchase->getGeneral($request->id);
        $getTax = $purchase->getTaxPercentage($request->id);
        $vendor = $purchase->getVendorDetails($general[0]->vendor_id);
        $items = $purchase->getItemDetails($request->id);

        $accounts = $purchase->getAccDetails($request->id);
        $received = $purchase->getReceived($request->id);
        $statusname = $purchase->getstatusname($request->id);
        $totalSalesTaxAmount = 0;

        //      $company = $purchase->companyDetails();


        if (!file_exists(asset('storage/images/company/qrcode.png'))) {
            $qrcodetext = $company[0]->name . " | " . $company[0]->ptcl_contact . " | " . $company[0]->address;
            \QrCode::size(200)
                ->format('png')
                ->generate($qrcodetext, Storage::disk('public')->put("images/company/", "qrcode.png"));
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
        $pdf->Image(asset('storage/images/company/' . $company[0]->logo), 12, 10, -200);
        $pdf->Cell(105, 12, $company[0]->name, 0, 0, 'L');
        $pdf->Cell(50, 0, "", 0, 1, 'R');
        $pdf->Image(asset('storage/images/company/qrcode.png'), 175, 10, -200);

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
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Purchase Order', 'B,T', 1, 'L');
        $pdf->ln(1);

        //details start here
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(70, 6, 'Vendor Information:', 0, 0);
        $pdf->Cell(50, 6, 'Order Information:', 0, 0);
        $pdf->Cell(40, 6, 'Purchase Order No: ', 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(30, 6, $general[0]->po_no, 0, 1, 'L');

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(70, 6, $vendor[0]->vendor_name, 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(20, 6, 'Date: ', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(30, 6, date('d M Y', strtotime($general[0]->order_date)), 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(40, 6, 'Total Due:', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        if ($general[0]->status_id == 7 || $general[0]->status_id == 6 || $general[0]->status_id == 3 || $general[0]->status_id == 5 || $general[0]->status_id == 9) {
            $pdf->Cell(30, 6, 'Rs. ' . number_format(\Custom_Helper::getSubTotal($received), 2), 0, 1, 'L');
        } else {
            $pdf->Cell(30, 6, 'Rs. ' . number_format($accounts[0]->net_amount, 2), 0, 1, 'L');
        }

        $pdf->SetFont('Arial', '', 11);
        $addresses = str_split($vendor[0]->address, 32);

        $pdf->Cell(70, 6, $addresses[0], 0, 0);
        // $pdf->Cell(70,6,$vendor[0]->address,0,0);
        $pdf->Cell(20, 6, 'Delivery: ', 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(30, 6, date('d-m-Y', strtotime($general[0]->delivery_date)), 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(40, 6, 'NTN#', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(30, 6, ($vendor[0]->ntn == "" ? "NA" : $vendor[0]->ntn), 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(70, 6, $vendor[0]->vendor_contact, 0, 0);
        $pdf->Cell(20, 6, 'Status: ', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(30, 6, $statusname[0]->name, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(40, 6, 'STRN#', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(30, 6, ($vendor[0]->strn == "" ? "NA" : $vendor[0]->strn), 0, 1, 'L');



        $pdf->ln(1);


        $pdf->SetFont('Arial', 'B', 11);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(60, 7, 'Poduct Name', 'B', 0, 'L', 1);
        $pdf->Cell(20, 7, 'Price', 'B', 0, 'C', 1);
        $pdf->Cell(20, 7, 'ST.Price', 'B', 0, 'C', 1);
        $pdf->Cell(25, 7, 'Quantity', 'B', 0, 'R', 1);
        $pdf->Cell(30, 7, 'Received', 'B', 0, 'R', 1);

        $pdf->Cell(35, 7, 'Total', 'B', 1, 'R', 1);


        $pdf->SetFont('Arial', '', 10);
        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $totalAmount = 0;
        if ($general[0]->status_id == 7 || $general[0]->status_id == 6 || $general[0]->status_id == 3 || $general[0]->status_id == 5 || $general[0]->status_id == 9) {
            foreach ($received as $value) {

                $totalAmount += (($value->price * $value->qty_rec) + ($value->tax_per_item_value * $value->qty_rec));
                $totalSalesTaxAmount += ($value->tax_per_item_value * $value->qty_rec);
                $unitPriceWithTax = $totalAmount / $value->qty_rec;

                $pdf->Cell(60, 7, $value->product_name, 0, 0, 'L', 1);
                $pdf->Cell(20, 7, number_format($value->price, 4), 0, 0, 'C', 1);
                $pdf->Cell(20, 7, number_format($unitPriceWithTax, 4), 0, 0, 'C', 1);
                $pdf->Cell(25, 7, number_format($value->quantity, 2), 0, 0, 'R', 1);
                $pdf->Cell(30, 7, number_format($value->qty_rec, 2), 0, 0, 'R', 1);
                // $pdf->Cell(35,7,number_format($value->received * $value->total_amount,2),0,1,'R',1);
                $pdf->Cell(35, 7, number_format($totalAmount, 2), 0, 1, 'R', 1);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(5, 2, '', 0, 0, 'L', 1);
                $pdf->Cell(185, 2, $value->product_description, 0, 1, 'L', 1);
            }
        } else {
            foreach ($items as $value) {
                $totalAmount += (($value->price * $value->quantity) + ($value->tax_per_item_value * $value->quantity));
                $totalSalesTaxAmount += ($value->tax_per_item_value * $value->quantity);
                $unitPriceWithTax = $totalAmount / $value->quantity;
                $pdf->Cell(60, 7, $value->product_name, 0, 0, 'L', 1);
                $pdf->Cell(20, 7, number_format($value->price, 2), 0, 0, 'R', 1);
                $pdf->Cell(20, 7, number_format($unitPriceWithTax, 2), 0, 0, 'R', 1);
                $pdf->Cell(25, 7, number_format($value->quantity, 2), 0, 0, 'R', 1);
                $pdf->Cell(30, 7, number_format(0, 2), 0, 0, 'R', 1);
                // $pdf->Cell(35,7,number_format($value->quantity * $value->total_amount,2),0,1,'R',1);
                $pdf->Cell(35, 7, number_format($totalAmount, 2), 0, 1, 'R', 1);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(5, 2, '', 0, 0, 'L', 1);
                $pdf->Cell(185, 2, $value->product_description, 0, 1, 'L', 1);
            }
        }

        $pdf->ln(2);

        if ($general[0]->status_id == 7 || $general[0]->status_id == 6 || $general[0]->status_id == 3 || $general[0]->status_id == 5 || $general[0]->status_id == 9) {
            $pdf->setFillColor(233, 233, 233);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(190, 2, '', 0, 1, 'R', 1);
            $pdf->Cell(160, 6, 'Gross (+): ', 0, 0, 'R', 1);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(30, 6, number_format(\Custom_Helper::getSubTotal($received), 2), 0, 1, 'R', 1);


            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(160, 6, 'Sales Tax(' . $getTax[0]->value . '): ', 0, 0, 'R', 1);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(30, 6, number_format($totalSalesTaxAmount, 2), 0, 1, 'R', 1);

            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(160, 6, 'Discount (+): ', 0, 0, 'R', 1);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(30, 6, number_format($accounts[0]->discount, 2), 0, 1, 'R', 1);

            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(160, 6, 'Other Expenses(+): ', 0, 0, 'R', 1);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(30, 6, number_format($accounts[0]->shipment, 2), 0, 1, 'R', 1);

            $pdf->SetFont('Arial', '', 13);
            $pdf->Cell(160, 8, 'Total: ', 0, 0, 'R', 1);
            $pdf->SetFont('Arial', 'B', 13);
            $pdf->Cell(30, 8, number_format((\Custom_Helper::getSubTotal($received) + $totalSalesTaxAmount + $accounts[0]->shipment) - $accounts[0]->discount, 2), 0, 1, 'R', 1);
        } else {
            $pdf->setFillColor(233, 233, 233);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(190, 2, '', 0, 1, 'R', 1);
            $pdf->Cell(160, 6, 'Sub Total: ', 0, 0, 'R', 1);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(30, 6, number_format($accounts[0]->total_amount, 2), 0, 1, 'R', 1);
            $pdf->SetFont('Arial', '', 11);
            // $pdf->Cell(160,6,'Taxes: ',0,0,'R',1);
            // $pdf->SetFont('Arial','B',11);
            // $pdf->Cell(30,6,number_format($accounts[0]->tax_amount,2),0,1,'R',1);
            // $pdf->SetFont('Arial','',11);
            // $pdf->Cell(160,6,'Discount: ',0,0,'R',1);
            // $pdf->SetFont('Arial','B',11);
            // $pdf->Cell(30,6,number_format($accounts[0]->discount,2),0,1,'R',1);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(160, 6, 'Delivery Charges: ', 0, 0, 'R', 1);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(30, 6, number_format($accounts[0]->shipment, 2), 0, 1, 'R', 1);
            $pdf->SetFont('Arial', '', 13);
            $pdf->Cell(160, 8, 'Total: ', 0, 0, 'R', 1);
            $pdf->SetFont('Arial', 'B', 13);
            $pdf->Cell(30, 8, number_format($accounts[0]->net_amount, 2), 0, 1, 'R', 1);
        }



        //save file
        $pdf->Output('Purchase Order' . $general[0]->po_no . '.pdf', 'I');
    }
}
