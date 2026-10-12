<?php

namespace App\Services\Accounting;

use App\Models\Accounting\AccountingPeriod;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\JournalEntry;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Phase 1.5: period aur saal band karna.
 *   open   → closed  : entries band (JournalService sirf open period me post karti hai; automatic posting error dikhati hai)
 *   closed → open    : wapas kholna (saal band na ho)
 *   closed → locked  : pakka band
 *   locked → closed  : sirf super admin
 * Saal band (closeYear): saal ke Revenue / Expense balances ki ulti entry → Retained Earnings (source year_close),
 * phir saare periods closed + saal closed. reopenYear closing entry reverse karta hai.
 */
class PeriodCloseService
{
    const TRANSITIONS = [
        'close' => ['open', 'closed'],
        'reopen' => ['closed', 'open'],
        'lock' => ['closed', 'locked'],
        'unlock' => ['locked', 'closed'],
    ];

    public function __construct(private JournalService $journal, private AccountBalanceService $balances)
    {
    }

    public function changePeriod(int $companyId, int $periodId, string $action, bool $isSuperAdmin = false): AccountingPeriod
    {
        if (!isset(self::TRANSITIONS[$action])) {
            throw new RuntimeException('Unknown action.');
        }
        [$fromStatus, $toStatus] = self::TRANSITIONS[$action];

        $period = AccountingPeriod::whereHas('fiscalYear', fn ($q) => $q->where('company_id', $companyId))->findOrFail($periodId);
        if ($period->status !== $fromStatus) {
            throw new RuntimeException("Period is {$period->status}, it cannot be {$action}ed.");
        }
        if ($action === 'unlock' && !$isSuperAdmin) {
            throw new RuntimeException('Only the administrator can unlock a period.');
        }
        if ($action === 'reopen' && $period->fiscalYear->status === 'closed') {
            throw new RuntimeException('The fiscal year is closed. Reopen the year first.');
        }

        $period->update(['status' => $toStatus]);
        return $period;
    }

    /** Saal band: P&L → Retained Earnings, saare periods closed (locked waise hi), saal closed. */
    public function closeYear(int $companyId, int $yearId, $userId = null): ?JournalEntry
    {
        return DB::transaction(function () use ($companyId, $yearId, $userId) {
            $year = FiscalYear::where('company_id', $companyId)->lockForUpdate()->findOrFail($yearId);
            if ($year->status === 'closed') {
                throw new RuntimeException('This fiscal year is already closed.');
            }
            $previousOpen = FiscalYear::where('company_id', $companyId)->where('end_date', '<', $year->start_date)->where('status', 'open')->exists();
            if ($previousOpen) {
                throw new RuntimeException('Close the earlier fiscal year first.');
            }

            $last = AccountingPeriod::where('fiscal_year_id', $year->id)->orderByDesc('month')->first() /* periods() relation pehle se month asc */;
            if ($last->status === 'locked') {
                throw new RuntimeException('The last period is locked; the closing entry cannot be posted. Unlock it first.');
            }
            $last->update(['status' => 'open']); // closing entry isi me jati hai

            $entry = $this->postClosingEntry($companyId, $year, $userId);

            $year->periods()->where('status', 'open')->update(['status' => 'closed']);
            $year->update(['status' => 'closed']);
            return $entry;
        });
    }

    /** Saal dobara kholna (super admin): closing entry reverse, aakhri period open. */
    public function reopenYear(int $companyId, int $yearId, $userId = null): void
    {
        DB::transaction(function () use ($companyId, $yearId, $userId) {
            $year = FiscalYear::where('company_id', $companyId)->lockForUpdate()->findOrFail($yearId);
            if ($year->status !== 'closed') {
                throw new RuntimeException('This fiscal year is not closed.');
            }
            if (FiscalYear::where('company_id', $companyId)->where('start_date', '>', $year->end_date)->where('status', 'closed')->exists()) {
                throw new RuntimeException('A later fiscal year is closed. Reopen that first.');
            }

            $year->update(['status' => 'open']);
            $last = AccountingPeriod::where('fiscal_year_id', $year->id)->orderByDesc('month')->first() /* periods() relation pehle se month asc */;
            $last->update(['status' => 'open']);

            $closing = JournalEntry::where('company_id', $companyId)->where('source_type', 'year_close')
                ->where('source_ref', (string) $year->id)->where('status', 'posted')
                ->whereNull('reversal_of_id')->whereDoesntHave('reversedBy')->first();
            if ($closing) {
                $this->journal->reverse($closing, $year->end_date->toDateString(), $userId, 'Fiscal year reopened');
            }
        });
    }

    private function postClosingEntry(int $companyId, FiscalYear $year, $userId): ?JournalEntry
    {
        $accounts = $this->balances->accounts($companyId, $year->start_date->toDateString(), $year->end_date->toDateString());
        $retained = $accounts->firstWhere('system_key', 'retained_earnings');
        if (!$retained) {
            throw new RuntimeException('Retained Earnings account not found.');
        }

        $lines = [];
        $net = 0.0; // debit − credit jo P&L accounts ko 0 karne me laga
        foreach ($accounts->where('is_group', false)->whereIn('type', ['Revenue', 'Expense']) as $a) {
            $movement = round($a->debit - $a->credit, 2); // saal ki harkat (opening pichle band saal ki, ab 0)
            if (abs($movement) < 0.005) {
                continue;
            }
            // ulta: debit balance ko credit, credit balance ko debit
            $lines[] = ['account_id' => $a->id, 'debit' => $movement < 0 ? -$movement : 0, 'credit' => $movement > 0 ? $movement : 0, 'narration' => 'Year-end close'];
            $net += $movement;
        }
        if (!$lines) {
            return null;
        }
        $net = round($net, 2);
        // munafa (credit P&L, net < 0) → Cr Retained Earnings; nuqsan → Dr
        $lines[] = ['account_id' => $retained->id, 'debit' => $net > 0 ? $net : 0, 'credit' => $net < 0 ? -$net : 0,
            'narration' => $net < 0 ? 'Net profit for the year' : 'Net loss for the year'];

        return $this->journal->createPosted($companyId, [
            'entry_date' => $year->end_date->toDateString(),
            'branch_id' => null,
            'narration' => 'Year-end closing — ' . $year->name,
            'source_type' => 'year_close',
            'source_ref' => (string) $year->id,
        ], $lines, $userId);
    }
}
