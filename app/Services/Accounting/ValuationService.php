<?php

namespace App\Services\Accounting;

use App\Services\Accounting\Posting\Posters\PurchaseValuation;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;

/**
 * Phase 1.4: moving weighted-average cost — company + product (branch ke beech transfer value nahi badalta).
 * POS ki sales_receipt_details.total_cost aur inventory_stock.balance pe bharosa nahi (research 2026-10-12:
 * 53% lines cost 0, void restock lots sale price pe, lots sirf ~20% terminals pe ghatte hain). Isliye har run
 * start date se source documents dobara padh kar average banata hai:
 *   shuruat  : start se pehle ke lots — qty = active balance (99,999+ nakli qty chhod kar), cost = cost > 0 lots ka qty-weighted average,
 *              warna price list ki cost (0 < cost < retail)
 *   IN       : GRN (PO price × ratio, PurchaseValuation), opening / CSV / positive adjustment (cost > 0 ho to, warna average pe),
 *              web sales return (average pe — COGS ulta)
 *   OUT      : sale (recipe ho to ingredients), negative adjustment, purchase return — sab average pe
 *   Nahi     : void restock 'Stock Return' lots — na opening average me, na IN (void sale pehle hi shamil nahi; ye lot
 *              sale price pe bante hain), branch transfers (company level pe 0)
 *   Guard    : cost > retail price × MAX_COST_TO_RETAIL ignore (bekaar data), placeholder qty (99,999+) ignore
 * Ek din me pehle IN phir OUT. Cost pata na ho to line "unvalued" (COGS 0) ginti hoti hai — Inventory Valuation screen pe.
 * Deals: aam product ki tarah — apni cost na ho to khud unvalued (inventory_general.is_deal bharosemand nahi: Wild ka
 * 'Shirt Cotton' is_deal = 1 hai magar 500 cost ke lots hain). Recipe wastage aur weight_qty (secondary unit) abhi nahi ginte.
 */
class ValuationService
{
    const PLACEHOLDER_QTY = 99999;
    /** Is se zyada (retail price ka guna) cost bekaar data maani jati hai — e.g. company 19 me arbon ki cost */
    const MAX_COST_TO_RETAIL = 10;

    private array $retail = [];

    private static array $computed = []; // preview / same run cache: company => result

    public function __construct(private PurchaseValuation $purchases)
    {
    }

    /** Hisaab lagao aur save karo (automatic posting se pehle). */
    public function refresh(int $companyId, string $start, string $to): array
    {
        $result = $this->compute($companyId, $start, $to);
        $now = now();

        DB::transaction(function () use ($companyId, $start, $to, $result, $now) {
            DB::table('stock_valuation_daily')->where('company_id', $companyId)->whereBetween('date', [$start, $to])->delete();
            foreach (array_chunk($result['daily'], 500) as $chunk) {
                DB::table('stock_valuation_daily')->insert(array_map(fn ($r) => $r + ['company_id' => $companyId], $chunk));
            }

            DB::table('product_valuations')->where('company_id', $companyId)->delete();
            foreach (array_chunk($result['products'], 500) as $chunk) {
                DB::table('product_valuations')->insert(array_map(fn ($r) => $r + ['company_id' => $companyId, 'computed_at' => $now], $chunk));
            }
        });

        return $result;
    }

    /** branch:date => cogs — SalesPoster ke liye. Is process me hisaab laga ho to wahi, warna table se. */
    public function cogsByBranchDate(int $companyId, string $from, string $to): array
    {
        if (isset(self::$computed[$companyId])) {
            $out = [];
            foreach (self::$computed[$companyId]['daily'] as $r) {
                if ($r['date'] >= $from && $r['date'] <= $to) {
                    $key = $r['branch_id'] . ':' . $r['date'];
                    $out[$key] = ($out[$key] ?? 0) + $r['cogs'];
                }
            }
            return $out;
        }

        return DB::table('stock_valuation_daily')->where('company_id', $companyId)->whereBetween('date', [$from, $to])
            ->groupBy('branch_id', 'date')->selectRaw('branch_id, date, SUM(cogs) AS cogs')->get()
            ->mapWithKeys(fn ($r) => [$r->branch_id . ':' . substr($r->date, 0, 10) => (float) $r->cogs])->all();
    }

    /** Sirf hisaab (kuch save nahi) — preview / dry run. */
    public function compute(int $companyId, string $start, string $to): array
    {
        $branches = SourceQuery::branchIds($companyId);
        $in = implode(',', $branches);
        $this->retail = DB::table('inventory_price as p')->join('inventory_general as g', 'g.id', '=', 'p.product_id')
            ->where('g.company_id', $companyId)->where('p.status_id', 1)->whereRaw('(p.retail_price + 0) > 0')
            ->groupBy('p.product_id')->selectRaw('p.product_id, MAX(p.retail_price + 0) AS retail')
            ->pluck('retail', 'p.product_id')->map(fn ($r) => (float) $r)->all();

        [$state, $fallback] = $this->openingState($in, $start);
        $movements = array_merge(
            $this->stockReportMovements($in, $start, $to),
            $this->grnMovements($in, $start, $to),
            $this->purchaseReturnMovements($in, $start, $to),
            $this->saleMovements($companyId, $in, $start, $to)
        );
        return self::$computed[$companyId] = $this->run($state, $fallback, $movements);
    }

    /** Moving average ka asal hisaab — source se bilkul alag (test isi ko nakli movements deta hai). */
    public function run(array $state, array $fallback, array $movements): array
    {
        // tareekh → pehle IN (0) phir OUT / return (1) → waqt
        usort($movements, fn ($a, $b) => [$a['date'], $a['priority'], $a['time']] <=> [$b['date'], $b['priority'], $b['time']]);

        $daily = [];
        $unvalued = [];
        foreach ($movements as $m) {
            if ($m['kind'] === 'in') {
                $this->receive($state, $m['product'], $m['qty'], $m['cost'], $m['source'], $m['date']);
                continue;
            }

            // OUT / return: har component (recipe ho to ingredients) ka us waqt ka average
            $cost = 0.0;
            $isUnvalued = $m['deal'];
            foreach ($m['components'] as [$product, $qty, $fallbackUnit]) {
                $avg = $this->avg($state, $product) ?: (float) ($fallback[$product] ?? 0) ?: (float) $fallbackUnit;
                if ($avg <= 0) {
                    $isUnvalued = true;
                }
                $cost += $qty * $avg;
                if ($m['kind'] === 'return') {
                    $this->receive($state, $product, $qty, $avg ?: null, 'return', $m['date']); // wapas usi average pe
                } else {
                    $this->issue($state, $product, $qty, $avg);
                }
            }

            if ($m['sale']) {
                $sign = $m['kind'] === 'return' ? -1 : 1;
                $key = $m['branch'] . '|' . $m['date'] . '|' . $m['product'];
                $daily[$key] ??= ['branch_id' => $m['branch'], 'date' => $m['date'], 'product_id' => $m['product'], 'qty' => 0, 'cogs' => 0, 'unvalued_lines' => 0];
                $daily[$key]['qty'] += $sign * $m['qty'];
                $daily[$key]['cogs'] += $sign * $cost;
                if ($isUnvalued) {
                    $daily[$key]['unvalued_lines']++;
                    $unvalued[$m['product']] = ($unvalued[$m['product']] ?? 0) + 1;
                }
            }
        }

        $daily = array_map(function ($r) {
            $r['qty'] = round($r['qty'], 4);
            $r['cogs'] = round($r['cogs'], 2);
            return $r;
        }, array_values($daily));

        $products = [];
        foreach ($state as $product => $s) {
            $products[] = [
                'product_id' => $product,
                'qty' => round($s['qty'], 4),
                'avg_cost' => round($s['avg'], 4),
                'value' => round($s['qty'] * $s['avg'], 2),
                'cost_source' => $s['source'],
                'last_in_date' => $s['last_in'],
                'unvalued_sales' => $unvalued[$product] ?? 0,
            ];
        }

        return ['daily' => $daily, 'products' => $products, 'movements' => count($movements)];
    }

    // ---------------------------------------------------------------- moving average

    private function receive(array &$state, int $product, float $qty, ?float $cost, string $source, string $date): void
    {
        $s = &$state[$product];
        $s ??= ['qty' => 0.0, 'avg' => 0.0, 'source' => 'none', 'last_in' => null];
        if ($cost !== null && $cost > 0) {
            // stock khatam / minus tha to naya average yahi cost, warna qty-weighted blend
            $s['avg'] = $s['qty'] <= 0 ? $cost : ($s['qty'] * $s['avg'] + $qty * $cost) / ($s['qty'] + $qty);
            if ($source !== 'return') {
                $s['source'] = $source;
            }
        }
        $s['qty'] += $qty; // cost nahi to qty badhti hai, average wahi
        $s['last_in'] = $date;
    }

    private function issue(array &$state, int $product, float $qty, float $avg): void
    {
        $s = &$state[$product];
        $s ??= ['qty' => 0.0, 'avg' => $avg, 'source' => 'none', 'last_in' => null];
        $s['qty'] -= $qty; // minus stock bhi isi average pe; average nahi badalta
    }

    private function avg(array $state, int $product): float
    {
        return (float) ($state[$product]['avg'] ?? 0);
    }

    /** Cost theek lagti ho to wahi, warna null (bekaar / 0) */
    private function sane(int $product, $cost): ?float
    {
        $cost = (float) $cost;
        if ($cost <= 0) {
            return null;
        }
        $retail = $this->retail[$product] ?? 0;
        return $retail > 0 && $cost > $retail * self::MAX_COST_TO_RETAIL ? null : $cost;
    }

    // ---------------------------------------------------------------- sources

    /** start se pehle ke lots: [state, price-list fallback] */
    private function openingState(string $in, string $start): array
    {
        // void restock lot = jis ki 'Stock Return' (mode 1) report row hai — sale price pe bana, cost nahi
        $lots = DB::select("
            SELECT s.product_id, s.status_id, s.qty + 0 AS qty, s.balance + 0 AS balance, s.cost_price + 0 AS cost
            FROM inventory_stock s
            WHERE s.branch_id IN ({$in}) AND LEFT(s.date, 10) < ?
              AND NOT EXISTS (SELECT 1 FROM inventory_stock_report_table r
                              WHERE r.foreign_id = s.stock_id AND r.narration = 'Stock Return' AND r.adjustment_mode = '1')",
            [$start]);

        $fallback = [];
        foreach (DB::table('inventory_price as p')->join('inventory_stock as s', 's.product_id', '=', 'p.product_id')
            ->whereRaw("s.branch_id IN ({$in})")->where('p.status_id', 1)
            ->whereRaw('(p.cost_price + 0) > 0 AND (p.cost_price + 0) < (p.retail_price + 0)')
            ->groupBy('p.product_id')->selectRaw('p.product_id, MAX(p.cost_price + 0) AS cost')->get() as $p) {
            $fallback[(int) $p->product_id] = (float) $p->cost;
        }

        $agg = [];
        foreach ($lots as $lot) {
            $product = (int) $lot->product_id;
            $a = &$agg[$product];
            $a ??= ['qty' => 0.0, 'cost_value' => 0.0, 'cost_qty' => 0.0];
            if ((int) $lot->status_id === 1 && $lot->balance < self::PLACEHOLDER_QTY) {
                $a['qty'] += (float) $lot->balance;
            }
            $cost = $this->sane($product, $lot->cost);
            if ($cost !== null && $lot->qty > 0 && $lot->qty < self::PLACEHOLDER_QTY) {
                $a['cost_value'] += $lot->qty * $cost;
                $a['cost_qty'] += (float) $lot->qty;
            }
            unset($a);
        }

        $state = [];
        foreach ($agg as $product => $a) {
            $avg = $a['cost_qty'] > 0 ? $a['cost_value'] / $a['cost_qty'] : (float) ($fallback[$product] ?? 0);
            $state[$product] = [
                'qty' => $a['qty'],
                'avg' => $avg,
                'source' => $a['cost_qty'] > 0 ? 'opening' : ($avg > 0 ? 'price_list' : 'none'),
                'last_in' => null,
            ];
        }
        return [$state, $fallback];
    }

    /** Opening / CSV / adjustments (stock report) */
    private function stockReportMovements(string $in, string $start, string $to): array
    {
        $floor = SourceQuery::idFloor('inventory_stock_report_table', 'stock_report_id', 'date', $start);
        $rows = DB::select("
            SELECT r.product_id, DATE(r.date) AS d, ABS(r.qty) AS qty, r.narration, r.adjustment_mode,
                   CASE WHEN r.narration = 'Stock Opening' THEN r.cost
                        ELSE COALESCE(NULLIF(CAST(NULLIF(s.cost_price, '') AS DECIMAL(18,4)), 0), r.cost) END AS cost
            FROM inventory_stock_report_table r
            LEFT JOIN inventory_stock s ON s.stock_id = r.foreign_id AND r.narration <> 'Stock Opening'
            WHERE r.stock_report_id >= ? AND r.branch_id IN ({$in}) AND DATE(r.date) BETWEEN ? AND ?
              AND (r.narration IN ('Stock Opening', 'Stock Openend from csv file') OR r.narration LIKE '(Stock Adjustment)%')",
            [$floor, $start, $to]);

        $out = [];
        foreach ($rows as $r) {
            if ($r->qty <= 0 || $r->qty >= self::PLACEHOLDER_QTY) {
                continue;
            }
            $isOut = str_starts_with((string) $r->narration, '(Stock Adjustment)') && (string) $r->adjustment_mode === '0';
            $out[] = $isOut
                ? $this->out((int) $r->product_id, (float) $r->qty, $r->d, '00:00:00', null)
                : $this->in((int) $r->product_id, (float) $r->qty, $this->sane((int) $r->product_id, $r->cost), str_starts_with((string) $r->narration, '(Stock') ? 'adjustment' : 'opening', $r->d);
        }
        return $out;
    }

    private function grnMovements(string $in, string $start, string $to): array
    {
        $floor = SourceQuery::idFloor('inventory_stock_report_table', 'stock_report_id', 'date', $start);
        $rows = DB::select("
            SELECT r.foreign_id AS po, r.product_id, DATE(r.date) AS d, r.qty
            FROM inventory_stock_report_table r
            JOIN purchase_general_details g ON g.purchase_id = r.foreign_id
            WHERE r.stock_report_id >= ? AND r.narration = 'Stock Purchase through Purchase Order'
              AND g.branch_id IN ({$in}) AND DATE(r.date) BETWEEN ? AND ?", [$floor, $start, $to]);

        return array_map(function ($r) {
            $po = $this->purchases->forPo((int) $r->po);
            $unit = ($po['prices'][(int) $r->product_id] ?? 0) * $po['ratio'];
            return $this->in((int) $r->product_id, (float) $r->qty, $this->sane((int) $r->product_id, $unit), 'purchase', $r->d);
        }, $rows);
    }

    private function purchaseReturnMovements(string $in, string $start, string $to): array
    {
        $rows = DB::select("
            SELECT pr.item_code, pr.quantity,
                (SELECT DATE(MIN(r.date)) FROM inventory_stock_report_table r
                  WHERE r.foreign_id = pr.purchase_id AND r.narration = 'Stock Return'
                    AND r.product_id = pr.item_code AND r.adjustment_mode IS NULL) AS d
            FROM purchase_return_itemdetails pr
            JOIN purchase_general_details g ON g.purchase_id = pr.purchase_id
            WHERE g.branch_id IN ({$in})");

        $out = [];
        foreach ($rows as $r) {
            if ($r->d && $r->d >= $start && $r->d <= $to) {
                $out[] = $this->out((int) $r->item_code, (float) $r->quantity, $r->d, '00:00:00', null);
            }
        }
        return $out;
    }

    /** Sales (status 4) + web returns (status 14). Recipe → ingredients, deal → unvalued. */
    private function saleMovements(int $companyId, string $in, string $start, string $to): array
    {
        $recipes = $this->recipes($in);

        $rows = DB::select("
            SELECT r.branch, r.date, COALESCE(r.time, '00:00:00') AS time, r.status, d.item_code, d.total_qty
            FROM sales_receipts r
            JOIN sales_receipt_details d ON d.receipt_id = r.id
            WHERE r.branch IN ({$in}) AND r.date BETWEEN ? AND ?
              AND ((r.status = 4 AND r.void_receipt = 0) OR (r.status = 14 AND r.is_sale_return = 1))", [$start, $to]);

        $out = [];
        foreach ($rows as $r) {
            $product = (int) $r->item_code;
            $qty = (float) $r->total_qty;
            if ($qty <= 0 || !$product) {
                continue;
            }
            $components = $recipes[$product][(int) $r->branch] ?? $recipes[$product][0] ?? [[$product, 1.0, 0.0]];

            $m = $this->out($product, $qty, $r->date, $r->time, (int) $r->branch, true);
            $m['components'] = array_map(fn ($c) => [$c[0], $c[1] * $qty, $c[2]], $components);
            if ((int) $r->status === 14) {
                $m['kind'] = 'return'; // web sales return: maal average pe wapas, COGS minus
            }
            $out[] = $m;
        }
        return $out;
    }

    /** product => [branch => [[ingredient, usage per unit, fallback unit cost]]] (0 = koi bhi branch) */
    private function recipes(string $in): array
    {
        $rows = DB::select("
            SELECT g.product_id, g.branch_id, d.item_id, (d.usage_qty + 0) AS usage_qty, (d.cost + 0) AS cost
            FROM recipy_general g JOIN recipy_details d ON d.recipy_id = g.recipy_id
            WHERE g.status_id = 1 AND g.branch_id IN ({$in}) AND (d.usage_qty + 0) > 0");

        $out = [];
        foreach ($rows as $r) {
            $line = [(int) $r->item_id, (float) $r->usage_qty, $r->usage_qty > 0 ? $r->cost / $r->usage_qty : 0.0];
            $out[(int) $r->product_id][(int) $r->branch_id][] = $line;
            $out[(int) $r->product_id][0] ??= [];
            if (count($out[(int) $r->product_id][0]) < count($out[(int) $r->product_id][(int) $r->branch_id])) {
                $out[(int) $r->product_id][0][] = $line;
            }
        }
        return $out;
    }

    public function in(int $product, float $qty, ?float $cost, string $source, string $date): array
    {
        return ['kind' => 'in', 'priority' => 0, 'product' => $product, 'qty' => $qty, 'cost' => $cost, 'source' => $source, 'date' => $date, 'time' => '00:00:00'];
    }

    public function out(int $product, float $qty, string $date, string $time, ?int $branch, bool $sale = false, bool $deal = false): array
    {
        return ['kind' => 'out', 'priority' => 1, 'product' => $product, 'qty' => $qty, 'date' => $date, 'time' => (string) $time,
            'branch' => $branch, 'sale' => $sale, 'deal' => $deal, 'components' => [[$product, $qty, 0.0]]];
    }
}
