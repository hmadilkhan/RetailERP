<?php

namespace App\Services\Accounting;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Phase 3.1: bank statement ↔ GL bank account (chart_of_accounts). Purani bank_deposit_details live pe khali hai —
 * asal bank hisaab GL me hai (POS card / online sales, vendor payments, JV), isliye sirf accounting-enabled company.
 * GL line ki raqam = debit − credit (+ = bank me aaya). Statement line raqam bhi + = aaya, − = gaya.
 * Zinda entries hi ginti hain: jo reverse ho gayi aur reversal entries dono bahar (automatic posting reverse + repost karti
 * hai — aisi entry ka match khud hat jata hai, nayi entry auto-match se phir judti hai).
 * Ek GL line ek hi statement line se (bank_matches unique) — reconciled statement ki lines "cleared".
 * Statement ki bank charges / profit jaisi line → "Create entry" (JournalService createPosted, source bank_reconciliation) + match.
 */
class BankReconciliationService
{
    const SOURCE = 'bank_reconciliation';

    public function create(int $companyId, int $accountId, string $from, string $to, float $opening, float $closing, $userId): int
    {
        $this->assertBankAccount($companyId, $accountId);
        if ($to < $from) {
            throw new RuntimeException('Statement end date must be after the start date.');
        }
        return DB::table('bank_reconciliations')->insertGetId([
            'company_id' => $companyId, 'account_id' => $accountId, 'statement_from' => $from, 'statement_to' => $to,
            'opening_balance' => round($opening, 2), 'closing_balance' => round($closing, 2), 'status' => 'open',
            'created_by' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function get(int $companyId, int $id): object
    {
        $rec = DB::table('bank_reconciliations')->where('company_id', $companyId)->where('id', $id)->first();
        if (!$rec) {
            throw new RuntimeException('Reconciliation not found.');
        }
        return $rec;
    }

    public function addLine(int $companyId, int $recId, string $date, ?string $description, ?string $reference, float $amount): int
    {
        $rec = $this->openRec($companyId, $recId);
        if (round($amount, 2) == 0.0) {
            throw new RuntimeException('Amount cannot be zero.');
        }
        if ($date < $rec->statement_from || $date > $rec->statement_to) {
            throw new RuntimeException('Date must be inside the statement period.');
        }
        return DB::table('bank_statement_lines')->insertGetId([
            'reconciliation_id' => $recId, 'txn_date' => $date, 'description' => $description ? mb_substr($description, 0, 500) : null,
            'reference' => $reference ? mb_substr($reference, 0, 100) : null, 'amount' => round($amount, 2), 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function deleteLine(int $companyId, int $recId, int $lineId): void
    {
        $this->openRec($companyId, $recId);
        if (DB::table('bank_matches')->where('statement_line_id', $lineId)->exists()) {
            throw new RuntimeException('Unmatch the line first.');
        }
        DB::table('bank_statement_lines')->where('reconciliation_id', $recId)->where('id', $lineId)->delete();
    }

    /**
     * CSV / Excel rows (pehli row header). Columns naam se pehchaane: date, description / narration / particulars,
     * reference / cheque, debit / withdrawal, credit / deposit, ya ek amount. @return [added, skipped, notes[]]
     */
    public function import(int $companyId, int $recId, array $rows): array
    {
        $rec = $this->openRec($companyId, $recId);
        $rows = array_values(array_filter($rows, fn ($r) => is_array($r) && array_filter($r, fn ($v) => trim((string) $v) !== '')));
        if (count($rows) < 2) {
            throw new RuntimeException('The file has no data rows.');
        }
        $header = array_map(fn ($h) => mb_strtolower(trim((string) $h)), $rows[0]);
        $find = function (array $words) use ($header) {
            // poora lafz (warna "cr" "description" ke andar mil jata)
            foreach ($words as $w) {
                foreach ($header as $i => $h) {
                    if ($h !== '' && preg_match('/(^|[^a-z])' . preg_quote($w, '/') . '([^a-z]|$)/', $h)) {
                        return $i;
                    }
                }
            }
            return null;
        };
        $col = [
            'date' => $find(['value date', 'txn date', 'transaction date', 'date']),
            'desc' => $find(['description', 'narration', 'particular', 'details', 'remarks']),
            'ref' => $find(['reference', 'ref', 'cheque', 'chq', 'instrument']),
            'debit' => $find(['withdrawal', 'debit', 'dr']),
            'credit' => $find(['deposit', 'credit', 'cr']),
            'amount' => $find(['amount']),
        ];
        if ($col['date'] === null || ($col['amount'] === null && $col['debit'] === null && $col['credit'] === null)) {
            throw new RuntimeException('Could not find the Date and Amount (or Debit / Credit) columns in the first row.');
        }

        $added = 0;
        $skipped = 0;
        $notes = [];
        DB::transaction(function () use ($rows, $col, $rec, $recId, &$added, &$skipped, &$notes) {
            foreach (array_slice($rows, 1) as $n => $r) {
                $date = $this->parseDate($r[$col['date']] ?? null);
                $num = fn ($i) => $i === null ? 0.0 : (float) str_replace([',', ' '], '', (string) ($r[$i] ?? '0'));
                $amount = $col['debit'] !== null || $col['credit'] !== null
                    ? $num($col['credit']) - $num($col['debit'])
                    : $num($col['amount']);
                if (!$date || round($amount, 2) == 0.0) {
                    $skipped++;
                    continue;
                }
                if ($date < $rec->statement_from || $date > $rec->statement_to) {
                    $skipped++;
                    if (count($notes) < 5) {
                        $notes[] = 'Row ' . ($n + 2) . ' (' . $date . ') is outside the statement period.';
                    }
                    continue;
                }
                DB::table('bank_statement_lines')->insert([
                    'reconciliation_id' => $recId, 'txn_date' => $date,
                    'description' => $col['desc'] !== null ? (mb_substr(trim((string) ($r[$col['desc']] ?? '')), 0, 500) ?: null) : null,
                    'reference' => $col['ref'] !== null ? (mb_substr(trim((string) ($r[$col['ref']] ?? '')), 0, 100) ?: null) : null,
                    'amount' => round($amount, 2), 'created_at' => now(), 'updated_at' => now(),
                ]);
                $added++;
            }
        });
        return [$added, $skipped, $notes];
    }

    /**
     * bank account ki zinda GL lines (statement_to tak) jo kisi statement me clear nahi hui. Is account ka sab se pehla
     * statement jis din se shuru hua us se pehle ki lines cleared maani jati hain (un ka hisaab us statement ke opening me) —
     * warna pehli reconciliation pe saalon purani entries "raste me" dikhti.
     */
    public function openGlLines(object $rec)
    {
        $start = DB::table('bank_reconciliations')->where('company_id', $rec->company_id)->where('account_id', $rec->account_id)->min('statement_from');
        return $this->glLines($rec)->whereNull('m.id')->where('e.entry_date', '>=', $start ?: $rec->statement_from)
            ->orderBy('e.entry_date')->orderBy('l.id')
            ->get(['l.id', 'e.id as entry_id', 'e.entry_no', 'e.entry_date', 'e.narration', 'l.narration as line_narration', DB::raw('(l.debit - l.credit) AS amount')]);
    }

    /** raqam barabar + tareekh ±$days, sab se qareeb pehle; reference narration me ho to tarjeeh. @return matched count */
    public function autoMatch(int $companyId, int $recId, int $days = 3): int
    {
        $rec = $this->openRec($companyId, $recId);
        $this->dropStale($recId);
        $statement = DB::table('bank_statement_lines as s')->where('s.reconciliation_id', $recId)
            ->whereNotExists(fn ($q) => $q->from('bank_matches as m')->whereColumn('m.statement_line_id', 's.id'))
            ->orderBy('s.txn_date')->get();
        $gl = $this->openGlLines($rec)->all();
        $used = [];
        $n = 0;
        DB::transaction(function () use ($statement, &$gl, &$used, &$n, $days, $recId) {
            foreach ($statement as $s) {
                $best = null;
                $bestScore = null;
                foreach ($gl as $g) {
                    if (isset($used[$g->id]) || abs((float) $g->amount - (float) $s->amount) > 0.005) {
                        continue;
                    }
                    $diff = abs((strtotime($g->entry_date) - strtotime($s->txn_date)) / 86400);
                    if ($diff > $days) {
                        continue;
                    }
                    $refHit = $s->reference && (str_contains((string) $g->narration, $s->reference) || str_contains((string) $g->line_narration, $s->reference));
                    $score = $diff - ($refHit ? 100 : 0);
                    if ($bestScore === null || $score < $bestScore) {
                        [$best, $bestScore] = [$g, $score];
                    }
                }
                if ($best) {
                    $used[$best->id] = true;
                    DB::table('bank_matches')->insert(['reconciliation_id' => $recId, 'statement_line_id' => $s->id, 'journal_entry_line_id' => $best->id, 'created_at' => now(), 'updated_at' => now()]);
                    $n++;
                }
            }
        });
        return $n;
    }

    /** ek statement line ↔ ek ya zyada GL lines (jod barabar) */
    public function match(int $companyId, int $recId, int $lineId, array $glLineIds): void
    {
        $rec = $this->openRec($companyId, $recId);
        $line = DB::table('bank_statement_lines')->where('reconciliation_id', $recId)->where('id', $lineId)->first();
        if (!$line) {
            throw new RuntimeException('Select a statement line.');
        }
        if (DB::table('bank_matches')->where('statement_line_id', $lineId)->exists()) {
            throw new RuntimeException('This statement line is already matched.');
        }
        $open = $this->openGlLines($rec)->keyBy('id');
        $glLineIds = array_values(array_unique(array_map('intval', $glLineIds)));
        if (!$glLineIds || array_diff($glLineIds, $open->keys()->all())) {
            throw new RuntimeException('Select ledger entries that are not cleared yet.');
        }
        $sum = round(collect($glLineIds)->sum(fn ($id) => (float) $open[$id]->amount), 2);
        if (abs($sum - (float) $line->amount) > 0.005) {
            throw new RuntimeException('Selected entries total ' . number_format($sum, 2) . ' but the statement line is ' . number_format((float) $line->amount, 2) . '.');
        }
        DB::transaction(function () use ($glLineIds, $recId, $lineId) {
            foreach ($glLineIds as $id) {
                DB::table('bank_matches')->insert(['reconciliation_id' => $recId, 'statement_line_id' => $lineId, 'journal_entry_line_id' => $id, 'created_at' => now(), 'updated_at' => now()]);
            }
        });
    }

    public function unmatch(int $companyId, int $recId, int $lineId): void
    {
        $this->openRec($companyId, $recId);
        DB::table('bank_matches')->where('reconciliation_id', $recId)->where('statement_line_id', $lineId)->delete();
    }

    /** statement ki line jo books me nahi (bank charges, profit, MDR) → JV (bank ↔ contra) + match */
    public function createEntry(int $companyId, int $recId, int $lineId, int $contraAccountId, ?string $narration, $userId): int
    {
        $rec = $this->openRec($companyId, $recId);
        $line = DB::table('bank_statement_lines')->where('reconciliation_id', $recId)->where('id', $lineId)->first();
        if (!$line || DB::table('bank_matches')->where('statement_line_id', $lineId)->exists()) {
            throw new RuntimeException('Select an unmatched statement line.');
        }
        if ($contraAccountId === (int) $rec->account_id) {
            throw new RuntimeException('Choose a different account for the other side.');
        }
        $amount = abs((float) $line->amount);
        $in = (float) $line->amount > 0;
        $entry = app(JournalService::class)->createPosted($companyId, [
            'entry_date' => $line->txn_date,
            'branch_id' => null,
            'narration' => $narration ?: ('Bank statement: ' . ($line->description ?: 'entry') . ($line->reference ? ' (' . $line->reference . ')' : '')),
            'source_type' => self::SOURCE,
            'source_id' => $recId,
        ], [
            ['account_id' => $rec->account_id, 'debit' => $in ? $amount : 0, 'credit' => $in ? 0 : $amount],
            ['account_id' => $contraAccountId, 'debit' => $in ? 0 : $amount, 'credit' => $in ? $amount : 0],
        ], $userId);
        $bankLine = DB::table('journal_entry_lines')->where('journal_entry_id', $entry->id)->where('account_id', $rec->account_id)->value('id');
        DB::transaction(function () use ($recId, $lineId, $bankLine, $entry) {
            DB::table('bank_matches')->insert(['reconciliation_id' => $recId, 'statement_line_id' => $lineId, 'journal_entry_line_id' => $bankLine, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('bank_statement_lines')->where('id', $lineId)->update(['journal_entry_id' => $entry->id, 'updated_at' => now()]);
        });
        return $entry->id;
    }

    /**
     * Book (GL) balance statement_to pe, clear na hui deposits / payments, statement ka hisaab, farq.
     * Adjusted statement = statement closing + raste me deposits − clear na hue payments; ye GL balance ke barabar hona chahiye.
     */
    public function summary(object $rec): array
    {
        $this->dropStale($rec->id);
        $glBalance = (float) $this->glLines($rec, false)->sum(DB::raw('l.debit - l.credit'));
        $open = $this->openGlLines($rec);
        $inTransit = (float) $open->where('amount', '>', 0)->sum('amount');
        $outstanding = (float) -$open->where('amount', '<', 0)->sum('amount');
        $lines = DB::table('bank_statement_lines as s')->where('s.reconciliation_id', $rec->id)
            ->leftJoin('bank_matches as m', 'm.statement_line_id', '=', 's.id')
            ->groupBy('s.id', 's.amount')->selectRaw('s.id, s.amount, COUNT(m.id) AS matches')->get();
        $statementNet = (float) $lines->sum('amount');
        $unmatched = $lines->where('matches', 0);
        $adjusted = (float) $rec->closing_balance + $inTransit - $outstanding;
        return [
            'gl_balance' => round($glBalance, 2),
            'in_transit' => round($inTransit, 2),
            'outstanding' => round($outstanding, 2),
            'adjusted_statement' => round($adjusted, 2),
            'difference' => round($adjusted - $glBalance, 2),
            'statement_lines' => $lines->count(),
            'unmatched_lines' => $unmatched->count(),
            'unmatched_amount' => round((float) $unmatched->sum('amount'), 2),
            'statement_check' => round((float) $rec->opening_balance + $statementNet - (float) $rec->closing_balance, 2), // 0 = lines poori
        ];
    }

    public function finish(int $companyId, int $recId, $userId): void
    {
        $rec = $this->openRec($companyId, $recId);
        $s = $this->summary($rec);
        if ($s['unmatched_lines'] > 0) {
            throw new RuntimeException($s['unmatched_lines'] . ' statement line(s) are not matched yet.');
        }
        if (abs($s['difference']) > 0.005) {
            throw new RuntimeException('There is still a difference of ' . number_format($s['difference'], 2) . '.');
        }
        DB::table('bank_reconciliations')->where('id', $recId)->update(['status' => 'reconciled', 'reconciled_by' => $userId, 'reconciled_at' => now(), 'updated_at' => now()]);
    }

    public function reopen(int $companyId, int $recId): void
    {
        $this->get($companyId, $recId);
        DB::table('bank_reconciliations')->where('id', $recId)->update(['status' => 'open', 'reconciled_by' => null, 'reconciled_at' => null, 'updated_at' => now()]);
    }

    public function delete(int $companyId, int $recId): void
    {
        $rec = $this->openRec($companyId, $recId);
        DB::transaction(function () use ($rec) {
            DB::table('bank_matches')->where('reconciliation_id', $rec->id)->delete();
            DB::table('bank_statement_lines')->where('reconciliation_id', $rec->id)->delete();
            DB::table('bank_reconciliations')->where('id', $rec->id)->delete();
        });
    }

    /** bank accounts: Asset leaf jin ka system_key bank, ya bank mapping ka target, ya naam me bank */
    public function bankAccounts(int $companyId)
    {
        $mapped = DB::table('account_mappings')->where('company_id', $companyId)->where('map_type', 'bank_account')->pluck('account_id')->all();
        return DB::table('chart_of_accounts')->where('company_id', $companyId)->where('type', 'Asset')->where('is_group', 0)->where('is_active', 1)
            ->where(fn ($q) => $q->where('system_key', 'bank')->orWhereIn('id', $mapped ?: [0])->orWhere('name', 'like', '%bank%'))
            ->orderBy('code')->get(['id', 'code', 'name']);
    }

    private function glLines(object $rec, bool $withMatchJoin = true)
    {
        $q = DB::table('journal_entry_lines as l')->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->where('e.company_id', $rec->company_id)->where('e.status', 'posted')->where('l.account_id', $rec->account_id)
            ->where('e.entry_date', '<=', $rec->statement_to)
            ->whereNull('e.reversal_of_id')
            ->whereNotExists(fn ($x) => $x->from('journal_entries as r')->whereColumn('r.reversal_of_id', 'e.id'));
        if ($withMatchJoin) {
            // doosre statement me clear hui lines bahar; is statement ki matched bhi (yahan "open" = abhi kahin match nahi)
            $q->leftJoin('bank_matches as m', 'm.journal_entry_line_id', '=', 'l.id');
        }
        return $q;
    }

    /** jo matched GL line ki entry baad me reverse ho gayi (automatic posting repost) — match hatao */
    private function dropStale(int $recId): void
    {
        $stale = DB::table('bank_matches as m')->join('journal_entry_lines as l', 'l.id', '=', 'm.journal_entry_line_id')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->where('m.reconciliation_id', $recId)
            ->where(fn ($q) => $q->whereNotNull('e.reversal_of_id')->orWhereExists(fn ($x) => $x->from('journal_entries as r')->whereColumn('r.reversal_of_id', 'e.id')))
            ->pluck('m.statement_line_id')->unique()->all();
        if ($stale && DB::table('bank_reconciliations')->where('id', $recId)->value('status') === 'open') {
            DB::table('bank_matches')->where('reconciliation_id', $recId)->whereIn('statement_line_id', $stale)->delete();
        }
    }

    private function openRec(int $companyId, int $recId): object
    {
        $rec = $this->get($companyId, $recId);
        if ($rec->status !== 'open') {
            throw new RuntimeException('This reconciliation is finished. Reopen it to make changes.');
        }
        return $rec;
    }

    private function assertBankAccount(int $companyId, int $accountId): void
    {
        if (!DB::table('chart_of_accounts')->where('company_id', $companyId)->where('id', $accountId)->where('type', 'Asset')->where('is_group', 0)->exists()) {
            throw new RuntimeException('Select a bank account.');
        }
    }

    private function parseDate($value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }
        if (is_numeric($value) && (float) $value > 20000 && (float) $value < 80000) { // Excel serial date
            return date('Y-m-d', (int) round(((float) $value - 25569) * 86400));
        }
        $v = trim((string) $value);
        foreach (['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y-m-d', 'd-M-Y', 'd M Y', 'd/m/y', 'd-M-y', 'Y/m/d', 'm/d/Y'] as $format) {
            $d = \DateTime::createFromFormat('!' . $format, $v);
            if ($d && $d->format($format) === $v) {
                return $d->format('Y-m-d');
            }
        }
        $t = strtotime($v);
        return $t ? date('Y-m-d', $t) : null;
    }
}
