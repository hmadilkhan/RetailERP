@extends('layouts.master-tailwind')

@section('title', 'Vehicles')
@section('page_title', 'Vehicles')
@section('page_subtitle', 'Manage delivery vehicles for the current branch.')

@section('content')
    @php
        $vehicleCollection = collect($vehicles ?? []);
        $activeVehicles = $vehicleCollection->where('status', 1);
        $inactiveVehicles = $vehicleCollection->where('status', 2);
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Active Vehicles</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($activeVehicles->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available for delivery</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Inactive Vehicles</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($inactiveVehicles->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Removed vehicle records</p>
            </div>
            <a href="{{ route('vehicle.create') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark sm:col-span-2">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Vehicle</div>
                    <p class="mt-2 text-sm text-white/75">Add a delivery vehicle</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Vehicles List</h2>
                    <p class="mt-1 text-sm text-erp-mute">Filter active or inactive vehicles.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <select id="vehicleStatusFilter" data-placeholder="Vehicle Status" class="v2-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-44">
                        <option value="1">Active</option>
                        <option value="2">Inactive</option>
                        <option value="all">All</option>
                    </select>
                    <input type="search" id="vehicleFilter" placeholder="Filter vehicles..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Model</th>
                            <th class="px-5 py-3 text-left font-bold">Model No</th>
                            <th class="px-5 py-3 text-left font-bold">Number</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="vehicleRows" class="divide-y divide-slate-100">
                        @forelse($vehicleCollection as $vehicle)
                            <tr class="hover:bg-slate-50" data-status="{{ $vehicle->status }}">
                                <td class="px-5 py-4 font-bold text-erp-ink">{{ $vehicle->model_name }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $vehicle->model_no }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $vehicle->number }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md px-2 py-1 text-xs font-bold ring-1 {{ $vehicle->status == 1 ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">
                                        {{ $vehicle->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        @if($vehicle->status == 1)
                                            <a href="{{ route('vehicle.edit', $vehicle->id) }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</a>
                                            <button type="button" onclick="changeVehicleStatus(@js($vehicle->id), 2)" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                        @else
                                            <button type="button" onclick="changeVehicleStatus(@js($vehicle->id), 1)" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">Activate</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-12 text-center text-erp-mute">No vehicles found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="vehicleStatus" class="border-t border-erp-line px-5 py-3 text-sm font-semibold text-erp-mute"></div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function applyVehicleFilters() {
            const term = document.getElementById('vehicleFilter').value.toLowerCase();
            const status = document.getElementById('vehicleStatusFilter').value;
            document.querySelectorAll('#vehicleRows tr').forEach(function (row) {
                row.hidden = (status !== 'all' && row.dataset.status !== status) || !row.textContent.toLowerCase().includes(term);
            });
        }
        document.getElementById('vehicleFilter').addEventListener('input', applyVehicleFilters);
        document.getElementById('vehicleStatusFilter').addEventListener('change', applyVehicleFilters);
        if (window.jQuery) jQuery('#vehicleStatusFilter').on('change.select2', applyVehicleFilters);
        applyVehicleFilters();

        function changeVehicleStatus(id, mode) {
            if (!confirm(mode === 1 ? 'Activate this vehicle?' : 'Delete this vehicle?')) return;
            fetch("{{ route('vehicle.delete') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ id: id, mode: mode })
            }).then(response => response.text()).then(function (response) {
                if (response.trim() === '1') window.location = "{{ route('vehicle.list') }}";
            });
        }
    </script>
@endpush
