<?php $positive = (isset($heads[0]->bal) ? $heads[0]->bal : 0) +(isset($heads[0]->order_delivered_cash) ? $heads[0]->order_delivered_cash : 0)  + (isset($heads[0]->Cash) ? $heads[0]->Cash : 0) + (isset($heads[0]->adv_booking_cash) ? $heads[0]->adv_booking_cash : 0) + (isset($heads[0]->cashIn) ? $heads[0]->cashIn : 0) + (isset($heads[0]->TaxCash) ? $heads[0]->TaxCash : 0);

$negative =  (isset($heads[0]->DiscountCash) ? $heads[0]->DiscountCash : 0)  + (isset($heads[0]->SalesReturn) ? $heads[0]->SalesReturn : 0) + (isset($heads[0]->cashOut) ? $heads[0]->cashOut : 0) + (isset($heads[0]->VoidReceiptsCash) ? $heads[0]->VoidReceiptsCash : 0); //+ $heads[0]->CreditCard +$heads[0]->CustomerCredit;
// Raza na ye Void ka code 22-11-2024 ko add kraya ha 
$CashInHand = $positive - $negative + $heads[0]->Delivery;// (isset($heads[0]->CardCustomerDiscount) ? $heads[0]->CardCustomerDiscount : 0) ;
$totalVoidReceipts = (isset($heads[0]->VoidReceiptsCash) ? $heads[0]->VoidReceiptsCash : 0) + (isset($heads[0]->VoidReceiptsCard) ? $heads[0]->VoidReceiptsCard : 0)  + (isset($heads[0]->VoidReceiptsBooking) ? $heads[0]->VoidReceiptsBooking : 0);
if (isset($result[0]->expenses) && $result[0]->expenses == 1) {
    $CashInHand = $CashInHand - $heads[0]->expenses;
}

if (session('company_id') == 102) {
    $CashInHand = $CashInHand - (isset($heads[0]->bal) ? $heads[0]->bal : 0);
}
$CashInHand = round($CashInHand);
$closingBalance = round($heads[0]->closingBal);
//$heads[0]->CashReturn + $heads[0]->CardReturn + $heads[0]->ChequeReturn
?>
@if (count($heads) > 0)
    <div class="col-xl-12 col-sm-12 col-md-12 mt-2">
        <div class="text-center text-primary">
            <div class="col-xl-4 col-md-4 col-sm-12 text-center mb-4">
                <span class="tag tag-success f-24 text-center" style="cursor:pointer;"
                    onclick="cashDetails('{{ Crypt::encrypt(isset($heads[0]->opening_id) ? $heads[0]->opening_id : '') }}','{{ Crypt::encrypt(isset($heads[0]->terminal_id) ? $heads[0]->terminal_id : '') }}','isdb')">Item
                    Sales</span>
            </div>
            <div class="col-xl-4 col-md-4 col-sm-12 mb-4">
                <h3 id="terminalName">
                    {{ isset($terminal_name[0]->terminal_name) ? $terminal_name[0]->terminal_name : '' }}
                </h3>
            </div>
            <div class="col-xl-4 col-md-4 col-sm-12 mb-4">
                <span id="status"
                    class="tag tag-{{ isset($heads[0]->status) && $heads[0]->status == 2 ? 'danger' : 'success' }} f-24 ">
                    {{ isset($heads[0]->status) && $heads[0]->status == 2 ? 'CLOSED' : 'ACTIVE' }}</span>
            </div>
            <div class="col-xl-4 col-md-4 col-sm-12 text-center">
                @if (isset($heads[0]->closingBal) && $heads[0]->closingBal == 0)
                    @if (isset($result[0]->ob) && $result[0]->ob == 0)
                        <span class="tag tag-danger f-24 " style="cursor:pointer"
                            onclick="closedTerminal('{{ Crypt::encrypt(isset($heads[0]->opening_id) ? $heads[0]->opening_id : '') }}','{{ Crypt::encrypt(isset($heads[0]->terminal_id) ? $heads[0]->terminal_id : '') }}')">Close
                            Terminal</span>
                    @endif
                @else
                    @if (isset($result[0]->cb) && $result[0]->cb == 0)
                        <span class="tag tag-success f-24 " style="cursor:pointer"
                            onclick="openTerminal('{{ isset($heads[0]->user_id) ? $heads[0]->user_id : '' }}','{{ isset($heads[0]->terminal_id) ? $heads[0]->terminal_id : '' }}')">Open
                            Terminal</span>
                    @endif
                @endif
            </div>

        </div>
    </div>

    <br />
    <div class=" col-xl-12 col-sm-12 col-md-12 dashboard-header mt-4 ">
        <div class="col-xl-4 col-lg-6 col-md-6  col-sm-12">
            <div class="card tilebox-one">
                <div class="card-header">OPENING </div>
                <div class="card-body">
                    <h2>{{ session('currency') }} {{ number_format(isset($heads[0]->bal) ? $heads[0]->bal : 0, 0) }}
                    </h2>
                    <span class="badge bg-primary me-2 fs-sm-1 fs-md-3 fs-lg-5"> Opening Date : </span> <span
                        class=" badge text-muted fs-sm-1 fs-md-3 fs-lg-5">{{ date('d M Y', strtotime($heads[0]->date)) . ' ' . date('h:i A', strtotime($heads[0]->time)) }}</span>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6  col-sm-12">
            <div class="card tilebox-one">
                <div class="card-header">TOTAL SALES</div>
                <div class="card-body">
                    <h2>{{ session('currency') }}
                        {{ number_format($heads[0]->TotalSales + $heads[0]->credit_card_transaction, 0) }}</h2>
                    <span class="badge bg-warning me-2">Sales </span>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6  col-sm-12">
            <div class="card tilebox-one">
                <div class="card-header">CLOSING</div>
                <div class="card-body">
                    <h2>{{ session('currency') }} {{ number_format($heads[0]->closingBal, 0) }}</h2>
                    <span class="badge bg-primary me-2"> Closing Date : </span> <span class="text-muted">
                        {{ $heads[0]->closingBal > 0 ? date('d M Y', strtotime($heads[0]->closingDate)) . ' ' . date('h:i A', strtotime($heads[0]->closingTime)) : '' }}</span>
                </div>
            </div>
        </div>
    </div>

    <br />
    <div class="col-xl-12 col-lg-12 col-md-12 dashboard-header m-t-5">
        <div class="col-xl-4 col-lg-6 col-md-6 grid-item">
            <div class="card">
                <div class="row">
                    <div class="col-sm-12 d-flex">
                        <div class="col-sm-5 bg-warning">
                            <div class="p-10 text-center">
                                <i class="icofont icofont-cur-dollar f-64"></i>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="">
                                <h3 id="takeaway" class="txt-warning  clock">{{ session('currency') }}
                                    {{ number_format($heads[0]->TakeAway, 0) }}</h3>
                                <span class="text-default  f-16">TAKE AWAY</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 grid-item">
            <div class="card">
                <div class="row">
                    <div class="col-sm-12 d-flex">
                        <div class="col-sm-5 bg-info">
                            <div class="p-10 text-center">
                                <i class="icofont icofont-cur-dollar f-64"></i>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="">
                                <h3 id="online" class="txt-warning clock ">{{ session('currency') }} 0</h3>
                                <span class="text-default f-16">ONLINE</span>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 grid-item">
            <div class="card">
                <div class="row">
                    <div class="col-sm-12 d-flex">
                        <div class="col-sm-5 bg-primary">
                            <div class="p-10 text-center">
                                <i class="icofont icofont-cur-dollar f-64"></i>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="">
                                <h3 id="delivery" class="txt-warning clock ">{{ session('currency') }}
                                    {{ number_format($heads[0]->Delivery, 0) }}</h3>
                                <span class="text-default  f-16">COD (DELIVERY)</span>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-12 row justify-content-center">
        <div class="col-auto">
            <table
                class="table table-responsive nowrap table-striped nowrap dt-responsive table-bordered m-t-10  no-footer dtr-inline">
                @if (isset($result[0]->ob) && $result[0]->ob == 1)
                    <tr id="ob">
                        <td style="width:500px">Opening Balance</td>
                        <td id="opening" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format($heads[0]->bal, 0) }}</td>
                    </tr>
                @endif

                @if (isset($result[0]->cash_sale) && $result[0]->cash_sale == 1)
                    <tr id="cash_sale"
                        onClick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}',1 )">
                        <td style="width:500px; cursor: pointer;">Cash Sale</td>
                        <td id="cashSales" class="text-end" style="width:500px">{{ session('currency') }}
                            {{ number_format($heads[0]->Cash, 0) }}</td>
                    </tr>
                @endif
                @if (isset($result[0]->card_sale) && $result[0]->card_sale == 1)
                    <tr id="card_sale"
                        onClick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}',2)">
                        <td style="width:500px;cursor: pointer;">Credit Card Sale</td>
                        <td id="creditCard" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format($heads[0]->CreditCard, 0) }}</td>
                    </tr>
                @endif
                @if (isset($result[0]->customer_credit_sale) && $result[0]->customer_credit_sale == 1)
                    <tr id="customer_credit_sale"
                        onClick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}',3)">
                        <td style="width:500px;cursor: pointer;">Customer Credit Sale</td>
                        <td id="customerCredit" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format($heads[0]->CustomerCredit, 0) }}</td>
                    </tr>
                @endif
                @if (isset($result[0]->wallets_sales) && $result[0]->wallets_sales == 1)
                    <tr id="wallets_sales"
                        onClick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}',8 )">
                        <td style="width:500px; cursor: pointer;">Wallet Sale</td>
                        <td id="cashSales" class="text-end" style="width:500px">{{ session('currency') }}
                            {{ number_format($heads[0]->WalletSales, 0) }}</td>
                    </tr>
                @endif

                <tr class="f-24 form-control-label">
                    <td style="width:500px">Total Sales</td>
                    <td id="Sales" style="width:500px" class="text-end">{{ session('currency') }}
                        {{ number_format($heads[0]->TotalSales + $heads[0]->credit_card_transaction, 0) }}</td>
                </tr>

                @if (isset($result[0]->cost) && $result[0]->cost == 1)
                    <tr id="cost">
                        <td style="width:500px">Total Receipt Item Cost</td>
                        <td id="totalCost" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format($heads[0]->cost, 0) }}</td>
                    </tr>
                @endif
                
                @if (isset($result[0]->cost) && $result[0]->cost == 1)
                <tr id="paidBycustomer">
                    <td style="width:500px">Paid By Customer (Customer Credit)</td>
                    <td id="totalCost" style="width:500px" class="text-end">{{ session('currency') }}
                        {{ number_format($heads[0]->paidByCustomer, 0) }}</td>
                </tr>
                @endif
                @if (isset($result[0]->order_booking) && $result[0]->order_booking == 1)
                <tr>
                    <td style="width:500px">Adv Booking (Cash)</td>
                    <td id="totalCost" style="width:500px" class="text-end">{{ session('currency') }}
                        {{ number_format($heads[0]->adv_booking_cash, 0) }}</td>
                </tr>

                <tr>
                    <td style="width:500px">Adv Booking (Card)</td>
                    <td id="totalCost" style="width:500px" class="text-end">{{ session('currency') }}
                        {{ number_format($heads[0]->adv_booking_card, 0) }}</td>
                </tr>

                <tr>
                    <td style="width:500px">Order Delivered (Cash)</td>
                    <td id="totalCost" style="width:500px" class="text-end">{{ session('currency') }}
                        {{ number_format($heads[0]->order_delivered_cash, 0) }}</td>
                </tr>
                
                <tr>
                    <td style="width:500px">Order Delivered (Card)</td>
                    <td id="totalCost" style="width:500px" class="text-end">{{ session('currency') }}
                        {{ number_format($heads[0]->order_delivered_card, 0) }}</td>
                </tr>
                @endif
                @if (isset($result[0]->r_cash) && $result[0]->r_cash == 1)
                    <tr id="r_cash">
                        <td style="width:500px">Customer Credit Return Cash</td>
                        <td id="cashReturn" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format($heads[0]->CashReturn, 0) }}</td>
                    </tr>
                @endif
                @if (isset($result[0]->r_card) && $result[0]->r_card == 1)
                    <tr id="r_card">
                        <td style="width:500px">Customer Credit Return Credit</td>
                        <td id="cardReturn" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format($heads[0]->CardReturn, 0) }}</td>
                    </tr>
                @endif
                
                @if (isset($result[0]->r_cheque) && $result[0]->r_cheque == 1)
                    <tr id="r_cheque">
                        <td style="width:500px">Customer Credit Return Cheque</td>
                        <td id="chequeReturn" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format($heads[0]->ChequeReturn, 0) }}</td>
                    </tr>
                @endif
                @if (isset($result[0]->sale_return) && $result[0]->sale_return == 1)
                    <tr id="sale_return" style="cursor: pointer;"
                        onClick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}','sr')">
                        <td style="width:500px">Sale Return</td>
                        <td id="salesReturn" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format($heads[0]->SalesReturn, 0) }}</td>
                    </tr>
                @endif
                @if (isset($result[0]->void_receipt) && $result[0]->void_receipt == 1)
                    <tr id="discounthead">
                        <td style="width:500px">Void Receipts (Cash)</td>
                        <td id="void_receipt" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format(isset($heads[0]->VoidReceiptsCash) ? $heads[0]->VoidReceiptsCash : 0, 0) }}</td>
                    </tr>
                    <tr id="discounthead">
                        <td style="width:500px">Void Receipts (Card)</td>
                        <td id="void_receipt" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format(isset($heads[0]->VoidReceiptsCard) ? $heads[0]->VoidReceiptsCard : 0, 0) }}</td>
                    </tr>
                    <tr id="discounthead">
                        <td style="width:500px">Void Receipts (Booking)</td>
                        <td id="void_receipt" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format(isset($heads[0]->VoidReceiptsBooking) ? $heads[0]->VoidReceiptsBooking : 0, 0) }}</td>
                    </tr>
                @endif
                @if (isset($result[0]->discount) && $result[0]->discount == 1)
                    <tr id="discounthead">
                        <td style="width:500px">Discount</td>
                        <td id="discount" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format($heads[0]->Discount, 0) }}</td>
                    </tr>
                @endif
                @if (isset($result[0]->cash_in) && $result[0]->cash_in == 1)
                    <tr id="cash_in" style="cursor: pointer;"
                        onClick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}','ci')">
                        <td style="width:500px">Cash In</td>
                        <td id="cashIn" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format($heads[0]->cashIn, 0) }}</td>
                    </tr>
                @endif
                @if (isset($result[0]->cash_out) && $result[0]->cash_out == 1)
                    <tr id="cash_out" style="cursor: pointer;"
                        onClick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}','co')">
                        <td style="width:500px">Cash Out</td>
                        <td id="cashOut" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format($heads[0]->cashOut, 0) }}</td>
                    </tr>
                @endif
                @if (isset($result[0]->expenses) && $result[0]->expenses == 1)
                    <tr id="expense" style="cursor: pointer;"
                        onClick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}','ex')">
                        <td style="width:500px">Expense</td>
                        <td id="totalCost" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format($heads[0]->expenses, 0) }}</td>
                    </tr>
                @endif
                @if (isset($result[0]->fbr_sync) && $result[0]->fbr_sync == 1)
                    <tr id="expense" style="cursor: pointer;">
                        <td style="width:500px">FBR (TAX)</td>
                        <td id="totalCost" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format($heads[0]->fbr, 0) }}</td>
                    </tr>
                @endif
                @if (isset($result[0]->srb_sync) && $result[0]->srb_sync == 1)
                    <tr id="expense" style="cursor: pointer;">
                        <td style="width:500px">SRB (TAX)</td>
                        <td id="totalCost" style="width:500px" class="text-end">{{ session('currency') }}
                            {{ number_format($heads[0]->srb, 0) }} </td>
                    </tr>
                @endif
                <tr class="f-24 form-control-label">
                    <td style="width:500px">Cash In Hand</td>
                    <td id="CIH" style="width:500px" class="text-end">{{ session('currency') }}
                        {{ number_format($CashInHand, 0) }}
                    </td>
                </tr>

                @if ($heads[0]->closingBal > 0)
                    <tr>
                        <td style="width:500px">Closing Balance</td>
                        <td id="CIH" style="width:500px"
                            class="{{ $closingBalance == $CashInHand ? 'text-success' : 'text-danger' }} text-end">
                            {{ session('currency') }} {{ $closingBalance }}
                            {{ $closingBalance > $CashInHand ? '(' . ($closingBalance - $CashInHand) . ' Amount Excess)' : '' }}
                            {{ $closingBalance < $CashInHand ? '(' . ($closingBalance - $CashInHand) . ' Amount Short)' : '' }}
                        </td>
                    </tr>
                @endif
            </table>
        </div>
    </div>
@else
    <div class="col-md-12">
        <div class="text-center text-primary">
            <p><strong>There is no Record</strong></p>
        </div>
    </div>
@endif
<script>
    function cashDetails(opening, terminal, mode) {
        window.open("{{ url('sales-show') }}/" + opening + "/" + terminal + "/" + mode);
    }

    function openTerminal(branchId, terminalId) {
        let amount = prompt("Please enter amount to open");
        if (amount == null || amount == "") {
            text = "User cancelled the prompt.";
        } else {

            $.ajax({
                url: "{{ url('/open-terminal') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    terminal: terminalId,
                    branch: branchId,
                    amount: amount
                },
                success: function(result) {
                    console.log(result)
                    if (result == 1) {
                        location.reload();
                    }

                },
                error: function(request, error) {
                    console.log(error)

                }
            });
        }
    }

    function closedTerminal(openingId, TerminalId) {
        let text;
        let person = prompt("Please enter the amount to close the this terminal", "");
        if (person == null || person == "") {
            text = "User cancelled the prompt.";
        } else {
            $.ajax({
                url: "{{ url('/close-terminal') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    terminal: TerminalId,
                    opening: openingId,
                    amount: person
                },
                beforeSend: function(xhr) {
                    $('#div_details').append(
                        "<center><div class='col-xl-2 col-md-4 col-sm-6'>" +
                        "<h6 class='sub-title'>Large</h6>" +
                        "<div class='preloader3 loader-block'>" +
                        "<div class='circ1 bg-success loader-lg'></div>" +
                        "<div class='circ2 bg-success loader-lg'></div>" +
                        "<div class='circ3 bg-success loader-lg'></div>" +
                        "<div class='circ4 bg-success loader-lg'></div>" +
                        "</div>" +
                        "</div></center>"
                    )
                },
                success: function(result) {
                    console.log(result)
                    if (result == 1) {
                        location.reload();
                    }

                },
                error: function(request, error) {
                    console.log(error)

                }
            });
        }

    }
</script>
