@extends('layouts.master-tailwind')

@section('title', 'Delivery Area Lists')
@section('page_title', 'Delivery Lists')
@section('page_subtitle', 'Configure website delivery areas, city coverage, delivery charges, and branch availability.')

@section('content')
    @php
        $websiteCollection = collect($website ?? []);
        $cityCollection = collect($city ?? []);
        $deliveryCollection = collect($deliveryList ?? []);
        $areaCollection = collect($deliveryAreaValue ?? []);
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Websites</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($websiteCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Active website records</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Delivery Lists</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($deliveryCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Configured branch lists</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Locations</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($areaCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Cities or named delivery areas</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Cities</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($cityCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available city options</p>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Create Delivery Area</h2>
                <p class="mt-1 text-sm text-erp-mute">Create delivery coverage for a website and branch.</p>
            </div>
            <form id="deliveryAreasForm" action="{{ route('deliveryAreaStore') }}" method="post" class="grid gap-4 p-5 md:grid-cols-12">
                @csrf
                <label class="block md:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website</span>
                    <select name="website" id="website" data-placeholder="Select Website" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Website</option>
                        @foreach($websiteCollection as $val)
                            <option data-type="{{ $val->type }}" value="{{ $val->id }}">{{ $val->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block md:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Branch</span>
                    <select name="branch" id="branch" data-placeholder="Select Branch" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" disabled>
                        <option value="">Select Branch</option>
                    </select>
                </label>
                <label class="block md:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Delivery Charge</span>
                    <input type="text" name="charges" id="charges" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block md:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Minimum Order</span>
                    <input type="text" name="min_order" id="min_order" value="0" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block md:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Free On Minimum</span>
                    <input type="text" name="delivery_free_on_min_order" id="delivery_free_on_min_order" value="0" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block md:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Estimated Time</span>
                    <input type="text" name="time_estimate" id="estimate_time" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block md:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Estimated Days</span>
                    <input type="text" name="estimate_day" id="estimate_day" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block md:col-span-3">
                    <span class="flex items-center justify-between gap-3 text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Use Area Names <input type="checkbox" name="on_off_btn" id="on_off_btn" class="rounded border-erp-line text-erp focus:ring-erp"></span>
                    <select name="city[]" id="city" data-placeholder="Select City" class="v2-select2 mt-2 min-h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" multiple>
                        @foreach($cityCollection as $val)
                            <option value="{{ $val->city_id }}">{{ $val->city_name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="hidden block md:col-span-6" id="areaBox">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Area Names</span>
                    <input type="text" name="areas" id="areas" placeholder="Comma separated area names" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <div class="flex items-end md:col-span-6">
                    <button type="button" id="btn_create" class="h-10 rounded-lg bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">Create</button>
                </div>
            </form>
            <div id="deliveryStatus" class="border-t border-erp-line px-5 py-3 text-sm font-semibold text-erp-mute"></div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Lists</h2>
                    <p class="mt-1 text-sm text-erp-mute">Review delivery coverage by website and branch.</p>
                </div>
                <input type="search" id="deliveryFilter" placeholder="Filter delivery lists..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Website</th>
                            <th class="px-5 py-3 text-left font-bold">Branch</th>
                            <th class="px-5 py-3 text-left font-bold">Locations</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="deliveryRows" class="divide-y divide-slate-100">
                        @forelse($deliveryCollection as $parent)
                            @php($locations = $areaCollection->where('website_id', $parent->website_id))
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-bold text-erp-ink">{{ $parent->website_name }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $parent->branch_name }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex max-w-3xl flex-wrap gap-1.5">
                                        @forelse($locations as $area)
                                            <span class="rounded-md px-2 py-1 text-xs font-bold ring-1 {{ $area->status == 1 ? 'bg-sky-50 text-sky-700 ring-sky-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">{{ ($area->is_city == 1 ? $area->city_name : $area->name) }} - Rs.{{ $area->charge }}</span>
                                        @empty
                                            <span class="text-erp-mute">No locations</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-5 py-4"><span class="rounded-md px-2 py-1 text-xs font-bold ring-1 {{ $parent->status == 1 ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">{{ $parent->status == 1 ? 'Live' : 'Inactive' }}</span></td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" onclick="toggleDeliveryStatus(@js($parent->branch_id), @js($parent->status == 1 ? 0 : 1))" class="rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">{{ $parent->status == 1 ? 'Disable' : 'Live' }}</button>
                                        <button type="button" onclick="deleteDeliveryArea(@js($parent->branch_id), @js($parent->branch_name))" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-erp-mute">No delivery lists found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function setDeliveryStatus(message, success = true) {
            const status = document.getElementById('deliveryStatus');
            status.textContent = message;
            status.className = 'border-t border-erp-line px-5 py-3 text-sm font-semibold ' + (success ? 'text-emerald-700' : 'text-rose-700');
        }

        document.getElementById('website').addEventListener('change', function () {
            const branch = document.getElementById('branch');
            branch.disabled = true;
            branch.innerHTML = '<option value="">Loading...</option>';
            if (window.jQuery) jQuery('#branch').trigger('change.select2');

            fetch("{{ route('getWebsiteBranches') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ websiteId: this.value })
            }).then(response => response.json()).then(function (result) {
                branch.innerHTML = '<option value="">Select Branch</option>';
                result.forEach(function (item) {
                    const option = document.createElement('option');
                    option.value = item.branch_id;
                    option.textContent = item.branch_name;
                    branch.appendChild(option);
                });
                branch.disabled = false;
                if (window.jQuery) jQuery('#branch').trigger('change.select2');
            }).catch(function () {
                branch.innerHTML = '<option value="">Select Branch</option>';
                setDeliveryStatus('Unable to load branches.', false);
            });
        });

        document.getElementById('on_off_btn').addEventListener('change', function () {
            document.getElementById('areaBox').classList.toggle('hidden', !this.checked);
        });

        document.getElementById('btn_create').addEventListener('click', function () {
            const form = document.getElementById('deliveryAreasForm');
            fetch(form.action, { method: 'POST', body: new FormData(form) })
                .then(function (response) {
                    if (response.ok || response.redirected) {
                        window.location = "{{ route('deliveryAreasList') }}";
                    } else {
                        setDeliveryStatus('Unable to create delivery area.', false);
                    }
                })
                .catch(() => setDeliveryStatus('Unable to create delivery area.', false));
        });

        function toggleDeliveryStatus(branchId, status) {
            const data = new FormData();
            data.append('_token', "{{ csrf_token() }}");
            data.append('_method', 'PATCH');
            data.append('branchid', branchId);
            data.append('status', status);
            fetch("{{ url('delivery') }}/" + branchId + "/update", { method: 'POST', body: data })
                .then(() => window.location = "{{ route('deliveryAreasList') }}")
                .catch(() => setDeliveryStatus('Unable to update delivery status.', false));
        }

        function deleteDeliveryArea(branchId, branchName) {
            if (!confirm('Delete delivery area for ' + branchName + '?')) return;
            const data = new FormData();
            data.append('_token', "{{ csrf_token() }}");
            data.append('_method', 'DELETE');
            data.append('branchid', branchId);
            data.append('branchName', branchName);
            fetch("{{ url('delivery') }}/" + branchId + "/destroy", { method: 'POST', body: data })
                .then(() => window.location = "{{ route('deliveryAreasList') }}")
                .catch(() => setDeliveryStatus('Unable to delete delivery area.', false));
        }

        document.getElementById('deliveryFilter').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#deliveryRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        });
    </script>
@endpush
