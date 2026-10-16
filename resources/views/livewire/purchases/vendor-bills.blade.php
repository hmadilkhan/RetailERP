<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $cellInput = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $btn = 'h-10 rounded-lg border border-erp-line bg-white px-4 text-sm font-bold text-erp-text transition hover:bg-slate-50';
        $btnPrimary = 'h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:opacity-90';
        $statusColors = ['draft' => 'bg-amber-50 text-amber-700', 'posted' => 'bg-emerald-50 text-emerald-700', 'cancelled' => 'bg-slate-100 text-slate-500'];
        $typeNames = \App\Models\VendorBill::TYPES;
        $money = fn ($v) => number_format((float) $v, 2);
        $qty = fn ($v) => rtrim(rtrim(number_format((float) $v, 4, '.', ','), '0'), '.');
    @endphp

    @if (session()->has('vb_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.opacity>{{ session('vb_message') }}</div>
    @endif
    @error('bill')
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ $message }}</div>
    @enderror

    @if ($showForm)
        {{-- ===================== New / edit draft bill ===================== --}}
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">{{ $billId ? 'Edit Draft Bill' : 'New Vendor Bill' }}</h2>
                <p class="mt-0.5 text-sm text-erp-mute">Enter the vendor's invoice. Posting adds the items to stock at their landed cost and adds the amount to the vendor's balance.</p>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-4">
                <label class="block">
                    <span class="{{ $labelClass }}">Vendor</span>
                    <select wire:model="vendorId" class="{{ $inputClass }}">
                        <option value="">— Select vendor —</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->vendor_name }}</option>
                        @endforeach
                    </select>
                    @error('vendorId') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Branch (stock goes here)</span>
                    <select wire:model="branchId" class="{{ $inputClass }}">
                        <option value="">— Select branch —</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                    @error('branchId') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Vendor's Bill / Invoice No</span>
                    <input type="text" wire:model="vendorRef" class="{{ $inputClass }}" placeholder="e.g. INV-2041">
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="block">
                        <span class="{{ $labelClass }}">Bill Date</span>
                        <input type="date" wire:model="billDate" class="{{ $inputClass }}">
                        @error('billDate') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Due Date</span>
                        <input type="date" wire:model="dueDate" class="{{ $inputClass }}">
                        @error('dueDate') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                    </label>
                </div>
            </div>

            <div class="border-t border-erp-line p-5">
                <div class="relative max-w-xl">
                    <span class="{{ $labelClass }}">Add Item</span>
                    <input type="text" wire:model.live.debounce.300ms="productSearch" class="{{ $inputClass }}" placeholder="Type product name or code (2+ letters)">
                    @if (count($searchResults))
                        <div class="absolute z-20 mt-1 max-h-72 w-full overflow-auto rounded-lg border border-erp-line bg-white shadow-lg">
                            @foreach ($searchResults as $product)
                                <button type="button" wire:click="addProduct({{ $product->id }})" class="block w-full px-4 py-2 text-left text-sm hover:bg-slate-50">
                                    {{ $product->product_name }} <span class="text-erp-mute">{{ $product->item_code }}</span>
                                </button>
                            @endforeach
                        </div>
                    @elseif (strlen(trim($productSearch)) >= 2)
                        <p class="mt-1 text-xs text-erp-mute">No active product matches.</p>
                    @endif
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-erp-line text-left {{ $labelClass }}">
                                <th class="py-2 pr-3">Item</th>
                                <th class="w-28 py-2 pr-3">Qty</th>
                                <th class="w-32 py-2 pr-3">Unit Price</th>
                                <th class="w-28 py-2 pr-3">Discount (Rs)</th>
                                <th class="w-36 py-2 pr-3">Tax</th>
                                <th class="w-32 py-2 pr-3">Batch</th>
                                <th class="w-36 py-2 pr-3">Expiry</th>
                                <th class="w-32 py-2 pr-3 text-right">Amount</th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lines as $i => $line)
                                @php
                                    $lineAmount = max(0, (float) ($line['qty'] ?: 0) * (float) ($line['unit_price'] ?: 0) - (float) ($line['discount'] ?: 0));
                                    $lineTax = $lineAmount * (float) ($line['tax_rate'] ?: 0) / 100;
                                @endphp
                                <tr class="border-b border-erp-line" wire:key="vb-line-{{ $i }}-{{ $line['product_id'] }}">
                                    <td class="py-2 pr-3 font-semibold text-erp-ink">{{ $line['name'] }}</td>
                                    <td class="py-2 pr-3"><input type="number" step="any" min="0" wire:model.live.debounce.400ms="lines.{{ $i }}.qty" class="{{ $cellInput }}"></td>
                                    <td class="py-2 pr-3"><input type="number" step="any" min="0" wire:model.live.debounce.400ms="lines.{{ $i }}.unit_price" class="{{ $cellInput }}"></td>
                                    <td class="py-2 pr-3"><input type="number" step="any" min="0" wire:model.live.debounce.400ms="lines.{{ $i }}.discount" class="{{ $cellInput }}"></td>
                                    <td class="py-2 pr-3">
                                        <select wire:model.live="lines.{{ $i }}.tax_rate" class="{{ $cellInput }}">
                                            <option value="">No tax</option>
                                            @foreach ($taxes as $tax)
                                                <option value="{{ (float) $tax->value }}">{{ $tax->name }} ({{ (float) $tax->value }}%)</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-2 pr-3"><input type="text" maxlength="100" wire:model="lines.{{ $i }}.batch_no" class="{{ $cellInput }}"></td>
                                    <td class="py-2 pr-3"><input type="date" wire:model="lines.{{ $i }}.expiry_date" class="{{ $cellInput }}"></td>
                                    <td class="py-2 pr-3 text-right tabular-nums">{{ $money($lineAmount + $lineTax) }}</td>
                                    <td class="py-2 text-right">
                                        <button type="button" wire:click="removeLine({{ $i }})" class="text-rose-600 hover:text-rose-800" title="Remove">&times;</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="py-6 text-center text-sm text-erp-mute">Search above and add the items on the vendor's bill.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid gap-4 border-t border-erp-line p-5 md:grid-cols-3">
                <label class="block">
                    <span class="{{ $labelClass }}">Other Charges (freight, loading)</span>
                    <input type="number" step="any" min="0" wire:model.live.debounce.400ms="otherCharges" class="{{ $inputClass }}">
                    <span class="mt-1 block text-xs text-erp-mute">Spread over the items by value and added to their cost.</span>
                    @error('otherCharges') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Narration</span>
                    <input type="text" wire:model="narration" class="{{ $inputClass }}">
                </label>
                @if ($formTotals)
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-1 self-end text-sm">
                        <dt class="text-erp-mute">Items</dt><dd class="text-right tabular-nums">{{ $money($formTotals['subtotal']) }}</dd>
                        <dt class="text-erp-mute">Discount</dt><dd class="text-right tabular-nums">− {{ $money($formTotals['discount']) }}</dd>
                        <dt class="text-erp-mute">Tax</dt><dd class="text-right tabular-nums">{{ $money($formTotals['tax_amount']) }}</dd>
                        <dt class="text-erp-mute">Other charges</dt><dd class="text-right tabular-nums">{{ $money((float) ($otherCharges ?: 0)) }}</dd>
                        <dt class="font-bold text-erp-ink">Bill Total</dt><dd class="text-right font-bold tabular-nums text-erp-ink">{{ $money($formTotals['total']) }}</dd>
                    </dl>
                @endif
            </div>

            <div class="flex flex-wrap justify-end gap-2 border-t border-erp-line px-5 py-4">
                <button type="button" wire:click="cancelForm" class="{{ $btn }}">Cancel</button>
                <button type="button" wire:click="saveDraft" wire:loading.attr="disabled" class="{{ $btn }}">Save Draft</button>
                <button type="button" wire:click="saveAndPost" wire:loading.attr="disabled"
                        wire:confirm="Post this bill? Items will be added to stock and the vendor balance will go up." class="{{ $btnPrimary }}">Save &amp; Post</button>
            </div>
        </div>

    @elseif ($viewing)
        {{-- ===================== Bill / note detail ===================== --}}
        @php $isBill = $viewing->type === 'bill'; $payable = in_array($viewing->type, \App\Models\VendorBill::PAYABLE, true); @endphp
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <button type="button" wire:click="back" class="text-xs font-bold text-erp hover:underline">&larr; All bills</button>
                    <h2 class="mt-1 text-lg font-bold text-erp-ink">
                        {{ $typeNames[$viewing->type] }} {{ $viewing->doc_no }}
                        <span class="ml-2 rounded-full px-2.5 py-0.5 text-xs font-bold {{ $statusColors[$viewing->status] ?? '' }}">{{ ucfirst($viewing->status) }}</span>
                        @if ($waitingApproval)<span class="ml-1 rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-bold text-sky-700">Waiting for approval</span>@endif
                    </h2>
                    <p class="mt-0.5 text-sm text-erp-mute">
                        {{ $vendors->firstWhere('id', $viewing->vendor_id)->vendor_name ?? 'Vendor #' . $viewing->vendor_id }}
                        · {{ $branchNames[$viewing->branch_id] ?? '' }} · {{ $viewing->bill_date->format('d M Y') }}
                        @if ($viewing->vendor_ref) · Bill# {{ $viewing->vendor_ref }} @endif
                        @if ($viewing->due_date) · Due {{ $viewing->due_date->format('d M Y') }} @endif
                        @if ($viewing->againstBill) · against <button type="button" wire:click="show({{ $viewing->againstBill->id }})" class="font-semibold text-erp hover:underline">{{ $viewing->againstBill->doc_no }}</button> @endif
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if ($viewing->type === 'po_bill')
                        {{-- Phase 4.4: PO se juda bill — edit / post / match PO Matching pe --}}
                        <a href="{{ url('po-matching') }}?bill={{ $viewing->id }}" class="{{ $viewing->status === 'draft' ? $btnPrimary : $btn }} inline-flex items-center">{{ $viewing->status === 'draft' ? 'Open in PO Matching' : 'View match' }}</a>
                        @if ($viewing->status === 'posted' && $viewing->open_amount >= 1)
                            <button type="button" wire:click="openAction('pay')" class="{{ $btnPrimary }}">Pay</button>
                        @endif
                        @if ($viewing->status === 'posted' && (float) $viewing->paid == 0)
                            <button type="button" wire:click="cancelBill({{ $viewing->id }})" wire:confirm="Cancel this PO bill? The difference booked to the vendor will be reversed." class="{{ $btn }} text-rose-700">Cancel Bill</button>
                        @endif
                    @elseif ($viewing->status === 'draft')
                        <button type="button" wire:click="edit({{ $viewing->id }})" class="{{ $btn }}">Edit</button>
                        <button type="button" wire:click="cancelBill({{ $viewing->id }})" wire:confirm="Delete this draft?" class="{{ $btn }} text-rose-700">Delete</button>
                        <button type="button" wire:click="post({{ $viewing->id }})" wire:confirm="Post this bill? Items will be added to stock and the vendor balance will go up." class="{{ $btnPrimary }}">Post</button>
                    @elseif ($isBill && $viewing->status === 'posted')
                        @if ($viewing->open_amount >= 1)
                            <button type="button" wire:click="openAction('pay')" class="{{ $btnPrimary }}">Pay</button>
                            <button type="button" wire:click="openAction('credit')" class="{{ $btn }}">Credit Note</button>
                        @endif
                        <button type="button" wire:click="openAction('debit')" class="{{ $btn }}">Return Goods (Debit Note)</button>
                        <button type="button" wire:click="openAction('landed')" class="{{ $btn }}">Add Landed Cost</button>
                        @if ((float) $viewing->paid == 0)
                            <button type="button" wire:click="cancelBill({{ $viewing->id }})" wire:confirm="Cancel this bill? Its stock will be removed and the vendor balance reversed. Only possible if none of the stock has been sold." class="{{ $btn }} text-rose-700">Cancel Bill</button>
                        @endif
                    @elseif ($viewing->type === 'landed_cost' && $viewing->status === 'posted' && $viewing->open_amount >= 1)
                        <button type="button" wire:click="openAction('pay')" class="{{ $btnPrimary }}">Pay</button>
                    @endif
                </div>
            </div>

            @if ($action === 'landed')
                <div class="grid gap-4 border-b border-erp-line bg-slate-50 p-5 md:grid-cols-6">
                    <label class="block">
                        <span class="{{ $labelClass }}">Amount</span>
                        <input type="number" step="any" min="0" wire:model="lcAmount" class="{{ $inputClass }}">
                        @error('lcAmount') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Spread by</span>
                        <select wire:model="lcMethod" class="{{ $inputClass }}">
                            <option value="value">Item value</option>
                            <option value="qty">Quantity</option>
                        </select>
                    </label>
                    <label class="block md:col-span-2">
                        <span class="{{ $labelClass }}">Paid to (transporter / agent)</span>
                        <select wire:model="lcVendor" class="{{ $inputClass }}">
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->vendor_name }}</option>
                            @endforeach
                        </select>
                        @error('lcVendor') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Their bill no</span>
                        <input type="text" wire:model="lcRef" class="{{ $inputClass }}">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Date</span>
                        <input type="date" wire:model="noteDate" class="{{ $inputClass }}">
                    </label>
                    <label class="block md:col-span-4">
                        <span class="{{ $labelClass }}">What is it</span>
                        <input type="text" wire:model="noteNarration" class="{{ $inputClass }}" placeholder="e.g. freight, customs duty, clearing">
                    </label>
                    <div class="flex items-end gap-2 md:col-span-2">
                        <button type="button" wire:click="closeAction" class="{{ $btn }}">Close</button>
                        <button type="button" wire:click="saveLandedCost" wire:loading.attr="disabled" wire:confirm="Add this cost to the items of this bill?" class="{{ $btnPrimary }}">Add Landed Cost</button>
                    </div>
                    <p class="text-xs text-erp-mute md:col-span-6">Freight, duty or clearing that arrives after the goods. It is spread over this bill's items and added to their cost, and the amount becomes payable to the vendor you pick here.</p>
                </div>
            @elseif ($action === 'pay')
                <div class="grid gap-4 border-b border-erp-line bg-slate-50 p-5 md:grid-cols-5">
                    <label class="block">
                        <span class="{{ $labelClass }}">Amount (open {{ $money($viewing->open_amount) }})</span>
                        <input type="number" step="1" min="1" wire:model="payAmount" class="{{ $inputClass }}">
                        @error('payAmount') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Paid From</span>
                        <select wire:model.live="payMode" class="{{ $inputClass }}">
                            <option value="cash">Cash (this branch)</option>
                            <option value="bank">Bank</option>
                        </select>
                    </label>
                    @if ($payMode === 'bank')
                        <label class="block">
                            <span class="{{ $labelClass }}">Bank Account</span>
                            <select wire:model="payBank" class="{{ $inputClass }}">
                                <option value="">— Select —</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->bank_account_id }}">{{ $bank->account_title }} {{ $bank->account_no }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block">
                            <span class="{{ $labelClass }}">Cheque / Ref No</span>
                            <input type="text" wire:model="payRef" class="{{ $inputClass }}">
                        </label>
                    @endif
                    <div class="flex items-end gap-2">
                        <button type="button" wire:click="closeAction" class="{{ $btn }}">Close</button>
                        <button type="button" wire:click="pay" wire:loading.attr="disabled" class="{{ $btnPrimary }}">Save Payment</button>
                    </div>
                    <p class="text-xs text-erp-mute md:col-span-5">Amounts are whole rupees (the vendor, cash and bank ledgers store whole rupees).</p>
                </div>
            @elseif ($action === 'credit')
                <div class="grid gap-4 border-b border-erp-line bg-slate-50 p-5 md:grid-cols-4">
                    <label class="block">
                        <span class="{{ $labelClass }}">Credit Amount</span>
                        <input type="number" step="any" min="0" wire:model="noteAmount" class="{{ $inputClass }}">
                        @error('noteAmount') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Date</span>
                        <input type="date" wire:model="noteDate" class="{{ $inputClass }}">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Reason</span>
                        <input type="text" wire:model="noteNarration" class="{{ $inputClass }}" placeholder="e.g. price difference, rebate">
                    </label>
                    <div class="flex items-end gap-2">
                        <button type="button" wire:click="closeAction" class="{{ $btn }}">Close</button>
                        <button type="button" wire:click="saveCreditNote" wire:loading.attr="disabled" class="{{ $btnPrimary }}">Post Credit Note</button>
                    </div>
                    <p class="text-xs text-erp-mute md:col-span-4">For a discount or rebate from the vendor without goods coming back. To send goods back use Return Goods.</p>
                </div>
            @elseif ($action === 'debit')
                <div class="border-b border-erp-line bg-slate-50 p-5">
                    <p class="text-sm text-erp-mute">Enter the quantity going back to the vendor. Stock is taken from this bill's lot first, then from older stock of the same item in this branch.</p>
                    <table class="mt-3 min-w-full text-sm">
                        <thead><tr class="text-left {{ $labelClass }}"><th class="py-1 pr-3">Item</th><th class="py-1 pr-3">Billed</th><th class="py-1 pr-3">Already returned</th><th class="w-32 py-1">Return now</th></tr></thead>
                        <tbody>
                            @foreach ($viewing->lines as $line)
                                <tr wire:key="dn-{{ $line->id }}">
                                    <td class="py-1 pr-3">{{ $productNames[$line->product_id] ?? '' }}</td>
                                    <td class="py-1 pr-3 tabular-nums">{{ $qty($line->qty) }}</td>
                                    <td class="py-1 pr-3 tabular-nums">{{ $qty($returned[$line->id] ?? 0) }}</td>
                                    <td class="py-1"><input type="number" step="any" min="0" wire:model="returnQty.{{ $line->id }}" class="{{ $cellInput }}"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3 grid gap-4 md:grid-cols-4">
                        <label class="block">
                            <span class="{{ $labelClass }}">Date</span>
                            <input type="date" wire:model="noteDate" class="{{ $inputClass }}">
                        </label>
                        <label class="block md:col-span-2">
                            <span class="{{ $labelClass }}">Reason</span>
                            <input type="text" wire:model="noteNarration" class="{{ $inputClass }}" placeholder="e.g. damaged, wrong item">
                        </label>
                        <div class="flex items-end gap-2">
                            <button type="button" wire:click="closeAction" class="{{ $btn }}">Close</button>
                            <button type="button" wire:click="saveDebitNote" wire:loading.attr="disabled" wire:confirm="Return these items to the vendor?" class="{{ $btnPrimary }}">Post Debit Note</button>
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto p-5">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-erp-line text-left {{ $labelClass }}">
                            <th class="py-2 pr-3">Item</th>
                            <th class="py-2 pr-3 text-right">Qty</th>
                            <th class="py-2 pr-3 text-right">Unit Price</th>
                            <th class="py-2 pr-3 text-right">Discount</th>
                            <th class="py-2 pr-3 text-right">Tax</th>
                            <th class="py-2 pr-3 text-right">Amount</th>
                            @if ($isBill)<th class="py-2 text-right">Cost / unit</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($viewing->lines as $line)
                            <tr class="border-b border-erp-line">
                                <td class="py-2 pr-3">
                                    {{ $line->product_id ? ($productNames[$line->product_id] ?? '#' . $line->product_id) : $line->description }}
                                    @if ($line->batch_no || $line->expiry_date)
                                        <span class="block text-xs text-erp-mute">@if ($line->batch_no)Batch {{ $line->batch_no }}@endif @if ($line->expiry_date)· Exp {{ \Illuminate\Support\Carbon::parse($line->expiry_date)->format('d M Y') }}@endif</span>
                                    @endif
                                </td>
                                <td class="py-2 pr-3 text-right tabular-nums">{{ $qty($line->qty) }}</td>
                                <td class="py-2 pr-3 text-right tabular-nums">{{ $money($line->unit_price) }}</td>
                                <td class="py-2 pr-3 text-right tabular-nums">{{ $money($line->discount) }}</td>
                                <td class="py-2 pr-3 text-right tabular-nums">{{ $money($line->tax_amount) }}</td>
                                <td class="py-2 pr-3 text-right tabular-nums">{{ $money((float) $line->amount + (float) $line->tax_amount) }}</td>
                                @if ($isBill)<td class="py-2 text-right tabular-nums text-erp-mute">{{ $money($line->landed_cost) }}</td>@endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <dl class="ml-auto mt-4 grid max-w-xs grid-cols-2 gap-x-4 gap-y-1 text-sm">
                    <dt class="text-erp-mute">Items</dt><dd class="text-right tabular-nums">{{ $money($viewing->subtotal) }}</dd>
                    <dt class="text-erp-mute">Discount</dt><dd class="text-right tabular-nums">− {{ $money($viewing->discount) }}</dd>
                    <dt class="text-erp-mute">Tax</dt><dd class="text-right tabular-nums">{{ $money($viewing->tax_amount) }}</dd>
                    @if ((float) $viewing->other_charges)<dt class="text-erp-mute">Other charges</dt><dd class="text-right tabular-nums">{{ $money($viewing->other_charges) }}</dd>@endif
                    <dt class="font-bold text-erp-ink">Total</dt><dd class="text-right font-bold tabular-nums text-erp-ink">{{ $money($viewing->total) }}</dd>
                    @if ($payable && $viewing->status === 'posted')
                        <dt class="text-erp-mute">Paid / credited</dt><dd class="text-right tabular-nums">{{ $money($viewing->paid) }}</dd>
                        <dt class="font-bold text-rose-700">Open</dt><dd class="text-right font-bold tabular-nums text-rose-700">{{ $money($viewing->open_amount) }}</dd>
                    @endif
                </dl>
                @if ($viewing->narration)<p class="mt-3 text-sm text-erp-mute">{{ $viewing->narration }}</p>@endif
            </div>

            @if ($payable && ($viewing->notes->count() || $payments->count()))
                <div class="grid gap-6 border-t border-erp-line p-5 md:grid-cols-2">
                    <div>
                        <h3 class="text-sm font-bold text-erp-ink">Payments</h3>
                        @forelse ($payments as $payment)
                            <div class="mt-2 flex justify-between text-sm">
                                <span>{{ \Illuminate\Support\Carbon::parse($payment->created_at)->format('d M Y') }} · {{ (int) $payment->bankid ? 'Bank ' . $payment->cheque : 'Cash' }}</span>
                                <span class="tabular-nums">{{ $money($payment->payment) }}</span>
                            </div>
                        @empty
                            <p class="mt-2 text-sm text-erp-mute">None yet.</p>
                        @endforelse
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-erp-ink">Debit / Credit Notes</h3>
                        @forelse ($viewing->notes as $note)
                            <div class="mt-2 flex justify-between text-sm">
                                <button type="button" wire:click="show({{ $note->id }})" class="font-semibold text-erp hover:underline">{{ $note->doc_no }} · {{ $typeNames[$note->type] }}</button>
                                <span class="tabular-nums">{{ $money($note->total) }}</span>
                            </div>
                        @empty
                            <p class="mt-2 text-sm text-erp-mute">None.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

    @else
        {{-- ===================== List ===================== --}}
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Vendor Bills</h2>
                    <p class="mt-0.5 text-sm text-erp-mute">Record purchases from vendors without a purchase order: stock, vendor balance, returns and payments in one place.</p>
                </div>
                <button type="button" wire:click="create" class="{{ $btnPrimary }}">+ New Bill</button>
            </div>
            <div class="grid gap-3 border-b border-erp-line p-5 md:grid-cols-4">
                <input type="text" wire:model.live.debounce.400ms="search" class="{{ $cellInput }}" placeholder="Search doc no, bill no, vendor">
                <select wire:model.live="vendorFilter" class="{{ $cellInput }}">
                    <option value="">All vendors</option>
                    @foreach ($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->vendor_name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="typeFilter" class="{{ $cellInput }}">
                    <option value="">Bills and notes</option>
                    @foreach ($typeNames as $key => $name)
                        <option value="{{ $key }}">{{ $name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="statusFilter" class="{{ $cellInput }}">
                    <option value="">Any status</option>
                    <option value="draft">Draft</option>
                    <option value="posted">Posted</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-erp-line text-left {{ $labelClass }}">
                            <th class="px-5 py-3">Doc No</th>
                            <th class="px-3 py-3">Date</th>
                            <th class="px-3 py-3">Vendor</th>
                            <th class="px-3 py-3">Bill No</th>
                            <th class="px-3 py-3">Branch</th>
                            <th class="px-3 py-3 text-right">Total</th>
                            <th class="px-3 py-3 text-right">Open</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bills as $bill)
                            <tr class="cursor-pointer border-b border-erp-line hover:bg-slate-50" wire:click="show({{ $bill->id }})" wire:key="vb-{{ $bill->id }}">
                                <td class="px-5 py-3 font-semibold text-erp">{{ $bill->doc_no }} @if ($bill->type !== 'bill')<span class="ml-1 text-xs text-erp-mute">{{ $typeNames[$bill->type] }}</span>@endif</td>
                                <td class="px-3 py-3">{{ $bill->bill_date->format('d M Y') }}</td>
                                <td class="px-3 py-3">{{ $bill->vendor_name }}</td>
                                <td class="px-3 py-3">{{ $bill->vendor_ref }}</td>
                                <td class="px-3 py-3">{{ $branchNames[$bill->branch_id] ?? '' }}</td>
                                <td class="px-3 py-3 text-right tabular-nums">{{ in_array($bill->type, \App\Models\VendorBill::PAYABLE, true) ? '' : '− ' }}{{ $money($bill->total) }}</td>
                                <td class="px-3 py-3 text-right tabular-nums {{ $bill->open_amount >= 1 ? 'font-semibold text-rose-700' : 'text-erp-mute' }}">{{ in_array($bill->type, \App\Models\VendorBill::PAYABLE, true) && $bill->status === 'posted' ? $money($bill->open_amount) : '' }}</td>
                                <td class="px-5 py-3"><span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $statusColors[$bill->status] ?? '' }}">{{ ucfirst($bill->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-5 py-10 text-center text-sm text-erp-mute">No vendor bills yet. Use <b>+ New Bill</b> when goods arrive with the vendor's invoice.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3">{{ $bills->links() }}</div>
        </div>
    @endif
</div>
