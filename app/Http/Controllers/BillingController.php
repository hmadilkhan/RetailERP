<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyBillingRate;
use App\Models\Invoice;
use App\Models\InvoiceAdjustment;
use App\Models\InvoiceLine;
use App\Models\InvoicePayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('company');

        if (!empty($request->company_id)) {
            $query->where('company_id', $request->company_id);
        }

        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        if (!empty($request->month)) {
            $month = Carbon::parse($request->month . '-01');
            $query->whereDate('period_start', '>=', $month->copy()->startOfMonth()->toDateString())
                ->whereDate('period_end', '<=', $month->copy()->endOfMonth()->toDateString());
        }

        $invoices = $query->orderByDesc('id')->paginate(20);
        $companies = Company::select('company_id', 'name')->orderBy('name')->get();

        return view('Admin.Billing.invoices.index', compact('invoices', 'companies'));
    }

    public function create()
    {
        $companies = Company::select('company_id', 'name', 'invoice_type', 'payment_due_days', 'invoice_prefix', 'monthly_charges_amount')
            ->where('status_id', 1)
            ->orderBy('name')
            ->get();

        return view('Admin.Billing.invoices.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:company,company_id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'tax_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $company = Company::findOrFail($data['company_id']);
        $periodStart = Carbon::parse($data['period_start'])->toDateString();
        $periodEnd = Carbon::parse($data['period_end'])->toDateString();
        $invoiceDate = Carbon::parse($data['invoice_date']);
        $dueDate = !empty($data['due_date'])
            ? Carbon::parse($data['due_date'])->toDateString()
            : $invoiceDate->copy()->addDays((int) ($company->payment_due_days ?? 15))->toDateString();

        $exists = Invoice::where('company_id', $company->company_id)
            ->whereDate('period_start', $periodStart)
            ->whereDate('period_end', $periodEnd)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['error' => 'Invoice already exists for this period.']);
        }

        DB::transaction(function () use ($company, $periodStart, $periodEnd, $invoiceDate, $dueDate, $data) {
            $lines = $this->buildInvoiceLines($company, $periodStart, $periodEnd);
            $subtotal = collect($lines)->sum('line_amount');

            $previousDue = (float) Invoice::where('company_id', $company->company_id)
                ->whereNotIn('status', ['paid', 'void'])
                ->sum('balance_amount');

            $taxAmount = (float) ($data['tax_amount'] ?? 0);
            $totalAmount = $subtotal + $taxAmount + $previousDue;

            $invoice = Invoice::create([
                'company_id' => $company->company_id,
                'invoice_no' => $this->generateInvoiceNo($company, $invoiceDate),
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'invoice_date' => $invoiceDate->toDateString(),
                'due_date' => $dueDate,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'previous_due' => $previousDue,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'balance_amount' => $totalAmount,
                'status' => 'issued',
                'generated_by' => session('userid'),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $line['invoice_id'] = $invoice->id;
                InvoiceLine::create($line);
            }
        });

        return redirect()->route('billing.invoices.index')->with('success', 'Invoice generated successfully.');
    }

    public function show($id)
    {
        $invoice = Invoice::with(['company', 'lines', 'payments', 'adjustments'])->findOrFail($id);
        return view('Admin.Billing.invoices.show', compact('invoice'));
    }

    public function addPayment(Request $request, $invoiceId)
    {
        $data = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode_id' => 'nullable|integer',
            'reference_no' => 'nullable|string|max:100',
            'narration' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($invoiceId, $data) {
            $invoice = Invoice::lockForUpdate()->findOrFail($invoiceId);

            InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'company_id' => $invoice->company_id,
                'payment_date' => $data['payment_date'],
                'payment_mode_id' => $data['payment_mode_id'] ?? null,
                'amount' => $data['amount'],
                'reference_no' => $data['reference_no'] ?? null,
                'narration' => $data['narration'] ?? null,
                'received_by' => session('userid'),
            ]);

            $invoice->paid_amount = (float) $invoice->paid_amount + (float) $data['amount'];
            $invoice->balance_amount = max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount);
            $invoice->status = $invoice->balance_amount <= 0 ? 'paid' : 'partial';
            $invoice->save();
        });

        return redirect()->route('billing.invoices.show', $invoiceId)->with('success', 'Payment received successfully.');
    }

    public function addAdjustment(Request $request, $invoiceId)
    {
        $data = $request->validate([
            'type' => 'required|in:debit,credit',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:255',
            'adjustment_date' => 'required|date',
        ]);

        DB::transaction(function () use ($invoiceId, $data) {
            $invoice = Invoice::lockForUpdate()->findOrFail($invoiceId);

            $adjustment = InvoiceAdjustment::create([
                'company_id' => $invoice->company_id,
                'invoice_id' => $invoice->id,
                'type' => $data['type'],
                'amount' => $data['amount'],
                'reason' => $data['reason'],
                'adjustment_date' => $data['adjustment_date'],
                'created_by' => session('userid'),
            ]);

            $signedAmount = $adjustment->type === 'debit' ? (float) $adjustment->amount : -(float) $adjustment->amount;
            $invoice->total_amount = (float) $invoice->total_amount + $signedAmount;
            $invoice->balance_amount = max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount);
            $invoice->status = $invoice->balance_amount <= 0 ? 'paid' : ($invoice->paid_amount > 0 ? 'partial' : 'issued');
            $invoice->save();

            InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'scope_type' => 'adjustment',
                'scope_id' => null,
                'description' => 'Adjustment: ' . $adjustment->reason . ' (' . strtoupper($adjustment->type) . ')',
                'qty' => 1,
                'unit_price' => $signedAmount,
                'line_amount' => $signedAmount,
                'meta' => json_encode(['adjustment_id' => $adjustment->id]),
            ]);
        });

        return redirect()->route('billing.invoices.show', $invoiceId)->with('success', 'Adjustment added successfully.');
    }

    private function buildInvoiceLines(Company $company, string $from, string $to): array
    {
        $effectiveDate = $to;
        $rates = CompanyBillingRate::where('company_id', $company->company_id)
            ->where('is_active', 1)
            ->whereDate('effective_from', '<=', $effectiveDate)
            ->where(function ($q) use ($effectiveDate) {
                $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', $effectiveDate);
            })->get();

        $lines = [];

        $companyRates = $rates->where('scope_type', 'company');
        foreach ($companyRates as $rate) {
            $amount = $this->calculateAmount($rate, $from, $to, null, null);
            $lines[] = [
                'scope_type' => 'adjustment',
                'scope_id' => null,
                'description' => 'Company Monthly Billing',
                'qty' => (float) $amount['qty'],
                'unit_price' => (float) $amount['unit_price'],
                'line_amount' => (float) $amount['line_amount'],
                'meta' => json_encode(['charge_type' => $rate->charge_type]),
            ];
        }

        if ($company->invoice_type === 'branch') {
            $branchIds = DB::table('branch')->where('company_id', $company->company_id)->pluck('branch_id');
            foreach ($branchIds as $branchId) {
                $rate = $rates->first(function ($r) use ($branchId) {
                    return $r->scope_type === 'branch' && (int) $r->scope_id === (int) $branchId;
                });

                if (!$rate) {
                    continue;
                }

                $branch = DB::table('branch')->where('branch_id', $branchId)->first();
                $amount = $this->calculateAmount($rate, $from, $to, (int) $branchId, null);
                $lines[] = [
                    'scope_type' => 'branch',
                    'scope_id' => (int) $branchId,
                    'description' => 'Branch: ' . ($branch->branch_name ?? ('#' . $branchId)),
                    'qty' => (float) $amount['qty'],
                    'unit_price' => (float) $amount['unit_price'],
                    'line_amount' => (float) $amount['line_amount'],
                    'meta' => json_encode(['charge_type' => $rate->charge_type]),
                ];
            }
        } else {
            $terminalRows = DB::table('terminal_details')
                ->join('branch', 'branch.branch_id', '=', 'terminal_details.branch_id')
                ->where('branch.company_id', $company->company_id)
                ->select('terminal_details.terminal_id', 'terminal_details.terminal_name')
                ->get();

            foreach ($terminalRows as $terminalRow) {
                $rate = $rates->first(function ($r) use ($terminalRow) {
                    return $r->scope_type === 'terminal' && (int) $r->scope_id === (int) $terminalRow->terminal_id;
                });

                if (!$rate) {
                    continue;
                }

                $amount = $this->calculateAmount($rate, $from, $to, null, (int) $terminalRow->terminal_id);
                $lines[] = [
                    'scope_type' => 'terminal',
                    'scope_id' => (int) $terminalRow->terminal_id,
                    'description' => 'Terminal: ' . ($terminalRow->terminal_name ?? ('#' . $terminalRow->terminal_id)),
                    'qty' => (float) $amount['qty'],
                    'unit_price' => (float) $amount['unit_price'],
                    'line_amount' => (float) $amount['line_amount'],
                    'meta' => json_encode(['charge_type' => $rate->charge_type]),
                ];
            }
        }

        if (empty($lines) && (float) $company->monthly_charges_amount > 0) {
            $lines[] = [
                'scope_type' => 'adjustment',
                'scope_id' => null,
                'description' => 'Company Monthly Billing',
                'qty' => 1,
                'unit_price' => (float) $company->monthly_charges_amount,
                'line_amount' => (float) $company->monthly_charges_amount,
                'meta' => json_encode(['source' => 'monthly_charges_amount']),
            ];
        }

        return $lines;
    }

    private function calculateAmount(CompanyBillingRate $rate, string $from, string $to, ?int $branchId, ?int $terminalId): array
    {
        if ($rate->charge_type === 'flat_monthly') {
            return [
                'qty' => 1,
                'unit_price' => (float) $rate->rate,
                'line_amount' => (float) $rate->rate,
            ];
        }

        $query = DB::table('sales_receipts')
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->where('void_receipt', 0);

        if (!empty($branchId)) {
            $query->where('branch', $branchId);
        }
        if (!empty($terminalId)) {
            $query->where('terminal_id', $terminalId);
        }

        if ($rate->charge_type === 'per_order') {
            $qty = (int) $query->count();
            return [
                'qty' => $qty,
                'unit_price' => (float) $rate->rate,
                'line_amount' => $qty * (float) $rate->rate,
            ];
        }

        $sum = (float) $query->sum('total_amount');
        return [
            'qty' => $sum,
            'unit_price' => (float) $rate->rate,
            'line_amount' => $sum * (float) $rate->rate,
        ];
    }

    private function generateInvoiceNo(Company $company, Carbon $invoiceDate): string
    {
        $prefix = $company->invoice_prefix ?: ('CMP' . $company->company_id);
        $base = $prefix . '-' . $invoiceDate->format('Ym');
        $count = Invoice::where('invoice_no', 'like', $base . '%')->count() + 1;
        return $base . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}

