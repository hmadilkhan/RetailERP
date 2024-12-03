<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\OrderMode;
use App\Models\OrderStatus;
use App\Models\ServiceProvider;
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

    public function getServiceProviders($branch="")
    {
        $serviceProvider = ServiceProvider::query();

        if (session("roleId") == 2) {
            $serviceProvider->whereIn("branch_id", Branch::where("company_id", session("company_id"))->pluck("branch_id"));
        } else {
            $serviceProvider->where("branch_id", session("branch"));
        }
        if (is_array($branch) && $branch != "" && $branch != "all") {
            $serviceProvider->whereIn("branch_id", $branch);
        }else{
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
        return OrderMode::all();
    }

    public function getOrderDetailsFromItems($from,$to,$branch,$productId)
    {
        return DB::select("SELECT * FROM sales_receipt_details a INNER JOIN sales_receipts b on b.id = a.receipt_id and b.date between ? and ? and b.branch = ? INNER JOIN sales_order_status c on c.order_status_id = b.status where a.item_code = ? group by receipt_id",[$from,$to,$branch,$productId]);
    }
}
