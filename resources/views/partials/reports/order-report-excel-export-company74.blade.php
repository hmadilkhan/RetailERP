@if (isset($record))

    @php

        $detailColspan = 18;

    @endphp

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

                <th style="background-color: #1a4567;color:white;text-align: center;">Order #</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">Order Date</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">Order Time</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">Category</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">Branch</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">Terminal</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">Customer</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">Mobile</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">Phone</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">OrderType</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">Payment</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">Sales Person</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">Count/Total</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">Delivery Date</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">Status</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">Amount</th>

                <th style="background-color: #1a4567;color:white;text-align: center;">Department</th>

            </tr>

        </thead>

        <tbody>

            @if ($record)

                @php $totalItem = 0; @endphp

                @foreach ($record as $value)

                    @php

                        $totalItem++;

                        $orderMobile = trim((string) (!empty($value->customer) ? $value->customer->mobile : ''));

                        $orderPhone = trim((string) (!empty($value->customer) ? $value->customer->phone : ''));

                        $profileRows = $orderMobile !== '' ? ($customersByMobile[$orderMobile] ?? collect()) : collect();

                    @endphp

                    <tr>

                        <td style="text-align: center;">

                            {{ $value->web == 1 ? strtoupper($value->url_orderid) : $value->machine_terminal_count }}

                        </td>

                        <td style="text-align: center;">{{ $value->id }}</td>

                        <td style="text-align: center;">{{ date('d M Y', strtotime($value->date)) }}</td>

                        <td style="text-align: center;">{{ date('H:i a', strtotime($value->time)) }}</td>

                        <td style="text-align: center;">{{ $value->web == 1 ? 'Website' : 'POS' }}</td>

                        <td style="text-align: center;">{{ $value->branchrelation->branch_name }}</td>

                        <td style="text-align: center;">{{ $value->terminal->terminal_name }}</td>

                        <td style="text-align: left;">{{ !empty($value->customer) ? $value->customer->name : '-' }}</td>

                        <td style="text-align: left;">{{ $orderMobile !== '' ? $orderMobile : '-' }}</td>

                        <td style="text-align: left;">{{ $orderPhone !== '' ? $orderPhone : '-' }}</td>

                        <td style="text-align: center;">{{ !empty($value->mode) ? $value->mode->order_mode : '-' }}</td>

                        <td style="text-align: center;">

                            {{ !empty($value->payment) ? $value->payment->payment_mode : 0 }}</td>

                        <td style="text-align: center;">

                            {{ !empty($value->salesperson) ? $value->salesperson->fullname : 0 }}</td>

                        <td style="text-align: center;">{{ $value->orderdetails_count . '/' . $value->amount_sum }}</td>

                        <td style="text-align: center;">{{ date('d-m-Y', strtotime($value->delivery_date)) }}</td>

                        <td style="text-align: center;">{{ $value->orderStatus->order_status_name }}</td>

                        <td style="text-align: center;">{{ $value->total_amount }}</td>

                        <td style="text-align: center;">{{ $value->inventory_department ?? 'N/A' }}</td>

                    </tr>

                    @if ($profileRows->isNotEmpty())

                        <tr>

                            <td colspan="{{ $detailColspan }}"

                                style="background-color:#fff7ed;font-size:11px;font-weight:bold;color:#9a3412;padding:4px 8px;">

                                Customer profiles for mobile {{ $orderMobile }} ({{ $profileRows->count() }})

                            </td>

                        </tr>

                        <tr style="background-color:#f1f5f9;font-size:10px;font-weight:bold;">

                            <td colspan="7"></td>

                            <td style="text-align:center;">Customer #</td>

                            <td style="text-align:center;">Name</td>

                            <td style="text-align:center;">Branch</td>

                            <td style="text-align:center;">Mobile</td>

                            <td style="text-align:center;">Phone</td>

                            <td style="text-align:center;">NIC</td>

                            <td colspan="3" style="text-align:center;">Address</td>

                            <td style="text-align:center;">Membership</td>

                            <td style="text-align:center;">Status</td>

                        </tr>

                        @foreach ($profileRows as $profile)

                            <tr style="background-color:#f8fafc;font-size:10px;">

                                <td colspan="7"></td>

                                <td style="text-align:center;">{{ $profile->id }}</td>

                                <td style="text-align:left;">{{ $profile->name ?? '-' }}</td>

                                <td style="text-align:left;">{{ $profile->branch_name ?? '-' }}</td>

                                <td style="text-align:left;">{{ !empty($profile->mobile) ? $profile->mobile : '-' }}</td>

                                <td style="text-align:left;">{{ !empty($profile->phone) ? $profile->phone : '-' }}</td>

                                <td style="text-align:left;">{{ !empty($profile->nic) ? $profile->nic : '-' }}</td>

                                <td colspan="3" style="text-align:left;">{{ !empty($profile->address) ? $profile->address : '-' }}</td>

                                <td style="text-align:left;">{{ !empty($profile->membership_card_no) ? $profile->membership_card_no : '-' }}</td>

                                <td style="text-align:left;">{{ $profile->status_name ?? '-' }}</td>

                            </tr>

                        @endforeach

                    @endif

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

