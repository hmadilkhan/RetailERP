<div>
    <form wire:submit.prevent="save" class="space-y-6">
        @if ($errors->any())
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800">
                <div class="font-bold">Please fix the following issues:</div>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">{{ $isEdit ? 'Edit' : 'Create' }} Billing Configuration</h2>
                <p class="mt-1 text-sm text-erp-mute">Company level setup with optional branch or terminal billing rates.</p>
            </div>
            <div class="grid gap-5 p-5 md:grid-cols-2 xl:grid-cols-4">
                <label class="block xl:col-span-2">
                    <span class="text-sm font-bold text-erp-ink">Company <span class="text-rose-600">*</span></span>
                    <select wire:model.live="company_id" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Company</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->company_id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id') <span class="mt-1 block text-xs font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Invoice Type</span>
                    <select wire:model="invoice_type" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="branch">By Branch</option>
                        <option value="terminal">By Terminal</option>
                    </select>
                    @error('invoice_type') <span class="mt-1 block text-xs font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Monthly Charges</span>
                    <input type="number" step="0.01" min="0" wire:model="monthly_charges_amount" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    @error('monthly_charges_amount') <span class="mt-1 block text-xs font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Billing Cycle Day</span>
                    <input type="number" min="1" max="28" wire:model="billing_cycle_day" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    @error('billing_cycle_day') <span class="mt-1 block text-xs font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Payment Due Days</span>
                    <input type="number" min="1" max="90" wire:model="payment_due_days" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    @error('payment_due_days') <span class="mt-1 block text-xs font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Invoice Prefix</span>
                    <input type="text" maxlength="30" wire:model="invoice_prefix" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    @error('invoice_prefix') <span class="mt-1 block text-xs font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>
                <label class="flex items-center gap-3 rounded-lg border border-erp-line bg-slate-50 px-4 py-3">
                    <input type="checkbox" wire:model="is_auto_invoice" class="rounded border-erp-line text-erp focus:ring-erp">
                    <span>
                        <span class="block text-sm font-bold text-erp-ink">Auto Invoice Generation</span>
                        <span class="block text-xs text-erp-mute">Generate invoices automatically.</span>
                    </span>
                </label>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Billing Rates</h2>
                    <p class="mt-1 text-sm text-erp-mute">Set company, branch, or terminal rate rows with effective dates.</p>
                </div>
                <button type="button" wire:click="addBillingRate" class="inline-flex h-10 items-center justify-center rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Add Rate</button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Scope Type</th>
                            <th class="px-5 py-3 text-left font-bold">Scope</th>
                            <th class="px-5 py-3 text-left font-bold">Rate</th>
                            <th class="px-5 py-3 text-left font-bold">Effective From</th>
                            <th class="px-5 py-3 text-left font-bold">Effective To</th>
                            <th class="px-5 py-3 text-left font-bold">Active</th>
                            <th class="px-5 py-3 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($billing_rates as $index => $rate)
                            <tr wire:key="billing-rate-{{ $index }}" class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <select wire:model.live="billing_rates.{{ $index }}.scope_type" class="h-10 min-w-36 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                        <option value="company">Company</option>
                                        <option value="branch">Branch</option>
                                        <option value="terminal">Terminal</option>
                                    </select>
                                </td>
                                <td class="px-5 py-4">
                                    @if($rate['scope_type'] == 'branch')
                                        <select wire:model="billing_rates.{{ $index }}.scope_id" class="h-10 min-w-56 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                            <option value="">Select Branch</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($rate['scope_type'] == 'terminal')
                                        <select wire:model="billing_rates.{{ $index }}.scope_id" class="h-10 min-w-56 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                            <option value="">Select Terminal</option>
                                            @foreach($terminals as $terminal)
                                                <option value="{{ $terminal->terminal_id }}">{{ $terminal->terminal_id }} | {{ $terminal->terminal_name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">Company level</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4"><input type="number" step="0.01" wire:model="billing_rates.{{ $index }}.rate" class="h-10 w-32 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></td>
                                <td class="px-5 py-4"><input type="date" wire:model="billing_rates.{{ $index }}.effective_from" class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></td>
                                <td class="px-5 py-4"><input type="date" wire:model="billing_rates.{{ $index }}.effective_to" class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></td>
                                <td class="px-5 py-4"><input type="checkbox" wire:model="billing_rates.{{ $index }}.is_active" class="rounded border-erp-line text-erp focus:ring-erp"></td>
                                <td class="px-5 py-4 text-right">
                                    <button type="button" wire:click="removeBillingRate({{ $index }})" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Remove</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-erp-mute">No billing rates added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('invoice-setup.index') }}" class="inline-flex h-11 items-center rounded-lg border border-erp-line px-5 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Cancel</a>
            <button type="submit" class="inline-flex h-11 items-center rounded-lg bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">{{ $isEdit ? 'Update' : 'Create' }} Invoice Setup</button>
        </div>
    </form>
</div>
