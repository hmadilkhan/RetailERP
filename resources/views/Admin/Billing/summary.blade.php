@extends('layouts.master-layout')

@section('title', 'Billing Summary')
@section('breadcrumtitle', 'Billing Summary')

@section('content')
<section class="panels-wells">
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h5 class="card-header-text">Company Billing Summary</h5>
        </div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Company Name</th>
                            <th>Total Invoices</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Balance Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($summary as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->company_name }}</td>
                            <td>{{ $item->total_invoices }}</td>
                            <td>{{ number_format($item->total_amount, 2) }}</td>
                            <td>{{ number_format($item->paid_amount, 2) }}</td>
                            <td>{{ number_format($item->balance_amount, 2) }}</td>
                            <td>
                                @if($item->balance_amount <= 0)
                                    <span class="badge badge-success">Paid</span>
                                @elseif($item->paid_amount > 0)
                                    <span class="badge badge-warning">Partial</span>
                                @else
                                    <span class="badge badge-danger">Unpaid</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('billing.invoices.index', ['company_id' => $item->company_id]) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="icofont icofont-eye"></i> View Invoices
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No billing data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <th colspan="3" class="text-right">Grand Total:</th>
                            <th>{{ number_format($summary->sum('total_amount'), 2) }}</th>
                            <th>{{ number_format($summary->sum('paid_amount'), 2) }}</th>
                            <th>{{ number_format($summary->sum('balance_amount'), 2) }}</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
