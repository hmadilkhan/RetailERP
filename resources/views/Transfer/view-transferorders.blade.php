@extends('layouts.master-layout')

@section('title', 'Transfer Order')

@section('breadcrumtitle', 'View Transfer Order')

@section('navtransfer', 'active')

@section('navcreatetrf', 'active')


@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Direct Transfer Order List</h5>
                <a href="{{ url('create-transferorder') }}"
                    class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i
                        class="icofont icofont-plus f-18 m-r-5"></i>Create Transfer Order
                </a>
            </div>
            <div class="card-block">

                <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%" cellspacing="0">

                    <thead>
                        <tr>
                            <th>Transfer Order No.</th>
                            <th>Transfer From Branch</th>
                            <th>Destination To Branch</th>
                            <th>Generation Date</th>
                            <th>Created By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gettransfer as $value)
                            <tr>
                                <td>TO-{{ $value->transfer_No }}</td>
                                <td>Head Office</td>
                                <td>{{ $value->branch_name }}</td>
                                <td>{{ $value->date }}</td>
                                <td>{{ $value->fullname }}</td>
                                <td>
                                    @if ($value->name == 'Draft')
                                        <span class="tag tag-default"> {{ $value->name }}</span>
                                    @elseif($value->name == 'Placed')
                                        <span class="tag tag-info"> {{ $value->name }}</span>
                                    @elseif($value->name == 'Approved')
                                        <span class="tag tag-info"> {{ $value->name }}</span>
                                    @elseif($value->name == 'Cancel')
                                        <span class="tag tag-danger"> {{ $value->name }}</span>
                                    @elseif($value->name == 'Delivered')
                                        <span class="tag tag-primary"> {{ $value->name }}</span>
                                    @elseif($value->status_name == 'Completed')
                                        <span class="tag tag-info"> {{ $value->status_name }}</span>
                                    @endif
                                </td>
                                <td class="action-icon">

                                    <i class="icofont icofont-eye-alt text-primary p-r-10 f-18" data-toggle="tooltip"
                                        data-placement="top" title="" data-original-title="View"
                                        onclick="view('{{ $value->transfer_id }}')"></i>

                                    <a class="{{ $value->name == 'Delivered' ? 'disabled' : '' }} m-r-10"
                                        data-toggle="tooltip" data-placement="top" title="Edit Transfer Order"
                                        data-original-title="{{ $value->name == 'Delivered' ? 'Disabled' : 'Edit' }}"><i
                                            class="icofont icofont-ui-edit text-{{ $value->name == 'Delivered' ? 'muted' : 'info' }} f-18"
                                            <?php echo $value->name == 'Delivered' ? '' : ' onclick="edit(' . $value->transfer_id . ')" '; ?>></i></a>

                                    <a class="{{ $value->name == 'Delivered' ? 'disabled' : '' }} m-r-10"
                                        data-toggle="tooltip" data-placement="top" title="Reject Transfer Order"
                                        data-original-title="{{ $value->name == 'Delivered' ? 'Disabled' : 'Delete' }}"><i
                                            class="icofont icofont-ui-delete text-{{ $value->name == 'Delivered' ? 'muted' : 'danger' }} f-18"
                                            <?php echo $value->name == 'Delivered' ? '' : ' onclick="reject(' . $value->transfer_id . ')" '; ?>></i></a>
                                    @if ($value->name == 'Delivered')
                                        <a target="_blank" href="{{ url('direct-transfer-report', $value->transfer_id) }}"
                                            class="text-danger p-r-10 f-18" data-toggle="tooltip" data-placement="top"
                                            title="" data-original-title="View"><i
                                                class="icofont icofont-printer"></i></a>
                                    @endif

                                </td>

                            </tr>
                        @endforeach



                    </tbody>


                </table>
            </div>
        </div>


    </section>


@endsection
@section('scriptcode_three')

    <script>
        $('.table').DataTable({
            displayLength: 10,
            info: false,
            language: {
                search: '',
                searchPlaceholder: 'Search Transfer Order',
                lengthMenu: '<span></span> _MENU_'

            },


        });

        function edit(id) {
            window.location = "{{ url('/edit_trf_details') }}/" + id;
        }

        function view(id) {
            // window.location= "/erp/get_trf_details/"+id;
            window.location = "{{ url('/get_trf_details') }}/" + id;
        }

        function reject(id) {
            swal({
                title: "Delete",
                text: "Do you want to Delete Transfer Order?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{ url('/trforder_delete') }}",
                        type: "GET",
                        data: {
                            _token: "{{ csrf_token() }}",
                            trfid: id,
                        },
                        success: function(id) {
                            if (id == 1) {
                                swal({
                                    title: "Success!",
                                    text: "Transfer Order Deleted Successfully :)",
                                    type: "success"
                                }, function(isConfirm) {
                                    if (isConfirm) {
                                        window.location = "{{ url('/trf_list') }}";
                                    }
                                });

                            } else {
                                swal("Alert!", "Transfer Order not Deleted:)", "error");
                            }

                        }

                    });

                } else {
                    swal("Cancel!", "Your Transfer Order is safe:)", "error");
                }
            });
        }
    </script>

@endsection
