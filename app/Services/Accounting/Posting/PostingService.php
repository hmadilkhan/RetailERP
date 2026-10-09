<?php

namespace App\Services\Accounting\Posting;

use App\Models\Accounting\AccountingSetting;
use App\Models\Accounting\JournalEntry;
use App\Services\Accounting\JournalService;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

/**
 * Phase 1.3: source tables → GL. Har poster ke documents ko journal entries se "sync" karta hai:
 *   - naya document          → entry post
 *   - document badla (hash)  → purani entry reverse + nayi post
 *   - document gayab (delete)→ purani entry reverse
 * Legacy flows nahi chhede — sab kuch padh kar hota hai, isliye webservice (POS) wali sales bhi aati hain.
 * Kisi document ka error baaqi ko nahi rokta; posting_errors me jata hai aur screen pe dikhta hai.
 * Debit / credit farq (rounding, kharab source data) suspense account me jata hai + warning.
 */
class PostingService
{
    /** source_type => Poster class. Naya source = yahan ek line. */
    const POSTERS = [
        Posters\SalesPoster::class,
        Posters\CustomerReceiptPoster::class,
        Posters\ExpensePoster::class,
        Posters\GrnPoster::class,
        Posters\PurchaseReturnPoster::class,
        Posters\VendorPaymentPoster::class,
        Posters\StockOpeningPoster::class,
        Posters\StockAdjustmentPoster::class,
    ];

    /** Is se kam farq suspense me chupchap jata hai; zyada ho to warning. */
    const WARN_IMBALANCE = 10;

    public function __construct(private JournalService $journal)
    {
    }

    /** @return Poster[] */
    public function posters(): array
    {
        return array_map(fn ($class) => app($class), self::POSTERS);
    }

    /**
     * Ek company ke [from, to] window ko sync karo. from < posting_start_date ho to start date se.
     * @return array ['posted' => n, 'reposted' => n, 'reversed' => n, 'unchanged' => n, 'errors' => n]
     */
    public function sync(int $companyId, string $from, string $to, ?array $only = null): array
    {
        $setting = AccountingSetting::where('company_id', $companyId)->first();
        if (!$setting || !$setting->enabled || !$setting->posting_start_date) {
            throw new RuntimeException('Automatic posting is not turned on for this company.');
        }
        $start = $setting->posting_start_date->toDateString();
        $from = max($from, $start);

        $stats = ['posted' => 0, 'reposted' => 0, 'reversed' => 0, 'unchanged' => 0, 'errors' => 0];
        if ($from > $to) {
            return $stats;
        }

        $accounts = new AccountResolver($companyId);
        foreach ($this->posters() as $poster) {
            if ($only && !in_array($poster->sourceType(), $only, true)) {
                continue;
            }
            $this->syncPoster($poster, $companyId, $from, $to, $accounts, $stats);
        }

        $setting->update(['last_posted_at' => now()]);
        return $stats;
    }

    /**
     * Kuch likhe baghair dikhao kya post hoga — har document ki normalized lines + farq.
     * Company ka accounting on hona zaroori nahi (dry run ke liye).
     */
    public function preview(int $companyId, string $from, string $to, ?array $only = null): array
    {
        $accounts = new AccountResolver($companyId, true);
        $out = [];
        foreach ($this->posters() as $poster) {
            if ($only && !in_array($poster->sourceType(), $only, true)) {
                continue;
            }
            foreach ($poster->documents($companyId, $from, $to, $accounts) as $doc) {
                [$lines, $imbalance] = $this->normalize($doc, $accounts);
                $out[] = ['type' => $poster->sourceType(), 'doc' => $doc, 'lines' => $lines, 'imbalance' => $imbalance];
            }
        }
        return ['documents' => $out, 'accounts' => $accounts->keys()];
    }

    private function syncPoster(Poster $poster, int $companyId, string $from, string $to, AccountResolver $accounts, array &$stats): void
    {
        $type = $poster->sourceType();

        // abhi "zinda" entries: posted, reversal nahi, aur reverse nahi hui
        $existing = JournalEntry::where('company_id', $companyId)
            ->where('source_type', $type)->where('status', 'posted')
            ->whereNull('reversal_of_id')->whereDoesntHave('reversedBy')
            ->whereBetween('entry_date', [$from, $to])
            ->orderBy('id')->get()->groupBy('source_ref');

        $seen = [];
        try {
            $documents = $poster->documents($companyId, $from, $to, $accounts);
            foreach ($documents as $doc) {
                if ($doc->date < $from || $doc->date > $to) {
                    continue;
                }
                $seen[$doc->ref] = true;
                $this->syncDocument($type, $companyId, $doc, $existing->get($doc->ref), $accounts, $stats);
            }
        } catch (Throwable $e) {
            // poster khud fail (e.g. SQL) — is source ki deletions bhi skip, warna sab reverse ho jata
            $this->recordError($companyId, $type, '*', 'error', $poster->label() . ': ' . $e->getMessage());
            $stats['errors']++;
            return;
        }
        $this->clearError($companyId, $type, '*');

        // jo document ab source me nahi aata (delete / window se bahar) us ka purana error bhi hatao
        DB::table('posting_errors')->where('company_id', $companyId)->where('source_type', $type)
            ->where('source_ref', '!=', '*')->whereNotIn('source_ref', array_map('strval', array_keys($seen)))->delete();

        // source me ab nahi — delete hua ya amount zero
        foreach ($existing as $ref => $entries) {
            if (isset($seen[$ref])) {
                continue;
            }
            foreach ($entries as $entry) {
                $this->attempt($companyId, $type, (string) $ref, $stats, function () use ($entry, &$stats) {
                    $this->reverseEntry($entry, 'source removed');
                    $stats['reversed']++;
                });
            }
        }
    }

    private function syncDocument(string $type, int $companyId, PostingDocument $doc, $current, AccountResolver $accounts, array &$stats): void
    {
        $this->attempt($companyId, $type, $doc->ref, $stats, function () use ($type, $companyId, $doc, $current, $accounts, &$stats) {
            [$lines, $imbalance] = $this->normalize($doc, $accounts);
            $hash = $lines ? sha1(json_encode([$doc->date, $doc->branchId, $lines])) : null;

            $active = $current ? $current->first() : null;
            // ek ref ki ek se zyada zinda entries (purani race) — extra ulti kar do
            foreach (($current ?? collect())->slice(1) as $duplicate) {
                $this->reverseEntry($duplicate, 'duplicate');
            }

            if ($active && $active->source_hash === $hash) {
                $stats['unchanged']++;
                return;
            }
            if ($active) {
                $this->reverseEntry($active, 'source changed');
            }
            if (!$lines) {
                if ($active) {
                    $stats['reversed']++;
                }
                $this->clearError($companyId, $type, $doc->ref);
                return;
            }

            $this->journal->createPosted($companyId, [
                'entry_date' => $doc->date,
                'branch_id' => $doc->branchId,
                'narration' => $doc->narration,
                'source_type' => $type,
                'source_id' => $doc->sourceId,
                'source_ref' => $doc->ref,
                'source_hash' => $hash,
            ], $lines);

            $active ? $stats['reposted']++ : $stats['posted']++;

            // int columns ki wajah se chand rupay ka rounding farq aam hai — us pe warning nahi
            if (abs($imbalance) >= self::WARN_IMBALANCE) {
                $this->recordError($companyId, $type, $doc->ref, 'warning',
                    'Source amounts did not balance; ' . number_format(abs($imbalance), 2) . ' posted to Suspense / Rounding.');
            } else {
                $this->clearError($companyId, $type, $doc->ref);
            }
        });
    }

    /**
     * Accounts resolve, negative ko doosri taraf, ek account + side ki lines jod do, zero hata do.
     * Farq ho to suspense line. Returns [lines for JournalService, imbalance].
     */
    private function normalize(PostingDocument $doc, AccountResolver $accounts): array
    {
        $sum = [];
        foreach ($doc->lines as $line) {
            $id = $accounts->id($line['account']);
            $net = (int) round(($line['debit'] - $line['credit']) * 100);
            if ($net === 0) {
                continue;
            }
            $side = $net > 0 ? 'debit' : 'credit';
            $key = $id . ':' . $side . ':' . ($line['narration'] ?? '');
            $sum[$key] = ($sum[$key] ?? ['account_id' => $id, 'side' => $side, 'cents' => 0, 'narration' => $line['narration'] ?? null]);
            $sum[$key]['cents'] += abs($net);
        }

        $debits = $credits = 0;
        foreach ($sum as $l) {
            $l['side'] === 'debit' ? $debits += $l['cents'] : $credits += $l['cents'];
        }
        if ($debits === 0 && $credits === 0) {
            return [[], 0];
        }

        $diff = $debits - $credits;
        if ($diff !== 0) {
            $sum['suspense'] = [
                'account_id' => $accounts->id('suspense'),
                'side' => $diff > 0 ? 'credit' : 'debit',
                'cents' => abs($diff),
                'narration' => 'Unbalanced source amounts',
            ];
        }

        $lines = [];
        foreach ($sum as $l) {
            $amount = number_format($l['cents'] / 100, 2, '.', '');
            $lines[] = [
                'account_id' => $l['account_id'],
                'debit' => $l['side'] === 'debit' ? $amount : 0,
                'credit' => $l['side'] === 'credit' ? $amount : 0,
                'narration' => $l['narration'],
            ];
        }
        // pehle debits, phir credits; andar account order
        usort($lines, fn ($a, $b) => [(float) $a['credit'] > 0, $a['account_id']] <=> [(float) $b['credit'] > 0, $b['account_id']]);
        return [$lines, $diff / 100];
    }

    /** Asli tareekh pe reverse; wo period band ho to aaj ki tareekh pe. */
    private function reverseEntry(JournalEntry $entry, string $reason): void
    {
        try {
            $this->journal->reverse($entry, $entry->entry_date->toDateString(), null, 'Auto: ' . $reason);
        } catch (RuntimeException $e) {
            if (!str_contains($e->getMessage(), 'period')) {
                throw $e;
            }
            $this->journal->reverse($entry, now()->toDateString(), null, 'Auto: ' . $reason);
        }
    }

    private function attempt(int $companyId, string $type, string $ref, array &$stats, callable $action): void
    {
        try {
            DB::transaction($action);
        } catch (RuntimeException $e) {
            $this->recordError($companyId, $type, $ref, 'error', $e->getMessage());
            $stats['errors']++;
        }
    }

    private function recordError(int $companyId, string $type, string $ref, string $level, string $message): void
    {
        $row = DB::table('posting_errors')->where(['company_id' => $companyId, 'source_type' => $type, 'source_ref' => $ref])->first();
        if ($row) {
            DB::table('posting_errors')->where('id', $row->id)->update([
                'level' => $level, 'message' => $message, 'attempts' => $row->attempts + 1, 'updated_at' => now(),
            ]);
        } else {
            DB::table('posting_errors')->insert([
                'company_id' => $companyId, 'source_type' => $type, 'source_ref' => $ref, 'level' => $level,
                'message' => $message, 'attempts' => 1, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function clearError(int $companyId, string $type, string $ref): void
    {
        DB::table('posting_errors')->where(['company_id' => $companyId, 'source_type' => $type, 'source_ref' => $ref])->delete();
    }
}
