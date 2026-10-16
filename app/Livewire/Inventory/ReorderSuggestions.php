<?php

namespace App\Livewire\Inventory;

use App\Services\Inventory\DemandForecastService;
use App\Services\Inventory\ExpiryService;
use App\Services\Inventory\ReplenishmentService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use RuntimeException;

/** Phase 2.3: branch ko kya mangwana hai + reorder levels + Pending Demand. Qaide ReplenishmentService me. */
class ReorderSuggestions extends Component
{
    use WithPagination;

    public $branchId = '';
    public $method = 'forecast'; // forecast (Phase 4.3) | average
    public $salesDays = 30;
    public $coverDays = 7;
    public $departmentId = '';
    public $show = 'needed';   // needed | all
    public $search = '';

    public array $selected = []; // product_id => true
    public array $orderQty = []; // product_id => qty (suggest ko badalna ho)

    public ?int $editId = null;
    public $editMin = '';
    public $editMax = '';
    public $editReorder = '';
    public $editLead = '';

    public function mount(): void
    {
        $allowed = ExpiryService::branchIds((int) session('company_id'));
        $this->branchId = (string) (in_array((int) session('branch'), $allowed, true) ? session('branch') : ($allowed[0] ?? ''));
    }

    public function updating($name): void
    {
        if (in_array($name, ['branchId', 'method', 'salesDays', 'coverDays', 'departmentId', 'show', 'search'], true)) {
            $this->resetPage();
            if ($name === 'branchId') {
                $this->selected = $this->orderQty = [];
                $this->editId = null;
            }
        }
    }

    public function selectAllSuggested(): void
    {
        foreach ($this->filtered() as $row) {
            if ($row['suggest'] > 0) {
                $this->selected[$row['product_id']] = true;
            }
        }
    }

    public function clearSelection(): void
    {
        $this->selected = $this->orderQty = [];
    }

    public function editLevel(int $productId): void
    {
        $row = $this->rows()[$productId] ?? null;
        if (!$row) {
            return;
        }
        $this->resetErrorBag();
        $this->editId = $productId;
        $level = DB::table('reorder_levels')->where('branch_id', $this->branchId)->where('product_id', $productId)->first();
        $this->editMin = $level && $level->min_qty !== null ? (string) (float) $level->min_qty : '';
        $this->editMax = $level && $level->max_qty !== null ? (string) (float) $level->max_qty : '';
        $this->editReorder = $level && $level->reorder_qty !== null ? (string) (float) $level->reorder_qty : '';
        $this->editLead = $level && $level->lead_time_days !== null ? (string) $level->lead_time_days : '';
    }

    public function saveLevel(): void
    {
        $this->run(function () {
            app(ReplenishmentService::class)->saveLevel((int) session('company_id'), (int) $this->branchId, (int) $this->editId,
                $this->editMin, $this->editMax, $this->editReorder, $this->editLead, session('userid'));
            $this->editId = null;
        }, 'Reorder level saved.');
    }

    public function fillLevels(): void
    {
        $ids = array_keys(array_filter($this->selected));
        if (!$ids) {
            $this->addError('reorder', 'Tick the products first, then fill their levels from sales.');
            return;
        }
        $this->run(function () use ($ids) {
            $n = app(ReplenishmentService::class)->fillFromSales((int) session('company_id'), (int) $this->branchId, $ids,
                (int) $this->salesDays, (int) $this->coverDays, session('userid'), $this->rates());
            session()->flash('rs_message', $n . ' product level(s) set from sales. Products without sales were skipped.');
        }, null);
    }

    public function createDemand(): void
    {
        $rows = $this->rows();
        $items = [];
        foreach (array_keys(array_filter($this->selected)) as $id) {
            $qty = isset($this->orderQty[$id]) && $this->orderQty[$id] !== '' ? (float) $this->orderQty[$id] : (float) ($rows[$id]['suggest'] ?? 0);
            if ($qty > 0) {
                $items[(int) $id] = $qty;
            }
        }
        $this->run(function () use ($items) {
            $ids = app(ReplenishmentService::class)->createDemand((int) session('company_id'), (int) $this->branchId, $items, session('userid'));
            $this->selected = $this->orderQty = [];
            session()->flash('rs_message', count($ids) . ' pending demand(s) created with ' . count($items) . ' product(s). Head office will see them in Received Demand.');
        }, null);
    }

    #[Title('Reorder Suggestions')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $allowed = ExpiryService::branchIds($companyId);
        $filtered = $this->filtered();
        $page = $this->getPage();
        $paginator = new LengthAwarePaginator(array_slice($filtered, ($page - 1) * 25, 25), count($filtered), 25, $page);

        $all = $this->rows();
        $needed = array_filter($all, fn ($r) => $r['suggest'] > 0);

        return view('livewire.inventory.reorder-suggestions', [
            'rows' => $paginator,
            'accuracy' => $this->method === 'forecast' && $this->rows() ? app(DemandForecastService::class)->forecast((int) $this->branchId)['accuracy'] : null,
            'summary' => ['products' => count($all), 'needed' => count($needed), 'out' => count(array_filter($all, fn ($r) => $r['on_hand'] <= 0 && $r['daily'] > 0))],
            'selectedCount' => count(array_filter($this->selected)),
            'branches' => DB::table('branch')->whereIn('branch_id', $allowed ?: [0])->where('status_id', 1)->orderBy('branch_name')->get(['branch_id', 'branch_name']),
            'branchNames' => DB::table('branch')->where('company_id', $companyId)->pluck('branch_name', 'branch_id'),
            'departments' => DB::table('inventory_department')->where('company_id', $companyId)->where('status', 1)->orderBy('department_name')->get(['department_id', 'department_name']),
        ])->layout('layouts.master-tailwind');
    }

    private ?array $rowsCache = null;

    private function rows(): array
    {
        if ($this->rowsCache !== null) {
            return $this->rowsCache;
        }
        $allowed = ExpiryService::branchIds((int) session('company_id'));
        if (!in_array((int) $this->branchId, $allowed, true)) {
            return $this->rowsCache = [];
        }
        return $this->rowsCache = app(ReplenishmentService::class)->suggestions((int) session('company_id'), (int) $this->branchId,
            (int) $this->salesDays, (int) $this->coverDays, $this->departmentId !== '' ? (int) $this->departmentId : null, $this->rates());
    }

    /** forecast ke rates (product => daily, trend) ya null = simple average */
    private function rates(): ?array
    {
        return $this->method === 'forecast' ? app(DemandForecastService::class)->forecast((int) $this->branchId)['rates'] : null;
    }

    private function filtered(): array
    {
        $term = mb_strtolower(trim((string) $this->search));
        $rows = array_filter($this->rows(), function ($r) use ($term) {
            if ($this->show === 'needed' && $r['suggest'] <= 0) {
                return false;
            }
            return $term === '' || str_contains(mb_strtolower($r['name'] . ' ' . $r['code']), $term);
        });
        // sab se pehle jo khatam / khatam hone wale hain
        usort($rows, fn ($a, $b) => [$a['days_left'] ?? INF, $a['name']] <=> [$b['days_left'] ?? INF, $b['name']]);
        return $rows;
    }

    private function run(callable $fn, ?string $message): void
    {
        $this->resetErrorBag();
        try {
            $fn();
        } catch (RuntimeException $e) {
            $this->addError('reorder', $e->getMessage());
            return;
        }
        $this->rowsCache = null;
        if ($message) {
            session()->flash('rs_message', $message);
        }
    }
}
