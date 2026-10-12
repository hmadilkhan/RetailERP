<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Models\Accounting\FiscalYear;
use App\Models\Branch;
use App\Services\Accounting\FinancialStatementService;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

// Phase 1.5: GL se Trial Balance / Income Statement / Balance Sheet / Cash Flow — ek page, tabs.
class FinancialStatements extends Component
{
    const TABS = ['tb' => 'Trial Balance', 'pl' => 'Income Statement', 'bs' => 'Balance Sheet', 'cf' => 'Cash Flow'];

    #[Url(except: 'tb')]
    public $tab = 'tb';
    #[Url(except: '')]
    public $from = '';
    #[Url(except: '')]
    public $to = '';
    #[Url(as: 'branch', except: '')]
    public $branchId = '';

    public function mount(): void
    {
        abort_unless(AccountingSetting::isEnabled(session('company_id')), 403, 'Accounting is not enabled for this company.');
        if (!array_key_exists($this->tab, self::TABS)) {
            $this->tab = 'tb';
        }
        $today = now()->toDateString();
        $year = FiscalYear::where('company_id', session('company_id'))->where('start_date', '<=', $today)->where('end_date', '>=', $today)->first();
        $this->from = $this->from ?: ($year ? $year->start_date->toDateString() : now()->startOfYear()->toDateString());
        $this->to = $this->to ?: $today;
    }

    public function setTab(string $tab): void
    {
        if (array_key_exists($tab, self::TABS)) {
            $this->tab = $tab;
        }
    }

    #[Title('Financial Statements')]
    public function render(FinancialStatementService $statements)
    {
        $companyId = (int) session('company_id');
        $branch = $this->branchId !== '' ? (int) $this->branchId : null;
        $from = $this->from ?: $this->to;

        $data = match ($this->tab) {
            'pl' => $statements->incomeStatement($companyId, $from, $this->to, $branch),
            'bs' => $statements->balanceSheet($companyId, $this->to, $branch),
            'cf' => $statements->cashFlow($companyId, $from, $this->to, $branch),
            default => $statements->trialBalance($companyId, $from, $this->to, $branch),
        };

        return view('livewire.accounting.financial-statements', [
            'data' => $data,
            'tabs' => self::TABS,
            'branches' => Branch::where('company_id', $companyId)->orderBy('branch_name')->pluck('branch_name', 'branch_id'),
            'companyName' => \App\Models\Company::where('company_id', $companyId)->value('name'),
        ])->layout('layouts.master-tailwind');
    }
}
