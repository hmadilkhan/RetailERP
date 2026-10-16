<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $input = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $btn = 'h-9 rounded-lg border border-erp-line bg-white px-3 text-sm font-bold text-erp-text transition hover:bg-slate-50';
        $btnPrimary = 'h-9 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:opacity-90';
        $money = fn ($v) => number_format((float) $v, 2);
        $qty = fn ($v) => rtrim(rtrim(number_format((float) $v, 3, '.', ''), '0'), '.');
        $states = ['not_billed' => ['bg-amber-50 text-amber-700', 'Not billed'], 'partial' => ['bg-sky-50 text-sky-700', 'Partly billed'], 'billed' => ['bg-emerald-50 text-emerald-700', 'Billed']];
    @endphp

    @if (session()->has('po_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition.opacity>{{ session('po_message') }}</div>
    @endif
    @error('po')
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ $message }}</div>
    @enderror

    @if (!$ready)
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">PO Matching is not set up on this server yet (database update pending).</div>
    @elseif (isset($bill))
        @php $draft = $bill->status === 'draft'; @endphp
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <button type="button" wire:click="back" class="text-xs font-bold text-erp hover:underline">&larr; All purchase orders</button>
                    <h2 class="mt-1 text-lg font-bold text-erp-ink">{{ $bill->doc_no }} · {{ $vendor }}
                        <span class="ml-1 rounded-full px-2 py-0.5 text-xs font-bold {{ $draft ? 'bg-slate-100 text-slate-600' : 'bg-emerald-50 text-emerald-700' }}">{{ ucfirst($bill->status) }}</span></h2>
                    <p class="text-sm text-erp-mute">Purchase order #{{ $po->po_no ?: $po->purchase_id }} · {{ $po->order_date ? date('d M Y', strtotime($po->order_date)) : '' }}</p>
                </div>
                @if (!$draft)<a href="{{ url('vendor-bills') }}?bill={{ $bill->id }}" class="{{ $btn }} inline-flex items-center">Open in Vendor Bills (pay)</a>@endif
            </div>

            <div class="grid gap-3 border-b border-erp-line p-5 md:grid-cols-5">
                <label class="block"><span class="{{ $labelClass }}">Vendor invoice no</span><input type="text" wire:model="vendorRef" class="mt-1 {{ $input }}" @disabled(!$draft)></label>
                <label class="block"><span class="{{ $labelClass }}">Invoice date</span><input type="date" wire:model="billDate" class="mt-1 {{ $input }}" @disabled(!$draft)></label>
                <label class="block"><span class="{{ $labelClass }}">Due date</span><input type="date" wire:model="dueDate" class="mt-1 {{ $input }}" @disabled(!$draft)></label>
                <label class="block"><span class="{{ $labelClass }}">Other charges (freight…)</span><input type="number" step="any" min="0" wire:model="otherCharges" class="mt-1 {{ $input }}" @disabled(!$draft)></label>
                <label class="block"><span class="{{ $labelClass }}">Note</span><input type="text" wire:model="narration" class="mt-1 {{ $input }}" @disabled(!$draft)></label>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                        <th class="px-5 py-3">Item</th>
                        <th class="px-2 py-3 text-right">Ordered</th><th class="px-2 py-3 text-right">Received</th><th class="px-2 py-3 text-right">Can bill</th>
                        <th class="w-24 px-2 py-3">Bill qty</th><th class="w-28 px-2 py-3">Rate</th><th class="w-24 px-2 py-3">Discount</th><th class="w-20 px-2 py-3">Tax %</th>
                        <th class="px-2 py-3 text-right">PO cost / unit</th><th class="px-2 py-3 text-right">Invoice cost / unit</th><th class="px-5 py-3 text-right">Difference</th>
                    </tr></thead>
                    @foreach ($lines as $i => $line)
                        @php $m = $match['lines'][$line['product_id']] ?? null; @endphp
                        <tr class="border-b border-erp-line" wire:key="pl-{{ $line['product_id'] }}">
                            <td class="px-5 py-2">{{ $line['name'] }}</td>
                            <td class="px-2 py-2 text-right tabular-nums">{{ $qty($m['ordered'] ?? 0) }}</td>
                            <td class="px-2 py-2 text-right tabular-nums">{{ $qty($m['kept'] ?? 0) }}</td>
                            <td class="px-2 py-2 text-right tabular-nums {{ ($m['qty_ok'] ?? true) ? '' : 'font-bold text-rose-700' }}">{{ $draft ? $qty($m['billable'] ?? 0) : '—' }}</td>
                            <td class="px-2 py-1"><input type="number" step="any" min="0" wire:model="lines.{{ $i }}.qty" class="{{ $input }} text-right" @disabled(!$draft)></td>
                            <td class="px-2 py-1"><input type="number" step="any" min="0" wire:model="lines.{{ $i }}.unit_price" class="{{ $input }} text-right" @disabled(!$draft)></td>
                            <td class="px-2 py-1"><input type="number" step="any" min="0" wire:model="lines.{{ $i }}.discount" class="{{ $input }} text-right" @disabled(!$draft)></td>
                            <td class="px-2 py-1"><input type="number" step="any" min="0" wire:model="lines.{{ $i }}.tax_rate" class="{{ $input }} text-right" @disabled(!$draft)></td>
                            <td class="px-2 py-2 text-right tabular-nums">{{ $money($m['po_cost'] ?? 0) }}</td>
                            <td class="px-2 py-2 text-right tabular-nums">{{ ($m['bill_cost'] ?? null) === null ? '—' : $money($m['bill_cost']) }}</td>
                            <td class="px-5 py-2 text-right tabular-nums font-semibold {{ ($m['price_ok'] ?? true) ? 'text-emerald-700' : 'text-rose-700' }}">{{ ($m['pct'] ?? null) === null ? '' : (($m['pct'] > 0 ? '+' : '') . $m['pct'] . '%') }}</td>
                        </tr>
                    @endforeach
                    <tfoot>
                        <tr><td colspan="9"></td><td class="px-2 py-2 text-right text-xs text-erp-mute">Invoice total</td><td class="px-5 py-2 text-right font-bold tabular-nums">{{ $money($bill->total) }}</td></tr>
                        <tr><td colspan="9"></td><td class="px-2 py-2 text-right text-xs text-erp-mute">Already booked at PO cost</td><td class="px-5 py-2 text-right tabular-nums">{{ $money($bill->lines->sum('booked_value')) }}</td></tr>
                        <tr><td colspan="9"></td><td class="px-2 py-2 text-right text-xs font-bold">Difference to vendor</td><td class="px-5 py-2 text-right font-bold tabular-nums {{ abs((float) $bill->variance) >= 0.5 ? 'text-amber-700' : 'text-emerald-700' }}">{{ $money($bill->variance) }}</td></tr>
                    </tfoot>
                </table>
            </div>
            <div class="space-y-1 px-5 py-3 text-xs text-erp-mute">
                <p>Stock and the vendor balance were already updated when the goods were received (at the purchase order rate). Posting this bill adds only the difference, and moves the invoice's tax to Input Tax.</p>
                <p>Set a quantity to 0 to leave an item for a later bill. Cost per unit includes discount, tax and the item's share of other charges. Differences above {{ $tolerance }}% need Admin / Sub-Admin acceptance.</p>
            </div>
            @if ($draft)
                <div class="flex flex-wrap items-center justify-end gap-2 border-t border-erp-line px-5 py-4">
                    @if ($match['needs_accept'])
                        <label class="mr-auto flex items-center gap-2 text-sm {{ $admin ? 'text-rose-700' : 'text-erp-mute' }}">
                            <input type="checkbox" wire:model="accept" class="rounded border-erp-line text-erp" @disabled(!$admin)>
                            Accept rate difference above {{ $tolerance }}%{{ $admin ? '' : ' (Admin / Sub-Admin only)' }}
                        </label>
                    @endif
                    <button type="button" wire:click="deleteDraft" wire:confirm="Delete this draft?" class="{{ $btn }} text-rose-700">Delete draft</button>
                    <button type="button" wire:click="save" class="{{ $btn }}">Save &amp; check</button>
                    <button type="button" wire:click="post" wire:confirm="Post this bill? Only the difference from the purchase order goes to the vendor balance." class="{{ $btnPrimary }}">Post</button>
                </div>
            @endif
        </div>
    @else
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">PO Matching</h2>
                    <p class="mt-0.5 text-sm text-erp-mute">Purchase orders with goods received. Record the vendor's invoice against what was ordered and received — quantity and rate differences are shown before posting.</p>
                </div>
                <div class="flex gap-2">
                    <input type="text" wire:model.live.debounce.400ms="search" placeholder="Vendor or PO no…" class="{{ $input }} w-48">
                    <select wire:model.live="status" class="{{ $input }} w-40"><option value="">All</option><option value="not_billed">Not billed</option><option value="partial">Partly billed</option><option value="billed">Billed</option></select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                        <th class="px-5 py-3">PO</th><th class="px-3 py-3">Vendor</th><th class="px-3 py-3 text-right">Ordered</th><th class="px-3 py-3 text-right">Received</th>
                        <th class="px-3 py-3 text-right">Billed</th><th class="px-3 py-3">Status</th><th class="px-3 py-3">Bills</th><th></th>
                    </tr></thead>
                    @forelse ($rows as $r)
                        <tr class="border-b border-erp-line" wire:key="po-{{ $r['purchase_id'] }}">
                            <td class="px-5 py-2 font-semibold">#{{ $r['po_no'] ?: $r['purchase_id'] }}<div class="text-xs font-normal text-erp-mute">{{ $r['order_date'] ? date('d M Y', strtotime($r['order_date'])) : '' }}</div></td>
                            <td class="px-3 py-2">{{ $r['vendor_name'] }}</td>
                            <td class="px-3 py-2 text-right tabular-nums">{{ $money($r['ordered']) }}</td>
                            <td class="px-3 py-2 text-right tabular-nums">{{ $money($r['received']) }}</td>
                            <td class="px-3 py-2 text-right tabular-nums">{{ $money($r['billed']) }}@if (abs($r['variance']) >= 0.5)<div class="text-xs text-amber-700">diff {{ $money($r['variance']) }}</div>@endif</td>
                            <td class="px-3 py-2"><span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $states[$r['state']][0] }}">{{ $states[$r['state']][1] }}</span></td>
                            <td class="px-3 py-2 text-xs">@foreach ($r['bills'] as $b)<button type="button" wire:click="openBill({{ $b->id }})" class="mr-2 font-mono font-bold text-erp hover:underline">{{ $b->doc_no }}{{ $b->status === 'draft' ? ' (draft)' : '' }}</button>@endforeach</td>
                            <td class="px-5 py-2 text-right">@if ($r['state'] !== 'billed')<button type="button" wire:click="createBill({{ $r['purchase_id'] }})" class="text-xs font-bold text-erp hover:underline">Enter invoice</button>@endif</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-erp-mute">No purchase orders with received goods.</td></tr>
                    @endforelse
                </table>
            </div>
        </div>
    @endif
</div>
