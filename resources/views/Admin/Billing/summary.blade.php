@extends('layouts.master-layout')

@section('title', 'Billing Summary')
@section('breadcrumtitle', 'Billing Summary')

@section('content')
<section class="panels-wells">
    <div class="card" style="margin-top: 20px; border: 0; overflow: hidden; box-shadow: 0 18px 40px rgba(32, 56, 85, 0.12);">
        <div class="card-header" style="background: linear-gradient(135deg, #0d8f55 0%, #16a765 100%); color: #fff;">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h5 class="card-header-text text-white m-b-5">Company Billing Summary</h5>
                    <p class="m-b-0" style="color: rgba(255,255,255,0.82);">Track invoice totals, collections, and pending balances company-wise.</p>
                </div>
                <a href="{{ route('billing.invoices.index') }}" class="btn btn-light btn-sm" style="color: #0f8d56; border: 0; font-weight: 600; padding: 10px 16px;">
                    <i class="icofont icofont-list"></i> All Invoices
                </a>
            </div>
        </div>
        <div class="card-block" style="background: linear-gradient(180deg, #fbfcfe 0%, #f4f7fb 100%);">
            <div class="row m-b-20">
                <div class="col-md-4">
                    <div class="card m-b-15" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
                        <div class="card-block">
                            <p class="text-muted text-uppercase m-b-5" style="letter-spacing: 0.08em; font-size: 11px;">Companies</p>
                            <h3 class="m-b-0" style="font-weight: 700;">{{ $summary->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card m-b-15" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
                        <div class="card-block">
                            <p class="text-muted text-uppercase m-b-5" style="letter-spacing: 0.08em; font-size: 11px;">Total Outstanding</p>
                            <h3 class="m-b-0 text-danger" style="font-weight: 700;">PKR {{ number_format($summary->sum('balance_amount'), 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card m-b-15" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
                        <div class="card-block">
                            <p class="text-muted text-uppercase m-b-5" style="letter-spacing: 0.08em; font-size: 11px;">Collected</p>
                            <h3 class="m-b-0 text-success" style="font-weight: 700;">PKR {{ number_format($summary->sum('paid_amount'), 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive" style="border-radius: 16px; overflow: hidden; box-shadow: 0 12px 35px rgba(30, 54, 80, 0.08);">
                <table class="table table-bordered table-hover m-b-0" style="background: #fff;">
                    <thead style="background: linear-gradient(90deg, #0d8f55 0%, #16a765 100%); color: #fff;">
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
                            <td>
                                <div class="f-w-600 text-dark">{{ $item->company_name }}</div>
                                <small class="text-muted">Company ID: {{ $item->company_id }}</small>
                            </td>
                            <td><span class="badge badge-default" style="padding: 8px 10px;">{{ $item->total_invoices }}</span></td>
                            <td class="f-w-600">PKR {{ number_format($item->total_amount, 2) }}</td>
                            <td class="text-success f-w-600">PKR {{ number_format($item->paid_amount, 2) }}</td>
                            <td class="text-danger f-w-600">PKR {{ number_format($item->balance_amount, 2) }}</td>
                            <td>
                                @if($item->balance_amount <= 0)
                                    <span class="badge badge-success" style="padding: 8px 10px;">Paid</span>
                                @elseif($item->paid_amount > 0)
                                    <span class="badge badge-warning" style="padding: 8px 10px;">Partial</span>
                                @else
                                    <span class="badge badge-danger" style="padding: 8px 10px;">Unpaid</span>
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
                            <td colspan="8" class="text-center p-4">
                                <div class="text-muted">
                                    <i class="icofont icofont-inbox f-28 d-block m-b-10"></i>
                                    No billing data found.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr style="background: #ecf8f1;">
                            <th colspan="3" class="text-right">Grand Total:</th>
                            <th>PKR {{ number_format($summary->sum('total_amount'), 2) }}</th>
                            <th class="text-success">PKR {{ number_format($summary->sum('paid_amount'), 2) }}</th>
                            <th class="text-danger">PKR {{ number_format($summary->sum('balance_amount'), 2) }}</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
