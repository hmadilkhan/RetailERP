@extends('layouts.master-tailwind')

@section('title', 'Company')
@section('page_title', 'Companies')
@section('page_subtitle', 'Review company records, city mapping, contact information, and status in the new Tailwind workspace.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Companies</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($company->total()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Active companies in system</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Visible Rows</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($company->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Showing on this page</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Search</div>
                <div class="mt-4 text-xl font-black text-erp-ink">{{ $search !== '' ? $search : 'All companies' }}</div>
                <p class="mt-2 text-sm text-erp-mute">Name, city, email, mobile, or status</p>
            </div>
            <a href="{{ url('/createcompany') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Company</div>
                    <p class="mt-2 text-sm text-white/75">Open company creation screen</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Company Directory</h2>
                    <p class="mt-1 text-sm text-erp-mute">Current company records from the existing company module.</p>
                </div>
                <form method="GET" action="{{ url('/companies') }}" id="searchForm" class="flex w-full gap-2 sm:w-auto">
                    <input type="text" name="search" id="companySearch" value="{{ $search }}" autocomplete="off"
                        placeholder="Search companies..."
                        class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Company</th>
                            <th class="px-5 py-3 text-left font-bold">City</th>
                            <th class="px-5 py-3 text-left font-bold">Contact</th>
                            <th class="px-5 py-3 text-left font-bold">Email</th>
                            <th class="px-5 py-3 text-left font-bold">Address</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($company as $value)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-erp-panel text-sm font-black text-white">
                                            {{ strtoupper(substr($value->name ?? 'C', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-erp-ink">{{ $value->name }}</div>
                                            <div class="mt-1 text-xs font-semibold uppercase tracking-[0.12em] text-erp-mute">ID {{ $value->company_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->city_name ?? '-' }}</td>
                                <td class="px-5 py-4 font-semibold text-erp-text">{{ $value->mobile_contact ?? '-' }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->email ?? '-' }}</td>
                                <td class="max-w-md px-5 py-4 text-erp-mute">
                                    <div class="line-clamp-2">{{ $value->address ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">
                                        {{ $value->status_name }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-14 text-center">
                                    <div class="text-base font-bold text-erp-ink">No companies found</div>
                                    <p class="mt-2 text-sm text-erp-mute">{{ $search ? 'Try a different search term.' : 'No company records are available.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-erp-mute">Showing {{ $company->firstItem() ?? 0 }} to {{ $company->lastItem() ?? 0 }} of {{ $company->total() }} companies</div>
                <div class="flex gap-2">
                    @if ($company->onFirstPage())
                        <span class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-300">Previous</span>
                    @else
                        <a href="{{ $company->previousPageUrl() }}" class="rounded-lg border border-erp-line px-3 py-2 text-sm font-semibold text-erp-text transition hover:border-erp hover:text-erp-dark">Previous</a>
                    @endif

                    @if ($company->hasMorePages())
                        <a href="{{ $company->nextPageUrl() }}" class="rounded-lg border border-erp-line px-3 py-2 text-sm font-semibold text-erp-text transition hover:border-erp hover:text-erp-dark">Next</a>
                    @else
                        <span class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-300">Next</span>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        let companySearchTimer;
        document.getElementById('companySearch')?.addEventListener('input', function () {
            clearTimeout(companySearchTimer);
            companySearchTimer = setTimeout(function () {
                document.getElementById('searchForm').submit();
            }, 400);
        });
    </script>
@endpush
