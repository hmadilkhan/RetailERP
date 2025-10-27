<?php

namespace App\Http\Controllers;

use App\stock;
use App\Models\Branch;
use App\Models\Terminal;
use App\Models\InventoryStock;
use App\Models\TerminalStock;
use App\pdfClass;
use App\transfer;
use App\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use View;
use Response;

class StockController extends Controller
{
    public function index(stock $stock)
    {
        $branches = $stock->getBranches();
        return view('stock.branchwise-stock', compact('branches'));
    }

    public function getStock(Request $request, stock $stock)
    {
        $purchase = $stock->getStockByPO($request->id);
        $transfer = $stock->getStockByTransfer($request->id);
        $stocks = $stock->getStockByBranch($request->id);
        $details = $stock->getStockDateWiseDetails($request->id);
        $product = $stock->getProductName($request->id);
        
        $report = $stock->getProductReport($request->id, $request->branch);
        
        $pricelogs = $stock->getCostPriceLogs($request->id);
        $product_id = $request->id;
        $conversion_unit = DB::table("inventory_general")->where("id", $request->id)->get("weight_qty");

        return view('stock.stockDetails', compact('stocks', 'purchase', 'transfer', 'details', 'product', 'report', 'product_id', "conversion_unit", "pricelogs"));
    }


    public function brnchwisestock(stock $stock, request $request)
    {
        $details = $stock->getStockByBranchPageWise($request->branchid, $request->code, $request->name, $request->dept, $request->sdept, $request->search);
        return $details;
    }

    public function stockFilter(Request $request, stock $stock)
    {
        $report = $stock->getProductReportFilter($request->id, session('branch'), $request->from, $request->to);
        $html = View::make('stock._filter_fetch', compact('report'))->render();
        $response = array('status' => true, 'data' => $html);
        return Response::json($response);
    }


    public function exportPDF(Request $request, order $order, Vendor $vendor)
    {
        if ($request->status == "") {
            $request->status = 1;
        }
        $company = $vendor->company(session('company_id'));
        $ordersResult = $order->getOrders($request->fromdate, $request->todate);

        $pdf = app('Fpdf');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Image(public_path('assets/images/company/' . $company[0]->logo), 10, 10, -200);
        $pdf->SetFont('Arial', 'BU', 18);
        $pdf->MultiCell(0, 10, 'ORDERS REPORT', 0, 'C');
        $pdf->Cell(2, 2, '', 0, 1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, strtoupper($ordersResult[0]->order_status_name) . " ORDERS", 0, 1, 'C'); //Here is center title
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(10, 10, '', 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'Tayyeb Jamal', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, 'Hamid Hussain Farooqi Rd, P.E.C.H.S Block 2,', 0, 0, 'L');
        $pdf->Cell(0, 4, 'From : ' . $request->first, 0, 1, 'R');
        $pdf->Cell(0, 5, 'Karachi, Karachi City, Sindh', 0, 0, 'L');
        $pdf->Cell(0, 5, 'To : ' . $request->second, 0, 1, 'R');
        $pdf->Cell(0, 4, '021-34513353', 0, 0, 'L');
        $pdf->Cell(0, 4, '', 0, 1, 'R');
        $pdf->Cell(190, 1, '', 'B', 1);

        //Seperate
        $pdf->ln();
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(20, 8, 'Date', 'B', 0, 'C');
        $pdf->Cell(50, 8, 'Receipt No', 'B', 0, 'C');
        $pdf->Cell(60, 8, 'Customer', 'B', 0, 'C');
        $pdf->Cell(30, 8, 'OrderType', 'B', 0, 'C');
        $pdf->Cell(30, 8, 'Total Amount', 'B', 1, 'C');

        foreach ($ordersResult as $key => $value) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(20, 8, $value->date, 0, 0, 'C');
            $pdf->Cell(50, 8, $value->receipt_no, 0, 0, 'C');
            $pdf->Cell(60, 8, $value->name, 0, 0, 'L');
            $pdf->Cell(30, 8, $value->order_mode, 0, 0, 'C');
            $pdf->Cell(30, 8, $value->total_amount, 0, 1, 'C');
        }

        //save file
        $pdf->Output('temp.pdf', 'I');
    }

    public function stockReportPDF(Request $request)
    {
        $pdf = app('Fpdf');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        $company = DB::table('company')->where('company_id', session('company_id'))->get();
        $product = DB::table('inventory_general')->where('id', $request->id)->get();

        $pdf->Image(public_path('assets/images/company/' . $company[0]->logo), 10, 10, -300);
        $pdf->SetFont('Arial', 'BU', 18);
        $pdf->MultiCell(0, 10, 'STOCK DETAIL REPORT', 0, 'C');
        $pdf->Cell(2, 2, '', 0, 1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, strtoupper($company[0]->name), 0, 1, 'C');



        $pdf->Cell(10, 10, '', 0, 1);
        $pdf->Cell(0, 8, 'From :', 0, 0, 'L');
        $pdf->Cell(0, 8, 'To :', 0, 1, 'R');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, date("d M Y", strtotime($request->from)), 0, 0, 'L');
        $pdf->Cell(0, 8, date("d M Y", strtotime($request->to)), 0, 1, 'R');

        $pdf->ln();
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(190, 8, strtoupper($product[0]->product_name), 0, 1, 'C');



        $pdf->ln();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 8, 'Date', 1, 0, 'C');
        $pdf->Cell(80, 8, 'Narration', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Qty', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Stock', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Cost', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Retail', 1, 1, 'C');


        $report = DB::table('inventory_stock_report_table')->where('product_id', $request->id)->whereBetween('date', [$request->from, date('Y-m-d H:i:s', strtotime($request->to . ' +1 day'))])->get();
        $pdf->SetFont('Arial', '', 10);
        $stock = 0;
        foreach ($report as $value) {
            if ($value->narration == 'Stock Opening') {
                $stock = $value->qty;
            } elseif ($value->narration == 'Sales') {
                $stock = $stock - $value->qty;
            } elseif ($value->narration == 'Sales Return') {
                $stock = $stock + $value->qty;
            } elseif ($value->narration == 'Stock Purchase through Purchase Order') {
                $stock = $stock + $value->qty;
            } elseif ($value->narration == 'Stock Openend from csv file') {
                $stock = $stock + $value->qty;
            } elseif ($value->narration == 'Stock Return') {
                $stock = $stock - $value->qty;
            }

            $pdf->Cell(30, 8, date("d M Y", strtotime($value->date)), 1, 0, 'C');
            $pdf->Cell(80, 8, $value->narration, 1, 0, 'C');
            $pdf->Cell(20, 8, $value->qty, 1, 0, 'C');
            $pdf->Cell(20, 8, $stock, 1, 0, 'C');
            $pdf->Cell(20, 8, $value->cost, 1, 0, 'C');
            $pdf->Cell(20, 8, $value->retail, 1, 1, 'C');
        }

        //save file
        $pdf->Output('Payment Voucher.pdf', 'I');
        //        $pdf->Output(public_path('assets/pdf/').'temp.pdf', 'D');
    }

    public function getStockForTransfer(Request $request, stock $stock)
    {
        $branch = Branch::find(auth()->user()->branch_id);
        $products = $stock->branchwise($branch->branch_id);
        $terminals = Terminal::where("branch_id", session('branch'))->get();

        return view("stock.stocktransfer", [
            "branch" => $branch->branch_name,
            "products" => $products,
            "terminals" => $terminals,
        ]);
    }

    public function saveStockTransfer(Request $request)
    {
        try {
            $items = [];
            $count = count($request->product);
            for ($i = 0; $i < $count; $i++) {
                if ($request->qty[$i] != "") {
                    $item = [
                        "terminal_id" => $request->terminal,
                        "product_id" => $request->product[$i],
                        "qty" => $request->qty[$i],
                        "balance" => $request->qty[$i],
                        "date" => date("Y-m-d"),
                        "time" => date("H:i:s"),
                    ];
                    $this->invent_stock_detection(session('branch'), $request->product[$i], $request->qty[$i], "");
                    array_push($items, $item);
                }
            }
            TerminalStock::insert($items);
            return redirect()->back();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function invent_stock_detection($branchId, $itemCode, $totalQty, $status)
    {
        if (!empty($branchId) && $branchId > 0 && !empty($itemCode) && $itemCode > 0) {

            $result = DB::select('SELECT * FROM inventory_stock WHERE product_id = ? and branch_id = ? and status_id IN(1,3)', [$itemCode, $branchId]);
            $updatedstock = 0;

            if (!empty($result)) {

                if ($status == "Open") {
                    $weightQty = DB::select('SELECT weight_qty FROM `inventory_general` where id = ?', [$itemCode]);
                    $qty = $totalQty / $weightQty[0]->weight_qty;
                    $updatedstock = $qty;
                } else {
                    $updatedstock = $totalQty;
                }

                for ($s = 0; $s < sizeof($result); $s++) {
                    $value = DB::select("SELECT * FROM inventory_stock WHERE product_id = ? and branch_id = ? and status_id  IN(1,3)", [$itemCode, $branchId]);
                    $updatedstock = ($updatedstock - $value[0]->balance);

                    if ($updatedstock > 0) {
                        $columns = "balance = 0,status_id = 2";
                        $update =  InventoryStock::where("stock_id", $value[0]->stock_id)->update(["balance" => 0, "status_id" => 2]);
                    } else if ($updatedstock < 0) {
                        $updatedstock = $updatedstock * (-1);
                        $update =  InventoryStock::where("stock_id", $value[0]->stock_id)->update(["balance" => $updatedstock, "status_id" => 1]);
                        break;
                    } else if ($updatedstock == 0) {
                        $update =  InventoryStock::where("stock_id", $value[0]->stock_id)->update(["balance" => 0, "status_id" => 2]);
                        break;
                    }
                }
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function getTerminalStock(Request $request)
    {
        $terminals = Terminal::where("branch_id", session('branch'))->get();
        $branch = Branch::find(auth()->user()->branch_id);
        // return TerminalStock::with("inventory")->where("terminal_id",$terminals[0]->terminal_id)->where("date",date("Y-m-d"))->get();

        return view("stock.terminalstock", [
            "terminals" => $terminals,
            "branch" => $branch->branch_name,
        ]);
    }

    public function getTerminalStockDetails(Request $request)
    {
        if ($request->terminal_id != "") {
            $stock = TerminalStock::with("inventory", "inventory.uom", "inventory.department", "inventory.subdepartment")
                ->where("terminal_id", $request->terminal_id)
                ->where("date", date("Y-m-d"))
                ->select(DB::raw('SUM(qty) as qty'), "id", "product_id", "date")
                ->groupBy("product_id")
                ->get();
            return response()->json(["stock" => $stock, "status" => 200, "message" => "Data retrieved successfully."]);
        } else {
            return response()->json(["status" => 500, "message" => "Terminal Id Required."]);
        }
    }

    public function StockAdjustmentVoucher(Request $request, Vendor $vendor,stock $stock)
    {
        $company = $vendor->company(session('company_id'));

        //queries
        $details = $stock->stockAdjustmentVoucher($request->id);

        // if (!file_exists(asset('storage/images/company/qrcode.png'))) {
        //   $qrcodetext = $company[0]->name . " | " . $company[0]->ptcl_contact . " | " . $company[0]->address;
        //   \QrCode::size(200)
        //     ->format('png')
        //     ->generate($qrcodetext, Storage::disk('public')->put("images/company/", "qrcode.png"));
        // }

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
        $pdf->Cell(190, 10, 'Stock Adjustment Voucher', 'B,T', 1, 'L');
        $pdf->ln(1);

        //details start here
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(48, 6, 'VOUCHER :', 0, 0);
        $pdf->Cell(48, 6, 'BRANCH:', 0, 0);
        $pdf->Cell(48, 6, 'CREATED ON ', 0, 0);
        $pdf->Cell(46, 6, 'Created BY ', 0, 1);

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(48, 6, $details[0]->grn_id, 0, 0);
        $pdf->Cell(48, 6, $details[0]->branch_name, 0, 0);
        $pdf->Cell(48, 6, date('d-m-Y', strtotime($details[0]->date)), 0, 0);
        $pdf->Cell(46, 6, $details[0]->fullname, 0, 1);



        $pdf->ln(2);


        $pdf->SetFont('Arial', 'B', 10);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(50, 7, 'Item Code', 'B', 0, 'L', 1);
        $pdf->Cell(50, 7, 'Product Name', 'B', 0, 'L', 1);
        $pdf->Cell(30, 7, 'Quantity', 'B', 0, 'L', 1);
        $pdf->Cell(30, 7, 'Cost', 'B', 0, 'L', 1);
        $pdf->Cell(30, 7, 'Unit', 'B', 1, 'L', 1);

        $pdf->SetFont('Arial', '', 10);
        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        foreach ($details as $value) {
            $pdf->Cell(50, 7, $value->item_code, 0, 0, 'L', 1);
            $pdf->Cell(50, 7, $value->product_name, 0, 0, 'L', 1);
            $pdf->Cell(30, 7, number_format($value->qty, 2), 0, 0, 'L', 1);
            $pdf->Cell(30, 7, number_format($value->cost, 2), 0, 0, 'L', 1);
            $pdf->Cell(30, 7, $value->name, 0, 1, 'L', 1);
        }
        $pdf->ln(30);
        $pdf->Cell(150, 7, "", 0, 0, 'L', 1);
        $pdf->Cell(40, 7, "Receiving Signature", 'B', 0, 'R', 1);

        //save file
        $pdf->Output('stock_adjustment_voucher_' . $details[0]->grn_id . '.pdf', 'I');
    }
}
