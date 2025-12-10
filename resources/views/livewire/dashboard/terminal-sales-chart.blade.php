<div>
    <!-- Loading Skeleton -->
    <div wire:loading class="card premium-card-white border-0 shadow-lg">
        <div class="card-header bg-white border-0 pt-4 pb-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="skeleton skeleton-text" style="width: 200px; height: 24px;"></div>
                <div class="skeleton skeleton-badge" style="width: 80px; height: 28px;"></div>
            </div>
        </div>
        <div class="card-body">
            <div class="skeleton skeleton-chart" style="height: 300px;"></div>
        </div>
    </div>

    <!-- Actual Chart -->
    <div wire:loading.remove class="card premium-card-white border-0 shadow-lg">
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


    <style>
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s ease-in-out infinite;
            border-radius: 8px;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .skeleton-chart {
            border-radius: 12px;
        }

        .skeleton-badge {
            border-radius: 50px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Use setTimeout to ensure DOM is ready when lazy loaded
        setTimeout(function() {
            const terminalCtx = document.getElementById('terminalSalesChart');
            if (terminalCtx) {
                const salesData = @json($sales);
                const ctx = terminalCtx.getContext('2d');

                // Create gradients for each dataset
                const cashGradient = ctx.createLinearGradient(0, 0, 0, 400);
                cashGradient.addColorStop(0, 'rgba(54, 162, 235, 0.9)');
                cashGradient.addColorStop(1, 'rgba(54, 162, 235, 0.6)');

                const creditCardGradient = ctx.createLinearGradient(0, 0, 0, 400);
                creditCardGradient.addColorStop(0, 'rgba(255, 99, 132, 0.9)');
                creditCardGradient.addColorStop(1, 'rgba(255, 99, 132, 0.6)');

                const customerCreditGradient = ctx.createLinearGradient(0, 0, 0, 400);
                customerCreditGradient.addColorStop(0, 'rgba(75, 192, 192, 0.9)');
                customerCreditGradient.addColorStop(1, 'rgba(75, 192, 192, 0.6)');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: salesData.map(s => s.terminal_name),
                        datasets: [{
                            label: 'Cash',
                            data: salesData.map(s => parseFloat(s.cash)),
                            backgroundColor: cashGradient,
                            borderColor: 'rgb(54, 162, 235)',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                            hoverBackgroundColor: 'rgb(54, 162, 235)',
                            hoverBorderWidth: 3
                        }, {
                            label: 'Credit Card',
                            data: salesData.map(s => parseFloat(s.creditCard)),
                            backgroundColor: creditCardGradient,
                            borderColor: 'rgb(255, 99, 132)',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                            hoverBackgroundColor: 'rgb(255, 99, 132)',
                            hoverBorderWidth: 3
                        }, {
                            label: 'Customer Credit',
                            data: salesData.map(s => parseFloat(s.CustomerCredit)),
                            backgroundColor: customerCreditGradient,
                            borderColor: 'rgb(75, 192, 192)',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                            hoverBackgroundColor: 'rgb(75, 192, 192)',
                            hoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
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
                                    delay = context.dataIndex * 80 + context.datasetIndex * 40;
                                }
                                return delay;
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    padding: 20,
                                    font: {
                                        size: 13,
                                        weight: '600',
                                        family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                    },
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 8,
                                    boxHeight: 8,
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
                                    font: {
                                        size: 12,
                                        weight: '500',
                                        family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                    },
                                    color: '#6B7280',
                                    padding: 12,
                                    callback: function(value) {
                                        return new Intl.NumberFormat('en-US', {
                                            notation: 'compact',
                                            compactDisplay: 'short'
                                        }).format(value);
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 12,
                                        weight: '600',
                                        family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                    },
                                    color: '#374151',
                                    padding: 10
                                },
                                barPercentage: 0.7,
                                categoryPercentage: 0.8
                            }
                        }
                    }
                });
            }
        }, 100);
    </script>
</div>