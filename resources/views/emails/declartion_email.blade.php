<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f1f1f1; margin: 0; padding: 20px;">
@php
$CashInHand = 0;
$positive = ($salesData["heads"][0]->bal ?? 0) +($salesData["heads"][0]->order_delivered_cash ?? 0)  + ($salesData["heads"][0]->Cash ?? 0) + ($salesData["heads"][0]->adv_booking_cash ?? 0) + ($salesData["heads"][0]->cashIn ?? 0);
$negative =  ($salesData["heads"][0]->Discount ?? 0) + ($salesData["heads"][0]->SalesReturn ?? 0) + ($salesData["heads"][0]->cashOut ?? 0); 

$CashInHand = $positive - $negative + ($salesData["heads"][0]->CardCustomerDiscount ?? 0) + ($salesData["heads"][0]->Delivery ?? 0);
if (isset($salesData["heads"][0]->expenses) && $salesData["heads"][0]->expenses == 1) {
    $CashInHand = $CashInHand - $salesData["heads"][0]->expenses;
}
if (session('company_id') == 102) {
    $CashInHand = $CashInHand - ($salesData["heads"][0]->bal ?? 0);
}
$CashInHand = round($CashInHand);
$closingBalance = round($salesData["heads"][0]->closingBal);    
@endphp
    <table align="center" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f1f1f1; padding: 20px;">
        <tr>
            <td>
                <table align="center" width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border: 1px solid #ddd; border-radius: 8px;">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #539a3c; text-align: center; padding: 50px;">
                            <img src="https://sabify.pk/images/logo.png" alt="Sabify Logo" style="display: block; margin: 0 auto;">
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 20px; color: #333;">
                            <p style="font-weight: 600;">Hi, {{$branchName}}!</p>
                            <p>Sales Declaration Report of Dated : 10 October 2024 is given below.</p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 20px; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">
                                <tr>
                                    <td width="50%" style="text-align: center; padding: 10px 0; font-weight: bold;font-size: 24px;">
                                        <span>SALES DECLARATION </span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Transaction Info -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 6px;">
                                <tr>
                                    <td colspan="2" style="padding: 15px 0;">
                                        <strong>Opening DateTime:</strong>
                                    </td>
    
                                    <td colspan="2" style="padding: 15px 0;text-align: right;">
                                        <strong>Closing DateTime:</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding: 5px 0;border-bottom: 1px solid #ddd;"  >
                                         {{date("d M Y",strtotime($salesData["heads"][0]->date))." ".date("H:i a",strtotime($salesData["heads"][0]->time)) ?? 0}}
                                    </td>
    
                                    <td colspan="2" style="padding: 5px 0;text-align: right;border-bottom: 1px solid #ddd;">
                                        {{date("d M Y",strtotime($salesData["heads"][0]->closingDate))." ".date("H:i a",strtotime($salesData["heads"][0]->closingTime)) ?? 0}}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding: 10px 0 solid #ddd;">
                                        <strong>Branch :</strong> {{$salesData["terminal"][0]->branch_name}}
                                    </td>
    
                                    <td colspan="2" style="padding: 10px 0;text-align: right; solid #ddd;">
                                        <strong>Terminal :</strong> <span style="">{{$salesData["terminal"][0]->terminal_name ?? "-"}}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding: 10px 0; border-bottom: 1px solid #ddd;">
                                        <strong>Declaration #:</strong> {{$declaration ?? "-"}}
                                    </td>
    
                                    <td colspan="2" style="padding: 10px 0;text-align: right;border-bottom: 1px solid #ddd;">
                                        <strong>Closing Balance :</strong> <span style="color: red; font-weight: bold; font-size: 24px;">Rs. {{number_format($salesData["heads"][0]->closingBal,0) ?? 0}}</span>
                                    </td>
                                </tr>
                                
                            </table>

                            <!-- Transaction Details -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 20px;">
                                <tr>
                                    <td colspan="2" style="font-size: 24px; font-weight: bold; padding-bottom: 10px;">Transaction Details</td>
                                </tr>
                                @if($salesData["permissions"][0]->ob == 1)
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold;">Opening Balance:</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->bal,0) ?? 0}}</td>
                                </tr>
                                @endif
                                @if($salesData["permissions"][0]->cash_sale == 1)
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold;">Cash Sale:</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->Cash,0) ?? 0}}</td>
                                </tr>
                                @endif
                                @if($salesData["permissions"][0]->customer_credit_sale == 1)
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold;">Credit Card Sale:</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->CreditCard,0) ?? 0}}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 10px 0; font-weight: 600;font-size:24px;">Total Sales:</td>
                                    <td style="padding: 10px 0; text-align: right;font-weight: 600;font-size:24px;">{{number_format($salesData["heads"][0]->TotalSales + $salesData["heads"][0]->credit_card_transaction,0) ?? 0}}</td>
                                </tr>
                                @if($salesData["permissions"][0]->order_booking == 1)
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold;">Adv Booking (Cash):</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->adv_booking_cash,0) ?? 0}}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold; ">Adv Booking (Card):</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->adv_booking_card,0) ?? 0}}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold;">Order Delivered (Cash):</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->order_delivered_cash,0) ?? 0}}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold;">Order Delivered (Card):</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->order_delivered_card,0) ?? 0}}</td>
                                </tr>
                                @endif
                                @if($salesData["permissions"][0]->sale_return == 1)
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold;">Sale Return:</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->SalesReturn,0) ?? 0}}</td>
                                </tr>
                                @endif
                                @if($salesData["permissions"][0]->void_receipt == 1)
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold;">Void Receipts:</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->VoidReceipts,0) ?? 0}}</td>
                                </tr>
                                @endif
                                @if($salesData["permissions"][0]->discount == 1)
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold;">Discount:</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->Discount,0) ?? 0}}</td>
                                </tr>
                                @endif
                                @if($salesData["permissions"][0]->cash_in == 1)
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold;">Cash In:</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->cashIn,0) ?? 0}}</td>
                                </tr>
                                @endif
                                @if($salesData["permissions"][0]->cash_out == 1)
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold;">Cash Out:</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->cashOut,0) ?? 0}}</td>
                                </tr>
                                @endif
                                @if($salesData["permissions"][0]->expenses == 1)
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold;">Expense:</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->expenses,0) ?? 0}}</td>
                                </tr>
                                @endif
                                @if($salesData["permissions"][0]->fbr_sync == 1)
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold;">FBR (TAX):</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->fbr,0) ?? 0}}</td>
                                </tr>
                                @endif
                                @if($salesData["permissions"][0]->srb_sync == 1)
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold;">SRB (TAX):</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->srb,0) ?? 0}}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 10px 0; font-weight: 600;font-size:24px;">Cash In Hand:</td>
                                    <td style="padding: 10px 0; text-align: right; font-weight: 600;font-size:24px;">{{number_format($CashInHand,0)}}</td>
                                </tr>
                                @if($salesData["permissions"][0]->cb == 1)
                                <tr>
                                    <td style="padding: 10px 0;font-weight:bold;">Closing Balance:</td>
                                    <td style="padding: 10px 0; text-align: right;">{{number_format($salesData["heads"][0]->closingBal,0) ?? 0}}</td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #539a3c; color: #ffffff; text-align: center; padding: 15px; font-size: 15px;">
                            <p style="margin: 1;">Details of this report can also be viewed on the Sabify app.</p>
                            <p style="margin: 1;">Thank you for using Sabify!</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>