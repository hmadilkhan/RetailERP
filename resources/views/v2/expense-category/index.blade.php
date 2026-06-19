@extends('layouts.master-tailwind')

@section('title', 'Expense Categories')
@section('page_title', 'Expense Categories')
@section('page_subtitle', 'Create and manage categories used to classify expenses.')

@section('content')
    <div class="grid gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Create Expense Category</h2>
            </div>
            <div class="space-y-4 p-5">
                <input type="hidden" id="hidd_id" value="0">

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Expense Category</span>
                    <input type="text" id="category" placeholder="Expense category" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <span id="category_alert" class="mt-1 block text-xs font-semibold text-rose-600"></span>
                </label>

                @if (session('company_id') == 134)
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Platform Type</span>
                        <select id="platform_type" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option selected value="0">All</option>
                            <option value="1">Web</option>
                            <option value="2">App</option>
                        </select>
                    </label>
                @endif

                <button type="button" id="btn_save" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">+ Add Category</button>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Expense Category List</h2>
                <input type="search" id="categorySearch" placeholder="Search category..." class="h-10 w-56 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-5 py-3">Category</th>
                            @if (session('company_id') == 134)
                                <th class="px-5 py-3">Platform Type</th>
                            @endif
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line">
                        @forelse($category as $value)
                            <tr class="category-row" data-search="{{ strtolower($value->expense_category) }}">
                                <td class="px-5 py-3 font-semibold text-erp-ink" id="{{ $value->exp_cat_id }}">{{ $value->expense_category }}</td>
                                @if (session('company_id') == 134)
                                    <td class="px-5 py-3 text-erp-text">{{ $value->platform_type == 1 ? 'WEB' : 'APP' }}</td>
                                @endif
                                <td class="px-5 py-3 text-right">
                                    <button type="button" onclick="edit_record('{{ $value->exp_cat_id }}')" class="font-bold text-erp-dark hover:text-erp">Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-6 text-center text-sm text-erp-mute">No categories yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const csrfToken = '{{ csrf_token() }}';

        document.getElementById('categorySearch').addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('.category-row').forEach(row => {
                row.classList.toggle('hidden', term !== '' && !row.dataset.search.includes(term));
            });
        });

        function saveCategory() {
            const category = document.getElementById('category').value;
            const platformType = document.getElementById('platform_type') ? document.getElementById('platform_type').value : '';
            document.getElementById('category_alert').textContent = '';

            if (!category) {
                document.getElementById('category').focus();
                document.getElementById('category_alert').textContent = 'Category name is required.';
                return;
            }

            fetch("{{ route('exp_category.store') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ category, platform_type: platformType })
            })
                .then(res => res.json())
                .then(r => {
                    if (r.state == 1) {
                        if (r.contrl) {
                            document.getElementById(r.contrl).focus();
                            document.getElementById(r.contrl + '_alert').textContent = r.msg;
                        }
                        alert(r.msg);
                    } else if (r.state == 2) {
                        alert("Category '" + category + "' already exists!");
                    } else {
                        alert("Category '" + category + "' added successfully!");
                        window.location = "{{ route('exp_category.index') }}";
                    }
                });
        }

        document.getElementById('btn_save').addEventListener('click', saveCategory);

        document.getElementById('category').addEventListener('keyup', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                saveCategory();
            }
        });

        function edit_record(id) {
            fetch("{{ url('/expcate_edit') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id })
            })
                .then(res => res.json())
                .then(r => {
                    if (r.state == 0) {
                        alert(r.msg);
                        return;
                    }

                    const inputValue = prompt('Edit Expense Category', r[0].category);
                    if (inputValue === null) return;
                    if (inputValue === '') {
                        alert('Category name is required.');
                        return;
                    }

                    fetch("{{ url('/expcate-update') }}", {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ id: r[0].id, cat: inputValue })
                    })
                        .then(res => res.json())
                        .then(r2 => {
                            if (r2.state == 1) {
                                alert("Category '" + inputValue + "' updated successfully!");
                                window.location = "{{ route('exp_category.index') }}";
                            } else {
                                alert("Category '" + inputValue + "' already exists!");
                            }
                        });
                });
        }
    </script>
@endpush
