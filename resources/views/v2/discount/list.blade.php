@extends('layouts.master-tailwind')

@section('title', 'Discount')
@section('page_title', 'Discount List')
@section('page_subtitle', 'Manage promotional discounts, their schedule, and eligibility.')

@section('content')
    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Discount List</h2>
                    <a href="{{ $status == 2 ? url('/get-discount') : url('/get-discount/in-active') }}" class="mt-1 inline-block text-sm font-bold text-erp-dark hover:text-erp">{{ $status == 2 ? 'Show Active Items' : 'Show In-Active Items' }}</a>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <input type="search" id="discountSearch" placeholder="Search discount..." class="h-10 w-56 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <button type="button" id="removeAllBtn" data-mode="{{ $status }}" class="hidden rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-bold text-rose-700 transition hover:bg-rose-100">Remove All</button>
                    <a href="{{ url('/create-discount') }}" class="rounded-lg border border-erp bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">+ Create Discount</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="expensetb" class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-5 py-3"><input type="checkbox" id="headCheckbox" class="rounded border-erp-line text-erp focus:ring-erp"></th>
                            <th class="px-5 py-3">Code</th>
                            <th class="px-5 py-3">Website Name</th>
                            <th class="px-5 py-3">Start Date</th>
                            <th class="px-5 py-3">Expiration Date</th>
                            <th class="px-5 py-3">Applies To</th>
                            <th class="px-5 py-3">Customer Eligibility</th>
                            <th class="px-5 py-3">Type</th>
                            <th class="px-5 py-3">Open Discount</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line">
                        @forelse($discount as $value)
                            <tr class="discount-row" data-search="{{ strtolower($value->discount_code.' '.$value->website_name) }}">
                                <td class="px-5 py-3"><input type="checkbox" class="child-chkbx rounded border-erp-line text-erp focus:ring-erp" value="{{ $value->discount_id }}"></td>
                                <td class="px-5 py-3 font-semibold text-erp-ink">{{ $value->discount_code }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->website_name }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->startdate.' '.$value->starttime }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->enddate.' '.$value->endtime }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->applies_name }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->customer_eligibilty == 1 ? 'EveryOne' : 'Limited' }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->type_name.' ('.$value->discount_value.')' }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->open_discount == 1 ? 'Open Discount' : 'Voucher Apply' }}</td>
                                <td class="px-5 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $value->status == 1 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ $value->status_name }}</span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <label class="relative inline-flex h-6 w-11 cursor-pointer items-center">
                                            <input type="checkbox" class="peer sr-only" onclick="switchMode({{ $value->discount_id }}, {{ $value->status }}, '{{ $value->discount_code }}', this)" {{ $value->status == 1 ? 'checked' : '' }}>
                                            <span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-erp"></span>
                                            <span class="absolute left-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                                        </label>
                                        <button type="button" onclick="modelcall({{ $value->discount_id }})" class="font-bold text-erp-dark hover:text-erp">View</button>
                                        <button type="button" onclick="discountDelete({{ $value->discount_id }}, {{ $status }})" class="font-bold text-rose-600 hover:text-rose-700">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11" class="px-5 py-6 text-center text-sm text-erp-mute">No discounts yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div id="discount-view-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="max-h-full w-full max-w-2xl overflow-y-auto rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Discount Details</h3>
                <button type="button" class="text-erp-mute hover:text-erp-ink" onclick="closeModal('discount-view-modal')">Close</button>
            </div>
            <div class="space-y-3 px-5 py-5 text-sm">
                <div class="flex justify-between"><span class="text-erp-mute">Code</span><span id="discount_code" class="font-bold text-erp-ink"></span></div>
                <div class="flex justify-between"><span class="text-erp-mute">Type</span><span id="discount_type" class="font-bold text-erp-ink"></span></div>
                <div id="applyTo" class="flex justify-between"><span class="text-erp-mute">Applies To</span><span id="discount_applies" class="font-bold text-erp-ink"></span></div>
                <div class="flex justify-between"><span class="text-erp-mute">Starts At</span><span id="discount_starts" class="font-bold text-erp-ink"></span></div>
                <div class="flex justify-between"><span class="text-erp-mute">Expired At</span><span id="discount_ends" class="font-bold text-erp-ink"></span></div>
                <div class="flex justify-between"><span class="text-erp-mute">Status</span><span id="discount_status_class" class="font-bold text-erp-ink"></span></div>

                <div id="CatandPro" class="hidden grid grid-cols-2 gap-3 overflow-y-auto pt-3 sm:grid-cols-3" style="max-height: 280px;"></div>

                <div id="BuyandGet1" class="hidden border-t border-erp-line pt-3">
                    <h4 id="cust_buy_heading" class="mb-2 text-sm font-bold text-erp-ink"></h4>
                    <div id="buys" class="grid grid-cols-2 gap-3 overflow-y-auto sm:grid-cols-3" style="max-height: 280px;"></div>
                </div>

                <div id="BuyandGet2" class="hidden border-t border-erp-line pt-3">
                    <h4 id="cust_get_heading" class="mb-2 text-sm font-bold text-erp-ink"></h4>
                    <div id="gets" class="grid grid-cols-2 gap-3 overflow-y-auto sm:grid-cols-3" style="max-height: 280px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="createSchedule-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="max-h-full w-full max-w-lg overflow-y-auto rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Create Schedule</h3>
                <button type="button" class="text-erp-mute hover:text-erp-ink" onclick="closeModal('createSchedule-modal')">Close</button>
            </div>
            <div class="space-y-4 px-5 py-5">
                <input type="hidden" id="discount_id_md">
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Start Date</span>
                        <input type="date" id="startdate" value="{{ date('Y-m-d') }}" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Start Time</span>
                        <input type="time" id="starttime" value="{{ date('H:i') }}" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>
                </div>

                <label class="inline-flex items-center gap-2 text-sm font-bold text-erp-text">
                    <input type="checkbox" id="chkEndDate" class="rounded border-erp-line text-erp focus:ring-erp">
                    End Date
                </label>

                <div id="divEndSection" class="hidden grid gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">End Date</span>
                        <input type="date" id="enddate" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">End Time</span>
                        <input type="time" id="endtime" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>
                </div>
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" onclick="re_active_discount()" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Save Changes</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const csrfToken = '{{ csrf_token() }}';

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        document.getElementById('discountSearch').addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('.discount-row').forEach(row => {
                row.classList.toggle('hidden', term !== '' && !row.dataset.search.includes(term));
            });
        });

        document.getElementById('headCheckbox').addEventListener('click', function () {
            document.querySelectorAll('.child-chkbx').forEach(chk => chk.checked = this.checked);
            document.getElementById('removeAllBtn').classList.toggle('hidden', !this.checked);
        });

        document.getElementById('removeAllBtn').addEventListener('click', function () {
            if (!confirm('You want to remove all discount!')) return;

            const ids = Array.from(document.querySelectorAll('.child-chkbx:checked')).map(c => c.value);
            const mode = this.dataset.mode;

            fetch("{{ url('/remove-discount') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id: ids, mode: 'removeAll' })
            })
                .then(res => res.text())
                .then(resp => {
                    if (resp == 1) {
                        window.location = "{{ url('/get-discount') }}" + (mode == 2 ? '/in-active' : '');
                    }
                });
        });

        function switchMode(discId, status, voucher, element) {
            let statusName, value;
            if (element.checked) {
                statusName = 'Active'; value = 1;
            } else {
                statusName = 'In-Active'; value = 2;
            }

            if (!confirm('You want to ' + statusName + ' this discount voucher ' + voucher + '!')) {
                element.checked = status == 1;
                return;
            }

            if (status == 1) {
                fetch("{{ url('/remove-discount') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ id: discId, mode: value })
                })
                    .then(res => res.text())
                    .then(resp => {
                        if (resp == 1) {
                            alert('Campaign ' + statusName + ' successfully.');
                            window.location = "{{ url('/get-discount') }}";
                        }
                    });
            } else {
                element.checked = false;
                document.getElementById('discount_id_md').value = discId;
                openModal('createSchedule-modal');
            }
        }

        function re_active_discount() {
            fetch("{{ route('reactiveDiscount') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({
                    id: document.getElementById('discount_id_md').value,
                    startdate: document.getElementById('startdate').value,
                    startime: document.getElementById('starttime').value,
                    endate: document.getElementById('enddate').value,
                    endtime: document.getElementById('endtime').value,
                })
            })
                .then(res => res.text())
                .then(resp => {
                    if (resp == 1) {
                        alert('Success!');
                        window.location = "{{ url('/get-discount') }}";
                    }
                });
        }

        function modelcall(id) {
            fetch("{{ url('get-discount-info') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id })
            })
                .then(res => res.json())
                .then(r => {
                    const info = r[0];
                    document.getElementById('discount_code').textContent = info.discount_code;
                    document.getElementById('discount_type').textContent = info.type_name;
                    document.getElementById('discount_applies').textContent = info.applies_name;
                    document.getElementById('discount_starts').textContent = info.starts;
                    document.getElementById('discount_ends').textContent = info.ends;
                    document.getElementById('discount_status_class').textContent = info.status;
                    document.getElementById('applyTo').classList.toggle('hidden', info.applies_name == null);

                    document.getElementById('CatandPro').innerHTML = '';
                    document.getElementById('buys').innerHTML = '';
                    document.getElementById('gets').innerHTML = '';
                    document.getElementById('CatandPro').classList.add('hidden');
                    document.getElementById('BuyandGet1').classList.add('hidden');
                    document.getElementById('BuyandGet2').classList.add('hidden');

                    const card = (image, label) => `
                        <div class="rounded-lg border border-erp-line bg-white p-2 text-center shadow-sm">
                            <img src="${image}" class="mx-auto h-20 w-full rounded-lg object-cover" alt="${label}">
                            <div class="mt-2 text-xs font-semibold text-erp-ink">${label}</div>
                        </div>
                    `;

                    if (info.applies_name == 'By Categories') {
                        document.getElementById('CatandPro').classList.remove('hidden');
                        fetch("{{ url('get-discount-categories') }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify({ id })
                        })
                            .then(res => res.json())
                            .then(rows => {
                                document.getElementById('CatandPro').innerHTML = rows.map(v => card("{{ asset('storage/images/task/task-u2.jpg') }}", v.department_name)).join('');
                            });
                    } else if (info.applies_name == 'By Products') {
                        document.getElementById('CatandPro').classList.remove('hidden');
                        fetch("{{ url('get-discount-products') }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify({ id })
                        })
                            .then(res => res.json())
                            .then(rows => {
                                document.getElementById('CatandPro').innerHTML = rows.map(v => card("{{ asset('assets/images/products/') }}/" + v.image, v.product_name)).join('');
                            });
                    } else if (info.applies_name == 'Entire Order') {
                        document.getElementById('BuyandGet1').classList.add('hidden');
                        document.getElementById('BuyandGet2').classList.add('hidden');
                    } else {
                        document.getElementById('BuyandGet1').classList.remove('hidden');
                        document.getElementById('BuyandGet2').classList.remove('hidden');

                        fetch("{{ url('get-customer-buys') }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify({ id })
                        })
                            .then(res => res.json())
                            .then(rows => {
                                document.getElementById('cust_buy_heading').textContent = 'Customer Buy ' + rows[0].buy_qty + ' Qty of Following';
                                document.getElementById('buys').innerHTML = rows.map(v => card("{{ asset('assets/images/products/') }}/" + v.image, v.product_name)).join('');
                            });

                        fetch("{{ url('get-customer-gets') }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify({ id })
                        })
                            .then(res => res.json())
                            .then(rows => {
                                document.getElementById('cust_get_heading').textContent = 'Customer Gets ' + rows[0].get_qty + ' Qty of Following';
                                document.getElementById('gets').innerHTML = rows.map(v => card("{{ asset('storage/images/products/') }}/" + v.image, v.product_name)).join('');
                            });
                    }

                    openModal('discount-view-modal');
                });
        }

        function discountDelete(id, md) {
            if (!confirm('This campaign will be deleted!')) return;

            fetch("{{ url('/remove-discount') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id, mode: 'delete' })
            })
                .then(res => res.text())
                .then(resp => {
                    if (resp == 1) {
                        alert('Campaign inactive successfully.');
                        window.location = "{{ url('/get-discount') }}" + (md == 2 ? '/in-active' : '');
                    }
                });
        }

        document.getElementById('chkEndDate').addEventListener('change', function () {
            document.getElementById('divEndSection').classList.toggle('hidden', !this.checked);
        });
    </script>
@endpush
