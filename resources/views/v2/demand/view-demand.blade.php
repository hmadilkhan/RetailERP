@extends('layouts.master-tailwind')

@section('title', 'Demand')
@section('page_title', 'Demand List')
@section('page_subtitle', 'Review demand orders created by the current branch.')

@section('content')
    @php($demandCollection = collect($demands ?? []))

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Demands</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($demandCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Total demand orders</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Drafts</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($demandCollection->where('name', 'Draft')->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Editable orders</p>
            </div>
            <a href="{{ url('create-demand') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark sm:col-span-2">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Demand Order</div>
                    <p class="mt-2 text-sm text-white/75">Start a new branch demand</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Demand Orders</h2>
                    <p class="mt-1 text-sm text-erp-mute">View, edit drafts, or remove draft demand orders.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <input type="search" id="demandFilter" placeholder="Filter demands..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
                    <button type="button" id="btn_removeall" class="hidden h-10 rounded-lg bg-rose-600 px-4 text-sm font-bold text-white transition hover:bg-rose-700">Remove</button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold"><input type="checkbox" class="mainchk rounded border-erp-line text-erp focus:ring-erp"></th>
                            <th class="px-5 py-3 text-left font-bold">DO No</th>
                            <th class="px-5 py-3 text-left font-bold">Branch</th>
                            <th class="px-5 py-3 text-left font-bold">Generation Date</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                            <th class="px-5 py-3 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody id="demandRows" class="divide-y divide-slate-100">
                        @forelse($demandCollection as $value)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4"><input type="checkbox" class="chkbx rounded border-erp-line text-erp focus:ring-erp" data-id="{{ $value->demand_id }}"></td>
                                <td class="px-5 py-4 font-bold text-erp-ink">DO-{{ $value->demand_id }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->branch_name }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->date }}</td>
                                <td class="px-5 py-4">@include('v2.partials.status-badge', ['status' => $value->name])</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ url('/demand-details') }}/{{ Crypt::encrypt($value->demandid) }}" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">View</a>
                                        @if($value->name == 'Draft')
                                            <a href="{{ url('/edit-demand') }}/{{ Crypt::encrypt($value->demandid) }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</a>
                                            <button type="button" onclick="btn_remove({{ $value->demandid }})" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-12 text-center text-erp-mute">No demands found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const selectedDemandIds = [];
        const removeAllButton = document.getElementById('btn_removeall');

        function refreshBulkButton() {
            removeAllButton.classList.toggle('hidden', document.querySelectorAll('.chkbx:checked').length === 0);
        }

        function submitDemandRemove(ids) {
            fetch("{{ url('/all-demand-remove') }}", {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ demandid: ids, statusid: 6 })
            }).then(response => response.text()).then(result => {
                if (result.trim() === '1') window.location = "{{ url('/demand') }}";
                else alert('Demand not deleted.');
            });
        }

        function btn_remove(id) {
            if (confirm('Delete this demand?')) submitDemandRemove(id);
        }

        document.querySelector('.mainchk').addEventListener('change', function () {
            document.querySelectorAll('.chkbx').forEach(checkbox => checkbox.checked = this.checked);
            refreshBulkButton();
        });
        document.querySelectorAll('.chkbx').forEach(checkbox => checkbox.addEventListener('change', refreshBulkButton));
        removeAllButton.addEventListener('click', function () {
            const ids = Array.from(document.querySelectorAll('.chkbx:checked')).map(checkbox => checkbox.dataset.id);
            if (ids.length && confirm('Delete selected demands?')) submitDemandRemove(ids);
        });
        document.getElementById('demandFilter').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#demandRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        });
    </script>
@endpush
