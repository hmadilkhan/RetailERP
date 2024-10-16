@if (isset($record))
    <table id="widget-product-list" class="table table-striped nowrap dt-responsive nowrap" width="100%" cellspacing="0">
        <tr>
            <td colspan="8" style="font-size:18px;font-weight:bold;">
                {{ $branch->company->name . ' - ' . $branch->branch_name . ' (' . $branch->code . ')' }}</td>
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
                <th style="background-color: #1a4567;color:white;text-align: center;">Total With Card</th>
            </tr>
        </thead>
        <tbody>
            @if ($record)
                <?php
                $totalItem = 0;
                $totalOpenings = 0;
                $totalCashSales = 0;
                $totalCardSales = 0;
                $totalBookingSales = 0;
                $totalSales = 0;
                $totalAdvBookingCash = 0;
                $totalAdvBookingCard = 0;
                $totalOrderDeliveredCash = 0;
                $totalOrderDeliveredCard = 0;
                $totalDiscount = 0;
                $totalCashIn = 0;
                $totalCashOut = 0;
                $totalCashOut = 0;
                $totalSalesReturn = 0;
                $totalVoidSales = 0;
                $totalExpenses = 0;
                $totalCashInHand = 0;
                $totalClosing = 0;
                $totalDifference = 0;
                $totalSalesWithCard = 0;
                
                ?>
                @foreach ($record as $value)
                    <?php
                    $totalItem++;
                    $cashInHand = $value->bal + $value->Cash + $value->sale_tax + $value->service_tax + $value->cashIn + $value->adv_booking_cash + $value->order_delivered_cash - ($value->cashOut + $value->Expenses + $value->Discount + $value->SalesReturn); //- $value->Discount - $value->promo - $value->coupon;
                    $balance = (float) $value->closingBal - (float) $cashInHand;
                    $totalCashWithCard = $cashInHand + $value->CreditCard + $value->adv_booking_card + $value->order_delivered_card;
                    $totalOpenings += $value->bal;
                    $totalCashSales += $value->Cash;
                    $totalCardSales += $value->CreditCard;
                    $totalBookingSales += $value->booking;
                    $totalSales += $value->TotalSales;
                    $totalAdvBookingCash += $value->adv_booking_cash;
                    $totalAdvBookingCard += $value->adv_booking_card;
                    $totalOrderDeliveredCash += $value->order_delivered_cash;
                    $totalOrderDeliveredCard += $value->order_delivered_card;
                    $totalDiscount += $value->Discount;
                    $totalCashIn += $value->cashIn;
                    $totalCashOut += $value->cashOut;
                    $totalSalesReturn += $value->SalesReturn;
                    $totalVoidSales += $value->voidSales;
                    $totalExpenses += $value->Expenses;
                    $totalCashInHand += $cashInHand;
                    $totalClosing += $value->closingBal;
                    $totalDifference += $balance ;
                    $totalSalesWithCard += $totalCashWithCard;
                    ?>
                    <tr>
                        <td style="text-align: center;" >
                            {{ date('d M Y', strtotime($value->date)) . ' ' . date('h:i a', strtotime($value->time)) }}
                        </td>
                        <td style="text-align: center;" >{{ number_format($value->opening_id, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->bal, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->Cash, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->CreditCard, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->booking, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->TotalSales, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->adv_booking_cash, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->adv_booking_card, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->order_delivered_cash, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->order_delivered_card, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->Discount, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->cashIn, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->cashOut, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->SalesReturn, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->voidSales, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->Expenses, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($cashInHand, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($value->closingBal, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($balance, 0) }}</td>
                        <td style="text-align: center;">{{ number_format($totalCashWithCard, 0) }}</td>

                    </tr>
                @endforeach
                <tr>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;" colspan="2">Total</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalOpenings,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalCashSales,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalCardSales,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalBookingSales,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalSales,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalAdvBookingCash,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalAdvBookingCard,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalOrderDeliveredCash,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalOrderDeliveredCard,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalDiscount,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalCashIn,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalCashOut,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalSalesReturn,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalVoidSales,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalExpenses,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalCashInHand,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalClosing,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalDifference,0)}}</td>
                    <td style="background-color: #1a4567;color:white;color:font-size:12px;font-weight:bold;text-align: center; border: 1px solid black;">{{number_format($totalSalesWithCard,0)}}</td>
                </tr>
            @endif

        </tbody>
    </table>
@endif
