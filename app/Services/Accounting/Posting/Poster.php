<?php

namespace App\Services\Accounting\Posting;

/**
 * Ek source (expenses, GRN, POS sales ...) ko GL documents me badalta hai. Sirf padhta hai — source tables me kuch nahi likhta.
 * documents() ko [from, to] ki har us cheez ka document dena hai jo abhi source me mojood hai;
 * jo pehle post hua tha aur ab nahi aaya (delete) us ki entry PostingService reverse kar deti hai.
 */
interface Poster
{
    /** journal_entries.source_type, e.g. 'expense' */
    public function sourceType(): string;

    /** Screen pe dikhane ke liye naam */
    public function label(): string;

    /** @return iterable<PostingDocument> */
    public function documents(int $companyId, string $from, string $to, AccountResolver $accounts): iterable;
}
