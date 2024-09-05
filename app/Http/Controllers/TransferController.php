<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\transfer;
use App\demand;
use App\pdfClass;
use App\Vendor;
use Crabbly\Fpdf\Fpdf;
use Illuminate\Support\Facades\Storage;

class TransferController extends Controller
{
  public function index(Request $request, transfer $transfer)
  {
    $gettransfer = $transfer->get_transfer_orders($request->id);
    $demandid = $request->id;
    return view('Received-Demands.view-transfer', compact('gettransfer', 'demandid'));
  }

  public function show(Request $request, transfer $transfer)
  {
    $getdetails = $transfer->tranferOrder_details($request->toid);
    return $getdetails;
  }

  public function show_transferdetails(Request $request, transfer $transfer)
  {
    $getdetails = $transfer->tranferOrder_details($request->id);
    return view('Received-Demands.view-transferdetails', compact('getdetails'));
  }

  public function transferlist(transfer $transfer)
  {
    $transferlist = $transfer->get_transferlist();
    return view('Received-Demands.transferorder-list', compact('transferlist'));
  }

  public function deliverychallan(Request $request, transfer $transfer)
  {

    $getdetails = $transfer->tranferOrder_details($request->id);
    $transferlist = $transfer->get_transferlist();
    $status = $transfer->status();
    return view('Received-Demands.create-deliverychallan', compact('getdetails', 'status', 'transferlist'));
  }

  public function getstock(Request $request, transfer $transfer)
  {
    $stock = $transfer->stock_details($request->itemcode, '');
    return $stock;
  }

  public function updatetransferitem(Request $request, transfer $transfer)
  {
    $result = $transfer->updateitem_transfer($request->id, $request->statusid);
    return $result;
  }

  public function updatechllan(Request $request, transfer $transfer)
  {

    $challanid = $transfer->getchallanid($request->transferid, session('branch'));

    $challanitems_id = $transfer->get_challanitems_id($challanid[0]->DC_id);

    $updategeneral = $transfer->update_challan($challanid[0]->DC_id, $request->shipmentamt);

    $totalcp = 0;

    foreach ($challanitems_id as $item) {

      $totalcp =  $totalcp + ($item->cost_price * $item->deliverd_qty);
    }

    foreach ($challanitems_id as $value) {

      $amount = $value->cost_price * $value->deliverd_qty;
      $amount = ($amount / $totalcp) * 100;

      $unitcp = ($amount * $request->shipmentamt) / 100;
      $unitcp = ($unitcp / $value->deliverd_qty);

      $updateitems = $transfer->update_challan_charges($value->dc_item_id, $unitcp);
    }
  }


  public function insert(Request $request, transfer $transfer)
  {

    $exsitschk = $transfer->exsits_chk(session('branch'), $request->transferid);

    if ($exsitschk == 0) {

      $count = $transfer->get_count();
      $count = $count + 1;

      $brto = $transfer->get_transferlist_byid($request->transferid);

      $items = [
        'DC_No' => $count,
        'Transfer_id' => $request->transferid,
        'date' => date('Y-m-d'),
        'branch_from' => session('branch'),
        'branch_to' => $brto[0]->branch_to,
        'user_id' => session('userid'),
        'shipment_amount' => '',
      ];

      $deliverychallan = $transfer->insert_deliverychallan('deliverychallan_general_details', $items);
      $items = [
        'DC_Id' => $deliverychallan,
        'product_id' => $request->productid,
        'deliverd_qty' => $request->qty,
        'cost_price' => $request->cp,
        'shipment_charges' => '',
      ];

      $deliveryitems = $transfer->insert_deliverychallan('deliverychallan_item_details', $items);

      $stockresult =  $this->stock_dedcution($transfer, $request->productid, $request->qty);

      return $deliveryitems;
    } else {
      $challanid = $transfer->getchallanid($request->transferid, session('branch'));


      $items = [
        'DC_Id' => $challanid[0]->DC_id,
        'product_id' => $request->productid,
        'deliverd_qty' => $request->qty,
        'cost_price' => $request->cp,
        'shipment_charges' => '',
      ];

      $deliveryitems = $transfer->insert_deliverychallan('deliverychallan_item_details', $items);
      $stockresult =  $this->stock_dedcution($transfer, $request->productid, $request->qty);

      return $deliveryitems;
    }
  }

  public function stock_dedcution($transfer, $productid, $qty)
  {
    $result = $transfer->get_stockbalance($productid);
    $updatedstock = $qty;
    for ($i = 0; $i < sizeof($result); $i++) {

      $value = $transfer->get_stockbalance($productid);
      $updatedstock = ($updatedstock - $value[0]->balance);
      if ($updatedstock > 0) {
        $update = $transfer->deduction_stock($value[0]->stock_id, 0, 2);
      } elseif ($updatedstock < 0) {
        $updatedstock = $updatedstock * (-1);
        $update = $transfer->deduction_stock($value[0]->stock_id, $updatedstock, 1);
        break;
      } elseif ($updatedstock == 0) {
        $update = $transfer->deduction_stock($value[0]->stock_id, 0, 2);
        break;
      }
    }
    return sizeof($result);
  }

  public function challanlist(request $request, transfer $transfer)
  {

    $challans = $transfer->get_challanlist();
    return view('Received-Demands.view-deliveryChallans', compact('challans'));
  }

  public function challandetails(request $request, transfer $transfer)
  {
    $details = $transfer->get_challan_Details($request->id);
    $challanid = $request->id;
    return view('Received-Demands.deliverchallan-details', compact('details', 'challanid'));
  }

  public function createGRN(request $request, transfer $transfer)
  {
    $details = $transfer->get_challan_Details($request->id);
    return view('Received-Demands.create-GRN', compact('details'));
  }


  public function grn_insert(Request $request, transfer $transfer)
  {

    if ($request->grn == "") {

      $count = $transfer->get_count_GRN();
      $count = $count + 1;
      $items = [

        'GRN' => 'GIN-' . $count,
        'user_id' => session('userid'),
        'created_at' => date('Y-m-d'),
        'updated_at' => date('Y-m-d'),
      ];

      $grn_general = $transfer->insert_GRN('purchase_rec_gen', $items);

      $items = [
        'GRN' => $grn_general,
        'dc_item_id' => $request->dcitem_id,
        'item_id' => $request->item_id,
        'qty_rec' => $request->qty_rec,
        'status_id' => 3,
        'DC_id' => $request->dc_id,

      ];
      $grn_items = $transfer->insert_GRN('purchase_rec_dc_details', $items);

      $items = [
        'grn_id' => $grn_general,
        'product_id' => $request->item_id,
        'uom' => $request->uom,
        'cost_price' => $request->cp,
        'retail_price' => $request->rp,
        'wholesale_price' => $request->wp,
        'discount_price' => $request->dp,
        'qty' => $request->qty_rec,
        'balance' => $request->qty_rec,
        'status_id' => 1,
        'branch_id' => session('branch'),
        'date' => date('Y-m-d'),
      ];
      $stock = $transfer->insert_stock($items);
      return $grn_general;
    } else {

      $items = [
        'GRN' => $request->grn,
        'dc_item_id' => $request->dcitem_id,
        'item_id' => $request->item_id,
        'qty_rec' => $request->qty_rec,
        'status_id' => 3,
        'DC_id' => $request->dc_id,
      ];
      $grn_items = $transfer->insert_GRN('purchase_rec_dc_details', $items);

      $items = [
        'grn_id' => $request->grn,
        'product_id' => $request->item_id,
        'uom' => $request->uom,
        'cost_price' => $request->cp,
        'retail_price' => $request->rp,
        'wholesale_price' => $request->wp,
        'discount_price' => $request->dp,
        'qty' => $request->qty_rec,
        'balance' => $request->qty_rec,
        'status_id' => 1,
        'branch_id' => session('branch'),
        'date' => date('Y-m-d'),
      ];
      $stock = $transfer->insert_stock($items);
      return 1;
    }
  }


  public function getdetails_po(request $request, transfer $transfer, demand $demand)
  {
    $sender = $demand->get_sender_info();
    $reciver = $demand->get_reciver_info();
    $details = $demand->demand_details_show($request->id);
    $podetails = $transfer->getPO($request->id);
    return view('Received-Demands.create-po', compact('details', 'sender', 'reciver', 'podetails'));
  }


  public function purchaseorder_insert(Request $request, transfer $transfer)
  {
    if ($request->poid == "") {
      $count = $transfer->count_PO();
      $count = $count[0]->counter + 1;

      $items = [
        'po_no' => "PO-" . $count,
        'user_id' => session('userid'),
        'vendor_id' => 1,
        'branch_id' => session('branch'),
        'tax_id' => 1,
        'order_date' => date('Y-m-d'),
        'refrence' => '',
        'delivery_date' => date('Y-m-d'),
        'comments' => '',
        'status_id' => 1,
        'date' => date('Y-m-d'),
        'time' => date('H:s:i'),
      ];

      $pogeneral =  $transfer->insert_PO('purchase_general_details', $items);

      $items = [
        'purchase_id' => $pogeneral,
        'item_code' => $request->productid,
        'unit' => $request->unit,
        'quantity' => $request->balance,
        'price' => 0,
        'total_amount' => 0,
      ];

      $poitems = $transfer->insert_PO('purchase_item_details', $items);
      $items = [
        'purchase_id' => $pogeneral,
        'demand_id' => $request->demandid,
      ];
      $podemand = $transfer->insert_PO('purchase_demand', $items);
      $items = [
        'purchase_id' => $pogeneral,
      ];
      $poaccount = $transfer->insert_PO('purchase_account_details', $items);
      return $pogeneral;
    } else {
      $items = [
        'purchase_id' => $request->poid,
        'item_code' => $request->productid,
        'unit' => $request->unit,
        'quantity' => $request->balance,
        'price' => 0,
        'total_amount' => 0,
      ];
      $poitems = $transfer->insert_PO('purchase_item_details', $items);
      return $request->poid;
    }
  }

  public function edit_transfer(Request $request, transfer $transfer)
  {
    $result = $transfer->edit_transfer($request->id, $request->qty);
    return 1;
  }


  public function gettransferorders(Request $request, transfer $transfer)
  {
    $details = $transfer->gettransferorders();
    return view('Received-Demands.transfer-view', compact('details'));
  }

  public function removetransferorder(Request $request, transfer $transfer)
  {
    $result = $transfer->removetransferorder($request->id, $request->statusid);
    return 1;
  }

  public function getToBranches(Request $request, transfer $transfer)
  {
    $branches = $transfer->getTobranches($request->branch);
    return $branches;
  }

  public function create_transferorder(Request $request, transfer $transfer)
  {
    $branches = $transfer->getbranches();
    $headoffice = $transfer->get_headoffice();
    $count = $transfer->get_count_trf();
    $count = $count[0]->counter + 1;
    $items = [

      'transfer_No' => $count,
      'user_id' => session('userid'),
      'status_id' => 1,
      'date' => date('Y-m-d'),
      'time' => date('H:s:i'),
      'company_id' => session('company_id'),
      'branch_from' => session('branch'),
      'branch_to' => 1,
    ];

    $addtransfer = $transfer->insert_trf('transfer_without_demand', $items);

    return view('Transfer.create-transfer', compact('branches', 'headoffice', 'addtransfer'));
  }

  public function trf_stock(Request $request, transfer $transfer)
  {
    $stock = $transfer->getstock($request->productid, $request->branchid);
    return $stock;
  }

  public function get_products(Request $request, transfer $transfer)
  {
    $products = $transfer->getproducts($request->branchid);
    return $products;
  }
  public function insert_trf(Request $request, transfer $transfer)
  {

    $exsist = $transfer->exsits_chk_trf($request->trfid);


    if ($exsist[0]->counter > 0) {
      $items = [

        'transfer_No' => $request->trfid,
        'user_id' => session('userid'),
        'status_id' => 1,
        'date' => $request->trfdate,
        'time' => date('H:s:i'),
        'company_id' => session('company_id'),
        'branch_from' => $request->branchfrom,
        'branch_to' => $request->branchto,
      ];

      $updatetrf = $transfer->update_trf($request->trfid, $items);

      $exsist = $transfer->product_exsist($request->trfid, $request->productid);
      if ($exsist[0]->counter == 0) {
        $items = [
          'transfer_id' => $request->trfid,
          'product_id' => $request->productid,
          'cp' => 'NULL',
          'qty' => $request->qty,
          'status_id' => 1,
        ];
        $additems = $transfer->insert_trf('transfer_item_details', $items);
        return 1;
      } else {
        return 0;
      }
    } else {
      $exsist = $transfer->product_exsist($request->trfid, $request->productid);
      if ($exsist[0]->counter == 0) {
        $items = [
          'transfer_id' => $request->trfid,
          'product_id' => $request->productid,
          'cp' => 'NULL',
          'qty' => $request->qty,
          'status_id' => 1,
        ];
        $additems = $transfer->insert_trf('transfer_item_details', $items);
        return 1;
      } else {
        return 0;
      }
    }
  }

  public function trf_details(Request $request, transfer $transfer)
  {
    $trfdetails = $transfer->trf_details($request->trfid);
    return $trfdetails;
  }

  public function trf_delete(Request $request, transfer $transfer)
  {
    $trfdel = $transfer->trf_delete($request->trfid);
    return $trfdel;
  }

  public function trf_submit_update(Request $request, transfer $transfer)
  {
    $result = $transfer->trf_submit_update($request->id, $request->statusid);
    return $result;
  }

  public function trf_list(Request $request, transfer $transfer)
  {
    $gettransfer = $transfer->get_trf_orders_without_demand();
    return view('Transfer.view-transferorders', compact('gettransfer'));
  }

  public function trforder_delete(Request $request, transfer $transfer)
  {
    $trfdel = $transfer->trforder_delete($request->trfid);
    return $trfdel;
  }

  public function get_trf_details(Request $request, transfer $transfer)
  {
    $getdetails = $transfer->get_trf_details($request->id);
    return view('Transfer.transferorder-details', compact('getdetails'));
  }

  public function qty_update_trf(Request $request, transfer $transfer)
  {
    $result = $transfer->qty_update_trf($request->id, $request->qty);
    return $result;
  }

  public function insert_direct_chalan(Request $request, transfer $transfer)
  {

    $exsitschk = $transfer->exsits_chk(session('branch'), $request->transferid);

    if ($exsitschk == 0) {

      $count = $transfer->get_count();
      $count = $count + 1;

      $items = [
        'DC_No' => $count,
        'Transfer_id' => $request->transferid,
        'date' => date('Y-m-d'),
        'branch_from' => session('branch'),
        'branch_to' => $request->branchto,
        'user_id' => session('userid'),
        'shipment_amount' => $request->shipmentamt,
      ];

      $deliverychallan = $transfer->insert_deliverychallan('deliverychallan_general_details', $items);

      // CREATE GRN FOR RECEIVING STOCK
      $grncount = $transfer->get_count_GRN();
      $grncount = $grncount + 1;
      $items = [
        'GRN' => 'GIN-' . $grncount,
        'user_id' => session('userid'),
        'created_at' => date('Y-m-d'),
        'updated_at' => date('Y-m-d'),
      ];

      $grn_general = $transfer->insert_GRN('purchase_rec_gen', $items);



      //get details for loop
      $trfdetails = $transfer->trf_details($request->transferid);
      $count = sizeof($trfdetails);

      for ($i = 0; $i < sizeof($trfdetails); $i++) {

        //calculation of shipment charges
        $totalcp = 0;
        $totalcp =  $totalcp + ($trfdetails[$i]->cp * $trfdetails[$i]->Transfer_Qty);

        $amount = $trfdetails[$i]->cp * $trfdetails[$i]->Transfer_Qty;
        $amount = ($amount / $totalcp) * 100;
        $unitcp = ($amount * $request->shipmentamt) / 100;
        $unitcp = ($unitcp / $trfdetails[$i]->Transfer_Qty);

        $items = [
          'DC_Id' => $deliverychallan,
          'product_id' => $trfdetails[$i]->product_id,
          'deliverd_qty' => $trfdetails[$i]->Transfer_Qty,
          'cost_price' => $trfdetails[$i]->cp,
          'shipment_charges' => $unitcp,
        ];

        $deliveryitems = $transfer->insert_deliverychallan('deliverychallan_item_details', $items);

        $items = [
          'GRN' => $grn_general,
          'dc_item_id' => $deliveryitems,
          'item_id' => $trfdetails[$i]->product_id,
          'qty_rec' => $trfdetails[$i]->Transfer_Qty,
          'status_id' => 3,
          'DC_id' => $deliverychallan,
        ];
        $grn_items = $transfer->insert_GRN('purchase_rec_dc_details', $items);

        $stockresult =  $this->stock_dedcution($transfer, $trfdetails[$i]->product_id, $trfdetails[$i]->Transfer_Qty);

        $items = [
          'grn_id' => $grn_general,
          'product_id' => $trfdetails[$i]->product_id,
          'uom' => $trfdetails[$i]->uom_id,
          'cost_price' => $trfdetails[$i]->cp,
          'retail_price' => $trfdetails[$i]->cp,
          'wholesale_price' => 0,
          'discount_price' => 0,
          'qty' => $trfdetails[$i]->Transfer_Qty,
          'balance' => $trfdetails[$i]->Transfer_Qty,
          'status_id' => 1,
          'branch_id' => $request->branchto,
          'date' => date('Y-m-d'),
        ];
        $stock = $transfer->insert_stock($items);
      }
      return $deliverychallan;
    } else {
      return 0;
    }
  }


  public function edit_trf_details(Request $request, transfer $transfer)
  {
    $getdetails = $transfer->get_details($request->id);
    $branches = $transfer->getbranches();
    $headoffice = $transfer->get_headoffice();


    return view('Transfer.edit-transferorder', compact('getdetails', 'branches', 'headoffice'));
  }


  //transfer report
  public function transferReport(Request $request, Vendor $vendor, transfer $transfer)
  {
    $company = $vendor->company(session('company_id'));

    //queries
    $details = $transfer->directTransferOrderReport($request->id);

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
    $pdf->Cell(190, 10, 'Transfer Order', 'B,T', 1, 'L');
    $pdf->ln(1);

    //details start here
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(70, 6, 'FROM :', 0, 0);
    $pdf->Cell(60, 6, 'TO:', 0, 0);
    $pdf->Cell(40, 6, 'TRANSFER ORDER | ', 0, 0);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(20, 6, $details[0]->transfer_No, 0, 1, 'L');

    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(70, 6, 'BRANCH MANAGER:', 0, 0);
    $pdf->Cell(60, 6, 'ADMINISTRATOR:', 0, 0);
    $pdf->Cell(30, 6, 'CREATED ON: ', 0, 0);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(30, 6, date('d-m-Y', strtotime($details[0]->date)), 0, 1, 'L');

    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(70, 6, $details[0]->branch_from, 0, 0);
    $pdf->Cell(60, 6, $details[0]->branch_to, 0, 0);
    $pdf->Cell(30, 6, 'STATUS: ', 0, 0);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(30, 6, $details[0]->to_status, 0, 1, 'L');

    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(70, 4, $details[0]->br_fr_address, 0, 0);
    $pdf->Cell(50, 4, $details[0]->br_to_address, 0, 0);
    $pdf->Cell(40, 4, '', 0, 0);
    $pdf->Cell(30, 4, '', 0, 1, 'L');

    $pdf->ln(2);


    $pdf->SetFont('Arial', 'B', 10);
    $pdf->setFillColor(0, 0, 0);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(100, 7, 'Poduct Name', 'B', 0, 'L', 1);
    $pdf->Cell(45, 7, 'Quantity', 'B', 0, 'L', 1);
    $pdf->Cell(45, 7, 'Status', 'B', 1, 'L', 1);

    $pdf->SetFont('Arial', '', 10);
    $pdf->setFillColor(255, 255, 255);
    $pdf->SetTextColor(0, 0, 0);
    foreach ($details as $value) {
      $pdf->Cell(100, 7, $value->product_name, 0, 0, 'L', 1);
      $pdf->Cell(45, 7, number_format($value->qty, 2), 0, 0, 'L', 1);
      $pdf->Cell(45, 7, $value->item_status, 0, 1, 'L', 1);
    }


    //save file
    $pdf->Output('Transfer Order' . $details[0]->transfer_No . '.pdf', 'I');
  }


  //Delivery Challan report
  public function dcreport(Request $request, Vendor $vendor, transfer $transfer)
  {


    $company = $vendor->company(session('company_id'));

    //queries
    $details = $transfer->get_challan_Details($request->id);

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
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(35, -18, '', 0, 0);
    $pdf->Cell(105, -18, $company[0]->address, 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(50, -18, "Generate Date:  " . date('Y-m-d'), 0, 1, 'R');

    //report name
    $pdf->ln(15);
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(190, 10, 'Delivery Challan', 'B,T', 1, 'L');
    $pdf->ln(1);

    //details start here
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(70, 6, 'FROM :', 0, 0);
    $pdf->Cell(60, 6, 'TO:', 0, 0);
    $pdf->Cell(40, 6, 'CHALLAN NUMBER | ', 0, 0);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(20, 6, $details[0]->DC_No, 0, 1, 'L');

    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(70, 6, 'BRANCH MANAGER:', 0, 0);
    $pdf->Cell(60, 6, 'ADMINISTRATOR:', 0, 0);
    $pdf->Cell(30, 6, 'CREATED ON: ', 0, 0);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(30, 6, date('d-m-Y', strtotime($details[0]->date)), 0, 1, 'L');

    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(70, 6, $details[0]->deliverd_by, 0, 0);
    $pdf->Cell(60, 6, $details[0]->destination, 0, 0);
    $pdf->Cell(45, 6, 'SHIPMENT CHARGES: ', 0, 0);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(15, 6, $details[0]->shipment_amount, 0, 1, 'L');

    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(70, 4, $details[0]->del_add, 0, 0);
    $pdf->Cell(50, 4, $details[0]->des_add, 0, 0);
    $pdf->Cell(40, 4, '', 0, 0);
    $pdf->Cell(30, 4, '', 0, 1, 'L');

    $pdf->ln(2);


    $pdf->SetFont('Arial', 'B', 10);
    $pdf->setFillColor(0, 0, 0);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(75, 7, 'Poduct Name', 'B', 0, 'L', 1);
    $pdf->Cell(25, 7, 'Quantity', 'B', 0, 'L', 1);
    $pdf->Cell(25, 7, 'Cost Price', 'B', 0, 'L', 1);
    $pdf->Cell(40, 7, 'Shipment Amount', 'B', 0, 'L', 1);
    $pdf->Cell(25, 7, 'Total Cost', 'B', 1, 'L', 1);

    $pdf->SetFont('Arial', '', 10);
    $pdf->setFillColor(255, 255, 255);
    $pdf->SetTextColor(0, 0, 0);
    foreach ($details as $value) {
      $pdf->Cell(75, 7, $value->product_name, 0, 0, 'L', 1);
      $pdf->Cell(25, 7, number_format($value->deliverd_qty, 2), 0, 0, 'L', 1);
      $pdf->Cell(25, 7, number_format($value->cost_price, 2), 0, 0, 'L', 1);
      $pdf->Cell(40, 7, number_format($value->shipment_charges, 2), 0, 0, 'L', 1);
      $pdf->Cell(25, 7, number_format($value->shipment_charges + $value->cost_price, 2), 0, 1, 'L', 1);
    }
    //save file
    $pdf->Output('Delivery Challan' . $details[0]->DC_No . '.pdf', 'I');
  }

  //transfer report
  public function directTransferReport(Request $request, Vendor $vendor, transfer $transfer)
  {
    $company = $vendor->company(session('company_id'));

    //queries
    $details = $transfer->get_trf_details($request->id);

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
    $pdf->Cell(190, 10, 'Transfer Order', 'B,T', 1, 'L');
    $pdf->ln(1);

    //details start here
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(70, 6, 'FROM :', 0, 0);
    $pdf->Cell(60, 6, 'TO:', 0, 0);
    $pdf->Cell(40, 6, 'TRANSFER ORDER | ', 0, 0);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(20, 6, $details[0]->transfer_No, 0, 1, 'L');

    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(70, 6, 'BRANCH MANAGER:', 0, 0);
    $pdf->Cell(60, 6, 'ADMINISTRATOR:', 0, 0);
    $pdf->Cell(30, 6, 'CREATED ON: ', 0, 0);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(30, 6, date('d-m-Y', strtotime($details[0]->date)), 0, 1, 'L');

    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(70, 6, $details[0]->branch_from, 0, 0);
    $pdf->Cell(60, 6, $details[0]->branch_to, 0, 0);
    $pdf->Cell(30, 6, 'STATUS: ', 0, 0);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(30, 6, $details[0]->to_status, 0, 1, 'L');

    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(70, 4, $details[0]->br_fr_address, 0, 0);
    $pdf->Cell(50, 4, $details[0]->br_to_address, 0, 0);
    $pdf->Cell(40, 4, '', 0, 0);
    $pdf->Cell(30, 4, '', 0, 1, 'L');

    $pdf->ln(2);


    $pdf->SetFont('Arial', 'B', 10);
    $pdf->setFillColor(0, 0, 0);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(100, 7, 'Poduct Name', 'B', 0, 'L', 1);
    $pdf->Cell(45, 7, 'Quantity', 'B', 0, 'L', 1);
    $pdf->Cell(45, 7, 'Status', 'B', 1, 'L', 1);

    $pdf->SetFont('Arial', '', 10);
    $pdf->setFillColor(255, 255, 255);
    $pdf->SetTextColor(0, 0, 0);
    foreach ($details as $value) {
      $pdf->Cell(100, 7, $value->product_name, 0, 0, 'L', 1);
      $pdf->Cell(45, 7, number_format($value->qty, 2), 0, 0, 'L', 1);
      $pdf->Cell(45, 7, $value->item_status, 0, 1, 'L', 1);
    }


    //save file
    $pdf->Output('Transfer Order' . $details[0]->transfer_No . '.pdf', 'I');
  }
}
