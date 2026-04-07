@extends('layouts.master-layout')

@section('title', 'Generate Invoice')
@section('breadcrumtitle', 'Generate Invoice')
@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Generate Monthly Invoice</h5>
                <h5 class="">
                    <a href="{{ route('billing.invoices.index') }}">
                        <i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18">Back to list</i>
                    </a>
                </h5>
            </div>
            <div class="card-block">
                @if ($errors->has('error'))
                    <div class="alert alert-danger">{{ $errors->first('error') }}</div>
                @endif
                <form method="post" action="{{ route('billing.invoices.store') }}" class="row">
                    @csrf
                    <div class="col-md-4">
                        <label>Company</label>
                        <select class="form-control select2" name="company_id" required>
                            <option value="">Select Company</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->company_id }}" {{ old('company_id') == $company->company_id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Period Start</label>
                        <input type="date" class="form-control" name="period_start" value="{{ old('period_start', date('Y-m-01')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label>Period End</label>
                        <input type="date" class="form-control" name="period_end" value="{{ old('period_end', date('Y-m-t')) }}" required>
                    </div>
                    <div class="col-md-4 m-t-10">
                        <label>Invoice Date</label>
                        <input type="date" class="form-control" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 m-t-10">
                        <label>Due Date (optional)</label>
                        <input type="date" class="form-control" name="due_date" value="{{ old('due_date') }}">
                    </div>
                    <div class="col-md-4 m-t-10">
                        <label>Tax Amount</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="tax_amount" value="{{ old('tax_amount', 0) }}">
                    </div>
                    <div class="col-md-12 m-t-10">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
                    </div>
                    <div class="col-md-12 m-t-20">
                        <div class="card" style="border: 1px solid #e8edf3; box-shadow: none;">
                            <div class="card-header" style="background: #f8fafc;">
                                <h6 class="m-b-0">Manual Branch-wise Due Periods</h6>
                                <small class="text-muted">Optional. Use this only for manual recovery when branches have different pending periods. Auto generation remains unchanged.</small>
                            </div>
                            <div class="card-block">
                                <div class="alert alert-info m-b-15">
                                    Example: if 2 branches are due from Oct-2025 to Mar-2026 and 1 branch is due from Jan-2025 to Mar-2026, tick those branches and set their own periods here.
                                </div>
                                <div id="branch-overrides-empty" class="text-muted">
                                    Select a company to load active branches.
                                </div>
                                <div id="branch-overrides-wrapper" class="table-responsive" style="display:none;">
                                    <table class="table table-bordered table-sm m-b-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 80px;">Include</th>
                                                <th>Branch</th>
                                                <th style="width: 200px;">Due Start</th>
                                                <th style="width: 200px;">Due End</th>
                                            </tr>
                                        </thead>
                                        <tbody id="branch-overrides-body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 m-t-20">
                        <button class="btn btn-primary">Generate Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('scriptcode_three')
    <script>
        $('.select2').select2();

        (function () {
            var $company = $('select[name="company_id"]');
            var $periodStart = $('input[name="period_start"]');
            var $periodEnd = $('input[name="period_end"]');
            var $wrapper = $('#branch-overrides-wrapper');
            var $body = $('#branch-overrides-body');
            var $empty = $('#branch-overrides-empty');
            var oldOverrides = @json(old('branch_overrides', []));

            function buildBranchRows(branches) {
                $body.empty();

                if (!branches.length) {
                    $wrapper.hide();
                    $empty.text('No active branches found for this company.').show();
                    return;
                }

                branches.forEach(function (branch, index) {
                    var oldRow = Array.isArray(oldOverrides)
                        ? oldOverrides.find(function (row) { return String(row.branch_id || '') === String(branch.branch_id); })
                        : null;

                    var includeChecked = oldRow ? !!oldRow.include : true;
                    var startValue = oldRow && oldRow.period_start ? oldRow.period_start : $periodStart.val();
                    var endValue = oldRow && oldRow.period_end ? oldRow.period_end : $periodEnd.val();

                    $body.append(
                        '<tr>' +
                            '<td class="text-center">' +
                                '<input type="hidden" name="branch_overrides[' + index + '][branch_id]" value="' + branch.branch_id + '">' +
                                '<input type="checkbox" name="branch_overrides[' + index + '][include]" value="1" ' + (includeChecked ? 'checked' : '') + '>' +
                            '</td>' +
                            '<td>' + branch.branch_name + '</td>' +
                            '<td><input type="date" class="form-control" name="branch_overrides[' + index + '][period_start]" value="' + (startValue || '') + '"></td>' +
                            '<td><input type="date" class="form-control" name="branch_overrides[' + index + '][period_end]" value="' + (endValue || '') + '"></td>' +
                        '</tr>'
                    );
                });

                $empty.hide();
                $wrapper.show();
            }

            function loadBranches(companyId) {
                $body.empty();

                if (!companyId) {
                    $wrapper.hide();
                    $empty.text('Select a company to load active branches.').show();
                    return;
                }

                $empty.text('Loading branches...').show();
                $wrapper.hide();

                $.ajax({
                    url: '{{ url('/get-branches-by-company') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        company: companyId
                    }
                }).done(function (response) {
                    buildBranchRows(Array.isArray(response) ? response : []);
                }).fail(function () {
                    $wrapper.hide();
                    $empty.text('Unable to load branches right now.').show();
                });
            }

            $company.on('change', function () {
                loadBranches($(this).val());
            });

            $periodStart.add($periodEnd).on('change', function () {
                if (!$body.children().length) {
                    return;
                }

                var startValue = $periodStart.val();
                var endValue = $periodEnd.val();

                $body.find('input[name$="[period_start]"]').each(function () {
                    if (!this.value) {
                        this.value = startValue;
                    }
                });

                $body.find('input[name$="[period_end]"]').each(function () {
                    if (!this.value) {
                        this.value = endValue;
                    }
                });
            });

            if ($company.val()) {
                loadBranches($company.val());
            }
        })();
    </script>
@endsection

