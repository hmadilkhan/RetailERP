@extends('layouts.master-tailwind')

@section('title', 'Job Orders')
@section('page_title', 'Job Order Details')
@section('page_subtitle', 'Raw materials and cost breakdown for this recipe.')

@section('content')
    <div class="mb-4">
        <a href="{{ url('/joborder') }}" class="text-sm font-bold text-erp-dark hover:text-erp">&larr; Back to list</a>
    </div>

    @if($details)
        <div class="space-y-6">
            <section class="rounded-lg border border-erp-line bg-white p-8 text-center shadow-sm">
                <img src="{{ asset('assets/images/products/'.(!empty($details[0]->image) ? $details[0]->image : 'placeholder.jpg')) }}" alt="{{ !empty($details[0]->image) ? $details[0]->image : 'placeholder.jpg' }}" class="mx-auto h-40 w-40 rounded-full object-cover ring-1 ring-slate-200">
                <h2 class="mt-4 text-2xl font-black text-erp-ink">{{ $details[0]->finish_good }}</h2>
            </section>

            <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
                <div class="rounded-lg border border-erp-line bg-white shadow-sm">
                    <div class="border-b border-erp-line px-5 py-4">
                        <h3 class="text-base font-bold text-erp-ink">Raw Materials</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-erp-line text-sm">
                            <thead class="bg-erp-soft">
                                <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                                    <th class="px-5 py-3">Raw Material</th>
                                    <th class="px-5 py-3">Usage Quantity</th>
                                    <th class="px-5 py-3">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-erp-line">
                                @foreach($details as $value)
                                    <tr>
                                        <td class="px-5 py-3 font-semibold text-erp-ink">{{ $value->raw_material }}</td>
                                        <td class="px-5 py-3 text-erp-text">{{ $value->usage_qty }}</td>
                                        <td class="px-5 py-3 text-erp-text">{{ $value->cost }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-lg border border-erp-line bg-white shadow-sm">
                    <div class="border-b border-erp-line px-5 py-4">
                        <h3 class="text-base font-bold text-erp-ink">Cost Summary</h3>
                    </div>
                    <div class="space-y-3 p-5 text-sm">
                        <div class="flex justify-between"><span class="text-erp-mute">Ingredients Cost</span><span class="font-semibold text-erp-ink">{{ $details[0]->job_cost }}</span></div>
                        <div class="flex justify-between"><span class="text-erp-mute">Packing Cost</span><span class="font-semibold text-erp-ink">{{ $details[0]->material_cost }}</span></div>
                        <div class="flex justify-between"><span class="text-erp-mute">Infra-Structure Cost</span><span class="font-semibold text-erp-ink">{{ $details[0]->infrastructure_cost }}</span></div>
                        <div class="flex justify-between border-t border-erp-line pt-3 text-base"><span class="font-bold text-erp-ink">Total Amount</span><span class="font-black text-erp-dark">{{ $details[0]->total_cost }}</span></div>
                    </div>
                </div>
            </section>
        </div>
    @endif
@endsection
