<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\InvoiceSetup;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class InvoiceGenerationService
{
    public function __construct(private InvoiceSettlementService $invoiceSettlementService)
    {
    }

    public function invoiceExists($companyId, $periodStart, $periodEnd, string $invoiceType = 'monthly')
    {
        $query = Invoice::where('company_id', $companyId)
            ->whereDate('period_start', $periodStart)
            ->whereDate('period_end', $periodEnd);

        if ($invoiceType === 'monthly') {
            $query->where(function ($innerQuery) {
                $innerQuery->whereNull('invoice_type')
                    ->orWhere('invoice_type', 'monthly');
            });
        } else {
            $query->where('invoice_type', $invoiceType);
        }

        return $query->exists();
    }

    public function generateInvoice(Company $company, $periodStart, $periodEnd, Carbon $invoiceDate, array $options = [])
    {
        $periodStart = Carbon::parse($periodStart)->toDateString();
        $periodEnd = Carbon::parse($periodEnd)->toDateString();
        $invoiceType = (string) ($options['invoice_type'] ?? 'monthly');
        $dueDate = !empty($options['due_date'])
            ? Carbon::parse($options['due_date'])->toDateString()
            : $invoiceDate->copy()->addDays((int) ($company->payment_due_days ?? 15))->toDateString();

        return DB::transaction(function () use ($company, $periodStart, $periodEnd, $invoiceDate, $dueDate, $options, $invoiceType) {
            $lines = $invoiceType === 'previous_due'
                ? $this->buildPreviousDueInvoiceLines($company)
                : $this->buildInvoiceLines($company, $periodStart, $periodEnd, $options);
            $subtotal = collect($lines)->sum('line_amount');

            $previousDue = $invoiceType === 'previous_due'
                ? 0
                : $this->invoiceSettlementService->calculateOutstandingPreviousDue($company->company_id);

            $taxAmount = (float) ($options['tax_amount'] ?? 0);
            $totalAmount = $subtotal + $taxAmount;

            if (empty($lines) && $totalAmount <= 0) {
                throw new \RuntimeException('No billable items found for the selected period. Please check the company billing setup.');
            }

            $invoice = Invoice::create([
                'company_id' => $company->company_id,
                'invoice_no' => $this->generateInvoiceNo($company, $invoiceDate),
                'invoice_type' => $invoiceType,
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
                'generated_by' => $options['generated_by'] ?? null,
                'notes' => $options['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $line['invoice_id'] = $invoice->id;
                InvoiceLine::create($line);
            }

            return $invoice;
        });
    }

    public function generatePreviousDueInvoice(Company $company, Carbon $invoiceDate, array $options = [])
    {
        $periodStart = !empty($options['period_start'])
            ? Carbon::parse($options['period_start'])->startOfMonth()->toDateString()
            : $invoiceDate->copy()->startOfMonth()->toDateString();
        $periodEnd = !empty($options['period_end'])
            ? Carbon::parse($options['period_end'])->endOfMonth()->toDateString()
            : $invoiceDate->copy()->endOfMonth()->toDateString();

        return $this->generateInvoice($company, $periodStart, $periodEnd, $invoiceDate, array_merge($options, [
            'invoice_type' => 'previous_due',
            'tax_amount' => $options['tax_amount'] ?? 0,
            'notes' => $options['notes'] ?? 'Outstanding previous dues invoice.',
        ]));
    }

    public function sendInvoicePdfToWhatsapp(Invoice $invoice, array $options = []): array
    {
        $invoice->loadMissing(['company', 'lines', 'payments', 'adjustments']);

        $company = $invoice->company;
        if (!$company) {
            throw new \RuntimeException('Invoice company not found.');
        }

        $to = $this->resolveWhatsAppNumber($company, $options['to'] ?? null);
        if (!$to) {
            $result = [
                'status' => 'skipped',
                'reason' => 'Company WhatsApp number is not configured.',
            ];

            $this->logInvoiceWhatsappActivity($invoice, $company, $result, $options);

            return $result;
        }

        $templateName = (string) ($options['template'] ?? config('services.whatsapp.templates.billing_invoice', 'report'));
        $language = (string) ($options['language'] ?? config('services.whatsapp.template_lang', 'en'));

        if ($templateName === '') {
            $result = [
                'status' => 'skipped',
                'reason' => 'WhatsApp invoice template is not configured.',
            ];

            $this->logInvoiceWhatsappActivity($invoice, $company, $result, $options);

            return $result;
        }

        $document = $this->storeInvoicePdf($invoice);
        $response = $this->sendWhatsAppTemplateWithDocument(
            $to,
            $templateName,
            $language,
            $document['url'],
            $document['filename'],
            [
                (string) $company->name,
                'Billing Invoice',
                $this->formatWhatsAppBillingPeriod($invoice),
            ]
        );

        if (!$response->successful()) {
            Log::warning('WhatsApp billing invoice send failed', [
                'invoice_id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no,
                'company_id' => $company->company_id,
                'to' => $to,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            $this->logInvoiceWhatsappActivity($invoice, $company, [
                'status' => 'failed',
                'to' => $to,
                'filename' => $document['filename'],
                'url' => $document['url'],
                'http_status' => $response->status(),
                'response_body' => Str::limit($response->body(), 2000),
                'reason' => 'WhatsApp send failed with status ' . $response->status() . '.',
            ], $options);

            throw new \RuntimeException('WhatsApp send failed with status ' . $response->status() . '.');
        }

        Log::info('WhatsApp billing invoice sent successfully', [
            'invoice_id' => $invoice->id,
            'invoice_no' => $invoice->invoice_no,
            'company_id' => $company->company_id,
            'to' => $to,
            'filename' => $document['filename'],
        ]);

        $result = [
            'status' => 'sent',
            'to' => $to,
            'filename' => $document['filename'],
            'url' => $document['url'],
        ];

        $this->logInvoiceWhatsappActivity($invoice, $company, $result, $options);

        return $result;
    }

    private function logInvoiceWhatsappActivity(Invoice $invoice, Company $company, array $result, array $options = []): void
    {
        try {
            $status = (string) ($result['status'] ?? 'unknown');
            $batchUuid = $options['batch_uuid'] ?? null;

            activity('billing_invoice_whatsapp')
                ->withCompany($company->company_id)
                ->performedOn($invoice)
                ->withProperties([
                    'invoice_id' => $invoice->id,
                    'invoice_no' => $invoice->invoice_no,
                    'company_id' => $company->company_id,
                    'company_name' => $company->name,
                    'status' => $status,
                    'to' => $result['to'] ?? null,
                    'filename' => $result['filename'] ?? null,
                    'url' => $result['url'] ?? null,
                    'reason' => $result['reason'] ?? null,
                    'http_status' => $result['http_status'] ?? null,
                    'response_body' => $result['response_body'] ?? null,
                    'trigger' => $options['trigger'] ?? 'manual',
                ])
                ->tap(function ($activity) use ($batchUuid) {
                    if ($batchUuid) {
                        $activity->batch_uuid = $batchUuid;
                    }
                })
                ->event('whatsapp_' . $status)
                ->log("Billing invoice WhatsApp {$status}");
        } catch (Throwable $exception) {
            Log::warning('Failed to record billing invoice WhatsApp activity', [
                'invoice_id' => $invoice->id,
                'status' => $result['status'] ?? null,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function storeInvoicePdf(Invoice $invoice): array
    {
        $pdf = Pdf::loadView('Admin.Billing.invoices.pdf', [
            'invoice' => $invoice,
        ]);

        $filename = 'invoice-' . preg_replace('/[^A-Za-z0-9._-]/', '-', (string) $invoice->invoice_no) . '.pdf';
        $directory = 'pdfs/billing-invoices';
        $path = $directory . '/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());

        return [
            'filename' => $filename,
            'path' => $path,
            'url' => url(Storage::disk('public')->url($path)),
        ];
    }

    private function sendWhatsAppTemplateWithDocument(
        string $to,
        string $templateName,
        string $language,
        string $documentUrl,
        string $filename,
        array $bodyParameters
    ) {
        $token = config('services.whatsapp.token');
        $phoneId = config('services.whatsapp.phone_id');

        if (!$token || !$phoneId) {
            throw new \RuntimeException('WhatsApp credentials are not configured.');
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $language,
                ],
                'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'document',
                                'document' => [
                                    'link' => $documentUrl,
                                    'filename' => $filename,
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'body',
                        'parameters' => array_map(function ($value) {
                            return [
                                'type' => 'text',
                                'text' => (string) $value,
                            ];
                        }, $bodyParameters),
                    ],
                ],
            ],
        ];

        return Http::withToken($token)
            ->post("https://graph.facebook.com/v17.0/{$phoneId}/messages", $payload);
    }

    private function resolveWhatsAppNumber(Company $company, ?string $fallback = null): ?string
    {
        $candidates = [
            $fallback,
            $company->whatsapp_number ?? null,
            $company->mobile_contact ?? null,
        ];

        foreach ($candidates as $candidate) {
            $normalized = $this->normalizeWhatsAppNumber($candidate);
            if ($normalized) {
                return $normalized;
            }
        }

        return null;
    }

    private function normalizeWhatsAppNumber(?string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0092')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (!str_starts_with($digits, '92')) {
            $digits = '92' . $digits;
        }

        return preg_match('/^92\d{10}$/', $digits) ? $digits : null;
    }

    private function formatWhatsAppBillingPeriod(Invoice $invoice): string
    {
        $periodStart = Carbon::parse($invoice->period_start);
        $periodEnd = Carbon::parse($invoice->period_end);

        if ($periodStart->format('Y-m') === $periodEnd->format('Y-m')) {
            return $periodStart->format('F-Y');
        }

        return $periodStart->format('F-Y') . ' to ' . $periodEnd->format('F-Y');
    }

    private function buildInvoiceLines($company, $periodStart, $periodEnd, array $options = [])
    {
        $manualScopePeriods = collect($options['manual_scope_periods'] ?? [])
            ->filter(fn ($row) => !empty($row['scope_type']) && !empty($row['scope_id']) && !empty($row['period_start']) && !empty($row['period_end']))
            ->values();

        if ($manualScopePeriods->isNotEmpty()) {
            return $this->buildManualScopedInvoiceLines($company, $periodStart, $periodEnd, $manualScopePeriods, $options);
        }

        $lines = [];
        $billingMonths = $this->getBillingPeriodMonths($periodStart, $periodEnd);
        $billingPeriodLabel = $this->formatBillingPeriodLabel($periodStart, $periodEnd);
        $billingContext = $this->getBillingContext($company->company_id, $periodStart, $periodEnd);
        $billingRates = $billingContext['rates'];
        $setup = $billingContext['setup'];

        if ($billingRates->count() > 0) {
            foreach ($billingRates as $rate) {
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
                                'description' => 'Sabsoft (Sabify) POS Application - ' . $chargeTypeLabel . ' (' . $billingPeriodLabel . ') (Terminal: ' . $terminal->terminal_name . ' | TID # ' . $terminal->terminal_id . ')',
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
        }

        if (!empty($lines)) {
            return $lines;
        }

        $invoiceType = $setup->invoice_type ?? null;
        $monthlyChargesAmount = (float) ($setup->monthly_charges_amount ?? 0);
        $configuredBranchIds = $this->getConfiguredScopeIds($company->company_id, 'branch');
        $configuredTerminalIds = $this->getConfiguredScopeIds($company->company_id, 'terminal');

        if ($invoiceType === 'branch') {
            if ($configuredBranchIds->isNotEmpty()) {
                $branchCount = DB::table('branch')
                    ->where('company_id', $company->company_id)
                    ->where('status_id', 1)
                    ->whereIn('branch_id', $configuredBranchIds->all())
                    ->count();
            } else {
                $branchCount = DB::table('branch')
                    ->where('company_id', $company->company_id)
                    ->where('status_id', 1)
                    ->count();
            }

            if ($branchCount > 0 && $monthlyChargesAmount > 0) {
                $lines[] = [
                    'scope_type' => 'branch',
                    'scope_id' => null,
                    'description' => 'Sabsoft (Sabify) POS Application - Monthly Subscription (' . $billingPeriodLabel . ') (' . $branchCount . ' Branch' . ($branchCount > 1 ? 'es' : '') . ')',
                    'qty' => $branchCount * $billingMonths,
                    'unit_price' => $monthlyChargesAmount,
                    'line_amount' => $branchCount * $billingMonths * $monthlyChargesAmount,
                ];
            }
        } elseif ($invoiceType === 'terminal') {
            if ($configuredTerminalIds->isNotEmpty()) {
                $terminalCount = DB::table('terminal_details')
                    ->join('branch', 'terminal_details.branch_id', '=', 'branch.branch_id')
                    ->where('branch.company_id', $company->company_id)
                    ->where('branch.status_id', 1)
                    ->whereIn('terminal_details.terminal_id', $configuredTerminalIds->all())
                    ->count();
            } else {
                $terminalCount = DB::table('terminal_details')
                    ->join('branch', 'terminal_details.branch_id', '=', 'branch.branch_id')
                    ->where('branch.company_id', $company->company_id)
                    ->where('branch.status_id', 1)
                    ->count();
            }

            if ($terminalCount > 0 && $monthlyChargesAmount > 0) {
                $lines[] = [
                    'scope_type' => 'terminal',
                    'scope_id' => null,
                    'description' => 'Sabsoft (Sabify) POS Application - Monthly Subscription (' . $billingPeriodLabel . ') (' . $terminalCount . ' Terminal' . ($terminalCount > 1 ? 's' : '') . ')',
                    'qty' => $terminalCount * $billingMonths,
                    'unit_price' => $monthlyChargesAmount,
                    'line_amount' => $terminalCount * $billingMonths * $monthlyChargesAmount,
                ];
            }
        }

        return $lines;
    }

    private function buildPreviousDueInvoiceLines(Company $company): array
    {
        $outstandingInvoices = Invoice::query()
            ->where('company_id', $company->company_id)
            ->where('status', '!=', 'void')
            ->where('balance_amount', '>', 0)
            ->where(function ($query) {
                $query->whereNull('invoice_type')
                    ->orWhere('invoice_type', 'monthly');
            })
            ->orderBy('period_start')
            ->orderBy('id')
            ->get();

        $lines = [];

        foreach ($outstandingInvoices as $invoice) {
            $lines[] = [
                'scope_type' => 'company',
                'scope_id' => $company->company_id,
                'description' => 'Previous due for Invoice ' . $invoice->invoice_no . ' (' . $this->formatBillingPeriodLabel($invoice->period_start, $invoice->period_end) . ')',
                'qty' => 1,
                'unit_price' => (float) $invoice->balance_amount,
                'line_amount' => (float) $invoice->balance_amount,
            ];
        }

        return $lines;
    }

    private function buildManualScopedInvoiceLines(Company $company, string $periodStart, string $periodEnd, $manualScopePeriods, array $options = [])
    {
        $lines = [];
        $billingContext = $this->getBillingContext($company->company_id, $periodStart, $periodEnd);
        $billingRates = collect($billingContext['rates']);
        $setup = $billingContext['setup'];
        $invoiceType = $setup->invoice_type ?? 'branch';
        $scopeType = $invoiceType === 'terminal' ? 'terminal' : 'branch';
        $includeInactiveScopes = !empty($options['include_inactive_scopes'] ?? false);

        if ($scopeType === 'branch') {
            $scopeIds = $manualScopePeriods->where('scope_type', 'branch')->pluck('scope_id')->map(fn ($id) => (int) $id)->all();
            $scopes = DB::table('branch')
                ->where('company_id', $company->company_id)
                ->whereIn('branch_id', $scopeIds)
                ->when(!$includeInactiveScopes, fn ($query) => $query->where('status_id', 1))
                ->select('branch_id as id', 'branch_name as name')
                ->get()
                ->keyBy('id');
        } else {
            $scopeIds = $manualScopePeriods->where('scope_type', 'terminal')->pluck('scope_id')->map(fn ($id) => (int) $id)->all();
            $scopes = DB::table('terminal_details')
                ->join('branch', 'terminal_details.branch_id', '=', 'branch.branch_id')
                ->where('branch.company_id', $company->company_id)
                ->when(!$includeInactiveScopes, fn ($query) => $query->where('branch.status_id', 1))
                ->whereIn('terminal_details.terminal_id', $scopeIds)
                ->select('terminal_details.terminal_id as id', 'terminal_details.terminal_name as name')
                ->get()
                ->keyBy('id');
        }

        foreach ($manualScopePeriods->where('scope_type', $scopeType) as $scopePeriod) {
            $scopeId = (int) $scopePeriod['scope_id'];
            $scope = $scopes->get($scopeId);
            if (!$scope) {
                continue;
            }

            $scopeStart = Carbon::parse($scopePeriod['period_start'])->toDateString();
            $scopeEnd = Carbon::parse($scopePeriod['period_end'])->toDateString();
            $scopeMonths = $this->getBillingPeriodMonths($scopeStart, $scopeEnd);
            $scopePeriodLabel = $this->formatBillingPeriodLabel($scopeStart, $scopeEnd);
            $scopeDisplay = $scopeType === 'branch'
                ? 'Branch: ' . $scope->name
                : 'Terminal: ' . $scope->name . ' | TID # ' . $scopeId;

            $applicableRates = $billingRates
                ->filter(function ($rate) use ($scopeType, $scopeId, $scopeStart, $scopeEnd) {
                    if ($rate->scope_type !== $scopeType) {
                        return false;
                    }

                    if ($rate->scope_id && (int) $rate->scope_id !== $scopeId) {
                        return false;
                    }

                    return $rate->effective_from <= $scopeEnd
                        && (empty($rate->effective_to) || $rate->effective_to >= $scopeStart);
                })
                ->values();

            if ($applicableRates->isNotEmpty()) {
                foreach ($applicableRates as $rate) {
                    $chargeTypeLabel = ucwords(str_replace('_', ' ', $rate->charge_type));
                    $rateMultiplier = $this->isMonthlyChargeType($rate->charge_type) ? $scopeMonths : 1;

                    $lines[] = [
                        'scope_type' => $scopeType,
                        'scope_id' => $scopeId,
                        'description' => 'Sabsoft (Sabify) POS Application - ' . $chargeTypeLabel . ' (' . $scopePeriodLabel . ') (' . $scopeDisplay . ')',
                        'qty' => $rateMultiplier,
                        'unit_price' => $rate->rate,
                        'line_amount' => $rateMultiplier * $rate->rate,
                    ];
                }

                continue;
            }

            if (($setup->invoice_type ?? null) === $scopeType && (float) ($setup->monthly_charges_amount ?? 0) > 0) {
                $lines[] = [
                    'scope_type' => $scopeType,
                    'scope_id' => $scopeId,
                    'description' => 'Sabsoft (Sabify) POS Application - Monthly Subscription (' . $scopePeriodLabel . ') (' . $scopeDisplay . ')',
                    'qty' => $scopeMonths,
                    'unit_price' => (float) $setup->monthly_charges_amount,
                    'line_amount' => $scopeMonths * (float) $setup->monthly_charges_amount,
                ];
            }
        }

        $companyScopedRates = $billingRates
            ->filter(function ($rate) use ($periodStart, $periodEnd) {
                return $rate->scope_type === 'company'
                    && $rate->effective_from <= $periodEnd
                    && (empty($rate->effective_to) || $rate->effective_to >= $periodStart);
            })
            ->values();

        $overallBillingMonths = $this->getBillingPeriodMonths($periodStart, $periodEnd);
        $overallBillingLabel = $this->formatBillingPeriodLabel($periodStart, $periodEnd);

        foreach ($companyScopedRates as $rate) {
            $chargeTypeLabel = ucwords(str_replace('_', ' ', $rate->charge_type));
            $rateMultiplier = $this->isMonthlyChargeType($rate->charge_type) ? $overallBillingMonths : 1;

            $lines[] = [
                'scope_type' => 'company',
                'scope_id' => $rate->scope_id,
                'description' => 'Sabsoft (Sabify) POS Application - ' . $chargeTypeLabel . ' (' . $overallBillingLabel . ') (Company)',
                'qty' => $rateMultiplier,
                'unit_price' => $rate->rate,
                'line_amount' => $rateMultiplier * $rate->rate,
            ];
        }

        return $lines;
    }

    private function getBillingContext(int $companyId, string $periodStart, string $periodEnd): array
    {
        $setup = InvoiceSetup::with(['billingRates' => function ($query) use ($periodStart, $periodEnd) {
            $query->where('is_active', 1)
                ->whereDate('effective_from', '<=', $periodEnd)
                ->where(function ($innerQuery) use ($periodStart) {
                    $innerQuery->whereNull('effective_to')
                        ->orWhereDate('effective_to', '>=', $periodStart);
                });
        }])->where('company_id', $companyId)->first();

        return [
            'setup' => $setup,
            'rates' => $setup ? $setup->billingRates : collect(),
        ];
    }

    private function getConfiguredScopeIds(int $companyId, string $scopeType)
    {
        $setup = InvoiceSetup::with(['billingRates' => function ($query) use ($scopeType) {
            $query->where('is_active', 1)
                ->where('scope_type', $scopeType)
                ->whereNotNull('scope_id');
        }])->where('company_id', $companyId)->first();

        return collect($setup?->billingRates ?? [])
            ->pluck('scope_id')
            ->filter()
            ->unique()
            ->values();
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
        $prefix = trim((string) ($company->invoice_prefix ?? '')) ?: 'INV';
        $year = $invoiceDate->format('Y');
        $month = $invoiceDate->format('m');
        $seriesPrefix = $prefix . '-' . $year . $month;

        $lastInvoice = Invoice::where('invoice_no', 'like', $seriesPrefix . '%')
            ->orderByDesc('invoice_no')
            ->first();

        $sequence = 1;
        if ($lastInvoice) {
            $lastSequence = (int) substr($lastInvoice->invoice_no, -4);
            $sequence = $lastSequence + 1;
        }

        return $seriesPrefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
