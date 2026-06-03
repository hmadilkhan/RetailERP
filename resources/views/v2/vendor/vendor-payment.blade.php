@extends('layouts.master-tailwind')

@section('title', 'Vendor Payables')
@section('page_title', 'Vendor Payables')
@section('page_subtitle', 'Review due vendor payments, filter by date/vendor, and update payment due dates.')

@section('content')
    @php($vendorCollection = collect($vendors ?? []))

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Vendors</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($vendorCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available in current company</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Mode</div>
                <div id="modeText" class="mt-4 text-2xl font-black text-erp-ink">All</div>
                <p class="mt-2 text-sm text-erp-mute">Current payable filter</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm sm:col-span-2">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Date Filter</div>
                <div id="filterText" class="mt-4 text-xl font-black text-erp-ink">No date filter</div>
                <p class="mt-2 text-sm text-erp-mute">Submit to apply custom date range.</p>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Vendor Due Payment Details</h2>
                <p class="mt-1 text-sm text-erp-mute">Filter payables and update due dates without leaving the page.</p>
            </div>
            <div class="space-y-4 p-5">
                <div class="flex flex-wrap gap-2">
                    <button type="button" data-mode="all" class="mode-tab rounded-lg border border-erp bg-erp px-3 py-2 text-xs font-bold text-white transition">All</button>
                    <button type="button" data-mode="today" class="mode-tab rounded-lg border border-erp-line bg-white px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Today</button>
                    <button type="button" data-mode="clear" class="mode-tab rounded-lg border border-erp-line bg-white px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Clear</button>
                </div>
                <div class="grid gap-4 md:grid-cols-12">
                    <label class="block md:col-span-4">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Vendor</span>
                        <select id="vendor" data-placeholder="Select Vendor" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option value="">Select Vendor</option>
                            @foreach($vendorCollection as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->vendor_name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block md:col-span-3">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">From Date</span>
                        <input type="date" id="from_date" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>
                    <label class="block md:col-span-3">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">To Date</span>
                        <input type="date" id="to_date" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>
                    <div class="flex items-end gap-2 md:col-span-2">
                        <button type="button" id="btnSubmit" class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Submit</button>
                        <button type="button" id="btnReset" class="h-10 rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Reset</button>
                    </div>
                </div>
            </div>
            <div id="tablesData" class="border-t border-erp-line"></div>
        </section>
    </div>

    <div id="dueDateModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-lg rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Change Due Date</h3>
                <button type="button" onclick="closeModal('dueDateModal')" class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute hover:bg-slate-100">x</button>
            </div>
            <div class="space-y-4 p-5">
                <input type="hidden" id="purchaseId">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Due Date</span>
                    <input type="date" id="duedate" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" onclick="updateDueDate()" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Change Date</button>
            </div>
        </div>
    </div>

    <div id="historyModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-2xl rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Vendor Payment History</h3>
                <button type="button" onclick="closeModal('historyModal')" class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute hover:bg-slate-100">x</button>
            </div>
            <div id="historyTable" class="max-h-[60vh] overflow-y-auto p-5"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let modes = 'all';

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        function getData(page = 1) {
            const params = new URLSearchParams({
                page: page,
                from: document.getElementById('from_date').value,
                to: document.getElementById('to_date').value,
                vendor: document.getElementById('vendor').value,
                mode: modes
            });

            document.getElementById('tablesData').innerHTML = '<div class="px-5 py-12 text-center text-erp-mute">Loading payables...</div>';
            fetch("{{ url('get-vendor-payments') }}?" + params.toString())
                .then(response => response.text())
                .then(html => document.getElementById('tablesData').innerHTML = html)
                .catch(() => document.getElementById('tablesData').innerHTML = '<div class="px-5 py-12 text-center text-rose-600">Unable to load payables.</div>');
        }

        document.getElementById('btnSubmit').addEventListener('click', function () {
            const from = document.getElementById('from_date').value;
            const to = document.getElementById('to_date').value;
            document.getElementById('filterText').textContent = from && to ? 'From: ' + from + ' To: ' + to : 'No date filter';
            getData(1);
        });

        document.getElementById('btnReset').addEventListener('click', function () {
            document.getElementById('from_date').value = '';
            document.getElementById('to_date').value = '';
            document.getElementById('vendor').value = '';
            if (window.jQuery) {
                jQuery('#vendor').val('').trigger('change.select2');
            }
            document.getElementById('filterText').textContent = 'No date filter';
            getData(1);
        });

        document.querySelectorAll('.mode-tab').forEach(function (button) {
            button.addEventListener('click', function () {
                modes = this.dataset.mode;
                document.getElementById('modeText').textContent = this.textContent;
                document.querySelectorAll('.mode-tab').forEach(btn => {
                    btn.className = 'mode-tab rounded-lg border border-erp-line bg-white px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark';
                });
                this.className = 'mode-tab rounded-lg border border-erp bg-erp px-3 py-2 text-xs font-bold text-white transition';
                getData(1);
            });
        });

        document.addEventListener('click', function (event) {
            const link = event.target.closest('#tablesData .pagination a');
            if (!link) return;
            event.preventDefault();
            const url = new URL(link.href);
            getData(url.searchParams.get('page') || 1);
        });

        function editDueDate(id, dueDate) {
            document.getElementById('duedate').value = dueDate || '';
            document.getElementById('purchaseId').value = id;
            openModal('dueDateModal');
        }

        function updateDueDate() {
            fetch("{{ url('update-vendor-payment-due-date') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ id: document.getElementById('purchaseId').value, date: document.getElementById('duedate').value })
            }).then(response => response.json()).then(function (response) {
                if (response.status == 200) {
                    closeModal('dueDateModal');
                    getData(1);
                }
            });
        }

        function viewPaymentHistory(id) {
            openModal('historyModal');
            document.getElementById('historyTable').innerHTML = '<div class="py-8 text-center text-erp-mute">Loading history...</div>';
            fetch("{{ url('vendor-payment-history') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ id: id })
            }).then(response => response.text()).then(html => document.getElementById('historyTable').innerHTML = html);
        }

        getData(1);
    </script>
@endpush
