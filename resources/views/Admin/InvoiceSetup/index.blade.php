@extends('layouts.master-layout')

@section('title', 'Invoice Setup')

@section('content')
<section class="panels-wells">
    <div class="card" style="border: 0; overflow: hidden; box-shadow: 0 18px 40px rgba(32, 56, 85, 0.12);margin-top:70px;">
        <div class="card-header" style="background: linear-gradient(135deg, #4CAF50 0%, #4CAF50 100%); color: #fff;">
            <div class="d-flex align-items-center flex-wrap w-100">
                <div>
                    <h5 class="card-header-text m-b-5 text-white">Invoice Setup List</h5>
                    <p class="m-b-0" style="color: rgba(255, 255, 255, 0.82);">Manage billing setup rules and jump directly to each company invoice history.</p>
                </div>
                <a href="{{ route('invoice-setup.create') }}" class="btn btn-light btn-sm text-white" style="margin-left: auto; color: white; border: 0; font-weight: 600; padding: 10px 16px; box-shadow: 0 10px 24px rgba(90, 102, 114, 0.18);">
                    <i class="icofont icofont-plus " style="color: white"></i> Create Invoice Setup
                </a>
            </div>
        </div>
        <div class="card-block" style="background: linear-gradient(180deg, #fbfcfe 0%, #f4f7fb 100%);">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row m-b-20">
                <div class="col-md-3 col-sm-6">
                    <div class="card m-b-15" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
                        <div class="card-block">
                            <p class="text-muted text-uppercase m-b-5" style="letter-spacing: 0.08em; font-size: 11px;">Filtered Setups</p>
                            <h3 class="m-b-0" style="font-weight: 700;">{{ $summary['total'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card m-b-15" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
                        <div class="card-block">
                            <p class="text-muted text-uppercase m-b-5" style="letter-spacing: 0.08em; font-size: 11px;">Auto Invoice On</p>
                            <h3 class="m-b-0 text-success" style="font-weight: 700;">{{ $summary['auto_enabled'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card m-b-15" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
                        <div class="card-block">
                            <p class="text-muted text-uppercase m-b-5" style="letter-spacing: 0.08em; font-size: 11px;">Branch Based</p>
                            <h3 class="m-b-0 text-primary" style="font-weight: 700;">{{ $summary['branch_based'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card m-b-15" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
                        <div class="card-block">
                            <p class="text-muted text-uppercase m-b-5" style="letter-spacing: 0.08em; font-size: 11px;">Terminal Based</p>
                            <h3 class="m-b-0 text-warning" style="font-weight: 700;">{{ $summary['terminal_based'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card m-b-20" style="border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(30, 54, 80, 0.08);">
                <div class="card-block">
                    <form method="get" class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Company</label>
                            <select name="company_id" class="form-control select2">
                                <option value="">All Companies</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->company_id }}" {{ request('company_id') == $company->company_id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="f-w-600">Invoice Type</label>
                            <select name="invoice_type" class="form-control">
                                <option value="">All Types</option>
                                <option value="branch" {{ request('invoice_type') == 'branch' ? 'selected' : '' }}>Branch</option>
                                <option value="terminal" {{ request('invoice_type') == 'terminal' ? 'selected' : '' }}>Terminal</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="f-w-600">Auto Invoice</label>
                            <select name="is_auto_invoice" class="form-control">
                                <option value="">All</option>
                                <option value="1" {{ request('is_auto_invoice') === '1' ? 'selected' : '' }}>Enabled</option>
                                <option value="0" {{ request('is_auto_invoice') === '0' ? 'selected' : '' }}>Disabled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="f-w-600">Search</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Company or prefix">
                        </div>
                        <div class="col-md-2 m-t-25">
                            <button class="btn btn-success btn-sm">Filter</button>
                            <a href="{{ route('invoice-setup.index') }}" class="btn btn-default btn-sm">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="table-responsive" style="border-radius: 16px; overflow: hidden; box-shadow: 0 12px 35px rgba(30, 54, 80, 0.08);">
                <table class="table table-bordered table-hover m-b-0" style="background: #fff;">
                    <thead style="background: linear-gradient(90deg, #a8b4c0 0%, #c7d0d9 100%); color: #fff;">
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>Company</th>
                            <th>Invoice Type</th>
                            <th>Monthly Charges</th>
                            <th>Billing Cycle Day</th>
                            <th>Payment Due Days</th>
                            <th>Auto Invoice</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoiceSetups as $setup)
                        <tr>
                            <td>
                                <span class="badge badge-default" style="font-size: 12px; padding: 7px 10px;">
                                    {{ ($invoiceSetups->firstItem() ?? 0) + $loop->index }}
                                </span>
                            </td>
                            <td>
                                <div class="f-w-600 text-dark">{{ $setup->company->name ?? 'N/A' }}</div>
                                <small class="text-muted">Setup ID: {{ $setup->id }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $setup->invoice_type === 'branch' ? 'badge-primary' : 'badge-warning' }}" style="padding: 8px 10px;">
                                    {{ ucfirst($setup->invoice_type) }}
                                </span>
                            </td>
                            <td class="f-w-600 text-dark">PKR {{ number_format($setup->monthly_charges_amount, 2) }}</td>
                            <td>{{ $setup->billing_cycle_day }}</td>
                            <td>{{ $setup->payment_due_days }}</td>
                            <td>
                                <span class="badge badge-{{ $setup->is_auto_invoice ? 'success' : 'danger' }}" style="padding: 8px 10px;">
                                    {{ $setup->is_auto_invoice ? 'Enabled' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('billing.invoices.index', ['company_id' => $setup->company_id]) }}" class="btn btn-sm btn-info m-r-5">
                                    <i class="icofont icofont-eye"></i> View
                                </a>
                                <a href="{{ route('invoice-setup.edit', $setup->id) }}" class="btn btn-sm btn-warning">
                                    <i class="icofont icofont-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center p-4">
                                <div class="text-muted">
                                    <i class="icofont icofont-inbox f-28 d-block m-b-10"></i>
                                    No invoice setups found for the selected filters.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="m-t-15">
                {{ $invoiceSetups->links('pagination::bootstrap-4') }}
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
