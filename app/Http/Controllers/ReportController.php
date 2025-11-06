<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\order;
use App\report;
use App\salary;
use App\Vendor;
use App\inventory;
use App\Customer;
use App\dashboard;
use App\expense;
use App\Models\Order as OrderModel;
use App\Models\ServiceProviderOrders;
use App\Models\OrderDetails;
use App\Models\Terminal;
use App\Models\Branch;
use App\Models\Inventory as InventoryModel;
use App\Models\SalesOpening;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use PDF, Auth;
use App\pdfClass;
use App\pdfClassA4landscape;
use Dompdf\Dompdf;
use App\Exports\ItemSaleReportExport;
use App\Exports\ConsolidatedItemSaleReportExport;
use App\Exports\StockReport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IsdbDatewiseExport;
use App\Exports\ConsolidatedIsdbDatewiseExport;
use App\Exports\CustomerSalesExport;
use App\Exports\OrderReceivingReportExport;
use App\Exports\OrderReportExport;
use App\Exports\SalesDeclarationExport;
use App\Exports\StockReportExcelExport;
use App\Exports\StockReportExport;
use App\Exports\WebsiteItemSummaryExport;
use App\Exports\SaleReturnExport;
use App\Mail\DeclarationEmail;
use App\Models\Company;
use App\Models\DailyStock;
use App\Models\InventoryDepartment;
use App\receiptpdf;
use App\Services\OrderService;
use App\Services\StockAdjustmentService;
use Mail;
use \Illuminate\Support\Arr;
use App\stock;
use App\Traits\MediaTrait;
use App\userDetails;
use Crabbly\Fpdf\Fpdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\OrderReportPDF;
use App\Services\CustomerService;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class ReportController extends Controller
{
    use MediaTrait;
    public $filesArray = [];
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function erpreportdashboard(report $report, OrderService $orderService, CustomerService $customerService)
    {
        $branches = $report->get_branches();
        $terminals = $report->get_terminals();
        $departments = $report->get_departments();
        $paymentModes = $report->getPaymentModes();
        $salespersons = $orderService->getServiceProviders();
        $statuses = $orderService->getOrderStatus();
        $ordermodes = $orderService->getOrderModes();

        return view('reports.erpreports', compact('terminals', 'departments', 'branches', 'paymentModes', 'salespersons', 'statuses', 'ordermodes'));
    }

    public function show(report $report, salary $salary)
    {
        $getemp  = $salary->getemployee(session("branch"));
        return view('reports.reportDashboard', compact('getemp'));
    }


    public function attreport_show(report $report)
    {
        $branch = $report->getbranch();
        $emp = $report->getemployee();
        return view('reports.attendance-report', compact('branch', 'emp'));
    }

    public function attendancereport(report $report, Request $request)
    {
        $details = $report->attendance_sheet_report($request->branchid, $request->fromdate, $request->todate, $request->empid, $request->approchid);
        return $details;
    }

    // Item Sales Report
    public function getIndex(Request $request, order $order, Customer $customer)
    {

        $customer = $customer->getcustomers();
        $paymentMode = $order->paymentMode();
        $mode = $order->ordersMode();
        $branch = $order->getBranch();

        $inventory = DB::table('inventory_general')->select('id', 'item_code', 'product_name')
            ->where('company_id', Auth::user()->company_id)->get();

        return view('reports.item-sale-report', compact('customer', 'paymentMode', 'mode', 'branch', 'inventory'));
    }

    public function getConsolidatedItemSaleReport(Request $request, order $order, Customer $customer)
    {

        $customer = $customer->getcustomers();
        $paymentMode = $order->paymentMode();
        $mode = $order->ordersMode();
        $branch = $order->getBranch();
        $departments = DB::table('inventory_department')->where('company_id', Auth::user()->company_id)->get();

        $inventory = DB::table('inventory_general')->select('id', 'item_code', 'product_name')
            ->where('company_id', Auth::user()->company_id)->get();

        return view('reports.consolidated-item-sale-report', compact('customer', 'paymentMode', 'mode', 'branch', 'inventory', 'departments'));
    }

    public function postConsolidatedItemSaleReport(Request $request)
    {
        $totalItems = 0;
        $total = $this->getItemTotalQuery($request);
        foreach ($total as $value) {
            $totalItems += $value->total;
        }
        $record = $this->getItemSalesQuery($request);
        $total = $this->getItemTotalQuery($request);
        $totalReceipts = $this->getReceiptCount($request);
        // $totalQty = $total[0]->total_qty;
        $totalQty = $totalItems;
        $totalAmount = $total[0]->total_amount;
        $totalCountReceipts = $totalReceipts[0]->total_receipts;
        $totalAmountReceipts = $totalReceipts[0]->total_amount;
        return view("partials.reports.consolidated-item-sale-report", compact("record", "totalQty", "totalAmount", "totalCountReceipts", "totalAmountReceipts"));
    }

    public function postIndex(Request $request, order $order, Customer $customer)
    {

        $customer = $customer->getcustomers();
        $paymentMode = $order->paymentMode();
        $mode = $order->ordersMode();
        $branch = $order->getBranch();

        $inventory = DB::table('inventory_general')->select('id', 'item_code', 'product_name')
            ->where('company_id', Auth::user()->company_id)->get();

        if (isset($request->fromdate)) {
            $record = DB::table('sales_receipt_details')
                ->join('sales_receipts', 'sales_receipts.id', 'sales_receipt_details.receipt_id')
                ->join('branch', 'branch.branch_id', 'sales_receipts.branch')
                ->join('terminal_details', 'terminal_details.terminal_id', 'sales_receipts.terminal_id')
                ->selectRaw('sales_receipt_details.receipt_id,sales_receipt_details.item_code,sum(sales_receipt_details.total_qty) as total_qty,sales_receipt_details.item_name,sales_receipt_details.total_amount,sales_receipt_details.item_price,branch,branch_name as branch,terminal_details.terminal_name as terminal,sales_receipts.date')
                ->whereRaw('sales_receipts.date >="' . $request->fromdate . '"')
                ->groupby('sales_receipt_details.item_code')
                ->get();

            //$record = $request->fromdate;


            // select sales_receipt_details.receipt_id,sales_receipt_details.item_code,sum(sales_receipt_details.total_qty) as total_qty from sales_receipt_details Left Join sales_receipts ON sales_receipt_details.receipt_id = sales_receipts.id where sales_receipts.date >= '2022-05-30'  and sales_receipts.date <= '2022-06-01' and sales_receipt_details.item_code = 194830 GROUP By sales_receipt_details.item_code
        }


        if (isset($request->todate)) {
            $record = DB::table('sales_receipt_details')
                ->join('sales_receipts', 'sales_receipts.id', 'sales_receipt_details.receipt_id')
                ->join('branch', 'branch.branch_id', 'sales_receipts.branch')
                ->join('terminal_details', 'terminal_details.terminal_id', 'sales_receipts.terminal_id')
                ->join('inventory_general', 'inventory_general.item_code', 'sales_receipt_details.item_code')
                ->selectRaw('sales_receipt_details.receipt_id,sales_receipt_details.item_code,sum(sales_receipt_details.total_qty) as total_qty,sales_receipt_details.total_amount,inventory_general.product_name as item_name,sales_receipt_details.item_price,branch,branch_name as branch,terminal_details.terminal_name as terminal,sales_receipts.date')
                ->whereRaw('sales_receipts.date <= "' . $request->todate . '"')
                ->groupby('sales_receipt_details.item_code')
                ->get();
        }


        if (isset($request->fromdate) && isset($request->todate)) {
            $record = DB::table('sales_receipt_details')
                ->join('sales_receipts', 'sales_receipts.id', 'sales_receipt_details.receipt_id')
                ->join('branch', 'branch.branch_id', 'sales_receipts.branch')
                ->join('terminal_details', 'terminal_details.terminal_id', 'sales_receipts.terminal_id')
                ->join('inventory_general', 'inventory_general.item_code', 'sales_receipt_details.item_code')
                ->selectRaw('sales_receipt_details.receipt_id,sales_receipt_details.item_code,sum(sales_receipt_details.total_qty) as total_qty,sales_receipt_details.total_amount,inventory_general.product_name as item_name,sales_receipt_details.item_price,branch,branch_name as branch,terminal_details.terminal_name as terminal,sales_receipt.date')
                ->whereRaw('sales_receipts.date >= "' . $request->fromdate . '" and sales_receipts.date <= "' . $request->todate . '" ')
                ->groupby('sales_receipt_details.item_code')
                ->get();
        }


        // if(isset($request->get('product-name')) && !isset($request->fromdate) && !isset($request->todate)){
        //     $record = DB::table('sales_receipt_details')
        //                 ->join('sales_receipts','sales_receipts.id','sales_receipt_details.receipt_id')
        //                 ->join('branch','branch.branch_id','sales_receipts.branch')
        //                 ->join('terminal_details','terminal_details.terminal_id','sales_receipts.terminal_id')
        //                 ->join('inventory_general','inventory_general.item_code','sales_receipt_details.item_code')
        //                 ->selectRaw('sales_receipt_details.receipt_id,sales_receipt_details.item_code,sum(sales_receipt_details.total_qty) as total_qty,sales_receipt_details.total_amount,inventory_general.product_name as item_name,sales_receipt_details.item_price,branch,branch_name as branch,terminal_details.terminal_name as terminal')
        //                 ->where('inventory_general.product_name',$request->get('product-name'))
        //                 ->groupby('sales_receipt_details.item_code')
        //                 ->get();
        // } 

        // if(isset($request->get('product-name')) && isset($request->fromdate) && !isset($request->todate)){
        //     $record = DB::table('sales_receipt_details')
        //                 ->join('sales_receipts','sales_receipts.id','sales_receipt_details.receipt_id')
        //                 ->join('branch','branch.branch_id','sales_receipts.branch')
        //                 ->join('terminal_details','terminal_details.terminal_id','sales_receipts.terminal_id')
        //                 ->join('inventory_general','inventory_general.item_code','sales_receipt_details.item_code')
        //                 ->selectRaw('sales_receipt_details.receipt_id,sales_receipt_details.item_code,sum(sales_receipt_details.total_qty) as total_qty,sales_receipt_details.total_amount,inventory_general.product_name as item_name,sales_receipt_details.item_price,branch,branch_name as branch,terminal_details.terminal_name as terminal')
        //                 ->where('inventory_general.product_name',$request->get('product-name'))
        //                 ->whereRaw('sales_receipts.date >= "'.$request->fromdate.'"')
        //                 ->groupby('sales_receipt_details.item_code')
        //                 ->get();
        // }         

        // if(isset($request->get('product-name')) && !isset($request->fromdate) && isset($request->todate)){
        //     $record = DB::table('sales_receipt_details')
        //                 ->join('sales_receipts','sales_receipts.id','sales_receipt_details.receipt_id')
        //                 ->join('branch','branch.branch_id','sales_receipts.branch')
        //                 ->join('terminal_details','terminal_details.terminal_id','sales_receipts.terminal_id')
        //                 ->join('inventory_general','inventory_general.item_code','sales_receipt_details.item_code')
        //                 ->selectRaw('sales_receipt_details.receipt_id,sales_receipt_details.item_code,sum(sales_receipt_details.total_qty) as total_qty,sales_receipt_details.total_amount,inventory_general.product_name as item_name,sales_receipt_details.item_price,branch,branch_name as branch,terminal_details.terminal_name as terminal')
        //                 ->where('inventory_general.product_name',$request->get('product-name'))
        //                 ->whereRaw('sales_receipts.date >= "'.$request->todate.'"')
        //                 ->groupby('sales_receipt_details.item_code')
        //                 ->get();
        // }    


        // if(isset($request->get('product-name')) && isset($request->fromdate) && isset($request->todate)){
        //     $record = DB::table('sales_receipt_details')
        //                 ->join('sales_receipts','sales_receipts.id','sales_receipt_details.receipt_id')
        //                 ->join('branch','branch.branch_id','sales_receipts.branch')
        //                 ->join('terminal_details','terminal_details.terminal_id','sales_receipts.terminal_id')
        //                 ->join('inventory_general','inventory_general.item_code','sales_receipt_details.item_code')
        //                 ->selectRaw('sales_receipt_details.receipt_id,sales_receipt_details.item_code,sum(sales_receipt_details.total_qty) as total_qty,sales_receipt_details.total_amount,sales_receipt_details.item_price,branch,branch_name as branch,terminal_details.terminal_name as terminal,inventory_general.product_name as item_name')
        //                 ->where('inventory_general.product_name',$request->get('product-name'))
        //                 ->whereRaw('sales_receipts.date >= "'.$request->fromdate.'" and sales_receipts.date <= "'.$request->todate.'"')
        //                 ->groupby('sales_receipt_details.item_code')
        //                 ->get();
        // }        


        //  if(isset($request->terminal) && !isset($request->get('product-name')) && !isset($request->fromdate) && !isset($request->todate)){
        //     $record = DB::table('sales_receipt_details')
        //                 ->join('sales_receipts','sales_receipts.id','sales_receipt_details.receipt_id')
        //                 ->join('branch','branch.branch_id','sales_receipts.branch')
        //                 ->join('terminal_details','terminal_details.terminal_id','sales_receipts.terminal_id')
        //                 ->join('inventory_general','inventory_general.item_code','sales_receipt_details.item_code')
        //                 ->selectRaw('sales_receipt_details.receipt_id,sales_receipt_details.item_code,sum(sales_receipt_details.total_qty) as total_qty,sales_receipt_details.total_amount,sales_receipt_details.item_price,branch,branch_name as branch,terminal_details.terminal_name as terminal,inventory_general.product_name as item_name')
        //                 ->where('terminal_details.terminal_name',$request->terminal)
        //                 ->groupby('sales_receipt_details.item_code')
        //                 ->get();
        // }         

        // if(isset($request->terminal) && isset($request->get('product-name')) && !isset($request->fromdate) && !isset($request->todate)){
        //     $record = DB::table('sales_receipt_details')
        //                 ->join('sales_receipts','sales_receipts.id','sales_receipt_details.receipt_id')
        //                 ->join('branch','branch.branch_id','sales_receipts.branch')
        //                 ->join('terminal_details','terminal_details.terminal_id','sales_receipts.terminal_id')
        //                 ->join('inventory_general','inventory_general.item_code','sales_receipt_details.item_code')
        //                 ->selectRaw('sales_receipt_details.receipt_id,sales_receipt_details.item_code,sum(sales_receipt_details.total_qty) as total_qty,sales_receipt_details.total_amount,sales_receipt_details.item_price,branch,branch_name as branch,terminal_details.terminal_name as terminal,inventory_general.product_name as item_name')
        //                 ->whereRaw('terminal_details.terminal_name = "'.$request->terminal.'" and inventory_general.product_name = "'.$request->get('product-name').'"')
        //                 ->groupby('sales_receipt_details.item_code')
        //                 ->get();
        // }   


        return view('reports.item-sale-report', compact('customer', 'paymentMode', 'mode', 'branch', 'record', 'inventory'));
    }

    public function searchitem_sale_report(Request $request, order $order, Customer $customer)
    {


        return $this->item_sale_report($request, $order, $customer);
    }

    public function getItemSaleReport(Request $request)
    {
        $totalItems = 0;
        $total = $this->getItemTotalQuery($request);
        foreach ($total as $value) {
            $totalItems += $value->total;
        }
        // return $this->getItemSalesQuery($request);
        $record = $this->getItemSalesQuery($request);
        $total = $this->getItemTotalQuery($request);
        $totalReceipts = $this->getReceiptCount($request);
        // $totalQty = $total[0]->total_qty;
        $totalQty = $totalItems;
        $totalAmount = $total[0]->total_amount;
        $totalCountReceipts = $totalReceipts[0]->total_receipts;
        $totalAmountReceipts = $totalReceipts[0]->total_amount;
        return view("partials.reports.item-sale-report", compact("record", "totalQty", "totalAmount", "totalCountReceipts", "totalAmountReceipts"));
    }

    public function getConsolidatedItemSaleReportExcelExport(Request $request, report $report)
    {
        if ($request->type == "consolidated") {
            return $this->ConsolidatedReport($request, "specific");
        } else {
            return $this->DatewiseReport($request, $report, "specific");
        }
    }

    public function getItemSaleReportExcelExport(Request $request, report $report)
    {
        if ($request->type == "consolidated") {
            return $this->ConsolidatedReport($request, "normal");
        } else {
            return $this->DatewiseReport($request, $report, "normal");
        }
    }

    public function generateDateRange(Carbon $start_date, Carbon $end_date)
    {
        $dates = [];

        for ($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }
    public function ConsolidatedReport(Request $request, $mode)
    {
        if ($request->branch == "all") {
            $branch = Branch::with("company:company_id,name")->where("company_id", session("company_id"))->get();
        } else {
            $branch = Branch::with("company:company_id,name")->where("branch_id", $request->branch)->get();
        }
        // $branch = Branch::with("company:company_id,name")->where("branch_id",$request->branch)->first();
        $record = $this->getItemSalesQuery($request);
        $datearray = [
            "from" => $request->fromdate,
            "to" => $request->todate,
        ];
        if ($mode == "specific") {
            return Excel::download(new ItemSaleReportExport($record, $branch, $datearray, $mode), "Consolidated Item Sale Report Export.xlsx");
        } else {
            return Excel::download(new ItemSaleReportExport($record, $branch, $datearray, $mode), "Item Sale Report Export.xlsx");
        }
    }

    public function DatewiseReport(Request $request, report $report, $mode)
    {
        if ($mode == "specific") {
            return  Excel::download(new ConsolidatedIsdbDatewiseExport($report, $request), "Consolidated Item Sales Database.xlsx");
        } else {
            return  Excel::download(new IsdbDatewiseExport($report, $request), "Item Sales Database.xlsx");
        }
    }

    public function getItemSaleReportPdfExport(Request $request)
    {
        if ($request->branch == "all") {
            $branch = Branch::with("company:company_id,name")->where("company_id", session("company_id"))->first();
        } else {
            $branch = Branch::with("company:company_id,name")->where("branch_id", $request->branch)->first();
        }

        $record = $this->getItemSalesQuery($request);

        $dates = [
            "from" => $request->fromdate,
            "to" => $request->todate,
        ];

        // Configure mPDF with Urdu settings
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 8,
            'margin_bottom' => 8,
            'margin_left' => 10,
            'margin_right' => 10,
            'fontDir' => array_merge($fontDirs, [
                resource_path('fonts'),
            ]),
            'fontdata' => $fontData + [
                'jameel' => [
                    'R' => 'JameelNooriNastaleeq.ttf',
                ],
            ],
            'default_font' => 'jameel',
            'directionality' => 'ltr',
            'autoLangToFont' => true,
            'autoScriptToLang' => true,
        ]);

        // Add footer with page numbers
        $mpdf->SetHTMLFooter('
             <div style="text-align: center; font-size: 8pt;font-style: italic;color: #666;margin-top: 20px;">
                 Page {PAGENO} of {nbpg}
             </div>
         ');

        // Get the view content
        $html = view('partials.reports.item-sale-report-export', [
            'record' => $record,
            'branch' => $branch,
            'dates' => $dates,
            'mode' => 'normal'
        ])->render();

        // Generate PDF
        $mpdf->WriteHTML($html);

        // Output PDF
        return $mpdf->Output('Item Sale Database.pdf', 'I');
        // if ($request->branch == "all") {
        //     $branch = Branch::with("company:company_id,name")->where("company_id", session("company_id"))->get();
        // } else {
        //     $branch = Branch::with("company:company_id,name")->where("branch_id", $request->branch)->get();
        // }

        // $record = $this->getItemSalesQuery($request);

        // $datearray = [
        //     "from" => $request->fromdate,
        //     "to" => $request->todate,
        // ];
        // return Excel::download(new ItemSaleReportExport($record, $branch, $datearray, "normal"), 'Item Sale Report Export.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }

    public function getItemSalesQuery(Request $request)
    {
        // return DB::table("branch")->where("company_id",session("company_id"))->pluck("branch_id");
        // return DB::table("terminal_details")->where("branch_id",['219','237','238','239','240','243','244','245','246','249','250','251','252','253','256','259'])->pluck("branch_id"))->pluck("terminal_id");
        // return $openingIds = SalesOpening::whereBetween("date", [$request->fromdate, $request->todate])->whereIn("terminal_id",DB::table("terminal_details")->where("branch_id",DB::table("branch")->where("company_id",session("company_id"))->pluck("branch_id"))->pluck("terminal_id"))->pluck("opening_id");	;
        // $openingIds = SalesOpening::whereBetween("date", [$request->fromdate, $request->todate])->where("terminal_id",$request->terminal)->pluck("opening_id");	
        // return SalesOpening::whereBetween("date", [$request->fromdate, $request->todate])->whereIn("terminal_id",DB::table("terminal_details")->where("branch_id",DB::table("branch")->where("company_id",session("company_id"))->pluck("branch_id"))->pluck("terminal_id"))->pluck("opening_id");

        if ($request->branch == "all") {
            $openingIds = SalesOpening::whereBetween("date", [$request->fromdate, $request->todate])->whereIn("terminal_id", DB::table("terminal_details")->whereIn("branch_id", DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"))->pluck("terminal_id"))->pluck("opening_id");
        } else {
            $openingIds = SalesOpening::whereBetween("date", [$request->fromdate, $request->todate])->where("terminal_id", $request->terminal)->pluck("opening_id");
        }

        return OrderDetails::with("order", "inventory:id,item_code,product_name,weight_qty", "order.terminal:terminal_id,terminal_name", "order.branchrelation:branch_id,branch_name,code")
            ->whereHas('order', function ($q) use ($request, $openingIds) {
                $q->when($request->declaration == "declaration", function ($q) use ($request, $openingIds) {
                    $q->whereIn("opening_id", $openingIds);
                }, function ($q) use ($request) {
                    $q->whereBetween("date", [$request->fromdate, $request->todate]);
                });
                // $q->whereBetween("date", [$request->fromdate, $request->todate]);//->where("branch",auth()->user()->branch_id)

                $q->when($request->branch != "" && $request->branch != "all", function ($q) use ($request) {
                    $q->where("branch", $request->branch);
                });
                $q->when($request->branch == "" && $request->branch != "all", function ($q) use ($request) {
                    $q->where("branch", auth()->user()->branch_id);
                });
                $q->when($request->branch == "all", function ($query) use ($request) {
                    $query->whereIn('sales_receipts.branch', DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"));
                });
                $q->when($request->terminal != "", function ($q) use ($request) {
                    $q->where("terminal_id", $request->terminal);
                });
                $q->when($request->customerNo != "", function ($q) use ($request) {
                    $q->where("customer_id", $request->customerNo);
                });
                $q->when($request->paymentmode != "", function ($q) use ($request) {
                    $q->where("payment_id", $request->paymentmode);
                });
                $q->when($request->ordermode != "", function ($q) use ($request) {
                    $q->where("order_mode_id", $request->ordermode);
                });
            })
            ->when($request->department != "", function ($q) use ($request) {
                $q->whereIn("item_code", InventoryModel::where("company_id", session("company_id"))->where("department_id", $request->department)->pluck("id"));
            })
            ->when($request->product != "", function ($q) use ($request) {
                $q->where("item_code", $request->product);
            })
            ->select("receipt_detail_id", "receipt_id", "item_code", "item_price", DB::raw('SUM(total_qty) as total_qty'), DB::raw('AVG(item_price) as avg_price'), DB::raw('SUM(item_price*total_qty) as total_amount'), DB::raw('SUM(total_amount) as total_amount'))
            ->groupBy("item_code") //,"item_price"
            ->orderBy("item_code", "asc")
            ->get();
        // ->toSql();

    }

    public function getOrderRecievingExport(Request $request, report $report)
    {
        $details = $report->orderAmountReceivableTerminalExcel($request->fromdate, $request->todate, $request->branch);
        $companyName = Company::findOrFail(session("company_id"));
        $companyName = $companyName->name;
        $datearray = [
            "from" => $request->fromdate,
            "to" => $request->todate,
        ];
        return Excel::download(new OrderReceivingReportExport($details, $companyName, $datearray), "Order Receiving Report.xlsx");
    }

    public function getCustomerSalesExport(Request $request, report $report)
    {
        if ($request->customer == "null") {
            $request->customer = "all";
        }
        $details = $report->customerSalesReport($request->fromdate, $request->todate, $request->branch, $request->customer);
        $companyName = Company::findOrFail(session("company_id"));
        $companyName = $companyName->name;
        $datearray = [
            "from" => $request->fromdate,
            "to" => $request->todate,
        ];
        return Excel::download(new CustomerSalesExport($details, $companyName, $datearray), "Customer Sales Report.xlsx");
    }

    public function getOrdersReportExcelExport(Request $request, report $report, order $order)
    {
        return $this->ConsolidatedOrderReport($request, "normal", $order);
    }


    public function ConsolidatedOrderReport(Request $request, $mode, order $order)
    {
        // $branch = Branch::with("company:company_id,name")->where("branch_id",$request->branch)->first();
        if ($request->branch == "all") {
            $branch = Branch::with("company:company_id,name")->where("company_id", session("company_id"))->get();
        } else {
            $branch = Branch::with("company:company_id,name")->where("branch_id", $request->branch)->get();
        }

        $record = $this->getOrdersQuery($request);

        $datearray = [
            "from" => $request->fromdate,
            "to" => $request->todate,
        ];
        if ($request->report == "excel") {
            return Excel::download(new OrderReportExport($record, $branch, $datearray, $mode), "Orders Report.xlsx");
        } else {
            // return Excel::download(new OrderReportExport($record, $branch, $datearray, $mode), 'Orders Report.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
            return $this->orderReportPdf($request->branch, $branch, $datearray, $record);
        }
    }

    public function orderReportPdf($branchFilter, $branch, $dates, $record)
    {
        $pdf = new OrderReportPDF('L', 'mm', 'A3');

        if ($branchFilter[0] == "all") {
            $pdf->branch = "all"; // Assign to class property
        } else {
            $pdf->branch = $branch; // Assign to class property
        }


        $pdf->dates = $dates;   // Assign to class property
        $pdf->SetFont('Arial', '', 12);

        // Add a page
        $pdf->AddPage();

        // Table Header
        // $pdf->Header();
        // $pdf->TableHeader();

        // Table Rows
        foreach ($record as $value) {
            $pdf->TableRow($value, function ($key) {
                return session($key);
            });
        }

        // Output PDF
        $pdf->Output('I', 'Orders_Report.pdf');
    }

    public function getOrdersQuery(Request $request)
    {
        $request->branch = explode(",", $request->branch);
        $request->terminal = explode(",", $request->terminal);
        $request->status = explode(",", $request->status);

        if (!empty($request->branch) && $request->branch[0] == "all" && !empty($request->terminal) && $request->terminal[0] == "all") {
            $openingIds = SalesOpening::whereBetween("date", [$request->fromdate, $request->todate])->whereIn("terminal_id", DB::table("terminal_details")->whereIn("branch_id", DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"))->pluck("terminal_id"))->pluck("opening_id");
        } else if (!empty($request->branch) &&  $request->branch[0] != "all" && !empty($request->terminal) && $request->terminal[0] == "all") {
            $openingIds = SalesOpening::whereBetween("date", [$request->fromdate, $request->todate])->whereIn("terminal_id", DB::table("terminal_details")->whereIn("branch_id", DB::table("branch")->whereIn("branch_id", $request->branch)->pluck("branch_id"))->pluck("terminal_id"))->pluck("opening_id");
        } else {
            $openingIds = SalesOpening::whereBetween("date", [$request->fromdate, $request->todate])->whereIn("terminal_id", $request->terminal)->pluck("opening_id");
        }

        // if (!empty($request->branch) && $request->branch[0] == "all") {
        //     return 1;
        //     $openingIds = SalesOpening::whereBetween("date", [$request->fromdate, $request->todate])->whereIn("terminal_id", DB::table("terminal_details")->whereIn("branch_id", DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"))->pluck("terminal_id"))->pluck("opening_id");
        // } else {
        //     return 2;
        //     $openingIds = SalesOpening::whereBetween("date", [$request->fromdate, $request->todate])->where("terminal_id", $request->terminal)->pluck("opening_id");
        // }


        $amountSum = OrderDetails::selectRaw('sum(total_qty)')
            ->whereColumn('receipt_id', 'id')
            ->getQuery();

        return OrderModel::withCount("orderdetails")->with("salesperson", "customer", "terminal:terminal_id,terminal_name", "branchrelation:branch_id,branch_name,code", "orderStatus:order_status_id,order_status_name", "mode:order_mode_id,order_mode", "payment:payment_id,payment_mode", "statusLogs", "orderAccount")
            ->when($request->type == "declaration", function ($q) use ($request, $openingIds) {
                $q->whereIn("opening_id", $openingIds);
            }, function ($q) use ($request) {
                $q->whereBetween("date", [$request->fromdate, $request->todate]);
            })
            ->when(!empty($request->branch) &&  $request->branch[0] != 'all', function ($q) use ($request) {
                $q->whereIn("branch", $request->branch);
            })
            ->when(!empty($request->branch) && $request->branch[0] == "all", function ($q) use ($request) {
                $q->whereIn("branch", DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"));
            })
            // ->when($request->branch == "all", function ($query) use ($request) {
            //     $query->whereIn('sales_receipts.branch', DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"));
            // })
            // ->when($request->terminal != "" && $request->terminal != "null", function ($q) use ($request) {
            //     $q->where("terminal_id", $request->terminal);
            // })

            ->when(!empty($request->terminal) && $request->terminal[0] != "all", function ($query) use ($request) {
                $query->whereIn('terminal_id', $request->terminal);
            })
            ->when($request->customer != "", function ($q) use ($request) {
                $q->where("customer_id", $request->customer);
            })
            ->when($request->paymentmode != "", function ($q) use ($request) {
                $q->where("payment_id", $request->paymentmode);
            })
            ->when($request->ordermode != "", function ($q) use ($request) {
                $q->where("order_mode_id", $request->ordermode);
            })
            ->when($request->department != "", function ($q) use ($request) {
                $q->whereIn("item_code", InventoryModel::where("company_id", session("company_id"))->where("department_id", $request->department)->pluck("id"));
            })
            ->when($request->customerNo != "", function ($query) use ($request) {
                $query->where('customers.mobile', $request->customerNo);
            })
            ->when($request->sales_tax != "", function ($query) {
                $query->where("fbrInvNumber", '!=', "");
            })
            ->when(!empty($request->status) && $request->status[0] != null, function ($query) use ($request) {
                $query->whereIn('status', $request->status);
            })
            ->when($request->salesperson != "" && $request->salesperson != "all", function ($query) use ($request) {
                $query->where("sales_person_id", '=', $request->salesperson);
            })
            ->when($request->order_no != "", function ($query) use ($request) {
                $query->where('id', $request->order_no);
            })
            ->when($request->machineOrderNo != "", function ($query) use ($request) {
                $query->where('machine_terminal_count', $request->machineOrderNo);
            })
            ->when($request->category != "" && $request->category != "all", function ($query) use ($request) {
                $query->where("web", "=", $request->category);
            })
            // ->where("web", "=", 0)
            ->selectSub($amountSum, 'amount_sum')
            ->orderBy("id", "asc")
            ->get();
        // ->toSql();
    }

    public function getStockReportExcelExport(Request $request, stock $stock)
    {
        $details = $stock->getStockByBranchForExcel($request->branchid, $request->code, $request->name, $request->dept, $request->sdept);
        $branch = Branch::findOrFail($request->branchid);
        // return Excel::download(new StockReport($details,$branch->branch_name), 'products.xlsx');
        return Excel::download(new StockReportExport($details, $branch->branch_name), "Stock Report.xlsx");
    }



    public function dailyStockReportExport(Request $request, StockAdjustmentService $stockAdjustmentService)
    {
        $details = $stockAdjustmentService->getStockReport($request->from, $request->to, $request->branch, $request->department, $request->subdepartment);
        $branch = Branch::findOrFail($request->branch);
        $datearray = [
            "from" => $request->fromdate,
            "to" => $request->todate,
        ];
        return Excel::download(new StockReportExcelExport($details, $branch->branch_name, $datearray), "Daily Stock Report.xlsx");
    }

    public function getSalesDeclarationExport(Request $request, report $report)
    {
        // $branch = "";
        $companyName = Company::where("company_id", session("company_id"))->first();
        $companyName = $companyName->name;
        $branchName  = "";
        if ($request->branch == "all") {
            $branch = Branch::with("company")->where("company_id", session('company_id'))->get();
            $branchName = "All Branches";
        } else {
            $branch = Branch::with("company")->where("branch_id", $request->branch)->first();
            $branchName = $branch->branch_name;
        }

        if (is_null($request->terminal)) {
            $terminal = 0;
        } else {
            $terminal = $request->terminal;
        }
        $datearray = [
            "from" => $request->from,
            "to" => $request->to,
        ];
        $details =  $report->sales_details_excel_query($request->branch, $request->terminal, $request->from, $request->to);
        $details =  collect($details);
        return Excel::download(new SalesDeclarationExport($details, $branch, $datearray, $terminal, $companyName, $branchName), "Sales Declaration Report.xlsx");
    }

    public function getReceiptCount(Request $request)
    {
        if ($request->branch == "all") {
            $openingIds = SalesOpening::whereBetween("date", [$request->fromdate, $request->todate])->whereIn("terminal_id", DB::table("terminal_details")->whereIn("branch_id", DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"))->pluck("terminal_id"))->pluck("opening_id");
        } else {
            $openingIds = SalesOpening::whereBetween("date", [$request->fromdate, $request->todate])->where("terminal_id", $request->terminal)->pluck("opening_id");
        }
        $q = OrderModel::query();
        $q->when($request->declaration == "declaration", function ($q) use ($request, $openingIds) {
            $q->whereIn("opening_id", $openingIds);
        }, function ($q) use ($request) {
            $q->whereBetween("date", [$request->fromdate, $request->todate]);
        })
            ->when($request->branch != "" && $request->branch != "all", function ($q) use ($request) {
                $q->where("branch", $request->branch);
            })
            ->when($request->branch == "" && $request->branch != "all", function ($q) use ($request) {
                $q->where("branch", auth()->user()->branch_id);
            })
            ->when($request->branch == "all", function ($query) use ($request) {
                $query->whereIn('sales_receipts.branch', DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"));
            })
            ->when($request->terminal != "", function ($q) use ($request) {
                $q->where("terminal_id", $request->terminal);
            })
            ->when($request->customerNo != "", function ($q) use ($request) {
                $q->where("customer_id", $request->customerNo);
            })
            ->when($request->paymentmode != "", function ($q) use ($request) {
                $q->where("payment_id", $request->paymentmode);
            })
            ->when($request->ordermode != "", function ($q) use ($request) {
                $q->where("order_mode_id", $request->ordermode);
            })
            ->when($request->department != "", function ($q) use ($request) {
                $q->whereIn("id", OrderDetails::whereIn("item_code", InventoryModel::where("company_id", session("company_id"))->where("department_id", $request->department)->pluck("id"))->groupBy("receipt_id")->pluck("receipt_id"));
            })
            ->select(DB::raw('Count(*) as total_receipts'), DB::raw('SUM(total_amount) as total_amount'));
        return $q->get();
    }

    public function getItemTotalQuery(Request $request)
    {
        // $openingIds = SalesOpening::whereBetween("date", [$request->fromdate, $request->todate])->where("terminal_id",$request->terminal)->pluck("opening_id");	
        if ($request->branch == "all") {
            $openingIds = SalesOpening::whereBetween("date", [$request->fromdate, $request->todate])->whereIn("terminal_id", DB::table("terminal_details")->whereIn("branch_id", DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"))->pluck("terminal_id"))->pluck("opening_id");
        } else {
            $openingIds = SalesOpening::whereBetween("date", [$request->fromdate, $request->todate])->where("terminal_id", $request->terminal)->pluck("opening_id");
        }
        return OrderDetails::with("order", "inventory:id,item_code,product_name,weight_qty", "order.terminal:terminal_id,terminal_name", "order.branchrelation:branch_id,branch_name,code")
            ->whereHas('order', function ($q) use ($request, $openingIds) {
                $q->when($request->declaration == "declaration", function ($q) use ($request, $openingIds) {
                    $q->whereIn("opening_id", $openingIds);
                }, function ($q) use ($request) {
                    $q->whereBetween("date", [$request->fromdate, $request->todate]);
                });
                $q->when($request->branch == "all", function ($query) use ($request) {
                    $query->whereIn('sales_receipts.branch', DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"));
                });
                $q->when($request->branch != "" && $request->branch != "all", function ($q) use ($request) {
                    $q->where("branch", $request->branch);
                });
                $q->when($request->branch == "" && $request->branch != "all", function ($q) use ($request) {
                    $q->where("branch", auth()->user()->branch_id);
                });

                $q->when($request->terminal != "", function ($q) use ($request) {
                    $q->where("terminal_id", $request->terminal);
                });
                $q->when($request->customerNo != "", function ($q) use ($request) {
                    $q->where("customer_id", $request->customerNo);
                });
                $q->when($request->paymentmode != "", function ($q) use ($request) {
                    $q->where("payment_id", $request->paymentmode);
                });
                $q->when($request->ordermode != "", function ($q) use ($request) {
                    $q->where("order_mode_id", $request->ordermode);
                });
            })
            ->when($request->department != "", function ($q) use ($request) {
                $q->whereIn("item_code", InventoryModel::where("company_id", session("company_id"))->where("department_id", $request->department)->pluck("id"));
            })
            ->when($request->product != "", function ($q) use ($request) {
                $q->where("item_code", $request->product);
            })
            ->select()
            ->addSelect([
                'total' => \App\Models\Inventory::query()
                    ->whereColumn('id', 'sales_receipt_details.item_code')
                    ->selectRaw(DB::raw('sum(total_qty * weight_qty) as total'))
            ])

            // ->select(DB::raw('SUM(total_qty) as total_qty'),DB::raw('SUM(item_price*total_qty) as total_amount'))
            // ->groupBy("item_code","item_price")
            ->orderBy("item_code", "asc")
            ->get();
        // ->toSql();
    }

    public function getTerminals(Request $request)
    {
        if (is_array($request->branch) && $request->branch != "") {
            return response()->json(["terminal" => Terminal::whereIn("branch_id", $request->branch)->where("status_id", 1)->select("terminal_id", "terminal_name")->get()]);
        } elseif ($request->branch != "") {
            return response()->json(["terminal" => Terminal::where("branch_id", $request->branch)->where("status_id", 1)->select("terminal_id", "terminal_name")->get()]);
        } else {
            return 0;
        }
    }

    public function pdf_attendance(report $report, Request $request)
    {

        $branch = $report->getbranchbyid($request->branchid);
        if ($request->empid != '' && $request->empid != 'All') {
            $employee = $report->getemployeebyid($request->empid);
        } else {
            $request->empid = '';
        }

        $company = $report->getcompany();

        $pdf = app('Fpdf');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Image(asset('storage/images/company/' . $company[0]->logo), 10, 10, -800);
        // $pdf->SetFont('Arial','BU',18);
        // $pdf->MultiCell(0,10,$company[0]->name,0,'C');
        // $pdf->Cell(2,2,'',0,1);
        // $pdf->SetFont('Arial','B',12);
        // $pdf->Cell(0,3,'Attendance Sheet',0,1,'C'); //Here is center title
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(10, 10, '', 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 35, $company[0]->name, 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, -25, $company[0]->address, 0, 1, 'L');
        $pdf->Cell(0, 35, 'Karachi, Karachi City, Sindh', 0, 1, 'L');
        $pdf->Cell(0, -25, $company[0]->ptcl_contact, 0, 1, 'L');
        $pdf->Cell(0, 10, '', 0, 1, 'R');
        $pdf->Cell(190, 5, '', '', 1); //SPACE
        // $pdf->ln();

        $pdf->Cell(190, 1, '', 'T', 1); //SPACE

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'ATTENDANCE SHEET', 0, 1, 'C'); //Here is center title
        $pdf->Cell(190, 2, '', 'T', 1); //SPACE


        $pdf->SetFont('Arial', 'B', 10);
        $pdf->setFillColor(230, 230, 230);
        $pdf->Cell(190, 7, 'Apply Filters', 0, 1, 'L', 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(25, 7, 'From Date:', 0, 0, 'L');
        $pdf->Cell(50, 7, $request->fromdate, 0, 0, 'L');
        $pdf->Cell(35, 7, 'Branch Name:', 0, 0, 'L');
        $pdf->Cell(20, 7, $branch[0]->branch_name, 0, 1, 'L');

        $pdf->Cell(25, 2, 'To Date:', 0, 0, 'L');
        $pdf->Cell(50, 2, $request->todate, 0, 0, 'L');
        $pdf->Cell(35, 2, 'Employee Name:', 0, 0, 'L');
        if ($request->empid != '' && $request->empid != 'All') {
            $pdf->Cell(20, 2, $employee[0]->emp_name, 0, 1, 'L');
        } else {
            $pdf->Cell(20, 2, 'ALL', 0, 1, 'L');
        }


        if ($request->approchid == 1) {

            $pdf->Cell(190, 3, '', '', 1); //SPACE

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(46, 8, "Employee Name", 0, 0, 'C', 1);
            $pdf->Cell(24, 8, "Date", 0, 0, 'C', 1);
            $pdf->Cell(24, 8, "Clock In", 0, 0, 'C', 1);
            $pdf->Cell(24, 8, "Clock Out", '0', 0, 'C', 1);
            $pdf->Cell(18, 8, "Late", '0', 0, 'C', 1);
            $pdf->Cell(18, 8, "Early", '0', 0, 'C', 1);
            $pdf->Cell(18, 8, "OT", '0', 0, 'C', 1);
            $pdf->Cell(18, 8, "ATT.Hrs", '0', 1, 'C', 1);
            $pdf->Cell(190, 1, '', '', 1); //SPACE


            $pdf->SetFont('Arial', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $details = $report->attendance_sheet_report($request->branchid, $request->fromdate, $request->todate, $request->empid, $request->approchid);

            foreach ($details as $value) {

                $pdf->Cell(46, 7, $value->emp_name, 0, 0, 'L');
                $pdf->Cell(24, 7, $value->date, 0, 0, 'L');
                $pdf->Cell(24, 7, $value->clock_in, 0, 0, 'C');
                $pdf->Cell(24, 7, $value->clockout, 0, 0, 'C');
                $pdf->Cell(18, 7, $value->lates, 0, 0, 'C');
                $pdf->Cell(18, 7, $value->earlys, 0, 0, 'C');
                $pdf->Cell(18, 7, $value->ot, 0, 0, 'C');
                $pdf->Cell(18, 7, $value->Atttime, 0, 1, 'C');
            }

            $pdf->Cell(190, 5, '', '', 1); //SPACE
        } else {
            $pdf->Cell(190, 3, '', '', 1); //SPACE

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(46, 8, "Employee Name", 0, 0, 'C', 1);
            $pdf->Cell(30, 8, "Present", 0, 0, 'C', 1);
            $pdf->Cell(30, 8, "Absent", 0, 0, 'C', 1);
            $pdf->Cell(28, 8, "Late", '0', 0, 'C', 1);
            $pdf->Cell(28, 8, "Early", '0', 0, 'C', 1);
            $pdf->Cell(28, 8, "Over Time", '0', 1, 'C', 1);
            $pdf->Cell(190, 1, '', '', 1); //SPACE


            $pdf->SetFont('Arial', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $details = $report->attendance_sheet_report($request->branchid, $request->fromdate, $request->todate, $request->empid, $request->approchid);

            foreach ($details as $value) {

                $pdf->Cell(46, 7, $value->emp_name, 0, 0, 'L');
                $pdf->Cell(30, 7, $value->present, 0, 0, 'C');
                $pdf->Cell(30, 7, $value->absent, 0, 0, 'C');
                $pdf->Cell(28, 7, $value->late, 0, 0, 'C');
                $pdf->Cell(28, 7, $value->early, 0, 0, 'C');
                $pdf->Cell(28, 7, $value->ot, 0, 1, 'C');
            }

            $pdf->Cell(190, 5, '', '', 1); //SPACE
        }


        // $pdf->SetFont('Arial','B',11);
        // $pdf->setFillColor(230,230,230);
        // $pdf->Cell(60,7,'Total Present',0,0,'L',1);
        // $pdf->Cell(1,7,'5',0,1,'R',1);

        // $pdf->Cell(190,2,'','',1);//SPACE

        // $pdf->Cell(60,7,'Total Absent',0,0,'L',1);
        // $pdf->Cell(1,7,'5',0,1,'R',1);

        // $pdf->Cell(190,2,'','',1);//SPACE

        // $pdf->Cell(60,7,'Total Late',0,0,'L',1);
        // $pdf->Cell(1,7,'5',0,1,'R',1);

        $pdf->Cell(190, 10, '', '', 1); //SPACE

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(185, 4, 'This is computer generated report no signature required', 0, 1, 'L');



        $pdf->Output('test.pdf', 'I');
    }


    public function pdf_loandetails(report $report, Request $request)
    {

        $company = $report->getcompany();
        $loan = $report->loans($request->fromdate, $request->todate, $request->empid);
        if (count($loan) != 0) {
            $pdf = app('Fpdf');
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);

            $pdf->Image(asset('storage/images/company/' . $company[0]->logo), 10, 10, -800);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(10, 10, '', 0, 1);

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 35, $company[0]->name, 0, 1, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, -25, $company[0]->address, 0, 1, 'L');
            $pdf->Cell(0, 35, 'Karachi, Karachi City, Sindh', 0, 1, 'L');
            $pdf->Cell(0, -25, $company[0]->ptcl_contact, 0, 1, 'L');
            $pdf->Cell(0, 10, '', 0, 1, 'R');
            $pdf->Cell(190, 5, '', '', 1); //SPACE


            $pdf->Cell(190, 1, '', 'T', 1); //SPACE

            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 8, 'LOAN DETAILS', 0, 1, 'C'); //Here is center title
            $pdf->Cell(190, 2, '', 'T', 1); //SPACE


            $pdf->SetFont('Arial', 'B', 10);
            $pdf->setFillColor(230, 230, 230);
            $pdf->Cell(190, 7, 'Apply Filters', 0, 1, 'L', 1);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(40, 7, 'From Date:', 0, 0, 'L');
            $pdf->Cell(50, 7, $request->fromdate, 0, 0, 'L');
            $pdf->Cell(35, 7, 'To Date:', 0, 0, 'L');
            $pdf->Cell(20, 7, $request->todate, 0, 1, 'L');

            $pdf->Cell(40, 2, 'Employee Name:', 0, 0, 'L');
            $pdf->Cell(50, 2, $loan[0]->emp_name, 0, 1, 'L');

            $pdf->Cell(190, 3, '', '', 1); //SPACE


            foreach ($loan as $field) {
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(80, 8, "Loan Amount: " . $field->loan_amount, 0, 0, 'L', 1);
                $pdf->Cell(75, 8, "Loan Date: " . $field->date, 0, 0, 'L', 1);
                $pdf->Cell(35, 8, "Status: " . $field->status_name, 0, 1, 'L', 1);
                $pdf->Cell(190, 1, '', '', 1); //SPACE

                $pdf->SetFont('Arial', '', 10);
                $pdf->SetTextColor(0, 0, 0);


                $balance = $field->loan_amount;
                $details = $report->loan_installment($request->fromdate, $request->todate, $request->empid, $field->loan_id);

                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(65, 7, "Installment Date", 0, 0, 'C');
                $pdf->Cell(65, 7, "Installment Amount", 0, 0, 'C');
                $pdf->Cell(60, 7, "Balance", 0, 1, 'C');
                $pdf->Cell(190, 1, '', '', 1); //SPACE
                foreach ($details as $value) {
                    $balance = $balance - $value->installment_amount;

                    $pdf->SetFont('Arial', '', 11);
                    $pdf->Cell(65, 7, $value->date, 0, 0, 'C');
                    $pdf->Cell(65, 7, $value->installment_amount, 0, 0, 'C');
                    $pdf->Cell(60, 7, $balance, 0, 1, 'C');
                }
            }
            $pdf->Cell(190, 5, '', '', 1); //SPACE

            $pdf->Output('loanDetails_' . $loan[0]->emp_name . ".pdf", 'I');
        } else {
            return 0;
        }
    }



    public function consolidated_salary_sheet(salary $salary, request $request)
    {
        $company = $salary->getcompany();

        $payslip = $salary->payslip_report($request->empid, $request->fromdate, $request->todate);

        $pdf = app('Fpdf');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Image(asset('storage/images/company/' . $company[0]->logo), 10, 10, -150);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(10, 10, '', 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 35, $company[0]->name, 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, -25, $company[0]->address, 0, 1, 'L');
        $pdf->Cell(0, 35, 'Karachi, Karachi City, Sindh', 0, 1, 'L');
        $pdf->Cell(0, -25, $company[0]->ptcl_contact, 0, 1, 'L');
        $pdf->Cell(0, 10, '', 0, 1, 'R');
        $pdf->Cell(190, 5, '', '', 1); //SPACE
        // $pdf->ln();

        $pdf->Cell(190, 1, '', 'T', 1); //SPACE
        // $pdf->ln();

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'Consolidated Salary Sheet', 0, 1, 'C'); //Here is center title
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 5, 'From ' . $request->fromdate . " To " . $request->todate, 0, 1, 'C'); //Here is center title
        $pdf->Cell(190, 2, '', 'T', 1); //SPACE


        //        $pdf->SetFont('Arial','B',11);
        //        $pdf->Cell(25,10,"From Date:",'',0,'L');
        //        $pdf->Cell(44,10,$request->fromdate,'',0,'L');
        //        $pdf->Cell(30,10,"To Date:",'',0,'L');
        //        $pdf->Cell(20,10,$request->todate,'',1,'L');
        //        $pdf->Cell(190,1,'','',1);//SPACE
        //        $pdf->Cell(190,1,'','T',1);//SPACE


        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(70, 10, "ACC | Employee Name", '', 0, 'L');
        $pdf->Cell(70, 10, "Father Name", '', 0, 'L');
        $pdf->Cell(46, 10, "Mobile Number", '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE


        $pdf->SetFont('Arial', 'B', 10);

        $emp = $salary->emp_details($request->empid, $request->fromdate, $request->todate);


        $pdf->Cell(70, 1, $emp[0]->emp_acc . " | " . $emp[0]->emp_name, '', 0, 'L');
        $pdf->Cell(70, 1, $emp[0]->emp_fname, '', 0, 'L');
        $pdf->Cell(46, 1, $emp[0]->emp_contact, '', 1, 'L');

        $pdf->Cell(190, 1, '', '', 1); //SPACE


        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(70, 10, "Branch", '', 0, 'L');
        $pdf->Cell(70, 10, "Department", '', 0, 'L');
        $pdf->Cell(46, 10, "Designation", '', 1, 'L');

        $pdf->Cell(190, 1, '', '', 1); //SPACE

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(70, 1, $emp[0]->branch_name, '', 0, 'L');
        $pdf->Cell(70, 1, $emp[0]->department_name, '', 0, 'L');
        $pdf->Cell(46, 1, $emp[0]->designation_name, '', 1, 'L');

        $pdf->Cell(190, 1, '', '', 1); //SPACE

        $pdf->Cell(190, 4, '', 'B', 1); //SPACE

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(32, 10, "Presnet Days", '', 0, 'L');
        $pdf->Cell(32, 10, "Absent Days", '', 0, 'L');
        $pdf->Cell(32, 10, "Late Count", '', 0, 'L');
        $pdf->Cell(32, 10, "Early Count", '', 0, 'L');
        $pdf->Cell(32, 10, "OT Duration", '', 0, 'L');
        $pdf->Cell(30, 10, "Basic Salary", '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(32, 1, $emp[0]->present, '', 0, 'L');
        $pdf->Cell(32, 1, $emp[0]->absent, '', 0, 'L');
        $pdf->Cell(32, 1, $emp[0]->late . " mints", '', 0, 'L');
        $pdf->Cell(32, 1, $emp[0]->early . " mints", '', 0, 'L');
        $pdf->Cell(32, 1, $emp[0]->ot . " mints", '', 0, 'L');
        $pdf->Cell(30, 1, $emp[0]->basic_salary, '', 1, 'L');
        $pdf->Cell(190, 6, '', '', 1); //SPACE

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(21, 8, "Date", 0, 0, 'C', 1);
        $pdf->Cell(21, 8, "B. Salary", 0, 0, 'C', 1);
        $pdf->Cell(21, 8, "OverTime", 0, 0, 'C', 1);
        $pdf->Cell(21, 8, "Advance", 0, 0, 'C', 1);
        $pdf->Cell(21, 8, "Loan", 0, 0, 'C', 1);
        $pdf->Cell(21, 8, "Absent", 0, 0, 'C', 1);
        $pdf->Cell(21, 8, "Allowance", 0, 0, 'C', 1);
        $pdf->Cell(21, 8, "Special A", 0, 0, 'C', 1);
        $pdf->Cell(22, 8, "Net Salary", 0, 1, 'C', 1);
        $pdf->Cell(190, 1, '', '', 1); //SPACE

        foreach ($payslip as $value) {
            $pdf->SetFont('Arial', '', 11);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(21, 5, $value->payslip_date, 0, 0, 'L', 1);
            $pdf->Cell(21, 5, $value->basic_salary, 0, 0, 'C', 1);
            $pdf->Cell(21, 5, $value->ot_amount, 0, 0, 'C', 1);
            $pdf->Cell(21, 5, $value->advance_amount, 0, 0, 'C', 1);
            $pdf->Cell(21, 5, $value->loan_amount, 0, 0, 'C', 1);
            $pdf->Cell(21, 5, $value->absent_amount, 0, 0, 'C', 1);
            $pdf->Cell(21, 5, $value->allowance_amount, 0, 0, 'C', 1);
            $pdf->Cell(21, 5, $value->special_amount, 0, 0, 'C', 1);
            $pdf->Cell(22, 5, $value->net_salary, 0, 1, 'C', 1);
            $pdf->Cell(190, 1, '', '', 1); //SPACE
        }

        $pdf->Output('Consolidated_Salary_Sheet_' . $emp[0]->emp_name . '.pdf', 'I');
    }



    public function pdf_advancedetails(report $report, Request $request)
    {

        $company = $report->getcompany();
        $advance = $report->advance($request->fromdate, $request->todate, $request->empid);
        if (count($advance) != 0) {
            $pdf = app('Fpdf');
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);

            $pdf->Image(asset('storage/images/company/' . $company[0]->logo), 10, 10, -800);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(10, 10, '', 0, 1);

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 35, $company[0]->name, 0, 1, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, -25, $company[0]->address, 0, 1, 'L');
            $pdf->Cell(0, 35, 'Karachi, Karachi City, Sindh', 0, 1, 'L');
            $pdf->Cell(0, -25, $company[0]->ptcl_contact, 0, 1, 'L');
            $pdf->Cell(0, 10, '', 0, 1, 'R');
            $pdf->Cell(190, 5, '', '', 1); //SPACE


            $pdf->Cell(190, 1, '', 'T', 1); //SPACE

            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 8, 'Advance DETAILS', 0, 1, 'C'); //Here is center title
            $pdf->Cell(190, 2, '', 'T', 1); //SPACE


            $pdf->SetFont('Arial', 'B', 10);
            $pdf->setFillColor(230, 230, 230);
            $pdf->Cell(190, 7, 'Apply Filters', 0, 1, 'L', 1);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(40, 7, 'From Date:', 0, 0, 'L');
            $pdf->Cell(50, 7, $request->fromdate, 0, 0, 'L');
            $pdf->Cell(35, 7, 'To Date:', 0, 0, 'L');
            $pdf->Cell(20, 7, $request->todate, 0, 1, 'L');

            $pdf->Cell(40, 2, 'Employee Name:', 0, 0, 'L');
            $pdf->Cell(50, 2, $advance[0]->emp_name, 0, 1, 'L');

            $pdf->Cell(190, 3, '', '', 1); //SPACE

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, 8, "Date", 0, 0, 'C', 1);
            $pdf->Cell(40, 8, "Amount", 0, 0, 'C', 1);
            $pdf->Cell(70, 8, "Reason", 0, 0, 'C', 1);
            $pdf->Cell(40, 8, "Status", 0, 1, 'C', 1);
            $pdf->Cell(190, 1, '', '', 1); //SPACE

            foreach ($advance as $value) {
                $pdf->SetFont('Arial', '', 11);
                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(40, 5, $value->date, 0, 0, 'C', 1);
                $pdf->Cell(40, 5, $value->amount, 0, 0, 'C', 1);
                $pdf->Cell(70, 5, $value->reason, 0, 0, 'L', 1);
                $pdf->Cell(40, 5, $value->status_name, 0, 1, 'C', 1);
                $pdf->Cell(190, 1, '', '', 1); //SPACE
            }

            $pdf->Cell(190, 5, '', '', 1); //SPACE

            $pdf->Output('AdvanceDetails_' . $advance[0]->emp_name . ".pdf", 'I');
        } else {
            return 0;
        }
    }

    //Profit & Loss Standard Report
    public function profitLossStandardReport(Request $request, Vendor $vendor, Report $report)
    {
        $branchname = "";

        if ($request->branch != "all") {
            $branchname = Branch::where("branch_id", $request->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " (All Branches) ";
        }

        $company = $vendor->company(session('company_id'));
        $totalBalance = 0;
        $totalRevenue = 0;
        $totalCogs = 0;
        $gross = 0;
        $net = 0;

        $sales = $report->total_sales($request->fromdate, $request->todate, $request->branch);
        $receivables = $report->Customer_receivable($request->fromdate, $request->todate);
        $expense = $report->expenses($request->fromdate, $request->todate, $request->branch);
        $purchases = $report->pruchase_amount($request->fromdate, $request->todate, $request->branch);
        $salaries = $report->total_salaries($request->fromdate, $request->todate, $request->branch);
        $discounts = $report->total_discounts($request->fromdate, $request->todate, $request->branch);
        $salesreturn = $report->total_sales_return($request->fromdate, $request->todate, $request->branch);
        $cogs = $report->total_COGS($request->fromdate, $request->todate, $request->branch);


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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Profit & Loss ' . $branchname, 'B,T', 1, 'L');
        $pdf->ln(1);


        //Income START HERE


        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(230, 230, 230);
        $pdf->Cell(190, 8, 'INCOME', 0, 1, 'L', 1);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(70, 6, 'Sales ', 0, 0, 'L');
        $pdf->Cell(40, 6, '', 0, 0, 'L');
        $pdf->Cell(40, 6, '', 0, 0, 'L');
        $pdf->Cell(40, 6, number_format($sales[0]->sales + $discounts[0]->discounts, 2), 0, 1, 'R');

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(70, 6, 'Receivable ', 0, 0, 'L');
        $pdf->Cell(40, 6, '', 0, 0, 'L');
        $pdf->Cell(40, 6, '', 0, 0, 'L');
        $pdf->Cell(40, 6, number_format($receivables[0]->balance, 2), 0, 1, 'R');

        $totalRevenue = $sales[0]->sales + $discounts[0]->discounts;

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(95, 6, 'Total Income', 0, 0, 'L');
        $pdf->Cell(95, 6, number_format($totalRevenue, 2), 0, 1, 'R');

        //INCOME END HERE

        $pdf->Cell(190, 2, '', '', 1); //SPACE

        //COGS START HERE

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(230, 230, 230);
        $pdf->Cell(190, 8, 'COST OF GOOD SALES', 0, 1, 'L', 1);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(70, 8, 'Cost of Good Sold - All Products', 0, 0, 'L');
        $pdf->Cell(40, 8, '', 0, 0, 'L');
        $pdf->Cell(40, 8, '', 0, 0, 'L');
        $pdf->Cell(40, 8, number_format($cogs[0]->cost, 2), 0, 1, 'R');

        $totalCogs = $cogs[0]->cost;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(95, 5, 'Total COGS', 0, 0, 'L');
        $pdf->Cell(95, 5, number_format($totalCogs, 2), 0, 1, 'R');

        //COGS END HERE

        $pdf->Cell(190, 2, '', '', 1); //SPACE

        //GROSS
        $gross = $totalRevenue - $totalCogs;
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(95, 8, "GROSS PROFIT", 0, 0, 'L', 1); //your cell
        $pdf->Cell(95, 8, "Rs. " . number_format($gross, 2), 0, 1, 'R', 1); //your cell

        $pdf->Cell(190, 2, '', '', 1); //SPACE

        //PURCHASES START HERE

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(230, 230, 230);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 8, 'PURCHASES', 0, 1, 'L', 1);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(70, 6, 'Purchases', 0, 0, 'L');
        $pdf->Cell(40, 6, '', 0, 0, 'L');
        $pdf->Cell(40, 6, '', 0, 0, 'L');
        $pdf->Cell(40, 6, number_format($purchases[0]->purchase_amount, 2), 0, 1, 'R');

        //EXPENSE START HERE

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(230, 230, 230);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 8, 'EXPENSES', 0, 1, 'L', 1);

        $expesnsetotal = 0;

        //for loop for expense head
        foreach ($expense as $value) {
            $expesnsetotal = $expesnsetotal + $value->expenseamt;
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(5, 6, '', 0, 0, 'L');
            $pdf->Cell(105, 6, $value->expense_category, 0, 0, 'L');
            $pdf->Cell(40, 6, '', 0, 0, 'L');
            $pdf->Cell(40, 6, number_format($value->expenseamt, 2), 0, 1, 'R');
        }

        // $expesnsetotal = ($expesnsetotal  ); //$purchases[0]->purchase_amount
        $pdf->Cell(190, 2, '', '', 1); //SPACE
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(95, 5, 'Total Expenses', 0, 0, 'L');
        $pdf->Cell(95, 5, number_format($expesnsetotal, 2), 0, 1, 'R');

        $pdf->Cell(190, 2, '', '', 1); // EXtra Space
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(230, 230, 230);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(95, 8, "Discounts", 0, 0, 'L', 1); //your cell
        $pdf->Cell(95, 8, "Rs. " . number_format($discounts[0]->discounts, 2), 0, 1, 'R', 1); 
       
        $pdf->Cell(190, 2, '', '', 1); // EXtra Space
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(230, 230, 230);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(95, 8, "Sales Return", 0, 0, 'L', 1); //your cell
        $pdf->Cell(95, 8, "Rs. " . number_format($salesreturn[0]->salesreturn, 2), 0, 1, 'R', 1); 
       
        $pdf->Cell(190, 2, '', '', 1); // EXtra Space
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(230, 230, 230);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(95, 8, "Salaries", 0, 0, 'L', 1); //your cell
        $pdf->Cell(95, 8, "Rs. " . number_format($salaries[0]->salaries, 2), 0, 1, 'R', 1); 

       

        // $pdf->Cell(70, 6, 'Discounts', 0, 0, 'L');
        // $pdf->Cell(40, 6, '', 0, 0, 'L');
        // $pdf->Cell(40, 6, '', 0, 0, 'L');
        // $pdf->Cell(40, 6, number_format($discounts[0]->discounts, 2), 0, 1, 'R');

        // $pdf->Cell(70, 6, 'Sales Return', 0, 0, 'L');
        // $pdf->Cell(40, 6, '', 0, 0, 'L');
        // $pdf->Cell(40, 6, '', 0, 0, 'L');
        // $pdf->Cell(40, 6, number_format($salesreturn[0]->salesreturn, 2), 0, 1, 'R');



        // $pdf->Cell(70, 6, 'Salaries', 0, 0, 'L');
        // $pdf->Cell(40, 6, '', 0, 0, 'L');
        // $pdf->Cell(40, 6, '', 0, 0, 'L');
        // $pdf->Cell(40, 6, number_format($salaries[0]->salaries, 2), 0, 1, 'R');

        $expesnsetotal = ($expesnsetotal  +  $discounts[0]->discounts + $salesreturn[0]->salesreturn + $salaries[0]->salaries); //$purchases[0]->purchase_amount

        // $pdf->SetFont('Arial', 'B', 12);
        // $pdf->Cell(95, 5, 'Total Expenses', 0, 0, 'L');
        // $pdf->Cell(95, 5, number_format($expesnsetotal, 2), 0, 1, 'R');

        //Expense END HERE
        $pdf->Cell(190, 2, '', '', 1); //SPACE

        //GROSS
        $net =  $gross - $expesnsetotal;
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(95, 8, "NET PROFIT", 0, 0, 'L', 1); //your cell
        $pdf->Cell(95, 8, "Rs. " . number_format($net, 2), 0, 1, 'R', 1); //your cell

        //save file
        $pdf->Output('Profit & Loss Standard.pdf', 'I');
    }

    //Profit & Loss Details Report
    public function profitLossDetailsReport(Request $request, Vendor $vendor, Report $report)
    {
        $branchname = "";

        if ($request->branch != "all") {
            $branchname = Branch::where("branch_id", $request->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " (All Branches) ";
        }

        $company = $vendor->company(session('company_id'));
        $totalBalance = 0;
        $totalRevenue = 0;
        $totalCogs = 0;
        $gross = 0;
        $net = 0;

        $salesreceipts = $report->sales_recipts($request->fromdate, $request->todate, $request->branch);

        $receiveables = $report->Customer_receivable_details($request->fromdate, $request->todate);

        $expensedetails = $report->expenses_details($request->fromdate, $request->todate, $request->branch);

        $purchases = $report->pruchase_orders($request->fromdate, $request->todate, $request->branch);

        $discounts = $report->discounts($request->fromdate, $request->todate, $request->branch);
        $salereturn = $report->sales_return($request->fromdate, $request->todate, $request->branch);

        $salaries = $report->salaries($request->fromdate, $request->todate, $request->branch);

        $cogs = $report->COGS($request->fromdate, $request->todate);



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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Profit & Loss Details ' . $branchname, 'B,T', 1, 'L');
        $pdf->ln(1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(25, 7, 'Type', 'B', 0, 'C', 1);
        $pdf->Cell(25, 7, 'Date', 'B', 0, 'C', 1);
        $pdf->Cell(25, 7, 'Number', 'B', 0, 'L', 1);
        $pdf->Cell(50, 7, 'Name', 'B', 0, 'L', 1);
        $pdf->Cell(40, 7, 'Category', 'B', 0, 'L', 1);
        $pdf->Cell(25, 7, 'Total', 'B', 1, 'C', 1);



        //Income START HERE


        //        $pdf->SetFont('Arial','B',12);
        //        $pdf->SetTextColor(0,0,0);
        //        $pdf->Cell(190,6,'Income',0,1,'L');

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(5, 6, '', 0, 0, 'L');
        $pdf->Cell(185, 6, 'Sales', 0, 1, 'L');


        $totalsales = 0;
        $pdf->SetFont('Arial', '', 10);
        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);

        foreach ($salesreceipts as $value) {

            $totalsales = $totalsales + $value->total_amount;

            $pdf->Cell(25, 5, 'Sales Receipt', 0, 0, 'L', 1);
            $pdf->Cell(25, 5, $value->date, 0, 0, 'L', 1);
            $pdf->Cell(25, 5, $value->id, 0, 0, 'C', 1);
            $pdf->Cell(50, 5, $value->name, 0, 0, 'L', 1);
            $pdf->Cell(40, 5, $value->payment_mode, 0, 0, 'C', 1);
            $pdf->Cell(25, 5, number_format($value->total_amount, 2), 0, 1, 'R', 1);
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 7, '', 0, 0, 'L');
        $pdf->Cell(160, 7, 'Total Sales ', 0, 0, 'L');
        $pdf->Cell(20, 7, number_format($totalsales, 2), 'T,B', 1, 'R');

        $pdf->ln(3);


        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(5, 6, '', 0, 0, 'L');
        $pdf->Cell(185, 6, 'Receivables', 0, 1, 'L');


        //receiveables
        $totalreceiveable = 0;
        foreach ($receiveables as $value) {
            if ($value->balance > 0) {
                $totalreceiveable = $totalreceiveable + $value->balance;
                $pdf->SetFont('Arial', '', 10);
                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(25, 5, 'Credited', 0, 0, 'L', 1);
                $pdf->Cell(25, 5, '', 0, 0, 'L', 1);
                $pdf->Cell(25, 5, '', 0, 0, 'R', 1);
                $pdf->Cell(50, 5, $value->name, 0, 0, 'L', 1);
                $pdf->Cell(40, 5, '', 0, 0, 'C', 1);
                $pdf->Cell(25, 5, number_format($value->balance, 2), 0, 1, 'R', 1);
            }
        }


        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 7, '', 0, 0, 'L');
        $pdf->Cell(160, 7, 'Total Receivable ', 0, 0, 'L');
        $pdf->Cell(20, 7, number_format($totalreceiveable, 2), 'T,B', 1, 'R');


        $totalRevenue = $totalsales;

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(95, 6, 'Total Income', 'T,B', 0, 'L');
        $pdf->Cell(95, 6, number_format($totalRevenue, 2), 'T,B', 1, 'R');

        //INCOME END HERE

        $pdf->Cell(190, 2, '', '', 1); //SPACE

        //COGS START HERE

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(5, 6, '', 0, 0, 'L');
        $pdf->Cell(185, 6, 'Cost of Good Sales', 0, 1, 'L');

        $totalcogs = 0;
        foreach ($cogs as $value) {

            $totalcogs = $totalcogs + (int)$value->total_cost;
            $pdf->SetFont('Arial', '', 10);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(25, 5, 'Sales Receipt', 0, 0, 'L', 1);
            $pdf->Cell(25, 5, $value->date, 0, 0, 'L', 1);
            $pdf->Cell(25, 5, $value->id, 0, 0, 'C', 1);
            $pdf->Cell(50, 5, $value->name, 0, 0, 'L', 1);
            $pdf->Cell(40, 5, $value->payment_mode, 0, 0, 'C', 1);
            $pdf->Cell(25, 5, number_format((int)$value->total_cost, 2), 0, 1, 'R', 1);
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 7, '', 0, 0, 'L');
        $pdf->Cell(160, 7, 'Total COGS ', 0, 0, 'L');
        $pdf->Cell(20, 7, number_format($totalcogs, 2), 'T,B', 1, 'R');


        //COGS END HERE

        $pdf->Cell(190, 2, '', '', 1); //SPACE

        //GROSS
        $gross = $totalRevenue - $totalcogs;
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(95, 6, 'Gross Profit', 'T,B', 0, 'L');
        $pdf->Cell(95, 6, number_format($gross, 2), 'T,B', 1, 'R');

        $pdf->Cell(190, 2, '', '', 1); //SPACE

        //EXPENSE START HERE

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(5, 6, '', 0, 0, 'L');
        $pdf->Cell(185, 6, 'Expenses', 0, 1, 'L');

        //for loop for expense head
        $expensetotal = 0;
        foreach ($expensedetails as $value) {
            $expensetotal = $expensetotal + $value->net_amount;
            $pdf->SetFont('Arial', '', 10);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(25, 5, 'Voucher', 0, 0, 'L', 1);
            $pdf->Cell(25, 5, $value->date, 0, 0, 'L', 1);
            $pdf->Cell(25, 5, $value->exp_id, 0, 0, 'R', 1);
            $pdf->Cell(50, 5, $value->expense_details, 0, 0, 'L', 1);
            $pdf->Cell(40, 5, $value->expense_category, 0, 0, 'C', 1);
            $pdf->Cell(25, 5, number_format($value->net_amount, 2), 0, 1, 'R', 1);
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 7, '', 0, 0, 'L');
        $pdf->Cell(160, 7, 'Total Expenses ', 0, 0, 'L');
        $pdf->Cell(20, 7, number_format($expensetotal, 2), 'T,B', 1, 'R');

        $pdf->ln(3);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(5, 6, '', 0, 0, 'L');
        $pdf->Cell(185, 6, 'Purchases', 0, 1, 'L');


        $purchasestotal = 0;
        foreach ($purchases as $value) {
            $purchasestotal = $purchasestotal + $value->net_amount;
            $pdf->SetFont('Arial', '', 10);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(25, 5, 'PO', 0, 0, 'L', 1);
            $pdf->Cell(25, 5, $value->order_date, 0, 0, 'L', 1);
            $pdf->Cell(25, 5, $value->po_no, 0, 0, 'R', 1);
            $pdf->Cell(50, 5, $value->vendor_name, 0, 0, 'L', 1);
            $pdf->Cell(40, 5, $value->name, 0, 0, 'C', 1);
            $pdf->Cell(25, 5, number_format($value->net_amount, 2), 0, 1, 'R', 1);
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 7, '', 0, 0, 'L');
        $pdf->Cell(160, 7, 'Total Purchases ', 0, 0, 'L');
        $pdf->Cell(20, 7, number_format($purchasestotal, 2), 'T,B', 1, 'R');

        $pdf->ln(3);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(5, 6, '', 0, 0, 'L');
        $pdf->Cell(185, 6, 'Discounts', 0, 1, 'L');

        $totaldiscounts = 0;
        foreach ($discounts as $value) {
            if ($value->discount_amount > 0) {
                $totaldiscounts = $totaldiscounts + $value->discount_amount;
                $pdf->SetFont('Arial', '', 10);
                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(25, 5, 'Sales Receipt', 0, 0, 'L', 1);
                $pdf->Cell(25, 5, $value->date, 0, 0, 'L', 1);
                $pdf->Cell(25, 5, $value->receipt_id, 0, 0, 'C', 1);
                $pdf->Cell(50, 5, $value->name, 0, 0, 'L', 1);
                $pdf->Cell(40, 5, $value->payment_mode, 0, 0, 'C', 1);
                $pdf->Cell(25, 5, number_format($value->discount_amount, 2), 0, 1, 'R', 1);
            }
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 7, '', 0, 0, 'L');
        $pdf->Cell(160, 7, 'Total Discounts ', 0, 0, 'L');
        $pdf->Cell(20, 7, number_format($totaldiscounts, 2), 'T,B', 1, 'R');

        $pdf->ln(3);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(5, 6, '', 0, 0, 'L');
        $pdf->Cell(185, 6, 'Sales Return', 0, 1, 'L');

        $totalsalereturn = 0;
        foreach ($salereturn as $value) {
            // if ($value->discount_amount > 0)
            // {
            $totalsalereturn = $totalsalereturn + $value->amount;
            $pdf->SetFont('Arial', '', 10);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(25, 5, 'Sales Return', 0, 0, 'L', 1);
            $pdf->Cell(40, 5, date("d-M-Y h:i A", strtotime($value->timestamp)), 0, 0, 'L', 1);
            $pdf->Cell(25, 5, $value->receipt_id, 0, 0, 'C', 1);
            $pdf->Cell(35, 5, $value->name, 0, 0, 'L', 1);
            $pdf->Cell(40, 5, $value->payment_mode, 0, 0, 'C', 1);
            $pdf->Cell(25, 5, number_format($value->amount, 2), 0, 1, 'R', 1);
            // }
        }
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 7, '', 0, 0, 'L');
        $pdf->Cell(160, 7, 'Total Sales Return ', 0, 0, 'L');
        $pdf->Cell(20, 7, number_format($totalsalereturn, 2), 'T,B', 1, 'R');

        $pdf->ln(3);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(5, 6, '', 0, 0, 'L');
        $pdf->Cell(185, 6, 'Salaries', 0, 1, 'L');

        $totalsalries = 0;
        foreach ($salaries as $value) {

            $totalsalries = $totalsalries + $value->net_salary;
            $pdf->SetFont('Arial', '', 10);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(25, 5, 'Payslip', 0, 0, 'L', 1);
            $pdf->Cell(25, 5, $value->date, 0, 0, 'L', 1);
            $pdf->Cell(25, 5, '', 0, 0, 'R', 1);
            $pdf->Cell(50, 5, $value->emp_name, 0, 0, 'L', 1);
            $pdf->Cell(40, 5, '', 0, 0, 'C', 1);
            $pdf->Cell(25, 5, number_format($value->net_salary, 2), 0, 1, 'R', 1);
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 7, '', 0, 0, 'L');
        $pdf->Cell(160, 7, 'Total Salaries', 0, 0, 'L');
        $pdf->Cell(20, 7, number_format($totalsalries, 2), 'T,B', 1, 'R');

        $pdf->Cell(190, 2, '', '', 1); //SPACE

        $totalexpenses = $expensetotal  + $totaldiscounts + $totalsalereturn + $totalsalries; //+ $purchasestotal
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(95, 6, 'Total Expenses', 'T,B', 0, 'L');
        $pdf->Cell(95, 6, number_format($totalexpenses, 2), 'T,B', 1, 'R');


        //Expense END HERE
        $pdf->Cell(190, 2, '', '', 1); //SPACE

        //GROSS
        $net =  $gross - $totalexpenses;
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(95, 8, "NET PROFIT", 0, 0, 'L', 1); //your cell
        $pdf->Cell(95, 8, "Rs. " . number_format($net, 2), 0, 1, 'R', 1); //your cell

        //save file
        $pdf->Output('Profit & Loss Details.pdf', 'I');
    }

    public function customerAgingReport(Request $request, Vendor $vendor, Report $report)
    {
        $company = $vendor->company(session('company_id'));
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
        $pdf->Cell(190, 10, 'Customer Aging Report', 'B,T', 1, 'L');
        $pdf->ln(1);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(10, 7, 'Id', 'B', 0, 'L', 1);
        $pdf->Cell(110, 7, 'Customer Name', 'B', 0, 'L', 1);
        $pdf->Cell(30, 7, 'Contact', 'B', 0, 'L', 1);
        $pdf->Cell(20, 7, 'Last Order Date', 'B', 0, 'R', 1);
        $pdf->Cell(20, 7, 'Total Days', 'B', 1, 'R', 1);

        $orders = $report->customerAgingQuery();

        $pdf->SetFont('Arial', '', 10);
        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);

        foreach ($orders as $value) {
            $date = Carbon::parse($value->lastorderdate);
            $now = Carbon::now();

            $pdf->Cell(10, 7, $value->id, 'B', 0, 'L', 1);
            $pdf->Cell(100, 7, $value->name, 'B', 0, 'L', 1);
            $pdf->Cell(40, 7, $value->mobile, 'B', 0, 'C', 1);
            $pdf->Cell(20, 7, date("d M Y", strtotime($value->lastorderdate)), 'B', 0, 'R', 1);
            $pdf->Cell(20, 7, $date->diffInDays($now), 'B', 1, 'R', 1);
        }


        //save file
        $pdf->Output('CustomerAging.pdf', 'I');
    }

    //inventory summary report
    public function inventoryReport(Request $request, Vendor $vendor, Report $report)
    {
        $company = $vendor->company(session('company_id'));

        $branchname = "";

        if ($request->branch != "all") {
            $branchname = Branch::where("branch_id", $request->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " (All Branches) ";
        }

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
        $pdf->Cell(190, 10, 'Stock Summary ' . $branchname, 'B,T', 1, 'L');
        $pdf->ln(1);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(28, 7, 'Item Code', 'B', 0, 'L', 1);
        $pdf->Cell(50, 7, 'Inventory', 'B', 0, 'L', 1);
        $pdf->Cell(10, 7, 'UOM', 'B', 0, 'L', 1);
        // $pdf->Cell(28,7,'Avg. Cost','B',0,'R',1);
        // $pdf->Cell(33,7,'Asset','B',0,'R',1);
        $pdf->Cell(23, 7, 'Cost Pr.', 'B', 0, 'R', 1);
        $pdf->Cell(23, 7, 'Retail Pr.', 'B', 0, 'R', 1);
        $pdf->Cell(28, 7, 'Total', 'B', 0, 'R', 1);
        $pdf->Cell(28, 7, 'On Hand', 'B', 1, 'R', 1);

        $inventory = $report->get_inventory_details($request->branch, $request->department, $request->subdepartment);

        $pdf->SetFont('Arial', '', 10);
        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);

        $qty = 0;
        $totalQty = 0;
        $totalRetail = 0;
        $totalCost = 0;
        $asset = 0;
        foreach ($inventory as $value) {
            $qty = $qty + $value->qty;
            $totalQty = $totalQty + $value->totalqty;
            $cost = str_replace(',', '', $value->cost);
            $totalRetail = $totalRetail + $value->retail_price;
            // $totalCost = $totalCost + $value->cost_price;
            $totalCost = $totalCost + $cost;
            // echo $value->qty." | ".$cost." -".  $value->qty * $cost."</br>";   
            $asset = $asset +  $value->qty * $cost ;//($value->qty * number_format($cost,2));
            $pdf->Cell(28, 5, $value->item_code, 0, 0, 'L', 1);
            $pdf->Cell(50, 5, $value->product_name, 0, 0, 'L', 1);
            // $pdf->Cell(23,5,number_format($value->qty,2),0,0,'L',1);
            $pdf->Cell(10, 5, $value->um, 0, 0, 'L', 1);
            // $pdf->Cell(28,5, number_format($value->cost,2),0,0,'R',1);
            // $pdf->Cell(33,5,number_format($value->qty*$value->cost,2),0,0,'R',1);
            $pdf->Cell(23, 5, number_format($cost,2), 0, 0, 'R', 1);
            $pdf->Cell(23, 5, number_format($value->retail_price, 2), 0, 0, 'R', 1);
            $pdf->Cell(28, 5, number_format($value->totalqty, 2), 0, 0, 'R', 1);
            $pdf->Cell(28, 5, number_format($value->qty, 2), 0, 1, 'R', 1);
        }
        $pdf->Cell(190, 2, '', '', 1); //SPACE
        //total
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(88, 7, "Total", 'T,B', 0, 'L', 1);
        $pdf->Cell(23, 7, number_format($totalCost, 2), 'T,B', 0, 'R', 1);
        $pdf->Cell(23, 7, number_format($totalRetail, 2), 'T,B', 0, 'R', 1);
        $pdf->Cell(28, 7, number_format($totalQty, 2), 'T,B', 0, 'R', 1);
        $pdf->Cell(28, 7, number_format($qty, 2), 'T,B', 1, 'R', 1);



        //save file
        $pdf->Output('Stock Report.pdf', 'I');
    }

    //inventory details report
    public function inventory_detailsPDF(Request $request, Vendor $vendor, Report $report)
    {
        $company = $vendor->company(session('company_id'));

        $branchname = "";

        if ($request->branch != "all") {
            $branchname = Branch::where("branch_id", $request->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " (All Branches) ";
        }

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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Inventory Details ' . $branchname, 'B,T', 1, 'L');
        $pdf->ln(1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(35, 7, 'Date', 'B', 0, 'L', 1);
        $pdf->Cell(70, 7, 'Details', 'B', 0, 'L', 1);
        $pdf->Cell(15, 7, 'Qty', 'B', 0, 'R', 1);
        $pdf->Cell(20, 7, 'Stock', 'B', 0, 'R', 1);
        $pdf->Cell(25, 7, 'Cost', 'B', 0, 'R', 1);
        $pdf->Cell(25, 7, 'Asset', 'B', 1, 'R', 1);

        $products = $report->get_inventory_products($request->branch, $request->department, $request->subdepartment);



        $qty = 0;
        foreach ($products as $value) {

            $pdf->Cell(190, 2, '', '', 1); //SPACE
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);

            // $pdf->Cell(40,40,$pdf->Image(asset('storage/images/company/'.$company[0]->logo),$pdf->GetX(), $pdf->GetY(), 33.78),0,0,'R',1);
            $pdf->Cell(40, 7, $value->item_code, 0, 0, 'L', 1);
            $pdf->Cell(150, 7, $value->product_name, 0, 1, 'L', 1);

            $details = $report->stock_report_details($value->id, $request->fromdate, $request->todate, $request->branch, $request->department);
            foreach ($details as $values) {
                if ($values->narration == "Sales") {
                    $qty = (($values->qty) * (-1));
                } else {
                    $qty = $values->qty;
                }
                $pdf->SetFont('Arial', '', 10);
                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);

                $pdf->Cell(35, 5, date("Y-m-d", strtotime($values->date)), 0, 0, 'L', 1);
                $pdf->Cell(70, 5, $values->narration, 0, 0, 'L', 1);
                $pdf->Cell(15, 5, number_format($qty, 2), 0, 0, 'R', 1);
                $pdf->Cell(20, 5, number_format($values->stock, 2), 0, 0, 'R', 1);
                $pdf->Cell(25, 5, number_format($values->cost, 2), 0, 0, 'R', 1);
                $pdf->Cell(25, 5, number_format($values->qty * $values->cost, 2), 0, 1, 'R', 1);
            }
            $pdf->Cell(190, 2, '', '', 1); //SPACE
            //total
            $total = $report->current_stock_asset($value->id);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(120, 7, "Total " . $value->product_name, 'T,B', 0, 'L', 1);
            $pdf->Cell(20, 7, number_format($total[0]->stock, 2), 'T,B', 0, 'R', 1);
            $pdf->Cell(25, 7, "", 'T,B', 0, 'L', 1);
            $pdf->Cell(25, 7, number_format($total[0]->stock * $total[0]->cp, 2), 'T,B', 1, 'R', 1);
        }




        //save file
        $pdf->Output('Inventory_Details.pdf', 'I');
    }


    public function cash_voucher(Request $request, Vendor $vendor, Report $report)
    {
        $company = $vendor->company(session('company_id'));

        //get cash details
        $cash = $report->cash_voucher($request->id);


        $pdf = app('Fpdf');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        if (!file_exists(asset('storage/images/company/qrcode.png'))) {
            $qrcodetext = $company[0]->name . " | " . $company[0]->ptcl_contact . " | " . $company[0]->address;
            \QrCode::size(200)
                ->format('png')
                ->generate($qrcodetext, Storage::disk('public')->put("images/company/", "qrcode.png"));
        }

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
        $pdf->Cell(190, 10, 'Cash Voucher (' . $cash[0]->date . ' )', 'B,T', 1, 'L');

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(20, 8, 'Sr.', 'B,R', 0, 'C');
        $pdf->Cell(140, 8, 'Discription', 'B,R', 0, 'L');
        $pdf->Cell(30, 8, 'Amount', 'B', 1, 'R');



        $total = 0;
        $amount = 0;
        foreach ($cash as $key => $value) {
            if ($value->debit > 0) {
                $amount = $value->debit;
                $total = $total + $amount;
            } else {
                $amount = $value->credit;
                $total = $total + $amount;
            }
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(20, 10, $key + 1, 'R', 0, 'C');
            $pdf->Cell(140, 10, $value->narration, 'R', 0, 'L');
            $pdf->Cell(30, 10, number_format($amount, 2), 0, 1, 'R');
        }


        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(160, 8, 'Total:', 'T,B', 0, 'R');
        $pdf->Cell(30, 8, number_format($total, 2), 'T,B', 1, 'R');

        $pdf->ln(3);
        // Select Arial italic 8
        $pdf->SetFont('Arial', 'I', 10);
        // Print centered page number
        $pdf->Cell(160, 2, 'System Generated Report: Sabify', 0, 0, 'L');

        //save file
        $pdf->Output('Cash Voucher.pdf', 'I');
    }


    public function expense_by_categorypdf(Request $request, report $report, expense $expense)
    {
        $company = $expense->company(session('company_id'));
        //get expense details
        $expenses = $report->expenses_details($request->fromdate, $request->todate, $request->branch);


        $pdf = new pdfClass();
        $pdf->AliasNbPages();
        $pdf->AddPage();

        if (!file_exists(asset('storage/images/company/qrcode.png'))) {
            $qrcodetext = $company[0]->name . " | " . $company[0]->ptcl_contact . " | " . $company[0]->address;
            \QrCode::size(200)
                ->format('png')
                ->generate($qrcodetext, Storage::disk('public')->put("images/company/", "qrcode.png"));
        }

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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Expense By Category', 'B,T', 1, 'L');

        if (session('company_id') == 7) {
            // Separate expenses by platform_type
            $webExpenses = collect($expenses)->where('platform_type', 1);
            $otherExpenses = collect($expenses)->where('platform_type', '!=', 1);
            
            $total = 0;
            $sr = 1;
            
            // Web Expenses Section
            if ($webExpenses->count() > 0) {
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->Cell(190, 8, 'WEB EXPENSES', 'B', 1, 'L');
                
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(20, 8, 'Sr.', 'B,R', 0, 'C');
                $pdf->Cell(140, 8, 'Expense Discription', 'B,R', 0, 'L');
                $pdf->Cell(30, 8, 'Amount', 'B', 1, 'R');
                
                foreach ($webExpenses as $value) {
                    $total += $value->net_amount;
                    $pdf->SetFont('Arial', 'B', 11);
                    $pdf->Cell(20, 7, $sr++, 'R', 0, 'C');
                    $pdf->Cell(140, 7, $value->expense_category . " | " . $value->date, 'R', 0, 'L');
                    $pdf->Cell(30, 7, number_format($value->net_amount, 2), 0, 1, 'R');
                    
                    $pdf->SetFont('Arial', '', 11);
                    $pdf->Cell(20, 3, '', 'R', 0, 'C');
                    $pdf->Cell(5, 3, '', '', 0, 'L');
                    $pdf->Cell(135, 3, $value->expense_details, 'R', 0, 'L');
                    $pdf->Cell(30, 3, '', 0, 1, 'R');
                    
                    $pdf->Cell(20, 3, '', 'R', 0, 'C');
                    $pdf->Cell(170, 3, '', 0, 1, 'L');
                }
                $pdf->ln(5);
            }
            
            // Other Expenses Section
            if ($otherExpenses->count() > 0) {
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->Cell(190, 8, 'OTHER EXPENSES', 'B', 1, 'L');
                
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(20, 8, 'Sr.', 'B,R', 0, 'C');
                $pdf->Cell(140, 8, 'Expense Discription', 'B,R', 0, 'L');
                $pdf->Cell(30, 8, 'Amount', 'B', 1, 'R');
                
                foreach ($otherExpenses as $value) {
                    $total += $value->net_amount;
                    $pdf->SetFont('Arial', 'B', 11);
                    $pdf->Cell(20, 7, $sr++, 'R', 0, 'C');
                    $pdf->Cell(140, 7, $value->expense_category . " | " . $value->date, 'R', 0, 'L');
                    $pdf->Cell(30, 7, number_format($value->net_amount, 2), 0, 1, 'R');
                    
                    $pdf->SetFont('Arial', '', 11);
                    $pdf->Cell(20, 3, '', 'R', 0, 'C');
                    $pdf->Cell(5, 3, '', '', 0, 'L');
                    $pdf->Cell(135, 3, $value->expense_details, 'R', 0, 'L');
                    $pdf->Cell(30, 3, '', 0, 1, 'R');
                    
                    $pdf->Cell(20, 3, '', 'R', 0, 'C');
                    $pdf->Cell(170, 3, '', 0, 1, 'L');
                }
            }
        } else {
            // Original format for other companies
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(20, 8, 'Sr.', 'B,R', 0, 'C');
            $pdf->Cell(140, 8, 'Expense Discription', 'B,R', 0, 'L');
            $pdf->Cell(30, 8, 'Amount', 'B', 1, 'R');
            
            $total = 0;
            foreach ($expenses as $key => $value) {
                $total += $value->net_amount;
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(20, 7, $key + 1, 'R', 0, 'C');
                $pdf->Cell(140, 7, $value->expense_category . " | " . $value->date, 'R', 0, 'L');
                $pdf->Cell(30, 7, number_format($value->net_amount, 2), 0, 1, 'R');
                
                $pdf->SetFont('Arial', '', 11);
                $pdf->Cell(20, 3, '', 'R', 0, 'C');
                $pdf->Cell(5, 3, '', '', 0, 'L');
                $pdf->Cell(135, 3, $value->expense_details, 'R', 0, 'L');
                $pdf->Cell(30, 3, '', 0, 1, 'R');
                
                $pdf->Cell(20, 3, '', 'R', 0, 'C');
                $pdf->Cell(170, 3, '', 0, 1, 'L');
            }
        }


        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(160, 6, 'Total:', 'T', 0, 'R');
        $pdf->Cell(30, 6, number_format($total, 2), 'T', 1, 'R');

        //save file
        $pdf->Output('Expense_by_Category.pdf', 'I');
    }

    public function salesdeclerationreport(Request $request, Vendor $vendor, Report $report)
    {
        $company = $vendor->company(session('company_id'));

        $branchname = "";

        if ($request->branch != "all") {
            $branchname = Branch::where("branch_id", $request->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " (All Branches) ";
        }

        $pdf = app('Fpdf');
        $pdf->AliasNbPages();
        $pdf->AddPage(['L', 'mm', array(100, 150)]);

        if (!file_exists(asset('storage/images/company/qrcode.png'))) {
            $qrcodetext = $company[0]->name . " | " . $company[0]->ptcl_contact . " | " . $company[0]->address;
            \QrCode::size(200)
                ->format('png')
                ->generate($qrcodetext, Storage::disk('public')->put("images/company/", "qrcode.png"));
        }

        //first row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Cell(105, 0, "Company Name:", 0, 0, 'L');
        $pdf->Cell(135, 0, "", 0, 1, 'R');

        //second row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Image(asset('storage/images/company/' . $company[0]->logo), 12, 10, -200);
        $pdf->Cell(105, 12, $company[0]->name, 0, 0, 'L');
        $pdf->Cell(135, 0, "", 0, 1, 'R');
        $pdf->Image(asset('storage/images/company/qrcode.png'), 260, 10, -200);

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
        $pdf->Cell(190, -18, $company[0]->address, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, -18, "Generate Date:  " . date('Y-m-d'), 0, 1, 'R');

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(275, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(275, 10, 'Sales Decleration Report ' . $branchname, 'B,T', 1, 'L');


        $pdf->SetFont('Arial', 'B', 10);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(14, 7, 'Date', 'B', 0, 'C', 1);
        $pdf->Cell(14, 7, 'Time', 'B', 0, 'C', 1);
        $pdf->Cell(20, 7, 'Opening', 'B', 0, 'L', 1);
        $pdf->Cell(20, 7, 'Cash', 'B', 0, 'L', 1);
        $pdf->Cell(20, 7, 'Card', 'B', 0, 'L', 1);
        $pdf->Cell(20, 7, 'Credit', 'B', 0, 'L', 1);
        $pdf->Cell(24, 7, 'Total', 'B', 0, 'L', 1);
        $pdf->Cell(20, 7, 'Discount', 'B', 0, 'L', 1);
        $pdf->Cell(19, 7, 'Sales', 'B', 0, 'L', 1);
        $pdf->Cell(22.5, 7, 'Cash ', 'B', 0, 'L', 1); //In
        if (session("company_id") != 102) {
            $pdf->Cell(22.5, 7, 'Delivery', 'B', 0, 'L', 1); //out
        }

        $pdf->Cell(22.5, 7, 'Expenses', 'B', 0, 'L', 1); //return 15
        $pdf->Cell(22.5, 7, 'Cash', 'B', 0, 'L', 1); //in hand 15
        if (session("company_id") != 102) {
            $pdf->Cell(22, 7, 'Closing', 'B', 1, 'L', 1); // Amount
        } else {
            $pdf->Cell(22, 7, 'Closing', 'B', 0, 'L', 1);
            $pdf->Cell(22, 7, 'Difference', 'B', 1, 'L', 1);
        }

        //second row
        $pdf->Cell(14, 4, '', 'B', 0, 'L', 1);
        $pdf->Cell(14, 4, '', 'B', 0, 'L', 1);
        $pdf->Cell(20, 4, 'Amount', 'B', 0, 'L', 1);
        $pdf->Cell(20, 4, 'Sales', 'B', 0, 'L', 1);
        $pdf->Cell(20, 4, 'Sales', 'B', 0, 'L', 1);
        $pdf->Cell(20, 4, 'Sales', 'B', 0, 'L', 1);
        $pdf->Cell(24, 4, 'Sales', 'B', 0, 'L', 1);
        $pdf->Cell(20, 4, '', 'B', 0, 'L', 1);
        $pdf->Cell(19, 4, 'Return', 'B', 0, 'L', 1);
        $pdf->Cell(22.5, 4, 'In/Out', 'B', 0, 'L', 1); //In/Out
        if (session("company_id") != 102) {
            $pdf->Cell(22.5, 4, '', 'B', 0, 'L', 1); //Delivery
        }
        $pdf->Cell(22.5, 4, '', 'B', 0, 'L', 1); //Expenses
        $pdf->Cell(22.5, 4, 'In Hand', 'B', 0, 'L', 1); //in hand
        if (session("company_id") != 102) {
            $pdf->Cell(22, 4, 'Amount', 'B', 1, 'L', 1); // Amount
        } else {
            $pdf->Cell(22, 4, 'Amount', 'B', 0, 'L', 1);
            $pdf->Cell(22, 4, '', 'B', 1, 'L', 1);
        }


        $cashinhand = 0;

        //variables for total
        $totalop = 0;
        $totalcash = 0;
        $totalcard = 0;
        $totalcredit = 0;
        $totalsales = 0;
        $totaldiscount = 0;
        $totaldelivery = 0;
        $totalexpenses = 0;
        $totalpromo = 0;
        $totalcoupon = 0;
        $totalsaletax = 0;
        $totalservicetax = 0;
        $totalcashin = 0;
        $totalcashout = 0;
        $totalpaid = 0;
        $totalhand = 0;
        $totalclosing = 0;
        $totalbalance = 0;
        $totalsalesreturn = 0;

        //quries
        if ($request->terminalid == 0) {
            $terminals = $report->getTerminals($request->branch);
            foreach ($terminals as $values) {
                $cashinhand = 0;

                //variables for total
                $totalop = 0;
                $totalcash = 0;
                $totalcard = 0;
                $totalcredit = 0;
                $totalsales = 0;
                $totaldiscount = 0;
                $totaldelivery = 0;
                $totalexpenses = 0;
                $totalpromo = 0;
                $totalcoupon = 0;
                $totalsaletax = 0;
                $totalservicetax = 0;
                $totalcashin = 0;
                $totalcashout = 0;
                $totalpaid = 0;
                $totalhand = 0;
                $totalclosing = 0;
                $totalbalance = 0;
                $totalsalesreturn = 0;

                $pdf->SetFont('Arial', 'B', 14);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(275, 10, "Terminal Name: " . $values->terminal_name, 0, 1, 'L');
                $details = $report->sales_details($values->terminal_id, $request->fromdate, $request->todate, $request->branch);

                foreach ($details as $value) {
                    $cashinhand = ($value->bal + $value->Cash + $value->sale_tax + $value->service_tax + $value->cashIn + $value->adv_booking_cash + $value->order_delivered_cash) - ($value->cashOut + $value->Expenses + $value->SalesReturn); //- $value->Discount - $value->promo - $value->coupon
                    if (session("company_id") == 102) {
                        $cashinhand = $cashinhand - $value->bal;
                    }
                    $balance = (float)$value->closingBal - (float)$cashinhand;
                    //total calculation
                    $totalsalesreturn = $totalsalesreturn + $value->SalesReturn;
                    $totalop = $totalop + $value->bal;
                    $totalcash = $totalcash + $value->Cash;
                    $totalcard = $totalcard + $value->CreditCard;
                    $totalcredit = $totalcredit + $value->CustomerCredit;
                    $totalsales = $totalsales + $value->TotalSales;
                    $totaldiscount = $totaldiscount + $value->Discount;
                    $totaldelivery = $totaldelivery + $value->Delivery;
                    $totalexpenses = $totalexpenses + $value->Expenses;
                    $totalpromo = $totalpromo + $value->promo;
                    $totalcoupon = $totalcoupon + $value->coupon;
                    $totalsaletax = $totalsaletax + $value->sale_tax;
                    $totalservicetax = $totalservicetax + $value->service_tax;
                    $totalcashin = $totalcashin + $value->cashIn;
                    $totalcashout = $totalcashout + $value->cashOut;
                    $totalpaid = $totalpaid + $value->paidByCustomer;
                    $totalhand = $totalhand + $cashinhand;
                    $totalclosing = (float) $totalclosing + (float) $value->closingBal;
                    $totalbalance = (float) $totalbalance + (float) $balance;



                    $pdf->SetFont('Arial', '', 9);
                    $pdf->setFillColor(232, 232, 232);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(14, 7, date("d-m-y", strtotime($value->date)), 0, 0, 'C', 1);
                    $pdf->Cell(14, 7, date("h:i A", strtotime($value->time)), 0, 0, 'C', 1);
                    $pdf->Cell(20, 7, number_format($value->bal, 2), 0, 0, 'C', 1);
                    $pdf->Cell(20, 7, number_format($value->Cash, 2), 0, 0, 'L', 1);
                    $pdf->Cell(20, 7, number_format($value->CreditCard, 2), 0, 0, 'L', 1);
                    $pdf->Cell(20, 7, number_format($value->CustomerCredit, 2), 0, 0, 'L', 1);
                    $pdf->Cell(24, 7, number_format($value->TotalSales, 2), 0, 0, 'L', 1);
                    $pdf->Cell(20, 7, number_format($value->Discount, 2), 0, 0, 'L', 1);
                    $pdf->Cell(19, 7, number_format($value->SalesReturn, 2), 0, 0, 'L', 1);
                    $pdf->Cell(22.5, 7, number_format($value->cashIn, 2) . "/" . number_format($value->cashOut, 2), 0, 0, 'L', 1);
                    if (session("company_id") != 102) {
                        $pdf->Cell(22.5, 7, number_format($value->Delivery, 2), 0, 0, 'L', 1);
                    }
                    $pdf->Cell(22.5, 7, number_format($value->Expenses, 2), 0, 0, 'L', 1);
                    $pdf->Cell(22.5, 7, number_format($cashinhand, 2), 0, 0, 'L', 1); //cash in hand
                    if (session("company_id") != 102) {
                        $pdf->Cell(22, 7, number_format($value->closingBal, 2), 0, 1, 'L', 1); //closing amount
                    } else {
                        $pdf->Cell(22, 7, number_format($value->closingBal, 2), 0, 0, 'L', 1);
                        $pdf->Cell(22.5, 7, number_format($balance, 2), 0, 1, 'L', 1);
                    }

                    $pdf->ln(1);
                }
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(14, 7, "Total", 'B,T', 0, 'L');
                $pdf->Cell(14, 7, "", 'B,T', 0, 'L');
                $pdf->Cell(20, 7, number_format($totalop, 2), 'B,T', 0, 'C');
                $pdf->Cell(20, 7, number_format($totalcash, 2), 'B,T', 0, 'L');
                $pdf->Cell(20, 7, number_format($totalcard, 2), 'B,T', 0, 'L');
                $pdf->Cell(20, 7, number_format($totalcredit, 2), 'B,T', 0, 'L');
                $pdf->Cell(24, 7, number_format($totalsales, 2), 'B,T', 0, 'L');
                $pdf->Cell(20, 7, number_format($totaldiscount, 2), 'B,T', 0, 'L');
                $pdf->Cell(19, 7, number_format($totalsalesreturn, 2), 'B,T', 0, 'L');
                $pdf->Cell(22.5, 7, number_format($totalcashin, 2) . "/" . number_format($totalcashout, 2), 'B,T', 0, 'L');
                if (session("company_id") != 102) {
                    $pdf->Cell(22.5, 7, number_format($totaldelivery, 2), 'B,T', 0, 'L');
                }
                $pdf->Cell(22.5, 7, number_format($totalexpenses, 2), 'B,T', 0, 'L');
                $pdf->Cell(22.5, 7, number_format($totalhand, 2), 'B,T', 0, 'L');
                if (session("company_id") != 102) {
                    $pdf->Cell(22, 7, number_format($totalclosing, 2), 'B,T', 1, 'L');
                } else {
                    $pdf->Cell(22, 7, number_format($totalclosing, 2), 'B,T', 0, 'L');
                    $pdf->Cell(22.5, 7, number_format($totalbalance, 2), 'B,T', 1, 'L');
                }
            }
        } else {
            $terminals = $report->get_terminals_byid($request->terminalid);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(275, 10, "Terminal Name: " . $terminals[0]->terminal_name, 0, 1, 'L');
            $details = $report->sales_details($request->terminalid, $request->fromdate, $request->todate, $request->branch);

            foreach ($details as $key => $value) {
                $cashinhand = ($value->bal + $value->Cash + $value->sale_tax + $value->service_tax + $value->cashIn + $value->adv_booking_cash + $value->order_delivered_cash) - ($value->cashOut + $value->Expenses + $value->SalesReturn); //- $value->Discount - $value->promo - $value->coupon
                if (session("company_id") == 102) {
                    $cashinhand = $cashinhand - $value->bal;
                }
                $balance = $value->closingBal - $cashinhand;
                //total calculation
                $totalsalesreturn = $totalsalesreturn + $value->SalesReturn;
                $totalop = $totalop + $value->bal;
                $totalcash = $totalcash + $value->Cash;
                $totalcard = $totalcard + $value->CreditCard;
                $totalcredit = $totalcredit + $value->CustomerCredit;
                $totalsales = $totalsales + $value->TotalSales;
                $totaldiscount = $totaldiscount + $value->Discount;
                $totaldelivery = $totaldelivery + $value->Delivery;
                $totalexpenses = $totalexpenses + $value->Expenses;
                $totalpromo = $totalpromo + $value->promo;
                $totalcoupon = $totalcoupon + $value->coupon;
                $totalsaletax = $totalsaletax + $value->sale_tax;
                $totalservicetax = $totalservicetax + $value->service_tax;
                $totalcashin = $totalcashin + $value->cashIn;
                $totalcashout = $totalcashout + $value->cashOut;
                $totalpaid = $totalpaid + $value->paidByCustomer;
                $totalhand = $totalhand + $cashinhand;
                $totalclosing = $totalclosing + $value->closingBal;
                $totalbalance = $totalbalance + $balance;

                $pdf->SetFont('Arial', '', 9);
                $pdf->setFillColor(232, 232, 232);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(14, 7, date("d-m-y", strtotime($value->date)), 0, 0, 'C', 1);
                $pdf->Cell(14, 7, date("h:i A", strtotime($value->time)), 0, 0, 'C', 1);
                $pdf->Cell(20, 7, number_format($value->bal, 2), 0, 0, 'C', 1);
                $pdf->Cell(20, 7, number_format($value->Cash, 2), 0, 0, 'L', 1);
                $pdf->Cell(20, 7, number_format($value->CreditCard, 2), 0, 0, 'L', 1);
                $pdf->Cell(20, 7, number_format($value->CustomerCredit, 2), 0, 0, 'L', 1);
                $pdf->Cell(24, 7, number_format($value->TotalSales, 2), 0, 0, 'L', 1);
                $pdf->Cell(20, 7, number_format($value->Discount, 2), 0, 0, 'L', 1);;
                $pdf->Cell(19, 7, number_format($value->SalesReturn, 2), 0, 0, 'L', 1);
                $pdf->Cell(22.5, 7, number_format($value->cashIn, 2) . "/" . number_format($value->cashOut, 2), 0, 0, 'L', 1);
                if (session("company_id") != 102) {
                    $pdf->Cell(22.5, 7, number_format($value->Delivery, 2), 0, 0, 'L', 1);
                }
                $pdf->Cell(22.5, 7, number_format($value->Expenses, 2), 0, 0, 'L', 1);
                $pdf->Cell(22.5, 7, number_format($cashinhand, 2), 0, 0, 'L', 1); //cash in hand
                if (session("company_id") != 102) {
                    $pdf->Cell(22, 7, number_format($value->closingBal, 2), 0, 1, 'L', 1); //closing amount
                } else {
                    $pdf->Cell(22, 7, number_format($value->closingBal, 2), 0, 0, 'L', 1);
                    $pdf->Cell(22.5, 7, number_format($balance, 2), 0, 1, 'L', 1);
                }

                $pdf->ln(1);
            }
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(14, 7, "Total", 'B,T', 0, 'L');
            $pdf->Cell(14, 7, "", 'B,T', 0, 'L');
            $pdf->Cell(20, 7, number_format($totalop, 2), 'B,T', 0, 'C');
            $pdf->Cell(20, 7, number_format($totalcash, 2), 'B,T', 0, 'L');
            $pdf->Cell(20, 7, number_format($totalcard, 2), 'B,T', 0, 'L');
            $pdf->Cell(20, 7, number_format($totalcredit, 2), 'B,T', 0, 'L');
            $pdf->Cell(24, 7, number_format($totalsales, 2), 'B,T', 0, 'L');
            $pdf->Cell(20, 7, number_format($totaldiscount, 2), 'B,T', 0, 'L');
            $pdf->Cell(19, 7, number_format($totalsalesreturn, 2), 'B,T', 0, 'L');
            $pdf->Cell(22.5, 7, number_format($totalcashin, 2) . "/" . number_format($totalcashout, 2), 'B,T', 0, 'L');
            if (session("company_id") != 102) {
                $pdf->Cell(22.5, 7, number_format($totaldelivery, 2), 'B,T', 0, 'L');
            }
            $pdf->Cell(22.5, 7, number_format($totalexpenses, 2), 'B,T', 0, 'L');
            $pdf->Cell(22.5, 7, number_format($totalhand, 2), 'B,T', 0, 'L');
            if (session("company_id") != 102) {
                $pdf->Cell(22, 7, number_format($totalclosing, 2), 'B,T', 1, 'L');
            } else {
                $pdf->Cell(22, 7, number_format($totalclosing, 2), 'B,T', 0, 'L');
                $pdf->Cell(22.5, 7, number_format($totalbalance, 2), 'B,T', 1, 'L');
            }
        }

        //save file
        $pdf->Output('Sales_Decleration_Report.pdf', 'I');
    }

    public function fbrReport(Request $request, Vendor $vendor, Report $report)
    {
        $company = $vendor->company(session('company_id'));

        $branchname = "";

        if ($request->branch != "all") {
            $branchname = Branch::where("branch_id", $request->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " (All Branches) ";
        }

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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'FBR Report ' . $branchname, 'B,T', 1, 'L');
        $pdf->ln(1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(10, 7, 'S.No', 'B', 0, 'L', 1);
        $pdf->Cell(30, 7, 'Sales ID', 'B', 0, 'C', 1);
        $pdf->Cell(45, 7, 'FBR Inv Number', 'B', 0, 'L', 1);
        $pdf->Cell(25, 7, 'Date', 'B', 0, 'C', 1);
        $pdf->Cell(25, 7, 'Sales', 'B', 0, 'C', 1);
        $pdf->Cell(20, 7, 'S.Tax', 'B', 0, 'C', 1);
        $pdf->Cell(35, 7, 'Total Amount', 'B', 1, 'C', 1);

        //total variables
        $totalqty = 0;
        $totalactualamount = 0;
        $totalsalestax = 0;
        $totalamount = 0;
        $price = 0;

        if ($request->terminalid == 0) {

            $terminals = $report->getTerminals($request->branch);
            foreach ($terminals as  $values) {
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(190, 10, "Terminal Name: " . $values->terminal_name, 0, 1, 'L');
                $details = $report->sales($values->terminal_id, $request->fromdate, $request->todate);

                foreach ($details as $key => $value) {
                    $actualAmount = 0;
                    $salesTaxAmount = 0;
                    if ($value->actual_amount == 0) {
                        $actualAmount = $value->total_amount - $value->sales_tax_amount;
                    } else {
                        $actualAmount = $value->actual_amount;
                    }

                    $totalqty = $totalqty++;
                    $totalactualamount = $totalactualamount + $actualAmount;
                    $totalsalestax = $totalsalestax + $value->sales_tax_amount;
                    $totalamount = $totalamount + $value->total_amount;

                    $pdf->SetFont('Arial', '', 10);
                    $pdf->setFillColor(232, 232, 232);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(10, 6, ++$key, 0, 0, 'L', 1);
                    $pdf->Cell(30, 6, $value->id, 0, 0, 'C', 1);
                    $pdf->Cell(45, 6, $value->fbrInvNumber, 0, 0, 'L', 1);
                    $pdf->Cell(25, 6, date("d M Y", strtotime($value->date)), 0, 0, 'C', 1);
                    $pdf->Cell(25, 6, number_format($actualAmount, 2), 0, 0, 'C', 1);
                    $pdf->Cell(20, 6, number_format($value->sales_tax_amount, 2), 0, 0, 'C', 1);
                    $pdf->Cell(35, 6, number_format($value->total_amount, 2), 0, 1, 'C', 1);
                    $pdf->ln(1);
                }
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(55, 7, "", 'B,T', 0, 'L');
                $pdf->Cell(20, 7, "", 'B,T', 0, 'C');
                $pdf->Cell(35, 7, '', 'B,T', 0, 'C');
                $pdf->Cell(25, 7, number_format($totalactualamount, 2), 'B,T', 0, 'C');
                $pdf->Cell(20, 7, number_format($totalsalestax, 2), 'B,T', 0, 'C');
                $pdf->Cell(35, 7, number_format($totalamount, 2), 'B,T', 1, 'C');
            }
        } else {
            $terminals = $report->get_terminals_byid($request->terminalid);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(190, 10, "Terminal Name: " . $terminals[0]->terminal_name, 0, 1, 'L');
            $details = $report->itemsale_details($request->fromdate, $request->todate, $request->terminalid, "", "", "all", "all");
            foreach ($details as $value) {
                $actualAmount = 0;
                $salesTaxAmount = 0;
                if ($value->actual_amount == 0) {
                    $actualAmount = $value->total_amount - $value->sales_tax_amount;
                } else {
                    $actualAmount = $value->actual_amount;
                }
                $totalqty = $totalqty++;
                $totalactualamount = $totalactualamount + $actualAmount;
                $totalsalestax = $totalsalestax + $value->sales_tax_amount;
                $totalamount = $totalamount + $value->total_amount;

                $pdf->SetFont('Arial', '', 10);
                $pdf->setFillColor(232, 232, 232);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(10, 6, ++$key, 0, 0, 'L', 1);
                $pdf->Cell(30, 6, $value->id, 0, 0, 'C', 1);
                $pdf->Cell(45, 6, $value->fbrInvNumber, 0, 0, 'L', 1);
                $pdf->Cell(25, 6, date("d M Y", strtotime($value->date)), 0, 0, 'C', 1);
                $pdf->Cell(25, 6, number_format($actualAmount, 2), 0, 0, 'C', 1);
                $pdf->Cell(20, 6, number_format($value->sales_tax_amount, 2), 0, 0, 'C', 1);
                $pdf->Cell(35, 6, number_format($value->total_amount, 2), 0, 1, 'C', 1);
                $pdf->ln(1);
            }

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(55, 7, "", 'B,T', 0, 'L');
            $pdf->Cell(20, 7, "", 'B,T', 0, 'C');
            $pdf->Cell(35, 7, '', 'B,T', 0, 'C');
            $pdf->Cell(25, 7, number_format($totalactualamount, 2), 'B,T', 0, 'C');
            $pdf->Cell(20, 7, number_format($totalsalestax, 2), 'B,T', 0, 'C');
            $pdf->Cell(35, 7, number_format($totalamount, 2), 'B,T', 1, 'C');
        }

        //save file
        $pdf->Output('FBR_Report.pdf', 'I');
    }

    public function invoiceReport(Request $request, Vendor $vendor, Report $report)
    {
        $company = $vendor->company(session('company_id'));

        $branchname = "";

        if ($request->branch != "all") {
            $branchname = Branch::where("branch_id", $request->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " (All Branches) ";
        }

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
        $pdf->Cell(90, 0, "Company Name:", 0, 0, 'L');
        $pdf->Cell(75, 0, "NTN #", 0, 1, 'L');

        //second row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Image(asset('storage/images/company/' . $company[0]->logo), 10, 10, -200);
        $pdf->Cell(90, 10, $company[0]->name, 0, 0, 'L');
        $pdf->Cell(75, 10, "(" . $company[0]->ntn . ")", 0, 1, 'L');
        $pdf->Image(asset('storage/images/company/qrcode.png'), 175, 10, -200);

        //third row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 4, '', 0, 0);
        $pdf->Cell(90, 4, "Contact Number:", 0, 0, 'L');
        $pdf->Cell(75, 4, "SRB #", 0, 1, 'L');

        //forth row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, 6, '', 0, 0);
        $pdf->Cell(90, 6, $company[0]->ptcl_contact, 0, 0, 'L');
        $pdf->Cell(75, 6, "(" . $company[0]->srb . ")", 0, 1, 'L');

        //fifth row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 7, '', 0, 0);
        $pdf->Cell(105, 7, "Company Address:", 0, 0, 'L');
        $pdf->Cell(50, 7, "", 0, 1, 'R');
        //Generate Date:  ".date('Y-m-d')
        //sixth row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(35, 5, '', 0, 0);
        $pdf->Cell(105, 5, $company[0]->address, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, 5, "", 0, 1, 'R');

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Invoice Detail Report ' . $branchname, 'B,T', 1, 'L');
        $pdf->ln(1);

        //total variables
        $totalqty = 0;
        $totalactualamount = 0;
        $totaldiscountamount = 0;
        $totalsalestax = 0;
        $totalamount = 0;
        $totalreceiveamount = 0;
        $price = 0;
        $salesTax = 0;

        if ($request->terminalid == 0) {

            $terminals = $report->getTerminals($request->branch);
            foreach ($terminals as  $values) {
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(190, 10, "Terminal Name: " . $values->terminal_name, 0, 1, 'L');
                $details = $report->totalSales($values->terminal_id, $request->fromdate, $request->todate, $request->type, "all", $request->customer);
                $permission = $report->terminalPermission($values->terminal_id);

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(15, 7, 'No.', 'B', 0, 'C', 1);
                $pdf->Cell(20, 7, 'Date.', 'B', 0, 'C', 1);
                $pdf->Cell(45, 7, 'Name', 'B', 0, 'L', 1);
                $pdf->Cell(22, 7, 'Type', 'B', 0, 'L', 1);
                $pdf->Cell(25, 7, 'Items/Total', 'B', 0, 'C', 1);
                $pdf->Cell(20, 7, 'Base', 'B', 0, 'C', 1);
                $pdf->Cell(15, 7, 'Tax', 'B', 0, 'C', 1);
                $pdf->Cell(15, 7, 'Disc.', 'B', 0, 'C', 1);
                $pdf->Cell(20, 7, 'T.Amount', 'B', 1, 'R', 1);
                $pdf->setFillColor(232, 232, 232);
                $pdf->SetTextColor(0, 0, 0);

                foreach ($details as $key => $value) {
                    if ($permission[0]->fbr_sync == 1) {
                        $salesTax = $value->sales_tax_amount;
                    } else {
                        $salesTax = $value->srb;
                    }
                    if ($value->void_receipt == 0) {
                        $totalqty = $totalqty++;
                        $totalactualamount = $totalactualamount + $value->actual_amount;
                        $totalsalestax = $totalsalestax + $salesTax;
                        $totalamount = $totalamount + $value->total_amount;
                        $totalreceiveamount = $totalreceiveamount + $value->receive_amount;
                        $totaldiscountamount = $totaldiscountamount + $value->discount_amount;
                    }
                    $pdf->SetFont('Arial', '', 9);
                    if ($value->void_receipt == 1) {
                        $pdf->setFillColor(255, 0, 0);
                        $pdf->SetTextColor(255, 255, 255);
                    } else {
                        $pdf->setFillColor(232, 232, 232);
                        $pdf->SetTextColor(0, 0, 0);
                    }

                    $pdf->Cell(12, 6, $value->id, 0, 0, 'L', 1);
                    $pdf->Cell(16, 6, date("d-m-y", strtotime($value->date)), 0, 0, 'C', 1);
                    $pdf->Cell(38, 6, $value->customer, 0, 0, 'L', 1);
                    $pdf->Cell(17, 6, ucfirst(strtolower(str_replace(' ', '', $value->order_mode))), 0, 0, 'L', 1);
                    $pdf->Cell(25, 6, $value->countItems . "/" . $value->totalItems, 0, 0, 'C', 1);
                    $pdf->Cell(15, 6, number_format($value->actual_amount, 2), 0, 0, 'C', 1);
                    $pdf->Cell(25, 6, number_format($value->receive_amount, 2), 0, 0, 'C', 1);
                    $pdf->Cell(15, 6, number_format($salesTax, 2), 0, 0, 'C', 1);
                    $pdf->Cell(15, 6, number_format($value->discount_amount, 2), 0, 0, 'C', 1);
                    if (session("company_id") != 102) {
                        $pdf->Cell(20, 6, number_format($value->total_amount, 2), 0, 1, 'R', 1);
                    } else {
                        $pdf->Cell(20, 6, number_format($value->actual_amount - $value->receive_amount, 2), 0, 1, 'R', 1);
                    }
                    $pdf->ln(1);
                }

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(12, 6, '', 0, 0, 'L', 1);
                $pdf->Cell(16, 6, '', 0, 0, 'C', 1);
                $pdf->Cell(38, 6, '', 0, 0, 'L', 1);
                $pdf->Cell(17, 6, '', 0, 0, 'L', 1);
                $pdf->Cell(15, 6, '', 0, 0, 'L', 1);
                $pdf->Cell(25, 6, number_format($totalactualamount, 2), 0, 0, 'C', 1);
                $pdf->Cell(25, 6, number_format($totalreceiveamount, 2), 0, 0, 'C', 1);
                $pdf->Cell(15, 6, number_format($totalsalestax, 2), 0, 0, 'C', 1);
                $pdf->Cell(15, 6, number_format($totaldiscountamount, 2), 0, 0, 'C', 1);
                $pdf->Cell(20, 6, number_format($totalamount, 2), 0, 1, 'R', 1);
            }
        } else {
            $terminals = $report->get_terminals_byid($request->terminalid);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(190, 10, "Terminal Name: " . $terminals[0]->terminal_name, 0, 1, 'L');

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(12, 7, 'No.', 'B', 0, 'C', 1);
            $pdf->Cell(16, 7, 'Date.', 'B', 0, 'C', 1);
            $pdf->Cell(38, 7, 'Name', 'B', 0, 'L', 1);
            $pdf->Cell(17, 7, 'Type', 'B', 0, 'L', 1);
            $pdf->Cell(25, 7, 'Items/Total', 'B', 0, 'C', 1);
            $pdf->Cell(15, 7, 'Base', 'B', 0, 'C', 1);
            $pdf->Cell(25, 7, 'Received', 'B', 0, 'C', 1);
            $pdf->Cell(15, 7, 'Tax', 'B', 0, 'C', 1);
            $pdf->Cell(15, 7, 'Disc.', 'B', 0, 'L', 1);
            if (session("company_id") != 102) {
                $pdf->Cell(20, 7, 'T.Amount', 'B', 1, 'R', 1);
            } else {
                $pdf->Cell(20, 7, 'B.Amount', 'B', 1, 'R', 1);
            }

            $details = $report->totalSales($request->terminalid, $request->fromdate, $request->todate, $request->type, "all", $request->customer);
            $permission = $report->terminalPermission($request->terminalid);
            foreach ($details as $value) {

                if ($permission[0]->fbr_sync == 1) {
                    $salesTax = $value->sales_tax_amount;
                } else {
                    $salesTax = $value->srb;
                }
                if ($value->void_receipt == 0) {
                    $totalqty = $totalqty++;
                    $totalactualamount = $totalactualamount + $value->actual_amount;
                    $totalsalestax = $totalsalestax + $salesTax;
                    $totalamount = $totalamount + $value->total_amount;
                    $totalreceiveamount = $totalreceiveamount + $value->receive_amount;
                    $totaldiscountamount = $totaldiscountamount + $value->discount_amount;
                }
                $pdf->SetFont('Arial', '', 9);
                if ($value->void_receipt == 1) {
                    $pdf->setFillColor(255, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                } else {
                    $pdf->setFillColor(232, 232, 232);
                    $pdf->SetTextColor(0, 0, 0);
                }
                $pdf->Cell(12, 6, $value->id, 0, 0, 'L', 1);
                $pdf->Cell(16, 6, date("d-m-y", strtotime($value->date)), 0, 0, 'C', 1);
                $pdf->Cell(38, 6, $value->customer, 0, 0, 'L', 1);
                $pdf->Cell(17, 6, ucfirst(strtolower(str_replace(' ', '', $value->order_mode))), 0, 0, 'L', 1);
                $pdf->Cell(25, 6, $value->countItems . "/" . $value->totalItems, 0, 0, 'C', 1);
                $pdf->Cell(15, 6, number_format($value->actual_amount, 2), 0, 0, 'C', 1);
                $pdf->Cell(25, 6, number_format($value->receive_amount, 2), 0, 0, 'C', 1);
                $pdf->Cell(15, 6, number_format($salesTax, 2), 0, 0, 'C', 1);
                $pdf->Cell(15, 6, number_format($value->discount_amount, 2), 0, 0, 'C', 1);
                if (session("company_id") != 102) {
                    $pdf->Cell(20, 6, number_format($value->total_amount, 2), 0, 1, 'R', 1);
                } else {
                    $pdf->Cell(20, 6, number_format($value->actual_amount - $value->receive_amount, 2), 0, 1, 'R', 1);
                }
                $pdf->ln(1);
            }

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(12, 6, '', 0, 0, 'L', 1);
            $pdf->Cell(16, 6, '', 0, 0, 'C', 1);
            $pdf->Cell(38, 6, '', 0, 0, 'L', 1);
            $pdf->Cell(17, 6, '', 0, 0, 'L', 1);
            $pdf->Cell(15, 6, '', 0, 0, 'L', 1);
            $pdf->Cell(25, 6, number_format($totalactualamount, 2), 0, 0, 'C', 1);
            $pdf->Cell(25, 6, number_format($totalreceiveamount, 2), 0, 0, 'C', 1);
            $pdf->Cell(15, 6, number_format($totalsalestax, 2), 0, 0, 'C', 1);
            $pdf->Cell(15, 6, number_format($totaldiscountamount, 2), 0, 0, 'C', 1);
            $pdf->Cell(20, 6, number_format($totalamount, 2), 0, 1, 'R', 1);
        }
        //save file
        $pdf->Output('Invoice_Detail_Report.pdf', 'I');
    }

    public function salesInvoicesReport(Request $request, Vendor $vendor, Report $report)
    {
        $company = $vendor->company(session('company_id'));

        $branchname = "";

        if ($request->branch != "all") {
            $branchname = Branch::where("branch_id", $request->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " (All Branches) ";
        }

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
        $pdf->Cell(90, 0, "Company Name:", 0, 0, 'L');
        $pdf->Cell(75, 0, "NTN #", 0, 1, 'L');

        //second row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Image(asset('storage/images/company/' . $company[0]->logo), 10, 10, -200);
        $pdf->Cell(90, 10, $company[0]->name, 0, 0, 'L');
        $pdf->Cell(75, 10, "(" . $company[0]->ntn . ")", 0, 1, 'L');
        $pdf->Image(asset('storage/images/company/qrcode.png'), 175, 10, -200);

        //third row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 4, '', 0, 0);
        $pdf->Cell(90, 4, "Contact Number:", 0, 0, 'L');
        $pdf->Cell(75, 4, "SRB #", 0, 1, 'L');

        //forth row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, 6, '', 0, 0);
        $pdf->Cell(90, 6, $company[0]->ptcl_contact, 0, 0, 'L');
        $pdf->Cell(75, 6, "(" . $company[0]->srb . ")", 0, 1, 'L');

        //fifth row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 7, '', 0, 0);
        $pdf->Cell(105, 7, "Company Address:", 0, 0, 'L');
        $pdf->Cell(50, 7, "", 0, 1, 'R');
        //Generate Date:  ".date('Y-m-d')
        //sixth row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(35, 5, '', 0, 0);
        $pdf->Cell(105, 5, $company[0]->address, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, 5, "", 0, 1, 'R');

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Sales Invoice Details Report ' . $branchname, 'B,T', 1, 'L');
        $pdf->ln(1);

        //total variables
        $totalqty = 0;
        $totalactualamount = 0;
        $totaldiscountamount = 0;
        $totalsalestax = 0;
        $totalamount = 0;
        $price = 0;
        $salesTax = 0;
        $totalItemCount = 0;

        if ($request->terminalid == 0) {

            $terminals = $report->getTerminals($request->branch);
            foreach ($terminals as  $values) {
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(190, 10, "Terminal Name: " . $values->terminal_name, 0, 1, 'L');
                $details = $report->totalSales($values->terminal_id, $request->fromdate, $request->todate, $request->type, $request->category, $request->customer);
                $permission = $report->terminalPermission($values->terminal_id);

                $pdf->SetFont('Arial', 'B', 12);
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);

                $pdf->Cell(20, 7, 'No.', 'B', 0, 'C', 1);
                $pdf->Cell(60, 7, 'Name', 'B', 0, 'L', 1);
                $pdf->Cell(25, 7, 'Type', 'B', 0, 'C', 1);
                $pdf->Cell(20, 7, 'Base', 'B', 0, 'C', 1);
                $pdf->Cell(20, 7, 'Tax', 'B', 0, 'C', 1);
                $pdf->Cell(15, 7, 'Disc.', 'B', 0, 'C', 1);
                $pdf->Cell(30, 7, 'Total Amount', 'B', 1, 'R', 1);
                $pdf->setFillColor(232, 232, 232);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->ln(2);
                foreach ($details as $key => $value) {

                    if ($permission[0]->fbr_sync == 1) {
                        $salesTax = $value->sales_tax_amount;
                    } else {
                        $salesTax = $value->srb;
                    }

                    if ($value->void_receipt == 0) {
                        $totalqty = $totalqty++;
                        $totalactualamount = $totalactualamount + $value->actual_amount;
                        $totalsalestax = $totalsalestax + $salesTax;
                        $totalamount = $totalamount + $value->total_amount;
                        $totaldiscountamount = $totaldiscountamount + $value->discount_amount;
                    }

                    if ($permission[0]->fbr_sync == 1) {
                        $pdf->SetFont('Arial', 'B', 12);
                        $pdf->setFillColor(54, 69, 79);
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->Cell(190, 6, "FBR Invoice Number : " . $value->fbrInvNumber, 0, 1, 'C', 1);
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->setFillColor(160, 160, 160);
                        $pdf->SetTextColor(0, 0, 0);
                    }

                    $pdf->SetFont('Arial', '', 10);
                    if ($value->void_receipt == 1) {
                        $pdf->setFillColor(255, 0, 0);
                        $pdf->SetTextColor(255, 255, 255);
                    } else {
                        $pdf->setFillColor(160, 160, 160);
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $pdf->Cell(20, 6, $value->id, 0, 0, 'L', 1);
                    $pdf->Cell(60, 6, $value->customer, 0, 0, 'L', 1);
                    $pdf->Cell(25, 6, $value->order_mode, 0, 0, 'L', 1);
                    $pdf->Cell(20, 6, number_format($value->actual_amount, 2), 0, 0, 'C', 1);
                    $pdf->Cell(20, 6, number_format($salesTax, 2), 0, 0, 'C', 1);
                    $pdf->Cell(15, 6, number_format($value->discount_amount, 2), 0, 0, 'C', 1);
                    $pdf->Cell(30, 6, number_format($value->total_amount, 2), 0, 1, 'C', 1);
                    // $pdf->ln(1);

                    $receiptDetails = $report->receiptDetails($value->id);

                    if (!empty($receiptDetails)) {

                        $pdf->SetFont('Arial', 'B', 12);
                        $pdf->setFillColor(0, 153, 76);
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->Cell(20, 7, 'No.', 'B', 0, 'C', 1);
                        $pdf->Cell(65, 7, 'Name', 'B', 0, 'L', 1);
                        $pdf->Cell(30, 7, 'Price', 'B', 0, 'C', 1);
                        $pdf->Cell(30, 7, 'Qty', 'B', 0, 'C', 1);
                        $pdf->Cell(15, 7, 'Tax', 'B', 0, 'C', 1);
                        $pdf->Cell(30, 7, 'Total Amount', 'B', 1, 'R', 1);

                        foreach ($receiptDetails as $value) {
                            // THIS CODE IS ONLY FOR SNOWHITE FOR CALCULATING SHALWAR QAMEEZ TO DOUBLE;
                            $itemQty = 0;
                            if (session('company_id') == 74) {
                                $itemQty = $itemQty + ($value->total_qty * $value->weight_qty);
                            } else {
                                $itemQty = $value->total_qty;
                            }
                            // if($value->item_code == 817947 or $value->item_code == 817992 ){
                            // $itemQty = $itemQty + ($value->total_qty * 2);
                            // }else{
                            // $itemQty = $value->total_qty;
                            // }
                            $totalItemCount = $totalItemCount + $itemQty;
                            $pdf->SetFont('Arial', '', 10);
                            $pdf->setFillColor(232, 232, 232);
                            $pdf->SetTextColor(0, 0, 0);
                            $pdf->Cell(20, 7, $value->item_code, 'B', 0, 'C', 1);
                            $pdf->Cell(65, 7, $value->item_name, 'B', 0, 'L', 1);
                            $pdf->Cell(30, 7, number_format($value->item_price, 2), 'B', 0, 'C', 1);
                            $pdf->Cell(30, 7, number_format($value->total_qty, 2), 'B', 0, 'C', 1);
                            $pdf->Cell(15, 7, number_format($value->taxamount, 2), 'B', 0, 'C', 1);
                            $pdf->Cell(30, 7, number_format($value->total_amount, 2), 'B', 1, 'R', 1);
                        }
                        $pdf->ln(4);
                    }
                }
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(20, 6, 'Total Items :', 0, 0, 'L', 1);
                $pdf->Cell(65, 6, $totalItemCount, 0, 0, 'C', 1);
                $pdf->Cell(20, 6, '', 0, 0, 'L', 1);
                $pdf->Cell(20, 6, number_format($totalactualamount, 2), 0, 0, 'C', 1);
                $pdf->Cell(20, 6, number_format($totalsalestax, 2), 0, 0, 'C', 1);
                $pdf->Cell(15, 6, number_format($totaldiscountamount, 2), 0, 0, 'C', 1);
                $pdf->Cell(30, 6, number_format($totalamount, 2), 0, 1, 'C', 1);
            }
        } else {
            $terminals = $report->get_terminals_byid($request->terminalid);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(190, 10, "Terminal Name: " . $terminals[0]->terminal_name, 0, 1, 'L');

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(20, 7, 'No.', 'B', 0, 'C', 1);
            $pdf->Cell(60, 7, 'Name', 'B', 0, 'L', 1);
            $pdf->Cell(25, 7, 'Type', 'B', 0, 'C', 1);
            $pdf->Cell(20, 7, 'Base', 'B', 0, 'C', 1);
            $pdf->Cell(20, 7, 'Tax', 'B', 0, 'C', 1);
            $pdf->Cell(15, 7, 'Disc.', 'B', 0, 'C', 1);
            $pdf->Cell(30, 7, 'Total Amount', 'B', 1, 'R', 1);
            $pdf->ln(2);
            $details = $report->totalSales($request->terminalid, $request->fromdate, $request->todate, $request->type, $request->category, $request->customer);
            $permission = $report->terminalPermission($request->terminalid);
            foreach ($details as $value) {

                if ($permission[0]->fbr_sync == 1) {
                    $salesTax = $value->sales_tax_amount;
                } else {
                    $salesTax = $value->srb;
                }

                if ($value->void_receipt == 0) {
                    $totalqty = $totalqty++;
                    $totalactualamount = $totalactualamount + $value->actual_amount;
                    $totalsalestax = $totalsalestax + $salesTax;
                    $totalamount = $totalamount + $value->total_amount;
                    $totaldiscountamount = $totaldiscountamount + $value->discount_amount;
                }

                $pdf->SetFont('Arial', '', 10);

                if ($permission[0]->fbr_sync == 1) {
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->setFillColor(54, 69, 79);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->Cell(190, 6, "FBR Invoice Number : " . $value->fbrInvNumber, 0, 1, 'C', 1);
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->setFillColor(160, 160, 160);
                    $pdf->SetTextColor(0, 0, 0);
                }

                if ($value->void_receipt == 1) {
                    $pdf->setFillColor(255, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                } else {
                    $pdf->setFillColor(160, 160, 160);
                    $pdf->SetTextColor(0, 0, 0);
                }

                $pdf->Cell(20, 6, $value->id, 0, 0, 'L', 1);
                $pdf->Cell(60, 6, $value->customer, 0, 0, 'L', 1);
                $pdf->Cell(25, 6, $value->order_mode, 0, 0, 'L', 1);
                $pdf->Cell(20, 6, number_format($value->actual_amount, 2), 0, 0, 'C', 1);
                $pdf->Cell(20, 6, number_format($salesTax, 2), 0, 0, 'C', 1);
                $pdf->Cell(15, 6, number_format($value->discount_amount, 2), 0, 0, 'C', 1);
                $pdf->Cell(30, 6, number_format($value->total_amount, 2), 0, 1, 'R', 1);
                // $pdf->ln(1);

                $receiptDetails = $report->receiptDetails($value->id);

                if (!empty($receiptDetails)) {

                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->setFillColor(0, 153, 76);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->Cell(20, 7, 'No.', 'B', 0, 'C', 1);
                    $pdf->Cell(65, 7, 'Name', 'B', 0, 'L', 1);
                    $pdf->Cell(30, 7, 'Price', 'B', 0, 'C', 1);
                    $pdf->Cell(30, 7, 'Qty', 'B', 0, 'C', 1);
                    $pdf->Cell(15, 7, 'Tax', 'B', 0, 'C', 1);
                    $pdf->Cell(30, 7, 'Total Amount', 'B', 1, 'R', 1);

                    foreach ($receiptDetails as $value) {
                        // THIS CODE IS ONLY FOR SNOWHITE FOR CALCULATING SHALWAR QAMEEZ TO DOUBLE;
                        $itemQty = 0;
                        if (session('company_id') == 74) {
                            $itemQty = $itemQty + ($value->total_qty * $value->weight_qty);
                        } else {
                            $itemQty = $value->total_qty;
                        }
                        // if($value->item_code == 817947 or $value->item_code == 817992 ){
                        // $itemQty = $itemQty + ($value->total_qty * 2);
                        // }else{
                        // $itemQty = $value->total_qty;
                        // }
                        $totalItemCount = $totalItemCount + $itemQty;
                        $pdf->SetFont('Arial', '', 10);
                        $pdf->setFillColor(232, 232, 232);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(20, 7, $value->item_code, 'B', 0, 'C', 1);
                        $pdf->Cell(65, 7, $value->item_name, 'B', 0, 'L', 1);
                        $pdf->Cell(30, 7, number_format($value->item_price, 2), 'B', 0, 'C', 1);
                        $pdf->Cell(30, 7, number_format($value->total_qty, 2), 'B', 0, 'C', 1);
                        $pdf->Cell(15, 7, number_format($value->taxamount, 2), 'B', 0, 'C', 1);
                        $pdf->Cell(30, 7, number_format($value->total_amount, 2), 'B', 1, 'R', 1);
                    }
                    $pdf->ln(4);
                }
            }

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(20, 6, 'Total Items : ', 0, 0, 'L', 1);
            $pdf->Cell(60, 6, $totalItemCount, 0, 0, 'L', 1);
            $pdf->Cell(20, 6, '', 0, 0, 'L', 1);
            $pdf->Cell(20, 6, number_format($totalactualamount, 2), 0, 0, 'C', 1);
            $pdf->Cell(20, 6, number_format($totalsalestax, 2), 0, 0, 'C', 1);
            $pdf->Cell(20, 6, number_format($totaldiscountamount, 2), 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($totalamount, 2), 0, 1, 'R', 1);
        }

        //save file
        $pdf->Output('FBR_Report.pdf', 'I');
    }

    //item sale database report
    public function itemsaledatabasepdf(Request $request, Vendor $vendor, Report $report)
    {
        $company = $vendor->company(session('company_id'));
        $departments = [];
        $branchname = "";

        if ($request->branch != "all") {
            $branchname = Branch::where("branch_id", $request->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " (All Branches) ";
        }

        if (is_array($request->department)) {
            $departments = InventoryDepartment::whereIn("department_id", $request->department)->select("department_id", "department_name")->get();
        }

        if (!file_exists(asset('storage/images/company/qrcode.png'))) {
            $qrcodetext = $company[0]->name . " | " . $company[0]->ptcl_contact . " | " . $company[0]->address;
            \QrCode::size(200)
                ->format('png')
                ->generate($qrcodetext, Storage::disk('public')->put("images/company/", "qrcode.png"));
        }

        $pdf = new pdfClass();

        $pdf->AliasNbPages();
        $pdf->AddPage();


        // first row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Cell(105, 0, "Company Name:", 0, 0, 'L');
        $pdf->Cell(50, 0, "", 0, 1, 'L');

        // second row
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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Item Sale Database ' . $branchname, 'B,T', 1, 'L');
        $pdf->ln(1);

        //total variables
        $totalCount = 0;
        $totalqty = 0;
        $totalamount = 0;
        $totalcost = 0;
        $totalmargin = 0;
        $price = 0;

        $totalDeliveredOrders = 0;
        $totalDeliveredOrdersAmount = 0;
        $totalVoidOrders = 0;
        $totalVoidOrdersAmount = 0;
        $totalSalesReturnOrders = 0;
        $totalSalesReturnOrdersAmount = 0;

        if ($request->terminalid == 0) {

            $terminals = $report->getTerminals($request->branch);

            foreach ($terminals as $values) {
                $totalDeliveredOrders = 0;
                $totalDeliveredOrdersAmount = 0;
                $totalVoidOrders = 0;
                $totalVoidOrdersAmount = 0;
                $totalSalesReturnOrders = 0;
                $totalSalesReturnOrdersAmount = 0;
                $totalCount = 0;

                $pdf->SetFont('Arial', 'B', 15);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(190, 10, "Terminal Name: " . $values->terminal_name, 0, 1, 'C');
                $modes = $report->itemSalesOrderMode($request->fromdate, $request->todate, $values->terminal_id, $request->ordermode, $request->status);

                foreach ($modes as $mode) {
                    $totalCount = 0;
                    $totalqty = 0;
                    $totalamount = 0;
                    $totalcost = 0;
                    $totalmargin = 0;
                    $totalDeliveredOrders = 0;
                    $totalDeliveredOrdersAmount = 0;
                    $totalVoidOrders = 0;
                    $totalVoidOrdersAmount = 0;
                    $totalSalesReturnOrders = 0;
                    $totalSalesReturnOrdersAmount = 0;
                    //report name
                    $pdf->ln(2);
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->setFillColor(128, 128, 128);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->Cell(190, 5, $mode->ordermode, 0, 1, 'C', 1);
                    // $pdf->ln(1);
                    // TABLE HEADERS
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->setFillColor(0, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->Cell(20, 7, 'Code', 'B', 0, 'C', 1);
                    $pdf->Cell(50, 7, 'Poduct Name', 'B', 0, 'L', 1);
                    $pdf->Cell(20, 7, 'Qty', 'B', 0, 'C', 1);
                    $pdf->Cell(20, 7, 'Price', 'B', 0, 'C', 1);
                    $pdf->Cell(20, 7, 'Amount', 'B', 0, 'R', 1);
                    $pdf->Cell(15, 7, 'COGS', 'B', 0, 'R', 1);
                    $pdf->Cell(15, 7, 'Margin', 'B', 0, 'R', 1);
                    $pdf->Cell(30, 7, 'Status', 'B', 1, 'R', 1);

                    $details = $report->itemsale_details($request->fromdate, $request->todate, $values->terminal_id, $mode->order_mode_id, $request->department, $request->subdepartment, $request->ordermode, $request->status, $request->inventory);

                    if (!empty($departments)) {
                        foreach ($departments as $key => $department) {
                            $totaldepartmentCount = 0;
                            $totaldepartmentqty = 0;
                            $totaldepartmentamount = 0;
                            $totaldepartmentcost = 0;
                            $totaldepartmentmargin = 0;

                            $pdf->ln(2);
                            $pdf->SetFont('Arial', 'B', 12);
                            $pdf->setFillColor(128, 128, 128);
                            $pdf->SetTextColor(255, 255, 255);
                            $pdf->Cell(190, 5, $department->department_name, 0, 1, 'C', 1);

                            $filtered = collect($details)->filter(function ($order)  use ($department) {
                                return $order->department_id == $department->department_id;
                            })->values();

                            // $filtered = json_encode($filtered);
                            foreach ($filtered as $value) {
                                $totalCount++;
                                // THIS CODE IS ONLY FOR SNOWHITE FOR CALCULATING SHALWAR QAMEEZ TO DOUBLE;
                                if (session('company_id') == 74) {
                                    $totalqty = $totalqty + ($value->qty * $value->weight_qty);
                                } else {
                                    $totalqty = $totalqty + $value->qty;
                                }
                                $totalamount = $totalamount + $value->amount;
                                $totalcost = $totalcost + $value->cost;
                                $totalmargin = $totalmargin + ($value->amount - $value->cost);

                                $totaldepartmentCount++;
                                // THIS CODE IS ONLY FOR SNOWHITE FOR CALCULATING SHALWAR QAMEEZ TO DOUBLE;
                                if (session('company_id') == 74) {
                                    $totaldepartmentqty = $totaldepartmentqty + ($value->qty * $value->weight_qty);
                                } else {
                                    $totaldepartmentqty = $totaldepartmentqty + $value->qty;
                                }
                                $totaldepartmentamount = $totaldepartmentamount + $value->amount;
                                $totaldepartmentcost = $totaldepartmentcost + $value->cost;
                                $totaldepartmentmargin = $totaldepartmentmargin + ($value->amount - $value->cost);



                                $pdf->SetFont('Arial', '', 10);
                                if ($value->void_receipt == 1) {
                                    $pdf->setFillColor(255, 0, 0);
                                    $pdf->SetTextColor(255, 255, 255);
                                    $totalVoidOrders += $value->qty;
                                    $totalVoidOrdersAmount += $value->amount;
                                    $itemStatus = "Void";
                                } else if ($value->is_sale_return == 1) {
                                    $pdf->setFillColor(192, 64, 0);
                                    $pdf->SetTextColor(255, 255, 255);
                                    $totalSalesReturnOrders += $value->qty;
                                    $totalSalesReturnOrdersAmount += $value->amount;
                                    $itemStatus = "SR";
                                } else {
                                    $pdf->setFillColor(232, 232, 232);
                                    $pdf->SetTextColor(0, 0, 0);
                                    $totalDeliveredOrders++;
                                    $totalDeliveredOrdersAmount += $value->amount;
                                }
                                $pdf->Cell(20, 6, $value->code, 0, 0, 'L', 1);
                                $pdf->Cell(50, 6, $value->product_name, 0, 0, 'L', 1);
                                $pdf->Cell(20, 6, number_format($value->qty), 0, 0, 'C', 1);
                                $pdf->Cell(20, 6, number_format($value->price), 0, 0, 'C', 1);
                                $pdf->Cell(20, 6, number_format($value->amount), 0, 0, 'R', 1);
                                $pdf->Cell(15, 6, number_format($value->price), 0, 0, 'R', 1);
                                $pdf->Cell(15, 6, number_format($value->amount - $value->cost), 0, 0, 'R', 1);
                                $pdf->Cell(30, 6, $value->order_status_name, 0, 1, 'R', 1);
                            }
                            $pdf->setFillColor(255, 255, 255);
                            $pdf->SetTextColor(0, 0, 0);
                            $pdf->SetFont('Arial', 'B', 10);
                            $pdf->Cell(20, 7, "Total", 'B,T', 0, 'L');
                            $pdf->Cell(50, 7, "Item Count (" . $totaldepartmentCount . ")", 'B,T', 0, 'L');
                            $pdf->Cell(20, 7, number_format($totaldepartmentqty), 'B,T', 0, 'C');
                            $pdf->Cell(20, 7, '', 'B,T', 0, 'C');
                            $pdf->Cell(20, 7, number_format($totaldepartmentamount), 'B,T', 0, 'C');
                            $pdf->Cell(15, 7, number_format($totaldepartmentcost), 'B,T', 0, 'R');
                            $pdf->Cell(15, 7, number_format($totaldepartmentmargin), 'B,T', 0, 'R');
                            $pdf->Cell(30, 7, '-', 'B,T', 1, 'R');

                            $pdf->ln(2);
                        }
                    } else {
                        foreach ($details as $value) {
                            $totalCount++;
                            // THIS CODE IS ONLY FOR SNOWHITE FOR CALCULATING SHALWAR QAMEEZ TO DOUBLE;
                            if (session('company_id') == 74) {
                                $totalqty = $totalqty + ($value->qty * $value->weight_qty);
                            } else {
                                $totalqty = $totalqty + $value->qty;
                            }
                            $totalamount = $totalamount + $value->amount;
                            $totalcost = $totalcost + $value->cost;
                            $totalmargin = $totalmargin + ($value->amount - $value->cost);

                            $pdf->SetFont('Arial', '', 10);
                            if ($value->void_receipt == 1) {
                                $pdf->setFillColor(255, 0, 0);
                                $pdf->SetTextColor(255, 255, 255);
                                $totalVoidOrders += $value->qty;
                                $totalVoidOrdersAmount += $value->amount;
                                $itemStatus = "Void";
                            } else if ($value->is_sale_return == 1) {
                                $pdf->setFillColor(192, 64, 0);
                                $pdf->SetTextColor(255, 255, 255);
                                $totalSalesReturnOrders += $value->qty;
                                $totalSalesReturnOrdersAmount += $value->amount;
                                $itemStatus = "SR";
                            } else {
                                $pdf->setFillColor(232, 232, 232);
                                $pdf->SetTextColor(0, 0, 0);
                                $totalDeliveredOrders++;
                                $totalDeliveredOrdersAmount += $value->amount;
                            }
                            $pdf->Cell(20, 6, $value->code, 0, 0, 'L', 1);
                            $pdf->Cell(50, 6, $value->product_name, 0, 0, 'L', 1);
                            $pdf->Cell(20, 6, number_format($value->qty), 0, 0, 'C', 1);
                            $pdf->Cell(20, 6, number_format($value->price), 0, 0, 'C', 1);
                            $pdf->Cell(20, 6, number_format($value->amount), 0, 0, 'R', 1);
                            $pdf->Cell(15, 6, number_format($value->price), 0, 0, 'R', 1);
                            $pdf->Cell(15, 6, number_format($value->amount - $value->cost), 0, 0, 'R', 1);
                            $pdf->Cell(30, 6, $value->order_status_name, 0, 1, 'R', 1);

                            // $pdf->ln(1);
                        }
                    }

                    $pdf->ln(2);
                    $pdf->SetFont('Arial', 'B', 12);

                    $pdf->setFillColor(0, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->Cell(190, 7, 'SUMMARY', 'B', 1, 'C', 1);
                    $pdf->setFillColor(255, 255, 255);
                    $pdf->SetTextColor(0, 0, 0);

                    $pdf->setFillColor(255, 255, 255);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(20, 7, "Total", 'B,T', 0, 'L');
                    $pdf->Cell(50, 7, "Item Count (" . $totalCount . ")", 'B,T', 0, 'L');
                    $pdf->Cell(20, 7, number_format($totalqty), 'B,T', 0, 'C');
                    $pdf->Cell(20, 7, '', 'B,T', 0, 'C');
                    $pdf->Cell(20, 7, number_format($totalamount), 'B,T', 0, 'C');
                    $pdf->Cell(15, 7, number_format($totalcost), 'B,T', 0, 'R');
                    $pdf->Cell(15, 7, number_format($totalmargin), 'B,T', 0, 'R');
                    $pdf->Cell(30, 7, '-', 'B,T', 1, 'R');

                    $pdf->ln(2);
                    $pdf->SetFont('Arial', 'B', 12);

                    // $allOrdersByStatus = $report->groupByItemSaleStatus($request->fromdate, $request->todate, $request->terminalid, $mode->order_mode_id, $request->department, $request->subdepartment, $request->ordermode, $request->status);
                    // if (!empty($allOrdersByStatus)) {
                    //     $pdf->setFillColor(0, 0, 0);
                    //     $pdf->SetTextColor(255, 255, 255);
                    //     $pdf->Cell(190, 7, 'SUMMARY', 'B', 1, 'C', 1);
                    //     $pdf->setFillColor(255, 255, 255);
                    //     $pdf->SetTextColor(0, 0, 0);
                    // }

                    // $pdf->SetFont('Arial', '', 10);
                    // $pdf->setFillColor(232, 232, 232);
                    // $pdf->SetTextColor(0, 0, 0);
                    // foreach ($allOrdersByStatus as $status) {
                    //     $pdf->Cell(63, 7, $status->status, 'B,T', 0, 'C');
                    //     $pdf->Cell(63, 7, number_format($status->totalorders, 0), 'B,T', 0, 'C');
                    //     $pdf->Cell(63, 7, number_format($status->totalamount, 0), 'B,T', 1, 'C');
                    // }
                }



                $pdf->ln(10);
            }
        } else {

            $terminals = $report->get_terminals_byid($request->terminalid);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(190, 10, "Terminal Name: " . $terminals[0]->terminal_name, 0, 1, 'L');
            $modes = $report->itemSalesOrderMode($request->fromdate, $request->todate, $request->terminalid, $request->ordermode, $request->status);

            foreach ($modes as $mode) {
                $totalCount = 0;
                $totalqty = 0;
                $totalamount = 0;
                $totalcost = 0;
                $totalmargin = 0;
                $totalDeliveredOrders = 0;
                $totalDeliveredOrdersAmount = 0;
                $totalVoidOrders = 0;
                $totalVoidOrdersAmount = 0;
                $totalSalesReturnOrders = 0;
                $totalSalesReturnOrdersAmount = 0;
                //report name
                $pdf->ln(2);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->setFillColor(128, 128, 128);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(190, 5, $mode->ordermode, 0, 1, 'C', 1);
                // $pdf->ln(1);
                // TABLE HEADERS
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(20, 7, 'Code', 'B', 0, 'C', 1);
                $pdf->Cell(50, 7, 'Poduct Name', 'B', 0, 'L', 1);
                $pdf->Cell(20, 7, 'Qty', 'B', 0, 'C', 1);
                $pdf->Cell(20, 7, 'Price', 'B', 0, 'C', 1);
                $pdf->Cell(20, 7, 'Amount', 'B', 0, 'R', 1);
                $pdf->Cell(15, 7, 'COGS', 'B', 0, 'R', 1);
                $pdf->Cell(15, 7, 'Margin', 'B', 0, 'R', 1);
                $pdf->Cell(30, 7, 'Status', 'B', 1, 'R', 1);
                $details = $report->itemsale_details($request->fromdate, $request->todate, $request->terminalid, $mode->order_mode_id, $request->department, $request->subdepartment, $request->ordermode, $request->status, $request->inventory);

                if (!empty($departments)) {
                    foreach ($departments as $key => $department) {
                        $totaldepartmentCount = 0;
                        $totaldepartmentqty = 0;
                        $totaldepartmentamount = 0;
                        $totaldepartmentcost = 0;
                        $totaldepartmentmargin = 0;

                        $pdf->ln(2);
                        $pdf->SetFont('Arial', 'B', 12);
                        $pdf->setFillColor(128, 128, 128);
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->Cell(190, 5, $department->department_name, 0, 1, 'C', 1);

                        $filtered = collect($details)->filter(function ($order)  use ($department) {
                            return $order->department_id == $department->department_id;
                        })->values();
                        // $filtered = json_encode($filtered);
                        foreach ($filtered as $value) {
                            $totalCount++;
                            // THIS CODE IS ONLY FOR SNOWHITE FOR CALCULATING SHALWAR QAMEEZ TO DOUBLE;
                            if (session('company_id') == 74) {
                                $totalqty = $totalqty + ($value->qty * $value->weight_qty);
                            } else {
                                $totalqty = $totalqty + $value->qty;
                            }
                            $totalamount = $totalamount + $value->amount;
                            $totalcost = $totalcost + $value->cost;
                            $totalmargin = $totalmargin + ($value->amount - $value->cost);

                            $totaldepartmentCount++;
                            // THIS CODE IS ONLY FOR SNOWHITE FOR CALCULATING SHALWAR QAMEEZ TO DOUBLE;
                            if (session('company_id') == 74) {
                                $totaldepartmentqty = $totaldepartmentqty + ($value->qty * $value->weight_qty);
                            } else {
                                $totaldepartmentqty = $totaldepartmentqty + $value->qty;
                            }
                            $totaldepartmentamount = $totaldepartmentamount + $value->amount;
                            $totaldepartmentcost = $totaldepartmentcost + $value->cost;
                            $totaldepartmentmargin = $totaldepartmentmargin + ($value->amount - $value->cost);

                            $pdf->SetFont('Arial', '', 10);
                            if ($value->void_receipt == 1) {
                                $pdf->setFillColor(255, 0, 0);
                                $pdf->SetTextColor(255, 255, 255);
                                $totalVoidOrders += $value->qty;
                                $totalVoidOrdersAmount += $value->amount;
                                $itemStatus = "Void";
                            } else if ($value->is_sale_return == 1) {
                                $pdf->setFillColor(192, 64, 0);
                                $pdf->SetTextColor(255, 255, 255);
                                $totalSalesReturnOrders += $value->qty;
                                $totalSalesReturnOrdersAmount += $value->amount;
                                $itemStatus = "SR";
                            } else {
                                $pdf->setFillColor(232, 232, 232);
                                $pdf->SetTextColor(0, 0, 0);
                                $totalDeliveredOrders++;
                                $totalDeliveredOrdersAmount += $value->amount;
                            }
                            $pdf->Cell(20, 6, $value->code, 0, 0, 'L', 1);
                            $pdf->Cell(50, 6, $value->product_name, 0, 0, 'L', 1);
                            $pdf->Cell(20, 6, number_format($value->qty), 0, 0, 'C', 1);
                            $pdf->Cell(20, 6, number_format($value->price), 0, 0, 'C', 1);
                            $pdf->Cell(20, 6, number_format($value->amount), 0, 0, 'R', 1);
                            $pdf->Cell(15, 6, number_format($value->price), 0, 0, 'R', 1);
                            $pdf->Cell(15, 6, number_format($value->amount - $value->cost), 0, 0, 'R', 1);
                            $pdf->Cell(30, 6, $value->order_status_name, 0, 1, 'R', 1);
                        }

                        $pdf->setFillColor(255, 255, 255);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->Cell(20, 7, "Total", 'B,T', 0, 'L');
                        $pdf->Cell(50, 7, "Item Count (" . $totaldepartmentCount . ")", 'B,T', 0, 'L');
                        $pdf->Cell(20, 7, number_format($totaldepartmentqty), 'B,T', 0, 'C');
                        $pdf->Cell(20, 7, '', 'B,T', 0, 'C');
                        $pdf->Cell(20, 7, number_format($totaldepartmentamount), 'B,T', 0, 'C');
                        $pdf->Cell(15, 7, number_format($totaldepartmentcost), 'B,T', 0, 'R');
                        $pdf->Cell(15, 7, number_format($totaldepartmentmargin), 'B,T', 0, 'R');
                        $pdf->Cell(30, 7, '-', 'B,T', 1, 'R');
                    }
                } else {
                    foreach ($details as $value) {
                        $totalCount++;
                        // THIS CODE IS ONLY FOR SNOWHITE FOR CALCULATING SHALWAR QAMEEZ TO DOUBLE;
                        if (session('company_id') == 74) {
                            $totalqty = $totalqty + ($value->qty * $value->weight_qty);
                        } else {
                            $totalqty = $totalqty + $value->qty;
                        }
                        $totalamount = $totalamount + $value->amount;
                        $totalcost = $totalcost + $value->cost;
                        $totalmargin = $totalmargin + ($value->amount - $value->cost);

                        $pdf->SetFont('Arial', '', 10);
                        if ($value->void_receipt == 1) {
                            $pdf->setFillColor(255, 0, 0);
                            $pdf->SetTextColor(255, 255, 255);
                            $totalVoidOrders += $value->qty;
                            $totalVoidOrdersAmount += $value->amount;
                            $itemStatus = "Void";
                        } else if ($value->is_sale_return == 1) {
                            $pdf->setFillColor(192, 64, 0);
                            $pdf->SetTextColor(255, 255, 255);
                            $totalSalesReturnOrders += $value->qty;
                            $totalSalesReturnOrdersAmount += $value->amount;
                            $itemStatus = "SR";
                        } else {
                            $pdf->setFillColor(232, 232, 232);
                            $pdf->SetTextColor(0, 0, 0);
                            $totalDeliveredOrders++;
                            $totalDeliveredOrdersAmount += $value->amount;
                        }
                        $pdf->Cell(20, 6, $value->code, 0, 0, 'L', 1);
                        $pdf->Cell(50, 6, $value->product_name, 0, 0, 'L', 1);
                        $pdf->Cell(20, 6, number_format($value->qty), 0, 0, 'C', 1);
                        $pdf->Cell(20, 6, number_format($value->price), 0, 0, 'C', 1);
                        $pdf->Cell(20, 6, number_format($value->amount), 0, 0, 'R', 1);
                        $pdf->Cell(15, 6, number_format($value->price), 0, 0, 'R', 1);
                        $pdf->Cell(15, 6, number_format($value->amount - $value->cost), 0, 0, 'R', 1);
                        $pdf->Cell(30, 6, $value->order_status_name, 0, 1, 'R', 1);
                    }
                }

                $pdf->ln(2);
                $pdf->SetFont('Arial', 'B', 12);

                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(190, 7, 'SUMMARY', 'B', 1, 'C', 1);
                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);

                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(20, 7, "Total", 'B,T', 0, 'L');
                $pdf->Cell(50, 7, "Item Count (" . $totalCount . ")", 'B,T', 0, 'L');
                $pdf->Cell(20, 7, number_format($totalqty), 'B,T', 0, 'C');
                $pdf->Cell(20, 7, '', 'B,T', 0, 'C');
                $pdf->Cell(20, 7, number_format($totalamount), 'B,T', 0, 'C');
                $pdf->Cell(15, 7, number_format($totalcost), 'B,T', 0, 'R');
                $pdf->Cell(15, 7, number_format($totalmargin), 'B,T', 0, 'R');
                $pdf->Cell(30, 7, '-', 'B,T', 1, 'R');

                $pdf->ln(2);

                // $allOrdersByStatus = $report->groupByItemSaleStatus($request->fromdate, $request->todate, $request->terminalid, $mode->order_mode_id, $request->department, $request->subdepartment, $request->ordermode, $request->status);
                // $pdf->SetFont('Arial', '', 10);
                // $pdf->setFillColor(232, 232, 232);
                // $pdf->SetTextColor(0, 0, 0);
                // foreach ($allOrdersByStatus as $status) {
                //     $pdf->Cell(63, 7, $status->status, 'B,T', 0, 'C');
                //     $pdf->Cell(63, 7, number_format($status->totalorders, 0), 'B,T', 0, 'C');
                //     $pdf->Cell(63, 7, number_format($status->totalamount, 0), 'B,T', 1, 'C');
                // }
            }
            $pdf->ln(10);
        }

        $pdf->Output('Item_Sale_Database.pdf', 'I');
    }

    public function newitemsaledatabasepdf(Request $request, Vendor $vendor, Report $report)
    {
        $company = $vendor->company(session('company_id'));
        $departments = [];
        $branchname = "";

        if ($request->branch != "all") {
            $branchname = Branch::where("branch_id", $request->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " ( All Branches )";
        }

        if (is_array($request->department)) {
            $departments = InventoryDepartment::whereIn("department_id", $request->department)
                ->select("department_id", "department_name")
                ->get();
        }

        // if (!file_exists(asset('storage/images/company/qrcode.png'))) {
        //     $qrcodetext = $company[0]->name . " | " . $company[0]->ptcl_contact . " | " . $company[0]->address;
        //     \QrCode::size(200)
        //         ->format('png')
        //         ->generate($qrcodetext, Storage::disk('public')->put("images/company/", "qrcode.png"));
        // }

        // Initialize MPDF with RTL and Unicode support
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'jameel-noori-nastaleeq',
            'default_font_size' => 12,
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_header' => 10,
            'margin_footer' => 10,
            'direction' => 'rtl'
        ]);

        // Add custom Urdu font
        $mpdf->fontdata['jameel-noori-nastaleeq'] = [
            'R' => 'Jameel-Noori-Nastaleeq.ttf',
            'useOTL' => 0xFF,
        ];

        // Start building HTML content
        $html = '
    <html dir="ltr">
    <head>
        <style>
            body { font-family: jameel-noori-nastaleeq; }
              h1{
                    line-height: 0.6;
                }
                .text-bold{
                    font-weight: bold;
                }
                p{
                    font-size: 14px;
                    line-height: 0.9;
                    margin: 5px 0;
                }
                h2{
                    font-size: 14px;
                    line-height: 0.6;
                    color: green;
                }
                .text-center{
                    text-align: center;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                td {
                    vertical-align: top;
                    padding: 5px;
                }
                .right-align {
                    text-align: right;
                }
                .company-info {
                    width: 50%;
                }
                .qr-section {
                    width: 50%;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }

        
                .header-row td {
                    font-size: 18px;
                    font-weight: bold;
                    padding: 10px;
                    text-align: center;
                    background-color: #f8f9fa;
                    border-bottom: 2px solid #dee2e6;
                }

            
                thead th {
                    background-color: #1a4567;
                    color: white;
                    padding: 12px 8px;
                    text-align: center;
                    font-weight: bold;
                    border: 1px solid #0d2235;
                    font-size: 14px;
                }

        
                tbody td {
                    padding: 8px;
                    text-align: center;
                    border: 1px solid #dee2e6;
                    font-size: 13px;
                }

    
                tbody tr:nth-child(even) {
                    background-color: #f8f9fa;
                }
                .summary { 
                    margin-top: 20px;
                    background-color: #f9f9f9;
                    padding: 10px;
                }
          
            .void { background-color: #ffcccc; }
            .return { background-color: #ffd9cc; }
            .normal { background-color: #f2f2f2; }
        </style>
    </head>
    <body>';

        // Add company header
        // $html .= '
        // <div class="header">
        //     <img src="' . asset('storage/images/company/' . $company[0]->logo) . '" class="company-logo">
        //     <div class="company-info">
        //         <h2>' . $company[0]->name . '</h2>
        //         <p>Contact No: ' . $company[0]->ptcl_contact . '</p>
        //         <p>Address : ' . $company[0]->address . '</p>
        //     </div>
        // </div>';
        $html .= '
        <table>
            <tr>
               <td class="company-info">
                    <table style="width: auto;">
                        <tr>
                            <td>
                                <img width="100" height="100" src="' . asset('storage/images/company/' . $company[0]->logo) . '" alt="">
                            </td>
                            <td style="padding-left: 16px;">
                                <p>Company Name:</p>
                                <h4 class="text-bold">' . $company[0]->name . '</h4>
                                <p>Contact Number</p>
                                <p class="text-bold">0' . $company[0]->ptcl_contact . '</p>
                                <p>Company Address</p>
                                <p class="text-bold">' . $company[0]->address . '</p>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="qr-section" style="width: 100px; text-align: right;">
                    <img width="100" height="100" src="' . asset('storage/images/company/qrcode.png') . '" alt="" style="margin-left: auto; display: block;">
                </td>
            </tr>
        </table>';

        // Add date range
        $fromdate = date('Y-m-d', strtotime($request->fromdate));
        $todate = date('Y-m-d', strtotime($request->todate));
        $html .= '<h4 style="text-align: center;">Date: ' . $fromdate . ' From ' . $todate . ' To </h4>';
        $html .= '<h2 style="text-align: center;">Item Sale Database' . $branchname . '</h2>';

        // Process terminals and create tables
        if ($request->terminalid == 0) {
            $terminals = $report->getTerminals($request->branch);
        } else {
            $terminals = $report->get_terminals_byid($request->terminalid);
        }

        foreach ($terminals as $terminal) {
            $html .= '<h3 style="text-align: center;background-color: #1a4567;color: #FFFFFF;">Terminal: ' . $terminal->terminal_name . '</h3>';

            $modes = $report->itemSalesOrderMode(
                $request->fromdate,
                $request->todate,
                $terminal->terminal_id,
                $request->ordermode,
                $request->status
            );

            if (empty($modes)) {
                // If no modes found for this terminal
                $html .= '<div style="text-align: center; padding: 20px; background-color: #f9f9f9; border: 1px solid #ddd; margin: 10px 0;">
                    <p style="color: #666; font-size: 16px; margin: 0;">Modes are empty<p>
                    <p style="color: #999; font-size: 14px; margin: 5px 0 0 0;">No Data Found</p>
                </div>';
                continue; // Skip to next terminal
            }

            foreach ($modes as $mode) {
                $html .= '<h5 style="text-align: center;background-color: #ddd;color: #000;margin-bottom: -2px;padding: 12px 8px;">' . $mode->ordermode . '</h5>';

                $html .= '
            <table >
                <thead>
                    <tr>
                        <th>Item code</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Amount</th>
                        <th>COGS</th>
                        <th>Margin</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>';

                $details = $report->itemsale_details(
                    $request->fromdate,
                    $request->todate,
                    $terminal->terminal_id,
                    $mode->order_mode_id,
                    $request->department,
                    $request->subdepartment,
                    $request->ordermode,
                    $request->status,
                    $request->inventory
                );

                if (empty($details)) {
                    // If no details found for this mode
                    $html .= '<div style="text-align: center; padding: 15px; background-color: #f5f5f5; border: 1px solid #eee; margin-bottom: 20px;">
                        <p style="color: #666; font-size: 14px; margin: 0;">No Data Found</p>
                    </div>';
                    continue; // Skip to next mode
                }

                $totalCount = 0;
                $totalQty = 0;
                $totalAmount = 0;
                $totalCost = 0;
                $totalMargin = 0;
                if (!empty($details)) {
                    foreach ($details as $item) {
                        $rowClass = $item->void_receipt == 1 ? 'void' : ($item->is_sale_return == 1 ? 'return' : 'normal');

                        $html .= sprintf(
                            '
                        <tr class="%s">
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                        </tr>',
                            $rowClass,
                            $item->code,
                            $item->product_name,
                            number_format($item->qty),
                            number_format($item->price),
                            number_format($item->amount),
                            number_format($item->cost),
                            number_format($item->amount - $item->cost),
                            $item->order_status_name
                        );
                        if($item->void_receipt != 1){
                            $totalCount++;
                            $totalQty += $item->qty;
                            $totalAmount += $item->amount;
                            $totalCost += $item->cost;
                            $totalMargin += ($item->amount - $item->cost);
                        }
                    }
                } else {
                    $html .= '<tr><td colspan="8" style="text-align: center;">No data found</td></tr>';
                }

                // Add totals row
                $html .= sprintf(
                    '
                <tr style="font-weight: bold;background-color: #00000;color: #FFFFFF;">
                    <td colspan="2" style="color: #FFFFFF;font-weight: bold;">Total Items: %s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">-</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">-</td>
                </tr>',
                    $totalCount,
                    number_format($totalQty),
                    number_format($totalAmount),
                    number_format($totalCost),
                    number_format($totalMargin)
                );

                $html .= '</tbody></table>';
            }
        }

        $html .= '</body></html>';

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        // Output PDF
        $mpdf->Output('Item_Sale_Database_Urdu.pdf', 'I');
    }

    //Sale Return  report
    public function salesreturnpdf(Request $request, Vendor $vendor, Report $report)
    {
        $company = $vendor->company(session('company_id'));

        $branchname = "";

        if ($request->branch != "all") {
            $branchname = Branch::where("branch_id", $request->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " (All Branches) ";
        }


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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Sales Return Report ' . $branchname, 'B,T', 1, 'L');
        $pdf->ln(1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(40, 7, 'Receipt No', 'B', 0, 'L', 1);
        $pdf->Cell(50, 7, 'Poduct Name', 'B', 0, 'L', 1);
        $pdf->Cell(15, 7, 'Qty.', 'B', 0, 'C', 1);
        $pdf->Cell(25, 7, 'Amount', 'B', 0, 'C', 1);
        $pdf->Cell(35, 7, 'Date', 'B', 0, 'C', 1);
        $pdf->Cell(25, 7, 'Time', 'B', 1, 'C', 1);


        //total variables
        $totalqty = 0;
        $totalamount = 0;
        $totalcost = 0;
        $totalmargin = 0;
        if ($request->terminalid == 0) {
            $terminals = $report->getTerminals($request->branch);

            foreach ($terminals as $values) {
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(190, 10, "Terminal Name: " . $values->terminal_name, 0, 1, 'L');
                $details = $report->salereturn_details($request->fromdate, $request->todate, $values->terminal_id, $request->code);
                foreach ($details as $value) {
                    $totalqty = $totalqty + $value->qty;
                    $totalamount = $totalamount + $value->amount;

                    $pdf->SetFont('Arial', '', 10);
                    $pdf->setFillColor(232, 232, 232);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(40, 6, $value->receipt_no, 0, 0, 'L', 1);
                    $pdf->Cell(50, 6, $value->product_name, 0, 0, 'L', 1);
                    $pdf->Cell(15, 6, number_format($value->qty, 2), 0, 0, 'C', 1);
                    $pdf->Cell(25, 6, number_format($value->amount, 2), 0, 0, 'C', 1);
                    $pdf->Cell(35, 6, date("d F Y", strtotime($value->date)), 0, 0, 'C', 1);
                    $pdf->Cell(25, 6, date("h:i a", strtotime($value->time)), 0, 1, 'C', 1);

                    $pdf->ln(1);
                }
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(90, 7, "Total", 'B,T', 0, 'L');
                $pdf->Cell(15, 7, number_format($totalqty, 2), 'B,T', 0, 'C');
                $pdf->Cell(25, 7, number_format($totalamount, 2), 'B,T', 0, 'L');
                $pdf->Cell(25, 7, "", 'B,T', 0, 'R');
                $pdf->Cell(45, 7, "", 'B,T', 1, 'R');
            }
        } else {
            $terminals = $report->get_terminals_byid($request->terminalid);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(190, 10, "Terminal Name: " . $terminals[0]->terminal_name, 0, 1, 'L');
            $details = $report->salereturn_details($request->fromdate, $request->todate, $request->terminalid, $request->code);
            foreach ($details as $value) {
                $totalqty = $totalqty + $value->qty;
                $totalamount = $totalamount + $value->amount;

                $pdf->SetFont('Arial', '', 10);
                $pdf->setFillColor(232, 232, 232);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(40, 6, $value->receipt_no, 0, 0, 'L', 1);
                $pdf->Cell(50, 6, $value->product_name, 0, 0, 'L', 1);
                $pdf->Cell(15, 6, number_format($value->qty, 2), 0, 0, 'C', 1);
                $pdf->Cell(25, 6, number_format($value->amount, 2), 0, 0, 'C', 1);
                $pdf->Cell(35, 6, date("d F Y", strtotime($value->date)), 0, 0, 'C', 1);
                $pdf->Cell(25, 6, date("h:i a", strtotime($value->time)), 0, 1, 'C', 1);
                $pdf->ln(1);
            }
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(90, 7, "Total", 'B,T', 0, 'L');
            $pdf->Cell(15, 7, number_format($totalqty, 2), 'B,T', 0, 'C');
            $pdf->Cell(25, 7, number_format($totalamount, 2), 'B,T', 0, 'L');
            $pdf->Cell(25, 7, "", 'B,T', 0, 'R');
            $pdf->Cell(45, 7, "", 'B,T', 1, 'R');
        }

        //save file
        $pdf->Output('Sales Return.pdf', 'I');
    }

    public function orderBookingReport(Request $request, Vendor $vendor, Report $report)
    {


        $company = $vendor->company(session('company_id'));

        $branchname = "";

        if ($request->branch != "all") {
            $branchname = Branch::where("branch_id", $request->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " (All Branches) ";
        }

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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Order Booking Report ' . $branchname, 'B,T', 1, 'L');
        $pdf->ln(1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(40, 7, 'Receipt No', 'B', 0, 'L', 1);
        $pdf->Cell(50, 7, 'Customer Name', 'B', 0, 'L', 1);
        $pdf->Cell(20, 7, 'Total.', 'B', 0, 'L', 1);
        $pdf->Cell(20, 7, 'Receive', 'B', 0, 'L', 1);
        $pdf->Cell(20, 7, 'Balance', 'B', 0, 'L', 1);
        $pdf->Cell(40, 7, 'Payment Mehthod', 'B', 1, 'C', 1);


        //total variables
        $totalamount = 0;
        $totalreceiveamount = 0;
        $totalbalanceamount = 0;

        $orders = $report->orderBookingQuery($request->fromdate, $request->todate, $request->paymentmethod, $request->branch, $request->mode);

        foreach ($orders as $values) {
            $received = $values->receive_amount + $values->received;
            $balance = $values->total_amount - $received;


            if ($request->mode != "" && $request->mode == "balances") {
                if ($balance > 0) {
                    $totalamount += $values->total_amount;
                    $totalreceiveamount += $received;
                    $totalbalanceamount += $balance;

                    $pdf->SetFont('Arial', '', 10);
                    $pdf->setFillColor(232, 232, 232);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(40, 6, $values->receipt_no, 0, 0, 'L', 1);
                    $pdf->Cell(50, 6, $values->name, 0, 0, 'L', 1);
                    $pdf->Cell(20, 6, number_format($values->total_amount, 0), 0, 0, 'L', 1);
                    $pdf->Cell(20, 6, number_format($received, 0), 0, 0, 'L', 1);
                    $pdf->Cell(20, 6, number_format($balance, 0), 0, 0, 'L', 1);
                    $pdf->Cell(40, 6, $values->payment_mode, 0, 1, 'C', 1);
                    $pdf->ln(1);
                }
            } else {
                $totalamount += $values->total_amount;
                $totalreceiveamount += $received;
                $totalbalanceamount += $balance;

                $pdf->SetFont('Arial', '', 10);
                $pdf->setFillColor(232, 232, 232);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(40, 6, $values->receipt_no, 0, 0, 'L', 1);
                $pdf->Cell(50, 6, $values->name, 0, 0, 'L', 1);
                $pdf->Cell(20, 6, number_format($values->total_amount, 0), 0, 0, 'L', 1);
                $pdf->Cell(20, 6, number_format($received, 0), 0, 0, 'L', 1);
                $pdf->Cell(20, 6, number_format($balance, 0), 0, 0, 'L', 1);
                $pdf->Cell(40, 6, $values->payment_mode, 0, 1, 'C', 1);
                $pdf->ln(1);
            }
        }
        $pdf->ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(63, 7, 'Total Amount', 'B', 0, 'C', 1);
        $pdf->Cell(63, 7, 'Receive Amount.', 'B', 0, 'C', 1);
        $pdf->Cell(63, 7, 'Balance Amount', 'B', 1, 'C', 1);

        $pdf->SetFont('Arial', '', 10);
        $pdf->setFillColor(232, 232, 232);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(63, 7, number_format($totalamount, 0), 'B,T', 0, 'C');
        $pdf->Cell(63, 7, number_format($totalreceiveamount, 0), 'B,T', 0, 'C');
        $pdf->Cell(63, 7, number_format($totalbalanceamount, 0), 'B,T', 1, 'C');
        //save file
        $pdf->Output('Order Booking Report.pdf', 'I');
    }

    //inventory physical sheet
    public function inventoryReportPhysical(Request $request, Vendor $vendor, Report $report)
    {


        $company = $vendor->company(session('company_id'));


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
        $pdf->Cell(190, 10, 'Physical Inventory Worksheet', 'B,T', 1, 'L');
        $pdf->ln(1);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(80, 7, 'Inventory', 'B', 0, 'L', 1);
        $pdf->Cell(40, 7, 'Pref Vendor', 'B', 0, 'L', 1);
        $pdf->Cell(25, 7, 'On Hand', 'B', 0, 'L', 1);
        $pdf->Cell(15, 7, 'U/M', 'B', 0, 'L', 1);
        $pdf->Cell(30, 7, 'Physical Count', 'B', 1, 'C', 1);

        $inventory = $report->physical_inventory($request->departid);
        if (!empty($inventory)) {
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            if ($request->departid == 0) {
                $pdf->Cell(190, 8, "All Department", 0, 1, 'L', 1);
            } else {
                $pdf->Cell(190, 8, "Department Name: " . $inventory[0]->department_name, 0, 1, 'L', 1);
            }

            $pdf->SetFont('Arial', '', 10);

            foreach ($inventory as $value) {
                $pdf->Cell(80, 8, $value->product_name, 0, 0, 'L', 1);
                $pdf->Cell(40, 8, $value->vendor_name, 0, 0, 'L', 1);
                $pdf->Cell(25, 8, number_format($value->stock, 2), 0, 0, 'L', 1);
                $pdf->Cell(15, 8, $value->uom, 0, 0, 'L', 1);
                $pdf->Cell(30, 8, '________________', 0, 1, 'C', 1);
            }
        } else {
            $pdf->SetFont('Arial', '', 12);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(190, 10, "No data found", 0, 1, 'C', 1);
        }
        //save file
        $pdf->Output('Physical_Inventory_Sheet.pdf', 'I');
    }

    //inventory stock Adjustment
    public function stockAdjustmentReport(Request $request, Vendor $vendor, Report $report)
    {

        $company = $vendor->company(session('company_id'));

        if ($request->branch != "all") {
            $branchname = DB::table("branch")->where("branch_id", $request->branch)->get();
            $branchname = $branchname[0]->branch_name;
        } else {
            $branchname = "All Branches";
        }


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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Stock Adjustment ' . $branchname, 'B,T', 1, 'L');
        $pdf->ln(1);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(20, 7, 'Date', 'B', 0, 'L', 1);
        $pdf->Cell(20, 7, 'Voucher', 'B', 0, 'L', 1);
        $pdf->Cell(20, 7, 'Code', 'B', 0, 'L', 1);
        $pdf->Cell(50, 7, 'Inventory', 'B', 0, 'L', 1);
        // $pdf->Cell(30,7,'Pref Vendor','B',0,'L',1);
        $pdf->Cell(15, 7, 'U/M', 'B', 0, 'L', 1);
        $pdf->Cell(15, 7, 'Qty.', 'B', 0, 'L', 1);
        // $pdf->Cell(20,7,'GRN','B',0,'L',1);
        $pdf->Cell(50, 7, 'Narration', 'B', 1, 'L', 1);

        $inventory = $report->stockadjustment($request->fromdate, $request->todate, $request->branch);

        $pdf->SetFont('Arial', '', 10);
        $pdf->setFillColor(255, 255, 255);

        foreach ($inventory as $value) {
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(20, 6, date("d-m-Y", strtotime($value->date)), 0, 0, 'L', 1);
            $pdf->Cell(20, 6, $value->grn_id, 0, 0, 'L', 1);
            $pdf->Cell(20, 6, $value->item_code, 0, 0, 'L', 1);
            $pdf->Cell(50, 6, $value->product_name, 0, 0, 'L', 1);
            // $pdf->Cell(30,6,$value->vendor_name,0,0,'L',1);
            $pdf->Cell(15, 6, $value->name, 0, 0, 'L', 1);
            if ($value->adjustment_mode  == "0") {
                $pdf->SetTextColor(256, 0, 0);
                $pdf->Cell(15, 6, number_format($value->qty, 2), 0, 0, 'L', 1);
            } else {
                $pdf->SetTextColor(0, 128, 0);
                $pdf->Cell(15, 6, number_format($value->qty, 2), 0, 0, 'L', 1);
            }
            $pdf->SetTextColor(0, 0, 0);
            // $pdf->Cell(20,6,$value->grn_id,0,0,'L',1);
            $pdf->Cell(50, 6, $value->narration, 0, 1, 'L', 1);
        }
        //save file
        $pdf->Output('Stock_Adjustment.pdf', 'I');
    }

    public function generatedSystematicReport(Request $request)
    {
        $this->filesArray = [];
        $totalReports = DB::table("fbr_details")
            ->join("branch", "branch.branch_id", "=", "fbr_details.branch_id")
            ->join("company", "company.company_id", "=", "branch.company_id")
            ->select("branch.branch_id", "branch.branch_email", "branch.branch_name", "company.company_id", "company.name as company_name", "company.ptcl_contact", "company.address", "company.logo")
            ->where("fbr_details.status", 1)->get();

        foreach ($totalReports as $report) {
            $this->savefbrReport($report, "2024-09-01", "2024-09-30");
        }
        // echo "Email Sending";
        // $this->sendEmail("2024-09-01", $report, "FBR Report");
        return 1;
    }

    public function savefbrReport($report, $from, $to)
    {

        $reportmodel = new report();

        if (!file_exists(asset('storage/images/company/qrcode.png'))) {
            $qrcodetext = $report->company_name . " | " . $report->ptcl_contact . " | " . $report->address;
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
        $pdf->Image(asset('storage/images/company/' . $report->logo), 12, 10, -200);
        $pdf->Cell(105, 12, "FBR REPORT", 0, 0, 'L');
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
        $pdf->Cell(105, -15, $report->ptcl_contact, 0, 0, 'L');
        $pdf->Cell(50, -15, "", 0, 1, 'L');

        //fifth row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 28, '', 0, 0);
        $pdf->Cell(105, 28, "Company Address:", 0, 0, 'L');
        $pdf->Cell(50, 28, "", 0, 1, 'L');

        //sixth row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(35, -18, '', 0, 0);
        $pdf->Cell(105, -18, $report->address, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, -18, "Generate Date:  " . date('Y-m-d'), 0, 1, 'R');

        //filter section
        $fromdate = date('F-d-Y', strtotime($from));
        $todate = date('F-d-Y', strtotime($to));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'FBR Report', 'B,T', 1, 'L');
        $pdf->ln(1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(10, 7, 'S.No', 'B', 0, 'L', 1);
        $pdf->Cell(30, 7, 'Sales ID', 'B', 0, 'C', 1);
        $pdf->Cell(45, 7, 'FBR Inv Number', 'B', 0, 'L', 1);
        $pdf->Cell(25, 7, 'Date', 'B', 0, 'C', 1);
        $pdf->Cell(25, 7, 'Sales', 'B', 0, 'C', 1);
        $pdf->Cell(20, 7, 'S.Tax', 'B', 0, 'C', 1);
        $pdf->Cell(35, 7, 'Total Amount', 'B', 1, 'C', 1);

        //total variables
        $totalqty = 0;
        $totalactualamount = 0;
        $totalsalestax = 0;
        $totalamount = 0;
        $price = 0;


        $terminals = $reportmodel->get_terminals_by_branch($report->branch_id);
        foreach ($terminals as  $values) {
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(190, 10, "Terminal Name: " . $values->terminal_name, 0, 1, 'L');
            $details = $reportmodel->sales($values->terminal_id, $from, $to);
            foreach ($details as $key => $value) {
                $actualAmount = 0;
                $salesTaxAmount = 0;
                if ($value->actual_amount == 0) {
                    $actualAmount = $value->total_amount - $value->sales_tax_amount;
                } else {
                    $actualAmount = $value->actual_amount;
                }

                $totalqty = $totalqty++;
                $totalactualamount = $totalactualamount + $actualAmount;
                $totalsalestax = $totalsalestax + $value->sales_tax_amount;
                $totalamount = $totalamount + $value->total_amount;

                $pdf->SetFont('Arial', '', 10);
                $pdf->setFillColor(232, 232, 232);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(10, 6, ++$key, 0, 0, 'L', 1);
                $pdf->Cell(30, 6, $value->id, 0, 0, 'C', 1);
                $pdf->Cell(45, 6, $value->fbrInvNumber, 0, 0, 'L', 1);
                $pdf->Cell(25, 6, date("d M Y", strtotime($value->date)), 0, 0, 'C', 1);
                $pdf->Cell(25, 6, number_format($actualAmount, 2), 0, 0, 'C', 1);
                $pdf->Cell(20, 6, number_format($value->sales_tax_amount, 2), 0, 0, 'C', 1);
                $pdf->Cell(35, 6, number_format($value->total_amount, 2), 0, 1, 'C', 1);
                $pdf->ln(1);
            }
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(55, 7, "", 'B,T', 0, 'L');
            $pdf->Cell(20, 7, "", 'B,T', 0, 'C');
            $pdf->Cell(35, 7, '', 'B,T', 0, 'C');
            $pdf->Cell(25, 7, number_format($totalactualamount, 2), 'B,T', 0, 'C');
            $pdf->Cell(20, 7, number_format($totalsalestax, 2), 'B,T', 0, 'C');
            $pdf->Cell(35, 7, number_format($totalamount, 2), 'B,T', 1, 'C');
        }
        $fileName = 'FBR_REPORT_' . date("M", strtotime($from)) . "_" . $report->company_name . '.pdf';
        $filePath = storage_path('app/public/pdfs/') . $fileName;
        array_push($this->filesArray, storage_path('app/public/pdfs/' . $fileName));
        // //save file
        $pdf->Output($filePath, 'F');
        $this->sendSingleEmail("2024-09-01", $report, "FBR Report", $filePath);
    }

    public function sendSingleEmail($from, $report, $reportname, $file)
    {
        $data["email"] =  $report->branch_email;
        $data["title"] =  $reportname;
        $data["body"]  =  $report;
        $data["from"]  =  $from;

        Mail::send('emails.automaticemail', $data, function ($message) use ($data, $file) {
            $message->to($data["email"], "Sabify")
                ->cc(['faizanakramkhanfaizan@gmail.com'])
                // ->cc(['adil.khan@sabsons.com.pk','faizan.akram@sabsons.com.pk'])
                ->subject($data["title"]);
            $message->attach($file);
        });
    }

    public function sendEmail($from, $report, $reportname)
    {
        $data["email"] =  "faizanakramkhanfaizan@gmail.com";
        $data["title"] =  $reportname;
        $data["body"]  =  $report;
        $data["from"]  =  $from;

        // $files = [
        //     asset('assets/pdf/' . $from . '_' . trim($report->branch_id) . '_FBR_Report.pdf'),
        // ];
        $files = $this->filesArray;

        // Mail::send('emails.automaticemail', $data, function ($message) use ($data, $files) {
        //     $message->to($data["email"], "Sabify")
        //         ->cc(['hmadilkhan@gmail.com'])
        //         // ->cc(['adil.khan@sabsons.com.pk','faizan.akram@sabsons.com.pk'])
        //         ->subject($data["title"]);

        //     foreach ($files as $file) {
        //         $message->attach($file);
        //     }
        // });
    }

    public function testDeclarationEmail(Request $request, dashboard $dash, userDetails $users)
    {
        $date = date("Y-m-d", strtotime("-1 day"));
        $terminalId = 250;
        $terminals = DB::select("SELECT d.company_id,d.name as company,d.logo,c.branch_id,c.branch_name as branch, b.terminal_name as terminal, a.permission_id,a.terminal_id FROM users_sales_permission a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id INNER JOIN branch c on c.branch_id = b.branch_id INNER JOIN company d on d.company_id = c.company_id where a.Email_Reports = 1 and b.status_id = 1 and a.terminal_id = ?", [$terminalId]);
        foreach ($terminals as $key => $terminal) {
            $emails  = DB::table("branch_emails")->where("branch_id", $terminal->branch_id)->where("status", 1)->pluck("email");
            if (!empty($emails)) {
                // $emails = implode(",", $emails->toArray());

                // return implode(",",$emails->toArray());
                $settings = DB::table("settings")->where("company_id", $terminal->company_id)->first();
                $settings = !empty($settings) ? json_decode($settings->data) : '';
                $currency = !empty($settings) ? $settings->currency : 'Rs.';
                $opening = SalesOpening::where("terminal_id", $terminal->terminal_id)->where("date", $date)->where("status", 2)->first();
                $companyLogo = "https://retail.sabsoft.com.pk/storage/images/company/" . $terminal->logo;
                if (!empty($opening)) {
                    // if (count($emails) == 1) {
                    //     $emails = $emails[0];
                    // }
                    // return $emails;
                    $permissions = $users->getPermission($terminal->terminal_id);
                    $terminal_name = $users->getTerminalName($terminal->terminal_id);
                    $heads = $dash->getheadsDetailsFromOpeningIdForClosing($opening->opening_id);
                    if (!empty($heads)) {
                        $data = [];
                        $data["permissions"] =  $permissions;
                        $data["terminal"] =  $terminal_name;
                        $data["heads"]  =  $heads;

                        $branchName = $terminal_name[0]->branch_name;
                        $subject = "Sales Declaration Email of " . $terminal_name[0]->branch_name . " (" . $terminal_name[0]->terminal_name . ") ";
                        $declarationNo =  $heads[0]->opening_id;

                        $this->generateCompleteReportForEmail($terminal->company_id, $terminal->branch_id, $terminal->terminal_id, $opening->opening_id);
                        // print($emails);
                        $emails = ["hmadilkhan@gmail.com"];
                        //->cc(["humayunshamimbarry@gmail.com"])
                        Mail::to($emails)->send(new DeclarationEmail($branchName, $subject, $declarationNo, $data, $currency, $date, $companyLogo));
                    } // Details not found
                } // Opening Id not found
            } // Email Not found bracket
        } //foreach loop end

        // return $data;

        // return view("emails.declartion_email",[
        //     'branchName' => "Snowhite Gymkhana",
        //     'declaration' => 1234567,
        //     'salesData' => $data,
        // ]);
        // Mail::to('hmadilkhan@gmail.com')->send(new DeclarationEmail( $branchName,$subject,$declarationNo,$data));
    }

    public function SendDeclarationEmail($openingId)
    {
        $users = new userDetails();
        $dash = new dashboard();
        $opening = SalesOpening::where("opening_id", $openingId)->where("status", 2)->first();
        $date = $opening->date;
        $terminals = DB::select("SELECT d.company_id,d.name as company,d.logo,c.branch_id,c.branch_name as branch, b.terminal_name as terminal, a.permission_id,a.terminal_id FROM users_sales_permission a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id INNER JOIN branch c on c.branch_id = b.branch_id INNER JOIN company d on d.company_id = c.company_id where a.Email_Reports = 1 and b.status_id = 1 and b.terminal_id = ?", [$opening->terminal_id]);

        foreach ($terminals as $key => $terminal) {
            $emails  = DB::table("branch_emails")->where("branch_id", $terminal->branch_id)->where("status", 1)->pluck("email");
            if (!empty($emails)) {
                // $emails = implode(",", $emails->toArray());

                // return implode(",",$emails->toArray());
                $settings = DB::table("settings")->where("company_id", $terminal->company_id)->first();
                $settings = !empty($settings) ? json_decode($settings->data) : '';
                $currency = !empty($settings) ? $settings->currency : 'Rs.';
                $companyLogo = "https://retail.sabsoft.com.pk/storage/images/company/" . $terminal->logo;
                if (!empty($opening)) {
                    // if (count($emails) == 1) {
                    //     $emails = $emails[0];
                    // }
                    // return $emails;
                    $permissions = $users->getPermission($terminal->terminal_id);
                    $terminal_name = $users->getTerminalName($terminal->terminal_id);
                    $heads = $dash->getheadsDetailsFromOpeningIdForClosing($opening->opening_id);
                    if (!empty($heads)) {
                        $data = [];
                        $data["permissions"] =  $permissions;
                        $data["terminal"] =  $terminal_name;
                        $data["heads"]  =  $heads;

                        $branchName = $terminal_name[0]->branch_name;
                        $subject = "Sales Declaration Email of " . $terminal_name[0]->branch_name . " (" . $terminal_name[0]->terminal_name . ") ";
                        $declarationNo =  $heads[0]->opening_id;

                        $this->generateCompleteReportForEmail($terminal->company_id, $terminal->branch_id, $terminal->terminal_id, $opening->opening_id);
                        // print($emails);
                        // $emails = ["hmadilkhan@gmail.com"]; 
                        //->cc(["humayunshamimbarry@gmail.com"])
                        Mail::to($emails)->cc(["humayunshamimbarry@gmail.com"])->send(new DeclarationEmail($branchName, $subject, $declarationNo, $data, $currency, $date, $companyLogo));
                    } // Details not found
                } // Opening Id not found
            } // Email Not found bracket
            //
        }
        return 1;
    }

    public function generateCompleteReportForEmail($company, $branch, $terminal, $opening)
    {
        $users = new userDetails();
        $dash = new dashboard();
        $report = new report();
        $company = Company::findOrFail($company);
        $branch = Branch::findOrFail($branch);
        $permissions = $users->getPermission($terminal);
        $terminal_name = $users->getTerminalName($terminal);
        $heads = $dash->getheadsDetailsFromOpeningIdForClosing($opening);
        $CashInHand = "";
        $declarationNo =  $heads[0]->opening_id  ?? 0;
        $voidReceipts = $heads[0]->VoidReceiptsCash + $heads[0]->VoidReceiptsCard;
        $salesReturnCount = DB::table("sales_return")->where("opening_id", $opening)->count();

        $positive =
            ($heads[0]->bal ?? 0) +
            ($heads[0]->order_delivered_cash ?? 0) +
            ($heads[0]->Cash ?? 0) +
            ($heads[0]->adv_booking_cash ?? 0) +
            ($heads[0]->cashIn ?? 0);
        $negative =
            ($heads[0]->Discount ?? 0) +
            ($heads[0]->SalesReturn ?? 0) +
            ($heads[0]->VoidReceiptsCash ?? 0) +
            ($heads[0]->cashOut ?? 0);
        $CashInHand =
            $positive -
            $negative +
            ($heads[0]->CardCustomerDiscount ?? 0) +
            ($heads[0]->Delivery ?? 0);
        if (isset($permissions[0]->expenses) && $permissions[0]->expenses == 1) {
            $CashInHand -= $heads[0]->expenses;
        }
        if (session('company_id') == 102) {
            $CashInHand -= $heads[0]->bal ?? 0;
        }
        $CashInHand = round($CashInHand);
        $closingBalance = round($heads[0]->closingBal ?? 0);

        $pdf = new Fpdf('P', 'mm', array(80, 200));


        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetMargins(1, 0, 0, 1);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTitle("Declaration Details");

        $pdf->Image('https://retail.sabsoft.com.pk/storage/images/company/' . $company->logo, 28, 4, -200);
        $pdf->ln(23);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(80, 0, $company->name . " (" . $branch->branch_name . ") ", 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->ln(4);
        $pdf->Cell(80, 0, $terminal_name[0]->terminal_name, 0, 1, 'C');
        $pdf->SetFont('Arial', '', 7);
        $pdf->Multicell(80, 7, $branch->branch_address, 0, 'C', 0);
        $pdf->Cell(80, 1, $branch->branch_ptcl . " | " . $branch->branch_mobile, 0, 1, 'C');
        $pdf->ln(1);


        $pdf->ln(2);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(75, 6, 'SALES DECLARATION', 0, 0, 'C', 1);
        $pdf->ln(6);

        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);

        // HEAD
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(35, 4, "Opening DateTime  ", 0, 0, 'L');
        // CENTER SPACE
        $pdf->Cell(5, 4, ":", 0, 0, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(35, 4, date('d M Y', strtotime($heads[0]->date)) . ' ' . date('H:i a', strtotime($heads[0]->time)) ?? 0, 0, 1, 'R');

        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(35, 4, "Closing DateTime  ", 0, 0, 'L');
        // CENTER SPACE
        $pdf->Cell(5, 4, ":", 0, 0, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(35, 4, date('d M Y', strtotime($heads[0]->closingDate)) . ' ' . date('H:i a', strtotime($heads[0]->closingTime)) ?? 0, 0, 1, 'R');

        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(15, 4, "Branch  ", 'T', 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(20, 4, $terminal_name[0]->branch_name, 'T', 0, 'R');
        // CENTER SPACE
        $pdf->Cell(5, 4, "", 'T', 0, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(15, 4, "Terminal  ", 'T', 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(20, 4, $terminal_name[0]->terminal_name, 'T', 1, 'R');

        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(20, 4, "Declaration No.", 'B', 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(15, 4, $declarationNo, 'B', 0, 'R');
        // CENTER SPACE
        $pdf->Cell(5, 4, "", 'B', 0, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(20, 4, "Closing Balance  ", 'B', 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(15, 4,  $closingBalance, 'B', 1, 'R');

        $pdf->ln(2);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(75, 6, 'TRANSACTION DETAILS', 0, 0, 'C', 1);
        $pdf->ln(6);

        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);

        if ($permissions[0]->ob == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Opening Balance", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->bal, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->cash_sale == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Cash Sales", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->Cash, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->customer_credit_sale == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Credit Card Sales", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->CreditCard, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->wallets_sales == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Wallet Sales", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->WalletSales, 0) ?? 0, 0, 1, 'R', 1);
        }

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(38, 6, "Total Sales", 0, 0, 'L', 1);
        $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
        $pdf->Cell(30, 6, number_format($heads[0]->TotalSales + $heads[0]->credit_card_transaction, 0) ?? 0, 0, 1, 'R', 1);

        if ($permissions[0]->order_booking == 1) {

            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Adv Booking (Cash)", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->adv_booking_cash, 0) ?? 0, 0, 1, 'R', 1);

            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Adv Booking (Card)", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->adv_booking_card, 0) ?? 0, 0, 1, 'R', 1);

            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Order Delivered (Cash)", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->order_delivered_cash, 0) ?? 0, 0, 1, 'R', 1);

            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Order Delivered (Card)", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->order_delivered_card, 0) ?? 0, 0, 1, 'R', 1);
        }

        if ($permissions[0]->sale_return == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Sale Return", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->SalesReturn, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->void_receipt == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Void Receipts", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($voidReceipts, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->discount == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Discount", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->Discount, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->cash_in == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Cash In", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->cashIn, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->cash_out == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Cash Out", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->cashOut, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->expenses == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Expense", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->expenses, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->fbr_sync == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "FBR (TAX)", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->fbr, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->srb_sync == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "SRB (TAX)", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->srb, 0) ?? 0, 0, 1, 'R', 1);
        }

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(38, 6, "Cash In Hand", 0, 0, 'L', 1);
        $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
        $pdf->Cell(30, 6, number_format($CashInHand, 0) ?? 0, 0, 1, 'R', 1);


        if ($permissions[0]->cb == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Closing Balance", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($closingBalance, 0) ?? 0, 0, 1, 'R', 1);
        }

        $status = "";
        if ($closingBalance > $CashInHand) {
            $pdf->SetTextColor(255, 0, 0);
            $status = '(' . ($closingBalance - $CashInHand) . ' Amount Excess)';
        } else if ($closingBalance < $CashInHand) {
            $pdf->SetTextColor(255, 0, 0);
            $status =  '(' . ($closingBalance - $CashInHand) . ' Amount Short)';
        } else if ($closingBalance == $CashInHand) {
            $pdf->SetTextColor(34, 139, 34);
        }

        if ($permissions[0]->cb == 1 && $status != "") {
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(38, 6, "", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, $status, 0, 1, 'R', 1);
        }


        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->ln(6);

        $pdf->AddPage();

        if ($permissions[0]->isdb == 1) {

            $pdf->ln(6);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(78, 6, 'ITEM SALES DETAILS', 0, 0, 'C', 1);
            $pdf->ln(6);

            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);

            $pdf->setFillColor(233, 233, 233);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(40, 7, 'Product', 0, 0, 'L', 1);
            $pdf->Cell(13, 7, 'Price', 0, 0, 'L', 1);
            $pdf->Cell(11, 7, 'Qty', 0, 0, 'C', 1);
            $pdf->Cell(14, 7, 'Amount', 0, 1, 'C', 1);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);

            $items = $report->itemsalesdatabaseforpdf($opening);

            $totalPrice = 0;
            $totalQty = 0;
            $totalWeightQty = 0;
            $totalAmount = 0;
            foreach ($items as $key => $item) {
                $totalPrice += $item->price;
                $totalQty += $item->qty;
                $totalWeightQty += ($item->qty * $item->weight_qty);
                $totalAmount += $item->amount;

                $pdf->Cell(78, 7, "(" . $item->item_code . ") " . $item->product_name, 0, 1, 'L', 1);
                $pdf->Cell(40, 7, number_format($item->price, 0), 0, 0, 'L', 1);
                $pdf->Cell(13, 7, $item->qty, 0, 0, 'L', 1);
                $pdf->Cell(11, 7, $item->qty * $item->weight_qty, 0, 0, 'C', 1);
                $pdf->Cell(14, 7, number_format($item->amount, 0), 0, 1, 'C', 1);
            }

            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, 7, number_format($totalPrice, 0), 0, 0, 'L', 1);
            $pdf->Cell(13, 7, number_format($totalQty, 0), 0, 0, 'L', 1);
            $pdf->Cell(11, 7, number_format($totalWeightQty, 0), 0, 0, 'C', 1);
            $pdf->Cell(14, 7, number_format($totalAmount, 0), 0, 1, 'C', 1);
            $pdf->ln(6);

            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);

            $pdf->AddPage();
        }

        // BOOKING DELIVERY REPORT
        if ($permissions[0]->delivery_report == 1) {
            $pdf->ln(6);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(78, 6, 'BOOKING DELIVERY REPORT', 0, 0, 'C', 1);
            $pdf->ln(6);

            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);

            $records = $report->bookingDeliveryEmailReport($opening, $terminal);

            $totalCount = 0;
            $totalActualAmount = 0;
            $totalTaxAmount = 0;
            $totalDiscountAmount = 0;
            $totalAmount = 0;
            $totalReceivedAmount = 0;
            $pdf->SetFont('Arial', '', 6);
            $pdf->setFillColor(233, 233, 233);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(9, 7, 'Machine#', 0, 0, 'L', 1);
            $pdf->Cell(15, 7, 'Actual Amount', 0, 0, 'L', 1);
            $pdf->Cell(13, 7, 'Tax ', 0, 0, 'C', 1);
            $pdf->Cell(13, 7, 'Discount', 0, 0, 'L', 1);
            $pdf->Cell(14, 7, 'Total Amount', 0, 0, 'L', 1);
            $pdf->Cell(14, 7, 'Received', 0, 1, 'C', 1);
            $pdf->SetFont('Arial', '', 9);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            if (count($records) > 0) {
                foreach ($records as $key => $record) {
                    $totalCount++;
                    $totalActualAmount += $record->actual_amount;
                    $totalTaxAmount += $record->taxamount;
                    $totalDiscountAmount += $record->discount;
                    $totalAmount += $record->total_amount;
                    $totalReceivedAmount += $record->received;

                    $pdf->Cell(10, 7, $record->machine_terminal_count, 0, 0, 'L', 1);
                    $pdf->Cell(14, 7, number_format($record->actual_amount, 0), 0, 0, 'C', 1);
                    $pdf->Cell(13, 7, number_format($record->taxamount, 0), 0, 0, 'C', 1);
                    $pdf->Cell(13, 7, number_format($record->discount, 0), 0, 0, 'C', 1);
                    $pdf->Cell(14, 7, number_format($record->total_amount, 0), 0, 0, 'C', 1);
                    $pdf->Cell(14, 7, number_format($record->received, 0), 0, 1, 'C', 1);
                }
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(10, 7, "Total", 0, 0, 'L', 1);
                $pdf->Cell(14, 7, number_format($totalActualAmount, 0), 0, 0, 'C', 1);
                $pdf->Cell(13, 7, number_format($totalTaxAmount, 0), 0, 0, 'C', 1);
                $pdf->Cell(13, 7, number_format($totalDiscountAmount, 0), 0, 0, 'C', 1);
                $pdf->Cell(14, 7, number_format($totalAmount, 0), 0, 0, 'C', 1);
                $pdf->Cell(14, 7, number_format($totalReceivedAmount, 0), 0, 1, 'C', 1);
                $pdf->ln(6);

                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
            } else {
                $pdf->Cell(78, 7, "No Record Found", 0, 1, 'C', 1);
            }
        }

        // WALLET SALES
        if ($permissions[0]->wallets_sales == 1) {
            $pdf->ln(6);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(78, 6, 'WALLET DETAILS', 0, 0, 'C', 1);
            $pdf->ln(6);

            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);

            $walletSales = DB::table("sales_receipts")->where("opening_id", $opening)->where("payment_id", 8)->get();
            $totalWalletSales = 0;

            $pdf->setFillColor(233, 233, 233);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(20, 7, 'Date', 0, 0, 'L', 1);
            $pdf->Cell(20, 7, 'Amount', 0, 0, 'L', 1);
            $pdf->Cell(38, 7, 'Narration', 0, 1, 'C', 1);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            if (count($walletSales) > 0) {
                foreach ($walletSales as $key => $walletSale) {
                    $totalWalletSales += $walletSale->total_amount;
                    $pdf->Cell(40, 7, $walletSale->receipt_no, 0, 0, 'L', 1);
                    $pdf->Cell(38, 7, $walletSale->total_amount, 0, 1, 'C', 1);
                }
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(40, 7, "Totals", 0, 0, 'L', 1);
                $pdf->Cell(38, 7, number_format($totalWalletSales, 0), 0, 1, 'L', 1);
                $pdf->ln(6);

                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
            } else {
                $pdf->Cell(78, 7, "No Record Found", 0, 1, 'C', 1);
            }
        }

        // EXPENSES
        if ($permissions[0]->expenses == 1) {
            $pdf->ln(6);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(78, 6, 'EXPENSE DETAILS', 0, 0, 'C', 1);
            $pdf->ln(6);

            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);

            $expenses = expense::join('expense_categories', 'expense_categories.exp_cat_id', '=', 'expenses.exp_cat_id')->where('expenses.opening_id', $opening)->get();
            $totalExpenseAmount = 0;

            $pdf->setFillColor(233, 233, 233);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(20, 7, 'Category', 0, 0, 'L', 1);
            $pdf->Cell(20, 7, 'Amount', 0, 0, 'L', 1);
            $pdf->Cell(38, 7, 'Details', 0, 1, 'C', 1);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            if (count($expenses) > 0) {
                foreach ($expenses as $key => $expense) {
                    $totalExpenseAmount += $expense->amount;
                    $pdf->Cell(20, 7, $expense->expense_category, 0, 0, 'L', 1);
                    $pdf->Cell(20, 7, number_format($expense->amount, 0), 0, 0, 'L', 1);
                    $pdf->Cell(38, 7, $expense->expense_details, 0, 1, 'C', 1);
                }
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(20, 7, "Total", 0, 0, 'L', 1);
                $pdf->Cell(20, 7, number_format($totalExpenseAmount, 0), 0, 0, 'L', 1);
                $pdf->Cell(38, 7, "", 0, 1, 'C', 1);
                $pdf->ln(6);

                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
            } else {
                $pdf->Cell(78, 7, "No Record Found", 0, 1, 'C', 1);
            }
        }

        // CASH IN 
        if ($permissions[0]->cash_in == 1) {
            $pdf->ln(6);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(78, 6, 'CASH-IN DETAILS', 0, 0, 'C', 1);
            $pdf->ln(6);

            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);

            $cashins = DB::table("sales_cash_in")->where("opening_id", $opening)->get();
            $totalCashIns = 0;

            $pdf->setFillColor(233, 233, 233);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(20, 7, 'Date', 0, 0, 'L', 1);
            $pdf->Cell(20, 7, 'Amount', 0, 0, 'L', 1);
            $pdf->Cell(38, 7, 'Narration', 0, 1, 'C', 1);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            if (count($cashins) > 0) {
                foreach ($cashins as $key => $cashin) {
                    $totalCashIns += $cashin->amount;
                    $pdf->Cell(20, 7, date("d M Y", strtotime($cashin->datetime)), 0, 0, 'L', 1);
                    $pdf->Cell(20, 7, number_format($cashin->amount, 0), 0, 0, 'L', 1);
                    $pdf->Cell(38, 7, $cashin->narration, 0, 1, 'C', 1);
                }
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(20, 7, "Totals", 0, 0, 'L', 1);
                $pdf->Cell(20, 7, number_format($totalCashIns, 0), 0, 0, 'L', 1);
                $pdf->Cell(38, 7, "", 0, 1, 'C', 1);
                $pdf->ln(6);

                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
            } else {
                $pdf->Cell(78, 7, "No Record Found", 0, 1, 'C', 1);
            }
        }

        // CASH OUT
        if ($permissions[0]->cash_out == 1) {
            $pdf->ln(6);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(78, 6, 'CASH-OUT DETAILS', 0, 0, 'C', 1);
            $pdf->ln(6);

            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);

            $cashouts = DB::table("sales_cash_out")->where("opening_id", $opening)->get();
            $totalCashOuts = 0;

            $pdf->setFillColor(233, 233, 233);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(20, 7, 'Date', 0, 0, 'L', 1);
            $pdf->Cell(20, 7, 'Amount', 0, 0, 'L', 1);
            $pdf->Cell(38, 7, 'Narration', 0, 1, 'C', 1);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            if (count($cashouts) > 0) {
                foreach ($cashouts as $key => $cashout) {
                    $pdf->Cell(20, 7, date("d M Y", strtotime($cashout->datetime)), 0, 0, 'L', 1);
                    $pdf->Cell(20, 7, number_format($cashout->amount, 0), 0, 0, 'L', 1);
                    $pdf->Cell(38, 7, $cashout->narration, 0, 1, 'C', 1);
                }
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(20, 7, "Totals", 0, 0, 'L', 1);
                $pdf->Cell(20, 7, number_format($totalCashOuts, 0), 0, 0, 'L', 1);
                $pdf->Cell(38, 7, "", 0, 1, 'C', 1);
                $pdf->ln(6);

                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
            } else {
                $pdf->Cell(78, 7, "No Record Found", 0, 1, 'C', 1);
            }
        }

        // SALES RETURN
        if ($salesReturnCount > 0) {
            $pdf->ln(6);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(78, 6, 'SALES RETURN DETAILS', 0, 0, 'C', 1);
            $pdf->ln(6);

            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);

            $salesReturnDetails = DB::table("sales_return")
                ->join("sales_receipts", "sales_receipts.id", "=", "sales_return.receipt_id")
                ->join("inventory_general", "inventory_general.id", "=", "sales_return.item_id")
                ->where("sales_return.opening_id", $opening)
                ->get();
            $totalSalesReturnCount = 0;
            $totalSalesReturnQty = 0;
            $totalSalesReturnAmount = 0;

            $pdf->setFillColor(233, 233, 233);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(20, 7, 'Receipt#', 0, 0, 'L', 1);
            $pdf->Cell(20, 7, 'Product', 0, 0, 'L', 1);
            $pdf->Cell(20, 7, 'Qty', 0, 0, 'L', 1);
            $pdf->Cell(18, 7, 'Amount', 0, 1, 'C', 1);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            if ($salesReturnCount > 0) {
                foreach ($salesReturnDetails as $key => $salesReturn) {
                    $totalSalesReturnCount++;
                    $totalSalesReturnQty += $salesReturn->qty;
                    $totalSalesReturnAmount += $salesReturn->amount;

                    $pdf->Cell(20, 7, $salesReturn->receipt_id, 0, 0, 'L', 1);
                    $pdf->Cell(20, 7, $salesReturn->product_name, 0, 0, 'L', 1);
                    $pdf->Cell(20, 7, $salesReturn->qty, 0, 0, 'L', 1);
                    $pdf->Cell(18, 7, $salesReturn->amount, 0, 1, 'C', 1);
                }
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(20, 7, "Totals", 0, 0, 'L', 1);
                $pdf->Cell(20, 7, $totalSalesReturnCount, 0, 0, 'L', 1);
                $pdf->Cell(20, 7, number_format($totalSalesReturnQty, 0), 0, 0, 'L', 1);
                $pdf->Cell(18, 7, number_format($totalSalesReturnAmount, 0), 0, 1, 'C', 1);
                $pdf->ln(6);

                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
            } else {
                $pdf->Cell(78, 7, "No Record Found", 0, 1, 'C', 1);
            }
        }

        header('Content-Type: application/pdf; charset=utf-8');

        $filePath = storage_path('app/public/declarationpdfs/sales_declaration_report_' . $opening  . '.pdf');

        // Ensure the 'pdfs' folder exists, if not, create it
        if (!file_exists(storage_path('app/public/declarationpdfs'))) {
            mkdir(storage_path('app/public/declarationpdfs'), 0777, true);
        }

        // Save the PDF to the specified path
        return $pdf->Output('F', $filePath);
        // return  $pdf->Output('I', "Declration Details.pdf", true);

        // return response($pdf->Output())
        //     ->header('Content-Type', 'application/pdf');


    }

    public function testLaravelProject()
    {
        // return 1;
        $terminals = DB::select("SELECT d.company_id,d.name as company,d.logo,c.branch_id,c.branch_name as branch, b.terminal_name as terminal, a.permission_id,a.terminal_id FROM users_sales_permission a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id INNER JOIN branch c on c.branch_id = b.branch_id INNER JOIN company d on d.company_id = c.company_id where a.Email_Reports = 1 and b.status_id = 1");
        return $terminals;
    }

    public function rawUsage(Request $request, Report $report)
    {
        $from = ($request->from != "" ? $request->from : date("Y-m-d"));
        $to = ($request->to != "" ? $request->to : date("Y-m-d"));

        $recipyItems = $report->getRecipeDetails(session("company_id"));
        $totalSaleItems = $report->getSalesItemByDate($from, $to, session("company_id"), session("branch"));
        $allItemUsage = $report->getAllItemUsage($from, $to);
        $totalItemsArray = [];


        foreach ($totalSaleItems as $item) {

            $filteredArray = Arr::where($recipyItems, function ($value, $key) use ($item) {
                return $value->recipy_id == $item->recipy_id;
            });

            foreach ($filteredArray as $key => $recipyItem) {
                $recipyitem = [
                    "item_id" => $recipyItem->item_id,
                    "item_name" => $recipyItem->product_name,
                    "uom" => $recipyItem->uom,
                    "usage_qty" => $recipyItem->usage_qty,
                    "total_qty" => $item->totalqty,
                    "total_usage" => $item->totalqty * $recipyItem->usage_qty,
                    "recipy_id" => $item->recipy_id,
                ];
                array_push($totalItemsArray, $recipyitem);
                // if($key == 1){
                // echo $recipyItem->item_id."</br>";
                // echo $item->totalqty * $recipyItem->usage_qty."</br>";
                // return $this->invent_stock_detection(session("branch"), $recipyItem->item_id, ( $item->totalqty * $recipyItem->usage_qty), "");
                // }

            }
        }
        // return $totalItemsArray;

        // $collection = collect($totalItemsArray);
        // $group = $collection->groupBy('item_id');
        // $resulttotalArray  = $group->map(function($item, $key) {
        // return [
        // 'qty' => $item->sum("total_usage"),
        // 'inventoryProductId' => $key,
        // ];
        // })->values();

        // $inventoriesPluck =  $resulttotalArray->pluck("inventoryProductId");
        // $inventories = $report->getInventories($inventoriesPluck);
        // foreach($inventories as $inventory){
        // "item_id" => $inventory->id,
        // "item_name" => $inventory->product_name,
        // "uom" => $inventory->name,
        // "usage_qty" => $inventory->usage_qty,
        // "total_qty" => $item->totalqty,
        // "total_usage" => $item->totalqty * $recipyItem->usage_qty,
        // "recipy_id" => $item->recipy_id,
        // }
        return view("reports.raw-usage", compact('totalSaleItems', 'totalItemsArray', 'allItemUsage', 'from', 'to'));
    }

    public function generateDailyUsage($from, $to)
    {
        $report = new Report();
        $from = ($from != "" ? $from : date("Y-m-d"));
        $to = ($to != "" ? $to : date("Y-m-d"));

        $recipyItems = $report->getRecipeDetails(session("company_id"));
        $totalSaleItems = $report->getSalesItemByDate($from, $to, session("company_id"), session("branch"));
        $allItemUsage = $report->getAllItemUsage($from, $to);
        $totalItemsArray = [];
        $totals = [];


        foreach ($totalSaleItems as $item) {

            $filteredArray = Arr::where($recipyItems, function ($value, $key) use ($item) {
                return $value->recipy_id == $item->recipy_id;
            });

            foreach ($filteredArray as $recipyItem) {
                $recipyitem = [
                    "item_id" => $recipyItem->item_id,
                    "item_name" => $recipyItem->product_name,
                    "uom" => $recipyItem->uom,
                    "usage_qty" => $recipyItem->usage_qty,
                    "total_qty" => $item->totalqty,
                    "total_usage" => $item->totalqty * $recipyItem->usage_qty,
                    "recipy_id" => $item->recipy_id,
                ];
                array_push($totalItemsArray, $recipyitem);
                $previousStock = DB::table("inventory_stock")->where("product_id", $recipyItem->item_id)->get();
                // return $previousStock->isEmpty();
                // return empty($previousStock);
                // $this->invent_stock_detection(session("branch"), $recipyItem->item_id, ( $item->totalqty * $recipyItem->usage_qty), "");
                $currentStock = DB::table("inventory_stock")->where("product_id", $recipyItem->item_id)->get();
                // return ;
                // array_push($totals,[
                // "item_id" => $recipyItem->item_id,
                // "usage_qty" => $recipyItem->usage_qty,
                // "total_qty" => $item->totalqty,
                // "total_usage" => $item->totalqty * $recipyItem->usage_qty,
                // "recipy_id" => $item->recipy_id,
                // "opening_id" => $item->opening_id,
                // "original_date" => $item->date,
                // "previous_stock" => ($previousStock->isEmpty() == 0 ? $previousStock[0]->balance : 0),
                // "current_stock" => ($previousStock->isEmpty() == 0 ? $currentStock[0]->balance : 0),
                // ]);
                DB::table("daily_recipe_usage")->insert([
                    "item_id" => $recipyItem->item_id,
                    "usage_qty" => $recipyItem->usage_qty,
                    "total_qty" => $item->totalqty,
                    "total_usage" => $item->totalqty * $recipyItem->usage_qty,
                    "recipy_id" => $item->recipy_id,
                    "opening_id" => $item->opening_id,
                    "original_date" => $item->date,
                    "previous_stock" => ($previousStock->isEmpty() == 0 ? $previousStock[0]->balance : 0),
                    "current_stock" => ($previousStock->isEmpty() == 0 ? $currentStock[0]->balance : 0),
                ]);
            }
            // return $totals;
        }
    }


    public function generateDailyUsageFromWebservice(Request $request)
    {
        return $request;
        $report = new Report();
        $from = ($request->from != "" ? $request->from : date("Y-m-d"));
        $to = ($request->to != "" ? $request->to : date("Y-m-d"));
        $company = $request->company;
        $branch = $request->branch;

        $recipyItems = $report->getRecipeDetails($company);
        $totalSaleItems = $report->getSalesItemByDate($from, $to, $company, $branch);
        $allItemUsage = $report->getAllItemUsage($from, $to);
        $totalItemsArray = [];
        $totals = [];


        foreach ($totalSaleItems as $item) {

            $filteredArray = Arr::where($recipyItems, function ($value, $key) use ($item) {
                return $value->recipy_id == $item->recipy_id;
            });

            foreach ($filteredArray as $recipyItem) {
                $recipyitem = [
                    "item_id" => $recipyItem->item_id,
                    "item_name" => $recipyItem->product_name,
                    "uom" => $recipyItem->uom,
                    "usage_qty" => $recipyItem->usage_qty,
                    "total_qty" => $item->totalqty,
                    "total_usage" => $item->totalqty * $recipyItem->usage_qty,
                    "recipy_id" => $item->recipy_id,
                ];
                array_push($totalItemsArray, $recipyitem);
                $previousStock = DB::table("inventory_stock")->where("product_id", $recipyItem->item_id)->get();
                // $this->invent_stock_detection(session("branch"), $recipyItem->item_id, ( $item->totalqty * $recipyItem->usage_qty), "");
                $currentStock = DB::table("inventory_stock")->where("product_id", $recipyItem->item_id)->get();
                DB::table("daily_recipe_usage")->insert([
                    "item_id" => $recipyItem->item_id,
                    "usage_qty" => $recipyItem->usage_qty,
                    "total_qty" => $item->totalqty,
                    "total_usage" => $item->totalqty * $recipyItem->usage_qty,
                    "recipy_id" => $item->recipy_id,
                    "opening_id" => $item->opening_id,
                    "original_date" => $item->date,
                    "previous_stock" => ($previousStock->isEmpty() == 0 ? $previousStock[0]->balance : 0),
                    "current_stock" => ($previousStock->isEmpty() == 0 ? $currentStock[0]->balance : 0),
                ]);
            }
        }
    }

    function invent_stock_detection($branchId, $itemCode, $totalQty, $status)
    {

        if (!empty($branchId) && $branchId > 0 && !empty($itemCode) && $itemCode > 0) {

            $result = DB::select("SELECT * FROM inventory_stock WHERE product_id = $itemCode and branch_id = $branchId and status_id IN(1,3) ");

            $updatedstock = 0;

            if (!empty($result)) {

                if ($status == "Open") {
                    $weightQty = DB::select("SELECT weight_qty FROM `inventory_general` where id = '$itemCode'");
                    $qty = $totalQty / $weightQty[0]->weight_qty;
                    $updatedstock = $qty;
                } else {
                    $updatedstock = $totalQty;
                }

                for ($s = 0; $s < sizeof($result); $s++) {

                    $value =  DB::select("SELECT * FROM inventory_stock WHERE product_id = $itemCode and branch_id = $branchId and status_id  IN(1,3)");
                    $updatedstock = ($updatedstock - $value[0]->balance);
                    // return  $updatedstock;

                    if ($updatedstock > 0) {
                        // $columns = "balance = 0,status_id = 2";
                        // $update = $GLOBALS['crud']->modify_mode($columns, 'inventory_stock', "stock_id = " . $value[0]->stock_id . " ");
                        DB::table("inventory_stock")->where("stock_id", $value[0]->stock_id)->update([
                            "balance" => 0,
                            "status_id" => 2,
                        ]);
                    } else if ($updatedstock < 0) {
                        $updatedstock = $updatedstock * (-1);
                        // $columns = "balance = " . $updatedstock . ",status_id = 1";
                        //                    echo  $updatedstock;
                        // $update = $GLOBALS['crud']->modify_mode($columns, 'inventory_stock', "stock_id = " . $value[0]->stock_id . " ");
                        DB::table("inventory_stock")->where("stock_id", $value[0]->stock_id)->update([
                            "balance" => $updatedstock,
                            "status_id" => 1,
                        ]);
                        break;
                    } else if ($updatedstock == 0) {
                        // $columns = "balance = 0,status_id = 2";
                        // $update = $GLOBALS['crud']->modify_mode($columns, 'inventory_stock', "stock_id = " . $value[0]->stock_id . " ");
                        DB::table("inventory_stock")->where("stock_id", $value[0]->stock_id)->update([
                            "balance" => 0,
                            "status_id" => 2,
                        ]);
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

    public function factoryOperationReport(Request $request, Vendor $vendor, Report $report)
    {
        $company = $vendor->company(session('company_id'));
        $branch = $vendor->getBranch(session('branch'));
        $orders = OrderModel::with("user", "branchrelation", "orderdetails", "orderdetails.inventory", "orderAccount", "orderAccountSub", "customer", "branchrelation", "orderStatus", "statusLogs", "statusLogs.status", "statusLogs.branch")->where("id", $request->order)->first();
        $provider = ServiceProviderOrders::with("serviceprovider")->where("receipt_id", $orders->id)->first();
        $pdf = new pdfClass();

        $pdf->AliasNbPages();
        $pdf->AddPage();

        // First row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Cell(110, 0, "Branch Name:", 0, 0, 'L');
        $pdf->Cell(50, 0, "Customer Name:", 0, 0, 'L');
        $pdf->Cell(50, 0, "", 0, 1, 'L');

        // Second row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Image(asset('storage/images/company/' . $company[0]->logo), 12, 10, -200);
        $pdf->Cell(110, 12, $orders->branchrelation->branch_name, 0, 0, 'L');
        $pdf->Cell(50, 12, $orders->customer->name, 0, 0, 'L');
        $pdf->Cell(50, 0, "", 0, 1, 'R');

        // Third row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 25, '', 0, 0);
        $pdf->Cell(110, 25, "Sales Person :", 0, 0, 'L');
        $pdf->Cell(50, 25, "Customer Number :", 0, 0, 'L');
        $pdf->Cell(50, 25, "", 0, 1, 'L');

        // Fourth row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, -15, '', 0, 0);
        $pdf->Cell(110, -15, (!empty($provider) ? $provider->serviceprovider->provider_name : '-'), 0, 0, 'L');
        $pdf->Cell(50, -15, $orders->customer->mobile, 0, 0, 'L');
        $pdf->Cell(50, -15, "", 0, 1, 'L');

        // Fifth row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 28, '', 0, 0);
        $pdf->Cell(110, 28, "Receipt No:", 0, 0, 'L');
        $pdf->Cell(50, 28, "Order #:", 0, 0, 'L');
        $pdf->Cell(50, 28, "", 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, -18, '', 0, 0);
        $pdf->Cell(110, -18, $orders->receipt_no, 0, 0, 'L');
        $pdf->Cell(50, -18, $orders->id, 0, 0, 'L');
        $pdf->Cell(50, -18, "", 0, 1, 'L');

        $pdf->ln(2);
        // Sixth row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 28, '', 0, 0);
        $pdf->Cell(110, 28, "Delivery Date:", 0, 0, 'L');
        $pdf->Cell(50, 28, "", 0, 0, 'L');
        $pdf->Cell(50, 28, "", 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, -18, '', 0, 0);
        $pdf->Cell(110, -18, date("d M Y ", strtotime($orders->delivery_date)), 0, 0, 'L');
        $pdf->Cell(50, -18, "", 0, 0, 'L');
        $pdf->Cell(50, -18, "", 0, 1, 'L');

        // Report name
        $pdf->ln(15);
        $pdf->Cell(190, 15, '', 'T', 1, 'C');

        // Move the status table to the top of the first page
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(110, 5, "Status :", 0, 1, 'L');

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(21.1, 8, 'Moom', 1, 0, 'C');
        $pdf->Cell(21.1, 8, "Casting", 1, 0, 'C');
        $pdf->Cell(21.1, 8, "Making", 1, 0, 'C');
        $pdf->Cell(21.1, 8, "Jhalai", 1, 0, 'C');
        $pdf->Cell(21.1, 8, 'Polish', 1, 0, 'C');
        $pdf->Cell(21.1, 8, "Gems", 1, 0, 'C');
        $pdf->Cell(21.1, 8, "Bandhai", 1, 0, 'C');
        $pdf->Cell(21.1, 8, "Delivered", 1, 0, 'C');
        $pdf->Cell(21.1, 8, 'Weight', 1, 1, 'C');

        $pdf->Cell(21.1, 15, "", 1, 0, 'C');
        $pdf->Cell(21.1, 15, "", 1, 0, 'C');
        $pdf->Cell(21.1, 15, "", 1, 0, 'C');
        $pdf->Cell(21.1, 15, "", 1, 0, 'C');
        $pdf->Cell(21.1, 15, "", 1, 0, 'C');
        $pdf->Cell(21.1, 15, "", 1, 0, 'C');
        $pdf->Cell(21.1, 15, "", 1, 0, 'C');
        $pdf->Cell(21.1, 15, "", 1, 0, 'C');
        $pdf->Cell(21.1, 15, "", 1, 1, 'C');

        // Line break after status table
        $pdf->ln(2);

        $count = count($orders->orderdetails);
        foreach ($orders->orderdetails as $key => $item) {
            if ($key != 0) {
                $pdf->ln(30);
            }

            // Add Item Code
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(190, 10, "Item Code: " . $item->item_code, 0, 1, 'C');

            // $imageUrl = $item->url;
            // $imagePath = 'storage/images/products/' . $item->image;

            // // Determine image path
            // if (filter_var($imageUrl, FILTER_VALIDATE_URL) && $this->isImageUrlAccessible($imageUrl)) {
            //     // Use the URL if it's valid and accessible
            //     $pdf->Cell(50, 50, $pdf->Image($imageUrl, $pdf->GetX() + 0, $pdf->GetY() + 0, 50, 50), 1);
            // } else {
            //     // Fallback to local storage if URL is not accessible or invalid
            //     if (Storage::exists($imagePath)) {
            //         $pdf->Cell(50, 50, $pdf->Image(asset($imagePath), $pdf->GetX() + 0, $pdf->GetY() + 0, 50, 50), 1);
            //     } else {
            //         // Fallback message if no image found
            //         $pdf->Cell(50, 50, 'No Image', 1, 0, 'C');
            //     }
            // }
            //    print_r(json_encode($item));
            //     exit();
            $imagePath = (asset('storage/images/products/' . ($item->inventory->image != '' ? $item->inventory->image : 'placeholder.jpg')));

            // Set up image path (replace with your actual image path)
            list($width, $height) = getimagesize($imagePath);

            // Add image centered on the page
            if ($key == 0) {
                $pdf->CellImageCenter($imagePath, 320 / 3, 320 / 3);
            } else {
                $pdf->CellImageCenter($imagePath, 320 / 2, 320 / 2);
            }

            // Add Reason (item note)
            $pdf->MultiCellText(190, 10, "Reason: " . $item->note);

            if ($key != $count - 1) {
                $pdf->AddPage();
            }
        }

        // Save file
        $pdf->Output('Factory_Operation_Report.pdf', 'I');
    }


    // Function to check if URL is accessible and returns valid image data
    function isImageUrlAccessible($url)
    {
        try {
            $response = Http::get($url);
            return $response->ok() && strpos($response->header('Content-Type'), 'image/') === 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function inventoryImageReport(Request $request, Vendor $vendor, Report $report)
    {

        $company = $vendor->company(session('company_id'));

        $branchname = "";

        if ($request->branch != "all") {
            $branchname = Branch::where("branch_id", $request->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " (All Branches) ";
        }

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
        $pdf->Cell(190, 10, 'Inventory Details ' . $branchname, 'B,T', 1, 'L');
        $pdf->ln(1);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(40, 7, 'Image', 'B', 0, 'C', 1);
        $pdf->Cell(30, 7, 'Item Code', 'B', 0, 'C', 1);
        $pdf->Cell(40, 7, 'Product Name', 'B', 0, 'C', 1);
        $pdf->Cell(40, 7, 'Department', 'B', 0, 'C', 1);
        $pdf->Cell(40, 7, 'Sub Department', 'B', 1, 'C', 1);

        $pdf->SetFont('Arial', '', 10);
        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);

        $inventory = $report->get_inventory_details_with_image($request->department, $request->subdepartment, $request->branch);

        foreach ($inventory as $item) {
            // Add image cell (adjust x, y as needed)
            if ($pdf->GetY() + 40 > 270) { // 40 for the height of the image and row, 270 is a margin (adjust as needed)
                $pdf->AddPage(); // Add a new page
            }

            // $imagePath = ($item->url != null ? $item->url : asset('storage/images/products/' . $item->image));

            // if (file_exists($imagePath) && $item->image != "") {
            //     $pdf->Cell(50, 50, $pdf->Image($imagePath, $pdf->GetX() + 0, $pdf->GetY() + 0, 50, 50), 1); // Image inside the cell
            // } else {
            //     $pdf->Cell(50, 50, 'No Image'.$item->url, 1, 0, 'C'); // Fallback if no image found
            // }

            $imageUrl = $item->url;
            $localImagePath = asset('storage/images/products/' . $item->image);
            // $localImagePath = storage_path('app/public/images/products/' . $item->image);;

            // $localImagePath =  url('/') . '/storage/images/products/' . $item->image;



            // Determine image path
            if (filter_var($imageUrl, FILTER_VALIDATE_URL) && $this->isImageUrlAccessible($imageUrl)) {
                // Use the URL if it's valid and accessible
                $pdf->Cell(50, 50, $pdf->Image($imageUrl, $pdf->GetX() + 0, $pdf->GetY() + 0, 50, 50), 1);
            } else {
                // Fallback to local storage if URL is not accessible or invalid
                //Storage::disk('public')->exists($localImagePath)
                if ($item->image != "") {
                    $pdf->Cell(50, 50, $pdf->Image($localImagePath, $pdf->GetX() + 0, $pdf->GetY() + 0, 50, 50), 1);
                } else {
                    // Fallback message if no image found
                    $pdf->Cell(50, 50, 'No Image' . $localImagePath, 1, 0, 'C');
                }
            }

            // Add item code
            $pdf->Cell(20, 50, $item->item_code, 1, 0, 'C');

            // Add item name
            $pdf->Cell(40, 50, $item->product_name, 1, 0, 1);


            $pdf->Cell(40, 50, $item->department_name, 1, 0, 1);

            // Add department
            $pdf->Cell(40, 50, $item->sub_depart_name, 1, 0, 1);

            // Line break after each row
            $pdf->Ln();
        }
        $pdf->Output('Inventory Details Report.pdf', 'I');
    }

    public function salesPersonReport(Request $request, Vendor $vendor, Report $report)
    {
        $branchname = "";
        $company = $vendor->company(session('company_id'));

        if ($request->branch != "all") {
            $branchname = Branch::where("branch_id", $request->branch)->first();
            $branchname = " (" . $branchname->branch_name . ") ";
        } else {
            $branchname = " (All Branches) ";
        }

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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Sales Person Report ' . $branchname, 'B,T', 1, 'L');
        $pdf->ln(1);

        if ($request->salesperson != "all") {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(20, 7, 'Order#', 'B', 0, 'L', 1);
            $pdf->Cell(40, 7, 'Receipt No', 'B', 0, 'L', 1);
            $pdf->Cell(35, 7, 'Customer Name', 'B', 0, 'L', 1);
            $pdf->Cell(30, 7, 'Total Amount', 'B', 0, 'L', 1);
            $pdf->Cell(25, 7, 'Status', 'B', 0, 'L', 1);
            $pdf->Cell(20, 7, 'Date', 'B', 0, 'L', 1);
            $pdf->Cell(20, 7, 'Time', 'B', 1, 'C', 1);
        }

        if ($request->salesperson == "all") {
            $salespersons = $report->totalsalesPersonReportQuery($request->fromdate, $request->todate, $request->branch, $request->salesperson, $request->status);
            foreach ($salespersons as $key => $salesperson) {
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(190, 10,  "Sales Person : " . $salesperson->fullname, 0, 1, 'L');

                //total variables
                $totalamount = 0;
                $totalOrder = 0;
                $totalbalanceamount = 0;

                $orders = $report->salesPersonReportQuery($request->fromdate, $request->todate, $request->branch, $salesperson->id, $request->status);

                $pdf->SetFont('Arial', 'B', 12);
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(20, 7, 'Order#', 'B', 0, 'L', 1);
                $pdf->Cell(40, 7, 'Receipt No', 'B', 0, 'L', 1);
                $pdf->Cell(45, 7, 'Customer Name', 'B', 0, 'L', 1);
                $pdf->Cell(40, 7, 'Total Amount', 'B', 0, 'L', 1);
                $pdf->Cell(25, 7, 'Status', 'B', 0, 'L', 1);
                $pdf->Cell(20, 7, 'Date', 'B', 1, 'L', 1);
                // $pdf->Cell(20, 7, 'Time', 'B', 1, 'C', 1);

                foreach ($orders as $values) {
                    $totalamount += (int)$values->total_amount;
                    $totalOrder++;

                    $pdf->SetFont('Arial', '', 10);
                    $pdf->setFillColor(232, 232, 232);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(20, 6, $values->id, 0, 0, 'L', 1);
                    $pdf->Cell(40, 6, $values->receipt_no, 0, 0, 'L', 1);
                    $pdf->Cell(45, 6, $values->name, 0, 0, 'L', 1);
                    $pdf->Cell(40, 6, number_format($values->total_amount, 0), 0, 0, 'C', 1);
                    $pdf->Cell(25, 6, $values->status, 0, 0, 'L', 1);
                    $pdf->Cell(20, 6, date("d-m-Y", strtotime($values->date)), 0, 1, 'L', 1);
                    // $pdf->Cell(20, 6, date("h:i a", strtotime($values->time)), 0, 1, 'C', 1);
                    $pdf->ln(1);
                }

                if ($request->status == "all") {
                    // $pdf->ln(2);
                    // $pdf->SetFont('Arial', 'B', 12);
                    // $pdf->setFillColor(0, 0, 0);
                    // $pdf->SetTextColor(255, 255, 255);
                    // $pdf->Cell(63, 7, 'Status Name', 'B', 0, 'C', 1);
                    // $pdf->Cell(63, 7, 'Total Orders', 'B', 0, 'C', 1);
                    // $pdf->Cell(63, 7, 'Total Amount', 'B', 1, 'C', 1);


                    // $allOrdersByStatus = $report->salesPersonReportQueryByStatus($request->fromdate, $request->todate, $request->branch, $salesperson->id, $request->status);
                    // $pdf->SetFont('Arial', '', 10);
                    // $pdf->setFillColor(232, 232, 232);
                    // $pdf->SetTextColor(0, 0, 0);
                    // foreach ($allOrdersByStatus as $status) {
                    //     $pdf->Cell(63, 7, $status->status, 'B,T', 0, 'C');
                    //     $pdf->Cell(63, 7, number_format($status->totalorders, 0), 'B,T', 0, 'C');
                    //     $pdf->Cell(63, 7, number_format($status->totalamount, 0), 'B,T', 1, 'C');
                    // }
                }

                $pdf->ln(2);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(63, 7, 'Total', 'B', 0, 'C', 1);
                $pdf->Cell(63, 7, 'Total Orders', 'B', 0, 'C', 1);
                $pdf->Cell(63, 7, 'Total Amount', 'B', 1, 'C', 1);

                $pdf->SetFont('Arial', '', 10);
                $pdf->setFillColor(232, 232, 232);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(63, 7, "", 'B,T', 0, 'C');
                $pdf->Cell(63, 7, number_format($totalOrder, 0), 'B,T', 0, 'C');
                $pdf->Cell(63, 7, number_format($totalamount, 0), 'B,T', 1, 'C');
            }
        } else {
            //total variables
            $totalamount = 0;
            $totalOrder = 0;
            $totalbalanceamount = 0;

            $orders = $report->salesPersonReportQuery($request->fromdate, $request->todate, $request->branch, $request->salesperson, $request->status);

            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(190, 10,  "Sales Person : " . (!empty($orders) ?  $orders[0]->fullname : ''), 0, 1, 'L');

            foreach ($orders as $values) {
                $totalamount += $values->total_amount;
                $totalOrder++;
                // $totalbalanceamount += $values->balance_amount;

                $pdf->SetFont('Arial', '', 10);
                $pdf->setFillColor(232, 232, 232);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(20, 6, $values->id, 0, 0, 'L', 1);
                $pdf->Cell(40, 6, $values->receipt_no, 0, 0, 'L', 1);
                $pdf->Cell(45, 6, $values->name, 0, 0, 'L', 1);
                $pdf->Cell(40, 6, number_format($values->total_amount, 0), 0, 0, 'C', 1);
                $pdf->Cell(25, 6, $values->status, 0, 0, 'L', 1);
                $pdf->Cell(20, 6, date("d-m-Y", strtotime($values->date)), 0, 1, 'L', 1);
                // $pdf->Cell(20, 6, date("h:i a", strtotime($values->time)), 0, 1, 'C', 1);
                $pdf->ln(1);
            }
            $pdf->ln(10);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(63, 7, 'Total Orders', 'B', 0, 'C', 1);
            $pdf->Cell(63, 7, 'Total Amount', 'B', 0, 'C', 1);
            $pdf->Cell(63, 7, '', 'B', 1, 'C', 1);

            $pdf->SetFont('Arial', '', 10);
            $pdf->setFillColor(232, 232, 232);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(63, 7, number_format($totalOrder, 0), 'B,T', 0, 'C');
            $pdf->Cell(63, 7, number_format($totalamount, 0), 'B,T', 0, 'C');
            $pdf->Cell(63, 7, '', 'B,T', 1, 'C');
        }
        //save file
        $pdf->Output('Sales Person Report.pdf', 'I');
    }

    //item sale database report
    public function websiteItemsSummary(Request $request, Vendor $vendor, Report $report)
    {
        $company = $vendor->company(session('company_id'));

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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Website Items Summary', 'B,T', 1, 'L');
        $pdf->ln(1);


        //total variables
        $totalCount = 0;
        $totalqty = 0;
        $totalamount = 0;
        $totalcost = 0;
        $totalmargin = 0;

        // TABLE HEADERS
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(20, 7, 'Code', 'B', 0, 'C', 1);
        $pdf->Cell(60, 7, 'Poduct Name', 'B', 0, 'L', 1);
        $pdf->Cell(30, 7, 'Qty', 'B', 0, 'C', 1);
        $pdf->Cell(20, 7, 'Price', 'B', 0, 'C', 1);
        $pdf->Cell(30, 7, 'Amount', 'B', 0, 'R', 1);
        $pdf->Cell(30, 7, 'Status', 'B', 1, 'R', 1);
        $details = $report->getWebsiteItemSummaryQuery($request->fromdate, $request->todate);
        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        foreach ($details as $value) {
            $totalCount++;

            $totalqty += $value->qty;
            $totalamount = $totalamount + $value->amount;
            $totalcost = $totalcost + $value->cost;
            $totalmargin = $totalmargin + ($value->amount - $value->cost);

            $pdf->SetFont('Arial', '', 10);

            $pdf->Cell(20, 6, $value->code, 0, 0, 'L', 1);
            $pdf->Cell(60, 6, $value->product_name, 0, 0, 'L', 1);
            $pdf->Cell(30, 6, number_format($value->qty), 0, 0, 'C', 1);
            $pdf->Cell(20, 6, number_format($value->price), 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($value->amount), 0, 0, 'R', 1);
            $pdf->Cell(30, 6, $value->order_status_name, 0, 1, 'R', 1);
        }

        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 7, "Total", 'B,T', 0, 'L');
        $pdf->Cell(60, 7, "Item Count (" . $totalCount . ")", 'B,T', 0, 'L');
        $pdf->Cell(30, 7, number_format($totalqty), 'B,T', 0, 'C');
        $pdf->Cell(20, 7, '', 'B,T', 0, 'C');
        $pdf->Cell(30, 7, number_format($totalamount), 'B,T', 0, 'R');
        $pdf->Cell(30, 7, '-', 'B,T', 1, 'R');

        $pdf->ln(2);
        $pdf->SetFont('Arial', 'B', 12);

        // $pdf->setFillColor(0, 0, 0);
        // $pdf->SetTextColor(255, 255, 255);
        // $pdf->Cell(190, 7, 'SUMMARY', 'B', 1, 'C', 1);
        // $pdf->setFillColor(255, 255, 255);
        // $pdf->SetTextColor(0, 0, 0);

        $pdf->ln(10);

        //save file
        $pdf->Output('website_items_summary.pdf', 'I');
    }

    public function websiteItemsSummaryExcel(Request $request, report $report)
    {
        // $branch = "";
        $companyName = Company::where("company_id", session("company_id"))->first();
        $companyName = $companyName->name;
        $branchName  = "";
        // if ($request->branch == "all") {
        //     $branch = Branch::with("company")->where("company_id", session('company_id'))->get();
        //     $branchName = "All Branches";
        // } else {
        //     $branch = Branch::with("company")->where("branch_id", $request->branch)->first();
        //     $branchName = $branch->branch_name;
        // }


        $datearray = [
            "from" => $request->from,
            "to" => $request->to,
        ];
        $details =  $report->getWebsiteItemSummaryQuery($request->from, $request->to);
        $details =  collect($details);
        return Excel::download(new WebsiteItemSummaryExport($details, $datearray, $companyName), "Website Items Summary Report.xlsx");
    }

    public function salesReturnReportExportExcel(Request $request, report $report)
    {
        // $branch = "";
        $companyName = Company::where("company_id", session("company_id"))->first();
        $companyName = $companyName->name;
        $branchName  = "";

        $datearray = [
            "from" => $request->fromdate,
            "to" => $request->todate,
        ];

        $details =  $report->salereturn_excel_details($request->fromdate, $request->todate, $request->terminalid, $request->code);
        $details =  collect($details);
        return Excel::download(new SaleReturnExport($details, $datearray, $companyName), "Sales Return Report.xlsx");
    }

    public function salesInvoicesReportExcel(Request $request, report $report)
    {
        $terminals = $report->getTerminals($request->branch);
        $details = $report->totalSales($request->terminal_id, $request->fromdate, $request->todate, $request->type, $request->category, $request->customer);
        $permission = $report->terminalPermission($request->terminal_id);
        return $request;
    }

    public function orderTimingsSummary(Request $request, Vendor $vendor)
    {

        $branches = Branch::query();
        if ($request->branch == "all") {
            $branches->where("company_id", session("company_id"));
        } else {
            $branches->where("branch_id", $request->branch);
        }
        $branches = $branches->get();



        $company = $vendor->company(session('company_id'));

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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Order Timing Summary', 'B,T', 1, 'L');
        $pdf->ln(1);

        foreach ($branches as $key => $branch) {

            $hourRanges = [];

            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(190, 10,  "Branch : " . $branch->branch_name, 0, 1, 'L');

            $modes = DB::table('sales_order_mode')->whereIn("order_mode_id", DB::table("sales_receipts")->whereBetween("date", [$request->fromdate, $request->todate])->where("branch", [$branch->branch_id])->groupBy("order_mode_id")->pluck("order_mode_id"))->get();
            foreach ($modes as $key => $mode) {

                $pdf->ln(2);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->setFillColor(128, 128, 128);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(190, 5, $mode->order_mode, 0, 1, 'C', 1);


                // Get the orders grouped by hour
                $orders = DB::table('sales_receipts')
                    ->select(DB::raw('HOUR(time) as hour, COUNT(*) as total_orders,SUM(total_amount) as total_amount'))
                    ->groupBy(DB::raw('HOUR(time)'))
                    ->whereBetween("date", [$request->fromdate, $request->todate])
                    ->where("branch", [$branch->branch_id])
                    ->where("order_mode_id", [$mode->order_mode_id])
                    ->orderBy('hour')
                    ->get();

                for ($i = 0; $i < 24; $i++) {
                    $startTime = Carbon::createFromTime($i)->format('g:i A');
                    $endTime = Carbon::createFromTime($i, 59)->format('g:i A');

                    $hourRanges[] = [
                        'hour' => $i,
                        'hour_range' => $startTime . ' - ' . $endTime,
                        'total_orders' => 0, // Default to 0 orders
                        'total_amount' => 0, // Default to 0 orders
                    ];
                }

                // Merge the query results into the hour range array
                $peakOrders = collect($hourRanges)->map(function ($range) use ($orders) {
                    $matchingOrder = $orders->firstWhere('hour', $range['hour']);
                    if ($matchingOrder) {
                        $range['total_orders'] = $matchingOrder->total_orders;
                        $range['total_amount'] = $matchingOrder->total_amount;
                    }
                    return $range;
                });

                // TABLE HEADERS
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(70, 7, 'Time', 'B', 0, 'L', 1);
                $pdf->Cell(60, 7, 'Total Orders', 'B', 0, 'C', 1);
                $pdf->Cell(60, 7, 'Total Amount', 'B', 1, 'C', 1);

                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
                foreach ($peakOrders as $value) {

                    $pdf->SetFont('Arial', '', 11);

                    $pdf->Cell(70, 6, $value["hour_range"], 0, 0, 'L', 1);
                    $pdf->Cell(60, 6, number_format($value["total_orders"]), 0, 0, 'C', 1);
                    $pdf->Cell(60, 6, number_format($value["total_amount"]), 0, 1, 'C', 1);
                }

                $pdf->ln(10);
            } // Order Mode Loop end
        } // Branch loop end

        //save file
        $pdf->Output('order_timing_summart.pdf', 'I');
    }
    public function orderAmountReceivable(Request $request, Vendor $vendor, report $report)
    {

        $branches = Branch::query();
        if ($request->branch == "all") {
            $branches->where("company_id", session("company_id"));
        } else {
            $branches->where("branch_id", $request->branch);
        }
        $branches = $branches->get();



        $company = $vendor->company(session('company_id'));

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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Order Amount Receivable', 'B,T', 1, 'L');
        $pdf->ln(1);
        if ($request->branch == "all") {
            $terminals = $report->getTerminals($request->branch);
        } else {
            $terminals = DB::table("terminal_details")->where("branch_id", $request->branch)->get();
        }
        $totalReceivedAmount = 0;
        foreach ($terminals as $key => $terminal) {
            $totalReceivedAmount = 0;

            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(275, 10, "Terminal Name: " . $terminal->terminal_name, 0, 1, 'L');

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(10, 7, 'S.No', 'B', 0, 'L', 1);
            $pdf->Cell(28, 7, 'Receipt No', 'B', 0, 'C', 1);
            $pdf->Cell(42, 7, 'Customer Name', 'B', 0, 'L', 1);
            $pdf->Cell(15, 7, 'B.Date', 'B', 0, 'L', 1); //17
            $pdf->Cell(24, 7, 'B.Amount', 'B', 0, 'C', 1);
            $pdf->Cell(22, 7, 'Advance', 'B', 0, 'C', 1);
            $pdf->Cell(19, 7, 'Received', 'B', 0, 'C', 1);
            $pdf->Cell(17, 7, 'Rec. Date', 'B', 0, 'C', 1);
            $pdf->Cell(20, 7, 'Pay Mode', 'B', 1, 'R', 1);
            $pdf->setFillColor(232, 232, 232);
            $pdf->SetTextColor(0, 0, 0);

            $details = $report->orderAmountReceivableTerminal($request->fromdate, $request->todate, $terminal->terminal_id);
            foreach ($details as $key => $value) {
                $totalReceivedAmount += $value->receive_amount;

                $pdf->SetFont('Arial', '', 9);
                $pdf->setFillColor(232, 232, 232);
                $pdf->SetTextColor(0, 0, 0);

                $pdf->Cell(10, 6, ++$key, 0, 0, 'L', 1);
                $pdf->Cell(28, 6, $value->receipt_no, 0, 0, 'C', 1); // date("d-m-y", strtotime($value->date))
                $pdf->Cell(42, 6, $value->name, 0, 0, 'L', 1);
                $pdf->Cell(15, 6, date("d-m-y", strtotime($value->date)), 0, 0, 'L', 1);
                $pdf->Cell(24, 6, number_format($value->total_amount, 2), 0, 0, 'C', 1);
                $pdf->Cell(22, 6, number_format($value->paid, 2), 0, 0, 'C', 1);
                $pdf->Cell(19, 6, number_format($value->receive_amount, 2), 0, 0, 'C', 1);
                $pdf->Cell(17, 6, date("d-m-y", strtotime($value->received_date)), 0, 0, 'C', 1);
                $pdf->Cell(20, 6, $value->payment_mode, 0, 1, 'C', 1);

                $pdf->ln(1);
            }
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);

            $pdf->Cell(143, 6, "Total:", 0, 0, 'C', 1);
            $pdf->Cell(17, 6, number_format($totalReceivedAmount, 0), 0, 0, 'C', 1);
            $pdf->Cell(37, 6, "", 0, 1, 'C', 1);
        }

        //save file
        $pdf->Output('order_amount_receivable.pdf', 'I');
    }

    public function bookingDeliveryReport(Request $request, Vendor $vendor, report $report)
    {
        $branches = Branch::query();
        if ($request->branch == "all") {
            $branches->where("company_id", session("company_id"));
        } else {
            $branches->where("branch_id", $request->branch);
        }
        $branches = $branches->get();



        $company = $vendor->company(session('company_id'));

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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Order Amount Receivable', 'B,T', 1, 'L');
        $pdf->ln(1);
        if ($request->branch == "all") {
            $terminals = $report->getTerminals($request->branch);
        } else {
            $terminals = DB::table("terminal_details")->where("branch_id", $request->branch)->get();
        }
        $totalCount = 0;
        $totalActualAmount = 0;
        $totalTaxAmount = 0;
        $totalDiscountAmount = 0;
        $totalAmount = 0;
        $totalReceivedAmount = 0;

        foreach ($terminals as $key => $terminal) {
            $totalReceivedAmount = 0;

            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(275, 10, "Terminal Name: " . $terminal->terminal_name, 0, 1, 'L');

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(10, 7, 'S.No', 'B', 0, 'L', 1);
            $pdf->Cell(30, 7, 'Machine #', 'B', 0, 'C', 1);
            $pdf->Cell(30, 7, 'Actual Amount', 'B', 0, 'L', 1);
            $pdf->Cell(30, 7, 'Tax Amount', 'B', 0, 'L', 1);
            $pdf->Cell(30, 7, 'Discount Amount', 'B', 0, 'L', 1);
            $pdf->Cell(30, 7, 'Total Amount', 'B', 0, 'C', 1);
            $pdf->Cell(30, 7, 'Amount Received', 'B', 1, 'C', 1);

            $pdf->setFillColor(232, 232, 232);
            $pdf->SetTextColor(0, 0, 0);

            $details = $report->bookingDeliveryReport($request->fromdate, $request->todate, $terminal->terminal_id);
            foreach ($details as $key => $value) {

                $totalCount++;
                $totalActualAmount += $value->actual_amount;
                $totalTaxAmount += $value->taxamount;
                $totalDiscountAmount += $value->discount;
                $totalAmount += $value->total_amount;
                $totalReceivedAmount += $value->received;

                $pdf->SetFont('Arial', '', 9);
                $pdf->setFillColor(232, 232, 232);
                $pdf->SetTextColor(0, 0, 0);

                $pdf->Cell(10, 6, ++$key, 0, 0, 'L', 1);
                $pdf->Cell(30, 6, $value->machine_terminal_count, 0, 0, 'C', 1);
                $pdf->Cell(30, 6, $value->actual_amount, 0, 0, 'C', 1);
                $pdf->Cell(30, 6, $value->taxamount, 0, 0, 'C', 1);
                $pdf->Cell(30, 6, $value->discount, 0, 0, 'C', 1);
                $pdf->Cell(30, 6, $value->total_amount, 0, 0, 'C', 1);
                $pdf->Cell(30, 6, $value->received, 0, 1, 'C', 1);

                $pdf->ln(1);
            }
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);

            $pdf->Cell(10, 6, "Total:", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($totalCount, 0), 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($totalActualAmount, 0), 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($totalTaxAmount, 0), 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($totalDiscountAmount, 0), 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($totalAmount, 0), 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($totalReceivedAmount, 0), 0, 1, 'C', 1);
        }

        //save file
        $pdf->Output('booking_delivery_order_report.pdf', 'I');
    }

    public function customerSalesReport(Request $request, Vendor $vendor, report $report)
    {
        $branches = Branch::query();
        if ($request->branch == "all") {
            $branches->where("company_id", session("company_id"));
        } else {
            $branches->where("branch_id", $request->branch);
        }
        $branches = $branches->get();
        if ($request->customer == "null") {
            $request->customer = "all";
        }

        $company = $vendor->company(session('company_id'));

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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Customer Sales', 'B,T', 1, 'L');
        $pdf->ln(1);

        $totalCount = 0;
        $totalAmount = 0;

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(10, 7, 'S.No', 'B', 0, 'L', 1);
        $pdf->Cell(40, 7, 'Name', 'B', 0, 'L', 1);
        $pdf->Cell(35, 7, 'Branch', 'B', 0, 'L', 1);
        $pdf->Cell(25, 7, 'Contact', 'B', 0, 'C', 1);
        $pdf->Cell(30, 7, 'Membership #', 'B', 0, 'C', 1);
        $pdf->Cell(25, 7, 'Total Orders', 'B', 0, 'C', 1);
        $pdf->Cell(25, 7, 'Total Sales', 'B', 1, 'C', 1);


        $pdf->setFillColor(232, 232, 232);
        $pdf->SetTextColor(0, 0, 0);

        $details = $report->customerSalesReport($request->fromdate, $request->todate, $request->branch, $request->customer);

        foreach ($details as $key => $value) {

            $totalCount++;
            $totalAmount += $value->total_sales;

            $pdf->SetFont('Arial', '', 9);
            $pdf->setFillColor(232, 232, 232);
            $pdf->SetTextColor(0, 0, 0);

            $pdf->Cell(10, 6, ++$key, 0, 0, 'L', 1);
            $pdf->Cell(40, 6, $value->name, 0, 0, 'L', 1);
            $pdf->Cell(35, 6, $value->branch_name, 0, 0, 'L', 1);
            $pdf->Cell(30, 6, $value->mobile, 0, 0, 'C', 1);
            $pdf->Cell(25, 6, $value->membership_card_no, 0, 0, 'C', 1);
            $pdf->Cell(25, 6, $value->total_orders, 0, 0, 'C', 1);
            $pdf->Cell(25, 6, number_format($value->total_sales, 0), 0, 1, 'C', 1);

            $pdf->ln(1);
        }
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);

        $pdf->Cell(140, 6, "Total:", 0, 0, 'C', 1);
        $pdf->Cell(25, 6, number_format($totalCount, 0), 0, 0, 'C', 1);
        $pdf->Cell(25, 6, number_format($totalAmount, 0), 0, 1, 'C', 1);


        //save file
        $pdf->Output('booking_delivery_order_report.pdf', 'I');
    }

    public function cashInOutReport(Request $request, Vendor $vendor, report $report)
    {
        $branches = Branch::query();
        if ($request->branch == "all") {
            $branches->where("company_id", session("company_id"));
        } else {
            $branches->where("branch_id", $request->branch);
        }
        $branches = $branches->get();

        $company = $vendor->company(session('company_id'));

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

        //filter section
        $fromdate = date('F-d-Y', strtotime($request->fromdate));
        $todate = date('F-d-Y', strtotime($request->todate));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'Cash In/Out Report', 'B,T', 1, 'L');
        $pdf->ln(1);

        if ($request->branch == "all") {
            $terminals = $report->getTerminals($request->branch);
        } else {
            $terminals = DB::table("terminal_details")
                ->where("branch_id", $request->branch)
                ->when($request->terminalid != "", function ($query) use ($request) {
                    return $query->where("terminal_id", $request->terminalid);
                })
                ->get();
        }
        // TERMINALS 
        foreach ($terminals as $key => $terminal) {

            $pdf->ln(2);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(190, 10, "Terminal Name: " . $terminal->terminal_name, 'T,B', 1, 'L');

            $pdf->SetFont('Arial', 'B', 15);
            $pdf->setFillColor(232, 232, 232);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(190, 7, 'CASH-IN', 0, 1, 'C', 1);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(10, 7, 'S.No', 'B', 0, 'L', 1);
            $pdf->Cell(40, 7, 'Amount', 'B', 0, 'C', 1);
            $pdf->Cell(70, 7, 'Narration', 'B', 0, 'L', 1);
            $pdf->Cell(35, 7, 'Date', 'B', 0, 'C', 1);
            $pdf->Cell(35, 7, 'Time', 'B', 1, 'C', 1);

            $pdf->setFillColor(232, 232, 232);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 10);
            $totalCashIn = 0;

            // CASH IN
            $cashInDetails = $report->cashIn($request->fromdate, $request->todate, $terminal->terminal_id);
            if (!empty($cashInDetails)) {
                foreach ($cashInDetails as $key => $cashIn) {
                    $totalCashIn += $cashIn->amount;
                    $pdf->Cell(10, 7, ++$key, 'B', 0, 'L', 1);
                    $pdf->Cell(40, 7, $cashIn->amount, 'B', 0, 'C', 1);
                    $pdf->Cell(70, 7, $cashIn->narration, 'B', 0, 'L', 1);
                    $pdf->Cell(35, 7, date("d M Y", strtotime($cashIn->datetime)), 'B', 0, 'C', 1);
                    $pdf->Cell(35, 7, date("H:i:s", strtotime($cashIn->datetime)), 'B', 1, 'C', 1);
                }
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(95, 7, "Totals", 'B', 0, 'L', 1);
                $pdf->Cell(95, 7, $totalCashIn, 'B', 1, 'R', 1);
            } else {
                $pdf->Cell(190, 7, 'No Record Found', 0, 1, 'C', 1);
            }

            $pdf->ln(2);

            // CASH OUT
            $pdf->SetFont('Arial', 'B', 15);
            $pdf->setFillColor(232, 232, 232);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(190, 7, 'CASH-OUT', 0, 1, 'C', 1);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(10, 7, 'S.No', 'B', 0, 'L', 1);
            $pdf->Cell(40, 7, 'Amount', 'B', 0, 'C', 1);
            $pdf->Cell(70, 7, 'Narration', 'B', 0, 'L', 1);
            $pdf->Cell(35, 7, 'Date', 'B', 0, 'C', 1);
            $pdf->Cell(35, 7, 'Time', 'B', 1, 'C', 1);

            $pdf->setFillColor(232, 232, 232);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 10);

            $totalCashOut = 0;

            $cashOutDetails = $report->cashOut($request->fromdate, $request->todate, $terminal->terminal_id);
            if (!empty($cashOutDetails)) {
                foreach ($cashOutDetails as $key => $cashOut) {
                    $totalCashOut += $cashOut->amount;
                    $pdf->Cell(10, 7, ++$key, 'B', 0, 'L', 1);
                    $pdf->Cell(40, 7, $cashOut->amount, 'B', 0, 'C', 1);
                    $pdf->Cell(70, 7, $cashOut->narration, 'B', 0, 'L', 1);
                    $pdf->Cell(35, 7, date("d M Y", strtotime($cashOut->datetime)), 'B', 0, 'C', 1);
                    $pdf->Cell(35, 7, date("H:i:s", strtotime($cashOut->datetime)), 'B', 1, 'C', 1);
                }
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(95, 7, "Totals", 'B', 0, 'L', 1);
                $pdf->Cell(95, 7, $totalCashOut, 'B', 1, 'R', 1);
            } else {
                $pdf->Cell(190, 7, 'No Record Found', 0, 1, 'C', 1);
            }
        }

        //save file
        $pdf->Output('cash_in_and_out_report.pdf', 'I');
    }

    public function testFpdf()
    {
        $pdf = new FPDF();
        $pdf->AddPage();

        // Add Custom Urdu Font
        $pdf->AddFont('JameelNooriNastaleeq', '', 'JameelNooriNastaleeq.php');

        // Set Font to Urdu
        $pdf->SetFont('JameelNooriNastaleeq', '', 14);

        // Write Urdu Text (Use mb_convert_encoding)
        $urduText = mb_convert_encoding('یہ اردو کا جملہ ہے', 'ISO-8859-1', 'UTF-8');

        $pdf->Cell(0, 10, $urduText, 0, 1, 'C');

        $pdf->Cell(0, 10, $urduText, 0, 1, 'C');

        // Output PDF
        $pdf->Output();
        exit;
    }
}
