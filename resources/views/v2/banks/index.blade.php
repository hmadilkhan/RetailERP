@extends('layouts.master-tailwind')

@section('title', 'Banks')
@section('page_title', 'Banks')
@section('page_subtitle', 'Manage bank records used across accounts, cheques, and discounts.')

@section('content')
    @php($bankCollection = collect($banks ?? []))

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Banks</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($bankCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available bank master records</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Visible Rows</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($bankCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Showing in this list</p>
            </div>
            <a href="{{ url('create-bank') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark sm:col-span-2">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Bank</div>
                    <p class="mt-2 text-sm text-white/75">Add a new bank record</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Banks List</h2>
                    <p class="mt-1 text-sm text-erp-mute">Review and edit bank master records.</p>
                </div>
                <input type="search" id="bankFilter" placeholder="Filter banks..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Bank</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="bankRows" class="divide-y divide-slate-100">
                        @forelse($bankCollection as $value)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img class="h-11 w-11 rounded-lg object-cover ring-1 ring-slate-200" src="{{ asset('assets/images/banks/' . (!empty($value->image) ? $value->image : 'placeholder.jpg')) }}" alt="{{ $value->bank_name }}">
                                        <div class="font-bold text-erp-ink">{{ $value->bank_name }}</div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end">
                                        <a href="{{ url('/edit-bank') }}/{{ Crypt::encrypt($value->bank_id) }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-12 text-center text-erp-mute">No banks found.</td>
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
        document.getElementById('bankFilter').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#bankRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        });
    </script>
@endpush
