<div class="premium-dashboard-wrapper">
    @if ($permission)

    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card premium-card-white border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-end g-3">
                        <div class="col-lg-12 col-md-12">
                            <label class="form-label small fw-semibold mb-2">Quick Select</label>
                            <div class="d-flex gap-2 flex-wrap align-items-center">
                                <span wire:click="setDateRange('today')" class="badge px-3 py-2 cursor-pointer badge-hover {{ $selectedRange == 'today' ? 'bg-success text-white' : 'bg-white text-dark border' }}">Today</span>
                                <span wire:click="setDateRange('yesterday')" class="badge px-3 py-2 cursor-pointer badge-hover {{ $selectedRange == 'yesterday' ? 'bg-success text-white' : 'bg-white text-dark border' }}">Yesterday</span>
                                <span wire:click="setDateRange('this_week')" class="badge px-3 py-2 cursor-pointer badge-hover {{ $selectedRange == 'this_week' ? 'bg-success text-white' : 'bg-white text-dark border' }}">This Week</span>
                                <span wire:click="setDateRange('last_week')" class="badge px-3 py-2 cursor-pointer badge-hover {{ $selectedRange == 'last_week' ? 'bg-success text-white' : 'bg-white text-dark border' }}">Last Week</span>
                                <span wire:click="setDateRange('this_month')" class="badge px-3 py-2 cursor-pointer badge-hover {{ $selectedRange == 'this_month' ? 'bg-success text-white' : 'bg-white text-dark border' }}">This Month</span>
                                <span wire:click="setDateRange('lasst_month')" class="badge px-3 py-2 cursor-pointer badge-hover {{ $selectedRange == 'last_month' ? 'bg-success text-white' : 'bg-white text-dark border' }}">Last Month</span>
                                <span class="badge bg-{{ $salesComparison['isPositive'] ? 'success' : 'danger' }} text-white px-3 py-2 ms-auto">
                                    <i class="mdi mdi-{{ $salesComparison['isPositive'] ? 'trending-up' : 'trending-down' }}"></i>
                                    {{ number_format(abs($salesComparison['change']), 1) }}% vs Yesterday
                                </span>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-6">
                            <label class="form-label small fw-semibold mb-2">From Date</label>
                            <input type="date" wire:model="dateFrom" class="form-control">
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <label class="form-label small fw-semibold mb-2">To Date</label>
                            <input type="date" wire:model="dateTo" class="form-control">
                        </div>
                        <div class="col-lg-2 col-md-4 col-12">
                            <div class="d-flex gap-2">
                                <button wire:click="applyFilter" class="btn btn-primary flex-fill">
                                    <i class="mdi mdi-filter"></i> Apply
                                </button>
                                <button wire:click="exportData" class="btn btn-success flex-fill">
                                    <i class="mdi mdi-download"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="text-muted small mt-3">
                        <i class="mdi mdi-calendar-range"></i>
                        Showing data from <strong>{{ date('M d, Y', strtotime($dateFrom)) }}</strong> to <strong>{{ date('M d, Y', strtotime($dateTo)) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Today Sales Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card premium-card-white border-0 h-100 hover-lift shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-box-simple bg-primary bg-opacity-10">
                            <i class="mdi mdi-calendar-today text-primary"></i>
                        </div>
                        <span class="badge bg-success text-white">Today</span>
                    </div>
                    <h6 class="text-muted mb-2 text-uppercase small fw-semibold">Today's Sales</h6>
                    <h3 class="text-dark fw-bold mb-0">{{ number_format($salesComparison['today'], 2) }}</h3>
                    <small class="text-muted">Yesterday: {{ number_format($salesComparison['yesterday'], 2) }}</small>
                </div>
            </div>
        </div>

        <!-- Total Sales Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card premium-card-white border-0 h-100 hover-lift shadow-sm" wire:click="openSalesModal" style="cursor: pointer;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-box-simple bg-success bg-opacity-10">
                            <i class="mdi mdi-currency-usd text-success"></i>
                        </div>
                        <span class="badge bg-success text-white">Filtered</span>
                    </div>
                    <h6 class="text-muted mb-2 text-uppercase small fw-semibold">Total Sales</h6>
                    <h3 class="text-dark fw-bold mb-0">{{ empty($totalSales) ? 0 : number_format($totalSales[0]->TotalSales ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Profit Margin Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card premium-card-white border-0 h-100 hover-lift shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-box-simple bg-info bg-opacity-10">
                            <i class="mdi mdi-chart-line-variant text-info"></i>
                        </div>
                        <span class="badge bg-success text-white">{{ number_format($profitMargin['margin'], 1) }}%</span>
                    </div>
                    <h6 class="text-muted mb-2 text-uppercase small fw-semibold">Profit Margin</h6>
                    <h3 class="text-dark fw-bold mb-0">{{ number_format($profitMargin['profit'], 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card premium-card-white border-0 h-100 hover-lift shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-box-simple bg-warning bg-opacity-10">
                            <i class="mdi mdi-alert-circle text-warning"></i>
                        </div>
                        <span class="badge bg-success text-white">Alert</span>
                    </div>
                    <h6 class="text-muted mb-2 text-uppercase small fw-semibold">Low Stock Items</h6>
                    <h3 class="text-dark fw-bold mb-0">{{ count($lowStockProducts) }}</h3>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card premium-card-white border-0 h-100 hover-lift shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-box-simple bg-danger bg-opacity-10">
                            <i class="mdi mdi-cart text-danger"></i>
                        </div>
                        <span class="badge bg-success text-white">Today</span>
                    </div>
                    <h6 class="text-muted mb-2 text-uppercase small fw-semibold">Total Orders</h6>
                    <h3 class="text-dark fw-bold mb-0">{{ $orders[0]->total ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- New Widgets Row -->
    <div class="row g-4 mb-4">
        <!-- Low Stock Products -->
        <div class="col-xl-6 col-lg-12">
            <div class="card premium-card-white border-0 shadow-lg">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 fw-bold text-dark">Low Stock Alert</h5>
                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">{{ count($lowStockProducts) }} Items</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(count($lowStockProducts) > 0)
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th class="border-0 text-muted text-uppercase small">Product</th>
                                <th class="border-0 text-muted text-uppercase small d-none d-md-table-cell">Code</th>
                                <th class="border-0 text-muted text-uppercase small text-end">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $product)
                            <tr class="table-row-modern">
                                <td class="py-3 px-4">
                                    <span class="fw-semibold text-dark d-block">{{ $product->product_name }}</span>
                                    <small class="text-muted d-md-none">{{ $product->item_code }}</small>
                                </td>
                                <td class="py-3 d-none d-md-table-cell"><span class="badge bg-light text-dark border px-3 py-2">{{ $product->item_code }}</span></td>
                                <td class="py-3 text-end px-4"><span class="badge bg-danger text-white px-3 py-2">{{ $product->balance_qty }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="mdi mdi-check-circle" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mb-0 mt-3 fw-semibold">All products are well stocked!</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="col-xl-6 col-lg-12">
            <div class="card premium-card-white border-0 shadow-lg">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 fw-bold text-dark">Top 5 Customers</h5>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">VIP</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(count($topCustomers) > 0)
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th class="border-0 text-muted text-uppercase small">Customer</th>
                                <th class="border-0 text-muted text-uppercase small d-none d-md-table-cell">Mobile</th>
                                <th class="border-0 text-muted text-uppercase small text-end">Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCustomers as $customer)
                            <tr class="table-row-modern">
                                <td class="py-3 px-4">
                                    <span class="fw-bold text-dark d-block">{{ $customer->name }}</span>
                                    <small class="text-muted d-md-none">{{ $customer->mobile }}</small>
                                </td>
                                <td class="py-3 d-none d-md-table-cell"><span class="badge bg-light text-dark border px-3 py-2">{{ $customer->mobile }}</span></td>
                                <td class="py-3 text-end px-4"><span class="fw-bold text-success" style="font-size: 1.1rem;">{{ number_format($customer->total, 2) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="mdi mdi-account-group" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mb-0 mt-3 fw-semibold">No customer data available</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Terminal Daily Sales Chart -->
        <div class="col-xl-8">
            <livewire:dashboard.terminal-sales-chart />
        </div>

        <!-- Top Products Chart -->
        <div class="col-xl-4">
            <div class="card premium-card-white border-0 shadow-lg">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 fw-bold text-dark">Top 5 Products</h5>
                        <span class="badge bg-success bg-opacity-10 text-success">Best Sellers</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <canvas id="topProductsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Sales & Yearly Chart -->
    <div class="row g-4 mb-4">
        <div class="col-xl-12">
            <livewire:dashboard.monthly-sales-chart />
        </div>
    </div>

    <div class="row g-4">
        <!-- Yearly Chart -->
        <div class="col-xl-6">
            <livewire:dashboard.yearly-sales-chart />
        </div>

        <!-- Order Status -->
        <div class="col-xl-6">
            <div class="card premium-card-white border-0 shadow-lg">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 fw-bold text-dark">Order Status Overview</h5>
                        <span class="badge bg-warning bg-opacity-10 text-warning">Live Status</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="status-card status-pending">
                                <div class="status-icon">
                                    <i class="mdi mdi-clock-outline"></i>
                                </div>
                                <p class="status-label mb-1">Pending</p>
                                <h4 class="status-value mb-0">{{ $orders[0]->pending ?? 0 }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="status-card status-processing">
                                <div class="status-icon">
                                    <i class="mdi mdi-cog-outline"></i>
                                </div>
                                <p class="status-label mb-1">Processing</p>
                                <h4 class="status-value mb-0">{{ $orders[0]->processing ?? 0 }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="status-card status-ready">
                                <div class="status-icon">
                                    <i class="mdi mdi-check-circle-outline"></i>
                                </div>
                                <p class="status-label mb-1">Ready</p>
                                <h4 class="status-value mb-0">{{ $orders[0]->ready ?? 0 }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="status-card status-delivered">
                                <div class="status-icon">
                                    <i class="mdi mdi-truck-delivery-outline"></i>
                                </div>
                                <p class="status-label mb-1">Delivered</p>
                                <h4 class="status-value mb-0">{{ $orders[0]->delivery ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Top Products Chart - Enhanced
            const productsCtx = document.getElementById('topProductsChart');
            if (productsCtx) {
                const productsData = @json($products);
                new Chart(productsCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: productsData.map(p => p.product_name),
                        datasets: [{
                            data: productsData.map(p => parseInt(p.count)),
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.85)',
                                'rgba(54, 162, 235, 0.85)',
                                'rgba(255, 206, 86, 0.85)',
                                'rgba(75, 192, 192, 0.85)',
                                'rgba(153, 102, 255, 0.85)'
                            ],
                            borderColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                                'rgb(255, 206, 86)',
                                'rgb(75, 192, 192)',
                                'rgb(153, 102, 255)'
                            ],
                            borderWidth: 3,
                            hoverOffset: 15,
                            hoverBorderWidth: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    font: {
                                        size: 12,
                                        weight: '600',
                                        family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                    },
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 10,
                                    boxHeight: 10,
                                    color: '#374151'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.95)',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: 'rgba(255, 255, 255, 0.2)',
                                borderWidth: 1,
                                padding: 16,
                                cornerRadius: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold',
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                bodyFont: {
                                    size: 13,
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                displayColors: true,
                                boxWidth: 12,
                                boxHeight: 12,
                                boxPadding: 8,
                                usePointStyle: true,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        label += value + ' (' + percentage + '%)';
                                        return label;
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }

            window.toggleCategory = function(categoryId) {
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
        });
    </script>

    <style>
        .premium-dashboard-wrapper {
            background: #f5f6fa;
            min-height: 100vh;
            padding: 2rem;
            margin: -2rem;
        }

        .premium-card {
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .premium-card-white {
            border-radius: 20px;
            background: white;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .premium-card-white:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .hover-lift:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2) !important;
        }

        .gradient-card-1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }

        .gradient-card-1::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        .gradient-card-2 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            position: relative;
            overflow: hidden;
        }

        .gradient-card-2::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        .gradient-card-3 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            position: relative;
            overflow: hidden;
        }

        .gradient-card-3::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        .gradient-card-4 {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            position: relative;
            overflow: hidden;
        }

        .gradient-card-4::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        .gradient-card-5 {
            background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
            position: relative;
            overflow: hidden;
        }

        .gradient-card-5::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .premium-card .card-body {
            position: relative;
            z-index: 1;
        }

        .icon-box-simple {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }



        .status-card {
            padding: 1.5rem;
            border-radius: 15px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .status-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            transition: width 0.3s ease;
        }

        .status-card:hover::before {
            width: 100%;
            opacity: 0.1;
        }

        .status-pending {
            background: linear-gradient(135deg, #fff5e6 0%, #ffe8cc 100%);
        }

        .status-pending::before {
            background: #ffa500;
        }

        .status-processing {
            background: linear-gradient(135deg, #e6f7ff 0%, #cceeff 100%);
        }

        .status-processing::before {
            background: #00bfff;
        }

        .status-ready {
            background: linear-gradient(135deg, #e6f0ff 0%, #cce0ff 100%);
        }

        .status-ready::before {
            background: #4169e1;
        }

        .status-delivered {
            background: linear-gradient(135deg, #e6ffe6 0%, #ccffcc 100%);
        }

        .status-delivered::before {
            background: #32cd32;
        }

        .status-icon {
            font-size: 24px;
            margin-bottom: 0.5rem;
            opacity: 0.7;
        }

        .status-label {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.7;
        }

        .status-value {
            font-size: 2rem;
            font-weight: 700;
        }

        .status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .status-card .status-icon {
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        canvas {
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.05));
        }

        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead th {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .table-modern tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s ease;
        }

        .table-row-modern:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .table-modern tbody tr:last-child {
            border-bottom: none;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 0.5rem 0.75rem;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .badge-hover {
            transition: all 0.3s ease;
        }

        .badge-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            opacity: 0.9;
        }

        .sales-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .sales-modal-sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: 450px;
            height: 100vh;
            background: white;
            box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        .modal-header-custom {
            padding: 2rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            background: linear-gradient(135deg, #fbfdfdff 0%, #e9ecebff 100%);
            color: #2c3e50;
        }

        .modal-header-custom h4 {
            color: white;
            font-weight: 700;
            margin: 0;
        }

        .btn-close-custom {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-close-custom:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .btn-back-custom {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-back-custom:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateX(-4px);
        }

        .modal-body-custom {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        .branches-scroll {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .branch-item-modal {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1.25rem;
            transition: all 0.3s ease;
        }

        .branch-item-modal {
            position: relative;
        }

        .branch-item-modal:hover {
            border-color: #09a372;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
            transform: translateX(-4px);
        }

        .branch-arrow {
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #09a372;
            font-size: 24px;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .branch-item-modal:hover .branch-arrow {
            opacity: 1;
            transform: translateY(-50%) translateX(4px);
        }

        .branch-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .branch-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #09a372 0%, #0aa775 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .branch-details {
            flex: 1;
        }

        .branch-name-modal {
            font-size: 1rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 0.25rem 0;
        }

        .branch-sales {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
        }

        .sales-amount-modal {
            font-size: 1.75rem;
            font-weight: 700;
            color: #09a372;
            margin-bottom: 0.25rem;
        }

        .sales-label-modal {
            font-size: 0.75rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .bg-success-subtle {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .terminals-scroll {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }

        .terminals-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .terminals-scroll::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 10px;
        }

        .terminals-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #09a372 0%, #0aa775 100%);
            border-radius: 10px;
        }

        .terminal-item-modal {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem;
            transition: all 0.3s ease;
            min-width: 200px;
            flex-shrink: 0;
            text-align: center;
        }

        .terminal-item-modal:hover {
            border-color: #09a372;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
            transform: translateY(-4px);
        }

        .terminal-item-active {
            background: linear-gradient(135deg, #09a372 0%, #0aa775 100%) !important;
            border-color: #09a372 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3) !important;
        }

        .terminal-item-active .terminal-icon {
            background: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
        }

        .terminal-item-active .terminal-name-modal {
            color: white !important;
        }

        .terminal-item-active .badge {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
        }

        .terminal-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .terminal-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #09a372 0%, #0aa775 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            margin: 0 auto;
        }

        .terminal-details {
            width: 100%;
        }

        .terminal-name-modal {
            font-size: 0.875rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 0.5rem 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .terminal-sales {
            display: none;
        }

        @media (max-width: 768px) {
            .premium-dashboard-wrapper {
                padding: 1rem;
                margin: -1rem;
            }

            .icon-box {
                width: 50px;
                height: 50px;
                font-size: 24px;
            }

            .status-value {
                font-size: 1.5rem;
            }

            .sales-modal-sidebar {
                width: 100%;
            }
        }
    </style>
    <!-- Sales Details Modal -->
    @if($showSalesModal)
    <div class="sales-modal-overlay" wire:click="closeSalesModal">
        <div class="sales-modal-sidebar" x-on:click.stop>
            <div class="modal-header-custom bg-white border-bottom sticky-top">
                <div class="pb-3">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-3">
                            @if($modalView !== 'branches')
                            @php
                            $backAction = match($modalView) {
                            'terminals' => 'backToBranches',
                            'declarations' => 'backToTerminals',
                            'details' => 'backToDeclarations',
                            default => 'backToBranches'
                            };
                            @endphp
                            <button wire:click="{{ $backAction }}"
                                class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center text-primary"
                                style="width: 36px; height: 36px; transition: all 0.2s ease;">
                                <i class="mdi mdi-arrow-left fs-5"></i>
                            </button>
                            @endif

                            <div>
                                <h5 class="mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">
                                    @switch($modalView)
                                    @case('branches') Sales Overview @break
                                    @case('terminals') Select Terminal @break
                                    @case('declarations') Sales Sessions @break
                                    @case('details') Transaction Details @break
                                    @endswitch
                                </h5>
                                <p class="text-muted small mb-0 fw-medium">
                                    @switch($modalView)
                                    @case('branches') {{ date('d M') }} - {{ date('d M, Y') }} @break
                                    @case('terminals') {{ $modalBranches[0]->branch_name ?? 'Branch Overview' }} @break
                                    @case('declarations') {{ $terminalName }} @break
                                    @case('details') {{ $terminalName }} @break
                                    @endswitch
                                </p>
                            </div>
                        </div>
                        <button wire:click="closeSalesModal" class="btn btn-close-custom p-2 text-muted hover-danger" style="background: none; border: none;">
                            <i class="mdi mdi-close fs-4"></i>
                        </button>
                    </div>

                    <div class="date-filter-wrapper bg-light rounded-4 p-2 border border-light-subtle">
                        <div class="d-flex align-items-center gap-2">
                            <div class="flex-grow-1">
                                <label class="d-block text-muted ms-2 mb-1" style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">From Date</label>
                                <div class="input-group input-group-sm bg-white rounded-3 overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 text-primary ps-3 pe-2"><i class="mdi mdi-calendar-range"></i></span>
                                    <input type="date" wire:model.live="salesDateFrom" class="form-control border-0 shadow-none fw-semibold" style="font-size: 13px; color: #495057;">
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-center pt-3 text-muted opacity-50">
                                <i class="mdi mdi-arrow-right"></i>
                            </div>
                            <div class="flex-grow-1">
                                <label class="d-block text-muted ms-2 mb-1" style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">To Date</label>
                                <div class="input-group input-group-sm bg-white rounded-3 overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 text-primary ps-3 pe-2"><i class="mdi mdi-calendar-range"></i></span>
                                    <input type="date" wire:model.live="salesDateTo" class="form-control border-0 shadow-none fw-semibold" style="font-size: 13px; color: #495057;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-body-custom p-0" style="overflow-y: auto; max-height: calc(100vh - 140px);">
                @if($modalView === 'branches')
                <div class="list-group list-group-flush">
                    @foreach($modalBranches as $branch)
                    <div class="list-group-item list-group-item-action p-3 border-bottom-0 border-top-0 border-start-0 border-end-0"
                        wire:click="selectBranch({{ session('roleId') == 2 ? $branch->branch_id : $branch->terminal_id }}, '{{ $branch->identify }}')"
                        style="cursor: pointer; transition: background 0.2s;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3 overflow-hidden flex-grow-1">
                                <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary flex-shrink-0" style="width: 48px; height: 48px; font-size: 24px;">
                                    <i class="mdi mdi-office-building"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <h6 class="mb-1 fw-semibold text-truncate">{{ session('roleId') == 2 ? $branch->branch_name : $branch->terminal_name }}</h6>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2">Active</span>
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0 ms-3">
                                <h6 class="mb-0 fw-bold text-dark">{{ number_format($branch->sales, 2) }}</h6>
                                <small class="text-muted">Total Sales</small>
                            </div>
                            <i class="mdi mdi-chevron-right text-muted fs-4 flex-shrink-0 ms-2"></i>
                        </div>
                    </div>
                    @endforeach
                </div>

                @elseif($modalView === 'terminals')
                <div class="list-group list-group-flush">
                    @foreach($modalTerminals as $terminal)
                    <div class="list-group-item list-group-item-action p-3"
                        wire:click="selectTerminal({{ $terminal->terminal_id }})"
                        style="cursor: pointer;">
                        <div class="d-flex align-items-center gap-3 overflow-hidden">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-info-subtle text-info flex-shrink-0" style="width: 48px; height: 48px; font-size: 24px;">
                                <i class="mdi mdi-monitor"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="mb-1 fw-semibold text-truncate">{{ $terminal->terminal_name }}</h6>
                                <span class="badge bg-light text-dark border">Terminal</span>
                            </div>
                            <div class="text-end flex-shrink-0 ms-2">
                                <i class="mdi mdi-chevron-right text-muted fs-4"></i>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @elseif($modalView === 'declarations')
                <div class="list-group list-group-flush">
                    @forelse($modalDeclarations as $decl)
                    <div class="list-group-item list-group-item-action p-3"
                        wire:click="selectDeclaration({{ $decl->opening_id }})"
                        style="cursor: pointer;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center {{ $decl->status == 2 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}"
                                    style="width: 48px; height: 48px; font-size: 24px;">
                                    <i class="mdi mdi-{{ $decl->status == 2 ? 'lock' : 'lock-open' }}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-semibold">{{ date('d M Y', strtotime($decl->date)) }} <small class="text-muted">{{ date('h:i A', strtotime($decl->time)) }}</small></h6>
                                    <span class="badge {{ $decl->status == 2 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} rounded-pill px-2">
                                        {{ $decl->status == 2 ? 'Closed' : 'Open' }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 fw-bold">{{ number_format($decl->total_sales, 2) }}</h6>
                                <small class="text-muted">Sales</small>
                            </div>
                            <i class="mdi mdi-chevron-right text-muted fs-4"></i>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="mdi mdi-history text-muted" style="font-size: 48px;"></i>
                        <p class="text-muted mt-3">No sales sessions found for this date range.</p>
                    </div>
                    @endforelse
                </div>

                @elseif($modalView === 'details')
                <div class="p-3">
                    @include('dashboard.partials.terminal-details-partial', [
                    'heads' => $declarationDetails,
                    'result' => $terminalPermissions,
                    'terminal_name' => [(object)['terminal_name' => $terminalName]]
                    ])
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    @endif
</div>

<script>
    function selectTerminal(element, terminalId) {
        // Remove active class from all items
        document.querySelectorAll('.terminal-item-modal').forEach(el => {
            el.classList.remove('terminal-item-active');
        });

        // Add active class to clicked item
        element.classList.add('terminal-item-active');

        // Update subtitle with terminal name
        const name = element.querySelector('.terminal-name-modal').innerText;
        const subtitle = document.getElementById('terminal-subtitle-modal');
        if (subtitle) {
            subtitle.innerText = name;
        }

        // Call existing getPartial
        getPartial(terminalId);
    }

    function getPartial(terminal) {
        const detailsDiv = document.getElementById('div_details');
        if (!detailsDiv) return;
        detailsDiv.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';

        fetch('{{ url("/get-terminal-details") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    terminal: terminal
                })
            })
            .then(response => response.text())
            .then(html => detailsDiv.innerHTML = html)
            .catch(() => detailsDiv.innerHTML = '<div class="alert alert-danger">Error loading details</div>');
    }

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('terminals-loaded', () => {
            setTimeout(() => {
                const firstTerminal = document.querySelector('.terminal-item-modal');
                if (firstTerminal) {
                    firstTerminal.click();
                }
            }, 100);
        });
    });
</script>