@extends('layouts.master-tailwind')

@section('title', 'Purchase')
@section('page_title', 'Purchases')
@section('page_subtitle', 'Track purchase orders by workflow status and manage draft orders.')

@section('content')
    @php($purchaseStatuses = ['Draft', 'Placed', 'Received', 'Cancelled', 'Partially Return', 'Complete Return', 'Complete', 'Partially Received'])

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Selected Status</div>
                <div id="selectedStatusText" class="mt-4 text-2xl font-black text-erp-ink">Draft</div>
                <p class="mt-2 text-sm text-erp-mute">Current purchase order list</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Visible Rows</div>
                <div id="visibleRowsText" class="mt-4 text-3xl font-black text-erp-ink">0</div>
                <p class="mt-2 text-sm text-erp-mute">Loaded on this page</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Matches</div>
                <div id="totalRowsText" class="mt-4 text-3xl font-black text-erp-ink">0</div>
                <p class="mt-2 text-sm text-erp-mute">From server-side filter</p>
            </div>
            <a href="{{ route('add-purchase') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Purchase</div>
                    <p class="mt-2 text-sm text-white/75">Add a purchase order</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-erp-line px-5 py-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-base font-bold text-erp-ink">Purchases List</h2>
                        <p class="mt-1 text-sm text-erp-mute">Switch status tabs or search by PO, vendor, or branch.</p>
                    </div>
                    <input type="search" id="purchaseSearch" placeholder="Search purchases..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp lg:w-80">
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($purchaseStatuses as $status)
                        <button type="button" data-status="{{ $status }}" class="purchase-status rounded-lg border px-3 py-2 text-xs font-bold transition {{ $loop->first ? 'border-erp bg-erp text-white' : 'border-erp-line bg-white text-erp-text hover:border-erp hover:text-erp-dark' }}">{{ $status }}</button>
                    @endforeach
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Generation Date</th>
                            <th class="px-5 py-3 text-left font-bold">PO No</th>
                            <th class="px-5 py-3 text-left font-bold">Vendor</th>
                            <th class="px-5 py-3 text-left font-bold">Branch</th>
                            <th class="px-5 py-3 text-left font-bold">Delivery Date</th>
                            <th class="px-5 py-3 text-left font-bold">Payment Date</th>
                            <th class="px-5 py-3 text-right font-bold">Amount</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="purchaseRows" class="divide-y divide-slate-100">
                        <tr><td colspan="9" class="px-5 py-12 text-center text-erp-mute">Loading purchases...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col gap-3 border-t border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div id="purchaseStatus" class="text-sm font-semibold text-erp-mute"></div>
                <div class="flex gap-2">
                    <button type="button" id="prevPurchasePage" class="rounded-lg border border-erp-line px-3 py-2 text-sm font-semibold text-erp-text transition hover:border-erp hover:text-erp-dark">Previous</button>
                    <button type="button" id="nextPurchasePage" class="rounded-lg border border-erp-line px-3 py-2 text-sm font-semibold text-erp-text transition hover:border-erp hover:text-erp-dark">Next</button>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        let purchaseType = 'Draft';
        let purchasePage = 0;
        const purchaseLength = 10;
        let purchaseTotal = 0;
        let purchaseTimer;

        function statusClass(status) {
            if (['Placed', 'Complete'].includes(status)) return 'bg-emerald-50 text-emerald-700 ring-emerald-200';
            if (status === 'Received') return 'bg-sky-50 text-sky-700 ring-sky-200';
            if (status === 'Draft') return 'bg-slate-100 text-slate-700 ring-slate-200';
            return 'bg-rose-50 text-rose-700 ring-rose-200';
        }

        function renderActions(row) {
            let html = '';
            if (row.Status === 'Received') {
                html += `<a href="{{ url('grn-details') }}/${row.purchase_id}" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">GRN</a>`;
            }
            if (row.Status !== 'Draft') {
                html += `<a href="{{ url('view') }}/${row.purchase_id}" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">View</a>`;
            }
            if (row.Status === 'Draft') {
                html += `<a href="{{ url('edit') }}/${row.purchase_id}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</a>`;
                html += `<button type="button" onclick="deletePurchase(${row.purchase_id})" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>`;
            }
            return `<div class="flex justify-end gap-2">${html || '<span class="text-erp-mute">-</span>'}</div>`;
        }

        function loadPurchases() {
            const columns = ['order_date', 'po_no', 'Vendor', 'Branch', 'delivery_date', 'payment_date', 'Amount', 'Status', 'purchase_id'];
            const params = new URLSearchParams();
            params.set('draw', '1');
            params.set('type', purchaseType);
            params.set('start', String(purchasePage * purchaseLength));
            params.set('length', String(purchaseLength));
            params.set('search[value]', document.getElementById('purchaseSearch').value);
            params.set('order[0][column]', '0');
            params.set('order[0][dir]', 'desc');
            columns.forEach((column, index) => params.set(`columns[${index}][data]`, column));

            document.getElementById('purchaseRows').innerHTML = '<tr><td colspan="9" class="px-5 py-12 text-center text-erp-mute">Loading purchases...</td></tr>';

            fetch("{{ route('get-purchase') }}?" + params.toString())
                .then(response => response.json())
                .then(function (response) {
                    const rows = response.aaData || [];
                    purchaseTotal = Number(response.iTotalDisplayRecords || 0);
                    document.getElementById('visibleRowsText').textContent = rows.length;
                    document.getElementById('totalRowsText').textContent = purchaseTotal;
                    document.getElementById('selectedStatusText').textContent = purchaseType;
                    document.getElementById('purchaseStatus').textContent = purchaseTotal ? `Showing ${purchasePage * purchaseLength + 1} to ${purchasePage * purchaseLength + rows.length} of ${purchaseTotal}` : 'No purchases found.';

                    if (!rows.length) {
                        document.getElementById('purchaseRows').innerHTML = '<tr><td colspan="9" class="px-5 py-12 text-center text-erp-mute">No purchases found.</td></tr>';
                        return;
                    }

                    document.getElementById('purchaseRows').innerHTML = rows.map(row => `
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 text-erp-text">${row.order_date || '-'}</td>
                            <td class="px-5 py-4 font-bold text-erp-ink">${row.po_no || '-'}</td>
                            <td class="px-5 py-4 text-erp-text">${row.Vendor || '-'}</td>
                            <td class="px-5 py-4 text-erp-text">${row.Branch || '-'}</td>
                            <td class="px-5 py-4 text-erp-text">${row.delivery_date || '-'}</td>
                            <td class="px-5 py-4 text-erp-text">${row.payment_date || '-'}</td>
                            <td class="px-5 py-4 text-right font-bold text-erp-ink">${row.Amount || '-'}</td>
                            <td class="px-5 py-4"><span class="rounded-md px-2 py-1 text-xs font-bold ring-1 ${statusClass(row.Status)}">${row.Status || '-'}</span></td>
                            <td class="px-5 py-4">${renderActions(row)}</td>
                        </tr>
                    `).join('');
                })
                .catch(function () {
                    document.getElementById('purchaseRows').innerHTML = '<tr><td colspan="9" class="px-5 py-12 text-center text-rose-600">Unable to load purchases.</td></tr>';
                });
        }

        document.querySelectorAll('.purchase-status').forEach(function (button) {
            button.addEventListener('click', function () {
                purchaseType = this.dataset.status;
                purchasePage = 0;
                document.querySelectorAll('.purchase-status').forEach(btn => {
                    btn.className = 'purchase-status rounded-lg border border-erp-line bg-white px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark';
                });
                this.className = 'purchase-status rounded-lg border border-erp bg-erp px-3 py-2 text-xs font-bold text-white transition';
                loadPurchases();
            });
        });

        document.getElementById('purchaseSearch').addEventListener('input', function () {
            clearTimeout(purchaseTimer);
            purchaseTimer = setTimeout(function () {
                purchasePage = 0;
                loadPurchases();
            }, 350);
        });

        document.getElementById('prevPurchasePage').addEventListener('click', function () {
            if (purchasePage > 0) {
                purchasePage--;
                loadPurchases();
            }
        });

        document.getElementById('nextPurchasePage').addEventListener('click', function () {
            if ((purchasePage + 1) * purchaseLength < purchaseTotal) {
                purchasePage++;
                loadPurchases();
            }
        });

        function deletePurchase(id) {
            if (!confirm('Delete this purchase order?')) {
                return;
            }

            fetch("{{ url('/DeletePO') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ id: id })
            }).then(response => response.text()).then(function (response) {
                if (response.trim() === '1') {
                    loadPurchases();
                }
            });
        }

        loadPurchases();
    </script>
@endpush
