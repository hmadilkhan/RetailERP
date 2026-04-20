@php
    $isEdit = strtoupper($method) !== 'POST';
    $inputClass = 'mt-2 block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm text-crm-ink placeholder:text-slate-400 focus:border-crm-blue focus:ring-crm-blue';
    $labelClass = 'block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute';
    $panelClass = 'rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm';
@endphp

<form action="{{ $action }}" method="POST" class="grid gap-6 xl:grid-cols-12">
    @csrf
    @if ($isEdit)
        @method($method)
    @endif

    <div class="space-y-6 xl:col-span-8">
        @if ($errors->any())
            <div class="rounded-[28px] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800 shadow-crm-soft">
                <div class="font-semibold">Please review the highlighted lead details.</div>
                <ul class="mt-3 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (isset($duplicateLeads) && $duplicateLeads->isNotEmpty())
            <div class="rounded-[28px] border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800 shadow-crm-soft">
                <div class="font-semibold">Potential duplicate leads detected</div>
                <div class="mt-3 space-y-2">
                    @foreach ($duplicateLeads as $duplicateLead)
                        <div>
                            <span class="font-semibold">{{ $duplicateLead->lead_code }}</span>
                            {{ $duplicateLead->contact_person_name }}
                            @if ($duplicateLead->company_name)
                                | {{ $duplicateLead->company_name }}
                            @endif
                            | {{ $duplicateLead->contact_number }}
                            @if ($duplicateLead->email)
                                | {{ $duplicateLead->email }}
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <section class="{{ $panelClass }}">
            <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Contact & Company</h3>
            <p class="mt-1 text-sm text-crm-mute">Capture the decision-maker, business identity, and main communication channels.</p>
            <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-6">
                <div class="xl:col-span-3">
                    <label class="{{ $labelClass }}">Contact Person Name *</label>
                    <input type="text" name="contact_person_name" class="{{ $inputClass }}" value="{{ old('contact_person_name', $lead->contact_person_name) }}">
                </div>
                <div class="xl:col-span-3">
                    <label class="{{ $labelClass }}">Company Name</label>
                    <input type="text" name="company_name" class="{{ $inputClass }}" value="{{ old('company_name', $lead->company_name) }}">
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Contact Number *</label>
                    <input type="text" name="contact_number" class="{{ $inputClass }}" value="{{ old('contact_number', $lead->contact_number) }}">
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Alternate Number</label>
                    <input type="text" name="alternate_number" class="{{ $inputClass }}" value="{{ old('alternate_number', $lead->alternate_number) }}">
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" class="{{ $inputClass }}" value="{{ old('whatsapp_number', $lead->whatsapp_number) }}">
                </div>
                <div class="xl:col-span-3">
                    <label class="{{ $labelClass }}">Email</label>
                    <input type="email" name="email" class="{{ $inputClass }}" value="{{ old('email', $lead->email) }}">
                </div>
                <div class="xl:col-span-3">
                    <label class="{{ $labelClass }}">Website</label>
                    <input type="text" name="website" class="{{ $inputClass }}" value="{{ old('website', $lead->website) }}">
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Country</label>
                    <input type="text" name="country" class="{{ $inputClass }}" value="{{ old('country', $lead->country) }}">
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">City</label>
                    <input type="text" name="city" class="{{ $inputClass }}" value="{{ old('city', $lead->city) }}">
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Preferred Contact Method</label>
                    <select name="preferred_contact_method" class="{{ $inputClass }}">
                        <option value="">Select method</option>
                        @foreach ($contactMethodOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('preferred_contact_method', $lead->preferred_contact_method) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="xl:col-span-6">
                    <label class="{{ $labelClass }}">Address</label>
                    <textarea name="address" rows="3" class="{{ $inputClass }}">{{ old('address', $lead->address) }}</textarea>
                </div>
            </div>
        </section>

        <section class="{{ $panelClass }}">
            <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Acquisition & Product Fit</h3>
            <p class="mt-1 text-sm text-crm-mute">Track source quality, product interest, and market context.</p>
            <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-6">
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Lead Source *</label>
                    <select name="lead_source_id" class="{{ $inputClass }}">
                        <option value="">Select source</option>
                        @foreach ($leadSources as $source)
                            <option value="{{ $source->id }}" @selected((string) old('lead_source_id', $lead->lead_source_id) === (string) $source->id)>{{ $source->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Campaign Name</label>
                    <input type="text" name="campaign_name" class="{{ $inputClass }}" value="{{ old('campaign_name', $lead->campaign_name) }}">
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Referral Person</label>
                    <input type="text" name="referral_person_name" class="{{ $inputClass }}" value="{{ old('referral_person_name', $lead->referral_person_name) }}">
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Product Type *</label>
                    <select name="product_type_id" id="crm-product-type" class="{{ $inputClass }}">
                        <option value="">Select product type</option>
                        @foreach ($productTypes as $type)
                            <option value="{{ $type->id }}" @selected((string) old('product_type_id', $lead->product_type_id) === (string) $type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Product</label>
                    <select name="product_id" id="crm-product-id" class="{{ $inputClass }}" data-selected="{{ old('product_id', $lead->product_id) }}">
                        <option value="">Select product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected((string) old('product_id', $lead->product_id) === (string) $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Inquiry Type</label>
                    <input type="text" name="inquiry_type" class="{{ $inputClass }}" value="{{ old('inquiry_type', $lead->inquiry_type) }}" placeholder="Demo, pricing, implementation">
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Business Type</label>
                    <input type="text" name="business_type" class="{{ $inputClass }}" value="{{ old('business_type', $lead->business_type) }}">
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Required Quantity</label>
                    <input type="number" min="0" name="required_quantity" class="{{ $inputClass }}" value="{{ old('required_quantity', $lead->required_quantity) }}">
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Branch Count</label>
                    <input type="number" min="0" name="branch_count" class="{{ $inputClass }}" value="{{ old('branch_count', $lead->branch_count) }}">
                </div>
                <div class="xl:col-span-3">
                    <label class="{{ $labelClass }}">Existing System</label>
                    <input type="text" name="existing_system" class="{{ $inputClass }}" value="{{ old('existing_system', $lead->existing_system) }}">
                </div>
                <div class="xl:col-span-3">
                    <label class="{{ $labelClass }}">Competitor Name</label>
                    <input type="text" name="competitor_name" class="{{ $inputClass }}" value="{{ old('competitor_name', $lead->competitor_name) }}">
                </div>
            </div>
        </section>

        <section class="{{ $panelClass }}">
            <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Requirement Summary</h3>
            <p class="mt-1 text-sm text-crm-mute">Define business needs, technical scope, and deal framing.</p>
            <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-6">
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Budget Range</label>
                    <input type="text" name="budget_range" class="{{ $inputClass }}" value="{{ old('budget_range', $lead->budget_range) }}">
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Expected Go Live Date</label>
                    <input type="date" name="expected_go_live_date" class="{{ $inputClass }}" value="{{ old('expected_go_live_date', optional($lead->expected_go_live_date)->format('Y-m-d')) }}">
                </div>
                <div class="xl:col-span-2">
                    <label class="{{ $labelClass }}">Expected Deal Value</label>
                    <input type="number" min="0" step="0.01" name="expected_deal_value" class="{{ $inputClass }}" value="{{ old('expected_deal_value', $lead->expected_deal_value) }}">
                </div>
                <div class="xl:col-span-6">
                    <label class="{{ $labelClass }}">Requirement Summary *</label>
                    <textarea name="requirement_summary" rows="6" class="{{ $inputClass }}">{{ old('requirement_summary', $lead->requirement_summary) }}</textarea>
                </div>
                <div class="xl:col-span-6">
                    <label class="{{ $labelClass }}">Lost Reason</label>
                    <textarea name="lost_reason" rows="4" class="{{ $inputClass }}" placeholder="Required only when status is Lost">{{ old('lost_reason', $lead->lost_reason) }}</textarea>
                </div>
            </div>
        </section>
    </div>

    <div class="space-y-6 xl:col-span-4">
        <section class="{{ $panelClass }}">
            <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Lead Control</h3>
            <p class="mt-1 text-sm text-crm-mute">Status, urgency, follow-up, and ownership settings.</p>
            <div class="mt-6 space-y-5">
                <div>
                    <label class="{{ $labelClass }}">Lead Code</label>
                    <input type="text" class="{{ $inputClass }}" value="{{ $suggestedLeadCode }}" disabled>
                    <p class="mt-2 text-sm text-crm-mute">Auto-generated when the lead is saved.</p>
                </div>
                <div>
                    <label class="{{ $labelClass }}">Lead Status *</label>
                    <select name="status_id" class="{{ $inputClass }}">
                        <option value="">Select status</option>
                        @foreach ($leadStatuses as $status)
                            <option value="{{ $status->id }}" @selected((string) old('status_id', $lead->status_id) === (string) $status->id)>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="{{ $labelClass }}">Priority</label>
                        <select name="priority" class="{{ $inputClass }}">
                            @foreach ($priorityOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('priority', $lead->priority) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Temperature</label>
                        <select name="temperature" class="{{ $inputClass }}">
                            @foreach ($temperatureOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('temperature', $lead->temperature) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if (!empty($canAssignLead))
                    <div>
                        <label class="{{ $labelClass }}">Assigned To</label>
                        <select name="assigned_to" class="{{ $inputClass }}">
                            <option value="">Unassigned</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected((string) old('assigned_to', $lead->assigned_to) === (string) $user->id)>{{ $user->fullname }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Assigned To</div>
                        <div class="mt-2 text-sm font-medium text-crm-text">{{ $lead->assignedUser->fullname ?? 'Assignment is managed by Admin or Sales Manager' }}</div>
                    </div>
                @endif
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="{{ $labelClass }}">Lead Score</label>
                        <input type="number" min="0" max="100" name="lead_score" class="{{ $inputClass }}" value="{{ old('lead_score', $lead->lead_score) }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Probability %</label>
                        <input type="number" min="0" max="100" name="probability_percent" class="{{ $inputClass }}" value="{{ old('probability_percent', $lead->probability_percent) }}">
                    </div>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="{{ $labelClass }}">Last Contact Date</label>
                        <input type="date" name="last_contact_date" class="{{ $inputClass }}" value="{{ old('last_contact_date', optional($lead->last_contact_date)->format('Y-m-d')) }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Next Follow-up Date</label>
                        <input type="date" name="next_followup_date" class="{{ $inputClass }}" value="{{ old('next_followup_date', optional($lead->next_followup_date)->format('Y-m-d')) }}">
                    </div>
                </div>
                <label class="flex items-center gap-3 rounded-2xl border border-crm-line bg-crm-soft px-4 py-3">
                    <input type="checkbox" id="is_converted" name="is_converted" value="1"
                        class="h-4 w-4 rounded border-slate-300 text-crm-blue focus:ring-crm-blue" @checked(old('is_converted', $lead->is_converted))>
                    <span class="text-sm font-medium text-crm-text">Mark as converted</span>
                </label>
            </div>
        </section>

        <section class="{{ $panelClass }}">
            <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Save Action</h3>
            <p class="mt-1 text-sm text-crm-mute">Commit the lead and keep the CRM trail clean.</p>
            <div class="mt-6 space-y-3">
                <button type="submit"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-crm-blue px-5 py-3 text-sm font-semibold text-white transition hover:bg-crm-deep">
                    {{ $submitLabel }}
                </button>
                <a href="{{ route('crm.leads.index') }}"
                    class="inline-flex w-full items-center justify-center rounded-2xl border border-crm-line bg-white px-5 py-3 text-sm font-semibold text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                    Cancel
                </a>
            </div>
        </section>
    </div>
</form>

@push('crm_scripts')
    <script>
        (function() {
            const productTypeSelect = document.getElementById('crm-product-type');
            const productSelect = document.getElementById('crm-product-id');
            const selectedProduct = productSelect ? String(productSelect.dataset.selected || '') : '';
            const products = @json($allProducts);

            if (!productTypeSelect || !productSelect) {
                return;
            }

            const renderProducts = function(typeId) {
                productSelect.innerHTML = '<option value="">Select product</option>';

                products.filter(function(product) {
                    return !typeId || String(product.product_type_id) === String(typeId);
                }).forEach(function(product) {
                    const option = document.createElement('option');
                    option.value = product.id;
                    option.textContent = product.name;

                    if (selectedProduct && String(product.id) === selectedProduct) {
                        option.selected = true;
                    }

                    productSelect.appendChild(option);
                });
            };

            renderProducts(productTypeSelect.value);

            productTypeSelect.addEventListener('change', function() {
                productSelect.dataset.selected = '';
                renderProducts(this.value);
            });
        })();
    </script>
@endpush
