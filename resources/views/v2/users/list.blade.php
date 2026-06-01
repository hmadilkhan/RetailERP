@extends('layouts.master-tailwind')

@section('title', 'Users')
@section('page_title', 'Users')
@section('page_subtitle', 'Manage staff accounts, roles, branches, login status, and impersonation access.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Users</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($getusers->total()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Active accounts in current scope</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Visible Rows</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($getusers->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Showing on this page</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Search</div>
                <div class="mt-4 text-xl font-black text-erp-ink">{{ $search !== '' ? $search : 'All users' }}</div>
                <p class="mt-2 text-sm text-erp-mute">Name, username, role, branch, or contact</p>
            </div>
            <a href="{{ url('/create-user') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create User</div>
                    <p class="mt-2 text-sm text-white/75">Add a staff account</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">User Directory</h2>
                    <p class="mt-1 text-sm text-erp-mute">Review users, update login status, edit accounts, or impersonate.</p>
                </div>
                <form method="GET" action="{{ url('/usersDetails') }}" id="searchForm" class="flex w-full gap-2 sm:w-auto">
                    <input type="text" name="search" id="userSearch" value="{{ $search }}" autocomplete="off"
                        placeholder="Search users..."
                        class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">User</th>
                            <th class="px-5 py-3 text-left font-bold">Username</th>
                            @if (session('roleId') == 1)
                                <th class="px-5 py-3 text-left font-bold">Password</th>
                            @endif
                            <th class="px-5 py-3 text-left font-bold">Role</th>
                            <th class="px-5 py-3 text-left font-bold">Branch</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                            <th class="px-5 py-3 text-left font-bold">Login</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($getusers as $value)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img class="h-11 w-11 rounded-lg object-cover ring-1 ring-slate-200"
                                            src="{{ asset('storage/images/users/' . (!empty($value->image) ? $value->image : 'placeholder.jpg')) }}"
                                            alt="{{ $value->fullname }}">
                                        <div class="min-w-0">
                                            <div class="truncate font-bold text-erp-ink">{{ $value->fullname }}</div>
                                            <div class="mt-1 text-xs text-erp-mute">{{ $value->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 font-semibold text-erp-text">{{ $value->username }}</td>
                                @if (session('roleId') == 1)
                                    <td class="px-5 py-4 text-erp-mute">{{ $value->show_password }}</td>
                                @endif
                                <td class="px-5 py-4">
                                    <span class="rounded-md bg-indigo-50 px-2 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-200">{{ $value->role_name }}</span>
                                </td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->branch_name }}</td>
                                <td class="px-5 py-4">
                                    @php $statusLower = strtolower($value->status_name); @endphp
                                    <span class="rounded-md px-2 py-1 text-xs font-bold ring-1 {{ in_array($statusLower, ['active', 'enabled', 'online']) ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-rose-50 text-rose-700 ring-rose-200' }}">
                                        {{ $value->status_name }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <label class="inline-flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" class="login-toggle sr-only" data-user-id="{{ $value->authorization_id }}" {{ $value->isLoggedIn == 1 ? 'checked' : '' }}>
                                        <span class="toggle-ui h-6 w-11 rounded-full bg-slate-300 p-0.5 transition">
                                            <span class="block h-5 w-5 rounded-full bg-white shadow transition"></span>
                                        </span>
                                    </label>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ url('/user-edit') }}/{{ Crypt::encrypt($value->id) }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</a>
                                        <button type="button" onclick="deleteUser('{{ $value->authorization_id }}')" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                        @if (auth()->user()->canImpersonate() && $value->canBeImpersonated())
                                            <a href="{{ route('impersonate', $value->id) }}" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">Login As</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ session('roleId') == 1 ? 8 : 7 }}" class="px-5 py-14 text-center">
                                    <div class="text-base font-bold text-erp-ink">No users found</div>
                                    <p class="mt-2 text-sm text-erp-mute">{{ $search ? 'Try a different search term.' : 'Create your first user to get started.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-erp-mute">Showing {{ $getusers->firstItem() ?? 0 }} to {{ $getusers->lastItem() ?? 0 }} of {{ $getusers->total() }} users</div>
                <div class="flex gap-2">
                    @if ($getusers->onFirstPage())
                        <span class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-300">Previous</span>
                    @else
                        <a href="{{ $getusers->previousPageUrl() }}" class="rounded-lg border border-erp-line px-3 py-2 text-sm font-semibold text-erp-text transition hover:border-erp hover:text-erp-dark">Previous</a>
                    @endif

                    @if ($getusers->hasMorePages())
                        <a href="{{ $getusers->nextPageUrl() }}" class="rounded-lg border border-erp-line px-3 py-2 text-sm font-semibold text-erp-text transition hover:border-erp hover:text-erp-dark">Next</a>
                    @else
                        <span class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-300">Next</span>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .login-toggle:checked + .toggle-ui { background: #4CAF50; }
        .login-toggle:checked + .toggle-ui span { transform: translateX(1.25rem); }
    </style>
@endpush

@push('scripts')
    <script>
        let userSearchTimer;
        document.getElementById('userSearch')?.addEventListener('input', function () {
            clearTimeout(userSearchTimer);
            userSearchTimer = setTimeout(function () {
                document.getElementById('searchForm').submit();
            }, 400);
        });

        document.querySelectorAll('.login-toggle').forEach(function (toggle) {
            toggle.addEventListener('change', function () {
                fetch("{{ url('/change-loggedin-value') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        id: this.dataset.userId,
                        value: this.checked ? 1 : 0
                    })
                }).catch(() => {
                    this.checked = !this.checked;
                    alert('Unable to update login status.');
                });
            });
        });

        function deleteUser(id) {
            if (!confirm('Delete this user?')) {
                return;
            }

            fetch("{{ url('/user-delete') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: JSON.stringify({ id: id })
            })
                .then(response => response.text())
                .then(function (response) {
                    if (response.trim() === '1') {
                        window.location = "{{ url('/usersDetails') }}";
                    } else {
                        alert('Unable to delete user.');
                    }
                })
                .catch(function () {
                    alert('Unable to delete user.');
                });
        }
    </script>
@endpush
