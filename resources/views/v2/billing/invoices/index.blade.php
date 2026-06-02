@extends('layouts.master-tailwind')

@section('title', 'Billing Invoices')
@section('page_title', 'Billing Invoices')
@section('page_subtitle', 'Review generated invoices, balances, payment status, due dates, and WhatsApp delivery actions.')

@section('content')
    @php
        $visibleTotal = $invoices->getCollection()->sum('total_amount');
        $visiblePaid = $invoices->getCollection()->sum('paid_amount');
        $visibleBalance = $invoices->getCollection()->sum('balance_amount');
    @endphp

    <div class="space-y-6">
        @if ($errors->has('error'))
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-800">{{ $errors->first('error') }}</div>
        @endif

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Invoices</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($invoices->total()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Matching current filters</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Visible Total</div>
                <div class="mt-4 text-2xl font-black text-erp-ink">PKR {{ number_format($visibleTotal, 2) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Current page amount</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Paid / Balance</div>
                <div class="mt-4 text-2xl font-black text-emerald-700">{{ number_format($visiblePaid, 2) }}</div>
                <p class="mt-2 text-sm text-rose-700">Balance PKR {{ number_format($visibleBalance, 2) }}</p>
            </div>
            <a href="{{ route('billing.invoices.create') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Generate Invoice</div>
                    <p class="mt-2 text-sm text-white/75">Create monthly or previous due invoice</p>
                </div>
            </a>
        </section>

        @if(!empty($paymentSummary))
            <section class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Received Amount</div>
                    <div class="mt-3 text-2xl font-black text-emerald-700">PKR {{ number_format($paymentSummary->received_amount, 2) }}</div>
                </div>
                <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Invoices Paid</div>
                    <div class="mt-3 text-2xl font-black text-erp-ink">{{ number_format($paymentSummary->invoice_count) }}</div>
                </div>
                <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Payment Vouchers</div>
                    <div class="mt-3 text-2xl font-black text-erp-ink">{{ number_format($paymentSummary->voucher_count) }}</div>
                </div>
            </section>
        @endif

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <h2 class="text-base font-bold text-erp-ink">Invoice Directory</h2>
                        <p class="mt-1 text-sm text-erp-mute">Filter invoices by company, status, invoice month, or payment month.</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('billing.delivery-history') }}" class="inline-flex h-10 items-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Delivery History</a>
                        <a href="{{ route('billing.invoices.create') }}" class="inline-flex h-10 items-center rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Generate</a>
                    </div>
                </div>
                <form method="GET" action="{{ route('billing.invoices.index') }}" class="mt-4 grid gap-2 md:grid-cols-3 xl:grid-cols-6">
                    <select name="company_id" class="billing-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="All Companies">
                        <option value="">All Companies</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->company_id }}" {{ request('company_id') == $company->company_id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                    <select name="invoice_type" class="billing-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="Select invoice type">
                        <option value="monthly" {{ ($invoiceType ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="previous_due" {{ ($invoiceType ?? 'monthly') == 'previous_due' ? 'selected' : '' }}>Previous Due</option>
                    </select>
                    <select name="status" class="billing-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="All Statuses">
                        <option value="">All Statuses</option>
                        @foreach (['draft', 'issued', 'partial', 'paid', 'overdue', 'void'] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <input type="month" name="month" value="{{ request('month') }}" class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <input type="month" name="payment_month" value="{{ request('payment_month') }}" class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <div class="flex gap-2">
                        <button class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Filter</button>
                        <a href="{{ route('billing.invoices.index') }}" class="inline-flex h-10 items-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Reset</a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Invoice</th>
                            <th class="px-5 py-3 text-left font-bold">Company</th>
                            <th class="px-5 py-3 text-left font-bold">Period</th>
                            <th class="px-5 py-3 text-right font-bold">Total</th>
                            <th class="px-5 py-3 text-right font-bold">Paid</th>
                            <th class="px-5 py-3 text-right font-bold">Balance</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                            <th class="px-5 py-3 text-left font-bold">Due / Paid</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-erp-ink">{{ $invoice->invoice_no }}</div>
                                    <div class="mt-1 text-xs font-semibold uppercase tracking-[0.12em] text-erp-mute">{{ ucfirst($invoice->invoice_type ?? 'monthly') }}</div>
                                </td>
                                <td class="px-5 py-4 font-semibold text-erp-text">{{ optional($invoice->company)->name }}</td>
                                <td class="px-5 py-4 text-erp-mute">{{ $invoice->period_start }}<br>{{ $invoice->period_end }}</td>
                                <td class="px-5 py-4 text-right font-bold text-erp-ink">{{ number_format($invoice->total_amount, 2) }}</td>
                                <td class="px-5 py-4 text-right font-bold text-emerald-700">{{ number_format($invoice->paid_amount, 2) }}</td>
                                <td class="px-5 py-4 text-right font-bold text-rose-700">{{ number_format($invoice->balance_amount, 2) }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md px-2 py-1 text-xs font-bold ring-1 {{ $invoice->status === 'paid' ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : ($invoice->status === 'partial' ? 'bg-amber-50 text-amber-700 ring-amber-200' : ($invoice->status === 'void' ? 'bg-rose-50 text-rose-700 ring-rose-200' : 'bg-sky-50 text-sky-700 ring-sky-200')) }}">{{ ucfirst($invoice->status) }}</span>
                                </td>
                                <td class="px-5 py-4 text-erp-mute">
                                    <div>{{ $invoice->due_date }}</div>
                                    <div class="mt-1 text-xs">{{ $invoice->status === 'paid' && !empty($invoice->payments_max_payment_date) ? \Carbon\Carbon::parse($invoice->payments_max_payment_date)->format('M d, Y') : 'Not paid' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('billing.invoices.show', $invoice->id) }}" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">View</a>
                                        <form method="post" action="{{ route('billing.invoices.whatsapp.send', $invoice->id) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">WhatsApp</button>
                                        </form>
                                        @if ($invoice->payments_count == 0)
                                            <form method="post" action="{{ route('billing.invoices.destroy', $invoice->id) }}" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-14 text-center">
                                    <div class="text-base font-bold text-erp-ink">No invoices found</div>
                                    <p class="mt-2 text-sm text-erp-mute">Generate an invoice or change filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-erp-line px-5 py-4">
                {{ $invoices->links('pagination::tailwind') }}
            </div>
        </section>
    </div>
@endsection
