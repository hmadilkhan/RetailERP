@extends('layouts.master-tailwind')

@section('title', 'Display Inventory')
@section('page_title', 'Display Inventory')
@section('page_subtitle', 'Filter products and control POS, online, and hide visibility per item.')

@section('content')
    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Filter Inventory</h2>
            </div>
            <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-5">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Search Item Code</span>
                    <input type="text" id="code" placeholder="Enter item code" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Search Product</span>
                    <input type="text" id="name" placeholder="Enter product name" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Department</span>
                    <select id="depart" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Department</option>
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Sub-Department</span>
                    <select id="subdepart" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Sub Department</option>
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Status</span>
                    <select id="status" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">All</option>
                        <option value="1">Yes</option>
                        <option value="2">No</option>
                    </select>
                </label>
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" id="search" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Search</button>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div id="table_data">
                @include('v2.partials.inventory_table')
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const csrfToken = '{{ csrf_token() }}';

        function currentFilters() {
            return {
                code: document.getElementById('code').value,
                name: document.getElementById('name').value,
                depart: document.getElementById('depart').value,
                sdepart: document.getElementById('subdepart').value,
                status: document.getElementById('status').value,
            };
        }

        function fetch_data(page) {
            const filters = currentFilters();
            const params = new URLSearchParams({ ...filters, page: page ?? '' });

            fetch("{{ url('fetch-inventory-data') }}?" + params.toString())
                .then(res => res.text())
                .then(html => {
                    document.getElementById('table_data').innerHTML = html;
                });
        }

        document.getElementById('search').addEventListener('click', function () {
            fetch_data();
        });

        document.getElementById('table_data').addEventListener('click', function (event) {
            const link = event.target.closest('.pagination a, nav a');
            if (!link) return;
            event.preventDefault();
            const match = link.href.match(/page=(\d+)/);
            fetch_data(match ? match[1] : 1);
        });

        function load_department() {
            fetch("{{ url('get_departments') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({})
            })
                .then(res => res.json())
                .then(resp => {
                    const depart = document.getElementById('depart');
                    depart.innerHTML = '<option value="">Select Department</option>';
                    (resp || []).forEach(value => {
                        const opt = document.createElement('option');
                        opt.value = value.department_id;
                        opt.textContent = value.department_name;
                        depart.appendChild(opt);
                    });
                });
        }
        load_department();

        document.getElementById('depart').addEventListener('change', function () {
            fetch("{{ url('get_sub_departments') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id: this.value })
            })
                .then(res => res.json())
                .then(resp => {
                    const subdepart = document.getElementById('subdepart');
                    subdepart.innerHTML = '<option value="">Select Sub Department</option>';
                    (resp || []).forEach(value => {
                        const opt = document.createElement('option');
                        opt.value = value.sub_department_id;
                        opt.textContent = value.sub_depart_name;
                        subdepart.appendChild(opt);
                    });
                });
        });
    </script>
@endpush
