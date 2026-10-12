<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $setting = $selected['setting'] ?? null;
        $isOn = $setting && $setting->enabled;
        $locked = $selected && $selected['years']->isNotEmpty();
    @endphp

    @if (session()->has('accounting_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.opacity>
            {{ session('accounting_message') }}
        </div>
    @endif

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Accounting Module</h2>
            <p class="mt-0.5 text-sm text-erp-mute">Turn on accounting for a company. Enabling creates the standard retail Chart of Accounts and the current fiscal year with 12 periods.</p>
        </div>

        <div class="grid gap-4 p-5 md:grid-cols-3">
            <label class="block">
                <span class="{{ $labelClass }}">Company</span>
                <select wire:model.live="companyId" class="{{ $inputClass }}">
                    <option value="">Select company</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->company_id }}">{{ $company->name }} (#{{ $company->company_id }})</option>
                    @endforeach
                </select>
                @error('companyId') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="{{ $labelClass }}">Fiscal Year</span>
                <select wire:model="startMonth" class="{{ $inputClass }} disabled:bg-slate-50 disabled:text-slate-400" @disabled($locked)>
                    @foreach ($months as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @if ($locked)
                    <span class="mt-1 block text-xs text-erp-mute">Locked — fiscal years already exist.</span>
                @endif
            </label>

            <div class="flex items-end gap-2">
                @if ($companyId !== '')
                    <button type="button" wire:click="enable" wire:loading.attr="disabled"
                            class="h-10 rounded-lg border border-erp bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark disabled:opacity-60">
                        {{ $isOn ? 'Re-check setup' : 'Enable Accounting' }}
                    </button>
                    @if ($isOn)
                        <button type="button" wire:click="disable" wire:confirm="Hide the accounting menu for this company? Accounts and fiscal years are kept."
                                class="h-10 rounded-lg border border-rose-200 bg-rose-50 px-5 text-sm font-bold text-rose-700 transition hover:bg-rose-100">
                            Turn Off
                        </button>
                    @endif
                @endif
            </div>
        </div>

        @if ($selected && $isOn)
            <div class="grid gap-4 border-t border-erp-line p-5 md:grid-cols-3">
                <label class="block">
                    <span class="{{ $labelClass }}">Automatic Posting From</span>
                    <input type="date" wire:model="postingStartDate" class="{{ $inputClass }}">
                    @error('postingStartDate') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>
                <div class="flex items-end">
                    <button type="button" wire:click="savePostingStart" class="h-10 rounded-lg border border-erp-line bg-white px-5 text-sm font-bold text-erp-text transition hover:bg-slate-50">Save</button>
                </div>
                <p class="self-end text-xs text-erp-mute">Sales, expenses, purchases and payments from this date are posted to the General Ledger every 15 minutes. Leave empty to turn off.</p>
            </div>
        @endif

        @if ($selected && $isOn)
            <div class="border-t border-erp-line p-5">
                <div class="flex flex-col gap-1">
                    <div class="text-sm font-bold text-erp-ink">Post Older Data (Backfill)</div>
                    <p class="text-sm text-erp-mute">Moves the posting start back to a cut-off date and posts every sale, expense, purchase and payment since then. Fiscal years are created as needed. An opening balance entry dated on or after the cut-off is reversed (otherwise it would count twice) &mdash; enter opening balances for the day before the cut-off instead. Safe to run again: only differences are posted.</p>
                </div>
                <div class="mt-3 flex flex-wrap items-end gap-3">
                    <label class="block">
                        <span class="{{ $labelClass }}">Cut-off Date</span>
                        <input type="date" wire:model="backfillFrom" class="{{ $inputClass }}">
                    </label>
                    <button type="button" wire:click="previewBackfill" wire:loading.attr="disabled" class="h-10 rounded-lg border border-erp-line bg-white px-5 text-sm font-bold text-erp-text transition hover:bg-slate-50 disabled:opacity-60">
                        <span wire:loading.remove wire:target="previewBackfill">Preview</span><span wire:loading wire:target="previewBackfill">Reading history&hellip;</span>
                    </button>
                    @if ($backfillPlan)
                        <button type="button" wire:click="runBackfill" wire:loading.attr="disabled" wire:confirm="Post all history from {{ $backfillFrom }}? This can take a few minutes."
                                class="h-10 rounded-lg border border-erp bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark disabled:opacity-60">
                            <span wire:loading.remove wire:target="runBackfill">Run Backfill</span><span wire:loading wire:target="runBackfill">Posting&hellip;</span>
                        </button>
                    @endif
                </div>
                @error('backfill') <div class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</div> @enderror
                @error('backfillFrom') <div class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</div> @enderror

                @if ($backfillPlan)
                    <div class="mt-4 grid gap-3 rounded-lg border border-erp-line bg-slate-50/60 p-4 text-sm text-erp-text md:grid-cols-2">
                        <div><span class="{{ $labelClass }}">Earliest data</span> <span class="ml-1 font-bold">{{ $backfillPlan['earliest'] ?? '-' }}</span></div>
                        <div><span class="{{ $labelClass }}">Posting start</span> <span class="ml-1 font-bold">{{ $backfillPlan['current_start'] ?? 'off' }}</span> &rarr; <span class="font-bold">{{ $backfillPlan['cutoff'] }}</span></div>
                        <div><span class="{{ $labelClass }}">Fiscal years to create</span> <span class="ml-1 font-bold">{{ implode(', ', $backfillPlan['years_to_create']) ?: 'none' }}</span></div>
                        <div><span class="{{ $labelClass }}">Opening entries to reverse</span> <span class="ml-1 font-bold {{ $backfillPlan['conflicting_openings'] ? 'text-amber-700' : '' }}">{{ implode(', ', $backfillPlan['conflicting_openings']) ?: 'none' }}</span></div>
                    </div>
                    <table class="mt-3 min-w-full text-sm">
                        <thead class="text-left"><tr>
                            <th class="py-2 {{ $labelClass }}">Source</th><th class="py-2 text-right {{ $labelClass }}">Entries</th>
                            <th class="py-2 text-right {{ $labelClass }}">Total Debit</th><th class="py-2 text-right {{ $labelClass }}">With difference</th>
                        </tr></thead>
                        <tbody class="divide-y divide-erp-line">
                            @forelse ($backfillPlan['sources'] as $type => $s)
                                <tr><td class="py-1.5 text-erp-text">{{ str_replace('_', ' ', $type) }}</td><td class="py-1.5 text-right">{{ number_format($s['documents']) }}</td>
                                    <td class="py-1.5 text-right">{{ number_format($s['debits'], 2) }}</td><td class="py-1.5 text-right {{ $s['unbalanced'] ? 'text-amber-700 font-bold' : 'text-erp-mute' }}">{{ $s['unbalanced'] ?: '' }}</td></tr>
                            @empty
                                <tr><td colspan="4" class="py-3 text-erp-mute">Nothing to post in this range.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif

                @if ($backfillResult)
                    <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                        <div class="font-bold">Backfill finished.</div>
                        <div>Fiscal years created: {{ implode(', ', $backfillResult['years_created']) ?: 'none' }}</div>
                        <div>{{ collect($backfillResult['stats'])->map(fn ($n, $k) => $k . ' ' . $n)->implode(' / ') }}</div>
                        <div class="mt-1 text-xs">Errors and warnings are listed on the company Posting Rules page.</div>
                    </div>
                @endif
            </div>
        @endif

        @if ($selected)
            <div class="grid gap-4 border-t border-erp-line p-5 sm:grid-cols-3">
                <div>
                    <div class="{{ $labelClass }}">Status</div>
                    <div class="mt-1 text-sm font-bold {{ $isOn ? 'text-emerald-700' : 'text-slate-500' }}">
                        {{ $isOn ? 'Enabled' : ($setting ? 'Turned off' : 'Not set up') }}
                        @if ($setting && $setting->enabled_at)
                            <span class="font-medium text-erp-mute">· since {{ $setting->enabled_at->format('d M Y') }}</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="{{ $labelClass }}">Accounts</div>
                    <div class="mt-1 text-sm font-bold text-erp-ink">{{ $selected['accounts'] }}</div>
                </div>
                <div>
                    <div class="{{ $labelClass }}">Fiscal Years</div>
                    <div class="mt-1 text-sm font-bold text-erp-ink">{{ $selected['years']->pluck('name')->implode(', ') ?: '—' }}</div>
                </div>
            </div>
        @endif
    </div>

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Companies with Accounting</h2>
            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-erp-text">{{ $enabledCompanies->count() }}</span>
        </div>
        @if ($enabledCompanies->isEmpty())
            <div class="px-6 py-10 text-center text-sm font-medium text-erp-mute">No company has accounting turned on yet.</div>
        @else
            <ul class="divide-y divide-erp-line">
                @foreach ($enabledCompanies as $row)
                    <li class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="font-bold text-erp-ink">{{ $row['name'] }} <span class="font-medium text-erp-mute">#{{ $row['setting']->company_id }}</span></span>
                        <span class="text-erp-mute">{{ $months[$row['setting']->fiscal_year_start_month] ?? '' }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
