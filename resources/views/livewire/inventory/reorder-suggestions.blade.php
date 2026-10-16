<div class="space-y-6">
    @php
        $cellInput = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $btn = 'h-9 rounded-lg border border-erp-line bg-white px-3 text-sm font-bold text-erp-text transition hover:bg-slate-50';
        $btnPrimary = 'h-9 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:opacity-90';
        $qty = fn ($v) => $v === null ? '—' : rtrim(rtrim(number_format((float) $v, 2, '.', ','), '0'), '.');
    @endphp

    @if (session()->has('rs_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity>{{ session('rs_message') }}</div>
    @endif
    @error('reorder')
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ $message }}</div>
    @enderror

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
            <div class="{{ $labelClass }}">Need to order</div>
            <div class="mt-2 text-2xl font-bold text-rose-700">{{ $summary['needed'] }}</div>
            <div class="mt-1 text-sm text-erp-mute">products at or below their reorder point</div>
        </div>
        <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
            <div class="{{ $labelClass }}">Out of stock, still selling</div>
            <div class="mt-2 text-2xl font-bold text-amber-700">{{ $summary['out'] }}</div>
            <div class="mt-1 text-sm text-erp-mute">still selling, nothing on hand</div>
        </div>
        <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
            <div class="{{ $labelClass }}">Products tracked</div>
            <div class="mt-2 text-2xl font-bold text-erp-ink">{{ $summary['products'] }}</div>
            <div class="mt-1 text-sm text-erp-mute">with stock, sales or a reorder level in this branch</div>
        </div>
    </div>

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Reorder Suggestions</h2>
            <p class="mt-0.5 text-sm text-erp-mute">
                Suggests what this branch should order: <b>on hand + in transit</b> is compared with the <b>reorder point</b> (the higher of the minimum and daily sales × lead time).
                Minimum comes from the branch level, or the product's Qty Reminder if no level is set. Tick products and <b>Create Demand</b> to send a pending demand to head office.
            </p>
            @if ($method === 'forecast')
                <p class="mt-2 text-xs text-erp-mute">
                    Forecast = weekly sales of the last 26 weeks, recent weeks weighted more (a product's weeks before its first sale are ignored). ↑ / ↓ = last 4 weeks vs the 8 before.
                    @if ($accuracy)
                        Tested on this branch's past 16 weeks: forecast was off by <b class="{{ $accuracy['forecast_error'] <= $accuracy['average_error'] ? 'text-emerald-700' : 'text-amber-700' }}">{{ $accuracy['forecast_error'] }}%</b>
                        vs <b>{{ $accuracy['average_error'] }}%</b> for a simple 28-day average{{ $accuracy['forecast_error'] > $accuracy['average_error'] ? ' — for this branch the simple average has been closer' : '' }}.
                    @else
                        Not enough past sales in this branch to measure accuracy.
                    @endif
                </p>
            @endif
        </div>
        <div class="grid gap-3 border-b border-erp-line p-5 md:grid-cols-6">
            <label class="block md:col-span-2">
                <span class="{{ $labelClass }}">Branch</span>
                <select wire:model.live="branchId" class="mt-1 {{ $cellInput }}">
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="{{ $labelClass }}">Daily sales from</span>
                <select wire:model.live="method" class="mt-1 {{ $cellInput }}">
                    <option value="forecast">Forecast (recent weeks count more)</option>
                    <option value="average">Simple average</option>
                </select>
            </label>
            <label class="block">
                <span class="{{ $labelClass }}">{{ $method === 'forecast' ? 'Compare with average of' : 'Sales period' }}</span>
                <select wire:model.live="salesDays" class="mt-1 {{ $cellInput }}">
                    @foreach ([7, 14, 30, 60, 90] as $d)
                        <option value="{{ $d }}">Last {{ $d }} days</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="{{ $labelClass }}">Order for (extra days)</span>
                <select wire:model.live="coverDays" class="mt-1 {{ $cellInput }}">
                    @foreach ([3, 7, 14, 30] as $d)
                        <option value="{{ $d }}">{{ $d }} days</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="{{ $labelClass }}">Department</span>
                <select wire:model.live="departmentId" class="mt-1 {{ $cellInput }}">
                    <option value="">All</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->department_id }}">{{ $department->department_name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="{{ $labelClass }}">Show</span>
                <select wire:model.live="show" class="mt-1 {{ $cellInput }}">
                    <option value="needed">Need to order</option>
                    <option value="all">All products</option>
                </select>
            </label>
            <input type="text" wire:model.live.debounce.400ms="search" class="{{ $cellInput }} md:col-span-2" placeholder="Search product or code">
            <div class="flex flex-wrap items-center gap-2 md:col-span-4 md:justify-end">
                <span class="text-sm text-erp-mute">{{ $selectedCount }} selected</span>
                <button type="button" wire:click="selectAllSuggested" class="{{ $btn }}">Select all to order</button>
                @if ($selectedCount)
                    <button type="button" wire:click="clearSelection" class="{{ $btn }}">Clear</button>
                    <button type="button" wire:click="fillLevels" wire:confirm="Set min / max for the ticked products from their sales (min = daily sales × lead time, max = daily sales × (lead time + order-for days))?" class="{{ $btn }}">Set levels from sales</button>
                    <button type="button" wire:click="createDemand" wire:loading.attr="disabled" wire:confirm="Create a pending demand for the ticked products?" class="{{ $btnPrimary }}">Create Demand</button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-erp-line text-left {{ $labelClass }}">
                        <th class="w-10 px-5 py-3"></th>
                        <th class="px-3 py-3">Product</th>
                        <th class="px-3 py-3 text-right">On hand</th>
                        <th class="px-3 py-3 text-right">In transit</th>
                        <th class="px-3 py-3 text-right">Sold / day</th>
                        <th class="px-3 py-3 text-right">Days left</th>
                        <th class="px-3 py-3">Level (min / max · lead)</th>
                        <th class="px-3 py-3 text-right">Reorder point</th>
                        <th class="w-28 px-3 py-3 text-right">Order qty</th>
                        <th class="px-5 py-3">Stock elsewhere</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        @php $id = $row['product_id']; @endphp
                        <tr class="border-b border-erp-line {{ $row['suggest'] > 0 ? '' : 'text-erp-mute' }}" wire:key="rs-{{ $branchId }}-{{ $id }}">
                            <td class="px-5 py-3"><input type="checkbox" wire:model.live="selected.{{ $id }}" class="h-4 w-4 rounded border-erp-line"></td>
                            <td class="px-3 py-3"><span class="font-semibold text-erp-ink">{{ $row['name'] }}</span> <span class="text-erp-mute">{{ $row['code'] }}</span></td>
                            <td class="px-3 py-3 text-right tabular-nums {{ $row['on_hand'] <= 0 ? 'font-bold text-rose-700' : '' }}">{{ $qty($row['on_hand']) }}</td>
                            <td class="px-3 py-3 text-right tabular-nums">{{ $row['inbound'] ? $qty($row['inbound']) : '' }}</td>
                            <td class="px-3 py-3 text-right tabular-nums" title="Average of last {{ $salesDays }} days: {{ $qty($row['average']) }}">{{ $row['daily'] ? $qty($row['daily']) : '' }}@if ($method === 'forecast' && in_array($row['trend'], ['up', 'down', 'new'], true))<span class="ml-1 text-xs {{ $row['trend'] === 'down' ? 'text-rose-600' : 'text-emerald-600' }}">{{ ['up' => '↑', 'down' => '↓', 'new' => 'new'][$row['trend']] }}</span>@endif</td>
                            <td class="px-3 py-3 text-right tabular-nums">{{ $row['days_left'] === null ? '' : $qty($row['days_left']) }}</td>
                            <td class="px-3 py-3">
                                @if ($editId === $id)
                                    <div class="grid w-72 grid-cols-4 gap-1">
                                        <input type="number" step="any" min="0" wire:model="editMin" placeholder="Min" class="{{ $cellInput }}">
                                        <input type="number" step="any" min="0" wire:model="editMax" placeholder="Max" class="{{ $cellInput }}">
                                        <input type="number" step="any" min="0" wire:model="editReorder" placeholder="Qty" title="Reorder qty (used when no max)" class="{{ $cellInput }}">
                                        <input type="number" step="1" min="0" wire:model="editLead" placeholder="Days" title="Lead time days" class="{{ $cellInput }}">
                                    </div>
                                    <div class="mt-1 flex gap-2">
                                        <button type="button" wire:click="saveLevel" class="text-xs font-bold text-erp hover:underline">Save</button>
                                        <button type="button" wire:click="$set('editId', null)" class="text-xs font-bold text-erp-mute hover:underline">Cancel</button>
                                        <span class="text-xs text-erp-mute">Leave all empty to remove</span>
                                    </div>
                                @else
                                    <button type="button" wire:click="editLevel({{ $id }})" class="text-left hover:underline">
                                        {{ $qty($row['min']) }} / {{ $row['max'] === null ? '—' : $qty($row['max']) }} · {{ $row['lead'] }}d
                                        @if ($row['min_source'] === 'reminder')<span class="block text-xs text-erp-mute">min from Qty Reminder</span>@elseif (!$row['has_level'])<span class="block text-xs text-erp-mute">no level — set</span>@endif
                                    </button>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-right tabular-nums">{{ $qty($row['point']) }}</td>
                            <td class="px-3 py-3 text-right">
                                <input type="number" step="any" min="0" wire:model.blur="orderQty.{{ $id }}" placeholder="{{ $row['suggest'] ? $qty($row['suggest']) : '0' }}"
                                       class="{{ $cellInput }} text-right {{ $row['suggest'] > 0 ? 'font-bold' : '' }}">
                            </td>
                            <td class="px-5 py-3 text-xs">
                                @if ($row['source'])
                                    {{ $branchNames[$row['source']['branch_id']] ?? '#' . $row['source']['branch_id'] }}: <b>{{ $qty($row['source']['qty']) }}</b>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-5 py-10 text-center text-sm text-erp-mute">
                            {{ $show === 'needed' ? 'Nothing needs ordering for this branch right now.' : 'No products with stock, sales or a reorder level in this branch.' }}
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3">{{ $rows->links('livewire::tailwind') }}</div>
    </div>
</div>
