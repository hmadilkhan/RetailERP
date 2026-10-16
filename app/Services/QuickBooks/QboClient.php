<?php

namespace App\Services\QuickBooks;

/** Phase 3.6: QuickBooks Online ke jo calls journal sync ko chahiye (test me fake se badla ja sakta hai). */
interface QboClient
{
    /** @return array<int, array{id: string, name: string, type: string, number: ?string}> active accounts */
    public function accounts(): array;

    /** DocNumber wali JournalEntry pehle se ho to us ka Id (dobara na bane) */
    public function findJournal(string $docNumber): ?string;

    /** @return string naya QBO JournalEntry Id */
    public function createJournal(array $payload): string;
}
