<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Phase 3.4: cost centers (department / project), automatic posting ke qaide (branch default sirf income / expense lines,
 * expense category), aur cost center-wise P&L. Manual JV lines pe cost center Journal Entries form se.
 * Qaide naye posting run me lagte hain; purani entries tab badalti hain jab document dobara post ho (Backfill se).
 */
class CostCenters extends Component
{
    public $tab = 'report';   // report | centers | rules
    public $newCode = '';
    public $newName = '';
    public $from = '';
    public $to = '';
    public $branchId = '';
    public array $branchRule = [];   // branch_id => cost center id
    public array $categoryRule = []; // exp_cat_id => cost center id

    public function mount(): void
    {
        abort_unless(AccountingSetting::isEnabled(session('company_id')), 403, 'Accounting is not enabled for this company.');
        $this->from = date('Y-m-01');
        $this->to = date('Y-m-d');
        $this->loadRules();
    }

    public function addCenter(): void
    {
        $this->validate(['newCode' => 'required|string|max:20', 'newName' => 'required|string|max:150'], [], ['newCode' => 'code', 'newName' => 'name']);
        if (DB::table('cost_centers')->where('company_id', session('company_id'))->where('code', trim($this->newCode))->exists()) {
            $this->addError('newCode', 'This code is already used.');
            return;
        }
        DB::table('cost_centers')->insert(['company_id' => (int) session('company_id'), 'code' => trim($this->newCode), 'name' => trim($this->newName),
            'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $this->newCode = $this->newName = '';
        session()->flash('cc_message', 'Cost center added.');
    }

    public function toggleCenter(int $id): void
    {
        DB::table('cost_centers')->where('company_id', session('company_id'))->where('id', $id)->update(['is_active' => DB::raw('1 - is_active'), 'updated_at' => now()]);
    }

    public function saveRules(): void
    {
        $companyId = (int) session('company_id');
        $valid = DB::table('cost_centers')->where('company_id', $companyId)->pluck('id')->map(fn ($v) => (int) $v)->all();
        DB::transaction(function () use ($companyId, $valid) {
            DB::table('cost_center_assignments')->where('company_id', $companyId)->delete();
            foreach (['branch' => $this->branchRule, 'expense_category' => $this->categoryRule] as $source => $rules) {
                foreach ($rules as $sourceId => $cc) {
                    if ($cc !== '' && in_array((int) $cc, $valid, true)) {
                        DB::table('cost_center_assignments')->insert(['company_id' => $companyId, 'source' => $source, 'source_id' => (int) $sourceId,
                            'cost_center_id' => (int) $cc, 'created_at' => now(), 'updated_at' => now()]);
                    }
                }
            }
        });
        session()->flash('cc_message', 'Rules saved. New automatic postings use them; to apply them to older entries run Backfill again from Accounting Setup.');
    }

    #[Title('Cost Centers')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $centers = DB::table('cost_centers')->where('company_id', $companyId)->orderBy('code')->get();
        $data = [
            'centers' => $centers,
            'branches' => DB::table('branch')->where('company_id', $companyId)->where('status_id', 1)->orderBy('branch_name')->get(['branch_id', 'branch_name']),
        ];
        if ($this->tab === 'rules') {
            $data['categories'] = DB::table('expense_categories')->whereIn('branch_id', $data['branches']->pluck('branch_id')->all() ?: [0])
                ->orderBy('expense_category')->get(['exp_cat_id', 'expense_category']);
        }
        if ($this->tab === 'report') {
            $data += $this->report($companyId, $centers);
        }
        return view('livewire.accounting.cost-centers', $data)->layout('layouts.master-tailwind');
    }

    /** income / expense accounts × cost center (posted, period, year close chhod kar) */
    private function report(int $companyId, $centers): array
    {
        $from = strtotime($this->from) ? date('Y-m-d', strtotime($this->from)) : date('Y-m-01');
        $to = strtotime($this->to) ? date('Y-m-d', strtotime($this->to)) : date('Y-m-d');
        $rows = DB::table('journal_entry_lines as l')->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->join('chart_of_accounts as a', 'a.id', '=', 'l.account_id')
            ->where('e.company_id', $companyId)->where('e.status', 'posted')->whereBetween('e.entry_date', [$from, $to])
            ->where(fn ($q) => $q->whereNull('e.source_type')->orWhere('e.source_type', '<>', 'year_close'))
            ->whereIn('a.type', ['Revenue', 'Expense'])
            ->when($this->branchId !== '', fn ($q) => $q->where('e.branch_id', (int) $this->branchId))
            ->groupBy('a.id', 'a.code', 'a.name', 'a.type', 'l.cost_center_id')
            ->selectRaw('a.id, a.code, a.name, a.type, l.cost_center_id, SUM(l.credit - l.debit) AS net')
            ->get();

        $accounts = [];
        $totals = [];
        foreach ($rows as $r) {
            $key = $r->cost_center_id ?: 0;
            $amount = (float) $r->net; // income +, kharcha − (credit − debit)
            $accounts[$r->type][$r->id] ??= ['code' => $r->code, 'name' => $r->name, 'cols' => []];
            $accounts[$r->type][$r->id]['cols'][$key] = ($accounts[$r->type][$r->id]['cols'][$key] ?? 0) + $amount;
            $totals[$key] = ($totals[$key] ?? 0) + $amount;
        }
        foreach ($accounts as &$group) {
            uasort($group, fn ($a, $b) => strcmp($a['code'], $b['code']));
        }
        unset($group);
        $columns = $centers->filter(fn ($c) => isset($totals[$c->id]) || $c->is_active)->values();
        return ['report' => $accounts, 'totals' => $totals, 'columns' => $columns, 'hasUnassigned' => isset($totals[0])];
    }

    private function loadRules(): void
    {
        foreach (DB::table('cost_center_assignments')->where('company_id', session('company_id'))->get() as $r) {
            $r->source === 'branch' ? $this->branchRule[$r->source_id] = (string) $r->cost_center_id : $this->categoryRule[$r->source_id] = (string) $r->cost_center_id;
        }
    }
}
