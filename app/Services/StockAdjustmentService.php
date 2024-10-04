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

    public function stockReport()
    {
        return DB::select("WITH opening_stock_cte AS (
    SELECT 
        product_id, 
        MIN(Date(created_at)) AS opening_date, 
        opening_stock AS opening_stock
    FROM 
        daily_stock
    WHERE 
        Date(created_at) BETWEEN '2024-09-24' AND '2024-09-24'
        AND branch_id = 283
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
        Date(created_at) BETWEEN '2024-09-24' AND '2024-09-24'
        AND branch_id = 283
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
            WHERE date BETWEEN '2024-09-24' AND '2024-09-24'
        )
        AND sr.branch = 283 AND sr.order_mode_id != 2
    GROUP BY 
        srd.item_code
)
SELECT 
    ds1.product_id,
    os.opening_date,
    os.opening_stock,
    cs.closing_date,
    cs.closing_stock,
    COALESCE(sales.sales, 0) AS sales
FROM 
    daily_stock ds1
JOIN opening_stock_cte os ON ds1.product_id = os.product_id
JOIN closing_stock_cte cs ON ds1.product_id = cs.product_id
LEFT JOIN sales_cte sales ON ds1.product_id = sales.product_id
WHERE 
    Date(ds1.created_at) BETWEEN '2024-09-23' AND '2024-09-24'
    AND ds1.branch_id = 283
GROUP BY 
    ds1.product_id, os.opening_date, cs.closing_date
Order By 
	sales.sales DESC
");
        // return DB::select("SELECT 
        //                     product_id,
        //                     MIN(Date(created_at)) AS opening_date,
        //                     (SELECT SUM(total_qty) FROM `sales_receipt_details` where receipt_id IN (Select id from sales_receipts where opening_id IN ( Select opening_id from sales_opening where date between  '2024-09-23' AND '2024-09-24') and branch = 283 ) and item_code = product_id) as sales,
        //                     (SELECT opening_stock FROM daily_stock ds2 
        //                     WHERE ds2.product_id = ds1.product_id 
        //                     AND Date(ds2.created_at) = MIN(Date(ds1.created_at))) AS opening_stock,
                            
        //                     MAX(Date(created_at)) AS closing_date,
        //                     (SELECT opening_stock FROM daily_stock ds2 
        //                     WHERE ds2.product_id = ds1.product_id 
        //                     AND Date(ds2.created_at) = MAX(Date(ds1.created_at))) AS closing_stock
        //                 FROM 
        //                     daily_stock ds1
        //                 WHERE 
        //                     Date(created_at) BETWEEN '2024-09-23' AND '2024-09-24'  -- Replace with the date range
        //                 AND
        //                     branch_id = 283
        //                 GROUP BY 
        //                     product_id");
    }
}
