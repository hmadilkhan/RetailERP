<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\JournalEntryLine;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

// Company ka Chart of Accounts — tree view + add / edit / deactivate / delete.
// System accounts (system_key) posting ke liye reserved hain: sirf naam / code badal sakte hain.
class ChartOfAccounts extends Component
{
    public $search = '';
    public bool $showForm = false;
    public $editingId = null;

    public $code = '';
    public $name = '';
    public $type = 'Asset';
    public $parentId = '';
    public bool $isGroup = false;
    public bool $isActive = true;

    public function mount(): void
    {
        abort_unless(AccountingSetting::isEnabled(session('company_id')), 403, 'Accounting is not enabled for this company.');
    }

    public function create($parentId = null): void
    {
        $this->resetForm();
        if ($parentId && ($parent = $this->account($parentId))) {
            $this->parentId = (string) $parent->id;
            $this->type = $parent->type;
        }
        $this->showForm = true;
    }

    public function edit($id): void
    {
        $account = $this->account($id);
        $this->resetErrorBag();
        $this->editingId = $account->id;
        $this->code = $account->code;
        $this->name = $account->name;
        $this->type = $account->type;
        $this->parentId = (string) ($account->parent_id ?? '');
        $this->isGroup = $account->is_group;
        $this->isActive = $account->is_active;
        $this->showForm = true;
    }

    public function updatedParentId(): void
    {
        // child ka type hamesha parent jaisa
        if ($this->parentId !== '' && ($parent = $this->account($this->parentId))) {
            $this->type = $parent->type;
        }
    }

    public function save(): void
    {
        $companyId = session('company_id');
        $account = $this->editingId ? $this->account($this->editingId) : null;

        $this->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('chart_of_accounts', 'code')->where('company_id', $companyId)->ignore($this->editingId)],
            'name' => 'required|string|max:150',
            'type' => ['required', Rule::in(ChartOfAccount::TYPES)],
            'parentId' => ['nullable', Rule::exists('chart_of_accounts', 'id')->where('company_id', $companyId)->where('is_group', 1)],
        ], [], ['parentId' => 'parent group']);

        $parent = $this->parentId !== '' ? $this->account($this->parentId) : null;

        if ($account && $parent && $this->isDescendant($parent->id, $account->id)) {
            $this->addError('parentId', 'An account cannot be placed under itself.');
            return;
        }
        if ($account && !$this->isGroup && $account->is_group && $account->children()->exists()) {
            $this->addError('isGroup', 'This group has accounts under it — move them first.');
            return;
        }

        $data = [
            'code' => trim($this->code),
            'name' => trim($this->name),
            'type' => $parent ? $parent->type : $this->type,
            'parent_id' => $parent->id ?? null,
            'is_group' => $this->isGroup,
            'is_active' => $this->isActive,
        ];

        if ($account && $account->isSystem()) {
            // posting service in accounts pe depend karti hai — structure same rehna chahiye
            $data = array_intersect_key($data, array_flip(['code', 'name']));
        }

        if ($account) {
            $account->update($data);
        } else {
            ChartOfAccount::create($data + ['company_id' => $companyId]);
        }

        $this->showForm = false;
        $this->resetForm();
        session()->flash('coa_message', 'Account saved.');
    }

    public function toggleActive($id): void
    {
        $account = $this->account($id);
        if ($account->isSystem()) {
            return;
        }
        $account->update(['is_active' => !$account->is_active]);
    }

    public function delete($id): void
    {
        $account = $this->account($id);
        if ($account->isSystem() || $account->children()->exists()) {
            session()->flash('coa_error', 'System accounts and groups with accounts under them cannot be deleted.');
            return;
        }
        if (JournalEntryLine::where('account_id', $account->id)->exists()) {
            session()->flash('coa_error', 'This account has journal entries — deactivate it instead.');
            return;
        }
        $account->delete();
        session()->flash('coa_message', 'Account deleted.');
    }

    public function cancel(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    #[Title('Chart of Accounts')]
    public function render()
    {
        $accounts = ChartOfAccount::forCompany(session('company_id'))->orderBy('code')->get();
        $rows = $this->tree($accounts);

        if (trim($this->search) !== '') {
            $term = mb_strtolower(trim($this->search));
            $rows = array_values(array_filter($rows, fn ($row) => str_contains(mb_strtolower($row['account']->code . ' ' . $row['account']->name), $term)));
        }

        return view('livewire.accounting.chart-of-accounts', [
            'rows' => $rows,
            'groups' => $accounts->where('is_group', true)->where('id', '!=', $this->editingId),
            'types' => ChartOfAccount::TYPES,
            'editing' => $this->editingId ? $accounts->firstWhere('id', $this->editingId) : null,
            'counts' => $accounts->where('is_group', false)->countBy('type'),
        ])->layout('layouts.master-tailwind');
    }

    /** Parent → children order me flat list, har row ke saath depth. */
    private function tree($accounts): array
    {
        $byParent = $accounts->groupBy(fn ($a) => $a->parent_id ?? 0);
        $rows = [];
        $walk = function ($parentId, $depth) use (&$walk, &$rows, $byParent) {
            foreach ($byParent[$parentId] ?? [] as $account) {
                $rows[] = ['account' => $account, 'depth' => $depth];
                $walk($account->id, $depth + 1);
            }
        };
        $walk(0, 0);
        return $rows;
    }

    private function isDescendant($candidateId, $accountId): bool
    {
        $current = ChartOfAccount::find($candidateId);
        while ($current) {
            if ((int) $current->id === (int) $accountId) {
                return true;
            }
            $current = $current->parent_id ? ChartOfAccount::find($current->parent_id) : null;
        }
        return false;
    }

    private function account($id): ChartOfAccount
    {
        return ChartOfAccount::forCompany(session('company_id'))->findOrFail($id);
    }

    private function resetForm(): void
    {
        $this->resetErrorBag();
        $this->editingId = null;
        $this->code = '';
        $this->name = '';
        $this->type = 'Asset';
        $this->parentId = '';
        $this->isGroup = false;
        $this->isActive = true;
    }
}
