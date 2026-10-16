<?php

namespace App\Livewire\Purchases;

use App\Models\VendorBill;
use App\Services\Purchases\PoMatchService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use RuntimeException;

/** Phase 4.4: PO → GRN → vendor invoice (three-way match). Qaide PoMatchService me. */
class PoMatching extends Component
{
    public $status = '';
    public $search = '';

    public ?int $billId = null;
    public $vendorRef = '';
    public $billDate = '';
    public $dueDate = '';
    public $otherCharges = '';
    public $narration = '';
    public array $lines = [];   // [product_id, name, qty, unit_price, discount, tax_rate]
    public bool $accept = false;

    public function mount(): void
    {
        if ($id = (int) request()->query('bill')) {
            $this->openBill($id);
        }
    }

    private function admin(): bool
    {
        return in_array((int) session('roleId'), [1, 2], true);
    }

    public function createBill(int $poId): void
    {
        $this->attempt(function () use ($poId) {
            $bill = app(PoMatchService::class)->createDraft((int) session('company_id'), $poId, session('userid'));
            $this->openBill($bill->id);
        }, 'Draft created from what was received. Enter the vendor invoice number and check the rates.');
    }

    public function openBill(int $id): void
    {
        $bill = VendorBill::where('company_id', session('company_id'))->where('type', 'po_bill')->with('lines')->find($id);
        if (!$bill) {
            return;
        }
        $this->billId = $bill->id;
        $this->vendorRef = (string) $bill->vendor_ref;
        $this->billDate = $bill->bill_date->toDateString();
        $this->dueDate = $bill->due_date?->toDateString() ?? '';
        $this->otherCharges = (float) $bill->other_charges ? (string) (float) $bill->other_charges : '';
        $this->narration = (string) $bill->narration;
        $this->accept = false;
        // editor me PO ki har item (bill pe na ho to qty 0) — product ke hisaab se
        $onBill = $bill->lines->keyBy('product_id');
        $this->lines = [];
        foreach (app(PoMatchService::class)->match($bill)['lines'] as $pid => $m) {
            $l = $onBill[$pid] ?? null;
            if (!$l && $m['billable'] <= 0) {
                continue;
            }
            $this->lines[] = ['product_id' => (int) $pid, 'name' => $m['name'], 'qty' => $l ? (string) (float) $l->qty : '0',
                'unit_price' => (string) (float) ($l->unit_price ?? $m['rate']), 'discount' => $l && (float) $l->discount ? (string) (float) $l->discount : '',
                'tax_rate' => $l && (float) $l->tax_rate ? (string) (float) $l->tax_rate : ''];
        }
        $this->resetErrorBag();
    }

    public function back(): void
    {
        $this->billId = null;
    }

    public function save(): bool
    {
        return $this->attempt(fn () => app(PoMatchService::class)->saveDraft((int) session('company_id'), (int) $this->billId, [
            'vendor_ref' => $this->vendorRef, 'bill_date' => $this->billDate, 'due_date' => $this->dueDate,
            'other_charges' => $this->otherCharges, 'narration' => $this->narration, 'lines' => $this->lines,
        ], session('userid')), 'Saved — match checked again.');
    }

    public function post(): void
    {
        if (!$this->save()) {
            return;
        }
        if ($this->accept && !$this->admin()) {
            $this->addError('po', 'Only Admin / Sub-Admin can accept a rate difference.');
            return;
        }
        $this->attempt(function () {
            $bill = app(PoMatchService::class)->post((int) session('company_id'), (int) $this->billId, session('userid'), $this->accept);
            $this->billId = null;
            session()->flash('po_message', $bill->doc_no . ' posted.' . ((float) $bill->variance != 0 ? ' Difference of ' . number_format((float) $bill->variance, 2) . ' booked to the vendor.' : ' It matched the purchase order exactly.'));
        }, null);
    }

    public function deleteDraft(): void
    {
        $this->attempt(function () {
            app(PoMatchService::class)->cancel((int) session('company_id'), (int) $this->billId);
            $this->billId = null;
        }, 'Draft deleted.');
    }

    #[Title('PO Matching')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $svc = app(PoMatchService::class);
        $data = ['admin' => $this->admin(), 'tolerance' => PoMatchService::TOLERANCE, 'ready' => $svc->ready()];
        if (!$data['ready']) {
            $data['rows'] = [];
        } elseif ($this->billId && ($bill = VendorBill::where('company_id', $companyId)->where('type', 'po_bill')->with('lines')->find($this->billId))) {
            $data += ['bill' => $bill, 'match' => $svc->match($bill),
                'po' => DB::table('purchase_general_details')->where('purchase_id', $bill->purchase_id)->first(['purchase_id', 'po_no', 'order_date']),
                'vendor' => DB::table('vendors')->where('id', $bill->vendor_id)->value('vendor_name')];
        } else {
            $this->billId = null;
            $data['rows'] = $svc->list($companyId, $this->status, $this->search);
        }
        return view('livewire.purchases.po-matching', $data)->layout('layouts.master-tailwind');
    }

    private function attempt(callable $fn, ?string $message): bool
    {
        $this->resetErrorBag();
        try {
            $fn();
        } catch (RuntimeException $e) {
            $this->addError('po', $e->getMessage());
            return false;
        }
        if ($message) {
            session()->flash('po_message', $message);
        }
        return true;
    }
}
