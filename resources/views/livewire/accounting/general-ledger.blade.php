<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        // Dr/Cr ke saath balance: positive = debit balance
        $bal = fn ($v) => number_format(abs((float) $v), 2) . ' ' . ((float) $v >= 0 ? 'Dr' : 'Cr');
        $money = fn ($v) => (float) $v ? number_format((float) $v, 2) : '';
    @endphp

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="grid gap-4 p-5 md:grid-cols-4">
            <label class="block md:col-span-2">
                <span class="{{ $labelClass }}">Account</span>
                <select wire:model.live="accountId" class="{{ $inputClass }}">
                    <option value="">Select account</option>
                    @foreach ($accounts as $a)
                        <option value="{{ $a->id }}">{{ str_repeat('— ', $depths[$a->id] ?? 0) }}{{ $a->code }} · {{ $a->name }}{{ $a->is_group ? ' (group)' : '' }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block"><span class="{{ $labelClass }}">From</span><input type="date" wire:model.live="from" class="{{ $inputClass }}"></label>
            <label class="block"><span class="{{ $labelClass }}">To</span><input type="date" wire:model.live="to" class="{{ $inputClass }}"></label>
            <label class="block">
                <span class="{{ $labelClass }}">Branch</span>
                <select wire:model.live="branchId" class="{{ $inputClass }}">
                    <option value="">All branches</option>
                    @foreach ($branches as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </label>
        </div>
    </div>

    @if (!$account)
        <div class="rounded-lg border border-erp-line bg-white px-6 py-16 text-center text-sm font-medium text-erp-mute shadow-sm">
            Select an account to see its ledger. Choosing a group includes every account under it.
        </div>
    @else
        <div class="grid gap-3 sm:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white px-4 py-3 shadow-sm"><div class="{{ $labelClass }}">Opening</div><div class="mt-1 text-lg font-black text-erp-ink">{{ $bal($opening) }}</div></div>
            <div class="rounded-lg border border-erp-line bg-white px-4 py-3 shadow-sm"><div class="{{ $labelClass }}">Debits</div><div class="mt-1 text-lg font-black text-erp-ink">{{ number_format($debit, 2) }}</div></div>
            <div class="rounded-lg border border-erp-line bg-white px-4 py-3 shadow-sm"><div class="{{ $labelClass }}">Credits</div><div class="mt-1 text-lg font-black text-erp-ink">{{ number_format($credit, 2) }}</div></div>
            <div class="rounded-lg border border-erp bg-white px-4 py-3 shadow-sm ring-1 ring-erp"><div class="{{ $labelClass }}">Closing</div><div class="mt-1 text-lg font-black text-erp-dark">{{ $bal($closing) }}</div></div>
        </div>

        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">{{ $account->code }} · {{ $account->name }}</h2>
                <p class="mt-0.5 text-sm text-erp-mute">{{ $account->type }}{{ $account->is_group ? ' · group (all accounts under it)' : '' }} · posted entries only</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left">
                        <tr>
                            <th class="px-5 py-3 {{ $labelClass }}">Date</th>
                            <th class="px-5 py-3 {{ $labelClass }}">Entry</th>
                            <th class="px-5 py-3 {{ $labelClass }}">Narration</th>
                            <th class="px-5 py-3 text-right {{ $labelClass }}">Debit</th>
                            <th class="px-5 py-3 text-right {{ $labelClass }}">Credit</th>
                            <th class="px-5 py-3 text-right {{ $labelClass }}">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line">
                        <tr class="bg-slate-50/60">
                            <td class="px-5 py-2.5 text-erp-mute">{{ $from ? \Carbon\Carbon::parse($from)->format('d M Y') : '' }}</td>
                            <td class="px-5 py-2.5 font-bold text-erp-text" colspan="4">Opening balance</td>
                            <td class="whitespace-nowrap px-5 py-2.5 text-right font-bold text-erp-ink">{{ $bal($opening) }}</td>
                        </tr>
                        @forelse ($rows as $row)
                            <tr wire:key="gl-{{ $row->id }}">
                                <td class="whitespace-nowrap px-5 py-2.5 text-erp-text">{{ \Carbon\Carbon::parse($row->entry_date)->format('d M Y') }}</td>
                                <td class="whitespace-nowrap px-5 py-2.5">
                                    <a href="{{ route('journal-entries', ['entry' => $row->journal_entry_id]) }}" class="font-mono text-xs font-bold text-sky-700 hover:underline">{{ $row->entry_no }}</a>
                                    @if ($row->source_type !== 'manual') <span class="ml-1 text-[10px] font-bold uppercase text-erp-mute">{{ $row->source_type }}</span> @endif
                                </td>
                                <td class="px-5 py-2.5 text-erp-text">
                                    {{ $row->narration ?: $row->entry_narration }}
                                    @if ($account->is_group) <span class="block text-xs text-erp-mute">{{ $row->account_label }}</span> @endif
                                </td>
                                <td class="whitespace-nowrap px-5 py-2.5 text-right">{{ $money($row->debit) }}</td>
                                <td class="whitespace-nowrap px-5 py-2.5 text-right">{{ $money($row->credit) }}</td>
                                <td class="whitespace-nowrap px-5 py-2.5 text-right font-semibold text-erp-ink">{{ $bal($row->running) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-8 text-center text-sm text-erp-mute">No posted entries in this range.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold">
                        <tr>
                            <td class="px-5 py-2.5" colspan="3">Closing balance</td>
                            <td class="px-5 py-2.5 text-right">{{ number_format($debit, 2) }}</td>
                            <td class="px-5 py-2.5 text-right">{{ number_format($credit, 2) }}</td>
                            <td class="whitespace-nowrap px-5 py-2.5 text-right text-erp-dark">{{ $bal($closing) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</div>
