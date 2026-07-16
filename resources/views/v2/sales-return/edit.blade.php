@extends('layouts.master-tailwind')

@section('title', 'Sales Returns — Edit')
@section('page_title')
    Sales Return #{{ $order->id }}
@endsection
@section('page_subtitle', 'Delete line items; totals and tax recalculate automatically.')

@section('content')
    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Sales Return #{{ $order->id }}</h2>
                    <p class="mt-1 text-sm text-erp-mute">
                        Original <span class="font-semibold text-erp-text">order_ref</span>: {{ $order->order_ref ?? '—' }}
                        · {{ $order->date }} {{ $order->time }}
                        · {{ optional($order->branchrelation)->branch_name ?? $order->branch }}
                    </p>
                </div>
                <a href="{{ route('sales-returns.fbr') }}"
                   class="rounded-lg border border-erp bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">
                    Send to FBR
                </a>
            </div>

            <div class="space-y-5 p-5">
                @include('v2.sales-return._nav')

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4" id="totalsBox">
                    <div class="rounded-lg border border-erp-line bg-erp-soft px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Actual Amount</div>
                        <div class="mt-1 text-lg font-bold text-erp-ink" id="actual_amount">{{ number_format((float) $order->actual_amount, 2) }}</div>
                    </div>
                    <div class="rounded-lg border border-erp-line bg-erp-soft px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Sales Tax</div>
                        <div class="mt-1 text-lg font-bold text-erp-ink" id="sales_tax_amount">{{ number_format((float) optional($order->orderAccountSub)->sales_tax_amount, 2) }}</div>
                    </div>
                    <div class="rounded-lg border border-erp-line bg-erp-soft px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Discount</div>
                        <div class="mt-1 text-lg font-bold text-erp-ink" id="discount_amount">{{ number_format((float) optional($order->orderAccountSub)->discount_amount, 2) }}</div>
                    </div>
                    <div class="rounded-lg border border-erp-line bg-erp-soft px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Total Amount</div>
                        <div class="mt-1 text-lg font-bold text-erp-ink" id="total_amount">{{ number_format((float) $order->total_amount, 2) }}</div>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-lg border border-erp-line">
                    <table class="min-w-full divide-y divide-erp-line text-sm" id="itemsTable">
                        <thead class="bg-erp-soft">
                            <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                                <th class="px-3 py-3">#</th>
                                <th class="px-3 py-3">Item</th>
                                <th class="px-3 py-3">Qty</th>
                                <th class="px-3 py-3">Price</th>
                                <th class="px-3 py-3">Tax</th>
                                <th class="px-3 py-3">Discount</th>
                                <th class="px-3 py-3">Line Total</th>
                                <th class="px-3 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-erp-line text-erp-text">
                            @forelse ($order->orderdetails as $index => $item)
                                <tr data-detail-id="{{ $item->receipt_detail_id }}">
                                    <td class="px-3 py-3">{{ $index + 1 }}</td>
                                    <td class="px-3 py-3 font-semibold">{{ $item->item_name ?: optional($item->inventory)->product_name }}</td>
                                    <td class="px-3 py-3">{{ $item->total_qty }}</td>
                                    <td class="px-3 py-3">{{ number_format((float) $item->item_price, 2) }}</td>
                                    <td class="px-3 py-3">{{ number_format((float) $item->taxamount, 2) }}</td>
                                    <td class="px-3 py-3">{{ number_format((float) $item->discount, 2) }}</td>
                                    <td class="px-3 py-3">{{ number_format((float) $item->total_amount, 2) }}</td>
                                    <td class="px-3 py-3">
                                        <button type="button"
                                                class="btn-delete-item rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100"
                                                data-id="{{ $item->receipt_detail_id }}">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyRow">
                                    <td colspan="8" class="px-3 py-8 text-center text-erp-mute">No line items left.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const csrf = '{{ csrf_token() }}';
    const deleteBase = '{{ url('sales-returns/items') }}';

    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.btn-delete-item');
        if (!btn) return;

        const detailId = btn.getAttribute('data-id');
        if (!confirm('Delete this line item and recalculate totals?')) return;

        btn.disabled = true;
        btn.textContent = 'Deleting...';

        try {
            const res = await fetch(deleteBase + '/' + detailId + '/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({})
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Delete failed');

            const row = document.querySelector('tr[data-detail-id="' + detailId + '"]');
            if (row) row.remove();

            const totals = (data.data && data.data.totals) ? data.data.totals : {};
            if (totals.actual_amount !== undefined) {
                document.getElementById('actual_amount').textContent = Number(totals.actual_amount).toFixed(2);
            }
            if (totals.sales_tax_amount !== undefined) {
                document.getElementById('sales_tax_amount').textContent = Number(totals.sales_tax_amount).toFixed(2);
            }
            if (totals.discount_amount !== undefined) {
                document.getElementById('discount_amount').textContent = Number(totals.discount_amount).toFixed(2);
            }
            if (totals.total_amount !== undefined) {
                document.getElementById('total_amount').textContent = Number(totals.total_amount).toFixed(2);
            }

            const tbody = document.querySelector('#itemsTable tbody');
            if (tbody && !tbody.querySelector('tr[data-detail-id]')) {
                tbody.innerHTML = '<tr id="emptyRow"><td colspan="8" class="px-3 py-8 text-center text-erp-mute">No line items left.</td></tr>';
            }
        } catch (err) {
            alert(err.message || 'Delete failed');
            btn.disabled = false;
            btn.textContent = 'Delete';
        }
    });
})();
</script>
@endpush
