@extends('layouts.master-layout')

@section('title', 'Users')
@section('breadcrumtitle', 'Users')
@section('navbranchoperation', 'active')
@section('navuser', 'active')

@section('scriptcode_one')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<style>
    .users-page .card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }
    .users-page .card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e9ecef;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .users-page .card-header-text {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #1a202c;
    }
    .users-page .header-right {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-left: auto;
        flex-wrap: wrap;
    }
    .users-page .search-input {
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
    .users-page .search-input:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 3px rgba(40,167,69,.12);
    }
    .users-page table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .users-page table thead th {
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
    .users-page table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background .15s;
    }
    .users-page table tbody tr:hover { background: #f8fafc; }
    .users-page table tbody td {
        padding: 10px 14px;
        vertical-align: middle;
        color: #374151;
    }
    .users-page .img-circle {
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e2e8f0;
    }
    .users-page .status-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 600;
    }
    .users-page .status-active { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
    .users-page .status-inactive { background:#fef2f2; color:#7f1d1d; border:1px solid #fecaca; }
    .users-page .action-icon a, .users-page .action-icon i {
        transition: transform .15s, color .15s;
        cursor: pointer;
    }
    .users-page .action-icon a:hover, .users-page .action-icon i:hover { transform: translateY(-1px); }
    .users-page td.login-status { text-align: center; vertical-align: middle; }
    .users-page .pagination-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        border-top: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 8px;
    }
    .users-page .pagination-info { font-size: 13px; color: #6b7280; }
    .users-page .pagination { margin: 0; }
    .users-page .pagination .page-link {
        border-radius: 6px !important;
        margin: 0 2px;
        font-size: 13px;
        padding: 5px 10px;
        color: #374151;
        border-color: #e2e8f0;
    }
    .users-page .pagination .page-item.active .page-link { background:#28a745; border-color:#28a745; color:#fff; }
    .users-page .pagination .page-link:hover { background:#f1f5f9; color:#28a745; }
    .users-page .empty-state { text-align:center; padding:48px 20px; color:#9ca3af; }
    .users-page .empty-state i { font-size:40px; margin-bottom:10px; display:block; }
</style>
@endsection

@section('content')
<div class="users-page">
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">
                    <i class="icofont icofont-users m-r-5 text-success"></i> Users List
                </h5>
                <div class="header-right">
                    <form method="GET" action="{{ url('/usersDetails') }}" id="searchForm">
                        <input type="text" name="search" id="userSearch" class="search-input"
                            placeholder="Search by name, username, branch..." value="{{ $search }}" autocomplete="off">
                    </form>
                    <a href="{{ url('/create-user') }}" class="btn btn-success btn-sm waves-effect waves-light">
                        <i class="icofont icofont-plus m-r-5"></i> Create User
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            @if (session('roleId') == 1)
                                <th>Password</th>
                            @endif
                            <th>Role</th>
                            <th>Branch</th>
                            <th>Status</th>
                            <th class="login-status">Login Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($getusers as $value)
                            <tr>
                                <td>
                                    <img width="38" height="38"
                                        src="{{ asset('storage/images/users/' . (!empty($value->image) ? $value->image : 'placeholder.jpg')) }}"
                                        class="img-circle" alt="photo">
                                </td>
                                <td><strong>{{ $value->fullname }}</strong></td>
                                <td>{{ $value->username }}</td>
                                @if (session('roleId') == 1)
                                    <td>{{ $value->show_password }}</td>
                                @endif
                                <td>{{ $value->role_name }}</td>
                                <td>{{ $value->branch_name }}</td>
                                <td>
                                    @php $statusLower = strtolower($value->status_name); @endphp
                                    <span class="status-badge {{ in_array($statusLower, ['active','enabled','online']) ? 'status-active' : 'status-inactive' }}">
                                        {{ $value->status_name }}
                                    </span>
                                </td>
                                <td class="login-status">
                                    <input id="changeCheckbox{{ $value->id }}"
                                        data-user-id="{{ $value->authorization_id }}"
                                        type="checkbox" class="status-toggle"
                                        {{ $value->isLoggedIn == 1 ? 'checked' : '' }}
                                        data-toggle="toggle" data-size="mini" data-width="20" data-height="20">
                                </td>
                                <td class="action-icon" style="white-space:nowrap;">
                                    <a href="{{ url('/user-edit') }}/{{ Crypt::encrypt($value->id) }}"
                                        class="p-r-10 f-18 text-warning" data-toggle="tooltip" title="Edit">
                                        <i class="icofont icofont-ui-edit"></i>
                                    </a>
                                    <i onclick="deleteUser('{{ $value->authorization_id }}')"
                                        class="icofont icofont-ui-delete text-danger f-18 p-r-10"
                                        data-toggle="tooltip" title="Delete"></i>
                                    @if (auth()->user()->canImpersonate() && $value->canBeImpersonated())
                                        <a href="{{ route('impersonate', $value->id) }}"
                                            class="p-r-10 f-18 text-primary"
                                            data-toggle="tooltip" title="Login as {{ $value->fullname }}">
                                            <i class="icofont icofont-user-alt-3"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ session('roleId') == 1 ? 9 : 8 }}">
                                    <div class="empty-state">
                                        <i class="icofont icofont-users"></i>
                                        <p>No users found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                <div class="pagination-info">
                    Showing {{ $getusers->firstItem() ?? 0 }} to {{ $getusers->lastItem() ?? 0 }}
                    of {{ $getusers->total() }} users
                </div>
                <div>{{ $getusers->links('pagination::bootstrap-4') }}</div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scriptcode_three')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
    // Init toggles
    $(function () { $('.status-toggle').bootstrapToggle(); });

    // Debounce search
    let searchTimer;
    document.getElementById('userSearch').addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => document.getElementById('searchForm').submit(), 400);
    });

    // Login status toggle
    $(document).off('change.statusToggle').on('change.statusToggle', '.status-toggle', function () {
        var $cb = $(this), userId = $cb.data('user-id'), value = $cb.is(':checked') ? 1 : 0;
        if ($cb.data('busy')) return;
        $cb.data('busy', true);
        $.ajax({
            url: "{{ url('/change-loggedin-value') }}", type: 'POST',
            data: { _token: "{{ csrf_token() }}", id: userId, value: value }
        }).fail(function () {
            $cb.bootstrapToggle(value === 1 ? 'off' : 'on', true);
        }).always(function () { $cb.data('busy', false); });
    });

    function deleteUser(id) {
        swal({
            title: "Are you sure?", text: "This user will be deleted!",
            type: "warning", showCancelButton: true,
            confirmButtonClass: "btn-danger", confirmButtonText: "Delete",
            cancelButtonText: "Cancel", closeOnConfirm: false, closeOnCancel: false
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "{{ url('user-delete') }}", type: 'POST',
                    data: { _token: "{{ csrf_token() }}", id: id },
                    success: function (resp) {
                        if (resp == 1) {
                            swal({ title: "Deleted", text: "User deleted.", type: "success" },
                                function () { window.location = "{{ url('/usersDetails') }}"; });
                        }
                    }
                });
            } else { swal("Cancelled", "User is safe.", "error"); }
        });
    }
</script>
@endsection
