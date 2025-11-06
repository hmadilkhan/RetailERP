@extends('layouts.master-layout')

@section('title', 'Expense')

@section('breadcrumtitle', 'Add Expense')
@section('navaccountsoperation', 'active')
@section('navexpenses', 'active')

@section('content')

    @if (session('roleId') == 2 or session('roleId') == 10)
        @include('Expense.addexpense')
    @endif
    <!-- Add Expense Section is placed in seperate file -->
    <section class="panels-wells">
        <div class="card">
            <div class="card-block">
                <div class="row">
                    <div class="col-lg-3 col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">Select Expense Category</label>
                            <select name="category" id="category" data-placeholder="Select Expense Category"
                                class="form-control select2">
                                <option value="">Select Expense Category</option>
                                @if ($cat)
                                    @foreach ($cat as $value)
                                        <option value="{{ $value->exp_cat_id }}">{{ $value->expense_category }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">From Date</label>
                            <input class="form-control" type="text" name="from" id="from"
                                placeholder="DD-MM-YYYY" />
                            <div class="form-control-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">To Date</label>
                            <input class="form-control" type="text" name="to" id="to"
                                placeholder="DD-MM-YYYY" />
                            <div class="form-control-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-2  col-sm-2">
                        <div class="form-group">
                            <button type="button" id="btnSubmit"
                                class="btn btn-md btn-primary waves-effect waves-light m-t-25" onclick="getdata()">
                                <i class="icofont icofont-search"></i>&nbsp;Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="">
            <div class="" style="background-color: #E5E5E5;">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="card dashboard-product">
                            <span>Total Expenses</span>
                            <h2 class="dashboard-total-products" id="totalexpenses"></h2>
                            <span class="label label-warning">Expenses</span>
                            <div class="side-box">
                                <i class="ti-package text-warning-color"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card dashboard-product">
                            <span>Total Amount</span>
                            <h2 class="dashboard-total-products">{{ session('currency') }} <span id="totalamount"></span>
                            </h2>
                            <span class="label label-success">Amount</span>
                            <div class="side-box">
                                <i class="ti-direction-alt text-success-color"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="panels-wells ">
        <div class="card" style="margin-top:-10px;">
            <div class="card-header">
                <h5 class="card-header-text">Expense Lists</h5>
            </div>
            <div class="card-block">
                <table id="expensetb" class="table dt-responsive table-striped nowrap" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Amount</th>
                            @if (session('company_id') == 7)
                                <th>Platform Type</th>
                            @endif
                            <th>Narration</th>
                            @if (session('roleId') == 2 or session('roleId') == 1 or session('roleId') == 10)
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if ($expense)
                            @foreach ($expense as $value)
                                <tr>
                                    <td>{{ $value->date }}</td>
                                    <td>{{ $value->expense_category }}</td>
                                    <td>{{ number_format($value->net_amount, 2) }}</td>
                                    @if (session('company_id') == 7)
                                        <td>{{ $value->platform_type == 1 ? "WEB" : "APP" }}</td>
                                    @endif
                                    <td>{{ $value->expense_details }}</td>
                                    @if (session('roleId') == 2 or session('roleId') == 1)
                                        <td class="action-icon">
                                            <i onclick="deleteExpense('{{ $value->exp_id }}')"
                                                class="text-danger text-center icofont icofont-ui-delete"
                                                data-toggle="tooltip" data-placement="top" title=""
                                                data-original-title="Delete"></i>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="modal fade modal-flex" id="expense-cat-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <!--  <form id="expcatform" method="POST" class="form-horizontal" action="">
                  @csrf -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add Expense Category</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Category Name:</label>
                                <input type="text" name="category" id="category" class="form-control" />
                                <span id="category_alert" class="text-danger help-block"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light"
                        onClick="addExpCat()">Add Expense Category</button>
                </div>
                <!--  </form> -->
            </div>
        </div>
    </div>

@endsection

@section('scriptcode_three')


    <script type="text/javascript">
        $(".select2").select2();
        $('#expensetb').DataTable({
            displayLength: 50,
            info: false,
            sorting: false,
            language: {
                search: '',
                searchPlaceholder: 'Search Expense',
                lengthMenu: '<span></span> _MENU_'
            },
        });

        $('#from, #to').bootstrapMaterialDatePicker({
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
        $('#expensedate').bootstrapMaterialDatePicker({
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
        getdata()

        function getdata() {
            loader();
            $.ajax({
                url: "{{ url('/expense-details-filter') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    category: $('#category').val(),
                    first: $('#from').val(),
                    second: $('#to').val(),
                },
                success: function(result) {
                    if (result) {
                        var balance = 0;

                        $("#expensetb tbody").empty();
                        for (var count = 0; count < result.length; count++) {
                            balance += result[count].net_amount;
                            if (parseInt(result[count].balance) > 0) {
                                balance += parseInt(result[count].balance);
                            }
                            $("#expensetb tbody").append(
                                "<tr>" +
                                "<td>" + result[count].date + "</td>" +
                                "<td>" + result[count].expense_category + "</td>" +
                                "<td>" + result[count].net_amount + "</td>" +
                                @if(session('company_id') == 7)
                                "<td>" + (result[count].platform_type == 1 ? 'Web' : 'Other') + "</td>" +
                                @endif
                                "<td>" + result[count].expense_details + "</td>" +
                                "<td class='action-icon'>" +
                                "<i onclick='deleteExpense(" + result[count].exp_id +
                                ")' class='text-danger text-center icofont icofont-ui-delete' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i>" +
                                "</td>" +
                                "</tr>"
                            );
                        }
                        $("#btnSubmit").prop("disabled", false);
                        $("#totalexpenses").html((count));
                        $("#totalamount").html((balance).toLocaleString());
                    }
                }
            });
        }

        function loader() {
            $("#totalexpenses").html(0);
            $("#totalamount").html(0);
            $("#expensetb tbody").empty();
            $("#btnSubmit").prop("disabled", true);
            $("#expensetb tbody").append("<tr><td colspan='5' class='text-center f-24 fw-bold'>Loading...</td></tr>");
        }

        

        $("#btn_clear").on('click', function() {
            $("#btn_save").html('<i class="icofont icofont-plus"></i>&nbsp; Save');
            $("#btn_save").attr("data-original-title", 'Save');
            $("#btn_save").attr("data-placement", 'bottom');
            $("#expenseform")[0].reset();
            $("#tax,#exp_cat").val('').change();
        });


        $('#btn_exp_cat').click(function() {
            $('#category').val('');
            $('#expense-cat-modal').modal("show");
        });

        load_categories();

        function load_categories() {
            $.ajax({
                url: "{{ url('category') }}",
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(resp) {
                    $('#exp_cat').empty();
                    $("#exp_cat").append("<option value=''>Select Expense Category</option>");
                    $.each(resp, function(index, value) {
                        $("#exp_cat").append(
                            "<option value=" + value.exp_cat_id + ">" + value.expense_category +
                            "</option>"
                        );
                    });
                }
            });
        }

        function addExpCat() {
            if ($("#category").val() == "") {
                $("#category").focus();
                $("#category_alert").html('Category name is required.');
            } else {

                $.ajax({
                    url: '{{ route('exp_category.store') }}',
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        category: $('#category').val()
                    },
                    dataType: "json",
                    success: function(r) {
                        if (r.state == 1) {
                            if (r.contrl != "") {
                                $("#" + r.contrl).focus();
                                $("#" + r.contrl + "_alert").html(r.msg);
                            }
                            swal_alert('Alert!', r.msg, 'error', false);

                        } else if (r.state == 2) {
                            var message = "Category '" + $('#category').val() + "' already exists !";
                            swal_alert('Alert!', message, 'error', false);

                        } else {
                            var message = "Category '" + $('#category').val() + "' added Successfully !";
                            $("#category_alert").html('');
                            swal_alert('Successfully!', message, 'success', false);
                            load_categories();
                            $('#exp_cat').val('').change();
                            $('#category').val('');
                            $('#expense-cat-modal').modal("hide");
                        }
                    }
                });


            }
        }

        function taxVal() {
            var str = $("#tax option:selected").text();
            var matches = str.match(/(\d+)/);
            calc = "";
            calc = (($('#amount').val() / 100) * matches[0]);
            var sum = parseFloat($('#amount').val()) + parseFloat(calc);
            $("#hidd_amt").val(calc);
            $("#net_amount").val(sum);

        }


        $('#amount').on('change', function() {
            var amount = parseFloat($('#amount').val());
            $("#net_amount").val(amount);
        });

        $("#btn_save").on('click', function() {

            if ($("#exp_cat").val() == "") {
                $("#exp_cat").focus();
            } else if ($("#details").val() == "") {
                $("#details").focus();
            } else if ($("#amount").val() <= 0) {
                $("#amount").focus();
                $("#amount").val('');
                swal_alert('Alert!', 'Incorrect Amount ', 'error', false);
            } else {

                if ($("#hidd_id").val() == "0") {

                    $.ajax({
                        url: "{{ route('expense.store') }}",
                        type: "POST",
                        data: $('#expenseform').serialize(),
                        dataType: "json",
                        success: function(r) {
                            if (r.state == 1) {
                                swal_alert('Success!', r.msg, 'success', true);
                            } else {
                                swal_alert('Alert!', r.msg, 'error', false);
                            }
                        }
                    });

                } else {

                    $.ajax({
                        url: '{{ route('updatexp') }}',
                        type: "POST",
                        data: $('#expenseform').serialize(),
                        dataType: "json",
                        success: function(r) {

                            if (r.state == 1) {
                                swal_alert('Success!', r.msg, 'success', true);
                            } else {
                                swal_alert('Alert!', r.msg, 'error', false);
                            }
                        }
                    });

                }
            }

        });

        function updateCall(id) {
            $("#title-hcard").html('Update Expense');
            $("#btn_save").attr("data-original-title", 'Update');
            $("#btn_save").attr("data-placement", 'bottom');
            $("#btn_save").html('<i class="icofont icofont-ui-edit"></i>&nbsp; Update');
            $("#btn_cancel").removeClass('hidden');

            $.ajax({
                url: '{{ url('/getData') }}',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                type: "POST",
                success: function(result) {
                    $('#exp_cat').val(result[0].exp_cat_id).change();
                    $('#amount').val(result[0].amount);
                    $('#hidd_amt').val(result[0].amount);
                    $('#details').val(result[0].expense_details);
                    $('#net_amount').val(result[0].net_amount);
                    $('#hidd_id').val(result[0].exp_id);
                    $('#expensedate').val(result[0].date);
                    if (result[0].tax_id != "") {
                        $('#tax').val(result[0].tax_id).change();
                    }
                }



            });

        }

        function swal_alert(title, msg, type, mode) {

            swal({
                title: title,
                text: msg,
                type: type
            }, function(isConfirm) {
                if (isConfirm) {
                    if (mode == true) {
                        window.location = "{{ route('expense.index') }}";
                    }
                }
            });
        }

        function generate_voucher(expid) {
            window.location = "{{ url('expense_voucher') }}?expid=" + expid;
        }

        function deleteExpense(id) {
            swal({
                    title: "Are you sure?",
                    text: "Do you want to delete this expense!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "delete it!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: "{{ route('delete.expense') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            success: function(resp) {
                                if (resp.status == 200) {
                                    swal({
                                        title: "Deleted",
                                        text: "Expense Deleted",
                                        type: "success"
                                    }, function(isConfirm) {
                                        if (isConfirm) {
                                            window.location = "{{ url('/expense') }}";
                                        }
                                    });
                                }
                            }

                        });

                    } else {
                        swal("Cancelled", "Your record is safe :)", "error");
                    }
                });
        }
    </script>

@endsection
