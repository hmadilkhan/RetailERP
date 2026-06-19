@extends('layouts.master-tailwind')

@section('title', 'Work Orders')
@section('page_title', 'Work Orders')
@section('page_subtitle', 'Track submitted production runs and their costing.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Work Orders</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format(collect($result)->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Submitted production runs</p>
            </div>
            <a href="{{ url('repeat-job') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark sm:col-span-1 xl:col-span-2">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Work Order</div>
                    <p class="mt-2 text-sm text-white/75">Repeat a job order into production</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Work Order List</h2>
                    <p class="mt-1 text-sm text-erp-mute">Search by order name and open a work order for details.</p>
                </div>
                <input type="search" id="workorderSearch" placeholder="Search order name..." class="h-10 w-64 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-5 py-3">Order No.</th>
                            <th class="px-5 py-3">Order Name</th>
                            <th class="px-5 py-3">Order Date</th>
                            <th class="px-5 py-3">Order Cost</th>
                            <th class="px-5 py-3">Order Type</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line">
                        @forelse($result as $value)
                            <tr class="workorder-row" data-search="{{ strtolower($value->joborder_name) }}">
                                <td class="px-5 py-3 text-erp-text">{{ $value->job_order_id }}</td>
                                <td class="px-5 py-3 font-semibold text-erp-ink">{{ $value->joborder_name }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ date("d F Y", strtotime($value->created_at)) }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->cost }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->job_status_name }}</td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ url('/getworkorderdetails') }}/{{ Crypt::encrypt($value->job_order_id) }}" class="font-bold text-erp-dark hover:text-erp">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-6 text-center text-sm text-erp-mute">No work orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('workorderSearch').addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('.workorder-row').forEach(row => {
                row.classList.toggle('hidden', term !== '' && !row.dataset.search.includes(term));
            });
        });
    </script>
@endpush
