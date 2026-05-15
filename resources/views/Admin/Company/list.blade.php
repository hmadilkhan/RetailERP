@extends('layouts.master-layout')

@section('title', 'Company')

@section('breadcrumtitle', 'Company')

@section('navcompany', 'active')

@section('scriptcode_one')
<style>
    .company-page .card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }
    .company-page .card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e9ecef;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .company-page .card-header-text {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #1a202c;
    }
    .company-page .header-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-left: auto;
    }
    .company-page .search-input {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 8px 14px 8px 36px;
        font-size: 14px;
        width: 300px;
        height: 38px;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E") no-repeat 11px center;
    }
    .company-page .search-input:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 3px rgba(40,167,69,.12);
    }
    .company-page table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .company-page table thead th {
        background: #f1f5f9;
        color: #475569;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .4px;
        padding: 10px 14px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .company-page table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background .15s;
    }
    .company-page table tbody tr:hover {
        background: #f8fafc;
    }
    .company-page table tbody td {
        padding: 10px 14px;
        vertical-align: middle;
        color: #374151;
    }
    .company-page .img-circle {
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e2e8f0;
    }
    .company-page .status-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 600;
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .company-page .action-icon a,
    .company-page .action-icon i {
        transition: transform .15s, color .15s;
        cursor: pointer;
    }
    .company-page .action-icon a:hover,
    .company-page .action-icon i:hover {
        transform: translateY(-1px);
    }
    .company-page .pagination-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        border-top: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 8px;
    }
    .company-page .pagination-info {
        font-size: 13px;
        color: #6b7280;
    }
    .company-page .pagination {
        margin: 0;
    }
    .company-page .pagination .page-link {
        border-radius: 6px !important;
        margin: 0 2px;
        font-size: 13px;
        padding: 5px 10px;
        color: #374151;
        border-color: #e2e8f0;
    }
    .company-page .pagination .page-item.active .page-link {
        background: #28a745;
        border-color: #28a745;
        color: #fff;
    }
    .company-page .pagination .page-link:hover {
        background: #f1f5f9;
        color: #28a745;
    }
    .company-page .empty-state {
        text-align: center;
        padding: 48px 20px;
        color: #9ca3af;
    }
    .company-page .empty-state i {
        font-size: 40px;
        margin-bottom: 10px;
        display: block;
    }
</style>
@endsection

@section('content')
<div class="company-page">
    <section class="panels-wells">
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-header-text">
                    <i class="icofont icofont-building-alt m-r-5 text-success"></i> Companies
                </h5>
                <div class="header-right">
                    <form method="GET" action="{{ route('company.index') }}" id="searchForm">
                        <input
                            type="text"
                            name="search"
                            class="search-input"
                            placeholder="Search companies..."
                            value="{{ $search }}"
                            autocomplete="off"
                            id="companySearch"
                        >
                    </form>
                    <a href="{{ route('company.create') }}" class="btn btn-success btn-sm waves-effect waves-light">
                        <i class="icofont icofont-plus m-r-5"></i> Create Company
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table>
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
                        @forelse ($companies as $value)
                            <tr>
                                <td class="text-center">
                                    <img width="38" height="38"
                                        src="{{ asset('storage/images/company/' . (!empty($value->logo) ? $value->logo : 'placeholder.jpg')) }}"
                                        class="d-inline-block img-circle"
                                        alt="logo">
                                </td>
                                <td><strong>{{ $value->name }}</strong></td>
                                <td>{{ $value->city_name ?? '-' }}</td>
                                <td>{{ $value->mobile_contact ?? '-' }}</td>
                                <td>{{ $value->email ?? '-' }}</td>
                                <td>{{ $value->address ?? '-' }}</td>
                                <td><span class="status-badge">{{ $value->status_name }}</span></td>
                                <td class="action-icon" style="white-space:nowrap;">
                                    <a href="{{ url('/company-edit') }}/{{ $value->company_id }}"
                                        class="p-r-10 f-18 text-warning" data-toggle="tooltip" title="Edit">
                                        <i class="icofont icofont-ui-edit"></i>
                                    </a>
                                    <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm"
                                        data-id="{{ $value->company_id }}" data-toggle="tooltip" title="Delete"
                                        onclick="deleteConfirm('{{ $value->company_id }}')"></i>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="icofont icofont-building-alt"></i>
                                        <p>No companies found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                <div class="pagination-info">
                    Showing {{ $companies->firstItem() ?? 0 }} to {{ $companies->lastItem() ?? 0 }}
                    of {{ $companies->total() }} companies
                </div>
                <div>
                    {{ $companies->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@section('scriptcode_three')
    <script type="text/javascript">
        let searchTimer;
        document.getElementById('companySearch').addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 400);
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
