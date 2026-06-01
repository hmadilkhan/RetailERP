<section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
        <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Companies</div>
        <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($summary->count()) }}</div>
        <p class="mt-2 text-sm text-erp-mute">Matching current filters</p>
    </div>
    <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
        <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Outstanding</div>
        <div class="mt-4 text-2xl font-black text-rose-700">PKR {{ number_format($summary->sum('balance_amount'), 2) }}</div>
        <p class="mt-2 text-sm text-erp-mute">Pending receivables</p>
    </div>
    <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
        <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Collected</div>
        <div class="mt-4 text-2xl font-black text-emerald-700">PKR {{ number_format($summary->sum('paid_amount'), 2) }}</div>
        <p class="mt-2 text-sm text-erp-mute">Received amount</p>
    </div>
    <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
        <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Billing Time Due</div>
        <div class="mt-4 text-2xl font-black text-amber-700">{{ number_format($summary->sum('unpaid_months'), 1) }} months</div>
        <p class="mt-2 text-sm text-erp-mute">{{ number_format($summary->sum('full_unpaid_months'), 0) }} full + {{ number_format($summary->sum('partial_unpaid_months'), 1) }} partial</p>
    </div>
</section>

<section class="rounded-lg border border-erp-line bg-white shadow-sm">
    <div class="border-b border-erp-line px-5 py-4">
        <h2 class="text-base font-bold text-erp-ink">Company Billing Summary</h2>
        <p class="mt-1 text-sm text-erp-mute">Company-wise totals, collections, outstanding balances, and payment status.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                <tr>
                    <th class="px-5 py-3 text-left font-bold">Company</th>
                    <th class="px-5 py-3 text-left font-bold">Company Status</th>
                    <th class="px-5 py-3 text-right font-bold">Invoices</th>
                    <th class="px-5 py-3 text-right font-bold">Total</th>
                    <th class="px-5 py-3 text-right font-bold">Paid</th>
                    <th class="px-5 py-3 text-right font-bold">Balance</th>
                    <th class="px-5 py-3 text-left font-bold">Billing Time Due</th>
                    <th class="px-5 py-3 text-left font-bold">Status</th>
                    <th class="px-5 py-3 text-left font-bold">Paid Date</th>
                    <th class="px-5 py-3 text-right font-bold">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($summary as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4">
                            <div class="font-bold text-erp-ink">{{ $item->company_name }}</div>
                            <div class="mt-1 text-xs font-semibold uppercase tracking-[0.12em] text-erp-mute">Company ID {{ $item->company_id }}</div>
                        </td>
                        <td class="px-5 py-4">
                            @if((int) ($item->company_status_id ?? 0) === 1)
                                <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">Active</span>
                            @else
                                <span class="rounded-md bg-rose-50 px-2 py-1 text-xs font-bold text-rose-700 ring-1 ring-rose-200">Inactive</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right font-bold text-erp-ink">{{ number_format($item->total_invoices) }}</td>
                        <td class="px-5 py-4 text-right font-bold text-erp-ink">PKR {{ number_format($item->total_amount, 2) }}</td>
                        <td class="px-5 py-4 text-right font-bold text-emerald-700">PKR {{ number_format($item->paid_amount, 2) }}</td>
                        <td class="px-5 py-4 text-right font-bold text-rose-700">PKR {{ number_format($item->balance_amount, 2) }}</td>
                        <td class="px-5 py-4">
                            <span class="rounded-md bg-amber-50 px-2 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-200">{{ number_format($item->unpaid_months, 1) }} months due</span>
                            <div class="mt-2 text-xs text-erp-mute">{{ number_format($item->full_unpaid_months, 0) }} full + {{ number_format($item->partial_unpaid_months, 1) }} partial</div>
                        </td>
                        <td class="px-5 py-4">
                            @if($item->balance_amount <= 0)
                                <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">Paid</span>
                            @elseif($item->paid_amount > 0)
                                <span class="rounded-md bg-amber-50 px-2 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-200">Partial</span>
                            @else
                                <span class="rounded-md bg-rose-50 px-2 py-1 text-xs font-bold text-rose-700 ring-1 ring-rose-200">Unpaid</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-erp-mute">
                            @if($item->balance_amount <= 0 && !empty($item->latest_paid_date))
                                <span class="font-semibold text-emerald-700">{{ \Carbon\Carbon::parse($item->latest_paid_date)->format('M d, Y') }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('billing.invoices.index', ['company_id' => $item->company_id, 'status' => request('status')]) }}" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">View Invoices</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-5 py-14 text-center">
                            <div class="text-base font-bold text-erp-ink">No billing data found</div>
                            <p class="mt-2 text-sm text-erp-mute">Try changing filters or generate invoices first.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-slate-50 text-sm">
                <tr>
                    <th colspan="3" class="px-5 py-4 text-right font-black text-erp-ink">Grand Total</th>
                    <th class="px-5 py-4 text-right font-black text-erp-ink">PKR {{ number_format($summary->sum('total_amount'), 2) }}</th>
                    <th class="px-5 py-4 text-right font-black text-emerald-700">PKR {{ number_format($summary->sum('paid_amount'), 2) }}</th>
                    <th class="px-5 py-4 text-right font-black text-rose-700">PKR {{ number_format($summary->sum('balance_amount'), 2) }}</th>
                    <th class="px-5 py-4 text-amber-700">{{ number_format($summary->sum('unpaid_months'), 1) }} months due</th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</section>
