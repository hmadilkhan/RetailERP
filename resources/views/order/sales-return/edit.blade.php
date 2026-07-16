@extends('layouts.master-layout')

@section('title', 'Sales Returns - Edit')
@section('breadcrumtitle', 'Sales Returns')
@section('navbranchoperation', 'active')
@section('navorder', 'active')
@section('navsalesreturns', 'active')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Sales Return #{{ $order->id }}</h5>
                        <small class="text-muted">
                            Original order_ref: {{ $order->order_ref ?? '—' }}
                            | Date: {{ $order->date }} {{ $order->time }}
                            | Branch: {{ optional($order->branchrelation)->branch_name ?? $order->branch }}
                        </small>
                    </div>
                    <a href="{{ route('sales-returns.fbr') }}" class="btn btn-sm btn-success">Send to FBR</a>
                </div>
                <div class="card-body">
                    @include('order.sales-return._nav')

                    <div class="row mb-4" id="totalsBox">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <div class="text-muted small">Actual Amount</div>
                                <div class="h5 mb-0" id="actual_amount">{{ number_format((float)$order->actual_amount, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <div class="text-muted small">Sales Tax</div>
                                <div class="h5 mb-0" id="sales_tax_amount">{{ number_format((float)optional($order->orderAccountSub)->sales_tax_amount, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <div class="text-muted small">Discount</div>
                                <div class="h5 mb-0" id="discount_amount">{{ number_format((float)optional($order->orderAccountSub)->discount_amount, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <div class="text-muted small">Total Amount</div>
                                <div class="h5 mb-0" id="total_amount">{{ number_format((float)$order->total_amount, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="itemsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Tax</th>
                                    <th>Discount</th>
                                    <th>Line Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->orderdetails as $index => $item)
                                    <tr data-detail-id="{{ $item->receipt_detail_id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->item_name ?: optional($item->inventory)->product_name }}</td>
                                        <td>{{ $item->total_qty }}</td>
                                        <td>{{ number_format((float)$item->item_price, 2) }}</td>
                                        <td>{{ number_format((float)$item->taxamount, 2) }}</td>
                                        <td>{{ number_format((float)$item->discount, 2) }}</td>
                                        <td>{{ number_format((float)$item->total_amount, 2) }}</td>
                                        <td>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger btn-delete-item"
                                                    data-id="{{ $item->receipt_detail_id }}">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyRow">
                                        <td colspan="8" class="text-center text-muted">No line items left.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
    $(document).on('click', '.btn-delete-item', function () {
        var detailId = $(this).data('id');
        if (!confirm('Delete this line item and recalculate totals?')) {
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('Deleting...');

        $.ajax({
            url: '{{ url('sales-returns/items') }}/' + detailId + '/delete',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                $('tr[data-detail-id="' + detailId + '"]').remove();

                var totals = (res.data && res.data.totals) ? res.data.totals : {};
                if (totals.actual_amount !== undefined) {
                    $('#actual_amount').text(Number(totals.actual_amount).toFixed(2));
                }
                if (totals.sales_tax_amount !== undefined) {
                    $('#sales_tax_amount').text(Number(totals.sales_tax_amount).toFixed(2));
                }
                if (totals.discount_amount !== undefined) {
                    $('#discount_amount').text(Number(totals.discount_amount).toFixed(2));
                }
                if (totals.total_amount !== undefined) {
                    $('#total_amount').text(Number(totals.total_amount).toFixed(2));
                }

                if ($('#itemsTable tbody tr[data-detail-id]').length === 0) {
                    $('#itemsTable tbody').html(
                        '<tr id="emptyRow"><td colspan="8" class="text-center text-muted">No line items left.</td></tr>'
                    );
                }
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Delete failed';
                alert(msg);
                $btn.prop('disabled', false).text('Delete');
            }
        });
    });
})();
</script>
@endsection
