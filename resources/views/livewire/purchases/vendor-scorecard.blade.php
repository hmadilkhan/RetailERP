<div class="space-y-6">
    @php
        $cellInput = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $money = fn ($v) => number_format((float) $v, 0);
        $pct = fn ($v) => $v === null ? '—' : rtrim(rtrim(number_format((float) $v, 1), '0'), '.') . '%';
    @endphp

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Vendor Scorecard</h2>
            <p class="mt-0.5 text-sm text-erp-mute">
                How each vendor performed in the period: purchases and returns from Vendor Bills, the change in price for the same items (first vs last price),
                and for purchase orders, delivery on time and how much of the order arrived. A dash means there is no data for that measure.
            </p>
        </div>
        <div class="grid gap-3 border-b border-erp-line p-5 md:grid-cols-4">
            <label class="block"><span class="{{ $labelClass }}">From</span><input type="date" wire:model.live="from" class="mt-1 {{ $cellInput }}"></label>
            <label class="block"><span class="{{ $labelClass }}">To</span><input type="date" wire:model.live="to" class="mt-1 {{ $cellInput }}"></label>
            <label class="block">
                <span class="{{ $labelClass }}">Branch</span>
                <select wire:model.live="branchId" class="mt-1 {{ $cellInput }}">
                    <option value="">All branches</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-erp-line text-left {{ $labelClass }}">
                        <th class="px-5 py-3">Vendor</th>
                        <th class="px-3 py-3 text-right">Bills</th>
                        <th class="px-3 py-3 text-right">Purchases</th>
                        <th class="px-3 py-3 text-right" title="Debit notes ÷ purchases">Returned</th>
                        <th class="px-3 py-3 text-right">Credit notes</th>
                        <th class="px-3 py-3 text-right" title="Same items: last price vs first price in the period, weighted by value">Price change</th>
                        <th class="px-3 py-3 text-right">POs</th>
                        <th class="px-3 py-3 text-right" title="First receipt on or before the PO delivery date">On time</th>
                        <th class="px-3 py-3 text-right" title="Received ÷ ordered">Fill rate</th>
                        <th class="px-5 py-3 text-right">Payable now</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr class="border-b border-erp-line" wire:key="vs-{{ $row['vendor_id'] }}">
                            <td class="px-5 py-3 font-semibold text-erp-ink">{{ $row['name'] }}</td>
                            <td class="px-3 py-3 text-right tabular-nums">{{ $row['bills'] ?: '' }}</td>
                            <td class="px-3 py-3 text-right tabular-nums">{{ $row['purchases'] ? $money($row['purchases']) : '' }}</td>
                            <td class="px-3 py-3 text-right tabular-nums {{ ($row['return_pct'] ?? 0) >= 5 ? 'font-bold text-rose-700' : '' }}">{{ $pct($row['return_pct']) }}</td>
                            <td class="px-3 py-3 text-right tabular-nums">{{ $row['credit_notes'] ? $money($row['credit_notes']) : '' }}</td>
                            <td class="px-3 py-3 text-right tabular-nums {{ ($row['price_change_pct'] ?? 0) > 0 ? 'text-rose-700' : (($row['price_change_pct'] ?? 0) < 0 ? 'text-emerald-700' : '') }}">
                                {{ $row['price_change_pct'] === null ? '—' : (($row['price_change_pct'] > 0 ? '+' : '') . $pct($row['price_change_pct'])) }}
                            </td>
                            <td class="px-3 py-3 text-right tabular-nums">{{ $row['pos'] ?: '' }}</td>
                            <td class="px-3 py-3 text-right tabular-nums {{ $row['on_time_pct'] !== null && $row['on_time_pct'] < 80 ? 'font-bold text-rose-700' : '' }}">{{ $pct($row['on_time_pct']) }}</td>
                            <td class="px-3 py-3 text-right tabular-nums {{ $row['fill_pct'] !== null && $row['fill_pct'] < 90 ? 'font-bold text-rose-700' : '' }}">{{ $pct($row['fill_pct']) }}</td>
                            <td class="px-5 py-3 text-right tabular-nums">{{ $row['payable'] ? $money($row['payable']) : '' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-5 py-10 text-center text-sm text-erp-mute">No vendor bills or purchase orders in this period. Record purchases with Vendor Bills and they will show up here.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
