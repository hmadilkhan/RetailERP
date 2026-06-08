@if(isset($record))
    <div class="overflow-x-auto">
        <table id="widget-product-list" class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                <tr>
                    <th class="px-5 py-3 text-left font-bold">Code</th>
                    <th class="px-5 py-3 text-left font-bold">Name</th>
                    <th class="px-5 py-3 text-right font-bold">Qty</th>
                    <th class="px-5 py-3 text-right font-bold">Unit Price</th>
                    <th class="px-5 py-3 text-right font-bold">Total Amount</th>
                    <th class="px-5 py-3 text-left font-bold">Branch</th>
                    <th class="px-5 py-3 text-left font-bold">Terminal</th>
                    <th class="px-5 py-3 text-left font-bold">Date</th>
                </tr>
            </thead>
            <tbody id="itemSaleRows" class="divide-y divide-slate-100">
                @forelse($record as $value)
                    @php($totalamount = $value->total_qty * $value->item_price)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4 text-erp-text">{{ $value->inventory->item_code }}</td>
                        <td class="px-5 py-4 font-semibold text-erp-ink">{{ $value->inventory->product_name }}</td>
                        <td class="px-5 py-4 text-right text-erp-text">{{ $value->total_qty }}</td>
                        <td class="px-5 py-4 text-right text-erp-text">{{ $value->item_price }}</td>
                        <td class="px-5 py-4 text-right font-bold text-erp-ink">{{ number_format($totalamount, 2) }}</td>
                        <td class="px-5 py-4 text-erp-text">{{ $value->order->branchrelation->branch_name }}</td>
                        <td class="px-5 py-4 text-erp-text">{{ $value->order->terminal->terminal_name }}</td>
                        <td class="px-5 py-4 text-erp-text">{{ date('d F Y', strtotime($value->order->date)) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-5 py-12 text-center text-erp-mute">No items found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif
<script>
    document.getElementById('totaldiv').classList.remove('hidden');
    document.getElementById('totalorders').textContent = "{{ $totalQty }}";
    document.getElementById('totalamount').textContent = "{{ number_format($totalAmountReceipts, 2) }}";
    document.getElementById('totalreceipts').textContent = "{{ $totalCountReceipts }}";
</script>
