@extends('layouts.master-tailwind')

@section('title', 'Transfer Order Details')
@section('page_title', 'Transfer Order Details')
@section('page_subtitle', 'Review direct transfer order branch and item details.')

@section('content')
    @php($detailCollection = collect($getdetails ?? []))

    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Transfer Order Details</h2>
                    <p class="mt-1 text-sm text-erp-mute">Transfer Number: {{ $detailCollection->isEmpty() ? '' : $detailCollection[0]->transfer_No }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ url('/trf_list') }}" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Back</a>
                    @if($detailCollection->isNotEmpty())
                        <a target="_blank" href="{{ url('direct-transfer-report', $detailCollection[0]->transfer_id) }}" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Print</a>
                    @endif
                </div>
            </div>
            @if($detailCollection->isNotEmpty())
                <div class="grid gap-4 p-5 md:grid-cols-3">
                    <div class="rounded-lg border border-erp-line bg-slate-50 p-4">
                        <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">From</div>
                        <div class="mt-3 font-bold text-erp-ink">Administrator</div>
                        <p class="mt-1 text-sm text-erp-text">Head Office</p>
                        <p class="mt-1 text-sm text-erp-mute">Park Avenue, Groud Floor, Shahrah-e-Faisal, Malir</p>
                    </div>
                    <div class="rounded-lg border border-erp-line bg-slate-50 p-4">
                        <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">To</div>
                        <div class="mt-3 font-bold text-erp-ink">Branch Manager</div>
                        <p class="mt-1 text-sm text-erp-text">{{ $detailCollection[0]->branch_name }}</p>
                        <p class="mt-1 text-sm text-erp-mute">{{ $detailCollection[0]->branch_address }}</p>
                    </div>
                    <div class="rounded-lg border border-erp-line bg-slate-50 p-4">
                        <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Summary</div>
                        <p class="mt-3 text-sm text-erp-text">Created on: <span class="font-bold text-erp-ink">{{ $detailCollection[0]->date }}</span></p>
                        <p class="mt-2 text-sm text-erp-text">Shipment Amount: <span class="font-bold text-erp-ink">{{ $detailCollection[0]->shipment_amount }}</span></p>
                    </div>
                </div>
            @endif
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Item Code</th>
                            <th class="px-5 py-3 text-left font-bold">Product Name</th>
                            <th class="px-5 py-3 text-right font-bold">Transfer Quantity</th>
                            <th class="px-5 py-3 text-right font-bold">Shipment Cost</th>
                            <th class="px-5 py-3 text-right font-bold">Cost Price</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($detailCollection as $value)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 text-erp-text">{{ $value->item_code }}</td>
                                <td class="px-5 py-4 font-semibold text-erp-ink">{{ $value->product_name }}</td>
                                <td class="px-5 py-4 text-right text-erp-text">{{ $value->transfer_qty }}</td>
                                <td class="px-5 py-4 text-right text-erp-text">{{ $value->shipment_charges }}</td>
                                <td class="px-5 py-4 text-right text-erp-text">{{ $value->cp }}</td>
                                <td class="px-5 py-4">@include('v2.partials.status-badge', ['status' => $value->item_status])</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-12 text-center text-erp-mute">No transfer items found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
