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
        .page-break { page-break-before: always; }
        .attachment-page { page-break-inside: avoid; }
        .attachment-grid { width: 100%; border-collapse: separate; border-spacing: 10px 12px; table-layout: fixed; }
        .attachment-grid td { width: 50%; vertical-align: top; }
        .screenshot-card { border: 1px solid #d9dee5; border-radius: 10px; padding: 10px; height: 330px; page-break-inside: avoid; }
        .screenshot-title { font-size: 12px; font-weight: bold; color: #2c3e50; margin-bottom: 8px; }
        .screenshot-frame { width: 100%; height: 270px; border: 1px solid #edf1f5; text-align: center; vertical-align: middle; }
        .screenshot-frame td { vertical-align: middle; text-align: center; }
        .screenshot-image { max-width: 100%; max-height: 250px; width: auto; height: auto; }
        .screenshot-meta { margin-top: 6px; font-size: 10px; color: #666; }
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

    <div class="section-title">Yearly Invoice Summary {{ \Carbon\Carbon::parse($voucher->payment_date)->format('Y') }}</div>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 16%;">Invoice No</th>
                <th style="width: 30%;">Period</th>
                <th style="width: 9%;" class="text-right">Months</th>
                <th style="width: 15%;" class="text-right">Total Amount</th>
                <th style="width: 15%;" class="text-right">Paid Amount</th>
                <th style="width: 15%;" class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($yearlyInvoices as $row)
            <tr>
                <td>{{ $row['invoice_no'] }}</td>
                <td>{{ $row['period'] }}</td>
                <td class="text-right">{{ $row['months'] }}</td>
                <td class="text-right">PKR {{ number_format($row['total_amount'], 2) }}</td>
                <td class="text-right">PKR {{ number_format($row['paid_amount'], 2) }}</td>
                <td class="text-right">PKR {{ number_format($row['balance_amount'], 2) }}</td>
            </tr>
            @endforeach
            @if($yearlyInvoices->isEmpty())
            <tr>
                <td colspan="6" class="text-right">No invoices found for this year.</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td class="label">Total Invoice Amount:</td>
                <td class="text-right">PKR {{ number_format($yearlyInvoices->sum('total_amount'), 2) }}</td>
            </tr>
            <tr>
                <td class="label">Total Paid:</td>
                <td class="text-right">PKR {{ number_format($yearlyInvoices->sum('paid_amount'), 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td>Total Balance:</td>
                <td class="text-right">PKR {{ number_format($yearlyInvoices->sum('balance_amount'), 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Thank you. This voucher confirms receipt and allocation of your payment.</p>
        <p>This is a computer-generated voucher and does not require a signature.</p>
    </div>

    @if(($screenshots ?? collect())->isNotEmpty())
        @foreach($screenshots->chunk(4) as $pageIndex => $screenshotPage)
            <div class="page-break"></div>

            <div class="attachment-page">
                <div class="header" style="margin-top: 0;">
                    <table>
                        <tr>
                            <td style="width: 60%;">
                                <div class="title" style="text-align: left; font-size: 22px;">Payment Screenshots</div>
                                <div class="subtitle" style="text-align: left;">Reference page {{ $pageIndex + 1 }} for voucher #{{ $voucher->voucher_no }}</div>
                            </td>
                            <td style="width: 40%; vertical-align: top; text-align: right;">
                                <div class="section-title" style="text-align: right;">Attachment Summary</div>
                                <span class="info-label">Count:</span> {{ $screenshots->count() }}<br>
                                <span class="info-label">Page:</span> {{ $pageIndex + 1 }} of {{ $screenshots->chunk(4)->count() }}<br>
                                <span class="info-label">Payment Date:</span> {{ date('M d, Y', strtotime($voucher->payment_date)) }}
                            </td>
                        </tr>
                    </table>
                </div>

                <table class="attachment-grid">
                    @foreach($screenshotPage->chunk(2) as $row)
                        <tr>
                            @foreach($row as $index => $screenshot)
                                <td>
                                    <div class="screenshot-card">
                                        <div class="screenshot-title">Screenshot {{ ($pageIndex * 4) + $loop->parent->index * 2 + $loop->iteration }}</div>
                                        <table class="screenshot-frame">
                                            <tr>
                                                <td>
                                                    <img src="{{ $screenshot['data_uri'] }}" class="screenshot-image" alt="{{ $screenshot['name'] }}">
                                                </td>
                                            </tr>
                                        </table>
                                        <div class="screenshot-meta">{{ $screenshot['name'] }}</div>
                                    </div>
                                </td>
                            @endforeach
                            @if($row->count() === 1)
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                    @if($screenshotPage->count() <= 2)
                        <tr>
                            <td colspan="2" style="border: 0; padding: 0;"></td>
                        </tr>
                    @endif
                </table>
            </div>
        @endforeach
    @endif
</body>
</html>
