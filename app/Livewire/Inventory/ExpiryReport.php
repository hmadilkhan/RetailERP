<?php

namespace App\Livewire\Inventory;

use App\Services\Inventory\ExpiryService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/** Phase 2.2: expire ho chuke / hone wale lots (batch, expiry, bacha maal, value). */
class ExpiryReport extends Component
{
    use WithPagination;

    public $status = 'expiring';   // expiring | expired | all
    public $days = 30;
    public $branchId = '';
    public $search = '';

    public function updating($name): void
    {
        $this->resetPage();
    }

    #[Title('Expiry Report')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $enabled = ExpiryService::enabled();
        $allowed = ExpiryService::branchIds($companyId);
        $branchIds = $this->branchId !== '' && in_array((int) $this->branchId, $allowed, true) ? [(int) $this->branchId] : $allowed;
        $days = in_array((int) $this->days, [7, 15, 30, 60, 90, 180], true) ? (int) $this->days : 30;

        $lots = $enabled
            ? ExpiryService::lots($branchIds, $this->status, $days, $this->search)
                ->orderBy('s.expiry_date')->orderBy('g.product_name')
                ->select('s.stock_id', 's.branch_id', 's.batch_no', 's.expiry_date', 's.balance', 's.cost_price', 'g.product_name', 'g.item_code')
                ->paginate(25)
            : null;

        return view('livewire.inventory.expiry-report', [
            'enabled' => $enabled,
            'lots' => $lots,
            'summary' => $enabled ? ExpiryService::summary($branchIds, $days) : null,
            'days' => $days,
            'branches' => DB::table('branch')->whereIn('branch_id', $allowed ?: [0])->orderBy('branch_name')->get(['branch_id', 'branch_name']),
            'branchNames' => DB::table('branch')->whereIn('branch_id', $allowed ?: [0])->pluck('branch_name', 'branch_id'),
        ])->layout('layouts.master-tailwind');
    }
}
