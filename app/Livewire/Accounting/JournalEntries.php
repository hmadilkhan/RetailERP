<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\JournalEntry;
use App\Models\Branch;
use App\Services\Accounting\JournalService;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use RuntimeException;

// Phase 1.2: Journal entries — list, manual JV (draft → post), reverse. Saare rules JournalService me hain.
class JournalEntries extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $from = '';
    #[Url(except: '')]
    public $to = '';
    #[Url(except: '')]
    public $status = '';
    #[Url(except: '')]
    public $search = '';

    // form
    public bool $showForm = false;
    public $entryId = null;
    public $entryDate = '';
    public $branchId = '';
    public $narration = '';
    public array $lines = [];

    // detail — ?entry=ID se seedha khulti hai (General Ledger ka link)
    #[Url(as: 'entry', except: null)]
    public $viewId = null;
    public $reverseDate = '';
    public $reverseReason = '';

    public function mount(): void
    {
        abort_unless(AccountingSetting::isEnabled(session('company_id')), 403, 'Accounting is not enabled for this company.');
        $this->from = $this->from ?: now()->startOfMonth()->toDateString();
        $this->to = $this->to ?: now()->toDateString();
    }

    public function updating($name): void
    {
        if (in_array($name, ['from', 'to', 'status', 'search'])) {
            $this->resetPage();
        }
    }

    public function create(): void
    {
        $this->resetErrorBag();
        $this->viewId = null;
        $this->entryId = null;
        $this->entryDate = now()->toDateString();
        $this->branchId = (string) (session('branch') ?? '');
        $this->narration = '';
        $this->lines = [$this->blankLine(), $this->blankLine()];
        $this->showForm = true;
    }

    public function edit($id): void
    {
        $entry = $this->entry($id);
        if ($entry->isPosted()) {
            return;
        }
        $this->resetErrorBag();
        $this->viewId = null;
        $this->entryId = $entry->id;
        $this->entryDate = $entry->entry_date->toDateString();
        $this->branchId = (string) ($entry->branch_id ?? '');
        $this->narration = (string) $entry->narration;
        $this->lines = $entry->lines->map(fn ($l) => [
            'account_id' => (string) $l->account_id,
            'debit' => (float) $l->debit > 0 ? (string) $l->debit : '',
            'credit' => (float) $l->credit > 0 ? (string) $l->credit : '',
            'narration' => (string) $l->narration,
        ])->all();
        $this->showForm = true;
    }

    public function addLine(): void
    {
        $this->lines[] = $this->blankLine();
    }

    public function removeLine($index): void
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
        if (count($this->lines) < 2) {
            $this->lines[] = $this->blankLine();
        }
    }

    public function saveDraft(JournalService $journal): void
    {
        $this->persist($journal, false);
    }

    public function saveAndPost(JournalService $journal): void
    {
        $this->persist($journal, true);
    }

    public function cancel(): void
    {
        $this->showForm = false;
        $this->resetErrorBag();
    }

    public function view($id): void
    {
        $this->showForm = false;
        $this->viewId = (int) $this->viewId === (int) $id ? null : $id;
        $this->reverseDate = now()->toDateString();
        $this->reverseReason = '';
        $this->resetErrorBag();
    }

    public function post($id, JournalService $journal): void
    {
        $this->attempt(fn () => $journal->post($this->entry($id), session('userid')), 'Entry posted.');
    }

    public function reverse($id, JournalService $journal): void
    {
        $this->validate(['reverseDate' => 'required|date'], [], ['reverseDate' => 'reversal date']);
        $this->attempt(function () use ($id, $journal) {
            $reversal = $journal->reverse($this->entry($id), $this->reverseDate, session('userid'), trim($this->reverseReason) ?: null);
            $this->viewId = $reversal->id;
        }, 'Reversal entry posted.');
    }

    public function delete($id, JournalService $journal): void
    {
        $this->attempt(function () use ($id, $journal) {
            $journal->deleteDraft($this->entry($id));
            $this->viewId = null;
        }, 'Draft deleted.');
    }

    #[Title('Journal Entries')]
    public function render()
    {
        $companyId = session('company_id');

        $entries = JournalEntry::where('company_id', $companyId)
            ->when($this->from, fn ($q) => $q->where('entry_date', '>=', $this->from))
            ->when($this->to, fn ($q) => $q->where('entry_date', '<=', $this->to))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when(trim($this->search) !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('entry_no', 'like', '%' . trim($this->search) . '%')
                ->orWhere('narration', 'like', '%' . trim($this->search) . '%')))
            ->withExists('reversedBy')
            ->orderByDesc('entry_date')->orderByDesc('id')
            ->paginate(20);

        $accounts = ChartOfAccount::forCompany($companyId)->where('is_group', false)->where('is_active', true)
            ->orderBy('code')->get(['id', 'code', 'name', 'type']);

        $debits = array_sum(array_map(fn ($l) => $this->cents($l['debit'] ?? 0), $this->lines));
        $credits = array_sum(array_map(fn ($l) => $this->cents($l['credit'] ?? 0), $this->lines));

        return view('livewire.accounting.journal-entries', [
            'entries' => $entries,
            'accountsByType' => $accounts->groupBy('type'),
            'branches' => Branch::where('company_id', $companyId)->where('status_id', 1)->orderBy('branch_name')->get(['branch_id', 'branch_name']),
            'branchNames' => Branch::where('company_id', $companyId)->pluck('branch_name', 'branch_id'),
            'viewing' => $this->viewId ? JournalEntry::where('company_id', $companyId)->with(['lines.account', 'reversalOf', 'reversedBy'])->find($this->viewId) : null,
            'totalDebit' => $debits / 100,
            'totalCredit' => $credits / 100,
            'difference' => ($debits - $credits) / 100,
        ])->layout('layouts.master-tailwind');
    }

    private function persist(JournalService $journal, bool $post): void
    {
        $this->validate(['entryDate' => 'required|date', 'narration' => 'nullable|string|max:500'], [], ['entryDate' => 'date']);
        $existing = $this->entryId ? $this->entry($this->entryId) : null;

        $this->attempt(function () use ($journal, $post, $existing) {
            $entry = $journal->saveDraft((int) session('company_id'), [
                'entry_date' => $this->entryDate,
                'branch_id' => $this->branchId,
                'narration' => trim($this->narration),
            ], $this->lines, $existing, session('userid'));
            $this->entryId = $entry->id; // post fail ho to dobara save isi draft ko update kare, naya na bane

            if ($post) {
                $journal->post($entry, session('userid'));
            }
            $this->showForm = false;
            $this->viewId = $entry->id;
        }, $post ? 'Entry posted.' : 'Draft saved.');
    }

    /** Service ke rule toote to message form ke upar dikhao. Post fail ho to bhi draft save rehta hai. */
    private function attempt(callable $action, string $success): void
    {
        try {
            $action();
            session()->flash('je_message', $success);
        } catch (RuntimeException $e) {
            $this->addError('journal', $e->getMessage());
        }
    }

    private function entry($id): JournalEntry
    {
        return JournalEntry::where('company_id', session('company_id'))->findOrFail($id);
    }

    private function blankLine(): array
    {
        return ['account_id' => '', 'debit' => '', 'credit' => '', 'narration' => ''];
    }

    private function cents($value): int
    {
        return (int) round(((float) str_replace(',', '', (string) $value)) * 100);
    }
}
