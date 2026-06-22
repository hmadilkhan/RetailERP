@extends('layouts.master-tailwind')

@section('title', 'Customer')
@section('page_title', 'Customers')
@section('page_subtitle', 'Search customers, review balances, open ledgers, and manage active or inactive profiles.')

@section('content')
    @php
        $activeCustomers = collect($details)->where('status_id', 1);
        $inactiveCustomers = collect($details)->where('status_id', 2);
        $totalBalance = collect($details)->sum('balance');
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Active Customers</div>
                <div class="mt-3 text-2xl font-bold text-erp-ink">{{ number_format($activeCustomers->count()) }}</div>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Inactive Customers</div>
                <div class="mt-3 text-2xl font-bold text-erp-ink">{{ number_format($inactiveCustomers->count()) }}</div>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Net Balance</div>
                <div class="mt-3 text-2xl font-bold text-erp-ink">{{ session('currency') }} {{ number_format($totalBalance, 2) }}</div>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Import Customers</h2>
                    <p class="mt-1 text-sm text-erp-mute">Upload a customer CSV or download the sample template.</p>
                </div>
                <button type="button" id="downloadsample" class="rounded-lg border border-erp-line bg-white px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Download Sample</button>
            </div>
            <form method="POST" action="{{ url('uploadFile') }}" enctype="multipart/form-data" class="grid gap-4 px-5 py-5 md:grid-cols-[1fr_auto] md:items-end">
                @csrf
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Select File</span>
                    <input type="file" name="file" class="mt-2 block w-full rounded-lg border border-erp-line bg-white p-2 text-sm text-erp-text file:mr-4 file:rounded-lg file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white">
                </label>
                <button type="submit" class="rounded-lg border border-erp bg-erp px-5 py-2.5 text-sm font-bold text-white transition hover:bg-erp-dark">Import</button>
            </form>
        </section>

        <section x-data="{ tab: 'active' }" class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div class="flex rounded-lg border border-erp-line bg-erp-soft p-1">
                    <button type="button" @click="tab = 'active'" :class="tab === 'active' ? 'bg-white text-erp-ink shadow-sm' : 'text-erp-mute'" class="rounded-md px-4 py-2 text-sm font-bold">Active</button>
                    <button type="button" @click="tab = 'inactive'" :class="tab === 'inactive' ? 'bg-white text-erp-ink shadow-sm' : 'text-erp-mute'" class="rounded-md px-4 py-2 text-sm font-bold">Inactive</button>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <input id="customerSearch" type="search" placeholder="Search customer..." class="h-10 w-64 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <button type="button" onclick="openReport()" class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-bold text-rose-700 transition hover:bg-rose-100">PDF</button>
                    <button type="button" onclick="openExcelReport()" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100">Excel</button>
                    <a href="{{ route('customer.create') }}" class="rounded-lg border border-erp bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Create Customer</a>
                </div>
            </div>

            <div x-show="tab === 'active'" x-cloak class="overflow-x-auto">
                @include('v2.customer.partials.table', ['customers' => $activeCustomers, 'status' => 'active'])
            </div>

            <div x-show="tab === 'inactive'" x-cloak class="overflow-x-auto">
                @include('v2.customer.partials.table', ['customers' => $inactiveCustomers, 'status' => 'inactive'])
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const csrfToken = '{{ csrf_token() }}';

        document.getElementById('customerSearch').addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('.customer-row').forEach(row => {
                row.classList.toggle('hidden', term !== '' && !row.dataset.search.includes(term));
            });
        });

        function removeCustomer(id) {
            if (!confirm('Do you want to remove this customer?')) return;

            fetch("{{ url('/inactivecustomer') }}", {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id: id })
            }).then(res => res.text()).then(resp => {
                if (resp == 1) window.location = "{{ route('customer.index') }}";
            });
        }

        function activeCustomer(id) {
            if (!confirm('Do you want to activate this customer?')) return;

            fetch("{{ url('active-customer') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id: id })
            }).then(res => res.text()).then(resp => {
                if (resp == 1) window.location = "{{ url('customer') }}";
            });
        }

        function changeCheckbox(elementId, customerId) {
            const value = document.getElementById(elementId).checked ? 1 : 0;
            fetch("{{ url('/mobile-app-status') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id: customerId, value: value })
            });
        }

        function openReport() {
            window.open("{{ url('customers-report-pdf') }}");
        }

        function openExcelReport() {
            window.open("{{ url('export-customer-balance') }}");
        }

        document.getElementById('downloadsample').addEventListener('click', function () {
            window.location = 'https://sabsoft.com.pk/Retail/assets/samples/sample_customer.csv';
        });
    </script>
@endpush
