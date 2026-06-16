@if (count($heads) === 0)
    <div class="rounded-lg border border-dashed border-erp-line bg-white p-10 text-center">
        <div class="text-base font-black text-erp-ink">No Record Found</div>
        <p class="mt-2 text-sm text-erp-mute">There are no terminal details available for this selection.</p>
    </div>
@else
@php
    $head = $heads[0];
    $currency = session('currency');
    $positive = ($head->bal ?? 0) + ($head->order_delivered_cash ?? 0) + ($head->Cash ?? 0) + ($head->adv_booking_cash ?? 0) + ($head->cashIn ?? 0) + ($head->TaxCash ?? 0);
    $negative = ($head->DiscountCash ?? 0) + ($head->SalesReturn ?? 0) + ($head->cashOut ?? 0) + ($head->VoidReceiptsCash ?? 0);
    $cashInHand = $positive - $negative + ($head->Delivery ?? 0);

    if (isset($result[0]->expenses) && $result[0]->expenses == 1) {
        $cashInHand -= $head->expenses ?? 0;
    }

    if (session('company_id') == 102) {
        $cashInHand -= $head->bal ?? 0;
    }

    $cashInHand = round($cashInHand);
    $closingBalance = round($head->closingBal ?? 0);
    $totalSales = ($head->TotalSales ?? 0) + ($head->credit_card_transaction ?? 0);
    $netSales = $totalSales - ($head->Discount ?? 0) + ($head->srb ?? 0);
    $totalRevenue = ($head->Cash ?? 0) + ($head->CreditCard ?? 0) + ($head->CustomerCredit ?? 0) + ($head->WalletSales ?? 0) + ($head->adv_booking_cash ?? 0) + ($head->adv_booking_card ?? 0) + ($head->order_delivered_cash ?? 0) + ($head->order_delivered_card ?? 0) + ($head->paidByCustomer ?? 0) + ($head->cashIn ?? 0);
    $totalDeductions = ($head->SalesReturn ?? 0) + ($head->Discount ?? 0) + ($head->cashOut ?? 0) + ($head->expenses ?? 0) + ($head->VoidReceiptsCash ?? 0) + ($head->VoidReceiptsCard ?? 0) + ($head->VoidReceiptsBooking ?? 0) + ($head->CashReturn ?? 0) + ($head->CardReturn ?? 0) + ($head->ChequeReturn ?? 0);

    $metricCards = [
        ['label' => 'Opening Balance', 'value' => $head->bal ?? 0, 'meta' => date('d M Y', strtotime($head->date)) . ' ' . date('h:i A', strtotime($head->time))],
        ['label' => 'Total Sales', 'value' => $totalSales, 'meta' => 'All transactions'],
        ['label' => 'Closing Balance', 'value' => $head->closingBal ?? 0, 'meta' => ($head->closingBal ?? 0) > 0 ? date('d M Y', strtotime($head->closingDate)) . ' ' . date('h:i A', strtotime($head->closingTime)) : 'Not closed'],
    ];

    $salesTypes = [
        ['label' => 'Take Away', 'value' => $head->TakeAway ?? 0],
        ['label' => 'Online', 'value' => $head->Online ?? 0],
        ['label' => 'COD Delivery', 'value' => $head->Delivery ?? 0],
    ];
@endphp

<div class="space-y-4">
    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-4 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="text-xs font-black uppercase tracking-[0.16em] text-erp-mute">Terminal</div>
                <h3 class="mt-1 text-xl font-black text-erp-ink">{{ $terminal_name[0]->terminal_name ?? '' }}</h3>
                <p class="mt-1 text-sm text-erp-mute">Opening #{{ $head->opening_id ?? '-' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex h-10 items-center rounded-lg px-4 text-sm font-black text-white {{ isset($head->status) && $head->status == 2 ? 'bg-red-600' : 'bg-erp' }}">
                    {{ isset($head->status) && $head->status == 2 ? 'Closed' : 'Active' }}
                </span>
                <button type="button" class="inline-flex h-10 items-center rounded-lg bg-blue-600 px-4 text-sm font-bold text-white transition hover:bg-blue-700"
                    onclick="cashDetails('{{ Crypt::encrypt($head->opening_id ?? '') }}','{{ Crypt::encrypt($head->terminal_id ?? '') }}','isdb')">
                    Item Sales
                </button>
                @if (($head->closingBal ?? 0) == 0)
                    @if (isset($result[0]->ob) && $result[0]->ob == 0)
                        <button type="button" class="inline-flex h-10 items-center rounded-lg bg-red-600 px-4 text-sm font-bold text-white transition hover:bg-red-700"
                            onclick="closedTerminal('{{ Crypt::encrypt($head->opening_id ?? '') }}','{{ Crypt::encrypt($head->terminal_id ?? '') }}')">
                            Close Terminal
                        </button>
                    @endif
                @else
                    @if (isset($result[0]->cb) && $result[0]->cb == 0)
                        <button type="button" class="inline-flex h-10 items-center rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark"
                            onclick="openTerminal('{{ $head->user_id ?? '' }}','{{ $head->terminal_id ?? '' }}')">
                            Open Terminal
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </section>

    <section class="grid gap-3 md:grid-cols-3">
        @foreach ($metricCards as $card)
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-black uppercase tracking-[0.16em] text-erp-mute">{{ $card['label'] }}</div>
                <div class="mt-3 text-2xl font-black text-erp-ink">{{ $currency }} {{ number_format($card['value'], 0) }}</div>
                <p class="mt-2 text-sm text-erp-mute">{{ $card['meta'] }}</p>
            </div>
        @endforeach
    </section>

    <section class="grid gap-3 md:grid-cols-3">
        @foreach ($salesTypes as $type)
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-black uppercase tracking-[0.16em] text-erp-mute">{{ $type['label'] }}</div>
                <div class="mt-3 text-xl font-black text-erp-ink">{{ $currency }} {{ number_format($type['value'], 0) }}</div>
            </div>
        @endforeach
    </section>

    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <button type="button" class="flex w-full items-center justify-between border-b border-erp-line px-5 py-4 text-left" onclick="toggleCategory('revenue')">
            <span>
                <span class="block text-base font-black text-erp-ink">Revenue & Income</span>
                <span class="mt-1 block text-sm text-erp-mute">Sales, bookings, delivered orders, and cash in.</span>
            </span>
            <span class="text-lg font-black text-erp-dark">+{{ $currency }} {{ number_format($totalRevenue, 0) }}</span>
        </button>
        <div id="revenue" class="divide-y divide-erp-line">
            @if (isset($result[0]->cash_sale) && $result[0]->cash_sale == 1)
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Cash Sale', 'amount' => $head->Cash ?? 0, 'action' => "cashDetails('" . Crypt::encrypt($head->opening_id) . "','" . Crypt::encrypt($head->terminal_id) . "',1)"])
            @endif
            @if (isset($result[0]->card_sale) && $result[0]->card_sale == 1)
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Credit Card Sale', 'amount' => $head->CreditCard ?? 0, 'action' => "cashDetails('" . Crypt::encrypt($head->opening_id) . "','" . Crypt::encrypt($head->terminal_id) . "',2)"])
            @endif
            @if (isset($result[0]->customer_credit_sale) && $result[0]->customer_credit_sale == 1)
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Customer Credit Sale', 'amount' => $head->CustomerCredit ?? 0, 'action' => "cashDetails('" . Crypt::encrypt($head->opening_id) . "','" . Crypt::encrypt($head->terminal_id) . "',3)"])
            @endif
            @if (isset($result[0]->wallets_sales) && $result[0]->wallets_sales == 1)
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Wallet Sale', 'amount' => $head->WalletSales ?? 0, 'action' => "cashDetails('" . Crypt::encrypt($head->opening_id) . "','" . Crypt::encrypt($head->terminal_id) . "',8)"])
            @endif
            @if (isset($result[0]->order_booking) && $result[0]->order_booking == 1)
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Adv Booking Cash', 'amount' => $head->adv_booking_cash ?? 0])
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Adv Booking Card', 'amount' => $head->adv_booking_card ?? 0])
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Order Delivered Cash', 'amount' => $head->order_delivered_cash ?? 0])
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Order Delivered Card', 'amount' => $head->order_delivered_card ?? 0])
            @endif
            @if (isset($result[0]->cost) && $result[0]->cost == 1)
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Paid By Customer', 'amount' => $head->paidByCustomer ?? 0])
            @endif
            @if (isset($result[0]->cash_in) && $result[0]->cash_in == 1)
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Cash In', 'amount' => $head->cashIn ?? 0, 'action' => "cashDetails('" . Crypt::encrypt($head->opening_id) . "','" . Crypt::encrypt($head->terminal_id) . "','ci')"])
            @endif
        </div>
    </section>

    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <button type="button" class="flex w-full items-center justify-between border-b border-erp-line px-5 py-4 text-left" onclick="toggleCategory('deductions')">
            <span>
                <span class="block text-base font-black text-erp-ink">Deductions & Returns</span>
                <span class="mt-1 block text-sm text-erp-mute">Returns, discounts, voids, cash out, and expenses.</span>
            </span>
            <span class="text-lg font-black text-red-600">-{{ $currency }} {{ number_format($totalDeductions, 0) }}</span>
        </button>
        <div id="deductions" class="divide-y divide-erp-line">
            @if (isset($result[0]->sale_return) && $result[0]->sale_return == 1)
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Sale Return', 'amount' => $head->SalesReturn ?? 0, 'action' => "cashDetails('" . Crypt::encrypt($head->opening_id) . "','" . Crypt::encrypt($head->terminal_id) . "','sr')"])
            @endif
            @if (isset($result[0]->void_receipt) && $result[0]->void_receipt == 1)
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Void Receipts Cash', 'amount' => $head->VoidReceiptsCash ?? 0])
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Void Receipts Card', 'amount' => $head->VoidReceiptsCard ?? 0])
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Void Receipts Booking', 'amount' => $head->VoidReceiptsBooking ?? 0])
            @endif
            @if (isset($result[0]->discount) && $result[0]->discount == 1)
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Discount', 'amount' => $head->Discount ?? 0])
            @endif
            @if (isset($result[0]->cash_out) && $result[0]->cash_out == 1)
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Cash Out', 'amount' => $head->cashOut ?? 0, 'action' => "cashDetails('" . Crypt::encrypt($head->opening_id) . "','" . Crypt::encrypt($head->terminal_id) . "','co')"])
            @endif
            @if (isset($result[0]->expenses) && $result[0]->expenses == 1)
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Expense', 'amount' => $head->expenses ?? 0, 'action' => "cashDetails('" . Crypt::encrypt($head->opening_id) . "','" . Crypt::encrypt($head->terminal_id) . "','ex')"])
            @endif
            @if (isset($result[0]->r_cash) && $result[0]->r_cash == 1)
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Customer Credit Return Cash', 'amount' => $head->CashReturn ?? 0])
            @endif
            @if (isset($result[0]->r_card) && $result[0]->r_card == 1)
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Customer Credit Return Credit', 'amount' => $head->CardReturn ?? 0])
            @endif
            @if (isset($result[0]->r_cheque) && $result[0]->r_cheque == 1)
                @include('Dashboard.partials.sales-v2-row', ['label' => 'Customer Credit Return Cheque', 'amount' => $head->ChequeReturn ?? 0])
            @endif
        </div>
    </section>

    <section class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
        <div class="grid gap-3 md:grid-cols-3">
            <div>
                <div class="text-xs font-black uppercase tracking-[0.16em] text-erp-mute">Net Sales</div>
                <div class="mt-2 text-xl font-black text-erp-ink">{{ $currency }} {{ number_format($netSales, 0) }}</div>
            </div>
            <div>
                <div class="text-xs font-black uppercase tracking-[0.16em] text-erp-mute">Cash In Hand</div>
                <div class="mt-2 text-xl font-black text-erp-dark">{{ $currency }} {{ number_format($cashInHand, 0) }}</div>
            </div>
            @if (($head->closingBal ?? 0) > 0)
                <div>
                    <div class="text-xs font-black uppercase tracking-[0.16em] text-erp-mute">Closing Match</div>
                    <div class="mt-2 text-xl font-black {{ $closingBalance == $cashInHand ? 'text-erp-dark' : 'text-red-600' }}">
                        {{ $closingBalance == $cashInHand ? 'Balanced' : number_format(abs($closingBalance - $cashInHand), 0) . ($closingBalance > $cashInHand ? ' Excess' : ' Short') }}
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
@endif
