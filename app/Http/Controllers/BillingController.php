<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyBillingRate;
use App\Models\Invoice;
use App\Models\InvoiceAdjustment;
use App\Models\InvoiceLine;
use App\Models\InvoicePayment;
use App\Models\InvoiceSetup;
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

    private function buildInvoiceLines($company, $periodStart, $periodEnd)
    {
        $lines = [];
        $billingMonths = $this->getBillingPeriodMonths($periodStart, $periodEnd);
        $billingPeriodLabel = $this->formatBillingPeriodLabel($periodStart, $periodEnd);

        $setup = InvoiceSetup::with(['billingRates' => function ($query) use ($periodStart, $periodEnd) {
            $query->where('is_active', 1);
            // ->where('effective_from', '<=', $periodEnd)
            // ->where(function ($q) use ($periodStart) {
            //     $q->whereNull('effective_to')
            //         ->orWhere('effective_to', '>=', $periodStart);
            // });

        }])->where('company_id', $company->company_id)->first();

        // If InvoiceSetup exists with billing rates, use it
        if ($setup && $setup->billingRates->count() > 0) {
            foreach ($setup->billingRates as $rate) {
                // Formatting description based on charge_type nicely (e.g. flat_monthly -> Flat Monthly)
                $chargeTypeLabel = ucwords(str_replace('_', ' ', $rate->charge_type));
                $rateMultiplier = $this->isMonthlyChargeType($rate->charge_type) ? $billingMonths : 1;

                if ($rate->scope_type === 'company') {
                    $lines[] = [
                        'scope_type' => 'company',
                        'scope_id' => $rate->scope_id,
                        'description' => 'Sabsoft (Sabify) POS Application - ' . $chargeTypeLabel . ' (' . $billingPeriodLabel . ') (Company)',
                        'qty' => $rateMultiplier,
                        'unit_price' => $rate->rate,
                        'line_amount' => $rateMultiplier * $rate->rate,
                    ];
                } elseif ($rate->scope_type === 'branch') {
                    if ($rate->scope_id) {
                        $branch = DB::table('branch')
                            ->where('branch_id', $rate->scope_id)
                            ->where('status_id', 1)
                            ->first();

                        if ($branch) {
                            $lines[] = [
                                'scope_type' => 'branch',
                                'scope_id' => $rate->scope_id,
                                'description' => 'Sabsoft (Sabify) POS Application - ' . $chargeTypeLabel . ' (' . $billingPeriodLabel . ') (Branch: ' . $branch->branch_name . ')',
                                'qty' => $rateMultiplier,
                                'unit_price' => $rate->rate,
                                'line_amount' => $rateMultiplier * $rate->rate,
                            ];
                        }
                    } else {
                        $branchCount = DB::table('branch')
                            ->where('company_id', $company->company_id)
                            ->where('status_id', 1)
                            ->count();

                        if ($branchCount > 0) {
                            $lines[] = [
                                'scope_type' => 'branch',
                                'scope_id' => null,
                                'description' => 'Sabsoft (Sabify) POS Application - ' . $chargeTypeLabel . ' (' . $billingPeriodLabel . ') (' . $branchCount . ' Branch' . ($branchCount > 1 ? 'es' : '') . ')',
                                'qty' => $branchCount * $rateMultiplier,
                                'unit_price' => $rate->rate,
                                'line_amount' => $branchCount * $rateMultiplier * $rate->rate,
                            ];
                        }
                    }
                } elseif ($rate->scope_type === 'terminal') {
                    if ($rate->scope_id) {
                        $terminal = DB::table('terminal_details')
                            ->join('branch', 'terminal_details.branch_id', '=', 'branch.branch_id')
                            ->where('terminal_details.terminal_id', $rate->scope_id)
                            ->where('branch.status_id', 1)
                            ->select('terminal_details.*')
                            ->first();

                        if ($terminal) {
                            $lines[] = [
                                'scope_type' => 'terminal',
                                'scope_id' => $rate->scope_id,
                                'description' => 'Sabsoft (Sabify) POS Application - ' . $chargeTypeLabel . ' (' . $billingPeriodLabel . ') (Terminal: ' . $terminal->terminal_name . ')',
                                'qty' => $rateMultiplier,
                                'unit_price' => $rate->rate,
                                'line_amount' => $rateMultiplier * $rate->rate,
                            ];
                        }
                    } else {
                        $terminalCount = DB::table('terminal_details')
                            ->join('branch', 'terminal_details.branch_id', '=', 'branch.branch_id')
                            ->where('branch.company_id', $company->company_id)
                            ->where('branch.status_id', 1)
                            ->count();

                        if ($terminalCount > 0) {
                            $lines[] = [
                                'scope_type' => 'terminal',
                                'scope_id' => null,
                                'description' => 'Sabsoft (Sabify) POS Application - ' . $chargeTypeLabel . ' (' . $billingPeriodLabel . ') (' . $terminalCount . ' Terminal' . ($terminalCount > 1 ? 's' : '') . ')',
                                'qty' => $terminalCount * $rateMultiplier,
                                'unit_price' => $rate->rate,
                                'line_amount' => $terminalCount * $rateMultiplier * $rate->rate,
                            ];
                        }
                    }
                }
            }
            return $lines;
        }

        // --- Fallback if InvoiceSetup doesn't exist for the company ---
        if ($company->invoice_type === 'branch') {
            $branchCount = DB::table('branch')
                ->where('company_id', $company->company_id)
                ->where('status_id', 1)
                ->count();

            if ($branchCount > 0 && $company->monthly_charges_amount > 0) {
                $lines[] = [
                    'scope_type' => 'branch',
                    'scope_id' => null,
                    'description' => 'Sabsoft (Sabify) POS Application - Monthly Subscription (' . $billingPeriodLabel . ') (' . $branchCount . ' Branch' . ($branchCount > 1 ? 'es' : '') . ')',
                    'qty' => $branchCount * $billingMonths,
                    'unit_price' => $company->monthly_charges_amount,
                    'line_amount' => $branchCount * $billingMonths * $company->monthly_charges_amount,
                ];
            }
        } elseif ($company->invoice_type === 'terminal') {
            $terminalCount = DB::table('terminal_details')
                ->join('branch', 'terminal_details.branch_id', '=', 'branch.branch_id')
                ->where('branch.company_id', $company->company_id)
                ->where('branch.status_id', 1)
                ->count();

            if ($terminalCount > 0 && $company->monthly_charges_amount > 0) {
                $lines[] = [
                    'scope_type' => 'terminal',
                    'scope_id' => null,
                    'description' => 'Sabsoft (Sabify) POS Application - Monthly Subscription (' . $billingPeriodLabel . ') (' . $terminalCount . ' Terminal' . ($terminalCount > 1 ? 's' : '') . ')',
                    'qty' => $terminalCount * $billingMonths,
                    'unit_price' => $company->monthly_charges_amount,
                    'line_amount' => $terminalCount * $billingMonths * $company->monthly_charges_amount,
                ];
            }
        }

        return $lines;
    }

    private function getBillingPeriodMonths($periodStart, $periodEnd)
    {
        $start = Carbon::parse($periodStart)->startOfMonth();
        $end = Carbon::parse($periodEnd)->startOfMonth();

        return $start->diffInMonths($end) + 1;
    }

    private function formatBillingPeriodLabel($periodStart, $periodEnd)
    {
        $start = Carbon::parse($periodStart);
        $end = Carbon::parse($periodEnd);

        return $start->format('M-Y') . ' to ' . $end->format('M-Y');
    }

    private function isMonthlyChargeType($chargeType)
    {
        return $chargeType === 'flat_monthly';
    }

    private function generateInvoiceNo($company, $invoiceDate)
    {
        $prefix = $company->invoice_prefix ?? 'INV';
        $year = $invoiceDate->format('Y');
        $month = $invoiceDate->format('m');

        $lastInvoice = Invoice::where('company_id', $company->company_id)
            ->where('invoice_no', 'like', $prefix . '-' . $year . $month . '%')
            ->orderByDesc('id')
            ->first();

        $sequence = 1;
        if ($lastInvoice) {
            $lastSequence = (int) substr($lastInvoice->invoice_no, -4);
            $sequence = $lastSequence + 1;
        }

        return $prefix . '-' . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
