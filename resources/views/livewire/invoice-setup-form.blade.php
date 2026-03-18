<div>
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">{{ $isEdit ? 'Edit' : 'Create' }} Invoice Setup</h5>
            </div>
            <div class="card-block">
                <form wire:submit.prevent="save">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group @error('company_id') has-danger @enderror">
                                <label class="form-control-label">Company <span class="text-danger">*</span></label>
                                <select id="company_id" class="form-control select2">
                                    <option value="">Select Company</option>
                                    @foreach($companies as $company)
                                    <option value="{{ $company->company_id }}" {{ $company_id == $company->company_id ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id') <div class="form-control-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <hr />
                            <h6 class="m-t-10">Billing Rates</h6>
                            <p class="text-muted m-b-10">For new company, company-level rate is recommended. Branch/terminal rates can be refined after branches/terminals are created.</p>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Scope Type</th>
                                            <th>Scope</th>
                                            <!-- <th>Charge Type</th> -->
                                            <th>Rate</th>
                                            <th>Effective From</th>
                                            <th>Effective To</th>
                                            <th>Active</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($billing_rates as $index => $rate)
                                        <tr wire:key="billing-rate-{{ $index }}">
                                            <td>
                                                <select wire:model.live="billing_rates.{{ $index }}.scope_type" class="form-control">
                                                    <option value="company">Company</option>
                                                    <option value="branch">Branch</option>
                                                    <option value="terminal">Terminal</option>
                                                </select>
                                            </td>
                                            <td wire:key="scope-select-{{ $index }}">
                                                @if($rate['scope_type'] == 'branch')
                                                <select id="scope_id_{{ $index }}" class="form-control select2-scope">
                                                    <option value="">Select Branch</option>
                                                    @foreach($branches as $branch)
                                                    <option value="{{ $branch->branch_id }}" {{ isset($rate['scope_id']) && $rate['scope_id'] == $branch->branch_id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                                                    @endforeach
                                                </select>
                                                @elseif($rate['scope_type'] == 'terminal')
                                                <select id="scope_id_{{ $index }}" class="form-control select2-scope">
                                                    <option value="">Select Terminal</option>
                                                    @foreach($terminals as $terminal)
                                                    <option value="{{ $terminal->terminal_id }}" {{ isset($rate['scope_id']) && $rate['scope_id'] == $terminal->terminal_id ? 'selected' : '' }}>{{ $terminal->terminal_name }}</option>
                                                    @endforeach
                                                </select>
                                                @else
                                                <input type="text" class="form-control" value="N/A" readonly>
                                                @endif
                                            </td>
                                            <!-- <td>
                                                <input type="text" wire:model="billing_rates.{{ $index }}.charge_type" class="form-control">
                                            </td> -->
                                            <td>
                                                <input type="number" step="0.01" wire:model="billing_rates.{{ $index }}.rate" class="form-control">
                                            </td>
                                            <td>
                                                <input type="date" wire:model="billing_rates.{{ $index }}.effective_from" class="form-control">
                                            </td>
                                            <td>
                                                <input type="date" wire:model="billing_rates.{{ $index }}.effective_to" class="form-control">
                                            </td>
                                            <td>
                                                <input type="checkbox" wire:model="billing_rates.{{ $index }}.is_active">
                                            </td>
                                            <td>
                                                <button type="button" wire:click="removeBillingRate({{ $index }})" class="btn btn-sm btn-danger">Remove</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" wire:click="addBillingRate" class="btn btn-sm btn-info">Add Rate</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <hr />
                            <h6>Billing Configuration</h6>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group @error('invoice_type') has-danger @enderror">
                                <label class="form-control-label">Invoice Type</label>
                                <select wire:model="invoice_type" class="form-control">
                                    <option value="branch">By Branch</option>
                                    <option value="terminal">By Terminal</option>
                                </select>
                                @error('invoice_type') <div class="form-control-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group @error('monthly_charges_amount') has-danger @enderror">
                                <label class="form-control-label">Monthly Charges Amount</label>
                                <input class="form-control" type="number" step="0.01" min="0" wire:model="monthly_charges_amount" />
                                @error('monthly_charges_amount') <div class="form-control-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group @error('billing_cycle_day') has-danger @enderror">
                                <label class="form-control-label">Billing Cycle Day</label>
                                <input class="form-control" type="number" min="1" max="28" wire:model="billing_cycle_day" />
                                @error('billing_cycle_day') <div class="form-control-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group @error('payment_due_days') has-danger @enderror">
                                <label class="form-control-label">Payment Due Days</label>
                                <input class="form-control" type="number" min="1" max="90" wire:model="payment_due_days" />
                                @error('payment_due_days') <div class="form-control-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group @error('invoice_prefix') has-danger @enderror">
                                <label class="form-control-label">Invoice Prefix</label>
                                <input class="form-control" type="text" maxlength="30" wire:model="invoice_prefix" />
                                @error('invoice_prefix') <div class="form-control-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group m-t-30">
                                <label class="form-control-label d-block">Auto Invoice Generation</label>
                                <input type="checkbox" wire:model="is_auto_invoice"> Enable
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-md btn-primary waves-effect waves-light f-right">
                        {{ $isEdit ? 'Update' : 'Create' }} Invoice Setup
                    </button>
                    <a href="{{ route('invoice-setup.index') }}" class="btn btn-md btn-default waves-effect waves-light f-right m-r-10">Cancel</a>
                </form>
            </div>
        </div>
    </section>
</div>
@script
<script>
    $(document).ready(function() {
        function initSelect2() {
            // Initialize select2 only on elements that haven't been initialized yet.
            $(".select2:not(.select2-hidden-accessible)").select2();
            $(".select2-scope:not(.select2-hidden-accessible)").select2();
        }

        initSelect2();

        $(document).on('change', '#company_id', function(e) {
            // Use .val() to get the value, which is the correct method for modern select2.
            var data = $(this).val();
            $wire.set('company_id', data);
        });

        $(document).on('change', '.select2-scope', function(e) {
            var index = $(this).attr('id').replace('scope_id_', '');
            // Use .val() here as well.
            var data = $(this).val();
            $wire.set('billing_rates.' + index + '.scope_id', data);
        });

        // After Livewire updates the DOM, run initSelect2 to initialize any new dropdowns.
        Livewire.hook('morph.updated', () => {
            initSelect2();
        });

        // Clean up select2 before updating to prevent duplicate rendering issues
        Livewire.hook('morph.updating', () => {
            $('.select2-hidden-accessible').select2('destroy');
        });
    });
</script>
@endscript