<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-100 text-sm">
        <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
            <tr>
                <th class="px-5 py-3 text-left font-bold">Date</th>
                <th class="px-5 py-3 text-left font-bold">Time</th>
                <th class="px-5 py-3 text-left font-bold">PO No</th>
                <th class="px-5 py-3 text-left font-bold">Vendor</th>
                <th class="px-5 py-3 text-left font-bold">Address</th>
                <th class="px-5 py-3 text-left font-bold">Due Date</th>
                <th class="px-5 py-3 text-right font-bold">Amount</th>
                <th class="px-5 py-3 text-right font-bold">Balance</th>
                <th class="px-5 py-3 text-right font-bold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($result as $value)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-4 text-erp-text">{{ date('d M Y', strtotime($value->vendorpurchases->date)) }}</td>
                    <td class="px-5 py-4 text-erp-text">{{ date('H:i', strtotime($value->vendorpurchases->time)) }}</td>
                    <td class="px-5 py-4 font-bold text-erp-ink"><a href="{{ url('view', $value->vendorpurchases->purchase_id) }}" target="_blank" class="text-erp-dark hover:underline">{{ $value->vendorpurchases->po_no }}</a></td>
                    <td class="px-5 py-4 text-erp-text">{{ $value->vendorpurchases->vendor['vendor_name'] }}</td>
                    <td class="max-w-xs px-5 py-4 text-erp-mute"><div class="line-clamp-2">{{ $value->vendorpurchases->vendor['address'] }}</div></td>
                    <td class="px-5 py-4 text-erp-text">{{ $value->vendorpurchases->payment_date }}</td>
                    <td class="px-5 py-4 text-right font-semibold text-erp-ink">{{ number_format($value->vendorpurchases->purchaseAccount['total_amount'], 2) }}</td>
                    <td class="px-5 py-4 text-right font-bold text-erp-ink">{{ number_format($value->vendorpurchases->purchaseAccount['balance_amount'], 2) }}</td>
                    <td class="px-5 py-4">
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="editDueDate(@js($value->vendorpurchases->purchase_id), @js($value->vendorpurchases->payment_date))" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Due Date</button>
                            <button type="button" onclick="viewPaymentHistory(@js($value->vendorpurchases->vendor['id']))" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">History</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-5 py-12 text-center text-erp-mute">No payable records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="border-t border-erp-line px-5 py-4">
    {{ $result->links() }}
</div>
