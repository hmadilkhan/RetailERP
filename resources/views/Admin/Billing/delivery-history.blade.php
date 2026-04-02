@extends('layouts.master-layout')

@section('title', 'Billing Delivery History')
@section('breadcrumtitle', 'Billing Delivery History')
@section('content')
    <section class="panels-wells" style="margin-top:70px;">
        <div class="card" style="border: 0; overflow: hidden; box-shadow: 0 18px 40px rgba(32, 56, 85, 0.12);">
            <div class="card-header" style="background: linear-gradient(135deg, #4CAF50 0%, #2e7d32 100%); color: #fff;">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h5 class="card-header-text text-white m-b-5">Billing Delivery History</h5>
                        <p class="m-b-0" style="color: rgba(255,255,255,0.82);">Track invoice WhatsApp attempts by run ID, company, invoice number, and delivery status.</p>
                    </div>
                    <div class="d-flex align-items-center flex-wrap" style="margin-left:auto;">
                        <a href="{{ route('billing.invoices.index') }}" class="btn btn-light btn-sm m-r-10" style="border:0; font-weight:600; padding:10px 16px;">
                            <i class="icofont icofont-list"></i> Billing Invoices
                        </a>
                        <a href="{{ route('billing.summary') }}" class="btn btn-outline-light btn-sm" style="color:white; padding:10px 16px; border-width:1px;">
                            <i class="icofont icofont-chart-bar-graph"></i> Billing Summary
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-block" style="background: linear-gradient(180deg, #fbfcfe 0%, #f4f7fb 100%);">
                <form method="get" class="row">
                    <div class="col-md-3">
                        <label>Company</label>
                        <select name="company_id" class="form-control select2">
                            <option value="">All</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->company_id }}" {{ request('company_id') == $company->company_id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All</option>
                            @foreach (['sent', 'skipped', 'failed'] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Invoice #</label>
                        <input type="text" name="invoice_no" class="form-control" value="{{ request('invoice_no') }}" placeholder="INV-2026040001">
                    </div>
                    <div class="col-md-3">
                        <label>Run ID</label>
                        <input type="text" name="run_id" class="form-control" value="{{ request('run_id') }}" placeholder="Paste batch UUID">
                    </div>
                    <div class="col-md-2">
                        <label>Date</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-12 m-t-15">
                        <button class="btn btn-success btn-sm">
                            <i class="icofont icofont-search-1"></i> Filter
                        </button>
                        <a href="{{ route('billing.delivery-history') }}" class="btn btn-default btn-sm m-l-5">
                            Reset
                        </a>
                    </div>
                </form>

                @if($recentRuns->count() > 0)
                    <div class="row m-t-25">
                        @foreach($recentRuns as $run)
                            <div class="col-md-3 col-sm-6">
                                <div class="card m-b-15" style="border:0; border-radius:16px; box-shadow:0 10px 30px rgba(30, 54, 80, 0.08);">
                                    <div class="card-block">
                                        <p class="text-muted text-uppercase m-b-5" style="letter-spacing:0.08em; font-size:11px;">Recent Run</p>
                                        <h6 class="m-b-5" style="font-weight:700;">{{ \Illuminate\Support\Str::limit($run->batch_uuid, 12, '...') }}</h6>
                                        <p class="m-b-5 text-muted">{{ date('M d, Y h:i A', strtotime($run->created_at)) }}</p>
                                        <p class="m-b-10 text-muted">
                                            {{ $run->period_start ? date('M d', strtotime($run->period_start)) : '-' }}
                                            to
                                            {{ $run->period_end ? date('M d', strtotime($run->period_end)) : '-' }}
                                        </p>
                                        <div class="m-b-10">
                                            <span class="badge badge-success">Sent {{ $run->whatsapp_sent_count }}</span>
                                            <span class="badge badge-warning">Skipped {{ $run->whatsapp_skipped_count }}</span>
                                            <span class="badge badge-danger">Failed {{ $run->whatsapp_failed_count }}</span>
                                        </div>
                                        <a href="{{ route('billing.delivery-history', ['run_id' => $run->batch_uuid]) }}" class="btn btn-outline-success btn-sm btn-block">
                                            View Run
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="table-responsive m-t-10" style="border-radius: 16px; overflow: hidden; box-shadow: 0 12px 35px rgba(30, 54, 80, 0.08);">
                    <table class="table table-bordered table-hover m-b-0" style="background:#fff;">
                        <thead style="background: #f3f5f7;">
                            <tr>
                                <th>Date</th>
                                <th>Company</th>
                                <th>Invoice</th>
                                <th>Status</th>
                                <th>Recipient</th>
                                <th>Run ID</th>
                                <th>Trigger</th>
                                <th>Detail</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deliveryLogs as $log)
                                <tr>
                                    <td>{{ date('M d, Y h:i A', strtotime($log->created_at)) }}</td>
                                    <td>{{ $log->company_name ?? 'N/A' }}</td>
                                    <td>{{ $log->invoice_no ?? 'N/A' }}</td>
                                    <td>
                                        @if($log->status === 'sent')
                                            <span class="badge badge-success">SENT</span>
                                        @elseif($log->status === 'skipped')
                                            <span class="badge badge-warning">SKIPPED</span>
                                        @elseif($log->status === 'failed')
                                            <span class="badge badge-danger">FAILED</span>
                                        @else
                                            <span class="badge badge-default">{{ strtoupper($log->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->to ?? '-' }}</td>
                                    <td>
                                        @if($log->batch_uuid)
                                            <a href="{{ route('billing.delivery-history', ['run_id' => $log->batch_uuid]) }}">
                                                {{ \Illuminate\Support\Str::limit($log->batch_uuid, 12, '...') }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $log->trigger ?? '-' }}</td>
                                    <td>{{ $log->reason ?? $log->description }}</td>
                                    <td>
                                        @if($log->invoice_id)
                                            <a href="{{ route('billing.invoices.show', $log->invoice_id) }}" class="btn btn-info btn-sm">View Invoice</a>
                                        @else
                                            <button type="button" class="btn btn-default btn-sm" disabled>N/A</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">No delivery history found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="m-t-15">
                    {{ $deliveryLogs->links('pagination::bootstrap-4') }}
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
