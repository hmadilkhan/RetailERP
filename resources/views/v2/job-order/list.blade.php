@extends('layouts.master-tailwind')

@section('title', 'Job Orders')
@section('page_title', 'Job Orders')
@section('page_subtitle', 'Manage recipes and costing for finished goods built from raw materials.')

@section('content')
    @php
        $isRestaurant = auth()->user()->company->application_id == 2;
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Active Job Orders</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format(collect($result)->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Recipes currently active</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Mode</div>
                <div class="mt-4 text-xl font-black text-erp-ink">{{ $isRestaurant ? 'Restaurant' : 'Manufacturing' }}</div>
                <p class="mt-2 text-sm text-erp-mute">Costing model in use</p>
            </div>
            <a href="{{ url('/create-job') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Job Order</div>
                    <p class="mt-2 text-sm text-white/75">Build a new recipe</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Job Order List</h2>
                    <p class="mt-1 text-sm text-erp-mute">Recipes and their ingredient, packing, and infrastructure costs.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <input type="search" id="jobSearch" placeholder="Search job order..." class="h-10 w-64 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <label class="inline-flex h-10 cursor-pointer items-center gap-2 rounded-lg border border-erp-line px-3 text-sm font-bold text-erp-text">
                        <input type="checkbox" id="chkactive" class="rounded border-erp-line text-erp focus:ring-erp">
                        Show Inactive
                    </label>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="tbljoborders" class="min-w-full divide-y divide-erp-line text-sm">
                    @if($isRestaurant)
                        <thead class="bg-erp-soft">
                            <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                                <th class="px-5 py-3">Job Order Name</th>
                                <th class="px-5 py-3">DineIn Cost</th>
                                <th class="px-5 py-3">Takeaway &amp; Delivery Cost</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody id="jobOrdersBody" class="divide-y divide-erp-line">
                            @forelse($result as $value)
                                <tr class="job-row" data-search="{{ strtolower($value->product_name) }}">
                                    <td class="px-5 py-3 font-semibold text-erp-ink">{{ $value->product_name }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $value->DineInCost + $value->infrastructure_cost }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $value->TakedelCost + $value->infrastructure_cost }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ url('/getdetails') }}/{{ Crypt::encrypt($value->recipy_id) }}" class="font-bold text-erp-dark hover:text-erp">View</a>
                                            <a href="{{ url('/edit-job') }}/{{ Crypt::encrypt($value->recipy_id) }}" class="font-bold text-erp-dark hover:text-erp">Edit</a>
                                            <button type="button" class="font-bold text-rose-600 hover:text-rose-700" onclick="inactive({{ $value->recipy_id }})">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-6 text-center text-sm text-erp-mute">No job orders yet.</td></tr>
                            @endforelse
                        </tbody>
                    @else
                        <thead class="bg-erp-soft">
                            <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                                <th class="px-5 py-3">Job Order Name</th>
                                <th class="px-5 py-3">Ingredient Cost</th>
                                <th class="px-5 py-3">Packing Cost</th>
                                <th class="px-5 py-3">Infrastructure Cost</th>
                                <th class="px-5 py-3">Total Cost</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody id="jobOrdersBody" class="divide-y divide-erp-line">
                            @forelse($result as $value)
                                <tr class="job-row" data-search="{{ strtolower($value->product_name) }}">
                                    <td class="px-5 py-3 font-semibold text-erp-ink">{{ $value->product_name }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $value->ingredients_cost }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $value->material_cost }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $value->infrastructure_cost }}</td>
                                    <td class="px-5 py-3 font-semibold text-erp-ink">{{ $value->total_cost }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ url('/getdetails') }}/{{ Crypt::encrypt($value->recipy_id) }}" class="font-bold text-erp-dark hover:text-erp">View</a>
                                            <a href="{{ url('/edit-job') }}/{{ Crypt::encrypt($value->recipy_id) }}" class="font-bold text-erp-dark hover:text-erp">Edit</a>
                                            <button type="button" class="font-bold text-rose-600 hover:text-rose-700" onclick="inactive({{ $value->recipy_id }})">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-6 text-center text-sm text-erp-mute">No job orders yet.</td></tr>
                            @endforelse
                        </tbody>
                    @endif
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const csrfToken = '{{ csrf_token() }}';

        document.getElementById('jobSearch').addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('.job-row').forEach(row => {
                row.classList.toggle('hidden', term !== '' && !row.dataset.search.includes(term));
            });
        });

        function inactive(recipyid) {
            if (!confirm('Your will not be able to recover this Job Order!')) return;

            fetch("{{ url('/inactiverecipy') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ recipyid })
            })
                .then(res => res.json())
                .then(result => {
                    if (result) {
                        alert('Job Order In-active Successfully');
                        window.location = "{{ url('/joborder') }}";
                    }
                })
                .catch(() => alert('Unable to deactivate job order.'));
        }

        function reactive(recipyid, productid) {
            fetch("{{ url('/reactiverecipy') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ recipyid, productid })
            })
                .then(res => res.json())
                .then(resp => {
                    if (resp == 1) {
                        alert('Job Order Re-active Successfully');
                    } else {
                        alert("Job Order Already Active, You can't active two job orders at same time!");
                    }
                    window.location = "{{ url('/joborder') }}";
                })
                .catch(() => alert('Unable to reactivate job order.'));
        }

        document.getElementById('chkactive').addEventListener('change', function () {
            if (!this.checked) {
                window.location = "{{ url('/joborder') }}";
                return;
            }

            fetch("{{ url('/joborder-inactive') }}")
                .then(res => res.json())
                .then(result => {
                    const tbody = document.getElementById('jobOrdersBody');
                    tbody.innerHTML = '';
                    if (!result || !result.length) {
                        tbody.innerHTML = '<tr><td colspan="6" class="px-5 py-6 text-center text-sm text-erp-mute">No inactive job orders.</td></tr>';
                        return;
                    }
                    result.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.className = 'job-row';
                        tr.innerHTML = `
                            <td class="px-5 py-3 font-semibold text-erp-ink">${row.product_name}</td>
                            <td class="px-5 py-3 text-erp-text">${row.ingredients_cost}</td>
                            <td class="px-5 py-3 text-erp-text">${row.material_cost}</td>
                            <td class="px-5 py-3 text-erp-text">${row.infrastructure_cost}</td>
                            <td class="px-5 py-3 font-semibold text-erp-ink">${row.total_cost}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex justify-end gap-3">
                                    <button type="button" class="font-bold text-erp-dark hover:text-erp" onclick="show(${row.recipy_id})">View</button>
                                    <button type="button" class="font-bold text-emerald-600 hover:text-emerald-700" onclick="reactive(${row.recipy_id}, ${row.product_id})">Reactivate</button>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                });
        });

        function show(recipyid) {
            window.location = "{{ url('/getdetails') }}/" + recipyid;
        }
    </script>
@endpush
