<?php

namespace App\Livewire\Purchases;

use App\Services\Purchases\RfqService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use RuntimeException;

/** Phase 3.5: RFQ / vendor quotation comparison. Qaide RfqService me. */
class Rfqs extends Component
{
    public $statusFilter = 'open';

    // new RFQ
    public bool $showForm = false;
    public $title = '';
    public $branchId = '';
    public $rfqDate = '';
    public $dueDate = '';
    public $notes = '';
    public array $lines = [];      // [product_id, name, qty, notes]
    public array $vendorIds = [];
    public $productSearch = '';

    // detail
    public ?int $viewId = null;
    public ?int $quoteFor = null;  // rfq_vendor_id
    public array $quote = [];      // delivery_days, valid_until, other_charges, notes, prices[line_id][unit_price|discount_pct|tax_rate]
    public $addVendorId = '';
    public array $awards = [];     // rfq_line_id => rfq_vendor_id

    public function mount(): void
    {
        $this->rfqDate = date('Y-m-d');
        $this->branchId = (string) session('branch');
        if ($id = (int) request()->query('rfq')) {
            $this->open($id);
        }
    }

    public function newRfq(): void
    {
        $this->reset(['title', 'dueDate', 'notes', 'lines', 'vendorIds', 'productSearch']);
        $this->rfqDate = date('Y-m-d');
        $this->showForm = true;
        $this->viewId = null;
    }

    public function addProduct(int $productId): void
    {
        $p = DB::table('inventory_general')->where('company_id', session('company_id'))->where('id', $productId)->first(['id', 'product_name']);
        if ($p && !collect($this->lines)->contains('product_id', $p->id)) {
            $this->lines[] = ['product_id' => $p->id, 'name' => $p->product_name, 'qty' => '1', 'notes' => ''];
        }
        $this->productSearch = '';
    }

    public function removeLine(int $i): void
    {
        unset($this->lines[$i]);
        $this->lines = array_values($this->lines);
    }

    /** product ke vendor_product wale vendors khud tick */
    public function suggestVendors(): void
    {
        $ids = DB::table('vendor_product as vp')->join('vendors as v', 'v.id', '=', 'vp.vendor_id')
            ->where('v.user_id', session('company_id'))->where('v.status_id', 1)->where('vp.status', 1)
            ->whereIn('vp.product_id', collect($this->lines)->pluck('product_id'))->distinct()->pluck('vp.vendor_id')->map(fn ($v) => (string) $v)->all();
        $this->vendorIds = array_values(array_unique(array_merge($this->vendorIds, $ids)));
        session()->flash('rfq_message', $ids ? count($ids) . ' vendor(s) linked to these items selected.' : 'No vendors are linked to these items.');
    }

    public function create(): void
    {
        $this->run(function () {
            $id = app(RfqService::class)->create((int) session('company_id'), [
                'branch_id' => $this->branchId, 'title' => $this->title, 'rfq_date' => $this->rfqDate, 'due_date' => $this->dueDate,
                'notes' => $this->notes, 'lines' => $this->lines, 'vendor_ids' => $this->vendorIds,
            ], session('userid'));
            $this->showForm = false;
            $this->open($id);
        }, 'RFQ created. Send the request to the vendors, then enter their quotes.');
    }

    public function open(int $id): void
    {
        $this->viewId = $id;
        $this->showForm = false;
        $this->quoteFor = null;
        $this->resetErrorBag();
        try {
            $cmp = app(RfqService::class)->comparison(app(RfqService::class)->get((int) session('company_id'), $id));
        } catch (RuntimeException $e) {
            $this->viewId = null;
            return;
        }
        $this->awards = [];
        foreach ($cmp['lines'] as $l) {
            $awarded = collect($l['quotes'])->search(fn ($q) => $q['awarded']);
            $this->awards[$l['id']] = (string) ($awarded ?: ($cmp['recommended'] && isset($l['quotes'][$cmp['recommended']]) ? $cmp['recommended'] : ($l['best_vendor'] ?? '')));
        }
    }

    public function back(): void
    {
        $this->viewId = null;
        $this->showForm = false;
    }

    public function editQuote(int $rfqVendorId): void
    {
        $rv = DB::table('rfq_vendors')->where('id', $rfqVendorId)->first();
        $this->quoteFor = $rfqVendorId;
        $this->quote = ['delivery_days' => (string) ($rv->delivery_days ?? ''), 'valid_until' => $rv->valid_until ?? '',
            'other_charges' => (float) ($rv->other_charges ?? 0) ? (string) (float) $rv->other_charges : '', 'notes' => $rv->notes ?? '', 'prices' => []];
        foreach (DB::table('rfq_quotes')->where('rfq_vendor_id', $rfqVendorId)->get() as $q) {
            $this->quote['prices'][$q->rfq_line_id] = ['unit_price' => (string) (float) $q->unit_price,
                'discount_pct' => (float) $q->discount_pct ? (string) (float) $q->discount_pct : '', 'tax_rate' => (float) $q->tax_rate ? (string) (float) $q->tax_rate : ''];
        }
    }

    public function saveQuote(): void
    {
        $this->run(function () {
            app(RfqService::class)->saveQuote((int) session('company_id'), (int) $this->quoteFor, $this->quote);
            $this->open((int) $this->viewId);
        }, 'Quote saved.');
    }

    public function decline(int $rfqVendorId): void
    {
        $this->run(function () use ($rfqVendorId) {
            app(RfqService::class)->decline((int) session('company_id'), $rfqVendorId);
            $this->open((int) $this->viewId);
        }, 'Marked as declined.');
    }

    public function addVendor(): void
    {
        $this->run(fn () => app(RfqService::class)->addVendor((int) session('company_id'), (int) $this->viewId, (int) $this->addVendorId), 'Vendor added.');
        $this->addVendorId = '';
    }

    public function removeVendor(int $rfqVendorId): void
    {
        $this->run(fn () => app(RfqService::class)->removeVendor((int) session('company_id'), $rfqVendorId), 'Vendor removed.');
        $this->open((int) $this->viewId);
    }

    public function award(): void
    {
        $this->run(function () {
            $bills = app(RfqService::class)->award((int) session('company_id'), (int) $this->viewId, $this->awards, session('userid'));
            session()->flash('rfq_message', count($bills) . ' draft vendor bill(s) created. Post each bill on Vendor Bills when the goods arrive.');
        }, null);
    }

    public function cancelRfq(): void
    {
        $this->run(fn () => app(RfqService::class)->cancel((int) session('company_id'), (int) $this->viewId), 'RFQ cancelled.');
    }

    #[Title('Request for Quotation')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $svc = app(RfqService::class);
        $vendors = DB::table('vendors')->where('user_id', $companyId)->where('status_id', 1)->orderBy('vendor_name')->get(['id', 'vendor_name']);
        $data = ['vendors' => $vendors];

        if ($this->viewId) {
            $rfq = $svc->get($companyId, $this->viewId);
            $data += ['rfq' => $rfq, 'cmp' => $svc->comparison($rfq), 'requestText' => $svc->requestText($rfq),
                'branchName' => DB::table('branch')->where('branch_id', $rfq->branch_id)->value('branch_name')];
        } elseif ($this->showForm) {
            $data['branches'] = DB::table('branch')->where('company_id', $companyId)->where('status_id', 1)->orderBy('branch_name')->get(['branch_id', 'branch_name']);
            $data['searchResults'] = collect();
            if (strlen(trim($this->productSearch)) >= 2) {
                $term = '%' . trim($this->productSearch) . '%';
                $data['searchResults'] = DB::table('inventory_general')->where('company_id', $companyId)->where('status', 1)
                    ->where(fn ($q) => $q->where('product_name', 'like', $term)->orWhere('item_code', 'like', $term))
                    ->orderBy('product_name')->limit(15)->get(['id', 'product_name', 'item_code']);
            }
        } else {
            $data['list'] = DB::table('rfqs as r')->leftJoin('branch as b', 'b.branch_id', '=', 'r.branch_id')
                ->where('r.company_id', $companyId)->when($this->statusFilter !== '', fn ($q) => $q->where('r.status', $this->statusFilter))
                ->orderByDesc('r.id')->limit(200)
                ->get(['r.*', 'b.branch_name',
                    DB::raw('(SELECT COUNT(*) FROM rfq_lines l WHERE l.rfq_id = r.id) AS items'),
                    DB::raw('(SELECT COUNT(*) FROM rfq_vendors v WHERE v.rfq_id = r.id) AS vendor_count'),
                    DB::raw("(SELECT COUNT(*) FROM rfq_vendors v WHERE v.rfq_id = r.id AND v.status = 'quoted') AS quoted")]);
        }
        return view('livewire.purchases.rfqs', $data)->layout('layouts.master-tailwind');
    }

    private function run(callable $fn, ?string $message): void
    {
        $this->resetErrorBag();
        try {
            $fn();
        } catch (RuntimeException $e) {
            $this->addError('rfq', $e->getMessage());
            return;
        }
        if ($message) {
            session()->flash('rfq_message', $message);
        }
    }
}
