<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Models\Accounting\FiscalYear;
use App\Services\Accounting\AccountingSetupService;
use App\Services\Accounting\PeriodCloseService;
use RuntimeException;
use Livewire\Attributes\Title;
use Livewire\Component;

// Company ke fiscal years + 12 periods: agla saal banana, period close / reopen / lock, saal band (P&L → Retained Earnings).
class FiscalYears extends Component
{
    public $openYearId = null;

    public function mount(): void
    {
        abort_unless(AccountingSetting::isEnabled(session('company_id')), 403, 'Accounting is not enabled for this company.');
        $this->openYearId = $this->currentYear()->id ?? null;
    }

    public function toggleYear($id): void
    {
        $this->openYearId = (int) $this->openYearId === (int) $id ? null : $id;
    }

    public function createNextYear(AccountingSetupService $setup): void
    {
        $year = $setup->createNextFiscalYear((int) session('company_id'));
        $this->openYearId = $year->id;
        session()->flash('fy_message', $year->name . ' created with 12 periods.');
    }

    public function createPreviousYear(AccountingSetupService $setup): void
    {
        $year = $setup->createPreviousFiscalYear((int) session('company_id'));
        $this->openYearId = $year->id;
        session()->flash('fy_message', $year->name . ' created with 12 periods (for entering older data).');
    }

    /** close / reopen / lock / unlock (unlock sirf super admin) */
    public function changePeriod($periodId, string $action, PeriodCloseService $close): void
    {
        $this->attempt(function () use ($periodId, $action, $close) {
            $period = $close->changePeriod((int) session('company_id'), (int) $periodId, $action, (int) session('roleId') === 1);
            session()->flash('fy_message', $period->start_date->format('F Y') . ' is now ' . $period->status . '.');
        });
    }

    public function closeYear($yearId, PeriodCloseService $close): void
    {
        $this->attempt(function () use ($yearId, $close) {
            $entry = $close->closeYear((int) session('company_id'), (int) $yearId, session('userid'));
            session()->flash('fy_message', 'Fiscal year closed.' . ($entry ? ' Profit / loss moved to Retained Earnings in ' . $entry->entry_no . '.' : ''));
        });
    }

    public function reopenYear($yearId, PeriodCloseService $close): void
    {
        abort_unless((int) session('roleId') === 1, 403);
        $this->attempt(function () use ($yearId, $close) {
            $close->reopenYear((int) session('company_id'), (int) $yearId, session('userid'));
            session()->flash('fy_message', 'Fiscal year reopened; the closing entry was reversed.');
        });
    }

    private function attempt(callable $action): void
    {
        try {
            $action();
        } catch (RuntimeException $e) {
            $this->addError('fy', $e->getMessage());
        }
    }

    #[Title('Fiscal Years')]
    public function render()
    {
        $years = FiscalYear::where('company_id', session('company_id'))->with('periods')->orderByDesc('start_date')->get();

        return view('livewire.accounting.fiscal-years', [
            'years' => $years,
            'currentId' => $this->currentYear()->id ?? null,
            'today' => now()->toDateString(),
            'isSuperAdmin' => (int) session('roleId') === 1,
        ])->layout('layouts.master-tailwind');
    }

    private function currentYear(): ?FiscalYear
    {
        $today = now()->toDateString();
        return FiscalYear::where('company_id', session('company_id'))
            ->where('start_date', '<=', $today)->where('end_date', '>=', $today)->first();
    }
}
