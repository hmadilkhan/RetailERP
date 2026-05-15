@extends('layouts.master-layout')

@section('title', 'Branches')
@section('breadcrumtitle', 'Branches')
@section('navbranchoperation', 'active')
@section('navbranch', 'active')

@section('scriptcode_one')
<style>
    .branch-page {
        margin-top: 20px;
    }
    .branch-page .card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }
    .branch-page .card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e9ecef;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .branch-page .card-header-text {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #1a202c;
    }
    .branch-page .search-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-left: auto;
    }
    .branch-page .search-input {
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
    .branch-page .search-input:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 3px rgba(40,167,69,.12);
    }
    .branch-page .table-responsive {
        overflow-x: auto;
    }
    .branch-page table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .branch-page table thead th {
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
    .branch-page table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background .15s;
    }
    .branch-page table tbody tr:hover {
        background: #f8fafc;
    }
    .branch-page table tbody td {
        padding: 10px 14px;
        vertical-align: middle;
        color: #374151;
    }
    .branch-page .img-circle {
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e2e8f0;
    }
    .branch-page .badge-terminal {
        display: inline-block;
        background: #e0f2fe;
        color: #0369a1;
        border-radius: 9999px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
        margin: 1px;
    }
    .branch-page .action-icon a,
    .branch-page .action-icon i {
        transition: transform .15s, color .15s;
        cursor: pointer;
    }
    .branch-page .action-icon a:hover,
    .branch-page .action-icon i:hover {
        transform: translateY(-1px);
    }
    .branch-page .pagination-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        border-top: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 8px;
    }
    .branch-page .pagination-info {
        font-size: 13px;
        color: #6b7280;
    }
    .branch-page .pagination {
        margin: 0;
    }
    .branch-page .pagination .page-link {
        border-radius: 6px !important;
        margin: 0 2px;
        font-size: 13px;
        padding: 5px 10px;
        color: #374151;
        border-color: #e2e8f0;
    }
    .branch-page .pagination .page-item.active .page-link {
        background: #28a745;
        border-color: #28a745;
        color: #fff;
    }
    .branch-page .pagination .page-link:hover {
        background: #f1f5f9;
        color: #28a745;
    }
    .branch-page .empty-state {
        text-align: center;
        padding: 48px 20px;
        color: #9ca3af;
    }
    .branch-page .empty-state i {
        font-size: 40px;
        margin-bottom: 10px;
        display: block;
    }
</style>
@endsection

@section('content')
<div class="branch-page">
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">
                    <i class="icofont icofont-building-alt m-r-5 text-success"></i> Branches
                </h5>
                <div class="search-wrap">
                    <form method="GET" action="{{ url('/branches') }}" id="searchForm">
                        <input
                            type="text"
                            name="search"
                            class="search-input"
                            placeholder="Search branches..."
                            value="{{ $search }}"
                            autocomplete="off"
                            id="branchSearch"
                        >
                    </form>
                    <a href="{{ url('/createbranch') }}" class="btn btn-success btn-sm waves-effect waves-light">
                        <i class="icofont icofont-plus m-r-5"></i> Create Branch
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Code</th>
                            <th>Branch Name</th>
                            <th>City</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Terminals</th>
                            <th>Serials</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($details as $value)
                            <tr>
                                <td>
                                    <img width="38" height="38"
                                        src="{{ asset('storage/images/branch/' . (!empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg')) }}"
                                        class="img-circle"
                                        alt="logo">
                                </td>
                                <td>{{ $value->code ?? '-' }}</td>
                                <td><strong>{{ $value->branch_name }}</strong></td>
                                <td>{{ $value->city->city_name ?? '-' }}</td>
                                <td>{{ $value->branch_mobile ?? '-' }}</td>
                                <td>{{ $value->branch_email ?? '-' }}</td>
                                <td>
                                    @foreach ($value->terminals->pluck('terminal_id')->filter() as $tid)
                                        <span class="badge-terminal">{{ $tid }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($value->terminals->pluck('serial_no')->filter() as $sno)
                                        <span class="badge-terminal">{{ $sno }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $value->branch_address ?? '-' }}</td>
                                <td class="action-icon" style="white-space:nowrap;">
                                    <a href="{{ url('/branch-emails') }}/{{ Crypt::encrypt($value->branch_id) }}"
                                        class="p-r-10 f-18 text-info" data-toggle="tooltip" title="Emails">
                                        <i class="icofont icofont-email"></i>
                                    </a>
                                    <a href="{{ url('/branch-edit') }}/{{ Crypt::encrypt($value->branch_id) }}"
                                        class="p-r-10 f-18 text-warning" data-toggle="tooltip" title="Edit">
                                        <i class="icofont icofont-ui-edit"></i>
                                    </a>
                                    <i class="icofont icofont-ui-delete text-danger f-18 p-r-10"
                                        onclick="deleteBranch('{{ $value->branch_id }}')"
                                        data-toggle="tooltip" title="Delete"></i>
                                    <a href="{{ url('/terminals') }}/{{ Crypt::encrypt($value->branch_id) }}"
                                        class="f-18 text-info" data-toggle="tooltip" title="Add Terminal">
                                        <i class="icofont icofont-plus"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <i class="icofont icofont-building-alt"></i>
                                        <p>No branches found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                <div class="pagination-info">
                    Showing {{ $details->firstItem() ?? 0 }} to {{ $details->lastItem() ?? 0 }}
                    of {{ $details->total() }} branches
                </div>
                <div>
                    {{ $details->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scriptcode_three')
<script>
    // Debounce search — 400ms delay
    let searchTimer;
    document.getElementById('branchSearch').addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            document.getElementById('searchForm').submit();
        }, 400);
    });

    function deleteBranch(id) {
        swal({
            title: "Are you sure?",
            text: "This branch will be deleted!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Delete",
            cancelButtonText: "Cancel",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "{{ url('/removebranch') }}",
                    type: 'PUT',
                    data: { _token: "{{ csrf_token() }}", id: id },
                    success: function (resp) {
                        if (resp == 1) {
                            swal({ title: "Deleted", text: "Branch deleted.", type: "success" },
                                function () { window.location = "{{ url('/branches') }}"; });
                        }
                    }
                });
            } else {
                swal("Cancelled", "Branch is safe.", "error");
            }
        });
    }
</script>
@endsection
