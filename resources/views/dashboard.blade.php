@extends('layouts.master-layout')

@section('title', 'Dashboard')

@section('breadcrumtitle', 'Dashboard')

@section('navdashboard', 'active')

@section('dashboardInlineCSS', 1)



@section('content')

    <br /><br />

    @if ($permission)
        <div class="row">
            <div id="projectedSales" class="col-lg-6 col-md-6" style="cursor:pointer;">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-poll widget-icon bg-danger rounded-circle text-white"></i>
                        </div>
                        <h5 class=" fw-normal mt-0" title="Revenue">Projected Sales</h5>
                        <h3 class="mt-3 mb-3">{{ empty($projected) ? 0 : number_format($projected[0]->sales, 2) }}</h3>
                        <p class="mb-0 ">
                            <span class="badge badge-info mr-1">
                                <i class="mdi mdi-arrow-down-bold"></i> Projected Sales</span>
                            <span class="text-nowrap">Since last month</span>
                        </p>
                    </div>
                </div>
            </div>
            <div id="closedSales" class="col-lg-6 col-md-6" onclick="getdetails()" style="cursor:pointer;">
                <div class="card widget-flat ">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-currency-usd widget-icon  rounded-circle "></i>
                        </div>
                        <h5 class=" fw-normal mt-0" title="Revenue">All Closed Sales</h5>
                        <h3 class="mt-3 mb-3">
                            {{ empty($totalSales) ? 0 : number_format($totalSales[0]->TotalSales, 2) }}</h3>
                        <p class="mb-0 ">
                            <span class="badge badge-warning mr-1">
                                <i class="mdi mdi-arrow-up-bold"></i> Total Sales</span>
                            <span class="text-nowrap">Since last month</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            var data1 = [
                @foreach ($sales as $saleValue)
                    {
                        y: '{{ $saleValue->terminal_name }}',
                        a: {{ $saleValue->cash }},
                        b: {{ $saleValue->creditCard }},
                        c: {{ $saleValue->CustomerCredit }}
                    },
                @endforeach
            ];
            {{-- var data2 = [
                @foreach ($monthsales as $sales)
                    {
                        "x": '{{ $sales->branch_name . ' (' . date('F', strtotime($sales->date)) . ')' }}',
                        "y": '{{ $sales->total }}'
                    },
                @endforeach
            ]; --}}
        </script>
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-xl-4  ">

                <div class="card" style="height: 380px;">
                    <div class="card-header">
                        <h5 class="card-header-text">Terminal Daily Sales Chart</h5>
                    </div>
                    <div class="card-block">
                        <div id="bar-example1" class="" style="height: 260px;">
                            <script type="text/javascript">
                                var $arrColors = ['#666', '#3498DB', '#7D3323', '#48C9B0', '#2471A3', '#6C3483', '#6E2C00', '#F1C40F', '#73C6B6',
                                    '#34495E'
                                ];
                                Morris.Bar({
                                    element: 'bar-example1',
                                    barGap: 1,
                                    barSizeRatio: 0.35,
                                    data: data1,
                                    xkey: 'y',
                                    ykeys: ['a', 'b', 'c'],
                                    labels: ['Cash', 'CreditCard', 'CustomerCredit']
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="row">
            <div class="col-xl-12 col-md-12  col-xl-12 col-lg-12 ">

                <div class="card" style="height: 380px;">
                    <div class="card-header">
                        <h5 class="card-header-text">Monthly Sales Chart</h5>
                    </div>
                    <div class="card-block">
                        <div id="bar-example" class="" style="height: 260px;">
                            <script type="text/javascript">
                                var $arrColors = ['#34495E', '#26B99A', '#666', '#3498DB', '#7D3323', '#48C9B0', '#2471A3', '#6C3483', '#6E2C00',
                                    '#F1C40F', '#73C6B6', '#34495E', '#34495E', '#26B99A', '#666', '#3498DB', '#7D3323', '#48C9B0', '#2471A3', '#6C3483'
                                ];
                                Morris.Bar({
                                    barGap: 1,
                                    barSizeRatio: 0.25,
                                    element: 'bar-example',
                                    data: data2,
                                    xkey: 'x',
                                    ykeys: ['y'],
                                    barColors: function(row, series, type) {
                                        return $arrColors[row.x];
                                    },
                                    labels: ['Added']
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>   --}}

        {{-- @livewire('dashboard.monthly-sales-chart') --}}
        <livewire:dashboard.monthly-sales-chart />

        <div class="row">
            <div class="col-md-6 col-xl-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-text">Top 5 Products</h5>
                    </div>
                    <div class="card-block">
                        <div id="donut-example">

                            <script type="text/javascript">
                                Morris.Donut({
                                    element: 'donut-example',
                                    data: [
                                        @foreach ($products as $key => $value)

                                            {
                                                label: "{{ $value->product_name }}",
                                                value: '{{ $value->count }}',
                                                labelColor: 'red',
                                            },
                                        @endforeach
                                    ],
                                    colors: ['#EC407A', '#00897B', '#C0CA33', '#9CC4E4', '#7D3323'],
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-6 col-md-6 col-xl-6 ">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-text">Yearly Chart</h5>
                    </div>
                    <div class="card-block">
                        <div id="line-example">
                            <script type="text/javascript">
                                Morris.Line({
                                    element: 'line-example',
                                    data: [
                                        @foreach ($year as $value)
                                            {
                                                y: '{{ $value->year }}',
                                                a: '{{ $value->amount }}'
                                            },
                                        @endforeach
                                    ],
                                    xkey: 'y',
                                    redraw: true,
                                    ykeys: ['a'],
                                    labels: ['Series A'],
                                    lineColors: ['#2196F3']
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal fade modal-flex" id="Modal-tab" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content ">
                    <div class="modal-body ">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <ul class="nav nav-tabs" role="tablist" id="terminalTab">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tab-home" role="tab">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-profile" role="tab">Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-messages" role="tab">Messages</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-settings" role="tab">Settings</a>
                            </li>
                        </ul>
                        <div class="tab-content modal-body">
                            <div class="row text-center text-primary">
                                <h1>Terminal Name</h1>
                            </div>

                            <div class="row dashboard-header m-t-5">
                                <div class="col-lg-4 col-md-4">
                                    <div class="card dashboard-product">
                                        <span class="label label-info">OPENING</span>
                                        <h2 class="dashboard-total-products">4500</h2>
                                        <!--  <span class="label label-info">OPENING</span> -->
                                        <div class="side-box">
                                            <i class="icon-cursor text-info-color"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4">
                                    <div class="card dashboard-product">
                                        <!-- <span>TOTAL SALES</span> -->
                                        <span class="label label-warning">TOTAL SALES</span>
                                        <h2 class="dashboard-total-products">4500</h2>

                                        <div class="side-box">
                                            <i class="icon-handbag text-warning-color"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4">
                                    <div class="card dashboard-product" style="border-color: #CD5C5C;">
                                        <!--   <span>CLOSING</span> -->
                                        <span class="label label-danger">CLOSING</span>
                                        <h2 class="dashboard-total-products">4500</h2>

                                        <div class="side-box">
                                            <i class="icon-vector text-danger-color"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row dashboard-header m-t-5">
                                <div class="col-lg-4 col-md-4 grid-item">
                                    <div class="card">
                                        <div class="row">
                                            <div class="col-sm-12 d-flex">
                                                <div class="col-sm-5 bg-warning">
                                                    <div class="p-10 text-center">
                                                        <i class="icofont icofont-cur-dollar f-64"></i>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="text-center">
                                                        <h3 class="txt-warning f-34" id="MyClockDisplay" class="clock">
                                                            Rs. 1500</h3>
                                                        <span class="text-default  f-18">TAKE AWAY</span>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>


                                <div class="col-lg-4 col-md-4 grid-item">
                                    <div class="card">
                                        <div class="row">
                                            <div class="col-sm-12 d-flex">
                                                <div class="col-sm-5 bg-info">
                                                    <div class="p-10 text-center">
                                                        <i class="icofont icofont-cur-dollar f-64"></i>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="text-center">
                                                        <h3 class="txt-warning  f-w-50" class="clock">Rs. 1500</h3>
                                                        <span class="text-default f-18 ">ONLINE</span>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-4 grid-item">
                                    <div class="card">
                                        <div class="row">
                                            <div class="col-sm-12 d-flex">
                                                <div class="col-sm-5 bg-primary">
                                                    <div class="p-10 text-center">
                                                        <i class="icofont icofont-cur-dollar f-64"></i>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="text-center">
                                                        <h3 class="txt-warning f-34" id="MyClockDisplay" class="clock">
                                                            Rs. 1500</h3>
                                                        <span class="text-default  f-18">DELIVERY</span>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <table width="100%" class="table table-responsive nowrap">
                                <tr>
                                    <td style="width:500px">Opening Balance</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Cash Sale</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Credit Card Sale</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Customer Credit Sale</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Total Sale</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Total Receipt Item Cost</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Customer Credit Return Cash</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Customer Credit Return Credit</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Customer Credit Return Cheque</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Bank Deposit</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Expense</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Purchase</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Sale Return</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Discount</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Cash In</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Cash Out</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Coupon</td>
                                    <td style="width:500px">2</td>
                                </tr>
                                <tr>
                                    <td style="width:500px">Cash In Hand</td>
                                    <td style="width:500px">2</td>
                                </tr>

                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('css_code')

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.css"
integrity="sha256-NAxhqDvtY0l4xn+YVa6WjAcmd94NNfttjNsDmNatFVc=" crossorigin="anonymous" />
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<style>
a {
    text-decoration: none !important;
}

.outer {
    width: 100%;
    height: 150px;
    white-space: nowrap;
    position: relative;
    overflow-x: scroll;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
}

.outer .inner {
    width: 30%;
    background-color: #eee;
    float: none;
    height: 90%;
    margin: 0 0.25%;
    display: inline-block;
    zoom: 1;
}

.card-resize {
    height: 469px;
}

#projectedSales div:hover {
    background: #4CAF50;
    color: #ffffff;
}
/*#198754 #5cb85c*/
#closedSales div:hover {
    background: #4CAF50;
    color: #ffffff;
}

nav .navbar{
    position: relative;
}

.navbar-custom-menu{
    position: absolute;
    right: 0;
}
</style>


    <style>
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

        @-webkit-keyframes spin {
            from {
                -webkit-transform: rotate(0deg);
            }

            to {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
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
            border-color: black;
            border-top-color: transparent;
            border-width: 4px;
            border-radius: 50%;
            -webkit-animation: spin .8s linear infinite;
            animation: spin .8s linear infinite;
        }

        @media (max-width: 1000px) {
            section {
                margin-top: 30px;
            }

            body {
                line-height: 0;
            }

        }

        @media (max-width: 790px) {
            section {
                margin-top: 61px;
            }

            body {
                line-height: 0;
            }

            .container-fluid {
                margin-top: 65px;
            }

        }

        @media (max-width: 600px) {
            section {
                margin-top: 61px;
            }

            body {
                line-height: 0;
            }

            .container-fluid {
                margin-top: 65px;
            }

            .new-orders i {
                padding: 17px 15px;
            }

        }

        .bg-success {
            background-color: #4CAF50 !important;
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

    //  function getdetails() {
    //     window.location = "{{-- url('sales-details') --}}";
    // }


        <?php if (session('login_msg')) { ?>

        $(document).ready(function() {

            notify('{{ session('login_msg') }}', 'success');
            <?php $_SESSION['login_msg'] = ''; ?>
        });

        <?php } ?>



        function showTime() {
            var date = new Date();
            var h = date.getHours(); // 0 - 23
            var m = date.getMinutes(); // 0 - 59
            var s = date.getSeconds(); // 0 - 59
            var session = "AM";

            if (h == 0) {
                h = 12;
            }

            if (h > 12) {
                h = h - 12;
                session = "PM";
            }

            h = (h < 10) ? "0" + h : h;
            m = (m < 10) ? "0" + m : m;
            s = (s < 10) ? "0" + s : s;

            var time = h + ":" + m + " " + session;
            $('#MyClockDisplay').html(time);
            // document.getElementById("MyClockDisplay").innerText = time;
            // document.getElementById("MyClockDisplay").textContent = time;

            setTimeout(showTime, 1000);

        }

        let total = "{{ $orders[0]->total }}";
        let pending = "{{ $orders[0]->pending }}";
        let processing = "{{ $orders[0]->processing }}";
        let ready = "{{ $orders[0]->ready }}";
        let delivered = "{{ $orders[0]->delivery }}";
        let cancelled = "{{ $orders[0]->cancelled }}";

        let pendingpercentage = pending / total * 100;
        let processingpercentage = processing / total * 100;
        let readypercentage = ready / total * 100;
        let deliveredpercentage = delivered / total * 100;
        let cancelledpercentage = cancelled / total * 100;


        showTime();

        "use strict";
        $(document).ready(function() {
            var progression1 = 0;
            var progression2 = 0;
            var progression3 = 0;
            var progression4 = 0;
            var progression5 = 0;
            var progress = setInterval(function() {

                $('.progress .faq-text1').text(progression1 + '%');
                $('.progress .faq-text1').css({
                    'left': progression1 + '%'
                });
                $('.progress .faq-text1').css({
                    'top': '-20px'
                });
                $('.progress .faq-bar1').css({
                    'width': progression1 + '%'
                });

                if (progression1 == parseInt(pendingpercentage)) {
                    clearInterval(progress);

                } else
                    progression1 += 1;

            }, 100);

            var progress1 = setInterval(function() {
                $('.progress .faq-text2').text(progression2 + '%');
                $('.progress .faq-text2').css({
                    'left': progression2 + '%'
                });
                $('.progress .faq-text2').css({
                    'top': '-20px'
                });
                $('.progress .faq-bar2').css({
                    'width': progression2 + '%'
                });
                if (progression2 == parseInt(processingpercentage)) {
                    clearInterval(progress1);

                } else
                    progression2 += 1;

            }, 100);
            var progress2 = setInterval(function() {
                $('.progress .faq-text5').text(progression3 + '%');
                $('.progress .faq-text5').css({
                    'left': progression3 + '%'
                });
                $('.progress .faq-text5').css({
                    'top': '-20px'
                });
                $('.progress .faq-bar5').css({
                    'width': progression3 + '%'
                });
                if (progression3 == parseInt(readypercentage)) {
                    clearInterval(progress2);

                } else
                    progression3 += 1;

            }, 100);
            var progress3 = setInterval(function() {
                $('.progress .faq-text4').text(progression4 + '%');
                $('.progress .faq-text4').css({
                    'left': progression4 + '%'
                });
                $('.progress .faq-text4').css({
                    'top': '-20px'
                });
                $('.progress .faq-bar4').css({
                    'width': progression4 + '%'
                });
                if (progression4 == parseInt(deliveredpercentage)) {
                    clearInterval(progress3);

                } else
                    progression4 += 1;

            }, 100);
            var progress4 = setInterval(function() {
                $('.progress .faq-text3').text(progression5 + '%');
                $('.progress .faq-text3').css({
                    'left': progression5 + '%'
                });
                $('.progress .faq-text3').css({
                    'top': '-20px'
                });
                $('.progress .faq-bar3').css({
                    'width': progression5 + '%'
                });
                if (progression5 == parseInt(cancelledpercentage)) {
                    clearInterval(progress4);

                } else
                    progression5 += 1;

            }, 100);




            $('#contact-list').DataTable({
                fixedHeader: true,
                "scrollY": 572,
                "paging": false,
                "ordering": false,
                "bLengthChange": false,
                "searching": false,
                "info": false

            });

            // add scroll to data table
            $(".dataTables_scrollBody").slimScroll({
                height: 675,
                allowPageScroll: false,
                wheelStep: 5,
                color: '#000'
            });

        });




        function getTerminals(branch) {
            $.ajax({
                url: "{{ url('/getTerminals') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    branch: branch
                },
                dataType: 'json',
                success: function(result) {
                    console.log(result)
                    $('#terminalTab').html('');
                    $.each(result, function(index, value) {
                        $('#terminalTab').append(
                            "<li class='nav-item'><a class='nav-link " + (index == 0 ? "active" :
                                "") + "'  data-toggle='tab' href='#tab-home' role='tab'>" + value
                            .terminal_name + "</a></li>"
                        );
                    });
                }
            });
        }
        getcheques();

        function getcheques() {

            var d = new Date(),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;

            var today = [year, month, day].join('-');

            const date = moment(new Date());
            date.add(1, 'days');
            var tomorrow = date.format('YYYY-MM-DD'); //

            $.ajax({
                url: "{{ url('/getcheques') }}",
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                dataType: 'json',
                success: function(result) {
                    if (result) {
                        $("#tblcheques tbody").empty();
                        for (var count = 0; count < result.length; count++) {
                            $("#tblcheques tbody").append(
                                "<tr>" +
                                "<td>" + today + "</td>" +
                                "<td>" + result[count].todays + "</td>" +
                                "<td><a href='{{ url('/chequemodule') }}/" + today +
                                "'><i class='icofont icofont-eye-alt'></i></a></td>" +
                                "</tr>" +
                                "<tr>" +
                                "<td>" + tomorrow + "</td>" +
                                "<td>" + result[count].tomorrow + "</td>" +
                                "<td><a href='{{ url('/chequemodule') }}/" + tomorrow +
                                "'><i class='icofont icofont-eye-alt'></i></a></td>" +
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

