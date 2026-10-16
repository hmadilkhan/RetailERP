<?php

namespace App\Services\QuickBooks;

use App\Models\Accounting\AccountingSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

/**
 * Phase 3.6: GL → QuickBooks Online. Har posted journal entry (sync_from_date se) ek QBO JournalEntry (DocNumber = entry_no):
 * lines account mapping (`qbo_account_map`) se. Reversal bhi apni alag entry ke taur pe jati hai (QBO me bhi ulti entry).
 * Record `qbo_journal_sync` (synced / failed + error). Dobara bhejne se pehle DocNumber dhoondte hain (crash ke baad duplicate nahi).
 * Failed (mapping nahi / QBO error) har run me dobara, 5 koshish tak; mapping save karne pe koshishen phir 0.
 */
class QboJournalSyncService
{
    const MAX_ATTEMPTS = 5;

    /** test me fake client: fn (int $companyId): QboClient */
    public static $clientFactory = null;

    public function client(int $companyId): QboClient
    {
        return self::$clientFactory ? (self::$clientFactory)($companyId) : new QboSdkClient($companyId);
    }

    /** migration chali? (null = check, cache) */
    public static ?bool $ready = null;

    public function ready(): bool
    {
        return self::$ready ??= Schema::hasTable('qbo_journal_sync') && Schema::hasColumn('quickbook_settings', 'sync_journals');
    }

    public function setting(int $companyId): ?object
    {
        return DB::table('quickbook_settings')->where('company_id', $companyId)->first();
    }

    public function saveSettings(int $companyId, bool $enabled, ?string $fromDate): void
    {
        if (!$this->setting($companyId)) {
            throw new RuntimeException('Connect QuickBooks first.');
        }
        if ($enabled && !$fromDate) {
            throw new RuntimeException('Choose the date to start sending journal entries from.');
        }
        DB::table('quickbook_settings')->where('company_id', $companyId)->update([
            'sync_journals' => $enabled ? 1 : 0, 'sync_from_date' => $fromDate ?: null, 'updated_at' => now(),
        ]);
    }

    /** jin accounts pe from-date se posted lines hain (mapping inhi ki chahiye) + mapping */
    public function accountsToMap(int $companyId, ?string $from): array
    {
        $used = DB::table('journal_entry_lines as l')->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->where('e.company_id', $companyId)->where('e.status', 'posted')
            ->when($from, fn ($q) => $q->where('e.entry_date', '>=', $from))
            ->distinct()->pluck('l.account_id')->flip();
        $map = DB::table('qbo_account_map')->where('company_id', $companyId)->get()->keyBy('account_id');
        return DB::table('chart_of_accounts')->where('company_id', $companyId)->where('is_group', 0)
            ->orderBy('code')->get(['id', 'code', 'name', 'type'])
            ->map(fn ($a) => (array) $a + ['used' => isset($used[$a->id]), 'qbo_account_id' => $map[$a->id]->qbo_account_id ?? null,
                'qbo_account_name' => $map[$a->id]->qbo_account_name ?? null])
            ->all();
    }

    /** $map: account_id => qbo_account_id ('' = hatao); $qboAccounts: client()->accounts() (naam ke liye) */
    public function saveMapping(int $companyId, array $map, array $qboAccounts = []): void
    {
        $names = collect($qboAccounts)->pluck('name', 'id');
        $valid = DB::table('chart_of_accounts')->where('company_id', $companyId)->pluck('id')->flip();
        DB::transaction(function () use ($companyId, $map, $names, $valid) {
            foreach ($map as $accountId => $qboId) {
                if (!isset($valid[(int) $accountId])) {
                    continue;
                }
                $where = ['company_id' => $companyId, 'account_id' => (int) $accountId];
                if ((string) $qboId === '') {
                    DB::table('qbo_account_map')->where($where)->delete();
                    continue;
                }
                $existing = DB::table('qbo_account_map')->where($where)->first();
                DB::table('qbo_account_map')->updateOrInsert($where, ['qbo_account_id' => (string) $qboId,
                    'qbo_account_name' => $names[(string) $qboId] ?? ($existing && $existing->qbo_account_id === (string) $qboId ? $existing->qbo_account_name : null),
                    'updated_at' => now(), 'created_at' => $existing->created_at ?? now()]);
            }
            DB::table('qbo_journal_sync')->where('company_id', $companyId)->where('status', 'failed')->update(['attempts' => 0, 'updated_at' => now()]);
        });
    }

    /** khali mapping: QBO account number = hamara code, warna naam barabar (case ke baghair). @return account_id => qbo_id */
    public function suggestMapping(int $companyId, array $qboAccounts): array
    {
        $byNumber = [];
        $byName = [];
        foreach ($qboAccounts as $q) {
            if ($q['number']) {
                $byNumber[strtolower(trim($q['number']))] = $q['id'];
            }
            $leaf = strtolower(trim(str_contains($q['name'], ':') ? substr(strrchr($q['name'], ':'), 1) : $q['name']));
            $byName[$leaf] = $byName[$leaf] ?? $q['id'];
        }
        $out = [];
        foreach ($this->accountsToMap($companyId, null) as $a) {
            if ($a['qbo_account_id']) {
                continue;
            }
            $id = $byNumber[strtolower($a['code'])] ?? $byName[strtolower(trim($a['name']))] ?? null;
            if ($id) {
                $out[$a['id']] = $id;
            }
        }
        return $out;
    }

    public function counts(int $companyId, ?string $from): array
    {
        $synced = DB::table('qbo_journal_sync')->where('company_id', $companyId)->where('status', 'synced')->count();
        $failed = DB::table('qbo_journal_sync')->where('company_id', $companyId)->where('status', 'failed')->count();
        $pending = $from ? $this->pendingQuery($companyId, $from, false)->count() : 0;
        return ['synced' => $synced, 'failed' => $failed, 'pending' => $pending];
    }

    public function failures(int $companyId, int $limit = 50)
    {
        return DB::table('qbo_journal_sync as s')->join('journal_entries as e', 'e.id', '=', 's.journal_entry_id')
            ->where('s.company_id', $companyId)->where('s.status', 'failed')->orderBy('e.entry_date')->limit($limit)
            ->get(['s.*', 'e.entry_no', 'e.entry_date', 'e.narration']);
    }

    public function retryFailed(int $companyId): void
    {
        DB::table('qbo_journal_sync')->where('company_id', $companyId)->where('status', 'failed')->update(['attempts' => 0, 'updated_at' => now()]);
    }

    /** @return [synced, failed, errors[]] */
    public function sync(int $companyId, int $limit = 200): array
    {
        $s = $this->setting($companyId);
        if (!$s || empty($s->sync_journals) || !$s->sync_from_date) {
            throw new RuntimeException('Journal sync is off for this company.');
        }
        if (!AccountingSetting::isEnabled($companyId)) {
            throw new RuntimeException('Accounting is not enabled for this company.');
        }
        $entries = $this->pendingQuery($companyId, $s->sync_from_date, true)->orderBy('e.entry_date')->orderBy('e.id')->limit($limit)->get(['e.*']);
        $result = ['synced' => 0, 'failed' => 0, 'errors' => []];
        if ($entries->isEmpty()) {
            DB::table('quickbook_settings')->where('company_id', $companyId)->update(['last_journal_sync_at' => now()]);
            return $result;
        }

        $client = $this->client($companyId);
        $map = DB::table('qbo_account_map')->where('company_id', $companyId)->pluck('qbo_account_id', 'account_id');
        $lines = DB::table('journal_entry_lines as l')->join('chart_of_accounts as a', 'a.id', '=', 'l.account_id')
            ->whereIn('l.journal_entry_id', $entries->pluck('id'))->orderBy('l.id')
            ->get(['l.*', 'a.code', 'a.name'])->groupBy('journal_entry_id');

        foreach ($entries as $e) {
            try {
                $payload = $this->payload($e, $lines[$e->id] ?? collect(), $map);
                $qboId = $client->findJournal($e->entry_no) ?? $client->createJournal($payload);
                $this->record($companyId, $e->id, 'synced', $qboId, null);
                $result['synced']++;
            } catch (Throwable $ex) {
                $this->record($companyId, $e->id, 'failed', null, mb_substr($ex->getMessage(), 0, 1000));
                $result['failed']++;
                $result['errors'][] = $e->entry_no . ': ' . $ex->getMessage();
                if ($ex instanceof RuntimeException && str_contains($ex->getMessage(), ' 401 ')) {
                    break; // token kharab — baaqi bhi fail honge
                }
            }
        }
        DB::table('quickbook_settings')->where('company_id', $companyId)->update(['last_journal_sync_at' => now()]);
        return $result;
    }

    public function payload(object $entry, $lines, $map): array
    {
        $missing = [];
        $out = [];
        $dr = 0.0;
        $cr = 0.0;
        foreach ($lines as $l) {
            $debit = round((float) $l->debit, 2);
            $credit = round((float) $l->credit, 2);
            if ($debit == 0 && $credit == 0) {
                continue;
            }
            if (empty($map[$l->account_id])) {
                $missing[$l->account_id] = $l->code . ' ' . $l->name;
                continue;
            }
            $dr += $debit;
            $cr += $credit;
            $out[] = [
                'Description' => mb_substr((string) ($l->narration ?: $entry->narration), 0, 4000) ?: null,
                'Amount' => $debit ?: $credit,
                'DetailType' => 'JournalEntryLineDetail',
                'JournalEntryLineDetail' => ['PostingType' => $debit ? 'Debit' : 'Credit', 'AccountRef' => ['value' => (string) $map[$l->account_id]]],
            ];
        }
        if ($missing) {
            throw new RuntimeException('Map these accounts to QuickBooks: ' . implode(', ', $missing));
        }
        if (count($out) < 2 || abs($dr - $cr) > 0.005) {
            throw new RuntimeException('Entry is not balanced after rounding.');
        }
        return array_filter([
            'DocNumber' => mb_substr($entry->entry_no, 0, 21),
            'TxnDate' => substr((string) $entry->entry_date, 0, 10),
            'PrivateNote' => mb_substr(trim(($entry->narration ?? '') . ' [Sabify ' . $entry->entry_no . ']'), 0, 4000),
            'Line' => array_map(fn ($l) => array_filter($l, fn ($v) => $v !== null), $out),
        ], fn ($v) => $v !== null && $v !== '');
    }

    /** posted entries jo sync nahi hui (ya failed + koshish baqi) */
    private function pendingQuery(int $companyId, string $from, bool $withRetries)
    {
        return DB::table('journal_entries as e')
            ->leftJoin('qbo_journal_sync as s', fn ($j) => $j->on('s.journal_entry_id', '=', 'e.id')->where('s.company_id', $companyId))
            ->where('e.company_id', $companyId)->where('e.status', 'posted')->where('e.entry_date', '>=', $from)
            ->where(fn ($q) => $withRetries
                ? $q->whereNull('s.id')->orWhere(fn ($r) => $r->where('s.status', 'failed')->where('s.attempts', '<', self::MAX_ATTEMPTS))
                : $q->whereNull('s.id'));
    }

    private function record(int $companyId, int $entryId, string $status, ?string $qboId, ?string $error): void
    {
        $where = ['company_id' => $companyId, 'journal_entry_id' => $entryId];
        $existing = DB::table('qbo_journal_sync')->where($where)->first();
        DB::table('qbo_journal_sync')->updateOrInsert($where, [
            'status' => $status, 'qbo_id' => $qboId, 'error' => $error,
            'attempts' => ($existing->attempts ?? 0) + 1, 'synced_at' => $status === 'synced' ? now() : null,
            'created_at' => $existing->created_at ?? now(), 'updated_at' => now(),
        ]);
    }
}
