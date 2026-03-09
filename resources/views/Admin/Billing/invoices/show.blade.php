@extends('layouts.master-layout')

@section('title', 'Invoice Detail')
@section('breadcrumtitle', 'Invoice Detail')
@section('content')
<section class="panels-wells">
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h5 class="card-header-text">Invoice #{{ $invoice->invoice_no }}</h5>
            <div class="card-header-right">
                <a href="{{ route('billing.invoices.pdf', $invoice->id) }}" class="btn btn-primary btn-sm">
                    <i class="icofont icofont-file-pdf"></i> Download PDF
                </a>
                <a href="{{ route('billing.invoices.index') }}" class="btn btn-inverse btn-sm">
                    <i class="icofont icofont-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-block">
            <div class="row">
                <div class="col-lg-8 col-md-7">
                    <h6 class="text-muted f-w-400">Company Information</h6>
                    <h5 class="m-b-5">{{ optional($invoice->company)->name }}</h5>
                    <p class="text-muted m-b-0"><i class="icofont icofont-calendar"></i> {{ date('M d, Y', strtotime($invoice->period_start)) }} - {{ date('M d, Y', strtotime($invoice->period_end)) }}</p>
                </div>
                <div class="col-lg-4 col-md-5 text-right">
                    <h6 class="text-muted f-w-400">Invoice Status</h6>
                    @if($invoice->status == 'paid')
                        <span class="badge badge-success f-14">PAID</span>
                    @elseif($invoice->status == 'partial')
                        <span class="badge badge-warning f-14">PARTIAL</span>
                    @elseif($invoice->status == 'void')
                        <span class="badge badge-danger f-14">VOID</span>
                    @else
                        <span class="badge badge-info f-14">ISSUED</span>
                    @endif
                    <p class="m-t-15 m-b-0"><strong>Due:</strong> {{ date('M d, Y', strtotime($invoice->due_date)) }}</p>
                </div>
            </div>

            <div class="table-responsive m-t-30">
                <table class="table table-bordered table-hover m-b-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th width="50%" class="f-w-600">Description</th>
                            <th width="10%" class="text-center f-w-600">Qty</th>
                            <th width="20%" class="text-right f-w-600">Unit Price</th>
                            <th width="20%" class="text-right f-w-600">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->lines as $line)
                        <tr>
                            <td>{{ $line->description }}</td>
                            <td class="text-center">{{ number_format($line->qty, 0) }}</td>
                            <td class="text-right">PKR {{ number_format($line->unit_price, 2) }}</td>
                            <td class="text-right">PKR {{ number_format($line->line_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                            <td class="text-right"><strong>PKR {{ number_format($invoice->subtotal, 2) }}</strong></td>
                        </tr>
                        @if($invoice->tax_amount > 0)
                        <tr>
                            <td colspan="3" class="text-right">Tax:</td>
                            <td class="text-right">PKR {{ number_format($invoice->tax_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($invoice->previous_due > 0)
                        <tr>
                            <td colspan="3" class="text-right">Previous Due:</td>
                            <td class="text-right">PKR {{ number_format($invoice->previous_due, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="table-active">
                            <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                            <td class="text-right"><strong>PKR {{ number_format($invoice->total_amount, 2) }}</strong></td>
                        </tr>
                        @if($invoice->paid_amount > 0)
                        <tr>
                            <td colspan="3" class="text-right">Paid Amount:</td>
                            <td class="text-right text-success">PKR {{ number_format($invoice->paid_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="table-warning">
                            <td colspan="3" class="text-right"><strong>Balance Due:</strong></td>
                            <td class="text-right"><strong class="text-danger">PKR {{ number_format($invoice->balance_amount, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-header-text"><i class="icofont icofont-cur-dollar"></i> Receive Payment</h5>
                </div>
                <div class="card-block">
                    <form method="post" action="{{ route('billing.invoices.payments.store', $invoice->id) }}">
                        @csrf
                        <div class="form-group">
                            <label>Payment Date</label>
                            <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount" min="0.01" step="0.01" placeholder="0.00" required>
                        </div>
                        <div class="form-group">
                            <label>Payment Mode ID</label>
                            <input type="number" class="form-control" name="payment_mode_id" placeholder="Optional">
                        </div>
                        <div class="form-group">
                            <label>Reference #</label>
                            <input type="text" class="form-control" name="reference_no" placeholder="Optional">
                        </div>
                        <div class="form-group">
                            <label>Narration</label>
                            <textarea class="form-control" name="narration" rows="2" placeholder="Optional notes"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-block"><i class="icofont icofont-check"></i> Add Payment</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-header-text"><i class="icofont icofont-edit"></i> Add Adjustment</h5>
                </div>
                <div class="card-block">
                    <form method="post" action="{{ route('billing.invoices.adjustments.store', $invoice->id) }}">
                        @csrf
                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" name="type" required>
                                <option value="debit">Debit (+)</option>
                                <option value="credit">Credit (-)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount" min="0.01" step="0.01" placeholder="0.00" required>
                        </div>
                        <div class="form-group">
                            <label>Adjustment Date</label>
                            <input type="date" class="form-control" name="adjustment_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Reason</label>
                            <input type="text" class="form-control" name="reason" placeholder="Reason for adjustment" required>
                        </div>
                        <button type="submit" class="btn btn-warning btn-block"><i class="icofont icofont-plus"></i> Add Adjustment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text"><i class="icofont icofont-history"></i> Payment History</h5>
        </div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
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
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ date('M d, Y', strtotime($payment->payment_date)) }}</td>
                            <td class="text-success"><strong>PKR {{ number_format($payment->amount, 2) }}</strong></td>
                            <td>{{ $payment->payment_mode_id ?? 'N/A' }}</td>
                            <td>{{ $payment->reference_no ?? '-' }}</td>
                            <td>{{ $payment->narration ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No payments recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text"><i class="icofont icofont-ui-edit"></i> Adjustments</h5>
        </div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->adjustments as $adjustment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ date('M d, Y', strtotime($adjustment->adjustment_date)) }}</td>
                            <td>
                                @if($adjustment->type == 'debit')
                                    <span class="badge badge-success">DEBIT (+)</span>
                                @else
                                    <span class="badge badge-danger">CREDIT (-)</span>
                                @endif
                            </td>
                            <td><strong>PKR {{ number_format($adjustment->amount, 2) }}</strong></td>
                            <td>{{ $adjustment->reason }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No adjustments made yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
