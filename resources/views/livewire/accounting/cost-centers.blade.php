<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $cellInput = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $btn = 'h-9 rounded-lg border border-erp-line bg-white px-3 text-sm font-bold text-erp-text transition hover:bg-slate-50';
        $btnPrimary = 'h-9 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:opacity-90';
        $money = fn ($v) => $v == 0 ? '' : number_format((float) $v, 0);
        $tabs = ['report' => 'P&L by Cost Center', 'centers' => 'Cost Centers', 'rules' => 'Automatic Rules'];
    @endphp

    @if (session()->has('cc_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition.opacity>{{ session('cc_message') }}</div>
    @endif

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Cost Centers</h2>
            <p class="mt-0.5 text-sm text-erp-mute">Track income and expenses by department or project, across branches. Tag journal entry lines with a cost center, or set rules so automatic postings are tagged for you.</p>
        </div>
        <div class="flex flex-wrap gap-1 border-b border-erp-line px-5 pt-3">
            @foreach ($tabs as $key => $label)
                <button type="button" wire:click="$set('tab', '{{ $key }}')"
                        class="rounded-t-lg px-4 py-2 text-sm font-bold {{ $tab === $key ? 'border border-b-white border-erp-line bg-white text-erp-ink -mb-px' : 'text-erp-mute hover:text-erp-ink' }}">{{ $label }}</button>
            @endforeach
        </div>

        @if ($tab === 'centers')
            <div class="grid gap-3 border-b border-erp-line p-5 md:grid-cols-4">
                <label class="block"><span class="{{ $labelClass }}">Code</span><input type="text" wire:model="newCode" class="mt-1 {{ $cellInput }}" placeholder="e.g. MKT">
                    @error('newCode') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror</label>
                <label class="block md:col-span-2"><span class="{{ $labelClass }}">Name</span><input type="text" wire:model="newName" class="mt-1 {{ $cellInput }}" placeholder="e.g. Marketing, Kitchen, Online store">
                    @error('newName') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror</label>
                <div class="flex items-end"><button type="button" wire:click="addCenter" class="{{ $btnPrimary }}">Add</button></div>
            </div>
            <table class="min-w-full text-sm">
                @forelse ($centers as $c)
                    <tr class="border-b border-erp-line" wire:key="cc-{{ $c->id }}">
                        <td class="px-5 py-3 font-semibold text-erp-ink">{{ $c->code }}</td><td class="px-3 py-3">{{ $c->name }}</td>
                        <td class="px-3 py-3">{{ $c->is_active ? 'Active' : 'Off' }}</td>
                        <td class="px-5 py-3 text-right"><button type="button" wire:click="toggleCenter({{ $c->id }})" class="text-xs font-bold text-erp hover:underline">{{ $c->is_active ? 'Turn off' : 'Turn on' }}</button></td>
                    </tr>
                @empty
                    <tr><td class="px-5 py-10 text-center text-sm text-erp-mute">No cost centers yet.</td></tr>
                @endforelse
            </table>
        @elseif ($tab === 'rules')
            <div class="grid gap-6 p-5 md:grid-cols-2">
                <div>
                    <h3 class="text-sm font-bold text-erp-ink">Branch default</h3>
                    <p class="text-xs text-erp-mute">Income and expense lines of automatic postings (sales, expenses, payroll…) for the branch, unless a more specific rule applies.</p>
                    @foreach ($branches as $b)
                        <label class="mt-2 grid grid-cols-2 items-center gap-2 text-sm" wire:key="bb-{{ $b->branch_id }}">
                            <span>{{ $b->branch_name }}</span>
                            <select wire:model="branchRule.{{ $b->branch_id }}" class="{{ $cellInput }}">
                                <option value="">—</option>
                                @foreach ($centers->where('is_active', 1) as $c)<option value="{{ $c->id }}">{{ $c->code }} {{ $c->name }}</option>@endforeach
                            </select>
                        </label>
                    @endforeach
                </div>
                <div>
                    <h3 class="text-sm font-bold text-erp-ink">Expense category</h3>
                    <p class="text-xs text-erp-mute">Expense of this category goes to the cost center (overrides the branch default).</p>
                    <div class="max-h-[28rem] overflow-auto">
                        @forelse ($categories as $cat)
                            <label class="mt-2 grid grid-cols-2 items-center gap-2 text-sm" wire:key="ec-{{ $cat->exp_cat_id }}">
                                <span>{{ $cat->expense_category }}</span>
                                <select wire:model="categoryRule.{{ $cat->exp_cat_id }}" class="{{ $cellInput }}">
                                    <option value="">—</option>
                                    @foreach ($centers->where('is_active', 1) as $c)<option value="{{ $c->id }}">{{ $c->code }} {{ $c->name }}</option>@endforeach
                                </select>
                            </label>
                        @empty
                            <p class="mt-2 text-sm text-erp-mute">No expense categories.</p>
                        @endforelse
                    </div>
                </div>
                <div class="md:col-span-2 flex justify-end"><button type="button" wire:click="saveRules" class="{{ $btnPrimary }}">Save Rules</button></div>
            </div>
        @else
            <div class="grid gap-3 border-b border-erp-line p-5 md:grid-cols-4">
                <label class="block"><span class="{{ $labelClass }}">From</span><input type="date" wire:model.live="from" class="mt-1 {{ $cellInput }}"></label>
                <label class="block"><span class="{{ $labelClass }}">To</span><input type="date" wire:model.live="to" class="mt-1 {{ $cellInput }}"></label>
                <label class="block">
                    <span class="{{ $labelClass }}">Branch</span>
                    <select wire:model.live="branchId" class="mt-1 {{ $cellInput }}">
                        <option value="">All branches</option>
                        @foreach ($branches as $b)<option value="{{ $b->branch_id }}">{{ $b->branch_name }}</option>@endforeach
                    </select>
                </label>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                        <th class="px-5 py-3">Account</th>
                        @foreach ($columns as $c)<th class="px-3 py-3 text-right">{{ $c->code }}</th>@endforeach
                        @if ($hasUnassigned)<th class="px-3 py-3 text-right">Unassigned</th>@endif
                        <th class="px-5 py-3 text-right">Total</th>
                    </tr></thead>
                    <tbody>
                        @foreach (['Revenue' => 'Income', 'Expense' => 'Expenses'] as $type => $title)
                            @if (!empty($report[$type]))
                                <tr class="bg-slate-50"><td colspan="{{ $columns->count() + ($hasUnassigned ? 3 : 2) }}" class="px-5 py-2 text-xs font-bold uppercase tracking-wider text-erp-mute">{{ $title }}</td></tr>
                                @foreach ($report[$type] as $acc)
                                    @php $sign = $type === 'Revenue' ? 1 : -1; @endphp
                                    <tr class="border-b border-erp-line">
                                        <td class="px-5 py-2"><span class="font-mono text-xs text-erp-mute">{{ $acc['code'] }}</span> {{ $acc['name'] }}</td>
                                        @foreach ($columns as $c)<td class="px-3 py-2 text-right tabular-nums">{{ $money($sign * ($acc['cols'][$c->id] ?? 0)) }}</td>@endforeach
                                        @if ($hasUnassigned)<td class="px-3 py-2 text-right tabular-nums text-erp-mute">{{ $money($sign * ($acc['cols'][0] ?? 0)) }}</td>@endif
                                        <td class="px-5 py-2 text-right font-semibold tabular-nums">{{ $money($sign * array_sum($acc['cols'])) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                        <tr class="font-bold">
                            <td class="px-5 py-3">Net profit / (loss)</td>
                            @foreach ($columns as $c)<td class="px-3 py-3 text-right tabular-nums {{ ($totals[$c->id] ?? 0) < 0 ? 'text-rose-700' : '' }}">{{ number_format($totals[$c->id] ?? 0, 0) }}</td>@endforeach
                            @if ($hasUnassigned)<td class="px-3 py-3 text-right tabular-nums {{ ($totals[0] ?? 0) < 0 ? 'text-rose-700' : '' }}">{{ number_format($totals[0] ?? 0, 0) }}</td>@endif
                            <td class="px-5 py-3 text-right tabular-nums {{ array_sum($totals) < 0 ? 'text-rose-700' : '' }}">{{ number_format(array_sum($totals), 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
