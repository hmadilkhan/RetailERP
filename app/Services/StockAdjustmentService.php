<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\StockReport;
use Illuminate\Support\Facades\DB;

class StockAdjustmentService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getStockAdjustmentLists($from, $to, $code, $name, $branch)
    {
        //     $query = StockReport::query();
        //     $query->with("products");
        //     if ($from != "" && $to != "") {
        //         $query->whereBetween(DB::raw('DATE(date)'), [$from, $to]);
        //     }
        //     if ($code != "") {
        //         $query->whereHas("products", function ($q) use ($code) {
        //             $q->where("item_code", $code);
        //         });
        //     }
        //     if ($name != "") {
        //         $query->whereHas("products", function ($q) use ($name) {
        //             $q->where('product_name', 'like', '%' . $name . '%');
        //         });
        //     }
        //     if (session("roleId") == 2) {
        //         if ($branch != "") {
        //             $query->where("branch_id", $branch);
        //         } else {
        //             $query->whereIn("branch_id", Branch::where("company_id", session("company_id"))->pluck("branch_id"));
        //         }
        //     } else {
        //         $query->where("branch_id", session("branch"));
        //     }
        //     $query->where('narration', 'like', '%(Stock Adjustment)%');
        //     return $query->paginate(50);
        // dd($query->get());

        $query = StockReport::with('products','productstock',"productstock.grn.user")
            ->when(!empty($from) && !empty($to), function ($q) use ($from, $to) {
                $q->whereBetween(DB::raw('DATE(date)'), [$from, $to]);
            })
            ->when(!empty($code), function ($q) use ($code) {
                $q->whereHas('products', function ($q) use ($code) {
                    $q->where('item_code', $code);
                });
            })
            ->when(!empty($name), function ($q) use ($name) {
                $q->whereHas('products', function ($q) use ($name) {
                    $q->where('product_name', 'like', '%' . $name . '%');
                });
            })
            ->when(session('roleId') == 2, function ($q) use ($branch) {
                $q->when(!empty($branch), function ($q) use ($branch) {
                    $q->where('branch_id', $branch);
                }, function ($q) {
                    $q->whereIn('branch_id', Branch::where('company_id', session('company_id'))->pluck('branch_id'));
                });
            }, function ($q) {
                $q->where('branch_id', session('branch'));
            })
            ->where('narration', 'like', '%(Stock Adjustment)%')
            ->paginate(50);

        return $query;
    }
}
