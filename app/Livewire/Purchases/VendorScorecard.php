<?php

namespace App\Livewire\Purchases;

use App\Services\Purchases\VendorScorecardService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Phase 2.6: vendor scorecard — VendorScorecardService. */
class VendorScorecard extends Component
{
    public $from = '';
    public $to = '';
    public $branchId = '';

    public function mount(): void
    {
        $this->from = date('Y-m-d', strtotime('-90 days'));
        $this->to = date('Y-m-d');
    }

    #[Title('Vendor Scorecard')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $from = strtotime($this->from) ? date('Y-m-d', strtotime($this->from)) : date('Y-m-d', strtotime('-90 days'));
        $to = strtotime($this->to) ? date('Y-m-d', strtotime($this->to)) : date('Y-m-d');
        $branch = $this->branchId !== '' && DB::table('branch')->where('branch_id', $this->branchId)->where('company_id', $companyId)->exists()
            ? (int) $this->branchId : null;

        return view('livewire.purchases.vendor-scorecard', [
            'rows' => app(VendorScorecardService::class)->build($companyId, $from, min($to, date('Y-m-d', strtotime('+1 day'))), $branch),
            'branches' => DB::table('branch')->where('company_id', $companyId)->where('status_id', 1)->orderBy('branch_name')->get(['branch_id', 'branch_name']),
        ])->layout('layouts.master-tailwind');
    }
}
