@extends('layouts.master-tailwind')

@section('title', 'Delivery Service Provider')
@section('page_title', 'Service Provider')
@section('page_subtitle', 'Manage delivery service providers, ledgers, wallet links, and active status.')

@section('content')
    @php
        $providerCollection = collect($providers ?? []);
        $websiteCollection = collect($website ?? []);
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Providers</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($providerCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Currently loaded service providers</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Wallet Links</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($providerCollection->whereNotNull('website_id')->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Linked to websites</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Websites</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($websiteCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available website records</p>
            </div>
            <a href="{{ url('/service-provider-create') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Provider</div>
                    <p class="mt-2 text-sm text-white/75">Add delivery partner</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Service Provider Detail</h2>
                    <p class="mt-1 text-sm text-erp-mute">Review, edit, link wallet websites, or change active state.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <select id="providerStatusFilter" data-placeholder="Provider Status" class="v2-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-44">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <input type="search" id="providerFilter" placeholder="Filter providers..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Provider</th>
                            @if(session("roleId") == 2)<th class="px-5 py-3 text-left font-bold">Branch</th>@endif
                            <th class="px-5 py-3 text-left font-bold">Category</th>
                            <th class="px-5 py-3 text-left font-bold">Contact Person</th>
                            <th class="px-5 py-3 text-left font-bold">Contact No.</th>
                            <th class="px-5 py-3 text-left font-bold">Payment</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="providerRows" class="divide-y divide-slate-100">
                        @forelse($providerCollection as $value)
                            <tr class="hover:bg-slate-50" data-status="active">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img class="h-11 w-11 rounded-lg object-cover ring-1 ring-slate-200" src="{{ asset('storage/images/service-provider/' . (!empty($value->image) ? $value->image : 'placeholder.jpg')) }}" alt="{{ $value->provider_name }}">
                                        <div class="font-bold text-erp-ink">{{ $value->provider_name }}</div>
                                    </div>
                                </td>
                                @if(session("roleId") == 2)<td class="px-5 py-4 text-erp-text">{{ $value->branch_name }}</td>@endif
                                <td class="px-5 py-4 text-erp-text">{{ $value->category }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->person }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->contact }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->type }}</td>
                                <td class="px-5 py-4"><span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">{{ $value->status_name }}</span></td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ url('/service-provider-ledger', Crypt::encrypt($value->id)) }}" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">Ledger</a>
                                        <a href="{{ url('/service-provider-edit') }}/{{ Crypt::encrypt($value->id) }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</a>
                                        <button type="button" onclick="changeProviderStatus(@js($value->id), 2)" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Inactive</button>
                                        @if($websiteCollection->isNotEmpty() && $value->category == 'Wallets')
                                            <input type="hidden" id="walletId{{ $value->id }}" value="{{ Crypt::encrypt($value->id) }}">
                                            <input type="hidden" id="websiteWalletUniqueId{{ $value->id }}" value="{{ Crypt::encrypt($value->website_wallet_id) }}">
                                            <button type="button" onclick="websiteSetting(@js(isset($value->website_id) ? $value->website_id : 0), @js($value->provider_name), @js($value->id))" class="rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">{{ isset($value->website_id) ? 'Unlink' : 'Link' }}</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ session('roleId') == 2 ? 8 : 7 }}" class="px-5 py-12 text-center text-erp-mute">No service providers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="providerStatus" class="border-t border-erp-line px-5 py-3 text-sm font-semibold text-erp-mute"></div>
        </section>
    </div>

    <div id="websiteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-lg rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Link To Website</h3>
                <button type="button" onclick="closeWebsiteModal()" class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute hover:bg-slate-100">x</button>
            </div>
            <div class="space-y-4 p-5">
                <input type="hidden" id="wallet_md">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website</span>
                    <select id="website_md" data-placeholder="Select Website" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Website</option>
                        @foreach($websiteCollection as $val)
                            <option value="{{ Crypt::encrypt($val->id) }}">{{ $val->name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" id="btnSubmit" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Save</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function setProviderStatus(message, success = true) {
            const status = document.getElementById('providerStatus');
            status.textContent = message;
            status.className = 'border-t border-erp-line px-5 py-3 text-sm font-semibold ' + (success ? 'text-emerald-700' : 'text-rose-700');
        }

        function applyProviderFilters() {
            const term = document.getElementById('providerFilter').value.toLowerCase();
            const status = document.getElementById('providerStatusFilter').value;
            document.querySelectorAll('#providerRows tr').forEach(row => row.hidden = row.dataset.status !== status || !row.textContent.toLowerCase().includes(term));
        }

        document.getElementById('providerFilter').addEventListener('input', applyProviderFilters);
        document.getElementById('providerStatusFilter').addEventListener('change', function () {
            if (this.value === 'inactive') loadInactiveProviders();
            applyProviderFilters();
        });
        if (window.jQuery) jQuery('#providerStatusFilter').on('change.select2', function () {
            if (this.value === 'inactive') loadInactiveProviders();
            applyProviderFilters();
        });

        function loadInactiveProviders() {
            fetch("{{ url('/inacive-getserviceprovider') }}")
                .then(response => response.json())
                .then(function (result) {
                    const body = document.getElementById('providerRows');
                    body.querySelectorAll('tr[data-status="inactive"]').forEach(row => row.remove());
                    result.forEach(function (item) {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-slate-50';
                        row.dataset.status = 'inactive';
                        row.innerHTML = `<td class="px-5 py-4 font-bold text-erp-ink">${item.provider_name}</td>@if(session("roleId") == 2)<td class="px-5 py-4 text-erp-text">${item.branch_name || '-'}</td>@endif<td class="px-5 py-4 text-erp-text">${item.category}</td><td class="px-5 py-4 text-erp-text">${item.person}</td><td class="px-5 py-4 text-erp-text">${item.contact}</td><td class="px-5 py-4 text-erp-text">${item.percentage || item.type || '-'}</td><td class="px-5 py-4"><span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">${item.status_name}</span></td><td class="px-5 py-4 text-right"><button type="button" onclick="changeProviderStatus(${item.id}, 1)" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">Activate</button></td>`;
                        body.appendChild(row);
                    });
                    applyProviderFilters();
                });
        }

        function changeProviderStatus(id, mode) {
            if (!confirm(mode === 1 ? 'Activate this service provider?' : 'Set this service provider inactive?')) return;
            const url = mode === 1 ? "{{ url('/reactive-serviceprovider') }}" : "{{ url('/inactive-serviceprovider') }}";
            fetch(url, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ providerid: id })
            }).then(response => response.text()).then(function (response) {
                if (response.trim() === '1') window.location = "{{ url('/service-provider') }}";
                else setProviderStatus('Unable to update service provider.', false);
            }).catch(() => setProviderStatus('Unable to update service provider.', false));
        }

        function openWebsiteModal() {
            document.getElementById('websiteModal').classList.remove('hidden');
            document.getElementById('websiteModal').classList.add('flex');
        }
        function closeWebsiteModal() {
            document.getElementById('websiteModal').classList.add('hidden');
            document.getElementById('websiteModal').classList.remove('flex');
        }
        function websiteSetting(value, wallet, code) {
            if (value === 0) {
                document.getElementById('wallet_md').value = document.getElementById('walletId' + code).value;
                openWebsiteModal();
                return;
            }
            if (!confirm('Unlink website from this ' + wallet + ' wallet?')) return;
            fetch("{{ route('walletUnLinkToWebsite') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ uniqueId: document.getElementById('websiteWalletUniqueId' + code).value })
            }).then(response => { if (response.ok) window.location = "{{ url('/service-provider') }}"; });
        }
        document.getElementById('btnSubmit').addEventListener('click', function () {
            if (!document.getElementById('website_md').value) {
                setProviderStatus('Select website first.', false);
                return;
            }
            fetch("{{ route('walletLinkToWebsite') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ website: document.getElementById('website_md').value, wallet: document.getElementById('wallet_md').value })
            }).then(response => { if (response.ok) window.location = "{{ url('/service-provider') }}"; });
        });
        applyProviderFilters();
    </script>
@endpush
