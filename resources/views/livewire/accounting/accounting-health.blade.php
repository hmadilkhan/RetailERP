<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $btn = 'h-9 rounded-lg border border-erp-line bg-white px-3 text-sm font-bold text-erp-text transition hover:bg-slate-50';
        $style = [
            'fail' => ['bg-rose-50 text-rose-700', 'Fix first'],
            'warn' => ['bg-amber-50 text-amber-700', 'Check'],
            'info' => ['bg-sky-50 text-sky-700', 'Next step'],
            'ok' => ['bg-emerald-50 text-emerald-700', 'OK'],
        ];
        $groups = ['Posting', 'Balances', 'Inventory', 'Setup', 'Period close', 'Banks & assets', 'Integrations'];
    @endphp

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-3 px-5 py-4">
            <div>
                <h2 class="text-base font-bold text-erp-ink">Accounting Health</h2>
                <p class="mt-0.5 text-sm text-erp-mute">Checks that the ledger is complete and the reports can be trusted. Fix the red items first — they make the financial statements wrong.</p>
                <p class="mt-1 text-xs text-erp-mute">Checked {{ \Illuminate\Support\Carbon::parse($checkedAt)->diffForHumans() }}</p>
            </div>
            <button type="button" wire:click="refresh" wire:loading.attr="disabled" class="{{ $btn }}"><span wire:loading.remove wire:target="refresh">Run checks again</span><span wire:loading wire:target="refresh">Checking…</span></button>
        </div>
        <div class="grid gap-3 border-t border-erp-line px-5 py-4 sm:grid-cols-4">
            @foreach (['fail' => 'Fix first', 'warn' => 'Check', 'info' => 'Next steps', 'ok' => 'OK'] as $k => $label)
                <div class="rounded-lg border border-erp-line p-3">
                    <div class="{{ $labelClass }}">{{ $label }}</div>
                    <div class="mt-1 text-2xl font-bold {{ $summary[$k] && $k !== 'ok' ? explode(' ', $style[$k][0])[1] : 'text-erp-ink' }}">{{ $summary[$k] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    @foreach ($groups as $group)
        @continue(!isset($checks[$group]))
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-3"><h3 class="text-sm font-bold text-erp-ink">{{ $group }}</h3></div>
            @foreach ($checks[$group] as $c)
                <div class="flex flex-wrap items-start gap-3 border-b border-erp-line px-5 py-3 last:border-b-0" wire:key="h-{{ $c['key'] }}">
                    <span class="mt-0.5 w-20 shrink-0 rounded-full px-2 py-0.5 text-center text-xs font-bold {{ $style[$c['status']][0] }}">{{ $style[$c['status']][1] }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-baseline justify-between gap-2">
                            <span class="font-semibold text-erp-ink">{{ $c['title'] }}</span>
                            <span class="text-sm font-bold tabular-nums">{{ $c['value'] }}</span>
                        </div>
                        <p class="mt-0.5 text-sm text-erp-mute">{{ $c['detail'] }}</p>
                        @if ($c['hint'] || $c['link'])
                            <p class="mt-1 text-sm">
                                @if ($c['hint'])<span class="text-erp-text">{{ $c['hint'] }}</span>@endif
                                @if ($c['link'])<a href="{{ url($c['link']) }}" class="ml-1 font-bold text-erp hover:underline">Open &rarr;</a>@endif
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="border-b border-erp-line px-5 py-3"><h3 class="text-sm font-bold text-erp-ink">Going live — checklist</h3></div>
        <ol class="list-decimal space-y-2 py-4 pl-10 pr-5 text-sm text-erp-text">
            <li>Review the <a href="{{ url('chart-of-accounts') }}" class="font-bold text-erp hover:underline">Chart of Accounts</a> — rename or add accounts the business needs (system accounts can be renamed, not deleted).</li>
            <li>Pick the start date (usually the first day of a month or fiscal year). In Accounting Setup set <em>Automatic Posting From</em> to it{{ $setting && $setting->posting_start_date ? ' (now ' . date('d M Y', strtotime($setting->posting_start_date)) . ')' : '' }}.</li>
            <li>Enter <a href="{{ url('opening-balances') }}" class="font-bold text-erp hover:underline">Opening Balances</a> on the day before — cash, bank, stock, customers, vendors. "Fill from system" gives a starting point; check it against the real counts.</li>
            <li>Link expense categories, bank accounts and payment modes on <a href="{{ url('posting-rules') }}" class="font-bold text-erp hover:underline">Posting Rules</a>.</li>
            <li>Give products a cost (purchases / opening stock) so cost of sales is right — see <a href="{{ url('inventory-valuation') }}" class="font-bold text-erp hover:underline">Inventory Valuation</a>.</li>
            <li>Every month: reconcile the bank, run depreciation, record the sales tax return, then close the month on <a href="{{ url('fiscal-years') }}" class="font-bold text-erp hover:underline">Fiscal Years</a>.</li>
        </ol>
        <p class="border-t border-erp-line px-5 py-3 text-xs text-erp-mute">If the ledger was filled from test or wrong data, do not delete entries by hand — ask your Sabify contact to restart the ledger from a clean start date.</p>
    </div>
</div>
