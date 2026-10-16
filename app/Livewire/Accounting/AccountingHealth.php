<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Services\Accounting\AccountingHealthService;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Phase 4.2: accounting health / go-live checks. Qaide AccountingHealthService me; nateeja 10 min cache. */
class AccountingHealth extends Component
{
    public function mount(): void
    {
        abort_unless(AccountingSetting::isEnabled(session('company_id')), 403, 'Accounting is not enabled for this company.');
    }

    public static function cacheKey(int $companyId): string
    {
        return 'accounting-health:' . $companyId;
    }

    public function refresh(): void
    {
        Cache::forget(self::cacheKey((int) session('company_id')));
    }

    #[Title('Accounting Health')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $svc = app(AccountingHealthService::class);
        $result = Cache::remember(self::cacheKey($companyId), 600, fn () => ['checks' => $svc->checks($companyId), 'at' => now()->toDateTimeString()]);
        $order = ['fail' => 0, 'warn' => 1, 'info' => 2, 'ok' => 3];
        return view('livewire.accounting.accounting-health', [
            'checks' => collect($result['checks'])->sortBy(fn ($c) => $order[$c['status']] ?? 9)->groupBy('group', true),
            'summary' => $svc->summary($result['checks']),
            'checkedAt' => $result['at'],
            'setting' => AccountingSetting::where('company_id', $companyId)->first(),
        ])->layout('layouts.master-tailwind');
    }
}
