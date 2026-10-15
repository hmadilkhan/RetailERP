<?php

namespace App\Livewire\Approvals;

use App\Services\Approvals\ApprovalService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use RuntimeException;

/** Phase 2.4: approvals — pending (faisla), meri requests, history, rules (Admin / Sub-Admin). Qaide ApprovalService me. */
class Approvals extends Component
{
    use WithPagination;

    public $tab = 'pending';    // pending | mine | history | rules
    public array $remarks = []; // request id => remarks

    // naya rule
    public $ruleModule = 'stock_adjustment';
    public $ruleBranch = '';
    public $ruleMinAmount = '0';
    public $ruleRole = '2';

    public function mount(): void
    {
        if (!ApprovalService::enabled()) {
            return;
        }
        app(ApprovalService::class)->syncExpenseReviews((int) session('company_id'));
        if (!$this->canDecideAny() && $this->tab === 'pending') {
            $this->tab = 'mine';
        }
    }

    public function updatingTab(): void
    {
        $this->resetPage();
        $this->resetErrorBag();
    }

    public function approve(int $id): void
    {
        $this->run(fn () => app(ApprovalService::class)->approve($id, (int) session('company_id'), (int) session('roleId'), session('userid'), session('branch'), $this->remarks[$id] ?? null), 'Approved.');
    }

    public function reject(int $id): void
    {
        $this->run(function () use ($id) {
            app(ApprovalService::class)->reject($id, (int) session('company_id'), (int) session('roleId'), session('userid'), session('branch'), $this->remarks[$id] ?? null);
            return 'Rejected.';
        }, null);
    }

    public function withdraw(int $id): void
    {
        $this->run(function () use ($id) {
            app(ApprovalService::class)->cancel($id, (int) session('company_id'), session('userid'));
            return 'Request withdrawn.';
        }, null);
    }

    public function addRule(): void
    {
        abort_unless($this->isOwner(), 403);
        $this->validate([
            'ruleModule' => 'required|in:' . implode(',', array_keys(ApprovalService::MODULES)),
            'ruleMinAmount' => 'required|numeric|min:0',
            'ruleRole' => 'required|integer',
        ], [], ['ruleModule' => 'module', 'ruleMinAmount' => 'amount', 'ruleRole' => 'approver']);
        if ($this->ruleBranch !== '' && !DB::table('branch')->where('branch_id', $this->ruleBranch)->where('company_id', session('company_id'))->exists()) {
            $this->addError('ruleBranch', 'Select a branch of this company.');
            return;
        }
        DB::table('approval_rules')->insert([
            'company_id' => (int) session('company_id'),
            'module' => $this->ruleModule,
            'branch_id' => $this->ruleBranch !== '' ? (int) $this->ruleBranch : null,
            'min_amount' => round((float) $this->ruleMinAmount, 2),
            'approver_role_id' => (int) $this->ruleRole,
            'is_active' => 1,
            'created_by' => session('userid'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->ruleMinAmount = '0';
        session()->flash('ap_message', 'Rule added. From now on matching work waits for approval.');
    }

    public function toggleRule(int $id): void
    {
        abort_unless($this->isOwner(), 403);
        DB::table('approval_rules')->where('id', $id)->where('company_id', session('company_id'))
            ->update(['is_active' => DB::raw('1 - is_active'), 'updated_at' => now()]);
    }

    public function deleteRule(int $id): void
    {
        abort_unless($this->isOwner(), 403);
        DB::table('approval_rules')->where('id', $id)->where('company_id', session('company_id'))->delete();
    }

    #[Title('Approvals')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $enabled = ApprovalService::enabled();
        $service = app(ApprovalService::class);
        $rows = null;
        $pendingCount = 0;

        if ($enabled) {
            $base = DB::table('approval_requests as a')
                ->leftJoin('user_details as u', 'u.id', '=', 'a.requested_by')
                ->leftJoin('user_details as d', 'd.id', '=', 'a.decided_by')
                ->leftJoin('branch as b', 'b.branch_id', '=', 'a.branch_id')
                ->where('a.company_id', $companyId)
                ->select('a.*', 'u.fullname as requester', 'd.fullname as decider', 'b.branch_name');

            $pending = (clone $base)->where('a.status', 'pending')->orderBy('a.created_at')->get()
                ->filter(fn ($r) => $service->canDecide($r, ...$this->who()))->values();
            $pendingCount = $pending->count();

            if ($this->tab === 'pending') {
                $page = $this->getPage();
                $rows = new \Illuminate\Pagination\LengthAwarePaginator($pending->forPage($page, 20)->values(), $pendingCount, 20, $page);
            } elseif ($this->tab === 'mine') {
                $rows = (clone $base)->where('a.requested_by', session('userid'))->orderByDesc('a.id')->paginate(20);
            } elseif ($this->tab === 'history') {
                $rows = (clone $base)->whereIn('a.status', ['approved', 'rejected', 'cancelled'])
                    ->when(!$this->isOwner(), fn ($q) => $q->where(fn ($w) => $w->where('a.decided_by', session('userid'))->orWhere('a.requested_by', session('userid'))))
                    ->orderByDesc('a.decided_at')->orderByDesc('a.id')->paginate(20);
            }
        }

        return view('livewire.approvals.approvals', [
            'enabled' => $enabled,
            'rows' => $rows,
            'pendingCount' => $pendingCount,
            'isOwner' => $this->isOwner(),
            'modules' => ApprovalService::MODULES,
            'rules' => $enabled && $this->tab === 'rules' ? DB::table('approval_rules as r')->leftJoin('branch as b', 'b.branch_id', '=', 'r.branch_id')
                ->leftJoin('user_roles as ro', 'ro.role_id', '=', 'r.approver_role_id')
                ->where('r.company_id', $companyId)->orderBy('r.module')->orderBy('r.min_amount')
                ->get(['r.*', 'b.branch_name', 'ro.role as role_name']) : collect(),
            'branches' => DB::table('branch')->where('company_id', $companyId)->where('status_id', 1)->orderBy('branch_name')->get(['branch_id', 'branch_name']),
            'roles' => DB::table('user_roles')->whereNotIn('role_id', [1, 14])->whereNull('deleted_at')->orderBy('role_id')->get(['role_id', 'role']),
        ])->layout('layouts.master-tailwind');
    }

    private function who(): array
    {
        return [(int) session('company_id'), (int) session('roleId'), session('userid'), session('branch')];
    }

    private function isOwner(): bool
    {
        return in_array((int) session('roleId'), ApprovalService::ALWAYS, true);
    }

    private function canDecideAny(): bool
    {
        if ($this->isOwner()) {
            return true;
        }
        return DB::table('approval_rules')->where('company_id', session('company_id'))->where('approver_role_id', (int) session('roleId'))->exists();
    }

    private function run(callable $fn, ?string $fallbackMessage): void
    {
        $this->resetErrorBag();
        try {
            $message = $fn();
        } catch (RuntimeException $e) {
            $this->addError('approval', $e->getMessage());
            return;
        }
        session()->flash('ap_message', is_string($message) ? $message : $fallbackMessage);
    }
}
