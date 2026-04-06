<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Voucher {{ $voucher->voucher_no }}</title>
    <style>
        @page { margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
        .header { margin-bottom: 15px; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; }
        .header table { width: 100%; border-collapse: collapse; }
        .company-logo { max-width: 180px; height: auto; margin-bottom: 10px; }
        .company-details { font-size: 11px; color: #666; line-height: 1.5; }
        .title { font-size: 26px; color: #2c3e50; text-align: right; margin-bottom: 5px; }
        .subtitle { font-size: 13px; color: #666; text-align: right; }
        .section { margin: 15px 0; }
        .section table { width: 100%; border-collapse: collapse; }
        .section-title { font-size: 13px; font-weight: bold; color: #2c3e50; margin-bottom: 8px; text-transform: uppercase; }
        .info-label { font-weight: bold; }
        table.items { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table.items thead { background-color: #2c3e50; color: white; }
        table.items th { padding: 10px 8px; text-align: left; font-size: 11px; text-transform: uppercase; }
        table.items td { padding: 8px; border-bottom: 1px solid #ddd; font-size: 11px; }
        table.items tbody tr:nth-child(even) { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .totals { width: 100%; margin-top: 15px; }
        .totals table { width: 340px; float: right; border-collapse: collapse; }
        .totals td { padding: 8px 12px; font-size: 12px; }
        .totals .label { font-weight: bold; }
        .totals .grand-total { background-color: #2c3e50; color: white; font-weight: bold; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; text-align: center; font-size: 10px; color: #666; clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td style="width: 60%;">
                    <img src="{{ asset('storage/images/Sabify.fw.png') }}" class="company-logo" alt="Sabify Logo">
                    <div class="company-details">
                        Park Avenue, Ground Floor, Showroom No. 7-A, 24-A,<br>
                        P.E.C.H.S, Main Shahrah-e-Faisal Lal Kothi Bus Stop <br>
                        Karachi, Sindh, Pakistan 75400<br>
                        Phone: 021-34389215-16-17<br>
                        Email: info@sabify.pk
                    </div>
                </td>
                <td style="width: 40%; vertical-align: top;">
                    <div class="title">PAYMENT RECEIVE VOUCHER</div>
                    <div class="subtitle">#{{ $voucher->voucher_no }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table>
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class="section-title">Bill To</div>
                    <strong>{{ optional($voucher->company)->name }}</strong><br>
                    @if(optional($voucher->company)->address){{ $voucher->company->address }}<br>@endif
                    @if(optional($voucher->company)->city){{ $voucher->company->city }}, {{ optional($voucher->company)->state }} {{ optional($voucher->company)->zip }}@endif
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <div class="section-title" style="text-align: right;">Voucher Information</div>
                    <span class="info-label">Payment Date:</span> {{ date('M d, Y', strtotime($voucher->payment_date)) }}<br>
                    <span class="info-label">Payment Mode:</span> {{ optional($voucher->paymentMode)->payment_mode ?? 'N/A' }}<br>
                    <span class="info-label">Reference #:</span> {{ $voucher->reference_no ?: '-' }}<br>
                    <span class="info-label">Narration:</span> {{ $voucher->narration ?: '-' }}
                </td>
            </tr>
        </table>
    </div>

    @php
        $groupedPayments = $voucher->invoicePayments->groupBy('invoice_id')->map(function ($payments) {
            $invoice = $payments->first()->invoice;
            if (!$invoice) {
                return null;
            }

            $months = \Carbon\Carbon::parse($invoice->period_start)->startOfMonth()
                ->diffInMonths(\Carbon\Carbon::parse($invoice->period_end)->startOfMonth()) + 1;

            return [
                'invoice_no' => $invoice->invoice_no,
                'period' => date('M d, Y', strtotime($invoice->period_start)) . ' to ' . date('M d, Y', strtotime($invoice->period_end)),
                'months' => $months,
                'invoice_total' => (float) $invoice->total_amount,
                'paid_amount' => (float) $payments->sum('amount'),
                'balance_amount' => (float) $invoice->balance_amount,
            ];
        })->filter()->values();
    @endphp

    <table class="items">
        <thead>
            <tr>
                <th style="width: 18%;">Invoice No</th>
                <th style="width: 28%;">Period</th>
                <th style="width: 10%;" class="text-right">Months</th>
                <th style="width: 15%;" class="text-right">Invoice Total</th>
                <th style="width: 15%;" class="text-right">Paid</th>
                <th style="width: 14%;" class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedPayments as $row)
            <tr>
                <td>{{ $row['invoice_no'] }}</td>
                <td>{{ $row['period'] }}</td>
                <td class="text-right">{{ $row['months'] }}</td>
                <td class="text-right">PKR {{ number_format($row['invoice_total'], 2) }}</td>
                <td class="text-right">PKR {{ number_format($row['paid_amount'], 2) }}</td>
                <td class="text-right">PKR {{ number_format($row['balance_amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td class="label">Total Invoice Amount:</td>
                <td class="text-right">PKR {{ number_format($groupedPayments->sum('invoice_total'), 2) }}</td>
            </tr>
            <tr>
                <td class="label">Total Paid:</td>
                <td class="text-right">PKR {{ number_format($groupedPayments->sum('paid_amount'), 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td>Total Balance:</td>
                <td class="text-right">PKR {{ number_format($groupedPayments->sum('balance_amount'), 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Thank you. This voucher confirms receipt and allocation of your payment.</p>
        <p>This is a computer-generated voucher and does not require a signature.</p>
    </div>
</body>
</html>
