@extends('layouts.master-layout')

@section('title', 'Dashboard')

@section('breadcrumtitle', 'Dashboard')

@section('navdashboard', 'active')
@section('css_code')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.css"
        integrity="sha256-NAxhqDvtY0l4xn+YVa6WjAcmd94NNfttjNsDmNatFVc=" crossorigin="anonymous" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('home') }}" class="btn btn-success text-white"><i
                        class="icofont icofont-arrow-left"></i>Back</a>
            </div>
        </div>
        <br />
        <div class="row">
            <input type="hidden" id="terminalID">
            <input type="hidden" id="openingID">
            <section class="">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-10">
                                <button id="active" class="btn btn-outline-success" type="button"
                                    onclick="showHide(this,'tab-active')">Active</button>
                                <button id="closed" class="btn btn-outline-success" type="button"
                                    onclick="showHide(this,'tab-closed')">Closed</button>
                            </div>
                            <div class="col-md-2">
                                <input type="date" class="form-control" id="dateselection" style="display:none"
                                    value="{{ date('Y-m-d', strtotime('-1 days')) }}" />
                            </div>
                        </div>
                    </div>
                </div>
                {{-- </section>
            <section class="mt-n2"> --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Branches</h5>
                    </div>
                    <div class="card-header card-body">
                        <div class="row">
                            <div id="tab-active" class="mt-1">
                                @foreach ($branches as $value)
                                    <div class="col-xl-3 col-lg-3 col-md-3 inner" style="cursor: pointer;"
                                        onclick="getdetails('{{ session('roleId') == 2 ? $value->branch_id : $value->terminal_id }}','{{ $value->identify }}','open')">
                                        <div class="card">
                                            <div class="card-block">
                                                <div class="media d-flex">
                                                    <div class="media-left media-middle">
                                                        <a href="#">
                                                            <img class="media-object img-circle"
                                                                src="{{ asset('storage/images/branch/' . (!empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg') . '') }}"
                                                                width="50" height="50">
                                                        </a>
                                                    </div>
                                                    <div class="media-body p-t-10">
                                                        <span class="counter-txt f-w-600 f-20">
                                                            <span class="text-primary"> {{ session('currency') }}
                                                                {{ number_format($value->sales, 0) }} /=</span>
                                                        </span>
                                                        <h6 class="f-w-300 m-t-1">
                                                            {{ session('roleId') == 2 ? $value->branch_name : $value->terminal_name }}
                                                        </h6>
                                                    </div>
                                                </div>
                                                <ul>
                                                    <li class="new-users">
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div id="tab-closed" style="display:none">
                                @foreach ($branchesClosedSales as $value)
                                    <div class="col-xl-3 col-lg-3 col-md-3 inner" style="cursor: pointer;"
                                        onclick="getdetails('{{ session('roleId') == 2 ? $value->branch_id : $value->terminal_id }}','{{ $value->identify }}','close')">
                                        <div class="card">
                                            <div class="card-block">
                                                <div class="media d-flex">
                                                    <div class="media-left media-middle">
                                                        <a href="#">
                                                            <img class="media-object img-circle"
                                                                src="{{ asset('storage/images/branch/' . (!empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg') . '') }}"
                                                                width="50" height="50">
                                                        </a>
                                                    </div>
                                                    <div class="media-body p-t-10">
                                                        <span class="counter-txt f-w-600 f-20">
                                                            <span class="text-primary"> {{ session('currency') }}
                                                                {{ number_format($value->sales, 0) }} /=</span>
                                                        </span>
                                                        <h6 class="f-w-300 m-t-1">
                                                            {{ session('roleId') == 2 ? $value->branch_name : $value->terminal_name }}
                                                        </h6>
                                                    </div>
                                                </div>
                                                <ul>
                                                    <li class="new-users">
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                {{-- </section>
            <section> --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Terminals</h5>
                        <hr />
                        <div class="col-md-12 overflow-x-auto" style="overflow-y:hidden;">
                            <ul class="list-group list-group-horizontal flex-nowrap" id="terminalTab">

                            </ul>
                        </div>
                        <div class="col-md-12 overflow-x-auto mt-2" style="overflow-y:hidden;">
                            <ul class="list-group list-group-horizontal flex-nowrap" id="declartionTab">

                            </ul>
                        </div>
                    </div>
                    <div class=" card-body">
                        <div id="div_details"></div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    </div>

@endsection

@section('scriptcode_three')
    <script type="text/javascript">
        var activeStatus = "";

        function showHide(arg, id) {
            if (id == 'tab-closed') {
                $('.active-active').removeClass('active');
                $('.closed-active').addClass('active');
                $('#tab-closed').show();
                $('#tab-active').hide();
                activeStatus = "close"
                $("#dateselection").css("display", "block");
                $("#declartionTab").html("");
            } else {
                $('.closed-active').removeClass('active');
                $('.active-active').addClass('active');
                $('#tab-closed').hide();
                $('#tab-active').show();
                activeStatus = "open"
                $("#dateselection").css("display", "none");
                $("#declartionTab").html("");
            }
        }
        var terminal = 0;
        var terminal_name = "";

        function getdetails(branch, status, branchstatus) {
            if (branchstatus == "close") {
                getCloseTerminals(branch, status)
            } else {
                getTerminals(branch, status);
            }
            $('#div_details').empty();
        }

        getTerminals('{{ $branches[0]->branch_id }}');

        function getTerminals(branch, status) {

            $.ajax({
                url: "{{ url('/getTerminals') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    branch: branch,
                    status: status
                },
                dataType: 'json',
                async: false,
                success: function(result) {
                    $('#terminalTab').html('');


                    $.each(result, function(index, value) {
                        if (index == 0) {
                            $('#terminalName').html(value.terminal_name);
                            terminal = value.terminal_id;
                            terminal_name = value.terminal_name;
                            $('#terminalName').html(value.terminal_name);
                            getPartial(value.terminal_id)
                        }
                        let terminalDiv = '<li id=' + value.terminal_id + ' onclick="getPartial(' +
                            value.terminal_id +
                            ', 1)" class="list-group-item"> <button id="active" class="btn btn-outline-success" type="button">' +
                            value.terminal_name + '</button></li>';
                        $('#terminalTab').append(terminalDiv);
                    });
                }
            });
        }

        function getCloseTerminals(branch, status) {

            $.ajax({
                url: "{{ url('/getTerminals') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    branch: branch,
                    status: status
                },
                dataType: 'json',
                async: false,
                success: function(result) {
                    $('#terminalTab').html('');
                    $.each(result, function(index, value) {

                        if (index == 0) {
                            $('#terminalName').html(value.terminal_name);
                            terminal = value.terminal_id;
                            terminal_name = value.terminal_name;
                            $('#terminalName').html(value.terminal_name);
                        }
                        let terminalDiv = '<li id=' + value.terminal_id + ' onclick="getDeclarations(' +
                            value.terminal_id +
                            ')" class="list-group-item"> <button id="active" class="btn btn-outline-success" type="button">' +
                            value.terminal_name + '</button></li>';
                        $('#terminalTab').append(terminalDiv);
                        // $('#terminalTab').append(
                        //     "<li id=" + value.terminal_id + " onclick='getDeclarations(" + value
                        //     .terminal_id + ")' class='nav-item m-t-5 f-24'><a id=" + value
                        //     .terminal_id + " class='nav-link " + (index == 0 ? "active" : "") +
                        //     "'  data-toggle='tab' href='#tab-home' role='tab'>" + value
                        //     .terminal_name + "</a></li>"
                        // );
                    });
                }
            });
        }

        function getDeclarations(terminalId) {
            console.log("declarations");

            let date = $("#dateselection").val();
            if (date == "") {
                alert("Please select date")
            } else {
                $.ajax({
                    url: "{{ url('/get-close-declarations') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        terminal: terminalId,
                        date: date
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#declartionTab').html('');
                        $('#div_details').html('');
                        $.each(result, function(index, value) {
                            let DeclarationDiv = '<li id=' + value.opening_id + ' onclick="getLastDayPartial(' +
                            terminalId + "," + value.opening_id +
                            ')" class="list-group-item"> <button id="active" class="btn btn-outline-success" type="button">' +
                            "D#"+value.opening_id + '</button></li>';
                            $('#declartionTab').append(DeclarationDiv);
                            // $('#declartionTab').append(
                            //     "<li id=" + value.opening_id + " onclick='getLastDayPartial(" +
                            //     terminalId + "," + value.opening_id +
                            //     ")' class='nav-item m-t-5 f-24'><a id=" + value.opening_id +
                            //     " class='nav-link " + (index == 0 ? "active" : "") +
                            //     "'  data-toggle='tab' href='#tab-home' role='tab'>D#" + value
                            //     .opening_id + "</a></li>"
                            // );
                        });
                    }
                });
            }
        }

        getPartial(terminal) //ye wala comment

        function getHeads(terminal, index) {
            getPermission(terminal);
            clearControls();
            $('#terminalID').val(terminal);
            if (index > 0) {
                $('#terminalName').html($("#" + terminal + "").text());
            }
            $.ajax({
                url: "{{ url('/heads-details') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    terminal: terminal
                },
                dataType: 'json',
                success: function(result) {
                    if (result != "") {
                        $('#openingID').val(result[0].opening_id);
                        if (result[0].closingBal == 0) {
                            $('#status').html("ACTIVE")
                            $('#status').removeClass("tag-danger");
                            $('#status').addClass("tag-success");
                        } else {
                            $('#status').html("CLOSED");
                            $('#status').removeClass("tag-success");
                            $('#status').addClass("tag-danger");
                        }

                        $('#ob').html("Rs. " + parseInt(result[0].bal).toLocaleString());
                        $('#odate').html(getDateInWords(result[0].date) + " | " + result[0].time);
                        $('#cdate').html(getDateInWords(result[0].closingDate) + " " + (result[0].closingTime ==
                            null ? "" : " | " + result[0].closingTime));
                        $('#totalSales').html("Rs. " + parseInt(result[0].TotalSales).toLocaleString());
                        $('#cb').html("Rs. " + parseInt(result[0].closingBal).toLocaleString());
                        $('#takeaway').html("Rs. " + parseInt(result[0].TakeAway).toLocaleString());
                        $('#delivery').html("Rs. " + parseInt(result[0].Delivery).toLocaleString());
                        $('#online').html("Rs. " + parseInt(result[0].Online).toLocaleString());
                        $('#opening').html("Rs. " + parseInt(result[0].bal).toLocaleString());
                        $('#cashSales').html("Rs. " + (parseInt(result[0].Cash + parseInt(result[0].Discount)))
                            .toLocaleString());
                        $('#creditCard').html("Rs. " + parseInt(result[0].CreditCard).toLocaleString());
                        $('#customerCredit').html("Rs. " + parseInt(result[0].CustomerCredit).toLocaleString());
                        $('#Sales').html("Rs. " + parseInt(result[0].TotalSales).toLocaleString());
                        $('#totalCost').html("Rs. " + parseInt(result[0].cost).toLocaleString());
                        $('#discount').html("Rs. " + parseInt(result[0].Discount).toLocaleString());
                        $('#salesReturn').html("Rs. " + parseInt(result[0].SalesReturn).toLocaleString());
                        $('#cashReturn').html("Rs. " + parseInt(result[0].CashReturn).toLocaleString());
                        $('#cardReturn').html("Rs. " + parseInt(result[0].CardReturn).toLocaleString());
                        $('#chequeReturn').html("Rs. " + parseInt(result[0].ChequeReturn).toLocaleString());
                        $('#cashIn').html("Rs. " + parseInt(result[0].cashIn).toLocaleString());
                        $('#cashOut').html("Rs. " + parseInt(result[0].cashOut).toLocaleString());

                        var positive = parseInt(result[0].bal) + parseInt(result[0].Cash) + parseInt(result[0]
                                .CreditCard) + parseInt(result[0].CustomerCredit) + parseInt(result[0].cashIn) +
                            parseInt(result[0].CashReturn) + parseInt(result[0].CardReturn) + parseInt(result[0]
                                .ChequeReturn);
                        var negative = parseInt(result[0].cost) + parseInt(result[0].SalesReturn) + parseInt(
                            result[0].cashOut);
                        var CashInHand = positive - negative;
                        $('#CIH').html("Rs. " + CashInHand.toLocaleString());
                    } else {
                        clearControls();
                    }
                }
            });
        }

        function clearControls() {
            $('#ob').html("Rs. 0");
            $('#totalSales').html("Rs. 0");
            $('#cb').html("Rs. 0");
            $('#takeaway').html("Rs. 0");
            $('#delivery').html("Rs. 0");
            $('#online').html("Rs. 0");
            $('#opening').html("Rs. 0");
            $('#cashSales').html("Rs. 0");
            $('#creditCard').html("Rs. 0");
            $('#customerCredit').html("Rs. 0");
            $('#Sales').html("Rs. 0");
            $('#totalCost').html("Rs. 0");
            $('#discount').html("Rs. 0");
            $('#salesReturn').html("Rs. 0");
            $('#cashReturn').html("Rs. 0");
            $('#cardReturn').html("Rs. 0");
            $('#chequeReturn').html("Rs. 0");
            $('#cashIn').html("Rs. 0");
            $('#cashOut').html("Rs. 0");
            $('#CIH').html("Rs. 0");
        }



        function getDateInWords(date) {
            if (date != null) {
                var d = new Date(date);
                var days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                var month = ["January", "February", "March", "April", "May", "June", "July"];
                return days[d.getDay()] + ", " + d.getDay() + " " + month[d.getMonth()] + " " + d.getFullYear();
            } else {
                return "";
            }

        }

        function getPartial(terminal) {
            $('#div_details').empty();
            $('#div_details').append(
                "<div class='position-absolute w-100 h-100 d-flex flex-column align-items-center bg-white justify-content-center'>" +
                "<div class='spinner-grow text-success' role='status'>" +
                "<span class='visually-hidden'>Loading...</span></div></div>"
            )
            $.ajax({
                url: "{{ url('/heads') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    terminal: terminal,
                    status: activeStatus
                },
                success: function(result) {
                    $('#div_details').empty();
                    $('#div_details').html(result);
                },
                error: function(request, error) {
                    $('#div_details').empty();
                }
            });
        }

        function getLastDayPartial(terminal, openingId) {
            $('#div_details').empty();
            $('#div_details').append(
                "<div class='position-absolute w-100 h-100 d-flex flex-column align-items-center bg-white justify-content-center'>" +
                "<div class='spinner-grow text-success' role='status'>" +
                "<span class='visually-hidden'>Loading...</span></div></div>"
            )
            $.ajax({
                url: "{{ url('/last-day-heads') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    terminal: terminal,
                    openingId: openingId
                },
                success: function(result) {
                    $('#div_details').empty();
                    $('#div_details').html(result);
                },
                error: function(request, error) {
                    $('#div_details').empty();
                }
            });
        }
    </script>
@endsection
