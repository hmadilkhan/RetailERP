<div class="py-4">
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
                    <input type="date" class="form-control" wire:model.live="fromDate">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">To Date</label>
                    <input type="date" class="form-control" wire:model.live="toDate">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Branch</label>
                    <select class="form-select" wire:model.live="branch">
                        <option value="">-- Select Branch --</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Terminal</label>
                    <select class="form-select" wire:model.live="terminal">
                        <option value="">-- Select Terminal --</option>
                        @foreach ($terminals as $terminal)
                            <option value="{{ $terminal->terminal_id }}">{{ $terminal->terminal_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Customer</label>
                    <select class="form-select" wire:model.live="customer">
                        <option value="">-- Select Customer --</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">User</label>
                    <select class="form-select" wire:model.live="user">
                        <option value="">-- Select User --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->fullname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select class="form-select" wire:model.live="status">
                        <option value="">-- Select Status --</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->order_status_id }}">{{ $status->order_status_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Order Type</label>
                    <select class="form-select" wire:model.live="orderType">
                        <option value="">-- Select Order Type --</option>
                        @foreach ($orderTypes as $orderType)
                            <option value="{{ $orderType->order_mode_id }}">{{ $orderType->order_mode }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Payment Method</label>
                    <select class="form-select" wire:model.live="paymentMethod">
                        <option value="">-- Select Payment Method --</option>
                        @foreach ($paymentMethods as $paymentMethod)
                            <option value="{{ $paymentMethod->payment_id }}">{{ $paymentMethod->payment_mode }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <!-- Filters -->
    {{-- <div class="card mb-4">
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
    </div> --}}

    <!-- Group By -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Group By Fields</h6>
            <div class="ms-auto">

            </div>
        </div>
        <div class="card-body">
            <div class="mb-4">
                {{-- <h5 class="mb-2 fw-semibold">Group By Fields</h5> --}}
                <select class="form-select " wire:model="groupByFields" id="groupByFields">
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
        </div>
    </div>

    <!-- Calculated Fields -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Custom Calculated Fields</h6>
            <div class="ms-auto">
                <button class="btn btn-sm btn-primary" type="button" wire:click="addCalculatedField">
                    <i class="bi bi-plus-lg me-1"></i>+ Add Calculation
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-4 mt-4">
                {{-- <h5 class="fw-semibold">Custom Calculated Fields</h5> --}}
                {{-- <button type="button" class="btn btn-success btn-sm mb-2" wire:click="addCalculatedField">
                    + Add Calculation
                </button> --}}
                @foreach ($calculatedFields as $index => $calc)
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="New Field Name"
                                wire:model="calculatedFields.{{ $index }}.name">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control"
                                placeholder="Formula (e.g., orders.amount * 1.18)"
                                wire:model="calculatedFields.{{ $index }}.formula">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Generate Button -->
    <div class="mb-4 card-footer">
        <button class="btn btn-primary px-4 f-right" type="button" wire:click="generateReport"
            @if ($isGenerating) disabled @endif>
            @if ($isGenerating)
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Generating Report...
            @else
                Generate Report
            @endif
        </button>
    </div>

    <!-- Error Message -->
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div wire:loading wire:target="generateReport" >
        <div class=" text-center position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(255, 255, 255, 0.8); z-index: 9999;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Generating report, please wait...</p>
        </div>
    </div>
    <!-- Results --> 
    {{-- @if ($isGenerating)
        <div class="table-responsive">
            <div class="d-flex align-items-center gap-2 justify-content-between">
                <h3 class="fw-semibold mb-3">Report Results</h3>
                <button class="btn btn-sm btn-success btn-gradient mb-3" disabled>
                    <i class="bi bi-plus me-1"></i> Export to Excel
                </button>
            </div>
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Generating report, please wait...</p>
            </div>
        </div>
    @else --}}
    @if (!empty($reportResults))
        <div wire:loading.remove wire:target="generateReport" class="table-responsive">
            <div class="d-flex align-items-center gap-2 justify-content-between">
                <h3 class="fw-semibold mb-3">Report Results</h3>
                <button class="btn btn-sm btn-success btn-gradient mb-3" wire:click="exportToExcel">
                    <i class="bi bi-plus me-1"></i> Export to Excel
                </button>
            </div>
            <table class="table table-bordered border-dark">
                <thead>
                    <tr>
                        @foreach($selectedFields as $field)
                            @php
                                $label = '';
                                foreach ($availableTables as $table => $fields) {
                                    foreach ($fields as $fieldInfo) {
                                        if ($fieldInfo['value'] === $field) {
                                            $label = $fieldInfo['label'];
                                            break 2;
                                        }
                                    }
                                }
                            @endphp
                            @if(!str_contains($field, 'sales_receipt_details.') && !str_contains($field, 'inventory_general.'))
                            <th class="{{$showOrderDetails ? 'bg-dark bg-gradient text-white border-dark' : ''}}">{{ $label }}</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportResults as $order)
                        <tr>
                            @foreach($selectedFields as $field)
                                @php
                                    $fieldName = explode('.', $field)[1] ?? $field;
                                @endphp
                                @if(!str_contains($field, 'sales_receipt_details.') && !str_contains($field, 'inventory_general.'))
                                    <td class="{{$showOrderDetails ? 'bg-info-subtle bg-gradient' : ''}}">{{ $order->$fieldName ?? '' }}</td>
                                @endif
                            @endforeach
                        </tr>
                        @if($showOrderDetails && !empty($order->details) &&  $order->details->isNotEmpty())
                            <tr class="order-details-row">
                                <td colspan="{{ count(array_filter($selectedFields, function($field) { 
                                    return !str_contains($field, 'sales_receipt_details.') && !str_contains($field, 'inventory_general.'); 
                                })) }}">
                                    <div class="order-details-container">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    @foreach($selectedFields as $field)
                                                        @if(str_contains($field, 'sales_receipt_details.') || str_contains($field, 'inventory_general.'))
                                                            @php
                                                                $label = '';
                                                                foreach ($availableTables as $table => $fields) {
                                                                    foreach ($fields as $fieldInfo) {
                                                                        if ($fieldInfo['value'] === $field) {
                                                                            $label = $fieldInfo['label'];
                                                                            break 2;
                                                                        }
                                                                    }
                                                                }
                                                            @endphp
                                                            <th class="{{$showOrderDetails ? 'bg-success bg-gradient' : ''}}">{{ $label }}</th>
                                                        @endif
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->details as $detail)
                                                    <tr>
                                                        @foreach($selectedFields as $field)
                                                            @if(str_contains($field, 'sales_receipt_details.') || str_contains($field, 'inventory_general.'))
                                                                @php
                                                                    $fieldName = explode('.', $field)[1] ?? $field;
                                                                @endphp
                                                                <td>{{ $detail->$fieldName ?? '' }}</td>
                                                            @endif
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Controls -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="d-flex align-items-center">
                    <label class="me-2">Items per page:</label>
                    <select class="form-select form-select-sm" style="width: 70px" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-outline-primary me-2" wire:click="previousPage"
                        @if ($currentPage === 1) disabled @endif>
                        Previous
                    </button>

                    <span class="mx-2">
                        Page {{ $currentPage }} of {{ $lastPage }}
                        (Total: {{ $totalResults }} records)
                    </span>

                    <button class="btn btn-sm btn-outline-primary ms-2" wire:click="nextPage"
                        @if ($currentPage === $lastPage) disabled @endif>
                        Next
                    </button>
                </div>

                <div class="d-flex align-items-center">
                    <span class="me-2">Go to page:</span>
                    <input type="number" class="form-control form-control-sm" style="width: 70px"
                        wire:model.live="currentPage" min="1" max="{{ $lastPage }}">
                </div>
            </div>
        </div>
    @endif

    <style>
        .order-details-row {
            background-color: #f8f9fa;
        }
        .order-details-container {
            padding: 10px;
        }
        .order-details-container table {
            margin-bottom: 0;
        }
    </style>

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
                select2Instance.on('change', function(e) {
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
</div>