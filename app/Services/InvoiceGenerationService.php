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
use Throwable;

class InvoiceGenerationService
{
    public function invoiceExists($companyId, $periodStart, $periodEnd)
    {
        return Invoice::where('company_id', $companyId)
            ->whereDate('period_start', $periodStart)
            ->whereDate('period_end', $periodEnd)
            ->exists();
    }

    public function generateInvoice(Company $company, $periodStart, $periodEnd, Carbon $invoiceDate, array $options = [])
    {
        $periodStart = Carbon::parse($periodStart)->toDateString();
        $periodEnd = Carbon::parse($periodEnd)->toDateString();
        $dueDate = !empty($options['due_date'])
            ? Carbon::parse($options['due_date'])->toDateString()
            : $invoiceDate->copy()->addDays((int) ($company->payment_due_days ?? 15))->toDateString();

        return DB::transaction(function () use ($company, $periodStart, $periodEnd, $invoiceDate, $dueDate, $options) {
            $lines = $this->buildInvoiceLines($company, $periodStart, $periodEnd);
            $subtotal = collect($lines)->sum('line_amount');

            $previousDue = (float) Invoice::where('company_id', $company->company_id)
                ->whereNotIn('status', ['paid', 'void'])
                ->sum('balance_amount');

            $taxAmount = (float) ($options['tax_amount'] ?? 0);
            $totalAmount = $subtotal + $taxAmount + $previousDue;

            if (empty($lines) && $totalAmount <= 0) {
                throw new \RuntimeException('No billable items found for the selected period. Please check the company billing setup.');
            }

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

    public function sendInvoicePdfToWhatsapp(Invoice $invoice, array $options = []): array
    {
        $invoice->loadMissing(['company', 'lines', 'payments', 'adjustments']);

        $company = $invoice->company;
        if (!$company) {
            throw new \RuntimeException('Invoice company not found.');
        }

        $to = $this->resolveWhatsAppNumber($company, $options['to'] ?? null);
        if (!$to) {
            return [
                'status' => 'skipped',
                'reason' => 'Company WhatsApp number is not configured.',
            ];
        }

        $templateName = (string) ($options['template'] ?? config('services.whatsapp.templates.billing_invoice', 'report'));
        $language = (string) ($options['language'] ?? config('services.whatsapp.template_lang', 'en'));

        if ($templateName === '') {
            return [
                'status' => 'skipped',
                'reason' => 'WhatsApp invoice template is not configured.',
            ];
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
                (string) $invoice->invoice_no,
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

            throw new \RuntimeException('WhatsApp send failed with status ' . $response->status() . '.');
        }

        Log::info('WhatsApp billing invoice sent successfully', [
            'invoice_id' => $invoice->id,
            'invoice_no' => $invoice->invoice_no,
            'company_id' => $company->company_id,
            'to' => $to,
            'filename' => $document['filename'],
        ]);

        return [
            'status' => 'sent',
            'to' => $to,
            'filename' => $document['filename'],
            'url' => $document['url'],
        ];
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

    private function buildInvoiceLines($company, $periodStart, $periodEnd)
    {
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

        if ($invoiceType === 'branch') {
            $branchCount = DB::table('branch')
                ->where('company_id', $company->company_id)
                ->where('status_id', 1)
                ->count();

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
            $terminalCount = DB::table('terminal_details')
                ->join('branch', 'terminal_details.branch_id', '=', 'branch.branch_id')
                ->where('branch.company_id', $company->company_id)
                ->where('branch.status_id', 1)
                ->count();

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
