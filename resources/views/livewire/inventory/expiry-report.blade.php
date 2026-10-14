<div class="space-y-6">
    @php
        $cellInput = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $money = fn ($v) => number_format((float) $v, 2);
        $qty = fn ($v) => rtrim(rtrim(number_format((float) $v, 4, '.', ','), '0'), '.');
        $today = \Illuminate\Support\Carbon::today();
    @endphp

    @if (!$enabled)
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">
            Batch / expiry tracking is not switched on yet (database update pending). Ask your administrator to run the latest update.
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2">
            <button type="button" wire:click="$set('status', 'expired')" class="rounded-lg border p-5 text-left shadow-sm transition {{ $status === 'expired' ? 'border-rose-300 bg-rose-50' : 'border-erp-line bg-white hover:bg-slate-50' }}">
                <div class="{{ $labelClass }}">Expired, still in stock</div>
                <div class="mt-2 text-2xl font-bold text-rose-700">{{ $summary['expired']['lots'] }} <span class="text-sm font-semibold">lots</span></div>
                <div class="mt-1 text-sm text-erp-mute">{{ $qty($summary['expired']['qty']) }} units · Rs {{ $money($summary['expired']['value']) }} at cost</div>
            </button>
            <button type="button" wire:click="$set('status', 'expiring')" class="rounded-lg border p-5 text-left shadow-sm transition {{ $status === 'expiring' ? 'border-amber-300 bg-amber-50' : 'border-erp-line bg-white hover:bg-slate-50' }}">
                <div class="{{ $labelClass }}">Expiring in the next {{ $days }} days</div>
                <div class="mt-2 text-2xl font-bold text-amber-700">{{ $summary['expiring']['lots'] }} <span class="text-sm font-semibold">lots</span></div>
                <div class="mt-1 text-sm text-erp-mute">{{ $qty($summary['expiring']['qty']) }} units · Rs {{ $money($summary['expiring']['value']) }} at cost</div>
            </button>
        </div>

        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Expiry Report</h2>
                <p class="mt-0.5 text-sm text-erp-mute">Stock lots with an expiry date that still have quantity left. Sales and transfers use the lot that expires first. Expiry dates come from Vendor Bills, purchase receipts (GRN), Stock Opening and Stock Adjustment.</p>
            </div>
            <div class="grid gap-3 border-b border-erp-line p-5 md:grid-cols-4">
                <select wire:model.live="status" class="{{ $cellInput }}">
                    <option value="expiring">Expiring soon</option>
                    <option value="expired">Expired</option>
                    <option value="all">All lots with expiry</option>
                </select>
                <select wire:model.live="days" class="{{ $cellInput }}" @disabled($status !== 'expiring')>
                    @foreach ([7, 15, 30, 60, 90, 180] as $d)
                        <option value="{{ $d }}">Next {{ $d }} days</option>
                    @endforeach
                </select>
                <select wire:model.live="branchId" class="{{ $cellInput }}">
                    <option value="">{{ count($branches) > 1 ? 'All branches' : 'My branch' }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
                <input type="text" wire:model.live.debounce.400ms="search" class="{{ $cellInput }}" placeholder="Search product, code, batch">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-erp-line text-left {{ $labelClass }}">
                            <th class="px-5 py-3">Product</th>
                            <th class="px-3 py-3">Batch</th>
                            <th class="px-3 py-3">Expiry</th>
                            <th class="px-3 py-3">Days left</th>
                            <th class="px-3 py-3">Branch</th>
                            <th class="px-3 py-3 text-right">Qty left</th>
                            <th class="px-5 py-3 text-right">Value at cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lots as $lot)
                            @php $left = $today->diffInDays(\Illuminate\Support\Carbon::parse($lot->expiry_date), false); @endphp
                            <tr class="border-b border-erp-line" wire:key="lot-{{ $lot->stock_id }}">
                                <td class="px-5 py-3 font-semibold text-erp-ink">{{ $lot->product_name }} <span class="font-normal text-erp-mute">{{ $lot->item_code }}</span></td>
                                <td class="px-3 py-3">{{ $lot->batch_no ?: '—' }}</td>
                                <td class="px-3 py-3">{{ \Illuminate\Support\Carbon::parse($lot->expiry_date)->format('d M Y') }}</td>
                                <td class="px-3 py-3">
                                    @if ($left < 0)
                                        <span class="rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-bold text-rose-700">Expired {{ abs((int) $left) }}d ago</span>
                                    @elseif ($left <= 30)
                                        <span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-700">{{ (int) $left }} days</span>
                                    @else
                                        <span class="text-erp-mute">{{ (int) $left }} days</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3">{{ $branchNames[$lot->branch_id] ?? '' }}</td>
                                <td class="px-3 py-3 text-right tabular-nums">{{ $qty($lot->balance) }}</td>
                                <td class="px-5 py-3 text-right tabular-nums">{{ $money((float) $lot->balance * (float) $lot->cost_price) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-10 text-center text-sm text-erp-mute">No lots here. Add a batch and expiry date when you receive stock (Vendor Bill, Stock Opening or Stock Adjustment) and they will show up.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3">{{ $lots->links() }}</div>
        </div>
    @endif
</div>
