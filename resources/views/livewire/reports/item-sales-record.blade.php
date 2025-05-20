<div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Filters</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label small">From Date</label>
                    <input type="date" class="form-control" wire:model.lazy="dateFrom" placeholder="From Date">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">To Date</label>
                    <input type="date" class="form-control" wire:model.lazy="dateTo" placeholder="To Date">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Branch</label>
                    <select id="branch" class="form-select" wire:model.live="branch">
                        <option value="">-- Select Branch --</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Terminal</label>
                    <select class="form-select" wire:model.live="terminal">
                        <option value="all">-- All Terminals --</option>
                        @foreach ($terminals as $terminal)
                            <option value="{{ $terminal->terminal_id }}">{{ $terminal->terminal_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Type</label>
                    <select class="form-select" wire:model.live="type">
                        <option value="declaration" selected>Declaration</option>
                        <option value="datewise">Datewise</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Departments</label>
                    <select class="form-select select2-department" wire:model.live="department" >
                        <option value="all">-- All Departments --</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->department_id }}">{{ $dept->department_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Sub-Departments</label>
                    <select class="form-select select2-subdepartment" wire:model.live="subDepartment">
                        <option value="">-- Select Sub Department --</option>
                        @foreach ($subDepartments as $subDept)
                            <option value="{{ $subDept->sub_department_id }}">{{ $subDept->sub_depart_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Products</label>
                    <select class="form-select select2-product" wire:model.live="product">
                        <option value="">-- Select Product --</option>
                        @foreach ($products as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->product_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select class="form-select" wire:model.live="status">
                        <option value="all" selected>-- All Status --</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->order_status_id }}">{{ $status->order_status_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class=" card-footer">
            <div class="d-flex gap-2 justify-content-end">
                <button class="btn btn-primary px-4" type="button" wire:click="generateReport"
                    @if ($isGenerating) disabled @endif>
                    @if ($isGenerating)
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Generating Report...
                    @else
                        Generate Report
                    @endif
                </button>
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-end">
            <div class="d-flex gap-2">
                <button class="btn btn-danger px-4" type="button" wire:click="exportToPdf" {{empty($results) ? 'disabled' : ''}}
                    @if ($isGenerating) disabled @endif>
                    @if ($isGenerating)
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Exporting...
                    @else
                        Export to PDF
                    @endif
                </button>
                <button class="btn btn-success px-4" type="button" wire:click="exportToExcel" {{empty($results) ? 'disabled' : ''}}
                    @if ($isGenerating) disabled @endif>
                    @if ($isGenerating)
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Exporting...
                    @else
                        Export to Excel
                    @endif
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Item code</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Amount</th>
                            <th>COGS</th>
                            <th>Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $row)
                            <tr>
                                <td>{{ $row->code }}</td>
                                <td>{{ $row->product_name }}</td>
                                <td>{{ $row->qty }}</td>
                                <td>{{ $row->price }}</td>
                                <td>{{ $row->amount }}</td>
                                <td>{{ $row->cogs }}</td>
                                <td>{{ $row->margin }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', function () {
                initializeSelect2();
            });

            document.addEventListener('initializeSelect2', function () {
                initializeSelect2();
            });

            function initializeSelect2() {
                $('.select2-department').select2({
                    placeholder: 'Select Departments',
                    allowClear: true,
                    width: '100%',
                    multiple: true
                }).on('change', function () {
                    let values = $(this).val();
                    // If "all" is selected, clear other selections
                    if (values && values.includes('all')) {
                        $(this).val(['all']).trigger('change');
                        values = ['all'];
                    }
                    @this.set('department', values);
                });

                $('.select2-subdepartment').select2({
                    placeholder: 'Select Sub Department',
                    allowClear: true,
                    width: '100%'
                }).on('change', function () {
                    @this.set('subDepartment', $(this).val());
                });

                $('.select2-product').select2({
                    placeholder: 'Select Product',
                    allowClear: true,
                    width: '100%'
                }).on('change', function () {
                    @this.set('product', $(this).val());
                });
            }

            // Clean up Select2 when component is removed
            document.addEventListener('livewire:unload', function () {
                $('.select2-department, .select2-subdepartment, .select2-product').select2('destroy');
            });
        </script>
    @endpush
</div>
