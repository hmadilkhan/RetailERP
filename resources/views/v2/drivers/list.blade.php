@extends('layouts.master-tailwind')

@section('title', 'Drivers')
@section('page_title', 'Drivers')
@section('page_subtitle', 'Manage delivery drivers for the current branch.')

@section('content')
    @php
        $driverCollection = collect($drivers ?? []);
        $activeDrivers = $driverCollection->where('status', 1);
        $inactiveDrivers = $driverCollection->where('status', 2);
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Active Drivers</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($activeDrivers->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available for assignment</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Inactive Drivers</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($inactiveDrivers->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Removed driver records</p>
            </div>
            <a href="{{ route('driver.create') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark sm:col-span-2">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Driver</div>
                    <p class="mt-2 text-sm text-white/75">Add a delivery driver</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Drivers List</h2>
                    <p class="mt-1 text-sm text-erp-mute">Filter active or inactive drivers.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <select id="driverStatusFilter" data-placeholder="Driver Status" class="v2-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-44">
                        <option value="1">Active</option>
                        <option value="2">Inactive</option>
                        <option value="all">All</option>
                    </select>
                    <input type="search" id="driverFilter" placeholder="Filter drivers..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Driver</th>
                            <th class="px-5 py-3 text-left font-bold">Contact</th>
                            <th class="px-5 py-3 text-left font-bold">Address</th>
                            <th class="px-5 py-3 text-left font-bold">License No</th>
                            <th class="px-5 py-3 text-left font-bold">NIC No</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="driverRows" class="divide-y divide-slate-100">
                        @forelse($driverCollection as $driver)
                            <tr class="hover:bg-slate-50" data-status="{{ $driver->status }}">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset('assets/images/drivers/' . (!empty($driver->image) ? $driver->image : 'placeholder.jpg')) }}" class="h-11 w-11 rounded-lg object-cover ring-1 ring-slate-200" alt="{{ $driver->name }}">
                                        <div class="font-bold text-erp-ink">{{ $driver->name }}</div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-erp-text">{{ $driver->mobile }}</td>
                                <td class="max-w-xs px-5 py-4 text-erp-mute"><div class="line-clamp-2">{{ $driver->address }}</div></td>
                                <td class="px-5 py-4 text-erp-text">{{ $driver->license_no }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $driver->nic_no }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        @if($driver->status == 1)
                                            <a href="{{ route('driver.edit', $driver->id) }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</a>
                                            <button type="button" onclick="changeDriverStatus(@js($driver->id), 2)" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                        @else
                                            <button type="button" onclick="changeDriverStatus(@js($driver->id), 1)" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">Activate</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-12 text-center text-erp-mute">No drivers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="driverStatus" class="border-t border-erp-line px-5 py-3 text-sm font-semibold text-erp-mute"></div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function applyDriverFilters() {
            const term = document.getElementById('driverFilter').value.toLowerCase();
            const status = document.getElementById('driverStatusFilter').value;
            document.querySelectorAll('#driverRows tr').forEach(function (row) {
                row.hidden = (status !== 'all' && row.dataset.status !== status) || !row.textContent.toLowerCase().includes(term);
            });
        }
        document.getElementById('driverFilter').addEventListener('input', applyDriverFilters);
        document.getElementById('driverStatusFilter').addEventListener('change', applyDriverFilters);
        if (window.jQuery) jQuery('#driverStatusFilter').on('change.select2', applyDriverFilters);
        applyDriverFilters();

        function changeDriverStatus(id, mode) {
            if (!confirm(mode === 1 ? 'Activate this driver?' : 'Delete this driver?')) return;
            fetch("{{ route('driver.delete') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ id: id, mode: mode })
            }).then(response => response.text()).then(function (response) {
                if (response.trim() === '1') window.location = "{{ route('driver.list') }}";
            });
        }
    </script>
@endpush
