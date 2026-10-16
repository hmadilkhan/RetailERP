<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Services\Accounting\BudgetService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use RuntimeException;

/** Phase 3.2: budgets (grid) + budget vs actual. Qaide BudgetService me. */
class Budgets extends Component
{
    public ?int $viewId = null;
    public $tab = 'report';     // report | edit

    public $newYear = '';
    public $newName = '';
    public $newBranch = '';
    public $newCostCenter = '';

    public array $grid = [];    // account_id => [period => amount]
    public array $annual = [];  // account_id => saal ki raqam (12 me baantne ke liye)
    public $lastYearPct = '0';

    public $fromPeriod = '';
    public $toPeriod = '';

    public function mount(): void
    {
        abort_unless(AccountingSetting::isEnabled(session('company_id')), 403, 'Accounting is not enabled for this company.');
        $this->newYear = (string) (DB::table('fiscal_years')->where('company_id', session('company_id'))
            ->where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'))->value('id') ?? '');
    }

    public function create(): void
    {
        $this->run(function () {
            $this->open(app(BudgetService::class)->create((int) session('company_id'), (int) $this->newYear, (string) $this->newName,
                $this->newBranch !== '' ? (int) $this->newBranch : null, $this->newCostCenter !== '' ? (int) $this->newCostCenter : null, session('userid')));
            $this->tab = 'edit';
            $this->newName = '';
        }, 'Budget created. Fill the amounts and Save.');
    }

    public function open(int $id): void
    {
        $svc = app(BudgetService::class);
        $budget = $svc->get((int) session('company_id'), $id);
        $this->viewId = $id;
        $this->grid = [];
        foreach ($svc->grid($budget) as $acc => $months) {
            foreach ($months as $p => $v) {
                $this->grid[$acc][$p] = (string) (float) $v;
            }
        }
        $periods = $svc->periods($budget);
        $this->fromPeriod = $periods[0];
        $current = date('Y-m');
        $this->toPeriod = in_array($current, $periods, true) ? $current : end($periods);
        $this->annual = [];
        $this->resetErrorBag();
    }

    public function back(): void
    {
        $this->viewId = null;
    }

    public function spread(int $accountId): void
    {
        $budget = app(BudgetService::class)->get((int) session('company_id'), (int) $this->viewId);
        $periods = app(BudgetService::class)->periods($budget);
        $total = (float) str_replace(',', '', (string) ($this->annual[$accountId] ?? 0));
        $each = floor($total / count($periods));
        foreach ($periods as $i => $p) {
            $this->grid[$accountId][$p] = (string) ($i === count($periods) - 1 ? $total - $each * (count($periods) - 1) : $each);
        }
    }

    public function fillFromLastYear(): void
    {
        $svc = app(BudgetService::class);
        $budget = $svc->get((int) session('company_id'), (int) $this->viewId);
        $filled = $svc->fromLastYear($budget, (float) $this->lastYearPct);
        foreach ($filled as $acc => $months) {
            foreach ($months as $p => $v) {
                $this->grid[$acc][$p] = (string) $v;
            }
        }
        session()->flash('bg_message', $filled ? 'Filled from last year\'s actuals' . ((float) $this->lastYearPct ? ' ' . ((float) $this->lastYearPct > 0 ? '+' : '') . (float) $this->lastYearPct . '%' : '') . '. Review and Save.' : 'No actuals found for last year.');
    }

    public function save(): void
    {
        $this->run(fn () => app(BudgetService::class)->save((int) session('company_id'), (int) $this->viewId, $this->grid), 'Budget saved.');
    }

    public function deleteBudget(): void
    {
        DB::table('budgets')->where('company_id', session('company_id'))->where('id', $this->viewId)->delete();
        $this->viewId = null;
        session()->flash('bg_message', 'Budget deleted.');
    }

    #[Title('Budgets')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $svc = app(BudgetService::class);
        $data = [
            'years' => DB::table('fiscal_years')->where('company_id', $companyId)->orderByDesc('start_date')->get(['id', 'start_date', 'end_date']),
            'branches' => DB::table('branch')->where('company_id', $companyId)->where('status_id', 1)->orderBy('branch_name')->get(['branch_id', 'branch_name']),
            'centers' => $this->costCenters($companyId),
        ];
        if ($this->viewId) {
            $budget = $svc->get($companyId, $this->viewId);
            $periods = $svc->periods($budget);
            $from = in_array($this->fromPeriod, $periods, true) ? $this->fromPeriod : $periods[0];
            $to = in_array($this->toPeriod, $periods, true) ? $this->toPeriod : end($periods);
            $data += [
                'budget' => $budget,
                'periods' => $periods,
                'accounts' => $svc->accounts($companyId),
                'report' => $this->tab === 'report' ? $svc->report($budget, min($from, $to), max($from, $to)) : null,
            ];
        } else {
            $data['list'] = DB::table('budgets as b')->join('fiscal_years as f', 'f.id', '=', 'b.fiscal_year_id')
                ->leftJoin('branch as br', 'br.branch_id', '=', 'b.branch_id')
                ->where('b.company_id', $companyId)->orderByDesc('f.start_date')->orderBy('b.name')
                ->get(['b.*', 'f.start_date', 'f.end_date', 'br.branch_name']);
        }
        return view('livewire.accounting.budgets', $data)->layout('layouts.master-tailwind');
    }

    private function costCenters(int $companyId)
    {
        try {
            return DB::table('cost_centers')->where('company_id', $companyId)->where('is_active', 1)->orderBy('code')->get(['id', 'code', 'name']);
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function run(callable $fn, ?string $message): void
    {
        $this->resetErrorBag();
        try {
            $fn();
        } catch (RuntimeException $e) {
            $this->addError('bg', $e->getMessage());
            return;
        }
        if ($message) {
            session()->flash('bg_message', $message);
        }
    }
}
