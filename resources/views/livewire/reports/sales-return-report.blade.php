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
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Receipt</th>
                            <th>Product Name</th>
                            <th>Qty</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalCount = 0;
                            $totalQty = 0;
                            $totalAmount = 0;
                        @endphp
                        @forelse($results as $row)
                            @php
                                $totalCount++;
                                $totalQty += $row->qty ?? 0;
                                $totalAmount += $row->amount;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->order->receipt_no ?? 'N/A' }}</td>
                                <td>{{ $row->inventory->product_name }}</td>
                                <td>{{ $row->qty }}</td>
                                <td>{{ number_format($row->amount) }}</td>
                                <td>{{ date('d M Y', strtotime($row->timestamp)) }}</td>
                                <td>{{ date('h:i A', strtotime($row->timestamp)) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No data.</td>
                            </tr>
                        @endforelse
                        <tr>
                            <td class="bg-dark text-white fw-bold" colspan="3">Total </td>
                            <td class="bg-dark text-white fw-bold">{{ number_format($totalQty) }}</td>
                            <td class="bg-dark text-white fw-bold" class="text-center">
                                {{ number_format($totalAmount) }}</td>
                            <td class="bg-dark text-white fw-bold text-center" colspan="3"> - </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
