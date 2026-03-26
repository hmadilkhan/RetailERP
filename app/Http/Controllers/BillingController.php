<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceAdjustment;
use App\Models\InvoiceLine;
use App\Models\InvoicePayment;
use App\Services\InvoiceGenerationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function summary()
    {
        $summary = DB::table('invoices')
            ->join('company', 'invoices.company_id', '=', 'company.company_id')
            ->select(
                'company.company_id',
                'company.name as company_name',
                DB::raw('COUNT(invoices.id) as total_invoices'),
                DB::raw('SUM(invoices.total_amount) as total_amount'),
                DB::raw('SUM(invoices.paid_amount) as paid_amount'),
                DB::raw('SUM(invoices.balance_amount) as balance_amount')
            )
            ->where('invoices.status', '!=', 'void')
            ->groupBy('company.company_id', 'company.name')
            ->orderBy('company.name')
            ->get();

        return view('Admin.Billing.summary', compact('summary'));
    }

    public function index(Request $request)
    {
        $query = Invoice::with('company')->withCount('payments');

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

    public function store(Request $request, InvoiceGenerationService $invoiceGenerationService)
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

        $exists = $invoiceGenerationService->invoiceExists($company->company_id, $periodStart, $periodEnd);

        if ($exists) {
            return back()->withInput()->withErrors(['error' => 'Invoice already exists for this period.']);
        }

        $invoiceGenerationService->generateInvoice($company, $periodStart, $periodEnd, $invoiceDate, [
            'due_date' => $data['due_date'] ?? null,
            'tax_amount' => $data['tax_amount'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'generated_by' => session('userid'),
        ]);

        return redirect()->route('billing.invoices.index')->with('success', 'Invoice generated successfully.');
    }

    public function show($id)
    {
        $invoice = Invoice::with(['company', 'lines', 'payments', 'adjustments'])->findOrFail($id);
        return view('Admin.Billing.invoices.show', compact('invoice'));
    }

    public function destroy($id)
    {
        $invoice = Invoice::withCount('payments')->findOrFail($id);

        if ($invoice->payments_count > 0) {
            return redirect()->route('billing.invoices.index')
                ->withErrors(['error' => 'Invoice cannot be deleted because payment has already been received.']);
        }

        DB::transaction(function () use ($invoice) {
            InvoiceLine::where('invoice_id', $invoice->id)->delete();
            InvoiceAdjustment::where('invoice_id', $invoice->id)->delete();
            $invoice->delete();
        });

        return redirect()->route('billing.invoices.index')->with('success', 'Invoice deleted successfully.');
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

    public function downloadPdf($id)
    {
        $invoice = Invoice::with(['company', 'lines'])->findOrFail($id);
        $pdf = \PDF::loadView('Admin.Billing.invoices.pdf', compact('invoice'));
        return $pdf->download('invoice-' . $invoice->invoice_no . '.pdf');
    }

}
