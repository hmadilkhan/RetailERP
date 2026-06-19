@extends('layouts.master-tailwind')

@section('title', 'Stock Transfer')
@section('page_title', 'Stock Transfer')
@section('page_subtitle', 'Move stock from ' . $branch . ' to a POS terminal.')

@section('content')
    <form method="POST" action="{{ url('save-stock-tranfer') }}" class="space-y-6">
        @csrf

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Destination Terminal</h2>
                    <p class="mt-1 text-sm text-erp-mute">Source branch: <span class="font-bold text-erp-ink">{{ $branch }}</span></p>
                </div>
                <a href="{{ url('/stock-list') }}" class="text-sm font-bold text-erp-dark hover:text-erp">&larr; Back to list</a>
            </div>
            <div class="p-5 sm:max-w-sm">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Select Terminal</span>
                    <select id="terminal" name="terminal" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Terminal</option>
                        @foreach($terminals as $terminal)
                            <option value="{{ $terminal->terminal_id }}">{{ $terminal->terminal_name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Products In Stock</h2>
                <p class="mt-1 text-sm text-erp-mute">Enter a transfer quantity for each item you want to move.</p>
            </div>
            <div class="overflow-x-auto">
                <table id="widget-product-list" class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-5 py-3">Product #</th>
                            <th class="px-5 py-3">Item Code</th>
                            <th class="px-5 py-3">Product Name</th>
                            <th class="px-5 py-3">Unit</th>
                            <th class="px-5 py-3">Department</th>
                            <th class="px-5 py-3">Sub Department</th>
                            <th class="px-5 py-3">Stock Qty</th>
                            <th class="px-5 py-3">Transfer Qty</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line">
                        @forelse($products as $product)
                            @if($product->qty > 0)
                                <tr>
                                    <td class="px-5 py-3 text-erp-text">
                                        <input type="hidden" name="product[]" value="{{ $product->id }}">{{ $product->id }}
                                    </td>
                                    <td class="px-5 py-3 text-erp-text">{{ $product->item_code }}</td>
                                    <td class="px-5 py-3 font-semibold text-erp-ink">{{ $product->product_name }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $product->name }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $product->department_name }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $product->sub_depart_name }}</td>
                                    <td class="px-5 py-3 text-erp-text">
                                        <input type="hidden" name="stock[]" value="{{ $product->qty }}">{{ $product->qty }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <input type="text" name="qty[]" class="h-9 w-24 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="8" class="px-5 py-6 text-center text-sm text-erp-mute">No stock available to transfer.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Submit</button>
        </div>
    </form>
@endsection
