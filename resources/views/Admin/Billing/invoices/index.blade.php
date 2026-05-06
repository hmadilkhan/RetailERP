@extends('layouts.master-layout')

@section('title', 'Billing Invoices')
@section('breadcrumtitle', 'Billing Invoices')
@section('content')
    <section class="panels-wells" style="margin-top:70px;">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Billing Invoices</h5>
                <a href="{{ route('billing.delivery-history') }}" class="btn btn-success btn-sm f-right m-l-10">Delivery
                    History</a>
                <a href="{{ route('billing.invoices.create') }}" class="btn btn-primary btn-sm f-right">Generate Invoice</a>
            </div>
            <div class="card-block">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->has('error'))
                    <div class="alert alert-danger">
                        {{ $errors->first('error') }}
                    </div>
                @endif

                <form method="get" class="row">
                    <div class="col-md-3">
                        <label>Company</label>
                        <select name="company_id" class="form-control select2">
                            <option value="">All</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->company_id }}"
                                    {{ request('company_id') == $company->company_id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Invoice Type</label>
                        <select name="invoice_type" class="form-control">
                            <option value="monthly" {{ ($invoiceType ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="previous_due" {{ ($invoiceType ?? 'monthly') == 'previous_due' ? 'selected' : '' }}>Previous Due</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All</option>
                            @foreach (['draft', 'issued', 'partial', 'paid', 'overdue', 'void'] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Invoice Month</label>
                        <input type="month" name="month" class="form-control" value="{{ request('month') }}">
                    </div>
                    <div class="col-md-3 m-t-10">
                        <label>Payment Month</label>
                        <input type="month" name="payment_month" class="form-control" value="{{ request('payment_month') }}">
                    </div>
                    <div class="col-md-12 m-t-15">
                        <button class="btn btn-success btn-sm f-right">Filter</button>
                    </div>
                </form>

                @if(!empty($paymentSummary))
                    <div class="row m-t-15">
                        <div class="col-md-4">
                            <div class="card" style="border:0; box-shadow:0 8px 24px rgba(30,54,80,0.08);">
                                <div class="card-block">
                                    <p class="text-muted text-uppercase m-b-5" style="letter-spacing:0.08em; font-size:11px;">Received Amount</p>
                                    <h4 class="m-b-0">{{ number_format($paymentSummary->received_amount, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card" style="border:0; box-shadow:0 8px 24px rgba(30,54,80,0.08);">
                                <div class="card-block">
                                    <p class="text-muted text-uppercase m-b-5" style="letter-spacing:0.08em; font-size:11px;">Invoices Paid</p>
                                    <h4 class="m-b-0">{{ number_format($paymentSummary->invoice_count) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card" style="border:0; box-shadow:0 8px 24px rgba(30,54,80,0.08);">
                                <div class="card-block">
                                    <p class="text-muted text-uppercase m-b-5" style="letter-spacing:0.08em; font-size:11px;">Payment Vouchers</p>
                                    <h4 class="m-b-0">{{ number_format($paymentSummary->voucher_count) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="table-responsive m-t-15">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Type</th>
                                <th>Company</th>
                                <th>Period</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Credit</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th>Paid Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_no }}</td>
                                    <td>{{ ucfirst($invoice->invoice_type ?? 'monthly') }}</td>
                                    <td>{{ optional($invoice->company)->name }}</td>
                                    <td>{{ $invoice->period_start }} to {{ $invoice->period_end }}</td>
                                    <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                    <td>{{ number_format($invoice->paid_amount, 2) }}</td>
                                    <td>{{ number_format($invoice->credit_applied_amount ?? 0, 2) }}</td>
                                    <td>{{ number_format($invoice->balance_amount, 2) }}</td>
                                    <td>{{ ucfirst($invoice->status) }}</td>
                                    <td>{{ $invoice->due_date }}</td>
                                    <td>
                                        @if($invoice->status === 'paid' && !empty($invoice->payments_max_payment_date))
                                            {{ \Carbon\Carbon::parse($invoice->payments_max_payment_date)->format('M d, Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('billing.invoices.show', $invoice->id) }}"
                                            class="btn btn-info btn-sm">View</a>
                                        <form method="post"
                                            action="{{ route('billing.invoices.whatsapp.send', $invoice->id) }}"
                                            style="display:inline;"
                                            class="m-b-0">
                                            @csrf
                                            <button type="submit" class="btn btn-light btn-sm"
                                                style="color: white; border: 0; font-weight: 600; padding: 10px 16px;background-color:#4CAF50;">
                                                <i class="icofont icofont-social-whatsapp" style="color:white;"></i> Send
                                                WhatsApp
                                            </button>
                                        </form>
                                        @if ($invoice->payments_count == 0)
                                            <form method="post"
                                                action="{{ route('billing.invoices.destroy', $invoice->id) }}"
                                                style="display:inline;"
                                                onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-danger btn-sm" disabled
                                                title="Payment already received">Delete</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center">No invoices found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div>
                    {{ $invoices->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scriptcode_three')
    <script>
        $('.select2').select2();
    </script>
@endsection
