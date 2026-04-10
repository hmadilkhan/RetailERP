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
                        <label>Month</label>
                        <input type="month" name="month" class="form-control" value="{{ request('month') }}">
                    </div>
                    <div class="col-md-3 m-t-25">
                        <button class="btn btn-success btn-sm">Filter</button>
                    </div>
                </form>

                <div class="table-responsive m-t-15">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Company</th>
                                <th>Period</th>
                                <th>Total</th>
                                <th>Paid</th>
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
                                    <td>{{ optional($invoice->company)->name }}</td>
                                    <td>{{ $invoice->period_start }} to {{ $invoice->period_end }}</td>
                                    <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                    <td>{{ number_format($invoice->paid_amount, 2) }}</td>
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
                                    <td colspan="10" class="text-center">No invoices found.</td>
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
