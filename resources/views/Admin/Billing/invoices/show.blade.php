@extends('layouts.master-layout')

@section('title', 'Invoice Detail')
@section('breadcrumtitle', 'Invoice Detail')
@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Invoice {{ $invoice->invoice_no }}</h5>
                <h5 class="">
                    <a href="{{ route('billing.invoices.index') }}">
                        <i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18">Back to list</i>
                    </a>
                </h5>
            </div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-3"><strong>Company:</strong> {{ optional($invoice->company)->name }}</div>
                    <div class="col-md-3"><strong>Period:</strong> {{ $invoice->period_start }} to {{ $invoice->period_end }}</div>
                    <div class="col-md-2"><strong>Status:</strong> {{ ucfirst($invoice->status) }}</div>
                    <div class="col-md-2"><strong>Due:</strong> {{ $invoice->due_date }}</div>
                    <div class="col-md-2"><strong>Balance:</strong> {{ number_format($invoice->balance_amount, 2) }}</div>
                </div>

                <div class="table-responsive m-t-20">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->lines as $line)
                                <tr>
                                    <td>{{ $line->description }}</td>
                                    <td>{{ ucfirst($line->scope_type) }}</td>
                                    <td>{{ number_format($line->qty, 2) }}</td>
                                    <td>{{ number_format($line->unit_price, 2) }}</td>
                                    <td>{{ number_format($line->line_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-4"><strong>Subtotal:</strong> {{ number_format($invoice->subtotal, 2) }}</div>
                    <div class="col-md-4"><strong>Tax:</strong> {{ number_format($invoice->tax_amount, 2) }}</div>
                    <div class="col-md-4"><strong>Previous Due:</strong> {{ number_format($invoice->previous_due, 2) }}</div>
                </div>
                <div class="row m-t-10">
                    <div class="col-md-4"><strong>Total:</strong> {{ number_format($invoice->total_amount, 2) }}</div>
                    <div class="col-md-4"><strong>Paid:</strong> {{ number_format($invoice->paid_amount, 2) }}</div>
                    <div class="col-md-4"><strong>Balance:</strong> {{ number_format($invoice->balance_amount, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Receive Payment</h5>
            </div>
            <div class="card-block">
                <form method="post" action="{{ route('billing.invoices.payments.store', $invoice->id) }}" class="row">
                    @csrf
                    <div class="col-md-3">
                        <label>Date</label>
                        <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label>Amount</label>
                        <input type="number" class="form-control" name="amount" min="0.01" step="0.01" required>
                    </div>
                    <div class="col-md-3">
                        <label>Payment Mode ID</label>
                        <input type="number" class="form-control" name="payment_mode_id">
                    </div>
                    <div class="col-md-3">
                        <label>Reference #</label>
                        <input type="text" class="form-control" name="reference_no">
                    </div>
                    <div class="col-md-12 m-t-10">
                        <label>Narration</label>
                        <input type="text" class="form-control" name="narration">
                    </div>
                    <div class="col-md-12 m-t-10">
                        <button class="btn btn-success btn-sm">Add Payment</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Add Adjustment</h5>
            </div>
            <div class="card-block">
                <form method="post" action="{{ route('billing.invoices.adjustments.store', $invoice->id) }}" class="row">
                    @csrf
                    <div class="col-md-3">
                        <label>Type</label>
                        <select class="form-control" name="type">
                            <option value="debit">Debit (+)</option>
                            <option value="credit">Credit (-)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Amount</label>
                        <input type="number" class="form-control" name="amount" min="0.01" step="0.01" required>
                    </div>
                    <div class="col-md-3">
                        <label>Date</label>
                        <input type="date" class="form-control" name="adjustment_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label>Reason</label>
                        <input type="text" class="form-control" name="reason" required>
                    </div>
                    <div class="col-md-12 m-t-10">
                        <button class="btn btn-warning btn-sm">Add Adjustment</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Payment History</h5>
            </div>
            <div class="card-block table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Mode</th>
                            <th>Reference</th>
                            <th>Narration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date }}</td>
                                <td>{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->payment_mode_id }}</td>
                                <td>{{ $payment->reference_no }}</td>
                                <td>{{ $payment->narration }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No payments added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Adjustments</h5>
            </div>
            <div class="card-block table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->adjustments as $adjustment)
                            <tr>
                                <td>{{ $adjustment->adjustment_date }}</td>
                                <td>{{ strtoupper($adjustment->type) }}</td>
                                <td>{{ number_format($adjustment->amount, 2) }}</td>
                                <td>{{ $adjustment->reason }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No adjustments added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

