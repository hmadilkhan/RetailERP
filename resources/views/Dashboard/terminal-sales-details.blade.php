<?php
$positive = (isset($heads[0]->bal) ? $heads[0]->bal : 0) + (isset($heads[0]->order_delivered_cash) ? $heads[0]->order_delivered_cash : 0) + (isset($heads[0]->Cash) ? $heads[0]->Cash : 0) + (isset($heads[0]->adv_booking_cash) ? $heads[0]->adv_booking_cash : 0) + (isset($heads[0]->cashIn) ? $heads[0]->cashIn : 0) + (isset($heads[0]->TaxCash) ? $heads[0]->TaxCash : 0);

$negative = (isset($heads[0]->DiscountCash) ? $heads[0]->DiscountCash : 0) + (isset($heads[0]->SalesReturn) ? $heads[0]->SalesReturn : 0) + (isset($heads[0]->cashOut) ? $heads[0]->cashOut : 0) + (isset($heads[0]->VoidReceiptsCash) ? $heads[0]->VoidReceiptsCash : 0);

$CashInHand = $positive - $negative + $heads[0]->Delivery;
$totalVoidReceipts = (isset($heads[0]->VoidReceiptsCash) ? $heads[0]->VoidReceiptsCash : 0) + (isset($heads[0]->VoidReceiptsCard) ? $heads[0]->VoidReceiptsCard : 0) + (isset($heads[0]->VoidReceiptsBooking) ? $heads[0]->VoidReceiptsBooking : 0);

if (isset($result[0]->expenses) && $result[0]->expenses == 1) {
    $CashInHand = $CashInHand - $heads[0]->expenses;
}

if (session('company_id') == 102) {
    $CashInHand = $CashInHand - (isset($heads[0]->bal) ? $heads[0]->bal : 0);
}

$CashInHand = round($CashInHand);
$closingBalance = round($heads[0]->closingBal);

// Calculate totals for categories
$totalRevenue = ($heads[0]->Cash ?? 0) + ($heads[0]->CreditCard ?? 0) + ($heads[0]->CustomerCredit ?? 0) + ($heads[0]->WalletSales ?? 0) + ($heads[0]->adv_booking_cash ?? 0) + ($heads[0]->adv_booking_card ?? 0) + ($heads[0]->order_delivered_cash ?? 0) + ($heads[0]->order_delivered_card ?? 0) + ($heads[0]->paidByCustomer ?? 0) + ($heads[0]->cashIn ?? 0);

$totalDeductions = ($heads[0]->SalesReturn ?? 0) + ($heads[0]->Discount ?? 0) + ($heads[0]->cashOut ?? 0) + ($heads[0]->expenses ?? 0) + (isset($heads[0]->VoidReceiptsCash) ? $heads[0]->VoidReceiptsCash : 0) + (isset($heads[0]->VoidReceiptsCard) ? $heads[0]->VoidReceiptsCard : 0) + (isset($heads[0]->VoidReceiptsBooking) ? $heads[0]->VoidReceiptsBooking : 0) + ($heads[0]->CashReturn ?? 0) + ($heads[0]->CardReturn ?? 0) + ($heads[0]->ChequeReturn ?? 0);
?>

@if (count($heads) > 0)
<!-- Header Section -->
<div class="premium-header mb-4">
    <div class="header-top">
        <div class="terminal-info">
            <div class="icon-badge">
                <i class="mdi mdi-monitor"></i>
            </div>
            <div>
                <h3 class="terminal-name">
                    {{ isset($terminal_name[0]->terminal_name) ? $terminal_name[0]->terminal_name : '' }}
                </h3>
                <p class="terminal-subtitle">Terminal Details & Transactions</p>
            </div>
        </div>
        <div class="header-actions">
            <span
                class="status-badge status-{{ isset($heads[0]->status) && $heads[0]->status == 2 ? 'closed' : 'active' }} text-white">
                <i
                    class="mdi mdi-{{ isset($heads[0]->status) && $heads[0]->status == 2 ? 'lock' : 'check-circle' }}"></i>
                {{ isset($heads[0]->status) && $heads[0]->status == 2 ? 'CLOSED' : 'ACTIVE' }}
            </span>
            <button class="btn-action btn-item-sales"
                onclick="cashDetails('{{ Crypt::encrypt(isset($heads[0]->opening_id) ? $heads[0]->opening_id : '') }}','{{ Crypt::encrypt(isset($heads[0]->terminal_id) ? $heads[0]->terminal_id : '') }}','isdb')">
                <i class="mdi mdi-cart"></i> Item Sales
            </button>
            @if (isset($heads[0]->closingBal) && $heads[0]->closingBal == 0)
            @if (isset($result[0]->ob) && $result[0]->ob == 0)
            <button class="btn-action btn-close"
                onclick="closedTerminal('{{ Crypt::encrypt(isset($heads[0]->opening_id) ? $heads[0]->opening_id : '') }}','{{ Crypt::encrypt(isset($heads[0]->terminal_id) ? $heads[0]->terminal_id : '') }}')">
                <i class="mdi mdi-lock"></i> Close Terminal
            </button>
            @endif
            @else
            @if (isset($result[0]->cb) && $result[0]->cb == 0)
            <button class="btn-action btn-open"
                onclick="openTerminal('{{ isset($heads[0]->user_id) ? $heads[0]->user_id : '' }}','{{ isset($heads[0]->terminal_id) ? $heads[0]->terminal_id : '' }}')">
                <i class="mdi mdi-lock-open"></i> Open Terminal
            </button>
            @endif
            @endif
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="summary-card card-opening">
            <div class="card-icon">
                <i class="mdi mdi-cash-plus"></i>
            </div>
            <div class="card-content">
                <h6 class="card-label">Opening Balance</h6>
                <h3 class="card-value">{{ session('currency') }}
                    {{ number_format(isset($heads[0]->bal) ? $heads[0]->bal : 0, 0) }}
                </h3>
                <p class="card-meta">
                    <i class="mdi mdi-calendar"></i>
                    {{ date('d M Y', strtotime($heads[0]->date)) }}
                    {{ date('h:i A', strtotime($heads[0]->time)) }}
                </p>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="summary-card card-sales">
            <div class="card-icon">
                <i class="mdi mdi-chart-line"></i>
            </div>
            <div class="card-content">
                <h6 class="card-label">Total Sales</h6>
                <h3 class="card-value">{{ session('currency') }}
                    {{ number_format($heads[0]->TotalSales + $heads[0]->credit_card_transaction, 0) }}
                </h3>
                <p class="card-meta">
                    <i class="mdi mdi-trending-up"></i>
                    All Transactions
                </p>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="summary-card card-closing">
            <div class="card-icon">
                <i class="mdi mdi-cash-minus"></i>
            </div>
            <div class="card-content">
                <h6 class="card-label">Closing Balance</h6>
                <h3 class="card-value">{{ session('currency') }}
                    {{ number_format($heads[0]->closingBal, 0) }}
                </h3>
                <p class="card-meta">
                    <i class="mdi mdi-calendar"></i>
                    {{ $heads[0]->closingBal > 0 ? date('d M Y', strtotime($heads[0]->closingDate)) . ' ' . date('h:i A', strtotime($heads[0]->closingTime)) : 'Not Closed' }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Sales Type Cards -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="sales-type-card type-takeaway">
            <div class="type-icon">
                <i class="mdi mdi-shopping"></i>
            </div>
            <div class="type-content">
                <h4 class="type-value">{{ session('currency') }}
                    {{ number_format($heads[0]->TakeAway, 0) }}
                </h4>
                <p class="type-label">Take Away</p>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="sales-type-card type-online">
            <div class="type-icon">
                <i class="mdi mdi-web"></i>
            </div>
            <div class="type-content">
                <h4 class="type-value">{{ session('currency') }} 0</h4>
                <p class="type-label">Online</p>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="sales-type-card type-delivery">
            <div class="type-icon">
                <i class="mdi-truck-delivery"></i>
            </div>
            <div class="type-content">
                <h4 class="type-value">{{ session('currency') }}
                    {{ number_format($heads[0]->Delivery, 0) }}
                </h4>
                <p class="type-label">COD (Delivery)</p>
            </div>
        </div>
    </div>
</div>

<!-- Categorized Transactions -->
<div class="card premium-card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="mdi mdi-format-list-bulleted"></i>
            Transaction Details
        </h5>
    </div>
    <div class="card-body p-0">
        <!-- Opening Balance -->
        @if (isset($result[0]->ob) && $result[0]->ob == 1)
        <div class="transaction-category">
            <div class="category-header category-neutral">
                <div class="category-info">
                    <i class="mdi mdi-cash-plus"></i>
                    <span class="category-name">Opening Balance</span>
                </div>
                <div class="category-amount">{{ session('currency') }}
                    {{ number_format($heads[0]->bal, 0) }}
                </div>
            </div>
        </div>
        @endif

        <!-- Revenue Section -->
        <div class="transaction-category">
            <div class="category-header category-revenue" onclick="toggleCategory('revenue')">
                <div class="category-info">
                    <i class="mdi mdi-chevron-down collapse-icon" id="revenue-icon"></i>
                    <i class="mdi mdi-trending-up"></i>
                    <span class="category-name">Revenue & Income</span>
                    <span class="category-count">{{ $totalRevenue > 0 ? 'Multiple Items' : '' }}</span>
                </div>
                <div class="category-amount amount-positive">+{{ session('currency') }}
                    {{ number_format($totalRevenue, 0) }}
                </div>
            </div>
            <div class="category-content" id="revenue">
                @if (isset($result[0]->cash_sale) && $result[0]->cash_sale == 1)
                <div class="transaction-row clickable-row"
                    onclick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}',1)">
                    <div class="transaction-label">
                        <i class="mdi mdi-cash text-success"></i>
                        <span>Cash Sale</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->Cash, 0) }}
                    </div>
                </div>
                @endif

                @if (isset($result[0]->card_sale) && $result[0]->card_sale == 1)
                <div class="transaction-row clickable-row"
                    onclick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}',2)">
                    <div class="transaction-label">
                        <i class="mdi mdi-credit-card text-info"></i>
                        <span>Credit Card Sale</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->CreditCard, 0) }}
                    </div>
                </div>
                @endif

                @if (isset($result[0]->customer_credit_sale) && $result[0]->customer_credit_sale == 1)
                <div class="transaction-row clickable-row"
                    onclick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}',3)">
                    <div class="transaction-label">
                        <i class="mdi mdi-account-cash text-warning"></i>
                        <span>Customer Credit Sale</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->CustomerCredit, 0) }}
                    </div>
                </div>
                @endif

                @if (isset($result[0]->wallets_sales) && $result[0]->wallets_sales == 1)
                <div class="transaction-row clickable-row"
                    onclick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}',8)">
                    <div class="transaction-label">
                        <i class="mdi mdi-wallet text-purple"></i>
                        <span>Wallet Sale</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->WalletSales, 0) }}
                    </div>
                </div>
                @endif

                @if (isset($result[0]->order_booking) && $result[0]->order_booking == 1)
                <div class="transaction-row">
                    <div class="transaction-label">
                        <i class="mdi mdi-book-open text-info"></i>
                        <span>Adv Booking (Cash)</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->adv_booking_cash, 0) }}
                    </div>
                </div>
                <div class="transaction-row">
                    <div class="transaction-label">
                        <i class="mdi mdi-book-open text-info"></i>
                        <span>Adv Booking (Card)</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->adv_booking_card, 0) }}
                    </div>
                </div>
                <div class="transaction-row">
                    <div class="transaction-label">
                        <i class="mdi mdi-package-variant text-success"></i>
                        <span>Order Delivered (Cash)</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->order_delivered_cash, 0) }}
                    </div>
                </div>
                <div class="transaction-row">
                    <div class="transaction-label">
                        <i class="mdi mdi-package-variant text-success"></i>
                        <span>Order Delivered (Card)</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->order_delivered_card, 0) }}
                    </div>
                </div>
                @endif

                @if (isset($result[0]->cost) && $result[0]->cost == 1)
                <div class="transaction-row">
                    <div class="transaction-label">
                        <i class="mdi mdi-account-cash text-success"></i>
                        <span>Paid By Customer (Customer Credit)</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->paidByCustomer, 0) }}
                    </div>
                </div>
                @endif

                @if (isset($result[0]->cash_in) && $result[0]->cash_in == 1)
                <div class="transaction-row clickable-row"
                    onclick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}','ci')">
                    <div class="transaction-label">
                        <i class="mdi mdi-arrow-down-bold text-success"></i>
                        <span>Cash In</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->cashIn, 0) }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Deductions Section -->
        <div class="transaction-category">
            <div class="category-header category-deduction" onclick="toggleCategory('deductions')">
                <div class="category-info">
                    <i class="mdi mdi-chevron-down collapse-icon" id="deductions-icon"></i>
                    <i class="mdi mdi-trending-down"></i>
                    <span class="category-name">Deductions & Returns</span>
                    <span class="category-count">{{ $totalDeductions > 0 ? 'Multiple Items' : '' }}</span>
                </div>
                <div class="category-amount amount-negative">-{{ session('currency') }}
                    {{ number_format($totalDeductions, 0) }}
                </div>
            </div>
            <div class="category-content" id="deductions">
                @if (isset($result[0]->sale_return) && $result[0]->sale_return == 1)
                <div class="transaction-row clickable-row"
                    onclick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}','sr')">
                    <div class="transaction-label">
                        <i class="mdi mdi-undo-variant text-danger"></i>
                        <span>Sale Return</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->SalesReturn, 0) }}
                    </div>
                </div>
                @endif

                @if (isset($result[0]->void_receipt) && $result[0]->void_receipt == 1)
                <div class="transaction-row">
                    <div class="transaction-label">
                        <i class="mdi mdi-cancel text-danger"></i>
                        <span>Void Receipts (Cash)</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format(isset($heads[0]->VoidReceiptsCash) ? $heads[0]->VoidReceiptsCash : 0, 0) }}
                    </div>
                </div>
                <div class="transaction-row">
                    <div class="transaction-label">
                        <i class="mdi mdi-cancel text-danger"></i>
                        <span>Void Receipts (Card)</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format(isset($heads[0]->VoidReceiptsCard) ? $heads[0]->VoidReceiptsCard : 0, 0) }}
                    </div>
                </div>
                <div class="transaction-row">
                    <div class="transaction-label">
                        <i class="mdi mdi-cancel text-danger"></i>
                        <span>Void Receipts (Booking)</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format(isset($heads[0]->VoidReceiptsBooking) ? $heads[0]->VoidReceiptsBooking : 0, 0) }}
                    </div>
                </div>
                @endif

                @if (isset($result[0]->discount) && $result[0]->discount == 1)
                <div class="transaction-row">
                    <div class="transaction-label">
                        <i class="mdi mdi-percent text-warning"></i>
                        <span>Discount</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->Discount, 0) }}
                    </div>
                </div>
                @endif

                @if (isset($result[0]->cash_out) && $result[0]->cash_out == 1)
                <div class="transaction-row clickable-row"
                    onclick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}','co')">
                    <div class="transaction-label">
                        <i class="mdi mdi-arrow-up-bold text-danger"></i>
                        <span>Cash Out</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->cashOut, 0) }}
                    </div>
                </div>
                @endif

                @if (isset($result[0]->expenses) && $result[0]->expenses == 1)
                <div class="transaction-row clickable-row"
                    onclick="cashDetails('{{ Crypt::encrypt($heads[0]->opening_id) }}','{{ Crypt::encrypt($heads[0]->terminal_id) }}','ex')">
                    <div class="transaction-label">
                        <i class="mdi mdi-receipt text-warning"></i>
                        <span>Expense</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->expenses, 0) }}
                    </div>
                </div>
                @endif

                @if (isset($result[0]->r_cash) && $result[0]->r_cash == 1)
                <div class="transaction-row">
                    <div class="transaction-label">
                        <i class="mdi mdi-cash-refund text-danger"></i>
                        <span>Customer Credit Return Cash</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->CashReturn, 0) }}
                    </div>
                </div>
                @endif

                @if (isset($result[0]->r_card) && $result[0]->r_card == 1)
                <div class="transaction-row">
                    <div class="transaction-label">
                        <i class="mdi mdi-credit-card-refund text-danger"></i>
                        <span>Customer Credit Return Credit</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->CardReturn, 0) }}
                    </div>
                </div>
                @endif

                @if (isset($result[0]->r_cheque) && $result[0]->r_cheque == 1)
                <div class="transaction-row">
                    <div class="transaction-label">
                        <i class="mdi mdi-checkbook text-danger"></i>
                        <span>Customer Credit Return Cheque</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->ChequeReturn, 0) }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Additional Info Section -->
        @if ((isset($result[0]->cost) && $result[0]->cost == 1) || (isset($result[0]->fbr_sync) && $result[0]->fbr_sync == 1) || (isset($result[0]->srb_sync) && $result[0]->srb_sync == 1))
        <div class="transaction-category">
            <div class="category-header category-info" onclick="toggleCategory('additional')">
                <div class="category-info">
                    <i class="mdi mdi-chevron-down collapse-icon" id="additional-icon"></i>
                    <i class="mdi mdi-information"></i>
                    <span class="category-name">Additional Information</span>
                </div>
                <div class="category-amount"></div>
            </div>
            <div class="category-content" id="additional">
                @if (isset($result[0]->cost) && $result[0]->cost == 1)
                <div class="transaction-row">
                    <div class="transaction-label">
                        <i class="mdi mdi-currency-usd text-muted"></i>
                        <span>Total Receipt Item Cost</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->cost, 0) }}
                    </div>
                </div>
                @endif

                @if (isset($result[0]->fbr_sync) && $result[0]->fbr_sync == 1)
                <div class="transaction-row">
                    <div class="transaction-label">
                        <i class="mdi mdi-file-document text-info"></i>
                        <span>FBR (TAX)</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->fbr, 0) }}
                    </div>
                </div>
                @endif

                @if (isset($result[0]->srb_sync) && $result[0]->srb_sync == 1)
                <div class="transaction-row">
                    <div class="transaction-label">
                        <i class="mdi mdi-file-document text-info"></i>
                        <span>SRB (TAX)</span>
                    </div>
                    <div class="transaction-amount">{{ session('currency') }}
                        {{ number_format($heads[0]->srb, 0) }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Summary Section -->
        <div class="transaction-summary">
            <div class="summary-row summary-total">
                <div class="summary-label">
                    <i class="mdi mdi-sigma"></i>
                    <span>Total Sales</span>
                </div>
                <div class="summary-amount">{{ session('currency') }}
                    {{ number_format($heads[0]->TotalSales + $heads[0]->credit_card_transaction, 0) }}
                </div>
            </div>
            <div class="summary-row summary-cash">
                <div class="summary-label">
                    <i class="mdi mdi-cash-multiple"></i>
                    <span>Cash In Hand</span>
                </div>
                <div class="summary-amount">{{ session('currency') }}
                    {{ number_format($CashInHand, 0) }}
                </div>
            </div>
            @if ($heads[0]->closingBal > 0)
            <div
                class="summary-row summary-closing {{ $closingBalance == $CashInHand ? 'balanced' : 'unbalanced' }}">
                <div class="summary-label">
                    <i class="mdi mdi-cash-lock"></i>
                    <span>Closing Balance</span>
                </div>
                <div class="summary-amount">
                    {{ session('currency') }} {{ $closingBalance }}
                    @if ($closingBalance > $CashInHand)
                    <span class="badge bg-danger ms-2">{{ $closingBalance - $CashInHand }} Excess</span>
                    @elseif ($closingBalance < $CashInHand)
                        <span class="badge bg-warning ms-2">{{ $CashInHand - $closingBalance }} Short</span>
                        @else
                        <span class="badge bg-success ms-2">Balanced</span>
                        @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@else
<div class="empty-state">
    <i class="mdi mdi-information-outline"></i>
    <h4>No Record Found</h4>
    <p>There are no terminal details available</p>
</div>
@endif

<style>
    .premium-header {
        background: white;
        border-radius: 16px;
        padding: 1.5rem 2rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.5rem;
    }

    .header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .terminal-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .icon-badge {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .terminal-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }

    .terminal-subtitle {
        font-size: 0.875rem;
        color: #6c757d;
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-active {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .status-closed {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-item-sales {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-close {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .btn-open {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .summary-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .card-opening::before {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .card-sales::before {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .card-closing::before {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(102, 126, 234, 0.15);
    }

    .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        color: #667eea;
    }

    .card-opening .card-icon {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);
        color: #10b981;
    }

    .card-sales .card-icon {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(217, 119, 6, 0.1) 100%);
        color: #f59e0b;
    }

    .card-closing .card-icon {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%);
        color: #ef4444;
    }

    .card-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .card-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .card-meta {
        font-size: 0.875rem;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }

    .sales-type-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .sales-type-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }

    .type-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
    }

    .type-takeaway .type-icon {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(217, 119, 6, 0.1) 100%);
        color: #f59e0b;
    }

    .type-online .type-icon {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.1) 100%);
        color: #3b82f6;
    }

    .type-delivery .type-icon {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(124, 58, 237, 0.1) 100%);
        color: #8b5cf6;
    }

    .type-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0 0 0.25rem 0;
    }

    .type-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .premium-card {
        border-radius: 20px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        background: white;
        overflow: hidden;
    }

    .premium-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
        padding: 1.25rem 1.5rem;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-title i {
        color: #667eea;
        font-size: 1.25rem;
    }

    /* Categorized Transaction Styles */
    .transaction-category {
        border-bottom: 1px solid #f1f3f5;
    }

    .transaction-category:last-child {
        border-bottom: none;
    }

    .category-header {
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: all 0.3s ease;
        user-select: none;
    }

    .category-header:hover {
        background: rgba(102, 126, 234, 0.02);
    }

    .category-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .category-name {
        font-weight: 700;
        font-size: 1rem;
        color: #2c3e50;
    }

    .category-count {
        font-size: 0.75rem;
        color: #6c757d;
        font-weight: 500;
    }

    .category-amount {
        font-weight: 700;
        font-size: 1.125rem;
    }

    .amount-positive {
        color: #10b981;
    }

    .amount-negative {
        color: #ef4444;
    }

    .collapse-icon {
        transition: transform 0.3s ease;
        font-size: 1.25rem;
        color: #6c757d;
    }

    .collapse-icon.rotated {
        transform: rotate(-90deg);
    }

    .category-revenue {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.03) 0%, rgba(5, 150, 105, 0.03) 100%);
        border-left: 4px solid #10b981;
    }

    .category-deduction {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.03) 0%, rgba(220, 38, 38, 0.03) 100%);
        border-left: 4px solid #ef4444;
    }

    .category-info-header {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.03) 0%, rgba(37, 99, 235, 0.03) 100%);
        border-left: 4px solid #3b82f6;
    }

    .category-neutral {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%);
        border-left: 4px solid #667eea;
    }

    .category-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease;
    }

    .category-content.expanded {
        max-height: 2000px;
    }

    .transaction-row {
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f8f9fa;
        transition: all 0.2s ease;
    }

    .transaction-row:last-child {
        border-bottom: none;
    }

    .transaction-row:hover {
        background: rgba(102, 126, 234, 0.02);
    }

    .clickable-row {
        cursor: pointer;
    }

    .clickable-row:hover {
        background: rgba(102, 126, 234, 0.05);
    }

    .transaction-label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9375rem;
        color: #495057;
    }

    .transaction-label i {
        font-size: 1.25rem;
    }

    .transaction-amount {
        font-weight: 600;
        font-size: 1rem;
        color: #2c3e50;
    }

    .transaction-summary {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1rem 0;
    }

    .summary-row {
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #dee2e6;
    }

    .summary-row:last-child {
        border-bottom: none;
    }

    .summary-label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 700;
        font-size: 1rem;
        color: #2c3e50;
    }

    .summary-label i {
        font-size: 1.5rem;
    }

    .summary-amount {
        font-weight: 700;
        font-size: 1.25rem;
        color: #2c3e50;
    }

    .summary-total {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    }

    .summary-cash {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, rgba(5, 150, 105, 0.05) 100%);
    }

    .summary-cash .summary-amount {
        color: #10b981;
    }

    .summary-closing.balanced .summary-amount {
        color: #10b981;
    }

    .summary-closing.unbalanced .summary-amount {
        color: #ef4444;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .empty-state i {
        font-size: 64px;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    .empty-state h4 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        font-size: 1rem;
        color: #6c757d;
        margin: 0;
    }

    @media (max-width: 768px) {
        .header-top {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
            flex-direction: column;
        }

        .btn-action,
        .status-badge {
            width: 100%;
            justify-content: center;
        }

        .sales-type-card {
            flex-direction: column;
            text-align: center;
        }

        .category-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .transaction-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>

<script>
    function cashDetails(opening, terminal, mode) {
        window.open("{{ url('sales-show') }}/" + opening + "/" + terminal + "/" + mode);
    }

    function openTerminal(branchId, terminalId) {
        let amount = prompt("Please enter amount to open");
        if (amount == null || amount == "") {
            return;
        }

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
                if (result == 1) {
                    location.reload();
                }
            },
            error: function(request, error) {
                console.log(error);
            }
        });
    }

    function closedTerminal(openingId, TerminalId) {
        let person = prompt("Please enter the amount to close the this terminal", "");
        if (person == null || person == "") {
            return;
        }

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
                    "<h6 class='sub-title'>Loading...</h6>" +
                    "<div class='preloader3 loader-block'>" +
                    "<div class='circ1 bg-success loader-lg'></div>" +
                    "<div class='circ2 bg-success loader-lg'></div>" +
                    "<div class='circ3 bg-success loader-lg'></div>" +
                    "<div class='circ4 bg-success loader-lg'></div>" +
                    "</div>" +
                    "</div></center>"
                );
            },
            success: function(result) {
                if (result == 1) {
                    location.reload();
                }
            },
            error: function(request, error) {
                console.log(error);
            }
        });
    }

    function toggleCategory(categoryId) {
        const content = document.getElementById(categoryId);
        const icon = document.getElementById(categoryId + '-icon');

        if (content.classList.contains('expanded')) {
            content.classList.remove('expanded');
            icon.classList.remove('rotated');
        } else {
            content.classList.add('expanded');
            icon.classList.add('rotated');
        }
    }

    // Auto-expand all categories on load
    document.addEventListener('DOMContentLoaded', function() {
        const categories = ['revenue', 'deductions', 'additional'];
        categories.forEach(cat => {
            const content = document.getElementById(cat);
            const icon = document.getElementById(cat + '-icon');
            if (content && icon) {
                content.classList.add('expanded');
            }
        });
    });
</script>