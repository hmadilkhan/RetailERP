<?php

namespace App\Livewire\Purchases;

use App\Models\VendorBill;
use App\Services\Approvals\ApprovalService;
use App\Services\Purchases\VendorBillService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use RuntimeException;

/**
 * Phase 2.1: Vendor Bills — purchase invoice (PO ke baghair), Debit Note, Credit Note, payment. Saare qaide VendorBillService me.
 */
class VendorBills extends Component
{
    use WithPagination;

    // list
    public $search = '';
    public $vendorFilter = '';
    public $typeFilter = '';
    public $statusFilter = '';

    // form (draft bill)
    public bool $showForm = false;
    public ?int $billId = null;
    public $branchId = '';
    public $vendorId = '';
    public $vendorRef = '';
    public $billDate = '';
    public $dueDate = '';
    public $otherCharges = '';
    public $narration = '';
    public array $lines = [];
    public $productSearch = '';

    // detail + actions
    public ?int $viewId = null;
    public $action = '';          // pay | debit | credit
    public $payAmount = '';
    public $payMode = 'cash';
    public $payBank = '';
    public $payRef = '';
    public array $returnQty = []; // bill line id => qty
    public $noteDate = '';
    public $noteAmount = '';
    public $noteNarration = '';

    public function mount(): void
    {
        if ($id = (int) request()->query('bill')) {
            $this->viewId = $id;
        }
    }

    public function updating($name): void
    {
        if (in_array($name, ['search', 'vendorFilter', 'typeFilter', 'statusFilter'], true)) {
            $this->resetPage();
        }
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->viewId = null;
        $this->branchId = (string) session('branch');
        $this->billDate = date('Y-m-d');
    }

    public function edit(int $id): void
    {
        $bill = $this->bill($id);
        if ($bill->status !== 'draft') {
            return;
        }
        $this->resetForm();
        $this->billId = $bill->id;
        $this->branchId = (string) $bill->branch_id;
        $this->vendorId = (string) $bill->vendor_id;
        $this->vendorRef = (string) $bill->vendor_ref;
        $this->billDate = $bill->bill_date->toDateString();
        $this->dueDate = $bill->due_date?->toDateString() ?? '';
        $this->otherCharges = (float) $bill->other_charges ? (string) (float) $bill->other_charges : '';
        $this->narration = (string) $bill->narration;
        $names = DB::table('inventory_general')->whereIn('id', $bill->lines->pluck('product_id'))->pluck('product_name', 'id');
        foreach ($bill->lines as $line) {
            $this->lines[] = [
                'product_id' => $line->product_id,
                'name' => $names[$line->product_id] ?? ('#' . $line->product_id),
                'qty' => (string) (float) $line->qty,
                'unit_price' => (string) (float) $line->unit_price,
                'discount' => (float) $line->discount ? (string) (float) $line->discount : '',
                'tax_rate' => (float) $line->tax_rate ? (string) (float) $line->tax_rate : '',
                'batch_no' => (string) $line->batch_no,
                'expiry_date' => $line->expiry_date ? substr((string) $line->expiry_date, 0, 10) : '',
            ];
        }
        $this->showForm = true;
        $this->viewId = null;
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function addProduct(int $productId): void
    {
        $product = DB::table('inventory_general')->where('id', $productId)->where('company_id', session('company_id'))->first(['id', 'product_name', 'item_code']);
        if (!$product) {
            return;
        }
        // pichli cost: is product ka sabse naya lot, warna price list
        $cost = DB::table('inventory_stock')->where('product_id', $productId)->where('cost_price', '>', 0)->orderByDesc('stock_id')->value('cost_price')
            ?? DB::table('inventory_price')->where('product_id', $productId)->where('status_id', 1)->value('cost_price');
        $this->lines[] = [
            'product_id' => $product->id,
            'name' => $product->product_name . ($product->item_code ? ' (' . $product->item_code . ')' : ''),
            'qty' => '1',
            'unit_price' => $cost ? (string) (float) $cost : '',
            'discount' => '',
            'tax_rate' => '',
            'batch_no' => '',
            'expiry_date' => '',
        ];
        $this->productSearch = '';
    }

    public function removeLine(int $index): void
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
    }

    public function saveDraft(): void
    {
        $this->persist(false);
    }

    public function saveAndPost(): void
    {
        $this->persist(true);
    }

    private function persist(bool $post): void
    {
        $service = app(VendorBillService::class);
        $this->validate([
            'branchId' => 'required|integer',
            'vendorId' => 'required|integer',
            'billDate' => 'required|date',
            'dueDate' => 'nullable|date|after_or_equal:billDate',
            'otherCharges' => 'nullable|numeric|min:0',
        ], [], ['branchId' => 'branch', 'vendorId' => 'vendor', 'billDate' => 'bill date', 'dueDate' => 'due date', 'otherCharges' => 'other charges']);

        try {
            $bill = $service->saveDraft((int) session('company_id'), [
                'branch_id' => $this->branchId,
                'vendor_id' => $this->vendorId,
                'vendor_ref' => $this->vendorRef,
                'bill_date' => $this->billDate,
                'due_date' => $this->dueDate,
                'other_charges' => $this->otherCharges ?: 0,
                'narration' => $this->narration,
                'lines' => $this->lines,
            ], $this->billId, session('userid'));
            $waiting = $post ? $this->approvalGate($bill) : null;
            if ($post && !$waiting) {
                $bill = $service->post((int) session('company_id'), $bill->id, session('userid'));
            }
        } catch (RuntimeException $e) {
            $this->addError('bill', $e->getMessage());
            return;
        }

        session()->flash('vb_message', $waiting ?? ($post ? $bill->doc_no . ' posted — stock added and vendor balance updated.' : $bill->doc_no . ' saved as draft.'));
        $this->cancelForm();
        $this->viewId = $bill->id;
    }

    public function post(int $id): void
    {
        $service = app(VendorBillService::class);
        if ($waiting = $this->approvalGate($this->bill($id))) {
            session()->flash('vb_message', $waiting);
            return;
        }
        $this->run(fn () => $service->post((int) session('company_id'), $id, session('userid')), 'Bill posted — stock added and vendor balance updated.');
    }

    /** Phase 2.4: approval rule lage to post nahi — request (ek hi) aur message; null = post kar sakte ho */
    private function approvalGate(VendorBill $bill): ?string
    {
        $approval = app(ApprovalService::class);
        $role = $approval->approverFor((int) session('company_id'), 'vendor_bill', (int) $bill->branch_id, (float) $bill->total, (int) session('roleId'));
        if (!$role) {
            return null;
        }
        if (!$approval->pendingFor('vendor_bill', $bill->id)) {
            $vendor = DB::table('vendors')->where('id', $bill->vendor_id)->value('vendor_name');
            $approval->request((int) session('company_id'), 'vendor_bill', (int) $bill->branch_id, (float) $bill->total, $role, $bill->id,
                'Vendor Bill ' . $bill->doc_no . ' — ' . $vendor . ($bill->vendor_ref ? ' (Bill# ' . $bill->vendor_ref . ')' : ''), null, session('userid'));
        }
        return $bill->doc_no . ' sent for approval. It will be posted (stock and vendor balance) once approved.';
    }

    public function cancelBill(int $id): void
    {
        $service = app(VendorBillService::class);
        $bill = $this->bill($id);
        $wasDraft = $bill->status === 'draft';
        if ($this->run(fn () => $service->cancel((int) session('company_id'), $id, session('userid')), $wasDraft ? 'Draft deleted.' : 'Bill cancelled — stock removed and vendor balance reversed.') && $wasDraft) {
            $this->viewId = null;
        }
    }

    public function show(int $id): void
    {
        $this->viewId = $id;
        $this->showForm = false;
        $this->closeAction();
    }

    public function back(): void
    {
        $this->viewId = null;
        $this->closeAction();
    }

    public function openAction(string $action): void
    {
        $this->closeAction();
        $this->action = $action;
        $this->noteDate = date('Y-m-d');
        if ($action === 'pay' && $this->viewId) {
            $this->payAmount = (string) round($this->bill($this->viewId)->open_amount);
        }
    }

    public function closeAction(): void
    {
        $this->resetErrorBag();
        $this->action = '';
        $this->payAmount = $this->payBank = $this->payRef = $this->noteAmount = $this->noteNarration = '';
        $this->payMode = 'cash';
        $this->returnQty = [];
    }

    public function pay(): void
    {
        $service = app(VendorBillService::class);
        $this->validate(['payAmount' => 'required|numeric|min:1', 'payMode' => 'required|in:cash,bank'], [], ['payAmount' => 'amount']);
        $this->run(fn () => $service->pay((int) session('company_id'), (int) $this->viewId, (float) $this->payAmount, $this->payMode,
            $this->payBank ? (int) $this->payBank : null, $this->payRef, session('branch')), 'Payment saved.');
    }

    public function saveDebitNote(): void
    {
        $service = app(VendorBillService::class);
        $this->validate(['noteDate' => 'required|date'], [], ['noteDate' => 'date']);
        $this->run(fn () => $service->debitNote((int) session('company_id'), (int) $this->viewId, $this->returnQty, $this->noteDate,
            $this->noteNarration, session('userid')), 'Debit note posted — stock returned and vendor balance reduced.');
    }

    public function saveCreditNote(): void
    {
        $service = app(VendorBillService::class);
        $this->validate(['noteDate' => 'required|date', 'noteAmount' => 'required|numeric|min:0.01'], [], ['noteDate' => 'date', 'noteAmount' => 'amount']);
        $this->run(fn () => $service->creditNote((int) session('company_id'), (int) $this->viewId, (float) $this->noteAmount, $this->noteDate,
            $this->noteNarration, session('userid')), 'Credit note posted — vendor balance reduced.');
    }

    #[Title('Vendor Bills')]
    public function render()
    {
        $service = app(VendorBillService::class);
        $companyId = (int) session('company_id');

        $bills = VendorBill::where('vendor_bills.company_id', $companyId)
            ->join('vendors', 'vendors.id', '=', 'vendor_bills.vendor_id')
            ->when($this->vendorFilter, fn ($q) => $q->where('vendor_bills.vendor_id', $this->vendorFilter))
            ->when($this->typeFilter, fn ($q) => $q->where('vendor_bills.type', $this->typeFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('vendor_bills.status', $this->statusFilter))
            ->when(trim($this->search) !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('vendor_bills.doc_no', 'like', '%' . trim($this->search) . '%')
                ->orWhere('vendor_bills.vendor_ref', 'like', '%' . trim($this->search) . '%')
                ->orWhere('vendors.vendor_name', 'like', '%' . trim($this->search) . '%')))
            ->orderByDesc('vendor_bills.bill_date')->orderByDesc('vendor_bills.id')
            ->select('vendor_bills.*', 'vendors.vendor_name')
            ->paginate(20);

        $viewing = $this->viewId ? VendorBill::where('company_id', $companyId)->with(['lines', 'notes', 'againstBill'])->find($this->viewId) : null;
        $productNames = $viewing ? DB::table('inventory_general')->whereIn('id', $viewing->lines->pluck('product_id')->filter())->pluck('product_name', 'id') : collect();

        $searchResults = [];
        if ($this->showForm && strlen(trim($this->productSearch)) >= 2) {
            $term = '%' . trim($this->productSearch) . '%';
            $searchResults = DB::table('inventory_general')->where('company_id', $companyId)->where('status', 1)
                ->where(fn ($q) => $q->where('product_name', 'like', $term)->orWhere('item_code', 'like', $term))
                ->orderBy('product_name')->limit(15)->get(['id', 'product_name', 'item_code']);
        }

        $formLines = [];
        if ($this->showForm) {
            try {
                $formLines = $service->buildLines($companyId, $this->lines, (float) ($this->otherCharges ?: 0));
            } catch (RuntimeException $e) {
                $formLines = [];
            }
        }

        return view('livewire.purchases.vendor-bills', [
            'bills' => $bills,
            'viewing' => $viewing,
            'waitingApproval' => $viewing && $viewing->status === 'draft' && ApprovalService::enabled()
                && app(ApprovalService::class)->pendingFor('vendor_bill', $viewing->id),
            'productNames' => $productNames,
            'returned' => $viewing && $viewing->type === 'bill' ? $service->returnedQty($viewing->id) : [],
            'payments' => $viewing && $viewing->type === 'bill' ? $this->payments($viewing) : collect(),
            'vendors' => DB::table('vendors')->where('user_id', $companyId)->where('status_id', 1)->orderBy('vendor_name')->get(['id', 'vendor_name']),
            'branches' => DB::table('branch')->where('company_id', $companyId)->where('status_id', 1)->orderBy('branch_name')->get(['branch_id', 'branch_name']),
            'branchNames' => DB::table('branch')->where('company_id', $companyId)->pluck('branch_name', 'branch_id'),
            'banks' => DB::table('bank_account_generaldetails')->whereIn('branch_id_company', $service->branchIds($companyId))->orderBy('account_title')->get(['bank_account_id', 'account_title', 'account_no']),
            'taxes' => DB::table('taxes')->where('company_id', $companyId)->where('status_id', 1)->where('show_in_purchase', 1)->get(['id', 'name', 'value']),
            'searchResults' => $searchResults,
            'formTotals' => $this->showForm ? $service->totals($formLines, (float) ($this->otherCharges ?: 0)) : null,
        ])->layout('layouts.master-tailwind');
    }

    /** bill ki payments — vendor_payment narration me doc_no */
    private function payments(VendorBill $bill)
    {
        return DB::table('vendor_payment as p')
            ->join('vendor_payment_details as d', 'd.payment_id', '=', 'p.payment_id')
            ->join('vendor_ledger as l', 'l.vendor_account_id', '=', 'd.account_id')
            ->where('l.vendor_id', $bill->vendor_id)->where('p.narration', 'like', '%— ' . $bill->doc_no . '%')
            ->orderBy('p.payment_id')
            ->get(['p.payment_id', 'p.bankid', 'p.cheque', 'p.payment', 'l.created_at']);
    }

    private function run(callable $fn, string $message): bool
    {
        try {
            $fn();
        } catch (RuntimeException $e) {
            $this->addError('bill', $e->getMessage());
            return false;
        }
        session()->flash('vb_message', $message);
        $this->closeAction();
        return true;
    }

    private function bill(int $id): VendorBill
    {
        return VendorBill::where('company_id', session('company_id'))->with('lines')->findOrFail($id);
    }

    private function resetForm(): void
    {
        $this->resetErrorBag();
        $this->billId = null;
        $this->branchId = $this->vendorId = $this->vendorRef = $this->billDate = $this->dueDate = $this->otherCharges = $this->narration = $this->productSearch = '';
        $this->lines = [];
    }
}
