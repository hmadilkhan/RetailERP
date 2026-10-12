<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\JournalEntry;
use App\Services\Accounting\JournalService;
use App\Services\Accounting\OpeningBalanceSuggester;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use RuntimeException;

/**
 * Phase 1.5: shuruati balances — ek hi "opening_balance" entry. Har account ka Dr / Cr; farq khud Opening Balance Equity me.
 * Save pe purani opening entry reverse + nayi post (ek waqt me ek hi zinda). Automatic posting is source type ko nahi chhedti.
 */
class OpeningBalances extends Component
{
    const SOURCE = 'opening_balance';

    public $entryDate = '';
    public array $amounts = []; // account_id => ['debit' => '', 'credit' => '']
    public $search = '';
    public array $notes = [];     // account_id => "amount kahan se aaya" (Fill from system)
    public array $warnings = [];

    public function mount(): void
    {
        abort_unless(AccountingSetting::isEnabled(session('company_id')), 403, 'Accounting is not enabled for this company.');

        $current = $this->current();
        if ($current) {
            $this->entryDate = $current->entry_date->toDateString();
            foreach ($current->lines as $line) {
                $this->amounts[$line->account_id] = [
                    'debit' => (float) $line->debit ? (string) $line->debit : '',
                    'credit' => (float) $line->credit ? (string) $line->credit : '',
                ];
            }
        } else {
            // posting start se ek din pehle — taake opening automatic entries se pehle ho
            $start = AccountingSetting::where('company_id', session('company_id'))->value('posting_start_date');
            $this->entryDate = $start ? \Carbon\Carbon::parse($start)->subDay()->toDateString() : now()->toDateString();
        }
    }

    /** Purane ledgers se opening date ka hisaab form me bharo — save nahi hota, user dekh kar Save & Post kare. */
    public function fillFromSystem(OpeningBalanceSuggester $suggester): void
    {
        $this->validate(['entryDate' => 'required|date'], [], ['entryDate' => 'date']);
        $result = $suggester->suggest((int) session('company_id'), $this->entryDate);

        foreach ($result['amounts'] as $accountId => $amount) {
            $this->amounts[$accountId] = [
                'debit' => $amount > 0 ? number_format($amount, 2, '.', '') : '',
                'credit' => $amount < 0 ? number_format(-$amount, 2, '.', '') : '',
            ];
        }
        $this->notes = $result['notes'];
        $this->warnings = $result['warnings'];
        session()->flash('ob_message', 'Filled ' . count($result['amounts']) . ' accounts from the system for ' . $this->entryDate . '. Nothing is saved yet — check the numbers, then Save & Post.');
    }

    public function save(JournalService $journal): void
    {
        $this->validate(['entryDate' => 'required|date'], [], ['entryDate' => 'date']);
        $companyId = (int) session('company_id');
        $equity = ChartOfAccount::forCompany($companyId)->where('system_key', 'opening_balance_equity')->value('id');

        $lines = [];
        $net = 0;
        foreach ($this->amounts as $accountId => $amount) {
            if ((int) $accountId === (int) $equity) {
                continue; // farq khud yahan aata hai
            }
            $dr = round((float) ($amount['debit'] ?? 0), 2);
            $cr = round((float) ($amount['credit'] ?? 0), 2);
            if ($dr < 0 || $cr < 0) {
                $this->addError('opening', 'Amounts cannot be negative.');
                return;
            }
            $value = round($dr - $cr, 2);
            if ($value == 0) {
                continue;
            }
            $lines[] = ['account_id' => (int) $accountId, 'debit' => $value > 0 ? $value : 0, 'credit' => $value < 0 ? -$value : 0, 'narration' => 'Opening balance'];
            $net += $value;
        }
        $net = round($net, 2);
        if ($net != 0) {
            $lines[] = ['account_id' => (int) $equity, 'debit' => $net < 0 ? -$net : 0, 'credit' => $net > 0 ? $net : 0, 'narration' => 'Opening balance difference'];
        }

        try {
            DB::transaction(function () use ($journal, $companyId, $lines) {
                if ($current = $this->current()) {
                    $journal->reverse($current, $current->entry_date->toDateString(), session('userid'), 'Opening balances updated');
                }
                if (count($lines) >= 2) {
                    $journal->createPosted($companyId, [
                        'entry_date' => $this->entryDate,
                        'branch_id' => null,
                        'narration' => 'Opening balances',
                        'source_type' => self::SOURCE,
                        'source_ref' => 'opening',
                    ], $lines, session('userid'));
                }
            });
            session()->flash('ob_message', count($lines) >= 2 ? 'Opening balances posted.' : 'Opening balances cleared.');
        } catch (RuntimeException $e) {
            $this->addError('opening', $e->getMessage());
        }
    }

    #[Title('Opening Balances')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $accounts = ChartOfAccount::forCompany($companyId)->where('is_group', false)->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('system_key')->orWhere('system_key', '!=', 'opening_balance_equity'))
            ->orderBy('code')->get(['id', 'code', 'name', 'type']);

        if (trim($this->search) !== '') {
            $term = mb_strtolower(trim($this->search));
            $accounts = $accounts->filter(fn ($a) => str_contains(mb_strtolower($a->code . ' ' . $a->name), $term));
        }

        $dr = $cr = 0;
        foreach ($this->amounts as $a) {
            $dr += (float) ($a['debit'] ?? 0);
            $cr += (float) ($a['credit'] ?? 0);
        }

        return view('livewire.accounting.opening-balances', [
            'accountsByType' => $accounts->groupBy('type'),
            'current' => $this->current(),
            'totalDebit' => $dr,
            'totalCredit' => $cr,
            'postingStart' => AccountingSetting::where('company_id', $companyId)->value('posting_start_date'),
            'laterEntries' => JournalEntry::where('company_id', $companyId)->where('status', 'posted')
                ->where('source_type', '!=', self::SOURCE)->where('entry_date', '<=', $this->entryDate ?: now()->toDateString())->count(),
        ])->layout('layouts.master-tailwind');
    }

    private function current(): ?JournalEntry
    {
        return JournalEntry::where('company_id', session('company_id'))->where('source_type', self::SOURCE)
            ->where('status', 'posted')->whereNull('reversal_of_id')->whereDoesntHave('reversedBy')
            ->with('lines')->latest('id')->first();
    }
}
