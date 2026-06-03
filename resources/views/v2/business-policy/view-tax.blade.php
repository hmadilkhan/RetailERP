@extends('layouts.master-tailwind')

@section('title', 'Business Policy')
@section('page_title', 'Business Policy')
@section('page_subtitle', 'Manage tax rules used in purchases and POS.')

@section('content')
    @php($taxCollection = collect($details ?? []))

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Tax Rules</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($taxCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Active business tax policies</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Purchase Rules</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($taxCollection->where('show_in_purchase', 1)->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Visible in purchase flow</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">POS Rules</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($taxCollection->where('show_in_pos', 1)->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Visible in POS flow</p>
            </div>
            <a href="{{ url('/Tax-create') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Tax Rule</div>
                    <p class="mt-2 text-sm text-white/75">Add a business tax policy</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Tax Rules List</h2>
                    <p class="mt-1 text-sm text-erp-mute">Review, edit, or remove tax rules.</p>
                </div>
                <input type="search" id="taxFilter" placeholder="Filter tax rules..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Rule No</th>
                            <th class="px-5 py-3 text-left font-bold">Tax Head</th>
                            <th class="px-5 py-3 text-left font-bold">Percentage</th>
                            <th class="px-5 py-3 text-left font-bold">Purchase</th>
                            <th class="px-5 py-3 text-left font-bold">POS</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="taxRows" class="divide-y divide-slate-100">
                        @forelse($taxCollection as $value)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-semibold text-erp-text">{{ $value->id }}</td>
                                <td class="px-5 py-4 font-bold text-erp-ink">{{ $value->name }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->value }}%</td>
                                <td class="px-5 py-4"><span class="rounded-md px-2 py-1 text-xs font-bold ring-1 {{ $value->show_in_purchase ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">{{ $value->show_in_purchase ? 'Yes' : 'No' }}</span></td>
                                <td class="px-5 py-4"><span class="rounded-md px-2 py-1 text-xs font-bold ring-1 {{ $value->show_in_pos ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">{{ $value->show_in_pos ? 'Yes' : 'No' }}</span></td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->status_name }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ url('/show-tax') }}/{{ Crypt::encrypt($value->id) }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</a>
                                        <button type="button" onclick="deleteTax(@js($value->id))" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-erp-mute">No tax rules found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="taxStatus" class="border-t border-erp-line px-5 py-3 text-sm font-semibold text-erp-mute"></div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function setTaxStatus(message, success = true) {
            const status = document.getElementById('taxStatus');
            status.textContent = message;
            status.className = 'border-t border-erp-line px-5 py-3 text-sm font-semibold ' + (success ? 'text-emerald-700' : 'text-rose-700');
        }

        function deleteTax(id) {
            if (!confirm('Delete this tax rule?')) {
                return;
            }

            fetch("{{ url('/delete_tax') }}", {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ id: id })
            }).then(response => response.text()).then(function (response) {
                if (response.trim() === '1') {
                    window.location = "{{ url('/BusinessPolicy') }}";
                } else {
                    setTaxStatus('Unable to delete tax rule.', false);
                }
            }).catch(() => setTaxStatus('Unable to delete tax rule.', false));
        }

        document.getElementById('taxFilter').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#taxRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        });
    </script>
@endpush
