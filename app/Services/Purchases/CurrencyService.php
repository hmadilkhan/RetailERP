<?php

namespace App\Services\Purchases;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Phase 4.5: company ki base currency (settings.data.currency — "Rs." = PKR, khali / "Select Currency" = PKR) aur
 * exchange rates (1 foreign = rate × base, din-wise; bill / payment ke din ya us se pehle ka aakhri rate).
 */
class CurrencyService
{
    const COMMON = ['USD', 'EUR', 'GBP', 'AED', 'SAR', 'CNY', 'JPY', 'TRY', 'INR', 'PKR'];

    /** migration chali? null = check */
    public static ?bool $ready = null;

    public function ready(): bool
    {
        return self::$ready ??= Schema::hasTable('exchange_rates') && Schema::hasColumn('vendor_bills', 'currency');
    }

    public function base(int $companyId): string
    {
        $data = json_decode((string) DB::table('settings')->where('company_id', $companyId)->value('data'), true);
        $c = strtoupper(trim((string) ($data['currency'] ?? '')));
        return match (true) {
            $c === 'RS.' || $c === 'RS' || $c === 'PKR' => 'PKR',
            (bool) preg_match('/^[A-Z]{3}$/', $c) => $c,
            default => 'PKR',
        };
    }

    /** $date ya us se pehle ka aakhri rate; base currency = 1; na ho to null */
    public function rate(int $companyId, string $currency, string $date): ?float
    {
        $currency = strtoupper($currency);
        if ($currency === '' || $currency === $this->base($companyId)) {
            return 1.0;
        }
        $r = DB::table('exchange_rates')->where('company_id', $companyId)->where('currency', $currency)->where('rate_date', '<=', $date)
            ->orderByDesc('rate_date')->value('rate');
        return $r !== null ? (float) $r : null;
    }

    public function setRate(int $companyId, string $currency, string $date, $rate, $userId = null): void
    {
        $currency = strtoupper(trim($currency));
        $rate = (float) str_replace(',', '', (string) $rate);
        if (!preg_match('/^[A-Z]{3}$/', $currency) || $currency === $this->base($companyId)) {
            throw new RuntimeException('Enter a 3-letter currency code other than ' . $this->base($companyId) . ' (e.g. USD).');
        }
        if ($rate <= 0 || !strtotime($date)) {
            throw new RuntimeException('Enter the date and a rate above 0.');
        }
        DB::table('exchange_rates')->updateOrInsert(['company_id' => $companyId, 'currency' => $currency, 'rate_date' => date('Y-m-d', strtotime($date))],
            ['rate' => round($rate, 6), 'created_by' => $userId, 'updated_at' => now(), 'created_at' => now()]);
    }

    /** currencies jin ka rate hai (dropdown) */
    public function currencies(int $companyId): array
    {
        if (!$this->ready()) {
            return [];
        }
        return DB::table('exchange_rates')->where('company_id', $companyId)->distinct()->orderBy('currency')->pluck('currency')->all();
    }

    /** har currency ka aakhri rate + pichle rates */
    public function latest(int $companyId)
    {
        return DB::table('exchange_rates as r')->where('r.company_id', $companyId)
            ->whereRaw('r.rate_date = (SELECT MAX(x.rate_date) FROM exchange_rates x WHERE x.company_id = r.company_id AND x.currency = r.currency)')
            ->orderBy('r.currency')->get(['r.currency', 'r.rate_date', 'r.rate']);
    }
}
