<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $stockValue = (float) ($summary->stock_value ?? 0);
        $difference = round($glInventory - $stockValue, 2);
    @endphp

    @if (session()->has('val_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.opacity>{{ session('val_message') }}</div>
    @endif
    @error('valuation')
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ $message }}</div>
    @enderror

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-erp-ink">Inventory Valuation</h2>
                <p class="mt-0.5 text-sm text-erp-mute">
                    Moving average cost per product, worked out from purchases, opening stock and adjustments
                    @if ($setting && $setting->posting_start_date) since {{ $setting->posting_start_date->format('d M Y') }} @endif.
                    Cost of goods sold for every sale uses this average.
                    @if ($summary->computed_at) Updated {{ \Carbon\Carbon::parse($summary->computed_at)->diffForHumans() }}. @endif
                </p>
            </div>
            <button type="button" wire:click="recalculate" wire:loading.attr="disabled" class="h-10 shrink-0 rounded-lg border border-erp bg-white px-5 text-sm font-bold text-erp-dark transition hover:bg-emerald-50 disabled:opacity-60">
                <span wire:loading.remove wire:target="recalculate">Recalculate now</span><span wire:loading wire:target="recalculate">Calculating…</span>
            </button>
        </div>
        <div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-4">
            <div><div class="{{ $labelClass }}">Stock Value</div><div class="mt-1 text-xl font-black text-erp-ink">{{ number_format($stockValue, 2) }}</div><div class="text-xs text-erp-mute">{{ number_format($summary->products ?? 0) }} products</div></div>
            <div><div class="{{ $labelClass }}">Inventory in Ledger</div><div class="mt-1 text-xl font-black text-erp-ink">{{ number_format($glInventory, 2) }}</div>
                <div class="text-xs {{ abs($difference) < 1 ? 'text-emerald-700' : 'text-amber-700' }}">{{ abs($difference) < 1 ? 'Matches stock value' : 'Difference ' . number_format($difference, 2) }}</div></div>
            <div><div class="{{ $labelClass }}">Sales Without Cost</div><div class="mt-1 text-xl font-black {{ $unvaluedLines ? 'text-rose-700' : 'text-erp-ink' }}">{{ number_format($unvaluedLines) }}</div><div class="text-xs text-erp-mute">sale lines posted with 0 cost</div></div>
            <div><div class="{{ $labelClass }}">Products Needing Cost</div><div class="mt-1 text-xl font-black {{ $summary->no_cost ? 'text-amber-700' : 'text-erp-ink' }}">{{ number_format($summary->no_cost ?? 0) }}</div><div class="text-xs text-erp-mute">{{ number_format($summary->negative ?? 0) }} with negative stock</div></div>
        </div>
        <div class="border-t border-erp-line bg-slate-50/60 px-5 py-3 text-xs text-erp-text">
            <span class="font-bold">How to fix "no cost":</span> receive the item through a Purchase Order (GRN) or enter its cost on Opening Stock / Stock Adjustment, or set a cost price lower than the retail price on the product. Voided-receipt restocks are ignored because the POS stores them at the sale price.
            A difference with the ledger usually comes from the opening balance — compare with Opening Balances.
        </div>
    </div>

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="grid gap-4 border-b border-erp-line p-5 md:grid-cols-3">
            <label class="block md:col-span-2"><span class="{{ $labelClass }}">Search</span><input type="search" wire:model.live.debounce.300ms="search" class="{{ $inputClass }}" placeholder="Product name or code"></label>
            <label class="block">
                <span class="{{ $labelClass }}">Show</span>
                <select wire:model.live="filter" class="{{ $inputClass }}">
                    <option value="all">All products</option>
                    <option value="unvalued">Needs cost</option>
                    <option value="negative">Negative stock</option>
                    <option value="valued">With cost</option>
                </select>
            </label>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left">
                    <tr>
                        <th class="px-5 py-3 {{ $labelClass }}">Product</th>
                        <th class="px-5 py-3 text-right {{ $labelClass }}">Qty</th>
                        <th class="px-5 py-3 text-right {{ $labelClass }}">Avg Cost</th>
                        <th class="px-5 py-3 text-right {{ $labelClass }}">Value</th>
                        <th class="px-5 py-3 {{ $labelClass }}">Cost From</th>
                        <th class="px-5 py-3 text-right {{ $labelClass }}">Sold Without Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-erp-line">
                    @forelse ($rows as $row)
                        <tr wire:key="pv-{{ $row->product_id }}" class="{{ $row->avg_cost <= 0 && ($row->qty != 0 || $row->unvalued_sales) ? 'bg-amber-50/40' : '' }}">
                            <td class="px-5 py-2.5"><span class="font-mono text-xs text-erp-mute">{{ $row->item_code }}</span> <span class="text-erp-ink">{{ $row->product_name }}</span></td>
                            <td class="whitespace-nowrap px-5 py-2.5 text-right {{ $row->qty < 0 ? 'font-bold text-rose-700' : 'text-erp-text' }}">{{ rtrim(rtrim(number_format($row->qty, 4), '0'), '.') }}</td>
                            <td class="whitespace-nowrap px-5 py-2.5 text-right text-erp-text">{{ $row->avg_cost > 0 ? number_format($row->avg_cost, 2) : '—' }}</td>
                            <td class="whitespace-nowrap px-5 py-2.5 text-right font-semibold text-erp-ink">{{ number_format($row->value, 2) }}</td>
                            <td class="px-5 py-2.5 text-xs text-erp-mute">{{ $sources[$row->cost_source] ?? $row->cost_source }}</td>
                            <td class="whitespace-nowrap px-5 py-2.5 text-right {{ $row->unvalued_sales ? 'font-bold text-rose-700' : 'text-erp-mute' }}">{{ $row->unvalued_sales ?: '' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-erp-mute">
                            {{ $summary->computed_at ? 'No products match.' : 'Not calculated yet — it runs with automatic posting, or press Recalculate now.' }}
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($rows->hasPages())
            <div class="border-t border-erp-line px-5 py-3">{{ $rows->links() }}</div>
        @endif
    </div>
</div>
