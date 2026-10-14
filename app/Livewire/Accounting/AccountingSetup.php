<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\FiscalYear;
use App\Models\Company;
use App\Services\Accounting\AccountingSetupService;
use App\Services\Accounting\BackfillService;
use Throwable;
use Livewire\Attributes\Title;
use Livewire\Component;

// Super admin (roleId 1): company pe accounting module on / off. On karte hi default COA + current fiscal year banta hai.
class AccountingSetup extends Component
{
    public $companyId = '';
    public $startMonth = 7;
    public $postingStartDate = '';
    public bool $customerLedgerReversed = false;
    public $backfillFrom = '';
    public ?array $backfillPlan = null;
    public ?array $backfillResult = null;

    public function mount(): void
    {
        abort_unless((int) session('roleId') === 1, 403);
    }

    public function updatedCompanyId(): void
    {
        $this->resetErrorBag();
        $setting = AccountingSetting::where('company_id', $this->companyId)->first();
        $this->startMonth = (int) ($setting->fiscal_year_start_month ?? 7);
        $this->postingStartDate = $setting && $setting->posting_start_date ? $setting->posting_start_date->toDateString() : '';
        $this->customerLedgerReversed = (bool) ($setting->customer_ledger_reversed ?? false);
        $this->backfillPlan = $this->backfillResult = null;
        $this->backfillFrom = '';
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

    /** Automatic posting (Phase 1.3) kis tareekh se. Khali = band. Purana data 1.6 (backfill) me aata hai. */
    public function savePostingStart(): void
    {
        abort_unless((int) session('roleId') === 1, 403);
        $this->validate([
            'companyId' => 'required|integer',
            'postingStartDate' => 'nullable|date',
        ], [], ['postingStartDate' => 'posting start date']);

        $setting = AccountingSetting::where('company_id', $this->companyId)->where('enabled', true)->first();
        if (!$setting) {
            $this->addError('postingStartDate', 'Enable accounting for this company first.');
            return;
        }
        $setting->update(['posting_start_date' => $this->postingStartDate ?: null]);
        session()->flash('accounting_message', $this->postingStartDate
            ? 'Automatic posting starts from ' . $this->postingStartDate . '. Entries appear within 15 minutes.'
            : 'Automatic posting turned off.');
    }

    /**
     * Customer ledger (customer_account) ka sign: kuch companies ledger screen ki ulti headings ke hisaab se
     * bill credit me aur wasooli debit me likhti hain. Badalne ke baad purani entries Backfill dobara chalane se theek hoti hain.
     */
    public function saveCustomerLedgerSign(): void
    {
        abort_unless((int) session('roleId') === 1, 403);
        $setting = AccountingSetting::where('company_id', $this->companyId)->where('enabled', true)->first();
        if (!$setting) {
            return;
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('accounting_settings', 'customer_ledger_reversed')) {
            $this->addError('customerLedgerReversed', 'Run the migration first (customer_ledger_reversed column is missing).');
            return;
        }
        $setting->update(['customer_ledger_reversed' => $this->customerLedgerReversed]);
        session()->flash('accounting_message', 'Customer ledger sign saved. Run Backfill again to repost older customer adjustments.');
    }

    /** Phase 1.6: kya hoga — kuch save nahi */
    public function previewBackfill(BackfillService $backfill): void
    {
        abort_unless((int) session('roleId') === 1, 403);
        $this->backfillResult = null;
        try {
            $this->backfillFrom = $this->backfillFrom ?: (string) $backfill->suggestedCutoff((int) $this->companyId);
            if (!$this->backfillFrom) {
                $this->addError('backfill', 'No sales, expenses or vendor data found for this company.');
                return;
            }
            $this->backfillPlan = $backfill->plan((int) $this->companyId, $this->backfillFrom);
        } catch (Throwable $e) {
            $this->addError('backfill', $e->getMessage());
        }
    }

    public function runBackfill(BackfillService $backfill): void
    {
        abort_unless((int) session('roleId') === 1, 403);
        $this->validate(['backfillFrom' => 'required|date'], [], ['backfillFrom' => 'cut-off date']);
        try {
            @set_time_limit(0);
            $this->backfillResult = $backfill->run((int) $this->companyId, $this->backfillFrom, session('userid'));
            $this->backfillPlan = null;
            $this->postingStartDate = $this->backfillFrom;
            session()->flash('accounting_message', 'History posted from ' . $this->backfillFrom . '.');
        } catch (Throwable $e) {
            $this->addError('backfill', $e->getMessage());
        }
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
