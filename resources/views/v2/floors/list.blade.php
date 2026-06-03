@extends('layouts.master-tailwind')

@section('title', 'Floors')
@section('page_title', 'Floors')
@section('page_subtitle', 'Create and manage dining floors and table capacity for the current branch.')

@section('content')
    @php
        $floorCollection = collect($floors ?? []);
        $totalTables = $floorCollection->sum('table_qty');
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Floors</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($floorCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Configured for current branch</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Table Capacity</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($totalTables) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Total tables across floors</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm sm:col-span-2">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Current Mode</div>
                <div id="formModeLabel" class="mt-4 text-xl font-black text-erp-ink">Create Floor</div>
                <p class="mt-2 text-sm text-erp-mute">Select a row action to edit an existing floor.</p>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Floor Details</h2>
                <p class="mt-1 text-sm text-erp-mute">Add floor names and their table quantity.</p>
            </div>
            <form id="floorForm" class="grid gap-4 p-5 md:grid-cols-12">
                @csrf
                <input type="hidden" name="floorid" id="floorid">
                <label class="block md:col-span-5">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Floor Name</span>
                    <input type="text" name="floorname" id="floorname" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <span id="floorname_alert" class="mt-1 hidden text-xs font-semibold text-rose-600"></span>
                </label>
                <label class="block md:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Table Qty</span>
                    <input type="number" min="0" name="tableQty" id="tableQty" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <div class="flex items-end gap-2 md:col-span-4">
                    <button type="button" id="btn_save" class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Save</button>
                    <button type="button" id="btn_update" class="hidden h-10 rounded-lg bg-sky-600 px-4 text-sm font-bold text-white transition hover:bg-sky-700">Update</button>
                    <button type="button" id="btn_clear" class="h-10 rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Clear</button>
                </div>
            </form>
            <div id="floorStatus" class="border-t border-erp-line px-5 py-3 text-sm font-semibold text-erp-mute"></div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Floor List</h2>
                    <p class="mt-1 text-sm text-erp-mute">Review, edit, or delete floor records.</p>
                </div>
                <input type="search" id="floorFilter" placeholder="Filter floors..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Floor Name</th>
                            <th class="px-5 py-3 text-left font-bold">Table Qty</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="floorRows" class="divide-y divide-slate-100">
                        @forelse($floorCollection as $floor)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-bold text-erp-ink">{{ $floor->floor_name }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">{{ $floor->table_qty }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" data-id="{{ $floor->floor_id }}" data-name="{{ e($floor->floor_name) }}" data-qty="{{ $floor->table_qty }}" class="edit-floor rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</button>
                                        <button type="button" data-id="{{ $floor->floor_id }}" class="delete-floor rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center text-erp-mute">No floors found.</td>
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
        const floorForm = document.getElementById('floorForm');
        const floorStatus = document.getElementById('floorStatus');

        function setFloorStatus(message, success = true) {
            floorStatus.textContent = message;
            floorStatus.className = 'border-t border-erp-line px-5 py-3 text-sm font-semibold ' + (success ? 'text-emerald-700' : 'text-rose-700');
        }

        function resetFloorForm() {
            floorForm.reset();
            document.getElementById('floorid').value = '';
            document.getElementById('btn_save').classList.remove('hidden');
            document.getElementById('btn_update').classList.add('hidden');
            document.getElementById('formModeLabel').textContent = 'Create Floor';
            document.getElementById('floorname_alert').classList.add('hidden');
        }

        function submitFloor(url) {
            if (!document.getElementById('floorname').value.trim()) {
                document.getElementById('floorname_alert').textContent = 'Floor name is required.';
                document.getElementById('floorname_alert').classList.remove('hidden');
                document.getElementById('floorname').focus();
                return;
            }

            fetch(url, { method: 'POST', body: new FormData(floorForm) })
                .then(response => response.json())
                .then(function (result) {
                    if (Number(result.state) === 0) {
                        setFloorStatus('Saved successfully. Refreshing...');
                        window.setTimeout(() => window.location = "{{ url('/view-floors') }}", 350);
                    } else {
                        setFloorStatus(result.msg || 'Unable to save floor.', false);
                    }
                })
                .catch(() => setFloorStatus('Unable to save floor.', false));
        }

        document.getElementById('btn_save').addEventListener('click', () => submitFloor("{{ url('create-floors') }}"));
        document.getElementById('btn_update').addEventListener('click', () => submitFloor("{{ url('update-floors') }}"));
        document.getElementById('btn_clear').addEventListener('click', resetFloorForm);

        document.querySelectorAll('.edit-floor').forEach(function (button) {
            button.addEventListener('click', function () {
                document.getElementById('floorid').value = this.dataset.id;
                document.getElementById('floorname').value = this.dataset.name;
                document.getElementById('tableQty').value = this.dataset.qty;
                document.getElementById('btn_save').classList.add('hidden');
                document.getElementById('btn_update').classList.remove('hidden');
                document.getElementById('formModeLabel').textContent = 'Edit Floor';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });

        document.querySelectorAll('.delete-floor').forEach(function (button) {
            button.addEventListener('click', function () {
                if (!confirm('Delete this floor?')) {
                    return;
                }

                const data = new FormData();
                data.append('_token', "{{ csrf_token() }}");
                data.append('id', this.dataset.id);

                fetch("{{ url('/delete-floors') }}", { method: 'POST', body: data })
                    .then(response => response.text())
                    .then(function (response) {
                        if (response.trim() === '1') {
                            window.location = "{{ url('/view-floors') }}";
                        } else {
                            setFloorStatus('Unable to delete floor.', false);
                        }
                    })
                    .catch(() => setFloorStatus('Unable to delete floor.', false));
            });
        });

        document.getElementById('floorFilter').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#floorRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        });
    </script>
@endpush
