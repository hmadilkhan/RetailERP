@extends('layouts.master-tailwind')

@section('title', 'Customer List')
@section('page_title', 'Customer List')
@section('page_subtitle', 'Browse branch customers with country, city, contact, and profile details.')

@section('content')
    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Import Customers</h2>
                    <p class="mt-1 text-sm text-erp-mute">Upload customer records or download the sample CSV template.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button id="downloadsample" type="button" class="rounded-lg border border-erp-line bg-white px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Download Sample</button>
                    <a href="{{ route('customer.create') }}" class="rounded-lg border border-erp bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Create Customer</a>
                </div>
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

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Customers</h2>
                    <p class="mt-1 text-sm text-erp-mute">{{ $main->total() ?? count($main) }} records found for this branch.</p>
                </div>
                <input id="customerListSearch" type="search" placeholder="Search visible records..." class="h-10 w-64 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Mobile</th>
                            <th class="px-5 py-3">Phone</th>
                            <th class="px-5 py-3">CNIC</th>
                            <th class="px-5 py-3">Country</th>
                            <th class="px-5 py-3">City</th>
                            <th class="px-5 py-3">Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line">
                        @forelse($main as $customer)
                            <tr class="customer-list-row" data-search="{{ strtolower(($customer->name ?? '').' '.($customer->mobile ?? '').' '.($customer->phone ?? '').' '.($customer->nic ?? '').' '.($customer->country_name ?? '').' '.($customer->city_name ?? '')) }}">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <img class="h-11 w-11 rounded-lg object-cover ring-1 ring-erp-line" src="{{ asset('storage/images/customers/'.(!empty($customer->image) ? $customer->image : 'placeholder.jpg')) }}" alt="{{ $customer->name ?? 'Customer' }}">
                                        <div>
                                            <div class="font-bold text-erp-ink">{{ $customer->name ?? '-' }}</div>
                                            <div class="text-xs text-erp-mute">{{ $customer->email ?? 'No email' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-erp-text">{{ $customer->mobile ?? '-' }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $customer->phone ?? '-' }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $customer->nic ?? '-' }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $customer->country_name ?? '-' }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $customer->city_name ?? '-' }}</td>
                                <td class="max-w-sm px-5 py-3 text-erp-text">
                                    <span class="line-clamp-2">{{ $customer->address ?? '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-sm text-erp-mute">No customers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($main, 'links'))
                <div class="border-t border-erp-line px-5 py-4">
                    {{ $main->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('customerListSearch').addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('.customer-list-row').forEach(row => {
                row.classList.toggle('hidden', term !== '' && !row.dataset.search.includes(term));
            });
        });

        document.getElementById('downloadsample').addEventListener('click', function () {
            window.location = 'https://sabsoft.com.pk/Retail/assets/samples/sample_customer.csv';
        });
    </script>
@endpush
