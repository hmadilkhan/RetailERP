<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $inputClass = 'mt-1 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $cellInput = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $btn = 'h-9 rounded-lg border border-erp-line bg-white px-3 text-sm font-bold text-erp-text transition hover:bg-slate-50';
        $btnPrimary = 'h-9 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:opacity-90 disabled:opacity-50';
        $money = fn ($v) => number_format((float) $v, 2);
        $fmtDate = fn ($d) => \Illuminate\Support\Carbon::parse($d)->format('d M Y');
    @endphp

    @if (session()->has('br_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition.opacity>{{ session('br_message') }}</div>
    @endif
    @error('br')
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ $message }}</div>
    @enderror

    @if (!$rec)
        {{-- ======== list + new ======== --}}
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Bank Reconciliation</h2>
                <p class="mt-0.5 text-sm text-erp-mute">
                    Match your bank statement with the bank account in the General Ledger. Card and online sales, vendor payments and journal entries all reach the bank account automatically;
                    the statement shows what actually arrived (often the next day, after card fees). Enter the statement's opening and closing balance, import its lines, then match.
                    The first statement of an account starts the reconciliation: ledger entries dated before it are treated as already cleared (they are inside its opening balance).
                </p>
            </div>
            <div class="grid gap-3 p-5 md:grid-cols-6">
                <label class="block md:col-span-2">
                    <span class="{{ $labelClass }}">Bank account</span>
                    <select wire:model="newAccount" class="{{ $inputClass }}">
                        <option value="">— Select —</option>
                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->code }} {{ $bank->name }}</option>
                        @endforeach
                    </select>
                    @error('newAccount') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>
                <label class="block"><span class="{{ $labelClass }}">Statement from</span><input type="date" wire:model="newFrom" class="{{ $inputClass }}"></label>
                <label class="block"><span class="{{ $labelClass }}">To</span><input type="date" wire:model="newTo" class="{{ $inputClass }}"></label>
                <label class="block">
                    <span class="{{ $labelClass }}">Opening balance</span>
                    <input type="number" step="any" wire:model="newOpening" class="{{ $inputClass }}">
                    @error('newOpening') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Closing balance</span>
                    <input type="number" step="any" wire:model="newClosing" class="{{ $inputClass }}">
                    @error('newClosing') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>
                <div class="md:col-span-6 flex justify-end"><button type="button" wire:click="create" class="{{ $btnPrimary }}">+ New Statement</button></div>
            </div>
            <div class="overflow-x-auto border-t border-erp-line">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                        <th class="px-5 py-3">Account</th><th class="px-3 py-3">Period</th><th class="px-3 py-3 text-right">Opening</th><th class="px-3 py-3 text-right">Closing</th><th class="px-5 py-3">Status</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($list as $r)
                            <tr class="cursor-pointer border-b border-erp-line hover:bg-slate-50" wire:click="show({{ $r->id }})" wire:key="br-{{ $r->id }}">
                                <td class="px-5 py-3 font-semibold text-erp-ink">{{ $r->code }} {{ $r->account_name }}</td>
                                <td class="px-3 py-3">{{ $fmtDate($r->statement_from) }} – {{ $fmtDate($r->statement_to) }}</td>
                                <td class="px-3 py-3 text-right tabular-nums">{{ $money($r->opening_balance) }}</td>
                                <td class="px-3 py-3 text-right tabular-nums">{{ $money($r->closing_balance) }}</td>
                                <td class="px-5 py-3"><span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $r->status === 'reconciled' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ ucfirst($r->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-erp-mute">No statements yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        {{-- ======== detail ======== --}}
        @php $isOpen = $rec->status === 'open'; @endphp
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <button type="button" wire:click="back" class="text-xs font-bold text-erp hover:underline">&larr; All statements</button>
                    <h2 class="mt-1 text-lg font-bold text-erp-ink">{{ $account->code }} {{ $account->name }}
                        <span class="ml-2 rounded-full px-2.5 py-0.5 text-xs font-bold {{ $isOpen ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">{{ ucfirst($rec->status) }}</span></h2>
                    <p class="text-sm text-erp-mute">{{ $fmtDate($rec->statement_from) }} – {{ $fmtDate($rec->statement_to) }} · statement opening {{ $money($rec->opening_balance) }} · closing {{ $money($rec->closing_balance) }}</p>
                </div>
                <div class="flex gap-2">
                    @if ($isOpen)
                        <button type="button" wire:click="deleteRec" wire:confirm="Delete this statement and its lines? Entries already posted stay in the ledger." class="{{ $btn }} text-rose-700">Delete</button>
                        <button type="button" wire:click="finish" class="{{ $btnPrimary }}" @disabled($summary['unmatched_lines'] || abs($summary['difference']) > 0.005)>Mark Reconciled</button>
                    @elseif (in_array((int) session('roleId'), [1, 2], true))
                        <button type="button" wire:click="reopen" class="{{ $btn }}">Reopen</button>
                    @endif
                </div>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-5">
                <div><div class="{{ $labelClass }}">Statement closing</div><div class="mt-1 text-lg font-bold tabular-nums">{{ $money($rec->closing_balance) }}</div></div>
                <div><div class="{{ $labelClass }}">+ Deposits not on statement</div><div class="mt-1 text-lg font-bold tabular-nums">{{ $money($summary['in_transit']) }}</div></div>
                <div><div class="{{ $labelClass }}">− Payments not on statement</div><div class="mt-1 text-lg font-bold tabular-nums">{{ $money($summary['outstanding']) }}</div></div>
                <div><div class="{{ $labelClass }}">Ledger balance on {{ $fmtDate($rec->statement_to) }}</div><div class="mt-1 text-lg font-bold tabular-nums">{{ $money($summary['gl_balance']) }}</div></div>
                <div><div class="{{ $labelClass }}">Difference</div><div class="mt-1 text-lg font-bold tabular-nums {{ abs($summary['difference']) > 0.005 ? 'text-rose-700' : 'text-emerald-700' }}">{{ $money($summary['difference']) }}</div></div>
                <p class="text-xs text-erp-mute md:col-span-5">
                    Statement closing + deposits not yet on the statement − payments not yet on the statement should equal the ledger balance.
                    {{ $summary['unmatched_lines'] }} of {{ $summary['statement_lines'] }} statement line(s) unmatched ({{ $money($summary['unmatched_amount']) }}).
                    @if (abs($summary['statement_check']) > 0.005)<span class="font-semibold text-amber-700">Statement lines don't add up to the closing balance yet (off by {{ $money($summary['statement_check']) }}) — some lines may be missing.</span>@endif
                </p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            {{-- statement --}}
            <div class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="border-b border-erp-line px-5 py-4">
                    <h3 class="text-sm font-bold text-erp-ink">Bank statement</h3>
                    <p class="text-xs text-erp-mute">Click a line, tick the matching ledger entries on the right, then Match. Lines that are only on the statement (bank charges, profit) → Create entry.</p>
                </div>
                @if ($isOpen)
                    <div class="grid gap-2 border-b border-erp-line p-5 md:grid-cols-6">
                        <input type="date" wire:model="lineDate" class="{{ $cellInput }}">
                        <input type="text" wire:model="lineDesc" placeholder="Description" class="{{ $cellInput }} md:col-span-2">
                        <input type="text" wire:model="lineRef" placeholder="Ref / cheque" class="{{ $cellInput }}">
                        <input type="number" step="any" wire:model="lineAmount" placeholder="+ in / − out" class="{{ $cellInput }}">
                        <button type="button" wire:click="addLine" class="{{ $btn }}">Add line</button>
                        <div class="flex flex-wrap items-center gap-2 md:col-span-6">
                            <input type="file" wire:model="file" accept=".csv,.xlsx,.xls" class="text-sm">
                            <button type="button" wire:click="upload" wire:loading.attr="disabled" class="{{ $btn }}" @disabled(!$file)>Import</button>
                            <span class="text-xs text-erp-mute">CSV / Excel with a Date column and Amount (or Debit / Credit) columns in the first row.</span>
                            @error('file') <span class="text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex items-center gap-2 md:col-span-6">
                            <span class="text-sm">Auto-match: same amount within</span>
                            <input type="number" min="0" max="15" wire:model="matchDays" class="{{ $cellInput }} w-20">
                            <span class="text-sm">days</span>
                            <button type="button" wire:click="autoMatch" class="{{ $btnPrimary }}">Auto-match</button>
                        </div>
                    </div>
                @endif
                <div class="max-h-[34rem] overflow-auto">
                    <table class="min-w-full text-sm">
                        <thead class="sticky top-0 bg-white"><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                            <th class="px-4 py-2">Date</th><th class="px-2 py-2">Description</th><th class="px-2 py-2 text-right">Amount</th><th class="px-4 py-2">Status</th>
                        </tr></thead>
                        <tbody>
                            @forelse ($lines as $line)
                                <tr class="border-b border-erp-line align-top {{ $selectedLine === $line->id ? 'bg-sky-50' : ($line->matches ? 'text-erp-mute' : '') }}" wire:key="sl-{{ $line->id }}">
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $fmtDate($line->txn_date) }}</td>
                                    <td class="px-2 py-2">{{ $line->description }} @if ($line->reference)<span class="block text-xs text-erp-mute">{{ $line->reference }}</span>@endif</td>
                                    <td class="px-2 py-2 text-right tabular-nums {{ $line->amount < 0 ? 'text-rose-700' : 'text-emerald-700' }}">{{ $money($line->amount) }}</td>
                                    <td class="px-4 py-2 text-xs">
                                        @if ($line->matches)
                                            <span class="font-bold text-emerald-700">Matched</span> {{ $line->entries }}
                                            @if ($isOpen)<button type="button" wire:click="unmatch({{ $line->id }})" class="ml-1 font-bold text-erp hover:underline">Unmatch</button>@endif
                                        @elseif ($isOpen)
                                            <button type="button" wire:click="selectLine({{ $line->id }})" class="font-bold text-erp hover:underline">{{ $selectedLine === $line->id ? 'Selected' : 'Select' }}</button>
                                            <button type="button" wire:click="startEntry({{ $line->id }})" class="ml-2 font-bold text-erp hover:underline">Create entry</button>
                                            <button type="button" wire:click="deleteLine({{ $line->id }})" class="ml-2 font-bold text-rose-700 hover:underline">×</button>
                                        @else
                                            <span class="text-rose-700">Unmatched</span>
                                        @endif
                                        @if ($entryLine === $line->id)
                                            <div class="mt-2 space-y-2 rounded-lg border border-erp-line bg-slate-50 p-3">
                                                <select wire:model="entryAccount" class="{{ $cellInput }}">
                                                    <option value="">— Other side account —</option>
                                                    @foreach ($contraAccounts as $a)
                                                        <option value="{{ $a->id }}">{{ $a->code }} {{ $a->name }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text" wire:model="entryNarration" placeholder="Narration (optional)" class="{{ $cellInput }}">
                                                <div class="flex gap-2">
                                                    <button type="button" wire:click="createEntry" class="{{ $btnPrimary }}">Post &amp; match</button>
                                                    <button type="button" wire:click="$set('entryLine', null)" class="{{ $btn }}">Cancel</button>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-10 text-center text-sm text-erp-mute">Import the statement or add its lines.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ledger --}}
            <div class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-erp-line px-5 py-4">
                    <div>
                        <h3 class="text-sm font-bold text-erp-ink">Ledger entries not yet on a statement</h3>
                        <p class="text-xs text-erp-mute">Up to {{ $fmtDate($rec->statement_to) }}, including older uncleared ones.</p>
                    </div>
                    @if ($isOpen && $selectedLine)
                        <div class="flex items-center gap-3 text-sm">
                            <span>Selected <b class="tabular-nums">{{ $money($selectedSum) }}</b> of <b class="tabular-nums">{{ $money($selectedAmount) }}</b></span>
                            <button type="button" wire:click="match" class="{{ $btnPrimary }}" @disabled(abs($selectedSum - (float) $selectedAmount) > 0.005)>Match</button>
                        </div>
                    @endif
                </div>
                <div class="max-h-[40rem] overflow-auto">
                    <table class="min-w-full text-sm">
                        <thead class="sticky top-0 bg-white"><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                            <th class="w-8 px-4 py-2"></th><th class="px-2 py-2">Date</th><th class="px-2 py-2">Entry</th><th class="px-4 py-2 text-right">Amount</th>
                        </tr></thead>
                        <tbody>
                            @forelse ($gl as $g)
                                <tr class="border-b border-erp-line align-top {{ !empty($selectedGl[$g->id]) ? 'bg-sky-50' : '' }}" wire:key="gl-{{ $g->id }}">
                                    <td class="px-4 py-2">@if ($isOpen && $selectedLine)<input type="checkbox" wire:model.live="selectedGl.{{ $g->id }}" class="h-4 w-4 rounded border-erp-line">@endif</td>
                                    <td class="px-2 py-2 whitespace-nowrap">{{ $fmtDate($g->entry_date) }}</td>
                                    <td class="px-2 py-2"><a href="{{ url('journal-entries') }}?entry={{ $g->entry_id }}" class="font-semibold text-erp hover:underline">{{ $g->entry_no }}</a> <span class="block text-xs text-erp-mute">{{ \Illuminate\Support\Str::limit($g->line_narration ?: $g->narration, 90) }}</span></td>
                                    <td class="px-4 py-2 text-right tabular-nums {{ $g->amount < 0 ? 'text-rose-700' : 'text-emerald-700' }}">{{ $money($g->amount) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-10 text-center text-sm text-erp-mute">Every ledger entry up to this date is matched.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
