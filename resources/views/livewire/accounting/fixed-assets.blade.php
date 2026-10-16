<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $inputClass = 'mt-1 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $cellInput = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $btn = 'h-9 rounded-lg border border-erp-line bg-white px-3 text-sm font-bold text-erp-text transition hover:bg-slate-50';
        $btnPrimary = 'h-9 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:opacity-90';
        $money = fn ($v) => number_format((float) $v, 2);
        $err = fn ($f) => $errors->has($f) ? '<span class="mt-1 block text-xs font-semibold text-rose-600">' . e($errors->first($f)) . '</span>' : '';
    @endphp

    @if (session()->has('fa_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition.opacity>{{ session('fa_message') }}</div>
    @endif
    @error('fa')
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ $message }}</div>
    @enderror

    @if ($viewing)
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <button type="button" wire:click="back" class="text-xs font-bold text-erp hover:underline">&larr; All assets</button>
                    <h2 class="mt-1 text-lg font-bold text-erp-ink">{{ $viewing->code }} {{ $viewing->name }}
                        <span class="ml-2 rounded-full px-2.5 py-0.5 text-xs font-bold {{ $viewing->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ ucfirst($viewing->status) }}</span></h2>
                    <p class="text-sm text-erp-mute">Bought {{ \Illuminate\Support\Carbon::parse($viewing->purchase_date)->format('d M Y') }} ·
                        {{ $viewing->method === 'declining' ? 'Declining ' . (float) $viewing->rate . '% a year' : 'Straight line over ' . $viewing->useful_life_months . ' months' }}
                        @if ($viewing->status === 'disposed') · disposed {{ \Illuminate\Support\Carbon::parse($viewing->disposed_on)->format('d M Y') }} for {{ $money($viewing->disposal_amount) }}@endif</p>
                </div>
                @if ($viewing->status === 'active' && !$disposing)
                    <button type="button" wire:click="startDispose" class="{{ $btn }} text-rose-700">Sell / Dispose</button>
                @endif
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-4">
                <div><div class="{{ $labelClass }}">Cost</div><div class="mt-1 text-lg font-bold tabular-nums">{{ $money($viewing->cost) }}</div></div>
                <div><div class="{{ $labelClass }}">Depreciated so far</div><div class="mt-1 text-lg font-bold tabular-nums">{{ $money($viewingAccumulated) }}</div></div>
                <div><div class="{{ $labelClass }}">Book value</div><div class="mt-1 text-lg font-bold tabular-nums">{{ $money((float) $viewing->cost - $viewingAccumulated) }}</div></div>
                <div><div class="{{ $labelClass }}">Salvage value</div><div class="mt-1 text-lg font-bold tabular-nums">{{ $money($viewing->salvage_value) }}</div></div>
            </div>
            @if ($disposing)
                <div class="grid gap-3 border-t border-erp-line bg-slate-50 p-5 md:grid-cols-4">
                    <label class="block"><span class="{{ $labelClass }}">Date</span><input type="date" wire:model="disposeDate" class="{{ $inputClass }}"></label>
                    <label class="block"><span class="{{ $labelClass }}">Sold for (0 = scrapped)</span><input type="number" step="any" min="0" wire:model="disposeAmount" class="{{ $inputClass }}"></label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Money received in</span>
                        <select wire:model="disposeAccount" class="{{ $inputClass }}">
                            <option value="">—</option>
                            @foreach ($payAccounts as $a)<option value="{{ $a->id }}">{{ $a->code }} {{ $a->name }}</option>@endforeach
                        </select>
                    </label>
                    <div class="flex items-end gap-2">
                        <button type="button" wire:click="$set('disposing', false)" class="{{ $btn }}">Cancel</button>
                        <button type="button" wire:click="dispose" wire:confirm="Dispose this asset? Cost and depreciation are taken off the books and any gain or loss is posted." class="{{ $btnPrimary }}">Dispose</button>
                    </div>
                    <p class="text-xs text-erp-mute md:col-span-4">Run depreciation up to the month before first. The difference between the sale price and book value goes to Gain / Loss on Asset Disposal.</p>
                </div>
            @endif
            <div class="grid gap-6 border-t border-erp-line p-5 md:grid-cols-2">
                <div>
                    <h3 class="text-sm font-bold text-erp-ink">Posted depreciation</h3>
                    <table class="mt-2 min-w-full text-sm">
                        @forelse ($history as $h)
                            <tr class="border-b border-erp-line"><td class="py-1">{{ $h->period }}</td><td class="py-1 text-erp-mute">{{ $h->entry_no }}</td><td class="py-1 text-right tabular-nums">{{ $money($h->amount) }}</td></tr>
                        @empty
                            <tr><td class="py-2 text-erp-mute">None yet{{ (float) $viewing->opening_accumulated ? ' (opening depreciation ' . $money($viewing->opening_accumulated) . ')' : '' }}.</td></tr>
                        @endforelse
                    </table>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-erp-ink">Next 12 months (estimate)</h3>
                    <table class="mt-2 min-w-full text-sm">
                        @forelse ($schedule as $p => $s)
                            <tr class="border-b border-erp-line"><td class="py-1">{{ $p }}</td><td class="py-1 text-right tabular-nums">{{ $money($s['amount']) }}</td><td class="py-1 text-right tabular-nums text-erp-mute">{{ $money($s['book_value']) }}</td></tr>
                        @empty
                            <tr><td class="py-2 text-erp-mute">Fully depreciated.</td></tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Fixed Assets</h2>
                    <p class="mt-0.5 text-sm text-erp-mute">Furniture, equipment, computers, vehicles. Depreciation is posted to the ledger every month (automatically on the 1st, or with Run below).</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <input type="month" wire:model="runUpTo" class="{{ $cellInput }} w-40">
                    <button type="button" wire:click="runDepreciation" wire:confirm="Post depreciation for all active assets up to this month?" class="{{ $btn }}">Run depreciation</button>
                    <button type="button" wire:click="$toggle('showForm')" class="{{ $btnPrimary }}">+ Add Asset</button>
                </div>
            </div>

            @if ($showForm)
                <div class="grid gap-3 border-b border-erp-line bg-slate-50 p-5 md:grid-cols-4">
                    <label class="block md:col-span-2"><span class="{{ $labelClass }}">Name</span><input type="text" wire:model="form.name" class="{{ $inputClass }}" placeholder="e.g. Delivery van, POS computers">{!! $err('form.name') !!}</label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Asset account</span>
                        <select wire:model="form.asset_account_id" class="{{ $inputClass }}">
                            <option value="">— Select —</option>
                            @foreach ($assetAccounts as $a)<option value="{{ $a->id }}">{{ $a->code }} {{ $a->name }}</option>@endforeach
                        </select>{!! $err('form.asset_account_id') !!}
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Branch</span>
                        <select wire:model="form.branch_id" class="{{ $inputClass }}">
                            <option value="">Company</option>
                            @foreach ($branches as $b)<option value="{{ $b->branch_id }}">{{ $b->branch_name }}</option>@endforeach
                        </select>
                    </label>
                    <label class="block"><span class="{{ $labelClass }}">Purchase date</span><input type="date" wire:model="form.purchase_date" class="{{ $inputClass }}">{!! $err('form.purchase_date') !!}</label>
                    <label class="block"><span class="{{ $labelClass }}">Cost</span><input type="number" step="any" min="0" wire:model="form.cost" class="{{ $inputClass }}">{!! $err('form.cost') !!}</label>
                    <label class="block"><span class="{{ $labelClass }}">Salvage value</span><input type="number" step="any" min="0" wire:model="form.salvage_value" class="{{ $inputClass }}"></label>
                    <label class="block"><span class="{{ $labelClass }}">Useful life (months)</span><input type="number" min="1" wire:model="form.useful_life_months" class="{{ $inputClass }}">{!! $err('form.useful_life_months') !!}</label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Method</span>
                        <select wire:model.live="form.method" class="{{ $inputClass }}">
                            <option value="straight_line">Straight line</option>
                            <option value="declining">Declining balance</option>
                        </select>
                    </label>
                    @if ($form['method'] === 'declining')
                        <label class="block"><span class="{{ $labelClass }}">Rate % a year</span><input type="number" step="any" min="0" wire:model="form.rate" class="{{ $inputClass }}"></label>
                    @endif
                    <label class="block"><span class="{{ $labelClass }}">Depreciate from (month)</span><input type="date" wire:model="form.depreciation_start" class="{{ $inputClass }}" title="Default: purchase month"></label>
                    <label class="block"><span class="{{ $labelClass }}">Depreciation before this system</span><input type="number" step="any" min="0" wire:model="form.opening_accumulated" class="{{ $inputClass }}"></label>
                    <label class="block md:col-span-2">
                        <span class="{{ $labelClass }}">Post the purchase from (optional)</span>
                        <select wire:model="form.paid_from" class="{{ $inputClass }}">
                            <option value="">Don't post (already in the ledger / opening balances)</option>
                            @foreach ($payAccounts as $a)<option value="{{ $a->id }}">{{ $a->code }} {{ $a->name }}</option>@endforeach
                        </select>
                    </label>
                    <div class="flex items-end justify-end gap-2 md:col-span-2">
                        <button type="button" wire:click="$set('showForm', false)" class="{{ $btn }}">Cancel</button>
                        <button type="button" wire:click="create" class="{{ $btnPrimary }}">Save Asset</button>
                    </div>
                </div>
            @endif

            <div class="flex gap-2 border-b border-erp-line px-5 py-3">
                <select wire:model.live="statusFilter" class="{{ $cellInput }} w-48">
                    <option value="active">Active</option>
                    <option value="disposed">Disposed</option>
                    <option value="">All</option>
                </select>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                        <th class="px-5 py-3">Asset</th><th class="px-3 py-3">Account</th><th class="px-3 py-3">Bought</th>
                        <th class="px-3 py-3 text-right">Cost</th><th class="px-3 py-3 text-right">Depreciated</th><th class="px-5 py-3 text-right">Book value</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($assets as $a)
                            <tr class="cursor-pointer border-b border-erp-line hover:bg-slate-50" wire:click="show({{ $a->id }})" wire:key="fa-{{ $a->id }}">
                                <td class="px-5 py-3"><span class="font-semibold text-erp-ink">{{ $a->code }}</span> {{ $a->name }}</td>
                                <td class="px-3 py-3">{{ $a->account_code }} {{ $a->account_name }}</td>
                                <td class="px-3 py-3">{{ \Illuminate\Support\Carbon::parse($a->purchase_date)->format('d M Y') }}</td>
                                <td class="px-3 py-3 text-right tabular-nums">{{ $money($a->cost) }}</td>
                                <td class="px-3 py-3 text-right tabular-nums">{{ $money($a->accumulated) }}</td>
                                <td class="px-5 py-3 text-right font-semibold tabular-nums">{{ $money($a->book_value) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-erp-mute">No assets yet. Use <b>+ Add Asset</b>.</td></tr>
                        @endforelse
                    </tbody>
                    @if ($assets->count())
                        <tfoot><tr class="font-bold"><td class="px-5 py-3" colspan="3">Total</td>
                            <td class="px-3 py-3 text-right tabular-nums">{{ $money($assets->sum('cost')) }}</td>
                            <td class="px-3 py-3 text-right tabular-nums">{{ $money($assets->sum('accumulated')) }}</td>
                            <td class="px-5 py-3 text-right tabular-nums">{{ $money($assets->sum('book_value')) }}</td></tr></tfoot>
                    @endif
                </table>
            </div>
        </div>
    @endif
</div>
