<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\InvoiceSetup as InvoiceSetupModel;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Terminal;

class InvoiceSetup extends Component
{
    public $invoiceSetupId;
    public $company_id;
    public $invoice_type = 'branch';
    public $monthly_charges_amount = 0;
    public $billing_cycle_day = 1;
    public $payment_due_days = 15;
    public $invoice_prefix;
    public $is_auto_invoice = true;
    public $billing_rates = [];
    public $companies;
    public $branches = [];
    public $terminals = [];
    public $isEdit = false;

    protected $rules = [
        'company_id' => 'required|exists:company,company_id',
        'invoice_type' => 'required|in:branch,terminal',
        'monthly_charges_amount' => 'required|numeric|min:0',
        'billing_cycle_day' => 'required|integer|min:1|max:28',
        'payment_due_days' => 'required|integer|min:1|max:90',
        'invoice_prefix' => 'nullable|string|max:30',
        'is_auto_invoice' => 'boolean',
        'billing_rates.*.scope_type' => 'required|in:company,branch,terminal',
        'billing_rates.*.scope_id' => 'nullable',
        'billing_rates.*.charge_type' => 'required|string',
        'billing_rates.*.rate' => 'required|numeric|min:0',
        'billing_rates.*.effective_from' => 'required|date',
        'billing_rates.*.effective_to' => 'nullable|date|after:effective_from',
        'billing_rates.*.is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        $this->companies = Company::all();
        
        if ($id) {
            $this->isEdit = true;
            $this->invoiceSetupId = $id;
            $this->loadInvoiceSetup($id);
        }
    }

    public function loadInvoiceSetup($id)
    {
        $setup = InvoiceSetupModel::with('billingRates')->findOrFail($id);
        $this->company_id = $setup->company_id;
        $this->invoice_type = $setup->invoice_type;
        $this->monthly_charges_amount = $setup->monthly_charges_amount;
        $this->billing_cycle_day = $setup->billing_cycle_day;
        $this->payment_due_days = $setup->payment_due_days;
        $this->invoice_prefix = $setup->invoice_prefix;
        $this->is_auto_invoice = $setup->is_auto_invoice;
        $this->billing_rates = $setup->billingRates->toArray();
        $this->loadBranchesAndTerminals();
    }

    public function updatedCompanyId()
    {
        $this->loadBranchesAndTerminals();
    }

    public function loadBranchesAndTerminals()
    {
        if ($this->company_id) {
            $this->branches = Branch::where('company_id', $this->company_id)->get();
            $this->terminals = Terminal::where('company_id', $this->company_id)->get();
        }
    }

    public function addBillingRate()
    {
        $this->billing_rates[] = [
            'scope_type' => 'company',
            'scope_id' => null,
            'charge_type' => '',
            'rate' => 0,
            'effective_from' => now()->format('Y-m-d'),
            'effective_to' => null,
            'is_active' => true,
        ];
    }

    public function removeBillingRate($index)
    {
        unset($this->billing_rates[$index]);
        $this->billing_rates = array_values($this->billing_rates);
    }

    public function save()
    {
        $this->validate();

        $data = [
            'company_id' => $this->company_id,
            'invoice_type' => $this->invoice_type,
            'monthly_charges_amount' => $this->monthly_charges_amount,
            'billing_cycle_day' => $this->billing_cycle_day,
            'payment_due_days' => $this->payment_due_days,
            'invoice_prefix' => $this->invoice_prefix,
            'is_auto_invoice' => $this->is_auto_invoice,
        ];

        if ($this->isEdit) {
            $invoiceSetup = InvoiceSetupModel::findOrFail($this->invoiceSetupId);
            $invoiceSetup->update($data);
            $invoiceSetup->billingRates()->delete();
        } else {
            $invoiceSetup = InvoiceSetupModel::create($data);
        }

        foreach ($this->billing_rates as $rate) {
            $invoiceSetup->billingRates()->create($rate);
        }

        session()->flash('success', $this->isEdit ? 'Invoice setup updated successfully!' : 'Invoice setup created successfully!');
        return redirect()->route('invoice-setup.index');
    }

    public function render()
    {
        return view('livewire.invoice-setup-form');
    }
}
