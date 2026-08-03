@extends('layouts.master-tailwind')

@section('title', 'Branches')
@section('page_title', 'Branches')
@section('page_subtitle', 'Manage branch profiles, terminals, contact details, reporting setup, and location coverage.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Branches</div>
                <div id="statTotal" class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($details->total()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Active records in current scope</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Visible Rows</div>
                <div id="statVisible" class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($details->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Showing on this page</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Search</div>
                <div id="statSearch" class="mt-4 text-xl font-black text-erp-ink">{{ $search !== '' ? $search : 'All branches' }}</div>
                <p class="mt-2 text-sm text-erp-mute">Filtered by branch, city, contact, or code</p>
            </div>
            <a href="{{ url('/createbranch') }}"
                class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Branch</div>
                    <p class="mt-2 text-sm text-white/75">Add a new operational location</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Branch Directory</h2>
                    <p class="mt-1 text-sm text-erp-mute">Search, review, edit, email-map, delete, or attach terminals.</p>
                </div>
                <form method="GET" action="{{ url('/branches') }}" id="searchForm" class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row" onsubmit="return false;">
                    <select name="company_id" id="companyFilter" data-placeholder="All Companies"
                        class="v2-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-56">
                        <option value="">All Companies</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->company_id }}" {{ (string) ($companyId ?? '') === (string) $company->company_id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="search" id="branchSearch" value="{{ $search }}" autocomplete="off"
                        placeholder="Search branches..."
                        class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
                </form>
            </div>

            <div id="branchResults" class="relative min-h-[220px]">
                <div id="branchTableLoader" class="pointer-events-none absolute inset-0 z-10 hidden items-center justify-center bg-white/70 backdrop-blur-[1px]">
                    <div class="flex flex-col items-center gap-3 rounded-xl border border-erp-line bg-white px-6 py-5 shadow-sm">
                        <span class="inline-block h-9 w-9 animate-spin rounded-full border-[3px] border-erp/20 border-t-erp"></span>
                        <span class="text-sm font-semibold text-erp-mute">Loading branches...</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                            <tr>
                                <th class="px-5 py-3 text-left font-bold">Branch</th>
                                <th class="px-5 py-3 text-left font-bold">City</th>
                                <th class="px-5 py-3 text-left font-bold">Contact</th>
                                <th class="px-5 py-3 text-left font-bold">Terminals</th>
                                <th class="px-5 py-3 text-left font-bold">Serials</th>
                                <th class="px-5 py-3 text-left font-bold">Address</th>
                                <th class="px-5 py-3 text-right font-bold">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="branchTableBody" class="divide-y divide-slate-100">
                            @include('v2.branch.partials.rows', ['details' => $details, 'search' => $search])
                        </tbody>
                    </table>
                </div>

                <div id="branchPagination" class="flex flex-col gap-3 border-t border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    @include('v2.branch.partials.pagination', ['details' => $details])
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        let branchSearchTimer;
        let branchRequestController = null;
        const branchSearchInput = document.getElementById('branchSearch');
        const companyFilter = document.getElementById('companyFilter');
        const branchTableBody = document.getElementById('branchTableBody');
        const branchPagination = document.getElementById('branchPagination');
        const branchTableLoader = document.getElementById('branchTableLoader');
        const branchesUrl = "{{ url('/branches') }}";

        function setBranchLoader(visible) {
            if (!branchTableLoader) return;
            branchTableLoader.classList.toggle('hidden', !visible);
            branchTableLoader.classList.toggle('flex', visible);
        }

        function updateBranchStats(data) {
            const totalEl = document.getElementById('statTotal');
            const visibleEl = document.getElementById('statVisible');
            const searchEl = document.getElementById('statSearch');
            if (totalEl) totalEl.textContent = Number(data.total || 0).toLocaleString();
            if (visibleEl) visibleEl.textContent = Number(data.visible || 0).toLocaleString();
            if (searchEl) searchEl.textContent = data.searchLabel || 'All branches';
        }

        function bindPaginationLinks() {
            branchPagination?.querySelectorAll('[data-page-link]').forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    const url = new URL(this.href);
                    loadBranches({
                        search: url.searchParams.get('search') || '',
                        company_id: url.searchParams.get('company_id') || '',
                        page: url.searchParams.get('page') || '1',
                    });
                });
            });
        }

        function loadBranches(overrides = {}) {
            const search = overrides.search !== undefined ? overrides.search : (branchSearchInput?.value || '');
            const companyId = overrides.company_id !== undefined ? overrides.company_id : (companyFilter?.value || '');
            const page = overrides.page !== undefined ? overrides.page : '1';

            const params = new URLSearchParams();
            if (search) params.set('search', search);
            if (companyId) params.set('company_id', companyId);
            if (page && page !== '1') params.set('page', page);

            const query = params.toString();
            const requestUrl = query ? `${branchesUrl}?${query}` : branchesUrl;

            if (branchRequestController) {
                branchRequestController.abort();
            }
            branchRequestController = new AbortController();

            setBranchLoader(true);

            fetch(requestUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                signal: branchRequestController.signal,
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('Unable to load branches');
                    return response.json();
                })
                .then(function (data) {
                    if (branchTableBody) branchTableBody.innerHTML = data.rows || '';
                    if (branchPagination) branchPagination.innerHTML = data.pagination || '';
                    updateBranchStats(data);
                    bindPaginationLinks();
                    window.history.replaceState({}, '', requestUrl);
                })
                .catch(function (error) {
                    if (error.name === 'AbortError') return;
                    if (branchTableBody) {
                        branchTableBody.innerHTML = `
                            <tr>
                                <td colspan="7" class="px-5 py-14 text-center">
                                    <div class="text-base font-bold text-rose-700">Unable to load branches</div>
                                    <p class="mt-2 text-sm text-erp-mute">Please try again.</p>
                                </td>
                            </tr>
                        `;
                    }
                })
                .finally(function () {
                    setBranchLoader(false);
                });
        }

        if (branchSearchInput?.value) {
            window.requestAnimationFrame(function () {
                branchSearchInput.focus();
                branchSearchInput.setSelectionRange(branchSearchInput.value.length, branchSearchInput.value.length);
            });
        }

        branchSearchInput?.addEventListener('input', function () {
            clearTimeout(branchSearchTimer);
            branchSearchTimer = setTimeout(function () {
                loadBranches({ page: '1' });
            }, 400);
        });

        if (window.jQuery) {
            jQuery('#companyFilter').on('change.select2', function () {
                loadBranches({ page: '1' });
            });
        } else {
            companyFilter?.addEventListener('change', function () {
                loadBranches({ page: '1' });
            });
        }

        bindPaginationLinks();

        function deleteBranch(id) {
            if (!confirm('Delete this branch?')) {
                return;
            }

            fetch("{{ url('/removebranch') }}", {
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
                        loadBranches();
                    } else {
                        alert('Unable to delete branch.');
                    }
                })
                .catch(function () {
                    alert('Unable to delete branch.');
                });
        }
    </script>
@endpush
