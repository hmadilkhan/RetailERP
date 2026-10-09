<?php

namespace App\Services\Accounting;

use App\Models\Accounting\AccountingPeriod;
use App\Models\Accounting\AccountingSetting;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\JournalEntry;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Phase 1.2: General Ledger ki har entry isi service se banti hai — manual JV bhi aur 1.3 ki automatic posting bhi.
 * Rules:
 *   - posted entry me SUM(debit) = SUM(credit), kam se kam 2 lines
 *   - har line pe sirf debit ya sirf credit (> 0)
 *   - account usi company ka, active, aur group nahi
 *   - entry_date kisi open fiscal year ke open period me
 *   - posted entry badalti / delete nahi hoti — reverse() ulti entry banata hai
 * Amounts paise (int) me compare hote hain, float ki 0.01 wali galti na ho.
 *
 * $lines format: [['account_id' => 5, 'debit' => 100, 'credit' => 0, 'narration' => '...'], ...]
 */
class JournalService
{
    /** Draft banao ya update karo. Draft unbalanced ho sakta hai — balance post pe check hota hai. */
    public function saveDraft(int $companyId, array $header, array $lines, ?JournalEntry $entry = null, $userId = null): JournalEntry
    {
        if ($entry && $entry->isPosted()) {
            throw new RuntimeException('Posted entry cannot be edited. Reverse it instead.');
        }

        return DB::transaction(function () use ($companyId, $header, $lines, $entry, $userId) {
            $clean = $this->cleanLines($companyId, $lines, false);
            $period = $this->periodFor($companyId, $header['entry_date']);

            $data = [
                'branch_id' => $header['branch_id'] ?: null,
                'entry_date' => $header['entry_date'],
                'period_id' => $period->id,
                'narration' => $header['narration'] ?? null,
                'total' => $this->toAmount(array_sum(array_column($clean, 'debit_cents'))),
            ];

            if ($entry) {
                $entry->update($data);
                $entry->lines()->delete();
            } else {
                $entry = JournalEntry::create($data + [
                    'company_id' => $companyId,
                    'entry_no' => $this->nextEntryNo($companyId),
                    'source_type' => $header['source_type'] ?? 'manual',
                    'source_id' => $header['source_id'] ?? null,
                    'status' => 'draft',
                    'created_by' => $userId,
                ]
                    // sirf automatic posting bhejti hai — manual JV posting migration se pehle bhi chale
                    + array_filter(['source_ref' => $header['source_ref'] ?? null, 'source_hash' => $header['source_hash'] ?? null], fn ($v) => $v !== null));
            }

            $this->insertLines($entry, $clean);
            return $entry->fresh('lines');
        });
    }

    public function post(JournalEntry $entry, $userId = null): JournalEntry
    {
        return DB::transaction(function () use ($entry, $userId) {
            $entry = JournalEntry::lockForUpdate()->findOrFail($entry->id);
            if ($entry->isPosted()) {
                throw new RuntimeException('Entry is already posted.');
            }

            $lines = $entry->lines->map(fn ($l) => [
                'account_id' => $l->account_id, 'debit' => $l->debit, 'credit' => $l->credit, 'narration' => $l->narration,
            ])->all();
            $this->cleanLines($entry->company_id, $lines, true); // balance + accounts dobara check
            $this->periodFor($entry->company_id, $entry->entry_date->toDateString());

            $entry->update(['status' => 'posted', 'posted_by' => $userId, 'posted_at' => now()]);
            return $entry;
        });
    }

    /** Ek hi qadam me banao aur post karo — automatic posting (1.3) yahi use karegi. */
    public function createPosted(int $companyId, array $header, array $lines, $userId = null): JournalEntry
    {
        return DB::transaction(function () use ($companyId, $header, $lines, $userId) {
            $this->cleanLines($companyId, $lines, true);
            $entry = $this->saveDraft($companyId, $header, $lines, null, $userId);
            return $this->post($entry, $userId);
        });
    }

    /** Posted entry ki ulti entry (debit ↔ credit) — galti sudharne ka wahid tareeqa. */
    public function reverse(JournalEntry $entry, string $date, $userId = null, ?string $reason = null): JournalEntry
    {
        return DB::transaction(function () use ($entry, $date, $userId, $reason) {
            $entry = JournalEntry::lockForUpdate()->findOrFail($entry->id);
            if (!$entry->isPosted()) {
                throw new RuntimeException('Only posted entries can be reversed.');
            }
            if ($entry->reversedBy()->exists()) {
                throw new RuntimeException('This entry is already reversed.');
            }
            if ($entry->reversal_of_id) {
                throw new RuntimeException('A reversal entry cannot be reversed again.');
            }

            $lines = $entry->lines->map(fn ($l) => [
                'account_id' => $l->account_id, 'debit' => $l->credit, 'credit' => $l->debit, 'narration' => $l->narration,
            ])->all();

            $reversal = $this->createPosted($entry->company_id, [
                'entry_date' => $date,
                'branch_id' => $entry->branch_id,
                'narration' => 'Reversal of ' . $entry->entry_no . ($reason ? ' — ' . $reason : ''),
                'source_type' => $entry->source_type,
                'source_id' => $entry->source_id,
                'source_ref' => $entry->source_ref ?? null,
            ], $lines, $userId);

            $reversal->update(['reversal_of_id' => $entry->id]);
            return $reversal;
        });
    }

    public function deleteDraft(JournalEntry $entry): void
    {
        if ($entry->isPosted()) {
            throw new RuntimeException('Posted entry cannot be deleted. Reverse it instead.');
        }
        $entry->delete(); // lines cascade
    }

    /** entry_date ka period — fiscal year aur period dono open hone chahiye. */
    public function periodFor(int $companyId, string $date): AccountingPeriod
    {
        $period = AccountingPeriod::query()
            ->join('fiscal_years', 'fiscal_years.id', '=', 'accounting_periods.fiscal_year_id')
            ->where('fiscal_years.company_id', $companyId)
            ->where('accounting_periods.start_date', '<=', $date)
            ->where('accounting_periods.end_date', '>=', $date)
            ->select('accounting_periods.*', 'fiscal_years.status as fiscal_year_status')
            ->first();

        if (!$period) {
            throw new RuntimeException('No fiscal year covers ' . $date . '. Create the fiscal year first.');
        }
        if ($period->fiscal_year_status !== 'open' || $period->status !== 'open') {
            throw new RuntimeException('The period for ' . $date . ' is ' . ($period->status !== 'open' ? $period->status : 'in a closed fiscal year') . '.');
        }
        return $period;
    }

    /**
     * Lines ko check karke paise (cents) me badlo. $forPosting = true pe balance + kam se kam 2 lines bhi zaroori.
     * Khali lines (na account, na amount) chhod di jati hain.
     */
    private function cleanLines(int $companyId, array $lines, bool $forPosting): array
    {
        $clean = [];
        foreach ($lines as $i => $line) {
            $debit = $this->toCents($line['debit'] ?? 0);
            $credit = $this->toCents($line['credit'] ?? 0);
            $accountId = $line['account_id'] ?? null;
            if (!$accountId && $debit === 0 && $credit === 0) {
                continue;
            }

            $n = $i + 1;
            if (!$accountId) {
                throw new RuntimeException("Line {$n}: select an account.");
            }
            if ($debit < 0 || $credit < 0) {
                throw new RuntimeException("Line {$n}: amounts cannot be negative.");
            }
            if (($debit > 0) === ($credit > 0)) {
                throw new RuntimeException("Line {$n}: enter either a debit or a credit.");
            }
            $clean[] = ['account_id' => (int) $accountId, 'debit_cents' => $debit, 'credit_cents' => $credit, 'narration' => $line['narration'] ?? null];
        }

        if (empty($clean)) {
            throw new RuntimeException('Add at least one line.');
        }

        $accounts = ChartOfAccount::forCompany($companyId)->whereIn('id', array_column($clean, 'account_id'))->get()->keyBy('id');
        foreach ($clean as $i => $line) {
            $account = $accounts[$line['account_id']] ?? null;
            $n = $i + 1;
            if (!$account) {
                throw new RuntimeException("Line {$n}: account not found.");
            }
            if ($account->is_group) {
                throw new RuntimeException("Line {$n}: {$account->name} is a group — post to an account under it.");
            }
            if (!$account->is_active) {
                throw new RuntimeException("Line {$n}: {$account->name} is inactive.");
            }
        }

        if ($forPosting) {
            if (count($clean) < 2) {
                throw new RuntimeException('A journal entry needs at least two lines.');
            }
            $debits = array_sum(array_column($clean, 'debit_cents'));
            $credits = array_sum(array_column($clean, 'credit_cents'));
            if ($debits !== $credits) {
                throw new RuntimeException('Debits (' . number_format($debits / 100, 2) . ') and credits (' . number_format($credits / 100, 2) . ') must be equal.');
            }
            if ($debits === 0) {
                throw new RuntimeException('Entry total cannot be zero.');
            }
        }

        return $clean;
    }

    private function insertLines(JournalEntry $entry, array $clean): void
    {
        foreach ($clean as $line) {
            $entry->lines()->create([
                'account_id' => $line['account_id'],
                'debit' => $this->toAmount($line['debit_cents']),
                'credit' => $this->toAmount($line['credit_cents']),
                'narration' => $line['narration'],
            ]);
        }
    }

    /** JV-000001 — company-wise. accounting_settings row lock karke do request ek hi number na lein. */
    private function nextEntryNo(int $companyId): string
    {
        AccountingSetting::where('company_id', $companyId)->lockForUpdate()->first();
        $last = JournalEntry::where('company_id', $companyId)->where('entry_no', 'like', 'JV-%')
            ->orderByDesc('id')->value('entry_no');
        $next = $last ? ((int) substr($last, 3)) + 1 : 1;
        return 'JV-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    private function toCents($value): int
    {
        return (int) round(((float) str_replace(',', '', (string) $value)) * 100);
    }

    private function toAmount(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }
}
