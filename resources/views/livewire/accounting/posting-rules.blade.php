<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $selectClass = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $isOn = $setting && $setting->posting_start_date;
    @endphp

    @if (session()->has('posting_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity>{{ session('posting_message') }}</div>
    @endif
    @error('posting')
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ $message }}</div>
    @enderror

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-erp-ink">Automatic Posting</h2>
                <p class="mt-0.5 text-sm text-erp-mute">
                    @if ($isOn)
                        On from <span class="font-bold text-erp-ink">{{ $setting->posting_start_date->format('d M Y') }}</span> — {{ $sources->implode(', ') }} are posted to the General Ledger every 15 minutes.
                        Last run: {{ $setting->last_posted_at ? $setting->last_posted_at->diffForHumans() : 'never' }}.
                    @else
                        Off. Ask your administrator to set the posting start date in Accounting Setup.
                    @endif
                </p>
            </div>
            @if ($isOn)
                <button type="button" wire:click="postNow" wire:loading.attr="disabled" class="h-10 shrink-0 rounded-lg border border-erp bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark disabled:opacity-60">
                    <span wire:loading.remove wire:target="postNow">Post Now</span><span wire:loading wire:target="postNow">Posting…</span>
                </button>
            @endif
        </div>
        @if ($lastRun)
            <div class="grid gap-3 border-t border-erp-line px-5 py-3 text-sm sm:grid-cols-5">
                @foreach ($lastRun as $label => $count)
                    <div><span class="{{ $labelClass }}">{{ $label }}</span> <span class="ml-1 font-black text-erp-ink">{{ $count }}</span></div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($issues->isNotEmpty())
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Needs Attention</h2>
                <p class="mt-0.5 text-sm text-erp-mute">Errors were not posted. Warnings were posted, with the difference in Suspense / Rounding.</p>
            </div>
            <table class="min-w-full text-sm">
                <tbody class="divide-y divide-erp-line">
                    @foreach ($issues as $issue)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-2.5"><span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $issue->level === 'error' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700' }}">{{ ucfirst($issue->level) }}</span></td>
                            <td class="whitespace-nowrap px-5 py-2.5 text-xs font-semibold text-erp-text">{{ $sources[$issue->source_type] ?? $issue->source_type }} <span class="font-mono text-erp-mute">{{ $issue->source_ref }}</span></td>
                            <td class="px-5 py-2.5 text-erp-text">{{ $issue->message }}</td>
                            <td class="whitespace-nowrap px-5 py-2.5 text-xs text-erp-mute">{{ \Carbon\Carbon::parse($issue->updated_at)->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="border-b border-erp-line px-5 py-4">
                    <h2 class="text-base font-bold text-erp-ink">Expense Categories</h2>
                    <p class="mt-0.5 text-sm text-erp-mute">Not mapped → {{ $systemNames['general_expense'] ?? 'General Expenses' }}.</p>
                </div>
                <div class="max-h-[28rem] divide-y divide-erp-line overflow-y-auto">
                    @forelse ($categories as $cat)
                        <div class="grid grid-cols-2 items-center gap-3 px-5 py-2" wire:key="cat-{{ $cat->exp_cat_id }}">
                            <span class="truncate text-sm font-semibold text-erp-text">{{ $cat->expense_category }}</span>
                            <select wire:model="expenseMap.{{ $cat->exp_cat_id }}" class="{{ $selectClass }}">
                                <option value="">Default</option>
                                @foreach ($expenseAccounts as $a)
                                    <option value="{{ $a->id }}">{{ $a->code }} · {{ $a->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-sm text-erp-mute">No expense categories.</div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-lg border border-erp-line bg-white shadow-sm">
                    <div class="border-b border-erp-line px-5 py-4">
                        <h2 class="text-base font-bold text-erp-ink">Bank Accounts</h2>
                        <p class="mt-0.5 text-sm text-erp-mute">Bank expenses and vendor cheques. Not mapped → {{ $systemNames['bank'] ?? 'Bank Accounts' }}.</p>
                    </div>
                    <div class="divide-y divide-erp-line">
                        @forelse ($banks as $bank)
                            <div class="grid grid-cols-2 items-center gap-3 px-5 py-2" wire:key="bank-{{ $bank->bank_account_id }}">
                                <span class="truncate text-sm font-semibold text-erp-text">{{ $bank->bank_name }} — {{ $bank->account_title }} <span class="text-xs text-erp-mute">{{ $bank->account_no }}</span></span>
                                <select wire:model="bankMap.{{ $bank->bank_account_id }}" class="{{ $selectClass }}">
                                    <option value="">Default</option>
                                    @foreach ($assetAccounts->where('type', 'Asset') as $a)
                                        <option value="{{ $a->id }}">{{ $a->code }} · {{ $a->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @empty
                            <div class="px-5 py-6 text-sm text-erp-mute">No bank accounts.</div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-lg border border-erp-line bg-white shadow-sm">
                    <div class="border-b border-erp-line px-5 py-4">
                        <h2 class="text-base font-bold text-erp-ink">POS Payment Modes</h2>
                        <p class="mt-0.5 text-sm text-erp-mute">Where each payment type of a POS sale is debited.</p>
                    </div>
                    <div class="divide-y divide-erp-line">
                        @foreach ($paymentModes as $mode)
                            <div class="grid grid-cols-2 items-center gap-3 px-5 py-2" wire:key="pm-{{ $mode->payment_id }}">
                                <span class="truncate text-sm font-semibold text-erp-text">{{ $mode->payment_mode }}</span>
                                <select wire:model="paymentMap.{{ $mode->payment_id }}" class="{{ $selectClass }}">
                                    <option value="">Default — {{ $systemNames[$paymentDefaults[$mode->payment_id]] ?? $paymentDefaults[$mode->payment_id] }}</option>
                                    @foreach ($assetAccounts as $a)
                                        <option value="{{ $a->id }}">{{ $a->code }} · {{ $a->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="h-10 rounded-lg border border-erp bg-erp px-6 text-sm font-bold text-white transition hover:bg-erp-dark">Save Mappings</button>
        </div>
    </form>
</div>
