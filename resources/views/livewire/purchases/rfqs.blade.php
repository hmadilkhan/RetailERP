<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $input = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $btn = 'h-9 rounded-lg border border-erp-line bg-white px-3 text-sm font-bold text-erp-text transition hover:bg-slate-50';
        $btnPrimary = 'h-9 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:opacity-90';
        $money = fn ($v) => number_format((float) $v, 2);
        $qty = fn ($v) => rtrim(rtrim(number_format((float) $v, 3, '.', ''), '0'), '.');
        $badge = ['open' => 'bg-sky-50 text-sky-700', 'awarded' => 'bg-emerald-50 text-emerald-700', 'cancelled' => 'bg-slate-100 text-slate-500',
                  'invited' => 'bg-amber-50 text-amber-700', 'quoted' => 'bg-emerald-50 text-emerald-700', 'declined' => 'bg-slate-100 text-slate-500'];
    @endphp

    @if (session()->has('rfq_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition.opacity>{{ session('rfq_message') }}</div>
    @endif
    @error('rfq')
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ $message }}</div>
    @enderror

    @if ($showForm)
        {{-- new RFQ --}}
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <button type="button" wire:click="back" class="text-xs font-bold text-erp hover:underline">&larr; All RFQs</button>
                <h2 class="mt-1 text-base font-bold text-erp-ink">New Request for Quotation</h2>
            </div>
            <div class="grid gap-3 p-5 md:grid-cols-4">
                <label class="block md:col-span-2"><span class="{{ $labelClass }}">Title</span><input type="text" wire:model="title" class="mt-1 {{ $input }}" placeholder="e.g. Packaging for November"></label>
                <label class="block">
                    <span class="{{ $labelClass }}">Deliver to branch</span>
                    <select wire:model="branchId" class="mt-1 {{ $input }}">
                        <option value="">—</option>
                        @foreach ($branches as $b)<option value="{{ $b->branch_id }}">{{ $b->branch_name }}</option>@endforeach
                    </select>
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="block"><span class="{{ $labelClass }}">Date</span><input type="date" wire:model="rfqDate" class="mt-1 {{ $input }}"></label>
                    <label class="block"><span class="{{ $labelClass }}">Reply by</span><input type="date" wire:model="dueDate" class="mt-1 {{ $input }}"></label>
                </div>
                <label class="block md:col-span-4"><span class="{{ $labelClass }}">Notes</span><input type="text" wire:model="notes" class="mt-1 {{ $input }}"></label>
            </div>

            <div class="border-t border-erp-line px-5 py-4">
                <h3 class="text-sm font-bold text-erp-ink">Items</h3>
                <div class="relative mt-2 max-w-md">
                    <input type="text" wire:model.live.debounce.300ms="productSearch" class="{{ $input }}" placeholder="Search product name or code…">
                    @if ($searchResults->count())
                        <div class="absolute z-10 mt-1 max-h-64 w-full overflow-y-auto rounded-lg border border-erp-line bg-white shadow-lg">
                            @foreach ($searchResults as $p)
                                <button type="button" wire:click="addProduct({{ $p->id }})" class="block w-full px-3 py-2 text-left text-sm hover:bg-slate-50">{{ $p->product_name }} <span class="text-xs text-erp-mute">{{ $p->item_code }}</span></button>
                            @endforeach
                        </div>
                    @endif
                </div>
                @if ($lines)
                    <table class="mt-3 min-w-full text-sm">
                        <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}"><th class="py-2">Product</th><th class="w-32 px-2 py-2">Qty</th><th class="px-2 py-2">Note for vendor</th><th></th></tr></thead>
                        @foreach ($lines as $i => $l)
                            <tr class="border-b border-erp-line" wire:key="nl-{{ $l['product_id'] }}">
                                <td class="py-2">{{ $l['name'] }}</td>
                                <td class="px-2 py-1"><input type="number" step="any" min="0" wire:model="lines.{{ $i }}.qty" class="{{ $input }} text-right"></td>
                                <td class="px-2 py-1"><input type="text" wire:model="lines.{{ $i }}.notes" class="{{ $input }}" placeholder="brand, size, packing…"></td>
                                <td class="text-right"><button type="button" wire:click="removeLine({{ $i }})" class="text-xs font-bold text-rose-700">Remove</button></td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            </div>

            <div class="border-t border-erp-line px-5 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-erp-ink">Vendors to ask</h3>
                    @if ($lines)<button type="button" wire:click="suggestVendors" class="{{ $btn }}">Select vendors linked to these items</button>@endif
                </div>
                <div class="mt-3 grid max-h-64 gap-1 overflow-y-auto sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($vendors as $v)
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" value="{{ $v->id }}" wire:model="vendorIds" class="rounded border-erp-line text-erp"> {{ $v->vendor_name }}</label>
                    @empty
                        <p class="text-sm text-erp-mute">No active vendors.</p>
                    @endforelse
                </div>
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4"><button type="button" wire:click="create" class="{{ $btnPrimary }}">Create RFQ</button></div>
        </div>

    @elseif ($viewId)
        {{-- detail + comparison --}}
        @php $open = $rfq->status === 'open'; $vRows = $cmp['vendors']; @endphp
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <button type="button" wire:click="back" class="text-xs font-bold text-erp hover:underline">&larr; All RFQs</button>
                    <h2 class="mt-1 text-lg font-bold text-erp-ink">{{ $rfq->doc_no }} · {{ $rfq->title }}
                        <span class="ml-1 rounded-full px-2 py-0.5 text-xs font-bold {{ $badge[$rfq->status] ?? '' }}">{{ ucfirst($rfq->status) }}</span></h2>
                    <p class="text-sm text-erp-mute">{{ $branchName }} · {{ date('d M Y', strtotime($rfq->rfq_date)) }}{{ $rfq->due_date ? ' · reply by ' . date('d M Y', strtotime($rfq->due_date)) : '' }}{{ $rfq->notes ? ' · ' . $rfq->notes : '' }}</p>
                </div>
                <div class="flex gap-2" x-data="{ copied: false }">
                    <textarea x-ref="req" class="hidden">{{ $requestText }}</textarea>
                    <button type="button" class="{{ $btn }}" x-on:click="navigator.clipboard.writeText($refs.req.value); copied = true; setTimeout(() => copied = false, 2000)">
                        <span x-text="copied ? 'Copied' : 'Copy request text'"></span></button>
                    @if ($open)<button type="button" wire:click="cancelRfq" wire:confirm="Cancel this RFQ?" class="{{ $btn }} text-rose-700">Cancel RFQ</button>@endif
                </div>
            </div>

            {{-- vendors --}}
            <div class="border-b border-erp-line px-5 py-4">
                <h3 class="text-sm font-bold text-erp-ink">Vendors</h3>
                <table class="mt-2 min-w-full text-sm">
                    <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                        <th class="py-2">Vendor</th><th class="px-2 py-2">Status</th><th class="px-2 py-2 text-right">Items quoted</th><th class="px-2 py-2 text-right">Other charges</th>
                        <th class="px-2 py-2 text-right">Total</th><th class="px-2 py-2">Delivery</th><th class="px-2 py-2">Valid until</th><th></th>
                    </tr></thead>
                    @foreach ($vRows as $rvId => $v)
                        <tr class="border-b border-erp-line {{ $cmp['recommended'] === $rvId ? 'bg-emerald-50/50' : '' }}" wire:key="rv-{{ $rvId }}">
                            <td class="py-2 font-semibold">{{ $v['vendor_name'] }}
                                @if ($cmp['recommended'] === $rvId)<span class="ml-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-800">Lowest full quote</span>@endif
                                @if ($v['notes'])<div class="text-xs font-normal text-erp-mute">{{ $v['notes'] }}</div>@endif
                            </td>
                            <td class="px-2 py-2"><span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $badge[$v['status']] ?? '' }}">{{ ucfirst($v['status']) }}</span></td>
                            <td class="px-2 py-2 text-right tabular-nums">{{ $v['items_quoted'] }} / {{ count($cmp['lines']) }}</td>
                            <td class="px-2 py-2 text-right tabular-nums">{{ (float) $v['other_charges'] ? $money($v['other_charges']) : '—' }}</td>
                            <td class="px-2 py-2 text-right font-semibold tabular-nums">{{ $v['status'] === 'quoted' ? $money($v['total']) : '—' }}</td>
                            <td class="px-2 py-2">{{ $v['delivery_days'] !== null ? $v['delivery_days'] . ' days' : '—' }}</td>
                            <td class="px-2 py-2 {{ $v['valid_until'] && $v['valid_until'] < date('Y-m-d') ? 'text-rose-700' : '' }}">{{ $v['valid_until'] ? date('d M Y', strtotime($v['valid_until'])) : '—' }}</td>
                            <td class="whitespace-nowrap py-2 text-right">
                                @if ($v['vendor_bill_id'])
                                    <a href="{{ url('vendor-bills') }}?bill={{ $v['vendor_bill_id'] }}" class="text-xs font-bold text-erp hover:underline">Open bill</a>
                                @elseif ($open)
                                    <button type="button" wire:click="editQuote({{ $rvId }})" class="text-xs font-bold text-erp hover:underline">{{ $v['status'] === 'quoted' ? 'Edit quote' : 'Enter quote' }}</button>
                                    @if ($v['status'] !== 'declined')<button type="button" wire:click="decline({{ $rvId }})" class="ml-2 text-xs font-bold text-erp-mute hover:underline">Declined</button>@endif
                                    @if ($v['status'] !== 'quoted')<button type="button" wire:click="removeVendor({{ $rvId }})" wire:confirm="Remove this vendor?" class="ml-2 text-xs font-bold text-rose-700 hover:underline">Remove</button>@endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
                @if ($open)
                    <div class="mt-3 flex max-w-md gap-2">
                        <select wire:model="addVendorId" class="{{ $input }}">
                            <option value="">Add another vendor…</option>
                            @foreach ($vendors->whereNotIn('id', collect($vRows)->pluck('vendor_id')) as $v)<option value="{{ $v->id }}">{{ $v->vendor_name }}</option>@endforeach
                        </select>
                        <button type="button" wire:click="addVendor" class="{{ $btn }}">Add</button>
                    </div>
                @endif
            </div>

            {{-- quote entry --}}
            @if ($quoteFor && isset($vRows[$quoteFor]))
                <div class="border-b border-erp-line bg-slate-50 px-5 py-4">
                    <h3 class="text-sm font-bold text-erp-ink">Quote from {{ $vRows[$quoteFor]['vendor_name'] }}</h3>
                    <p class="text-xs text-erp-mute">Leave the rate empty for an item the vendor did not quote.</p>
                    <table class="mt-2 min-w-full text-sm">
                        <thead><tr class="text-left {{ $labelClass }}"><th class="py-2">Item</th><th class="px-2 py-2 text-right">Qty</th><th class="w-32 px-2 py-2">Rate</th><th class="w-24 px-2 py-2">Disc %</th><th class="w-24 px-2 py-2">Tax %</th></tr></thead>
                        @foreach ($cmp['lines'] as $l)
                            <tr wire:key="q-{{ $quoteFor }}-{{ $l['id'] }}">
                                <td class="py-1">{{ $l['product_name'] }}</td>
                                <td class="px-2 py-1 text-right tabular-nums">{{ $qty($l['qty']) }}</td>
                                <td class="px-2 py-1"><input type="number" step="any" min="0" wire:model="quote.prices.{{ $l['id'] }}.unit_price" class="{{ $input }} text-right"></td>
                                <td class="px-2 py-1"><input type="number" step="any" min="0" max="100" wire:model="quote.prices.{{ $l['id'] }}.discount_pct" class="{{ $input }} text-right"></td>
                                <td class="px-2 py-1"><input type="number" step="any" min="0" max="100" wire:model="quote.prices.{{ $l['id'] }}.tax_rate" class="{{ $input }} text-right"></td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="mt-3 grid gap-3 md:grid-cols-4">
                        <label class="block"><span class="{{ $labelClass }}">Other charges (freight…)</span><input type="number" step="any" min="0" wire:model="quote.other_charges" class="mt-1 {{ $input }}"></label>
                        <label class="block"><span class="{{ $labelClass }}">Delivery (days)</span><input type="number" min="0" wire:model="quote.delivery_days" class="mt-1 {{ $input }}"></label>
                        <label class="block"><span class="{{ $labelClass }}">Valid until</span><input type="date" wire:model="quote.valid_until" class="mt-1 {{ $input }}"></label>
                        <label class="block"><span class="{{ $labelClass }}">Note</span><input type="text" wire:model="quote.notes" class="mt-1 {{ $input }}" placeholder="payment terms…"></label>
                    </div>
                    <div class="mt-3 flex justify-end gap-2">
                        <button type="button" wire:click="$set('quoteFor', null)" class="{{ $btn }}">Close</button>
                        <button type="button" wire:click="saveQuote" class="{{ $btnPrimary }}">Save Quote</button>
                    </div>
                </div>
            @endif

            {{-- comparison --}}
            <div class="px-5 py-4">
                <h3 class="text-sm font-bold text-erp-ink">Comparison</h3>
                <p class="text-xs text-erp-mute">Net rate = rate − discount + tax. Green = lowest net rate for the item.{{ $open ? ' Choose who to buy each item from, then Create Vendor Bills.' : '' }}</p>
                <div class="mt-2 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                            <th class="py-2">Item</th><th class="px-2 py-2 text-right">Qty</th><th class="px-2 py-2 text-right">Last bought</th>
                            @foreach ($vRows as $v)<th class="px-2 py-2 text-right">{{ $v['vendor_name'] }}</th>@endforeach
                            <th class="px-2 py-2">Buy from</th>
                        </tr></thead>
                        @foreach ($cmp['lines'] as $l)
                            <tr class="border-b border-erp-line" wire:key="c-{{ $l['id'] }}">
                                <td class="py-2">{{ $l['product_name'] }}@if ($l['notes'])<div class="text-xs text-erp-mute">{{ $l['notes'] }}</div>@endif</td>
                                <td class="px-2 py-2 text-right tabular-nums">{{ $qty($l['qty']) }}</td>
                                <td class="px-2 py-2 text-right text-xs text-erp-mute">@if ($l['last_price']){{ $money($l['last_price']['price']) }}<div>{{ $l['last_price']['vendor'] }}</div>@else — @endif</td>
                                @foreach ($vRows as $rvId => $v)
                                    @php $q = $l['quotes'][$rvId] ?? null; @endphp
                                    <td class="px-2 py-2 text-right tabular-nums {{ $q && $q['best'] ? 'bg-emerald-50 font-bold text-emerald-800' : '' }}">
                                        @if ($q)
                                            {{ $money($q['net']) }}
                                            <div class="text-xs font-normal text-erp-mute">{{ $money($q['total']) }}@if ($q['awarded']) · bought @endif</div>
                                        @else — @endif
                                    </td>
                                @endforeach
                                <td class="px-2 py-2">
                                    @if ($open)
                                        <select wire:model="awards.{{ $l['id'] }}" class="{{ $input }} min-w-[10rem]">
                                            <option value="">Don't buy</option>
                                            @foreach ($l['quotes'] as $rvId => $q)<option value="{{ $rvId }}">{{ $vRows[$rvId]['vendor_name'] }}</option>@endforeach
                                        </select>
                                    @else
                                        {{ collect($l['quotes'])->filter(fn ($q) => $q['awarded'])->keys()->map(fn ($id) => $vRows[$id]['vendor_name'])->join(', ') ?: '—' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr class="font-semibold">
                            <td class="py-2" colspan="3">Total (incl. other charges)</td>
                            @foreach ($vRows as $v)<td class="px-2 py-2 text-right tabular-nums">{{ $v['status'] === 'quoted' ? $money($v['total']) : '—' }}@if ($v['status'] === 'quoted' && !$v['complete'])<div class="text-xs font-normal text-amber-700">partial</div>@endif</td>@endforeach
                            <td class="px-2 py-2 text-xs font-normal text-erp-mute">Cheapest per item: {{ $money($cmp['split_total']) }}</td>
                        </tr>
                    </table>
                </div>
                @if ($open && collect($vRows)->where('status', 'quoted')->count())
                    <div class="mt-4 flex justify-end">
                        <button type="button" wire:click="award" wire:confirm="Create a draft vendor bill for each selected vendor? The RFQ will be closed." class="{{ $btnPrimary }}">Create Vendor Bills</button>
                    </div>
                @endif
            </div>
        </div>

    @else
        {{-- list --}}
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Request for Quotation</h2>
                    <p class="mt-0.5 text-sm text-erp-mute">Ask several vendors for rates, compare their quotes item by item, and turn the best ones into vendor bills.</p>
                </div>
                <div class="flex gap-2">
                    <select wire:model.live="statusFilter" class="{{ $input }} w-36">
                        <option value="">All</option><option value="open">Open</option><option value="awarded">Awarded</option><option value="cancelled">Cancelled</option>
                    </select>
                    <button type="button" wire:click="newRfq" class="{{ $btnPrimary }} whitespace-nowrap">+ New RFQ</button>
                </div>
            </div>
            <table class="min-w-full text-sm">
                <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                    <th class="px-5 py-3">RFQ</th><th class="px-3 py-3">Date</th><th class="px-3 py-3">Branch</th><th class="px-3 py-3 text-right">Items</th><th class="px-3 py-3 text-right">Quotes</th><th class="px-5 py-3">Status</th>
                </tr></thead>
                @forelse ($list as $r)
                    <tr class="cursor-pointer border-b border-erp-line hover:bg-slate-50" wire:click="open({{ $r->id }})" wire:key="r-{{ $r->id }}">
                        <td class="px-5 py-3"><span class="font-mono text-xs text-erp-mute">{{ $r->doc_no }}</span> <span class="font-semibold text-erp-ink">{{ $r->title }}</span></td>
                        <td class="px-3 py-3">{{ date('d M Y', strtotime($r->rfq_date)) }}</td>
                        <td class="px-3 py-3">{{ $r->branch_name }}</td>
                        <td class="px-3 py-3 text-right tabular-nums">{{ $r->items }}</td>
                        <td class="px-3 py-3 text-right tabular-nums">{{ $r->quoted }} / {{ $r->vendor_count }}</td>
                        <td class="px-5 py-3"><span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $badge[$r->status] ?? '' }}">{{ ucfirst($r->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-erp-mute">No RFQs.</td></tr>
                @endforelse
            </table>
        </div>
    @endif
</div>
