<div class=" py-4">
    <h2 class="mb-4 h4 fw-bold">Dynamic Report Builder</h2>

    <!-- Select Fields -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Select Fields</h6>
        </div>
        <div class="card-body">
            @foreach ($availableTables as $table => $fields)
                <div class="mb-2">
                    <strong class="d-block mb-1 text-capitalize">{{ $table }}</strong>
                    <div class="row">
                        @foreach ($fields as $field)
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="selectedFields"
                                        value="{{ $field['value'] }}" id="{{ $table . $field['value'] }}">
                                    <label class="form-check-label" for="{{ $table . $field['value'] }}">
                                        {{ $field['label'] }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- NEW Filters -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Filters</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label small">From Date</label>
                    <input type="date" class="form-control" wire:model="fromDate">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">To Date</label>
                    <input type="date" class="form-control" wire:model="toDate">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Branch</label>
                    <select class="form-select" wire:model="branch">
                        <option value="">-- Select Branch --</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Terminal</label>
                    <select class="form-select" wire:model="terminal">
                        <option value="">-- Select Terminal --</option>
                        @foreach ($terminals as $terminal)
                            <option value="{{ $terminal->terminal_id }}">{{ $terminal->terminal_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Customer</label>
                    <select class="form-select" wire:model="customer">
                        <option value="">-- Select Customer --</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">User</label>
                    <select class="form-select" wire:model="user">
                        <option value="">-- Select User --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->fullname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select class="form-select" wire:model="status">
                        <option value="">-- Select Status --</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->order_status_id }}">{{ $status->order_status_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Order Type</label>
                    <select class="form-select" wire:model="orderType">
                        <option value="">-- Select Order Type --</option>
                        @foreach ($orderTypes as $orderType)
                            <option value="{{ $orderType->order_mode_id }}">{{ $orderType->order_mode }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Payment Method</label>
                    <select class="form-select" wire:model="paymentMethod">
                        <option value="">-- Select Payment Method --</option>
                        @foreach ($paymentMethods as $paymentMethod)
                            <option value="{{ $paymentMethod->payment_id }}">{{ $paymentMethod->payment_mode }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Filters</h6>
            <div class="ms-auto">
                <button class="btn btn-sm btn-primary" type="button" wire:click="addFilter">
                    <i class="bi bi-plus-lg me-1"></i>Add Filter
                </button>
            </div>
        </div>
        <div class="card-body">
            @forelse ($filters as $index => $filter)
                <div class="border p-3 mb-3 rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Filter #{{ $index + 1 }}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            wire:click="removeFilter({{ $index }})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small">Field</label>
                            <select class="form-select" wire:model="filters.{{ $index }}.field">
                                <option value="">-- Select Field --</option>
                                @foreach ($availableFields as $field)
                                    <option value="{{ $field }}">{{ $field }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Operator</label>
                            <select class="form-select" wire:model="filters.{{ $index }}.operator">
                                <option value="=">=</option>
                                <option value="!=">!=</option>
                                <option value="<">&lt;</option>
                                <option value=">">&gt;</option>
                                <option value="<=">&lt;=</option>
                                <option value=">=">&gt;=</option>
                                <option value="like">like</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Value</label>
                            <input type="text" class="form-control" placeholder="Enter value"
                                wire:model="filters.{{ $index }}.value">
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-3">
                    <i class="bi bi-filter-circle display-6"></i>
                    <p class="mb-0 small">No filters added yet. Click <strong>Add Filter</strong> to begin.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Group By -->
    <div class="mb-4">
        <h5 class="mb-2 fw-semibold">Group By Fields</h5>
        <select  class="form-select " wire:model="groupByFields" id="groupByFields">
            <option value="">
                -- Select Field --
            </option>
            @foreach ($availableTables as $table => $fields)
                <optgroup label="{{ ucfirst(str_replace('_', ' ', $table)) }}">
                    @foreach ($fields as $field)
                        <option value="{{ $field['value'] }}">
                            {{ $field['label'] }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        <div class="form-text">Hold Ctrl/Cmd to select multiple fields</div>
    </div>

    <!-- Calculated Fields -->
    <div class="mb-4 mt-4">
        <h5 class="fw-semibold">Custom Calculated Fields</h5>
        <button type="button" class="btn btn-success btn-sm mb-2" wire:click="addCalculatedField">
            + Add Calculation
        </button>
        @foreach ($calculatedFields as $index => $calc)
            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="New Field Name"
                        wire:model="calculatedFields.{{ $index }}.name">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Formula (e.g., orders.amount * 1.18)"
                        wire:model="calculatedFields.{{ $index }}.formula">
                </div>
            </div>
        @endforeach
    </div>

    <!-- Generate Button -->
    <div class="mb-4">
        <button class="btn btn-primary px-4" type="button" wire:click="generateReport">
            Generate Report
        </button>
    </div>

    <!-- Results -->
    @if (!empty($reportResults))
        <div class="table-responsive">
            <h5 class="fw-semibold mb-3">Report Results</h5>
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        @foreach (array_keys((array) $reportResults[0]) as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportResults as $row)
                        <tr>
                            @foreach ((array) $row as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@push('scripts')
<script>
    let select2Instance = null;

    function initializeSelect2() {
        if (select2Instance) {
            select2Instance.off('change'); // Remove previous listeners
            select2Instance.select2('destroy');
        }

        select2Instance = $('#groupByFields').select2({
            placeholder: 'Select fields to group by',
            allowClear: true,
            width: '100%'
        });

        // Reapply selected values from Livewire
        select2Instance.val(@this.get('groupByFields')).trigger('change');

        // On change, update Livewire model
        select2Instance.on('change', function (e) {
            @this.set('groupByFields', $(this).val());
        });
    }

    // Initialize on load and Livewire update
    document.addEventListener('livewire:load', initializeSelect2);
    document.addEventListener('livewire:update', initializeSelect2);

    // Destroy Select2 cleanly if component is removed
    document.addEventListener('livewire:destroy', () => {
        if (select2Instance) {
            select2Instance.off('change');
            select2Instance.select2('destroy');
        }
    });
</script>
@endpush