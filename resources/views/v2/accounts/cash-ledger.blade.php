@extends('layouts.master-tailwind')

@section('title', 'Cash Ledger')
@section('page_title', 'Cash Ledger')
@section('page_subtitle', 'Record cash debit/credit movement and review branch cash balance history.')

@section('content')
    @php
        $ledgerCollection = collect($cashLedger ?? []);
        $latestBalance = optional($ledgerCollection->first())->balance ?? 0;
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Ledger Rows</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($ledgerCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Cash entries in current branch</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Latest Balance</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($latestBalance, 2) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Based on latest ledger row</p>
            </div>
            <a href="{{ url('/view-accounts') }}" class="flex rounded-lg border border-erp-line bg-white p-5 text-erp-text shadow-sm transition hover:border-erp hover:text-erp-dark sm:col-span-2">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Back</div>
                    <div class="mt-4 text-xl font-black">Bank Accounts</div>
                    <p class="mt-2 text-sm text-erp-mute">Return to account list</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Cash Ledger Entry</h2>
                <p class="mt-1 text-sm text-erp-mute">Enter either debit or credit with narration.</p>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-12">
                <label class="block md:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Date</span>
                    <input type="date" name="date" id="date" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block md:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Debit</span>
                    <input type="number" name="debit" id="debit" value="0" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block md:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Credit</span>
                    <input type="number" name="credit" id="credit" value="0" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block md:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Narration</span>
                    <input type="text" name="narration" id="narration" placeholder="Enter Narration" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <div class="flex items-end md:col-span-2">
                    <button type="button" onclick="deposit()" class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Deposit Amount</button>
                </div>
            </div>
            <div id="cashStatus" class="border-t border-erp-line px-5 py-3 text-sm font-semibold text-erp-mute"></div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Cash Ledger Details</h2>
                    <p class="mt-1 text-sm text-erp-mute">Click narration to edit comments.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <input type="search" id="cashFilter" placeholder="Filter ledger..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-72">
                    <button type="button" onclick="generatePdf()" class="h-10 rounded-lg border border-rose-200 bg-rose-50 px-4 text-sm font-bold text-rose-700 transition hover:bg-rose-100">Print PDF</button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">S.No</th>
                            <th class="px-5 py-3 text-left font-bold">Date</th>
                            <th class="px-5 py-3 text-right font-bold">Debit</th>
                            <th class="px-5 py-3 text-right font-bold">Credit</th>
                            <th class="px-5 py-3 text-right font-bold">Balance</th>
                            <th class="px-5 py-3 text-left font-bold">Comments</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cashRows" class="divide-y divide-slate-100">
                        @forelse($ledgerCollection as $value)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-semibold text-erp-text">{{ $value->id }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->date }}</td>
                                <td class="px-5 py-4 text-right text-erp-text">{{ number_format($value->debit, 2) }}</td>
                                <td class="px-5 py-4 text-right text-erp-text">{{ number_format($value->credit, 2) }}</td>
                                <td class="px-5 py-4 text-right font-bold text-erp-ink">{{ number_format($value->balance, 2) }}</td>
                                <td class="px-5 py-4">
                                    <button type="button" onclick="editNarration(@js($value->id), @js($value->narration))" class="max-w-lg text-left font-semibold text-erp-dark hover:underline">{{ $value->narration }}</button>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button type="button" onclick="generateVoucher('{{ $value->id }}')" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Voucher</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-erp-mute">No cash ledger entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div id="narrationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-lg rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Edit Comments</h3>
                <button type="button" onclick="closeNarrationModal()" class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute hover:bg-slate-100">x</button>
            </div>
            <div class="space-y-4 p-5">
                <input type="hidden" id="editledgerid">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Comments</span>
                    <textarea id="editcomments" rows="4" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></textarea>
                </label>
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" onclick="updateNarration()" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Update Narration</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function setCashStatus(message, success = true) {
            const status = document.getElementById('cashStatus');
            status.textContent = message;
            status.className = 'border-t border-erp-line px-5 py-3 text-sm font-semibold ' + (success ? 'text-emerald-700' : 'text-rose-700');
        }

        function deposit() {
            const debit = Number(document.getElementById('debit').value || 0);
            const credit = Number(document.getElementById('credit').value || 0);

            if (!document.getElementById('date').value) {
                setCashStatus('Please select date.', false);
                return;
            }

            if (debit === 0 && credit === 0) {
                setCashStatus('Please enter debit or credit.', false);
                return;
            }

            if (!document.getElementById('narration').value.trim()) {
                setCashStatus('Please enter narration.', false);
                return;
            }

            fetch("{{ url('/cashLedgerDeposit') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({
                    narration: document.getElementById('narration').value,
                    date: document.getElementById('date').value,
                    debit: debit,
                    credit: credit
                })
            }).then(response => response.text()).then(function (response) {
                if (response.trim() === '1') {
                    window.location = "{{ url('cash-deposit') }}";
                } else {
                    setCashStatus('Cash ledger does not have sufficient balance for this transaction.', false);
                }
            }).catch(() => setCashStatus('Unable to deposit amount.', false));
        }

        function editNarration(id, narration) {
            document.getElementById('editcomments').value = narration;
            document.getElementById('editledgerid').value = id;
            document.getElementById('narrationModal').classList.remove('hidden');
            document.getElementById('narrationModal').classList.add('flex');
        }

        function closeNarrationModal() {
            document.getElementById('narrationModal').classList.add('hidden');
            document.getElementById('narrationModal').classList.remove('flex');
        }

        function updateNarration() {
            fetch("{{ url('/editledgernarration') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({
                    id: document.getElementById('editledgerid').value,
                    narration: document.getElementById('editcomments').value
                })
            }).then(response => response.text()).then(function (response) {
                if (response.trim() === '1') {
                    window.location = "{{ url('cash-deposit') }}";
                } else {
                    setCashStatus('Unable to update comments.', false);
                }
            }).catch(() => setCashStatus('Unable to update comments.', false));
        }

        function generatePdf() {
            window.location = "{{ url('cashledgerPDF') }}";
        }

        function generateVoucher(id) {
            window.location = "{{ url('cash_voucher') }}?id=" + id;
        }

        document.getElementById('cashFilter').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#cashRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        });
    </script>
@endpush
