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
    </script>
@endsection


