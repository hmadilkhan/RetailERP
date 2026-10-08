<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Models\Accounting\FiscalYear;
use App\Services\Accounting\AccountingSetupService;
use Livewire\Attributes\Title;
use Livewire\Component;

// Company ke fiscal years + 12 periods. Abhi sirf dekhna aur agla saal banana — period close / lock Phase 1.5 me.
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

    #[Title('Fiscal Years')]
    public function render()
    {
        $years = FiscalYear::where('company_id', session('company_id'))->with('periods')->orderByDesc('start_date')->get();

        return view('livewire.accounting.fiscal-years', [
            'years' => $years,
            'currentId' => $this->currentYear()->id ?? null,
            'today' => now()->toDateString(),
        ])->layout('layouts.master-tailwind');
    }

    private function currentYear(): ?FiscalYear
    {
        $today = now()->toDateString();
        return FiscalYear::where('company_id', session('company_id'))
            ->where('start_date', '<=', $today)->where('end_date', '>=', $today)->first();
    }
}
