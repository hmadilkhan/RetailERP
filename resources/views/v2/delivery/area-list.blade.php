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
                <div class="md:col-span-3">
                    <div class="flex items-center justify-between gap-3 text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">
                        <span>City</span>
                        <label for="on_off_btn" class="inline-flex cursor-pointer items-center gap-2 normal-case tracking-normal">
                            Use Area Names
                            <input type="checkbox" name="on_off_btn" id="on_off_btn" value="1" class="rounded border-erp-line text-erp focus:ring-erp">
                        </label>
                    </div>
                    <select name="city[]" id="city" data-placeholder="Select City" class="v2-select2 mt-2 min-h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" multiple>
                        @foreach($cityCollection as $val)
                            <option value="{{ $val->city_id }}">{{ $val->city_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="hidden md:col-span-6" id="areaBox">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Area Names</span>
                    <input type="text" name="areas" id="areas" placeholder="Type area name and press Enter or comma" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </div>
                <div class="flex items-end md:col-span-6" id="createBtnWrap">
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

@push('styles')
    <link rel="stylesheet" href="{{ asset('components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}" />
    <style>
        #areaBox .bootstrap-tagsinput {
            display: block;
            width: 100%;
            min-height: 2.5rem;
            margin-top: 0.5rem;
            padding: 0.35rem 0.5rem;
            border: 1px solid #d8e1ec;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
            background: #fff;
            line-height: 1.4;
        }
        #areaBox .bootstrap-tagsinput input {
            width: auto !important;
            max-width: 100%;
            margin: 2px 0;
            font-size: 0.875rem;
        }
        #areaBox .bootstrap-tagsinput .tag {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            margin: 2px 4px 2px 0;
            padding: 0.15rem 0.45rem;
            border-radius: 0.375rem;
            background: #ecfdf5;
            color: #047857;
            font-size: 0.75rem;
            font-weight: 700;
        }
        #areaBox .bootstrap-tagsinput .tag [data-role="remove"] {
            margin-left: 0.15rem;
            cursor: pointer;
        }
        #areaBox .bootstrap-tagsinput .tag [data-role="remove"]:after {
            content: "×";
            padding: 0 2px;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('components/bootstrap-tagsinput/dist/bootstrap-tagsinput.js') }}"></script>
    <script>
        function setDeliveryStatus(message, success = true) {
            const status = document.getElementById('deliveryStatus');
            status.textContent = message;
            status.className = 'border-t border-erp-line px-5 py-3 text-sm font-semibold ' + (success ? 'text-emerald-700' : 'text-rose-700');
        }

        function refreshSelect2($select) {
            if (window.jQuery && jQuery.fn.select2 && $select.hasClass('select2-hidden-accessible')) {
                $select.trigger('change.select2');
            }
        }

        function initCitySelect2($city) {
            $city.select2({
                allowClear: true,
                dropdownCssClass: 'v2-select2-dropdown',
                placeholder: $city.data('placeholder') || 'Select City',
                width: '100%'
            });
        }

        function setCityAreaMode(useAreas) {
            const $city = jQuery('#city');
            const areaBox = document.getElementById('areaBox');

            if ($city.hasClass('select2-hidden-accessible')) {
                $city.select2('destroy');
            }

            if (useAreas) {
                $city.attr('name', 'city').removeAttr('multiple').val('');
                areaBox.classList.remove('hidden');
            } else {
                $city.attr({ name: 'city[]', multiple: 'multiple' }).val(null);
                areaBox.classList.add('hidden');
            }

            initCitySelect2($city);

            if (jQuery.fn.tagsinput) {
                jQuery('#areas').tagsinput('removeAll');
            }
        }

        jQuery(function () {
            if (jQuery.fn.tagsinput) {
                jQuery('#areas').tagsinput({ maxTags: 40 });
            }
        });

        jQuery('#website').on('change', function () {
            const $branch = jQuery('#branch');
            $branch.prop('disabled', true).empty().append(jQuery('<option>').val('').text('Loading...'));
            refreshSelect2($branch);

            jQuery.ajax({
                url: "{{ route('getWebsiteBranches') }}",
                type: 'POST',
                data: { _token: "{{ csrf_token() }}", websiteId: jQuery(this).val() },
                success: function (result) {
                    $branch.empty().append(jQuery('<option>').val('').text('Select Branch'));
                    jQuery.each(result || [], function (_, item) {
                        $branch.append(jQuery('<option>').val(item.branch_id).text(item.branch_name));
                    });
                    $branch.prop('disabled', false);
                    refreshSelect2($branch);
                },
                error: function () {
                    $branch.empty().append(jQuery('<option>').val('').text('Select Branch'));
                    $branch.prop('disabled', false);
                    refreshSelect2($branch);
                    setDeliveryStatus('Unable to load branches.', false);
                }
            });
        });

        document.getElementById('on_off_btn').addEventListener('change', function () {
            setCityAreaMode(this.checked);
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
