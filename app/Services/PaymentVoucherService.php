<?php

namespace App\Services;

use App\Models\Company;
use App\Models\PaymentVoucher;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class PaymentVoucherService
{
    public function createVoucherNumber(Carbon $paymentDate): string
    {
        $seriesPrefix = 'PRV-' . $paymentDate->format('Ym');

        $lastVoucher = PaymentVoucher::where('voucher_no', 'like', $seriesPrefix . '%')
            ->orderByDesc('voucher_no')
            ->first();

        $sequence = 1;
        if ($lastVoucher) {
            $lastSequence = (int) substr((string) $lastVoucher->voucher_no, -4);
            $sequence = $lastSequence + 1;
        }

        return $seriesPrefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function storeVoucherPdf(PaymentVoucher $voucher): array
    {
        $voucher->loadMissing(['company', 'paymentMode', 'invoicePayments.invoice']);

        $pdf = Pdf::loadView('Admin.Billing.payments.voucher-pdf', [
            'voucher' => $voucher,
        ]);

        $filename = 'payment-voucher-' . preg_replace('/[^A-Za-z0-9._-]/', '-', (string) $voucher->voucher_no) . '.pdf';
        $directory = 'pdfs/payment-vouchers';
        $path = $directory . '/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());

        $voucher->pdf_path = $path;
        $voucher->save();

        return [
            'filename' => $filename,
            'path' => $path,
            'url' => url(Storage::disk('public')->url($path)),
        ];
    }

    public function sendVoucherToWhatsapp(PaymentVoucher $voucher, array $options = []): array
    {
        $voucher->loadMissing(['company', 'paymentMode', 'invoicePayments.invoice']);

        $company = $voucher->company;
        if (!$company) {
            throw new \RuntimeException('Payment voucher company not found.');
        }

        $to = $this->resolveWhatsAppNumber($company, $options['to'] ?? null);
        if (!$to) {
            $voucher->whatsapp_status = 'skipped';
            $voucher->save();

            return [
                'status' => 'skipped',
                'reason' => 'Company WhatsApp number is not configured.',
            ];
        }

        $templateName = (string) ($options['template'] ?? config('services.whatsapp.templates.billing_invoice', 'report'));
        $language = (string) ($options['language'] ?? config('services.whatsapp.template_lang', 'en'));
        if ($templateName === '') {
            $voucher->whatsapp_status = 'skipped';
            $voucher->save();

            return [
                'status' => 'skipped',
                'reason' => 'WhatsApp voucher template is not configured.',
            ];
        }

        $document = $this->storeVoucherPdf($voucher);
        $response = $this->sendWhatsAppTemplateWithDocument(
            $to,
            $templateName,
            $language,
            $document['url'],
            $document['filename'],
            [
                (string) $company->name,
                'Payment Receive Voucher',
                $voucher->voucher_no,
            ]
        );

        if (!$response->successful()) {
            $voucher->whatsapp_status = 'failed';
            $voucher->whatsapp_to = $to;
            $voucher->save();

            Log::warning('WhatsApp payment voucher send failed', [
                'voucher_id' => $voucher->id,
                'voucher_no' => $voucher->voucher_no,
                'company_id' => $company->company_id,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \RuntimeException('WhatsApp send failed with status ' . $response->status() . '.');
        }

        $voucher->whatsapp_status = 'sent';
        $voucher->whatsapp_to = $to;
        $voucher->whatsapp_sent_at = now();
        $voucher->save();

        return [
            'status' => 'sent',
            'to' => $to,
            'filename' => $document['filename'],
            'url' => $document['url'],
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
}
