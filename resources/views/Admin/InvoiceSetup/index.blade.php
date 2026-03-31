@extends('layouts.master-layout')

@section('title', 'Invoice Setup')

@section('content')
<section class="panels-wells">
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">Invoice Setup List</h5>
            <a href="{{ route('invoice-setup.create') }}" class="btn btn-primary f-right">
                <i class="icofont icofont-plus"></i> Create Invoice Setup
            </a>
        </div>
        <div class="card-block">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="get" class="row m-b-15">
                <div class="col-md-3">
                    <label>Company</label>
                    <select name="company_id" class="form-control select2">
                        <option value="">All Companies</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->company_id }}" {{ request('company_id') == $company->company_id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Invoice Type</label>
                    <select name="invoice_type" class="form-control">
                        <option value="">All Types</option>
                        <option value="branch" {{ request('invoice_type') == 'branch' ? 'selected' : '' }}>Branch</option>
                        <option value="terminal" {{ request('invoice_type') == 'terminal' ? 'selected' : '' }}>Terminal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Auto Invoice</label>
                    <select name="is_auto_invoice" class="form-control">
                        <option value="">All</option>
                        <option value="1" {{ request('is_auto_invoice') === '1' ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ request('is_auto_invoice') === '0' ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Company or prefix">
                </div>
                <div class="col-md-2 m-t-25">
                    <button class="btn btn-success btn-sm">Filter</button>
                    <a href="{{ route('invoice-setup.index') }}" class="btn btn-default btn-sm">Reset</a>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Company</th>
                            <th>Invoice Type</th>
                            <th>Monthly Charges</th>
                            <th>Billing Cycle Day</th>
                            <th>Payment Due Days</th>
                            <th>Auto Invoice</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoiceSetups as $setup)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $setup->company->name ?? 'N/A' }}</td>
                            <td>{{ ucfirst($setup->invoice_type) }}</td>
                            <td>{{ number_format($setup->monthly_charges_amount, 2) }}</td>
                            <td>{{ $setup->billing_cycle_day }}</td>
                            <td>{{ $setup->payment_due_days }}</td>
                            <td>
                                <span class="badge badge-{{ $setup->is_auto_invoice ? 'success' : 'danger' }}">
                                    {{ $setup->is_auto_invoice ? 'Enabled' : 'Disabled' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('invoice-setup.edit', $setup->id) }}" class="btn btn-sm btn-warning">
                                    <i class="icofont icofont-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No invoice setups found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="m-t-15">
                {{ $invoiceSetups->links('pagination::bootstrap-4') }}
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
