@extends('layouts.master-tailwind')

@section('title', 'Delivery Challan Details')
@section('page_title', 'Delivery Challan Details')
@section('page_subtitle', 'Review challan branch, shipment, and item cost details.')

@section('content')
    @php($detailCollection = collect($details ?? []))

    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Delivery Challan Details</h2>
                    <p class="mt-1 text-sm text-erp-mute">Challan Number: {{ $detailCollection->isEmpty() ? '' : $detailCollection[0]->DC_No }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ url('/challanlist') }}" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Back</a>
                    <button type="button" onclick="generate_pdf()" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-rose-700">Print PDF</button>
                </div>
            </div>
            @if($detailCollection->isNotEmpty())
                <div class="grid gap-4 p-5 md:grid-cols-3">
                    <div class="rounded-lg border border-erp-line bg-slate-50 p-4">
                        <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">From</div>
                        <div class="mt-3 font-bold text-erp-ink">Branch Manager</div>
                        <p class="mt-1 text-sm text-erp-text">{{ $detailCollection[0]->deliverd_by }}</p>
                        <p class="mt-1 text-sm text-erp-mute">{{ $detailCollection[0]->del_add }}</p>
                    </div>
                    <div class="rounded-lg border border-erp-line bg-slate-50 p-4">
                        <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">To</div>
                        <div class="mt-3 font-bold text-erp-ink">Branch Manager</div>
                        <p class="mt-1 text-sm text-erp-text">{{ $detailCollection[0]->destination }}</p>
                        <p class="mt-1 text-sm text-erp-mute">{{ $detailCollection[0]->des_add }}</p>
                    </div>
                    <div class="rounded-lg border border-erp-line bg-slate-50 p-4">
                        <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Summary</div>
                        <p class="mt-3 text-sm text-erp-text">Created on: <span class="font-bold text-erp-ink">{{ $detailCollection[0]->date }}</span></p>
                        <p class="mt-2 text-sm text-erp-text">Shipment Charges: <span class="font-bold text-erp-ink">{{ $detailCollection[0]->shipment_amount }}</span></p>
                    </div>
                </div>
            @endif
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Product ID</th>
                            <th class="px-5 py-3 text-left font-bold">Product Name</th>
                            <th class="px-5 py-3 text-right font-bold">Delivered Quantity</th>
                            <th class="px-5 py-3 text-right font-bold">Cost Price</th>
                            <th class="px-5 py-3 text-right font-bold">Shipment Amount</th>
                            <th class="px-5 py-3 text-right font-bold">Total Cost</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($detailCollection as $value)
                            @php($shipment = $value->shipment_charges == '' ? 0 : $value->shipment_charges)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 text-erp-text">{{ $value->product_id }}</td>
                                <td class="px-5 py-4 font-semibold text-erp-ink">{{ $value->product_name }}</td>
                                <td class="px-5 py-4 text-right text-erp-text">{{ $value->deliverd_qty }}</td>
                                <td class="px-5 py-4 text-right text-erp-text">{{ $value->cost_price }}</td>
                                <td class="px-5 py-4 text-right text-erp-text">{{ number_format($shipment, 2) }}</td>
                                <td class="px-5 py-4 text-right font-bold text-erp-ink">{{ number_format($shipment + $value->cost_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-12 text-center text-erp-mute">No challan items found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function generate_pdf() {
            window.location = "{{ url('dcreport', $challanid) }}";
        }
    </script>
@endpush
