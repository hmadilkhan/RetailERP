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
                        <option value="">-- Select Type --</option>
                        <option value="declaration">Declaration</option>
                        <option value="datewise">Datewise</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Customer</label>
                    <select id="customerId" class="form-select" wire:ignore.self>
                        <option value="">-- Select Customer --</option>
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

    <!-- Widgets -->
    <div class="row mb-3">
        <div class="col">
            <div class="card text-white bg-success">
                <div class="card-body">
                    Total Sales: {{ collect($results)->sum('total') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-end">
            
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
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Items</th>
                            <th>Qty</th>
                            <th>Base</th>
                            <th>Tax</th>
                            <th>Discount</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ date('d M Y', strtotime($row->date)) }}</td>
                                <td>{{ $row->customer->name }}</td>
                                <td>{{ $row->mode->order_mode }}</td>
                                <td>{{ $row->orderdetails[0]->total_items ?? 0 }}</td>
                                <td>{{ $row->orderdetails[0]->total_qty ?? 0 }}</td>
                                <td>{{ number_format($row->actual_amount) }}</td>
                                <td>{{ number_format($row->orderAccount->sales_tax_amount) ?? 0 }}</td>
                                <td>{{ number_format($row->orderAccount->discount_amount) ?? 0 }}</td>
                                <td>{{ number_format($row->total_amount, 0) ?? 0 }}</td>
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

    <script>
        document.addEventListener('livewire:initialized', () => {
            initializeSelect2();
        });

        // Define the initialization function in the global scope
        function initializeSelect2() {
            // Check if Select2 is already initialized
            if ($('#customerId').hasClass('select2-hidden-accessible')) {
                return; // Exit if already initialized
            }
            let branch = $('#branch').val();
            if (branch == '') {
                branch = 'all';
            }
            // Initialize Select2
            $('#customerId').select2({
                ajax: {
                    url: "{{ route('search-customer-by-names') }}",
                    type: 'GET',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            branch: branch,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        if (data === 0) {
                            return {
                                results: []
                            };
                        }

                        if (data && data.items) {
                            return {
                                results: $.map(data.items, function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name + " | " + item.branch_name,
                                        description: item.mobile || 'No description available'
                                    };
                                })
                            };
                        }

                        return {
                            results: []
                        };
                    },
                    error: function(xhr, status, error) {
                        // Handle error silently
                    },
                    cache: true
                },
                placeholder: 'Type to search customers...',
                minimumInputLength: 1,
                width: '100%',
                templateResult: formatProduct,
                templateSelection: formatProductSelection
            });

            // Format the product display in dropdown
            function formatProduct(product) {
                if (!product.id) return product.text;
                return $('<span><strong>' + product.text + '</strong><br><small class="text-muted">' +
                    (product.description || 'No description available') + '</small></span>');
            }

            // Format the selected product
            function formatProductSelection(product) {
                if (!product.id) return product.text;
                return product.text;
            }

            // Handle selection
            $('#customerId').on('select2:select', function(e) {
                let selectedData = e.params.data;
                // Add to inventory
                @this.addInventory(selectedData.id);

                // Clear selection after a short delay
                setTimeout(() => {
                    $(this).val('').trigger('change');
                }, 100);
            });
        }

        // Make the function globally available
        window.initializeSelect2 = initializeSelect2;

        // Listen for initialization event from parent
        Livewire.on('initializeSelect2', () => {
            initializeSelect2();
        });

        // Handle Livewire updates
        Livewire.hook('morph.updating', () => {
            if ($('#customerId').hasClass('select2-hidden-accessible')) {
                $('#customerId').select2('destroy');
            }
        });

        Livewire.hook('morph.updated', () => {
            // Only reinitialize if the element exists and is visible
            if ($('#customerId').length && $('#customerId').is(':visible')) {
                initializeSelect2();
            }
        });
    </script>
</div>
