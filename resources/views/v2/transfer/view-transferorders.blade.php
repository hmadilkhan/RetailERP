@extends('layouts.master-tailwind')

@section('title', 'Transfer Order')
@section('page_title', 'Direct Transfer Order List')
@section('page_subtitle', 'Create, review, edit, or print direct transfer orders.')

@section('content')
    @php($transferCollection = collect($gettransfer ?? []))

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Transfer Orders</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($transferCollection->count()) }}</div>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Delivered</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($transferCollection->where('name', 'Delivered')->count()) }}</div>
            </div>
            <a href="{{ url('create-transferorder') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark sm:col-span-2">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Transfer Order</div>
                    <p class="mt-2 text-sm text-white/75">Create a direct transfer</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Direct Transfer Orders</h2>
                    <p class="mt-1 text-sm text-erp-mute">Review transfer status and actions.</p>
                </div>
                <input type="search" id="directTransferFilter" placeholder="Filter transfer orders..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Transfer Order No.</th>
                            <th class="px-5 py-3 text-left font-bold">Transfer From Branch</th>
                            <th class="px-5 py-3 text-left font-bold">Destination To Branch</th>
                            <th class="px-5 py-3 text-left font-bold">Generation Date</th>
                            <th class="px-5 py-3 text-left font-bold">Created By</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                            <th class="px-5 py-3 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody id="directTransferRows" class="divide-y divide-slate-100">
                        @forelse($transferCollection as $value)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-bold text-erp-ink">TO-{{ $value->transfer_No }}</td>
                                <td class="px-5 py-4 text-erp-text">Head Office</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->branch_name }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->date }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->fullname }}</td>
                                <td class="px-5 py-4">@include('v2.partials.status-badge', ['status' => $value->name ?? $value->status_name ?? ''])</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" onclick="view('{{ $value->transfer_id }}')" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">View</button>
                                        @if($value->name != 'Delivered')
                                            <button type="button" onclick="edit({{ $value->transfer_id }})" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</button>
                                            <button type="button" onclick="reject({{ $value->transfer_id }})" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                        @else
                                            <a target="_blank" href="{{ url('direct-transfer-report', $value->transfer_id) }}" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-100">Print</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-12 text-center text-erp-mute">No transfer orders found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function edit(id) { window.location = "{{ url('/edit_trf_details') }}/" + id; }
        function view(id) { window.location = "{{ url('/get_trf_details') }}/" + id; }
        function reject(id) {
            if (!confirm('Delete this transfer order?')) return;
            fetch("{{ url('/trforder_delete') }}?trfid=" + encodeURIComponent(id))
                .then(response => response.text())
                .then(result => {
                    if (result.trim() === '1') window.location = "{{ url('/trf_list') }}";
                    else alert('Transfer order not deleted.');
                });
        }
        document.getElementById('directTransferFilter').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#directTransferRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        });
    </script>
@endpush
