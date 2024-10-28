<div class="card">
    <div class="card-header p-2">
        <div class="col-md-12 py-1">
            <div class="card">
                <div class="card-body">
                    <canvas id="chBar" style=" height: 100px;position: relative;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        let colors = ['#007bff', '#28a745', '#333333', '#c3e6cb', '#dc3545', '#6c757d'];

        /* bar chart */
        let chBar = document.getElementById("chBar");
        if (chBar) {
            chBar.height = 30;
            new Chart(chBar, {
                type: 'bar',
                data: {
                    labels: ["S", "M", "T", "W", "T", "F", "S"],
                    datasets: [{
                            data: [589, 445, 483, 503, 689, 692, 634],
                            backgroundColor: colors[0]
                        },
                        // {
                        //     data: [639, 465, 493, 478, 589, 632, 674],
                        //     backgroundColor: colors[1]
                        // }
                    ]
                },
                options: {
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            display: false // Hide the Y-axis
                        }],
                        xAxes: [{
                            barPercentage: 0.4,
                            categoryPercentage: 0.5
                        }]
                    }
                }
            });
        }
    </script>
@endscript
