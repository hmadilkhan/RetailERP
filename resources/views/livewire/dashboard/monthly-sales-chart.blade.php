<div class="card" wire:loading.remove>
    <div class="card-header">Monthly Sales by Branch (Last 7 Months)</div>
    <div class="card-body">
        <div class="row">
            <div class="col-xl-12 col-md-12  col-xl-12 col-lg-12 ">
                <canvas id="monthlySalesChart" height="100"></canvas>
            </div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@section('scriptcode_three')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('monthlySalesChart');
            canvas.height = 100; // Set height in pixels

            const ctx = canvas.getContext('2d');
            const monthlySalesChart = new Chart(ctx, {
                type: 'bar', // Bar chart
                data: {
                    labels: @json($monthNames), // Months as labels
                    datasets: @json($chartData) // Data for each branch
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {
                            stacked: false // Set to true if you want stacked bars
                        }
                    }
                }
            });
        });
    </script>
@endsection
