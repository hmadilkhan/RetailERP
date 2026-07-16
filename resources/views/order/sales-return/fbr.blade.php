@extends('layouts.master-layout')

@section('title', 'Sales Returns - FBR')
@section('breadcrumtitle', 'Sales Returns')
@section('navbranchoperation', 'active')
@section('navorder', 'active')
@section('navsalesreturns', 'active')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Sales Returns — Send to FBR</h5>
                    <small class="text-muted">Posts as InvoiceType 3 (Sales Return) with RefUSIN = original order_ref.</small>
                </div>
                <div class="card-body">
                    @include('order.sales-return._nav')

                    <form method="GET" action="{{ route('sales-returns.fbr') }}" class="row g-2 mb-3">
                        <div class="col-md-2">
                            <label class="form-label">From</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">FBR Status</label>
                            <select name="fbr_status" class="form-control">
                                <option value="">All</option>
                                <option value="pending" @selected(request('fbr_status') === 'pending')>Pending</option>
                                <option value="sent" @selected(request('fbr_status') === 'sent')>Sent</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-secondary w-100">Filter</button>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-success w-100" id="btnSendSelected">Send Selected</button>
                        </div>
                    </form>

                    <div id="fbrResult" class="alert" style="display:none;"></div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="fbrTable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="checkAll"></th>
                                    <th>Return ID</th>
                                    <th>Original (order_ref)</th>
                                    <th>Date</th>
                                    <th>Branch</th>
                                    <th>Total</th>
                                    <th>Tax</th>
                                    <th>FBR Invoice</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returns as $row)
                                    @php
                                        $hasFbr = !empty($row->fbrInvNumber);
                                    @endphp
                                    <tr data-id="{{ $row->id }}">
                                        <td>
                                            @if(!$hasFbr)
                                                <input type="checkbox" class="row-check" value="{{ $row->id }}">
                                            @endif
                                        </td>
                                        <td>{{ $row->id }}</td>
                                        <td>{{ $row->order_ref ?? '—' }}</td>
                                        <td>{{ $row->date }} {{ $row->time }}</td>
                                        <td>{{ optional($row->branchrelation)->branch_name ?? $row->branch }}</td>
                                        <td>{{ number_format((float)$row->total_amount, 2) }}</td>
                                        <td>{{ number_format((float)optional($row->orderAccountSub)->sales_tax_amount, 2) }}</td>
                                        <td class="fbr-inv">{{ $row->fbrInvNumber ?: '—' }}</td>
                                        <td>
                                            <a href="{{ route('sales-returns.edit', $row->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                            @if(!$hasFbr)
                                                <button type="button" class="btn btn-sm btn-primary btn-send-one" data-id="{{ $row->id }}">Send</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No sales returns found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {{ $returns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scriptcode_three')
<script>
(function () {
    $('#checkAll').on('change', function () {
        $('.row-check').prop('checked', this.checked);
    });

    function sendIds(ids) {
        if (!ids.length) {
            alert('Select at least one return order.');
            return;
        }
        if (!confirm('Send ' + ids.length + ' order(s) to FBR as sales returns?')) {
            return;
        }

        $('#fbrResult').hide();
        $('#btnSendSelected').prop('disabled', true).text('Sending...');

        $.ajax({
            url: '{{ route('sales-returns.fbr.send') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                ids: ids
            },
            success: function (res) {
                var html = '<strong>' + res.message + '</strong><ul class="mb-0 mt-2">';
                (res.data || []).forEach(function (r) {
                    html += '<li>#' + r.order_id + ': ' + (r.success ? 'OK — ' + (r.invoice_number || '') : r.message) + '</li>';
                    if (r.success && r.invoice_number) {
                        var $tr = $('tr[data-id="' + r.order_id + '"]');
                        $tr.find('.fbr-inv').text(r.invoice_number);
                        $tr.find('.row-check, .btn-send-one').remove();
                    }
                });
                html += '</ul>';
                $('#fbrResult').removeClass('alert-danger').addClass('alert-success').html(html).show();
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'FBR send failed';
                $('#fbrResult').removeClass('alert-success').addClass('alert-danger').text(msg).show();
            },
            complete: function () {
                $('#btnSendSelected').prop('disabled', false).text('Send Selected');
            }
        });
    }

    $('#btnSendSelected').on('click', function () {
        var ids = $('.row-check:checked').map(function () { return parseInt(this.value, 10); }).get();
        sendIds(ids);
    });

    $(document).on('click', '.btn-send-one', function () {
        sendIds([parseInt($(this).data('id'), 10)]);
    });
})();
</script>
@endsection
