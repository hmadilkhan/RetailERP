<div class="row m-b-20">
    <div class="col-lg-3 col-md-6">
        <div class="card m-b-15" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
            <div class="card-block">
                <p class="text-muted text-uppercase m-b-5" style="letter-spacing: 0.08em; font-size: 11px;">Companies</p>
                <h3 class="m-b-0" style="font-weight: 700;">{{ $summary->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card m-b-15" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
            <div class="card-block">
                <p class="text-muted text-uppercase m-b-5" style="letter-spacing: 0.08em; font-size: 11px;">Total Outstanding</p>
                <h3 class="m-b-0 text-danger" style="font-weight: 700;">PKR {{ number_format($summary->sum('balance_amount'), 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card m-b-15" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
            <div class="card-block">
                <p class="text-muted text-uppercase m-b-5" style="letter-spacing: 0.08em; font-size: 11px;">Collected</p>
                <h3 class="m-b-0 text-success" style="font-weight: 700;">PKR {{ number_format($summary->sum('paid_amount'), 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card m-b-15" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
            <div class="card-block">
                <p class="text-muted text-uppercase m-b-5" style="letter-spacing: 0.08em; font-size: 11px;">Estimated Unpaid Months</p>
                <h3 class="m-b-0" style="font-weight: 700; color: #c25b12;">{{ number_format($summary->sum('unpaid_months'), 1) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive" style="border-radius: 16px; overflow: hidden; box-shadow: 0 12px 35px rgba(30, 54, 80, 0.08);">
    <table class="table table-bordered table-hover m-b-0" style="background: #fff;">
        <thead style="background: linear-gradient(90deg, #a8b4c0 0%, #c7d0d9 100%); color: #fff;">
            <tr>
                <th>#</th>
                <th>Company Name</th>
                <th>Total Invoices</th>
                <th>Total Amount</th>
                <th>Paid Amount</th>
                <th>Balance Amount</th>
                <th>Unpaid Months</th>
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
                    <span class="badge" style="padding: 8px 10px; background: #fff3e8; color: #b45309; font-weight: 700;">
                        {{ number_format($item->unpaid_months, 1) }} months
                    </span>
                </td>
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
                    <a href="{{ route('billing.invoices.index', ['company_id' => $item->company_id]) }}" class="btn btn-sm btn-info">
                        <i class="icofont icofont-eye"></i> View Invoices
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center p-4">
                    <div class="text-muted">
                        <i class="icofont icofont-inbox f-28 d-block m-b-10"></i>
                        No billing data found.
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f3f5f7;">
                <th colspan="3" class="text-right">Grand Total:</th>
                <th>PKR {{ number_format($summary->sum('total_amount'), 2) }}</th>
                <th class="text-success">PKR {{ number_format($summary->sum('paid_amount'), 2) }}</th>
                <th class="text-danger">PKR {{ number_format($summary->sum('balance_amount'), 2) }}</th>
                <th style="color: #b45309;">{{ number_format($summary->sum('unpaid_months'), 1) }} months</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>
</div>
