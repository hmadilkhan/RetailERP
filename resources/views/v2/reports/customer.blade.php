@extends('layouts.master-tailwind')

@section('title', 'Customer Receivable')
@section('page_title', 'Customer Receivable')
@section('page_subtitle', 'Review receivable balances by customer and payment type.')

@section('content')
    @php($customerCollection = collect($master ?? []))

    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Filter</h2>
                <p class="mt-1 text-sm text-erp-mute">Select customer and payment type to refresh receivable balances.</p>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-12">
                <label class="block md:col-span-5">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Customer</span>
                    <select name="customer" id="customer" data-placeholder="Select Customer" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Customer</option>
                        @foreach($customerCollection as $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block md:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Payment Type</span>
                    <select name="payment_type" id="payment_type" data-placeholder="Select Payment Type" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">All</option>
                        <option value="0">Walk In Customers</option>
                        <option value="1">Cash</option>
                        <option value="2">Credit</option>
                    </select>
                </label>
                <div class="flex items-end md:col-span-3">
                    <button type="button" id="btnSubmit" class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Search</button>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Customers</div>
                <div id="customerRowsCount" class="mt-4 text-3xl font-black text-erp-ink">0</div>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Balance</div>
                <div id="customerTotal" class="mt-4 text-3xl font-black text-erp-ink">0</div>
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
                    <h2 class="text-base font-bold text-erp-ink">Customer Balances</h2>
                    <p id="customerStatus" class="mt-1 text-sm text-erp-mute">Loading report data...</p>
                </div>
                <input type="search" id="reportFilter" placeholder="Filter customers..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Sr.</th>
                            <th class="px-5 py-3 text-left font-bold">Customer Name</th>
                            <th class="px-5 py-3 text-left font-bold">Contact</th>
                            <th class="px-5 py-3 text-left font-bold">Payment Type</th>
                            <th class="px-5 py-3 text-right font-bold">Balance</th>
                        </tr>
                    </thead>
                    <tbody id="customerRows" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const customerField = document.getElementById('customer');
        const customerPaymentTypeField = document.getElementById('payment_type');
        const customerRowsElement = document.getElementById('customerRows');
        const customerRowsCountElement = document.getElementById('customerRowsCount');
        const customerTotalElement = document.getElementById('customerTotal');
        const customerStatusElement = document.getElementById('customerStatus');
        const customerReportFilter = document.getElementById('reportFilter');
        const formatMoney = value => Number(value || 0).toLocaleString();
        const paymentLabel = value => Number(value) === 1 ? 'Cash' : (Number(value) === 2 ? 'Credit' : 'Walk In');

        function filterCustomerRows() {
            const term = customerReportFilter.value.toLowerCase();
            document.querySelectorAll('#customerRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        }

        function renderCustomerRows(rows) {
            customerRowsElement.innerHTML = '';
            let balance = 0;
            let shown = 0;
            rows.forEach(function (row) {
                if (Number(row.balance || 0) <= 0) return;
                shown++;
                balance += Number(row.balance || 0);
                customerRowsElement.insertAdjacentHTML('beforeend', `
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4 text-erp-text">${shown}</td>
                        <td class="px-5 py-4 font-semibold text-erp-ink">${row.name || ''}</td>
                        <td class="px-5 py-4 text-erp-text">${row.mobile || ''}</td>
                        <td class="px-5 py-4"><span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-bold text-erp-text">${paymentLabel(row.payment_type)}</span></td>
                        <td class="px-5 py-4 text-right font-bold text-erp-ink">${formatMoney(row.balance)}</td>
                    </tr>
                `);
            });
            customerRowsCountElement.textContent = shown.toLocaleString();
            customerTotalElement.textContent = formatMoney(balance);
            customerStatusElement.textContent = shown ? 'Report loaded.' : 'No customers found.';
            filterCustomerRows();
        }

        function getdata() {
            customerStatusElement.textContent = 'Loading report data...';
            fetch('{{ url("/customer-report-filter") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ customer: customerField.value, paymentType: customerPaymentTypeField.value })
            }).then(response => response.json()).then(renderCustomerRows);
        }

        document.getElementById('btnSubmit').addEventListener('click', getdata);
        customerReportFilter.addEventListener('input', filterCustomerRows);
        document.getElementById('btnPdf').addEventListener('click', () => window.location = "{{ url('receivable') }}?customer=" + customerField.value);
        document.getElementById('btnExcel').addEventListener('click', () => window.location = "{{ url('export-customer-ledger') }}?customer=" + customerField.value);
        getdata();
    </script>
@endpush
