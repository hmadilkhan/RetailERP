<?php

namespace App\Services\Accounting\Posting;

/**
 * Ek source record (ya daily summary) ki GL entry ka khaka. Poster banata hai, PostingService post karta hai.
 * lines: [['account' => 'cash' (system_key) | 12 (account id), 'debit' => 100.0, 'credit' => 0.0, 'narration' => null], ...]
 * Negative amount chalta hai — engine use doosri taraf kar deta hai. Zero lines hat jati hain.
 */
class PostingDocument
{
    public array $lines = [];

    public function __construct(
        public string $ref,            // source ke andar unique key, e.g. "123" ya "153:2026-10-09"
        public string $date,           // Y-m-d — entry_date
        public ?int $branchId,
        public string $narration,
        public ?int $sourceId = null,
    ) {
    }

    public function debit($account, $amount, ?string $narration = null): self
    {
        $this->lines[] = ['account' => $account, 'debit' => (float) $amount, 'credit' => 0.0, 'narration' => $narration];
        return $this;
    }

    public function credit($account, $amount, ?string $narration = null): self
    {
        $this->lines[] = ['account' => $account, 'debit' => 0.0, 'credit' => (float) $amount, 'narration' => $narration];
        return $this;
    }
}
