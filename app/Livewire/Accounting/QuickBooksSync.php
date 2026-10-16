<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Services\QuickBooks\QboJournalSyncService;
use Livewire\Attributes\Title;
use Livewire\Component;
use Throwable;

/** Phase 3.6: QuickBooks — connection, journal sync on / off, account mapping, sync status. Qaide QboJournalSyncService me. */
class QuickBooksSync extends Component
{
    public bool $enabled = false;
    public $fromDate = '';
    public array $map = [];          // account_id => qbo_account_id
    public array $qboAccounts = [];  // QBO se load (id, name, type, number)
    public bool $showAll = false;    // sirf istemal wale accounts / saare
    public int $autoCount = 0;

    public function mount(): void
    {
        abort_unless(AccountingSetting::isEnabled(session('company_id')), 403, 'Accounting is not enabled for this company.');
        $svc = app(QboJournalSyncService::class);
        if (!$svc->ready()) {
            return;
        }
        $s = $svc->setting((int) session('company_id'));
        $this->enabled = (bool) ($s->sync_journals ?? false);
        $this->fromDate = $s->sync_from_date ?? (AccountingSetting::where('company_id', session('company_id'))->value('posting_start_date') ?: date('Y-m-01'));
        foreach ($svc->accountsToMap((int) session('company_id'), null) as $a) {
            $this->map[$a['id']] = (string) ($a['qbo_account_id'] ?? '');
            if ($a['qbo_account_id']) {
                $this->qboAccounts[$a['qbo_account_id']] = ['id' => $a['qbo_account_id'], 'name' => $a['qbo_account_name'] ?: ('#' . $a['qbo_account_id']), 'type' => '', 'number' => null];
            }
        }
        $this->qboAccounts = array_values($this->qboAccounts);
    }

    private function admin(): bool
    {
        return in_array((int) session('roleId'), [1, 2], true);
    }

    public function loadAccounts(): void
    {
        $this->attempt(function () {
            $this->qboAccounts = app(QboJournalSyncService::class)->client((int) session('company_id'))->accounts();
        }, fn () => count($this->qboAccounts) . ' QuickBooks accounts loaded.');
    }

    public function autoMap(): void
    {
        $this->attempt(function () {
            if (!$this->qboAccounts || !$this->qboAccounts[0]['type']) {
                $this->qboAccounts = app(QboJournalSyncService::class)->client((int) session('company_id'))->accounts();
            }
            $this->autoCount = 0;
            foreach (app(QboJournalSyncService::class)->suggestMapping((int) session('company_id'), $this->qboAccounts) as $acc => $qbo) {
                if (($this->map[$acc] ?? '') === '') {
                    $this->map[$acc] = (string) $qbo;
                    $this->autoCount++;
                }
            }
        }, fn () => $this->autoCount . ' account(s) matched by number or name. Review and Save mapping.');
    }

    public function saveMapping(): void
    {
        abort_unless($this->admin(), 403);
        $this->attempt(fn () => app(QboJournalSyncService::class)->saveMapping((int) session('company_id'), $this->map, $this->qboAccounts), fn () => 'Mapping saved.');
    }

    public function saveSettings(): void
    {
        abort_unless($this->admin(), 403);
        $this->attempt(fn () => app(QboJournalSyncService::class)->saveSettings((int) session('company_id'), $this->enabled, $this->fromDate ?: null),
            fn () => $this->enabled ? 'Journal sync is on. Entries are sent every 30 minutes.' : 'Journal sync is off.');
    }

    public function syncNow(): void
    {
        abort_unless($this->admin(), 403);
        $r = null;
        $this->attempt(function () use (&$r) {
            $r = app(QboJournalSyncService::class)->sync((int) session('company_id'));
        }, function () use (&$r) {
            return "Sent {$r['synced']} entr" . ($r['synced'] === 1 ? 'y' : 'ies') . ($r['failed'] ? ", {$r['failed']} failed — see below." : '.');
        });
    }

    public function retryFailed(): void
    {
        abort_unless($this->admin(), 403);
        app(QboJournalSyncService::class)->retryFailed((int) session('company_id'));
        session()->flash('qbo_message', 'Failed entries will be tried again on the next sync.');
    }

    #[Title('QuickBooks Sync')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $svc = app(QboJournalSyncService::class);
        $ready = $svc->ready();
        $setting = $ready ? $svc->setting($companyId) : null;
        $accounts = $ready ? collect($svc->accountsToMap($companyId, $this->fromDate ?: null)) : collect();
        return view('livewire.accounting.quickbooks-sync', [
            'ready' => $ready,
            'setting' => $setting,
            'admin' => $this->admin(),
            'accounts' => $this->showAll ? $accounts : $accounts->filter(fn ($a) => $a['used'] || $a['qbo_account_id']),
            'unmappedUsed' => $accounts->filter(fn ($a) => $a['used'] && ($this->map[$a['id']] ?? '') === '')->count(),
            'counts' => $ready && $setting ? $svc->counts($companyId, $setting->sync_from_date) : null,
            'failures' => $ready && $setting ? $svc->failures($companyId) : collect(),
        ])->layout('layouts.master-tailwind');
    }

    private function attempt(callable $fn, callable $message): void
    {
        $this->resetErrorBag();
        try {
            $fn();
        } catch (Throwable $e) {
            $this->addError('qbo', $e->getMessage());
            return;
        }
        session()->flash('qbo_message', $message());
    }
}
