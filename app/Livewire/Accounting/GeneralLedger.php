<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\JournalEntryLine;
use App\Models\Branch;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

// Phase 1.2: account-wise ledger — opening balance, posted entries, running balance. Group chuno to us ke andar ke saare accounts.
// Sirf posted entries ginti me aati hain; draft nahi.
class GeneralLedger extends Component
{
    #[Url(as: 'account', except: '')]
    public $accountId = '';
    #[Url(except: '')]
    public $from = '';
    #[Url(except: '')]
    public $to = '';
    #[Url(as: 'branch', except: '')]
    public $branchId = '';

    public function mount(): void
    {
        abort_unless(AccountingSetting::isEnabled(session('company_id')), 403, 'Accounting is not enabled for this company.');
        $this->from = $this->from ?: now()->startOfMonth()->toDateString();
        $this->to = $this->to ?: now()->toDateString();
    }

    #[Title('General Ledger')]
    public function render()
    {
        $companyId = session('company_id');
        $accounts = ChartOfAccount::forCompany($companyId)->orderBy('code')->get();
        $account = $this->accountId !== '' ? $accounts->firstWhere('id', (int) $this->accountId) : null;

        $data = ['opening' => 0, 'rows' => collect(), 'debit' => 0, 'credit' => 0, 'closing' => 0];
        if ($account) {
            $ids = $this->withDescendants($accounts, $account->id);
            $base = fn () => JournalEntryLine::query()
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->where('journal_entries.company_id', $companyId)
                ->where('journal_entries.status', 'posted')
                ->whereIn('journal_entry_lines.account_id', $ids)
                ->when($this->branchId !== '', fn ($q) => $q->where('journal_entries.branch_id', $this->branchId));

            $opening = $this->from
                ? (float) $base()->where('journal_entries.entry_date', '<', $this->from)
                    ->selectRaw('COALESCE(SUM(journal_entry_lines.debit - journal_entry_lines.credit), 0) as bal')->value('bal')
                : 0;

            $rows = $base()
                ->when($this->from, fn ($q) => $q->where('journal_entries.entry_date', '>=', $this->from))
                ->when($this->to, fn ($q) => $q->where('journal_entries.entry_date', '<=', $this->to))
                ->orderBy('journal_entries.entry_date')->orderBy('journal_entries.id')->orderBy('journal_entry_lines.id')
                ->get([
                    'journal_entry_lines.*', 'journal_entries.entry_no', 'journal_entries.entry_date',
                    'journal_entries.narration as entry_narration', 'journal_entries.source_type', 'journal_entries.branch_id',
                ]);

            $running = $opening;
            $rows = $rows->map(function ($row) use (&$running, $accounts) {
                $running += (float) $row->debit - (float) $row->credit;
                $row->running = $running;
                $row->account_label = optional($accounts->firstWhere('id', $row->account_id))->name;
                return $row;
            });

            $data = [
                'opening' => $opening,
                'rows' => $rows,
                'debit' => (float) $rows->sum('debit'),
                'credit' => (float) $rows->sum('credit'),
                'closing' => $running,
            ];
        }

        return view('livewire.accounting.general-ledger', $data + [
            'accounts' => $accounts,
            'account' => $account,
            'depths' => $this->depths($accounts),
            'branches' => Branch::where('company_id', $companyId)->orderBy('branch_name')->pluck('branch_name', 'branch_id'),
        ])->layout('layouts.master-tailwind');
    }

    private function withDescendants($accounts, $rootId): array
    {
        $ids = [$rootId];
        $queue = [$rootId];
        while ($queue) {
            $parent = array_shift($queue);
            foreach ($accounts->where('parent_id', $parent) as $child) {
                $ids[] = $child->id;
                $queue[] = $child->id;
            }
        }
        return $ids;
    }

    private function depths($accounts): array
    {
        $depths = [];
        foreach ($accounts as $a) {
            $d = 0;
            $p = $a->parent_id;
            while ($p && $d < 10) {
                $d++;
                $p = optional($accounts->firstWhere('id', $p))->parent_id;
            }
            $depths[$a->id] = $d;
        }
        return $depths;
    }
}
