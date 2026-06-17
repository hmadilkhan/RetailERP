@extends('layouts.master-tailwind')

@section('title', 'Stock')
@section('page_title', 'Branch Stock')
@section('page_subtitle', 'Review available inventory by branch, department, item code, product name, and stock health.')

@section('content')
    @php
        $selectedBranchId = session('branch');
        $selectedBranch = collect($branches ?? [])->firstWhere('branch_id', $selectedBranchId) ?? collect($branches ?? [])->first();
    @endphp

    <div class="space-y-6">
        <section class="space-y-4">
            <div class="overflow-hidden rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="border-b border-erp-line px-5 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-bold text-erp-ink">Branch Scope</h2>
                            <p class="mt-1 text-sm text-erp-mute">Choose a branch or search across all company locations.</p>
                        </div>
                        <label class="inline-flex cursor-pointer items-center gap-3 rounded-lg border border-erp-line bg-slate-50 px-3 py-2 text-sm font-bold text-erp-text">
                            <input type="checkbox" id="searchAllBranches" class="rounded border-erp-line text-erp focus:ring-erp">
                            All branches
                        </label>
                    </div>
                </div>
                <div class="flex gap-3 overflow-x-auto px-5 py-3" id="branchStrip">
                    @forelse ($branches as $branch)
                        <button type="button"
                            class="branch-card min-w-[14rem] rounded-lg border p-3 text-left transition hover:border-erp hover:bg-emerald-50/50 {{ (string) $branch->branch_id === (string) $selectedBranchId ? 'border-erp bg-emerald-50 ring-1 ring-erp' : 'border-erp-line bg-white' }}"
                            data-branch-id="{{ $branch->branch_id }}"
                            data-branch-name="{{ $branch->branch_name }}">
                            <div class="flex items-center gap-3">
                                <img class="h-10 w-10 rounded-lg object-cover ring-1 ring-slate-200"
                                    src="{{ asset('storage/images/branch/' . (!empty($branch->branch_logo) ? $branch->branch_logo : 'placeholder.jpg')) }}"
                                    alt="{{ $branch->branch_name }}">
                                <div class="min-w-0">
                                    <div class="truncate font-black text-erp-ink">{{ $branch->branch_name }}</div>
                                    <div class="mt-1 text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Branch {{ $branch->branch_id }}</div>
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="w-full rounded-lg border border-dashed border-erp-line p-6 text-center text-sm text-erp-mute">
                            No branches available for this user.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Selected Branch</div>
                    <div id="selectedBranchText" class="mt-4 truncate text-2xl font-black text-erp-ink">{{ $selectedBranch->branch_name ?? 'No branch' }}</div>
                    <p class="mt-2 text-sm text-erp-mute">Current inventory scope</p>
                </div>
                <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Loaded Items</div>
                    <div id="loadedItemsText" class="mt-4 text-3xl font-black text-erp-ink">0</div>
                    <p class="mt-2 text-sm text-erp-mute">Visible rows in the table</p>
                </div>
                <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Low / Out</div>
                    <div id="attentionItemsText" class="mt-4 text-3xl font-black text-erp-ink">0</div>
                    <p class="mt-2 text-sm text-erp-mute">Items needing attention</p>
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <div>
                        <h2 class="text-base font-bold text-erp-ink">Stock Ledger</h2>
                        <p class="mt-1 text-sm text-erp-mute">Filter items, inspect stock health, and open item movement details.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" id="clearFilters"
                            class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                            Clear
                        </button>
                        <button type="button" id="exportStock"
                            class="rounded-lg border border-erp bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">
                            Export Excel
                        </button>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Item Code</span>
                        <input type="text" id="codeFilter" placeholder="Search item code..."
                            class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Product</span>
                        <input type="text" id="nameFilter" placeholder="Search product..."
                            class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Department</span>
                        <select id="departmentFilter" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option value="">All departments</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Sub-Department</span>
                        <select id="subDepartmentFilter" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option value="">All sub-departments</option>
                        </select>
                    </label>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Product</th>
                            <th class="px-5 py-3 text-left font-bold">Department</th>
                            <th class="px-5 py-3 text-right font-bold">Retail</th>
                            <th class="px-5 py-3 text-right font-bold">Qty</th>
                            <th class="px-5 py-3 text-left font-bold">Conversion</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                            <th class="px-5 py-3 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody id="stockRows" class="divide-y divide-slate-100">
                        <tr>
                            <td colspan="7" class="px-5 py-14 text-center text-erp-mute">Loading stock...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div id="stockStatusText" class="text-sm font-semibold text-erp-mute">Loading stock...</div>
                <button type="button" id="loadMoreStock"
                    class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                    Load more
                </button>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const csrfToken = "{{ csrf_token() }}";
        const currentCompanyId = Number("{{ session('company_id') }}");
        const currentUsername = "{{ auth()->user()->username ?? '' }}";
        const privilegedCompanies = [95, 102, 104];
        const privilegedUsers = ['demoadmin', 'fnkhan'];

        let selectedBranchId = "{{ $selectedBranch->branch_id ?? session('branch') }}";
        let selectedBranchName = "{{ $selectedBranch->branch_name ?? 'Selected branch' }}";
        let stockPage = 1;
        let totalRows = 0;
        let loadedRows = 0;
        let attentionRows = 0;
        let filterTimer;

        const rowsEl = document.getElementById('stockRows');
        const loadMoreBtn = document.getElementById('loadMoreStock');
        const searchAllEl = document.getElementById('searchAllBranches');
        const codeEl = document.getElementById('codeFilter');
        const nameEl = document.getElementById('nameFilter');
        const departmentEl = document.getElementById('departmentFilter');
        const subDepartmentEl = document.getElementById('subDepartmentFilter');

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            }[char]));
        }

        function numberValue(value, digits = 2) {
            const parsed = Number(value || 0);
            return parsed.toLocaleString(undefined, { minimumFractionDigits: digits, maximumFractionDigits: digits });
        }

        function itemStatus(row) {
            const qty = Number(row.qty || 0);
            const reminder = Number(row.reminder_qty || 0);

            if (qty <= 0) {
                return { label: 'Out of Stock', className: 'bg-rose-50 text-rose-700 ring-rose-200', attention: true };
            }

            if (qty <= reminder) {
                return { label: 'Low Stock', className: 'bg-amber-50 text-amber-700 ring-amber-200', attention: true };
            }

            return { label: 'In Stock', className: 'bg-emerald-50 text-emerald-700 ring-emerald-200', attention: false };
        }

        function productImage(row) {
            if ((privilegedCompanies.includes(currentCompanyId) || privilegedUsers.includes(currentUsername)) && row.url) {
                return row.url;
            }

            if (row.image) {
                return "{{ asset('storage/images/products') }}/" + row.image;
            }

            return "{{ asset('storage/images/no-image.png') }}";
        }

        function renderRows(rows, append = false) {
            if (!append) {
                rowsEl.innerHTML = '';
                loadedRows = 0;
                attentionRows = 0;
            }

            if (!rows.length && !append) {
                rowsEl.innerHTML = '<tr><td colspan="7" class="px-5 py-14 text-center"><div class="font-bold text-erp-ink">No stock found</div><p class="mt-2 text-sm text-erp-mute">Try another branch or loosen the filters.</p></td></tr>';
            }

            rows.forEach(row => {
                const status = itemStatus(row);
                const conversionQty = Number(row.qty || 0) * Number(row.weight_qty || 0);
                const conversionUnit = row.cname ? ' ' + escapeHtml(row.cname) : '';
                const rowBranchId = row.stock_branch_id || selectedBranchId;
                const detailUrl = "{{ url('/stock-details') }}/" + encodeURIComponent(row.id) + "/" + encodeURIComponent(rowBranchId);

                loadedRows += 1;
                if (status.attention) {
                    attentionRows += 1;
                }

                rowsEl.insertAdjacentHTML('beforeend', `
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4">
                            <div class="flex min-w-[18rem] items-center gap-3">
                                <img class="h-12 w-12 rounded-lg object-cover ring-1 ring-slate-200" src="${escapeHtml(productImage(row))}" alt="${escapeHtml(row.product_name)}">
                                <div class="min-w-0">
                                    <div class="truncate font-bold text-erp-ink">${escapeHtml(row.product_name)}</div>
                                    <div class="mt-1 flex flex-wrap gap-2 text-xs">
                                        <span class="font-bold uppercase tracking-[0.12em] text-erp-mute">${escapeHtml(row.item_code || '-')}</span>
                                        <span class="text-erp-mute">${escapeHtml(row.branch_name || selectedBranchName)}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-semibold text-erp-text">${escapeHtml(row.department_name || '-')}</div>
                            <div class="mt-1 text-xs text-erp-mute">${escapeHtml(row.sub_depart_name || '-')}</div>
                        </td>
                        <td class="px-5 py-4 text-right font-bold text-erp-ink">${numberValue(row.amount)}</td>
                        <td class="px-5 py-4 text-right">
                            <div class="font-black text-erp-ink">${numberValue(row.qty)}</div>
                            <div class="mt-1 text-xs text-erp-mute">${escapeHtml(row.name || '')}</div>
                        </td>
                        <td class="px-5 py-4 text-erp-text">${numberValue(conversionQty)}${conversionUnit}</td>
                        <td class="px-5 py-4">
                            <span class="rounded-md px-2 py-1 text-xs font-bold ring-1 ${status.className}">${status.label}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="${detailUrl}" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">View</a>
                        </td>
                    </tr>
                `);
            });

            document.getElementById('loadedItemsText').textContent = loadedRows.toLocaleString();
            document.getElementById('attentionItemsText').textContent = attentionRows.toLocaleString();
        }

        function updateMeta(response) {
            totalRows = Number(response.total || 0);
            const from = response.from || 0;
            const to = response.to || loadedRows;
            document.getElementById('stockStatusText').textContent = totalRows ? `Showing ${from} to ${to} of ${totalRows} items` : 'No stock found.';
            loadMoreBtn.disabled = !response.next_page_url;
            loadMoreBtn.classList.toggle('opacity-40', !response.next_page_url);
            loadMoreBtn.classList.toggle('cursor-not-allowed', !response.next_page_url);
        }

        function loadStock(append = false) {
            const params = new URLSearchParams();
            params.set('page', stockPage);
            params.set('branchid', selectedBranchId);
            params.set('code', codeEl.value);
            params.set('name', nameEl.value);
            params.set('dept', departmentEl.value);
            params.set('sdept', subDepartmentEl.value);
            params.set('search', searchAllEl.checked ? 'true' : 'false');

            if (!append) {
                rowsEl.innerHTML = '<tr><td colspan="7" class="px-5 py-14 text-center text-erp-mute">Loading stock...</td></tr>';
            }

            fetch("{{ url('/branchwise-stock') }}?" + params.toString(), {
                headers: { 'Accept': 'application/json' }
            })
                .then(response => response.json())
                .then(function (response) {
                    renderRows(response.data || [], append);
                    updateMeta(response);
                })
                .catch(function () {
                    rowsEl.innerHTML = '<tr><td colspan="7" class="px-5 py-14 text-center text-rose-600">Unable to load stock.</td></tr>';
                    document.getElementById('stockStatusText').textContent = 'Stock request failed.';
                });
        }

        function refreshStock() {
            stockPage = 1;
            loadStock(false);
        }

        document.querySelectorAll('.branch-card').forEach(function (card) {
            card.addEventListener('click', function () {
                selectedBranchId = this.dataset.branchId;
                selectedBranchName = this.dataset.branchName;
                document.getElementById('selectedBranchText').textContent = selectedBranchName;
                document.querySelectorAll('.branch-card').forEach(btn => {
                    btn.classList.remove('border-erp', 'bg-emerald-50', 'ring-1', 'ring-erp');
                    btn.classList.add('border-erp-line', 'bg-white');
                });
                this.classList.remove('border-erp-line', 'bg-white');
                this.classList.add('border-erp', 'bg-emerald-50', 'ring-1', 'ring-erp');
                searchAllEl.checked = false;
                refreshStock();
            });
        });

        [codeEl, nameEl].forEach(function (input) {
            input.addEventListener('input', function () {
                clearTimeout(filterTimer);
                filterTimer = setTimeout(refreshStock, 350);
            });
        });

        departmentEl.addEventListener('change', function () {
            loadSubDepartments(this.value);
            subDepartmentEl.value = '';
            refreshStock();
        });

        subDepartmentEl.addEventListener('change', refreshStock);
        searchAllEl.addEventListener('change', refreshStock);

        document.getElementById('clearFilters').addEventListener('click', function () {
            codeEl.value = '';
            nameEl.value = '';
            departmentEl.value = '';
            subDepartmentEl.innerHTML = '<option value="">All sub-departments</option>';
            searchAllEl.checked = false;
            refreshStock();
        });

        loadMoreBtn.addEventListener('click', function () {
            if (this.disabled) {
                return;
            }

            stockPage += 1;
            loadStock(true);
        });

        document.getElementById('exportStock').addEventListener('click', function () {
            const params = new URLSearchParams();
            params.set('branchid', selectedBranchId);
            params.set('code', codeEl.value);
            params.set('name', nameEl.value);
            params.set('dept', departmentEl.value);
            params.set('sdept', subDepartmentEl.value);
            window.open("{{ url('reports/excel-export-stock-report') }}?" + params.toString(), '_blank');
        });

        function loadDepartments() {
            fetch("{{ url('get_departments') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({})
            })
                .then(response => response.json())
                .then(function (departments) {
                    departmentEl.innerHTML = '<option value="">All departments</option>';
                    departments.forEach(function (department) {
                        departmentEl.insertAdjacentHTML('beforeend', `<option value="${escapeHtml(department.department_id)}">${escapeHtml(department.department_name)}</option>`);
                    });
                });
        }

        function loadSubDepartments(id) {
            subDepartmentEl.innerHTML = '<option value="">All sub-departments</option>';
            if (!id) {
                return;
            }

            fetch("{{ url('get_sub_departments') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ id: id })
            })
                .then(response => response.json())
                .then(function (subDepartments) {
                    subDepartments.forEach(function (subDepartment) {
                        subDepartmentEl.insertAdjacentHTML('beforeend', `<option value="${escapeHtml(subDepartment.sub_department_id)}">${escapeHtml(subDepartment.sub_depart_name)}</option>`);
                    });
                });
        }

        loadDepartments();
        loadStock(false);
    </script>
@endpush
