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
</div>
</div>
