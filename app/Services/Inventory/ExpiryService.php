<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Phase 2.2: lot-wise expiry (inventory_stock.expiry_date) — Expiry Report + alert banner.
 * Sirf woh lots jin me maal bacha hai (status 1, balance > 0). Value = balance × lot cost.
 */
class ExpiryService
{
    const ALERT_DAYS = 30;

    public static function enabled(): bool
    {
        return \Custom_Helper::hasLotExpiry();
    }

    /** branch ids jo user dekh sakta hai — Admin / Sub-Admin (roles 1, 2, 17) poori company, baaqi apni branch */
    public static function branchIds(int $companyId): array
    {
        $all = DB::table('branch')->where('company_id', $companyId)->pluck('branch_id')->map(fn ($b) => (int) $b)->all();
        return in_array((int) session('roleId'), [1, 2, 17], true) ? $all : array_values(array_intersect($all, [(int) session('branch')]));
    }

    /** lots query: $status = expired | expiring | all (sirf jin ki expiry di gayi) */
    public static function lots(array $branchIds, string $status, int $days, ?string $search = null)
    {
        $today = date('Y-m-d');
        $q = DB::table('inventory_stock as s')
            ->join('inventory_general as g', 'g.id', '=', 's.product_id')
            ->whereIn('s.branch_id', $branchIds ?: [0])
            ->where('s.status_id', 1)->where('s.balance', '>', 0)->whereNotNull('s.expiry_date');

        if ($status === 'expired') {
            $q->where('s.expiry_date', '<', $today);
        } elseif ($status === 'expiring') {
            $q->whereBetween('s.expiry_date', [$today, date('Y-m-d', strtotime("+{$days} days"))]);
        }
        if ($search !== null && trim($search) !== '') {
            $term = '%' . trim($search) . '%';
            $q->where(fn ($w) => $w->where('g.product_name', 'like', $term)->orWhere('g.item_code', 'like', $term)->orWhere('s.batch_no', 'like', $term));
        }
        return $q;
    }

    /** [expired => [lots, qty, value], expiring => [...]] */
    public static function summary(array $branchIds, int $days): array
    {
        $out = [];
        foreach (['expired', 'expiring'] as $status) {
            $row = self::lots($branchIds, $status, $days)
                ->selectRaw('COUNT(*) AS lots, COALESCE(SUM(s.balance), 0) AS qty, COALESCE(SUM(s.balance * s.cost_price), 0) AS value')
                ->first();
            $out[$status] = ['lots' => (int) $row->lots, 'qty' => (float) $row->qty, 'value' => (float) $row->value];
        }
        return $out;
    }

    /** banner ke liye (10 min cache) — null jab kuch dikhane ko nahi */
    public static function alert(): ?array
    {
        if (!self::enabled() || !session('company_id')) {
            return null;
        }
        $branchIds = self::branchIds((int) session('company_id'));
        $key = 'expiry_alert:' . session('company_id') . ':' . implode(',', $branchIds);
        $summary = Cache::remember($key, 600, fn () => self::summary($branchIds, self::ALERT_DAYS));
        return ($summary['expired']['lots'] + $summary['expiring']['lots']) > 0 ? $summary : null;
    }
}
