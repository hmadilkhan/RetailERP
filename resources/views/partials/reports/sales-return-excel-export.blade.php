@if (isset($record))
    <table id="widget-product-list" class="table table-striped nowrap dt-responsive nowrap" width="100%" cellspacing="0">
        <tr>
            <td colspan="7" style="font-size:18px;font-weight:bold;">
                {{ $companyname  }}</td>
        </tr>
        <tr>
            <td colspan="7" style="font-size:18px;font-weight:bold;">From {{ $dates['from'] }} To {{ $dates['to'] }}
            </td>
        </tr>
        <thead>
            <tr>
                <th style="background-color: #1a4567;color:white;text-align: center;">Terminal Name</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Receipt No</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Product Name </th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Qty</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Amount</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Date</th>
                <th style="background-color: #1a4567;color:white;text-align: center;">Time</th>
            </tr>
        </thead>
        <tbody>
            @if ($record)
                <?php
                $totalItem = 0;
                $totalAmount = 0;
                $totalQty = 0;
  
                ?>
                @foreach ($record as $value)
                    <?php
                    $totalItem++;
                    $totalQty += $value->qty;
                    $totalAmount += $value->amount;
                    ?>
                    <tr>
                        <td style="text-align: center;" >{{ $value->terminal_name }}</td>
                        <td style="text-align: center;" >{{ $value->receipt_no }}</td>
                        <td style="text-align: center;" >{{ $value->product_name }}</td>
                        <td style="text-align: center;" >{{ $value->qty }}</td>
                        <td style="text-align: center;">{{ number_format($value->amount, 0) }}</td>
                        <td style="text-align: center;">{{ date("d M Y",strtotime($value->date, 0)) }}</td>
                        <td style="text-align: center;">{{ date("h:i:s",strtotime($value->time, 0)) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;" colspan="3">Total</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalQty,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalAmount,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;" colspan="2"></td>
                </tr>
            @endif

        </tbody>
    </table>
@endif
