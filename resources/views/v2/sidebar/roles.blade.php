@extends('layouts.master-tailwind')

@section('title', 'Roles')
@section('page_title', 'Role Permissions')
@section('page_subtitle', 'Assign sidebar pages to roles and review current access coverage.')

@section('content')
    @php
        $roleCollection = collect($roles ?? []);
        $roleDetailsCollection = collect($roledetails ?? []);
        $pageCollection = collect($pages ?? []);
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Configured Roles</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($roleCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Roles with page access</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Available Roles</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format(count($getroles ?? [])) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Role master records</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Available Pages</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($pageCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Assignable sidebar entries</p>
            </div>
            <a href="{{ url('/pages') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Shortcut</div>
                    <div class="mt-4 text-xl font-black">Manage Pages</div>
                    <p class="mt-2 text-sm text-white/75">Create sidebar entries</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Assign Page To Role</h2>
                <p class="mt-1 text-sm text-erp-mute">Selecting a child page also grants its parent chain, matching the existing logic.</p>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Role</span>
                    <select id="role" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="0">Select Role</option>
                        @foreach($getroles as $value)
                            <option value="{{ $value->role_id }}">{{ $value->role }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Page</span>
                    <select id="pages" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="0">Select Page</option>
                        @foreach($pages as $value)
                            <option value="{{ $value->id }}">{{ $value->page_name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="flex items-center justify-between border-t border-erp-line px-5 py-4">
                <div id="roleStatus" class="text-sm font-semibold text-erp-mute"></div>
                <button type="button" onclick="storeRolePermission()" class="rounded-lg bg-erp px-5 py-2.5 text-sm font-bold text-white transition hover:bg-erp-dark">Submit</button>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Roles Detail</h2>
                    <p class="mt-1 text-sm text-erp-mute">Click manage to remove individual page permissions.</p>
                </div>
                <input type="search" id="roleFilter" class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp lg:w-80" placeholder="Filter roles...">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Role</th>
                            <th class="px-5 py-3 text-left font-bold">Pages</th>
                            <th class="px-5 py-3 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody id="rolesTableBody" class="divide-y divide-slate-100">
                        @forelse($roles as $role)
                            @php($assignedPages = $roleDetailsCollection->where('role_id', $role->role_id)->pluck('page_name')->filter()->values())
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-bold text-erp-ink">{{ $role->role }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex max-w-4xl flex-wrap gap-1.5">
                                        @forelse($assignedPages as $pageName)
                                            <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-bold text-slate-700 ring-1 ring-slate-200">{{ $pageName }}</span>
                                        @empty
                                            <span class="text-erp-mute">No pages assigned</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button type="button" data-role-id="{{ $role->role_id }}" data-role-name="{{ e($role->role) }}" class="manage-role rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Manage</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center text-erp-mute">No role permissions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div id="rolePagesModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-2xl rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <div>
                    <h3 class="text-base font-bold text-erp-ink">Assigned Pages</h3>
                    <p id="roleModalTitle" class="mt-1 text-sm text-erp-mute"></p>
                </div>
                <button type="button" onclick="closeRolePagesModal()" class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute hover:bg-slate-100">x</button>
            </div>
            <div class="max-h-[60vh] overflow-y-auto p-5">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold">Page Name</th>
                            <th class="px-4 py-3 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody id="rolePagesBody" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('roleFilter')?.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#rolesTableBody tr').forEach(function (row) {
                row.hidden = !row.textContent.toLowerCase().includes(term);
            });
        });

        document.querySelectorAll('.manage-role').forEach(function (button) {
            button.addEventListener('click', function () {
                openRolePages(this.dataset.roleId, this.dataset.roleName || '');
            });
        });

        function setRoleStatus(message, success = true) {
            const status = document.getElementById('roleStatus');
            status.textContent = message;
            status.className = 'text-sm font-semibold ' + (success ? 'text-emerald-700' : 'text-rose-700');
        }

        function storeRolePermission() {
            const role = document.getElementById('role').value;
            const pages = document.getElementById('pages').value;

            if (role === '0') {
                setRoleStatus('Please select role first.', false);
                return;
            }

            if (pages === '0') {
                setRoleStatus('Please select page first.', false);
                return;
            }

            fetch("{{ url('/insert-role') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({ role: role, pages: pages, mode: 0 })
            }).then(response => response.text()).then(function (response) {
                if (response.trim() === '1') {
                    setRoleStatus('Successfully inserted. Refreshing...');
                    window.setTimeout(() => window.location = "{{ url('/roles') }}", 450);
                } else {
                    setRoleStatus('Already exists or unable to insert.', false);
                }
            }).catch(function () {
                setRoleStatus('Unable to save permission.', false);
            });
        }

        function openRolePages(roleId, roleName) {
            document.getElementById('roleModalTitle').textContent = roleName;
            const body = document.getElementById('rolePagesBody');
            body.innerHTML = '<tr><td colspan="2" class="px-4 py-6 text-center text-erp-mute">Loading...</td></tr>';

            fetch("{{ url('/getbyroleid') }}?roleid=" + roleId)
                .then(response => response.json())
                .then(function (result) {
                    body.innerHTML = '';
                    if (!result.length) {
                        body.innerHTML = '<tr><td colspan="2" class="px-4 py-6 text-center text-erp-mute">No pages found.</td></tr>';
                        return;
                    }

                    result.forEach(function (item) {
                        const row = document.createElement('tr');
                        row.innerHTML = '<td class="px-4 py-3 font-semibold text-erp-ink"></td><td class="px-4 py-3 text-right"><button type="button" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button></td>';
                        row.querySelector('td').textContent = item.page_name;
                        row.querySelector('button').addEventListener('click', function () {
                            deleteRolePage(item.id);
                        });
                        body.appendChild(row);
                    });
                });

            document.getElementById('rolePagesModal').classList.remove('hidden');
            document.getElementById('rolePagesModal').classList.add('flex');
        }

        function closeRolePagesModal() {
            document.getElementById('rolePagesModal').classList.add('hidden');
            document.getElementById('rolePagesModal').classList.remove('flex');
        }

        function deleteRolePage(id) {
            if (!confirm('Delete this page permission?')) {
                return;
            }

            fetch("{{ url('/deletepagesetting') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: JSON.stringify({ id: id })
            }).then(response => response.text()).then(function (response) {
                if (response.trim() === '1') {
                    window.location = "{{ url('/roles') }}";
                } else {
                    alert('Unable to delete permission.');
                }
            }).catch(function () {
                alert('Unable to delete permission.');
            });
        }
    </script>
@endpush
