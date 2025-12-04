<div class="premium-dashboard-wrapper">
    @if ($permission)
        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <!-- Projected Sales Card -->
            <div class="col-xl-3 col-md-6">
                <div class="card premium-card border-0 h-100 hover-lift gradient-card-1">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-box icon-box-primary">
                                <i class="mdi mdi-chart-line"></i>
                            </div>
                            <span class="badge bg-white bg-opacity-25 text-white">+12%</span>
                        </div>
                        <h6 class="text-white text-opacity-75 mb-2 text-uppercase small fw-semibold">Projected Sales</h6>
                        <h3 class="text-white fw-bold mb-0">{{ empty($projected) ? 0 : number_format($projected[0]->sales, 2) }}</h3>
                    </div>
                </div>
            </div>

            <!-- Total Sales Card -->
            <div class="col-xl-3 col-md-6">
                <div class="card premium-card border-0 h-100 hover-lift gradient-card-2" style="cursor:pointer;" onclick="window.location='{{ route('premium.sales.details') }}'">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-box icon-box-success">
                                <i class="mdi mdi-currency-usd"></i>
                            </div>
                            <span class="badge bg-white bg-opacity-25 text-white">Live</span>
                        </div>
                        <h6 class="text-white text-opacity-75 mb-2 text-uppercase small fw-semibold">All Closed Sales</h6>
                        <h3 class="text-white fw-bold mb-0">{{ empty($totalSales) ? 0 : number_format($totalSales[0]->TotalSales, 2) }}</h3>
                    </div>
                </div>
            </div>

            <!-- Total Products Card -->
            <div class="col-xl-3 col-md-6">
                <div class="card premium-card border-0 h-100 hover-lift gradient-card-3">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-box icon-box-info">
                                <i class="mdi mdi-package-variant"></i>
                            </div>
                            <span class="badge bg-white bg-opacity-25 text-white">Active</span>
                        </div>
                        <h6 class="text-white text-opacity-75 mb-2 text-uppercase small fw-semibold">Total Products</h6>
                        <h3 class="text-white fw-bold mb-0">{{ $totalstock[0]->products ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="col-xl-3 col-md-6">
                <div class="card premium-card border-0 h-100 hover-lift gradient-card-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-box icon-box-warning">
                                <i class="mdi mdi-cart"></i>
                            </div>
                            <span class="badge bg-white bg-opacity-25 text-white">Today</span>
                        </div>
                        <h6 class="text-white text-opacity-75 mb-2 text-uppercase small fw-semibold">Total Orders</h6>
                        <h3 class="text-white fw-bold mb-0">{{ $orders[0]->total ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <!-- Terminal Daily Sales Chart -->
            <div class="col-xl-8">
                <div class="card premium-card-white border-0 shadow-lg">
                    <div class="card-header bg-white border-0 pt-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0 fw-bold text-dark">Terminal Daily Sales</h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary">Real-time</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="terminalSalesChart" height="300"></canvas>
                    </div>
                </div>
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
                    <div class="card-body">
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
                <div class="card premium-card-white border-0 shadow-lg">
                    <div class="card-header bg-white border-0 pt-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0 fw-bold text-dark">Yearly Sales Trend</h5>
                            <span class="badge bg-info bg-opacity-10 text-info">Annual</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="yearlyChart" height="300"></canvas>
                    </div>
                </div>
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
                    <div class="card-body">
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
                // Terminal Sales Chart
                const terminalCtx = document.getElementById('terminalSalesChart');
                if(terminalCtx) {
                    const salesData = @json($sales);
                    new Chart(terminalCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: salesData.map(s => s.terminal_name),
                            datasets: [{
                                label: 'Cash',
                                data: salesData.map(s => parseFloat(s.cash)),
                                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            }, {
                                label: 'Credit Card',
                                data: salesData.map(s => parseFloat(s.creditCard)),
                                backgroundColor: 'rgba(255, 99, 132, 0.8)',
                            }, {
                                label: 'Customer Credit',
                                data: salesData.map(s => parseFloat(s.CustomerCredit)),
                                backgroundColor: 'rgba(75, 192, 192, 0.8)',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { 
                                    position: 'top',
                                    labels: {
                                        padding: 15,
                                        font: { size: 12, weight: '600' },
                                        usePointStyle: true,
                                        pointStyle: 'circle'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleFont: { size: 14, weight: 'bold' },
                                    bodyFont: { size: 13 },
                                    cornerRadius: 8
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                                    ticks: { font: { size: 11 } }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { size: 11 } }
                                }
                            },
                            borderRadius: 8
                        }
                    });
                }

                // Top Products Chart
                const productsCtx = document.getElementById('topProductsChart');
                if(productsCtx) {
                    const productsData = @json($products);
                    new Chart(productsCtx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: productsData.map(p => p.product_name),
                            datasets: [{
                                data: productsData.map(p => parseInt(p.count)),
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.8)',
                                    'rgba(54, 162, 235, 0.8)',
                                    'rgba(255, 206, 86, 0.8)',
                                    'rgba(75, 192, 192, 0.8)',
                                    'rgba(153, 102, 255, 0.8)'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { 
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        font: { size: 12, weight: '600' },
                                        usePointStyle: true,
                                        pointStyle: 'circle'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleFont: { size: 14, weight: 'bold' },
                                    bodyFont: { size: 13 },
                                    cornerRadius: 8
                                }
                            },
                            cutout: '70%'
                        }
                    });
                }

                // Yearly Chart
                const yearlyCtx = document.getElementById('yearlyChart');
                if(yearlyCtx) {
                    const yearData = @json($year);
                    new Chart(yearlyCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: yearData.map(y => y.year),
                            datasets: [{
                                label: 'Sales',
                                data: yearData.map(y => parseFloat(y.amount)),
                                borderColor: 'rgba(54, 162, 235, 1)',
                                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleFont: { size: 14, weight: 'bold' },
                                    bodyFont: { size: 13 },
                                    cornerRadius: 8
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                                    ticks: { font: { size: 11 } }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { size: 11 } }
                                }
                            },
                            elements: {
                                line: { borderWidth: 3 },
                                point: { radius: 4, hoverRadius: 6 }
                            }
                        }
                    });
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
                background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
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
                background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
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
                background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
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
                background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
                animation: rotate 20s linear infinite;
            }
            
            @keyframes rotate {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            
            .premium-card .card-body {
                position: relative;
                z-index: 1;
            }
            
            .icon-box {
                width: 60px;
                height: 60px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(10px);
                font-size: 28px;
                color: white;
                animation: pulse 2s ease-in-out infinite;
            }
            
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
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
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-5px); }
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
            }
        </style>
    @endif
</div>
