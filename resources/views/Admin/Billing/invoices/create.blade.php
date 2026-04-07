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
                    <div class="col-md-4 m-t-10">
                        <label>Generation Mode</label>
                        <select class="form-control" name="generation_mode" id="generation_mode">
                            <option value="auto" {{ old('generation_mode', 'auto') === 'auto' ? 'selected' : '' }}>Automatic</option>
                            <option value="manual" {{ old('generation_mode') === 'manual' ? 'selected' : '' }}>Manual</option>
                        </select>
                    </div>
                    <div class="col-md-12 m-t-10">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
                    </div>
                    <div class="col-md-12 m-t-20" id="manual-scope-section" style="{{ old('generation_mode') === 'manual' ? '' : 'display:none;' }}">
                        <div class="card" style="border: 1px solid #e8edf3; box-shadow: none;">
                            <div class="card-header" style="background: #f8fafc;">
                                <h6 class="m-b-0" id="manual-scope-title">Manual Due Periods</h6>
                                <small class="text-muted">Use this only for manual recovery when branches or terminals have different pending periods. Auto generation remains unchanged.</small>
                            </div>
                            <div class="card-block">
                                <div class="alert alert-info m-b-15">
                                    Select only the branches or terminals you want to bill manually. Unselected rows will not be included.
                                </div>
                                <div id="scope-overrides-empty" class="text-muted">
                                    Select a company and switch to manual mode to load billing targets.
                                </div>
                                <div id="scope-overrides-wrapper" class="table-responsive" style="display:none;">
                                    <table class="table table-bordered table-sm m-b-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 80px;">Include</th>
                                                <th id="scope-name-header">Branch</th>
                                                <th style="width: 220px;">Details</th>
                                                <th style="width: 200px;">Due Start</th>
                                                <th style="width: 200px;">Due End</th>
                                            </tr>
                                        </thead>
                                        <tbody id="scope-overrides-body"></tbody>
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
            var $generationMode = $('#generation_mode');
            var $periodStart = $('input[name="period_start"]');
            var $periodEnd = $('input[name="period_end"]');
            var $section = $('#manual-scope-section');
            var $wrapper = $('#scope-overrides-wrapper');
            var $body = $('#scope-overrides-body');
            var $empty = $('#scope-overrides-empty');
            var $title = $('#manual-scope-title');
            var $scopeNameHeader = $('#scope-name-header');
            var oldOverrides = @json(old('scope_overrides', []));

            function buildScopeRows(invoiceType, items) {
                $body.empty();

                if (!items.length) {
                    $wrapper.hide();
                    $empty.text('No active ' + (invoiceType === 'terminal' ? 'terminals' : 'branches') + ' found for this company.').show();
                    return;
                }

                $title.text('Manual ' + (invoiceType === 'terminal' ? 'Terminal' : 'Branch') + '-wise Due Periods');
                $scopeNameHeader.text(invoiceType === 'terminal' ? 'Terminal' : 'Branch');

                items.forEach(function (item, index) {
                    var oldRow = Array.isArray(oldOverrides)
                        ? oldOverrides.find(function (row) {
                            return String(row.scope_type || '') === String(invoiceType)
                                && String(row.scope_id || '') === String(item.id);
                        })
                        : null;

                    var includeChecked = oldRow ? !!oldRow.include : false;
                    var startValue = oldRow && oldRow.period_start ? oldRow.period_start : $periodStart.val();
                    var endValue = oldRow && oldRow.period_end ? oldRow.period_end : $periodEnd.val();

                    $body.append(
                        '<tr>' +
                            '<td class="text-center">' +
                                '<input type="hidden" name="scope_overrides[' + index + '][scope_type]" value="' + invoiceType + '">' +
                                '<input type="hidden" name="scope_overrides[' + index + '][scope_id]" value="' + item.id + '">' +
                                '<input type="checkbox" name="scope_overrides[' + index + '][include]" value="1" ' + (includeChecked ? 'checked' : '') + '>' +
                            '</td>' +
                            '<td>' + item.name + '</td>' +
                            '<td>' + (item.meta || '-') + '</td>' +
                            '<td><input type="date" class="form-control" name="scope_overrides[' + index + '][period_start]" value="' + (startValue || '') + '"></td>' +
                            '<td><input type="date" class="form-control" name="scope_overrides[' + index + '][period_end]" value="' + (endValue || '') + '"></td>' +
                        '</tr>'
                    );
                });

                $empty.hide();
                $wrapper.show();
            }

            function loadManualTargets() {
                $body.empty();
                var companyId = $company.val();

                if ($generationMode.val() !== 'manual') {
                    $section.hide();
                    $wrapper.hide();
                    $empty.text('Select manual mode to load billing targets.').show();
                    return;
                }

                $section.show();

                if (!companyId) {
                    $wrapper.hide();
                    $empty.text('Select a company to load billing targets.').show();
                    return;
                }

                $empty.text('Loading billing targets...').show();
                $wrapper.hide();

                $.ajax({
                    url: '{{ route('billing.invoices.generation-targets') }}',
                    type: 'GET',
                    data: { company_id: companyId }
                }).done(function (response) {
                    buildScopeRows(response.invoice_type || 'branch', Array.isArray(response.items) ? response.items : []);
                }).fail(function () {
                    $wrapper.hide();
                    $empty.text('Unable to load billing targets right now.').show();
                });
            }

            $company.on('change', function () {
                loadManualTargets();
            });

            $generationMode.on('change', function () {
                loadManualTargets();
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

            loadManualTargets();
        })();
    </script>
@endsection
