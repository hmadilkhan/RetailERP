@extends('layouts.master-layout')

@section('title', 'Company')

@section('breadcrumtitle', 'Company')

@section('navcompany', 'active')

@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Companies</h5>
                <a href="{{ route('company.create') }}"
                    class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i
                        class="icofont icofont-plus f-18 m-r-5"></i>Create Company
                </a>
            </div>
            <div class="card-block">

                <table id="companyTable" class="table dt-responsiv`e table-striped nowrap" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Company Name</th>
                            <th>City</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($companies)
                            @foreach ($companies as $value)
                                <tr>

                                    <td class="text-center">
                                        <img width="42" height="42"
                                            src="{{ asset('storage/images/company/' . (!empty($value->logo) ? $value->logo : 'placeholder.jpg') . '') }}"
                                            class="d-inline-block img-circle "
                                            alt="{{ !empty($value->logo) ? $value->logo : 'placeholder.jpg' }}">
                                    </td>
                                    <td>{{ $value->name }}</td>
                                    <td>{{ $value->city_name }}</td>
                                    <td>{{ $value->mobile_contact }}</td>
                                    <td>{{ $value->email }}</td>
                                    <td>{{ $value->address }}</td>
                                    <td>{{ $value->status_name }}</td>
                                    <td class="action-icon">

                                        <a href="{{ url('/company-edit') }}/{{ $value->company_id }}"
                                            class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top"
                                            title="" data-original-title="Edit"><i
                                                class="icofont icofont-ui-edit"></i></a>

                                        <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm"
                                            data-id="{{ $value->company_id }}" data-toggle="tooltip" data-placement="top"
                                            title="" data-original-title="Delete"
                                            onclick="deleteConfirm('{{ $value->company_id }}')"></i>

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
        $('.table').DataTable({
            bLengthChange: true,
            displayLength: 50,
            info: false,
            language: {
                search: '',
                searchPlaceholder: 'Search Company',
                lengthMenu: '<span></span> _MENU_'
            }
        });

        function deleteConfirm(id) {
            swal({
                    title: "Are you sure?",
                    text: "Your will not be able to recover this company again!",
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
                            url: "{{ url('delete-company') }}/" + id,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            success: function(resp) {
                                if (resp == 1) {
                                    swal({
                                        title: "Deleted",
                                        text: "Do you want to remove this company.",
                                        type: "success"
                                    }, function(isConfirm) {
                                        if (isConfirm) {
                                            window.location =
                                                "{{ route('company.index') }}";
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

        // $('#company-table').on('click', '.alert-confirm', function() {
        //     var id = $(this).data("id");
        //     swal({
        //             title: "Are you sure?",
        //             text: "Your will not be able to recover this company again!",
        //             type: "warning",
        //             showCancelButton: true,
        //             confirmButtonClass: "btn-danger",
        //             confirmButtonText: "delete it!",
        //             cancelButtonText: "cancel plx!",
        //             closeOnConfirm: false,
        //             closeOnCancel: false
        //         },
        //         function(isConfirm) {
        //             if (isConfirm) {
        //                 $.ajax({
        //                     url: "{{ url('delete-company') }}",
        //                     type: 'POST',
        //                     data: {
        //                         _token: "{{ csrf_token() }}",
        //                         id: id
        //                     },
        //                     success: function(resp) {
        //                         if (resp == 1) {
        //                             swal({
        //                                 title: "Deleted",
        //                                 text: "Do you want to remove this company.",
        //                                 type: "success"
        //                             }, function(isConfirm) {
        //                                 if (isConfirm) {
        //                                     window.location =
        //                                         "{{ route('company.index') }}";
        //                                 }
        //                             });
        //                         }
        //                     }
        //                 });

        //             } else {
        //                 swal("Cancelled", "Your vendor is safe :)", "error");
        //             }
        //         });
        // });
    </script>
@endsection
