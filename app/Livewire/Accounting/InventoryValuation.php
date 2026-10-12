<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Models\Accounting\ChartOfAccount;
use App\Services\Accounting\ValuationService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

// Phase 1.4: product-wise moving average cost, stock value, bina cost ki sales, GL Inventory se farq.
class InventoryValuation extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $search = '';
    #[Url(except: 'all')]
    public $filter = 'all'; // all / unvalued / negative / valued

    public function mount(): void
    {
        abort_unless(AccountingSetting::isEnabled(session('company_id')), 403, 'Accounting is not enabled for this company.');
    }

    public function updating($name): void
    {
        if (in_array($name, ['search', 'filter'])) {
            $this->resetPage();
        }
    }

    public function recalculate(ValuationService $valuation): void
    {
        $setting = AccountingSetting::where('company_id', session('company_id'))->first();
        if (!$setting || !$setting->posting_start_date) {
            $this->addError('valuation', 'Automatic posting is off — valuation runs from the posting start date.');
            return;
        }
        try {
            $valuation->refresh((int) session('company_id'), $setting->posting_start_date->toDateString(), now()->toDateString());
            session()->flash('val_message', 'Valuation recalculated. Cost of goods sold is re-posted on the next posting run.');
        } catch (Throwable $e) {
            $this->addError('valuation', $e->getMessage());
        }
    }

    #[Title('Inventory Valuation')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $setting = AccountingSetting::where('company_id', $companyId)->first();

        $base = DB::table('product_valuations as v')
            ->join('inventory_general as g', 'g.id', '=', 'v.product_id')
            ->where('v.company_id', $companyId);

        $summary = (clone $base)->selectRaw('COUNT(*) AS products,
                SUM(CASE WHEN v.qty > 0 THEN v.value ELSE 0 END) AS stock_value,
                SUM(CASE WHEN v.avg_cost <= 0 AND (v.qty <> 0 OR v.unvalued_sales > 0) THEN 1 ELSE 0 END) AS no_cost,
                SUM(CASE WHEN v.qty < 0 THEN 1 ELSE 0 END) AS negative,
                MAX(v.computed_at) AS computed_at')->first();

        $rows = (clone $base)
            ->when(trim($this->search) !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('g.product_name', 'like', '%' . trim($this->search) . '%')->orWhere('g.item_code', 'like', '%' . trim($this->search) . '%')))
            ->when($this->filter === 'unvalued', fn ($q) => $q->where('v.avg_cost', '<=', 0)->where(fn ($w) => $w->where('v.qty', '<>', 0)->orWhere('v.unvalued_sales', '>', 0)))
            ->when($this->filter === 'negative', fn ($q) => $q->where('v.qty', '<', 0))
            ->when($this->filter === 'valued', fn ($q) => $q->where('v.avg_cost', '>', 0))
            ->orderByDesc('v.unvalued_sales')->orderByDesc('v.value')
            ->select('v.*', 'g.product_name', 'g.item_code')
            ->paginate(25);

        $unvaluedLines = (int) DB::table('stock_valuation_daily')->where('company_id', $companyId)->sum('unvalued_lines');

        $inventoryId = ChartOfAccount::forCompany($companyId)->where('system_key', 'inventory')->value('id');
        $glInventory = (float) DB::table('journal_entry_lines as l')->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->where('e.company_id', $companyId)->where('e.status', 'posted')->where('l.account_id', $inventoryId)
            ->sum(DB::raw('l.debit - l.credit'));

        return view('livewire.accounting.inventory-valuation', [
            'rows' => $rows,
            'summary' => $summary,
            'unvaluedLines' => $unvaluedLines,
            'glInventory' => $glInventory,
            'setting' => $setting,
            'sources' => ['opening' => 'Opening lots', 'purchase' => 'Purchase (GRN)', 'adjustment' => 'Stock adjustment', 'price_list' => 'Price list', 'none' => '—'],
        ])->layout('layouts.master-tailwind');
    }
}
