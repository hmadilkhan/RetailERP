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
        $query = StockReport::with('products', 'productstock', "productstock.grn.user")
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

    public function stockReport($from, $to, $branch)
    {
        if ($branch == "") {
            $branch = 283;
        }
        $closingDate = date("Y-m-d", strtotime("+1 day",strtotime($from)));
        return DB::select("WITH opening_stock_cte AS (
    SELECT 
        product_id, 
        MIN(Date(created_at)) AS opening_date, 
        opening_stock AS opening_stock
    FROM 
        daily_stock
    WHERE 
        Date(created_at) = '$from'
        AND branch_id = $branch
    GROUP BY 
        product_id, Date(created_at)
),
closing_stock_cte AS (
    SELECT 
        product_id, 
        MAX(Date(created_at)) AS closing_date, 
        opening_stock AS closing_stock
    FROM 
        daily_stock
    WHERE 
        Date(created_at) = '$closingDate'
        AND branch_id = $branch
    GROUP BY 
        product_id, Date(created_at)
),
sales_cte AS (
    SELECT 
        srd.item_code AS product_id, 
        SUM(srd.total_qty) AS sales
    FROM 
        sales_receipt_details srd
    JOIN sales_receipts sr ON srd.receipt_id = sr.id
    WHERE 
        sr.opening_id IN (
            SELECT opening_id 
            FROM sales_opening 
            WHERE date BETWEEN '$from' AND '$to'
        )
        AND sr.branch = $branch AND sr.order_mode_id != 2 and sr.is_sale_return = 0
    GROUP BY 
        srd.item_code
)
SELECT 
    ds1.product_id,
    inv.item_code,
    inv.product_name,
    os.opening_date,
    os.opening_stock,
    cs.closing_date,
    cs.closing_stock,
    COALESCE(sales.sales, 0) AS sales
FROM 
    daily_stock ds1
JOIN inventory_general inv ON inv.id = ds1.product_id
JOIN opening_stock_cte os ON ds1.product_id = os.product_id
LEFT JOIN closing_stock_cte cs ON ds1.product_id = cs.product_id
LEFT JOIN sales_cte sales ON ds1.product_id = sales.product_id
WHERE 
    Date(ds1.created_at) BETWEEN '$from' AND '$to'
    AND ds1.branch_id = $branch
GROUP BY 
    ds1.product_id, os.opening_date, cs.closing_date
Order By 
	sales.sales DESC
");
    }
}
