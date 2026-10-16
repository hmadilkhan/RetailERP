<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $input = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $btnPrimary = 'h-9 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:opacity-90';
    @endphp

    @if (session()->has('fx_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.opacity>{{ session('fx_message') }}</div>
    @endif
    @error('fx')
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ $message }}</div>
    @enderror

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Exchange Rates</h2>
            <p class="mt-0.5 text-sm text-erp-mute">Base currency: <b>{{ $base }}</b>. Rates are used for vendor bills in a foreign currency — the bill uses the rate on its date, a payment the rate on the day it is paid, and the difference goes to Exchange Gain / Loss.</p>
        </div>
        @if (!$ready)
            <p class="px-5 py-6 text-sm text-amber-700">Foreign currency is not set up on this server yet (database update pending).</p>
        @else
            @if ($canEdit)
                <div class="grid gap-3 border-b border-erp-line p-5 md:grid-cols-4">
                    <label class="block"><span class="{{ $labelClass }}">Currency</span>
                        <input type="text" list="fx-codes" maxlength="3" wire:model="currency" class="mt-1 {{ $input }} uppercase">
                        <datalist id="fx-codes">@foreach ($common as $c)<option value="{{ $c }}">@endforeach</datalist></label>
                    <label class="block"><span class="{{ $labelClass }}">Date</span><input type="date" wire:model="rateDate" class="mt-1 {{ $input }}"></label>
                    <label class="block"><span class="{{ $labelClass }}">1 unit = ? {{ $base }}</span><input type="number" step="any" min="0" wire:model="rate" class="mt-1 {{ $input }}"></label>
                    <div class="flex items-end justify-end"><button type="button" wire:click="save" class="{{ $btnPrimary }}">Save rate</button></div>
                </div>
            @endif
            <table class="min-w-full text-sm">
                <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}"><th class="px-5 py-3">Currency</th><th class="px-3 py-3">Latest date</th><th class="px-5 py-3 text-right">Rate ({{ $base }})</th></tr></thead>
                @forelse ($latest as $r)
                    <tr class="cursor-pointer border-b border-erp-line hover:bg-slate-50" wire:click="$set('history', '{{ $r->currency }}')" wire:key="fx-{{ $r->currency }}">
                        <td class="px-5 py-2 font-semibold">{{ $r->currency }}</td>
                        <td class="px-3 py-2">{{ date('d M Y', strtotime($r->rate_date)) }}</td>
                        <td class="px-5 py-2 text-right tabular-nums">{{ rtrim(rtrim(number_format($r->rate, 6), '0'), '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-erp-mute">No rates yet. Add one above to use that currency on vendor bills.</td></tr>
                @endforelse
            </table>
        @endif
    </div>

    @if ($rows->count())
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-3"><h3 class="text-sm font-bold text-erp-ink">{{ $history }} history</h3></div>
            <table class="min-w-full text-sm">
                @foreach ($rows as $r)
                    <tr class="border-b border-erp-line">
                        <td class="px-5 py-2">{{ date('d M Y', strtotime($r->rate_date)) }}</td>
                        <td class="px-3 py-2 text-right tabular-nums">{{ rtrim(rtrim(number_format($r->rate, 6), '0'), '.') }}</td>
                        <td class="px-5 py-2 text-right">@if ($canEdit)<button type="button" wire:click="delete({{ $r->id }})" wire:confirm="Delete this rate?" class="text-xs font-bold text-rose-700 hover:underline">Delete</button>@endif</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif
</div>
