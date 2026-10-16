<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $input = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $btn = 'h-9 rounded-lg border border-erp-line bg-white px-3 text-sm font-bold text-erp-text transition hover:bg-slate-50';
        $btnPrimary = 'h-9 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:opacity-90';
        $money = fn ($v) => number_format((float) $v, 0);
        $mon = fn ($p) => \Illuminate\Support\Carbon::parse($p . '-01')->format('M Y');
    @endphp

    @if (session()->has('tax_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity>{{ session('tax_message') }}</div>
    @endif
    @error('tax')
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ $message }}</div>
    @enderror

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-erp-line px-5 py-4">
            <div>
                <h2 class="text-base font-bold text-erp-ink">Sales Tax</h2>
                <p class="mt-0.5 text-sm text-erp-mute">Output tax on sales (FBR and SRB), input tax on vendor bills, what is payable each month, and receipts that were not reported to FBR.</p>
            </div>
            <div class="flex flex-wrap gap-1">
                @foreach (['summary' => 'Monthly Summary', 'sales' => 'Sales Register', 'purchases' => 'Purchase Register', 'filings' => 'Returns Filed'] as $key => $label)
                    <button type="button" wire:click="$set('tab', '{{ $key }}')" class="{{ $tab === $key ? $btnPrimary : $btn }}">{{ $label }}</button>
                @endforeach
            </div>
        </div>
        <div class="flex flex-wrap items-end gap-3 border-b border-erp-line px-5 py-3">
            @if ($tab === 'summary')
                <label class="block"><span class="{{ $labelClass }}">From</span><input type="month" wire:model.live="fromPeriod" class="mt-1 {{ $input }} w-40"></label>
                <label class="block"><span class="{{ $labelClass }}">To</span><input type="month" wire:model.live="toPeriod" class="mt-1 {{ $input }} w-40"></label>
            @elseif ($tab !== 'filings')
                <label class="block"><span class="{{ $labelClass }}">Month</span><input type="month" wire:model.live="month" class="mt-1 {{ $input }} w-40"></label>
            @endif
            @if ($tab !== 'filings' && $allBranches)
                <label class="block"><span class="{{ $labelClass }}">Branch</span>
                    <select wire:model.live="branchId" class="mt-1 {{ $input }} w-52"><option value="">All branches</option>
                        @foreach ($branches as $b)<option value="{{ $b->branch_id }}">{{ $b->branch_name }}</option>@endforeach</select></label>
            @endif
            @if ($tab === 'sales')
                <label class="flex h-9 items-center gap-2 text-sm"><input type="checkbox" wire:model.live="missingOnly" class="rounded border-erp-line text-erp"> Only taxed receipts without FBR invoice number</label>
                <button type="button" wire:click="export" class="{{ $btn }} ml-auto">Download CSV</button>
            @endif
        </div>

        @if ($tab === 'summary')
            @php
                $tot = fn ($k) => array_sum(array_column($summary, $k));
                $hasSrb = $tot('srb_tax') > 0;
            @endphp
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                        <th class="px-5 py-3">Month</th>
                        <th class="px-3 py-3 text-right">Sales (excl. tax)</th>
                        <th class="px-3 py-3 text-right">FBR output tax</th>
                        <th class="px-3 py-3 text-right">Input tax</th>
                        <th class="px-3 py-3 text-right">FBR payable</th>
                        @if ($hasSrb)<th class="px-3 py-3 text-right">SRB tax</th>@endif
                        @if ($fbrOn)<th class="px-3 py-3 text-right">Not sent to FBR</th>@endif
                        @if ($ledger !== null)<th class="px-3 py-3 text-right">Ledger output / input</th>@endif
                        <th class="px-5 py-3">Return</th>
                    </tr></thead>
                    <tbody>
                        @foreach ($summary as $p => $s)
                            @php
                                $payable = $s['fbr_tax'] - $s['input_tax'];
                                $glOut = $ledger[$p]['output'] ?? 0; $glIn = $ledger[$p]['input'] ?? 0;
                                $glDiff = $ledger !== null && (abs($glOut - $s['fbr_tax'] - $s['srb_tax']) >= 1 || abs($glIn - $s['input_tax']) >= 1);
                            @endphp
                            <tr class="border-b border-erp-line" wire:key="s-{{ $p }}">
                                <td class="px-5 py-2 font-semibold"><button type="button" wire:click="openRegister('{{ $p }}')" class="hover:underline">{{ $mon($p) }}</button>
                                    <div class="text-xs font-normal text-erp-mute">{{ number_format($s['taxed']) }} / {{ number_format($s['receipts']) }} receipts taxed{{ $s['returns_value'] ? ' · returns ' . $money($s['returns_value']) : '' }}</div></td>
                                <td class="px-3 py-2 text-right tabular-nums">{{ $money($s['value']) }}</td>
                                <td class="px-3 py-2 text-right tabular-nums">{{ $money($s['fbr_tax']) }}</td>
                                <td class="px-3 py-2 text-right tabular-nums">{{ $s['input_tax'] ? $money($s['input_tax']) : '—' }}</td>
                                <td class="px-3 py-2 text-right font-semibold tabular-nums {{ $payable < 0 ? 'text-emerald-700' : '' }}">{{ $payable < 0 ? '(' . $money(-$payable) . ') refund' : $money($payable) }}</td>
                                @if ($hasSrb)<td class="px-3 py-2 text-right tabular-nums">{{ $money($s['srb_tax']) }}</td>@endif
                                @if ($fbrOn)
                                    <td class="px-3 py-2 text-right tabular-nums">
                                        @if ($s['missing_fbr'])
                                            <button type="button" wire:click="openRegister('{{ $p }}', true)" class="font-semibold text-rose-700 hover:underline">{{ number_format($s['missing_fbr']) }} · {{ $money($s['missing_fbr_tax']) }}</button>
                                        @else <span class="text-emerald-700">0</span> @endif
                                    </td>
                                @endif
                                @if ($ledger !== null)
                                    <td class="px-3 py-2 text-right text-xs tabular-nums {{ $glDiff ? 'text-amber-700' : 'text-erp-mute' }}" title="{{ $glDiff ? 'Ledger differs from source — check reposting / manual entries' : 'Matches' }}">{{ $money($glOut) }} / {{ $money($glIn) }}</td>
                                @endif
                                <td class="whitespace-nowrap px-5 py-2 text-xs">
                                    @foreach (['fbr' => 'FBR', 'srb' => 'SRB'] as $auth => $authLabel)
                                        @continue($auth === 'srb' && !$s['srb_tax'])
                                        @continue($auth === 'fbr' && !$s['fbr_tax'] && !$s['input_tax'])
                                        @if ($f = $filings[$p . ':' . $auth] ?? null)
                                            <span class="mr-2 rounded-full bg-emerald-50 px-2 py-0.5 font-bold text-emerald-700">{{ $authLabel }} filed</span>
                                        @elseif ($admin && $filingsReady && $p < date('Y-m'))
                                            <button type="button" wire:click="startFiling('{{ $p }}', '{{ $auth }}')" class="mr-2 font-bold text-erp hover:underline">File {{ $authLabel }}</button>
                                        @endif
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr class="font-bold">
                        <td class="px-5 py-3">Total</td>
                        <td class="px-3 py-3 text-right tabular-nums">{{ $money($tot('value')) }}</td>
                        <td class="px-3 py-3 text-right tabular-nums">{{ $money($tot('fbr_tax')) }}</td>
                        <td class="px-3 py-3 text-right tabular-nums">{{ $money($tot('input_tax')) }}</td>
                        <td class="px-3 py-3 text-right tabular-nums">{{ $money($tot('fbr_tax') - $tot('input_tax')) }}</td>
                        @if ($hasSrb)<td class="px-3 py-3 text-right tabular-nums">{{ $money($tot('srb_tax')) }}</td>@endif
                        @if ($fbrOn)<td class="px-3 py-3 text-right tabular-nums {{ $tot('missing_fbr') ? 'text-rose-700' : '' }}">{{ number_format($tot('missing_fbr')) }} · {{ $money($tot('missing_fbr_tax')) }}</td>@endif
                        @if ($ledger !== null)<td></td>@endif
                        <td></td>
                    </tr></tfoot>
                </table>
            </div>
            <div class="space-y-1 px-5 py-3 text-xs text-erp-mute">
                <p>Input tax comes from posted Vendor Bills (debit notes reduce it). Purchases entered as stock adjustment, opening stock or CSV carry no tax.</p>
                @if ($fbrOn)<p>"Not sent to FBR" = taxed receipts at branches with FBR integration that have no FBR invoice number. Click to see them.</p>@endif
                <p>Tax on returns is not recorded by the POS, so returns are shown for information only and do not reduce output tax.</p>
            </div>

        @elseif ($tab === 'sales')
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                        <th class="px-5 py-3">Date</th><th class="px-3 py-3">Receipt</th><th class="px-3 py-3">Branch</th><th class="px-3 py-3">Customer</th>
                        <th class="px-3 py-3 text-right">Value excl. tax</th><th class="px-3 py-3 text-right">FBR tax</th><th class="px-3 py-3 text-right">SRB tax</th><th class="px-3 py-3 text-right">Total</th><th class="px-5 py-3">FBR invoice no</th>
                    </tr></thead>
                    @forelse ($register as $r)
                        <tr class="border-b border-erp-line">
                            <td class="whitespace-nowrap px-5 py-2">{{ date('d M', strtotime($r->date)) }}</td>
                            <td class="px-3 py-2 font-mono text-xs">{{ $r->receipt_no }}</td>
                            <td class="px-3 py-2">{{ $r->branch_name }}</td>
                            <td class="px-3 py-2">{{ $r->customer }}</td>
                            <td class="px-3 py-2 text-right tabular-nums">{{ number_format($r->net - $r->fbr_tax - $r->srb_tax - $r->charges, 2) }}</td>
                            <td class="px-3 py-2 text-right tabular-nums">{{ number_format($r->fbr_tax, 2) }}</td>
                            <td class="px-3 py-2 text-right tabular-nums">{{ (float) $r->srb_tax ? number_format($r->srb_tax, 2) : '—' }}</td>
                            <td class="px-3 py-2 text-right tabular-nums">{{ number_format($r->net, 2) }}</td>
                            <td class="px-5 py-2 font-mono text-xs {{ $r->fbrInvNumber ? '' : 'text-rose-700' }}">{{ $r->fbrInvNumber ?: 'not sent' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-5 py-10 text-center text-erp-mute">No taxed receipts in {{ $mon($month) }}.</td></tr>
                    @endforelse
                </table>
            </div>
            @if (count($register) >= 500)<p class="px-5 py-3 text-xs text-amber-700">Showing the first 500 receipts — download the CSV for all of them.</p>@endif

        @elseif ($tab === 'purchases')
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                        <th class="px-5 py-3">Date</th><th class="px-3 py-3">Document</th><th class="px-3 py-3">Vendor</th><th class="px-3 py-3">NTN / STRN</th><th class="px-3 py-3">Vendor invoice</th>
                        <th class="px-3 py-3 text-right">Value excl. tax</th><th class="px-5 py-3 text-right">Input tax</th>
                    </tr></thead>
                    @forelse ($purchases as $b)
                        @php $sign = $b->type === 'debit_note' ? -1 : 1; @endphp
                        <tr class="border-b border-erp-line">
                            <td class="whitespace-nowrap px-5 py-2">{{ date('d M', strtotime($b->bill_date)) }}</td>
                            <td class="px-3 py-2"><a href="{{ url('vendor-bills') }}?bill={{ $b->id }}" class="font-mono text-xs font-bold text-erp hover:underline">{{ $b->doc_no }}</a></td>
                            <td class="px-3 py-2">{{ $b->vendor_name }}</td>
                            <td class="px-3 py-2 text-xs {{ $b->ntn || $b->strn ? '' : 'text-amber-700' }}">{{ trim(($b->ntn ?? '') . ' / ' . ($b->strn ?? ''), ' /') ?: 'missing' }}</td>
                            <td class="px-3 py-2">{{ $b->vendor_ref }}</td>
                            <td class="px-3 py-2 text-right tabular-nums">{{ number_format($sign * $b->value, 2) }}</td>
                            <td class="px-5 py-2 text-right tabular-nums">{{ number_format($sign * $b->tax, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-erp-mute">No posted vendor bills in {{ $mon($month) }}. Input tax is claimed only on purchases entered as Vendor Bills.</td></tr>
                    @endforelse
                </table>
            </div>

        @else
            @if (!$filingsReady)
                <p class="px-5 py-6 text-sm text-amber-700">Return filing is not set up on this server yet (database update pending).</p>
            @else
                @if ($fileAuthority)
                    <div class="border-b border-erp-line bg-slate-50 px-5 py-4">
                        <h3 class="text-sm font-bold text-erp-ink">File {{ strtoupper($fileAuthority) }} return — {{ $mon($filePeriod) }}</h3>
                        <div class="mt-3 grid gap-3 md:grid-cols-4">
                            <label class="block"><span class="{{ $labelClass }}">Tax paid</span><input type="number" step="any" min="0" wire:model="filePaid" class="mt-1 {{ $input }}"></label>
                            <label class="block"><span class="{{ $labelClass }}">CPR / PSID no</span><input type="text" wire:model="fileRef" class="mt-1 {{ $input }}"></label>
                            <label class="block"><span class="{{ $labelClass }}">Filed / paid on</span><input type="date" wire:model="fileDate" class="mt-1 {{ $input }}"></label>
                            <label class="block"><span class="{{ $labelClass }}">Adjustment in return (+ / −)</span><input type="number" step="any" wire:model="fileAdjustment" class="mt-1 {{ $input }}"></label>
                            <label class="block md:col-span-2"><span class="{{ $labelClass }}">Notes</span><input type="text" wire:model="fileNotes" class="mt-1 {{ $input }}"></label>
                            @if ($accounting)
                                <label class="flex items-end gap-2 pb-2 text-sm"><input type="checkbox" wire:model.live="filePost" class="rounded border-erp-line text-erp"> Post to ledger</label>
                                @if ($filePost)
                                    <label class="block"><span class="{{ $labelClass }}">Paid from</span>
                                        <select wire:model="filePaidFrom" class="mt-1 {{ $input }}"><option value="">—</option>
                                            @foreach ($payAccounts as $a)<option value="{{ $a->id }}">{{ $a->code }} {{ $a->name }}</option>@endforeach</select></label>
                                @endif
                            @endif
                        </div>
                        @if ($accounting && $filePost)<p class="mt-2 text-xs text-erp-mute">Ledger entry: Dr Output Tax (month's tax) / Cr Input Tax (FBR, input used) / Cr the paid-from account. Any difference goes to Adjustments Clearing.</p>@endif
                        <div class="mt-3 flex justify-end gap-2">
                            <button type="button" wire:click="$set('fileAuthority', '')" class="{{ $btn }}">Cancel</button>
                            <button type="button" wire:click="file" class="{{ $btnPrimary }}">Record Return</button>
                        </div>
                    </div>
                @endif
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                        <th class="px-5 py-3">Month</th><th class="px-3 py-3">Authority</th><th class="px-3 py-3 text-right">Output</th><th class="px-3 py-3 text-right">Input</th>
                        <th class="px-3 py-3 text-right">Paid</th><th class="px-3 py-3">CPR</th><th class="px-3 py-3">Filed on</th><th class="px-3 py-3">Ledger</th><th></th>
                    </tr></thead>
                    @forelse ($filings as $f)
                        <tr class="border-b border-erp-line">
                            <td class="px-5 py-2 font-semibold">{{ $mon($f->period) }}</td>
                            <td class="px-3 py-2">{{ strtoupper($f->authority) }}</td>
                            <td class="px-3 py-2 text-right tabular-nums">{{ $money($f->output_tax) }}</td>
                            <td class="px-3 py-2 text-right tabular-nums">{{ $money($f->input_tax) }}</td>
                            <td class="px-3 py-2 text-right tabular-nums">{{ $money($f->paid_amount) }}{{ (float) $f->adjustment ? ' (adj ' . $money($f->adjustment) . ')' : '' }}</td>
                            <td class="px-3 py-2 font-mono text-xs">{{ $f->payment_ref }}</td>
                            <td class="px-3 py-2">{{ date('d M Y', strtotime($f->filed_on)) }}</td>
                            <td class="px-3 py-2">@if ($f->journal_entry_id)<a href="{{ url('journal-entries') }}?entry={{ $f->journal_entry_id }}" class="text-xs font-bold text-erp hover:underline">View entry</a>@else — @endif</td>
                            <td class="px-5 py-2 text-right">@if ($admin)<button type="button" wire:click="deleteFiling({{ $f->id }})" wire:confirm="Delete this filing{{ $f->journal_entry_id ? ' and reverse its ledger entry' : '' }}?" class="text-xs font-bold text-rose-700 hover:underline">Delete</button>@endif</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-5 py-10 text-center text-erp-mute">No returns recorded yet. Use "File FBR" on the Monthly Summary.</td></tr>
                    @endforelse
                </table>
            @endif
        @endif
    </div>
</div>
