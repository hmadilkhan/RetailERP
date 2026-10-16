<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Phase 4.3: branch + product ki roz ki bikri ka andaza (Reorder Suggestions isi se mangwata hai).
 *   Haftawar bikri (pichle 26 hafte, mukammal din — aaj nahi; web return minus; recipe → ingredients).
 *   Rate = haftawar bikri ka weighted average (naya hafta zyada wazan, half-life 2 hafte) ÷ 7; naya product = pehli bikri
 *   se pehle ke khali hafte nahi gine jate.
 *   Trend = pichle 4 hafte vs us se pehle ke 8 hafte.
 *   Accuracy = 4, 8, 12, 16 hafte pehle yahi tareeqa chalaya hota to agle 4 hafton ki asal bikri se kitna farq (WAPE) —
 *   saath me purani simple average (pichle 28 din) ka farq, taake dikhe forecast behtar hai ya nahi.
 * Live backtest (2026-10-16, 9 busy branches × 4 waqt): half-life 2 = 60.7% farq vs simple average 63.5% (7 / 9 branches
 * behtar); 26 hafte ki seedhi average 80%. Pichle saal ka season factor aazmaya — farq badha (63.1%), isliye nahi lagaya.
 * Data bahut companies ka thin hai — jis product ki bikri hi nahi us ka rate 0 (min / reminder pe hi reorder).
 */
class DemandForecastService
{
    const WEEKS = 26;
    const HALF_LIFE = 2;
    const BACKTEST_WEEKS = 4;            // har backtest ka horizon
    const BACKTEST_ORIGINS = [4, 8, 12, 16]; // kitne hafte pehle se
    const MIN_ACTUAL = 50;               // is se kam bikri pe accuracy nahi dikhate

    public function __construct(private ReplenishmentService $replenishment)
    {
    }

    /**
     * @return array{rates: array<int, array{daily: float, trend: ?string}>, accuracy: ?array}
     */
    public function forecast(int $branchId, int $horizonDays = 14): array
    {
        $end = date('Y-m-d', strtotime('-1 day'));
        $key = 'demand-forecast:' . $branchId . ':' . $horizonDays . ':' . $end;
        return Cache::remember($key, 3600, fn () => $this->compute($branchId, $horizonDays, $end));
    }

    public function forget(int $branchId, int $horizonDays = 14): void
    {
        Cache::forget('demand-forecast:' . $branchId . ':' . $horizonDays . ':' . date('Y-m-d', strtotime('-1 day')));
    }

    public function compute(int $branchId, int $horizonDays, string $end): array
    {
        $weeksBack = self::WEEKS + max(self::BACKTEST_ORIGINS);
        $start = date('Y-m-d', strtotime($end . ' -' . ($weeksBack * 7 - 1) . ' days'));

        // product → [hafta (0 = sab se naya) => qty]
        $raw = DB::select("
            SELECT d.item_code AS product_id, FLOOR(DATEDIFF(?, r.date) / 7) AS wk,
                   SUM(CASE WHEN r.status = 14 THEN -(d.total_qty + 0) ELSE (d.total_qty + 0) END) AS qty
            FROM sales_receipts r
            JOIN sales_receipt_details d ON d.receipt_id = r.id
            WHERE r.branch = ? AND r.date BETWEEN ? AND ? AND r.date LIKE '____-__-__'
              AND ((r.status = 4 AND r.void_receipt = 0) OR (r.status = 14 AND r.is_sale_return = 1))
            GROUP BY d.item_code, wk", [$end, $branchId, $start, $end]);

        $byWeek = [];
        foreach ($raw as $r) {
            $byWeek[(int) $r->wk][(int) $r->product_id] = (float) $r->qty;
        }
        $recipes = $this->replenishment->recipeMap($branchId, array_values(array_unique(array_map(fn ($r) => (int) $r->product_id, $raw))));
        $weeks = []; // product => [wk => qty] (ingredients samet)
        foreach ($byWeek as $wk => $sold) {
            foreach ($this->replenishment->explode($recipes, $sold) as $pid => $qty) {
                $weeks[$pid][$wk] = ($weeks[$pid][$wk] ?? 0) + $qty;
            }
        }

        $rates = [];
        foreach ($weeks as $pid => $w) {
            $rates[$pid] = ['daily' => round($this->rate($w, 0), 4), 'trend' => $this->trend($w)];
        }

        return ['rates' => $rates, 'accuracy' => $this->backtest($weeks)];
    }

    /** weighted average roz ki bikri, hafta $offset se purane WEEKS hafte */
    public function rate(array $w, int $offset): float
    {
        $last = null; // sab se purana hafta jis me bikri hui (product ki shuruat)
        for ($k = $offset + self::WEEKS - 1; $k >= $offset; $k--) {
            if (($w[$k] ?? 0) > 0) {
                $last = $k;
                break;
            }
        }
        if ($last === null) {
            return 0.0;
        }
        $num = 0.0;
        $den = 0.0;
        for ($k = $offset; $k <= $last; $k++) {
            $weight = 0.5 ** (($k - $offset) / self::HALF_LIFE);
            $num += $weight * ($w[$k] ?? 0);
            $den += $weight;
        }
        return max(0.0, $num / $den / 7);
    }

    private function trend(array $w): ?string
    {
        $recent = (($w[0] ?? 0) + ($w[1] ?? 0) + ($w[2] ?? 0) + ($w[3] ?? 0)) / 4;
        $before = 0.0;
        for ($k = 4; $k < 12; $k++) {
            $before += $w[$k] ?? 0;
        }
        $before /= 8;
        if ($before <= 0) {
            return $recent > 0 ? 'new' : null;
        }
        $ratio = $recent / $before;
        return $ratio > 1.2 ? 'up' : ($ratio < 0.8 ? 'down' : 'flat');
    }

    /** BACKTEST_ORIGINS hafte pehle forecast kiya hota to (agle 4 hafte): forecast vs simple average (28 din) — WAPE % */
    private function backtest(array $weeks): ?array
    {
        $days = self::BACKTEST_WEEKS * 7;
        $actualTotal = $fErr = $aErr = 0.0;
        foreach (self::BACKTEST_ORIGINS as $o) {
            foreach ($weeks as $w) {
                $actual = 0.0;
                $average = 0.0;
                for ($k = 0; $k < self::BACKTEST_WEEKS; $k++) {
                    $actual += $w[$o - self::BACKTEST_WEEKS + $k] ?? 0;
                    $average += $w[$o + $k] ?? 0;
                }
                $actual = max(0.0, $actual);
                $average = max(0.0, $average);
                $forecast = $this->rate($w, $o) * $days;
                $actualTotal += $actual;
                $fErr += abs($forecast - $actual);
                $aErr += abs($average - $actual);
            }
        }
        if ($actualTotal < self::MIN_ACTUAL) {
            return null;
        }
        return ['forecast_error' => round($fErr / $actualTotal * 100, 1), 'average_error' => round($aErr / $actualTotal * 100, 1), 'actual' => round($actualTotal, 2)];
    }
}
