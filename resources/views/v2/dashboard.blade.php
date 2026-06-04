@extends('layouts.master-tailwind')

@section('title', 'Dashboard')
@section('page_title', 'User Dashboard')
@section('page_subtitle', 'Monitor sales, inventory, order health, and branch performance from the V2 workspace.')
@section('breadcrumtitle', 'Dashboard')
@section('navdashboard', 'active')
@section('dashboardInlineCSS', 1)

@php
    $projectedSales = empty($projected) ? 0 : (float) ($projected[0]->sales ?? 0);
    $closedSales = empty($totalSales) ? 0 : (float) ($totalSales[0]->TotalSales ?? 0);
    $totalItems = empty($totalstock) ? 0 : (int) ($totalstock[0]->products ?? 0);
    $orderStats = $orders[0] ?? null;
    $totalOrders = $orderStats ? (int) ($orderStats->total ?? 0) : 0;
    $pendingOrders = $orderStats ? (int) ($orderStats->pending ?? 0) : 0;
    $processingOrders = $orderStats ? (int) ($orderStats->processing ?? 0) : 0;
    $readyOrders = $orderStats ? (int) ($orderStats->ready ?? 0) : 0;
    $deliveredOrders = $orderStats ? (int) ($orderStats->delivery ?? 0) : 0;
    $cancelledOrders = $orderStats ? (int) ($orderStats->cancelled ?? 0) : 0;
    $orderReportDate = $orderStats && !empty($orderStats->report_date) ? $orderStats->report_date : date('Y-m-d');
    $orderReportLabel = $orderReportDate === date('Y-m-d') ? 'Today' : 'Yesterday';
    $branchSalesTotal = collect($branches ?? [])->sum('sales');

    $orderCards = [
        ['label' => 'Pending', 'value' => $pendingOrders, 'class' => 'pending', 'icon' => 'icofont-wall-clock'],
        ['label' => 'Processing', 'value' => $processingOrders, 'class' => 'processing', 'icon' => 'icofont-refresh'],
        ['label' => 'Ready', 'value' => $readyOrders, 'class' => 'ready', 'icon' => 'icofont-check-circled'],
        ['label' => 'Delivered', 'value' => $deliveredOrders, 'class' => 'delivered', 'icon' => 'icofont-truck-loaded'],
        ['label' => 'Cancelled', 'value' => $cancelledOrders, 'class' => 'cancelled', 'icon' => 'icofont-close-circled'],
    ];
@endphp

@section('content')
    @if ($permission)
        <div class="erp-dashboard">
            <div class="dashboard-hero">
                <div>
                    <span class="hero-kicker">Retail overview</span>
                    <h1>Dashboard</h1>
                    <p>{{ date('l, d M Y') }} - {{ ucfirst(Auth::user()->fullname ?? Auth::user()->username ?? 'User') }}</p>
                </div>
                <div class="hero-actions">
                    <div class="hero-stat">
                        <span>Projected</span>
                        <strong>{{ number_format($projectedSales, 0) }}</strong>
                    </div>
                    <div class="hero-stat">
                        <span>Closed</span>
                        <strong>{{ number_format($closedSales, 0) }}</strong>
                    </div>
                    <div class="hero-stat">
                        <span>Orders</span>
                        <strong>{{ number_format($totalOrders) }}</strong>
                    </div>
                    <button type="button" class="hero-btn hero-btn-primary" onclick="getdetails()">
                        <i class="icofont icofont-eye-alt"></i>
                        Sales Detail
                    </button>
                </div>
            </div>

            <div class="row metric-row">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div id="projectedSales" class="premium-metric metric-green">
                        <div class="metric-icon"><i class="icofont icofont-chart-line"></i></div>
                        <span>Projected Sales</span>
                        <strong>{{ number_format($projectedSales, 2) }}</strong>
                        <small>Based on recent weekday trend</small>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div id="closedSales" class="premium-metric metric-blue" onclick="getdetails()">
                        <div class="metric-icon"><i class="icofont icofont-money-bag"></i></div>
                        <span>All Closed Sales</span>
                        <strong>{{ number_format($closedSales, 2) }}</strong>
                        <small>Open terminal sales summary</small>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="premium-metric metric-gold">
                        <div class="metric-icon"><i class="icofont icofont-box"></i></div>
                        <span>Active Items</span>
                        <strong>{{ number_format($totalItems) }}</strong>
                        <small>Inventory products in scope</small>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="premium-metric metric-red">
                        <div class="metric-icon"><i class="icofont icofont-shopping-cart"></i></div>
                        <span>Total Orders</span>
                        <strong>{{ number_format($totalOrders) }}</strong>
                        <small>Current order pipeline</small>
                    </div>
                </div>
            </div>

            <div class="row dashboard-grid">
                <div class="col-xl-8 col-lg-12">
                    <div class="premium-panel chart-panel">
                        <div class="panel-heading">
                            <div>
                                <span class="panel-kicker">Daily terminal mix</span>
                                <h3>Terminal Sales By Payment Mode</h3>
                            </div>
                            <div class="panel-legend">
                                <span><i class="legend-dot cash"></i><b>Cash</b></span>
                                <span><i class="legend-dot card"></i><b>Card</b></span>
                                <span><i class="legend-dot credit"></i><b>Customer Credit</b></span>
                            </div>
                        </div>
                        <div id="bar-example1" class="chart-canvas chart-large"></div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-12">
                    <div class="premium-panel order-panel">
                        <div class="panel-heading compact">
                            <div>
                                <span class="panel-kicker">Order health - {{ $orderReportLabel }}</span>
                                <h3>Status Board</h3>
                                <small class="panel-note">{{ date('d M Y', strtotime($orderReportDate)) }}</small>
                            </div>
                            <div class="order-total-badge">
                                <span>Total</span>
                                <strong>{{ number_format($totalOrders) }}</strong>
                            </div>
                        </div>

                        @if ($totalOrders > 0)
                            <div class="order-status-grid">
                                @foreach ($orderCards as $card)
                                    @php
                                        $percentage = round(($card['value'] / $totalOrders) * 100);
                                    @endphp
                                    <div class="order-status status-{{ $card['class'] }}">
                                        <div class="status-top">
                                            <span><i class="icofont {{ $card['icon'] }}"></i>{{ $card['label'] }}</span>
                                            <strong>{{ number_format($card['value']) }}</strong>
                                        </div>
                                        <div class="status-meta">{{ $percentage }}% of orders</div>
                                        <div class="status-track">
                                            <div style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="panel-empty-state order-empty-state">
                                <i class="icofont icofont-shopping-cart"></i>
                                <strong>No order activity</strong>
                                <span>There are no orders for {{ date('d M Y', strtotime($orderReportDate)) }}.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="dashboard-livewire">
                <livewire:dashboard.monthly-sales-chart />
            </div>

            <div class="row dashboard-grid">
                <div class="col-xl-4 col-lg-5">
                    <div class="premium-panel">
                        <div class="panel-heading">
                            <div>
                                <span class="panel-kicker">Best sellers</span>
                                <h3>Top 5 Products</h3>
                            </div>
                        </div>
                        <div id="donut-example" class="chart-canvas chart-medium"></div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-7">
                    <div class="premium-panel yearly-panel">
                        <div class="panel-heading">
                            <div>
                                <span class="panel-kicker">Long range</span>
                                <h3>Yearly Sales Trend</h3>
                            </div>
                            <span class="yearly-range-badge">Last 5 Years</span>
                        </div>
                        <div id="line-example" class="chart-canvas chart-medium"></div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-12">
                    <div class="premium-panel branch-panel">
                        <div class="panel-heading compact">
                            <div>
                                <span class="panel-kicker">Today</span>
                                <h3>Branch / Terminal Sales</h3>
                            </div>
                            <strong>{{ number_format($branchSalesTotal, 2) }}</strong>
                        </div>
                        <div class="branch-list">
                            @forelse ($branches as $branch)
                                <div class="branch-item">
                                    <div>
                                        <span>{{ ($branch->identify ?? '') === 'terminal' ? ($branch->terminal_name ?? 'Terminal') : ($branch->branch_name ?? 'Branch') }}</span>
                                        <small>{{ ucfirst($branch->identify ?? 'sales') }}</small>
                                    </div>
                                    <strong>{{ number_format((float) ($branch->sales ?? 0), 2) }}</strong>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="icofont-chart-bar-graph"></i>
                                    <span>No branch sales found for today.</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <table id="tblcheques" class="d-none">
                <tbody></tbody>
            </table>

            <script type="text/javascript">
                (function() {
                    var terminalSalesData = [
                        @foreach ($sales as $saleValue)
                            {
                                y: @json($saleValue->terminal_name),
                                a: {{ (float) $saleValue->cash }},
                                b: {{ (float) $saleValue->creditCard }},
                                c: {{ (float) $saleValue->CustomerCredit }}
                            },
                        @endforeach
                    ];

                    var productDonutData = [
                        @foreach ($products as $value)
                            {
                                label: @json($value->product_name),
                                value: {{ (float) $value->count }}
                            },
                        @endforeach
                    ];

                    var yearlySalesData = [
                        @foreach ($year as $value)
                            {
                                y: @json((string) $value->year),
                                a: {{ (float) $value->amount }}
                            },
                        @endforeach
                    ];

                    function drawEmptyState(elementId, iconClass, title, message) {
                        var element = document.getElementById(elementId);
                        if (!element) {
                            return;
                        }

                        element.classList.add('chart-empty');
                        element.innerHTML = '<div class="panel-empty-state">' +
                            '<i class="' + iconClass + '"></i>' +
                            '<strong>' + title + '</strong>' +
                            '<span>' + message + '</span>' +
                            '</div>';
                    }

                    function drawDashboardCharts() {
                        if (typeof Morris === 'undefined') {
                            return;
                        }

                        if (document.getElementById('bar-example1')) {
                            var hasTerminalSales = terminalSalesData.some(function(row) {
                                return Number(row.a || 0) > 0 || Number(row.b || 0) > 0 || Number(row.c || 0) > 0;
                            });

                            if (hasTerminalSales) {
                                Morris.Bar({
                                    element: 'bar-example1',
                                    barGap: 3,
                                    barSizeRatio: 0.38,
                                    data: terminalSalesData,
                                    xkey: 'y',
                                    ykeys: ['a', 'b', 'c'],
                                    labels: ['Cash', 'Credit Card', 'Customer Credit'],
                                    barColors: ['#4CAF50', '#2196F3', '#FFC107'],
                                    gridTextColor: '#6b7280',
                                    gridLineColor: '#eef2f6',
                                    hideHover: 'auto',
                                    resize: true
                                });
                            } else {
                                drawEmptyState('bar-example1', 'icofont icofont-chart-bar-graph', 'No terminal sales', 'No cash, card, or credit sales are available for this period.');
                            }
                        }

                        if (document.getElementById('donut-example')) {
                            Morris.Donut({
                                element: 'donut-example',
                                data: productDonutData.length ? productDonutData : [{ label: 'No Products', value: 1 }],
                                colors: ['#4CAF50', '#2196F3', '#FFC107', '#F44336', '#00BCD4'],
                                formatter: function(value) {
                                    return value;
                                },
                                resize: true
                            });
                        }

                        if (document.getElementById('line-example')) {
                            Morris.Bar({
                                element: 'line-example',
                                data: yearlySalesData.length ? yearlySalesData : [{ y: '{{ date('Y') }}', a: 0 }],
                                xkey: 'y',
                                ykeys: ['a'],
                                labels: ['Sales'],
                                barColors: ['#4CAF50'],
                                barSizeRatio: 0.42,
                                barGap: 6,
                                gridTextColor: '#6b7280',
                                gridLineColor: '#eef2f6',
                                hideHover: 'auto',
                                resize: true,
                                hoverCallback: function(index, options, content, row) {
                                    return '<div class="morris-hover-row-label">' + row.y + '</div>' +
                                        '<div class="morris-hover-point">Sales: ' + Number(row.a || 0).toLocaleString() + '</div>';
                                }
                            });
                        }
                    }

                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', drawDashboardCharts);
                    } else {
                        drawDashboardCharts();
                    }
                })();
            </script>
        </div>
    @endif
@endsection

@section('css_code')
    <style>
        a {
            text-decoration: none !important;
        }

        #cover-spin {
            position: fixed;
            width: 100%;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            display: none;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        #cover-spin::after {
            content: '';
            display: block;
            position: absolute;
            left: 48%;
            top: 40%;
            width: 40px;
            height: 40px;
            border-style: solid;
            border-color: #111827;
            border-top-color: transparent;
            border-width: 4px;
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }

        .erp-dashboard {
            color: #17202c;
            margin: -4px -4px 0;
            padding: 4px;
        }

        .dashboard-hero {
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            gap: 24px;
            padding: 26px;
            margin-bottom: 24px;
            border-radius: 8px;
            background:
                linear-gradient(135deg, rgba(15, 54, 39, .98), rgba(38, 116, 47, .96) 62%, rgba(99, 166, 70, .92)),
                radial-gradient(circle at 90% 10%, rgba(255, 255, 255, .18), transparent 28%);
            background-size: cover;
            box-shadow: 0 20px 46px rgba(18, 61, 43, .18);
            color: #fff;
        }

        .hero-kicker,
        .panel-kicker {
            display: block;
            margin-bottom: 6px;
            color: #6b7280;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .dashboard-hero .hero-kicker,
        .dashboard-hero p {
            color: rgba(255, 255, 255, .78);
        }

        .dashboard-hero h1 {
            margin: 0;
            font-size: 31px;
            font-weight: 800;
            letter-spacing: 0;
        }

        .dashboard-hero p {
            margin: 7px 0 0;
            font-size: 14px;
        }

        .hero-actions {
            display: grid;
            grid-template-columns: repeat(3, minmax(112px, 1fr)) auto;
            gap: 12px;
            align-content: center;
            align-items: center;
            min-width: 520px;
        }

        .hero-stat {
            min-height: 58px;
            padding: 11px 12px;
            border: 1px solid rgba(255, 255, 255, .58);
            border-radius: 8px;
            background: rgba(255, 255, 255, .92);
            text-align: left;
            box-shadow: 0 12px 24px rgba(15, 23, 42, .12);
        }

        .hero-stat span {
            display: block;
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
            line-height: 1.1;
            text-transform: uppercase;
        }

        .hero-stat strong {
            display: block;
            margin-top: 5px;
            color: #0f172a;
            font-size: 17px;
            font-weight: 900;
            line-height: 1.1;
            white-space: nowrap;
        }

        .hero-btn {
            min-height: 58px;
            border: 1px solid rgba(255, 255, 255, .72);
            background: #ffffff;
            color: #21662d;
            border-radius: 8px;
            padding: 0 18px;
            font-weight: 800;
            cursor: pointer;
            transition: .2s ease;
            box-shadow: 0 12px 24px rgba(15, 23, 42, .12);
        }

        .hero-btn i {
            margin-right: 7px;
        }

        .hero-btn:hover,
        .hero-btn-primary {
            background: #ffffff;
            color: #2f7d32;
        }

        .metric-row,
        .dashboard-grid {
            margin-left: 0;
            margin-right: 0;
        }

        .metric-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 22px;
            margin-bottom: 24px;
        }

        .dashboard-grid {
            display: grid;
            gap: 22px;
            margin-bottom: 22px;
        }

        .metric-row > [class*="col-"],
        .dashboard-grid > [class*="col-"] {
            display: flex;
            width: auto;
            max-width: none;
            min-width: 0;
            flex: none;
            padding-left: 0;
            padding-right: 0;
            margin-bottom: 0;
        }

        .dashboard-grid > .col-xl-8 {
            grid-column: span 9;
        }

        .dashboard-grid > .col-xl-4 {
            grid-column: span 3;
        }

        .dashboard-grid > .col-lg-5,
        .dashboard-grid > .col-lg-7,
        .dashboard-grid > .col-lg-12 {
            grid-column: span 4;
        }

        .dashboard-grid {
            grid-template-columns: repeat(12, minmax(0, 1fr));
        }

        .premium-metric,
        .premium-panel {
            position: relative;
            overflow: hidden;
            min-height: 100%;
            border: 1px solid #e6ebf1;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 16px 36px rgba(15, 23, 42, .09);
        }

        .premium-metric {
            display: flex;
            flex-direction: column;
            width: 100%;
            min-height: 150px;
            padding: 22px;
            cursor: default;
            transition: .2s ease;
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfd 100%);
        }

        #closedSales,
        #projectedSales {
            cursor: pointer;
        }

        .premium-metric:hover,
        .premium-panel:hover {
            transform: translateY(-3px);
            border-color: rgba(76, 175, 80, .24);
            box-shadow: 0 22px 48px rgba(15, 23, 42, .13);
        }

        .premium-metric::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 4px;
            width: 100%;
            background: var(--accent);
        }

        .metric-green { --accent: #4CAF50; }
        .metric-blue { --accent: #2196F3; }
        .metric-gold { --accent: #FFC107; }
        .metric-red { --accent: #F44336; }

        .metric-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 46px;
            margin-bottom: 16px;
            border: 1px solid rgba(0, 0, 0, .04);
            border-radius: 50%;
            color: var(--accent);
            font-size: 21px;
            line-height: 1;
            box-shadow: inset 0 0 0 6px rgba(255, 255, 255, .55);
        }

        .metric-icon i {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 1em;
            height: 1em;
            line-height: 1;
        }

        .metric-green .metric-icon { background: rgba(76, 175, 80, .13); }
        .metric-blue .metric-icon { background: rgba(33, 150, 243, .12); }
        .metric-gold .metric-icon { background: rgba(255, 193, 7, .18); }
        .metric-red .metric-icon { background: rgba(244, 67, 54, .12); }

        .premium-metric span {
            display: block;
            color: #6b7280;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .premium-metric strong {
            display: block;
            margin: 9px 0 5px;
            font-size: 26px;
            font-weight: 800;
            line-height: 1.1;
            color: #111827;
            word-break: break-word;
        }

        .premium-metric small {
            color: #8a94a3;
            font-size: 12px;
        }

        .premium-panel {
            padding: 24px;
            width: 100%;
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfd 100%);
        }

        .chart-panel,
        .order-panel {
            min-height: 430px;
        }

        .panel-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .panel-heading.compact {
            align-items: center;
        }

        .panel-heading h3 {
            margin: 0;
            color: #111827;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0;
        }

        .panel-heading strong {
            color: #111827;
            font-size: 18px;
        }

        .panel-note {
            display: block;
            margin-top: 4px;
            color: #8a94a3;
            font-size: 12px;
            font-weight: 700;
        }

        .panel-legend {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
            align-items: center;
            max-width: 290px;
        }

        .panel-legend span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 28px;
            padding: 0 10px;
            border: 1px solid #eef2f6;
            border-radius: 6px;
            background: #fbfcfd;
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
            line-height: 28px;
            white-space: nowrap;
        }

        .panel-legend b {
            display: inline-block;
            font: inherit;
            line-height: 1;
        }

        .legend-dot {
            display: block;
            flex: 0 0 auto;
            width: 9px;
            height: 9px;
            margin: 0;
            border-radius: 50%;
            transform: translateY(0);
        }

        .legend-dot.cash { background: #4CAF50; }
        .legend-dot.card { background: #2196F3; }
        .legend-dot.credit { background: #FFC107; }

        .chart-canvas {
            width: 100%;
            min-height: 250px;
        }

        .chart-empty {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chart-large {
            height: 322px;
        }

        .chart-medium {
            height: 285px;
        }

        .yearly-panel {
            background: linear-gradient(180deg, #ffffff 0%, #fbfdfb 100%);
        }

        .yearly-range-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 28px;
            padding: 0 10px;
            border: 1px solid rgba(76, 175, 80, .18);
            border-radius: 6px;
            background: rgba(76, 175, 80, .09);
            color: #2f7d32;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .yearly-panel .morris-hover {
            border: 0;
            border-radius: 8px;
            background: rgba(17, 24, 39, .95);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(15, 23, 42, .18);
        }

        .yearly-panel .morris-hover-row-label,
        .yearly-panel .morris-hover-point {
            color: #ffffff !important;
        }

        .order-total-badge {
            min-width: 84px;
            padding: 10px 12px;
            border-radius: 8px;
            background: #edf8ee;
            color: #2f7d32;
            text-align: right;
        }

        .order-total-badge span {
            display: block;
            color: #6b7280;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .order-status-grid {
            display: grid;
            gap: 9px;
        }

        .panel-empty-state {
            display: flex;
            min-height: 250px;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 8px;
            border: 1px dashed #d8e1ec;
            border-radius: 8px;
            background: #f8fafc;
            color: #64748b;
            text-align: center;
        }

        .panel-empty-state i {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #edf8ee;
            color: #2f7d32;
            font-size: 20px;
        }

        .panel-empty-state strong {
            color: #0f172a;
            font-size: 16px;
            font-weight: 900;
        }

        .panel-empty-state span {
            max-width: 290px;
            font-size: 13px;
            line-height: 1.5;
        }

        .order-empty-state {
            min-height: 318px;
        }

        .order-status {
            padding: 10px 12px;
            border: 1px solid #edf1f5;
            border-left: 4px solid var(--status-color);
            border-radius: 8px;
            background: #fbfcfd;
        }

        .status-top,
        .branch-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .status-top span {
            color: #4b5563;
            font-size: 13px;
            font-weight: 700;
        }

        .status-top i {
            margin-right: 8px;
        }

        .status-meta {
            margin-top: 5px;
            color: #8a94a3;
            font-size: 11px;
            font-weight: 700;
        }

        .status-track {
            height: 7px;
            margin-top: 8px;
            border-radius: 99px;
            background: #edf1f5;
            overflow: hidden;
        }

        .status-track div {
            height: 100%;
            border-radius: inherit;
            background: var(--status-color);
        }

        .status-pending { --status-color: #FFC107; }
        .status-processing { --status-color: #2196F3; }
        .status-ready { --status-color: #4CAF50; }
        .status-delivered { --status-color: #00BCD4; }
        .status-cancelled { --status-color: #F44336; }

        .dashboard-livewire {
            margin-bottom: 22px;
        }

        .branch-list {
            max-height: 302px;
            overflow: auto;
            padding-right: 4px;
        }

        .branch-item {
            padding: 13px 0;
            border-bottom: 1px solid #eef2f6;
        }

        .branch-item:last-child {
            border-bottom: 0;
        }

        .branch-item span,
        .branch-item strong {
            color: #111827;
            font-weight: 800;
        }

        .branch-item small {
            display: block;
            margin-top: 3px;
            color: #8a94a3;
            text-transform: capitalize;
        }

        .empty-state {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 28px 0;
            color: #6b7280;
            font-weight: 700;
        }

        .bg-success {
            background-color: #4CAF50 !important;
        }

        nav .navbar {
            position: relative;
        }

        .navbar-custom-menu {
            position: absolute;
            right: 0;
        }

        @media (max-width: 991px) {
            .dashboard-hero {
                align-items: flex-start;
                flex-direction: column;
            }

            .hero-actions {
                width: 100%;
                min-width: 0;
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .hero-btn {
                grid-column: 1 / -1;
            }

            .metric-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 18px;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .dashboard-grid > .col-xl-8,
            .dashboard-grid > .col-xl-4,
            .dashboard-grid > .col-lg-5,
            .dashboard-grid > .col-lg-7,
            .dashboard-grid > .col-lg-12 {
                grid-column: span 1;
            }

        }

        @media (max-width: 790px) {
            section {
                margin-top: 61px;
            }

            body {
                line-height: 1.35;
            }

            .container-fluid {
                margin-top: 65px;
            }

            .dashboard-hero {
                padding: 22px;
            }

            .dashboard-hero h1 {
                font-size: 25px;
            }

            .hero-actions {
                grid-template-columns: 1fr;
            }

            .hero-btn {
                width: 100%;
            }

            .metric-row {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .premium-metric,
            .premium-panel {
                border-radius: 8px;
            }

            .premium-metric {
                min-height: auto;
            }

            .panel-heading,
            .status-top,
            .branch-item {
                align-items: flex-start;
                flex-direction: column;
            }

            .chart-large,
            .chart-medium {
                height: 260px;
            }
        }
    </style>
@endsection

@section('scriptcode_one')
    <script>
        function getdetails() {
            window.location = "{{ url('sales-details') }}";
        }
    </script>
@endsection

@section('scriptcode_three')
    <script type="text/javascript">
        <?php if (session('login_msg')) { ?>
        $(document).ready(function() {
            notify('{{ session('login_msg') }}', 'success');
            <?php $_SESSION['login_msg'] = ''; ?>
        });
        <?php } ?>

        $(document).ready(function() {
            getcheques();
        });

        function getcheques() {
            var d = new Date(),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) {
                month = '0' + month;
            }

            if (day.length < 2) {
                day = '0' + day;
            }

            var today = [year, month, day].join('-');
            var date = moment(new Date());
            date.add(1, 'days');
            var tomorrow = date.format('YYYY-MM-DD');

            $.ajax({
                url: "{{ url('/getcheques') }}",
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                dataType: 'json',
                success: function(result) {
                    if (result && $("#tblcheques").length) {
                        $("#tblcheques tbody").empty();
                        for (var count = 0; count < result.length; count++) {
                            $("#tblcheques tbody").append(
                                "<tr>" +
                                "<td>" + today + "</td>" +
                                "<td>" + result[count].todays + "</td>" +
                                "<td><a href='{{ url('/chequemodule') }}/" + today + "'><i class='icofont icofont-eye-alt'></i></a></td>" +
                                "</tr>" +
                                "<tr>" +
                                "<td>" + tomorrow + "</td>" +
                                "<td>" + result[count].tomorrow + "</td>" +
                                "<td><a href='{{ url('/chequemodule') }}/" + tomorrow + "'><i class='icofont icofont-eye-alt'></i></a></td>" +
                                "</tr>"
                            );
                        }
                    }
                }
            });
        }

        function openReport() {
            window.location = "{{ url('profitLossStandardReport') }}" +
                "?fromdate={{ $currentDate }}&todate={{ $currentDate }}";
        }

        function openExpenseReport() {
            window.location = "{{ url('expense-report-pdf') }}" +
                "?first={{ $currentDate }}&second={{ $currentDate }}";
        }
    </script>
@endsection
