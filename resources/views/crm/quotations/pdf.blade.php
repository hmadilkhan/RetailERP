<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $quotation->quotation_no }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1e293b;
            font-size: 12px;
            margin: 28px;
        }

        .header {
            margin-bottom: 22px;
            border-bottom: 2px solid #114a8f;
            padding-bottom: 14px;
        }

        .header h1 {
            margin: 0;
            color: #114a8f;
            font-size: 24px;
        }

        .muted {
            color: #64748b;
            font-size: 11px;
        }

        .grid {
            width: 100%;
            margin-bottom: 18px;
        }

        .grid td {
            vertical-align: top;
            padding: 6px 0;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        table.items th,
        table.items td {
            border: 1px solid #dbe5f0;
            padding: 9px 8px;
        }

        table.items th {
            background: #eff6ff;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 10px;
        }

        .totals {
            margin-top: 20px;
            width: 280px;
            margin-left: auto;
            border: 1px solid #dbe5f0;
            border-radius: 12px;
        }

        .totals td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .totals tr:last-child td {
            border-bottom: 0;
            font-weight: bold;
            color: #0a2d57;
            background: #eff6ff;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Quotation</h1>
        <div class="muted">Quotation No: {{ $quotation->quotation_no }}</div>
        <div class="muted">Generated On: {{ now()->format('d M Y h:i A') }}</div>
    </div>

    <table class="grid">
        <tr>
            <td width="50%">
                <strong>Prepared For</strong><br>
                {{ $lead->company_name ?: $lead->contact_person_name }}<br>
                {{ $lead->contact_person_name }}<br>
                {{ $lead->contact_number }}<br>
                {{ $lead->email ?: 'N/A' }}
            </td>
            <td width="50%">
                <strong>Quotation Details</strong><br>
                Date: {{ optional($quotation->quotation_date)->format('d M Y') }}<br>
                Valid Until: {{ optional($quotation->valid_until)->format('d M Y') ?: 'Open ended' }}<br>
                Status: {{ $quotation->statusLabel() }}<br>
                Lead Ref: {{ $lead->lead_code }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th width="28%">Item</th>
                <th width="34%">Description</th>
                <th width="12%">Qty</th>
                <th width="13%">Unit Price</th>
                <th width="13%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->description ?: 'N/A' }}</td>
                    <td>{{ number_format((float) $item->quantity, 2) }}</td>
                    <td>{{ number_format((float) $item->unit_price, 2) }}</td>
                    <td>{{ number_format((float) $item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td align="right">{{ number_format((float) $quotation->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Discount</td>
            <td align="right">{{ number_format((float) $quotation->discount, 2) }}</td>
        </tr>
        <tr>
            <td>Tax</td>
            <td align="right">{{ number_format((float) $quotation->tax, 2) }}</td>
        </tr>
        <tr>
            <td>Total</td>
            <td align="right">{{ number_format((float) $quotation->total, 2) }}</td>
        </tr>
    </table>

    <div style="margin-top: 28px;">
        <strong>Notes</strong>
        <div class="muted" style="margin-top: 8px; font-size: 12px; color: #1e293b;">
            {{ $quotation->notes ?: 'No additional notes provided.' }}
        </div>
    </div>
</body>

</html>
