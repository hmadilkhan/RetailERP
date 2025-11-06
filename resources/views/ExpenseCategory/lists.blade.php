@extends('layouts.master-layout')

@section('title', 'Expense')

@section('breadcrumtitle', 'Add Expense')
@section('navaccountsoperation', 'active')
@section('navexpcat', 'active')

@section('content')
    <section class="panels-wells">

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text" id="title-hcard">Create Expense Category</h5>
            </div>
            <div class="card-block">
                <!-- <form id="expcatform" method="POST" class="form-horizontal" action="">
                 @csrf -->
                <input type="hidden" id="hidd_id" name="hidd_id" value="0">
                <div class="row">
                    <!-- Expense Details -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="category" class="form-control-label">Expense Category</label>
                            <input type="text" id="category" name="category" placeholder="Expense Category"
                                class="form-control " />
                            <span id="category_alert" class="text-danger help-block"></span>
                        </div>
                    </div>
                     @if (session('company_id') == 114)
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Platform Type</label>
                            <select name="platform_type" id="platform_type" data-placeholder="Platform Type"
                                class="form-control select2">
                                <option selected value="0">All</option>
                                <option value="1">Web</option>
                                <option value="2">App</option>
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-lg-1 ">
                        <button type="button" id="btn_save" class="btn btn-success btn-circle" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Create Category"
                            style="margin-top: 30px;"> <i class="icofont icofont-plus"></i> Add Category</button>
                    </div>
                </div>
                <!--  </form> -->
            </div>
        </div>
    </section>

    <section class="panels-wells">

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Expense Category Lists</h5>
            </div>
            <div class="card-block">
                <table class="table dt-responsive table-striped nowrap" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Category</th>
                            @if (session('company_id') == 114)
                                <th>Platform Type</th>
                            @endif
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @if ($category)
                            @foreach ($category as $value)
                                <tr>
                                    <td id="{{ $value->exp_cat_id }}">{{ $value->expense_category }}</td>
                                    @if (session('company_id') == 114)
                                        <td >{{ $value->platform_type == 1 ? "WEB" : "APP" }}</td>
                                    @endif
                                    <td class="action-icon">
                                        <i onclick="edit_record('{{ $value->exp_cat_id }}')"
                                            class="text-success text-center icofont icofont-ui-edit" data-toggle="tooltip"
                                            data-placement="top" title="" data-original-title="Edit"></i>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
    </section>

@endsection


@section('scriptcode_three')

    <script type="text/javascript">
        $(document).ready(function() {

            $('.table').DataTable({
                displayLength: 50,
                info: false,
                sorting: false,
                language: {
                    search: '',
                    searchPlaceholder: 'Search Category',
                    lengthMenu: '<span></span> _MENU_'
                },
            });

            $("#btn_save").on('click', function() {

                if ($("#category").val() == "") {
                    $("#category").focus();
                    $("#category_alert").html('Category name is required.');
                } else {

                    $.ajax({
                        url: "{{ route('exp_category.store') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            category: $('#category').val(),
                            platform_type: $('#platform_type').val(),
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
                                var message = "Category '" + $('#category').val() +
                                    "' already exists !";
                                swal_alert('Alert!', message, 'error', false);

                            } else {
                                $("#category_alert").html('');
                                var message = "Category '" + $('#category').val() +
                                    "' added Successfully !";
                                swal_alert('Successfully!', message, 'success', true);
                            }
                        }
                    });


                }

            });


            var input = document.getElementById("category");

            // Execute a function when the user releases a key on the keyboard
            input.addEventListener("keyup", function(event) {
                // Number 13 is the "Enter" key on the keyboard
                if (event.keyCode === 13) {
                    // Cancel the default action, if needed
                    event.preventDefault();

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
                                    var message = "Category '" + $('#category').val() +
                                        "' already exists !";
                                    swal_alert('Alert!', message, 'error', false);

                                } else {
                                    $("#category_alert").html('');
                                    var message = "Category '" + $('#category').val() +
                                        "' added Successfully !";
                                    swal_alert('Successfully!', message, 'success', true);
                                }
                            }
                        });


                    }

                }
            });







        });


        function edit_record(id) {

            $.ajax({
                url: '{{ url('/expcate_edit') }}',
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                dataType: "json",
                success: function(r) {
                    if (r.state == 0) {
                        swal_alert('Alert!', r.msg, 'error', false);
                    } else {
                        $("#category").val(r[0].category);
                        swal({
                            title: "Edit Expense Category",
                            text: "",
                            type: "input",
                            showCancelButton: true,
                            closeOnConfirm: false,
                            inputPlaceholder: 'Category',
                            inputValue: r[0].category
                        }, function(inputValue) {
                            if (inputValue === false) return false;
                            if (inputValue === "") {
                                swal.showInputError("Category name is required.");
                                return false
                            } else {

                                $.ajax({
                                    url: '{{ url('/expcate-update') }}',
                                    type: "PUT",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        id: r[0].id,
                                        cat: inputValue
                                    },
                                    dataType: "json",
                                    success: function(r) {

                                        if (r.state == 1) {
                                            var message = "Category '" + inputValue +
                                                "' updated Successfully !";
                                            swal_alert('Success!', message, 'success',
                                                true);

                                        } else {
                                            var message = "Category '" + inputValue +
                                                "' already exists !";
                                            swal_alert('Alert!', message, 'error', false);
                                        }
                                    }
                                });

                            }

                        });
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
                    if (mode === true) {
                        window.location = "{{ route('exp_category.index') }}";
                    }
                }
            });
        }
    </script>

@endsection
