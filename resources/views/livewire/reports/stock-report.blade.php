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
                    <label class="form-label small">Departments</label>
                    <select class="form-select select2-department" wire:model.live="department">
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
                    <label class="form-label small">Report Type</label>
                    <select class="form-select select2-product" wire:model.live="type">
                        <option selected value="productwise">Product Wise</option>
                        <option value="consolidated">Consolidated</option>
                    </select>
                </div>


            </div>
        </div>
        <div class=" card-footer">
            <div class="d-flex gap-2 justify-content-end">
                <button class="btn btn-secondary px-4" type="button" wire:click="resetFilters">
                    Reset
                </button>
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
                <button class="btn btn-danger px-4" type="button" wire:click="exportToPdf"
                    {{ empty($results) ? 'disabled' : '' }} @if ($isGenerating) disabled @endif>
                    @if ($isGenerating)
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Exporting...
                    @else
                        Export to PDF
                    @endif
                </button>
                <button class="btn btn-success px-4" type="button" wire:click="exportToExcel"
                    {{ empty($results) ? 'disabled' : '' }} @if ($isGenerating) disabled @endif>
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
            @if ($isGenerating)
                <div class="text-center mt-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            @else
                @if ($type == 'consolidated')
                    @include('livewire.reports.stock-report-consolidated')
                @else
                    @include('livewire.reports.stock-report-productwise')
                @endif
            @endif
            {{-- <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product Id</th>
                        <th>Product</th>
                        <th>Reference No</th>
                        <th>Transaction Type</th>
                        <th>Quantity</th>
                        <th>Stock Balance</th>
                        <th>User</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $stock = 0;
                    @endphp
                    @foreach ($results as $value)
                        @php
                            if ($value->narration == 'Stock Opening') {
                                $stock = (float) $value->stock;
                            } elseif ($value->narration == 'Sales') {
                                $stock =
                                    $stock -
                                    (preg_match('/Sales/', $value->narration)
                                        ? (float) $value->qty ?? (1 / $value->weight_qty ?? 1)
                                        : (float) $value->qty ?? 1);
                            } elseif ($value->narration == 'Sales Return') {
                                $stock = (float) $stock + (float) $value->qty;
                            } elseif ($value->narration == 'Stock Purchase through Purchase Order') {
                                $stock = (float) $stock + (float) $value->qty;
                            } elseif ($value->narration == 'Stock Opening from csv file') {
                                $stock = (float) $stock + (float) $value->qty;
                            } elseif ($value->narration == 'Stock Return') {
                                $stock = (float) $stock - (float) $value->qty;
                            } elseif (preg_match('/Stock Adjustment/', $value->narration)) {
                                $stock = (float) $stock + (float) $value->qty;
                            }
                        @endphp
                        <tr>
                            <td>{{ date('d M Y', strtotime($value->date)) }}</td>
                            <td>{{ $value->product_name }}</td>
                            <td>{{ $value->product_name }}</td>
                            <td>{{ $value->grn_id }}</td>
                            <td>{{ $value->narration }}</td>
                            <td>{{ preg_match('/Sales/', $value->narration) ? $value->qty ?? (1 / $value->weight_qty ?? 1) : $value->qty ?? 1 }}
                            </td>
                            <td>{{ number_format($stock, 2) }}</td>
                            <td>{{ $value->fullname }}</td>
                            <td>
                                @if (preg_match('/Purchase/', $value->narration) && $value->adjustment_mode == '')
                                    <a href="{{ route('view', $value->foreign_id) }}" class="p-r-10 f-18 text-info"
                                        data-toggle="tooltip" data-placement="top" title="View">
                                        <i class="icofont icofont-printer text-success"></i>
                                    </a>
                                @elseif(preg_match('/Sales Return/', $value->narration))
                                    <a href="{{ url('sales-return', $value->foreign_id) }}"
                                        class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top"
                                        title="View">
                                        <i class="icofont icofont-printer text-success"></i>
                                    </a>
                                @elseif(preg_match('/Sales/', $value->narration))
                                    <a href="{{ url('print', Custom_Helper::getReceiptID($value->foreign_id)) }}"
                                        class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top"
                                        title="View">
                                        <i class="icofont icofont-printer text-success"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table> --}}
        </div>
    </div>
</div>
