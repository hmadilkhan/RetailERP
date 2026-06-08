@extends('layouts.master-tailwind')

@section('title', 'Transfer Orders')
@section('page_title', 'Requested Transfer Orders')
@section('page_subtitle', 'Create challans or reject requested transfer orders.')

@section('content')
    @php($transferCollection = collect($transferlist ?? []))

    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-erp-ink">Requested List of Transfer Order</h2>
                <p class="mt-1 text-sm text-erp-mute">{{ number_format($transferCollection->count()) }} transfer orders.</p>
            </div>
            <input type="search" id="transferFilter" placeholder="Filter transfer orders..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                    <tr>
                        <th class="px-5 py-3 text-left font-bold">TO No.</th>
                        <th class="px-5 py-3 text-left font-bold">Demanded By</th>
                        <th class="px-5 py-3 text-left font-bold">Generation Date</th>
                        <th class="px-5 py-3 text-left font-bold">Status</th>
                        <th class="px-5 py-3 text-right font-bold">Action</th>
                    </tr>
                </thead>
                <tbody id="transferRows" class="divide-y divide-slate-100">
                    @forelse($transferCollection as $value)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-bold text-erp-ink">TO-{{ $value->transfer_No }}</td>
                            <td class="px-5 py-4 text-erp-text">{{ $value->demanded_by }}</td>
                            <td class="px-5 py-4 text-erp-text">{{ $value->date }}</td>
                            <td class="px-5 py-4">@include('v2.partials.status-badge', ['status' => $value->name])</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" onclick="view('{{ $value->transfer_id }}')" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">View</button>
                                    @if($value->name != 'Delivered')
                                        <button type="button" onclick="open_challan({{ $value->transfer_id }})" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">Challan</button>
                                        <button type="button" onclick="reject({{ $value->transfer_id }})" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Reject</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-erp-mute">No transfer orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function open_challan(id) { window.location = "{{ url('/createdeliverychallan') }}/" + id; }
        function view(id) { window.location = "{{ url('/showtransferdetails') }}/" + id; }
        function reject(id) {
            if (!confirm('Delete this transfer order?')) return;
            fetch("{{ url('/removetransferorder') }}", {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ id: id, statusid: 3 })
            }).then(response => response.text()).then(result => {
                if (result.trim() === '1') window.location = "{{ url('/transferlist') }}";
                else alert('Transfer order not deleted.');
            });
        }
        document.getElementById('transferFilter').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#transferRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        });
    </script>
@endpush
