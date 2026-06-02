@extends('layouts.master-tailwind')

@section('title', 'Billing Summary')
@section('page_title', 'Billing Summary')
@section('page_subtitle', 'Track company-wise invoice totals, collections, outstanding balances, and billing time due.')

@section('content')
    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <h2 class="text-base font-bold text-erp-ink">Summary Filters</h2>
                        <p class="mt-1 text-sm text-erp-mute">Filter company billing by invoice type, company, and payment status.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('billing.invoices.index') }}" class="inline-flex h-10 items-center rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">View Invoices</a>
                        <a href="{{ route('billing.delivery-history') }}" class="inline-flex h-10 items-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Delivery History</a>
                    </div>
                </div>

                <form method="GET" action="{{ route('billing.summary') }}" class="mt-4 grid gap-2 md:grid-cols-2 xl:grid-cols-5">
                    <select name="invoice_type" class="billing-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="Select invoice type">
                        <option value="monthly" {{ ($invoiceType ?? 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly Invoices</option>
                        <option value="previous_due" {{ ($invoiceType ?? 'monthly') === 'previous_due' ? 'selected' : '' }}>Previous Due</option>
                    </select>
                    <select name="company_id" class="billing-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="All Companies">
                        <option value="">All Companies</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->company_id }}" {{ (string) $selectedCompanyId === (string) $company->company_id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    <select name="status" class="billing-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="All Statuses">
                        <option value="">All Statuses</option>
                        <option value="paid" {{ $selectedStatus === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partial" {{ $selectedStatus === 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="unpaid" {{ $selectedStatus === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    </select>
                    <button type="submit" class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Apply Filter</button>
                    <a href="{{ route('billing.summary') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Reset</a>
                </form>
            </div>
        </section>

        @include('v2.billing.partials.summary-content', ['summary' => $summary])
    </div>
@endsection
