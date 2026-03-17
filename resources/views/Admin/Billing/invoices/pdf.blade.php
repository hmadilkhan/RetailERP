<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        @page { margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
        .header { margin-bottom: 15px; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; }
        .header table { width: 100%; border-collapse: collapse; }
        .company-logo { max-width: 180px; height: auto; margin-bottom: 10px; }
        .company-details { font-size: 11px; color: #666; line-height: 1.5; }
        .invoice-title { font-size: 32px; color: #2c3e50; text-align: right; margin-bottom: 5px; }
        .invoice-number { font-size: 14px; color: #666; text-align: right; }
        .status-badge { display: inline-block; padding: 5px 15px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-paid { background-color: #27ae60; color: white; }
        .status-partial { background-color: #f39c12; color: white; }
        .status-issued { background-color: #3498db; color: white; }
        .status-void { background-color: #e74c3c; color: white; }
        .invoice-details { margin: 15px 0; }
        .invoice-details table { width: 100%; border-collapse: collapse; }
        .section-title { font-size: 13px; font-weight: bold; color: #2c3e50; margin-bottom: 8px; text-transform: uppercase; }
        .info-label { font-weight: bold; }
        table.items { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table.items thead { background-color: #2c3e50; color: white; }
        table.items th { padding: 10px 8px; text-align: left; font-size: 11px; text-transform: uppercase; font-weight: bold; }
        table.items td { padding: 8px; border-bottom: 1px solid #ddd; font-size: 11px; }
        table.items tbody tr:nth-child(even) { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .totals { width: 100%; margin-top: 15px; }
        .totals table { width: 300px; float: right; border-collapse: collapse; }
        .totals td { padding: 8px 12px; font-size: 12px; }
        .totals .label { font-weight: bold; }
        .totals .grand-total { background-color: #2c3e50; color: white; font-weight: bold; font-size: 13px; }
        .totals .balance { font-weight: bold; color: #e74c3c; font-size: 13px; }
        .notes { clear: both; margin-top: 20px; padding-top: 15px; }
        .notes-title { font-weight: bold; margin-bottom: 8px; font-size: 12px; }
        .notes-content { background-color: #f9f9f9; padding: 12px; border-left: 3px solid #2c3e50; font-size: 11px; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; text-align: center; font-size: 10px; color: #666; clear: both; }
    </style>
</head>
<body>
    <!-- Header -->
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
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-number">#{{ $invoice->invoice_no }}</div>
                    <div style="text-align: right; margin-top: 5px;">
                        <span class="status-badge status-{{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Invoice Details -->
    <div class="invoice-details">
        <table>
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class="section-title">Bill To</div>
                    <strong>{{ optional($invoice->company)->name }}</strong><br>
                    @if(optional($invoice->company)->address){{ $invoice->company->address }}<br>@endif
                    @if(optional($invoice->company)->city){{ $invoice->company->city }}, {{ optional($invoice->company)->state }} {{ optional($invoice->company)->zip }}@endif
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <div class="section-title" style="text-align: right;">Invoice Information</div>
                    <div style="text-align: right;">
                        <span class="info-label">Period:</span> {{ date('M d, Y', strtotime($invoice->period_start)) }} - {{ date('M d, Y', strtotime($invoice->period_end)) }}<br>
                        <span class="info-label">Generation Date:</span> {{ date('M d, Y', strtotime($invoice->invoice_date)) }}<br>
                        <span class="info-label">Due Date:</span> {{ date('M d, Y', strtotime($invoice->due_date)) }}<br>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Invoice Items -->
    <table class="items">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">Description</th>
                {{-- <th style="width: 15%;">Type</th> --}}
                <th style="width: 10%;" class="text-right">Qty</th>
                <th style="width: 12%;" class="text-right">Unit Price</th>
                <th style="width: 13%;" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->lines as $index => $line)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $line->description }}</td>
                {{-- <td>{{ ucfirst($line->scope_type) }}</td> --}}
                <td class="text-center">{{ number_format($line->qty, 2) }}</td>
                <td class="text-center">PKR {{ number_format($line->unit_price, 2) }}</td>
                <td class="text-center">PKR {{ number_format($line->line_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <table>
            <tr>
                <td class="label">Subtotal:</td>
                <td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->tax_amount > 0)
            <tr>
                <td class="label">Tax:</td>
                <td class="text-right">${{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            @endif
            @if($invoice->previous_due > 0)
            <tr>
                <td class="label">Previous Due:</td>
                <td class="text-right">${{ number_format($invoice->previous_due, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td>Total Amount:</td>
                <td class="text-right">${{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
            @if($invoice->paid_amount > 0)
            <tr>
                <td class="label">Paid Amount:</td>
                <td class="text-right">${{ number_format($invoice->paid_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="balance">
                <td>Balance Due:</td>
                <td class="text-right">${{ number_format($invoice->balance_amount, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Notes -->
    @if($invoice->notes)
    <div class="notes">
        <div class="notes-title">Notes:</div>
        <div class="notes-content">{{ $invoice->notes }}</div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This is a computer-generated invoice and does not require a signature.</p>
    </div>
</body>
</html>
