@extends('layouts.master-tailwind')

@section('title', 'Vendors')
@section('page_title', 'Vendors')
@section('page_subtitle', 'Manage vendors, balances, ledgers, purchase history, and advance payments.')

@section('content')
    @php
        $vendorCollection = collect($vendor ?? []);
        $activeVendors = $vendorCollection->where('status_id', 1);
        $inactiveVendors = $vendorCollection->where('status_id', 2);
        $totalBalance = $activeVendors->sum(fn($row) => (float) ($row->balance ?? 0));
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Active Vendors</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($activeVendors->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available for purchase orders</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Inactive Vendors</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($inactiveVendors->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Removed vendor records</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Payable Balance</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($totalBalance, 2) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Current vendor ledger balance</p>
            </div>
            <a href="{{ route('vendors.create') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Vendor</div>
                    <p class="mt-2 text-sm text-white/75">Add supplier details</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Vendor Directory</h2>
                    <p class="mt-1 text-sm text-erp-mute">Filter active or inactive vendors and open related workflows.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <select id="vendorStatusFilter" data-placeholder="Vendor Status" class="v2-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-44">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="all">All</option>
                    </select>
                    <input type="search" id="vendorFilter" placeholder="Filter vendors..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Vendor</th>
                            <th class="px-5 py-3 text-left font-bold">Contact</th>
                            <th class="px-5 py-3 text-left font-bold">Created</th>
                            <th class="px-5 py-3 text-left font-bold">Payment Terms</th>
                            <th class="px-5 py-3 text-right font-bold">Balance</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="vendorRows" class="divide-y divide-slate-100">
                        @forelse($vendorCollection as $value)
                            <tr class="hover:bg-slate-50" data-status="{{ $value->status_id == 1 ? 'active' : 'inactive' }}">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset('storage/images/vendors/' . (!empty($value->image) ? $value->image : 'placeholder.jpg')) }}" class="h-11 w-11 rounded-lg object-cover ring-1 ring-slate-200" alt="{{ $value->vendor_name }}">
                                        <div>
                                            <div class="font-bold text-erp-ink">{{ $value->vendor_name }}</div>
                                            <div class="mt-1 text-xs text-erp-mute">{{ $value->company_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->vendor_contact }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ !empty($value->created_at) ? date('d-m-Y', strtotime($value->created_at)) : '-' }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->payment_terms }} Days</td>
                                <td class="px-5 py-4 text-right font-bold text-erp-ink">{{ number_format((float) ($value->balance ?? 0), 2) }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        @if($value->status_id == 1)
                                            <a href="{{ route('vendors.edit', $value->id) }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</a>
                                            <a href="{{ url('/ledgerlist', $value->slug) }}" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">Ledger</a>
                                            <a href="{{ url('add-vendor-product', $value->slug) }}" class="rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Products</a>
                                            <a href="{{ url('vendor-po', $value->slug) }}" class="rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Purchases</a>
                                            <a href="{{ url('advance-payment-view', $value->id) }}" class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-xs font-bold text-purple-700 transition hover:bg-purple-100">Advances</a>
                                            <button type="button" onclick="deleteVendor(@js($value->id), @js($value->vendor_name), @js($value->company_name))" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                        @else
                                            <button type="button" onclick="activateVendor(@js($value->id))" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">Activate</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-erp-mute">No vendors found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="vendorStatus" class="border-t border-erp-line px-5 py-3 text-sm font-semibold text-erp-mute"></div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function setVendorStatus(message, success = true) {
            const status = document.getElementById('vendorStatus');
            status.textContent = message;
            status.className = 'border-t border-erp-line px-5 py-3 text-sm font-semibold ' + (success ? 'text-emerald-700' : 'text-rose-700');
        }

        function applyVendorFilters() {
            const term = document.getElementById('vendorFilter').value.toLowerCase();
            const status = document.getElementById('vendorStatusFilter').value;
            document.querySelectorAll('#vendorRows tr').forEach(function (row) {
                const statusMatch = status === 'all' || row.dataset.status === status;
                const textMatch = row.textContent.toLowerCase().includes(term);
                row.hidden = !statusMatch || !textMatch;
            });
        }

        document.getElementById('vendorFilter').addEventListener('input', applyVendorFilters);
        document.getElementById('vendorStatusFilter').addEventListener('change', applyVendorFilters);
        if (window.jQuery) {
            jQuery('#vendorStatusFilter').on('change.select2', applyVendorFilters);
        }
        applyVendorFilters();

        function deleteVendor(id, name, companyName) {
            if (!confirm('Delete vendor ' + name + ' from ' + companyName + '?')) {
                return;
            }

            fetch("{{ url('vendors') }}/" + id, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" }
            }).then(response => response.json()).then(function (response) {
                if (response.result == 1) {
                    window.location = "{{ route('vendors.index') }}";
                } else {
                    setVendorStatus('Unable to delete vendor.', false);
                }
            }).catch(() => setVendorStatus('Unable to delete vendor.', false));
        }

        function activateVendor(id) {
            if (!confirm('Activate this vendor again?')) {
                return;
            }

            fetch("{{ url('active-vendor') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ id: id })
            }).then(response => response.text()).then(function (response) {
                if (response.trim() === '1') {
                    window.location = "{{ url('vendors') }}";
                } else {
                    setVendorStatus('Unable to activate vendor.', false);
                }
            }).catch(() => setVendorStatus('Unable to activate vendor.', false));
        }
    </script>
@endpush
