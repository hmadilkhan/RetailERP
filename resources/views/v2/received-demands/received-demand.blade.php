@extends('layouts.master-tailwind')

@section('title', 'Received Demand')
@section('page_title', 'Received Demand')
@section('page_subtitle', 'Review demand orders received from requesting branches.')

@section('content')
    @php($demandCollection = collect($demands ?? []))

    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-erp-ink">Requested Demand List</h2>
                <p class="mt-1 text-sm text-erp-mute">{{ number_format($demandCollection->count()) }} received demands.</p>
            </div>
            <input type="search" id="receivedDemandFilter" placeholder="Filter demands..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                    <tr>
                        <th class="px-5 py-3 text-left font-bold">DO No</th>
                        <th class="px-5 py-3 text-left font-bold">Branch</th>
                        <th class="px-5 py-3 text-left font-bold">Generation Date</th>
                        <th class="px-5 py-3 text-left font-bold">Generation Time</th>
                        <th class="px-5 py-3 text-left font-bold">Status</th>
                        <th class="px-5 py-3 text-right font-bold">Action</th>
                    </tr>
                </thead>
                <tbody id="receivedDemandRows" class="divide-y divide-slate-100">
                    @forelse($demandCollection as $value)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-bold text-erp-ink">DO-{{ $value->demand_id }}</td>
                            <td class="px-5 py-4 text-erp-text">{{ $value->branch_name }}</td>
                            <td class="px-5 py-4 text-erp-text">{{ $value->date }}</td>
                            <td class="px-5 py-4 text-erp-text">{{ date('h:i A', strtotime($value->time)) }}</td>
                            <td class="px-5 py-4">@include('v2.partials.status-badge', ['status' => $value->name])</td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" onclick="btn_view('{{ Crypt::encrypt($value->id) }}')" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">View</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-erp-mute">No received demands found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function btn_view(id) {
            window.location = "{{ url('/demand-details') }}/" + id;
        }

        document.getElementById('receivedDemandFilter').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#receivedDemandRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        });
    </script>
@endpush
