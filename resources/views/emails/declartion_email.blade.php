<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt</title>
</head>

<body
    style="font-family: Arial, sans-serif; background-color: #f1f1f1; margin: 0; padding: 0; width: 100%; max-width: 600px; margin: 0 auto;">
    @php
        $CashInHand = 0;
        $positive =
            ($salesData['heads'][0]->bal ?? 0) +
            ($salesData['heads'][0]->order_delivered_cash ?? 0) +
            ($salesData['heads'][0]->Cash ?? 0) +
            ($salesData['heads'][0]->adv_booking_cash ?? 0) +
            ($salesData['heads'][0]->cashIn ?? 0);
        $negative =
            ($salesData['heads'][0]->Discount ?? 0) +
            ($salesData['heads'][0]->SalesReturn ?? 0) +
            ($salesData['heads'][0]->VoidReceipts ?? 0) +
            ($salesData['heads'][0]->cashOut ?? 0);
        $CashInHand =
            $positive -
            $negative +
            ($salesData['heads'][0]->CardCustomerDiscount ?? 0) +
            ($salesData['heads'][0]->Delivery ?? 0);
        if (isset($salesData['heads'][0]->expenses) && $salesData['heads'][0]->expenses == 1) {
            $CashInHand -= $salesData['heads'][0]->expenses;
        }
        if (session('company_id') == 102) {
            $CashInHand -= $salesData['heads'][0]->bal ?? 0;
        }
        $CashInHand = round($CashInHand);
        $closingBalance = round($salesData['heads'][0]->closingBal);
    @endphp

    <table width="100%" align="center" cellpadding="0" cellspacing="0"
        style="background-color: #f1f1f1; padding: 10px; margin: 0 auto;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0"
                    style="max-width: 600px; background-color: #ffffff; border: 1px solid #ddd; border-radius: 8px;">
                    <!-- Header -->
                    {{-- <tr>
                        <td style="padding: 10px;">
                            <img src="https://retail.sabsoft.com.pk/storage/images/sabifyemaillogo.png" width="150" height="80"  alt="Sabify Logo" width="100" height="auto"
                                style="display: inline-block; float: left;margin-top:24px;">
                            <img src="{{$logo}}"
                                alt="Kashees Logo" width="150" height="auto"
                                style="display: inline-block; float: right;margin-top:24px;">
                        </td>
                    </tr> --}}

                    <td style="padding:10px;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="table-layout: fixed;">
                            <tr>
                                <td style="width:50%; text-align:center; vertical-align: center;">
                                    <img src="https://retail.sabsoft.com.pk/storage/images/sabifyemaillogo.png" width="150" height="80" alt="Sabify Logo" style="display:inline-block; margin-top:24px;" />
                                </td>
                                {{-- <td style="width:50%; text-align:right; vertical-align: middle;">
                                    <img src="{{$logo}}" alt="Kashees Logo" style="display:inline-block; margin-top:24px;" />
                                </td> --}}
                            </tr>
                        </table>
                    </td>
                    

                    <!-- Sales Declaration -->
                    <tr>
                        <td style="padding: 0 20px; color: #333;">
                            <p style="font-weight: 600;">Hi, {{ $branchName }}!</p>
                            <p>Sales Declaration Report of Dated: {{date("d F Y",strtotime($date))}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h2
                                style="margin-top: 20px;text-align: center;font-weight: bold;font-size: 20px;border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 6px 0;">
                                Sales Declaration</h2>
                        </td>
                    </tr>

                    <!-- Transaction Info -->
                    <tr>
                        <td style="padding: 0 20px; color: #333;">
                            <table width="100%" cellpadding="5" cellspacing="0" style="font-size: 14px;">
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Opening DateTime:</strong></td>
                                    <td style="padding: 5px 0;text-align: right;">
                                        {{ date('d M Y', strtotime($salesData['heads'][0]->date)) . ' ' . date('H:i a', strtotime($salesData['heads'][0]->time)) ?? 0 }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Closing DateTime:</strong>
                                    </td>
                                    <td style="padding: 5px 0; text-align: right;">
                                        {{ date('d M Y', strtotime($salesData['heads'][0]->closingDate)) . ' ' . date('H:i a', strtotime($salesData['heads'][0]->closingTime)) ?? 0 }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Branch:</strong>
                                        {{ $salesData['terminal'][0]->branch_name }}</td>
                                    <td style="padding: 5px 0; text-align: right;"><strong>Terminal:</strong>
                                        {{ $salesData['terminal'][0]->terminal_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Declaration #:</strong>
                                        {{ $declaration ?? '-' }}</td>
                                    <td style="padding: 5px 0; text-align: right;">
                                        <strong>Closing Balance:</strong> {{$currency}}
                                        <span
                                            style="font-size:16px;font-weight:bold;">{{ number_format($closingBalance, 0) ?? 0 }}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Transaction Details -->
                    <tr>
                        <td style="padding: 20px;">
                            <table width="100%" cellpadding="5" cellspacing="0" style="font-size: 14px;">
                                <tr>
                                    <td colspan="2"
                                        style="font-size: 20px; font-weight: 600; padding-bottom: 10px;border-top: 1px solid #ddd;border-bottom: 1px solid #ddd;">
                                        Transaction
                                        Details</td>
                                </tr>
                                @if ($salesData['permissions'][0]->ob == 1)
                                    <tr>
                                        <td style="padding: 6px 0;">Opening Balance:</td>
                                        <td style="padding: 6px 0; text-align: right;">
                                            {{ number_format($salesData['heads'][0]->bal, 0) ?? 0 }}</td>
                                    </tr>
                                @endif
                                @if ($salesData['permissions'][0]->cash_sale == 1)
                                    <tr>
                                        <td style="padding: 6px 0;">Cash Sale:</td>
                                        <td style="padding: 6px 0; text-align: right;">
                                            {{ number_format($salesData['heads'][0]->Cash, 0) ?? 0 }}</td>
                                    </tr>
                                @endif
                                @if ($salesData['permissions'][0]->customer_credit_sale == 1)
                                    <tr>
                                        <td style="padding: 6px 0;">Credit Card Sale:</td>
                                        <td style="padding: 6px 0; text-align: right;">
                                            {{ number_format($salesData['heads'][0]->CreditCard, 0) ?? 0 }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding: 6px 0; font-weight: 600;font-size:20px;">Total Sales:</td>
                                    <td style="padding: 6px 0; text-align: right;font-weight: 600;font-size:20px;">
                                        {{ number_format($salesData['heads'][0]->TotalSales + $salesData['heads'][0]->credit_card_transaction, 0) ?? 0 }}
                                    </td>
                                </tr>
                                @if ($salesData['permissions'][0]->order_booking == 1)
                                    <tr>
                                        <td style="padding: 6px 0;">Adv Booking (Cash):</td>
                                        <td style="padding: 6px 0; text-align: right;">
                                            {{ number_format($salesData['heads'][0]->adv_booking_cash, 0) ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; ">Adv Booking (Card):</td>
                                        <td style="padding: 6px 0; text-align: right;">
                                            {{ number_format($salesData['heads'][0]->adv_booking_card, 0) ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0;">Order Delivered (Cash):</td>
                                        <td style="padding: 6px 0; text-align: right;">
                                            {{ number_format($salesData['heads'][0]->order_delivered_cash, 0) ?? 0 }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0;">Order Delivered (Card):</td>
                                        <td style="padding: 6px 0; text-align: right;">
                                            {{ number_format($salesData['heads'][0]->order_delivered_card, 0) ?? 0 }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($salesData['permissions'][0]->sale_return == 1)
                                    <tr>
                                        <td style="padding: 6px 0;">Sale Return:</td>
                                        <td style="padding: 6px 0; text-align: right;">
                                            {{ number_format($salesData['heads'][0]->SalesReturn, 0) ?? 0 }}</td>
                                    </tr>
                                @endif
                                @if ($salesData['permissions'][0]->void_receipt == 1)
                                    <tr>
                                        <td style="padding: 6px 0;">Void Receipts:</td>
                                        <td style="padding: 6px 0; text-align: right;">
                                            {{ number_format($salesData['heads'][0]->VoidReceipts, 0) ?? 0 }}</td>
                                    </tr>
                                @endif
                                @if ($salesData['permissions'][0]->discount == 1)
                                    <tr>
                                        <td style="padding: 6px 0;">Discount:</td>
                                        <td style="padding: 6px 0; text-align: right;">
                                            {{ number_format($salesData['heads'][0]->Discount, 0) ?? 0 }}</td>
                                    </tr>
                                @endif
                                @if ($salesData['permissions'][0]->cash_in == 1)
                                    <tr>
                                        <td style="padding: 6px 0;">Cash In:</td>
                                        <td style="padding: 6px 0; text-align: right;">
                                            {{ number_format($salesData['heads'][0]->cashIn, 0) ?? 0 }}</td>
                                    </tr>
                                @endif
                                @if ($salesData['permissions'][0]->cash_out == 1)
                                    <tr>
                                        <td style="padding: 6px 0;">Cash Out:</td>
                                        <td style="padding: 6px 0; text-align: right;">
                                            {{ number_format($salesData['heads'][0]->cashOut, 0) ?? 0 }}</td>
                                    </tr>
                                @endif
                                @if ($salesData['permissions'][0]->expenses == 1)
                                    <tr>
                                        <td style="padding: 6px 0;">Expense:</td>
                                        <td style="padding: 6px 0; text-align: right;">
                                            {{ number_format($salesData['heads'][0]->expenses, 0) ?? 0 }}</td>
                                    </tr>
                                @endif
                                @if ($salesData['permissions'][0]->fbr_sync == 1)
                                    <tr>
                                        <td style="padding: 6px 0;">FBR (TAX):</td>
                                        <td style="padding: 6px 0; text-align: right;">
                                            {{ number_format($salesData['heads'][0]->fbr, 0) ?? 0 }}</td>
                                    </tr>
                                @endif
                                @if ($salesData['permissions'][0]->srb_sync == 1)
                                    <tr>
                                        <td style="padding: 6px 0;">SRB (TAX):</td>
                                        <td style="padding: 6px 0; text-align: right;">
                                            {{ number_format($salesData['heads'][0]->srb, 0) ?? 0 }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding: 6px 0; font-weight: 600;font-size:20px;">Cash In Hand:</td>
                                    <td style="padding: 6px 0; text-align: right; font-weight: 600;font-size:20px;">
                                        {{ number_format($CashInHand, 0) }}</td>
                                </tr>
                                @if ($salesData['permissions'][0]->cb == 1)
                                    <tr>
                                        <td style="padding: 6px 0;">Closing Balance:</td>
                                        <td style="padding: 6px 0; text-align: right;color:{{$closingBalance == $CashInHand ? 'green;' : 'red;'}}">
                                            {{ number_format($closingBalance, 0) ?? 0 }}
                                            {{ $closingBalance > $CashInHand ? '(' . ($closingBalance - $CashInHand) . ' Amount Excess)' : '' }}
                                            {{ $closingBalance < $CashInHand ? '(' . ($closingBalance - $CashInHand) . ' Amount Short)' : '' }}
                                        </td>
                                    </tr>
                                @endif
                                <!-- Repeat rows as needed for all fields -->
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 6px 0;text-align: center;font-weight:bold;color:red;">
                            <i><strong>Note : This report may vary from the original declaration. </strong><i>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color: #539a3c; color: #ffffff; text-align: center; padding:15px 0px; font-size: 15px;">
                            <p style="margin: 0;">Details of this report can also be viewed on the Sabify app.</p>
                            <p style="margin: 0;">Thank you for using <a href="https://sabify.pk/">Sabify!</a></p>
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="background-color: #539a3c; color: #ffffff; text-align: center; padding:5px 0px; font-size: 15px;">
                            <p style="margin: 0;font-weight:bold;margin-top:2px;">Sabify Karo Simplify Karo !</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
