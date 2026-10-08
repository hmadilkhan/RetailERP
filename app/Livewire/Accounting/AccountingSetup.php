<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\FiscalYear;
use App\Models\Company;
use App\Services\Accounting\AccountingSetupService;
use Livewire\Attributes\Title;
use Livewire\Component;

// Super admin (roleId 1): company pe accounting module on / off. On karte hi default COA + current fiscal year banta hai.
class AccountingSetup extends Component
{
    public $companyId = '';
    public $startMonth = 7;

    public function mount(): void
    {
        abort_unless((int) session('roleId') === 1, 403);
    }

    public function updatedCompanyId(): void
    {
        $this->resetErrorBag();
        $this->startMonth = (int) (AccountingSetting::where('company_id', $this->companyId)->value('fiscal_year_start_month') ?: 7);
    }

    public function enable(AccountingSetupService $setup): void
    {
        abort_unless((int) session('roleId') === 1, 403);
        $this->validate([
            'companyId' => 'required|integer|exists:company,company_id',
            'startMonth' => 'required|integer|between:1,12',
        ]);

        $setup->enable((int) $this->companyId, (int) $this->startMonth, session('userid'));
        session()->flash('accounting_message', 'Accounting enabled. Chart of Accounts and fiscal year are ready.');
    }

    public function disable(AccountingSetupService $setup): void
    {
        abort_unless((int) session('roleId') === 1, 403);
        $setup->disable((int) $this->companyId);
        session()->flash('accounting_message', 'Accounting menu turned off for this company. Data is kept.');
    }

    #[Title('Accounting Setup')]
    public function render()
    {
        $companies = Company::where('status_id', 1)->orderBy('name')->get(['company_id', 'name']);
        $settings = AccountingSetting::get()->keyBy('company_id');

        $selected = null;
        if ($this->companyId !== '') {
            $selected = [
                'setting' => $settings[(int) $this->companyId] ?? null,
                'accounts' => ChartOfAccount::forCompany($this->companyId)->count(),
                'years' => FiscalYear::where('company_id', $this->companyId)->orderByDesc('start_date')->get(),
            ];
        }

        $enabledCompanies = $settings->filter(fn ($s) => $s->enabled)
            ->map(fn ($s) => ['setting' => $s, 'name' => $companies->firstWhere('company_id', $s->company_id)->name ?? ('#' . $s->company_id)]);

        return view('livewire.accounting.accounting-setup', [
            'companies' => $companies,
            'selected' => $selected,
            'enabledCompanies' => $enabledCompanies,
            'months' => $this->months(),
        ])->layout('layouts.master-tailwind');
    }

    private function months(): array
    {
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $end = ($m + 10) % 12 + 1;
            $months[$m] = date('F', mktime(0, 0, 0, $m, 1)) . ' – ' . date('F', mktime(0, 0, 0, $end, 1));
        }
        return $months;
    }
}
