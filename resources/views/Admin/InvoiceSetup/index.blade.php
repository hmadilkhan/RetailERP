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
        </div>
    </div>
</section>
@endsection
