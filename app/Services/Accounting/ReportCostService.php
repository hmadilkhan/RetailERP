<?php

namespace App\Services\Accounting;

use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Purani reports (app/report.php) ke liye Phase 1.4 ki moving-average cost — sirf tab jab company ki accounting on ho
 * aur valuation us period ko cover karti ho (report ki from-date >= posting_start_date). Warna null → report apna
 * purana hisaab (sales_receipt_details.total_cost / AVG(inventory_stock.cost_price)) chalati hai.
 * Data ValuationService::refresh() har posting run (15 min) me likhta hai.
 */
class ReportCostService
{
    private static array $start = [];
    private static array $avg = [];

    /** posting_start_date (Y-m-d) agar valuation data maujood hai, warna null */
    public static function startDate(int $companyId): ?string
    {
        if (!array_key_exists($companyId, self::$start)) {
            try {
                $setting = DB::table('accounting_settings')->where('company_id', $companyId)->where('enabled', 1)->first();
                $has = $setting && $setting->posting_start_date
                    && DB::table('product_valuations')->where('company_id', $companyId)->exists();
                self::$start[$companyId] = $has ? substr($setting->posting_start_date, 0, 10) : null;
            } catch (Throwable $e) {
                self::$start[$companyId] = null; // tables na hon (migration se pehle)
            }
        }
        return self::$start[$companyId];
    }

    /** COGS ka total — branches + receipt date. null = valuation lagu nahi */
    public static function cogsTotal(int $companyId, array $branchIds, string $from, string $to): ?float
    {
        if (!self::covers($companyId, $from)) {
            return null;
        }
        return (float) DB::table('stock_valuation_daily')->where('company_id', $companyId)
            ->whereIn('branch_id', $branchIds)->whereBetween('date', [$from, $to])->sum('cogs');
    }

    /** din-wise COGS: [['date' => Y-m-d, 'cogs' => float], ...]. null = valuation lagu nahi */
    public static function cogsByDate(int $companyId, array $branchIds, string $from, string $to): ?array
    {
        if (!self::covers($companyId, $from)) {
            return null;
        }
        return DB::table('stock_valuation_daily')->where('company_id', $companyId)
            ->whereIn('branch_id', $branchIds)->whereBetween('date', [$from, $to])
            ->groupBy('date')->orderBy('date')->selectRaw('date, SUM(cogs) AS cogs')->get()
            ->map(fn ($r) => ['date' => substr($r->date, 0, 10), 'cogs' => (float) $r->cogs])->all();
    }

    /** product_id => moving average cost (sirf jin ki cost pata hai). Valuation na ho to [] */
    public static function averageCosts(int $companyId): array
    {
        if (!self::startDate($companyId)) {
            return [];
        }
        return self::$avg[$companyId] ??= DB::table('product_valuations')->where('company_id', $companyId)
            ->where('cost_source', '!=', 'none')->where('avg_cost', '>', 0)
            ->pluck('avg_cost', 'product_id')->map(fn ($v) => (float) $v)->all();
    }

    private static function covers(int $companyId, string $from): bool
    {
        $start = self::startDate($companyId);
        return $start !== null && substr($from, 0, 10) >= $start;
    }
}
