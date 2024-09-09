@extends('layouts.master-layout')

@section('title', 'Inventory')

@section('breadcrumtitle', 'View Inventory')

@section('navbranchoperation', 'active')
@section('navstock', 'active')

@section('content')
    <style>
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
    </style>
    <section class="panels-wells">
        <div class="card-block outer">
            <div class="wrapper">
                <div id="draggablePanelList ">
                    @foreach ($branches as $value)
                        <div class="col-xl-3 col-lg-6 inner" style="cursor: pointer;"
                            onclick="branchClick('{{ $value->branch_id }}','{{ $value->branch_name }}')">
                            <div class="card">
                                <div class="card-block">
                                    <div class="media d-flex">
                                        <div class="media-left media-middle">
                                            <a>
                                                <img class="media-object img-circle"
                                                    src="{{ asset('storage/images/branch/' . (!empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg') . '') }}"
                                                    width="50" height="50">
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <span class="counter-txt f-w-600 f-20">
                                                <span class="text-primary"> {{ $value->branch_name }}</span>
                                            </span>

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
        <br>


        <div class="col-lg-12 grid-item">
            <div class="card">
                <div class="card-header">
                    <h1 class=" text-info" id="headername"></h1>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="rkmd-checkbox checkbox-rotate">
                                <label class="input-checkbox checkbox-primary">
                                    <input type="checkbox" id="chkSearch" name="chkSearch">
                                    <span class="checkbox"></span>
                                </label>
                                <div class="captions">Search in all places</div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <input type="hidden" id="searchItem" value="false" />
                    <div class="row">
                        <input type="hidden" id="branch_id" />
                        <div class="col-md-3 col-sm-4">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i> Search
                                    ItemCode</label>
                                <input class="form-control" type="text" name="code" id="code"
                                    placeholder="Enter Product ItemCode for search" />
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-4">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i> Search
                                    Product</label>
                                <input class="form-control" type="text" name="name" id="name"
                                    placeholder="Enter Product Name for search" />
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Department</label>
                                <select class="select2" id="depart">
                                    <option value="">Select Department</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-4">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i
                                        class="icofont icofont-barcode"></i>Sub-Department</label>
                                <select class="select2" id="subdepart">
                                    <option value="">Select Sub Department</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 ">

                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-12 f-right">
                            <button type="button" id="clear" data-placement="bottom"
                                class="btn btn-warning  waves-effect waves-light f-right m-r-10">Clear</button>
                            <button type="button" id="search" data-placement="bottom"
                                class="btn btn-success  waves-effect waves-light f-right m-r-10">Search</button>
                            <button type="button" onclick="excelExport()" data-placement="bottom"
                                class="btn btn-success  waves-effect waves-light f-right m-r-10"><i
                                    class="icofont icofont-file-excel"> </i>Export to Excel</button>
                        </div>
                    </div>
                </div>
                <div class="card-block">
                    <div class="project-table">
                        <table id="tblstock" class="table dt-responsive " width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Item Code</th>
                                    <th>Product Name</th>
                                    <th>Department</th>
                                    <th>Sub-Department</th>
                                    <th>Amount</th>
                                    <th>Qty.</th>
                                    <th>UOM</th>
                                    <th>Conversion Qty.</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

    </section>
@endsection

@section('scriptcode_three')

    <script type="text/javascript">
        $(".select2").select2();
        var rem_id = [];
        var page = 1;
        var count = 0;
        var branch = 0;

        $(window).scroll(function() {
            count = count + 1;
            if (count > 4) {
                page = page + 1;
                count = 0;
                if ($('#code').val() != "" || $('#name').val() != "" || $('#depart').val() != "" || $('#subdepart')
                    .val() != "") {

                    getdetails(branch, '', page, $('#code').val(), $('#name').val(), $('#depart').val(), $(
                        '#subdepart').val())
                } else {

                    getdetails(branch, '', page, $('#code').val(), $('#name').val(), $('#depart').val(), $(
                        '#subdepart').val())
                }

            }
        });

        $("#search").click(function() {
            page = 1;
            getdetails(branch, '', page, $('#code').val(), $('#name').val(), $('#depart').val(), $('#subdepart')
                .val())
        });
        $('#branch_id').val("{{ session('branch') }}");

        function branchClick(branchid, branchname) {
            page = 1;
            branch = branchid;
            $('#branch_id').val(branchid);
            getdetails(branch, branchname, page, $('#code').val(), $('#name').val(), $('#depart').val(), $('#subdepart')
                .val())
        }

        function excelExport() {
            let url = "{{ url('reports/excel-export-stock-report') }}" + "?branchid=" + $('#branch_id').val() + "&code=" +
                $('#code').val() + "&name=" + $('#name').val() + "&dept=" + $('#depart').val() + "&sdept=" + $('#subdepart')
                .val()
            window.open(url);
        }

        $("#clear").click(function() {
            $('#code').val('');
            $('#name').val('');
            $('#depart').val('').change();
            $('#subdepart').val('').change();
            $('#chkSearch').prop('checked', false);
            $("#searchItem").val(false);
            $("#tblstock tbody").empty();
            getdetails('{{ session('branch') }}', '', page, $('#code').val(), $('#name').val(), $('#depart').val(),
                $('#subdepart').val());
        })

        load_department();

        function load_department() {
            $.ajax({
                url: "{{ url('get_departments') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(resp) {

                    $('#depart').empty();
                    $("#depart").append("<option value=''>Select Department</option>");
                    $.each(resp, function(index, value) {
                        $("#depart").append(
                            "<option value=" + value.department_id + ">" + value.department_name +
                            "</option>"
                        );
                    });

                }

            });
        }

        $('#depart').change(function() {
            load_subdept($('#depart').val())
        });

        function load_subdept(id) {
            $.ajax({
                url: "{{ url('get_sub_departments') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(resp) {
                    $('#subdepart').empty();
                    $("#subdepart").append("<option value=''>Select Sub Department</option>");
                    $.each(resp, function(index, value) {
                        $("#subdepart").append(
                            "<option value=" + value.sub_department_id + ">" + value
                            .sub_depart_name + "</option>"
                        );
                    });
                }
            });
        }


        $('.alert-confirm').on('click', function() {
            var id = $(this).data("id");
            swal({
                    title: "Are you sure?",
                    text: "Your will not be able to recover this imaginary file!",
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
                            url: "{{ url('delete-invent') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            success: function(resp) {
                                if (resp == 1) {
                                    swal({
                                        title: "Deleted",
                                        text: "Do you want to remove vendor.",
                                        type: "success"
                                    }, function(isConfirm) {
                                        if (isConfirm) {
                                            window.location =
                                                "{{ url('inventory-list') }}";
                                        }
                                    });
                                }
                            }

                        });

                    } else {
                        swal("Cancelled", "Your vendor is safe :)", "error");
                    }
                });
        });

        $(".mainchk").on('click', function() {

            if ($(this).is(":checked")) {
                $("#btn_removeall").removeClass('invisible');

                $(".chkbx").each(function(index) {
                    $(this).attr("checked", true);
                });

            } else {
                $("#btn_removeall").addClass('invisible');
                $(".chkbx").each(function(index) {
                    $(this).attr("checked", false);
                });
            }

        });

        $(".chkbx").on('click', function() {
            if ($(this).is(":checked")) {
                $("#btn_removeall").removeClass('invisible');

            } else {
                $("#btn_removeall").addClass('invisible');
            }

        });

        $("#chkSearch").on('click', function() {
            if ($(this).is(":checked")) {
                $("#searchItem").val(true);
                $("#showBranchHeader").css("display", "block");
            } else {
                $("#searchItem").val(false);
                $("#showBranchHeader").css("display", "none");
            }
        });


        getdetails('{{ session('branch') }}', '', page, $('#code').val(), $('#name').val(), $('#depart').val(), $(
            '#subdepart').val())

        function getdetails(branchid, branchname, page, code, name, dept, sdept) {

            branch = branchid;
            if (page == 1) {
                $("#tblstock tbody").empty();
            }
            $.ajax({
                url: "{{ url('/branchwise-stock') }}" + "?page=" + page,
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}",
                    branchid: branchid,
                    code: code,
                    name: name,
                    dept: dept,
                    sdept: sdept,
                    search: $("#searchItem").val()

                },
                dataType: 'json',
                success: function(result) {
                    $("#headername").html(branchname);
                    var imageUrl = "{{ asset('storage/images/placeholder.jpg') }}";

                    for (var count = 0; count < result.data.length; count++) {
                        if ($.inArray({{ session('company_id') }}, [95, 102, 104]) != -1 || $.inArray(
                                GlobalUsername, [
                                    'demoadmin', 'fnkhan'
                                ]) != -1) {

                            if (result.data[count].url != null) {
                                imageUrl = result.data[count].url;
                            }
                        } else {
                            if (result.data[count].image != '') {
                                imageUrl = "storage/images/products/" + result.data[count].image;
                            }
                        }
                       
                        $("#tblstock tbody").append(
                            "<tr>" +
                            "<td class='pro-name' > <img src='" + imageUrl +
                            "' class='img-fluid d-inline-block'></td>" +
                            "<td>" + result.data[count].item_code + "</td>" +
                            "<td><h6>" + result.data[count].product_name +
                            "</h6><span class='text-success f-16'>" + result.data[count].branch_name +
                            "</span></td>" +

                            "<td>" + result.data[count].department_name + " </td>" +
                            "<td>" + result.data[count].sub_depart_name + " </td>" +
                            "<td>" + result.data[count].amount + "</td>" +
                            "<td>" + (result.data[count].qty * 1).toFixed(2) + "</td>" +
                            "<td>" + result.data[count].name + "</td>" +
                            "<td>" + (result.data[count].qty * result.data[count].weight_qty).toFixed(2) +
                            " " + (result.data[count].cname == null ? "" : result.data[count].cname) +
                            "</td>" +

                            "<td>" +
                            "<label class=" +
                            (result.data[count].qty == 0 ? "text-danger" : (result.data[count].qty > 0 &&
                                result.data[count].qty > result.data[count].reminder_qty ?
                                'text-success' : ((result.data[count].qty <= result.data[count]
                                    .reminder_qty) ? 'text-warning' : 'text-danger'))) + ">" +
                            (result.data[count].qty == 0 ? "Out of Stock" : (result.data[count].qty > 0 &&
                                result.data[count].qty > result.data[count].reminder_qty ? 'In Stock' :
                                ((result.data[count].qty <= result.data[count].reminder_qty) ?
                                    'Low Stock' : 'Out Of Stock'))) + "</label>" +
                            "</td>" +



                            "<td class='action-icon'><a class='m-r-10' onclick='show(" + result.data[count]
                            .id +
                            ")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-eye-alt text-primary f-18' ></i></a></td>" +


                            "</tr>"
                        );
                    }
                }
            });
        }

        function show(id) {

            window.location = "{{ url('/stock-details') }}" + "/" + id;

        }
    </script>

@endsection
