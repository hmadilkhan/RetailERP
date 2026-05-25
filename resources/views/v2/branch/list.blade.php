@extends('layouts.master-tailwind')

@section('title', 'Branches')
@section('page_title', 'Branches')
@section('page_subtitle', 'Manage branch profiles, terminals, contact details, reporting setup, and location coverage.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Branches</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($details->total()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Active records in current scope</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Visible Rows</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($details->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Showing on this page</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Search</div>
                <div class="mt-4 text-xl font-black text-erp-ink">{{ $search !== '' ? $search : 'All branches' }}</div>
                <p class="mt-2 text-sm text-erp-mute">Filtered by branch, city, contact, or code</p>
            </div>
            <a href="{{ url('/createbranch') }}"
                class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Branch</div>
                    <p class="mt-2 text-sm text-white/75">Add a new operational location</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Branch Directory</h2>
                    <p class="mt-1 text-sm text-erp-mute">Search, review, edit, email-map, delete, or attach terminals.</p>
                </div>
                <form method="GET" action="{{ url('/branches') }}" id="searchForm" class="flex w-full gap-2 sm:w-auto">
                    <input type="text" name="search" id="branchSearch" value="{{ $search }}" autocomplete="off"
                        placeholder="Search branches..."
                        class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
                    <a href="{{ url('/createbranch') }}"
                        class="inline-flex h-10 shrink-0 items-center rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">
                        Create
                    </a>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Branch</th>
                            <th class="px-5 py-3 text-left font-bold">City</th>
                            <th class="px-5 py-3 text-left font-bold">Contact</th>
                            <th class="px-5 py-3 text-left font-bold">Terminals</th>
                            <th class="px-5 py-3 text-left font-bold">Serials</th>
                            <th class="px-5 py-3 text-left font-bold">Address</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($details as $value)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img class="h-11 w-11 rounded-lg object-cover ring-1 ring-slate-200"
                                            src="{{ asset('storage/images/branch/' . (!empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg')) }}"
                                            alt="{{ $value->branch_name }}">
                                        <div class="min-w-0">
                                            <div class="truncate font-bold text-erp-ink">{{ $value->branch_name }}</div>
                                            <div class="mt-1 text-xs font-semibold uppercase tracking-[0.12em] text-erp-mute">
                                                {{ $value->code ?? 'No code' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->city->city_name ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-erp-ink">{{ $value->branch_mobile ?? '-' }}</div>
                                    <div class="mt-1 text-xs text-erp-mute">{{ $value->branch_email ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($value->terminals->pluck('terminal_id')->filter() as $tid)
                                            <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">{{ $tid }}</span>
                                        @empty
                                            <span class="text-erp-mute">-</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($value->terminals->pluck('serial_no')->filter() as $sno)
                                            <span class="rounded-md bg-sky-50 px-2 py-1 text-xs font-bold text-sky-700 ring-1 ring-sky-200">{{ $sno }}</span>
                                        @empty
                                            <span class="text-erp-mute">-</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="max-w-xs px-5 py-4 text-erp-mute">
                                    <div class="line-clamp-2">{{ $value->branch_address ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ url('/branch-emails') }}/{{ Crypt::encrypt($value->branch_id) }}"
                                            class="rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                                            Emails
                                        </a>
                                        <a href="{{ url('/branch-edit') }}/{{ Crypt::encrypt($value->branch_id) }}"
                                            class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">
                                            Edit
                                        </a>
                                        <button type="button" onclick="deleteBranch('{{ $value->branch_id }}')"
                                            class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                                            Delete
                                        </button>
                                        <a href="{{ url('/terminals') }}/{{ Crypt::encrypt($value->branch_id) }}"
                                            class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">
                                            Terminal
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-14 text-center">
                                    <div class="text-base font-bold text-erp-ink">No branches found</div>
                                    <p class="mt-2 text-sm text-erp-mute">{{ $search ? 'Try a different search term.' : 'Create your first branch to get started.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-erp-mute">
                    Showing {{ $details->firstItem() ?? 0 }} to {{ $details->lastItem() ?? 0 }} of {{ $details->total() }} branches
                </div>
                <div class="flex gap-2">
                    @if ($details->onFirstPage())
                        <span class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-300">Previous</span>
                    @else
                        <a href="{{ $details->previousPageUrl() }}" class="rounded-lg border border-erp-line px-3 py-2 text-sm font-semibold text-erp-text transition hover:border-erp hover:text-erp-dark">Previous</a>
                    @endif

                    @if ($details->hasMorePages())
                        <a href="{{ $details->nextPageUrl() }}" class="rounded-lg border border-erp-line px-3 py-2 text-sm font-semibold text-erp-text transition hover:border-erp hover:text-erp-dark">Next</a>
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
        let branchSearchTimer;
        document.getElementById('branchSearch')?.addEventListener('input', function () {
            clearTimeout(branchSearchTimer);
            branchSearchTimer = setTimeout(function () {
                document.getElementById('searchForm').submit();
            }, 400);
        });

        function deleteBranch(id) {
            if (!confirm('Delete this branch?')) {
                return;
            }

            fetch("{{ url('/removebranch') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: JSON.stringify({ id: id })
            })
                .then(response => response.text())
                .then(function (response) {
                    if (response.trim() === '1') {
                        window.location = "{{ url('/branches') }}";
                    } else {
                        alert('Unable to delete branch.');
                    }
                })
                .catch(function () {
                    alert('Unable to delete branch.');
                });
        }
    </script>
@endpush
