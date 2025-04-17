<?php

namespace App\Services;

use App\Customer;
use App\Http\Resources\onlineSalesResource\ProductResource;
use App\Models\Branch;
use App\Models\CustomerAccount;
use App\Models\Order;
use App\Models\OrderMode;
use App\Models\OrderPayment;
use App\Models\OrderStatus;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderOrders;
use App\Models\ServiceProviderRelation;
use App\Models\Terminal;
use App\tax;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getServiceProviders($branch = "")
    {
        $serviceProvider = ServiceProvider::query();

        if (session("roleId") == 2) {
            $serviceProvider->whereIn("branch_id", Branch::where("company_id", session("company_id"))->pluck("branch_id"));
        } else {
            $serviceProvider->where("branch_id", session("branch"));
        }
        if (is_array($branch) && $branch != "" && $branch != "all") {
            $serviceProvider->whereIn("branch_id", $branch);
        } else {
            $serviceProvider->where("branch_id", $branch);
        }
        $serviceProvider->with("serviceprovideruser")->where("status_id", 1)->groupBy('id');
        return $serviceProvider->get();
    }

    public function getOrderStatus()
    {
        return OrderStatus::all();
    }

    public function getOrderModes()
    {
        return OrderMode::whereIn("order_mode_id", [6, 10])->get();
    }

    public function getPaymentModes()
    {
        return OrderPayment::all();
    }

    public function getOrderTaxes()
    {
        return tax::where("company_id", session('company_id'))->where("status_id", 1)->where("show_in_pos", 1)->get();
    }

    public function getOrderDetailsFromItems($from, $to, $branch, $productId)
    {
        return DB::select("SELECT * FROM sales_receipt_details a INNER JOIN sales_receipts b on b.id = a.receipt_id and b.date between ? and ? and b.branch = ? INNER JOIN sales_order_status c on c.order_status_id = b.status where a.item_code = ? group by receipt_id", [$from, $to, $branch, $productId]);
    }

    public function getTerminalsFromBranch($branchId)
    {
        return Terminal::where("branch_id", $branchId)->where("status_id", 1)->get();
    }

    public function getSalesPersonFromBranch($branchId)
    {
        return ServiceProvider::with("serviceprovideruser")->where("branch_id", $branchId)->where("categor_id", 1)->where("status_id", 1)->get();
    }

    /**
     * Retrieve order with all necessary relationships.
     *
     * @param int $orderId
     * @return OrderModel|null
     */
    public function getOrderWithRelations(int $orderId): ?Order
    {
        return Order::with([
            'orderdetails',
            'orderdetails.inventory',
            'orderdetails.itemstatus',
            'orderdetails.statusLogs',
            'orderdetails.statusLogs.status',
            'orderAccount',
            'orderAccountSub',
            'customer',
            'branchrelation',
            'orderStatus',
            'statusLogs',
            'statusLogs.status',
            'statusLogs.branch',
            'statusLogs.user',
            'payment',
            'address',
            'website',
            'salesperson',
            'service.serviceType',
        ])->find($orderId);
    }

    /**
     * Retrieve products for the order.
     *
     * @param int $orderId
     * @param string $websiteType
     * @param int $websiteId
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getOrderProducts(int $orderId, string $websiteType, int $websiteId)
    {
        $products = DB::table('inventory_general')
            ->join('sales_receipt_details', 'sales_receipt_details.item_code', '=', 'inventory_general.id')
            ->where('sales_receipt_details.receipt_id', $orderId)
            ->where('sales_receipt_details.mode', 'inventory-general')
            ->select([
                'sales_receipt_details.receipt_id',
                'sales_receipt_details.item_code',
                'sales_receipt_details.item_name',
                'sales_receipt_details.total_qty',
                'sales_receipt_details.item_price',
                'sales_receipt_details.total_amount',
                'sales_receipt_details.calcu_amount_webcart',
                'sales_receipt_details.receipt_detail_id',
                'sales_receipt_details.discount_value',
                'sales_receipt_details.discount_code',
                'sales_receipt_details.group_id',
                'sales_receipt_details.actual_price',
                'inventory_general.image',
                'inventory_general.url',
            ])
            ->get();

        return ProductResource::customCollection($products, $websiteType, $websiteId);
    }

    /**
     * Calculate total amount received for the order.
     *
     * @param int $orderId
     * @return float
     */
    public function getTotalReceived(int $orderId): float
    {
        return CustomerAccount::where('receipt_no', $orderId)->sum('received');
    }

    /**
     * Retrieve customer ledger details for the order.
     *
     * @param Customer $customer
     * @param int $customerId
     * @param int $orderId
     * @return mixed
     */
    public function getCustomerLedgerDetails(Customer $customer, int $customerId, int $orderId)
    {
        return $customer->LedgerDetailsShowInOrderDetails($customerId, $orderId);
    }

    /**
     * Retrieve service provider details for the order.
     *
     * @param int $orderId
     * @return ServiceProviderRelation|null
     */
    public function getServiceProvider(int $salesPersonId): ?ServiceProviderRelation
    {
        // return ServiceProviderOrders::with('serviceprovider')->where('receipt_id', $orderId)->first();
        return ServiceProviderRelation::with('serviceprovider')->where('user_id', $salesPersonId)->first();
    }

    /**
     * Retrieve service provider details for the order.
     *
     * @param int $orderId
     * @return ServiceProviderRelation|null
     */
    public function getWallet(int $walletId): ?ServiceProviderRelation
    {
        // return ServiceProviderOrders::with('serviceprovider')->where('receipt_id', $orderId)->first();
        return ServiceProviderRelation::with('serviceprovider')->where('user_id', $walletId)->first();
    }
}
