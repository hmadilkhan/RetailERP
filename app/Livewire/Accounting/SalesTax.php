<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Services\Accounting\SalesTaxService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use RuntimeException;

/** Phase 4.1: sales tax ledger + mahana return. Qaide SalesTaxService me. Saari companies (accounting on na bhi ho). */
class SalesTax extends Component
{
    public $tab = 'summary';        // summary | sales | purchases | filings
    public $fromPeriod = '';
    public $toPeriod = '';
    public $branchId = '';

    public $month = '';             // registers
    public bool $missingOnly = false;

    // filing form
    public $fileAuthority = '';
    public $filePeriod = '';
    public $filePaid = '';
    public $fileRef = '';
    public $fileDate = '';
    public $fileNotes = '';
    public $fileAdjustment = '';
    public bool $filePost = true;
    public $filePaidFrom = '';

    public function mount(): void
    {
        $this->toPeriod = date('Y-m', strtotime('first day of last month'));
        $this->fromPeriod = date('Y-m', strtotime($this->toPeriod . '-01 -11 months'));
        $this->month = $this->toPeriod;
        $this->fileDate = date('Y-m-d');
        if (!in_array((int) session('roleId'), [1, 2, 17], true)) {
            $this->branchId = (string) session('branch');
        }
    }

    private function branch(): ?int
    {
        if (!in_array((int) session('roleId'), [1, 2, 17], true)) {
            return (int) session('branch');
        }
        return $this->branchId !== '' ? (int) $this->branchId : null;
    }

    private function admin(): bool
    {
        return in_array((int) session('roleId'), [1, 2], true);
    }

    public function openRegister(string $period, bool $missing = false): void
    {
        $this->month = $period;
        $this->missingOnly = $missing;
        $this->tab = 'sales';
    }

    public function startFiling(string $period, string $authority): void
    {
        $s = app(SalesTaxService::class)->summary((int) session('company_id'), $period, $period, null)[$period];
        $due = $authority === 'fbr' ? max(0, $s['fbr_tax'] - max(0, $s['input_tax'])) : $s['srb_tax'];
        $this->filePeriod = $period;
        $this->fileAuthority = $authority;
        $this->filePaid = (string) round($due, 2);
        $this->fileRef = '';
        $this->fileNotes = '';
        $this->fileAdjustment = '';
        $this->tab = 'filings';
    }

    public function file(): void
    {
        abort_unless($this->admin(), 403);
        $this->attempt(function () {
            app(SalesTaxService::class)->file((int) session('company_id'), [
                'period' => $this->filePeriod, 'authority' => $this->fileAuthority, 'paid_amount' => $this->filePaid, 'payment_ref' => $this->fileRef,
                'filed_on' => $this->fileDate, 'notes' => $this->fileNotes, 'adjustment' => $this->fileAdjustment,
                'post_entry' => $this->filePost, 'paid_from' => $this->filePaidFrom,
            ], session('userid'));
            $this->fileAuthority = '';
        }, 'Return recorded.');
    }

    public function deleteFiling(int $id): void
    {
        abort_unless($this->admin(), 403);
        $this->attempt(fn () => app(SalesTaxService::class)->deleteFiling((int) session('company_id'), $id, session('userid')), 'Filing deleted.');
    }

    public function export()
    {
        $rows = app(SalesTaxService::class)->salesRegister((int) session('company_id'), $this->month, $this->branch(), $this->missingOnly, null);
        $name = 'sales-tax-register-' . $this->month . ($this->missingOnly ? '-without-fbr' : '') . '.csv';
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Receipt', 'Branch', 'Customer', 'CNIC', 'Value excl. tax', 'FBR tax', 'SRB tax', 'Charges', 'Total', 'FBR invoice no']);
            foreach ($rows as $r) {
                fputcsv($out, [$r->date, $r->receipt_no, $r->branch_name, $r->customer, $r->nic, round($r->net - $r->fbr_tax - $r->srb_tax - $r->charges, 2),
                    $r->fbr_tax, $r->srb_tax, $r->charges, $r->net, $r->fbrInvNumber]);
            }
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv']);
    }

    #[Title('Sales Tax')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $svc = app(SalesTaxService::class);
        $from = preg_match('/^\d{4}-\d{2}$/', $this->fromPeriod) ? $this->fromPeriod : date('Y-m', strtotime('-11 months'));
        $to = preg_match('/^\d{4}-\d{2}$/', $this->toPeriod) ? $this->toPeriod : date('Y-m');
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }
        if ((int) substr($to, 0, 4) * 12 + (int) substr($to, 5) - ((int) substr($from, 0, 4) * 12 + (int) substr($from, 5)) > 23) {
            $from = date('Y-m', strtotime($to . '-01 -23 months'));
        }
        $month = preg_match('/^\d{4}-\d{2}$/', $this->month) ? $this->month : $to;
        $filingsReady = $svc->filingsReady();
        $accounting = AccountingSetting::isEnabled($companyId);

        $data = [
            'admin' => $this->admin(),
            'allBranches' => in_array((int) session('roleId'), [1, 2, 17], true),
            'branches' => DB::table('branch')->where('company_id', $companyId)->where('status_id', 1)->orderBy('branch_name')->get(['branch_id', 'branch_name']),
            'accounting' => $accounting,
            'filingsReady' => $filingsReady,
            'filings' => $filingsReady ? $svc->filings($companyId)->keyBy(fn ($f) => $f->period . ':' . $f->authority) : collect(),
            'fbrOn' => DB::table('fbr_details')->where('company_id', $companyId)->where('status', 1)->whereNull('deleted_at')->exists(),
        ];
        if ($this->tab === 'summary') {
            $data['summary'] = $svc->summary($companyId, $from, $to, $this->branch());
            $data['ledger'] = $accounting && !$this->branch() ? $svc->ledger($companyId, $from, $to) : null;
        } elseif ($this->tab === 'sales') {
            $data['register'] = $svc->salesRegister($companyId, $month, $this->branch(), $this->missingOnly);
        } elseif ($this->tab === 'purchases') {
            $data['purchases'] = $svc->purchaseRegister($companyId, $month, $this->branch());
        } elseif ($this->fileAuthority && $accounting) {
            $data['payAccounts'] = $svc->payAccounts($companyId);
        }
        return view('livewire.accounting.sales-tax', $data)->layout('layouts.master-tailwind');
    }

    private function attempt(callable $fn, string $message): void
    {
        $this->resetErrorBag();
        try {
            $fn();
        } catch (RuntimeException $e) {
            $this->addError('tax', $e->getMessage());
            return;
        }
        session()->flash('tax_message', $message);
    }
}
