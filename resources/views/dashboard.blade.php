@extends('layouts.master-layout')

@section('title', 'Dashboard')
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
                    <button type="button" class="hero-btn" onclick="openReport()">
                        <i class="icofont-chart-line"></i>
                        Profit & Loss
                    </button>
                    <button type="button" class="hero-btn hero-btn-primary" onclick="getdetails()">
                        <i class="icofont-eye-alt"></i>
                        Sales Detail
                    </button>
                </div>
            </div>

            <div class="row metric-row">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div id="projectedSales" class="premium-metric metric-green">
                        <div class="metric-icon"><i class="icofont icofont-chart-growth"></i></div>
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

                        <div class="order-status-grid">
                            @foreach ($orderCards as $card)
                                @php
                                    $percentage = $totalOrders > 0 ? round(($card['value'] / $totalOrders) * 100) : 0;
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
                    <div class="premium-panel">
                        <div class="panel-heading">
                            <div>
                                <span class="panel-kicker">Long range</span>
                                <h3>Yearly Sales Trend</h3>
                            </div>
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

                    function drawDashboardCharts() {
                        if (typeof Morris === 'undefined') {
                            return;
                        }

                        if (document.getElementById('bar-example1')) {
                            Morris.Bar({
                                element: 'bar-example1',
                                barGap: 3,
                                barSizeRatio: 0.38,
                                data: terminalSalesData.length ? terminalSalesData : [{ y: 'No Sales', a: 0, b: 0, c: 0 }],
                                xkey: 'y',
                                ykeys: ['a', 'b', 'c'],
                                labels: ['Cash', 'Credit Card', 'Customer Credit'],
                                barColors: ['#4CAF50', '#2196F3', '#FFC107'],
                                gridTextColor: '#6b7280',
                                gridLineColor: '#eef2f6',
                                hideHover: 'auto',
                                resize: true
                            });
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
                            Morris.Line({
                                element: 'line-example',
                                data: yearlySalesData.length ? yearlySalesData : [{ y: '{{ date('Y') }}', a: 0 }],
                                xkey: 'y',
                                ykeys: ['a'],
                                labels: ['Sales'],
                                lineColors: ['#4CAF50'],
                                pointFillColors: ['#ffffff'],
                                pointStrokeColors: ['#4CAF50'],
                                gridTextColor: '#6b7280',
                                gridLineColor: '#eef2f6',
                                hideHover: 'auto',
                                resize: true
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
        }

        .dashboard-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 26px 28px;
            margin-bottom: 18px;
            border-radius: 8px;
            background: linear-gradient(135deg, #4CAF50, #2f7d32);
            background-size: cover;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .14);
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
            font-size: 30px;
            font-weight: 800;
            letter-spacing: 0;
        }

        .dashboard-hero p {
            margin: 7px 0 0;
            font-size: 14px;
        }

        .hero-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .hero-btn {
            border: 1px solid rgba(255, 255, 255, .32);
            background: rgba(255, 255, 255, .12);
            color: #fff;
            border-radius: 6px;
            padding: 10px 14px;
            font-weight: 700;
            cursor: pointer;
            transition: .2s ease;
        }

        .hero-btn i {
            margin-right: 7px;
        }

        .hero-btn:hover,
        .hero-btn-primary {
            background: #ffffff;
            color: #2f7d32;
        }

        .metric-row > [class*="col-"],
        .dashboard-grid > [class*="col-"] {
            margin-bottom: 18px;
        }

        .dashboard-grid {
            display: flex;
            flex-wrap: wrap;
        }

        .dashboard-grid > [class*="col-"] {
            display: flex;
        }

        .premium-metric,
        .premium-panel {
            position: relative;
            overflow: hidden;
            min-height: 100%;
            border: 1px solid #e6ebf1;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .07);
        }

        .premium-metric {
            padding: 20px 20px 18px;
            cursor: default;
            transition: .2s ease;
        }

        #closedSales,
        #projectedSales {
            cursor: pointer;
        }

        .premium-metric:hover,
        .premium-panel:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 38px rgba(15, 23, 42, .11);
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
            margin: 8px 0 4px;
            font-size: 25px;
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
            padding: 22px;
            width: 100%;
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

        .chart-large {
            height: 322px;
        }

        .chart-medium {
            height: 285px;
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
            margin-bottom: 18px;
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
                justify-content: flex-start;
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
