@extends('layouts.master-tailwind')

@section('title', 'Company')
@section('page_title', 'Companies')
@section('page_subtitle', 'Manage company records, logos, location mapping, contacts, package setup, and billing profile.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Companies</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($companies->total()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Active company records</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Visible Rows</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($companies->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Showing on this page</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Search</div>
                <div class="mt-4 text-xl font-black text-erp-ink">{{ $search !== '' ? $search : 'All companies' }}</div>
                <p class="mt-2 text-sm text-erp-mute">Name, city, email, mobile, or status</p>
            </div>
            <a href="{{ route('company.create') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Company</div>
                    <p class="mt-2 text-sm text-white/75">Add a new company</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Company Directory</h2>
                    <p class="mt-1 text-sm text-erp-mute">Search, edit, or deactivate company profiles.</p>
                </div>
                <form method="GET" action="{{ route('company.index') }}" id="searchForm" class="flex w-full gap-2 sm:w-auto">
                    <input type="text" name="search" id="companySearch" value="{{ $search }}" autocomplete="off"
                        placeholder="Search companies..."
                        class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
                    <a href="{{ route('company.create') }}" class="inline-flex h-10 shrink-0 items-center rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Create</a>
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
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($companies as $value)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img class="h-11 w-11 rounded-lg object-cover ring-1 ring-slate-200"
                                            src="{{ asset('storage/images/company/' . (!empty($value->logo) ? $value->logo : 'placeholder.jpg')) }}"
                                            alt="{{ $value->name }}">
                                        <div>
                                            <div class="font-bold text-erp-ink">{{ $value->name }}</div>
                                            <div class="mt-1 text-xs font-semibold uppercase tracking-[0.12em] text-erp-mute">ID {{ $value->company_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->city_name ?? '-' }}</td>
                                <td class="px-5 py-4 font-semibold text-erp-text">{{ $value->mobile_contact ?? '-' }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->email ?? '-' }}</td>
                                <td class="max-w-md px-5 py-4 text-erp-mute"><div class="line-clamp-2">{{ $value->address ?? '-' }}</div></td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">{{ $value->status_name }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ url('/company-edit') }}/{{ $value->company_id }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</a>
                                        <form method="POST" action="{{ url('/delete-company/' . $value->company_id) }}" onsubmit="return confirm('Delete this company?');">
                                            @csrf
                                            <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-14 text-center">
                                    <div class="text-base font-bold text-erp-ink">No companies found</div>
                                    <p class="mt-2 text-sm text-erp-mute">{{ $search ? 'Try a different search term.' : 'Create your first company to get started.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-erp-mute">Showing {{ $companies->firstItem() ?? 0 }} to {{ $companies->lastItem() ?? 0 }} of {{ $companies->total() }} companies</div>
                <div class="flex gap-2">
                    @if ($companies->onFirstPage())
                        <span class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-300">Previous</span>
                    @else
                        <a href="{{ $companies->previousPageUrl() }}" class="rounded-lg border border-erp-line px-3 py-2 text-sm font-semibold text-erp-text transition hover:border-erp hover:text-erp-dark">Previous</a>
                    @endif

                    @if ($companies->hasMorePages())
                        <a href="{{ $companies->nextPageUrl() }}" class="rounded-lg border border-erp-line px-3 py-2 text-sm font-semibold text-erp-text transition hover:border-erp hover:text-erp-dark">Next</a>
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
