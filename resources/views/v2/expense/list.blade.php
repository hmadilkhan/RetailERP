@extends('layouts.master-tailwind')

@section('title', 'Expense')
@section('page_title', 'Expenses')
@section('page_subtitle', 'Record expenses and review spend by category and date range.')

@section('content')
    @php
        $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $labelClass = 'text-xs font-bold uppercase tracking-[0.14em] text-erp-mute';
        $canCreate = session('roleId') == 2 || session('roleId') == 10;
        $canDelete = session('roleId') == 2 || session('roleId') == 1;
        $canSeeAction = session('roleId') == 2 || session('roleId') == 1 || session('roleId') == 10;
    @endphp

    <div class="space-y-6">
        @if ($canCreate)
            <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="border-b border-erp-line px-5 py-4">
                    <h2 class="text-base font-bold text-erp-ink" id="title-hcard">Create Expense</h2>
                </div>
                <form id="expenseform" class="space-y-4 p-5">
                    <input type="hidden" id="hidd_amt" value="">
                    <input type="hidden" id="hidd_id" value="0">

                    <div class="grid gap-4 sm:grid-cols-3">
                        <label class="block">
                            <span class="{{ $labelClass }}">Expense Category <button type="button" id="btn_exp_cat" class="ml-1 text-erp-dark underline">+ add</button></span>
                            <select id="exp_cat" class="{{ $inputClass }}">
                                <option value="">Select Category</option>
                            </select>
                        </label>
                        <label class="block">
                            <span class="{{ $labelClass }}">Date</span>
                            <input type="date" id="expensedate" class="{{ $inputClass }}">
                        </label>
                        <label class="block">
                            <span class="{{ $labelClass }}">Amount</span>
                            <input type="number" id="amount" value="0" min="1" class="{{ $inputClass }}">
                        </label>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-[1fr_auto]">
                        <label class="block">
                            <span class="{{ $labelClass }}">Narration</span>
                            <textarea id="details" rows="2" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></textarea>
                        </label>
                        <div class="flex items-end gap-2">
                            <button type="button" id="btn_clear" class="h-10 rounded-lg border border-rose-200 bg-rose-50 px-5 text-sm font-bold text-rose-700 transition hover:bg-rose-100">Clear</button>
                            <button type="button" id="btn_save" class="h-10 rounded-lg border border-erp bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">Save</button>
                        </div>
                    </div>
                </form>
            </section>
        @endif

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-4">
                <label class="block">
                    <span class="{{ $labelClass }}">Select Expense Category</span>
                    <select id="category" class="{{ $inputClass }}">
                        <option value="">Select Expense Category</option>
                        @if($cat)
                            @foreach($categories as $value)
                                <option value="{{ $value->exp_cat_id }}">{{ $value->expense_category }}</option>
                            @endforeach
                        @endif
                    </select>
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">From Date</span>
                    <input type="date" id="from" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">To Date</span>
                    <input type="date" id="to" class="{{ $inputClass }}">
                </label>
                <div class="flex items-end">
                    <button type="button" id="btnSubmit" onclick="getdata()" class="h-10 w-full rounded-lg border border-erp bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">Search</button>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Expenses</div>
                <div class="mt-4 text-3xl font-black text-erp-ink" id="totalexpenses">0</div>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Amount</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ session('currency') }} <span id="totalamount">0</span></div>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Expense List</h2>
            </div>
            <div class="overflow-x-auto">
                <table id="expensetb" class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Category</th>
                            <th class="px-5 py-3">Amount</th>
                            @if (session('company_id') == 134)
                                <th class="px-5 py-3">Platform Type</th>
                            @endif
                            <th class="px-5 py-3">Narration</th>
                            @if ($canSeeAction)
                                <th class="px-5 py-3 text-right">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line">
                        @forelse($expense as $value)
                            <tr>
                                <td class="px-5 py-3 text-erp-text">{{ $value->date }}</td>
                                <td class="px-5 py-3 font-semibold text-erp-ink">{{ $value->expense_category }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ number_format($value->net_amount, 2) }}</td>
                                @if (session('company_id') == 134)
                                    <td class="px-5 py-3 text-erp-text">{{ $value->platform_type == 1 ? 'WEB' : 'APP' }}</td>
                                @endif
                                <td class="px-5 py-3 text-erp-text">{{ $value->expense_details }}</td>
                                @if ($canDelete)
                                    <td class="px-5 py-3 text-right">
                                        <button type="button" onclick="deleteExpense({{ $value->exp_id }})" class="font-bold text-rose-600 hover:text-rose-700">Delete</button>
                                    </td>
                                @elseif ($canSeeAction)
                                    <td class="px-5 py-3"></td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-6 text-center text-sm text-erp-mute">No expenses found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div id="expense-cat-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-md rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Add Expense Category</h3>
                <button type="button" class="text-erp-mute hover:text-erp-ink" onclick="closeModal('expense-cat-modal')">Close</button>
            </div>
            <div class="space-y-2 px-5 py-5">
                <label class="block">
                    <span class="{{ $labelClass }}">Category Name</span>
                    <input type="text" id="expCatName" class="{{ $inputClass }}">
                    <span id="category_alert" class="mt-1 block text-xs font-semibold text-rose-600"></span>
                </label>
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" id="btn_depart" onclick="addExpCat()" class="rounded-lg border border-erp bg-erp px-5 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Add Expense Category</button>
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

        function loader() {
            document.getElementById('totalexpenses').textContent = '0';
            document.getElementById('totalamount').textContent = '0';
            document.getElementById('expensetb').querySelector('tbody').innerHTML = '<tr><td colspan="6" class="px-5 py-6 text-center text-sm text-erp-mute">Loading...</td></tr>';
            const btn = document.getElementById('btnSubmit');
            if (btn) btn.disabled = true;
        }

        function getdata() {
            loader();

            fetch("{{ url('/expense-details-filter') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({
                    category: document.getElementById('category').value,
                    first: document.getElementById('from').value,
                    second: document.getElementById('to').value,
                })
            })
                .then(res => res.json())
                .then(result => {
                    if (!result) return;
                    let balance = 0;
                    const tbody = document.getElementById('expensetb').querySelector('tbody');
                    tbody.innerHTML = '';

                    if (!result.length) {
                        tbody.innerHTML = '<tr><td colspan="6" class="px-5 py-6 text-center text-sm text-erp-mute">No expenses found.</td></tr>';
                    }

                    result.forEach(row => {
                        balance += row.net_amount;
                        if (parseInt(row.balance) > 0) balance += parseInt(row.balance);

                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="px-5 py-3 text-erp-text">${row.date}</td>
                            <td class="px-5 py-3 font-semibold text-erp-ink">${row.expense_category}</td>
                            <td class="px-5 py-3 text-erp-text">${row.net_amount}</td>
                            @if (session('company_id') == 134)
                                <td class="px-5 py-3 text-erp-text">${row.platform_type == 1 ? 'Web' : 'Other'}</td>
                            @endif
                            <td class="px-5 py-3 text-erp-text">${row.expense_details}</td>
                            @if ($canDelete)
                                <td class="px-5 py-3 text-right"><button type="button" class="font-bold text-rose-600 hover:text-rose-700" onclick="deleteExpense(${row.exp_id})">Delete</button></td>
                            @elseif ($canSeeAction)
                                <td class="px-5 py-3"></td>
                            @endif
                        `;
                        tbody.appendChild(tr);
                    });

                    document.getElementById('totalexpenses').textContent = result.length;
                    document.getElementById('totalamount').textContent = balance.toLocaleString();
                    const btn = document.getElementById('btnSubmit');
                    if (btn) btn.disabled = false;
                });
        }
        getdata();

        @if ($canCreate)
            document.getElementById('btn_clear').addEventListener('click', function () {
                document.getElementById('btn_save').textContent = 'Save';
                document.getElementById('expenseform').reset();
                document.getElementById('exp_cat').value = '';
                document.getElementById('hidd_id').value = '0';
            });

            document.getElementById('btn_exp_cat').addEventListener('click', function () {
                document.getElementById('expCatName').value = '';
                openModal('expense-cat-modal');
            });

            load_categories();
        @endif

        function load_categories() {
            fetch("{{ url('category') }}")
                .then(res => res.json())
                .then(resp => {
                    const select = document.getElementById('exp_cat');
                    select.innerHTML = '<option value="">Select Expense Category</option>';
                    resp.forEach(value => {
                        const opt = document.createElement('option');
                        opt.value = value.exp_cat_id;
                        opt.textContent = value.expense_category;
                        select.appendChild(opt);
                    });
                });
        }

        function addExpCat() {
            const category = document.getElementById('expCatName').value;
            document.getElementById('category_alert').textContent = '';

            if (!category) {
                document.getElementById('expCatName').focus();
                document.getElementById('category_alert').textContent = 'Category name is required.';
                return;
            }

            fetch("{{ route('exp_category.store') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ category })
            })
                .then(res => res.json())
                .then(r => {
                    if (r.state == 1) {
                        if (r.contrl) document.getElementById(r.contrl + '_alert').textContent = r.msg;
                        alert(r.msg);
                    } else if (r.state == 2) {
                        alert("Category '" + category + "' already exists!");
                    } else {
                        alert("Category '" + category + "' added successfully!");
                        load_categories();
                        document.getElementById('expCatName').value = '';
                        closeModal('expense-cat-modal');
                    }
                });
        }

        document.getElementById('amount')?.addEventListener('change', function () {
            document.getElementById('hidd_amt').value = parseFloat(this.value);
        });

        @if ($canCreate)
            document.getElementById('btn_save').addEventListener('click', function () {
                const expCat = document.getElementById('exp_cat').value;
                const details = document.getElementById('details').value;
                const amount = document.getElementById('amount').value;

                if (!expCat) { document.getElementById('exp_cat').focus(); return; }
                if (!details) { document.getElementById('details').focus(); return; }
                if (amount <= 0) {
                    document.getElementById('amount').focus();
                    document.getElementById('amount').value = '';
                    alert('Incorrect Amount');
                    return;
                }

                const hiddId = document.getElementById('hidd_id').value;
                const payload = {
                    hidd_id: hiddId,
                    exp_cat: expCat,
                    details,
                    amount,
                    expensedate: document.getElementById('expensedate').value,
                    hidd_amt: document.getElementById('hidd_amt').value,
                };

                fetch(hiddId == '0' ? "{{ route('expense.store') }}" : "{{ route('updatexp') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(payload)
                })
                    .then(res => res.json())
                    .then(r => {
                        if (r.state == 1) {
                            alert(r.msg);
                            window.location = "{{ route('expense.index') }}";
                        } else {
                            alert(r.msg);
                        }
                    });
            });
        @endif

        function deleteExpense(id) {
            if (!confirm('Do you want to delete this expense!')) return;

            fetch("{{ route('delete.expense') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id })
            })
                .then(res => res.json())
                .then(resp => {
                    if (resp.status == 200) {
                        alert('Expense Deleted');
                        window.location = "{{ url('/expense') }}";
                    }
                });
        }
    </script>
@endpush
