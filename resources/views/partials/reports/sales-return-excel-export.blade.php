@if (isset($record))
    <table id="widget-product-list" class="table table-striped nowrap dt-responsive nowrap" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th colspan="7" style="text-align: center; font-size: 16px; font-weight: bold;">{{ $companyname }}</th>
            </tr>
            <tr>
                <th colspan="7" style="text-align: center; font-size: 14px;">Sales Return Report</th>
            </tr>
            <tr>
                <th colspan="7" style="text-align: center;">From: {{ date('d M Y', strtotime($dates['from'])) }} To: {{ date('d M Y', strtotime($dates['to'])) }}</th>
            </tr>
            <tr>
                <th style="font-weight: bold;">No</th>
                <th style="font-weight: bold;">Receipt</th>
                <th style="font-weight: bold;">Product Name</th>
                <th style="font-weight: bold;">Qty</th>
                <th style="font-weight: bold;">Amount</th>
                <th style="font-weight: bold;">Date</th>
                <th style="font-weight: bold;">Time</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalQty = 0;
                $totalAmount = 0;
            @endphp
            @foreach($record as $row)
                @php
                    $totalQty += $row->qty ?? 0;
                    $totalAmount += $row->amount;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row->order->receipt_no ?? 'N/A' }}</td>
                    <td>{{ $row->inventory->product_name }}</td>
                    <td>{{ $row->qty }}</td>
                    <td>{{ number_format($row->amount) }}</td>
                    <td>{{ date('d M Y', strtotime($row->timestamp)) }}</td>
                    <td>{{ date('h:i A', strtotime($row->timestamp)) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" style="font-weight: bold;">Total</td>
                <td style="font-weight: bold;">{{ number_format($totalQty) }}</td>
                <td style="font-weight: bold;">{{ number_format($totalAmount) }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
@endif
