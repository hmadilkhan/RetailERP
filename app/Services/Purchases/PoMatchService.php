<?php

namespace App\Services\Purchases;

use App\Models\VendorBill;
use App\Services\Accounting\Posting\Posters\PurchaseValuation;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Phase 4.4: PO → GRN → vendor ka invoice (three-way match).
 * Purana PO flow: Final Submit pe vendor_ledger credit (PO total), kam maal ka adjustment PurchaseLedgerService; GL me GRN pe
 * Dr Inventory / Cr AP (GrnPoster) — dono PO rate × ratio (tax / discount / freight proportional) pe.
 * Isliye PO Bill (vendor_bills type 'po_bill') stock nahi badhata aur poori raqam dobara nahi likhta — sirf FARQ:
 *   booked   = bill qty × PO unit cost (rate × ratio) — yeh pehle se AP me hai
 *   variance = bill total − booked → vendor_ledger (narration "PO Bill …") + GL (VendorBillPoster):
 *              Dr Input Tax (bill ka tax, inventory se nikal kar) / Dr Inventory (variance − tax) / Cr AP (variance)
 * Match: bill qty ≤ (mila − wapas − pehle se billed); rate (tax / discount / charges samet unit) PO cost se TOLERANCE % se
 * zyada alag → post ke liye "accept" (Admin / Sub-Admin) chahiye.
 */
class PoMatchService
{
    const TOLERANCE = 2.0; // %
    const RECEIVED = 'Stock Purchase through Purchase Order';

    /** migration (vendor_bills.purchase_id) chali? null = check */
    public static ?bool $ready = null;

    public function __construct(private VendorBillService $bills, private PurchaseValuation $valuation)
    {
    }

    public function ready(): bool
    {
        return self::$ready ??= \Illuminate\Support\Facades\Schema::hasColumn('vendor_bills', 'purchase_id');
    }

    /** POs jin ka kuch maal aaya — ordered / received (book) / billed value + status. Grouped queries (remote DB pe per-PO query dheemi). */
    public function list(int $companyId, string $status = '', string $search = '')
    {
        $branches = $this->bills->branchIds($companyId) ?: [0];
        $received = DB::table('inventory_stock_report_table as r')->join('purchase_general_details as p', 'p.purchase_id', '=', 'r.foreign_id')
            ->whereIn('p.branch_id', $branches)->where('r.narration', self::RECEIVED)
            ->groupBy('r.foreign_id', 'r.product_id')->selectRaw('r.foreign_id AS po, r.product_id AS pid, SUM(r.qty) AS qty')->get();
        $ids = $received->pluck('po')->unique()->map(fn ($v) => (int) $v)->all();
        if (!$ids) {
            return [];
        }
        $pos = DB::table('purchase_general_details as p')->join('vendors as v', 'v.id', '=', 'p.vendor_id')
            ->whereIn('p.purchase_id', $ids)->whereNotIn('p.status_id', [1, 10])
            ->when(trim($search) !== '', fn ($q) => $q->where(fn ($w) => $w->where('v.vendor_name', 'like', '%' . trim($search) . '%')->orWhere('p.po_no', 'like', '%' . trim($search) . '%')))
            ->orderByDesc('p.purchase_id')->limit(300)
            ->get(['p.purchase_id', 'p.po_no', 'p.order_date', 'p.status_id', 'p.branch_id', 'p.vendor_id', 'v.vendor_name']);
        $ids = $pos->pluck('purchase_id')->all();
        if (!$ids) {
            return [];
        }
        $items = DB::table('purchase_item_details')->whereIn('purchase_id', $ids)->groupBy('purchase_id', 'item_code')
            ->selectRaw('purchase_id AS po, item_code AS pid, SUM(quantity) AS qty, SUM(quantity * price) AS amount')->get()->groupBy('po');
        $returned = DB::table('purchase_return_itemdetails')->whereIn('purchase_id', $ids)->groupBy('purchase_id', 'item_code')
            ->selectRaw('purchase_id AS po, item_code AS pid, SUM(quantity) AS qty')->get()->groupBy('po');
        $credits = DB::table('vendor_ledger')->whereIn('po_no', $ids)->where('credit', '>', 0)->whereNull('narration')
            ->groupBy('po_no', 'vendor_id')->selectRaw('po_no AS po, vendor_id, SUM(credit) AS credit')->get()->groupBy('po');
        $billed = DB::table('vendor_bill_lines as l')->join('vendor_bills as b', 'b.id', '=', 'l.vendor_bill_id')
            ->whereIn('b.purchase_id', $ids)->where('b.type', 'po_bill')->where('b.status', '<>', 'cancelled')
            ->groupBy('b.purchase_id', 'l.product_id')->selectRaw('b.purchase_id AS po, l.product_id AS pid, SUM(l.qty) AS qty')->get()->groupBy('po');
        $bills = DB::table('vendor_bills')->where('company_id', $companyId)->whereIn('purchase_id', $ids)->where('type', 'po_bill')
            ->where('status', '<>', 'cancelled')->orderBy('id')->get(['id', 'purchase_id', 'doc_no', 'status', 'total', 'variance', 'match_status'])->groupBy('purchase_id');
        $recv = $received->groupBy('po');

        $rows = [];
        foreach ($pos as $po) {
            $id = (int) $po->purchase_id;
            $lines = $items[$id] ?? collect();
            $gross = (float) $lines->sum('amount');
            $credit = (float) (($credits[$id] ?? collect())->firstWhere('vendor_id', $po->vendor_id)->credit ?? 0);
            $ratio = $gross > 0 && $credit > 0 ? $credit / $gross : 1.0;
            $r = ($recv[$id] ?? collect())->pluck('qty', 'pid');
            $ret = ($returned[$id] ?? collect())->pluck('qty', 'pid');
            $bil = ($billed[$id] ?? collect())->pluck('qty', 'pid');
            $ordered = $keptV = $billedV = $billable = 0.0;
            foreach ($lines as $l) {
                $cost = $l->qty > 0 ? $l->amount / $l->qty * $ratio : 0;
                $kept = max(0, (float) ($r[$l->pid] ?? 0) - (float) ($ret[$l->pid] ?? 0));
                $b = (float) ($bil[$l->pid] ?? 0);
                $ordered += $l->qty * $cost;
                $keptV += $kept * $cost;
                $billedV += $b * $cost;
                $billable += max(0, $kept - $b);
            }
            $state = $billedV <= 0.005 ? 'not_billed' : ($billable > 0.0001 ? 'partial' : 'billed');
            if ($status !== '' && $status !== $state) {
                continue;
            }
            $poBills = $bills[$id] ?? collect();
            $rows[] = (array) $po + ['ordered' => round($ordered, 2), 'received' => round($keptV, 2), 'billed' => round($billedV, 2), 'state' => $state,
                'bills' => $poBills, 'variance' => round((float) $poBills->where('status', 'posted')->sum('variance'), 2)];
        }
        return $rows;
    }

    /**
     * PO ki har item: ordered, received, returned, kept (= received − returned), billed (doosre PO bills, cancelled nahi),
     * billable, rate (PO unit), unit_cost (rate × ratio).
     */
    public function poLines(int $companyId, int $poId, ?int $exceptBillId = null): array
    {
        $po = DB::table('purchase_general_details')->where('purchase_id', $poId)->whereIn('branch_id', $this->bills->branchIds($companyId) ?: [0])->first();
        if (!$po) {
            throw new RuntimeException('Purchase order not found.');
        }
        $val = $this->valuation->forPo($poId);
        $ordered = DB::table('purchase_item_details as i')->join('inventory_general as g', 'g.id', '=', 'i.item_code')
            ->where('i.purchase_id', $poId)->groupBy('i.item_code', 'g.product_name', 'g.item_code')
            ->get(['i.item_code as product_id', 'g.product_name', 'g.item_code as code', DB::raw('SUM(i.quantity) AS qty')]);
        $received = DB::table('inventory_stock_report_table')->where('foreign_id', $poId)->where('narration', self::RECEIVED)
            ->groupBy('product_id')->pluck(DB::raw('SUM(qty)'), 'product_id');
        $returned = DB::table('purchase_return_itemdetails')->where('purchase_id', $poId)->groupBy('item_code')->pluck(DB::raw('SUM(quantity)'), 'item_code');
        $billed = DB::table('vendor_bill_lines as l')->join('vendor_bills as b', 'b.id', '=', 'l.vendor_bill_id')
            ->where('b.purchase_id', $poId)->where('b.type', 'po_bill')->where('b.status', '<>', 'cancelled')
            ->when($exceptBillId, fn ($q) => $q->where('b.id', '<>', $exceptBillId))
            ->groupBy('l.product_id')->selectRaw('l.product_id AS pid, SUM(l.qty) AS qty')->pluck('qty', 'pid');

        $out = [];
        foreach ($ordered as $o) {
            $pid = (int) $o->product_id;
            $rec = (float) ($received[$pid] ?? 0);
            $ret = (float) ($returned[$pid] ?? 0);
            $kept = max(0, $rec - $ret);
            $bill = (float) ($billed[$pid] ?? 0);
            $rate = (float) ($val['prices'][$pid] ?? 0);
            $out[$pid] = ['product_id' => $pid, 'name' => $o->product_name, 'code' => $o->code, 'ordered' => (float) $o->qty, 'received' => $rec,
                'returned' => $ret, 'kept' => $kept, 'billed' => $bill, 'billable' => max(0, round($kept - $bill, 4)),
                'rate' => round($rate, 4), 'unit_cost' => round($rate * $val['ratio'], 4)];
        }
        return $out;
    }

    /** Draft PO Bill — mila hua magar abhi bill na hua maal, PO rate; tax / discount / freight PO ke hisaab se (taake farq ~0) */
    public function createDraft(int $companyId, int $poId, $userId = null): VendorBill
    {
        $lines = array_filter($this->poLines($companyId, $poId), fn ($l) => $l['billable'] > 0);
        if (!$lines) {
            throw new RuntimeException('Everything received on this purchase order is already billed.');
        }
        $po = DB::table('purchase_general_details')->where('purchase_id', $poId)->first();
        $acc = DB::table('purchase_account_details')->where('purchase_id', $poId)->first();
        $gross = (float) DB::table('purchase_item_details')->where('purchase_id', $poId)->sum(DB::raw('quantity * price'));
        $discPct = $gross > 0 && $acc ? min(100, max(0, (float) $acc->discount / $gross * 100)) : 0;
        $taxRate = $gross > 0 && $acc && (float) $acc->tax_amount > 0 ? round((float) $acc->tax_amount / max(0.01, $gross - (float) $acc->discount) * 100, 4) : 0;
        $billGross = array_sum(array_map(fn ($l) => $l['billable'] * $l['rate'], $lines));
        $charges = $gross > 0 && $acc ? round((float) $acc->shipment * $billGross / $gross, 2) : 0;

        $rows = [];
        foreach ($lines as $l) {
            $rows[] = ['product_id' => $l['product_id'], 'qty' => $l['billable'], 'unit_price' => $l['rate'],
                'discount' => round($l['billable'] * $l['rate'] * $discPct / 100, 2), 'tax_rate' => $taxRate];
        }
        return $this->save($companyId, null, $poId, [
            'vendor_ref' => '', 'bill_date' => date('Y-m-d'), 'due_date' => null, 'other_charges' => $charges,
            'narration' => 'PO #' . ($po->po_no ?: $poId), 'lines' => $rows,
        ], $userId);
    }

    /** Draft badlo. $d: vendor_ref, bill_date, due_date, other_charges, narration, lines [[product_id, qty, unit_price, discount, tax_rate]] */
    public function saveDraft(int $companyId, int $billId, array $d, $userId = null): VendorBill
    {
        $bill = VendorBill::where('company_id', $companyId)->where('type', 'po_bill')->findOrFail($billId);
        if ($bill->status !== 'draft') {
            throw new RuntimeException('Only a draft can be edited.');
        }
        return $this->save($companyId, $bill, (int) $bill->purchase_id, $d, $userId);
    }

    private function save(int $companyId, ?VendorBill $bill, int $poId, array $d, $userId): VendorBill
    {
        $po = $this->poLines($companyId, $poId, $bill?->id);
        $poRow = DB::table('purchase_general_details')->where('purchase_id', $poId)->first();
        $rows = [];
        foreach ($d['lines'] ?? [] as $r) {
            $pid = (int) ($r['product_id'] ?? 0);
            if ((float) ($r['qty'] ?? 0) == 0) {
                continue; // qty 0 = is bill me nahi
            }
            if (!isset($po[$pid])) {
                throw new RuntimeException('An item is not on this purchase order.');
            }
            if ((float) $r['qty'] > $po[$pid]['billable'] + 0.0001) {
                throw new RuntimeException($po[$pid]['name'] . ': only ' . $this->num($po[$pid]['billable']) . ' received and not yet billed.');
            }
            $rows[] = $r;
        }
        $charges = round((float) ($d['other_charges'] ?? 0), 2);
        if ($charges < 0) {
            throw new RuntimeException('Other charges cannot be negative.');
        }
        $lines = $this->bills->buildLines($companyId, $rows, $charges);
        if (!$lines) {
            throw new RuntimeException('Enter a quantity on at least one item.');
        }
        foreach ($lines as &$l) {
            $l['po_unit_cost'] = $po[$l['product_id']]['unit_cost'];
            $l['booked_value'] = round($l['qty'] * $l['po_unit_cost'], 2);
        }
        unset($l);
        $totals = $this->bills->totals($lines, $charges);
        $variance = round($totals['total'] - array_sum(array_column($lines, 'booked_value')), 2);

        return DB::transaction(function () use ($companyId, $bill, $poId, $poRow, $d, $lines, $totals, $charges, $variance, $userId) {
            $bill ??= new VendorBill();
            $bill->fill([
                'company_id' => $companyId, 'branch_id' => (int) $poRow->branch_id, 'vendor_id' => (int) $poRow->vendor_id, 'type' => 'po_bill',
                'purchase_id' => $poId, 'vendor_ref' => trim((string) ($d['vendor_ref'] ?? '')) ?: null, 'bill_date' => ($d['bill_date'] ?? '') ?: date('Y-m-d'),
                'due_date' => ($d['due_date'] ?? null) ?: null, 'other_charges' => $charges, 'narration' => trim((string) ($d['narration'] ?? '')) ?: null,
                'status' => 'draft', 'variance' => $variance,
            ] + $totals);
            if (!$bill->exists) {
                $bill->doc_no = $this->bills->nextDocNo($companyId, 'po_bill');
                $bill->created_by = $userId;
            }
            $bill->save();
            $bill->lines()->delete();
            $bill->lines()->createMany($lines);
            return $bill->fresh('lines');
        });
    }

    /**
     * PO ki har item (product_id key): ordered, kept, billable (is bill ke ilawa), po_cost; bill pe ho to bill_qty, bill_cost
     * (tax / discount / charges samet), pct, price_ok, qty_ok. Kul: needs_accept, qty_ok.
     */
    public function match(VendorBill $bill): array
    {
        $po = $this->poLines((int) $bill->company_id, (int) $bill->purchase_id, $bill->id);
        $onBill = $bill->lines->keyBy('product_id');
        $out = ['lines' => [], 'needs_accept' => false, 'qty_ok' => true];
        foreach ($po as $pid => $p) {
            $row = ['name' => $p['name'], 'ordered' => $p['ordered'], 'kept' => $p['kept'], 'billable' => $p['billable'], 'rate' => $p['rate'],
                'po_cost' => $p['unit_cost'], 'bill_qty' => 0.0, 'bill_cost' => null, 'pct' => null, 'price_ok' => true, 'qty_ok' => true];
            if ($l = $onBill[$pid] ?? null) {
                $unit = (float) $l->qty > 0 ? ((float) $l->landed_cost * (float) $l->qty + (float) $l->tax_amount) / (float) $l->qty : 0;
                $poCost = (float) ($l->po_unit_cost ?? $p['unit_cost']);
                $pct = $poCost > 0 ? round(($unit - $poCost) / $poCost * 100, 2) : ($unit > 0 ? 100.0 : 0.0);
                $row = array_merge($row, ['bill_qty' => (float) $l->qty, 'bill_cost' => round($unit, 4), 'po_cost' => $poCost, 'pct' => $pct,
                    'price_ok' => abs($pct) <= self::TOLERANCE,
                    'qty_ok' => $bill->status !== 'draft' || (float) $l->qty <= $p['billable'] + 0.0001]);
                $out['needs_accept'] = $out['needs_accept'] || !$row['price_ok'];
                $out['qty_ok'] = $out['qty_ok'] && $row['qty_ok'];
            }
            $out['lines'][$pid] = $row;
        }
        return $out;
    }

    /** Post: qty dobara check, rate farq tolerance se bahar ho to $accept chahiye; sirf variance ledger me. */
    public function post(int $companyId, int $billId, $userId = null, bool $accept = false): VendorBill
    {
        return DB::transaction(function () use ($companyId, $billId, $userId, $accept) {
            $bill = VendorBill::where('company_id', $companyId)->where('type', 'po_bill')->lockForUpdate()->findOrFail($billId);
            if ($bill->status !== 'draft') {
                throw new RuntimeException('This bill is already ' . $bill->status . '.');
            }
            DB::table('purchase_general_details')->where('purchase_id', $bill->purchase_id)->lockForUpdate()->first();
            $m = $this->match($bill->load('lines'));
            if (!$m['qty_ok']) {
                throw new RuntimeException('Billed quantity is more than what was received and not yet billed. Edit the bill.');
            }
            if ($m['needs_accept'] && !$accept) {
                throw new RuntimeException('Rates differ from the purchase order by more than ' . self::TOLERANCE . '%. Review and accept the difference to post.');
            }
            $when = $bill->bill_date->toDateString() . ' ' . date('H:i:s');
            $v = (int) round((float) $bill->variance);
            if ($v != 0) {
                $label = 'PO Bill ' . $bill->doc_no . ($bill->vendor_ref ? ' (Bill# ' . $bill->vendor_ref . ')' : '') . ' — difference from PO #' . $bill->purchase_id;
                $this->bills->ledger((int) $bill->vendor_id, $v < 0 ? -$v : 0, $v > 0 ? $v : 0, $label, $when);
            }
            $bill->update(['status' => 'posted', 'match_status' => $m['needs_accept'] ? 'accepted' : 'matched', 'posted_by' => $userId, 'posted_at' => now()]);
            return $bill->fresh('lines');
        });
    }

    /** Draft = delete; posted = sirf bina payment — variance ulta */
    public function cancel(int $companyId, int $billId): VendorBill
    {
        return DB::transaction(function () use ($companyId, $billId) {
            $bill = VendorBill::where('company_id', $companyId)->where('type', 'po_bill')->lockForUpdate()->findOrFail($billId);
            if ($bill->status === 'draft') {
                $bill->lines()->delete();
                $bill->delete();
                return $bill;
            }
            if ($bill->status !== 'posted') {
                throw new RuntimeException('This bill is already ' . $bill->status . '.');
            }
            if ((float) $bill->paid > 0) {
                throw new RuntimeException('This bill has payments and cannot be cancelled.');
            }
            $v = (int) round((float) $bill->variance);
            if ($v != 0) {
                $this->bills->ledger((int) $bill->vendor_id, $v > 0 ? $v : 0, $v < 0 ? -$v : 0, 'PO Bill ' . $bill->doc_no . ' cancelled', date('Y-m-d H:i:s'));
            }
            $bill->update(['status' => 'cancelled', 'cancelled_at' => now()]);
            return $bill;
        });
    }

    private function num($n): string
    {
        return rtrim(rtrim(number_format((float) $n, 4, '.', ','), '0'), '.');
    }
}
