@extends('layouts.master-tailwind')

@section('title', 'Bank Discount')
@section('page_title', 'Bank Discounts')
@section('page_subtitle', 'Configure percentage discounts by bank for the current branch.')

@section('content')
    @php
        $bankCollection = collect($banks ?? []);
        $discountCollection = collect($discounts ?? []);
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Discount Rules</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($discountCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Active for this branch</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Banks</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($bankCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available for selection</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm sm:col-span-2">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Current Mode</div>
                <div id="formModeLabel" class="mt-4 text-xl font-black text-erp-ink">Create Discount</div>
                <p class="mt-2 text-sm text-erp-mute">Use row actions to update an existing discount.</p>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Bank Discount</h2>
                <p class="mt-1 text-sm text-erp-mute">Select a bank and enter discount percentage.</p>
            </div>
            <form id="discountForm" class="grid gap-4 p-5 md:grid-cols-12">
                @csrf
                <input type="hidden" name="id" id="id">
                <label class="block md:col-span-5">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Bank</span>
                    <select id="bank" name="bank" data-placeholder="Select Bank" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Bank</option>
                        @foreach($bankCollection as $bank)
                            <option value="{{ $bank->bank_id }}">{{ $bank->bank_name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block md:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Discount Percentage</span>
                    <input type="number" name="discount_percentage" id="discount_percentage" min="0" step="0.01" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <div class="flex items-end gap-2 md:col-span-4">
                    <button type="button" id="btn_save" class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Save</button>
                    <button type="button" id="btn_update" class="hidden h-10 rounded-lg bg-sky-600 px-4 text-sm font-bold text-white transition hover:bg-sky-700">Update</button>
                    <button type="button" id="btn_clear" class="h-10 rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Clear</button>
                </div>
            </form>
            <div id="discountStatus" class="border-t border-erp-line px-5 py-3 text-sm font-semibold text-erp-mute"></div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Discount Details</h2>
                    <p class="mt-1 text-sm text-erp-mute">Review, edit, or delete bank discounts.</p>
                </div>
                <input type="search" id="discountFilter" placeholder="Filter discounts..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Bank</th>
                            <th class="px-5 py-3 text-left font-bold">Percentage</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="discountRows" class="divide-y divide-slate-100">
                        @forelse($discountCollection as $val)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img class="h-11 w-11 rounded-lg object-cover ring-1 ring-slate-200" src="{{ asset('assets/images/banks/' . (!empty($val->image) ? $val->image : 'placeholder.jpg')) }}" alt="{{ $val->bank_name }}">
                                        <div class="font-bold text-erp-ink">{{ $val->bank_name }}</div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">{{ $val->percentage }}%</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" data-id="{{ $val->bank_discount_id }}" data-bank="{{ $val->bank_id }}" data-percentage="{{ $val->percentage }}" class="edit-discount rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</button>
                                        <button type="button" data-id="{{ $val->bank_discount_id }}" class="delete-discount rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center text-erp-mute">No bank discounts found.</td>
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
        const discountForm = document.getElementById('discountForm');
        const discountStatus = document.getElementById('discountStatus');

        function setDiscountStatus(message, success = true) {
            discountStatus.textContent = message;
            discountStatus.className = 'border-t border-erp-line px-5 py-3 text-sm font-semibold ' + (success ? 'text-emerald-700' : 'text-rose-700');
        }

        function resetDiscountForm() {
            discountForm.reset();
            document.getElementById('id').value = '';
            if (window.jQuery) {
                jQuery('#bank').val('').trigger('change.select2');
            }
            document.getElementById('btn_save').classList.remove('hidden');
            document.getElementById('btn_update').classList.add('hidden');
            document.getElementById('formModeLabel').textContent = 'Create Discount';
        }

        function submitDiscount(url) {
            if (!document.getElementById('bank').value) {
                setDiscountStatus('Please select bank.', false);
                return;
            }

            if (!document.getElementById('discount_percentage').value) {
                setDiscountStatus('Please enter discount percentage.', false);
                return;
            }

            fetch(url, { method: 'POST', body: new FormData(discountForm) })
                .then(response => response.json())
                .then(function (result) {
                    if (Number(result.state) === 0) {
                        setDiscountStatus('Saved successfully. Refreshing...');
                        window.setTimeout(() => window.location = "{{ url('/view-bank-discount') }}", 350);
                    } else {
                        setDiscountStatus(result.msg || 'Unable to save bank discount.', false);
                    }
                })
                .catch(() => setDiscountStatus('Unable to save bank discount.', false));
        }

        document.getElementById('btn_save').addEventListener('click', () => submitDiscount("{{ url('create-bank-discount') }}"));
        document.getElementById('btn_update').addEventListener('click', () => submitDiscount("{{ url('update-bank-discount') }}"));
        document.getElementById('btn_clear').addEventListener('click', resetDiscountForm);

        document.querySelectorAll('.edit-discount').forEach(function (button) {
            button.addEventListener('click', function () {
                document.getElementById('id').value = this.dataset.id;
                document.getElementById('bank').value = this.dataset.bank;
                document.getElementById('discount_percentage').value = this.dataset.percentage;
                if (window.jQuery) {
                    jQuery('#bank').val(this.dataset.bank).trigger('change.select2');
                }
                document.getElementById('btn_save').classList.add('hidden');
                document.getElementById('btn_update').classList.remove('hidden');
                document.getElementById('formModeLabel').textContent = 'Edit Discount';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });

        document.querySelectorAll('.delete-discount').forEach(function (button) {
            button.addEventListener('click', function () {
                if (!confirm('Delete this discount?')) {
                    return;
                }

                const data = new FormData();
                data.append('_token', "{{ csrf_token() }}");
                data.append('id', this.dataset.id);

                fetch("{{ url('/delete-bank-discount') }}", { method: 'POST', body: data })
                    .then(response => response.text())
                    .then(function (response) {
                        if (response.trim() === '1') {
                            window.location = "{{ url('/view-bank-discount') }}";
                        } else {
                            setDiscountStatus('Unable to delete discount.', false);
                        }
                    })
                    .catch(() => setDiscountStatus('Unable to delete discount.', false));
            });
        });

        document.getElementById('discountFilter').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#discountRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        });
    </script>
@endpush
