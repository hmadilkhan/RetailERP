<?php

namespace App\Services\Accounting\Posting;

use App\Models\Accounting\ChartOfAccount;
use App\Services\Accounting\AccountingSetupService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Posting ke liye account dhoondna: system_key (cash, sales, cogs ...) ya mapping (expense category → account).
 * Ek company ke liye bana, ek sync run me cache rehta hai.
 */
class AccountResolver
{
    private array $systemIds;
    private array $mappings = [];
    private array $pseudo = [];

    /** $readOnly: preview ke liye — missing system account seed nahi hota, nakli (negative) id milti hai. */
    public function __construct(private int $companyId, private bool $readOnly = false)
    {
        $this->loadSystem();
    }

    /** system_key ya account id → account id */
    public function id($account): int
    {
        if (is_int($account)) {
            return $account;
        }
        if (!isset($this->systemIds[$account]) && $this->readOnly) {
            return $this->pseudo[$account] ??= -(count($this->pseudo) + 1);
        }
        if (!isset($this->systemIds[$account])) {
            // naya system account (e.g. suspense) purani company me abhi seed nahi hua
            app(AccountingSetupService::class)->seedChartOfAccounts($this->companyId);
            $this->loadSystem();
        }
        if (!isset($this->systemIds[$account])) {
            throw new RuntimeException("System account '{$account}' not found in the Chart of Accounts.");
        }
        return $this->systemIds[$account];
    }

    /** Mapping ho to us ka account, warna fallback system account. */
    public function mapped(string $type, $key, string $fallbackSystemKey): int
    {
        if (!isset($this->mappings[$type])) {
            try {
                $this->mappings[$type] = DB::table('account_mappings')
                    ->where('company_id', $this->companyId)->where('map_type', $type)
                    ->pluck('account_id', 'map_key')->all();
            } catch (QueryException $e) {
                if (!$this->readOnly) {
                    throw $e;
                }
                $this->mappings[$type] = []; // preview: table abhi nahi bana (migration pending)
            }
        }
        return (int) ($this->mappings[$type][(string) $key] ?? $this->id($fallbackSystemKey));
    }

    /** Preview ke liye: id → system_key (nakli ids samet) */
    public function keys(): array
    {
        return array_flip($this->systemIds) + array_flip($this->pseudo);
    }

    private function loadSystem(): void
    {
        $this->systemIds = ChartOfAccount::forCompany($this->companyId)->whereNotNull('system_key')
            ->pluck('id', 'system_key')->map(fn ($id) => (int) $id)->all();
    }
}
