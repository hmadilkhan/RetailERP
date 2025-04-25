<!DOCTYPE html>
<html lang="ur">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'jameel', sans-serif;
            direction: rtl;
            margin: 0;
            padding: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        /* Header Styling */
        .header-row td {
            font-size: 18px;
            font-weight: bold;
            padding: 10px;
            text-align: center;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        /* Table Header Styling */
        thead th {
            background-color: #1a4567;
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #0d2235;
            font-size: 14px;
        }

        /* Table Body Styling */
        tbody td {
            padding: 8px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-size: 13px;
        }

        /* Alternating Row Colors */
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        /* Total Row Styling */
        .total-header {
            background-color: black !important;
            color: white !important;
            font-size: 14px !important;
            font-weight: bold !important;
            text-align: center !important;
            padding: 10px !important;
        }

        .total-row td {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            border-top: 2px solid #dee2e6;
            background-color: #f8f9fa;
        }

        /* Text Alignment Classes */
        .text-center {
            text-align: center !important;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        /* Spacing Classes */
        .p-1 {
            padding: 4px !important;
        }

        .p-2 {
            padding: 8px !important;
        }

        .p-3 {
            padding: 16px !important;
        }
/* Add these styles to your existing CSS */
        .header-section {
            width: 100%;
            margin-bottom: 20px;
        }

        .logo-cell {
            width: 15%;
            vertical-align: middle;
            padding: 10px;
        }

        .info-cell {
            width: 70%;
            vertical-align: middle;
            padding: 10px;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #1a4567;
        }

        .branch-info {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .date-range {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
        }

        .qr-cell {
            width: 15%;
            vertical-align: middle;
            text-align: left;
            padding: 10px;
        }

        .logo-img {
            max-width: 100px;
            height: auto;
        }

        .qr-img {
            max-width: 80px;
            height: auto;
        }
    </style>
</head>

<body>
    @if (isset($record))
        <table>
            <!-- Header Section -->
            <td class="logo-cell">
                <img src="{{ asset('storage/images/company/' . $branch->company->logo) }}" class="logo-img"
                    alt="Company Logo">
            </td> 
            <tr>
                <td colspan="{{ $mode == 'normal' ? '10' : '8' }}" class="header-row">
                    {{ ' (' . $branch->code . ')' . $branch->branch_name . ' - ' . $branch->company->name }}
                </td>
            </tr>
            <tr>
                <td colspan="{{ $mode == 'normal' ? '10' : '8' }}" class="header-row">
                    From {{ date('d M Y', strtotime($dates['from'])) }} To {{ date('d M Y', strtotime($dates['to'])) }}
                </td>
            </tr>

            <!-- Table Headers -->
            <thead>
                <tr>
                    <th>Total Amount</th>
                    <th>Qty Sold</th>
                    <th>Qty/Cur</th>
                    <th>Total Qty</th>

                    @if ($mode == 'normal')
                        <th>Price</th>
                    @endif
                    <th>Qty</th>
                    <th colspan="2">Item Name</th>
                    <th>Article</th>
                    <th>Branch Code</th>

                </tr>
            </thead>

            <!-- Table Body -->
            <tbody>
                @if ($record)
                    @php
                        $actualQty = 0;
                        $totalQtySold = 0;
                        $calcTotalQty = 0;
                        $grandTotal = 0;
                        $totalQty = 0;
                        $gtotal = 0;
                    @endphp

                    @foreach ($record as $value)
                        @php
                            $itemqty = 0;
                            if (
                                !empty($value->inventory) &&
                                ($value->inventory->id == 817947 or $value->inventory->id == 817992)
                            ) {
                                $itemqty = $itemqty + $value->total_qty * 2;
                            } else {
                                $itemqty = $itemqty + $value->total_qty;
                            }
                            $actualQty = $actualQty + $value->total_qty;

                            $weight_qty =
                                !empty($value->inventory) && !empty($value->inventory->weight_qty)
                                    ? $value->inventory->weight_qty
                                    : 1;
                            $qty = $itemqty / $weight_qty;
                            $originalqty = $value->total_qty / $weight_qty;

                            $totalamount = $originalqty * $value->item_price;
                            $calcTotalQty = $calcTotalQty + $qty;
                            $totalQtySold = $totalQtySold + $itemqty;
                            $grandTotal = $grandTotal + $value->total_amount;

                            $totalQty = $value->total_qty * $weight_qty;
                            $gtotal += $totalQty;
                        @endphp

                        <tr>
                            <td>{{ number_format($value->total_amount, 2) }}</td>
                            <td>{{ $value->total_qty }}</td>
                            <td>{{ !empty($value->inventory) ? $value->inventory->weight_qty : 1 }}</td>
                            <td>{{ $totalQty }}</td>

                            @if ($mode == 'normal')
                                <td>{{ $value->item_price }}</td>
                            @endif
                            <td>{{ number_format($qty, 2) }}</td>
                            <td class="text-left p-1" colspan="2">{{ $value->inventory->product_name ?? '-' }}</td>
                            <td>{{ $value->inventory->item_code ?? '-' }}</td>
                            <td>{{ $value->order->branchrelation->code ?? '-' }}</td>
                        </tr>
                    @endforeach

                    <!-- Total Section -->
                    <tr>
                        <td colspan="{{ $mode == 'normal' ? '10' : '8' }}" class="total-header">
                            Total
                        </td>
                    </tr>
                    <tr class="total-row">
                        <td>{{ number_format($grandTotal, 2) }}</td>
                        <td>{{ number_format($actualQty, 2) }}</td>
                        <td></td>
                        <td>{{ number_format($gtotal, 2) }}</td>
                        <td></td>
                        <td>{{ number_format($calcTotalQty, 2) }}</td>
                        <td colspan="2"></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif
</body>

</html>
