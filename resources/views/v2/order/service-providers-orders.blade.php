@extends('layouts.master-tailwind')

@section('title', 'Service Providers Orders')
@section('page_title', 'Service Provider Orders')
@section('page_subtitle', 'Filter delivery orders, assign drivers/vehicles, and track dispatch status.')

@section('content')
    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Service Provider Orders Details</h2>
                <a href="{{ url('web-orders-view') }}" class="text-sm font-bold text-erp-dark hover:text-erp">&larr; Back to list</a>
            </div>

            <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-4">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Receipt No</span>
                    <input type="text" id="receipt" placeholder="Receipt No" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">From Date</span>
                    <input type="date" id="fromdate" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">To Date</span>
                    <input type="date" id="todate" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Select Service Provider</span>
                    <select id="serviceprovider" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Service Provider</option>
                        @foreach($providers as $provider)
                            <option value="{{ $provider->id }}">{{ $provider->provider_name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" id="search" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Search</button>
            </div>
        </section>

        <section id="orderAssign" class="hidden rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Assign Orders</h2>
            </div>
            <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-4">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Drivers</span>
                    <select id="driver" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Driver</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Vehicles</span>
                    <select id="vehicle" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Loaders</span>
                    <select id="loader" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Loader</option>
                        @foreach($loaders as $loader)
                            <option value="{{ $loader->id }}">{{ $loader->fullname }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Checkers</span>
                    <select id="checker" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Checker</option>
                        @foreach($checkers as $checker)
                            <option value="{{ $checker->id }}">{{ $checker->fullname }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" id="assignOrders" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Assign</button>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap gap-2 border-b border-erp-line px-5 py-4">
                <input type="hidden" id="type" value="1">
                <button type="button" class="tab-link rounded-lg border border-erp bg-erp px-4 py-2 text-sm font-bold text-white" onclick="changeTab(this, '')">All</button>
                @foreach($status as $value)
                    <button type="button" class="tab-link rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark" onclick="changeTab(this, '{{ $value->order_status_id }}')">{{ $value->order_status_name }}</button>
                @endforeach
                <button type="button" class="tab-link rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark" onclick="changeTab(this, 'drivers')">Drivers</button>
            </div>

            <div id="tabledata" class="p-5"></div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const csrfToken = '{{ csrf_token() }}';
        let orderStatus = '';
        const loaderHtml = '<div class="px-5 py-6 text-center text-sm font-semibold text-erp-mute">Loading...</div>';

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        function getOrders() {
            const tabledata = document.getElementById('tabledata');
            tabledata.innerHTML = loaderHtml;

            const params = new URLSearchParams({
                receipt: document.getElementById('receipt').value,
                from: document.getElementById('fromdate').value,
                to: document.getElementById('todate').value,
                serviceprovider: document.getElementById('serviceprovider').value,
                status: orderStatus,
            });

            fetch("{{ url('/service-providers-orders') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken },
                body: params.toString()
            })
                .then(res => res.text())
                .then(html => { tabledata.innerHTML = html; });
        }
        getOrders();

        function getDriversOrders() {
            const tabledata = document.getElementById('tabledata');
            tabledata.innerHTML = loaderHtml;

            const params = new URLSearchParams({
                from: document.getElementById('fromdate').value,
                to: document.getElementById('todate').value,
            });

            fetch("{{ route('driver.assign') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken },
                body: params.toString()
            })
                .then(res => res.text())
                .then(html => { tabledata.innerHTML = html; });
        }

        document.getElementById('search').addEventListener('click', function () {
            if (orderStatus !== 'drivers') {
                getOrders();
            } else {
                getDriversOrders();
            }
        });

        function changeTab(el, type) {
            document.querySelectorAll('.tab-link').forEach(btn => {
                btn.classList.remove('border-erp', 'bg-erp', 'text-white');
                btn.classList.add('border-erp-line', 'text-erp-text');
            });
            el.classList.add('border-erp', 'bg-erp', 'text-white');
            el.classList.remove('border-erp-line', 'text-erp-text');

            orderStatus = type;
            if (type !== 'drivers') {
                getOrders();
            } else {
                getDriversOrders();
            }
        }

        function providerChange(id, dropdownid, receipt, oldserviceprovider) {
            const tabledata = document.getElementById('tabledata');
            tabledata.innerHTML = loaderHtml;

            fetch("{{ url('/update-service-providers') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id, serviceprovider: document.getElementById(dropdownid).value, receipt, oldserviceprovider })
            })
                .then(res => res.json())
                .then(result => {
                    alert(result.success == 1 ? 'Service Provider changed successfully' : 'Service Provider change failed');
                    getOrders();
                });
        }

        function statusChange(id, receipt) {
            const tabledata = document.getElementById('tabledata');
            tabledata.innerHTML = loaderHtml;

            fetch("{{ url('/update-order-status') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ status: document.getElementById(id).value, receipt })
            })
                .then(res => res.json())
                .then(result => {
                    alert(result.success == 1 ? 'Order Status changed successfully' : 'Order Status change failed');
                    getOrders();
                });
        }

        function showReceipt(receiptNo) {
            window.open("{{ url('print') }}/" + receiptNo);
        }

        function showItems(driverId, drivertime) {
            const tabledata = document.getElementById('tabledata');
            tabledata.innerHTML = loaderHtml;

            const params = new URLSearchParams({
                driverId,
                time: drivertime,
                from: document.getElementById('fromdate').value,
                to: document.getElementById('todate').value,
            });

            fetch("{{ route('driver.details') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken },
                body: params.toString()
            })
                .then(res => res.text())
                .then(html => { tabledata.innerHTML = html; });
        }

        document.getElementById('assignOrders').addEventListener('click', function () {
            const ids = Array.from(document.querySelectorAll('.chkbx:checked')).map(c => c.dataset.id);

            fetch("{{ route('sp.assign') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({
                    orders: ids,
                    driver: document.getElementById('driver').value,
                    vehicle: document.getElementById('vehicle').value,
                    loader: document.getElementById('loader').value,
                    checker: document.getElementById('checker').value,
                })
            })
                .then(res => res.json())
                .then(result => {
                    document.getElementById('orderAssign').classList.add('hidden');
                    alert(result.message);
                    if (result.status == 200) getOrders();
                });
        });

        document.getElementById('tabledata').addEventListener('change', function (event) {
            if (event.target.matches('.mainchk')) {
                const checked = event.target.checked;
                document.querySelectorAll('.chkbx').forEach(chk => chk.checked = checked);
                document.getElementById('orderAssign').classList.toggle('hidden', !checked);
            } else if (event.target.matches('.chkbx')) {
                const anyChecked = document.querySelectorAll('.chkbx:checked').length > 0;
                document.getElementById('orderAssign').classList.toggle('hidden', !anyChecked);
            }
        });

        function changeNarration(orderDetailsId, narration) {
            document.getElementById('modalReceiptDetailsId').value = orderDetailsId;
            document.getElementById('narration').value = narration;
            openModal('order-narration-modal');
        }

        function saveNarration(orderDetailsId, narration) {
            const label = document.getElementById('narration' + orderDetailsId);
            if (label) label.textContent = narration;
            closeModal('order-narration-modal');
            saveNarrationAndStatus(orderDetailsId, narration, '', '');
        }

        function orderStatusChange(selectId, orderDetailsId) {
            saveNarrationAndStatus(orderDetailsId, '', '', document.getElementById(selectId).value);
        }

        function paymentStatusChange(selectId, orderId) {
            saveNarrationAndStatus(orderId, '', document.getElementById(selectId).value, '');
        }

        function saveNarrationAndStatus(orderDetailsId, narration, paymentStatus, mainStatus) {
            fetch("{{ route('save.narration') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ receipt: orderDetailsId, narration, paymentStatus, mainStatus })
            })
                .then(res => res.json())
                .then(result => alert(result.message));
        }
    </script>
@endpush
