@extends('layouts.master-tailwind')

@section('title', 'Sales Returns — FBR')
@section('page_title', 'Send Sales Returns to FBR')
@section('page_subtitle', 'Post returns as InvoiceType 3 with RefUSIN set to the original order_ref. One at a time or in bulk.')

@section('content')
    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">FBR Sales Return Posting</h2>
                <p class="mt-1 text-sm text-erp-mute">Negative amounts · InvoiceType 3 · RefUSIN = original receipt id</p>
            </div>

            <div class="space-y-5 p-5">
                @include('v2.sales-return._nav')

                <form method="GET" action="{{ route('sales-returns.fbr') }}" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">From</span>
                        <input type="date" name="from_date" value="{{ request('from_date') }}"
                               class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">To</span>
                        <input type="date" name="to_date" value="{{ request('to_date') }}"
                               class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">FBR Status</span>
                        <select name="fbr_status" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option value="">All</option>
                            <option value="pending" @selected(request('fbr_status') === 'pending')>Pending</option>
                            <option value="sent" @selected(request('fbr_status') === 'sent')>Sent</option>
                        </select>
                    </label>
                    <div class="flex items-end">
                        <button type="submit"
                                class="h-10 w-full rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                            Filter
                        </button>
                    </div>
                    <div class="flex items-end">
                        <button type="button" id="btnSendSelected"
                                class="h-10 w-full rounded-lg border border-erp bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark disabled:opacity-60">
                            Send Selected
                        </button>
                    </div>
                </form>

                <div id="fbrResult" class="hidden rounded-lg border px-4 py-3 text-sm"></div>

                <div class="overflow-x-auto rounded-lg border border-erp-line">
                    <table class="min-w-full divide-y divide-erp-line text-sm" id="fbrTable">
                        <thead class="bg-erp-soft">
                            <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                                <th class="px-3 py-3">
                                    <input type="checkbox" id="checkAll" class="rounded border-erp-line text-erp focus:ring-erp">
                                </th>
                                <th class="px-3 py-3">Return ID</th>
                                <th class="px-3 py-3">Original</th>
                                <th class="px-3 py-3">Date</th>
                                <th class="px-3 py-3">Branch</th>
                                <th class="px-3 py-3">Total</th>
                                <th class="px-3 py-3">Tax</th>
                                <th class="px-3 py-3">FBR Invoice</th>
                                <th class="px-3 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-erp-line text-erp-text">
                            @forelse ($returns as $row)
                                @php $hasFbr = !empty($row->fbrInvNumber); @endphp
                                <tr data-id="{{ $row->id }}">
                                    <td class="px-3 py-3">
                                        @if (!$hasFbr)
                                            <input type="checkbox" class="row-check rounded border-erp-line text-erp focus:ring-erp" value="{{ $row->id }}">
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 font-semibold">{{ $row->id }}</td>
                                    <td class="px-3 py-3">{{ $row->order_ref ?? '—' }}</td>
                                    <td class="px-3 py-3">{{ $row->date }} {{ $row->time }}</td>
                                    <td class="px-3 py-3">{{ optional($row->branchrelation)->branch_name ?? $row->branch }}</td>
                                    <td class="px-3 py-3">{{ number_format((float) $row->total_amount, 2) }}</td>
                                    <td class="px-3 py-3">{{ number_format((float) optional($row->orderAccountSub)->sales_tax_amount, 2) }}</td>
                                    <td class="fbr-inv px-3 py-3">
                                        @if ($hasFbr)
                                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-800 ring-1 ring-inset ring-emerald-200">{{ $row->fbrInvNumber }}</span>
                                        @else
                                            <span class="text-erp-mute">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('sales-returns.edit', $row->id) }}"
                                               class="rounded-lg border border-erp-line px-3 py-1.5 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                                                Edit
                                            </a>
                                            @if (!$hasFbr)
                                                <button type="button"
                                                        class="btn-send-one rounded-lg border border-erp bg-erp px-3 py-1.5 text-xs font-bold text-white transition hover:bg-erp-dark"
                                                        data-id="{{ $row->id }}">
                                                    Send
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-3 py-8 text-center text-erp-mute">No sales returns found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-2">
                    {{ $returns->links() }}
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const csrf = '{{ csrf_token() }}';
    const sendUrl = '{{ route('sales-returns.fbr.send') }}';
    const resultEl = document.getElementById('fbrResult');
    const btnSendSelected = document.getElementById('btnSendSelected');

    document.getElementById('checkAll').addEventListener('change', function () {
        document.querySelectorAll('.row-check').forEach(function (el) {
            el.checked = this.checked;
        }.bind(this));
    });

    async function sendIds(ids) {
        if (!ids.length) {
            alert('Select at least one return order.');
            return;
        }
        if (!confirm('Send ' + ids.length + ' order(s) to FBR as sales returns?')) return;

        resultEl.classList.add('hidden');
        btnSendSelected.disabled = true;
        btnSendSelected.textContent = 'Sending...';

        try {
            const res = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ ids: ids })
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'FBR send failed');

            let html = '<p class="font-bold">' + escapeHtml(data.message) + '</p><ul class="mt-2 space-y-2">';
            (data.data || []).forEach(function (r) {
                html += '<li>';
                html += '<div><span class="font-semibold">#' + r.order_id + ':</span> ' + escapeHtml(r.success ? ('OK — ' + (r.invoice_number || '')) : (r.message || 'Failed')) + '</div>';
                if (!r.success && r.error_details) {
                    html += '<pre class="mt-2 overflow-x-auto rounded-lg bg-white/70 p-3 text-xs text-rose-900">' + escapeHtml(JSON.stringify(r.error_details, null, 2)) + '</pre>';
                }
                html += '</li>';
                if (r.success && r.invoice_number) {
                    const tr = document.querySelector('tr[data-id="' + r.order_id + '"]');
                    if (tr) {
                        const inv = tr.querySelector('.fbr-inv');
                        if (inv) {
                            inv.innerHTML = '<span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-800 ring-1 ring-inset ring-emerald-200">' + r.invoice_number + '</span>';
                        }
                        const check = tr.querySelector('.row-check');
                        if (check) check.remove();
                        const sendBtn = tr.querySelector('.btn-send-one');
                        if (sendBtn) sendBtn.remove();
                    }
                }
            });
            html += '</ul>';
            resultEl.className = 'rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900';
            resultEl.innerHTML = html;
            resultEl.classList.remove('hidden');
        } catch (err) {
            resultEl.className = 'rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800';
            resultEl.textContent = err.message || 'FBR send failed';
            resultEl.classList.remove('hidden');
        } finally {
            btnSendSelected.disabled = false;
            btnSendSelected.textContent = 'Send Selected';
        }
    }

    btnSendSelected.addEventListener('click', function () {
        const ids = Array.from(document.querySelectorAll('.row-check:checked')).map(function (el) {
            return parseInt(el.value, 10);
        });
        sendIds(ids);
    });

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-send-one');
        if (!btn) return;
        sendIds([parseInt(btn.getAttribute('data-id'), 10)]);
    });
})();
</script>
@endpush
