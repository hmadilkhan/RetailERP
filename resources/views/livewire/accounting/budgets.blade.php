<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $cellInput = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $btn = 'h-9 rounded-lg border border-erp-line bg-white px-3 text-sm font-bold text-erp-text transition hover:bg-slate-50';
        $btnPrimary = 'h-9 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:opacity-90';
        $money = fn ($v) => number_format((float) $v, 0);
        $fy = fn ($y) => \Illuminate\Support\Carbon::parse($y->start_date)->format('M Y') . ' – ' . \Illuminate\Support\Carbon::parse($y->end_date)->format('M Y');
    @endphp

    @if (session()->has('bg_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity>{{ session('bg_message') }}</div>
    @endif
    @error('bg')
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ $message }}</div>
    @enderror

    @if (!$viewId)
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Budgets</h2>
                <p class="mt-0.5 text-sm text-erp-mute">Plan income and expenses per month for a fiscal year, then compare with the actual figures from the ledger. A budget can be for the whole company, one branch or one cost center.</p>
            </div>
            <div class="grid gap-3 border-b border-erp-line p-5 md:grid-cols-5">
                <label class="block">
                    <span class="{{ $labelClass }}">Fiscal year</span>
                    <select wire:model="newYear" class="mt-1 {{ $cellInput }}">
                        <option value="">—</option>
                        @foreach ($years as $y)<option value="{{ $y->id }}">{{ $fy($y) }}</option>@endforeach
                    </select>
                </label>
                <label class="block md:col-span-2"><span class="{{ $labelClass }}">Name</span><input type="text" wire:model="newName" class="mt-1 {{ $cellInput }}" placeholder="e.g. FY 2026-27 company budget"></label>
                <label class="block">
                    <span class="{{ $labelClass }}">Branch</span>
                    <select wire:model="newBranch" class="mt-1 {{ $cellInput }}">
                        <option value="">Whole company</option>
                        @foreach ($branches as $b)<option value="{{ $b->branch_id }}">{{ $b->branch_name }}</option>@endforeach
                    </select>
                </label>
                @if ($centers->count())
                    <label class="block">
                        <span class="{{ $labelClass }}">Cost center</span>
                        <select wire:model="newCostCenter" class="mt-1 {{ $cellInput }}">
                            <option value="">All</option>
                            @foreach ($centers as $c)<option value="{{ $c->id }}">{{ $c->code }} {{ $c->name }}</option>@endforeach
                        </select>
                    </label>
                @endif
                <div class="flex items-end justify-end md:col-span-5"><button type="button" wire:click="create" class="{{ $btnPrimary }}">+ New Budget</button></div>
            </div>
            <table class="min-w-full text-sm">
                @forelse ($list as $b)
                    <tr class="cursor-pointer border-b border-erp-line hover:bg-slate-50" wire:click="open({{ $b->id }})" wire:key="bg-{{ $b->id }}">
                        <td class="px-5 py-3 font-semibold text-erp-ink">{{ $b->name }}</td>
                        <td class="px-3 py-3">{{ $fy($b) }}</td>
                        <td class="px-5 py-3 text-erp-mute">{{ $b->branch_name ?? 'Whole company' }}{{ $b->cost_center_id ? ' · cost center' : '' }}</td>
                    </tr>
                @empty
                    <tr><td class="px-5 py-10 text-center text-sm text-erp-mute">No budgets yet.</td></tr>
                @endforelse
            </table>
        </div>
    @else
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <button type="button" wire:click="back" class="text-xs font-bold text-erp hover:underline">&larr; All budgets</button>
                    <h2 class="mt-1 text-lg font-bold text-erp-ink">{{ $budget->name }}</h2>
                </div>
                <div class="flex gap-1">
                    @foreach (['report' => 'Budget vs Actual', 'edit' => 'Edit Budget'] as $key => $label)
                        <button type="button" wire:click="$set('tab', '{{ $key }}')" class="{{ $tab === $key ? $btnPrimary : $btn }}">{{ $label }}</button>
                    @endforeach
                </div>
            </div>

            @if ($tab === 'edit')
                <div class="flex flex-wrap items-center gap-2 border-b border-erp-line px-5 py-3 text-sm">
                    <span>Fill from last year's actuals</span>
                    <input type="number" step="any" wire:model="lastYearPct" class="{{ $cellInput }} w-24">
                    <span>%</span>
                    <button type="button" wire:click="fillFromLastYear" class="{{ $btn }}">Fill</button>
                    <span class="text-xs text-erp-mute">or type a yearly amount in the first column and press ↦ to spread it over the months.</span>
                    <div class="ml-auto flex gap-2">
                        <button type="button" wire:click="deleteBudget" wire:confirm="Delete this budget?" class="{{ $btn }} text-rose-700">Delete</button>
                        <button type="button" wire:click="save" class="{{ $btnPrimary }}">Save</button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                            <th class="sticky left-0 bg-white px-3 py-2">Account</th><th class="px-2 py-2">Year</th>
                            @foreach ($periods as $p)<th class="px-1 py-2 text-right">{{ \Illuminate\Support\Carbon::parse($p . '-01')->format('M y') }}</th>@endforeach
                        </tr></thead>
                        <tbody>
                            @foreach (['Revenue' => 'Income', 'Expense' => 'Expenses'] as $type => $title)
                                <tr class="bg-slate-50"><td colspan="{{ count($periods) + 2 }}" class="px-3 py-1 font-bold uppercase tracking-wider text-erp-mute">{{ $title }}</td></tr>
                                @foreach ($accounts->where('type', $type) as $a)
                                    <tr class="border-b border-erp-line" wire:key="row-{{ $a->id }}">
                                        <td class="sticky left-0 whitespace-nowrap bg-white px-3 py-1"><span class="font-mono text-erp-mute">{{ $a->code }}</span> {{ $a->name }}</td>
                                        <td class="px-1 py-1"><div class="flex w-32 gap-1"><input type="number" step="any" min="0" wire:model="annual.{{ $a->id }}" class="{{ $cellInput }} h-8 text-right"><button type="button" wire:click="spread({{ $a->id }})" class="font-bold text-erp" title="Spread over months">↦</button></div></td>
                                        @foreach ($periods as $p)
                                            <td class="px-1 py-1"><input type="number" step="any" min="0" wire:model="grid.{{ $a->id }}.{{ $p }}" class="{{ $cellInput }} h-8 w-24 text-right"></td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex flex-wrap items-center gap-2 border-b border-erp-line px-5 py-3 text-sm">
                    <span>Months</span>
                    <select wire:model.live="fromPeriod" class="{{ $cellInput }} w-32">@foreach ($periods as $p)<option value="{{ $p }}">{{ \Illuminate\Support\Carbon::parse($p . '-01')->format('M Y') }}</option>@endforeach</select>
                    <span>to</span>
                    <select wire:model.live="toPeriod" class="{{ $cellInput }} w-32">@foreach ($periods as $p)<option value="{{ $p }}">{{ \Illuminate\Support\Carbon::parse($p . '-01')->format('M Y') }}</option>@endforeach</select>
                    <span class="text-xs text-erp-mute">Variance: positive = better than budget (more income / less expense).</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                            <th class="px-5 py-3">Account</th><th class="px-3 py-3 text-right">Budget</th><th class="px-3 py-3 text-right">Actual</th>
                            <th class="px-3 py-3 text-right">Variance</th><th class="px-5 py-3 text-right">Actual % of budget</th>
                        </tr></thead>
                        <tbody>
                            @php $net = ['budget' => 0, 'actual' => 0]; @endphp
                            @foreach (['Revenue' => 'Income', 'Expense' => 'Expenses'] as $type => $title)
                                @php $rows = $report[$type]; $sb = array_sum(array_column($rows, 'budget')); $sa = array_sum(array_column($rows, 'actual'));
                                     $net['budget'] += $type === 'Revenue' ? $sb : -$sb; $net['actual'] += $type === 'Revenue' ? $sa : -$sa; @endphp
                                <tr class="bg-slate-50"><td colspan="5" class="px-5 py-2 text-xs font-bold uppercase tracking-wider text-erp-mute">{{ $title }}</td></tr>
                                @forelse ($rows as $r)
                                    <tr class="border-b border-erp-line">
                                        <td class="px-5 py-2"><span class="font-mono text-xs text-erp-mute">{{ $r['code'] }}</span> {{ $r['name'] }}</td>
                                        <td class="px-3 py-2 text-right tabular-nums">{{ $money($r['budget']) }}</td>
                                        <td class="px-3 py-2 text-right tabular-nums">{{ $money($r['actual']) }}</td>
                                        <td class="px-3 py-2 text-right tabular-nums {{ $r['variance'] < 0 ? 'text-rose-700' : 'text-emerald-700' }}">{{ $money($r['variance']) }}</td>
                                        <td class="px-5 py-2 text-right tabular-nums">{{ $r['pct'] === null ? '—' : $r['pct'] . '%' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-5 py-2 text-erp-mute">Nothing budgeted or posted.</td></tr>
                                @endforelse
                                <tr class="font-semibold"><td class="px-5 py-2">Total {{ strtolower($title) }}</td><td class="px-3 py-2 text-right tabular-nums">{{ $money($sb) }}</td><td class="px-3 py-2 text-right tabular-nums">{{ $money($sa) }}</td><td colspan="2"></td></tr>
                            @endforeach
                            <tr class="font-bold"><td class="px-5 py-3">Net profit / (loss)</td>
                                <td class="px-3 py-3 text-right tabular-nums">{{ $money($net['budget']) }}</td>
                                <td class="px-3 py-3 text-right tabular-nums">{{ $money($net['actual']) }}</td>
                                <td class="px-3 py-3 text-right tabular-nums {{ $net['actual'] - $net['budget'] < 0 ? 'text-rose-700' : 'text-emerald-700' }}">{{ $money($net['actual'] - $net['budget']) }}</td><td></td></tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>
