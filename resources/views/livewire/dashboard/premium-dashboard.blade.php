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
            // Terminal Sales Chart - Enhanced
            // const terminalCtx = document.getElementById('terminalSalesChart');
            // if (terminalCtx) {
            //     const salesData = @json($sales);
            //     const ctx = terminalCtx.getContext('2d');

            //     // Create gradients for each dataset
            //     const cashGradient = ctx.createLinearGradient(0, 0, 0, 400);
            //     cashGradient.addColorStop(0, 'rgba(54, 162, 235, 0.9)');
            //     cashGradient.addColorStop(1, 'rgba(54, 162, 235, 0.6)');

            //     const creditCardGradient = ctx.createLinearGradient(0, 0, 0, 400);
            //     creditCardGradient.addColorStop(0, 'rgba(255, 99, 132, 0.9)');
            //     creditCardGradient.addColorStop(1, 'rgba(255, 99, 132, 0.6)');

            //     const customerCreditGradient = ctx.createLinearGradient(0, 0, 0, 400);
            //     customerCreditGradient.addColorStop(0, 'rgba(75, 192, 192, 0.9)');
            //     customerCreditGradient.addColorStop(1, 'rgba(75, 192, 192, 0.6)');

            //     new Chart(ctx, {
            //         type: 'bar',
            //         data: {
            //             labels: salesData.map(s => s.terminal_name),
            //             datasets: [{
            //                 label: 'Cash',
            //                 data: salesData.map(s => parseFloat(s.cash)),
            //                 backgroundColor: cashGradient,
            //                 borderColor: 'rgb(54, 162, 235)',
            //                 borderWidth: 2,
            //                 borderRadius: 8,
            //                 borderSkipped: false,
            //                 hoverBackgroundColor: 'rgb(54, 162, 235)',
            //                 hoverBorderWidth: 3
            //             }, {
            //                 label: 'Credit Card',
            //                 data: salesData.map(s => parseFloat(s.creditCard)),
            //                 backgroundColor: creditCardGradient,
            //                 borderColor: 'rgb(255, 99, 132)',
            //                 borderWidth: 2,
            //                 borderRadius: 8,
            //                 borderSkipped: false,
            //                 hoverBackgroundColor: 'rgb(255, 99, 132)',
            //                 hoverBorderWidth: 3
            //             }, {
            //                 label: 'Customer Credit',
            //                 data: salesData.map(s => parseFloat(s.CustomerCredit)),
            //                 backgroundColor: customerCreditGradient,
            //                 borderColor: 'rgb(75, 192, 192)',
            //                 borderWidth: 2,
            //                 borderRadius: 8,
            //                 borderSkipped: false,
            //                 hoverBackgroundColor: 'rgb(75, 192, 192)',
            //                 hoverBorderWidth: 3
            //             }]
            //         },
            //         options: {
            //             responsive: true,
            //             maintainAspectRatio: false,
            //             interaction: {
            //                 mode: 'index',
            //                 intersect: false
            //             },
            //             animation: {
            //                 duration: 1500,
            //                 easing: 'easeInOutQuart',
            //                 delay: (context) => {
            //                     let delay = 0;
            //                     if (context.type === 'data' && context.mode === 'default') {
            //                         delay = context.dataIndex * 80 + context.datasetIndex * 40;
            //                     }
            //                     return delay;
            //                 }
            //             },
            //             plugins: {
            //                 legend: {
            //                     position: 'top',
            //                     align: 'end',
            //                     labels: {
            //                         padding: 20,
            //                         font: {
            //                             size: 13,
            //                             weight: '600',
            //                             family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
            //                         },
            //                         usePointStyle: true,
            //                         pointStyle: 'circle',
            //                         boxWidth: 8,
            //                         boxHeight: 8,
            //                         color: '#374151'
            //                     }
            //                 },
            //                 tooltip: {
            //                     backgroundColor: 'rgba(17, 24, 39, 0.95)',
            //                     titleColor: '#ffffff',
            //                     bodyColor: '#ffffff',
            //                     borderColor: 'rgba(255, 255, 255, 0.2)',
            //                     borderWidth: 1,
            //                     padding: 16,
            //                     cornerRadius: 12,
            //                     titleFont: {
            //                         size: 14,
            //                         weight: 'bold',
            //                         family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
            //                     },
            //                     bodyFont: {
            //                         size: 13,
            //                         family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
            //                     },
            //                     displayColors: true,
            //                     boxWidth: 10,
            //                     boxHeight: 10,
            //                     boxPadding: 6,
            //                     usePointStyle: true,
            //                     callbacks: {
            //                         label: function(context) {
            //                             let label = context.dataset.label || '';
            //                             if (label) {
            //                                 label += ': ';
            //                             }
            //                             if (context.parsed.y !== null) {
            //                                 label += new Intl.NumberFormat('en-US', {
            //                                     style: 'currency',
            //                                     currency: 'USD',
            //                                     minimumFractionDigits: 0,
            //                                     maximumFractionDigits: 0
            //                                 }).format(context.parsed.y);
            //                             }
            //                             return label;
            //                         }
            //                     }
            //                 }
            //             },
            //             scales: {
            //                 y: {
            //                     beginAtZero: true,
            //                     grid: {
            //                         color: 'rgba(0, 0, 0, 0.05)',
            //                         drawBorder: false,
            //                         lineWidth: 1
            //                     },
            //                     border: {
            //                         display: false
            //                     },
            //                     ticks: {
            //                         font: {
            //                             size: 12,
            //                             weight: '500',
            //                             family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
            //                         },
            //                         color: '#6B7280',
            //                         padding: 12,
            //                         callback: function(value) {
            //                             return new Intl.NumberFormat('en-US', {
            //                                 notation: 'compact',
            //                                 compactDisplay: 'short'
            //                             }).format(value);
            //                         }
            //                     }
            //                 },
            //                 x: {
            //                     grid: {
            //                         display: false,
            //                         drawBorder: false
            //                     },
            //                     border: {
            //                         display: false
            //                     },
            //                     ticks: {
            //                         font: {
            //                             size: 12,
            //                             weight: '600',
            //                             family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
            //                         },
            //                         color: '#374151',
            //                         padding: 10
            //                     },
            //                     barPercentage: 0.7,
            //                     categoryPercentage: 0.8
            //                 }
            //             }
            //         }
            //     });
            // }

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

            // Yearly Chart - Enhanced
            // const yearlyCtx = document.getElementById('yearlyChart');
            // if (yearlyCtx) {
            //     const yearData = @json($year);
            //     const ctx = yearlyCtx.getContext('2d');

            //     // Create gradient for area fill
            //     const areaGradient = ctx.createLinearGradient(0, 0, 0, 400);
            //     areaGradient.addColorStop(0, 'rgba(54, 162, 235, 0.3)');
            //     areaGradient.addColorStop(1, 'rgba(54, 162, 235, 0.01)');

            //     new Chart(ctx, {
            //         type: 'line',
            //         data: {
            //             labels: yearData.map(y => y.year),
            //             datasets: [{
            //                 label: 'Sales',
            //                 data: yearData.map(y => parseFloat(y.amount)),
            //                 borderColor: 'rgb(54, 162, 235)',
            //                 backgroundColor: areaGradient,
            //                 borderWidth: 3,
            //                 tension: 0.4,
            //                 fill: true,
            //                 pointBackgroundColor: 'rgb(54, 162, 235)',
            //                 pointBorderColor: '#fff',
            //                 pointBorderWidth: 3,
            //                 pointRadius: 6,
            //                 pointHoverRadius: 8,
            //                 pointHoverBackgroundColor: 'rgb(54, 162, 235)',
            //                 pointHoverBorderColor: '#fff',
            //                 pointHoverBorderWidth: 4
            //             }]
            //         },
            //         options: {
            //             responsive: true,
            //             maintainAspectRatio: false,
            //             interaction: {
            //                 mode: 'index',
            //                 intersect: false
            //             },
            //             animation: {
            //                 duration: 1500,
            //                 easing: 'easeInOutQuart',
            //                 delay: (context) => {
            //                     let delay = 0;
            //                     if (context.type === 'data' && context.mode === 'default') {
            //                         delay = context.dataIndex * 100;
            //                     }
            //                     return delay;
            //                 }
            //             },
            //             plugins: {
            //                 legend: {
            //                     display: false
            //                 },
            //                 tooltip: {
            //                     backgroundColor: 'rgba(17, 24, 39, 0.95)',
            //                     titleColor: '#ffffff',
            //                     bodyColor: '#ffffff',
            //                     borderColor: 'rgba(255, 255, 255, 0.2)',
            //                     borderWidth: 1,
            //                     padding: 16,
            //                     cornerRadius: 12,
            //                     titleFont: {
            //                         size: 14,
            //                         weight: 'bold',
            //                         family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
            //                     },
            //                     bodyFont: {
            //                         size: 13,
            //                         family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
            //                     },
            //                     displayColors: true,
            //                     boxWidth: 10,
            //                     boxHeight: 10,
            //                     boxPadding: 6,
            //                     usePointStyle: true,
            //                     callbacks: {
            //                         label: function(context) {
            //                             let label = context.dataset.label || '';
            //                             if (label) {
            //                                 label += ': ';
            //                             }
            //                             if (context.parsed.y !== null) {
            //                                 label += new Intl.NumberFormat('en-US', {
            //                                     style: 'currency',
            //                                     currency: 'USD',
            //                                     minimumFractionDigits: 0,
            //                                     maximumFractionDigits: 0
            //                                 }).format(context.parsed.y);
            //                             }
            //                             return label;
            //                         }
            //                     }
            //                 }
            //             },
            //             scales: {
            //                 y: {
            //                     beginAtZero: true,
            //                     grid: {
            //                         color: 'rgba(0, 0, 0, 0.05)',
            //                         drawBorder: false,
            //                         lineWidth: 1
            //                     },
            //                     border: {
            //                         display: false
            //                     },
            //                     ticks: {
            //                         font: {
            //                             size: 12,
            //                             weight: '500',
            //                             family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
            //                         },
            //                         color: '#6B7280',
            //                         padding: 12,
            //                         callback: function(value) {
            //                             return new Intl.NumberFormat('en-US', {
            //                                 notation: 'compact',
            //                                 compactDisplay: 'short'
            //                             }).format(value);
            //                         }
            //                     }
            //                 },
            //                 x: {
            //                     grid: {
            //                         display: false,
            //                         drawBorder: false
            //                     },
            //                     border: {
            //                         display: false
            //                     },
            //                     ticks: {
            //                         font: {
            //                             size: 12,
            //                             weight: '600',
            //                             family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
            //                         },
            //                         color: '#374151',
            //                         padding: 10
            //                     }
            //                 }
            //             }
            //         }
            //     });
            // }
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

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
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