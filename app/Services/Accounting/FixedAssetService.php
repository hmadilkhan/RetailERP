<?php

namespace App\Services\Accounting;

use App\Services\Accounting\Posting\AccountResolver;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Phase 3.3: fixed asset register + depreciation.
 *   Khareed (ikhtiyari JV): Dr asset account / Cr paid-from account (bank / cash / AP / Opening Balance Equity)
 *   Mahana depreciation: ek company-month ki ek JV — Dr Depreciation Expense / Cr Accumulated Depreciation (source
 *     'depreciation', source_ref YYYY-MM); har asset ka hissa fixed_asset_depreciations me (dobara nahi lagta)
 *     straight line = (cost − salvage − opening accumulated) / baaqi mahine; declining = book value × rate% / 12;
 *     book value salvage se neeche nahi.
 *   Dispose: Dr Accumulated (ab tak ka), Dr received account (proceeds), Cr asset cost, farq → 4950 Gain / Loss.
 * JournalService period rules lagte hain — band period me depreciation nahi chalti.
 */
class FixedAssetService
{
    const SOURCE_PURCHASE = 'fixed_asset';
    const SOURCE_DEPRECIATION = 'depreciation';

    public function create(int $companyId, array $d, $userId, ?int $paidFromAccountId = null): int
    {
        $this->assertAccount($companyId, (int) $d['asset_account_id']);
        $cost = round((float) $d['cost'], 2);
        $salvage = round((float) ($d['salvage_value'] ?? 0), 2);
        $life = (int) $d['useful_life_months'];
        $openingAcc = round((float) ($d['opening_accumulated'] ?? 0), 2);
        $method = ($d['method'] ?? 'straight_line') === 'declining' ? 'declining' : 'straight_line';
        if ($cost <= 0 || $life <= 0 || $salvage < 0 || $salvage >= $cost || $openingAcc < 0 || $openingAcc > $cost - $salvage) {
            throw new RuntimeException('Check cost, salvage value, life and opening depreciation.');
        }
        if ($method === 'declining' && ((float) ($d['rate'] ?? 0) <= 0 || (float) $d['rate'] >= 100)) {
            throw new RuntimeException('Enter a yearly rate between 0 and 100 for the declining method.');
        }
        $start = date('Y-m-01', strtotime($d['depreciation_start'] ?? $d['purchase_date']));

        return DB::transaction(function () use ($companyId, $d, $userId, $paidFromAccountId, $cost, $salvage, $life, $openingAcc, $method, $start) {
            $last = DB::table('fixed_assets')->where('company_id', $companyId)->lockForUpdate()->orderByDesc('id')->value('code');
            $code = 'FA-' . str_pad((string) ((int) substr((string) $last, 3) + 1), 4, '0', STR_PAD_LEFT);
            $id = DB::table('fixed_assets')->insertGetId([
                'company_id' => $companyId, 'branch_id' => ($d['branch_id'] ?? null) ?: null, 'code' => $code,
                'name' => trim((string) $d['name']), 'asset_account_id' => (int) $d['asset_account_id'],
                'purchase_date' => $d['purchase_date'], 'depreciation_start' => $start,
                'cost' => $cost, 'salvage_value' => $salvage, 'useful_life_months' => $life, 'method' => $method,
                'rate' => $method === 'declining' ? round((float) $d['rate'], 4) : null, 'opening_accumulated' => $openingAcc,
                'status' => 'active', 'notes' => ($d['notes'] ?? null) ?: null, 'created_by' => $userId,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            if ($paidFromAccountId) {
                $this->assertAccount($companyId, $paidFromAccountId);
                $entry = app(JournalService::class)->createPosted($companyId, [
                    'entry_date' => $d['purchase_date'], 'branch_id' => ($d['branch_id'] ?? null) ?: null,
                    'narration' => 'Fixed asset ' . $code . ' — ' . trim((string) $d['name']),
                    'source_type' => self::SOURCE_PURCHASE, 'source_id' => $id,
                ], [
                    ['account_id' => (int) $d['asset_account_id'], 'debit' => $cost, 'credit' => 0],
                    ['account_id' => $paidFromAccountId, 'debit' => 0, 'credit' => $cost],
                ], $userId);
                DB::table('fixed_assets')->where('id', $id)->update(['purchase_entry_id' => $entry->id]);
            }
            return $id;
        });
    }

    /** ab tak ka depreciation (opening + system ka) */
    public function accumulated(object $asset): float
    {
        return round((float) $asset->opening_accumulated + (float) DB::table('fixed_asset_depreciations')->where('fixed_asset_id', $asset->id)->sum('amount'), 2);
    }

    /** ek mahine ka depreciation (abhi ke accumulated ke hisaab se); 0 = khatam / shuru nahi */
    public function monthAmount(object $asset, string $period, float $accumulated): float
    {
        if ($period < substr($asset->depreciation_start, 0, 7)) {
            return 0.0;
        }
        $depreciable = (float) $asset->cost - (float) $asset->salvage_value;
        $left = round($depreciable - $accumulated, 2);
        if ($left <= 0) {
            return 0.0;
        }
        if ($asset->method === 'declining') {
            $amount = ((float) $asset->cost - $accumulated) * (float) $asset->rate / 100 / 12;
        } else {
            $done = (int) DB::table('fixed_asset_depreciations')->where('fixed_asset_id', $asset->id)->where('period', '<', $period)->count();
            $openingMonths = $depreciable > 0 ? (int) round((float) $asset->opening_accumulated / ($depreciable / $asset->useful_life_months)) : 0;
            $remaining = max(1, (int) $asset->useful_life_months - $openingMonths - $done);
            $amount = $left / $remaining;
        }
        return round(min($left, max(0, $amount)), 2);
    }

    /**
     * $upTo (YYYY-MM) tak jo mahine baaqi hain un ki depreciation — har mahine ki company ki ek JV.
     * @return array period => [entry_no, amount, assets]
     */
    public function run(int $companyId, string $upTo, $userId): array
    {
        $upTo = substr($upTo, 0, 7);
        $assets = DB::table('fixed_assets')->where('company_id', $companyId)->where('status', 'active')
            ->where('depreciation_start', '<=', $upTo . '-01')->orderBy('id')->get();
        if ($assets->isEmpty()) {
            return [];
        }
        $resolver = new AccountResolver($companyId);
        $expense = $resolver->id('depreciation_expense');
        $accumulatedAccount = $resolver->id('accumulated_depreciation');

        $start = substr((string) $assets->min('depreciation_start'), 0, 7);
        $done = [];
        for ($p = $start; $p <= $upTo; $p = date('Y-m', strtotime($p . '-01 +1 month'))) {
            $lines = [];
            $rows = [];
            foreach ($assets as $asset) {
                if (DB::table('fixed_asset_depreciations')->where('fixed_asset_id', $asset->id)->where('period', $p)->exists()) {
                    continue;
                }
                $amount = $this->monthAmount($asset, $p, $this->accumulated($asset));
                if ($amount <= 0) {
                    continue;
                }
                $rows[] = [$asset->id, $amount];
                $lines[] = ['account_id' => $expense, 'debit' => $amount, 'credit' => 0, 'narration' => $asset->code . ' ' . $asset->name];
                $lines[] = ['account_id' => $accumulatedAccount, 'debit' => 0, 'credit' => $amount, 'narration' => $asset->code . ' ' . $asset->name];
            }
            if (!$rows) {
                continue;
            }
            DB::transaction(function () use ($companyId, $p, $lines, $rows, $userId, &$done) {
                $entry = app(JournalService::class)->createPosted($companyId, [
                    'entry_date' => date('Y-m-t', strtotime($p . '-01')), 'branch_id' => null,
                    'narration' => 'Depreciation ' . date('M Y', strtotime($p . '-01')),
                    'source_type' => self::SOURCE_DEPRECIATION, 'source_ref' => $p,
                ], $lines, $userId);
                foreach ($rows as [$assetId, $amount]) {
                    DB::table('fixed_asset_depreciations')->insert(['fixed_asset_id' => $assetId, 'period' => $p, 'amount' => $amount,
                        'journal_entry_id' => $entry->id, 'created_at' => now(), 'updated_at' => now()]);
                }
                $done[$p] = ['entry_no' => $entry->entry_no, 'amount' => round(array_sum(array_column($rows, 1)), 2), 'assets' => count($rows)];
            });
        }
        return $done;
    }

    /** bechna / hatana: proceeds $receivedAccountId me (0 = mufta / scrap) */
    public function dispose(int $companyId, int $assetId, string $date, float $proceeds, ?int $receivedAccountId, $userId): int
    {
        $asset = DB::table('fixed_assets')->where('company_id', $companyId)->where('id', $assetId)->first();
        if (!$asset || $asset->status !== 'active') {
            throw new RuntimeException('Only an active asset can be disposed.');
        }
        $period = substr($date, 0, 7);
        if (DB::table('fixed_asset_depreciations')->where('fixed_asset_id', $assetId)->where('period', '>', $period)->exists()) {
            throw new RuntimeException('Depreciation is already posted after this date.');
        }
        $proceeds = round($proceeds, 2);
        if ($proceeds < 0 || ($proceeds > 0 && !$receivedAccountId)) {
            throw new RuntimeException('Select where the sale money was received.');
        }
        $accumulated = $this->accumulated($asset);
        $book = round((float) $asset->cost - $accumulated, 2);
        $gain = round($proceeds - $book, 2);
        $resolver = new AccountResolver($companyId);
        $lines = [
            ['account_id' => (int) $asset->asset_account_id, 'debit' => 0, 'credit' => (float) $asset->cost],
        ];
        if ($accumulated > 0) {
            $lines[] = ['account_id' => $resolver->id('accumulated_depreciation'), 'debit' => $accumulated, 'credit' => 0];
        }
        if ($proceeds > 0) {
            $this->assertAccount($companyId, (int) $receivedAccountId);
            $lines[] = ['account_id' => (int) $receivedAccountId, 'debit' => $proceeds, 'credit' => 0];
        }
        if (abs($gain) >= 0.005) {
            $lines[] = ['account_id' => $resolver->id('asset_disposal'), 'debit' => $gain < 0 ? -$gain : 0, 'credit' => $gain > 0 ? $gain : 0];
        }

        return DB::transaction(function () use ($companyId, $asset, $date, $proceeds, $lines, $userId) {
            $entry = app(JournalService::class)->createPosted($companyId, [
                'entry_date' => $date, 'branch_id' => $asset->branch_id,
                'narration' => 'Disposal of fixed asset ' . $asset->code . ' — ' . $asset->name,
                'source_type' => self::SOURCE_PURCHASE, 'source_id' => $asset->id,
            ], $lines, $userId);
            DB::table('fixed_assets')->where('id', $asset->id)->update(['status' => 'disposed', 'disposed_on' => $date,
                'disposal_amount' => $proceeds, 'disposal_entry_id' => $entry->id, 'updated_at' => now()]);
            return $entry->id;
        });
    }

    /** aage ka andaza (save nahi) — period => amount, book value */
    public function schedule(object $asset, int $months = 24): array
    {
        $acc = $this->accumulated($asset);
        $out = [];
        $lastPosted = DB::table('fixed_asset_depreciations')->where('fixed_asset_id', $asset->id)->max('period');
        $p = $lastPosted ? date('Y-m', strtotime($lastPosted . '-01 +1 month')) : substr($asset->depreciation_start, 0, 7);
        $sim = clone $asset;
        $doneBefore = (int) DB::table('fixed_asset_depreciations')->where('fixed_asset_id', $asset->id)->count();
        $depreciable = (float) $asset->cost - (float) $asset->salvage_value;
        $openingMonths = $depreciable > 0 ? (int) round((float) $asset->opening_accumulated / ($depreciable / $asset->useful_life_months)) : 0;
        for ($i = 0; $i < $months; $i++) {
            $left = round($depreciable - $acc, 2);
            if ($left <= 0) {
                break;
            }
            $amount = $sim->method === 'declining'
                ? ((float) $sim->cost - $acc) * (float) $sim->rate / 100 / 12
                : $left / max(1, (int) $sim->useful_life_months - $openingMonths - $doneBefore - $i);
            $amount = round(min($left, max(0, $amount)), 2);
            $acc += $amount;
            $out[$p] = ['amount' => $amount, 'book_value' => round((float) $sim->cost - $acc, 2)];
            $p = date('Y-m', strtotime($p . '-01 +1 month'));
        }
        return $out;
    }

    private function assertAccount(int $companyId, int $accountId): void
    {
        if (!DB::table('chart_of_accounts')->where('company_id', $companyId)->where('id', $accountId)->where('is_group', 0)->exists()) {
            throw new RuntimeException('Select a valid account.');
        }
    }
}
