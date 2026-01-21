@if (isset($record))
    <table id="widget-product-list" class="table table-striped nowrap dt-responsive nowrap" width="100%" cellspacing="0">
        <tr>
            <td colspan="8" style="font-size:18px;font-weight:bold;">
                {{ $branch[0]->company->name . ' - ' . $branch[0]->branch_name . ' (' . $branch[0]->code . ')' }}</td>
        </tr>
        <tr>
            <td colspan="8" style="font-size:18px;font-weight:bold;">From {{ $dates['from'] }} To {{ $dates['to'] }}
            </td>
        </tr>
        <tr colspan="8"></tr>
        <thead>
            <tr>
                <th style="background-color: #1a4567;color:white;text-align: center;">Machine / Website #</th>
                @if (session('company_id') != 102)
                    <th style="background-color: #1a4567;color:white;text-align: center;">Order #</th>
                @endif
                @if (session('company_id') == 102)
                    <th style="background-color: #1a4567;color:white;text-align: center;">Receipt #</th>
                @endif
                <th style="background-color: #1a4567;color:white;text-align: center;">Order Date</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Order Time</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Category</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Branch</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Terminal</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Customer</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Mobile</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">OrderType</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Payment</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Sales Person</th>
                @if (session('company_id') != 102)
                    <th style="background-color: #1a4567;color:white;text-align: center;">Count/Total</th>
                @endif
                <th style="background-color: #1a4567;color:white;text-align: center;">Delivery Date</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Status</th>
                @if (session('company_id') != 102)
                    <th style="background-color: #1a4567;color:white;text-align: center;">Amount</th>
                @endif
                @if (session('company_id') == 102)
                    <th style="background-color: #1a4567;color:white;text-align: center;">Total</th>
                    <th style="background-color: #1a4567;color:white;text-align: center;">Receive</th>
                    <th style="background-color: #1a4567;color:white;text-align: center;">Balance</th>
                    <th style="background-color: #1a4567;color:white;text-align: center;">Picked Person Name</th>
                    <th style="background-color: #1a4567;color:white;text-align: center;">Picked Person Contact</th>
                    <th style="background-color: #1a4567;color:white;text-align: center;">Picked Up At</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if ($record)
                <?php
                $totalItem = 0;
                
                ?>
                @foreach ($record as $value)
                    <?php
                    $totalItem++;
                    
                    ?>
                    <tr>
                        <td style="text-align: center;">
                            {{ $value->web == 1 ? strtoupper($value->url_orderid) : $value->machine_terminal_count }}
                        </td>
                        @if (session('company_id') != 102)
                            <td style="text-align: center;">{{ $value->id }}</td>
                        @endif
                        @if (session('company_id') == 102)
                            <td style="text-align: center;">{{ $value->receipt_no }}</td>
                        @endif
                        <td style="text-align: center;">{{ date('d M Y', strtotime($value->date)) }}</td>
                        <td style="text-align: center;">{{ date('H:i a', strtotime($value->time)) }}</td>
                        <td style="text-align: center;">{{ $value->web == 1 ? 'Website' : 'POS' }}</td>
                        <td style="text-align: center;">{{ $value->branchrelation->branch_name }}</td>
                        <td style="text-align: center;">{{ $value->terminal->terminal_name }}</td>
                        <td style="text-align: left;">{{ !empty($value->customer) ? $value->customer->name : '-' }}
                        <td style="text-align: left;">{{ !empty($value->customer) ? $value->customer->mobile : '-' }}
                        </td>
                        <td style="text-align: center;">{{ !empty($value->mode) ? $value->mode->order_mode : '-' }}</td>
                        <td style="text-align: center;">
                            {{ !empty($value->payment) ? $value->payment->payment_mode : 0 }}</td>
                        <td style="text-align: center;">
                            {{ !empty($value->salesperson) ? $value->salesperson->fullname : 0 }}</td>
                        @if (session('company_id') != 102)
                            <td style="text-align: center;">{{ $value->orderdetails_count . '/' . $value->amount_sum }}
                            </td>
                        @endif
                        <td style="text-align: center;">{{ date('d-m-Y', strtotime($value->delivery_date)) }}</td>
                        <td style="text-align: center;">{{ $value->orderStatus->order_status_name }}</td>
                        @if (session('company_id') != 102)
                            <td style="text-align: center;">{{ $value->total_amount }}</td>
                        @else
                            <td style="text-align: center;">{{ $value->actual_amount }}</td>
                            <td style="text-align: center;">
                                {{ $value->payment_id == 3 ? $value->orderAccount->receive_amount : $value->total_amount }}
                            </td>
                            <td style="text-align: center;">
                                {{ $value->payment_id == 3 ? $value->actual_amount - $value->orderAccount->receive_amount : 0 }}
                            </td>
                        @endif
                        @if (session('company_id') == 102 && $value->orderStatus->order_status_name == 'Order Picked Up')
                            @foreach ($value->statusLogs as $log)
                                @if ($log->status_id == 10)
                                    <td style="text-align: center;">{{ $log->name }}</td>
                                    <td style="text-align: center;">{{ $log->mobile }}</td>
                                    <td style="text-align: center;">
                                        {{ date('d M Y', strtotime($log->date)) . ' ' . date('h:i a', strtotime($log->time)) }}
                                    </td>
                                @endif
                            @endforeach
                        @endif
                    </tr>
                @endforeach
                <tr>
                    <td style="font-size:12px;font-weight:bold;text-align: center;">{{ number_format($totalItem, 2) }}
                    </td>
                    <td style="font-size:12px;font-weight:bold;text-align: center;"></td>
                    <td style="font-size:12px;font-weight:bold;text-align: center;" colspan="3"></td>
                    <td style="font-size:12px;font-weight:bold;text-align: center;"></td>
                    <td style="font-size:12px;font-weight:bold;text-align: center;"></td>
                    <td style="font-size:12px;font-weight:bold;text-align: center;"></td>
                    <td style="font-size:12px;font-weight:bold;text-align: center;"></td>
                    <td style="font-size:12px;font-weight:bold;text-align: center;"></td>
                </tr>
            @endif

        </tbody>
    </table>
@endif
