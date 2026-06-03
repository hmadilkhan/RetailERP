@extends('layouts.master-tailwind')

@section('title', 'Cheque Details')
@section('page_title', 'Cheque Details')
@section('page_subtitle', 'Record cheque and deposit slip payments, update status, and review cheque history.')

@section('content')
    @php
        $detailCollection = collect($details ?? []);
        $statusCollection = collect($status ?? []);
        $customerCollection = collect($customer ?? []);
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Cheques</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($detailCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Active cheque records</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Statuses</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($statusCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available clearance states</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm sm:col-span-2">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Customers</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($customerCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Eligible customer records</p>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Payments Via Cheque</h2>
                    <p class="mt-1 text-sm text-erp-mute">Create cheque or deposit slip records.</p>
                </div>
                <button type="button" id="toggleDetails" class="rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Collapse</button>
            </div>
            <form method="POST" enctype="multipart/form-data" action="{{ url('/insert-cheque') }}" id="detailsForm" class="grid gap-4 p-5 md:grid-cols-12">
                @csrf
                <label class="block md:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Cheque/Slip Number</span>
                    <input type="number" name="Chequenumber" id="Chequenumber" value="{{ old('Chequenumber') }}" placeholder="Enter number" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    @error('Chequenumber')<span class="mt-1 block text-xs font-semibold text-rose-600">Required field can not be blank.</span>@enderror
                </label>
                <label class="block md:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Cheque Date</span>
                    <input type="date" name="Chequedate" id="Chequedate" value="{{ old('Chequedate') }}" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    @error('Chequedate')<span class="mt-1 block text-xs font-semibold text-rose-600">Required field can not be blank.</span>@enderror
                </label>
                <label class="block md:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Amount</span>
                    <input type="number" name="amount" id="amount" min="1" value="{{ old('amount') }}" placeholder="Enter amount" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    @error('amount')<span class="mt-1 block text-xs font-semibold text-rose-600">Required field can not be blank.</span>@enderror
                </label>
                <label class="block md:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Cheque Type</span>
                    <select id="chtype" name="chtype" data-placeholder="Select Cheque Type" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Cheque Type</option>
                        <option value="cash">Cash</option>
                        <option value="Account Title">Account Title</option>
                    </select>
                </label>
                <label class="block md:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Bank Name</span>
                    <input type="text" name="bankname" id="bankname" placeholder="Bank Name" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block md:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Customer</span>
                    <select id="customer" name="customer" data-placeholder="Select Customer" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Customer</option>
                        @foreach($customerCollection as $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block md:col-span-12">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Narration</span>
                    <textarea name="narration" id="narration" rows="3" placeholder="Enter narration" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></textarea>
                </label>
                <div class="flex justify-end md:col-span-12">
                    <button type="submit" id="btnFinalSubmit" class="rounded-lg bg-erp px-5 py-2.5 text-sm font-bold text-white transition hover:bg-erp-dark">Submit Cheque Details</button>
                </div>
            </form>
            <div id="chequeStatusMessage" class="border-t border-erp-line px-5 py-3 text-sm font-semibold text-erp-mute"></div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Details</h2>
                    <p class="mt-1 text-sm text-erp-mute">Update clearance status or view cheque history.</p>
                </div>
                <input type="search" id="chequeFilter" placeholder="Filter cheques..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Date</th>
                            <th class="px-5 py-3 text-left font-bold">Cheque Number</th>
                            <th class="px-5 py-3 text-left font-bold">Type</th>
                            <th class="px-5 py-3 text-left font-bold">Bank</th>
                            <th class="px-5 py-3 text-right font-bold">Amount</th>
                            <th class="px-5 py-3 text-left font-bold">Customer</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="chequeRows" class="divide-y divide-slate-100">
                        @forelse($detailCollection as $value)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 text-erp-text">{{ $value->cheque_date }}</td>
                                <td class="px-5 py-4 font-bold text-erp-ink">{{ $value->cheque_number }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->payment_mode }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->bank_name }}</td>
                                <td class="px-5 py-4 text-right font-semibold text-erp-ink">{{ number_format($value->amount, 2) }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->name }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md bg-sky-50 px-2 py-1 text-xs font-bold text-sky-700 ring-1 ring-sky-200">{{ $value->status }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" onclick="showModal(@js($value->cheque_id), @js($value->cheque_number))" class="rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Clearance</button>
                                        <button type="button" onclick="viewCheque(@js($value->cheque_id))" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">History</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center text-erp-mute">No cheque records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div id="clearanceModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-2xl rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <div>
                    <h3 class="text-base font-bold text-erp-ink">Clearance Section</h3>
                    <p id="chequenumber" class="mt-1 text-sm font-semibold text-erp-mute"></p>
                </div>
                <button type="button" onclick="closeModal('clearanceModal')" class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute hover:bg-slate-100">x</button>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2">
                <input type="hidden" id="chequeid">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Date</span>
                    <input type="date" id="seconddate" name="seconddate" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="flex items-center justify-between gap-2 text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Cheque Status <button type="button" id="btn_status" class="text-erp-dark">Add</button></span>
                    <select id="chequestatus" name="chequestatus" data-placeholder="Select Status" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Status</option>
                        @foreach($statusCollection as $value)
                            <option value="{{ $value->id }}">{{ $value->status }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block md:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Narration</span>
                    <textarea id="secondnarration" name="secondnarration" rows="3" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></textarea>
                </label>
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" onclick="saveClearance()" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Save Details</button>
            </div>
        </div>
    </div>

    <div id="statusModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-md rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Create Status</h3>
                <button type="button" onclick="closeModal('statusModal')" class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute hover:bg-slate-100">x</button>
            </div>
            <div class="p-5">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Status Name</span>
                    <input type="text" name="statusname" id="statusname" placeholder="Enter Status Name" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" onclick="addStatus()" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Create Status</button>
            </div>
        </div>
    </div>

    <div id="viewModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-3xl rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Cheque History</h3>
                <button type="button" onclick="closeModal('viewModal')" class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute hover:bg-slate-100">x</button>
            </div>
            <div class="max-h-[60vh] overflow-y-auto p-5">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold">Date</th>
                            <th class="px-4 py-3 text-left font-bold">Cheque Number</th>
                            <th class="px-4 py-3 text-left font-bold">Status</th>
                            <th class="px-4 py-3 text-left font-bold">Narration</th>
                        </tr>
                    </thead>
                    <tbody id="tblcheque" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function setChequeStatus(message, success = true) {
            const status = document.getElementById('chequeStatusMessage');
            status.textContent = message;
            status.className = 'border-t border-erp-line px-5 py-3 text-sm font-semibold ' + (success ? 'text-emerald-700' : 'text-rose-700');
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        document.getElementById('toggleDetails').addEventListener('click', function () {
            document.getElementById('detailsForm').classList.toggle('hidden');
            this.textContent = document.getElementById('detailsForm').classList.contains('hidden') ? 'Expand' : 'Collapse';
        });

        function addStatus() {
            if (!document.getElementById('statusname').value.trim()) {
                setChequeStatus('Please enter status first.', false);
                return;
            }

            fetch("{{ url('/insert-chequeStatus') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ statusname: document.getElementById('statusname').value })
            }).then(response => response.text()).then(function (response) {
                if (response.trim() === '1') {
                    setChequeStatus('Status created successfully.');
                    closeModal('statusModal');
                } else {
                    setChequeStatus('Status already exists.', false);
                }
            }).catch(() => setChequeStatus('Unable to create status.', false));
        }

        function showModal(chequeid, chequenumber) {
            document.getElementById('chequeid').value = chequeid;
            document.getElementById('chequenumber').textContent = chequenumber;
            document.getElementById('seconddate').value = '';
            document.getElementById('secondnarration').value = '';
            if (window.jQuery) {
                jQuery('#chequestatus').val('').trigger('change.select2');
            }
            openModal('clearanceModal');
        }

        document.getElementById('btn_status').addEventListener('click', function () {
            document.getElementById('statusname').value = '';
            closeModal('clearanceModal');
            openModal('statusModal');
        });

        function saveClearance() {
            if (!document.getElementById('chequestatus').value) {
                setChequeStatus('Please select status first.', false);
                return;
            }

            fetch("{{ url('/save-chequeClearance') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({
                    chequeid: document.getElementById('chequeid').value,
                    narration: document.getElementById('secondnarration').value,
                    status: document.getElementById('chequestatus').value,
                    date: document.getElementById('seconddate').value
                })
            }).then(response => response.text()).then(function (response) {
                if (response.trim() === '1') {
                    setChequeStatus('Status changed successfully.');
                    closeModal('clearanceModal');
                } else {
                    setChequeStatus('Unable to save clearance.', false);
                }
            }).catch(() => setChequeStatus('Unable to save clearance.', false));
        }

        function viewCheque(chequeid) {
            fetch("{{ url('/getdetails-cheque') }}?chequeid=" + chequeid)
                .then(response => response.json())
                .then(function (result) {
                    const body = document.getElementById('tblcheque');
                    body.innerHTML = '';

                    if (!result.length) {
                        body.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-center text-erp-mute">No history found.</td></tr>';
                        return;
                    }

                    result.forEach(function (item) {
                        const row = document.createElement('tr');
                        row.innerHTML = '<td class="px-4 py-3"></td><td class="px-4 py-3"></td><td class="px-4 py-3"></td><td class="px-4 py-3"></td>';
                        row.children[0].textContent = item.date;
                        row.children[1].textContent = item.cheque_number;
                        row.children[2].textContent = item.status;
                        row.children[3].textContent = item.naraation || '';
                        body.appendChild(row);
                    });
                })
                .catch(() => setChequeStatus('Unable to load cheque history.', false));

            openModal('viewModal');
        }

        document.getElementById('chequeFilter').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#chequeRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        });
    </script>
@endpush
