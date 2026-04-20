@php
    $isEdit = strtoupper($method) !== 'POST';
    $inputClass = 'mt-2 block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm text-crm-ink placeholder:text-slate-400 focus:border-crm-blue focus:ring-crm-blue';
    $labelClass = 'block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute';
    $panelClass = 'rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm';
    $items = old('items', isset($quotation) && $quotation->items->isNotEmpty()
        ? $quotation->items->map(fn ($item) => [
            'item_name' => $item->item_name,
            'description' => $item->description,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
        ])->all()
        : [['item_name' => '', 'description' => '', 'quantity' => 1, 'unit_price' => 0]]);
@endphp

<form action="{{ $action }}" method="POST" class="grid gap-6 xl:grid-cols-12" x-data="quotationForm(@js($items))">
    @csrf
    @if ($isEdit)
        @method($method)
    @endif

    <div class="space-y-6 xl:col-span-8">
        @if ($errors->any())
            <div class="rounded-[28px] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800 shadow-crm-soft">
                <div class="font-semibold">Please review the quotation details below.</div>
                <ul class="mt-3 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="{{ $panelClass }}">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Quotation Details</h3>
                    <p class="mt-1 text-sm text-crm-mute">Commercial framing, validity, and proposal notes for this lead.</p>
                </div>
                <div class="rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                    {{ $quotation->quotation_no ?: 'Auto Number on Save' }}
                </div>
            </div>

            <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-6">
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Quotation Date *</label>
                    <input type="date" name="quotation_date" class="{{ $inputClass }}"
                        value="{{ old('quotation_date', optional($quotation->quotation_date)->format('Y-m-d') ?: $quotation->quotation_date) }}">
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Valid Until</label>
                    <input type="date" name="valid_until" class="{{ $inputClass }}"
                        value="{{ old('valid_until', optional($quotation->valid_until)->format('Y-m-d') ?: $quotation->valid_until) }}">
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Status *</label>
                    <select name="status" class="{{ $inputClass }}">
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $quotation->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="xl:col-span-6">
                    <label class="{{ $labelClass }}">Notes</label>
                    <textarea name="notes" rows="4" class="{{ $inputClass }}">{{ old('notes', $quotation->notes) }}</textarea>
                </div>
            </div>
        </section>

        <section class="{{ $panelClass }}">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Quotation Items</h3>
                    <p class="mt-1 text-sm text-crm-mute">Add the commercial line items proposed to this lead.</p>
                </div>
                <button type="button" @click="addItem()"
                    class="inline-flex items-center justify-center rounded-2xl border border-crm-line bg-white px-4 py-2 text-sm font-semibold text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                    Add Item
                </button>
            </div>

            <div class="mt-6 space-y-5">
                <template x-for="(item, index) in items" :key="index">
                    <div class="rounded-[28px] border border-slate-200 bg-slate-50/70 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="text-sm font-semibold text-crm-ink">Item <span x-text="index + 1"></span></div>
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                class="text-xs font-semibold uppercase tracking-[0.18em] text-rose-700">
                                Remove
                            </button>
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                            <div class="xl:col-span-2">
                                <label class="{{ $labelClass }}">Item Name *</label>
                                <input type="text" :name="`items[${index}][item_name]`" x-model="item.item_name" class="{{ $inputClass }}">
                            </div>
                            <div class="xl:col-span-2">
                                <label class="{{ $labelClass }}">Quantity *</label>
                                <input type="number" min="0.01" step="0.01" :name="`items[${index}][quantity]`" x-model.number="item.quantity" class="{{ $inputClass }}">
                            </div>
                            <div class="xl:col-span-2">
                                <label class="{{ $labelClass }}">Unit Price *</label>
                                <input type="number" min="0" step="0.01" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" class="{{ $inputClass }}">
                            </div>
                            <div class="xl:col-span-6">
                                <label class="{{ $labelClass }}">Description</label>
                                <textarea :name="`items[${index}][description]`" x-model="item.description" rows="3" class="{{ $inputClass }}"></textarea>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </section>
    </div>

    <div class="space-y-6 xl:col-span-4">
        <section class="{{ $panelClass }}">
            <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Lead Context</h3>
            <p class="mt-1 text-sm text-crm-mute">Quick commercial snapshot for the connected lead.</p>
            <div class="mt-6 grid gap-4">
                @foreach ([
                    'Lead' => $lead->contact_person_name,
                    'Company' => $lead->company_name ?: 'N/A',
                    'Lead Code' => $lead->lead_code,
                    'Expected Deal Value' => $lead->expected_deal_value ? number_format((float) $lead->expected_deal_value, 2) : '0.00',
                    'Current Status' => $lead->status->name ?? 'Unknown',
                ] as $label => $value)
                    <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-4">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">{{ $label }}</div>
                        <div class="mt-2 text-sm font-medium text-crm-text">{{ $value }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="{{ $panelClass }}">
            <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Totals Summary</h3>
            <p class="mt-1 text-sm text-crm-mute">A professional proposal total card with live calculations.</p>
            <div class="mt-6 space-y-4">
                <div>
                    <label class="{{ $labelClass }}">Discount</label>
                    <input type="number" min="0" step="0.01" name="discount" x-model.number="discount" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">Tax</label>
                    <input type="number" min="0" step="0.01" name="tax" x-model.number="tax" class="{{ $inputClass }}">
                </div>
                <div class="rounded-[28px] border border-blue-200 bg-blue-50 p-5">
                    <div class="flex items-center justify-between text-sm text-blue-900">
                        <span>Subtotal</span>
                        <span x-text="formatAmount(subtotal())"></span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-sm text-blue-900">
                        <span>Discount</span>
                        <span x-text="formatAmount(discount)"></span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-sm text-blue-900">
                        <span>Tax</span>
                        <span x-text="formatAmount(tax)"></span>
                    </div>
                    <div class="mt-4 border-t border-blue-200 pt-4">
                        <div class="flex items-center justify-between text-base font-semibold text-blue-950">
                            <span>Total</span>
                            <span x-text="formatAmount(total())"></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="{{ $panelClass }}">
            <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Save Action</h3>
            <p class="mt-1 text-sm text-crm-mute">Save this quotation inside the CRM commercial workflow.</p>
            <div class="mt-6 space-y-3">
                <button type="submit"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-crm-blue px-5 py-3 text-sm font-semibold text-white transition hover:bg-crm-deep">
                    {{ $submitLabel }}
                </button>
                <a href="{{ route('crm.leads.show', $lead) }}"
                    class="inline-flex w-full items-center justify-center rounded-2xl border border-crm-line bg-white px-5 py-3 text-sm font-semibold text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                    Back to Lead
                </a>
            </div>
        </section>
    </div>
</form>

@push('crm_scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function quotationForm(initialItems) {
            return {
                items: initialItems.length ? initialItems : [{
                    item_name: '',
                    description: '',
                    quantity: 1,
                    unit_price: 0
                }],
                discount: Number(@json((float) old('discount', $quotation->discount ?? 0))),
                tax: Number(@json((float) old('tax', $quotation->tax ?? 0))),
                addItem() {
                    this.items.push({
                        item_name: '',
                        description: '',
                        quantity: 1,
                        unit_price: 0
                    });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                subtotal() {
                    return this.items.reduce((sum, item) => {
                        return sum + ((Number(item.quantity) || 0) * (Number(item.unit_price) || 0));
                    }, 0);
                },
                total() {
                    return Math.max(0, this.subtotal() - (Number(this.discount) || 0) + (Number(this.tax) || 0));
                },
                formatAmount(amount) {
                    return Number(amount || 0).toFixed(2);
                }
            }
        }
    </script>
@endpush
