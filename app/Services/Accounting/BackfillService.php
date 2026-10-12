<?php

namespace App\Services\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\JournalEntry;
use App\Services\Accounting\Posting\PostingService;
use App\Services\Accounting\Posting\SourceQuery;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Phase 1.6: purana data GL me — posting start date peeche karke wahi PostingService poori history pe chalati hai.
 *   1. cut-off tak ke fiscal years banao (pichle saal)
 *   2. jo opening entry cut-off ke baad ki hai (e.g. aaj se pehle wali test opening) wo reverse — warna purana data + opening dono ginte
 *   3. posting_start_date = cut-off, valuation + saare posters cut-off se aaj tak
 * Cut-off se pehle ka data GL me nahi aata — us ka hisaab Opening Balances (cut-off se ek din pehle) se.
 * Dobara chalana safe hai (sync idempotent) — sirf farq post hota hai.
 */
class BackfillService
{
    public function __construct(
        private AccountingSetupService $setup,
        private PostingService $posting,
    ) {
    }

    /** Company ka sab se purana asal data (2015 se pehle ki tareekhein device ki galti — e.g. 1970 / 2010). */
    public function earliestDataDate(int $companyId): ?string
    {
        $in = implode(',', SourceQuery::branchIds($companyId));
        $dates = array_filter([
            DB::selectOne("SELECT MIN(date) AS d FROM sales_receipts WHERE branch IN ({$in}) AND date >= '2015-01-01' AND status = 4")->d ?? null,
            DB::selectOne("SELECT MIN(date) AS d FROM expenses WHERE branch_id IN ({$in}) AND date >= '2015-01-01'")->d ?? null,
            DB::selectOne("SELECT MIN(DATE(vl.created_at)) AS d FROM vendor_ledger vl JOIN vendors v ON v.id = vl.vendor_id WHERE v.user_id = ? AND vl.created_at >= '2015-01-01'", [$companyId])->d ?? null,
        ]);
        return $dates ? substr(min($dates), 0, 10) : null;
    }

    /** Fiscal year ki shuruat jis me $date aati hai — backfill cut-off ka sujhaav. */
    public function suggestedCutoff(int $companyId): ?string
    {
        $earliest = $this->earliestDataDate($companyId);
        if (!$earliest) {
            return null;
        }
        $month = (int) (AccountingSetting::where('company_id', $companyId)->value('fiscal_year_start_month') ?: 7);
        return $this->setup->fiscalYearStart(Carbon::parse($earliest), $month)->toDateString();
    }

    /** Kuch likhe baghair: kya hoga. */
    public function plan(int $companyId, string $cutoff, bool $withPreview = true): array
    {
        $setting = AccountingSetting::where('company_id', $companyId)->first();
        if (!$setting || !$setting->enabled) {
            throw new RuntimeException('Enable accounting for this company first.');
        }

        $first = FiscalYear::where('company_id', $companyId)->orderBy('start_date')->first();
        $month = (int) $setting->fiscal_year_start_month;
        $years = [];
        if ($first) {
            $start = Carbon::parse($first->start_date);
            while ($start->toDateString() > $cutoff) {
                $start = $start->copy()->subYear();
                $years[] = $start->month === 1 ? 'FY ' . $start->year : 'FY ' . $start->year . '-' . $start->copy()->addYear()->subDay()->format('y');
            }
        }

        $preview = $withPreview ? $this->posting->preview($companyId, $cutoff, now()->toDateString()) : ['documents' => []];
        $byType = collect($preview['documents'])->groupBy('type')->map(fn ($docs) => [
            'documents' => $docs->count(),
            'debits' => $docs->sum(fn ($d) => array_sum(array_map(fn ($l) => (float) $l['debit'], $d['lines']))),
            'unbalanced' => $docs->filter(fn ($d) => abs($d['imbalance']) >= PostingService::WARN_IMBALANCE)->count(),
        ]);

        return [
            'cutoff' => $cutoff,
            'earliest' => $this->earliestDataDate($companyId),
            'current_start' => $setting->posting_start_date?->toDateString(),
            'years_to_create' => array_reverse($years),
            'conflicting_openings' => $this->conflictingOpenings($companyId, $cutoff)->map(fn ($e) => $e->entry_no . ' (' . $e->entry_date->toDateString() . ')')->all(),
            'sources' => $byType->all(),
            'posted_already' => JournalEntry::where('company_id', $companyId)->where('status', 'posted')
                ->whereNotIn('source_type', ['manual', 'opening_balance', 'year_close'])->whereNull('reversal_of_id')->whereDoesntHave('reversedBy')->count(),
        ];
    }

    public function run(int $companyId, string $cutoff, $userId = null): array
    {
        $plan = $this->plan($companyId, $cutoff, false); // checks; preview dobara nahi (sync khud hisaab lagata hai)

        $created = $this->setup->ensureFiscalYearsFrom($companyId, $cutoff);

        $journal = app(JournalService::class);
        foreach ($this->conflictingOpenings($companyId, $cutoff) as $entry) {
            $journal->reverse($entry, $entry->entry_date->toDateString(), $userId, 'Replaced by backfill from ' . $cutoff);
        }

        AccountingSetting::where('company_id', $companyId)->update(['posting_start_date' => $cutoff]);
        $stats = $this->posting->sync($companyId, $cutoff, now()->toDateString());

        return ['plan' => $plan, 'years_created' => array_map(fn ($y) => $y->name, $created), 'stats' => $stats];
    }

    /** Cut-off ke din ya baad ki zinda opening entries (backfill ke baad double count karti) */
    private function conflictingOpenings(int $companyId, string $cutoff)
    {
        return JournalEntry::where('company_id', $companyId)->where('source_type', 'opening_balance')
            ->where('status', 'posted')->whereNull('reversal_of_id')->whereDoesntHave('reversedBy')
            ->where('entry_date', '>=', $cutoff)->get();
    }
}
