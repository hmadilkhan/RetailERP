<?php

namespace App\Services\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Services\Accounting\Posting\SourceQuery;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Phase 4.1: sales tax ledger + mahana return. Source data se (saari companies — accounting on ho ya na ho):
 *   Output: POS / web sales (sales_receipts status 4, void nahi) — FBR = sales_account_subdetails.sales_tax_amount,
 *           SRB = srb (varchar). Wahi qaide jo SalesPoster ke (subdetails ki aakhri row, sirf YYYY-MM-DD dates).
 *           Web returns (status 14) pe tax copy nahi hota → sirf maloomat (value), tax kam nahi karte.
 *   Input:  posted Vendor Bills (Debit Note minus) — FBR ke against. PO / expense me tax ka data nahi hota.
 *   FBR milan: jin branches ka FBR (fbr_details active) on hai, wahan tax wali receipts jin pe fbrInvNumber nahi.
 *   GL check (sirf accounting on): 2130 Output Tax ka mahana net credit, 1160 Input Tax ka net debit.
 * Filing: mahina + authority ka record (CPR, jama raqam); accounting on ho to JV: Dr Output Tax / Cr Input Tax + Cr Cash/Bank.
 */
class SalesTaxService
{
    const AUTHORITIES = ['fbr' => 'FBR (Sales Tax)', 'srb' => 'SRB (Sindh Services)'];

    /** sales_tax_filings migration chali? (null = check) */
    public static ?bool $filingsReady = null;

    public function filingsReady(): bool
    {
        return self::$filingsReady ??= \Illuminate\Support\Facades\Schema::hasTable('sales_tax_filings');
    }

    /** @return period => [fbr_tax, srb_tax, value (bina tax), receipts, taxed, fbr_numbered, fbr_numbered_tax, missing_fbr, missing_fbr_tax, returns_value, input_tax, purchase_value] */
    public function summary(int $companyId, string $fromPeriod, string $toPeriod, ?int $branchId = null): array
    {
        [$from, $to] = [$fromPeriod . '-01', date('Y-m-t', strtotime($toPeriod . '-01'))];
        $branches = $this->branches($companyId, $branchId);
        $out = [];
        for ($p = $fromPeriod; $p <= $toPeriod; $p = date('Y-m', strtotime($p . '-01 +1 month'))) {
            $out[$p] = ['fbr_tax' => 0, 'srb_tax' => 0, 'value' => 0, 'receipts' => 0, 'taxed' => 0, 'fbr_numbered' => 0, 'fbr_numbered_tax' => 0,
                'missing_fbr' => 0, 'missing_fbr_tax' => 0, 'returns_value' => 0, 'input_tax' => 0, 'purchase_value' => 0];
        }
        if (!$branches) {
            return $out;
        }
        $in = implode(',', $branches);
        $fbrBranches = DB::table('fbr_details')->where('company_id', $companyId)->where('status', 1)->whereNull('deleted_at')->pluck('branch_id')->map(fn ($b) => (int) $b)->all();
        $fbrIn = $fbrBranches ? implode(',', $fbrBranches) : '0';

        $rows = DB::select("
            SELECT SUBSTR(r.date, 1, 7) AS period, r.status,
                COUNT(*) AS receipts,
                SUM(r.total_amount + 0) AS net,
                SUM(COALESCE(s.sales_tax_amount, 0)) AS fbr_tax,
                SUM(COALESCE(s.srb + 0, 0)) AS srb_tax,
                SUM(COALESCE(s.service_tax_amount, 0) + COALESCE(s.delivery_charges_amount, 0)) AS charges,
                SUM(CASE WHEN COALESCE(s.sales_tax_amount, 0) + COALESCE(s.srb + 0, 0) > 0 THEN 1 ELSE 0 END) AS taxed,
                SUM(CASE WHEN COALESCE(r.fbrInvNumber, '') <> '' THEN 1 ELSE 0 END) AS fbr_numbered,
                SUM(CASE WHEN COALESCE(r.fbrInvNumber, '') <> '' THEN COALESCE(s.sales_tax_amount, 0) ELSE 0 END) AS fbr_numbered_tax,
                SUM(CASE WHEN r.branch IN ({$fbrIn}) AND COALESCE(r.fbrInvNumber, '') = '' AND COALESCE(s.sales_tax_amount, 0) > 0 THEN 1 ELSE 0 END) AS missing_fbr,
                SUM(CASE WHEN r.branch IN ({$fbrIn}) AND COALESCE(r.fbrInvNumber, '') = '' THEN COALESCE(s.sales_tax_amount, 0) ELSE 0 END) AS missing_fbr_tax
            FROM sales_receipts r
            LEFT JOIN sales_account_subdetails s ON s.id = (SELECT MAX(s2.id) FROM sales_account_subdetails s2 WHERE s2.receipt_id = r.id)
            WHERE r.branch IN ({$in}) AND r.date BETWEEN ? AND ? AND r.date LIKE '____-__-__'
              AND ((r.status = 4 AND r.void_receipt = 0) OR (r.status = 14 AND r.is_sale_return = 1))
            GROUP BY SUBSTR(r.date, 1, 7), r.status", [$from, $to]);

        foreach ($rows as $r) {
            if (!isset($out[$r->period])) {
                continue;
            }
            $o = &$out[$r->period];
            if ((int) $r->status === 14) {
                $o['returns_value'] += (float) $r->net;
                continue;
            }
            $o['fbr_tax'] += (float) $r->fbr_tax;
            $o['srb_tax'] += (float) $r->srb_tax;
            $o['value'] += (float) $r->net - (float) $r->fbr_tax - (float) $r->srb_tax - (float) $r->charges;
            foreach (['receipts', 'taxed', 'fbr_numbered', 'fbr_numbered_tax', 'missing_fbr', 'missing_fbr_tax'] as $k) {
                $o[$k] += (float) $r->{$k};
            }
            unset($o);
        }

        foreach ($this->purchaseQuery($companyId, $from, $to, $branchId)
            ->groupBy(DB::raw('SUBSTR(b.bill_date, 1, 7)'))
            ->selectRaw("SUBSTR(b.bill_date, 1, 7) AS period,
                SUM(CASE WHEN b.type = 'debit_note' THEN -l.tax_amount ELSE l.tax_amount END) AS tax,
                SUM(CASE WHEN b.type = 'debit_note' THEN -l.amount ELSE l.amount END) AS value")->get() as $r) {
            if (isset($out[$r->period])) {
                $out[$r->period]['input_tax'] = round((float) $r->tax, 2);
                $out[$r->period]['purchase_value'] = round((float) $r->value, 2);
            }
        }

        foreach ($out as &$o) {
            foreach ($o as $k => $v) {
                $o[$k] = round($v, 2);
            }
        }
        return $out;
    }

    /** GL ka mahana output (2130 net credit) / input (1160 net debit) — sirf accounting on; warna null */
    public function ledger(int $companyId, string $fromPeriod, string $toPeriod): ?array
    {
        if (!AccountingSetting::isEnabled($companyId)) {
            return null;
        }
        $ids = DB::table('chart_of_accounts')->where('company_id', $companyId)->whereIn('system_key', ['output_tax', 'input_tax'])->pluck('id', 'system_key');
        $out = [];
        foreach (DB::table('journal_entry_lines as l')->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->where('e.company_id', $companyId)->where('e.status', 'posted')
            ->whereBetween('e.entry_date', [$fromPeriod . '-01', date('Y-m-t', strtotime($toPeriod . '-01'))])
            ->whereIn('l.account_id', $ids->values())
            ->where(fn ($q) => $q->whereNull('e.source_type')->orWhere('e.source_type', '<>', 'sales_tax_filing'))
            ->groupBy(DB::raw("DATE_FORMAT(e.entry_date, '%Y-%m')"), 'l.account_id')
            ->selectRaw("DATE_FORMAT(e.entry_date, '%Y-%m') AS period, l.account_id, SUM(l.debit) AS dr, SUM(l.credit) AS cr")->get() as $r) {
            $out[$r->period]['output'] = ($out[$r->period]['output'] ?? 0) + ((int) $r->account_id === (int) ($ids['output_tax'] ?? 0) ? $r->cr - $r->dr : 0);
            $out[$r->period]['input'] = ($out[$r->period]['input'] ?? 0) + ((int) $r->account_id === (int) ($ids['input_tax'] ?? 0) ? $r->dr - $r->cr : 0);
        }
        return $out;
    }

    /** sales register: tax wali receipts (ya $missingOnly = FBR branch + bina FBR number) */
    public function salesRegister(int $companyId, string $period, ?int $branchId, bool $missingOnly = false, ?int $limit = 500)
    {
        $branches = $this->branches($companyId, $branchId) ?: [0];
        $fbrBranches = DB::table('fbr_details')->where('company_id', $companyId)->where('status', 1)->whereNull('deleted_at')->pluck('branch_id')->all() ?: [0];
        return DB::table('sales_receipts as r')
            ->leftJoin('sales_account_subdetails as s', 's.id', '=', DB::raw('(SELECT MAX(s2.id) FROM sales_account_subdetails s2 WHERE s2.receipt_id = r.id)'))
            ->leftJoin('branch as b', 'b.branch_id', '=', 'r.branch')
            ->leftJoin('customers as c', 'c.id', '=', 'r.customer_id')
            ->whereIn('r.branch', $branches)->whereBetween('r.date', [$period . '-01', date('Y-m-t', strtotime($period . '-01'))])
            ->where('r.date', 'like', '____-__-__')->where('r.status', 4)->where('r.void_receipt', 0)
            ->when($missingOnly,
                fn ($q) => $q->whereIn('r.branch', $fbrBranches)->whereRaw("COALESCE(r.fbrInvNumber, '') = ''")->where('s.sales_tax_amount', '>', 0),
                fn ($q) => $q->whereRaw('COALESCE(s.sales_tax_amount, 0) + COALESCE(s.srb + 0, 0) > 0'))
            ->orderBy('r.date')->orderBy('r.id')
            ->when($limit, fn ($q) => $q->limit($limit))
            ->get(['r.id', 'r.receipt_no', 'r.date', 'r.fbrInvNumber', 'b.branch_name', 'c.name as customer', 'c.nic',
                DB::raw('r.total_amount + 0 AS net'), DB::raw('COALESCE(s.sales_tax_amount, 0) AS fbr_tax'), DB::raw('COALESCE(s.srb + 0, 0) AS srb_tax'),
                DB::raw('COALESCE(s.service_tax_amount, 0) + COALESCE(s.delivery_charges_amount, 0) AS charges')]);
    }

    /** purchase register: posted vendor bills / debit notes, bill-wise */
    public function purchaseRegister(int $companyId, string $period, ?int $branchId)
    {
        return $this->purchaseQuery($companyId, $period . '-01', date('Y-m-t', strtotime($period . '-01')), $branchId)
            ->join('vendors as v', 'v.id', '=', 'b.vendor_id')
            ->groupBy('b.id', 'b.doc_no', 'b.type', 'b.bill_date', 'b.vendor_ref', 'v.vendor_name', 'v.ntn', 'v.strn')
            ->orderBy('b.bill_date')->orderBy('b.id')
            ->get(['b.id', 'b.doc_no', 'b.type', 'b.bill_date', 'b.vendor_ref', 'v.vendor_name', 'v.ntn', 'v.strn',
                DB::raw('SUM(l.amount) AS value'), DB::raw('SUM(l.tax_amount) AS tax')]);
    }

    public function filings(int $companyId)
    {
        return DB::table('sales_tax_filings')->where('company_id', $companyId)->orderByDesc('period')->orderBy('authority')->get();
    }

    /**
     * Mahina file karo. $d: period, authority, adjustment, paid_amount, payment_ref, filed_on, notes, post_entry (bool), paid_from (account id).
     * Output / input summary se (file karte waqt ki tasveer). GL entry (accounting on + post_entry):
     *   Dr Output Tax (output) / Cr Input Tax (input, sirf FBR) / Cr paid-from (paid) / farq → Adjustments Clearing.
     */
    public function file(int $companyId, array $d, $userId = null): int
    {
        $period = (string) ($d['period'] ?? '');
        $authority = (string) ($d['authority'] ?? '');
        if (!preg_match('/^\d{4}-\d{2}$/', $period) || !isset(self::AUTHORITIES[$authority])) {
            throw new RuntimeException('Choose the month and the tax authority.');
        }
        if ($period >= date('Y-m')) {
            throw new RuntimeException('A month can be filed only after it ends.');
        }
        if (DB::table('sales_tax_filings')->where(['company_id' => $companyId, 'period' => $period, 'authority' => $authority])->exists()) {
            throw new RuntimeException('This month is already filed. Delete the filing first to file it again.');
        }
        $num = fn ($v) => round((float) str_replace(',', '', (string) ($v ?? 0)), 2);
        $paid = $num($d['paid_amount'] ?? 0);
        $adj = $num($d['adjustment'] ?? 0);
        if ($paid < 0) {
            throw new RuntimeException('Paid amount cannot be negative.');
        }
        $filedOn = ($d['filed_on'] ?? '') ?: date('Y-m-d');
        $s = $this->summary($companyId, $period, $period)[$period];
        $output = $authority === 'fbr' ? $s['fbr_tax'] : $s['srb_tax'];
        $input = $authority === 'fbr' ? max(0, $s['input_tax']) : 0.0;

        return DB::transaction(function () use ($companyId, $period, $authority, $output, $input, $adj, $paid, $d, $filedOn, $userId) {
            $entryId = null;
            if (!empty($d['post_entry']) && AccountingSetting::isEnabled($companyId)) {
                $entryId = $this->postEntry($companyId, $period, $authority, $output, $input, $paid, (int) ($d['paid_from'] ?? 0), $filedOn, $userId);
            }
            return DB::table('sales_tax_filings')->insertGetId([
                'company_id' => $companyId, 'period' => $period, 'authority' => $authority, 'output_tax' => $output, 'input_tax' => $input,
                'adjustment' => $adj, 'paid_amount' => $paid, 'payment_ref' => trim((string) ($d['payment_ref'] ?? '')) ?: null, 'filed_on' => $filedOn,
                'notes' => trim((string) ($d['notes'] ?? '')) ?: null, 'journal_entry_id' => $entryId, 'created_by' => $userId,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        });
    }

    /** filing hatao; GL entry ho to reverse */
    public function deleteFiling(int $companyId, int $id, $userId = null): void
    {
        $f = DB::table('sales_tax_filings')->where('company_id', $companyId)->where('id', $id)->first();
        if (!$f) {
            throw new RuntimeException('Filing not found.');
        }
        DB::transaction(function () use ($f, $userId) {
            if ($f->journal_entry_id && ($e = \App\Models\Accounting\JournalEntry::find($f->journal_entry_id)) && $e->status === 'posted'
                && !\App\Models\Accounting\JournalEntry::where('reversal_of_id', $e->id)->exists()) {
                app(JournalService::class)->reverse($e, date('Y-m-d'), $userId, 'Sales tax filing deleted');
            }
            DB::table('sales_tax_filings')->where('id', $f->id)->delete();
        });
    }

    /** cash / bank accounts (paid from): Cash + Bank Reconciliation wale bank accounts */
    public function payAccounts(int $companyId)
    {
        $cash = DB::table('chart_of_accounts')->where('company_id', $companyId)->where('system_key', 'cash')->where('is_active', 1)->get(['id', 'code', 'name']);
        return $cash->concat(app(BankReconciliationService::class)->bankAccounts($companyId))->unique('id')->sortBy('code')->values();
    }

    private function postEntry(int $companyId, string $period, string $authority, float $output, float $input, float $paid, int $paidFrom, string $date, $userId): int
    {
        $acc = fn ($key) => (int) DB::table('chart_of_accounts')->where('company_id', $companyId)->where('system_key', $key)->value('id');
        $lines = [];
        if ($output > 0) {
            $lines[] = ['account_id' => $acc('output_tax'), 'debit' => $output, 'credit' => 0, 'narration' => 'Output tax ' . $period];
        }
        if ($input > 0) {
            $lines[] = ['account_id' => $acc('input_tax'), 'debit' => 0, 'credit' => $input, 'narration' => 'Input tax adjusted ' . $period];
        }
        if ($paid > 0) {
            if (!$this->payAccounts($companyId)->contains('id', $paidFrom)) {
                throw new RuntimeException('Choose the cash / bank account the tax was paid from.');
            }
            $lines[] = ['account_id' => $paidFrom, 'debit' => 0, 'credit' => $paid, 'narration' => 'Tax paid ' . $period];
        }
        $diff = round($output - $input - $paid, 2);
        if ($diff != 0) {
            $clearing = $acc('adjustments_clearing');
            if (!$clearing) {
                throw new RuntimeException('Adjustments Clearing account (2195) is missing.');
            }
            $lines[] = ['account_id' => $clearing, 'debit' => $diff < 0 ? -$diff : 0, 'credit' => $diff > 0 ? $diff : 0,
                'narration' => $diff > 0 ? 'Tax payable not yet paid / carried' : 'Refund / carry forward'];
        }
        if (count($lines) < 2) {
            throw new RuntimeException('Nothing to post for this month.');
        }
        $entry = app(JournalService::class)->createPosted($companyId, [
            'entry_date' => $date, 'branch_id' => null, 'narration' => self::AUTHORITIES[$authority] . ' return ' . $period,
            'source_type' => 'sales_tax_filing', 'source_ref' => $authority . ':' . $period,
        ], $lines, $userId);
        return (int) $entry->id;
    }

    private function purchaseQuery(int $companyId, string $from, string $to, ?int $branchId)
    {
        return DB::table('vendor_bill_lines as l')->join('vendor_bills as b', 'b.id', '=', 'l.vendor_bill_id')
            ->where('b.company_id', $companyId)->where('b.status', 'posted')->whereIn('b.type', ['bill', 'po_bill', 'debit_note'])
            ->whereBetween('b.bill_date', [$from, $to])
            ->when($branchId, fn ($q) => $q->where('b.branch_id', $branchId));
    }

    private function branches(int $companyId, ?int $branchId): array
    {
        $all = SourceQuery::branchIds($companyId);
        return $branchId ? array_values(array_intersect($all, [$branchId])) : $all;
    }
}
