@extends('layouts.master-tailwind')

@section('title', 'Vendor Payable')
@section('page_title', 'Vendor Payable')
@section('page_subtitle', 'Review payable balances by vendor and date range.')

@section('content')
    @php
        $vendorCollection = collect($vendors ?? []);
        $detailCollection = collect($details ?? []);
        $initialTotal = $detailCollection->sum(fn ($row) => ($row->balance ?? 0) * -1);
    @endphp

    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Filter</h2>
                <p class="mt-1 text-sm text-erp-mute">Select vendor and dates to refresh payable balances.</p>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-12">
                <label class="block md:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Vendor</span>
                    <select name="vendor" id="vendor" data-placeholder="Select Vendor" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Vendor</option>
                        @foreach($vendorCollection as $value)
                            <option value="{{ $value->id }}">{{ $value->vendor_name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block md:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">From Date</span>
                    <input class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" type="date" name="from" id="from">
                </label>
                <label class="block md:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">To Date</span>
                    <input class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" type="date" name="to" id="to">
                </label>
                <div class="flex items-end md:col-span-2">
                    <button type="button" id="btnSubmit" class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Search</button>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Rows</div>
                <div id="vendorRowsCount" class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($detailCollection->count()) }}</div>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Balance</div>
                <div id="vendorTotal" class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($initialTotal, 2) }}</div>
            </div>
            <button type="button" id="btnExcel" class="rounded-lg border border-emerald-200 bg-emerald-50 p-5 text-left text-emerald-800 shadow-sm transition hover:bg-emerald-100">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-700">Export</div>
                <div class="mt-4 text-xl font-black">Excel Sheet</div>
            </button>
            <button type="button" id="btnPdf" class="rounded-lg border border-rose-200 bg-rose-50 p-5 text-left text-rose-800 shadow-sm transition hover:bg-rose-100">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-rose-700">Export</div>
                <div class="mt-4 text-xl font-black">Print PDF</div>
            </button>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Vendor Balances</h2>
                    <p id="vendorStatus" class="mt-1 text-sm text-erp-mute">Current payable list.</p>
                </div>
                <input type="search" id="reportFilter" placeholder="Filter vendors..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">S.No</th>
                            <th class="px-5 py-3 text-left font-bold">Vendor Name</th>
                            <th class="px-5 py-3 text-left font-bold">Contact</th>
                            <th class="px-5 py-3 text-right font-bold">Balance</th>
                        </tr>
                    </thead>
                    <tbody id="vendorRows" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const vendorField = document.getElementById('vendor');
        const vendorFromField = document.getElementById('from');
        const vendorToField = document.getElementById('to');
        const vendorRowsElement = document.getElementById('vendorRows');
        const vendorRowsCountElement = document.getElementById('vendorRowsCount');
        const vendorTotalElement = document.getElementById('vendorTotal');
        const vendorStatusElement = document.getElementById('vendorStatus');
        const vendorReportFilter = document.getElementById('reportFilter');
        const initialVendors = @json($detailCollection->values());
        const amount = value => Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function filterVendorRows() {
            const term = vendorReportFilter.value.toLowerCase();
            document.querySelectorAll('#vendorRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        }

        function renderVendorRows(rows) {
            vendorRowsElement.innerHTML = '';
            let balance = 0;
            rows.forEach(function (row, index) {
                balance += Number(row.balance || 0);
                vendorRowsElement.insertAdjacentHTML('beforeend', `
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4 text-erp-text">${index + 1}</td>
                        <td class="px-5 py-4 font-semibold text-erp-ink">${row.vendor_name || ''}</td>
                        <td class="px-5 py-4 text-erp-text">${row.vendor_contact || ''}</td>
                        <td class="px-5 py-4 text-right font-bold text-erp-ink">${amount(Number(row.balance || 0) * -1)}</td>
                    </tr>
                `);
            });
            vendorRowsElement.insertAdjacentHTML('beforeend', `<tr class="bg-slate-50"><td class="px-5 py-4">${rows.length + 1}</td><td></td><td class="px-5 py-4 text-base font-black text-erp-ink">Total Balance</td><td class="px-5 py-4 text-right text-base font-black text-erp-ink">${amount(balance * -1)}</td></tr>`);
            vendorRowsCountElement.textContent = rows.length.toLocaleString();
            vendorTotalElement.textContent = amount(balance * -1);
            vendorStatusElement.textContent = rows.length ? 'Report loaded.' : 'No vendors found.';
            filterVendorRows();
        }

        function getdata() {
            vendorStatusElement.textContent = 'Loading report data...';
            fetch('{{ url("/vendor-report-filter") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ vendor: vendorField.value, first: vendorFromField.value, second: vendorToField.value })
            }).then(response => response.json()).then(renderVendorRows);
        }

        document.getElementById('btnSubmit').addEventListener('click', getdata);
        vendorReportFilter.addEventListener('input', filterVendorRows);
        document.getElementById('btnPdf').addEventListener('click', () => window.location = "{{ url('payable') }}?vendor=" + vendorField.value + "&first=" + vendorFromField.value + "&second=" + vendorToField.value);
        document.getElementById('btnExcel').addEventListener('click', () => window.location = "{{ url('export-vendor-ledger') }}?vendor=" + vendorField.value + "&first=" + vendorFromField.value + "&second=" + vendorToField.value);
        renderVendorRows(initialVendors);
    </script>
@endpush
