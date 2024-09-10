<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use flash, Auth, Session;
use App\order;
use App\master;
use App\inventory;
use App\Customer;
use App\Vendor;
use App\Models\Order as OrderModel;
use App\Models\Terminal;
use App\Models\OrderStatus;
use App\Models\CustomerAccount;
use App\Http\Resources\onlineSalesResource\salesReceiptResource;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderOrders;
use App\Models\ServiceProviderLedger;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function testQuery()
    {
        // $data = OrderModel::with("terminal")->whereIn("terminal_id", Terminal::where("branch_id", auth()->user()->branch_id)->pluck("terminal_id"))
        // ->select(DB::raw('COUNT(id) as orders'), "terminal_id")
        // ->whereDate("date", ["2023-10-11", "2023-10-20"])
        // ->groupBy("terminal_id")
        // ->get();
        $data = Terminal::with('orders')
            // whereHas('orders',function($query){
            // $query->sum('total_amount');
            // })
            // withSum('orders','total_amount')
            // with(["orders" => function($query){
            // return $query->select(DB::raw('COALESCE(SUM(total_amount), 0)'));
            // }])
            ->select(DB::raw('SUM(total_amount)'), "terminal_name")
            ->where("branch_id", auth()->user()->branch_id)
            // ->groupBy('terminal_id')
            ->toSql();
        return $data;
        $result = collect($data);
        $orders =  $result->map(function ($name, $key) {
            return $name->terminal->terminal_name;
        });
        $terminals =  $result->map(function ($name, $key) {
            return $name->orders;
        });
        return response()->json(["orders" => $orders, "terminals" => $terminals]);
    }

    public function ordersview(Request $request, order $order, Customer $customer)
    {
        DB::table("sales_receipts")->where("is_notify", 1)->update(["is_notify" => 0]);
        $customer = $customer->getcustomers();
        $orders = $order->orderStatus();
        $paymentMode = $order->paymentMode();
        $mode = $order->ordersMode();
        $branch = $order->getBranch();
        $serviceproviders = ServiceProvider::where("branch_id", session("branch"))->where("status_id", 1)->select(["id", "provider_name"])->get();
        $orders = $order->getPOSOrders($request->first, $request->second, $request->status, $request->customer, $request->receipt, $request->mode, $request->deli_from, $request->deli_to, $request->branch, $request->terminal, $request->payMode);
        return view('order.orderview', compact('orders', 'customer', 'mode', 'branch', 'paymentMode', 'orders', 'serviceproviders'));
    }

    public function orderdetails(Request $request, Customer $customer)
    {
        $orders = OrderModel::with("orderdetails", "orderdetails.inventory", "orderdetails.itemstatus", "orderdetails.statusLogs", "orderdetails.statusLogs.status", "orderAccount", "orderAccountSub", "customer", "branchrelation", "orderStatus", "statusLogs", "statusLogs.status", "statusLogs.branch", "statusLogs.user","payment")->where("id", $request->id)->first();
        $received = CustomerAccount::where("receipt_no", $request->id)->sum('received');
        $statuses = OrderStatus::all();
        $ledgerDetails = $customer->LedgerDetailsShowInOrderDetails($orders->customer->id, $request->id);
        $provider = ServiceProviderOrders::with("serviceprovider")->where("receipt_id",$orders->id)->first();
        // return $ledgerDetails;
        return view("order.order-details", compact("orders", "received", "statuses", "ledgerDetails","provider"));
    }

    public function orderStatusChange(Request $request, order $order)
    {
        $order->updateStatusOrder($request->id, $request->status, 0);
        return redirect()->route("order.details", $request->id);
    }

    public function sentToWorkshop(Request $request, order $order)
    {
        $order->sentToWorkshop($request->itemId);
        return redirect()->route("order.details", $request->id);
    }

    public function changeItemStatus(Request $request, order $order)
    {
        $order->changeItemStatus($request->id, $request->itemId, $request->status);
        return redirect()->route("order.details", $request->id);
    }

    public function statusUpdate_websiteOrder(Request $request, order $order)
    {
        $order->updateStatusOrder($request->id, $request->status, 0);
        if ($request->ordercode == null) {
            return 1;
        }

        return redirect()->route("getWebsiteSaleReceiptDetails", $request->ordercode);
    }

    public function ordersviewnew(Request $request, order $order, Customer $customer, OrderService $orderService)
    {
        $request->type = "declaration";
        DB::table("sales_receipts")->where("is_notify", 1)->update(["is_notify" => 0]);
        $customer = $customer->getcustomers();
        $statuses = $order->orderStatus();
        $paymentMode = $order->paymentMode();
        $mode = $order->ordersMode();
        $branch = $order->getBranch();
        $serviceproviders = $orderService->getServiceProviders();
        $orders = "";
        $totalorders = $order->getTotalAndSumofOrdersQuery($request);
        return view('order.orderviewnew', compact('orders', 'customer', 'mode', 'branch', 'paymentMode', 'orders', 'serviceproviders', 'totalorders', 'statuses'));
    }

    public function getNewPOSOrders(Request $request, order $order)
    {
        // return $request;
        $orders = $order->getNewPOSOrdersQuery($request);
        // return $orders;
        $totalorders = $order->getTotalAndSumofOrdersQuery($request);
        // return $totalorders;
        // $filteredArray = Arr::where($totalorders->toArray(), function ($value, $key) {
        // return $value->order_status_name == "Void";
        // });
        // $collection = collect($totalorders);
        // return $collection->filter(fn ($item) => $item->order_status_name == "Void")->values()->all();
        return view('partials.orders_table', compact('orders', 'totalorders'));
    }


    public function getOrderById(Request $request, order $order)
    {
        $orders = $order->getOrders($request->first, $request->second, $request->status, $request->customer, $request->receipt, $request->mode, $request->deli_from, $request->deli_to, $request->branch, $request->terminal, $request->payMode);
        return $orders;
    }

    public function getPOSOrders(Request $request, order $order)
    {
        return $order->getPOSOrders($request->first, $request->second, $request->status, $request->customer, $request->receipt, $request->mode, $request->deli_from, $request->deli_to, $request->branch, $request->terminal, $request->payMode);
    }

    public function getPOSFilterOrders(Request $request, order $order)
    {
        return $order->getPOSFilterOrders($request->first, $request->second, $request->status, $request->customer, $request->receipt, $request->mode, $request->branch, $request->terminal, $request->payMode, $request->order_no);
    }

    public function webOrders(Request $Request, order $order, Customer $customer)
    {
        $customer     = $order->getCustomers();
        $orders       = $order->orderStatus();
        $paymentMode  = $order->paymentMode();
        $mode         = $order->ordersMode();
        $branch       = $order->getBranch();
        $website      = DB::table('website_details')->where('company_id', Auth::user()->company_id)->select('id', 'name')->get();
        $totalorders  = $order->getWebOrders(DB::table("user_branches")->where('user_id', Auth::user()->id)->pluck('branch_id')->implode(','), 0);
        $riders       = $order->getRiders();
        return view('order.weborders', compact('orders', 'customer', 'mode', 'branch', 'paymentMode', 'totalorders', 'riders', 'website'));
    }

    public function websiteOrders(Request $Request, order $order, Customer $customer)
    {
        $customer     = $order->getCustomers();
        $orders       = $order->orderStatus();
        $paymentMode  = $order->paymentMode();
        $mode         = $order->ordersMode();
        $branch       = $order->getBranch();
        $website      = DB::table('website_details')->where('company_id', Auth::user()->company_id)->select('id', 'name')->get();
        $totalorders  = $order->getWebsiteOrders(DB::table("user_branches")->where('user_id', Auth::user()->id)->pluck('branch_id')->implode(','));
        $riders       = $order->getRiders();

        // return compact('totalorders');
        return view('order.website_orders', compact('orders', 'customer', 'mode', 'branch', 'paymentMode', 'totalorders', 'riders', 'website'));
    }

    public function checkwebsiteOrders(Request $request, order $order)
    {
        // $branch = null;
        // if(session('roleId') == '2'){
        //     $branch = DB::table('website_branches')
        //                  ->whereIn('website_id',[DB::table('website_details')->where('company_id',session('company_id'))->pluck('id')])
        //                  ->pluck('branch_id');
        // }else{
        //     $branch = DB::table('user_branches')
        //                  ->where('user_id',session('userid'))
        //                  ->pluck('branch_id');
        // }

        //   if($branch != null){
        $order_status    = $order->orderStatus();
        $getOrders       = $order->getWebOrders(DB::table("user_branches")->where('user_id', Auth::user()->id)->pluck('branch_id')->implode(','), 1);

        if ($getOrders > 0) {
            return response()->json(['status' => true, 'orders' => $getOrders, "orderStatus" => $order_status]);
        } else {
            return response()->json(['status' => false]);
        }


        //     }

        //   return response()->json(false);
    }

    public function websiteOrderDetail(Request $request, order $order, Customer $customer)
    {

        // $receiptDetail = DB::select('SELECT b.product_name,a.total_qty,a.total_amount FROM sales_receipt_details a
        //         INNER JOIN inventory_general b on b.id = a.item_code
        //         where a.receipt_id =  ?',[$request->id]);

        // $variatItem = DB::select('SELECT * FROM sales_receipt_details where receipt_id =  ? and parent_item_code != "" ',[$request->id]);

        // $subVariatItem = DB::select('SELECT * FROM sales_receipt_variations where receipt_id = ?',[$request->id]);

        // $orders = OrderModel::with("orderdetails","orderdetails.inventory","orderAccount","orderAccountSub","customer","orderStatus")->where("url_orderid",$request->id)->where("branch",session('branch'))->first();
        // return $orders;

        // DB::table('inventory_general')->join('sales_receipt_details','sales_receipt_details.item_code','inventory_general.id')->where('sales_receipt_details.receipt_id',$request->id)->where('sales_receipt_details.mode','inventory-general')->select('sales_receipt_details.receipt_id','sales_receipt_details.item_code','sales_receipt_details.item_name','sales_receipt_details.total_qty','sales_receipt_details.total_amount','sales_receipt_details.calcu_amount_webcart','sales_receipt_details.receipt_detail_id','sales_receipt_details.discount_value','sales_receipt_details.discount_code','sales_receipt_details.actual_price')->get()

        $record = $order->web_onlineOrderDetails($request->id);

        if ($record == null) {
            Session::flash('error', 'Error! order detail not found.');
            return redirect('web-orders-view');
        }

        $orders = new salesReceiptResource($record);

        if ($orders != null) {
            $orders = json_encode($orders);
            $orders = json_decode($orders);
        }
        // return response()->json($orders);
        // die();
        //  $data = ["products"=>$products];
        return view("order.online-order-details", compact("orders"));
        // return response()->json($data,200);
    }

    public function websiteOrdersFilter(Request $request, order $order)
    {
        $websiteId   = $request->website;
        $customer    = $order->getcustomers();
        $orders      = $order->orderStatus();
        $paymentMode = $order->paymentMode();
        $mode        = $order->ordersMode();
        $branch      = $order->getBranch();
        $riders      = $order->getRiders();
        $totalorders = $order->getWebsiteOrdersFilter($request->first, $request->second, $request->customer, $request->receipt, $request->branch, $websiteId);
        $website     = DB::table('website_details')->where('company_id', Auth::user()->company_id)->select('id', 'name')->get();

        return view('order.weborders', compact('orders', 'customer', 'mode', 'branch', 'paymentMode', 'totalorders', 'riders', 'website', 'websiteId'));
    }

    public function webOrdersFilter(Request $request, order $order)
    {
        $websiteId    = $request->website;
        $customer     = $order->getcustomers();
        $orders       = $order->orderStatus();
        $paymentMode  = $order->paymentMode();
        $mode         = $order->ordersMode();
        $branch       = $order->getBranch();
        $riders       = $order->getRiders();
        $totalorders  = $order->getWebOrdersFilter($request->first, $request->second, $request->customer, $request->receipt, $request->branch);
        $website      = DB::table('website_details')->where('company_id', Auth::user()->company_id)->select('id', 'name')->get();

        return view('order.weborders', compact('orders', 'customer', 'mode', 'branch', 'paymentMode', 'totalorders', 'riders'));
    }

    public function getWebOrders(Request $request, order $order)
    {
        $orders = $order->getWebOrders($request->first, $request->second, $request->status, $request->customer, $request->receipt, $request->mode, $request->deli_from, $request->deli_to, $request->branch, $request->terminal, $request->payMode);
        return $orders;
    }

    public function changeOrderBranch(Request $request, order $order)
    {
        $result = $order->updateBranchOrder($request->receipt, $request->branch);
        return $result;
    }

    public function changeOrderStatus(Request $request, order $order)
    {
        $result = $order->updateStatusOrder($request->receipt, $request->status, $request->rider);
        return $result;
    }

    public function changeOrderStatuswithLogs(Request $request, order $order)
    {
        $result = $order->updateOrderStatusWithLogs($request->receipt, $request->status, $request->name, $request->mobile, $request->comments, $request->branch);
        if ($result > 0) {
            return response()->json(["status" => 200]);
        } else {
            return response()->json(["status" => 500]);
        }
    }

    public function orderAssign(Request $request, order $order, master $master, inventory $inventory)
    {
        $chk = $order->compareStatus($request->id);
        if ($chk[0]->count == $chk[0]->masterAssign) {
            $status = $order->updateSalesReceiptStatus($request->id);
        }
        $items = $order->orderItems($request->id);
        $master = $master->getMasters();
        $raw = $inventory->getRawItems();
        $uom = $inventory->uom();
        $receipt = $request->id;
        $assign = $order->getAssignItems($request->id);
        $delAssign = $order->deletemasterAssignTemp();
        return view('order.orderassign', compact('items', 'master', 'raw', 'uom', 'receipt', 'assign'));
    }

    public function getUOMByProduct(Request $request, inventory $inventory)
    {
        $result = $inventory->getUOMFromProduct($request->id);
        return $result;
    }

    public function insertAssign(Request $request, order $order)
    {
        $chk = $order->chkOrder($request->finished, $request->product, $request->receipt);

        if ($chk == 0) {
            $items = [
                'master_id' => $request->master,
                'finished_good_id' => $request->finished,
                'receipt_no' => $request->receipt,
                'product_id' => $request->product,
                'uom_id' => $request->uomid,
                'qty' => $request->qty,
                'received' => '0',
                'amount' => $request->amount,
                'date' => date("Y-m-d"),
                'status' => 2,
            ];
            $result = $order->insertAssign($items);
            // if($result == 1)
            // {
            $items = $order->getTempItems();
            return $items;
            // }
        } else {
            return 2;
        }
    }

    public function updateAssign(Request $request, order $order)
    {

        $items = [
            'product_id' => $request->product,
            'uom_id' => $request->uomid,
            'qty' => $request->qty,
            'amount' => $request->amount,
        ];
        $result = $order->updateAssign($items, $request->id);
        // if($result == 1)
        // {
        $items = $order->getTempItems();
        return $items;
        // }


    }

    public function InsertAssignTemp(Request $request, order $order)
    {
        $chk = $order->chkMasterTemp($request->product);
        if ($chk == 0) {
            $items = [
                'product_id' => $request->product,
                'uom_id' => $request->uomid,
                'qty' => $request->qty,
                'amount' => $request->amount,
            ];

            $tempInsert = $order->masterAssignTemp($items);
            if ($tempInsert) {
                $items = $order->getTempItems();
                return $items;
            } else {
                return 0;
            }
        } else {
            return 2;
        }
    }

    public function getitemsByfinished(Request $request, order $order)
    {
        $items = $order->getTempItems();
        return $items;
    }

    public function getitemsByDetails(Request $request, order $order)
    {
        $items = $order->getTempDetails($request->assign);
        return $items;
    }

    public function getItemQty(Request $request, order $order)
    {
        $result = $order->getItemsQty($request->receipt, $request->finished);
        return $result;
    }

    public function getstatusChanged(Request $request, order $order, master $master, inventory $inventory)
    {


        $items = [
            'master_id' => $request->master,
            'finished_good_id' => $request->finished,
            'receipt_no' => $request->receipt,
            'qty' => $request->qty,
            'received' => '0',
            'date' => date("Y-m-d"),
            'status' => 2,
        ];
        $assignID = $order->insertAssign($items); //Insert into assign  general table

        $tempItems = $order->getTempItemsForInsert();

        foreach ($tempItems as $value) {
            $items = [
                'assign_id' => $assignID,
                'product_id' => $value->product_id,
                'uom_id' => $value->uom_id,
                'qty' => $value->qty,
                'amount' => $value->amount,
            ];

            $result = $order->insertSubAssign($items); //Insert into assign  general table

        }

        $result = $order->getItemsQty($request->receipt, $request->finished);

        if ($result[0]->assignQty == 0) {
            $status = 2;
        } else {
            $status = 1;
        }

        $items = $order->updateItemStatus($request->finished, $request->receipt, $status);
        $rate =  $order->getMasterRate($request->finished, $request->master, $request->receipt);
        $lastbalance = $master->getLastBalance($request->master);
        $lb = $lastbalance - $rate[0]->rate;
        $items = [
            'master_id' => $request->master,
            'receipt_no' => $request->receipt,
            'total_amount' => $rate[0]->rate,
            'debit' => '0',
            'credit' => $rate[0]->rate,
            'balance' => $rate[0]->rate,
            'TotalBalance' => $lb,
            'status_id' => '1',
            'created_at' => date('Y-m-d H:s:i'),
            'updated_at' => date('Y-m-d H:s:i'),
        ];

        $stock = $order->getTempItemsForInsert();
        foreach ($stock as  $value) {
            $deduct = $inventory->invent_stock_detection(session('branch'), $value->product_id, $value->qty);
        }
        $masterInsert = $order->insertIntoMaster($items);
        return $masterInsert;
    }

    public function GetMastersPendingOrders(Request $request, order $order)
    {
        $orders = $order->masterOrderCount($request->id);
        return $orders;
    }

    public function getReceiptitems(Request $request, order $order)
    {
        $result = $order->getReceiptItems($request->id);
        return $result;
    }

    public function getMasterByCategory(Request $request, order $order)
    {
        $result = $order->getMasterByCategory($request->id);
        return $result;
    }

    public function exportPDF(Request $request, order $order, Vendor $vendor)
    {
        if ($request->status == "") {
            $request->status = 1;
        }
        $company = $vendor->company(session('company_id'));
        $ordersResult = $order->getOrders($request->first, $request->second, $request->status, $request->customer, $request->receipt, $request->mode, $request->delFrom, $request->delTo, $request->branch, $request->terminal, $request->payMode);

        $pdf = app('Fpdf');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        // $pdf->Cell(40,10,'Hello World!');

        $pdf->Image(public_path('assets/images/company/' . $company[0]->logo), 10, 10, -200);
        $pdf->SetFont('Arial', 'BU', 18);
        $pdf->MultiCell(0, 10, 'ORDERS REPORT', 0, 'C');
        $pdf->Cell(2, 2, '', 0, 1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, strtoupper($ordersResult[0]->order_status_name) . " ORDERS", 0, 1, 'C'); //Here is center title
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(10, 10, '', 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, $company[0]->name, 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, $company[0]->address, 0, 0, 'L');
        $pdf->Cell(0, 4, 'From : ' . $request->first, 0, 1, 'R');
        // $pdf->Cell(10,10,'',0,1);
        $pdf->Cell(0, 5, $company[0]->ptcl_contact, 0, 0, 'L');
        $pdf->Cell(0, 5, 'To : ' . $request->second, 0, 1, 'R');
        // $pdf->SetFont('Arial','',12);
        // $pdf->Cell(0,8,'Company Address','B',0,'L');
        // $pdf->Cell(0,8,'To : 03-12-2020','B',1,'R');
        $pdf->Cell(0, 4, $company[0]->mobile_contact, 0, 0, 'L');
        $pdf->Cell(0, 4, '', 0, 1, 'R');

        $pdf->Cell(190, 1, '', 'B', 1);

        //Seperate
        $pdf->ln();
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(8, 8, 'Date', 'B', 0, 'C');
        $pdf->Cell(40, 8, 'Receipt No', 'B', 0, 'C');
        $pdf->Cell(50, 8, 'Customer', 'B', 0, 'L');
        $pdf->Cell(50, 8, 'Fbr Number', 'B', 0, 'L');
        $pdf->Cell(20, 8, 'OrderType', 'B', 0, 'C');
        $pdf->Cell(30, 8, 'Total Amount', 'B', 1, 'C');


        foreach ($ordersResult as $key => $value) {
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(8, 8, $value->date, 0, 0, 'C');
            $pdf->Cell(40, 8, $value->receipt_no, 0, 0, 'C');
            $pdf->Cell(50, 8, $value->name, 0, 0, 'L');
            $pdf->Cell(50, 8, $value->fbrInvNumber, 0, 0, 'L');
            $pdf->Cell(20, 8, $value->order_mode, 0, 0, 'C');
            $pdf->Cell(30, 8, number_format($value->total_amount, 2), 0, 1, 'L');
        }



        //save file
        $pdf->Output('temp.pdf', 'I');
    }

    public function getTerminal(order $order, Request $request)
    {
        $result = $order->getTerminal($request->branch);
        return $result;
    }

    public function orderSeen(Request $request)
    {
        if ($request->receiptNo > 0) {
            return DB::table("sales_receipts")->where("receipt_no", $request->receiptNo)->update(["isSeen" => 0, "is_notify" => 0]);
        } else {
            return 0;
        }
    }

    public function assignServiceProvider(Request $request)
    {

        $order = OrderModel::where("id", $request->receiptId)->first();
        $lastbalance = ServiceProviderLedger::where("provider_id", $request->sp)->max('balance');
        $provider = ServiceProvider::find($request->sp);
        $totalCredit = ($order->total_amount * ($provider->percentage_id / 100));

        $sporders = ServiceProviderOrders::create([
            "service_provider_id" =>  $request->sp,
            "receipt_id" => $request->receiptId
        ]);

        if (!empty($sporders)) {
            $spledger = ServiceProviderLedger::create([
                "provider_id" =>  $request->sp,
                "debit" => 0,
                "credit" => $totalCredit,
                "balance" => round($totalCredit + $lastbalance),
                "order_id" => $request->receiptId,
                "receipt_id" => $request->receiptId,
                "receipt_no" => $order->receipt_no,
                "receipt_total_amount" => $order->total_amount,
                "narration" => $request->narration,
            ]);

            if (!empty($spledger)) {
                OrderModel::where("id", $request->receiptId)->update(["status" => 1, "order_mode_id" => 3]);
                return response()->json(["status" => 200, "message" => "Success"]);
            }
        }

        return response()->json(["status" => 500, "message" => "Failed"]);
    }


    public function makeReceiptVoid(Request $request)
    {
        try {
            if ($request->id != "") {
                OrderModel::where("id", $request->id)->update(["void_receipt" => 1, "void_date" => date("Y-m-d H:i:s"), "status" => 12, "void_reason" => $request->reason]);
                $order = OrderModel::findOrFail($request->id);
                $this->sendPushNotification($order->id, $order->receipt_no, $order->terminal_id);
                return response()->json(["status" => 200, "message" => "Receipt has been voided"]);
            } else {
                return response()->json(["status" => 403, "message" => "Order Id is null"]);
            }
        } catch (\Exception $e) {
        }
    }

    public function makeReceiptDelivered(Request $request)
    {
        try {
            if ($request->id != "") {
                OrderModel::where("id", $request->id)->update(["order_delivery_date" => $request->reason, "status" => 4]);
                return response()->json(["status" => 200, "message" => "Receipt has been delivered"]);
            } else {
                return response()->json(["status" => 403, "message" => "Order Id is null"]);
            }
        } catch (\Exception $e) {
        }
    }

    public function sendPushNotification($receiptId, $receiptNo, $terminalId)
    {
        $message = "RID " . $receiptId . ",RNO" . $receiptNo;
        $body = "One Receipt has been void.";
        $tokens = array();
        $title = "Receipt Void";
        $firebaseToken = DB::table("terminal_details")->where("terminal_id", $terminalId)->whereNotNull("device_token")->get("device_token");
        foreach ($firebaseToken as $token) {
            array_push($tokens, $token->device_token);
        }


        $SERVER_API_KEY = 'AAAATXdhnIk:APA91bHFZZbCubOgnG3dihDVsqFbwGGQaBpC6f7BPFMnvpntpOOY88ysAEVAT2puQvdng3Xkd8j4HNVWFp1FQ2rHEe9g3Cv6nSZ7oeMsQtSh2GrJYNIxGHeogmen7TSPqRWHJxrG4QF_';
        $server_api_key_mobile = 'AAAA2dlOr6s:APA91bHGDpYDSZWI0LotnIYZUTpOTA9lLS56jsyB-2hq6Fsq6l0OPBoMYFqePTAbteVFawWzdyZOfMowMf-j8LBL8xJefdnpb_pZRVQHzu5rXykkdLBfPJgcr8gmPhPBDlXMWJy_-uv2';

        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => $title,
                "body" => $body,
                "icon" => "https://retail.sabsoft.com.pk/assets/images/Sabify72.png",
                "content_available" => true,
                "priority" => "high",
                // "click_action" => ,
            ],
            "data" => [
                "par1" => $message,
            ],
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $headers1 = [
            'Authorization: key=' . $server_api_key_mobile,
            'Content-Type: application/json',
        ];

        $chs = curl_init();

        curl_setopt($chs, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($chs, CURLOPT_POST, true);
        curl_setopt($chs, CURLOPT_HTTPHEADER, $headers1);
        curl_setopt($chs, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($chs, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chs, CURLOPT_POSTFIELDS, $dataString);

        $responseOne = curl_exec($chs);
        $response = curl_exec($ch);
        // return json_encode($responseOne).json_encode($response);
    }
}
