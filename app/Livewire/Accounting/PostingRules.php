<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Models\Accounting\ChartOfAccount;
use App\Services\Accounting\Posting\PostingService;
use App\Services\Accounting\Posting\Posters\SalesPoster;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Throwable;

// Phase 1.3: automatic posting — kaunsa source kis account me jaye (mapping), "Post now", aur errors / warnings.
// Mapping na ho to default system account (General Expenses, Bank Accounts, payment mode default).
class PostingRules extends Component
{
    public array $expenseMap = [];
    public array $bankMap = [];
    public array $paymentMap = [];
    public ?array $lastRun = null;

    public function mount(): void
    {
        abort_unless(AccountingSetting::isEnabled(session('company_id')), 403, 'Accounting is not enabled for this company.');
        $current = DB::table('account_mappings')->where('company_id', session('company_id'))->get()->groupBy('map_type');
        $pick = fn ($type) => ($current[$type] ?? collect())->pluck('account_id', 'map_key')->map(fn ($id) => (string) $id)->all();
        $this->expenseMap = $pick('expense_category');
        $this->bankMap = $pick('bank_account');
        $this->paymentMap = $pick('payment_mode');
    }

    public function save(): void
    {
        $companyId = (int) session('company_id');
        $valid = ChartOfAccount::forCompany($companyId)->where('is_group', false)->where('is_active', true)->pluck('id')->map(fn ($id) => (string) $id)->all();

        DB::transaction(function () use ($companyId, $valid) {
            foreach (['expense_category' => $this->expenseMap, 'bank_account' => $this->bankMap, 'payment_mode' => $this->paymentMap] as $type => $map) {
                foreach ($map as $key => $accountId) {
                    $where = ['company_id' => $companyId, 'map_type' => $type, 'map_key' => (string) $key];
                    if ($accountId === '' || $accountId === null || !in_array((string) $accountId, $valid, true)) {
                        DB::table('account_mappings')->where($where)->delete(); // default pe wapas
                        continue;
                    }
                    DB::table('account_mappings')->updateOrInsert($where, ['account_id' => (int) $accountId, 'updated_at' => now(), 'created_at' => now()]);
                }
            }
        });

        session()->flash('posting_message', 'Mappings saved. Entries already posted in the last 45 days are re-posted to the new accounts on the next run.');
    }

    /** Pichle 45 din abhi sync karo (scheduler har 15 minute yahi karta hai). */
    public function postNow(PostingService $posting): void
    {
        try {
            $this->lastRun = $posting->sync((int) session('company_id'), now()->subDays(45)->toDateString(), now()->toDateString());
            session()->flash('posting_message', 'Posting finished.');
        } catch (Throwable $e) {
            $this->addError('posting', $e->getMessage());
        }
    }

    #[Title('Posting Rules')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $branchIds = DB::table('branch')->where('company_id', $companyId)->pluck('branch_id');
        $accounts = ChartOfAccount::forCompany($companyId)->where('is_group', false)->where('is_active', true)->orderBy('code')->get(['id', 'code', 'name', 'type']);
        $setting = AccountingSetting::where('company_id', $companyId)->first();

        return view('livewire.accounting.posting-rules', [
            'setting' => $setting,
            'categories' => DB::table('expense_categories')->whereIn('branch_id', $branchIds)->orderBy('expense_category')->get(['exp_cat_id', 'expense_category']),
            'banks' => DB::table('bank_account_generaldetails as a')->leftJoin('banks as b', 'b.bank_id', '=', 'a.bank_id')
                ->whereIn('a.branch_id_company', $branchIds)->orderBy('a.account_title')
                ->get(['a.bank_account_id', 'a.account_title', 'a.account_no', 'b.bank_name']),
            'paymentModes' => DB::table('sales_payment')->whereIn('payment_id', array_keys(SalesPoster::PAYMENT_ACCOUNTS))->orderBy('payment_id')->get(['payment_id', 'payment_mode']),
            'paymentDefaults' => SalesPoster::PAYMENT_ACCOUNTS,
            'expenseAccounts' => $accounts->where('type', 'Expense'),
            'assetAccounts' => $accounts->whereIn('type', ['Asset', 'Liability']),
            'systemNames' => ChartOfAccount::forCompany($companyId)->whereNotNull('system_key')->pluck('name', 'system_key'),
            'issues' => DB::table('posting_errors')->where('company_id', $companyId)->orderByRaw("level = 'error' DESC")->orderByDesc('updated_at')->limit(100)->get(),
            'sources' => collect(app(PostingService::class)->posters())->mapWithKeys(fn ($p) => [$p->sourceType() => $p->label()]),
        ])->layout('layouts.master-tailwind');
    }
}
