@extends('layouts.master-tailwind')

@section('title', 'Website Details')
@section('page_title', 'Websites')
@section('page_subtitle', 'Manage company websites, domains, storefront status, brand assets, and deployment settings.')

@section('content')
    @php
        $activeCount = collect($websites)->where('status', 1)->count();
        $inactiveCount = collect($websites)->where('status', 0)->count();
    @endphp

    <div class="space-y-6">
        @if(Session::has('error'))
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-800">{{ Session::get('error') }}</div>
        @endif

        @if(Session::has('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800">{{ Session::get('success') }}</div>
        @endif

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Visible Websites</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format(count($websites)) }}</div>
                <p class="mt-2 text-sm text-erp-mute">{{ $mode == 1 ? 'Inactive records' : 'Active records' }}</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Active</div>
                <div class="mt-4 text-3xl font-black text-emerald-700">{{ number_format($activeCount) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Currently enabled</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Inactive</div>
                <div class="mt-4 text-3xl font-black text-rose-700">{{ number_format($inactiveCount) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Paused storefronts</p>
            </div>
            <a href="{{ route('website.create') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Website</div>
                    <p class="mt-2 text-sm text-white/75">Add a new storefront</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Website Directory</h2>
                    <p class="mt-1 text-sm text-erp-mute">{{ $mode == 1 ? 'Inactive website records.' : 'Active website records.' }}</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <input type="search" id="websiteSearch" class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80" placeholder="Search websites...">
                    <a href="{{ $mode == 0 ? route('inactiveWebsitelists', 'in-active') : route('inactiveWebsitelists') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                        {{ $mode == 0 ? 'Show Inactive' : 'Show Active' }}
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Website</th>
                            <th class="px-5 py-3 text-left font-bold">Company</th>
                            <th class="px-5 py-3 text-left font-bold">Type</th>
                            <th class="px-5 py-3 text-left font-bold">Domain</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="websiteTableBody" class="divide-y divide-slate-100">
                        @forelse($websites as $value)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img class="h-12 w-12 rounded-lg object-cover ring-1 ring-slate-200"
                                            src="{{ asset('storage/images/website/' . (!empty($value->logo) ? $value->logo : 'placeholder.jpg')) }}"
                                            alt="{{ $value->name }}">
                                        <div>
                                            <div class="font-bold text-erp-ink">{{ $value->name }}</div>
                                            <div class="mt-1 text-xs font-semibold uppercase tracking-[0.12em] text-erp-mute">ID {{ $value->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 font-semibold text-erp-text">{{ $value->company->name ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-bold text-slate-700 ring-1 ring-slate-200">{{ ucfirst($value->type) }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <a href="{{ $value->url }}" target="_blank" class="font-semibold text-erp-dark transition hover:text-erp">{{ $value->url }}</a>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md px-2 py-1 text-xs font-bold ring-1 {{ $value->status == 1 ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">
                                        {{ $value->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('website.edit', $value->id) }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</a>
                                        <form action="{{ route('websiteToggleStatus') }}" method="POST" onsubmit="return confirm('Change website status?');">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $value->id }}">
                                            <input type="hidden" name="mode" value="{{ $value->status == 1 ? 0 : 1 }}">
                                            <button type="submit" class="rounded-lg border {{ $value->status == 1 ? 'border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} px-3 py-2 text-xs font-bold transition">
                                                {{ $value->status == 1 ? 'Inactive' : 'Active' }}
                                            </button>
                                        </form>
                                        <a href="{{ route('rebuild', $value->id) }}" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">Rebuild</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-14 text-center">
                                    <div class="text-base font-bold text-erp-ink">No websites found</div>
                                    <p class="mt-2 text-sm text-erp-mute">Create a website to get started.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('websiteSearch')?.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#websiteTableBody tr').forEach(function (row) {
                row.hidden = !row.textContent.toLowerCase().includes(term);
            });
        });
    </script>
@endpush
