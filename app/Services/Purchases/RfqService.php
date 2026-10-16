<?php

namespace App\Services\Purchases;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Phase 3.5: RFQ / vendor quotation comparison.
 * RFQ = items (qty) + vendors. Har vendor ka quote: item ka rate, discount %, tax %, delivery din, other charges.
 * Comparison: net rate = rate × (1 − disc%) × (1 + tax%). Award: har item ek vendor ko → har jeetne wale vendor ka
 * Vendor Bill draft (VendorBillService::saveDraft — maal aane pe wahi bill post hota hai). PO nahi (clients use nahi karte).
 */
class RfqService
{
    /** $data: branch_id, title, rfq_date, due_date, notes, lines [[product_id, qty, notes]], vendor_ids [] */
    public function create(int $companyId, array $data, $userId = null): int
    {
        if (trim((string) ($data['title'] ?? '')) === '') {
            throw new RuntimeException('Enter a title.');
        }
        if (!DB::table('branch')->where('company_id', $companyId)->where('branch_id', (int) ($data['branch_id'] ?? 0))->exists()) {
            throw new RuntimeException('Select a branch.');
        }
        $lines = $this->cleanLines($companyId, $data['lines'] ?? []);
        $vendors = $this->cleanVendors($companyId, $data['vendor_ids'] ?? []);
        if (!$vendors) {
            throw new RuntimeException('Select at least one vendor.');
        }

        return DB::transaction(function () use ($companyId, $data, $userId, $lines, $vendors) {
            $id = DB::table('rfqs')->insertGetId([
                'company_id' => $companyId, 'branch_id' => (int) $data['branch_id'], 'doc_no' => $this->nextDocNo($companyId),
                'title' => trim($data['title']), 'rfq_date' => $data['rfq_date'] ?: date('Y-m-d'), 'due_date' => ($data['due_date'] ?? null) ?: null,
                'notes' => trim((string) ($data['notes'] ?? '')) ?: null, 'status' => 'open', 'created_by' => $userId,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            foreach ($lines as $l) {
                DB::table('rfq_lines')->insert($l + ['rfq_id' => $id, 'created_at' => now(), 'updated_at' => now()]);
            }
            foreach ($vendors as $v) {
                DB::table('rfq_vendors')->insert(['rfq_id' => $id, 'vendor_id' => $v, 'status' => 'invited', 'created_at' => now(), 'updated_at' => now()]);
            }
            return $id;
        });
    }

    public function get(int $companyId, int $id): object
    {
        $rfq = DB::table('rfqs')->where('company_id', $companyId)->where('id', $id)->first();
        if (!$rfq) {
            throw new RuntimeException('RFQ not found.');
        }
        return $rfq;
    }

    public function addVendor(int $companyId, int $rfqId, int $vendorId): void
    {
        $this->openRfq($companyId, $rfqId);
        if (!$this->cleanVendors($companyId, [$vendorId])) {
            throw new RuntimeException('Select a vendor.');
        }
        DB::table('rfq_vendors')->insertOrIgnore(['rfq_id' => $rfqId, 'vendor_id' => $vendorId, 'status' => 'invited', 'created_at' => now(), 'updated_at' => now()]);
    }

    public function removeVendor(int $companyId, int $rfqVendorId): void
    {
        $rv = $this->rfqVendor($companyId, $rfqVendorId);
        $this->openRfq($companyId, $rv->rfq_id);
        DB::table('rfq_vendors')->where('id', $rfqVendorId)->delete();
    }

    /**
     * Vendor ka quote. $data: delivery_days, valid_until, other_charges, notes,
     * prices: [rfq_line_id => [unit_price, discount_pct, tax_rate]] — khali rate = us item ka quote nahi.
     */
    public function saveQuote(int $companyId, int $rfqVendorId, array $data): void
    {
        $rv = $this->rfqVendor($companyId, $rfqVendorId);
        $this->openRfq($companyId, $rv->rfq_id);
        $lineIds = DB::table('rfq_lines')->where('rfq_id', $rv->rfq_id)->pluck('id')->flip();
        $num = fn ($v) => round((float) str_replace(',', '', (string) $v), 4);

        $rows = [];
        foreach ((array) ($data['prices'] ?? []) as $lineId => $p) {
            if (!isset($lineIds[(int) $lineId]) || trim((string) ($p['unit_price'] ?? '')) === '') {
                continue;
            }
            $price = $num($p['unit_price']);
            $disc = $num($p['discount_pct'] ?? 0);
            $tax = $num($p['tax_rate'] ?? 0);
            if ($price < 0 || $disc < 0 || $disc > 100 || $tax < 0 || $tax > 100) {
                throw new RuntimeException('Rates cannot be negative, and discount / tax must be between 0 and 100%.');
            }
            $rows[(int) $lineId] = ['unit_price' => $price, 'discount_pct' => $disc, 'tax_rate' => $tax];
        }
        if (!$rows) {
            throw new RuntimeException('Enter a rate for at least one item.');
        }
        $charges = $num($data['other_charges'] ?? 0);
        if ($charges < 0) {
            throw new RuntimeException('Other charges cannot be negative.');
        }

        DB::transaction(function () use ($rv, $data, $rows, $charges) {
            DB::table('rfq_vendors')->where('id', $rv->id)->update([
                'status' => 'quoted', 'quoted_at' => now(), 'updated_at' => now(), 'other_charges' => $charges,
                'delivery_days' => ($data['delivery_days'] ?? '') !== '' ? max(0, (int) $data['delivery_days']) : null,
                'valid_until' => ($data['valid_until'] ?? null) ?: null,
                'notes' => trim((string) ($data['notes'] ?? '')) ?: null,
            ]);
            DB::table('rfq_quotes')->where('rfq_vendor_id', $rv->id)->delete();
            foreach ($rows as $lineId => $r) {
                DB::table('rfq_quotes')->insert($r + ['rfq_vendor_id' => $rv->id, 'rfq_line_id' => $lineId, 'created_at' => now(), 'updated_at' => now()]);
            }
        });
    }

    public function decline(int $companyId, int $rfqVendorId): void
    {
        $rv = $this->rfqVendor($companyId, $rfqVendorId);
        $this->openRfq($companyId, $rv->rfq_id);
        DB::transaction(function () use ($rv) {
            DB::table('rfq_quotes')->where('rfq_vendor_id', $rv->id)->delete();
            DB::table('rfq_vendors')->where('id', $rv->id)->update(['status' => 'declined', 'other_charges' => 0, 'updated_at' => now()]);
        });
    }

    public function cancel(int $companyId, int $rfqId): void
    {
        $this->openRfq($companyId, $rfqId);
        DB::table('rfqs')->where('id', $rfqId)->update(['status' => 'cancelled', 'updated_at' => now()]);
    }

    /**
     * Comparison. @return [lines => [..., quotes => [rfq_vendor_id => [price, disc, tax, net, total, best]], best_vendor, last_price],
     *   vendors => [rfq_vendor_id => [..., total, items_quoted, complete]], recommended (sabse sasta poora quote), split_total]
     */
    public function comparison(object $rfq): array
    {
        $lines = DB::table('rfq_lines as l')->join('inventory_general as p', 'p.id', '=', 'l.product_id')
            ->where('l.rfq_id', $rfq->id)->orderBy('l.id')->get(['l.*', 'p.product_name', 'p.item_code']);
        $vendors = DB::table('rfq_vendors as rv')->join('vendors as v', 'v.id', '=', 'rv.vendor_id')
            ->where('rv.rfq_id', $rfq->id)->orderBy('v.vendor_name')
            ->get(['rv.*', 'v.vendor_name', 'v.vendor_contact', 'v.vendor_email']);
        $quotes = DB::table('rfq_quotes')->whereIn('rfq_vendor_id', $vendors->pluck('id'))->get()->groupBy('rfq_line_id');
        $lastPrices = $this->lastPrices((int) $rfq->company_id, $lines->pluck('product_id')->all());

        $vendorRows = [];
        foreach ($vendors as $v) {
            $vendorRows[$v->id] = (array) $v + ['total' => (float) $v->other_charges, 'items_quoted' => 0, 'complete' => false];
        }

        $lineRows = [];
        $split = 0.0;
        foreach ($lines as $l) {
            $row = (array) $l + ['quotes' => [], 'best_vendor' => null, 'last_price' => $lastPrices[$l->product_id] ?? null];
            $best = null;
            foreach ($quotes[$l->id] ?? [] as $q) {
                $net = round($q->unit_price * (1 - $q->discount_pct / 100) * (1 + $q->tax_rate / 100), 4);
                $total = round($net * $l->qty, 2);
                $row['quotes'][$q->rfq_vendor_id] = ['price' => (float) $q->unit_price, 'disc' => (float) $q->discount_pct, 'tax' => (float) $q->tax_rate,
                    'net' => $net, 'total' => $total, 'awarded' => (bool) $q->awarded, 'best' => false];
                if (isset($vendorRows[$q->rfq_vendor_id])) {
                    $vendorRows[$q->rfq_vendor_id]['total'] += $total;
                    $vendorRows[$q->rfq_vendor_id]['items_quoted']++;
                }
                if ($best === null || $net < $row['quotes'][$best]['net']) {
                    $best = $q->rfq_vendor_id;
                }
            }
            if ($best !== null) {
                $row['quotes'][$best]['best'] = true;
                $row['best_vendor'] = $best;
                $split += $row['quotes'][$best]['total'];
            }
            $lineRows[] = $row;
        }

        $recommended = null;
        foreach ($vendorRows as $id => &$v) {
            $v['total'] = round($v['total'], 2);
            $v['complete'] = $v['status'] === 'quoted' && $v['items_quoted'] === count($lineRows);
            if ($v['complete'] && ($recommended === null || $v['total'] < $vendorRows[$recommended]['total'])) {
                $recommended = $id;
            }
        }
        unset($v);

        return ['lines' => $lineRows, 'vendors' => $vendorRows, 'recommended' => $recommended, 'split_total' => round($split, 2)];
    }

    /**
     * Award: $awards [rfq_line_id => rfq_vendor_id] (khali = woh item nahi khareedna). Har jeetne wale vendor ka ek Vendor Bill draft;
     * vendor ke other charges usi ke bill me. @return [rfq_vendor_id => vendor_bill_id]
     */
    public function award(int $companyId, int $rfqId, array $awards, $userId = null): array
    {
        $rfq = $this->openRfq($companyId, $rfqId);
        $cmp = $this->comparison($rfq);
        $byVendor = [];
        foreach ($cmp['lines'] as $l) {
            $rvId = (int) ($awards[$l['id']] ?? 0);
            if (!$rvId) {
                continue;
            }
            if (!isset($l['quotes'][$rvId])) {
                throw new RuntimeException('"' . $l['product_name'] . '" was not quoted by the selected vendor.');
            }
            $byVendor[$rvId][] = $l;
        }
        if (!$byVendor) {
            throw new RuntimeException('Select a vendor for at least one item.');
        }

        $bills = app(VendorBillService::class);
        return DB::transaction(function () use ($rfq, $cmp, $byVendor, $bills, $companyId, $userId) {
            $out = [];
            foreach ($byVendor as $rvId => $lines) {
                $v = $cmp['vendors'][$rvId];
                $billLines = [];
                foreach ($lines as $l) {
                    $q = $l['quotes'][$rvId];
                    $billLines[] = ['product_id' => $l['product_id'], 'qty' => (float) $l['qty'], 'unit_price' => $q['price'],
                        'discount' => round($q['price'] * $l['qty'] * $q['disc'] / 100, 2), 'tax_rate' => $q['tax']];
                    DB::table('rfq_quotes')->where('rfq_vendor_id', $rvId)->where('rfq_line_id', $l['id'])->update(['awarded' => 1, 'updated_at' => now()]);
                }
                $bill = $bills->saveDraft($companyId, [
                    'branch_id' => $rfq->branch_id, 'vendor_id' => $v['vendor_id'], 'vendor_ref' => $rfq->doc_no,
                    'bill_date' => date('Y-m-d'), 'due_date' => null, 'other_charges' => (float) $v['other_charges'],
                    'narration' => 'From ' . $rfq->doc_no . ' — ' . $rfq->title, 'lines' => $billLines,
                ], null, $userId);
                DB::table('rfq_vendors')->where('id', $rvId)->update(['vendor_bill_id' => $bill->id, 'updated_at' => now()]);
                $out[$rvId] = $bill->id;
            }
            DB::table('rfqs')->where('id', $rfq->id)->update(['status' => 'awarded', 'awarded_at' => now(), 'updated_at' => now()]);
            return $out;
        });
    }

    /** vendor ko bhejne ke liye saada text (WhatsApp / email) */
    public function requestText(object $rfq, ?string $vendorName = null): string
    {
        $company = DB::table('company')->where('company_id', $rfq->company_id)->value('name');
        $text = ($vendorName ? "Dear {$vendorName},\n" : '') . "Please send your best rates for the items below.\n"
            . "Ref: {$rfq->doc_no} — {$rfq->title}\n";
        foreach (DB::table('rfq_lines as l')->join('inventory_general as p', 'p.id', '=', 'l.product_id')->where('l.rfq_id', $rfq->id)->orderBy('l.id')
            ->get(['p.product_name', 'p.item_code', 'l.qty', 'l.notes']) as $i => $l) {
            $text .= ($i + 1) . '. ' . $l->product_name . ($l->item_code ? " ({$l->item_code})" : '') . ' — Qty ' . rtrim(rtrim(number_format($l->qty, 3, '.', ''), '0'), '.')
                . ($l->notes ? " — {$l->notes}" : '') . "\n";
        }
        if ($rfq->due_date) {
            $text .= 'Please reply by ' . date('d M Y', strtotime($rfq->due_date)) . ".\n";
        }
        return $text . 'Mention delivery time, tax and any other charges.' . ($company ? "\n{$company}" : '');
    }

    /** product => aakhri posted vendor bill ka rate + vendor */
    private function lastPrices(int $companyId, array $productIds): array
    {
        if (!$productIds) {
            return [];
        }
        $rows = DB::table('vendor_bill_lines as l')->join('vendor_bills as b', 'b.id', '=', 'l.vendor_bill_id')
            ->join('vendors as v', 'v.id', '=', 'b.vendor_id')
            ->where('b.company_id', $companyId)->where('b.type', 'bill')->where('b.status', 'posted')->whereIn('l.product_id', $productIds)
            ->whereRaw('l.id = (SELECT MAX(l2.id) FROM vendor_bill_lines l2 JOIN vendor_bills b2 ON b2.id = l2.vendor_bill_id
                WHERE l2.product_id = l.product_id AND b2.company_id = b.company_id AND b2.type = \'bill\' AND b2.status = \'posted\')')
            ->get(['l.product_id', 'l.unit_price', 'b.bill_date', 'v.vendor_name']);
        $out = [];
        foreach ($rows as $r) {
            $out[$r->product_id] = ['price' => (float) $r->unit_price, 'date' => $r->bill_date, 'vendor' => $r->vendor_name];
        }
        return $out;
    }

    private function cleanLines(int $companyId, array $lines): array
    {
        $out = [];
        foreach ($lines as $l) {
            $qty = round((float) str_replace(',', '', (string) ($l['qty'] ?? 0)), 3);
            $pid = (int) ($l['product_id'] ?? 0);
            if (!$pid) {
                continue;
            }
            if ($qty <= 0) {
                throw new RuntimeException('Quantity must be more than 0.');
            }
            $out[$pid] = ['product_id' => $pid, 'qty' => $qty, 'notes' => trim((string) ($l['notes'] ?? '')) ?: null];
        }
        if (!$out) {
            throw new RuntimeException('Add at least one item.');
        }
        $valid = DB::table('inventory_general')->where('company_id', $companyId)->whereIn('id', array_keys($out))->count();
        if ($valid !== count($out)) {
            throw new RuntimeException('Some items do not belong to this company.');
        }
        return array_values($out);
    }

    private function cleanVendors(int $companyId, array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        return $ids ? DB::table('vendors')->where('user_id', $companyId)->whereIn('id', $ids)->pluck('id')->map(fn ($v) => (int) $v)->all() : [];
    }

    private function rfqVendor(int $companyId, int $id): object
    {
        $rv = DB::table('rfq_vendors as rv')->join('rfqs as r', 'r.id', '=', 'rv.rfq_id')
            ->where('r.company_id', $companyId)->where('rv.id', $id)->first(['rv.*']);
        if (!$rv) {
            throw new RuntimeException('Vendor not found on this RFQ.');
        }
        return $rv;
    }

    private function openRfq(int $companyId, int $id): object
    {
        $rfq = $this->get($companyId, $id);
        if ($rfq->status !== 'open') {
            throw new RuntimeException('This RFQ is ' . $rfq->status . ' and cannot be changed.');
        }
        return $rfq;
    }

    private function nextDocNo(int $companyId): string
    {
        $count = DB::table('rfqs')->where('company_id', $companyId)->lockForUpdate()->count();
        return 'RFQ-' . str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
    }
}
