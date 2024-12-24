<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderAccount;
use App\Models\OrderDetails;
use App\Models\OrderSubAccount;
use App\Models\ServiceProviderOrders;
use App\Models\ServiceProviderRelation;
use App\Services\BranchService;
use App\Services\InventoryService;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreOrderBookingController extends Controller
{
    protected $orderService;
    protected $branchService;

    public function __construct(OrderService $orderService, BranchService $branchService)
    {
        $this->orderService = $orderService;
        $this->branchService = $branchService;
    }
    public function index(): View
    {
        return view("orderbooking.pre-order-booking-retail", [
            "branches" => $this->branchService->getBranches(),
            "orderTypes" => $this->orderService->getOrderModes(),
            "payments" => $this->orderService->getPaymentModes(),
            "taxes" => $this->orderService->getOrderTaxes(),
        ]);
    }

    public function getProductPrice(Request $request, InventoryService $inventoryService)
    {
        if ($request->id != "") {
            return $inventoryService->getPriceFromProduct($request->id);
        }

        return response()->json(["status" => 500, "message" => "Product Id is null"]);
    }
    public function getTerminalsAndSalesPerson(Request $request): JsonResponse
    {
        if ($request->branchId != "") {
            return response()->json([
                "status" => 200,
                "terminals" => $this->orderService->getTerminalsFromBranch($request->branchId),
                "salesPersons" => $this->orderService->getSalesPersonFromBranch($request->branchId)
            ]);
        }

        return response()->json([
            "status" => 500,
            "error" => "No Data Found"
        ]);
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            "customerId" => "required",
            "orderTypeId" => "required",
            "paymentId" => "required",
            "branchId" => "required",
            "terminalId" => "required",
        ]);

        try {
            DB::beginTransaction();
            $order = Order::create([
                "receipt_no" => date("YmdHis"),
                "order_mode_id" => $request->orderTypeId,
                "userid" => auth()->user()->id,
                "customer_id" => $request->customerId,
                "payment_id" => $request->paymentId,
                "actual_amount" => $request->subTotal,
                "total_amount" => $request->totalAmount,
                "total_item_qty" => count($request->products),
                "status" => 1,
                "branch" => $request->branchId,
                "terminal_id" => $request->terminalId,
                "sales_person_id" => $request->salespersonId,
                "date" => date("Y-m-d"),
                "time" => date("H:i:s"),
            ]);
            for ($i = 0; $i < count($request->products); $i++) {
                OrderDetails::create([
                    "receipt_id" => $order->id,
                    "item_code" => $request->products[$i],
                    "total_qty" => $request->qty[$i],
                    "total_amount" => $request->amount[$i],
                    "item_price" => $request->price[$i],
                    "item_name" => $request->productnames[$i],
                ]);
            }
            OrderAccount::create([
                "receipt_id" => $order->id,
                "receive_amount" => $request->subTotal,
                "amount_paid_back" => 0,
                "total_amount" => $request->totalAmount,
            ]);
            OrderSubAccount::create([
                "receipt_id" => $order->id,
                "discount_amount" => $request->discountAmount,
                "sales_tax_amount" => $request->taxAmount,
            ]);
            // Checking Service Provider selected or not
            if ($request->salespersonId != "") {
                $sp = ServiceProviderRelation::where("user_id", $request->salespersonId)->first();
                if (!empty($sp)) {
                    ServiceProviderOrders::create([
                        "service_provider_id" => $sp->provider_id,
                        "receipt_id" => $order->id,
                    ]);
                }
            }
            DB::commit();

            return response()->json(["status" => 200, "orderId" => $order->id]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["status" => 500, "error" => $th->getMessage()]);
        }
    }
}
