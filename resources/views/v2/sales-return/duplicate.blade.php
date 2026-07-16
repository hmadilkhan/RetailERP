@extends('layouts.master-tailwind')

@section('title', 'Sales Returns — Duplicate')
@section('page_title', 'Sales Returns')
@section('page_subtitle', 'Duplicate POS receipts as sales returns (status 14) across all related tables.')

@section('content')
    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Duplicate Orders</h2>
                <p class="mt-1 text-sm text-erp-mute">
                    Clone receipts into status 14 with <span class="font-semibold text-erp-text">order_ref</span> pointing to the original ID.
                </p>
            </div>

            <div class="space-y-5 p-5">
                @include('v2.sales-return._nav')

                <div id="after-duplicate" class="rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">
                    After duplicating, use the <span class="font-bold">Edit</span> link in results to remove line items and recalculate totals before sending to FBR.
                </div>

                <form id="duplicateForm" class="space-y-4">
                    @csrf
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Order IDs (comma-separated)</span>
                        <textarea id="order_ids" name="order_ids" rows="4" required
                                  placeholder="e.g. 1,2,3,4"
                                  class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></textarea>
                    </label>
                    <div class="flex justify-end">
                        <button type="submit" id="btnDuplicate"
                                class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark disabled:opacity-60">
                            Duplicate Orders
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section id="resultArea" class="hidden rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Results</h2>
            </div>
            <div class="overflow-x-auto p-5">
                <table class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Original ID</th>
                            <th class="px-3 py-3">New ID</th>
                            <th class="px-3 py-3">Message</th>
                            <th class="px-3 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody id="resultBody" class="divide-y divide-erp-line text-erp-text"></tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const form = document.getElementById('duplicateForm');
    const btn = document.getElementById('btnDuplicate');
    const resultArea = document.getElementById('resultArea');
    const resultBody = document.getElementById('resultBody');
    const csrf = '{{ csrf_token() }}';
    const editBase = '{{ url('sales-returns') }}';

    function statusBadge(label) {
        const colors = {
            Duplicated: 'bg-emerald-50 text-emerald-800 ring-emerald-200',
            Skipped: 'bg-amber-50 text-amber-800 ring-amber-200',
            Failed: 'bg-rose-50 text-rose-800 ring-rose-200'
        };
        const cls = colors[label] || 'bg-slate-50 text-slate-700 ring-slate-200';
        return '<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset ' + cls + '">' + label + '</span>';
    }

    function buildRow(status, row, canEdit) {
        const newId = row.new_id || '';
        const editLink = (canEdit && newId)
            ? '<a href="' + editBase + '/' + newId + '/edit" class="font-bold text-erp-dark hover:text-erp">Edit</a>'
            : '<span class="text-erp-mute">—</span>';

        return '<tr>' +
            '<td class="px-3 py-3">' + statusBadge(status) + '</td>' +
            '<td class="px-3 py-3 font-semibold">' + (row.original_id || '') + '</td>' +
            '<td class="px-3 py-3 font-semibold">' + newId + '</td>' +
            '<td class="px-3 py-3 text-erp-mute">' + (row.message || '') + '</td>' +
            '<td class="px-3 py-3">' + editLink + '</td>' +
            '</tr>';
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        btn.disabled = true;
        btn.textContent = 'Duplicating...';

        try {
            const res = await fetch('{{ route('sales-returns.duplicate.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ order_ids: document.getElementById('order_ids').value })
            });

            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.message || 'Request failed');
            }

            const rows = [];
            const payload = data.data || {};
            (payload.duplicated || []).forEach(function (row) { rows.push(buildRow('Duplicated', row, true)); });
            (payload.skipped || []).forEach(function (row) { rows.push(buildRow('Skipped', row, !!row.new_id)); });
            (payload.failed || []).forEach(function (row) { rows.push(buildRow('Failed', row, false)); });

            resultBody.innerHTML = rows.length
                ? rows.join('')
                : '<tr><td colspan="5" class="px-3 py-6 text-center text-erp-mute">No results</td></tr>';
            resultArea.classList.remove('hidden');
        } catch (err) {
            alert(err.message || 'Request failed');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Duplicate Orders';
        }
    });
})();
</script>
@endpush
