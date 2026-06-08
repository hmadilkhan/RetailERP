@extends('layouts.master-tailwind')

@section('title', 'Delivery Challan List')
@section('page_title', 'Delivery Challan List')
@section('page_subtitle', 'Review delivery challans and create GRNs for received challans.')

@section('content')
    @php($challanCollection = collect($challans ?? []))

    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-erp-ink">Delivery Challans</h2>
                <p class="mt-1 text-sm text-erp-mute">{{ number_format($challanCollection->count()) }} challans.</p>
            </div>
            <input type="search" id="challanFilter" placeholder="Filter challans..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                    <tr>
                        <th class="px-5 py-3 text-left font-bold">Challan No.</th>
                        <th class="px-5 py-3 text-left font-bold">Delivered By</th>
                        <th class="px-5 py-3 text-left font-bold">Destination</th>
                        <th class="px-5 py-3 text-left font-bold">Delivered Date</th>
                        <th class="px-5 py-3 text-right font-bold">Action</th>
                    </tr>
                </thead>
                <tbody id="challanRows" class="divide-y divide-slate-100">
                    @forelse($challanCollection as $value)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-bold text-erp-ink">DC-{{ $value->DC_No }}</td>
                            <td class="px-5 py-4 text-erp-text">{{ $value->deliverd_by }}</td>
                            <td class="px-5 py-4 text-erp-text">{{ $value->destination }}</td>
                            <td class="px-5 py-4 text-erp-text">{{ $value->date }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" onclick="open_challan('{{ $value->DC_id }}')" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">View</button>
                                    @if($value->branch_to == session('branch') && $value->counter == 0)
                                        <button type="button" onclick="GRN({{ $value->DC_id }})" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">Receive</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-erp-mute">No challans found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function open_challan(id) { window.location = "{{ url('/challandetails') }}/" + id; }
        function GRN(id) {
            fetch("{{ url('/removetransferorder') }}", {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ id: id, statusid: 9 })
            }).then(() => window.location = "{{ url('/createGRN') }}/" + id);
        }
        document.getElementById('challanFilter').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#challanRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        });
    </script>
@endpush
