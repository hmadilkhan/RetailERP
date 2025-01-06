@if (isset($record))
    <table id="widget-product-list" class="table table-striped nowrap dt-responsive nowrap" width="100%" cellspacing="0">
        <tr>
            <td colspan="6" style="font-size:18px;font-weight:bold;">
                {{ $company }}</td>
        </tr>
        <tr>
            <td colspan="6" style="font-size:18px;font-weight:bold;">
                Customer Sales Report</td>
        </tr>
        <tr>
            <td colspan="6" style="font-size:18px;font-weight:bold;">
                From : {{$dates["from"]}} - To : {{$dates["to"]}}</td>
        </tr>
        <thead>
            <tr>
                <th style="background-color: #1a4567;color:white;text-align: center;">S.No #</th>
                <th style="background-color: #1a4567;color:white;text-align: left;">Customer Name</th>
                <th style="background-color: #1a4567;color:white;text-align: left;">Branch Name</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Contact Number</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Membership Number</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Total Orders</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Total Sales</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalOrders = 0;
                $totalAmount = 0;
            @endphp
            @if ($record)
                @foreach ($record as $key => $value)
                    @php
                        $totalOrders += $value->total_orders;
                        $totalAmount += $value->total_sales;
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ ++$key }}</td>
                        <td style="text-align: left;">{{ $value->name }}</td>
                        <td style="text-align: left;">{{ $value->branch_name }}</td>
                        <td style="text-align: left;">{{ $value->mobile }}</td>
                        <td style="text-align: left;">{{ $value->membership_card_no }}</td>
                        <td style="text-align: left;">{{ number_format($value->total_orders, 0) }}</td>
                        <td style="text-align: left;">{{ number_format($value->total_sales, 0) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" style="background-color: #1a4567;color:white;text-align: center;">Total</td>
                    <td style="background-color: #1a4567;color:white;text-align: left;">
                        {{ number_format($totalOrders, 0) }}</td>
                    <td style="background-color: #1a4567;color:white;text-align: left;">
                        {{ number_format($totalAmount, 0) }}</td>
                </tr>
            @endif
        </tbody>
    </table>
@endif
