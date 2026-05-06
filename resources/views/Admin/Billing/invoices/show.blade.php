@extends('layouts.master-layout')

@section('title', 'Invoice Detail')
@section('breadcrumtitle', 'Invoice Detail')
@section('content')
<section class="panels-wells" style="margin-top:70px;">
    @php
        $paidDate = $invoice->status === 'paid'
            ? optional($invoice->payments->sortByDesc('payment_date')->first())->payment_date
            : null;
    @endphp

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('error'))
        <div class="alert alert-danger" style="border: 0; border-left: 6px solid #a71d2a; border-radius: 12px; padding: 18px 22px; box-shadow: 0 12px 30px rgba(167, 29, 42, 0.18); background: linear-gradient(135deg, #fff1f2 0%, #ffe2e6 100%); color: #7f1d1d; margin-bottom: 20px;">
            <div class="d-flex align-items-start">
                <div style="font-size: 22px; line-height: 1; margin-right: 12px;">
                    <i class="icofont icofont-warning-alt"></i>
                </div>
                <div>
                    <div style="font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px;">
                        Action Required
                    </div>
                    <div style="font-size: 15px; font-weight: 600; line-height: 1.6;">
                        {{ $errors->first('error') }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any() && !$errors->has('error'))
        <div class="alert alert-danger">
            <strong>Please review the payment form.</strong>
            <ul class="m-b-0 p-l-20">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="margin-top: 20px; border: 0; overflow: hidden; box-shadow: 0 18px 40px rgba(32, 56, 85, 0.12);">
        <div class="card-header" style="background: linear-gradient(135deg, #4CAF50 0%, #4CAF50 100%); color: #fff;">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h5 class="card-header-text text-white m-b-5">Invoice #{{ $invoice->invoice_no }}</h5>
                    <p class="m-b-0" style="color: rgba(255,255,255,0.82);">Detailed invoice breakdown, payments, adjustments, and current receivable status.</p>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="margin-left: auto;">
                <form method="post" action="{{ route('billing.invoices.whatsapp.send', $invoice->id) }}" class="m-r-10 m-b-0">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm" style="color: white; border: 0; font-weight: 600; padding: 10px 16px;background-color:#4CAF50;">
                        <i class="icofont icofont-social-whatsapp" style="color:white;"></i> Send WhatsApp
                    </button>
                </form>
                <a href="{{ route('billing.invoices.pdf', $invoice->id) }}" class="btn btn-light btn-sm m-r-10" style="color: white; border: 0; font-weight: 600; padding: 10px 16px;">
                    <i class="icofont icofont-file-pdf" style="color:white;"></i> Download PDF
                </a>
                <a href="{{ route('billing.delivery-history', ['invoice_no' => $invoice->invoice_no]) }}" class="btn btn-light btn-sm m-r-10" style="color: white; border: 0; font-weight: 600; padding: 10px 16px;">
                    <i class="icofont icofont-history" style="color:white;"></i> Delivery History
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
                                @if($paidDate)
                                    <div class="text-muted m-t-10">Paid on {{ \Carbon\Carbon::parse($paidDate)->format('M d, Y') }}</div>
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
                            <h4 class="m-b-0 text-danger" style="font-weight: 700;">PKR {{ number_format($invoice->balance_amount + $invoice->previous_due, 2) }}</h4>
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
                            <td colspan="3" class="text-right">Outstanding at Issue:</td>
                            <td class="text-right">PKR {{ number_format($invoice->previous_due, 2) }}</td>
                        </tr>
                        @endif
                        <tr style="background: #f3f5f7;">
                            <td colspan="3" class="text-right"><strong>Current Invoice Total:</strong></td>
                            <td class="text-right"><strong>PKR {{ number_format($invoice->total_amount, 2) }}</strong></td>
                        </tr>
                        @if($invoice->paid_amount > 0)
                        <tr>
                            <td colspan="3" class="text-right">Paid Amount:</td>
                            <td class="text-right text-success">PKR {{ number_format($invoice->paid_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if(($invoice->credit_applied_amount ?? 0) > 0)
                        <tr>
                            <td colspan="3" class="text-right">Customer Credit Applied:</td>
                            <td class="text-right text-info">PKR {{ number_format($invoice->credit_applied_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr style="background: #fff6e6;">
                            <td colspan="3" class="text-right"><strong>Current Invoice Balance:</strong></td>
                            <td class="text-right"><strong class="text-danger">PKR {{ number_format($invoice->balance_amount, 2) }}</strong></td>
                        </tr>
                        @if($invoice->previous_due > 0)
                        <tr style="background: #fdebd0;">
                            <td colspan="3" class="text-right"><strong>Total Outstanding at Issue:</strong></td>
                            <td class="text-right"><strong class="text-danger">PKR {{ number_format($invoice->balance_amount + $invoice->previous_due, 2) }}</strong></td>
                        </tr>
                        @endif
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
                    <div class="alert alert-info">
                        Payments are applied automatically to the oldest outstanding invoices for this company first. A payment receive voucher will be generated and sent to WhatsApp after payment is posted.
                    </div>
                    <form method="post" action="{{ route('billing.invoices.payments.store', $invoice->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Payment Date</label>
                            <input type="date" class="form-control" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount" min="0.01" step="0.01" placeholder="0.00" value="{{ old('amount') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Payment Mode</label>
                            <select class="form-control" name="payment_mode_id" id="payment_mode_id">
                                <option value="">Select payment mode</option>
                                @foreach($paymentModes as $paymentMode)
                                    <option value="{{ $paymentMode->payment_id }}" data-is-cash="{{ strcasecmp(trim((string) $paymentMode->payment_mode), 'cash') === 0 ? '1' : '0' }}" {{ old('payment_mode_id') == $paymentMode->payment_id ? 'selected' : '' }}>
                                        {{ $paymentMode->payment_mode }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Reference #</label>
                            <input type="text" class="form-control" name="reference_no" value="{{ old('reference_no') }}" placeholder="Optional">
                        </div>
                        <div class="form-group">
                            <label>Narration</label>
                            <textarea class="form-control" name="narration" rows="2" placeholder="Optional notes">{{ old('narration') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label id="screenshots_label">Payment Screenshots</label>
                            <input type="file" class="form-control" name="screenshots[]" id="payment_screenshots" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple>
                            <small class="text-muted d-block m-t-5" id="screenshots_help">
                                Upload up to 8 screenshots. This is required for non-cash modes and optional for cash. Max 5 MB per image.
                            </small>
                            @if($errors->has('screenshots') || $errors->has('screenshots.*'))
                                <small class="text-danger d-block m-t-5">{{ $errors->first('screenshots') ?: $errors->first('screenshots.*') }}</small>
                            @endif
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

    <div class="row">
        <div class="col-md-6">
            <div class="card" style="border: 0; border-radius: 18px; overflow: hidden; box-shadow: 0 14px 35px rgba(30, 54, 80, 0.10);">
                <div class="card-header" style="background: linear-gradient(135deg, #4CAF50 0%, #4CAF50 100%); color: #fff;">
                    <h5 class="card-header-text text-white"><i class="icofont icofont-wallet"></i> Customer Credit</h5>
                </div>
                <div class="card-block" style="background: #fff;">
                    <div class="alert alert-info">
                        Available customer credit: <strong>PKR {{ number_format($customerCreditBalance ?? 0, 2) }}</strong>
                    </div>
                    <form method="post" action="{{ route('billing.invoices.credits.apply', $invoice->id) }}">
                        @csrf
                        <div class="form-group">
                            <label>Application Date</label>
                            <input type="date" class="form-control" name="application_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount" min="0.01" max="{{ min((float) ($customerCreditBalance ?? 0), (float) $invoice->balance_amount) }}" step="0.01" placeholder="0.00" required>
                            <small class="text-muted">Maximum allowed: PKR {{ number_format(min((float) ($customerCreditBalance ?? 0), (float) $invoice->balance_amount), 2) }}</small>
                        </div>
                        <div class="form-group">
                            <label>Reason</label>
                            <input type="text" class="form-control" name="reason" value="Apply available customer credit" required>
                        </div>
                        <button type="submit" class="btn btn-info btn-block" {{ (($customerCreditBalance ?? 0) <= 0 || $invoice->balance_amount <= 0) ? 'disabled' : '' }}>
                            <i class="icofont icofont-check"></i> Apply Customer Credit
                        </button>
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
                            <th>Voucher #</th>
                            <th>Mode</th>
                            <th>Reference</th>
                            <th>Narration</th>
                            <th>Screenshots</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->payments as $payment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ date('M d, Y', strtotime($payment->payment_date)) }}</td>
                            <td class="text-success"><strong>PKR {{ number_format($payment->amount, 2) }}</strong></td>
                            <td>{{ optional($payment->voucher)->voucher_no ?? '-' }}</td>
                            <td>{{ optional($payment->paymentMode)->payment_mode ?? 'N/A' }}</td>
                            <td>{{ $payment->reference_no ?? '-' }}</td>
                            <td>{{ $payment->narration ?? '-' }}</td>
                            <td style="min-width: 220px;">
                                @if($payment->screenshots->isNotEmpty())
                                    <div class="d-flex flex-wrap">
                                        @foreach($payment->screenshots as $screenshot)
                                            <div style="width: 88px; margin-right: 10px; margin-bottom: 10px;">
                                                <a href="{{ $screenshot->url }}" target="_blank" style="display: block; border: 1px solid #e5e8ec; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 20px rgba(30, 54, 80, 0.08);">
                                                    <img src="{{ $screenshot->url }}" alt="{{ $screenshot->original_name }}" style="width: 100%; height: 74px; object-fit: cover; display: block;">
                                                </a>
                                                <div class="m-t-5">
                                                    <a href="{{ $screenshot->url }}" target="_blank" class="btn btn-sm btn-outline-info btn-block" style="margin-bottom: 4px;">View</a>
                                                    <a href="{{ route('billing.payment-screenshots.download', $screenshot->id) }}" class="btn btn-sm btn-outline-success btn-block">Download</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">No screenshots</span>
                                @endif
                            </td>
                            <td style="min-width: 160px;">
                                @if($payment->voucher)
                                    <form method="post" action="{{ route('billing.invoices.payments.voucher.send', [$invoice->id, $payment->id]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success btn-block">
                                            <i class="icofont icofont-social-whatsapp"></i> Send Voucher
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">No voucher</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No payments recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card" style="border: 0; border-radius: 18px; overflow: hidden; box-shadow: 0 14px 35px rgba(30, 54, 80, 0.10);">
        <div class="card-header" style="background: linear-gradient(135deg, #4CAF50 0%, #4CAF50 100%); color: #fff;">
            <h5 class="card-header-text text-white"><i class="icofont icofont-wallet"></i> Credit Applications</h5>
        </div>
        <div class="card-block" style="background: #fff;">
            <div class="table-responsive" style="border-radius: 14px; overflow: hidden;">
                <table class="table table-striped table-hover m-b-0">
                    <thead style="background: #f3f5f7;">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->creditApplications as $creditApplication)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ date('M d, Y', strtotime($creditApplication->application_date)) }}</td>
                            <td class="text-info"><strong>PKR {{ number_format($creditApplication->amount, 2) }}</strong></td>
                            <td>{{ $creditApplication->reason }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No customer credit applied yet.</td>
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

@push('script')
<script>
    (function () {
        var paymentModeSelect = document.getElementById('payment_mode_id');
        var screenshotsInput = document.getElementById('payment_screenshots');
        var screenshotsLabel = document.getElementById('screenshots_label');
        var screenshotsHelp = document.getElementById('screenshots_help');

        if (!paymentModeSelect || !screenshotsInput || !screenshotsLabel || !screenshotsHelp) {
            return;
        }

        function syncScreenshotRequirement() {
            var selectedOption = paymentModeSelect.options[paymentModeSelect.selectedIndex];
            var requiresScreenshot = selectedOption && selectedOption.value !== '' && selectedOption.getAttribute('data-is-cash') !== '1';

            screenshotsInput.required = requiresScreenshot;
            screenshotsLabel.innerHTML = requiresScreenshot ? 'Payment Screenshots <span class="text-danger">*</span>' : 'Payment Screenshots';
            screenshotsHelp.textContent = requiresScreenshot
                ? 'Non-cash payments require at least one screenshot. You can upload up to 8 images, max 5 MB each.'
                : 'Cash screenshots are optional. You can still upload up to 8 images, max 5 MB each.';
        }

        paymentModeSelect.addEventListener('change', syncScreenshotRequirement);
        syncScreenshotRequirement();
    })();
</script>
@endpush
