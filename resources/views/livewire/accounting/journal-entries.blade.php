<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $cellInput = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $statusColors = ['draft' => 'bg-amber-50 text-amber-700', 'posted' => 'bg-emerald-50 text-emerald-700'];
        $money = fn ($v) => number_format((float) $v, 2);
    @endphp

    @if (session()->has('je_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition.opacity>{{ session('je_message') }}</div>
    @endif
    @error('journal')
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ $message }}</div>
    @enderror

    @if ($showForm)
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">{{ $entryId ? 'Edit Draft' : 'New Journal Entry' }}</h2>
                <p class="mt-0.5 text-sm text-erp-mute">Total debits must equal total credits before the entry can be posted.</p>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-4">
                <label class="block">
                    <span class="{{ $labelClass }}">Date</span>
                    <input type="date" wire:model="entryDate" class="{{ $inputClass }}">
                    @error('entryDate') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Branch</span>
                    <select wire:model="branchId" class="{{ $inputClass }}">
                        <option value="">— Company level —</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block md:col-span-2">
                    <span class="{{ $labelClass }}">Narration</span>
                    <input type="text" wire:model="narration" class="{{ $inputClass }}" placeholder="What is this entry for?">
                </label>
            </div>

            <div class="overflow-x-auto border-t border-erp-line">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left">
                        <tr>
                            <th class="w-8 px-3 py-2"></th>
                            <th class="px-3 py-2 {{ $labelClass }}">Account</th>
                            <th class="w-40 px-3 py-2 text-right {{ $labelClass }}">Debit</th>
                            <th class="w-40 px-3 py-2 text-right {{ $labelClass }}">Credit</th>
                            <th class="px-3 py-2 {{ $labelClass }}">Line Note</th>
                            <th class="w-10 px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line">
                        @foreach ($lines as $i => $line)
                            <tr wire:key="line-{{ $i }}">
                                <td class="px-3 py-2 text-xs font-bold text-erp-mute">{{ $i + 1 }}</td>
                                <td class="min-w-[16rem] px-3 py-2">
                                    <select wire:model="lines.{{ $i }}.account_id" class="{{ $cellInput }}">
                                        <option value="">Select account</option>
                                        @foreach ($accountsByType as $type => $accounts)
                                            <optgroup label="{{ $type }}">
                                                @foreach ($accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-3 py-2"><input type="number" step="0.01" min="0" wire:model.live.debounce.400ms="lines.{{ $i }}.debit" class="{{ $cellInput }} text-right"></td>
                                <td class="px-3 py-2"><input type="number" step="0.01" min="0" wire:model.live.debounce.400ms="lines.{{ $i }}.credit" class="{{ $cellInput }} text-right"></td>
                                <td class="min-w-[12rem] px-3 py-2"><input type="text" wire:model="lines.{{ $i }}.narration" class="{{ $cellInput }}"></td>
                                <td class="px-3 py-2 text-right">
                                    <button type="button" wire:click="removeLine({{ $i }})" class="text-rose-500 hover:text-rose-700" title="Remove line">&times;</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 text-sm font-bold">
                        <tr>
                            <td></td>
                            <td class="px-3 py-2"><button type="button" wire:click="addLine" class="text-erp-dark hover:underline">+ Add line</button></td>
                            <td class="px-3 py-2 text-right text-erp-ink">{{ $money($totalDebit) }}</td>
                            <td class="px-3 py-2 text-right text-erp-ink">{{ $money($totalCredit) }}</td>
                            <td class="px-3 py-2" colspan="2">
                                @if (round($difference, 2) == 0 && $totalDebit > 0)
                                    <span class="text-emerald-700">Balanced</span>
                                @else
                                    <span class="text-rose-600">Difference {{ $money(abs($difference)) }}</span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="flex flex-wrap gap-2 border-t border-erp-line p-5">
                <button type="button" wire:click="saveAndPost" wire:loading.attr="disabled" class="h-10 rounded-lg border border-erp bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark disabled:opacity-60">Save &amp; Post</button>
                <button type="button" wire:click="saveDraft" wire:loading.attr="disabled" class="h-10 rounded-lg border border-erp-line bg-white px-5 text-sm font-bold text-erp-text transition hover:bg-slate-50">Save Draft</button>
                <button type="button" wire:click="cancel" class="h-10 rounded-lg px-4 text-sm font-bold text-erp-mute hover:text-erp-ink">Cancel</button>
            </div>
        </div>
    @endif

    @if ($viewing)
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-2 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-base font-bold text-erp-ink">{{ $viewing->entry_no }}</h2>
                        <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $statusColors[$viewing->status] ?? '' }}">{{ ucfirst($viewing->status) }}</span>
                        @if ($viewing->reversedBy)
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600">Reversed by {{ $viewing->reversedBy->entry_no }}</span>
                        @endif
                        @if ($viewing->reversalOf)
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600">Reverses {{ $viewing->reversalOf->entry_no }}</span>
                        @endif
                        @if ($viewing->source_type !== 'manual')
                            <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[11px] font-bold text-sky-700">{{ strtoupper($viewing->source_type) }} #{{ $viewing->source_id }}</span>
                        @endif
                    </div>
                    <p class="mt-0.5 text-sm text-erp-mute">
                        {{ $viewing->entry_date->format('d M Y') }}
                        · {{ $viewing->branch_id ? ($branchNames[$viewing->branch_id] ?? 'Branch #' . $viewing->branch_id) : 'Company level' }}
                        @if ($viewing->narration) · {{ $viewing->narration }} @endif
                    </p>
                </div>
                <button type="button" wire:click="view({{ $viewing->id }})" class="text-sm font-bold text-erp-mute hover:text-erp-ink">Close</button>
            </div>
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left">
                    <tr>
                        <th class="px-5 py-2 {{ $labelClass }}">Account</th>
                        <th class="px-5 py-2 {{ $labelClass }}">Note</th>
                        <th class="px-5 py-2 text-right {{ $labelClass }}">Debit</th>
                        <th class="px-5 py-2 text-right {{ $labelClass }}">Credit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-erp-line">
                    @foreach ($viewing->lines as $line)
                        <tr>
                            <td class="px-5 py-2"><span class="font-mono text-xs font-bold text-erp-mute">{{ $line->account->code }}</span> {{ $line->account->name }}</td>
                            <td class="px-5 py-2 text-erp-mute">{{ $line->narration }}</td>
                            <td class="px-5 py-2 text-right">{{ (float) $line->debit ? $money($line->debit) : '' }}</td>
                            <td class="px-5 py-2 text-right">{{ (float) $line->credit ? $money($line->credit) : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 font-bold">
                    <tr>
                        <td class="px-5 py-2" colspan="2">Total</td>
                        <td class="px-5 py-2 text-right">{{ $money($viewing->lines->sum('debit')) }}</td>
                        <td class="px-5 py-2 text-right">{{ $money($viewing->lines->sum('credit')) }}</td>
                    </tr>
                </tfoot>
            </table>
            <div class="flex flex-wrap items-end gap-2 border-t border-erp-line p-5">
                @if (!$viewing->isPosted())
                    <button type="button" wire:click="post({{ $viewing->id }})" wire:confirm="Post this entry? Posted entries cannot be edited." class="h-10 rounded-lg border border-erp bg-erp px-5 text-sm font-bold text-white hover:bg-erp-dark">Post</button>
                    <button type="button" wire:click="edit({{ $viewing->id }})" class="h-10 rounded-lg border border-erp-line bg-white px-5 text-sm font-bold text-erp-text hover:bg-slate-50">Edit</button>
                    <button type="button" wire:click="delete({{ $viewing->id }})" wire:confirm="Delete this draft?" class="h-10 rounded-lg border border-rose-200 bg-rose-50 px-5 text-sm font-bold text-rose-700 hover:bg-rose-100">Delete</button>
                @elseif (!$viewing->reversedBy && !$viewing->reversal_of_id)
                    <label class="block">
                        <span class="{{ $labelClass }}">Reversal Date</span>
                        <input type="date" wire:model="reverseDate" class="{{ $inputClass }}">
                    </label>
                    <label class="block min-w-[16rem] flex-1">
                        <span class="{{ $labelClass }}">Reason</span>
                        <input type="text" wire:model="reverseReason" class="{{ $inputClass }}" placeholder="Optional">
                    </label>
                    <button type="button" wire:click="reverse({{ $viewing->id }})" wire:confirm="Post a reversal entry for {{ $viewing->entry_no }}?" class="h-10 rounded-lg border border-amber-300 bg-amber-50 px-5 text-sm font-bold text-amber-800 hover:bg-amber-100">Reverse</button>
                    @error('reverseDate') <span class="w-full text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                @endif
            </div>
        </div>
    @endif

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="grid flex-1 gap-3 sm:grid-cols-4">
                <label class="block"><span class="{{ $labelClass }}">From</span><input type="date" wire:model.live="from" class="{{ $inputClass }}"></label>
                <label class="block"><span class="{{ $labelClass }}">To</span><input type="date" wire:model.live="to" class="{{ $inputClass }}"></label>
                <label class="block">
                    <span class="{{ $labelClass }}">Status</span>
                    <select wire:model.live="status" class="{{ $inputClass }}">
                        <option value="">All</option>
                        <option value="draft">Draft</option>
                        <option value="posted">Posted</option>
                    </select>
                </label>
                <label class="block"><span class="{{ $labelClass }}">Search</span><input type="search" wire:model.live.debounce.300ms="search" class="{{ $inputClass }}" placeholder="JV no or narration"></label>
            </div>
            <button type="button" wire:click="create" class="h-10 rounded-lg border border-erp bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">+ Journal Entry</button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left">
                    <tr>
                        <th class="px-5 py-3 {{ $labelClass }}">Entry</th>
                        <th class="px-5 py-3 {{ $labelClass }}">Date</th>
                        <th class="px-5 py-3 {{ $labelClass }}">Narration</th>
                        <th class="px-5 py-3 {{ $labelClass }}">Source</th>
                        <th class="px-5 py-3 text-right {{ $labelClass }}">Amount</th>
                        <th class="px-5 py-3 {{ $labelClass }}">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-erp-line">
                    @forelse ($entries as $entry)
                        <tr wire:key="je-{{ $entry->id }}" wire:click="view({{ $entry->id }})" class="cursor-pointer transition hover:bg-slate-50 {{ (int) $viewId === (int) $entry->id ? 'bg-slate-50' : '' }}">
                            <td class="whitespace-nowrap px-5 py-2.5 font-mono text-xs font-bold text-erp-ink">{{ $entry->entry_no }}</td>
                            <td class="whitespace-nowrap px-5 py-2.5 text-erp-text">{{ $entry->entry_date->format('d M Y') }}</td>
                            <td class="max-w-md truncate px-5 py-2.5 text-erp-text">{{ $entry->narration }}</td>
                            <td class="px-5 py-2.5 text-xs font-semibold uppercase text-erp-mute">{{ $entry->source_type }}</td>
                            <td class="whitespace-nowrap px-5 py-2.5 text-right font-semibold text-erp-ink">{{ $money($entry->total) }}</td>
                            <td class="px-5 py-2.5">
                                <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $statusColors[$entry->status] ?? '' }}">{{ ucfirst($entry->status) }}</span>
                                @if ($entry->reversed_by_exists)
                                    <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-500">Reversed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-erp-mute">No journal entries in this range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($entries->hasPages())
            <div class="border-t border-erp-line px-5 py-3">{{ $entries->links() }}</div>
        @endif
    </div>
</div>
