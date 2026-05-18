<style>
    .monthly-sales-card {
        background: #ffffff;
        border: 1px solid #e6ebf1;
        border-radius: 8px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .07);
        overflow: hidden;
    }

    .monthly-sales-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        padding: 22px;
        border-bottom: 1px solid #eef2f6;
    }

    .monthly-sales-title {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: 0;
    }

    .monthly-sales-subtitle {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
    }

    .monthly-sales-kicker {
        display: block;
        margin-bottom: 6px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .monthly-sales-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(120px, 1fr));
        gap: 10px;
        min-width: 430px;
    }

    .monthly-stat {
        padding: 12px;
        border: 1px solid #eef2f6;
        border-radius: 8px;
        background: #fbfcfd;
    }

    .monthly-stat span {
        display: block;
        color: #8a94a3;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .monthly-stat strong {
        display: block;
        margin-top: 5px;
        color: #111827;
        font-size: 16px;
        font-weight: 800;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .monthly-sales-body {
        padding: 22px;
    }

    .monthly-chart-wrap {
        position: relative;
        height: 380px;
        width: 100%;
    }

    @media (max-width: 991px) {
        .monthly-sales-header {
            flex-direction: column;
        }

        .monthly-sales-stats {
            width: 100%;
            min-width: 0;
        }
    }

    @media (max-width: 640px) {
        .monthly-sales-stats {
            grid-template-columns: 1fr;
        }

        .monthly-chart-wrap {
            height: 330px;
        }
    }
</style>

<div class="monthly-sales-card" wire:loading.remove>
    <div class="monthly-sales-header">
        <div>
            <span class="monthly-sales-kicker">Monthly trend</span>
            <h3 class="monthly-sales-title">Monthly Sales Performance</h3>
            <p class="monthly-sales-subtitle">Top branches are shown separately; remaining branches are grouped for readability.</p>
        </div>

        <div class="monthly-sales-stats">
            <div class="monthly-stat">
                <span>Total Sales</span>
                <strong>{{ number_format($summary['totalSales'] ?? 0, 0) }}</strong>
            </div>
            <div class="monthly-stat">
                <span>Avg / Month</span>
                <strong>{{ number_format($summary['avgSales'] ?? 0, 0) }}</strong>
            </div>
            <div class="monthly-stat">
                <span>Top Branch</span>
                <strong title="{{ $summary['topBranch'] ?? 'N/A' }}">{{ $summary['topBranch'] ?? 'N/A' }}</strong>
            </div>
        </div>
    </div>

    <div class="monthly-sales-body">
        <div class="monthly-chart-wrap">
            <canvas id="monthlySalesChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function() {
        function formatAmount(value) {
            return new Intl.NumberFormat('en-US', {
                notation: 'compact',
                compactDisplay: 'short',
                maximumFractionDigits: 1
            }).format(value || 0);
        }

        function drawMonthlySalesChart() {
            var canvas = document.getElementById('monthlySalesChart');
            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            if (window.monthlySalesChartInstance) {
                window.monthlySalesChartInstance.destroy();
            }

            var datasets = @json($chartData).map(function(dataset) {
                return {
                    label: dataset.label,
                    data: dataset.data,
                    backgroundColor: dataset.backgroundColor,
                    borderColor: dataset.borderColor,
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                    maxBarThickness: 54
                };
            });

            window.monthlySalesChartInstance = new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($monthNames),
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            align: 'start',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'rectRounded',
                                boxWidth: 10,
                                boxHeight: 10,
                                padding: 16,
                                color: '#4b5563',
                                font: {
                                    size: 12,
                                    weight: '700'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, .96)',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + Number(context.parsed.y || 0).toLocaleString();
                                },
                                footer: function(items) {
                                    var total = items.reduce(function(sum, item) {
                                        return sum + Number(item.parsed.y || 0);
                                    }, 0);

                                    return 'Month Total: ' + total.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#4b5563',
                                font: {
                                    size: 12,
                                    weight: '700'
                                }
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grid: {
                                color: '#eef2f6'
                            },
                            ticks: {
                                color: '#6b7280',
                                padding: 8,
                                callback: formatAmount
                            }
                        }
                    }
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', drawMonthlySalesChart);
        } else {
            drawMonthlySalesChart();
        }
    })();
</script>
