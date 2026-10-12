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
            <p class="mt-1 text-xs text-amber-700">Customer and vendor balances should match the customer and vendor ledgers — the automatic backfill (coming next) can fill those for you.</p>
        </div>
        <div class="grid gap-4 p-5 md:grid-cols-4">
            <label class="block"><span class="{{ $labelClass }}">Opening Date</span><input type="date" wire:model="entryDate" class="{{ $inputClass }}"></label>
            <label class="block md:col-span-2"><span class="{{ $labelClass }}">Find Account</span><input type="search" wire:model.live.debounce.300ms="search" class="{{ $inputClass }}" placeholder="Code or name"></label>
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
                            <td class="px-5 py-1.5 text-erp-text"><span class="font-mono text-xs text-erp-mute">{{ $a->code }}</span> {{ $a->name }}</td>
                            <td class="px-5 py-1.5"><input type="number" step="0.01" min="0" wire:model.live.debounce.500ms="amounts.{{ $a->id }}.debit" class="{{ $cellInput }}"></td>
                            <td class="px-5 py-1.5"><input type="number" step="0.01" min="0" wire:model.live.debounce.500ms="amounts.{{ $a->id }}.credit" class="{{ $cellInput }}"></td>
                        </tr>
                    @endforeach
                </tbody>
            @endforeach
        </table>
    </div>
</div>
