@extends('layouts.master-tailwind')

@section('title', 'Expense Report')
@section('page_title', 'Expense Report')
@section('page_subtitle', 'Filter branch expenses by category and date range.')

@section('content')
    @php($categoryCollection = collect($category ?? []))

    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Filter</h2>
                <p class="mt-1 text-sm text-erp-mute">Select category and dates to refresh the report.</p>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-12">
                <label class="block md:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Expense Category</span>
                    <select name="category" id="category" data-placeholder="Select Expense Category" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Expense Category</option>
                        @foreach($categoryCollection as $value)
                            <option value="{{ $value->exp_cat_id }}">{{ $value->expense_category }}</option>
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
                <div class="flex items-end gap-2 md:col-span-2">
                    <button type="button" id="btnSubmit" class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Search</button>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Rows</div>
                <div id="expenseRowsCount" class="mt-4 text-3xl font-black text-erp-ink">0</div>
                <p class="mt-2 text-sm text-erp-mute">Matching expenses</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Balance</div>
                <div id="expenseTotal" class="mt-4 text-3xl font-black text-erp-ink">0</div>
                <p class="mt-2 text-sm text-erp-mute">Filtered amount</p>
            </div>
            <button type="button" id="btnExcel" class="rounded-lg border border-emerald-200 bg-emerald-50 p-5 text-left text-emerald-800 shadow-sm transition hover:bg-emerald-100 sm:col-span-1">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-700">Export</div>
                <div class="mt-4 text-xl font-black">Excel Sheet</div>
            </button>
            <button type="button" id="btnPdf" class="rounded-lg border border-rose-200 bg-rose-50 p-5 text-left text-rose-800 shadow-sm transition hover:bg-rose-100 sm:col-span-1">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-rose-700">Export</div>
                <div class="mt-4 text-xl font-black">Print PDF</div>
            </button>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Expense Details</h2>
                    <p id="expenseStatus" class="mt-1 text-sm text-erp-mute">Loading report data...</p>
                </div>
                <input type="search" id="reportFilter" placeholder="Filter rows..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">S.No</th>
                            <th class="px-5 py-3 text-left font-bold">Category</th>
                            <th class="px-5 py-3 text-left font-bold">Details</th>
                            <th class="px-5 py-3 text-right font-bold">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="expenseRows" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const expenseCategoryField = document.getElementById('category');
        const expenseFromField = document.getElementById('from');
        const expenseToField = document.getElementById('to');
        const formatAmount = value => Number(value || 0).toLocaleString();

        function applyExpenseFilter() {
            const term = document.getElementById('reportFilter').value.toLowerCase();
            document.querySelectorAll('#expenseRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        }

        function renderExpenseRows(rows) {
            const tbody = document.getElementById('expenseRows');
            tbody.innerHTML = '';
            let balance = 0;
            rows.forEach(function (row, index) {
                balance += Number(row.net_amount || 0) > 0 ? Number(row.net_amount || 0) : 0;
                tbody.insertAdjacentHTML('beforeend', `
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4 text-erp-text">${index + 1}</td>
                        <td class="px-5 py-4 font-semibold text-erp-ink">${row.expense_category || ''}</td>
                        <td class="px-5 py-4 text-erp-text">${row.expense_details || ''}</td>
                        <td class="px-5 py-4 text-right font-bold text-erp-ink">${formatAmount(row.net_amount)}</td>
                    </tr>
                `);
            });
            tbody.insertAdjacentHTML('beforeend', `
                <tr class="bg-slate-50">
                    <td class="px-5 py-4 text-erp-mute">${rows.length + 1}</td>
                    <td class="px-5 py-4"></td>
                    <td class="px-5 py-4 text-base font-black text-erp-ink">Total Balance</td>
                    <td class="px-5 py-4 text-right text-base font-black text-erp-ink">${formatAmount(balance)}</td>
                </tr>
            `);
            document.getElementById('expenseRowsCount').textContent = rows.length.toLocaleString();
            document.getElementById('expenseTotal').textContent = formatAmount(balance);
            document.getElementById('expenseStatus').textContent = rows.length ? 'Report loaded.' : 'No expenses found.';
            applyExpenseFilter();
        }

        function getdata() {
            document.getElementById('expenseStatus').textContent = 'Loading report data...';
            fetch('{{ url("/expense-report-details") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ category: expenseCategoryField.value, first: expenseFromField.value, second: expenseToField.value })
            }).then(response => response.json()).then(renderExpenseRows);
        }

        document.getElementById('btnSubmit').addEventListener('click', getdata);
        document.getElementById('reportFilter').addEventListener('input', applyExpenseFilter);
        document.getElementById('btnExcel').addEventListener('click', () => alert('Work in process'));
        document.getElementById('btnPdf').addEventListener('click', () => {
            window.location = "{{ url('expense-report-pdf') }}?category=" + expenseCategoryField.value + "&first=" + expenseFromField.value + "&second=" + expenseToField.value;
        });
        getdata();
    </script>
@endpush
