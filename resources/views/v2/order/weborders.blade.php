@extends('layouts.master-tailwind')

@section('title', 'Orders')
@section('page_title', 'Web Orders')
@section('page_subtitle', 'Monitor incoming website orders, update status, and dispatch riders.')

@section('content')
    <audio id="orderSound" class="hidden" src="{{ asset('assets/sound/doorbell-sound.wav') }}"></audio>

    <div class="space-y-6">
        @if (Session::has('error'))
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-800 shadow-sm">{{ Session::get('error') }}</div>
        @endif

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Order Details</h2>
            </div>
            <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-4">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Order ID</span>
                    <input type="text" id="receipt" value="{{ request()->get('receipt') }}" placeholder="Order Id" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">From Date</span>
                    <input type="date" id="fromdate" value="{{ request()->get('first') }}" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">To Date</span>
                    <input type="date" id="todate" value="{{ request()->get('second') }}" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Select Customer</span>
                    @php $customerParam = request()->get('customer'); @endphp
                    <select id="customer" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Customer</option>
                        @foreach($customer as $value)
                            <option {{ $customerParam == $value->id ? 'selected' : '' }} value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Select Branch</span>
                    @php $branchFilter = request()->get('branch'); @endphp
                    <select id="branch" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Branch</option>
                        @foreach($branch as $value)
                            <option {{ $branchFilter == $value->branch_id ? 'selected' : '' }} value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                        @endforeach
                    </select>
                </label>
                @if ($website != null)
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Select Website</span>
                        @php $websiteIdParam = $websiteId ?? null; @endphp
                        <select id="website" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option value="">Select Website</option>
                            @foreach($website as $value)
                                <option {{ $websiteIdParam == $value->id ? 'selected' : '' }} value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" id="fetch" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Fetch</button>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table id="order_table" class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-5 py-3">Order#</th>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Time</th>
                            <th class="px-5 py-3">Branch</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Contact</th>
                            <th class="px-5 py-3">Total Amount</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line">
                        @forelse($totalorders as $value)
                            <tr id="tbRow{{ $value->id }}" class="{{ $value->isSeen == 1 ? 'bg-slate-50' : '' }}">
                                <td class="px-5 py-3 font-semibold text-erp-ink">{{ $value->url_orderid }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->date }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ date('h:i a', strtotime($value->time)) }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->branch }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->name }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->mobile }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ number_format($value->total_amount, 2) }}</td>
                                <td class="px-5 py-3">
                                    <select id="status{{ $value->id }}" class="h-9 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" onchange="statusChange('status{{ $value->id }}', '{{ $value->id }}', '{{ $value->receipt_no }}')">
                                        @foreach($orders as $val)
                                            <option {{ $val->order_status_name == $value->order_status_name ? 'selected' : '' }} value="{{ $val->order_status_id }}">{{ $val->order_status_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex justify-end items-center gap-3">
                                        <a href="{{ route('getWebsiteSaleReceiptDetails', $value->url_orderid) }}" class="font-bold {{ $value->isSeen == 1 ? 'text-erp-text' : 'text-erp-dark' }} hover:text-erp">View</a>
                                        <button type="button" onclick="showReceipt('{{ $value->receipt_no }}')" class="font-bold text-emerald-600 hover:text-emerald-700">Print</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-5 py-6 text-center text-sm text-erp-mute">No orders found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div id="order-status-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-md rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Select Rider</h3>
                <button type="button" class="text-erp-mute hover:text-erp-ink" onclick="closeModal('order-status-modal')">Close</button>
            </div>
            <div class="space-y-2 px-5 py-5">
                <input type="hidden" id="modalreceipt">
                <input type="hidden" id="modalreceiptno">
                <input type="hidden" id="modalstatus">
                <select id="rider" class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    @foreach($riders as $rider)
                        <option value="{{ $rider->id }}">{{ $rider->provider_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" id="btn_extra_item" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Save</button>
            </div>
        </div>
    </div>

    <div id="toast" class="fixed right-6 top-6 z-50 hidden rounded-lg border border-erp bg-white px-5 py-4 text-sm font-bold text-erp-ink shadow-menu"></div>
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

        function showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 4000);
        }

        let intervalCheckOrder = setInterval(checkOrders, 10000);
        @if (request()->has('page_mode'))
            clearInterval(intervalCheckOrder);
        @endif

        document.getElementById('fetch').addEventListener('click', function () {
            const fromdate = document.getElementById('fromdate').value;
            const todate = document.getElementById('todate').value;
            const receipt = document.getElementById('receipt').value;
            const branch = document.getElementById('branch').value;
            const customer = document.getElementById('customer').value;
            const website = document.getElementById('website') ? document.getElementById('website').value : '';

            if (fromdate || todate || receipt || branch || customer || website) {
                window.location = "{{ route('getWebOrderFilter') }}?first=" + fromdate + "&second=" + todate + "&customer=" + customer + "&receipt=" + receipt + "&branch=" + branch + "&website=" + website + "&page_mode=1";
            }
        });

        function statusChange(id, receipt, receiptNo) {
            const value = document.getElementById(id).value;
            if (value == 6) {
                document.getElementById('modalreceipt').value = receipt;
                document.getElementById('modalreceiptno').value = receiptNo;
                document.getElementById('modalstatus').value = value;
                openModal('order-status-modal');
            } else {
                statusChangeFromDB(receipt, value, receiptNo, 0);
            }
        }

        document.getElementById('btn_extra_item').addEventListener('click', function () {
            const receipt = document.getElementById('modalreceipt').value;
            const receiptno = document.getElementById('modalreceiptno').value;
            const status = document.getElementById('modalstatus').value;
            const rider = document.getElementById('rider').value;

            if (!rider) {
                alert('Please select Rider');
            } else {
                statusChangeFromDB(receipt, status, receiptno, rider);
            }
        });

        function statusChangeFromDB(receipt, status, receiptNo) {
            fetch("{{ url('/sales/change-website-order-status') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id: receipt, status })
            })
                .then(res => res.json())
                .then(() => {
                    orderSeen(receiptNo, receipt);
                    closeModal('order-status-modal');
                    showToast('Status changed successfully');
                });
        }

        function orderSeen(receiptNo, tbRowId) {
            fetch("{{ url('/order-seen') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ receiptNo })
            })
                .then(res => res.json())
                .then(resp => {
                    if (resp.status == true) {
                        const row = document.getElementById('tbRow' + tbRowId);
                        if (row) row.classList.remove('bg-erp-light/30');
                    }
                });
        }

        function showReceipt(receiptNo) {
            window.location = "{{ url('print') }}/" + receiptNo;
        }

        function numberWithCommas(number) {
            const parts = number.toString().split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            return parts.join('.');
        }

        function formatTimeToAMPM(timeString) {
            const timeParts = timeString.split(':');
            let hours = parseInt(timeParts[0]);
            const minutes = timeParts[1];
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            return hours + ':' + minutes + ' ' + ampm;
        }

        function checkOrders() {
            fetch("{{ route('checkwebsiteOrders') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({})
            })
                .then(res => res.json())
                .then(resp => {
                    if (!resp.status) return;

                    const orderLists = resp.orders;
                    const orderstatus = resp.orderStatus;
                    const tbody = document.querySelector('#order_table tbody');

                    orderLists.forEach(order => {
                        if (document.getElementById('tbRow' + order.id)) return;

                        showToast('You have a new order');
                        document.getElementById('orderSound').play();

                        let statusOptions = '';
                        orderstatus.forEach(s => {
                            statusOptions += `<option ${s.order_status_name == order.order_status_name ? 'selected' : ''} value="${s.order_status_id}">${s.order_status_name}</option>`;
                        });

                        const tr = document.createElement('tr');
                        tr.id = 'tbRow' + order.id;
                        tr.className = 'bg-erp-light/30';
                        tr.innerHTML = `
                            <td class="px-5 py-3 font-semibold text-erp-ink">${order.url_orderid}</td>
                            <td class="px-5 py-3 text-erp-text">${order.date}</td>
                            <td class="px-5 py-3 text-erp-text">${formatTimeToAMPM(order.time)}</td>
                            <td class="px-5 py-3 text-erp-text">${order.branch}</td>
                            <td class="px-5 py-3 text-erp-text">${order.name}</td>
                            <td class="px-5 py-3 text-erp-text">${order.mobile}</td>
                            <td class="px-5 py-3 text-erp-text">${numberWithCommas(order.total_amount)}</td>
                            <td class="px-5 py-3"><select id="status${order.id}" class="h-9 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" onchange="statusChange('status${order.id}','${order.id}','${order.receipt_no}')">${statusOptions}</select></td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex justify-end items-center gap-3">
                                    <a href="${location.origin}/sales/website-order-detail/${order.url_orderid}" class="font-bold text-erp-dark hover:text-erp">View</a>
                                    <button type="button" onclick="showReceipt('${order.receipt_no}')" class="font-bold text-emerald-600 hover:text-emerald-700">Print</button>
                                </div>
                            </td>
                        `;
                        tbody.prepend(tr);
                    });
                });
        }
    </script>
@endpush
