@extends('layouts.master-layout')

@section('title', 'Invoice Detail')
@section('breadcrumtitle', 'Invoice Detail')
@section('content')
<section class="panels-wells" style="margin-top:70px;">
    <div class="card" style="margin-top: 20px; border: 0; overflow: hidden; box-shadow: 0 18px 40px rgba(32, 56, 85, 0.12);">
        <div class="card-header" style="background: linear-gradient(135deg, #4CAF50 0%, #4CAF50 100%); color: #fff;">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h5 class="card-header-text text-white m-b-5">Invoice #{{ $invoice->invoice_no }}</h5>
                    <p class="m-b-0" style="color: rgba(255,255,255,0.82);">Detailed invoice breakdown, payments, adjustments, and current receivable status.</p>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="margin-left: auto;">
                <a href="{{ route('billing.invoices.pdf', $invoice->id) }}" class="btn btn-light btn-sm m-r-10" style="color: white; border: 0; font-weight: 600; padding: 10px 16px;">
                    <i class="icofont icofont-file-pdf" style="color:white;"></i> Download PDF
                </a>
                <a href="{{ route('billing.invoices.index', ['company_id' => $invoice->company_id]) }}" class="btn btn-outline-light btn-sm" style="color:white; padding: 10px 16px; border-width: 1px;">
                    <i class="icofont icofont-arrow-left" style="color:white;"></i> Back
                </a>
                </div>
            </div>
        </div>
        <div class="card-block" style="background: linear-gradient(180deg, #fbfcfe 0%, #f4f7fb 100%);">
            <div class="row m-b-20">
                <div class="col-md-3 col-sm-6">
                    <div class="card m-b-15" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
                        <div class="card-block">
                            <p class="text-muted text-uppercase m-b-5" style="letter-spacing: 0.08em; font-size: 11px;">Company</p>
                            <h5 class="m-b-0" style="font-weight: 700;">{{ optional($invoice->company)->name }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card m-b-15" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
                        <div class="card-block">
                            <p class="text-muted text-uppercase m-b-5" style="letter-spacing: 0.08em; font-size: 11px;">Invoice Status</p>
                            <div>
                    @if($invoice->status == 'paid')
                        <span class="badge badge-success f-14" style="padding: 9px 12px;">PAID</span>
                    @elseif($invoice->status == 'partial')
                        <span class="badge badge-warning f-14" style="padding: 9px 12px;">PARTIAL</span>
                    @elseif($invoice->status == 'void')
                        <span class="badge badge-danger f-14" style="padding: 9px 12px;">VOID</span>
                    @else
                        <span class="badge badge-info f-14" style="padding: 9px 12px;">ISSUED</span>
                    @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card m-b-15" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
                        <div class="card-block">
                            <p class="text-muted text-uppercase m-b-5" style="letter-spacing: 0.08em; font-size: 11px;">Billing Period</p>
                            <h6 class="m-b-0" style="font-weight: 700;">{{ date('M d, Y', strtotime($invoice->period_start)) }}</h6>
                            <small class="text-muted">to {{ date('M d, Y', strtotime($invoice->period_end)) }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card m-b-15" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
                        <div class="card-block">
                            <p class="text-muted text-uppercase m-b-5" style="letter-spacing: 0.08em; font-size: 11px;">Balance Due</p>
                            <h4 class="m-b-0 text-danger" style="font-weight: 700;">PKR {{ number_format($invoice->balance_amount, 2) }}</h4>
                            <small class="text-muted">Due {{ date('M d, Y', strtotime($invoice->due_date)) }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive m-t-10" style="border-radius: 16px; overflow: hidden; box-shadow: 0 12px 35px rgba(30, 54, 80, 0.08);">
                <table class="table table-bordered table-hover m-b-0" style="background: #fff;">
                    <thead style="background: linear-gradient(90deg, #EFEFEF 0%, #EFEFEF 100%); color: black;">
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
                        <tr style="background: #f3f5f7;">
                            <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                            <td class="text-right"><strong>PKR {{ number_format($invoice->total_amount, 2) }}</strong></td>
                        </tr>
                        @if($invoice->paid_amount > 0)
                        <tr>
                            <td colspan="3" class="text-right">Paid Amount:</td>
                            <td class="text-right text-success">PKR {{ number_format($invoice->paid_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr style="background: #fff6e6;">
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
            <div class="card" style="border: 0; border-radius: 18px; overflow: hidden; box-shadow: 0 14px 35px rgba(30, 54, 80, 0.10);">
                <div class="card-header" style="background: linear-gradient(135deg, #4CAF50 0%, #4CAF50 100%); color: #fff;">
                    <h5 class="card-header-text text-white"><i class="icofont icofont-cur-dollar"></i> Receive Payment</h5>
                </div>
                <div class="card-block" style="background: #fff;">
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
            <div class="card" style="border: 0; border-radius: 18px; overflow: hidden; box-shadow: 0 14px 35px rgba(30, 54, 80, 0.10);">
                <div class="card-header" style="background: linear-gradient(135deg, #4CAF50 0%, #4CAF50 100%); color: #fff;">
                    <h5 class="card-header-text text-white"><i class="icofont icofont-edit"></i> Add Adjustment</h5>
                </div>
                <div class="card-block" style="background: #fff;">
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

    <div class="card" style="border: 0; border-radius: 18px; overflow: hidden; box-shadow: 0 14px 35px rgba(30, 54, 80, 0.10);">
        <div class="card-header" style="background: linear-gradient(135deg, #4CAF50 0%, #4CAF50 100%); color: #fff;">
            <h5 class="card-header-text text-white"><i class="icofont icofont-history"></i> Payment History</h5>
        </div>
        <div class="card-block" style="background: #fff;">
            <div class="table-responsive" style="border-radius: 14px; overflow: hidden;">
                <table class="table table-striped table-hover m-b-0">
                    <thead style="background: #f3f5f7;">
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

    <div class="card" style="border: 0; border-radius: 18px; overflow: hidden; box-shadow: 0 14px 35px rgba(30, 54, 80, 0.10);">
        <div class="card-header" style="background: linear-gradient(135deg, #4CAF50 0%, #4CAF50 100%); color: #fff;">
            <h5 class="card-header-text text-white"><i class="icofont icofont-ui-edit"></i> Adjustments</h5>
        </div>
        <div class="card-block" style="background: #fff;">
            <div class="table-responsive" style="border-radius: 14px; overflow: hidden;">
                <table class="table table-striped table-hover m-b-0">
                    <thead style="background: #f3f5f7;">
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
