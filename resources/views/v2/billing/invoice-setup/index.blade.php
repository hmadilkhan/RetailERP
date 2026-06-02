@extends('layouts.master-tailwind')

@section('title', 'Invoice Setup')
@section('page_title', 'Invoice Setup')
@section('page_subtitle', 'Configure company billing rates, invoice type, cycle days, due dates, and auto invoice behavior.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Filtered Setups</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($summary['total']) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Matching current filters</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Auto Enabled</div>
                <div class="mt-4 text-3xl font-black text-emerald-700">{{ number_format($summary['auto_enabled']) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Auto invoice on</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Auto Disabled</div>
                <div class="mt-4 text-3xl font-black text-rose-700">{{ number_format($summary['auto_disabled']) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Manual billing flow</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Branch / Terminal</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($summary['branch_based']) }} / {{ number_format($summary['terminal_based']) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Invoice method split</p>
            </div>
            <a href="{{ route('invoice-setup.create') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Setup</div>
                    <p class="mt-2 text-sm text-white/75">Add billing rules</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <h2 class="text-base font-bold text-erp-ink">Setup Directory</h2>
                        <p class="mt-1 text-sm text-erp-mute">Filter company billing rules and jump to invoice history.</p>
                    </div>
                    <form method="GET" action="{{ route('invoice-setup.index') }}" class="grid gap-2 sm:grid-cols-2 lg:grid-cols-5">
                        <select name="company_id" class="billing-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="All Companies">
                            <option value="">All Companies</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->company_id }}" {{ request('company_id') == $company->company_id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        <select name="invoice_type" class="billing-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="All Types">
                            <option value="">All Types</option>
                            <option value="branch" {{ request('invoice_type') == 'branch' ? 'selected' : '' }}>Branch</option>
                            <option value="terminal" {{ request('invoice_type') == 'terminal' ? 'selected' : '' }}>Terminal</option>
                        </select>
                        <select name="is_auto_invoice" class="billing-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="Auto: All">
                            <option value="">Auto: All</option>
                            <option value="1" {{ request('is_auto_invoice') === '1' ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ request('is_auto_invoice') === '0' ? 'selected' : '' }}>Disabled</option>
                        </select>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Company or prefix"
                            class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <div class="flex gap-2">
                            <button class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Filter</button>
                            <a href="{{ route('invoice-setup.index') }}" class="inline-flex h-10 items-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Company</th>
                            <th class="px-5 py-3 text-left font-bold">Type</th>
                            <th class="px-5 py-3 text-left font-bold">Monthly Charges</th>
                            <th class="px-5 py-3 text-left font-bold">Cycle</th>
                            <th class="px-5 py-3 text-left font-bold">Due Days</th>
                            <th class="px-5 py-3 text-left font-bold">Auto</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($invoiceSetups as $setup)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-erp-ink">{{ $setup->company->name ?? 'N/A' }}</div>
                                    <div class="mt-1 text-xs font-semibold uppercase tracking-[0.12em] text-erp-mute">Setup ID {{ $setup->id }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md px-2 py-1 text-xs font-bold ring-1 {{ $setup->invoice_type === 'branch' ? 'bg-sky-50 text-sky-700 ring-sky-200' : 'bg-amber-50 text-amber-700 ring-amber-200' }}">{{ ucfirst($setup->invoice_type) }}</span>
                                </td>
                                <td class="px-5 py-4 font-bold text-erp-ink">PKR {{ number_format($setup->monthly_charges_amount, 2) }}</td>
                                <td class="px-5 py-4 text-erp-text">Day {{ $setup->billing_cycle_day }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $setup->payment_due_days }} days</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md px-2 py-1 text-xs font-bold ring-1 {{ $setup->is_auto_invoice ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-rose-50 text-rose-700 ring-rose-200' }}">{{ $setup->is_auto_invoice ? 'Enabled' : 'Disabled' }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('billing.invoices.index', ['company_id' => $setup->company_id]) }}" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">Invoices</a>
                                        <a href="{{ route('invoice-setup.edit', $setup->id) }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-14 text-center">
                                    <div class="text-base font-bold text-erp-ink">No invoice setups found</div>
                                    <p class="mt-2 text-sm text-erp-mute">Create a setup or change filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-erp-line px-5 py-4">
                {{ $invoiceSetups->links('pagination::tailwind') }}
            </div>
        </section>
    </div>
@endsection
