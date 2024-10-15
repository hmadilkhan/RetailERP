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
                <th style="background-color: #1a4567;color:white;text-align: center;">DateTime</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Opening Id</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Shift Opening</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Cash Sales</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Card Sales</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Booking Sales</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Total Sales</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Adv. Booking - Cash</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Adv. Booking - Card</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Order Delivered - Cash</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Order Delivered - Card</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Discount</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Cash In</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Cash Out</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Sales Return</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Void Sales</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Expenses</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Cash In Hand</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Closing</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Difference</th>
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
                    $cashInHand = 0;
                    ?>
                    <tr>
                        <td style="text-align: center;">
                            {{ date('d M Y', strtotime($value->date)) . ' ' . date('H:i a', strtotime($value->date)) }}
                        </td>
                        <td style="text-align: center;">{{ $value->opening_id }}</td>
                        <td style="text-align: center;">{{ $value->bal }}</td>
                        <td style="text-align: center;">{{ $value->Cash }}</td>
                        <td style="text-align: center;">{{ $value->CreditCard }}</td>
                        <td style="text-align: center;">{{ $value->booking }}</td>
                        <td style="text-align: left;">{{ $value->TotalSales }}</td>
                        <td style="text-align: left;">{{ $value->adv_booking_cash }}</td>
                        <td style="text-align: left;">{{ $value->adv_booking_card }}</td>
                        <td style="text-align: left;">{{ $value->order_delivered_cash }}</td>
                        <td style="text-align: left;">{{ $value->order_delivered_card }}</td>
                        <td style="text-align: left;">{{ $value->Discount }}</td>
                        <td style="text-align: left;">{{ $value->cashIn }}</td>
                        <td style="text-align: left;">{{ $value->cashOut }}</td>
                        <td style="text-align: left;">{{ $value->SalesReturn }}</td>
                        <td style="text-align: left;">{{ $value->voidSales }}</td>
                        <td style="text-align: left;">{{ $value->Expenses }}</td>
                        <td style="text-align: left;">{{ $cashInHand }}</td>
                        <td style="text-align: left;">{{ $value->closingBal }}</td>
                        <td style="text-align: left;"></td>

                    </tr>
                @endforeach
                {{-- <tr>
                    <td style="font-size:12px;font-weight:bold;text-align: center;">{{ number_format($totalItem, 2) }}
                    </td>
                    <td style="font-size:12px;font-weight:bold;text-align: center;"></td>
                    <td style="font-size:12px;font-weight:bold;text-align: center;" colspan="3"></td>
                    <td style="font-size:12px;font-weight:bold;text-align: center;"></td>
                    <td style="font-size:12px;font-weight:bold;text-align: center;"></td>
                    <td style="font-size:12px;font-weight:bold;text-align: center;"></td>
                    <td style="font-size:12px;font-weight:bold;text-align: center;"></td>
                    <td style="font-size:12px;font-weight:bold;text-align: center;"></td>
                </tr> --}}
            @endif

        </tbody>
    </table>
@endif
