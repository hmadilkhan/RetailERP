<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $cellInput = 'h-9 w-full rounded-lg border-erp-line text-right text-sm shadow-sm focus:border-erp focus:ring-erp';
        $diff = round($totalDebit - $totalCredit, 2);
    @endphp

    @if (session()->has('ob_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.opacity>{{ session('ob_message') }}</div>
    @endif
    @if ($postingStart && $entryDate && $entryDate >= \Carbon\Carbon::parse($postingStart)->toDateString())
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-5 py-3 text-sm font-semibold text-amber-800">
            The opening date is on or after the day automatic posting started ({{ \Carbon\Carbon::parse($postingStart)->format('d M Y') }}). {{ $laterEntries }} posted entries fall on or before it — Fill from system subtracts them, but the day before posting starts is cleaner.
        </div>
    @endif
    @foreach ($warnings as $warning)
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-5 py-3 text-sm font-semibold text-amber-800">{{ $warning }}</div>
    @endforeach

    @error('opening')
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ $message }}</div>
    @enderror

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Opening Balances</h2>
            <p class="mt-0.5 text-sm text-erp-mute">
                Enter each account's balance on the opening date. Any difference goes to Opening Balance Equity automatically.
                Saving again replaces the previous opening entry.
                @if ($current) Current: <a href="{{ route('journal-entries', ['entry' => $current->id]) }}" class="font-bold text-sky-700 hover:underline">{{ $current->entry_no }}</a>. @endif
            </p>
        </div>

        {{-- kab / kya / pehle se entry ho to --}}
        <div class="grid gap-4 border-b border-erp-line bg-slate-50/60 px-5 py-4 text-sm text-erp-text md:grid-cols-3">
            <div>
                <div class="{{ $labelClass }}">When to use</div>
                <p class="mt-1">Once, when you start accounting. Use the day <span class="font-semibold">before</span> automatic posting starts as the opening date{{ $postingStart ? ' (' . \Carbon\Carbon::parse($postingStart)->subDay()->format('d M Y') . ')' : '' }}.</p>
            </div>
            <div>
                <div class="{{ $labelClass }}">What Fill from system does</div>
                <p class="mt-1">Reads your stock, cash ledger, bank, customer and vendor ledgers and fills the form with their balances on the opening date. <span class="font-semibold">Nothing is saved</span> until you press Save &amp; Post — check the numbers first.</p>
            </div>
            <div>
                <div class="{{ $labelClass }}">If entries already exist</div>
                <p class="mt-1">Saving again replaces the previous opening entry. Automatic entries after the opening date are already taken into account, and anything already in the ledger on or before that date is subtracted, so nothing is counted twice.</p>
            </div>
        </div>
        <div class="grid gap-4 p-5 md:grid-cols-4">
            <label class="block"><span class="{{ $labelClass }}">Opening Date</span><input type="date" wire:model="entryDate" class="{{ $inputClass }}"></label>
            <label class="block"><span class="{{ $labelClass }}">Find Account</span><input type="search" wire:model.live.debounce.300ms="search" class="{{ $inputClass }}" placeholder="Code or name"></label>
            <div class="flex items-end">
                <button type="button" wire:click="fillFromSystem" wire:loading.attr="disabled" wire:target="fillFromSystem"
                        @if ($totalDebit + $totalCredit > 0) wire:confirm="Replace the numbers in the form with the system's balances?" @endif
                        class="h-10 w-full rounded-lg border border-erp bg-white px-5 text-sm font-bold text-erp-dark transition hover:bg-emerald-50 disabled:opacity-60">
                    <span wire:loading.remove wire:target="fillFromSystem">Fill from system</span><span wire:loading wire:target="fillFromSystem">Reading ledgers…</span>
                </button>
            </div>
            <div class="flex items-end">
                <button type="button" wire:click="save" wire:confirm="Post these opening balances?" class="h-10 w-full rounded-lg border border-erp bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">Save &amp; Post</button>
            </div>
        </div>
        <div class="grid gap-3 border-t border-erp-line px-5 py-3 text-sm sm:grid-cols-3">
            <div><span class="{{ $labelClass }}">Total Debit</span> <span class="ml-1 font-black text-erp-ink">{{ number_format($totalDebit, 2) }}</span></div>
            <div><span class="{{ $labelClass }}">Total Credit</span> <span class="ml-1 font-black text-erp-ink">{{ number_format($totalCredit, 2) }}</span></div>
            <div><span class="{{ $labelClass }}">To Opening Balance Equity</span>
                <span class="ml-1 font-black {{ $diff == 0 ? 'text-emerald-700' : 'text-amber-700' }}">{{ $diff == 0 ? 'Balanced' : number_format(abs($diff), 2) . ($diff > 0 ? ' Cr' : ' Dr') }}</span></div>
        </div>
    </div>

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="px-5 py-3 {{ $labelClass }}">Account</th>
                    <th class="w-48 px-5 py-3 text-right {{ $labelClass }}">Debit</th>
                    <th class="w-48 px-5 py-3 text-right {{ $labelClass }}">Credit</th>
                </tr>
            </thead>
            @foreach ($accountsByType as $type => $accounts)
                <tbody class="divide-y divide-erp-line">
                    <tr class="bg-slate-50/70"><td colspan="3" class="px-5 py-2 text-xs font-black uppercase tracking-wide text-erp-ink">{{ $type }}</td></tr>
                    @foreach ($accounts as $a)
                        <tr wire:key="ob-{{ $a->id }}">
                            <td class="px-5 py-1.5 text-erp-text">
                                <span class="font-mono text-xs text-erp-mute">{{ $a->code }}</span> {{ $a->name }}
                                @if (isset($notes[$a->id])) <span class="mt-0.5 block text-xs text-sky-700">{{ $notes[$a->id] }}</span> @endif
                            </td>
                            <td class="px-5 py-1.5"><input type="number" step="0.01" min="0" wire:model.live.debounce.500ms="amounts.{{ $a->id }}.debit" class="{{ $cellInput }}"></td>
                            <td class="px-5 py-1.5"><input type="number" step="0.01" min="0" wire:model.live.debounce.500ms="amounts.{{ $a->id }}.credit" class="{{ $cellInput }}"></td>
                        </tr>
                    @endforeach
                </tbody>
            @endforeach
        </table>
    </div>
</div>
