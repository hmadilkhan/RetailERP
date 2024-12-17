@extends('layouts.master-layout')

@section('title', 'Users-Details')

@section('breadcrumtitle', 'View Vendor')

@section('navbranchoperation', 'active')
@section('navuser', 'active')

@section('content')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Users List</h5>
                <a href="{{ url('/create-user') }}" data-toggle="tooltip" data-placement="bottom" title=""
                    data-original-title="Create Vendor"
                    class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i
                        class="icofont icofont-plus m-r-5"></i> CREATE USER
                </a>
                <button type="button" id="btn_removeall" class="btn btn-danger f-right m-r-10 invisible"><i
                        class="icofont icofont-ui-delete f-18 "></i>&nbsp;Remove</button>
            </div>
            <div class="card-block">

                <div class="project-table">
                    <table class="table table-striped nowrap dt-responsive" width="100%">
                        <thead>
                            <tr>
                                <th>Logo</th>
                                <th>Full name</th>
                                <th>User name</th>
                                @if (session('roleId') == 1)
                                    <th>Password</th>
                                @endif
                                <th>Role</th>
                                <th>Branch</th>
                                <th>Status</th>
                                <th>Login Status</th>

                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($getusers as $value)
                                <tr>
                                    <td class="text-center">
                                        <img width="42" height="42"
                                            src="{{ asset('storage/images/users/' . (!empty($value->image) ? $value->image : 'placeholder.jpg') . '') }}"
                                            class="d-inline-block img-circle "
                                            alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                                    </td>
                                    <td>{{ $value->fullname }}</td>
                                    <td>{{ $value->username }}</td>
                                    @if (session('roleId') == 1)
                                        <td>{{ $value->show_password }}</td>
                                    @endif
                                    <td>{{ $value->role }}</td>
                                    <td>{{ $value->branch_name }}</td>
                                    <td>{{ $value->status_name }}</td>
                                    <td>
                                        <div class="checkbox m-r-5">
                                            <label>
                                                <input id="changeCheckbox{{ $value->id }}"
                                                    onchange="changeCheckbox('changeCheckbox{{ $value->id }}','{{ $value->authorization_id }}')"
                                                    type="checkbox" {{ $value->isLoggedIn == 1 ? 'checked' : '' }}
                                                    data-toggle="toggle" data-size="mini" data-width="20" data-height="20">
                                            </label>
                                        </div>
                                    </td>
                                    <td class="action-icon">
                                        <a href="{{ url('/user-edit') }}/{{ Crypt::encrypt($value->id) }}"
                                            class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top"
                                            title="" data-original-title="Edit"><i
                                                class="icofont icofont-ui-edit"></i></a>

                                        <i onclick="deleteUser('{{ $value->authorization_id }}')"
                                            class="icofont icofont-ui-delete text-danger f-18 alert-confirm"
                                            data-id="{{ $value->authorization_id }}" data-toggle="tooltip"
                                            data-placement="top" title="" data-original-title="Delete"></i>

                                    </td>
                                </tr>
                            @endforeach



                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scriptcode_three')

    <script type="text/javascript">
        var rem_id = [];

        $('.table').DataTable({

            bLengthChange: true,
            displayLength: 50,
            info: false,
            language: {
                search: '',
                searchPlaceholder: 'Search Users',
                lengthMenu: '<span></span> _MENU_'

            }

        });


        function changeCheckbox(id, userId) {

            var value = "";
            if ($('#' + id).is(":checked")) {
                value = 1;
            } else {
                value = 0;
            }

            $.ajax({
                url: "{{ url('/change-loggedin-value') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: userId,
                    value: value
                },
                success: function(resp) {
                    console.log(resp)
                }
            });
        }



        //Alert confirm
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
                            url: "{{ url('user-delete') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            dataType: "json",
                            success: function(resp) {
                                if (resp == 1) {
                                    swal({
                                        title: "Deleted",
                                        text: "User Deleted.",
                                        type: "success"
                                    }, function(isConfirm) {
                                        if (isConfirm) {
                                            location.reload()
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


        $("#btn_removeall").on('click', function() {


            swal({
                title: "Delete",
                text: "Do you want to remove all vendor?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function(isConfirm) {
                if (isConfirm) {

                    $(".chkbx").each(function(index) {

                        if ($(this).is(":checked")) {
                            if (jQuery.inArray($(this).data('id'), rem_id) == -1) {
                                rem_id.push($(this).data('id'));

                            }
                        }

                    });

                    if (rem_id.length > 0) {

                        $.ajax({
                            url: "{{ url('/all-vendors-remove') }}",
                            type: "PUT",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: rem_id
                            },
                            success: function(resp) {

                                if (resp == 1) {
                                    swal({
                                        title: "Success!",
                                        text: "All vendor remove Successfully :)",
                                        type: "success"
                                    }, function(isConfirm) {
                                        if (isConfirm) {
                                            window.location =
                                                "{{ route('vendors.index') }}";
                                        }
                                    });

                                } else {
                                    swal("Alert!", "Vendor not removed:)", "error");
                                }

                            }

                        });
                    }

                } else {
                    swal("Cancel!", "Your all vendor is safe:)", "error");
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

        function deleteUser(id) {
            // var id= $(this).data("id");
            swal({
                    title: "Are you sure?",
                    text: "Do you want to delete this user!",
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
                            url: "{{ url('user-delete') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            success: function(resp) {
                                if (resp == 1) {
                                    swal({
                                        title: "Deleted",
                                        text: "User Deleted",
                                        type: "success"
                                    }, function(isConfirm) {
                                        if (isConfirm) {
                                            window.location = "{{ url('/usersDetails') }}";
                                        }
                                    });
                                }
                            }

                        });

                    } else {
                        swal("Cancelled", "Your vendor is safe :)", "error");
                    }
                });
        };
    </script>
@endsection
