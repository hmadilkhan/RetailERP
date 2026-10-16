<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $input = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $btn = 'h-9 rounded-lg border border-erp-line bg-white px-3 text-sm font-bold text-erp-text transition hover:bg-slate-50';
        $btnPrimary = 'h-9 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:opacity-90';
    @endphp

    @if (session()->has('qbo_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition.opacity>{{ session('qbo_message') }}</div>
    @endif
    @if (session()->has('qbo_error'))
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ session('qbo_error') }}</div>
    @endif
    @error('qbo')
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ $message }}</div>
    @enderror

    @if (!$ready)
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">QuickBooks sync is not set up on this server yet (database update pending).</div>
    @else
        {{-- connection --}}
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3 px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">QuickBooks Online</h2>
                    <p class="mt-0.5 text-sm text-erp-mute">Posted journal entries from this ledger are sent to QuickBooks as Journal Entries (same number, date and amounts). Each of your accounts must be linked to a QuickBooks account first.</p>
                    @if ($setting)
                        <p class="mt-2 text-sm"><span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700">Connected</span>
                            <span class="ml-2 text-erp-mute">Company ID {{ $setting->realm_id ?? 'from server settings' }}{{ $setting->last_journal_sync_at ? ' · last sync ' . \Illuminate\Support\Carbon::parse($setting->last_journal_sync_at)->diffForHumans() : '' }}</span></p>
                    @else
                        <p class="mt-2 text-sm"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-600">Not connected</span></p>
                    @endif
                </div>
                @if ($admin)
                    <a href="{{ url('quickbooks/connect') }}" class="{{ $setting ? $btn : $btnPrimary }} inline-flex items-center">{{ $setting ? 'Reconnect' : 'Connect to QuickBooks' }}</a>
                @endif
            </div>

            @if ($setting)
                <div class="grid gap-3 border-t border-erp-line px-5 py-4 md:grid-cols-4">
                    <label class="flex items-center gap-2 text-sm md:col-span-2"><input type="checkbox" wire:model="enabled" class="rounded border-erp-line text-erp" @disabled(!$admin)> Send journal entries to QuickBooks automatically</label>
                    <label class="block"><span class="{{ $labelClass }}">Send entries dated from</span><input type="date" wire:model.live="fromDate" class="mt-1 {{ $input }}" @disabled(!$admin)></label>
                    @if ($admin)<div class="flex items-end justify-end"><button type="button" wire:click="saveSettings" class="{{ $btnPrimary }}">Save</button></div>@endif
                </div>
                @if ($counts)
                    <div class="grid gap-3 border-t border-erp-line px-5 py-4 sm:grid-cols-3">
                        <div class="rounded-lg border border-erp-line p-3"><div class="{{ $labelClass }}">Sent</div><div class="mt-1 text-xl font-bold text-erp-ink">{{ number_format($counts['synced']) }}</div></div>
                        <div class="rounded-lg border border-erp-line p-3"><div class="{{ $labelClass }}">Waiting</div><div class="mt-1 text-xl font-bold text-erp-ink">{{ number_format($counts['pending']) }}</div></div>
                        <div class="rounded-lg border border-erp-line p-3"><div class="{{ $labelClass }}">Failed</div><div class="mt-1 text-xl font-bold {{ $counts['failed'] ? 'text-rose-700' : 'text-erp-ink' }}">{{ number_format($counts['failed']) }}</div></div>
                    </div>
                @endif
                @if ($admin && $setting->sync_journals)
                    <div class="flex justify-end gap-2 border-t border-erp-line px-5 py-3">
                        @if ($counts && $counts['failed'])<button type="button" wire:click="retryFailed" class="{{ $btn }}">Retry failed</button>@endif
                        <button type="button" wire:click="syncNow" wire:loading.attr="disabled" class="{{ $btnPrimary }}"><span wire:loading.remove wire:target="syncNow">Sync now</span><span wire:loading wire:target="syncNow">Sending…</span></button>
                    </div>
                @endif
            @endif
        </div>

        @if ($setting)
            {{-- mapping --}}
            <div class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                    <div>
                        <h3 class="text-sm font-bold text-erp-ink">Account mapping</h3>
                        <p class="text-xs text-erp-mute">
                            @if ($unmappedUsed) <span class="font-bold text-amber-700">{{ $unmappedUsed }} account(s) used since {{ $fromDate }} are not linked</span> — their entries cannot be sent.
                            @else All accounts used since {{ $fromDate }} are linked. @endif
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <label class="flex items-center gap-1 text-xs"><input type="checkbox" wire:model.live="showAll" class="rounded border-erp-line text-erp"> Show all accounts</label>
                        <button type="button" wire:click="loadAccounts" class="{{ $btn }}">Load QuickBooks accounts</button>
                        @if ($admin)
                            <button type="button" wire:click="autoMap" class="{{ $btn }}">Auto-match</button>
                            <button type="button" wire:click="saveMapping" class="{{ $btnPrimary }}">Save mapping</button>
                        @endif
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}"><th class="px-5 py-2">Account</th><th class="px-3 py-2">Type</th><th class="px-5 py-2">QuickBooks account</th></tr></thead>
                        @forelse ($accounts as $a)
                            <tr class="border-b border-erp-line" wire:key="m-{{ $a['id'] }}">
                                <td class="px-5 py-2"><span class="font-mono text-xs text-erp-mute">{{ $a['code'] }}</span> {{ $a['name'] }}
                                    @if ($a['used'] && ($map[$a['id']] ?? '') === '')<span class="ml-1 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-bold text-amber-700">needs link</span>@endif</td>
                                <td class="px-3 py-2 text-erp-mute">{{ $a['type'] }}</td>
                                <td class="px-5 py-1">
                                    <select wire:model="map.{{ $a['id'] }}" class="{{ $input }} max-w-md" @disabled(!$admin)>
                                        <option value="">— not linked —</option>
                                        @foreach ($qboAccounts as $q)<option value="{{ $q['id'] }}">{{ $q['name'] }}{{ $q['number'] ? ' (' . $q['number'] . ')' : '' }}{{ $q['type'] ? ' · ' . $q['type'] : '' }}</option>@endforeach
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-8 text-center text-erp-mute">No posted entries since this date.</td></tr>
                        @endforelse
                    </table>
                </div>
            </div>

            @if ($failures->count())
                <div class="rounded-lg border border-erp-line bg-white shadow-sm">
                    <div class="border-b border-erp-line px-5 py-4"><h3 class="text-sm font-bold text-erp-ink">Not sent</h3></div>
                    <table class="min-w-full text-sm">
                        @foreach ($failures as $f)
                            <tr class="border-b border-erp-line">
                                <td class="whitespace-nowrap px-5 py-2"><a href="{{ url('journal-entries') }}?entry={{ $f->journal_entry_id }}" class="font-mono text-xs font-bold text-erp hover:underline">{{ $f->entry_no }}</a></td>
                                <td class="whitespace-nowrap px-3 py-2">{{ date('d M Y', strtotime($f->entry_date)) }}</td>
                                <td class="px-3 py-2 text-rose-700">{{ $f->error }}</td>
                                <td class="whitespace-nowrap px-5 py-2 text-xs text-erp-mute">{{ $f->attempts }} {{ $f->attempts >= \App\Services\QuickBooks\QboJournalSyncService::MAX_ATTEMPTS ? 'tries — stopped' : 'tries' }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endif
        @endif
    @endif
</div>
