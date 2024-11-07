@if (isset($record))
    <table id="widget-product-list" class="table table-striped nowrap dt-responsive nowrap" width="100%" cellspacing="0">
        <tr>
            <td colspan="11" style="font-size:18px;font-weight:bold;">
                {{ $company }}</td>
        </tr>
        <thead>
            <tr>
                <th style="background-color: #1a4567;color:white;text-align: center;">S.No #</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Terminal</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Receipt No</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Customer Name</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Booking Date</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Booking Amount</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Advance Amount</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Received Amount</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Received Date</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Payment Mode</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Sales Person</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalBookingAmount = 0;
                $totalAdvanceAmount = 0;
                $totalReceivedAmount = 0;
            @endphp
            @if ($record)
                @foreach ($record as $key => $value)
                    @php
                        $totalBookingAmount += $value->total_amount;
                        $totalAdvanceAmount += $value->paid;
                        $totalReceivedAmount += $value->receive_amount;
                    @endphp
                    <tr>
                        <td style="text-align: left;">{{ ++$key }}</td>
                        <td style="text-align: left;">{{ $value->terminal_name }}</td>
                        <td style="text-align: left;">{{ $value->receipt_no }}</td>
                        <td style="text-align: left;">{{ $value->name }}</td>
                        <td style="text-align: center;">{{ date('Y-m-d', strtotime($value->date)) }}</td>
                        <td style="text-align: left;">{{ number_format($value->total_amount, 0) }}</td>
                        <td style="text-align: left;">{{ number_format($value->paid, 0) }}</td>
                        <td style="text-align: left;">{{ number_format($value->receive_amount, 0) }}</td>
                        <td style="text-align: center;">{{ date('Y-m-d', strtotime($value->received_date)) }}</td>
                        <td style="text-align: left;">{{ $value->payment_mode }}</td>
                        <td style="text-align: left;">{{ $value->fullname }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="5" style="background-color: #1a4567;color:white;text-align: center;">Total</td>
                    <td style="background-color: #1a4567;color:white;text-align: center;">
                        {{ number_format($totalBookingAmount, 0) }}</td>
                    <td style="background-color: #1a4567;color:white;text-align: center;">
                        {{ number_format($totalAdvanceAmount, 0) }}</td>
                    <td style="background-color: #1a4567;color:white;text-align: center;">
                        {{ number_format($totalReceivedAmount, 0) }}</td>
                    <td colspan="3" style="background-color: #1a4567;color:white;text-align: center;"></td>
                </tr>
            @endif
        </tbody>
    </table>
@endif
