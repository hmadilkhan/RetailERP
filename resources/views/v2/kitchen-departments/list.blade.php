@extends('layouts.master-tailwind')

@section('title', 'Kitchen Departments')
@section('page_title', 'Kitchen Departments')
@section('page_subtitle', 'Map kitchen departments to inventory departments and printer setup.')

@section('content')
    @php
        $departmentCollection = collect($departments ?? []);
        $generalCollection = collect($general ?? []);
        $detailsCollection = collect($details ?? []);
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Kitchen Departments</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($generalCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Configured for current branch</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Inventory Departments</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($departmentCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available to map</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm sm:col-span-2">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Printer Setup</div>
                <div class="mt-4 text-xl font-black text-erp-ink">Per Department</div>
                <p class="mt-2 text-sm text-erp-mute">Use the printers action to manage kitchen printer routing.</p>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Create Kitchen Department</h2>
                <p class="mt-1 text-sm text-erp-mute">Create a main kitchen department and attach one or more inventory departments.</p>
            </div>
            <form id="deptform" class="grid gap-4 p-5 md:grid-cols-12">
                @csrf
                <label class="block md:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Kitchen Department Name</span>
                    <input type="text" name="deptname" id="deptname" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block md:col-span-5">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Inventory Departments</span>
                    <select id="depart" name="depart[]" multiple data-placeholder="Select Departments" class="v2-select2 mt-2 min-h-28 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        @foreach($departmentCollection as $department)
                            <option value="{{ $department->department_id }}">{{ $department->department_name }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="flex items-end gap-2 md:col-span-3">
                    <button type="button" id="btn_save" class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Save</button>
                    <button type="button" id="btn_clear" class="h-10 rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Clear</button>
                </div>
            </form>
            <div id="kitchenStatus" class="border-t border-erp-line px-5 py-3 text-sm font-semibold text-erp-mute"></div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Kitchen Department List</h2>
                    <p class="mt-1 text-sm text-erp-mute">Edit department names, update department mappings, or configure printers.</p>
                </div>
                <input type="search" id="kitchenFilter" placeholder="Filter departments..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Main Department</th>
                            <th class="px-5 py-3 text-left font-bold">Sub Departments</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="kitchenRows" class="divide-y divide-slate-100">
                        @forelse($generalCollection as $value)
                            @php($subDepartments = $detailsCollection->where('kitchen_depart_id', $value->id)->pluck('department_name')->filter()->values())
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-bold text-erp-ink">{{ $value->kitchen_department_name }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex max-w-3xl flex-wrap gap-1.5">
                                        @forelse($subDepartments as $departmentName)
                                            <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-bold text-slate-700 ring-1 ring-slate-200">{{ $departmentName }}</span>
                                        @empty
                                            <span class="text-erp-mute">No departments assigned</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" data-id="{{ $value->id }}" data-name="{{ e($value->kitchen_department_name) }}" class="edit-department rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit Name</button>
                                        <button type="button" data-id="{{ $value->id }}" class="edit-subdepartments rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">Sub Departments</button>
                                        <a href="{{ url('printers-kitchen-departments', $value->id) }}" class="rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Printers</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center text-erp-mute">No kitchen departments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div id="departModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-lg rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Edit Department Name</h3>
                <button type="button" data-close-modal="departModal" class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute hover:bg-slate-100">x</button>
            </div>
            <div class="space-y-4 p-5">
                <input type="hidden" id="departid">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Department Name</span>
                    <input type="text" id="department" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" onclick="updateDepartName()" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
            </div>
        </div>
    </div>

    <div id="subdepartModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-lg rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Edit Sub Departments</h3>
                <button type="button" data-close-modal="subdepartModal" class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute hover:bg-slate-100">x</button>
            </div>
            <form id="modelForm" class="space-y-4 p-5">
                @csrf
                <input type="hidden" name="uhidd_id" id="uhidd_id" value="0">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Inventory Departments</span>
                    <select id="kdepartment" name="kdepartment[]" multiple data-placeholder="Select Departments" class="v2-select2 mt-2 min-h-40 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        @foreach($departmentCollection as $department)
                            <option value="{{ $department->department_id }}">{{ $department->department_name }}</option>
                        @endforeach
                    </select>
                </label>
            </form>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" onclick="updateSubDepartments()" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const kitchenStatus = document.getElementById('kitchenStatus');

        function setKitchenStatus(message, success = true) {
            kitchenStatus.textContent = message;
            kitchenStatus.className = 'border-t border-erp-line px-5 py-3 text-sm font-semibold ' + (success ? 'text-emerald-700' : 'text-rose-700');
        }

        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.querySelectorAll('[data-close-modal]').forEach(button => {
            button.addEventListener('click', () => closeModal(button.dataset.closeModal));
        });

        document.getElementById('btn_clear').addEventListener('click', function () {
            document.getElementById('deptform').reset();
            [...document.getElementById('depart').options].forEach(option => option.selected = false);
            if (window.jQuery) {
                jQuery('#depart').val(null).trigger('change.select2');
            }
        });

        document.getElementById('btn_save').addEventListener('click', function () {
            if (!document.getElementById('deptname').value.trim()) {
                setKitchenStatus('Enter department name.', false);
                document.getElementById('deptname').focus();
                return;
            }

            if (![...document.getElementById('depart').selectedOptions].length) {
                setKitchenStatus('Select at least one inventory department.', false);
                return;
            }

            fetch("{{ url('save-kitchen-department') }}", { method: 'POST', body: new FormData(document.getElementById('deptform')) })
                .then(response => response.text())
                .then(function (response) {
                    if (response.trim()) {
                        setKitchenStatus('Saved successfully. Refreshing...');
                        window.setTimeout(() => window.location = "{{ url('/view-kitchen-departments') }}", 350);
                    } else {
                        setKitchenStatus('Unable to save kitchen department.', false);
                    }
                })
                .catch(() => setKitchenStatus('Unable to save kitchen department.', false));
        });

        document.querySelectorAll('.edit-department').forEach(function (button) {
            button.addEventListener('click', function () {
                document.getElementById('department').value = this.dataset.name;
                document.getElementById('departid').value = this.dataset.id;
                openModal('departModal');
            });
        });

        function updateDepartName() {
            const data = new FormData();
            data.append('_token', "{{ csrf_token() }}");
            data.append('departid', document.getElementById('departid').value);
            data.append('departname', document.getElementById('department').value);

            fetch("{{ url('/update-depart') }}", { method: 'PUT', body: data })
                .then(response => response.text())
                .then(function (response) {
                    if (response.trim() !== '0') {
                        window.location = "{{ url('/view-kitchen-departments') }}";
                    } else {
                        setKitchenStatus('Unable to update department.', false);
                    }
                })
                .catch(() => setKitchenStatus('Unable to update department.', false));
        }

        document.querySelectorAll('.edit-subdepartments').forEach(function (button) {
            button.addEventListener('click', function () {
                const data = new FormData();
                data.append('_token', "{{ csrf_token() }}");
                data.append('departid', this.dataset.id);

                fetch("{{ url('/getsubkitchendepart') }}", { method: 'POST', body: data })
                    .then(response => response.json())
                    .then(function (result) {
                        [...document.getElementById('kdepartment').options].forEach(option => option.selected = false);

                        if (result.length) {
                            const selectedIds = result.map(item => String(item.inventory_department_id));
                            [...document.getElementById('kdepartment').options].forEach(function (option) {
                                option.selected = selectedIds.includes(option.value);
                            });
                            if (window.jQuery) {
                                jQuery('#kdepartment').val(selectedIds).trigger('change.select2');
                            }
                            document.getElementById('uhidd_id').value = result[0].kitchen_depart_id;
                        } else {
                            document.getElementById('uhidd_id').value = button.dataset.id;
                            if (window.jQuery) {
                                jQuery('#kdepartment').val(null).trigger('change.select2');
                            }
                        }

                        openModal('subdepartModal');
                    })
                    .catch(() => setKitchenStatus('Unable to load sub departments.', false));
            });
        });

        function updateSubDepartments() {
            if (![...document.getElementById('kdepartment').selectedOptions].length) {
                setKitchenStatus('Please select at least one department.', false);
                return;
            }

            fetch("{{ url('update-kitchen-details-update') }}", { method: 'POST', body: new FormData(document.getElementById('modelForm')) })
                .then(response => response.text())
                .then(function (response) {
                    if (response.trim() === '1') {
                        window.location = "{{ url('/view-kitchen-departments') }}";
                    } else {
                        setKitchenStatus('Please select at least one department.', false);
                    }
                })
                .catch(() => setKitchenStatus('Unable to update sub departments.', false));
        }

        document.getElementById('kitchenFilter').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#kitchenRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        });
    </script>
@endpush
