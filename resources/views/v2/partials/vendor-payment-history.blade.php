<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-100 text-sm">
        <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
            <tr>
                <th class="px-4 py-3 text-left font-bold">Date</th>
                <th class="px-4 py-3 text-left font-bold">Time</th>
                <th class="px-4 py-3 text-left font-bold">PO No</th>
                <th class="px-4 py-3 text-right font-bold">Amount</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($history as $value)
                <tr>
                    <td class="px-4 py-3 text-erp-text">{{ date('d M Y', strtotime($value->created_at)) }}</td>
                    <td class="px-4 py-3 text-erp-text">{{ date('H:i', strtotime($value->created_at)) }}</td>
                    <td class="px-4 py-3 font-semibold text-erp-ink">
                        @if($value->po_no > 0)
                            <a href="{{ url('view', $value->po_no) }}" target="_blank" class="text-erp-dark hover:underline">{{ $value->po_no }}</a>
                        @else
                            {{ $value->po_no }}
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-erp-ink">{{ number_format($value->debit, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-erp-mute">No payment history found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
