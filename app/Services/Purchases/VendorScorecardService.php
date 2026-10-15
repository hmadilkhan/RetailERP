<?php

namespace App\Services\Purchases;

use Illuminate\Support\Facades\DB;

/**
 * Phase 2.6: vendor scorecard — period me har vendor:
 *   bills / khareed (Vendor Bills posted), return % (debit notes ÷ khareed), credit notes,
 *   qeemat ka farq (har item ka period ka pehla vs aakhri rate — Vendor Bill lines + PO lines, value-weighted %),
 *   PO: time pe (pehla GRN ≤ delivery_date) %, fill rate (aaya ÷ order), aur abhi ka payable (vendor_ledger aakhri balance).
 * PO / GRN clients kam use karte hain (2025-26: 5 GRN) — wo columns zyada tar khali; asal data Vendor Bills se.
 */
class VendorScorecardService
{
    public function build(int $companyId, string $from, string $to, ?int $branchId = null): array
    {
        $vendors = DB::table('vendors')->where('user_id', $companyId)->pluck('vendor_name', 'id');
        if ($vendors->isEmpty()) {
            return [];
        }
        $rows = [];
        $row = function ($id) use (&$rows, $vendors) {
            return $rows[$id] ??= [
                'vendor_id' => (int) $id, 'name' => $vendors[$id] ?? ('#' . $id), 'bills' => 0, 'purchases' => 0.0,
                'returns' => 0.0, 'credit_notes' => 0.0, 'landed' => 0.0, 'return_pct' => null, 'price_change_pct' => null,
                'pos' => 0, 'on_time_pct' => null, 'fill_pct' => null, 'payable' => 0.0,
            ];
        };

        // Vendor Bills
        try {
            $docs = DB::table('vendor_bills')->where('company_id', $companyId)->where('status', 'posted')
                ->whereBetween('bill_date', [$from, $to])->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->groupBy('vendor_id', 'type')->selectRaw('vendor_id, type, COUNT(*) AS n, SUM(total) AS total')->get();
        } catch (\Illuminate\Database\QueryException $e) {
            $docs = collect(); // migration se pehle
        }
        foreach ($docs as $d) {
            $r = &$rows[(int) $d->vendor_id];
            $r = $r ?? $row($d->vendor_id);
            match ($d->type) {
                'bill' => [$r['bills'] = (int) $d->n, $r['purchases'] = (float) $d->total],
                'debit_note' => $r['returns'] = (float) $d->total,
                'credit_note' => $r['credit_notes'] = (float) $d->total,
                'landed_cost' => $r['landed'] = (float) $d->total,
                default => null,
            };
            unset($r);
        }

        // PO (Placed ya aage, Delete / Draft nahi)
        $pos = DB::table('purchase_general_details as g')->join('branch as b', 'b.branch_id', '=', 'g.branch_id')
            ->where('b.company_id', $companyId)->whereNotIn('g.status_id', [1, 4, 10])
            ->whereBetween('g.order_date', [$from, $to])->when($branchId, fn ($q) => $q->where('g.branch_id', $branchId))
            ->get(['g.purchase_id', 'g.vendor_id', 'g.delivery_date']);
        // har PO ki alag query remote DB pe bahut slow — grouped
        $ids = $pos->pluck('purchase_id')->all() ?: [0];
        $ordered = DB::table('purchase_item_details')->whereIn('purchase_id', $ids)->groupBy('purchase_id')->pluck(DB::raw('SUM(quantity)'), 'purchase_id');
        $received = DB::table('purchase_rec_details')->whereIn('po_id', $ids)->groupBy('po_id')->pluck(DB::raw('SUM(qty_rec)'), 'po_id');
        $firstGrn = DB::table('purchase_rec_details as r')->join('purchase_rec_gen as g', 'g.rec_id', '=', 'r.GRN')
            ->whereIn('r.po_id', $ids)->groupBy('r.po_id')->selectRaw('r.po_id AS po, MIN(g.created_at) AS first_grn')->get()
            ->pluck('first_grn', 'po');
        $poStats = [];
        foreach ($pos as $po) {
            $o = (float) ($ordered[$po->purchase_id] ?? 0);
            $rec = (float) ($received[$po->purchase_id] ?? 0);
            $grn = $firstGrn[$po->purchase_id] ?? null;
            $s = &$poStats[(int) $po->vendor_id];
            $s = $s ?? ['n' => 0, 'timed' => 0, 'on_time' => 0, 'ordered' => 0.0, 'received' => 0.0];
            $s['n']++;
            $s['ordered'] += $o;
            $s['received'] += min($rec, $o);
            if ($grn && $po->delivery_date && $po->delivery_date > '1900-01-01') {
                $s['timed']++;
                $s['on_time'] += substr((string) $grn, 0, 10) <= substr((string) $po->delivery_date, 0, 10) ? 1 : 0;
            }
            unset($s);
        }
        foreach ($poStats as $vendorId => $s) {
            $r = $row($vendorId);
            $r['pos'] = $s['n'];
            $r['on_time_pct'] = $s['timed'] ? round(100 * $s['on_time'] / $s['timed'], 1) : null;
            $r['fill_pct'] = $s['ordered'] > 0 ? round(100 * $s['received'] / $s['ordered'], 1) : null;
            $rows[$vendorId] = $r;
        }

        // Qeemat ka farq: har vendor + item ke period ke pehle aur aakhri rate
        foreach ($this->priceChanges($companyId, $from, $to, $branchId) as $vendorId => $pct) {
            $r = $row($vendorId);
            $r['price_change_pct'] = $pct;
            $rows[$vendorId] = $r;
        }

        // vendor_ledger: balance = pichla + debit − credit (minus = hamein dena hai) — har vendor ki aakhri row, ek query
        $balances = $rows ? collect(DB::select('SELECT vl.vendor_id, vl.balance FROM vendor_ledger vl
            JOIN (SELECT vendor_id, MAX(vendor_account_id) AS last_id FROM vendor_ledger WHERE vendor_id IN (' . implode(',', array_map('intval', array_keys($rows))) . ') GROUP BY vendor_id) x
              ON x.last_id = vl.vendor_account_id'))->pluck('balance', 'vendor_id') : collect();
        foreach ($rows as $id => &$r) {
            $r['return_pct'] = $r['purchases'] > 0 ? round(100 * $r['returns'] / $r['purchases'], 1) : null;
            $r['payable'] = -1 * (float) ($balances[$id] ?? 0);
        }
        unset($r);

        usort($rows, fn ($a, $b) => [$b['purchases'], $b['pos']] <=> [$a['purchases'], $a['pos']]);
        return $rows;
    }

    /** vendor_id => value-weighted % (aakhri rate vs pehla rate, sirf jin items ke 2+ rate) */
    private function priceChanges(int $companyId, string $from, string $to, ?int $branchId): array
    {
        $points = [];
        try {
            foreach (DB::table('vendor_bill_lines as l')->join('vendor_bills as b', 'b.id', '=', 'l.vendor_bill_id')
                ->where('b.company_id', $companyId)->where('b.type', 'bill')->where('b.status', 'posted')
                ->whereBetween('b.bill_date', [$from, $to])->when($branchId, fn ($q) => $q->where('b.branch_id', $branchId))
                ->where('l.qty', '>', 0)->orderBy('b.bill_date')->orderBy('b.id')
                ->get(['b.vendor_id', 'l.product_id', 'l.unit_price', 'l.qty', 'b.bill_date']) as $p) {
                $points[(int) $p->vendor_id][(int) $p->product_id][] = [(string) $p->bill_date, (float) $p->unit_price, (float) $p->qty];
            }
        } catch (\Illuminate\Database\QueryException $e) {
        }
        foreach (DB::table('purchase_item_details as i')->join('purchase_general_details as g', 'g.purchase_id', '=', 'i.purchase_id')
            ->join('branch as br', 'br.branch_id', '=', 'g.branch_id')
            ->where('br.company_id', $companyId)->whereNotIn('g.status_id', [1, 4, 10])
            ->whereBetween('g.order_date', [$from, $to])->when($branchId, fn ($q) => $q->where('g.branch_id', $branchId))
            ->where('i.quantity', '>', 0)->orderBy('g.order_date')
            ->get(['g.vendor_id', 'i.item_code', 'i.price', 'i.quantity', 'g.order_date']) as $p) {
            $points[(int) $p->vendor_id][(int) $p->item_code][] = [(string) $p->order_date, (float) $p->price, (float) $p->quantity];
        }

        $out = [];
        foreach ($points as $vendorId => $products) {
            $weighted = 0;
            $weight = 0;
            foreach ($products as $series) {
                if (count($series) < 2) {
                    continue;
                }
                usort($series, fn ($a, $b) => $a[0] <=> $b[0]);
                [$first, $last] = [$series[0][1], end($series)[1]];
                if ($first <= 0) {
                    continue;
                }
                $w = array_sum(array_map(fn ($s) => $s[1] * $s[2], $series));
                $weighted += ($last - $first) / $first * 100 * $w;
                $weight += $w;
            }
            if ($weight > 0) {
                $out[$vendorId] = round($weighted / $weight, 1);
            }
        }
        return $out;
    }
}
