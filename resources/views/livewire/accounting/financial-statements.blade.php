<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $m = fn ($v) => abs((float) $v) < 0.005 ? '—' : ((float) $v < 0 ? '(' . number_format(abs($v), 2) . ')' : number_format((float) $v, 2));
        $plain = fn ($v) => (float) $v ? number_format((float) $v, 2) : '';
        $glLink = fn ($a) => route('general-ledger', ['account' => $a->id, 'from' => $tab === 'bs' ? '' : $from, 'to' => $to]);
        $period = $tab === 'bs' ? 'As of ' . \Carbon\Carbon::parse($to)->format('d M Y')
            : \Carbon\Carbon::parse($from ?: $to)->format('d M Y') . ' – ' . \Carbon\Carbon::parse($to)->format('d M Y');
    @endphp

    <style>
        @media print {
            .fs-no-print, aside, nav, header { display: none !important; }
            .fs-sheet { box-shadow: none !important; border: 0 !important; }
        }
    </style>

    <div class="fs-no-print rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-wrap gap-1 border-b border-erp-line px-3 pt-3">
            @foreach ($tabs as $key => $label)
                <button type="button" wire:click="setTab('{{ $key }}')"
                        class="rounded-t-lg px-4 py-2 text-sm font-bold transition {{ $tab === $key ? 'border border-b-white border-erp-line bg-white text-erp-dark -mb-px' : 'text-erp-mute hover:text-erp-ink' }}">{{ $label }}</button>
            @endforeach
        </div>
        <div class="grid gap-4 p-5 md:grid-cols-4">
            @if ($tab !== 'bs')
                <label class="block"><span class="{{ $labelClass }}">From</span><input type="date" wire:model.live="from" class="{{ $inputClass }}"></label>
            @endif
            <label class="block"><span class="{{ $labelClass }}">{{ $tab === 'bs' ? 'As of' : 'To' }}</span><input type="date" wire:model.live="to" class="{{ $inputClass }}"></label>
            <label class="block">
                <span class="{{ $labelClass }}">Branch</span>
                <select wire:model.live="branchId" class="{{ $inputClass }}">
                    <option value="">All branches</option>
                    @foreach ($branches as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </label>
            <div class="flex items-end">
                <button type="button" onclick="window.print()" class="h-10 rounded-lg border border-erp-line bg-white px-5 text-sm font-bold text-erp-text transition hover:bg-slate-50">Print</button>
            </div>
        </div>
        @if ($branchId !== '' && $tab === 'bs')
            <p class="px-5 pb-4 text-xs text-amber-700">Branch balance sheets only include entries tagged to this branch; company-level entries (vendor payments without a PO, opening balances) are left out, so it may not balance.</p>
        @endif
    </div>

    <div class="fs-sheet relative rounded-lg border border-erp-line bg-white shadow-sm">
        <div wire:loading.flex class="absolute inset-0 z-10 items-center justify-center rounded-lg bg-white/70 text-sm font-semibold text-erp-mute">Loading…</div>

        <div class="border-b border-erp-line px-6 py-5 text-center">
            <div class="text-sm font-bold uppercase tracking-wide text-erp-mute">{{ $companyName }}{{ $branchId !== '' ? ' — ' . ($branches[$branchId] ?? '') : '' }}</div>
            <h2 class="mt-1 text-xl font-black text-erp-ink">{{ $tabs[$tab] }}</h2>
            <div class="mt-1 text-sm text-erp-mute">{{ $period }}</div>
        </div>

        {{-- ===================== TRIAL BALANCE ===================== --}}
        @if ($tab === 'tb')
            @php $t = $data['totals']; $balanced = abs($t['closing_dr'] - $t['closing_cr']) < 0.01 && abs($t['debit'] - $t['credit']) < 0.01; @endphp
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left">
                        <tr>
                            <th class="px-4 py-3 {{ $labelClass }}" rowspan="2">Account</th>
                            <th class="px-4 py-2 text-center {{ $labelClass }}" colspan="2">Opening</th>
                            <th class="px-4 py-2 text-center {{ $labelClass }}" colspan="2">Period</th>
                            <th class="px-4 py-2 text-center {{ $labelClass }}" colspan="2">Closing</th>
                        </tr>
                        <tr>
                            @foreach (['Debit', 'Credit', 'Debit', 'Credit', 'Debit', 'Credit'] as $h)
                                <th class="px-4 py-2 text-right {{ $labelClass }}">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line">
                        @forelse ($data['rows'] as $a)
                            <tr class="{{ $a->is_group ? 'bg-slate-50/70 font-bold text-erp-ink' : 'text-erp-text' }}">
                                <td class="whitespace-nowrap px-4 py-2" style="padding-left: {{ 16 + $a->depth * 18 }}px">
                                    <span class="font-mono text-xs text-erp-mute">{{ $a->code }}</span>
                                    @if ($a->is_group) {{ $a->name }} @else <a href="{{ $glLink($a) }}" class="hover:text-sky-700 hover:underline">{{ $a->name }}</a> @endif
                                </td>
                                <td class="px-4 py-2 text-right">{{ $plain(max($a->opening, 0)) }}</td>
                                <td class="px-4 py-2 text-right">{{ $plain(max(-$a->opening, 0)) }}</td>
                                <td class="px-4 py-2 text-right">{{ $plain($a->debit) }}</td>
                                <td class="px-4 py-2 text-right">{{ $plain($a->credit) }}</td>
                                <td class="px-4 py-2 text-right">{{ $plain(max($a->closing, 0)) }}</td>
                                <td class="px-4 py-2 text-right">{{ $plain(max(-$a->closing, 0)) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-10 text-center text-erp-mute">No posted entries in this range.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t-2 border-erp-ink bg-slate-50 font-black text-erp-ink">
                        <tr>
                            <td class="px-4 py-3">Total {!! $balanced ? '<span class="ml-2 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] text-emerald-700">Balanced</span>' : '<span class="ml-2 rounded-full bg-rose-50 px-2 py-0.5 text-[11px] text-rose-700">Not balanced</span>' !!}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($t['opening_dr'], 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($t['opening_cr'], 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($t['debit'], 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($t['credit'], 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($t['closing_dr'], 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($t['closing_cr'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        {{-- ===================== INCOME STATEMENT ===================== --}}
        @if ($tab === 'pl')
            <div class="mx-auto max-w-3xl p-6">
                @php
                    $block = function ($sections) use ($m, $glLink) {
                        $html = '';
                        foreach ($sections as $s) {
                            $html .= '<div class="mt-4"><div class="text-sm font-black uppercase tracking-wide text-erp-ink">' . e($s['title']) . '</div>';
                            foreach ($s['lines'] as $l) {
                                $html .= '<div class="flex justify-between border-b border-dashed border-erp-line py-1.5 pl-4 text-sm text-erp-text"><a href="' . e($glLink($l['account'])) . '" class="hover:text-sky-700 hover:underline">' . e($l['account']->name) . '</a><span>' . $m($l['amount']) . '</span></div>';
                            }
                            if ($s['lines']->isEmpty()) {
                                $html .= '<div class="py-1.5 pl-4 text-sm text-erp-mute">—</div>';
                            }
                            $html .= '<div class="flex justify-between py-1.5 text-sm font-bold text-erp-ink"><span>Total ' . e($s['title']) . '</span><span>' . $m($s['total']) . '</span></div></div>';
                        }
                        return $html;
                    };
                    $result = fn ($label, $value, $strong = false) => '<div class="mt-3 flex justify-between border-y-2 border-erp-ink py-2 ' . ($strong ? 'text-lg' : 'text-base') . ' font-black ' . ($value < 0 ? 'text-rose-700' : 'text-erp-ink') . '"><span>' . $label . '</span><span>' . $m($value) . '</span></div>';
                @endphp
                {!! $block($data['revenue']) !!}
                {!! $block($data['cost']) !!}
                {!! $result('Gross Profit', $data['gross_profit']) !!}
                {!! $block($data['expenses']) !!}
                {!! $result($data['net_profit'] < 0 ? 'Net Loss' : 'Net Profit', $data['net_profit'], true) !!}
            </div>
        @endif

        {{-- ===================== BALANCE SHEET ===================== --}}
        @if ($tab === 'bs')
            @php
                $bsBlock = function ($sections) use ($m, $glLink) {
                    $html = '';
                    foreach ($sections as $s) {
                        foreach ($s['lines'] as $l) {
                            $html .= '<div class="flex justify-between border-b border-dashed border-erp-line py-1.5 pl-4 text-sm text-erp-text"><a href="' . e($glLink($l['account'])) . '" class="hover:text-sky-700 hover:underline">' . e($l['account']->name) . '</a><span>' . $m($l['amount']) . '</span></div>';
                        }
                    }
                    return $html ?: '<div class="py-1.5 pl-4 text-sm text-erp-mute">—</div>';
                };
            @endphp
            <div class="grid gap-8 p-6 lg:grid-cols-2">
                <div>
                    <div class="text-sm font-black uppercase tracking-wide text-erp-ink">Assets</div>
                    {!! $bsBlock($data['assets']) !!}
                    <div class="mt-3 flex justify-between border-y-2 border-erp-ink py-2 font-black text-erp-ink"><span>Total Assets</span><span>{{ $m($data['total_assets']) }}</span></div>
                </div>
                <div>
                    <div class="text-sm font-black uppercase tracking-wide text-erp-ink">Liabilities</div>
                    {!! $bsBlock($data['liabilities']) !!}
                    <div class="flex justify-between py-1.5 text-sm font-bold text-erp-ink"><span>Total Liabilities</span><span>{{ $m($data['total_liabilities']) }}</span></div>

                    <div class="mt-4 text-sm font-black uppercase tracking-wide text-erp-ink">Equity</div>
                    {!! $bsBlock($data['equity']) !!}
                    <div class="flex justify-between border-b border-dashed border-erp-line py-1.5 pl-4 text-sm text-erp-text"><span>Current Year Earnings <span class="text-xs text-erp-mute">(not yet closed)</span></span><span>{{ $m($data['earnings']) }}</span></div>
                    <div class="flex justify-between py-1.5 text-sm font-bold text-erp-ink"><span>Total Equity</span><span>{{ $m($data['total_equity']) }}</span></div>

                    <div class="mt-3 flex justify-between border-y-2 border-erp-ink py-2 font-black text-erp-ink"><span>Total Liabilities &amp; Equity</span><span>{{ $m($data['total_liabilities'] + $data['total_equity']) }}</span></div>
                </div>
            </div>
            <div class="border-t border-erp-line px-6 py-3 text-center text-sm font-bold {{ abs($data['difference']) < 0.01 ? 'text-emerald-700' : 'text-rose-700' }}">
                {{ abs($data['difference']) < 0.01 ? 'Balanced — Assets = Liabilities + Equity' : 'Difference ' . number_format($data['difference'], 2) }}
            </div>
        @endif

        {{-- ===================== CASH FLOW ===================== --}}
        @if ($tab === 'cf')
            <div class="mx-auto max-w-3xl p-6">
                <div class="flex justify-between py-1.5 text-sm font-bold text-erp-ink"><span>Opening Cash &amp; Bank</span><span>{{ $m($data['opening']) }}</span></div>
                @foreach ($data['sections'] as $s)
                    <div class="mt-4 text-sm font-black uppercase tracking-wide text-erp-ink">{{ $s['title'] }}</div>
                    @forelse ($s['lines'] as $l)
                        <div class="flex justify-between border-b border-dashed border-erp-line py-1.5 pl-4 text-sm text-erp-text">
                            <span>{{ $l['amount'] >= 0 ? 'From' : 'For' }} {{ $l['account']->name }}</span><span>{{ $m($l['amount']) }}</span>
                        </div>
                    @empty
                        <div class="py-1.5 pl-4 text-sm text-erp-mute">—</div>
                    @endforelse
                    <div class="flex justify-between py-1.5 text-sm font-bold text-erp-ink"><span>Net cash from {{ strtolower($s['title']) }}</span><span>{{ $m($s['total']) }}</span></div>
                @endforeach
                <div class="mt-3 flex justify-between border-t border-erp-line py-2 text-sm font-bold text-erp-ink"><span>Net change in cash</span><span>{{ $m($data['net']) }}</span></div>
                <div class="flex justify-between border-y-2 border-erp-ink py-2 font-black text-erp-ink"><span>Closing Cash &amp; Bank</span><span>{{ $m($data['closing']) }}</span></div>
                <p class="mt-3 text-xs text-erp-mute">Cash accounts: {{ $data['cash_accounts']->implode(', ') }}.
                    @if (abs($data['difference']) >= 0.01) <span class="font-bold text-rose-700">Reconciliation difference {{ number_format($data['difference'], 2) }}.</span> @endif
                </p>
            </div>
        @endif
    </div>
</div>
