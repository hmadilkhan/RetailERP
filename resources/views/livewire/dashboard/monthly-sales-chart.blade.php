<style>
    .premium-chart-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        border: none;
        overflow: hidden;
        position: relative;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .premium-chart-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .premium-chart-header {
        background: #ffffff;
        padding: 30px 35px;
        position: relative;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    }

    .premium-chart-header-content {
        position: relative;
        z-index: 1;
    }

    .premium-chart-title {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 8px 0;
        letter-spacing: 0.3px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .premium-chart-subtitle {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
        font-weight: 400;
    }

    .chart-stats-row {
        display: flex;
        gap: 15px;
        margin-top: 20px;
        position: relative;
        z-index: 1;
    }

    .stat-card {
        flex: 1;
        background: #f9fafb;
        border-radius: 12px;
        padding: 15px 20px;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        background: #f3f4f6;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .stat-label {
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .stat-value {
        font-size: 20px;
        color: #1f2937;
        font-weight: 700;
        margin: 0;
    }

    .stat-icon {
        font-size: 18px;
        margin-right: 8px;
        color: #667eea;
        opacity: 1;
    }

    .premium-chart-body {
        padding: 35px 35px 30px 35px;
        background: #ffffff;
        position: relative;
    }

    .chart-container {
        position: relative;
        background: #ffffff;
        border-radius: 15px;
        padding: 20px;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .premium-chart-card {
        animation: fadeInUp 0.6s ease-out;
    }

    @media (max-width: 768px) {
        .chart-stats-row {
            flex-direction: column;
        }
        
        .premium-chart-header {
            padding: 25px 20px;
        }
        
        .premium-chart-body {
            padding: 25px 20px;
        }
    }
</style>

<div class="premium-chart-card" wire:loading.remove>
    <div class="premium-chart-header">
        <div class="premium-chart-header-content">
            <h3 class="premium-chart-title">Monthly Sales Performance</h3>
            <p class="premium-chart-subtitle">Branch comparison over the last 7 months</p>
            
            <div class="chart-stats-row">
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="mdi mdi-chart-line stat-icon"></i>Total Sales
                    </div>
                    <div class="stat-value" id="totalSalesValue">Loading...</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="mdi mdi-trending-up stat-icon"></i>Avg per Month
                    </div>
                    <div class="stat-value" id="avgSalesValue">Loading...</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="mdi mdi-store stat-icon"></i>Top Branch
                    </div>
                    <div class="stat-value" id="topBranchValue">Loading...</div>
                </div>
            </div>
        </div>
    </div>
    <div class="premium-chart-body">
        <div class="chart-container">
            <canvas id="monthlySalesChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@section('scriptcode_three')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('monthlySalesChart').getContext('2d');
            
            // Premium color palette matching premium-dashboard.blade.php
            const colorPalette = [
                { bg: 'rgba(54, 162, 235, 0.8)', border: 'rgb(54, 162, 235)', gradient: ['rgba(54, 162, 235, 0.9)', 'rgba(54, 162, 235, 0.6)'] },
                { bg: 'rgba(255, 99, 132, 0.8)', border: 'rgb(255, 99, 132)', gradient: ['rgba(255, 99, 132, 0.9)', 'rgba(255, 99, 132, 0.6)'] },
                { bg: 'rgba(75, 192, 192, 0.8)', border: 'rgb(75, 192, 192)', gradient: ['rgba(75, 192, 192, 0.9)', 'rgba(75, 192, 192, 0.6)'] },
                { bg: 'rgba(255, 206, 86, 0.8)', border: 'rgb(255, 206, 86)', gradient: ['rgba(255, 206, 86, 0.9)', 'rgba(255, 206, 86, 0.6)'] },
                { bg: 'rgba(153, 102, 255, 0.8)', border: 'rgb(153, 102, 255)', gradient: ['rgba(153, 102, 255, 0.9)', 'rgba(153, 102, 255, 0.6)'] },
                { bg: 'rgba(255, 159, 64, 0.8)', border: 'rgb(255, 159, 64)', gradient: ['rgba(255, 159, 64, 0.9)', 'rgba(255, 159, 64, 0.6)'] },
                { bg: 'rgba(201, 203, 207, 0.8)', border: 'rgb(201, 203, 207)', gradient: ['rgba(201, 203, 207, 0.9)', 'rgba(201, 203, 207, 0.6)'] },
                { bg: 'rgba(83, 102, 255, 0.8)', border: 'rgb(83, 102, 255)', gradient: ['rgba(83, 102, 255, 0.9)', 'rgba(83, 102, 255, 0.6)'] }
            ];

            // Process datasets with premium styling
            const rawData = @json($chartData);
            const enhancedDatasets = rawData.map((dataset, index) => {
                const colorIndex = index % colorPalette.length;
                const colors = colorPalette[colorIndex];
                
                // Create gradient for each bar
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, colors.gradient[0]);
                gradient.addColorStop(1, colors.gradient[1]);
                
                return {
                    ...dataset,
                    backgroundColor: gradient,
                    borderColor: colors.border,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    hoverBackgroundColor: colors.border,
                    hoverBorderColor: colors.border,
                    hoverBorderWidth: 3
                };
            });

            // Calculate statistics
            let totalSales = 0;
            let branchTotals = {};
            
            enhancedDatasets.forEach(dataset => {
                const branchTotal = dataset.data.reduce((sum, val) => sum + val, 0);
                totalSales += branchTotal;
                branchTotals[dataset.label] = branchTotal;
            });
            
            const avgPerMonth = totalSales / @json($monthNames).length;
            const topBranch = Object.keys(branchTotals).reduce((a, b) => branchTotals[a] > branchTotals[b] ? a : b);
            
            // Update stat cards
            document.getElementById('totalSalesValue').textContent = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
                notation: 'compact',
                compactDisplay: 'short'
            }).format(totalSales);
            
            document.getElementById('avgSalesValue').textContent = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
                notation: 'compact',
                compactDisplay: 'short'
            }).format(avgPerMonth);
            
            document.getElementById('topBranchValue').textContent = topBranch;

            const monthlySalesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($monthNames),
                    datasets: enhancedDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2.5,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart',
                        delay: (context) => {
                            let delay = 0;
                            if (context.type === 'data' && context.mode === 'default') {
                                delay = context.dataIndex * 100 + context.datasetIndex * 50;
                            }
                            return delay;
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 20,
                                font: {
                                    size: 13,
                                    weight: '600',
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                color: '#374151',
                                boxWidth: 8,
                                boxHeight: 8
                            }
                        },
                        tooltip: {
                            enabled: true,
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
                            boxWidth: 10,
                            boxHeight: 10,
                            boxPadding: 6,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('en-US', {
                                            style: 'currency',
                                            currency: 'USD',
                                            minimumFractionDigits: 0,
                                            maximumFractionDigits: 0
                                        }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false,
                                lineWidth: 1
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                padding: 12,
                                font: {
                                    size: 12,
                                    weight: '500',
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                color: '#6B7280',
                                callback: function(value) {
                                    return new Intl.NumberFormat('en-US', {
                                        notation: 'compact',
                                        compactDisplay: 'short'
                                    }).format(value);
                                }
                            }
                        },
                        x: {
                            stacked: false,
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                padding: 10,
                                font: {
                                    size: 12,
                                    weight: '600',
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                color: '#374151'
                            },
                            barPercentage: 0.7,
                            categoryPercentage: 0.8
                        }
                    }
                }
            });

            // Add smooth hover animation
            const canvas = document.getElementById('monthlySalesChart');
            canvas.style.transition = 'all 0.3s ease';
        });
    </script>
@endsection


