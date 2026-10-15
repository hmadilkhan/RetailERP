<?php

namespace App\Services\Inventory;

use App\Models\ReorderLevel;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Phase 2.3: branch ko kya mangwana hai.
 *   on hand   = inventory_stock (status 1) ka balance
 *   inbound   = challan bheja magar GRN nahi bana (pichle INBOUND_DAYS din) + PO Placed / Partially Received me bacha maal
 *   bikri     = roz ki average (status 4 bina void, minus web return) pichle N din
 *   min       = reorder_levels.min_qty, warna product ka purana reminder_qty (Qty Reminder, product-wide)
 *   reorder point = max(min, roz ki bikri × lead time)
 *   mangwao jab on hand + inbound ≤ reorder point:
 *     max ho → max tak; warna reorder_qty; warna bikri × (lead time + cover din) + min − available
 *   Qty upar round (challan / GRN qty int hain); pack_qty > 1 ho to poore carton.
 * Data bahut companies ka thin hai (Demand module 49 rows, transfer sirf company 120) — isliye "Create Demand" purane
 * Demand flow me Pending demand banata hai; wahan se head office transfer ya purchase karta hai.
 */
class ReplenishmentService
{
    const DEFAULT_LEAD_DAYS = 7;
    const INBOUND_DAYS = 60;
    const DEMAND_ITEMS = 12; // purani demand screen ek demand me 12 item rakhti hai

    /**
     * @return array<int, array> product_id => row (sirf woh products jin ka stock / bikri / level / reminder hai)
     */
    public function suggestions(int $companyId, int $branchId, int $salesDays = 30, int $coverDays = 7, ?int $departmentId = null): array
    {
        $salesDays = max(7, min(365, $salesDays));
        $from = date('Y-m-d', strtotime('-' . $salesDays . ' days'));
        $today = date('Y-m-d');

        $products = DB::table('inventory_general as g')
            ->leftJoin('inventory_qty_reminders as r', 'r.inventory_id', '=', 'g.id')
            ->where('g.company_id', $companyId)->where('g.status', 1)
            ->when($departmentId, fn ($q) => $q->where('g.department_id', $departmentId))
            ->get(['g.id', 'g.product_name', 'g.item_code', 'g.department_id', 'g.pack_qty', 'g.weight_qty', 'r.reminder_qty'])
            ->keyBy('id');
        if ($products->isEmpty()) {
            return [];
        }

        $onHand = DB::table('inventory_stock')->where('branch_id', $branchId)->where('status_id', 1)
            ->groupBy('product_id')->pluck(DB::raw('SUM(balance)'), 'product_id');

        $sold = collect(DB::select("
            SELECT d.item_code AS product_id,
                   SUM(CASE WHEN r.status = 14 THEN -(d.total_qty + 0) ELSE (d.total_qty + 0) END) AS qty
            FROM sales_receipts r
            JOIN sales_receipt_details d ON d.receipt_id = r.id
            WHERE r.branch = ? AND r.date BETWEEN ? AND ? AND r.date LIKE '____-__-__'
              AND ((r.status = 4 AND r.void_receipt = 0) OR (r.status = 14 AND r.is_sale_return = 1))
            GROUP BY d.item_code", [$branchId, $from, $today]))->pluck('qty', 'product_id')->map(fn ($q) => (float) $q)->all();
        $sold = $this->explodeRecipes($branchId, $sold);

        $inbound = $this->inbound($branchId);
        $levels = ReorderLevel::where('branch_id', $branchId)->get()->keyBy('product_id');
        $elsewhere = $this->stockElsewhere($companyId, $branchId);

        $rows = [];
        foreach ($products as $id => $p) {
            $level = $levels[$id] ?? null;
            $stock = (float) ($onHand[$id] ?? 0);
            $incoming = (float) ($inbound[$id] ?? 0);
            $daily = max(0, (float) ($sold[$id] ?? 0)) / $salesDays;
            $reminder = (float) ($p->reminder_qty ?? 0);
            $min = $level && $level->min_qty !== null ? (float) $level->min_qty : $reminder;
            if ($stock == 0 && $incoming == 0 && $daily == 0 && !$level && $reminder <= 0) {
                continue; // na stock, na bikri, na level — dikhane ka faida nahi
            }

            $lead = $level && $level->lead_time_days !== null ? (int) $level->lead_time_days : self::DEFAULT_LEAD_DAYS;
            $max = $level && $level->max_qty !== null ? (float) $level->max_qty : null;
            $reorderQty = $level && $level->reorder_qty !== null ? (float) $level->reorder_qty : null;
            $point = max($min, $daily * $lead);
            $available = $stock + $incoming;

            $suggest = 0.0;
            if ($available <= $point && ($point > 0 || $daily > 0)) {
                if ($max !== null && $max > 0) {
                    $suggest = $max - $available;
                } elseif ($reorderQty !== null && $reorderQty > 0) {
                    $suggest = $reorderQty;
                } else {
                    $suggest = $daily * ($lead + $coverDays) + $min - $available;
                }
            }
            $suggest = $this->roundUp($suggest, (float) ($p->pack_qty ?? 0));

            $rows[$id] = [
                'product_id' => (int) $id,
                'name' => $p->product_name,
                'code' => $p->item_code,
                'department_id' => (int) $p->department_id,
                'on_hand' => $stock,
                'inbound' => $incoming,
                'daily' => round($daily, 3),
                'days_left' => $daily > 0 ? round(max(0, $stock) / $daily, 1) : null,
                'min' => $min,
                'min_source' => $level && $level->min_qty !== null ? 'level' : ($reminder > 0 ? 'reminder' : null),
                'max' => $max,
                'reorder_qty' => $reorderQty,
                'lead' => $lead,
                'point' => round($point, 2),
                'suggest' => $suggest,
                'has_level' => (bool) $level,
                'source' => $elsewhere[$id] ?? null, // ['branch_id' => , 'qty' => ] sab se zyada stock wali doosri branch
            ];
        }
        return $rows;
    }

    /**
     * Recipe wala product bikta hai, ghatte ingredients hain — bikri ingredients me baanto (ValuationService jaisa:
     * usage_qty ingredient ki secondary unit me → ÷ weight_qty). Is branch ki recipe, warna kisi bhi branch ki.
     */
    private function explodeRecipes(int $branchId, array $sold): array
    {
        if (!$sold) {
            return $sold;
        }
        $rows = DB::table('recipy_general as g')->join('recipy_details as d', 'd.recipy_id', '=', 'g.recipy_id')
            ->leftJoin('inventory_general as i', 'i.id', '=', 'd.item_id')
            ->where('g.status_id', 1)->whereIn('g.product_id', array_keys($sold))->whereRaw('(d.usage_qty + 0) > 0')
            ->orderByRaw('g.branch_id = ? DESC', [$branchId])
            ->get(['g.product_id', 'g.branch_id', 'd.item_id', DB::raw('(d.usage_qty + 0) AS usage_qty'), DB::raw('CASE WHEN (i.weight_qty + 0) > 1 THEN (i.weight_qty + 0) ELSE 1 END AS w')]);

        $recipes = [];
        foreach ($rows->groupBy('product_id') as $productId => $lines) {
            $branch = $lines->first()->branch_id; // pehli = is branch ki (agar ho)
            $recipes[(int) $productId] = $lines->where('branch_id', $branch);
        }

        $out = [];
        foreach ($sold as $productId => $qty) {
            if (!isset($recipes[$productId])) {
                $out[$productId] = ($out[$productId] ?? 0) + $qty;
                continue;
            }
            foreach ($recipes[$productId] as $line) {
                $out[(int) $line->item_id] = ($out[(int) $line->item_id] ?? 0) + $qty * $line->usage_qty / $line->w;
            }
        }
        return $out;
    }

    /** product_id => raste me qty (challan bina GRN + PO ka bacha maal) */
    public function inbound(int $branchId): array
    {
        $out = [];
        $floor = date('Y-m-d', strtotime('-' . self::INBOUND_DAYS . ' days'));
        // bheja gaya magar receive nahi hua — status pe bharosa nahi, GRN row ka na hona dekho
        foreach (DB::select("
            SELECT d.product_id, SUM(d.deliverd_qty) AS qty
            FROM deliverychallan_item_details d
            JOIN deliverychallan_general_details h ON h.DC_id = d.DC_Id
            WHERE h.branch_to = ? AND LEFT(h.date, 10) >= ?
              AND NOT EXISTS (SELECT 1 FROM purchase_rec_dc_details r WHERE r.dc_item_id = d.dc_item_id)
            GROUP BY d.product_id", [$branchId, $floor]) as $r) {
            $out[(int) $r->product_id] = ($out[(int) $r->product_id] ?? 0) + (float) $r->qty;
        }
        // PO Placed (2) / Partially Received (7) — order − aaya hua
        foreach (DB::select("
            SELECT i.item_code AS product_id,
                   SUM(i.quantity) - COALESCE((SELECT SUM(rd.qty_rec) FROM purchase_rec_details rd
                                              WHERE rd.po_id = g.purchase_id AND rd.item_id = i.item_code), 0) AS qty
            FROM purchase_item_details i
            JOIN purchase_general_details g ON g.purchase_id = i.purchase_id
            WHERE g.branch_id = ? AND g.status_id IN (2, 7)
            GROUP BY g.purchase_id, i.item_code", [$branchId]) as $r) {
            if ((float) $r->qty > 0) {
                $out[(int) $r->product_id] = ($out[(int) $r->product_id] ?? 0) + (float) $r->qty;
            }
        }
        return $out;
    }

    /** product_id => ['branch_id', 'qty'] — company ki doosri active branch jahan sab se zyada stock hai */
    public function stockElsewhere(int $companyId, int $branchId): array
    {
        $out = [];
        foreach (DB::select("
            SELECT s.product_id, s.branch_id, SUM(s.balance) AS qty
            FROM inventory_stock s JOIN branch b ON b.branch_id = s.branch_id
            WHERE b.company_id = ? AND b.status_id = 1 AND s.branch_id <> ? AND s.status_id = 1
            GROUP BY s.product_id, s.branch_id HAVING qty > 0", [$companyId, $branchId]) as $r) {
            $id = (int) $r->product_id;
            if (!isset($out[$id]) || (float) $r->qty > $out[$id]['qty']) {
                $out[$id] = ['branch_id' => (int) $r->branch_id, 'qty' => (float) $r->qty];
            }
        }
        return $out;
    }

    /** level save — sab khali ho to row hata do (wapas reminder_qty / default pe) */
    public function saveLevel(int $companyId, int $branchId, int $productId, $min, $max, $reorderQty, $lead, $userId = null): void
    {
        $this->assertBranch($companyId, $branchId);
        if (!DB::table('inventory_general')->where('id', $productId)->where('company_id', $companyId)->exists()) {
            throw new RuntimeException('Product not found.');
        }
        $val = fn ($v) => $v === null || trim((string) $v) === '' ? null : (float) $v;
        $fields = [
            'min_qty' => $val($min),
            'max_qty' => $val($max),
            'reorder_qty' => $val($reorderQty),
            'lead_time_days' => $lead === null || trim((string) $lead) === '' ? null : (int) $lead,
        ];
        foreach ($fields as $k => $v) {
            if ($v !== null && $v < 0) {
                throw new RuntimeException('Levels cannot be negative.');
            }
        }
        if ($fields['max_qty'] !== null && $fields['min_qty'] !== null && $fields['max_qty'] < $fields['min_qty']) {
            throw new RuntimeException('Max cannot be less than min.');
        }

        if (array_filter($fields, fn ($v) => $v !== null) === []) {
            ReorderLevel::where('branch_id', $branchId)->where('product_id', $productId)->delete();
            return;
        }
        ReorderLevel::updateOrCreate(['branch_id' => $branchId, 'product_id' => $productId],
            $fields + ['company_id' => $companyId, 'updated_by' => $userId]);
    }

    /** bikri se levels: min = bikri × lead time, max = bikri × (lead time + cover) — sirf jin ki bikri hai */
    public function fillFromSales(int $companyId, int $branchId, array $productIds, int $salesDays, int $coverDays, $userId = null): int
    {
        $rows = $this->suggestions($companyId, $branchId, $salesDays, $coverDays);
        $n = 0;
        foreach ($productIds as $id) {
            $r = $rows[(int) $id] ?? null;
            if (!$r || $r['daily'] <= 0) {
                continue;
            }
            $lead = $r['lead'];
            $this->saveLevel($companyId, $branchId, (int) $id, ceil($r['daily'] * $lead), ceil($r['daily'] * ($lead + $coverDays)),
                $r['reorder_qty'], $lead, $userId);
            $n++;
        }
        return $n;
    }

    /**
     * Purane Demand flow me Pending (status 2) demand(s) — 12-12 items. Received Demand screen pe head office ko dikhti hai.
     * $items: product_id => qty. @return demand header ids
     */
    public function createDemand(int $companyId, int $branchId, array $items, $userId, ?string $comment = null): array
    {
        $this->assertBranch($companyId, $branchId);
        $items = array_filter(array_map(fn ($q) => (float) $q, $items), fn ($q) => $q > 0);
        if (!$items) {
            throw new RuntimeException('Select at least one product with a quantity.');
        }
        $valid = DB::table('inventory_general')->where('company_id', $companyId)->whereIn('id', array_keys($items))->pluck('id')->map(fn ($i) => (int) $i)->all();
        if (count($valid) !== count($items)) {
            throw new RuntimeException('Some products do not belong to this company.');
        }

        return DB::transaction(function () use ($branchId, $items, $userId, $comment) {
            $ids = [];
            foreach (array_chunk($items, self::DEMAND_ITEMS, true) as $chunk) {
                $number = (int) DB::table('demand_general_details')->where('branch_id', $branchId)->lockForUpdate()->max('demand_id') + 1;
                $headerId = DB::table('demand_general_details')->insertGetId([
                    'demand_id' => $number,
                    'user_id' => $userId,
                    'demand_status_id' => 2, // Pending — seedha Received Demand me
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'comment' => $comment ?: 'Auto reorder suggestion',
                    'branch_id' => $branchId,
                ]);
                foreach ($chunk as $productId => $qty) {
                    DB::table('demand_item_details')->insert(['demand_id' => $headerId, 'product_id' => $productId, 'qty' => $qty, 'status_id' => 2]);
                }
                $ids[] = $headerId;
            }
            return $ids;
        });
    }

    private function roundUp(float $qty, float $pack): float
    {
        if ($qty <= 0) {
            return 0.0;
        }
        $qty = ceil($qty - 1e-9);
        return $pack > 1 ? ceil($qty / $pack) * $pack : $qty;
    }

    private function assertBranch(int $companyId, int $branchId): void
    {
        if (!DB::table('branch')->where('branch_id', $branchId)->where('company_id', $companyId)->exists()) {
            throw new RuntimeException('Select a branch.');
        }
    }
}
