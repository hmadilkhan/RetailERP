<div class="space-y-3">
    @forelse($driverOrdersDetails as $key => $orderDetail)
        <details class="rounded-lg border border-erp-line border-l-4 border-l-erp bg-white shadow-sm" {{ $key == 0 ? 'open' : '' }}>
            <summary class="cursor-pointer list-none px-5 py-4">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Receipt No</div>
                        <div class="mt-1 font-bold text-erp-ink">{{ $orderDetail->order->receipt_no }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Total Amount</div>
                        <div class="mt-1 font-bold text-erp-ink">{{ $orderDetail->order->amount }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Total Items</div>
                        <div class="mt-1 font-bold text-erp-ink">{{ $orderDetail->order->total_item_qty }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Customer</div>
                        <div class="mt-1 font-bold text-erp-ink">{{ $orderDetail->order->customer->name }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Customer Mobile</div>
                        <div class="mt-1 font-bold text-erp-ink">{{ $orderDetail->order->customer->mobile }}</div>
                    </div>
                    <div class="xl:col-span-2">
                        <div class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Customer Address</div>
                        <div class="mt-1 font-bold text-erp-ink">{{ $orderDetail->order->customer->address }}</div>
                    </div>
                    <div onclick="event.preventDefault()">
                        <div class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Status</div>
                        <select id="status{{ $orderDetail->order->id }}" class="mt-1 h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" onchange="orderStatusChange('status{{ $orderDetail->order->id }}', '{{ $orderDetail->order->id }}')">
                            @foreach($orderStatus as $val)
                                <option {{ $val->order_status_id == $orderDetail->order->status ? 'selected' : '' }} value="{{ $val->order_status_id }}">{{ $val->order_status_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div onclick="event.preventDefault()">
                        <div class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Payment Status</div>
                        <select id="paymentStatus{{ $orderDetail->order->id }}" class="mt-1 h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" onchange="paymentStatusChange('paymentStatus{{ $orderDetail->order->id }}', '{{ $orderDetail->order->id }}')">
                            <option value="">Select Payment Status</option>
                            <option {{ $orderDetail->order->payment_status == 'cash' ? 'selected' : '' }} value="cash">Cash</option>
                            <option {{ $orderDetail->order->payment_status == 'credit' ? 'selected' : '' }} value="credit">Credit</option>
                            <option {{ $orderDetail->order->payment_status == 'partial' ? 'selected' : '' }} value="partial">Partial</option>
                        </select>
                    </div>
                </div>
            </summary>

            <div class="border-t border-erp-line px-5 py-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-erp-line text-sm">
                        <thead class="bg-erp-soft">
                            <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                                <th class="px-4 py-2">Item Code</th>
                                <th class="px-4 py-2">Product Name</th>
                                <th class="px-4 py-2">Price</th>
                                <th class="px-4 py-2">Qty</th>
                                <th class="px-4 py-2">Total Amount</th>
                                <th class="px-4 py-2">Narration</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-erp-line">
                            @foreach($orderDetail->order->orderdetails as $product)
                                <tr>
                                    <td class="px-4 py-2 text-erp-text">{{ $product->item_code }}</td>
                                    <td class="px-4 py-2 font-semibold text-erp-ink">{{ $product->inventory->product_name }}</td>
                                    <td class="px-4 py-2 text-erp-text">{{ $product->item_price }}</td>
                                    <td class="px-4 py-2 text-erp-text">{{ $product->total_qty }}</td>
                                    <td class="px-4 py-2 text-erp-text">{{ $product->amount }}</td>
                                    <td class="px-4 py-2">
                                        <button type="button" id="narration{{ $product->receipt_detail_id }}" onclick="changeNarration('{{ $product->receipt_detail_id }}', '{{ $product->narration }}')" class="text-erp-dark hover:text-erp">{{ $product->narration == '' ? 'N/A' : $product->narration }}</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </details>
    @empty
        <p class="rounded-lg border border-erp-line bg-white px-5 py-6 text-center text-sm text-erp-mute">No items found.</p>
    @endforelse
</div>

<div id="order-narration-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
    <div class="w-full max-w-md rounded-lg bg-white shadow-menu">
        <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
            <h3 class="text-base font-bold text-erp-ink">Edit Narration</h3>
            <button type="button" class="text-erp-mute hover:text-erp-ink" onclick="closeModal('order-narration-modal')">Close</button>
        </div>
        <div class="space-y-2 px-5 py-5">
            <input type="hidden" id="modalReceiptDetailsId">
            <textarea id="narration" rows="4" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></textarea>
        </div>
        <div class="flex justify-end border-t border-erp-line px-5 py-4">
            <button type="button" id="btn_edit_narration" onclick="saveNarration(document.getElementById('modalReceiptDetailsId').value, document.getElementById('narration').value)" class="rounded-lg border border-erp bg-erp px-5 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Save</button>
        </div>
    </div>
</div>
