<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Services\Accounting\FixedAssetService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use RuntimeException;

/** Phase 3.3: fixed asset register, depreciation run, disposal. Qaide FixedAssetService me. */
class FixedAssets extends Component
{
    public bool $showForm = false;
    public ?int $viewId = null;
    public array $form = [];
    public $runUpTo = '';
    public $statusFilter = 'active';

    // dispose
    public bool $disposing = false;
    public $disposeDate = '';
    public $disposeAmount = '';
    public $disposeAccount = '';

    public function mount(): void
    {
        abort_unless(AccountingSetting::isEnabled(session('company_id')), 403, 'Accounting is not enabled for this company.');
        $this->runUpTo = date('Y-m', strtotime('first day of last month'));
        $this->resetForm();
    }

    public function create(): void
    {
        $this->validate([
            'form.name' => 'required|string|max:255', 'form.asset_account_id' => 'required|integer', 'form.purchase_date' => 'required|date',
            'form.cost' => 'required|numeric|min:0.01', 'form.salvage_value' => 'nullable|numeric|min:0', 'form.useful_life_months' => 'required|integer|min:1|max:600',
            'form.method' => 'required|in:straight_line,declining', 'form.rate' => 'nullable|numeric|min:0', 'form.opening_accumulated' => 'nullable|numeric|min:0',
            'form.depreciation_start' => 'nullable|date',
        ], [], [
            'form.name' => 'name', 'form.asset_account_id' => 'asset account', 'form.purchase_date' => 'purchase date', 'form.cost' => 'cost',
            'form.useful_life_months' => 'useful life', 'form.depreciation_start' => 'depreciation start',
        ]);
        $this->run(function () {
            $paid = $this->form['paid_from'] !== '' ? (int) $this->form['paid_from'] : null;
            $this->viewId = app(FixedAssetService::class)->create((int) session('company_id'), $this->form, session('userid'), $paid);
            $this->showForm = false;
            $this->resetForm();
        }, 'Asset added.');
    }

    public function runDepreciation(): void
    {
        $this->validate(['runUpTo' => 'required|date_format:Y-m'], [], ['runUpTo' => 'month']);
        $this->run(function () {
            $done = app(FixedAssetService::class)->run((int) session('company_id'), $this->runUpTo, session('userid'));
            session()->flash('fa_message', $done
                ? 'Depreciation posted: ' . collect($done)->map(fn ($r, $p) => $p . ' ' . $r['entry_no'] . ' (' . number_format($r['amount'], 2) . ')')->implode(', ')
                : 'Nothing to post — depreciation is up to date.');
        }, null);
    }

    public function startDispose(): void
    {
        $this->disposing = true;
        $this->disposeDate = date('Y-m-d');
        $this->disposeAmount = '0';
        $this->disposeAccount = '';
    }

    public function dispose(): void
    {
        $this->validate(['disposeDate' => 'required|date', 'disposeAmount' => 'required|numeric|min:0'], [], ['disposeDate' => 'date', 'disposeAmount' => 'amount']);
        $this->run(function () {
            app(FixedAssetService::class)->dispose((int) session('company_id'), (int) $this->viewId, $this->disposeDate, (float) $this->disposeAmount,
                $this->disposeAccount !== '' ? (int) $this->disposeAccount : null, session('userid'));
            $this->disposing = false;
        }, 'Asset disposed and posted to the ledger.');
    }

    public function show(int $id): void
    {
        $this->resetErrorBag();
        $this->viewId = $id;
        $this->disposing = false;
        $this->showForm = false;
    }

    public function back(): void
    {
        $this->viewId = null;
        $this->disposing = false;
    }

    #[Title('Fixed Assets')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $svc = app(FixedAssetService::class);
        $assets = DB::table('fixed_assets as f')->leftJoin('chart_of_accounts as a', 'a.id', '=', 'f.asset_account_id')
            ->where('f.company_id', $companyId)->when($this->statusFilter, fn ($q) => $q->where('f.status', $this->statusFilter))
            ->orderBy('f.code')->get(['f.*', 'a.code as account_code', 'a.name as account_name']);
        foreach ($assets as $a) {
            $a->accumulated = $svc->accumulated($a);
            $a->book_value = round((float) $a->cost - $a->accumulated, 2);
        }
        $viewing = $this->viewId ? DB::table('fixed_assets')->where('company_id', $companyId)->find($this->viewId) : null;
        $fixedGroup = DB::table('chart_of_accounts')->where('company_id', $companyId)->where('code', '1200')->value('id');

        return view('livewire.accounting.fixed-assets', [
            'assets' => $assets,
            'viewing' => $viewing,
            'history' => $viewing ? DB::table('fixed_asset_depreciations as d')->leftJoin('journal_entries as e', 'e.id', '=', 'd.journal_entry_id')
                ->where('d.fixed_asset_id', $viewing->id)->orderBy('d.period')->get(['d.*', 'e.entry_no']) : collect(),
            'schedule' => $viewing && $viewing->status === 'active' ? $svc->schedule($viewing, 12) : [],
            'viewingAccumulated' => $viewing ? $svc->accumulated($viewing) : 0,
            'assetAccounts' => DB::table('chart_of_accounts')->where('company_id', $companyId)->where('type', 'Asset')->where('is_group', 0)->where('is_active', 1)
                ->where(fn ($q) => $q->where('parent_id', $fixedGroup ?: 0)->orWhere('code', 'like', '12%'))->whereNull('system_key')->orderBy('code')->get(['id', 'code', 'name']),
            'payAccounts' => DB::table('chart_of_accounts')->where('company_id', $companyId)->where('is_group', 0)->where('is_active', 1)->orderBy('code')->get(['id', 'code', 'name']),
            'branches' => DB::table('branch')->where('company_id', $companyId)->where('status_id', 1)->orderBy('branch_name')->get(['branch_id', 'branch_name']),
        ])->layout('layouts.master-tailwind');
    }

    private function resetForm(): void
    {
        $this->form = ['name' => '', 'asset_account_id' => '', 'branch_id' => '', 'purchase_date' => date('Y-m-d'), 'depreciation_start' => '',
            'cost' => '', 'salvage_value' => '0', 'useful_life_months' => '60', 'method' => 'straight_line', 'rate' => '',
            'opening_accumulated' => '0', 'paid_from' => '', 'notes' => ''];
    }

    private function run(callable $fn, ?string $message): void
    {
        $this->resetErrorBag();
        try {
            $fn();
        } catch (RuntimeException $e) {
            $this->addError('fa', $e->getMessage());
            return;
        }
        if ($message) {
            session()->flash('fa_message', $message);
        }
    }
}
