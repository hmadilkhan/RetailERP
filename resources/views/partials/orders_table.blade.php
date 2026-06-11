<style>
    .orders-view-toolbar {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .orders-bulk-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
    }

    .orders-view-switcher {
        display: inline-flex;
        align-items: center;
        border: 1px solid #d8e1ec;
        border-radius: 8px;
        overflow: hidden;
        background: #f8fafc;
    }

    .orders-view-btn {
        min-height: 2.35rem;
        border: 0;
        border-right: 1px solid #d8e1ec;
        background: transparent;
        color: #475569;
        padding: 0.45rem 0.8rem;
        font-size: 0.75rem;
        font-weight: 900;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        cursor: pointer;
        transition: background 0.18s ease, color 0.18s ease;
    }

    .orders-view-btn:last-child {
        border-right: 0;
    }

    .orders-view-btn.is-active {
        background: #16a34a;
        color: #fff;
    }

    .orders-view-pane {
        display: none;
    }

    .orders-view-pane.is-active {
        display: block;
    }

    #order_table th,
    #order_table td {
        vertical-align: middle;
        white-space: nowrap;
    }

    #order_table th:first-child,
    #order_table td:first-child {
        min-width: 48px;
        width: 48px;
        text-align: center;
        padding-left: 0.25rem;
        padding-right: 0.25rem;
    }

    #order_table {
        width: 100%;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    @media (max-width: 1024px) {
        #order_table th,
        #order_table td {
            font-size: 0.875rem;
            padding: 0.4rem;
        }
        
        .btn-group .btn {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
    }

    @media (max-width: 768px) {
        #order_table th,
        #order_table td {
            font-size: 0.8rem;
            padding: 0.3rem;
        }
        
        .btn-group .btn {
            font-size: 0.75rem;
            padding: 0.2rem 0.4rem;
        }
    }

    @media (max-width: 576px) {
        #order_table th,
        #order_table td {
            font-size: 0.75rem;
            padding: 0.25rem;
        }
    }

    .orders-card-grid,
    .orders-board-wrap {
        background: #f8fafc;
        color: #0f172a;
        border: 1px solid #d8e1ec;
        border-radius: 8px;
        padding: 1rem;
    }

    .orders-view-kicker {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        color: #6b7280;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }

    .orders-card-grid-list {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.85rem;
    }

    .orders-display-card {
        position: relative;
        min-height: 248px;
        border: 1px solid #d8e1ec;
        border-left: 3px solid var(--order-accent, #22c55e);
        border-radius: 4px;
        background: #fff;
        padding: 1rem;
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
    }

    .orders-display-card.is-return {
        border-color: #fecdd3;
        border-left-color: #ef4444;
    }

    .orders-card-top,
    .orders-card-actions,
    .orders-card-line,
    .orders-board-head,
    .orders-board-card-top,
    .orders-board-card-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .orders-channel-badge,
    .orders-status-pill {
        display: inline-flex;
        align-items: center;
        min-height: 1.35rem;
        border-radius: 3px;
        padding: 0.2rem 0.45rem;
        font-size: 0.62rem;
        font-weight: 900;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        line-height: 1;
    }

    .orders-channel-badge {
        background: #e2e8f0;
        color: #334155;
    }

    .orders-channel-badge.is-web {
        background: rgba(14, 165, 233, 0.16);
        color: #38bdf8;
    }

    .orders-status-pill {
        border: 1px solid rgba(34, 197, 94, 0.28);
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
    }

    .orders-display-card[data-status*="processing"] .orders-status-pill,
    .orders-board-column[data-status*="processing"] .orders-status-pill {
        border-color: rgba(245, 158, 11, 0.35);
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
    }

    .orders-display-card[data-status*="pending"] .orders-status-pill,
    .orders-board-column[data-status*="pending"] .orders-status-pill {
        border-color: rgba(148, 163, 184, 0.35);
        background: rgba(148, 163, 184, 0.12);
        color: #64748b;
    }

    .orders-display-card[data-status*="dispatch"] .orders-status-pill,
    .orders-board-column[data-status*="dispatch"] .orders-status-pill {
        border-color: rgba(14, 165, 233, 0.35);
        background: rgba(14, 165, 233, 0.12);
        color: #38bdf8;
    }

    .orders-display-card[data-status*="void"] .orders-status-pill,
    .orders-board-column[data-status*="void"] .orders-status-pill {
        border-color: rgba(239, 68, 68, 0.35);
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
    }

    .orders-card-order {
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 800;
    }

    .orders-card-title {
        margin: 1rem 0 0.2rem;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 900;
        letter-spacing: 0;
    }

    .orders-card-phone {
        min-height: 1.2rem;
        color: #7c8493;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .orders-card-divider {
        height: 1px;
        margin: 0.9rem 0;
        background: #e2e8f0;
    }

    .orders-card-label {
        color: #6b7280;
        font-size: 0.62rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .orders-card-amount {
        color: #0f172a;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: 0;
        white-space: nowrap;
    }

    .orders-card-meta {
        display: grid;
        gap: 0.45rem;
        margin: 0.9rem 0;
        color: #8b95a7;
        font-size: 0.76rem;
        font-weight: 700;
    }

    .orders-card-foot {
        padding-top: 0.8rem;
        border-top: 1px solid #e2e8f0;
        color: #6b7280;
        font-size: 0.66rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .orders-card-actions {
        justify-content: flex-start;
        flex-wrap: wrap;
        margin-top: 0.85rem;
    }

    .orders-icon-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border: 1px solid #d8e1ec;
        border-radius: 6px;
        background: #fff;
        color: #475569;
        cursor: pointer;
        transition: border-color 0.18s ease, color 0.18s ease, background 0.18s ease;
    }

    .orders-icon-action:hover {
        border-color: #16a34a;
        color: #15803d;
        background: #f0fdf4;
    }

    .orders-board-columns {
        display: grid;
        grid-auto-flow: column;
        grid-auto-columns: minmax(260px, 1fr);
        gap: 0.75rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }

    .orders-board-column {
        min-height: 520px;
        border: 1px solid #d8e1ec;
        border-radius: 8px;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        overflow: hidden;
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.07);
    }

    .orders-board-head {
        align-items: flex-start;
        padding: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.75);
        border-top: 4px solid var(--order-accent, #22c55e);
        background: linear-gradient(135deg, #f8fafc, #ffffff);
        box-shadow: inset 0 -1px 0 rgba(148, 163, 184, 0.16);
    }

    .orders-board-title {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        color: #0f172a;
        font-size: 0.82rem;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .orders-board-dot {
        width: 0.55rem;
        height: 0.55rem;
        border-radius: 999px;
        background: var(--order-accent, #22c55e);
        box-shadow: 0 0 0 4px rgba(226, 232, 240, 0.9);
    }

    .orders-board-count {
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.85);
        color: #475569;
        padding: 0.2rem 0.5rem;
        font-size: 0.68rem;
        font-weight: 900;
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.65), inset 0 0 0 1px rgba(148, 163, 184, 0.18);
    }

    .orders-board-total {
        margin-top: 1rem;
        color: #0f172a;
        font-size: 1.08rem;
        font-weight: 900;
        padding: 0.8rem 0.9rem;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.76);
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.14);
    }

    .orders-board-total span {
        display: block;
        margin-bottom: 0.28rem;
        color: #6b7280;
        font-size: 0.6rem;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }

    .orders-board-list {
        display: grid;
        gap: 0.65rem;
        padding: 0.85rem;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.1) 0%, rgba(248, 250, 252, 0.85) 12%, #f8fafc 100%);
    }

    .orders-board-card {
        border: 1px solid #d8e1ec;
        border-radius: 8px;
        background: #fff;
        padding: 0.8rem;
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.06);
    }

    .orders-board-card-title {
        margin-top: 0.65rem;
        color: #0f172a;
        font-size: 0.86rem;
        font-weight: 900;
    }

    .orders-board-card-phone {
        margin-top: 0.12rem;
        color: #6b7280;
        font-size: 0.7rem;
        font-weight: 700;
    }

    .orders-board-card-bottom {
        margin-top: 0.75rem;
        padding-top: 0.65rem;
        border-top: 1px solid #e2e8f0;
        color: #6b7280;
        font-size: 0.62rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .orders-empty-state {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 800;
    }

    @media (max-width: 1400px) {
        .orders-card-grid-list {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 992px) {
        .orders-card-grid-list {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .orders-view-toolbar {
            align-items: stretch;
        }

        .orders-view-switcher,
        .orders-bulk-actions {
            width: 100%;
        }

        .orders-view-btn {
            flex: 1;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .orders-card-grid-list {
            grid-template-columns: 1fr;
        }
    }
</style>
@php
    $boardSource = $displayOrders ?? $orders;
    $ordersList = method_exists($boardSource, 'items') ? collect($boardSource->items()) : collect($boardSource);
    $statusAccent = [
        'pending' => '#94a3b8',
        'processing' => '#f59e0b',
        'dispatch' => '#38bdf8',
        'delivered' => '#22c55e',
        'void' => '#ef4444',
        'sales return' => '#f43f5e',
    ];
    $preferredStatuses = ['Pending', 'Processing', 'Dispatch', 'Delivered', 'Void', 'Sales Return'];
    $groupedOrders = $ordersList->groupBy(fn($item) => Custom_Helper::getOrderStatus($item->order_status_name, $item->is_sale_return));
    $boardStatuses = collect($preferredStatuses)
        ->merge($groupedOrders->keys())
        ->unique()
        ->filter(fn($statusName) => $groupedOrders->has($statusName));
@endphp
<div class="col-md-12 orders-view-toolbar">
    <div class="orders-bulk-actions">
        <button id="void-selected-btn" class="btn btn-danger btn-sm" disabled>Mark as Void</button>
        <button id="deliver-selected-btn" class="btn btn-success btn-sm" disabled>Mark As Delivered</button>
    </div>
    <div class="orders-view-switcher mt-2" role="group" aria-label="Order layout">
        <button type="button" class="orders-view-btn is-active" data-orders-view="table">
            <i class="icofont icofont-table"></i> Table
        </button>
        <button type="button" class="orders-view-btn" data-orders-view="cards">
            <i class="icofont icofont-ui-card"></i> Cards
        </button>
        <button type="button" class="orders-view-btn" data-orders-view="board">
            <i class="icofont icofont-dashboard-web"></i> Board
        </button>
    </div>
</div>
<div class="col-md-12 table-responsive orders-view-pane is-active" data-orders-pane="table">
    <table id="order_table" class="table table-striped  dt-responsive dataTable no-footer dtr-inline" width="100%">
        <thead>
            <tr>
                <th style="width:40px; text-align:center;">
                    <input type="checkbox" id="select-all-orders" class="form-check-input">
                </th>
                <th>Machine/Website</th>
                <th>Order#</th>
                <th>Date</th>
                <th>Time</th>
                <th class="text-center">Category</th>
                <th>Branch</th>
                <th>Terminal</th>
                <th>Receipt#</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Order Type</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Items / Total</th>
                <th>Sales Person</th>
                @if(session('company_id') != 74)
                <th>Wallet</th>
                @else
                <th>Department</th>
                @endif
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if ($orders->isNotEmpty())
                @foreach ($orders as $key => $order)
                    <tr id="parent{{ $order->id }}"
                        class="{{ $order->is_sale_return == 1 ? 'table-danger' : '' }} main-row pointer">
                        <td style="text-align:center;">
                            <input type="checkbox" class="form-check-input order-checkbox" value="{{ $order->id }}">
                        </td>
                        <td>{{ $order->web == 1 ? strtoupper($order->url_orderid) : $order->machine_terminal_count }}
                        </td>
                        <td>{{ $order->id }}</td>
                        <td>
                            <div class="btn-group dropend border border-black">
                                <button type="button" class="btn btn-sm btn-default dropdown-toggle text-nowrap"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="min-width: 100px;">
                                    {{ date('d M Y', strtotime($order->date)) }}
                                </button>
                                <ul class="dropdown-menu px-4">
                                    <li><a class="dropdown-item" href="#"><strong>Stamp Date </strong><br />
                                            {{ date('d M Y ', strtotime($order->delivery_date)) }}</a></li>
                                    @if ($order->order_delivery_date != '')
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#"><strong>Order Delivery
                                                    Date</strong></a>{{ $order->order_delivery_date != '' ? date('d M Y ', strtotime($order->order_delivery_date)) : '' }}
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                        <td>{{ date('h:i a', strtotime($order->time)) }}</td>
                        <td class="text-center"><label
                                class="label {{ $order->web == 1 ? 'label-warning' : 'label-info' }}">
                                {{ $order->web == 1 ? 'Website' : 'POS' }}</label></td>
                        <td>{{ $order->branch_name }}</td>
                        <td>{{ $order->terminal_name }}</td>
                        <td>{{ $order->receipt_no }}</td>
                        <td>{{ $order->name }}</td>
                        <td>{{ $order->mobile }}</td>
                        <td>{{ $order->order_mode }}</td>
                        <td>{{ $order->payment_mode }}</td>
                        <td><label
                                class="label {{ Custom_Helper::getColorName($order->order_status_name) }}">{{ Custom_Helper::getOrderStatus($order->order_status_name, $order->is_sale_return) }}</label>
                        </td>
                        <td>{{ $order->total_amount }}</td>
                        <td>{{ $order->itemcount }}/{{ $order->itemstotalqty }}</td>
                        <td>{{ !empty($order->provider_name) ? $order->provider_name : '-' }}</td>
                        @if(session('company_id') != 74)
                        <td>{{ !empty($order->wallet) ? $order->wallet : '-' }}</td>
                        @else
                        <td>{{  $order->inventory_department ?? '-' }}</td>
                        @endif
                        <td>
                            <!-- Large button groups (default and split) -->
                            <div class="btn-group border border-black">
                                <button class="btn btn-default btn-sm dropdown-toggle" type="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu px-2">
                                    <li onclick='showOrderDetails("{{ $order->id }}")'><a class="dropdown-item"><i
                                                onclick='showOrderDetails("{{ $order->id }}")'
                                                class='icofont icofont-eye-alt icofont-1x text-info mx-2'
                                                data-toggle='tooltip' data-placement='top' title=''
                                                data-original-title='Show Order Details'></i>Show Order Details</a></li>
                                    @if (session('roleId') != 20 && session('roleId') != 19)
                                        <li onclick='showReceipt("{{ $order->receipt_no }}")'><a
                                                class="dropdown-item"><i
                                                    onclick='showReceipt("{{ $order->receipt_no }}")'
                                                    class='icofont icofont-printer text-success mx-2'
                                                    data-toggle='tooltip' data-placement='top' title=''
                                                    data-original-title='Show Receipt'></i>Show Receipt </a></li>
                                        <li onclick='assignToServiceProviderModal("{{ $order->id }}")'><a
                                                class="dropdown-item"><i
                                                    onclick='assignToServiceProviderModal("{{ $order->id }}")'
                                                    class='icofont icofont-business-man mx-2' data-toggle='tooltip'
                                                    data-placement='top' title=''
                                                    data-original-title='Assign To Service Provider'></i>Assign To
                                                Service
                                                Provider </a></li>
                                        @if (empty($order->provider_name) && $order->provider_name == '')
                                            <li
                                                onclick='assignSalesPerson("{{ $order->id }}","{{ $order->branch }}")'>
                                                <a class="dropdown-item"><i
                                                        onclick='assignSalesPerson("{{ $order->id }}","{{ $order->branch }}")'
                                                        class='icofont icofont-business-man mx-2' data-toggle='tooltip'
                                                        data-placement='top' title=''
                                                        data-original-title='Assign Sales Person'></i>Assign Sales
                                                    Person </a>
                                            </li>
                                        @endif
                                    @endif
                                    @if ($order->status != 12 && (session('roleId') != 20 && session('roleId') != 19 && session('roleId') != 10))
                                        <li onclick='voidReceipt("{{ $order->id }}")'><a class="dropdown-item"><i
                                                    onclick='voidReceipt("{{ $order->id }}")'
                                                    class='alert-confirm text-danger icofont icofont icofont-delete-alt mx-2'
                                                    data-toggle='tooltip' data-placement='top' title=''
                                                    data-original-title='Mark as Void'></i>Mark as Void</a></li>
                                    @endif
                                    @if ($order->status != 4 && session('roleId') != 20 && session('roleId') != 19 && session('roleId') != 10)
                                        <li onclick='deliveredReceipt("{{ $order->id }}")'><a
                                                class="dropdown-item"><i
                                                    onclick='deliveredReceipt("{{ $order->id }}")'
                                                    class='alert-confirm text-success icofont icofont icofont-tick-mark mx-2'
                                                    data-toggle='tooltip' data-placement='top' title=''
                                                    data-original-title='Mark as Delivered'></i>Mark as Delivered</a>
                                        </li>
                                    @endif
                                    @if (session('roleId') == 20 && $order->status == 6)
                                        <li onclick='assignToBranchModal("{{ $order->id }}")'><a
                                                class="dropdown-item"><i
                                                    onclick='assignToBranchModal("{{ $order->id }}")'
                                                    class='icofont icofont icofont-business-man mx-2'
                                                    data-toggle='tooltip' data-placement='top' title=''
                                                    data-original-title='Assign to Branch'></i>Assign to Branch</a>
                                        </li>
                                    @endif
                                    @if (in_array(session('roleId'), [1, 2, 4]))
                                        <li onclick='discountReceipt("{{ $order->id }}")'><a
                                                class="dropdown-item"><i
                                                    onclick='discountReceipt("{{ $order->id }}")'
                                                    class='alert-confirm text-info icofont icofont icofont-sale-discount mx-2'
                                                    data-toggle='tooltip' data-placement='top' title=''
                                                    data-original-title='Add Discount'></i>Add Discount</a></li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="18" class="text-center">No Record Found</td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class="col-md-12">
        {{ $orders->links('pagination::bootstrap-4') }}
    </div>
</div>
<div class="col-md-12 orders-view-pane" data-orders-pane="cards">
    <div class="orders-card-grid">
        <div class="orders-view-kicker">
            <span>{{ $ordersList->count() }} orders - grid view</span>
            <span>Sorted by latest</span>
        </div>
        @if ($ordersList->isNotEmpty())
            <div class="orders-card-grid-list">
                @foreach ($ordersList as $order)
                    @php
                        $displayStatus = Custom_Helper::getOrderStatus($order->order_status_name, $order->is_sale_return);
                        $statusKey = strtolower($displayStatus);
                        $accent = $statusAccent[$statusKey] ?? '#22c55e';
                        $amount = number_format((float) str_replace(',', '', $order->total_amount), 2);
                        $channel = $order->web == 1 ? 'Web' : 'POS';
                        $channelRef = $order->web == 1 ? strtoupper($order->url_orderid) : $order->machine_terminal_count;
                    @endphp
                    <article class="orders-display-card {{ $order->is_sale_return == 1 ? 'is-return' : '' }}"
                        data-status="{{ $statusKey }}" style="--order-accent: {{ $accent }};">
                        <div class="orders-card-top">
                            <div>
                                <span class="orders-channel-badge {{ $order->web == 1 ? 'is-web' : '' }}">{{ $channel }}</span>
                                <span class="orders-card-order">#{{ $order->id }}</span>
                            </div>
                            <span class="orders-status-pill">{{ $displayStatus }}</span>
                        </div>
                        <h4 class="orders-card-title">{{ $order->name ?: 'Walk In' }}</h4>
                        <div class="orders-card-phone"><i class="icofont icofont-phone"></i> {{ $order->mobile ?: '-' }}</div>
                        <div class="orders-card-divider"></div>
                        <div class="orders-card-line">
                            <span class="orders-card-label">Total</span>
                            <span class="orders-card-amount">Rs. {{ $amount }}</span>
                        </div>
                        <div class="orders-card-meta">
                            <div><i class="icofont icofont-clock-time"></i> {{ date('h:i A', strtotime($order->time)) }} - {{ date('d M Y', strtotime($order->date)) }}</div>
                            <div><i class="icofont icofont-location-pin"></i> {{ $order->branch_name ?: '-' }}</div>
                            <div><i class="icofont icofont-credit-card"></i> {{ $order->payment_mode ?: '-' }}</div>
                        </div>
                        <div class="orders-card-foot">
                            {{ $order->order_mode ?: '-' }} - {{ $order->terminal_name ?: '-' }} - {{ $channelRef ?: '-' }}
                        </div>
                        <div class="orders-card-actions">
                            <input type="checkbox" class="form-check-input order-checkbox" value="{{ $order->id }}"
                                title="Select order">
                            <button type="button" class="orders-icon-action" title="Show Order Details"
                                onclick='showOrderDetails("{{ $order->id }}")'>
                                <i class="icofont icofont-eye-alt"></i>
                            </button>
                            @if (session('roleId') != 20 && session('roleId') != 19)
                                <button type="button" class="orders-icon-action" title="Show Receipt"
                                    onclick='showReceipt("{{ $order->receipt_no }}")'>
                                    <i class="icofont icofont-printer"></i>
                                </button>
                                <button type="button" class="orders-icon-action" title="Assign Service Provider"
                                    onclick='assignToServiceProviderModal("{{ $order->id }}")'>
                                    <i class="icofont icofont-business-man"></i>
                                </button>
                                @if (empty($order->provider_name) && $order->provider_name == '')
                                    <button type="button" class="orders-icon-action" title="Assign Sales Person"
                                        onclick='assignSalesPerson("{{ $order->id }}","{{ $order->branch }}")'>
                                        <i class="icofont icofont-user-suited"></i>
                                    </button>
                                @endif
                            @endif
                            @if ($order->status != 12 && (session('roleId') != 20 && session('roleId') != 19 && session('roleId') != 10))
                                <button type="button" class="orders-icon-action" title="Mark as Void"
                                    onclick='voidReceipt("{{ $order->id }}")'>
                                    <i class="icofont icofont-delete-alt"></i>
                                </button>
                            @endif
                            @if ($order->status != 4 && session('roleId') != 20 && session('roleId') != 19 && session('roleId') != 10)
                                <button type="button" class="orders-icon-action" title="Mark as Delivered"
                                    onclick='deliveredReceipt("{{ $order->id }}")'>
                                    <i class="icofont icofont-tick-mark"></i>
                                </button>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="orders-empty-state">No Record Found</div>
        @endif
    </div>
</div>
<div class="col-md-12 orders-view-pane" data-orders-pane="board">
    <div class="orders-board-wrap">
        <div class="orders-view-kicker">
            <span>Kanban board - grouped by status</span>
            <span>{{ $ordersList->count() }} total</span>
        </div>
        @if ($ordersList->isNotEmpty())
            <div class="orders-board-columns">
                @foreach ($boardStatuses as $statusName)
                    @php
                        $columnOrders = $groupedOrders->get($statusName, collect());
                        $statusKey = strtolower($statusName);
                        $accent = $statusAccent[$statusKey] ?? '#22c55e';
                        $columnTotal = $columnOrders->sum(fn($item) => (float) str_replace(',', '', $item->total_amount));
                    @endphp
                    <section class="orders-board-column" data-status="{{ $statusKey }}" style="--order-accent: {{ $accent }};">
                        <div class="orders-board-head">
                            <div>
                                <div class="orders-board-title">
                                    <span class="orders-board-dot"></span>
                                    {{ $statusName }}
                                    <span class="orders-board-count">{{ $columnOrders->count() }}</span>
                                </div>
                                <div class="orders-board-total">
                                    <span>Subtotal</span>
                                    Rs. {{ number_format($columnTotal, 2) }}
                                </div>
                            </div>
                        </div>
                        <div class="orders-board-list">
                            @foreach ($columnOrders as $order)
                                @php
                                    $amount = number_format((float) str_replace(',', '', $order->total_amount), 2);
                                    $channel = $order->web == 1 ? 'Web' : 'POS';
                                    $channelRef = $order->web == 1 ? strtoupper($order->url_orderid) : $order->machine_terminal_count;
                                @endphp
                                <article class="orders-board-card">
                                    <div class="orders-board-card-top">
                                        <div>
                                            <span class="orders-channel-badge {{ $order->web == 1 ? 'is-web' : '' }}">{{ $channel }} - {{ $channelRef ?: $order->id }}</span>
                                        </div>
                                        <span class="orders-card-order">{{ date('h:i A', strtotime($order->time)) }}</span>
                                    </div>
                                    <div class="orders-board-card-title">{{ $order->name ?: 'Walk In' }}</div>
                                    <div class="orders-board-card-phone">{{ $order->mobile ?: '-' }}</div>
                                    <div class="orders-board-card-bottom">
                                        <span>{{ $order->order_mode ?: '-' }}</span>
                                        <strong>Rs. {{ $amount }}</strong>
                                    </div>
                                    <div class="orders-card-actions">
                                        <input type="checkbox" class="form-check-input order-checkbox"
                                            value="{{ $order->id }}" title="Select order">
                                        <button type="button" class="orders-icon-action" title="Show Order Details"
                                            onclick='showOrderDetails("{{ $order->id }}")'>
                                            <i class="icofont icofont-eye-alt"></i>
                                        </button>
                                        @if (session('roleId') != 20 && session('roleId') != 19)
                                            <button type="button" class="orders-icon-action" title="Show Receipt"
                                                onclick='showReceipt("{{ $order->receipt_no }}")'>
                                                <i class="icofont icofont-printer"></i>
                                            </button>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @else
            <div class="orders-empty-state">No Record Found</div>
        @endif
    </div>
</div>
<!-- Delivery Date Modal -->
<div class="modal fade in" id="deliveryDateModal" tabindex="-1" aria-labelledby="deliveryDateModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deliveryDateModalLabel">Enter Delivery Date</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="delivery-date-input" class="form-label">Delivery Date</label>
                    <input type="date" class="form-control" id="delivery-date-input">
                    <div class="invalid-feedback" id="delivery-date-error">Please select a delivery date.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-delivery-date-btn">Mark As
                    Delivered</button>
            </div>
        </div>
    </div>
</div>
<!-- Void Reason Modal -->
{{-- <div class="modal fade in" id="void-modal" tabindex="-1" role="dialog" aria-labelledby="voidModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="voidModalLabel">Void Order(s)</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="voidId" value="">
                <div class="form-group">
                    <label for="reason">Reason for voiding:</label>
                    <textarea id="reason" class="form-control"></textarea>
                    <span id="reason_message" class="text-danger"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" onclick="saveVoid()" class="btn btn-danger"
                    id="confirm-void-btn">Void</button>
            </div>
        </div>
    </div>
</div> --}}
<script type="text/javascript">
    $(document).ready(function() {
        var voidModal = new bootstrap.Modal(document.getElementById('void-modal'));
        voidModal.hide();

        function setOrdersView(view) {
            var nextView = view || window.ordersCurrentView || 'table';
            window.ordersCurrentView = nextView;
            $('.orders-view-btn').removeClass('is-active');
            $('.orders-view-btn[data-orders-view="' + nextView + '"]').addClass('is-active');
            $('.orders-view-pane').removeClass('is-active');
            $('.orders-view-pane[data-orders-pane="' + nextView + '"]').addClass('is-active');
            toggleBulkActionBtns();
        }

        function getActiveOrderCheckboxes() {
            return $('.orders-view-pane.is-active .order-checkbox');
        }

        function getSelectedOrderIds() {
            var seen = {};
            return getActiveOrderCheckboxes().filter(':checked').map(function() {
                var value = $(this).val();
                if (seen[value]) {
                    return null;
                }
                seen[value] = true;
                return value;
            }).get();
        }

        setOrdersView(window.ordersCurrentView || 'table');

        $(document).off('click.ordersView').on('click.ordersView', '.orders-view-btn', function() {
            var requestedView = $(this).data('orders-view');
            if ((window.ordersCurrentView || 'table') === requestedView) {
                setOrdersView(requestedView);
                return;
            }

            window.ordersCurrentView = requestedView;
            if (typeof fetch_data === 'function') {
                fetch_data(1);
                return;
            }

            setOrdersView(requestedView);
        });

        $('#select-all-orders').off('change.ordersBulk').on('change.ordersBulk', function() {
            getActiveOrderCheckboxes().prop('checked', this.checked);
            toggleBulkActionBtns();
        });
        $(document).off('change.ordersBulk').on('change.ordersBulk', '.order-checkbox', function() {
            var $activeCheckboxes = getActiveOrderCheckboxes();
            if (!this.checked) {
                $('#select-all-orders').prop('checked', false);
            } else if ($activeCheckboxes.length && $activeCheckboxes.filter(':checked').length === $activeCheckboxes.length) {
                $('#select-all-orders').prop('checked', true);
            }
            toggleBulkActionBtns();
        });

        function toggleBulkActionBtns() {
            var selectedCount = getSelectedOrderIds().length;
            if (selectedCount > 0) {
                $('#void-selected-btn').prop('disabled', false);
                $('#deliver-selected-btn').prop('disabled', false);
            } else {
                $('#void-selected-btn').prop('disabled', true);
                $('#deliver-selected-btn').prop('disabled', true);
            }
        }
        // Example bulk action handlers
        let selectedOrderIdsForDelivery = [];
        $('#deliver-selected-btn').off('click').on('click', function() {
            let selectedOrderIds = getSelectedOrderIds();
            if (selectedOrderIds.length === 0) {
                alert('Please select at least one order.');
                return;
            }
            selectedOrderIdsForDelivery = selectedOrderIds;
            $('#delivery-date-input').val('');
            $('#delivery-date-input').removeClass('is-invalid');
            var deliveryModal = new bootstrap.Modal(document.getElementById('deliveryDateModal'));
            deliveryModal.show();
        });
        $('#confirm-delivery-date-btn').on('click', function() {
            let deliveryDate = $('#delivery-date-input').val();
            if (!deliveryDate) {
                $('#delivery-date-input').addClass('is-invalid');
                return;
            } else {
                $('#delivery-date-input').removeClass('is-invalid');
            }
            let csrfToken = '{{ csrf_token() }}';
            let completed = 0;
            let total = selectedOrderIdsForDelivery.length;
            let deliveryModalEl = document.getElementById('deliveryDateModal');
            let deliveryModal = bootstrap.Modal.getInstance(deliveryModalEl);
            selectedOrderIdsForDelivery.forEach(function(orderId) {
                $.ajax({
                    url: "{{ url('make-receipt-delivered') }}",
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        id: orderId,
                        reason: deliveryDate
                    },
                    dataType: "json",
                    complete: function() {
                        completed++;
                        if (completed === total) {
                            deliveryModal.hide();
                            alert(
                                'Mark as Delivered requests sent for selected orders.'
                            );
                            // Optionally, refresh the table here
                            fetch_data(1);
                        }
                    }
                });
            });
        });

        let selectedOrderIdsForVoid = [];
        $('#void-selected-btn').off('click').on('click', function() {
            let selectedOrderIds = getSelectedOrderIds();
            if (selectedOrderIds.length === 0) {
                alert('Please select at least one order.');
                return;
            }
            selectedOrderIdsForVoid = selectedOrderIds;
            $('#voidId').val(selectedOrderIdsForVoid.join(','));
            $('#reason').val('');
            $('#reason_message').html('');
            var voidModal = new bootstrap.Modal(document.getElementById('void-modal'));
            voidModal.show();
        });

        $('#confirm-void-btn').off('click').on('click', function() {
            $('#reason_message').html('');
            let reason = $('#reason').val();
            if (!reason) {
                $('#reason_message').html('Please select reason');
                return;
            }
            let csrfToken = '{{ csrf_token() }}';
            let completed = 0;
            let total = selectedOrderIdsForVoid.length;
            let voidModalEl = document.getElementById('void-modal');
            let voidModal = bootstrap.Modal.getInstance(voidModalEl);
            selectedOrderIdsForVoid.forEach(function(orderId) {
                $.ajax({
                    url: "{{ url('make-receipt-void') }}",
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        id: orderId,
                        reason: reason
                    },
                    dataType: "json",
                    complete: function() {
                        completed++;
                        if (completed === total) {
                            voidModal.hide();
                            alert('Void requests sent for selected orders.');
                            fetch_data(1);
                        }
                    }
                });
            });
        });
    });

    @php
        $collection = collect($totalorders);
        $pending = $collection->filter(fn($item) => $item->order_status_name == 'Pending')->values()->all();
        $processing = $collection->filter(fn($item) => $item->order_status_name == 'Processing')->values()->all();
        $voidOrders = $collection->filter(fn($item) => $item->order_status_name == 'Void')->values()->all();
        $deliveredOrders = $collection->filter(fn($item) => $item->order_status_name == 'Delivered')->values()->all();
        $dispatchOrders = $collection->filter(fn($item) => $item->order_status_name == 'Dispatch')->values()->all();
        $salesReturnOrders = $collection->filter(fn($item) => $item->order_status_name == 'Sales Return')->values()->all();
    @endphp


    $("#pendingorders").html("{{ count($pending) > 0 ? $pending[0]->totalorders : 0 }}");
    $("#totalpendingamount").html("Rs. {{ count($pending) > 0 ? number_format($pending[0]->sales, 0) : 0 }}");
    $("#processingorders").html("{{ count($processing) > 0 ? $processing[0]->totalorders : 0 }}");
    $("#totalprocessingamount").html("Rs. {{ count($processing) > 0 ? number_format($processing[0]->sales, 0) : 0 }}");
    $("#voidorders").html("{{ count($voidOrders) > 0 ? $voidOrders[0]->totalorders : 0 }}");
    $("#totalvoidamount").html("Rs. {{ count($voidOrders) > 0 ? number_format($voidOrders[0]->sales, 0) : 0 }}");
    $("#deliveredorders").html("{{ count($deliveredOrders) > 0 ? $deliveredOrders[0]->totalorders : 0 }}");
    $("#totaldeliveredamount").html(
        "Rs. {{ count($deliveredOrders) > 0 ? number_format($deliveredOrders[0]->sales, 0) : 0 }}");
    $("#dispatchorders").html("{{ count($dispatchOrders) > 0 ? $dispatchOrders[0]->totalorders : 0 }}");
    $("#totaldispatchamount").html(
        "Rs. {{ count($dispatchOrders) > 0 ? number_format($dispatchOrders[0]->sales, 0) : 0 }}");
    $("#salesreturnorders").html("{{ count($salesReturnOrders) > 0 ? $salesReturnOrders[0]->totalorders : 0 }}");
    $("#totalsalesreturnamount").html(
        "Rs. {{ count($salesReturnOrders) > 0 ? number_format($salesReturnOrders[0]->sales, 0) : 0 }}");
    $("#totalorders").html(
        "{{ (count($processing) > 0 ? $processing[0]->totalorders : 0) + (count($voidOrders) > 0 ? $voidOrders[0]->totalorders : 0) + (count($deliveredOrders) > 0 ? $deliveredOrders[0]->totalorders : 0) + (count($pending) > 0 ? $pending[0]->totalorders : 0) + (count($dispatchOrders) > 0 ? $dispatchOrders[0]->totalorders : 0) + (count($salesReturnOrders) > 0 ? $salesReturnOrders[0]->totalorders : 0) }}"
    );
    $("#totalamount").html(
        "Rs. {{ number_format((count($processing) > 0 ? $processing[0]->sales : 0) + (count($voidOrders) > 0 ? $voidOrders[0]->sales : 0) + (count($deliveredOrders) > 0 ? $deliveredOrders[0]->sales : 0) + (count($pending) > 0 ? $pending[0]->sales : 0) + (count($dispatchOrders) > 0 ? $dispatchOrders[0]->sales : 0) + (count($salesReturnOrders) > 0 ? $salesReturnOrders[0]->sales : 0), 0) }}"
    );
    $("#totaltaxorders").html(
        "{{ count($totaltax) > 0 && $totaltax[0]->srbtaxamount > 0 ? $totaltax[0]->totalorders : 0 }}");
    $("#totaltaxamount").html("Rs. {{ count($totaltax) > 0 ? $totaltax[0]->srbtaxamount : 0 }}");
    $("#ordersVolumePeak").text("-");
    $("#ordersVolumeAvg").text("-");
    $("#ordersRevenueTotal").text("-");
    $("#ordersRevenuePeak").text("-");
    @if (!empty($orderTimingGraph))
        $(document).ready(function() {
            function formatCurrency(value) {
                return 'Rs. ' + Number(value || 0).toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }

            function formatCount(value) {
                return Number(value || 0).toLocaleString('en-US');
            }

            function average(values) {
                if (!values.length) {
                    return 0;
                }
                return values.reduce(function(sum, value) {
                    return sum + Number(value || 0);
                }, 0) / values.length;
            }

            function highestPoint(labels, values) {
                if (!values.length) {
                    return {
                        label: '-',
                        value: 0
                    };
                }

                var maxValue = Math.max.apply(null, values);
                var index = values.indexOf(maxValue);

                return {
                    label: labels[index] || '-',
                    value: maxValue
                };
            }

            var volumeLabels = {!! json_encode($orderTimingGraph->pluck('hour_range')->values()) !!};
            var volumeData = {!! json_encode($orderTimingGraph->pluck('total_orders')->map(fn($value) => (float) $value)->values()) !!};
            var revenueData = {!! json_encode($orderTimingGraph->pluck('total_amount')->map(fn($value) => (float) $value)->values()) !!};
            var height = Number("{{ $height }}") || 220;
            var peakVolume = highestPoint(volumeLabels, volumeData);
            var peakRevenue = highestPoint(volumeLabels, revenueData);
            var totalRevenue = revenueData.reduce(function(sum, value) {
                return sum + Number(value || 0);
            }, 0);

            $("#ordersVolumePeak").text(peakVolume.label + ' - ' + formatCount(peakVolume.value));
            $("#ordersVolumeAvg").text(formatCount(Math.round(average(volumeData))));
            $("#ordersRevenueTotal").text(formatCurrency(totalRevenue));
            $("#ordersRevenuePeak").text(peakRevenue.label + ' - ' + formatCurrency(peakRevenue.value));

            var chLine = document.getElementById("chLine");
            var lineCtx = chLine ? chLine.getContext('2d') : null;
            var lineGradient = lineCtx ? lineCtx.createLinearGradient(0, 0, 0, 280) : null;
            if (lineGradient) {
                lineGradient.addColorStop(0, 'rgba(37, 99, 235, 0.28)');
                lineGradient.addColorStop(1, 'rgba(37, 99, 235, 0.02)');
            }

            var lineChartData = {
                labels: volumeLabels,
                datasets: [{
                    label: 'Orders',
                    data: volumeData,
                    backgroundColor: lineGradient || 'rgba(37, 99, 235, 0.12)',
                    borderColor: '#2563eb',
                    borderWidth: 3,
                    fill: true,
                    lineTension: 0.38,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2
                }]
            };
            if (chLine) {
                chLine.height = height;
                if (window.ordersLineChart) {
                    window.ordersLineChart.destroy();
                }

                window.ordersLineChart = new Chart(chLine, {
                    type: 'line',
                    data: lineChartData,
                    options: {
                        legend: {
                            display: false
                        },
                        hover: {
                            mode: 'index',
                            intersect: false
                        },
                        tooltips: {
                            enabled: true,
                            backgroundColor: '#0f172a',
                            titleFontColor: '#ffffff',
                            bodyFontColor: '#e2e8f0',
                            displayColors: false,
                            callbacks: {
                                label: function(tooltipItem) {
                                    return formatCount(tooltipItem.yLabel) + ' orders';
                                }
                            }
                        },
                        animation: {
                            duration: 450
                        },
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    precision: 0,
                                    fontColor: '#64748b',
                                    padding: 10
                                },
                                gridLines: {
                                    color: 'rgba(148, 163, 184, 0.18)',
                                    drawBorder: false
                                }
                            }],
                            xAxes: [{
                                ticks: {
                                    fontColor: '#64748b',
                                    maxRotation: 0,
                                    minRotation: 0
                                },
                                gridLines: {
                                    display: false,
                                    drawBorder: false
                                }
                            }]
                        },
                        responsive: true
                    }
                });
            }

            var chBarOne = document.getElementById("chBarOne");
            var barCtx = chBarOne ? chBarOne.getContext('2d') : null;
            var barGradient = barCtx ? barCtx.createLinearGradient(0, 0, 0, 280) : null;
            if (barGradient) {
                barGradient.addColorStop(0, 'rgba(22, 163, 74, 0.88)');
                barGradient.addColorStop(1, 'rgba(74, 222, 128, 0.42)');
            }

            if (chBarOne) {
                chBarOne.height = height;
                if (window.ordersRevenueChart) {
                    window.ordersRevenueChart.destroy();
                }

                window.ordersRevenueChart = new Chart(chBarOne, {
                    type: 'bar',
                    data: {
                        labels: volumeLabels,
                        datasets: [{
                            label: 'Sales',
                            data: revenueData,
                            backgroundColor: barGradient || '#16a34a',
                            borderColor: '#15803d',
                            borderWidth: 1.5,
                            hoverBackgroundColor: '#22c55e'
                        }]
                    },
                    options: {
                        legend: {
                            display: false
                        },
                        tooltips: {
                            enabled: true,
                            backgroundColor: '#0f172a',
                            titleFontColor: '#ffffff',
                            bodyFontColor: '#e2e8f0',
                            displayColors: false,
                            callbacks: {
                                label: function(tooltipItem) {
                                    return formatCurrency(tooltipItem.yLabel);
                                }
                            }
                        },
                        animation: {
                            duration: 450
                        },
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                display: true,
                                ticks: {
                                    beginAtZero: true,
                                    fontColor: '#64748b',
                                    padding: 10,
                                    callback: function(value) {
                                        return value >= 1000 ? (value / 1000) + 'k' : value;
                                    }
                                },
                                gridLines: {
                                    color: 'rgba(148, 163, 184, 0.18)',
                                    drawBorder: false
                                }
                            }],
                            xAxes: [{
                                ticks: {
                                    fontColor: '#64748b',
                                    maxRotation: 0,
                                    minRotation: 0
                                },
                                gridLines: {
                                    display: false,
                                    drawBorder: false
                                },
                                barPercentage: 0.5,
                                categoryPercentage: 1
                            }]
                        }
                    }
                });
            }

        })
    @else
        if (window.ordersLineChart) {
            window.ordersLineChart.destroy();
            window.ordersLineChart = null;
        }
        if (window.ordersRevenueChart) {
            window.ordersRevenueChart.destroy();
            window.ordersRevenueChart = null;
        }
    @endif
</script>
