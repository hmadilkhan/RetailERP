@extends('layouts.master-tailwind')

@section('title', 'Modules Permissions')
@section('page_title', 'Company Module Permissions')
@section('page_subtitle', 'Assign available ERP modules to companies and manage package-level access.')

@section('content')
    @php
        $companiesCollection = collect($companies ?? []);
        $moduleDetailsCollection = collect($modulesdetails ?? []);
        $pageCollection = collect($pages ?? []);
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Configured Companies</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($companiesCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Companies with module access</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">All Companies</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format(count($company ?? [])) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available company records</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Modules</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($pageCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Assignable sidebar modules</p>
            </div>
            <a href="{{ url('/company') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Shortcut</div>
                    <div class="mt-4 text-xl font-black">Companies</div>
                    <p class="mt-2 text-sm text-white/75">Manage company records</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Assign Module To Company</h2>
                <p class="mt-1 text-sm text-erp-mute">Selecting a child module also grants its parent chain, matching the existing permission logic.</p>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Company</span>
                    <select id="company" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Company</option>
                        @foreach($company as $value)
                            <option value="{{ $value->company_id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Module</span>
                    <select id="pages" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="0">Select Module</option>
                        @foreach($pages as $value)
                            <option value="{{ $value->id }}">{{ $value->page_name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="flex items-center justify-between border-t border-erp-line px-5 py-4">
                <div id="moduleStatus" class="text-sm font-semibold text-erp-mute"></div>
                <button type="button" onclick="storeModulePermission()" class="rounded-lg bg-erp px-5 py-2.5 text-sm font-bold text-white transition hover:bg-erp-dark">Submit</button>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Company Modules Detail</h2>
                    <p class="mt-1 text-sm text-erp-mute">Click manage to remove individual modules from a company.</p>
                </div>
                <input type="search" id="companyModuleFilter" class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp lg:w-80" placeholder="Filter companies...">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Company</th>
                            <th class="px-5 py-3 text-left font-bold">Modules</th>
                            <th class="px-5 py-3 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody id="companyModulesTableBody" class="divide-y divide-slate-100">
                        @forelse($companies as $companyRow)
                            @php($assignedModules = $moduleDetailsCollection->where('company_id', $companyRow->company_id)->pluck('page_name')->filter()->values())
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-bold text-erp-ink">{{ $companyRow->name }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex max-w-4xl flex-wrap gap-1.5">
                                        @forelse($assignedModules as $moduleName)
                                            <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-bold text-slate-700 ring-1 ring-slate-200">{{ $moduleName }}</span>
                                        @empty
                                            <span class="text-erp-mute">No modules assigned</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button type="button" data-company-id="{{ $companyRow->company_id }}" data-company-name="{{ e($companyRow->name) }}" class="manage-company-modules rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Manage</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center text-erp-mute">No module permissions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div id="companyModulesModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-2xl rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <div>
                    <h3 class="text-base font-bold text-erp-ink">Assigned Modules</h3>
                    <p id="companyModalTitle" class="mt-1 text-sm text-erp-mute"></p>
                </div>
                <button type="button" onclick="closeCompanyModulesModal()" class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute hover:bg-slate-100">x</button>
            </div>
            <div class="max-h-[60vh] overflow-y-auto p-5">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold">Module Name</th>
                            <th class="px-4 py-3 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody id="companyModulesBody" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('companyModuleFilter')?.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#companyModulesTableBody tr').forEach(function (row) {
                row.hidden = !row.textContent.toLowerCase().includes(term);
            });
        });

        document.querySelectorAll('.manage-company-modules').forEach(function (button) {
            button.addEventListener('click', function () {
                openCompanyModules(this.dataset.companyId, this.dataset.companyName || '');
            });
        });

        function setModuleStatus(message, success = true) {
            const status = document.getElementById('moduleStatus');
            status.textContent = message;
            status.className = 'text-sm font-semibold ' + (success ? 'text-emerald-700' : 'text-rose-700');
        }

        function storeModulePermission() {
            const company = document.getElementById('company').value;
            const pages = document.getElementById('pages').value;

            if (!company) {
                setModuleStatus('Please select company first.', false);
                return;
            }

            if (pages === '0') {
                setModuleStatus('Please select module first.', false);
                return;
            }

            fetch("{{ url('/insert-modules') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({ company: company, pages: pages, mode: 0 })
            }).then(response => response.text()).then(function (response) {
                if (response.trim() === '1') {
                    setModuleStatus('Successfully inserted. Refreshing...');
                    window.setTimeout(() => window.location = "{{ url('/modules-permissions') }}", 450);
                } else {
                    setModuleStatus('Already exists or unable to insert.', false);
                }
            }).catch(function () {
                setModuleStatus('Unable to save module permission.', false);
            });
        }

        function openCompanyModules(companyId, companyName) {
            document.getElementById('companyModalTitle').textContent = companyName;
            const body = document.getElementById('companyModulesBody');
            body.innerHTML = '<tr><td colspan="2" class="px-4 py-6 text-center text-erp-mute">Loading...</td></tr>';

            fetch("{{ url('/getbycompanyid') }}?companyid=" + companyId)
                .then(response => response.json())
                .then(function (result) {
                    body.innerHTML = '';
                    if (!result.length) {
                        body.innerHTML = '<tr><td colspan="2" class="px-4 py-6 text-center text-erp-mute">No modules found.</td></tr>';
                        return;
                    }

                    result.forEach(function (item) {
                        const row = document.createElement('tr');
                        row.innerHTML = '<td class="px-4 py-3 font-semibold text-erp-ink"></td><td class="px-4 py-3 text-right"><button type="button" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button></td>';
                        row.querySelector('td').textContent = item.page_name;
                        row.querySelector('button').addEventListener('click', function () {
                            deleteCompanyModule(item.id);
                        });
                        body.appendChild(row);
                    });
                });

            document.getElementById('companyModulesModal').classList.remove('hidden');
            document.getElementById('companyModulesModal').classList.add('flex');
        }

        function closeCompanyModulesModal() {
            document.getElementById('companyModulesModal').classList.add('hidden');
            document.getElementById('companyModulesModal').classList.remove('flex');
        }

        function deleteCompanyModule(id) {
            if (!confirm('Delete this module permission?')) {
                return;
            }

            fetch("{{ url('/deletemodules') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: JSON.stringify({ id: id })
            }).then(response => response.text()).then(function (response) {
                if (response.trim() === '1') {
                    window.location = "{{ url('/modules-permissions') }}";
                } else {
                    alert('Unable to delete module permission.');
                }
            }).catch(function () {
                alert('Unable to delete module permission.');
            });
        }
    </script>
@endpush
