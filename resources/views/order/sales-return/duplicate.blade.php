@extends('layouts.master-layout')

@section('title', 'Sales Returns - Duplicate')
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
                    <h5 class="mb-0">Sales Returns — Duplicate Orders</h5>
                    <small class="text-muted">Clone receipts into status 14 with order_ref pointing to the original.</small>
                </div>
                <div class="card-body">
                    @include('order.sales-return._nav')

                    <div class="alert alert-info" id="edit-hint">
                        After duplicating, open a new return from the results table (Edit link) to delete line items and recalculate totals.
                    </div>

                    <form id="duplicateForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="order_ids">Order IDs (comma-separated)</label>
                            <textarea class="form-control" id="order_ids" name="order_ids" rows="3"
                                      placeholder="e.g. 1,2,3,4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" id="btnDuplicate">
                            Duplicate Orders
                        </button>
                    </form>

                    <div id="resultArea" class="mt-4" style="display:none;">
                        <h6>Results</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="resultTable">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Original ID</th>
                                        <th>New ID</th>
                                        <th>Message</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
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
    $('#duplicateForm').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#btnDuplicate');
        $btn.prop('disabled', true).text('Duplicating...');

        $.ajax({
            url: '{{ route('sales-returns.duplicate.store') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                order_ids: $('#order_ids').val()
            },
            success: function (res) {
                var rows = [];
                var data = res.data || {};

                (data.duplicated || []).forEach(function (row) {
                    rows.push(buildRow('Duplicated', row, true));
                });
                (data.skipped || []).forEach(function (row) {
                    rows.push(buildRow('Skipped', row, !!row.new_id));
                });
                (data.failed || []).forEach(function (row) {
                    rows.push(buildRow('Failed', row, false));
                });

                $('#resultTable tbody').html(rows.join('') || '<tr><td colspan="5">No results</td></tr>');
                $('#resultArea').show();
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Request failed';
                alert(msg);
            },
            complete: function () {
                $btn.prop('disabled', false).text('Duplicate Orders');
            }
        });
    });

    function buildRow(status, row, canEdit) {
        var newId = row.new_id || '';
        var editLink = (canEdit && newId)
            ? '<a href="{{ url('sales-returns') }}/' + newId + '/edit" class="btn btn-sm btn-outline-primary">Edit</a>'
            : '-';
        return '<tr>' +
            '<td>' + status + '</td>' +
            '<td>' + (row.original_id || '') + '</td>' +
            '<td>' + newId + '</td>' +
            '<td>' + (row.message || '') + '</td>' +
            '<td>' + editLink + '</td>' +
            '</tr>';
    }
})();
</script>
@endsection
