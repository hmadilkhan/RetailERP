@extends('layouts.master-tailwind')

@section('title', 'Work Orders')
@section('page_title', 'Work Order Details')
@section('page_subtitle', 'Items and quantities included in this production run.')

@section('content')
    <div class="mb-4">
        <a href="{{ url('/job-order') }}" class="text-sm font-bold text-erp-dark hover:text-erp">&larr; Back to list</a>
    </div>

    <div class="space-y-6">
        <section class="grid gap-4 rounded-lg border border-erp-line bg-white p-5 shadow-sm sm:grid-cols-3">
            <div>
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Work Order Name</div>
                <div class="mt-2 text-lg font-black text-erp-ink">{{ $sum[0]->joborder_name }}</div>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Created On</div>
                <div class="mt-2 text-lg font-black text-erp-ink">{{ date("d F Y", strtotime($sum[0]->created_at)) }}</div>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Order Amount</div>
                <div class="mt-2 text-lg font-black text-erp-dark">{{ $sum[0]->cost }}</div>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Order Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-5 py-3">Image</th>
                            <th class="px-5 py-3">Dish Name</th>
                            <th class="px-5 py-3">Order Quantity</th>
                            <th class="px-5 py-3">Order Cost</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line">
                        @forelse($details as $value)
                            <tr>
                                <td class="px-5 py-3">
                                    <img width="42" height="42" src="{{ asset('assets/images/products/'.(!empty($value->image) ? $value->image : 'placeholder.jpg')) }}" class="h-10 w-10 rounded-full object-cover ring-1 ring-slate-200" alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                                </td>
                                <td class="px-5 py-3 font-semibold text-erp-ink">{{ $value->product_name }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->order_qty }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->job_cost }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-6 text-center text-sm text-erp-mute">No items.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
