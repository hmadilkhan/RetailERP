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
        $closingDate = date("Y-m-d", strtotime("+1 day", strtotime($from)));
        return DB::table('daily_stock as ds1')
            ->join('inventory_general as inv', 'inv.id', '=', 'ds1.product_id')

            // Opening stock CTE
            ->with('opening_stock_cte as', function ($query) use ($from, $branch) {
                $query->select(
                    'product_id',
                    DB::raw('MIN(Date(created_at)) as opening_date'),
                    'opening_stock'
                )
                    ->from('daily_stock')
                    ->whereDate('created_at', '=', $from)
                    ->where('branch_id', '=', $branch)
                    ->groupBy('product_id', DB::raw('Date(created_at)'));
            })

            // Closing stock CTE
            ->with('closing_stock_cte as', function ($query) use ($closingDate, $branch) {
                $query->select(
                    'product_id',
                    DB::raw('MAX(Date(created_at)) as closing_date'),
                    'opening_stock as closing_stock'
                )
                    ->from('daily_stock')
                    ->whereDate('created_at', '=', $closingDate)
                    ->where('branch_id', '=', $branch)
                    ->groupBy('product_id', DB::raw('Date(created_at)'));
            })

            // Sales CTE
            ->with('sales_cte as', function ($query) use ($from, $to, $branch) {
                $query->select(
                    'srd.item_code as product_id',
                    DB::raw('SUM(srd.total_qty) as sales')
                )
                    ->from('sales_receipt_details as srd')
                    ->join('sales_receipts as sr', 'srd.receipt_id', '=', 'sr.id')
                    ->whereIn('sr.opening_id', function ($subquery) use ($from, $to) {
                        $subquery->select('opening_id')
                            ->from('sales_opening')
                            ->whereBetween('date', [$from, $to]);
                    })
                    ->where('sr.branch', '=', $branch)
                    ->where('sr.order_mode_id', '!=', 2)
                    ->where('sr.is_sale_return', '=', 0)
                    ->groupBy('srd.item_code');
            })

            // Main Query
            ->leftJoin('opening_stock_cte as os', 'ds1.product_id', '=', 'os.product_id')
            ->leftJoin('closing_stock_cte as cs', 'ds1.product_id', '=', 'cs.product_id')
            ->leftJoin('sales_cte as sales', 'ds1.product_id', '=', 'sales.product_id')

            // Select fields
            ->select(
                'ds1.product_id',
                'inv.item_code',
                'inv.product_name',
                'os.opening_date',
                'os.opening_stock',
                'cs.closing_date',
                'cs.closing_stock',
                DB::raw('COALESCE(sales.sales, 0) as sales')
            )

            // Conditions and Grouping
            ->whereBetween(DB::raw('Date(ds1.created_at)'), [$from, $to])
            ->where('ds1.branch_id', '=', $branch)
            ->groupBy('ds1.product_id', 'os.opening_date', 'cs.closing_date')

            // Order by Sales DESC
            ->orderBy('sales.sales', 'DESC')

            // Paginate the result
            ->paginate(10);
        //         return DB::select("WITH opening_stock_cte AS (
        //     SELECT 
        //         product_id, 
        //         MIN(Date(created_at)) AS opening_date, 
        //         opening_stock AS opening_stock
        //     FROM 
        //         daily_stock
        //     WHERE 
        //         Date(created_at) = '$from'
        //         AND branch_id = $branch
        //     GROUP BY 
        //         product_id, Date(created_at)
        // ),
        // closing_stock_cte AS (
        //     SELECT 
        //         product_id, 
        //         MAX(Date(created_at)) AS closing_date, 
        //         opening_stock AS closing_stock
        //     FROM 
        //         daily_stock
        //     WHERE 
        //         Date(created_at) = '$closingDate'
        //         AND branch_id = $branch
        //     GROUP BY 
        //         product_id, Date(created_at)
        // ),
        // sales_cte AS (
        //     SELECT 
        //         srd.item_code AS product_id, 
        //         SUM(srd.total_qty) AS sales
        //     FROM 
        //         sales_receipt_details srd
        //     JOIN sales_receipts sr ON srd.receipt_id = sr.id
        //     WHERE 
        //         sr.opening_id IN (
        //             SELECT opening_id 
        //             FROM sales_opening 
        //             WHERE date BETWEEN '$from' AND '$to'
        //         )
        //         AND sr.branch = $branch AND sr.order_mode_id != 2 and sr.is_sale_return = 0
        //     GROUP BY 
        //         srd.item_code
        // )
        // SELECT 
        //     ds1.product_id,
        //     inv.item_code,
        //     inv.product_name,
        //     os.opening_date,
        //     os.opening_stock,
        //     cs.closing_date,
        //     cs.closing_stock,
        //     COALESCE(sales.sales, 0) AS sales
        // FROM 
        //     daily_stock ds1
        // JOIN inventory_general inv ON inv.id = ds1.product_id
        // JOIN opening_stock_cte os ON ds1.product_id = os.product_id
        // LEFT JOIN closing_stock_cte cs ON ds1.product_id = cs.product_id
        // LEFT JOIN sales_cte sales ON ds1.product_id = sales.product_id
        // WHERE 
        //     Date(ds1.created_at) BETWEEN '$from' AND '$to'
        //     AND ds1.branch_id = $branch
        // GROUP BY 
        //     ds1.product_id, os.opening_date, cs.closing_date
        // Order By 
        // 	sales.sales DESC
        // ");
    }

    public function getStockReport($from, $to, $branch)
    {
        if ($branch == "") {
            $branch = 283;
        }
        $closingDate = date("Y-m-d", strtotime("+1 day", strtotime($from)));
        // Define the CTEs using withExpression()
        $openingStockCte = DB::table('daily_stock')
            ->select('product_id', DB::raw('MIN(Date(created_at)) as opening_date'), 'opening_stock')
            ->whereDate('created_at', '=', $from)
            ->where('branch_id', '=', $branch)
            ->groupBy('product_id', DB::raw('Date(created_at)'))->get();

        $closingStockCte = DB::table('daily_stock')
            ->select('product_id', DB::raw('MAX(Date(created_at)) as closing_date'), 'opening_stock as closing_stock')
            ->whereDate('created_at', '=', $closingDate)
            ->where('branch_id', '=', $branch)
            ->groupBy('product_id', DB::raw('Date(created_at)'))->get();
        // dd($closingStockCte);

        $salesCte = DB::table('sales_receipt_details as srd')
            ->select('srd.item_code as product_id', DB::raw('SUM(srd.total_qty) as sales'))
            ->join('sales_receipts as sr', 'srd.receipt_id', '=', 'sr.id')
            ->whereIn('sr.opening_id', function ($subquery) use ($from, $to) {
                $subquery->select('opening_id')
                    ->from('sales_opening')
                    ->whereBetween('date', [$from, $to]);
            })
            ->where('sr.branch', '=', $branch)
            ->where('sr.order_mode_id', '!=', 2)
            ->where('sr.is_sale_return', '=', 0)
            ->groupBy('srd.item_code')->get();
        // dd($salesCte);
        return [
            "opening" => $openingStockCte,
            "closing" => $closingStockCte,
            "sales" => $salesCte,
        ];
        // Main query
        // return DB::table('daily_stock as ds1')
        //     ->join('inventory_general as inv', 'inv.id', '=', 'ds1.product_id')
        //     ->leftJoinSub($openingStockCte, 'opening_stock_cte', 'ds1.product_id', '=', 'opening_stock_cte.product_id')
        //     // ->leftJoinSub($closingStockCte, 'closing_stock_cte', 'ds1.product_id', '=', 'closing_stock_cte.product_id')
        //     ->leftJoinSub($salesCte, 'sales_cte', 'ds1.product_id', '=', 'sales_cte.product_id')

        //     // Select fields
        //     ->select(
        //         'ds1.product_id',
        //         'inv.item_code',
        //         'inv.product_name',
        //         'opening_stock_cte.opening_date',
        //         'opening_stock_cte.opening_stock',
        //         // 'closing_stock_cte.closing_date',
        //         // 'closing_stock_cte.closing_stock',
        //         DB::raw('COALESCE(sales_cte.sales, 0) as sales')
        //     )

        //     // Conditions and Grouping
        //     ->whereBetween(DB::raw('Date(ds1.created_at)'), [$from, $to])
        //     ->where('ds1.branch_id', '=', $branch)
        //     ->groupBy('ds1.product_id', 'opening_stock_cte.opening_date')//'closing_stock_cte.closing_date'

        //     // Order by Sales DESC
        //     ->orderBy('sales', 'DESC')

        //     // Paginate the result
        //     // ->paginate(10); // Adjust the number of results per page as needed
        //     ->get();
        // ->toSql();
    }
}
