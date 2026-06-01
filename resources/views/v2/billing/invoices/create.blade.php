@extends('layouts.master-tailwind')

@section('title', 'Generate Invoice')
@section('page_title', 'Generate Invoice')
@section('page_subtitle', 'Create monthly or previous due invoices with optional manual branch or terminal billing targets.')

@section('content')
    <form method="post" action="{{ route('billing.invoices.store') }}" class="space-y-6">
        @csrf

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Invoice Details</h2>
                    <p class="mt-1 text-sm text-erp-mute">Select the company, billing period, date, tax, mode, and notes.</p>
                </div>
                <a href="{{ route('billing.invoices.index') }}" class="inline-flex h-10 items-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Back to list</a>
            </div>

            <div class="grid gap-5 p-5 md:grid-cols-2 xl:grid-cols-3">
                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Company</span>
                    <select class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" name="company_id" required>
                        <option value="">Select Company</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->company_id }}" {{ old('company_id') == $company->company_id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Period Start</span>
                    <input type="date" name="period_start" value="{{ old('period_start', date('Y-m-01')) }}" required class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Period End</span>
                    <input type="date" name="period_end" value="{{ old('period_end', date('Y-m-t')) }}" required class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Invoice Date</span>
                    <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Due Date</span>
                    <input type="date" name="due_date" value="{{ old('due_date') }}" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Tax Amount</span>
                    <input type="number" step="0.01" min="0" name="tax_amount" value="{{ old('tax_amount', 0) }}" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Generation Mode</span>
                    <select name="generation_mode" id="generation_mode" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="auto" {{ old('generation_mode', 'auto') === 'auto' ? 'selected' : '' }}>Automatic</option>
                        <option value="manual" {{ old('generation_mode') === 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                </label>
                <label class="block md:col-span-2">
                    <span class="text-sm font-bold text-erp-ink">Notes</span>
                    <textarea name="notes" rows="3" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">{{ old('notes') }}</textarea>
                </label>
            </div>
        </section>

        <section id="manual-scope-section" class="rounded-lg border border-erp-line bg-white shadow-sm" style="{{ old('generation_mode') === 'manual' ? '' : 'display:none;' }}">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 id="manual-scope-title" class="text-base font-bold text-erp-ink">Manual Due Periods</h2>
                <p class="mt-1 text-sm text-erp-mute">Select only the branches or terminals you want to bill manually.</p>
            </div>
            <div class="p-5">
                <div id="scope-overrides-empty" class="rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-800">Select a company and switch to manual mode to load billing targets.</div>
                <div id="scope-overrides-wrapper" class="overflow-x-auto" style="display:none;">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                            <tr>
                                <th class="px-5 py-3 text-left font-bold">Include</th>
                                <th id="scope-name-header" class="px-5 py-3 text-left font-bold">Branch</th>
                                <th class="px-5 py-3 text-left font-bold">Details</th>
                                <th class="px-5 py-3 text-left font-bold">Due Start</th>
                                <th class="px-5 py-3 text-left font-bold">Due End</th>
                            </tr>
                        </thead>
                        <tbody id="scope-overrides-body" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('billing.invoices.index') }}" class="inline-flex h-11 items-center rounded-lg border border-erp-line px-5 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Cancel</a>
            <button class="inline-flex h-11 items-center rounded-lg bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">Generate Invoice</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        (function () {
            const company = document.querySelector('select[name="company_id"]');
            const generationMode = document.getElementById('generation_mode');
            const periodStart = document.querySelector('input[name="period_start"]');
            const periodEnd = document.querySelector('input[name="period_end"]');
            const section = document.getElementById('manual-scope-section');
            const wrapper = document.getElementById('scope-overrides-wrapper');
            const body = document.getElementById('scope-overrides-body');
            const empty = document.getElementById('scope-overrides-empty');
            const title = document.getElementById('manual-scope-title');
            const scopeNameHeader = document.getElementById('scope-name-header');
            const oldOverrides = @json(old('scope_overrides', []));

            function inputClass() {
                return 'h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
            }

            function buildScopeRows(invoiceType, items) {
                body.innerHTML = '';

                if (!items.length) {
                    wrapper.style.display = 'none';
                    empty.textContent = 'No active ' + (invoiceType === 'terminal' ? 'terminals' : 'branches') + ' found for this company.';
                    empty.style.display = '';
                    return;
                }

                title.textContent = 'Manual ' + (invoiceType === 'terminal' ? 'Terminal' : 'Branch') + '-wise Due Periods';
                scopeNameHeader.textContent = invoiceType === 'terminal' ? 'Terminal' : 'Branch';

                items.forEach(function (item, index) {
                    const oldRow = Array.isArray(oldOverrides) ? oldOverrides.find(function (row) {
                        return String(row.scope_type || '') === String(invoiceType) && String(row.scope_id || '') === String(item.id);
                    }) : null;
                    const includeChecked = oldRow ? !!oldRow.include : false;
                    const startValue = oldRow && oldRow.period_start ? oldRow.period_start : periodStart.value;
                    const endValue = oldRow && oldRow.period_end ? oldRow.period_end : periodEnd.value;

                    body.insertAdjacentHTML('beforeend',
                        '<tr class="hover:bg-slate-50">' +
                            '<td class="px-5 py-4">' +
                                '<input type="hidden" name="scope_overrides[' + index + '][scope_type]" value="' + invoiceType + '">' +
                                '<input type="hidden" name="scope_overrides[' + index + '][scope_id]" value="' + item.id + '">' +
                                '<input type="checkbox" class="rounded border-erp-line text-erp focus:ring-erp" name="scope_overrides[' + index + '][include]" value="1" ' + (includeChecked ? 'checked' : '') + '>' +
                            '</td>' +
                            '<td class="px-5 py-4 font-bold text-erp-ink">' + item.name + '</td>' +
                            '<td class="px-5 py-4 text-erp-mute">' + (item.meta || '-') + '</td>' +
                            '<td class="px-5 py-4"><input type="date" class="' + inputClass() + '" name="scope_overrides[' + index + '][period_start]" value="' + (startValue || '') + '"></td>' +
                            '<td class="px-5 py-4"><input type="date" class="' + inputClass() + '" name="scope_overrides[' + index + '][period_end]" value="' + (endValue || '') + '"></td>' +
                        '</tr>'
                    );
                });

                empty.style.display = 'none';
                wrapper.style.display = '';
            }

            function loadManualTargets() {
                body.innerHTML = '';

                if (generationMode.value !== 'manual') {
                    section.style.display = 'none';
                    wrapper.style.display = 'none';
                    empty.textContent = 'Select manual mode to load billing targets.';
                    empty.style.display = '';
                    return;
                }

                section.style.display = '';

                if (!company.value) {
                    wrapper.style.display = 'none';
                    empty.textContent = 'Select a company to load billing targets.';
                    empty.style.display = '';
                    return;
                }

                empty.textContent = 'Loading billing targets...';
                empty.style.display = '';
                wrapper.style.display = 'none';

                fetch('{{ route('billing.invoices.generation-targets') }}?company_id=' + encodeURIComponent(company.value), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.json())
                    .then(response => buildScopeRows(response.invoice_type || 'branch', Array.isArray(response.items) ? response.items : []))
                    .catch(() => {
                        wrapper.style.display = 'none';
                        empty.textContent = 'Unable to load billing targets right now.';
                        empty.style.display = '';
                    });
            }

            company?.addEventListener('change', loadManualTargets);
            generationMode?.addEventListener('change', loadManualTargets);
            periodStart?.addEventListener('change', loadManualTargets);
            periodEnd?.addEventListener('change', loadManualTargets);
            loadManualTargets();
        })();
    </script>
@endpush
