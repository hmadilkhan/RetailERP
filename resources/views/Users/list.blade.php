@extends('layouts.master-layout')

@section('title', 'Users-Details')

@section('breadcrumtitle', 'View Vendor')

@section('navbranchoperation', 'active')
@section('navuser', 'active')

@section('content')
    
    <section class="panels-wells">
        <div class="card mt-3">
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
                                <th class="login-status all">Login Status</th>

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
                                    <td>{{ $value->role_name }}</td>
                                    <td>{{ $value->branch_name }}</td>
                                    <td>
                                        <?php $statusLower = strtolower($value->status_name); ?>
                                        <span class="status-badge {{ in_array($statusLower, ['active','enabled','online']) ? 'status-active' : 'status-inactive' }}">
                                            {{ $value->status_name }}
                                        </span>
                                    </td>
                                    <td class="login-status text-center">
                                        <div class="checkbox m-r-5">
                                            <label>
                                                <input id="changeCheckbox{{ $value->id }}"
                                                    data-user-id="{{ $value->authorization_id }}"
                                                    type="checkbox" class="status-toggle" {{ $value->isLoggedIn == 1 ? 'checked' : '' }}
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
                                        
                                        @if(auth()->user()->canImpersonate() && $value->canBeImpersonated())
                                        <a href="{{ route('impersonate', $value->id) }}" class="btn btn-sm btn-primary">
                                            Login as {{ $value->fullname }}
                                        </a>
                                        @endif

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


@section('scriptcode_one')
 <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
 <style>
     /* Ensure toggle renders correctly in narrow mobile cells */
     td.login-status .toggle {
         min-width: 40px;
         margin-left: auto;
         margin-right: auto;
     }
     td.login-status { text-align: center; vertical-align: middle; }

     /* Card/header polish */
     .card { border: 1px solid #e9ecef; border-radius: 10px; overflow: hidden; }
     .card-header { background: linear-gradient(90deg, #198754, #4CAF50 ); color: #fff; }
     .card-header .btn-primary { background-color: rgba(255,255,255,0.15); border-color: transparent; }
     .card-header .btn-primary:hover { background-color: rgba(255,255,255,0.25); }

     /* Table enhancements */
     .project-table .table { border-collapse: separate; border-spacing: 0 6px; }
     .project-table .table thead th { background: #f8fafc; position: sticky; top: 0; z-index: 2; }
     .project-table .table tbody tr { background: #fff; box-shadow: 0 1px 2px rgba(16,24,40,0.04); }
     .project-table .table tbody tr:hover { box-shadow: 0 4px 10px rgba(16,24,40,0.08); transform: translateY(-1px); }
     .project-table .table td, .project-table .table th { border-top: none !important; }
     .project-table .table td { vertical-align: middle; }

     /* DataTables search/input polish */
     .dataTables_filter input[type="search"] { border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px 12px; outline: none; box-shadow: 0 1px 2px rgba(0,0,0,0.03) inset; }
     .dataTables_filter input[type="search"]:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
     .dataTables_length select { border-radius: 6px; }

     /* Action icons and buttons */
     .action-icon a, .action-icon i { transition: transform .15s ease, color .15s ease; }
     .action-icon a:hover, .action-icon i:hover { transform: translateY(-1px); }

     /* Status badge styling */
     .status-badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
     .status-active { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
     .status-inactive { background: #fef2f2; color: #7f1d1d; border: 1px solid #fecaca; }

     /* Avatar rounding */
     .img-circle { box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
 </style>
@endsection

@section('scriptcode_three')
 <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script type="text/javascript">
        var rem_id = [];

     var table =  $('.table').DataTable({
                            bLengthChange: true,
                            displayLength: 50,
                            info: false,
                            responsive: true,
                           columnDefs: [
                               { targets: 'login-status', responsivePriority: 1 },
                               { targets: -1, responsivePriority: 2 } // action column
                           ],
                            language: {
                                search: '',
                                searchPlaceholder: 'Search Users',
                                lengthMenu: '<span></span> _MENU_'

                            }

                        });

        table.on('draw', function() {
            $('.status-toggle').bootstrapToggle();
        });

        // Re-init toggles when rows are shown/hidden in responsive mode
        table.on('responsive-display responsive-resize', function() {
            $('.status-toggle:visible').each(function(){
                if (!$(this).parent().hasClass('toggle')) {
                    $(this).bootstrapToggle();
                }
            });
        });

        // Also re-init on pagination and search changes to cover all redraw paths
        table.on('page.dt search.dt', function() {
            setTimeout(function(){
                $('.status-toggle:visible').each(function(){
                    if (!$(this).parent().hasClass('toggle')) {
                        $(this).bootstrapToggle();
                    }
                });
            }, 0);
        });

        // Delegated single handler to avoid duplicate requests and work with DataTables redraws
        $(document).off('change.statusToggle').on('change.statusToggle', '.status-toggle', function() {
            var $checkbox = $(this);
            var userId = $checkbox.data('user-id');
            var value = $checkbox.is(":checked") ? 1 : 0;
            console.log(value);
            
            if ($checkbox.data('busy')) {
                return;
            }
            $checkbox.data('busy', true);

            $.ajax({
                url: "{{ url('/change-loggedin-value') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: userId,
                    value: value
                }
            }).done(function(resp) {
                // Optionally handle success UI feedback here
            }).fail(function() {
                // Revert UI on failure
                $checkbox.bootstrapToggle(value === 1 ? 'off' : 'on', true);
            }).always(function() {
                $checkbox.data('busy', false);
            });
        });



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
