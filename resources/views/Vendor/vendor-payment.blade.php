@extends('layouts.master-layout')

@section('title', 'Vendor Payables')

@section('breadcrumtitle', 'View Vendor Payables')
@section('navVendorPO', 'active')
@section('navpurchase', 'active')
@section('nav_viewpurchase', 'active')


@section('content')
    <style>
        label {
            cursor: pointer;
        }
    </style>
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h1 class="">Vendor Payables</h1>
                <h5 class="card-header-text">Vendor Due Payment Details</h5><br />
                <h5 class="f-right card-header-text bg-warning label-lg" id="filterText"></h5>
            </div>

            <div class="card-block">
                <ul class="nav nav-tabs md-tabs">
                    <input type="hidden" name="type" id="type" value="all" />

                    <li class="nav-item">
                        <a class="nav-link active text-size" id="draft" onclick="changeTab(this,'all')">All</a>
                        <div class="slide"></div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-size" id="placed" onclick="changeTab(this,'today')">Today</a>
                        <div class="slide"></div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-size" id="received" onclick="changeTab(this,'clear')">Clear</a>
                        <div class="slide"></div>
                    </li>
                </ul>
                <!--
       <div class="row">
        <div class="col-md-12" >
        
        
         <div class="label-main">
          <label class="label label-lg bg-default col-md-1" onClick="setDate('today')">Today</label>
         </div>

         <div class="label-main  ">
          <label class="label label-lg bg-primary col-md-1" onClick="setDate('yesterday')">Yesterday</label>
         </div>
      
         <div class="label-main ">
          <label class="label label-lg bg-success col-md-1" onClick="setDate('this week')">This Week</label>
         </div>

         <div class="label-main ">
          <label class="label label-lg bg-warning col-md-1" onClick="setDate('last week')">Last Week</label>
         </div>

         <div class="label-main ">
          <label class="label label-lg bg-danger col-md-1" onClick="setDate('this month')">This Month</label>
         </div>
         
         <div class="label-main ">
          <label class="label label-lg bg-indigo col-md-1" onClick="setDate('last month')">Last Month</label>
         </div>
        </div>
        
       </div>-->
                <div class="row m-t-15">
                    <div class="col-lg-12 col-md-12 ">

                        <div class="col-lg-3">
                            <div class="form-group {{ $errors->has('product') ? 'has-danger' : '' }}">
                                <select id="vendor" class="select2">
                                    <option value="">Select Vendor</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->vendor_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 fromDate">
                            <div class="input-group">
                                <input type="text" class="form-control float-right" autocomplete="off" id="from_date"
                                    placeholder="From Date" value="">
                            </div>
                        </div>

                        <div class="col-lg-3 fromDate">
                            <div class="input-group">
                                <input type="text" class="form-control float-right" autocomplete="off" id="to_date"
                                    placeholder="To Date" value="">
                            </div>
                        </div>

                        <div class="col-lg-3  ">
                            <button class="btn btn-success btnSubmit m-l-1 f-right" id="btnSubmit">Submit</button>
                            <button class="btn btn-info resetBtn m-l-1 f-right" id="btnReset">Reset</button>
                        </div>

                    </div>
                </div>
                <br />
                <div class="clearfix"></div>


                <div id="tablesData"></div>



                <div class="modal fade modal-flex" id="duedate-modal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title">Change Due Date</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-control-label">Due Date:</label>
                                            <input type="text" name="duedate" id="duedate" class="form-control" />
                                            <input type="hidden" name="purchaseId" id="purchaseId" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success waves-effect waves-light"
                                    onClick="updateDueDate()">Change Date</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade modal-flex" id="vendor-payment-history-modal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title">Vendor Payment History</h4>
                            </div>
                            <div class="modal-body">
                                <div id="historyTable"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success waves-effect waves-light"
                                    onClick="hideModal()">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        </div>
        </div>
        </div>
    </section>
@endsection

@section('scriptcode_three')
    <script type="text/javascript">
        var from = to = "";
        let modes = "all";
        $("#filterText").css("display", "none");
        $(".select2").select2();
        $('#from_date,#to_date,#duedate').bootstrapMaterialDatePicker({
            format: 'YYYY-MM-DD',
            time: false,
            clearButton: true,

            icons: {
                date: "icofont icofont-ui-calendar",
                up: "icofont icofont-rounded-up",
                down: "icofont icofont-rounded-down",
                next: "icofont icofont-rounded-right",
                previous: "icofont icofont-rounded-left"
            }
        });

        $("#btnSubmit").click(function() {
            getData();
            if (from != "" && to != "") {
                text = "From : " + from + " To : " + to;
                $("#filterText").css("display", "block");
                $("#filterText").html(text);
            }
        });

        $(document).ready(function() {

            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                getData(page);
            });



            $("#fetch").click(function() {
                getData(1);
            });

        });

        getData(1);

        function getData(page) {
            $.ajax({
                url: "{{ url('get-vendor-payments') }}" + "?page=" + page,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}",
                    from: from,
                    to: $('#to_date').val(),
                    vendor: $('#vendor').val(),
                    mode: modes
                },
                success: function(resp) {
                    $("#tablesData").empty();
                    $("#tablesData").append(resp);

                }
            });
        }

        function editDueDate(id, DueDate) {
            $("#duedate").val(DueDate);
            $("#purchaseId").val(id);
            $("#duedate-modal").modal("show");
        }

        function updateDueDate() {
            $.ajax({
                url: "{{ url('update-vendor-payment-due-date') }}",
                type: 'POST',
                dataType: "json",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: $('#purchaseId').val(),
                    date: $('#duedate').val()
                },
                success: function(resp) {
                    if (resp.status == 200) {
                        $("#duedate-modal").modal("hide");
                        $("#duedate").val("");
                        $("#purchaseId").val("");
                        getData();
                    }
                }
            });
        }

        function setDate(value) {
            from = value;
            $("#filterText").css("display", "block");
            $("#filterText").html(value);
            getData();
        }
        $('#from_date').change(function() {
            from = $('#from_date').val();
        });
        $('#to_date').change(function() {
            to = $('#to_date').val();
        });

        $("#btnReset").click(function() {
            $('#from_date').val("");
            $('#to_date').val("");
            $('#vendor').val("").change();
            from = to = "";
            $("#filterText").html("");
            $("#filterText").css("display", "none");
        })


        function changeTab(arg, mode) {
            modes = mode;
            getData();
            /*
            	CODE FOR CHANGING TABS STARTS
            */
            $(".nav-link").removeClass('active');
            $(arg).addClass('active');
            $('input[type=search]').val('');
            $('.drp-selected').text('');
            if ($('#type').val() == 'today') {
                $('.mainDiv').addClass('push-md-7');
                $('.mainDiv').removeClass('push-md-3');
                $('.fromDate').hide();

            } else {
                $('.mainDiv').addClass('push-md-3');
                $('.mainDiv').removeClass('push-md-7');
                $('.fromDate').show();
            }

            /*
            	CODE FOR CHANGING TABS ENDS
            */
        }

        function viewPaymentHistory(id) {
            $("#vendor-payment-history-modal").modal("show");
            $.ajax({
                url: "{{ url('vendor-payment-history') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(resp) {
                    $("#historyTable").empty();
                    $("#historyTable").append(resp);
                }
            });
        }

        function hideModal() {
            $("#vendor-payment-history-modal").modal("hide");
        }
    </script>
@endsection
