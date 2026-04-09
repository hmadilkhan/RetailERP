<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceAdjustment;
use App\Models\InvoiceLine;
use App\Models\InvoicePayment;
use App\Models\InvoiceSetup;
use App\Models\OrderPayment;
use App\Models\PaymentVoucher;
use App\Models\PaymentVoucherScreenshot;
use App\Services\InvoiceGenerationService;
use App\Services\InvoiceSettlementService;
use App\Services\PaymentVoucherService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class BillingController extends Controller
{
    public function summary(Request $request)
    {
        $summary = DB::table('invoices')
            ->join('company', 'invoices.company_id', '=', 'company.company_id')
            ->select(
                'company.company_id',
                'company.name as company_name'
            )
            ->selectRaw('COUNT(invoices.id) as total_invoices')
            ->selectRaw('COALESCE(SUM(invoices.total_amount), 0) as total_amount')
            ->selectRaw('COALESCE(SUM(invoices.paid_amount), 0) as paid_amount')
            ->selectRaw('COALESCE(SUM(invoices.balance_amount), 0) as balance_amount')
            ->where('invoices.status', '!=', 'void')
            ->when(!empty($request->company_id), function ($query) use ($request) {
                $query->where('invoices.company_id', $request->company_id);
            })
            ->groupBy('company.company_id', 'company.name')
            ->orderBy('company.name')
            ->get();

        $invoicePeriodsByCompany = DB::table('invoices')
            ->select(
                'company_id',
                'period_start',
                'period_end',
                'total_amount',
                'balance_amount'
            )
            ->where('status', '!=', 'void')
            ->when(!empty($request->company_id), function ($query) use ($request) {
                $query->where('company_id', $request->company_id);
            })
            ->get()
            ->groupBy('company_id');

        $summary = $summary->map(function ($item) use ($invoicePeriodsByCompany) {
            $companyInvoices = $invoicePeriodsByCompany->get($item->company_id, collect());

            $estimatedUnpaidMonths = $companyInvoices->sum(function ($invoice) {
                $periodStart = Carbon::parse($invoice->period_start)->startOfMonth();
                $periodEnd = Carbon::parse($invoice->period_end)->startOfMonth();
                $invoiceMonths = $periodStart->diffInMonths($periodEnd) + 1;
                $totalAmount = (float) $invoice->total_amount;
                $balanceAmount = max((float) $invoice->balance_amount, 0);

                if ($invoiceMonths <= 0 || $totalAmount <= 0 || $balanceAmount <= 0) {
                    return 0;
                }

                return $invoiceMonths * min($balanceAmount / $totalAmount, 1);
            });

            $item->unpaid_months = round($estimatedUnpaidMonths, 1);
            $item->full_unpaid_months = (int) floor($estimatedUnpaidMonths);
            $item->partial_unpaid_months = round(max($estimatedUnpaidMonths - $item->full_unpaid_months, 0), 1);

            return $item;
        });

        $companies = Company::select('company_id', 'name')->orderBy('name')->get();
        $selectedCompanyId = $request->company_id;

        if ($request->ajax()) {
            return view('Admin.Billing.partials.summary-content', compact('summary'));
        }

        return view('Admin.Billing.summary', compact('summary', 'companies', 'selectedCompanyId'));
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

    public function deliveryHistory(Request $request)
    {
        $logsQuery = DB::table('activity_log as al')
            ->leftJoin('invoices', function ($join) {
                $join->on('invoices.id', '=', 'al.subject_id')
                    ->where('al.subject_type', '=', Invoice::class);
            })
            ->leftJoin('company', 'company.company_id', '=', 'al.company_id')
            ->select([
                'al.id',
                'al.log_name',
                'al.description',
                'al.event',
                'al.properties',
                'al.batch_uuid',
                'al.company_id',
                'al.created_at',
                'invoices.id as invoice_id',
                'invoices.invoice_no',
                'company.name as company_name',
            ])
            ->whereIn('al.log_name', ['billing_invoice_whatsapp', 'billing_invoice_generation'])
            ->orderByDesc('al.id');

        if (!empty($request->company_id)) {
            $logsQuery->where('al.company_id', $request->company_id);
        }

        if (!empty($request->status)) {
            $logsQuery->where('al.event', 'whatsapp_' . $request->status);
        }

        if (!empty($request->invoice_no)) {
            $logsQuery->where('invoices.invoice_no', 'like', '%' . trim($request->invoice_no) . '%');
        }

        if (!empty($request->run_id)) {
            $logsQuery->where('al.batch_uuid', trim($request->run_id));
        }

        if (!empty($request->date)) {
            $logsQuery->whereDate('al.created_at', Carbon::parse($request->date)->toDateString());
        }

        $deliveryLogs = $logsQuery->paginate(25)->appends($request->query());
        $deliveryLogs->setCollection(
            $deliveryLogs->getCollection()->map(function ($log) {
                $properties = json_decode($log->properties ?? '{}', true) ?: [];
                $status = str_replace('whatsapp_', '', (string) ($log->event ?? ''));
                if ($status === (string) ($log->event ?? '')) {
                    $status = str_replace('generation_', '', (string) ($log->event ?? ''));
                }

                $log->status = $status !== '' ? $status : ($properties['status'] ?? 'unknown');
                $log->stage = $properties['stage']
                    ?? ($log->log_name === 'billing_invoice_generation' ? 'generation' : 'whatsapp');
                $log->to = $properties['to'] ?? null;
                $log->reason = $properties['reason'] ?? null;
                $log->trigger = $properties['trigger'] ?? null;
                $log->filename = $properties['filename'] ?? null;
                $log->invoice_no = $log->invoice_no ?? ($properties['invoice_no'] ?? null);
                $log->company_name = $log->company_name ?? ($properties['company_name'] ?? null);
                $log->invoice_id = $log->invoice_id ?? ($properties['invoice_id'] ?? null);

                return $log;
            })
        );

        $recentRuns = DB::table('activity_log')
            ->select(['id', 'description', 'properties', 'batch_uuid', 'created_at'])
            ->where('log_name', 'billing_invoice_run')
            ->orderByDesc('id')
            ->limit(8)
            ->get()
            ->map(function ($run) {
                $properties = json_decode($run->properties ?? '{}', true) ?: [];

                return (object) [
                    'id' => $run->id,
                    'description' => $run->description,
                    'batch_uuid' => $run->batch_uuid,
                    'created_at' => $run->created_at,
                    'period_start' => $properties['period_start'] ?? null,
                    'period_end' => $properties['period_end'] ?? null,
                    'generated_count' => $properties['generated_count'] ?? 0,
                    'skipped_count' => $properties['skipped_count'] ?? 0,
                    'failed_count' => $properties['failed_count'] ?? 0,
                    'whatsapp_sent_count' => $properties['whatsapp_sent_count'] ?? 0,
                    'whatsapp_skipped_count' => $properties['whatsapp_skipped_count'] ?? 0,
                    'whatsapp_failed_count' => $properties['whatsapp_failed_count'] ?? 0,
                ];
            });

        $companies = Company::select('company_id', 'name')->orderBy('name')->get();

        return view('Admin.Billing.delivery-history', compact('deliveryLogs', 'recentRuns', 'companies'));
    }

    public function create()
    {
        $companies = Company::select('company_id', 'name', 'invoice_type', 'payment_due_days', 'invoice_prefix', 'monthly_charges_amount')
            ->where('status_id', 1)
            ->orderBy('name')
            ->get();

        return view('Admin.Billing.invoices.create', compact('companies'));
    }

    public function invoiceGenerationTargets(Request $request)
    {
        $companyId = (int) $request->query('company_id');
        if (!$companyId) {
            return response()->json([
                'invoice_type' => null,
                'items' => [],
            ]);
        }

        $setup = InvoiceSetup::with(['billingRates' => function ($query) {
            $query->where('is_active', 1);
        }])->where('company_id', $companyId)->first();
        $invoiceType = $setup->invoice_type ?? (Company::where('company_id', $companyId)->value('invoice_type') ?? 'branch');
        $configuredScopeIds = collect($setup?->billingRates ?? [])
            ->where('scope_type', $invoiceType)
            ->pluck('scope_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($invoiceType === 'terminal') {
            if ($configuredScopeIds->isEmpty()) {
                return response()->json([
                    'invoice_type' => $invoiceType,
                    'items' => [],
                ]);
            }

            $items = DB::table('terminal_details')
                ->join('branch', 'terminal_details.branch_id', '=', 'branch.branch_id')
                ->where('branch.company_id', $companyId)
                ->where('branch.status_id', 1)
                ->whereIn('terminal_details.terminal_id', $configuredScopeIds->all())
                ->select([
                    'terminal_details.terminal_id as id',
                    'terminal_details.terminal_name as name',
                    'branch.branch_name',
                ])
                ->orderBy('branch.branch_name')
                ->orderBy('terminal_details.terminal_name')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => (int) $item->id,
                        'name' => $item->name,
                        'meta' => $item->branch_name,
                    ];
                })
                ->values();
        } else {
            if ($configuredScopeIds->isEmpty()) {
                return response()->json([
                    'invoice_type' => $invoiceType,
                    'items' => [],
                ]);
            }

            $items = DB::table('branch')
                ->where('company_id', $companyId)
                ->where('status_id', 1)
                ->whereIn('branch_id', $configuredScopeIds->all())
                ->select([
                    'branch_id as id',
                    'branch_name as name',
                ])
                ->orderBy('branch_name')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => (int) $item->id,
                        'name' => $item->name,
                        'meta' => null,
                    ];
                })
                ->values();
        }

        return response()->json([
            'invoice_type' => $invoiceType,
            'items' => $items,
        ]);
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
            'generation_mode' => 'required|in:auto,manual',
            'scope_overrides' => 'nullable|array',
            'scope_overrides.*.scope_type' => 'nullable|in:branch,terminal',
            'scope_overrides.*.scope_id' => 'nullable|integer',
            'scope_overrides.*.include' => 'nullable',
            'scope_overrides.*.period_start' => 'nullable|date',
            'scope_overrides.*.period_end' => 'nullable|date',
        ]);

        $company = Company::findOrFail($data['company_id']);
        $setup = InvoiceSetup::with(['billingRates' => function ($query) {
            $query->where('is_active', 1);
        }])->where('company_id', $company->company_id)->first();
        $scopeType = ($setup->invoice_type ?? $company->invoice_type ?? 'branch') === 'terminal' ? 'terminal' : 'branch';

        $manualScopePeriods = collect($data['scope_overrides'] ?? [])
            ->filter(function (array $row) use ($data, $scopeType) {
                if (($data['generation_mode'] ?? 'auto') !== 'manual') {
                    return false;
                }

                if (($row['scope_type'] ?? null) !== $scopeType) {
                    return false;
                }

                return !empty($row['include']) && !empty($row['scope_id']);
            })
            ->map(function (array $row) {
                return [
                    'scope_type' => $row['scope_type'],
                    'scope_id' => (int) $row['scope_id'],
                    'period_start' => !empty($row['period_start']) ? Carbon::parse($row['period_start'])->toDateString() : null,
                    'period_end' => !empty($row['period_end']) ? Carbon::parse($row['period_end'])->toDateString() : null,
                ];
            })
            ->values();

        if ($manualScopePeriods->isNotEmpty()) {
            $validScopeIds = collect($setup?->billingRates ?? [])
                ->where('scope_type', $scopeType)
                ->pluck('scope_id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->all();

            foreach ($manualScopePeriods as $scopePeriod) {
                if (!$scopePeriod['period_start'] || !$scopePeriod['period_end']) {
                    throw ValidationException::withMessages([
                        'scope_overrides' => 'Each selected item must have both start and end date.',
                    ]);
                }

                if ($scopePeriod['period_end'] < $scopePeriod['period_start']) {
                    throw ValidationException::withMessages([
                        'scope_overrides' => 'Due end date must be after or equal to the start date.',
                    ]);
                }

                if (!in_array($scopePeriod['scope_id'], $validScopeIds, true)) {
                    throw ValidationException::withMessages([
                        'scope_overrides' => 'One or more selected items are not configured in invoice setup billing rates.',
                    ]);
                }
            }
        }

        $periodStart = $manualScopePeriods->isNotEmpty()
            ? $manualScopePeriods->min('period_start')
            : Carbon::parse($data['period_start'])->toDateString();
        $periodEnd = $manualScopePeriods->isNotEmpty()
            ? $manualScopePeriods->max('period_end')
            : Carbon::parse($data['period_end'])->toDateString();
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
            'manual_scope_periods' => $manualScopePeriods->all(),
        ]);

        return redirect()->route('billing.invoices.index')->with('success', 'Invoice generated successfully.');
    }

    public function show($id)
    {
        $invoice = Invoice::with([
            'company',
            'lines',
            'payments.paymentMode',
            'payments.voucher',
            'payments.screenshots',
            'adjustments',
        ])->findOrFail($id);
        $paymentModes = OrderPayment::orderBy('payment_mode')->get(['payment_id', 'payment_mode']);

        return view('Admin.Billing.invoices.show', compact('invoice', 'paymentModes'));
    }

    public function sendToWhatsApp($id, InvoiceGenerationService $invoiceGenerationService)
    {
        $invoice = Invoice::with(['company', 'lines', 'payments', 'adjustments'])->findOrFail($id);

        try {
            $result = $invoiceGenerationService->sendInvoicePdfToWhatsapp($invoice, [
                'to' => request('to'),
            ]);

            if (($result['status'] ?? null) === 'skipped') {
                return back()->withErrors(['error' => $result['reason'] ?? 'WhatsApp invoice was skipped.']);
            }

            return back()->with('success', 'Invoice PDF sent to WhatsApp successfully.');
        } catch (Throwable $exception) {
            return back()->withErrors(['error' => 'Unable to send invoice to WhatsApp. ' . $exception->getMessage()]);
        }
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

    public function addPayment(
        Request $request,
        $invoiceId,
        InvoiceSettlementService $invoiceSettlementService,
        PaymentVoucherService $paymentVoucherService
    )
    {
        $paymentModeId = $request->input('payment_mode_id');
        $selectedPaymentMode = $paymentModeId
            ? OrderPayment::query()->find($paymentModeId, ['payment_id', 'payment_mode'])
            : null;
        $isCashPayment = $selectedPaymentMode
            ? strcasecmp(trim((string) $selectedPaymentMode->payment_mode), 'cash') === 0
            : false;

        $validator = Validator::make($request->all(), [
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode_id' => 'nullable|integer|exists:sales_payment,payment_id',
            'reference_no' => 'nullable|string|max:100',
            'narration' => 'nullable|string|max:255',
            'screenshots' => [
                Rule::requiredIf(function () use ($request, $isCashPayment) {
                    return $request->filled('payment_mode_id') && !$isCashPayment;
                }),
                'array',
                'max:8',
            ],
            'screenshots.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'screenshots.required' => 'Payment screenshot is required for non-cash payments.',
            'screenshots.array' => 'Screenshots must be uploaded as a list of image files.',
            'screenshots.max' => 'You can upload up to 8 payment screenshots at a time.',
            'screenshots.*.image' => 'Each screenshot must be a valid image.',
            'screenshots.*.mimes' => 'Screenshots must be JPG, JPEG, PNG, or WEBP files.',
            'screenshots.*.max' => 'Each screenshot must not be larger than 5 MB.',
        ]);

        $data = $validator->validate();

        try {
            $result = DB::transaction(function () use ($invoiceId, $data, $request, $invoiceSettlementService, $paymentVoucherService) {
                $invoice = Invoice::lockForUpdate()->findOrFail($invoiceId);
                $paymentDate = Carbon::parse($data['payment_date']);

                $voucher = PaymentVoucher::create([
                    'voucher_no' => $paymentVoucherService->createVoucherNumber($paymentDate),
                    'company_id' => $invoice->company_id,
                    'payment_date' => $paymentDate->toDateString(),
                    'payment_mode_id' => $data['payment_mode_id'] ?? null,
                    'total_received_amount' => $data['amount'],
                    'reference_no' => $data['reference_no'] ?? null,
                    'narration' => $data['narration'] ?? null,
                    'received_by' => session('userid'),
                    'whatsapp_status' => 'pending',
                ]);

                foreach ($request->file('screenshots', []) as $index => $file) {
                    $path = $file->store('billing/payment-screenshots/' . $voucher->id, 'public');

                    PaymentVoucherScreenshot::create([
                        'payment_voucher_id' => $voucher->id,
                        'disk' => 'public',
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_name' => basename($path),
                        'mime_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'sort_order' => $index + 1,
                    ]);
                }

                $allocation = $invoiceSettlementService->applyPaymentToCompany($invoice, [
                    'payment_date' => $data['payment_date'],
                    'amount' => $data['amount'],
                    'payment_mode_id' => $data['payment_mode_id'] ?? null,
                    'reference_no' => $data['reference_no'] ?? null,
                    'narration' => $data['narration'] ?? null,
                    'received_by' => session('userid'),
                    'payment_voucher_id' => $voucher->id,
                ]);

                return [
                    'voucher' => $voucher->fresh(['company', 'paymentMode', 'invoicePayments.invoice', 'screenshots']),
                    'allocation' => $allocation,
                ];
            });
        } catch (Throwable $exception) {
            return redirect()
                ->route('billing.invoices.show', $invoiceId)
                ->withErrors(['error' => $exception->getMessage()]);
        }

        $summary = collect($result['allocation']['allocations'])
            ->map(function (array $item) {
                return $item['invoice_no'] . ' (PKR ' . number_format($item['amount'], 2) . ')';
            })
            ->implode(', ');

        $whatsAppMessage = '';
        try {
            $whatsAppResult = $paymentVoucherService->sendVoucherToWhatsapp($result['voucher'], [
                'trigger' => 'manual_payment',
            ]);

            if (($whatsAppResult['status'] ?? null) === 'sent') {
                $whatsAppMessage = ' Payment voucher sent to WhatsApp.';
            } elseif (($whatsAppResult['status'] ?? null) === 'skipped') {
                $whatsAppMessage = ' Payment voucher created, but WhatsApp was skipped: ' . ($whatsAppResult['reason'] ?? 'Unknown reason.') . '.';
            }
        } catch (Throwable $exception) {
            $whatsAppMessage = ' Payment voucher created, but WhatsApp sending failed: ' . $exception->getMessage() . '.';
        }

        return redirect()
            ->route('billing.invoices.show', $invoiceId)
            ->with('success', 'Payment received successfully. Voucher ' . $result['voucher']->voucher_no . ' created. Allocated to: ' . $summary . '.' . $whatsAppMessage);
    }

    public function downloadPaymentScreenshot($id)
    {
        $screenshot = PaymentVoucherScreenshot::findOrFail($id);
        $disk = $screenshot->disk ?: 'public';

        if (!Storage::disk($disk)->exists($screenshot->file_path)) {
            abort(404);
        }

        return Storage::disk($disk)->download(
            $screenshot->file_path,
            $screenshot->original_name ?: $screenshot->file_name ?: ('payment-screenshot-' . $screenshot->id)
        );
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
